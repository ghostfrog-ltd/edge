import json
import logging
import os
from dataclasses import dataclass
from urllib import request as urllib_request
from urllib.error import HTTPError, URLError

from app.env import load_project_env


load_project_env()

logger = logging.getLogger(__name__)


@dataclass
class SummaryInput:
    keyword: str
    marketplace: str
    family: str
    top_gap: str
    strongest_signal: str
    action: str


@dataclass
class GapCandidate:
    title: str
    why_it_matters: str
    what_to_add: str
    evidence_source: str
    priority_score: int
    ranking_reason: str


@dataclass
class RankingInput:
    keyword: str
    marketplace: str
    family: str
    evidence_count: int
    schema_count: int
    voc_count: int
    candidates: list[GapCandidate]


@dataclass
class RankingResult:
    ordered_titles: list[str]
    ranking_rationale: str
    confidence_score: int
    quality_score: int


def clamp_score(value: int, minimum: int = 0, maximum: int = 100) -> int:
    return max(minimum, min(maximum, int(value)))


def deterministic_ranking_result(payload: RankingInput) -> RankingResult:
    ordered = sorted(
        payload.candidates,
        key=lambda candidate: (-candidate.priority_score, candidate.title.lower()),
    )
    top_titles = [candidate.title for candidate in ordered[:3]]
    strongest_source = ordered[0].evidence_source if ordered else "Gap analysis"

    confidence_score = clamp_score(
        44
        + min(payload.evidence_count, 50) // 2
        + payload.schema_count * 4
        + payload.voc_count * 4
        + (6 if len(payload.candidates) >= 3 else 0),
        minimum=38,
        maximum=96,
    )
    quality_score = clamp_score(
        42
        + (ordered[0].priority_score if ordered else 50) // 2
        + payload.schema_count * 3
        + payload.voc_count * 2
        + min(payload.evidence_count, 50) // 3,
        minimum=40,
        maximum=97,
    )

    rationale = (
        f"Ghostfrog ranked the strongest eBay gaps by combining live listing evidence, "
        f"{payload.schema_count} schema findings, {payload.voc_count} buyer-friction signals, "
        f"and a strongest-first bias toward {strongest_source.lower()} opportunities."
    )

    return RankingResult(
        ordered_titles=top_titles,
        ranking_rationale=rationale,
        confidence_score=confidence_score,
        quality_score=quality_score,
    )


class BaseSummaryProvider:
    def summarize(self, payload: SummaryInput) -> str:
        raise NotImplementedError

    def rank_gaps(self, payload: RankingInput) -> RankingResult:
        return deterministic_ranking_result(payload)

    def provider_label(self) -> str:
        return type(self).__name__

    def provider_status(self) -> str:
        return "ok"

    def provider_error(self) -> str | None:
        return None

    def ranking_provider_label(self) -> str:
        return self.provider_label()

    def ranking_provider_status(self) -> str:
        return "deterministic"

    def ranking_provider_error(self) -> str | None:
        return None


class MockSummaryProvider(BaseSummaryProvider):
    def summarize(self, payload: SummaryInput) -> str:
        return (
            f"For '{payload.keyword}' on {payload.marketplace}, the clearest eBay listing gap is "
            f"{payload.top_gap.lower()}. The strongest competitor signal is "
            f"{payload.strongest_signal.lower()}, and the next best eBay move is to "
            f"{payload.action.lower()}."
        )

    def ranking_provider_label(self) -> str:
        return "Deterministic:mock"


class PlaceholderExternalSummaryProvider(BaseSummaryProvider):
    def __init__(self, provider_name: str) -> None:
        self.provider_name = provider_name
        self.fallback = MockSummaryProvider()

    def summarize(self, payload: SummaryInput) -> str:
        summary = self.fallback.summarize(payload)
        return f"{summary} (Summary provider: {self.provider_name} fallback mode.)"

    def provider_status(self) -> str:
        return "fallback"

    def provider_error(self) -> str | None:
        return f"External provider '{self.provider_name}' is not active."

    def ranking_provider_label(self) -> str:
        return f"{self.provider_name} (fallback)"

    def ranking_provider_status(self) -> str:
        return "fallback"

    def ranking_provider_error(self) -> str | None:
        return f"External provider '{self.provider_name}' ranking is not active."


class OpenAISummaryProvider(BaseSummaryProvider):
    def __init__(self, api_key: str, model: str) -> None:
        self.api_key = api_key
        self.model = model
        self.fallback = MockSummaryProvider()
        self.last_summary_mode = "fallback"
        self.last_summary_error = None
        self.last_ranking_mode = "fallback"
        self.last_ranking_error = None

    def summarize(self, payload: SummaryInput) -> str:
        self.last_summary_error = None
        request_body = {
            "model": self.model,
            "input": [
                {
                    "role": "system",
                    "content": (
                        "You are an eBay marketplace intelligence analyst. Return structured JSON only. "
                        "Write a crisp 2-3 sentence summary for an eBay seller. "
                        "Focus on the highest-value listing gap, the strongest competitor signal, "
                        "and the most practical next action using eBay language such as title, item specifics, "
                        "condition detail, buyer trust, and listing clarity."
                    ),
                },
                {
                    "role": "user",
                    "content": (
                        f"Keyword: {payload.keyword}\n"
                        f"Marketplace: {payload.marketplace}\n"
                        f"Product family: {payload.family}\n"
                        f"Top gap: {payload.top_gap}\n"
                        f"Strongest competitor signal: {payload.strongest_signal}\n"
                        f"Best next action: {payload.action}\n"
                    ),
                },
            ],
            "text": {
                "format": {
                    "type": "json_schema",
                    "name": "ghostfrog_report_summary",
                    "strict": True,
                    "schema": {
                        "type": "object",
                        "additionalProperties": False,
                        "properties": {
                            "summary": {
                                "type": "string",
                                "description": "A concise seller-facing summary of the listing opportunity.",
                            }
                        },
                        "required": ["summary"],
                    },
                }
            },
        }

        structured = self._post_json(
            "https://api.openai.com/v1/responses",
            request_body,
            headers={
                "Content-Type": "application/json",
                "Authorization": f"Bearer {self.api_key}",
            },
        )

        if structured is None:
            return self.fallback.summarize(payload)

        summary = (structured.get("summary") or "").strip()

        if summary:
            self.last_summary_mode = "openai"
            return summary

        self.last_summary_mode = "fallback"
        self.last_summary_error = "OpenAI response did not contain a valid structured summary."
        logger.warning("OpenAI summary response missing structured summary.")
        return self.fallback.summarize(payload)

    def rank_gaps(self, payload: RankingInput) -> RankingResult:
        self.last_ranking_error = None
        deterministic = deterministic_ranking_result(payload)
        candidates_text = "\n".join(
            [
                (
                    f"- Title: {candidate.title} | Source: {candidate.evidence_source} | "
                    f"Priority score: {candidate.priority_score} | "
                    f"Why: {candidate.why_it_matters} | "
                    f"Action: {candidate.what_to_add}"
                )
                for candidate in payload.candidates
            ]
        )
        request_body = {
            "model": self.model,
            "input": [
                {
                    "role": "system",
                    "content": (
                        "You are Ghostfrog's eBay gap-ranking analyst. Return structured JSON only. "
                        "Pick the best three listing gaps from the supplied candidates. "
                        "Prioritize fields that improve title clarity, item specifics coverage, "
                        "buyer reassurance, and direct eBay comparability."
                    ),
                },
                {
                    "role": "user",
                    "content": (
                        f"Keyword: {payload.keyword}\n"
                        f"Marketplace: {payload.marketplace}\n"
                        f"Product family: {payload.family}\n"
                        f"Evidence listings sampled: {payload.evidence_count}\n"
                        f"Schema findings: {payload.schema_count}\n"
                        f"VoC findings: {payload.voc_count}\n"
                        f"Candidates:\n{candidates_text}\n"
                    ),
                },
            ],
            "text": {
                "format": {
                    "type": "json_schema",
                    "name": "ghostfrog_gap_ranking",
                    "strict": True,
                    "schema": {
                        "type": "object",
                        "additionalProperties": False,
                        "properties": {
                            "ordered_titles": {
                                "type": "array",
                                "minItems": 1,
                                "maxItems": 3,
                                "items": {"type": "string"},
                            },
                            "ranking_rationale": {"type": "string"},
                            "confidence_score": {"type": "integer", "minimum": 0, "maximum": 100},
                            "quality_score": {"type": "integer", "minimum": 0, "maximum": 100},
                        },
                        "required": [
                            "ordered_titles",
                            "ranking_rationale",
                            "confidence_score",
                            "quality_score",
                        ],
                    },
                }
            },
        }

        structured = self._post_json(
            "https://api.openai.com/v1/responses",
            request_body,
            headers={
                "Content-Type": "application/json",
                "Authorization": f"Bearer {self.api_key}",
            },
            for_ranking=True,
        )

        if structured is None:
            return deterministic

        ordered_titles = [
            title.strip()
            for title in structured.get("ordered_titles", [])
            if isinstance(title, str) and title.strip()
        ]

        if not ordered_titles:
            self.last_ranking_mode = "fallback"
            self.last_ranking_error = "OpenAI ranking response did not contain any ordered titles."
            logger.warning("OpenAI ranking response missing ordered titles.")
            return deterministic

        self.last_ranking_mode = "openai"
        return RankingResult(
            ordered_titles=ordered_titles[:3],
            ranking_rationale=(structured.get("ranking_rationale") or deterministic.ranking_rationale).strip(),
            confidence_score=clamp_score(structured.get("confidence_score", deterministic.confidence_score)),
            quality_score=clamp_score(structured.get("quality_score", deterministic.quality_score)),
        )

    def provider_label(self) -> str:
        if self.last_summary_mode == "openai":
            return f"OpenAI:{self.model}"

        return f"OpenAI:{self.model} (fallback)"

    def provider_status(self) -> str:
        return self.last_summary_mode

    def provider_error(self) -> str | None:
        return self.last_summary_error

    def ranking_provider_label(self) -> str:
        if self.last_ranking_mode == "openai":
            return f"OpenAI:{self.model}"

        return f"OpenAI:{self.model} (fallback)"

    def ranking_provider_status(self) -> str:
        return self.last_ranking_mode

    def ranking_provider_error(self) -> str | None:
        return self.last_ranking_error

    def _post_json(
        self,
        url: str,
        request_body: dict,
        headers: dict[str, str],
        for_ranking: bool = False,
    ) -> dict | None:
        req = urllib_request.Request(
            url,
            data=json.dumps(request_body).encode("utf-8"),
            headers=headers,
            method="POST",
        )

        try:
            with urllib_request.urlopen(req, timeout=30) as response:
                payload_json = json.loads(response.read().decode("utf-8"))
        except HTTPError as exc:
            error_body = self._read_error_body(exc)
            error_message = f"HTTP {exc.code}: {error_body or exc.reason}"
            if for_ranking:
                self.last_ranking_mode = "fallback"
                self.last_ranking_error = error_message
                logger.warning("OpenAI ranking request failed with HTTP %s: %s", exc.code, error_message)
            else:
                self.last_summary_mode = "fallback"
                self.last_summary_error = error_message
                logger.warning("OpenAI summary request failed with HTTP %s: %s", exc.code, error_message)
            return None
        except URLError as exc:
            error_message = f"Network error: {exc.reason}"
            if for_ranking:
                self.last_ranking_mode = "fallback"
                self.last_ranking_error = error_message
                logger.warning("OpenAI ranking request network failure: %s", error_message)
            else:
                self.last_summary_mode = "fallback"
                self.last_summary_error = error_message
                logger.warning("OpenAI summary request network failure: %s", error_message)
            return None
        except TimeoutError:
            error_message = "Timeout contacting OpenAI."
            if for_ranking:
                self.last_ranking_mode = "fallback"
                self.last_ranking_error = error_message
                logger.warning("OpenAI ranking request timed out.")
            else:
                self.last_summary_mode = "fallback"
                self.last_summary_error = error_message
                logger.warning("OpenAI summary request timed out.")
            return None
        except json.JSONDecodeError:
            error_message = "Invalid JSON response from OpenAI."
            if for_ranking:
                self.last_ranking_mode = "fallback"
                self.last_ranking_error = error_message
                logger.warning("OpenAI ranking request returned invalid JSON.")
            else:
                self.last_summary_mode = "fallback"
                self.last_summary_error = error_message
                logger.warning("OpenAI summary request returned invalid JSON.")
            return None

        return self._extract_structured_payload(payload_json)

    def _extract_structured_payload(self, response_json: dict) -> dict:
        for item in response_json.get("output", []):
            for content in item.get("content", []):
                if content.get("type") in {"output_text", "text"}:
                    text_value = content.get("text", "")
                    try:
                        return json.loads(text_value)
                    except json.JSONDecodeError:
                        continue

        output_text = response_json.get("output_text")
        if isinstance(output_text, str):
            try:
                return json.loads(output_text)
            except json.JSONDecodeError:
                return {}

        return {}

    def _read_error_body(self, exc: HTTPError) -> str:
        try:
            return exc.read().decode("utf-8").strip()
        except Exception:
            return ""


class GeminiSummaryProvider(BaseSummaryProvider):
    def __init__(self, api_key: str, model: str) -> None:
        self.api_key = api_key
        self.model = model
        self.fallback = MockSummaryProvider()
        self.last_summary_mode = "fallback"
        self.last_summary_error = None
        self.last_ranking_mode = "fallback"
        self.last_ranking_error = None

    def summarize(self, payload: SummaryInput) -> str:
        self.last_summary_error = None
        request_body = {
            "systemInstruction": {
                "parts": [
                    {
                        "text": (
                            "You are a marketplace intelligence analyst. Return JSON only. "
                            "Write a crisp 2-3 sentence summary for an eBay seller. "
                            "Focus on the highest-value listing gap, the strongest competitor signal, "
                            "and the most practical next action using eBay language such as title, item specifics, "
                            "condition detail, buyer trust, and listing clarity."
                        )
                    }
                ]
            },
            "contents": [
                {
                    "role": "user",
                    "parts": [
                        {
                            "text": (
                                f"Keyword: {payload.keyword}\n"
                                f"Marketplace: {payload.marketplace}\n"
                                f"Product family: {payload.family}\n"
                                f"Top gap: {payload.top_gap}\n"
                                f"Strongest competitor signal: {payload.strongest_signal}\n"
                                f"Best next action: {payload.action}\n"
                            )
                        }
                    ],
                }
            ],
            "generationConfig": {
                "responseMimeType": "application/json",
                "responseJsonSchema": {
                    "type": "object",
                    "additionalProperties": False,
                    "properties": {
                        "summary": {
                            "type": "string",
                            "description": "A concise seller-facing summary of the listing opportunity.",
                        }
                    },
                    "required": ["summary"],
                },
            },
        }

        structured = self._post_json(request_body)

        if structured is None:
            return self.fallback.summarize(payload)

        summary = (structured.get("summary") or "").strip()

        if summary:
            self.last_summary_mode = "gemini"
            return summary

        self.last_summary_mode = "fallback"
        self.last_summary_error = "Gemini response did not contain a valid structured summary."
        logger.warning("Gemini summary response missing structured summary.")
        return self.fallback.summarize(payload)

    def rank_gaps(self, payload: RankingInput) -> RankingResult:
        self.last_ranking_error = None
        deterministic = deterministic_ranking_result(payload)
        candidates_text = "\n".join(
            [
                (
                    f"- Title: {candidate.title} | Source: {candidate.evidence_source} | "
                    f"Priority score: {candidate.priority_score} | "
                    f"Why: {candidate.why_it_matters} | "
                    f"Action: {candidate.what_to_add}"
                )
                for candidate in payload.candidates
            ]
        )
        request_body = {
            "systemInstruction": {
                "parts": [
                    {
                        "text": (
                            "You are Ghostfrog's eBay gap-ranking analyst. Return JSON only. "
                            "Pick the best three listing gaps from the supplied candidates. "
                            "Prioritize fields that improve title clarity, item specifics coverage, "
                            "buyer reassurance, and direct eBay comparability."
                        )
                    }
                ]
            },
            "contents": [
                {
                    "role": "user",
                    "parts": [
                        {
                            "text": (
                                f"Keyword: {payload.keyword}\n"
                                f"Marketplace: {payload.marketplace}\n"
                                f"Product family: {payload.family}\n"
                                f"Evidence listings sampled: {payload.evidence_count}\n"
                                f"Schema findings: {payload.schema_count}\n"
                                f"VoC findings: {payload.voc_count}\n"
                                f"Candidates:\n{candidates_text}\n"
                            )
                        }
                    ],
                }
            ],
            "generationConfig": {
                "responseMimeType": "application/json",
                "responseJsonSchema": {
                    "type": "object",
                    "additionalProperties": False,
                    "properties": {
                        "ordered_titles": {
                            "type": "array",
                            "minItems": 1,
                            "maxItems": 3,
                            "items": {"type": "string"},
                        },
                        "ranking_rationale": {"type": "string"},
                        "confidence_score": {"type": "integer", "minimum": 0, "maximum": 100},
                        "quality_score": {"type": "integer", "minimum": 0, "maximum": 100},
                    },
                    "required": [
                        "ordered_titles",
                        "ranking_rationale",
                        "confidence_score",
                        "quality_score",
                    ],
                },
            },
        }

        structured = self._post_json(request_body, for_ranking=True)

        if structured is None:
            return deterministic

        ordered_titles = [
            title.strip()
            for title in structured.get("ordered_titles", [])
            if isinstance(title, str) and title.strip()
        ]

        if not ordered_titles:
            self.last_ranking_mode = "fallback"
            self.last_ranking_error = "Gemini response did not contain any ordered titles."
            logger.warning("Gemini ranking response missing ordered titles.")
            return deterministic

        self.last_ranking_mode = "gemini"
        return RankingResult(
            ordered_titles=ordered_titles[:3],
            ranking_rationale=(structured.get("ranking_rationale") or deterministic.ranking_rationale).strip(),
            confidence_score=clamp_score(structured.get("confidence_score", deterministic.confidence_score)),
            quality_score=clamp_score(structured.get("quality_score", deterministic.quality_score)),
        )

    def provider_label(self) -> str:
        if self.last_summary_mode == "gemini":
            return f"Gemini:{self.model}"

        return f"Gemini:{self.model} (fallback)"

    def provider_status(self) -> str:
        return self.last_summary_mode

    def provider_error(self) -> str | None:
        return self.last_summary_error

    def ranking_provider_label(self) -> str:
        if self.last_ranking_mode == "gemini":
            return f"Gemini:{self.model}"

        return f"Gemini:{self.model} (fallback)"

    def ranking_provider_status(self) -> str:
        return self.last_ranking_mode

    def ranking_provider_error(self) -> str | None:
        return self.last_ranking_error

    def _post_json(self, request_body: dict, for_ranking: bool = False) -> dict | None:
        req = urllib_request.Request(
            f"https://generativelanguage.googleapis.com/v1beta/models/{self.model}:generateContent",
            data=json.dumps(request_body).encode("utf-8"),
            headers={
                "Content-Type": "application/json",
                "x-goog-api-key": self.api_key,
            },
            method="POST",
        )

        try:
            with urllib_request.urlopen(req, timeout=30) as response:
                payload_json = json.loads(response.read().decode("utf-8"))
        except HTTPError as exc:
            error_body = self._read_error_body(exc)
            error_message = f"HTTP {exc.code}: {error_body or exc.reason}"
            if for_ranking:
                self.last_ranking_mode = "fallback"
                self.last_ranking_error = error_message
                logger.warning("Gemini ranking request failed with HTTP %s: %s", exc.code, error_message)
            else:
                self.last_summary_mode = "fallback"
                self.last_summary_error = error_message
                logger.warning("Gemini summary request failed with HTTP %s: %s", exc.code, error_message)
            return None
        except URLError as exc:
            error_message = f"Network error: {exc.reason}"
            if for_ranking:
                self.last_ranking_mode = "fallback"
                self.last_ranking_error = error_message
                logger.warning("Gemini ranking request network failure: %s", error_message)
            else:
                self.last_summary_mode = "fallback"
                self.last_summary_error = error_message
                logger.warning("Gemini summary request network failure: %s", error_message)
            return None
        except TimeoutError:
            error_message = "Timeout contacting Gemini."
            if for_ranking:
                self.last_ranking_mode = "fallback"
                self.last_ranking_error = error_message
                logger.warning("Gemini ranking request timed out.")
            else:
                self.last_summary_mode = "fallback"
                self.last_summary_error = error_message
                logger.warning("Gemini summary request timed out.")
            return None
        except json.JSONDecodeError:
            error_message = "Invalid JSON response from Gemini."
            if for_ranking:
                self.last_ranking_mode = "fallback"
                self.last_ranking_error = error_message
                logger.warning("Gemini ranking request returned invalid JSON.")
            else:
                self.last_summary_mode = "fallback"
                self.last_summary_error = error_message
                logger.warning("Gemini summary request returned invalid JSON.")
            return None

        return self._extract_structured_payload(payload_json)

    def _extract_structured_payload(self, response_json: dict) -> dict:
        for candidate in response_json.get("candidates", []):
            content = candidate.get("content", {})
            for part in content.get("parts", []):
                text_value = part.get("text", "")
                if not text_value:
                    continue
                try:
                    return json.loads(text_value)
                except json.JSONDecodeError:
                    continue

        return {}

    def _read_error_body(self, exc: HTTPError) -> str:
        try:
            return exc.read().decode("utf-8").strip()
        except Exception:
            return ""


def build_summary_provider() -> BaseSummaryProvider:
    provider_name = os.getenv("GHOSTFROG_ENGINE_LLM_PROVIDER", "mock").strip().lower()

    if provider_name in {"", "mock"}:
        return MockSummaryProvider()

    if provider_name == "openai":
        api_key = os.getenv("OPENAI_API_KEY", "").strip()
        model = os.getenv("OPENAI_MODEL", "gpt-5-mini").strip() or "gpt-5-mini"

        if not api_key:
            return PlaceholderExternalSummaryProvider("openai-missing-key")

        return OpenAISummaryProvider(api_key=api_key, model=model)

    if provider_name == "gemini":
        api_key = os.getenv("GEMINI_API_KEY", "").strip()
        model = os.getenv("GEMINI_MODEL", "gemini-2.5-flash").strip() or "gemini-2.5-flash"

        if not api_key:
            return PlaceholderExternalSummaryProvider("gemini-missing-key")

        return GeminiSummaryProvider(api_key=api_key, model=model)

    return PlaceholderExternalSummaryProvider(provider_name)
