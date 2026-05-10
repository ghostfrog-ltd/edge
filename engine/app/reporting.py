from dataclasses import dataclass
from datetime import datetime, timezone
from typing import Optional

from app.llm import GapCandidate, RankingInput, SummaryInput, build_summary_provider


APPLE_FAMILY_RULES = {
    "iphone": {
        "label": "Apple iPhone",
        "missing_attributes": [
            "Exact iPhone model and generation",
            "Storage capacity and network lock status",
            "Battery health percentage in the eBay listing",
        ],
        "competitor_insights": [
            "Stronger eBay iPhone listings put battery health near the top of the title or first description block.",
            "Top eBay competitors call out network lock status, repair history, and parts originality clearly.",
            "Listings with exact storage, colour, and condition details look safer in eBay search results.",
        ],
        "listing_actions": [
            "Lead the eBay title with the exact iPhone model, storage, and colour in a predictable order.",
            "Add battery health, lock status, and repair history into item specifics and the first paragraph.",
            "Use a clear condition note so buyers can compare this iPhone against other eBay offers quickly.",
        ],
    },
    "ipad": {
        "label": "Apple iPad",
        "missing_attributes": [
            "Exact iPad generation",
            "Storage size and Wi-Fi or cellular connectivity",
            "Battery and screen condition detail in the eBay listing",
        ],
        "competitor_insights": [
            "Better eBay iPad listings spell out generation and screen condition in the title or first description block.",
            "Winning eBay sellers make Wi-Fi versus cellular obvious in both the title and item specifics.",
            "Accessory inclusion, charger status, and cosmetic condition reduce buyer hesitation on eBay.",
        ],
        "listing_actions": [
            "Rewrite the eBay title to include iPad generation, storage, and Wi-Fi or cellular status.",
            "Call out battery and display condition early and mirror the same details in item specifics where possible.",
            "List charger, Apple Pencil, case, or missing accessories as separate bullets so buyers trust the listing faster.",
        ],
    },
    "macbook": {
        "label": "Apple MacBook",
        "missing_attributes": [
            "Exact chip or processor, RAM, and model year",
            "SSD size",
            "Battery cycle count or condition detail in the eBay listing",
        ],
        "competitor_insights": [
            "Higher-confidence eBay MacBook listings lead with chip, RAM, SSD, and screen size in one clean title.",
            "Buyers respond better when battery cycles, keyboard layout, and cosmetic grade are explicit on eBay.",
            "MacBook listings that mention charger inclusion and defects clearly look safer in competitive eBay results.",
        ],
        "listing_actions": [
            "Put chip, RAM, SSD size, and screen size into the eBay title in a predictable order.",
            "Add battery cycles, cosmetic notes, and keyboard layout to item specifics and the top of the description.",
            "State charger inclusion and any faults early so the MacBook listing feels low-risk to eBay buyers.",
        ],
    },
    "apple generic": {
        "label": "Apple device",
        "missing_attributes": [
            "Exact Apple model identifier",
            "Storage or spec variant",
            "Condition details that are easy to compare on eBay",
        ],
        "competitor_insights": [
            "Stronger eBay Apple listings reduce ambiguity around the exact device variant.",
            "Listings that state condition, accessories, and limitations plainly feel safer and more premium on eBay.",
            "Apple listings with better item specifics usually win more buyer trust in crowded eBay search results.",
        ],
        "listing_actions": [
            "Clarify the exact Apple device variant in the eBay title and item specifics.",
            "Move condition details, included accessories, and any limitations higher in the description.",
            "Make the listing easier to compare by aligning the title, item specifics, and first paragraph.",
        ],
    },
    "generic": {
        "label": "Marketplace listing",
        "missing_attributes": [
            "Condition detail consistency across the eBay listing",
            "Completeness or included parts count",
            "Clear item specifics coverage",
        ],
        "competitor_insights": [
            "Stronger eBay listings describe completeness earlier in the title and first paragraph.",
            "Higher-confidence competitors repeat key item specifics inside the description so buyers do not have to guess.",
            "Clear condition language tends to stand out more in eBay search and comparison screens.",
        ],
        "listing_actions": [
            "Rewrite the eBay title so the most important qualifier appears earlier.",
            "Expand item specifics to match the strongest competitor pattern for this niche.",
            "Add one explicit completeness statement near the top of the eBay description.",
        ],
    },
}


NOTE_SIGNAL_RULES = [
    (
        ("battery",),
        "Battery health is likely a key eBay trust signal here, so surface it near the top of the listing and in item specifics where available.",
    ),
    (
        ("screen", "display"),
        "Screen condition matters in this niche, so mention scratches, pressure marks, or panel quality early in the eBay description.",
    ),
    (
        ("charger", "cable", "accessory", "pencil", "case"),
        "Accessory clarity matters on eBay, so make included and missing accessories obvious in both bullets and item specifics.",
    ),
    (
        ("cellular", "wifi", "wi-fi", "network", "locked", "unlock"),
        "Connectivity and lock status should be explicit in the eBay title and item specifics so buyers can compare variants quickly.",
    ),
    (
        ("condition", "grade", "scratches", "crack"),
        "Condition grading should be explicit and buyer-friendly so the eBay listing feels safer than nearby competing offers.",
    ),
]


VOC_GAP_TITLES = {
    "battery_cycle_count": "Battery Cycle Count",
    "battery_health": "Battery Health Percentage",
    "original_charger": "Original Charger Status",
    "screen_condition": "Screen Condition Proof",
    "network_lock": "Network Lock Status",
    "accessories": "Accessory Inclusion Clarity",
    "storage_connectivity": "Storage and Connectivity Variant",
    "fitment": "Compatibility Years and Fitment",
    "wear": "Wear and Donor Condition Proof",
}


@dataclass
class ScanPayload:
    keyword: str
    marketplace: str
    scan_type: Optional[str] = None
    category: Optional[str] = None
    ebay_category_id: Optional[str] = None
    competitor_store_url: Optional[str] = None
    schema_audit: Optional[list[dict]] = None
    intelligence_gathering: Optional[list[dict]] = None
    notes: Optional[str] = None
    evidence_count: int = 0


def schema_audit_attributes(schema_audit: Optional[list[dict]]) -> list[str]:
    findings = schema_audit or []

    return [
        f"{finding['aspect_name']} ({finding['requirement_level'].replace('_', ' ')})"
        for finding in findings[:3]
        if finding.get("aspect_name") and finding.get("requirement_level")
    ]


def schema_audit_insight(schema_audit: Optional[list[dict]]) -> Optional[str]:
    findings = schema_audit or []

    if not findings:
        return None

    top = findings[0]
    aspect_name = top.get("aspect_name", "key item specifics")
    coverage = top.get("coverage_percent")

    if coverage is None:
        return f"Top eBay competitors are still underusing the {aspect_name} item specific."

    return f"Top eBay competitors are still underusing the {aspect_name} item specific, with only {coverage}% coverage across the sampled listings."


def schema_audit_action(schema_audit: Optional[list[dict]]) -> Optional[str]:
    findings = schema_audit or []

    if not findings:
        return None

    top = findings[0]
    aspect_name = top.get("aspect_name", "the strongest missing aspect")
    level = top.get("requirement_level", "recommended").replace("_", " ")

    return f"Add {aspect_name} to your eBay item specifics and mirror it in the title or first description block because eBay treats it as {level}."


def voc_insights(intelligence_gathering: Optional[list[dict]]) -> list[str]:
    findings = intelligence_gathering or []

    return [
        finding["headline"]
        for finding in findings[:3]
        if finding.get("headline")
    ]


def voc_action(intelligence_gathering: Optional[list[dict]]) -> Optional[str]:
    findings = intelligence_gathering or []

    if not findings:
        return None

    return findings[0].get("action")


def infer_family(keyword: str, category: Optional[str]) -> str:
    text = f"{keyword} {category or ''}".lower()

    if "iphone" in text:
        return "iphone"
    if "ipad" in text:
        return "ipad"
    if "macbook" in text or "mac book" in text:
        return "macbook"
    if "apple" in text:
        return "apple generic"

    return "generic"


def note_signals(notes: Optional[str]) -> list[str]:
    if not notes:
        return []

    note_text = notes.lower()
    matches: list[str] = []

    for triggers, message in NOTE_SIGNAL_RULES:
        if any(trigger in note_text for trigger in triggers):
            matches.append(message)

    return matches


def gap_title_from_intelligence(finding: dict) -> str:
    signal_key = (finding.get("signal_key") or "").strip().lower()

    if signal_key in VOC_GAP_TITLES:
        return VOC_GAP_TITLES[signal_key]

    action = (finding.get("action") or "").strip().rstrip(".")
    headline = (finding.get("headline") or "").strip().rstrip(".")

    if action:
        return action

    if headline:
        return headline

    return "Buyer reassurance gap"


def schema_candidate_score(finding: dict) -> tuple[int, str]:
    level = (finding.get("requirement_level") or "recommended").lower()
    coverage = int(finding.get("coverage_percent") or 0)
    missing_count = int(finding.get("missing_count") or 0)
    level_weight = {
        "required": 20,
        "expected_required": 15,
        "recommended": 10,
    }.get(level, 8)
    score = 62 + level_weight + min(18, max(0, 100 - coverage) // 4) + min(10, missing_count * 2)
    reason = (
        f"eBay already signals this as {level.replace('_', ' ')}, and top listings still leave a large coverage gap."
    )

    return min(99, score), reason


def voc_candidate_score(finding: dict) -> tuple[int, str]:
    mention_count = int(finding.get("mention_count") or 0)
    coverage = int(finding.get("coverage_percent") or 0)
    score = 60 + min(16, mention_count * 4) + min(18, coverage // 4)
    reason = (
        "Competitor copy keeps repeating this reassurance point, which is a strong sign buyers care about it before converting."
    )

    return min(98, score), reason


def blueprint_candidate_score(index: int) -> tuple[int, str]:
    score = 58 - (index * 4)
    reason = "This still looks like a consistent eBay comparison gap even after the stronger evidence-backed opportunities."

    return max(44, score), reason


def build_gap_candidates(
    schema_audit: Optional[list[dict]],
    intelligence_gathering: Optional[list[dict]],
    blueprint: dict,
) -> list[GapCandidate]:
    suggestions: list[GapCandidate] = []

    for finding in schema_audit or []:
        title = finding.get("aspect_name")

        if not title:
            continue

        score, reason = schema_candidate_score(finding)
        suggestions.append(
            GapCandidate(
                title=title,
                why_it_matters=finding.get(
                    "headline",
                    "eBay is signalling that this field matters more than most competitors are showing.",
                ),
                what_to_add=f"Add {title} to your item specifics and echo it near the top of the title or first description block.",
                evidence_source="Schema audit",
                priority_score=score,
                ranking_reason=reason,
            )
        )

    for finding in intelligence_gathering or []:
        action = finding.get("action")
        headline = finding.get("headline")

        if not action or not headline:
            continue

        score, reason = voc_candidate_score(finding)
        suggestions.append(
            GapCandidate(
                title=gap_title_from_intelligence(finding),
                why_it_matters=headline,
                what_to_add=action,
                evidence_source="VoC intelligence",
                priority_score=score,
                ranking_reason=reason,
            )
        )

    for index, attribute in enumerate(blueprint.get("missing_attributes", [])):
        score, reason = blueprint_candidate_score(index)
        suggestions.append(
            GapCandidate(
                title=attribute,
                why_it_matters=f"This still shows up as a weak spot across the strongest {blueprint['label']} listings on eBay.",
                what_to_add=f"Make {attribute.lower()} explicit in the listing title, item specifics, and opening description block.",
                evidence_source="Gap analysis",
                priority_score=score,
                ranking_reason=reason,
            )
        )

    deduped: list[GapCandidate] = []
    seen: set[str] = set()

    for suggestion in sorted(
        suggestions,
        key=lambda candidate: (-candidate.priority_score, candidate.title.lower()),
    ):
        key = suggestion.title.strip().lower()

        if key in seen:
            continue

        seen.add(key)
        deduped.append(suggestion)

    return deduped


def inferred_evidence_count(payload: ScanPayload) -> int:
    if payload.evidence_count > 0:
        return payload.evidence_count

    for finding in payload.schema_audit or []:
        checked = int(finding.get("checked_listing_count") or 0)
        if checked > 0:
            return checked

    for finding in payload.intelligence_gathering or []:
        checked = int(finding.get("checked_listing_count") or 0)
        if checked > 0:
            return checked

    return 0


def rank_missing_three(
    candidates: list[GapCandidate],
    payload: ScanPayload,
    family_label: str,
):
    provider = build_summary_provider()
    ranking_result = provider.rank_gaps(
        RankingInput(
            keyword=payload.keyword,
            marketplace=payload.marketplace,
            family=family_label,
            evidence_count=inferred_evidence_count(payload),
            schema_count=len(payload.schema_audit or []),
            voc_count=len(payload.intelligence_gathering or []),
            candidates=candidates[:6],
        )
    )
    candidate_map = {candidate.title.strip().lower(): candidate for candidate in candidates}

    ranked_candidates: list[GapCandidate] = []
    seen: set[str] = set()

    for title in ranking_result.ordered_titles:
        key = title.strip().lower()
        candidate = candidate_map.get(key)

        if not candidate or key in seen:
            continue

        ranked_candidates.append(candidate)
        seen.add(key)

    for candidate in sorted(candidates, key=lambda item: (-item.priority_score, item.title.lower())):
        key = candidate.title.strip().lower()
        if key in seen:
            continue

        ranked_candidates.append(candidate)
        seen.add(key)

        if len(ranked_candidates) >= 3:
            break

    missing_three = [
        {
            "title": candidate.title,
            "why_it_matters": candidate.why_it_matters,
            "what_to_add": candidate.what_to_add,
            "evidence_source": candidate.evidence_source,
            "priority_score": candidate.priority_score,
            "ranking_reason": candidate.ranking_reason,
        }
        for candidate in ranked_candidates[:3]
    ]

    return provider, ranking_result, missing_three


def build_report_payload(payload: ScanPayload, engine_job_id: str) -> dict:
    keyword_slug = payload.keyword.lower()

    if "fail" in keyword_slug:
        return {
            "status": "failed",
            "engine_job_id": engine_job_id,
            "failure_reason": "Simulated engine failure for keywords containing 'fail'.",
        }

    family_key = infer_family(payload.keyword, payload.category)
    blueprint = APPLE_FAMILY_RULES[family_key].copy()
    schema_attributes = schema_audit_attributes(payload.schema_audit)
    schema_insight = schema_audit_insight(payload.schema_audit)
    schema_action = schema_audit_action(payload.schema_audit)
    voice_of_customer = voc_insights(payload.intelligence_gathering)
    voice_action = voc_action(payload.intelligence_gathering)

    if schema_attributes:
        blueprint["missing_attributes"] = schema_attributes + blueprint["missing_attributes"]
        blueprint["missing_attributes"] = blueprint["missing_attributes"][:3]

    if schema_insight:
        blueprint["competitor_insights"] = [schema_insight, *blueprint["competitor_insights"]]

    if schema_action:
        blueprint["listing_actions"] = [schema_action, *blueprint["listing_actions"]]

    if voice_of_customer:
        blueprint["competitor_insights"] = [*voice_of_customer, *blueprint["competitor_insights"]]

    if voice_action:
        blueprint["listing_actions"] = [voice_action, *blueprint["listing_actions"]]

    signals = note_signals(payload.notes)

    if signals:
        blueprint["competitor_insights"] = [*blueprint["competitor_insights"], *signals[:2]]

    gap_candidates = build_gap_candidates(payload.schema_audit, payload.intelligence_gathering, blueprint)
    provider, ranking_result, missing_three = rank_missing_three(gap_candidates, payload, blueprint["label"])

    summary = provider.summarize(
        SummaryInput(
            keyword=payload.keyword,
            marketplace=payload.marketplace,
            family=blueprint["label"],
            top_gap=(missing_three[0]["title"] if missing_three else blueprint["missing_attributes"][0]),
            strongest_signal=blueprint["competitor_insights"][0],
            action=blueprint["listing_actions"][0],
        )
    )

    base_quality_score = ranking_result.quality_score

    return {
        "status": "completed",
        "engine_job_id": engine_job_id,
        "summary": summary,
        "missing_three": missing_three,
        "missing_attributes": blueprint["missing_attributes"],
        "schema_audit": payload.schema_audit or [],
        "voc_insights": voice_of_customer,
        "competitor_insights": blueprint["competitor_insights"],
        "listing_actions": blueprint["listing_actions"],
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "report_meta": {
            "family": blueprint["label"],
            "llm_provider": provider.provider_label(),
            "llm_status": provider.provider_status(),
            "llm_error": provider.provider_error(),
            "ranking_provider": provider.ranking_provider_label(),
            "ranking_status": provider.ranking_provider_status(),
            "ranking_error": provider.ranking_provider_error(),
            "ranking_rationale": ranking_result.ranking_rationale,
            "confidence_score": ranking_result.confidence_score,
            "quality_score": base_quality_score,
            "quality_loop_score": base_quality_score,
            "feedback_loop_state": "awaiting_customer_feedback",
            "marketplace_focus": "ebay",
            "evidence_count": inferred_evidence_count(payload),
            "candidate_count": len(gap_candidates),
            "schema_audit_count": len(payload.schema_audit or []),
            "voc_signal_count": len(payload.intelligence_gathering or []),
            "missing_three_count": len(missing_three),
        },
    }
