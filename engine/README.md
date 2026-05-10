# Ghostfrog Engine

This folder contains the Python analysis bridge for Ghostfrog Ebay Edge.

Right now it is a FastAPI bridge with a deterministic report generator. It accepts scan jobs from Laravel, builds a structured report, and posts a callback back to the website. Apple-family keywords such as `iPad`, `iPhone`, and `MacBook` now produce richer mock reports so we can tune report quality before turning on a real LLM provider.

## Current Responsibilities

- receive scan dispatch requests from Laravel
- validate the shared secret
- create an engine job id
- simulate async processing
- generate richer Apple-focused report payloads
- callback into Laravel with either:
  - `completed`
  - `failed`

## Report Preview

You can preview the report generator without the website:

```bash
cd engine
source .venv/bin/activate
python -m app.preview_report --keyword "Apple iPad Pro 11" --marketplace ebay-uk --category Tablets --notes "Focus on battery health and screen condition."
```

## Python Tests

```bash
cd engine
source .venv/bin/activate
python -m unittest discover -s tests
```

## Run Locally

```bash
ddev start
ddev exec --service=engine python -m unittest discover -s tests -t /var/www/html/engine
```

## Environment Variables

- `GHOSTFROG_ENGINE_SHARED_SECRET`
- `GHOSTFROG_ENGINE_CALLBACK_SECRET`
- `GHOSTFROG_ENGINE_SIMULATED_DELAY_SECONDS`
- `GHOSTFROG_ENGINE_CALLBACK_TIMEOUT_SECONDS`
- `GHOSTFROG_ENGINE_LLM_PROVIDER`
- `OPENAI_API_KEY`
- `OPENAI_MODEL`
- `GEMINI_API_KEY`
- `GEMINI_MODEL`

The engine will automatically read the project root `.env` file, so in local development it can share the same LLM keys and provider settings as Laravel without extra shell exports.

`GHOSTFROG_ENGINE_LLM_PROVIDER` defaults to `mock`. The provider hook exists now so we can swap in a real LLM integration later, but this build intentionally keeps report generation deterministic until we are happy with the schema and report quality.

To turn on OpenAI-backed summaries:

```bash
export GHOSTFROG_ENGINE_LLM_PROVIDER=openai
export OPENAI_API_KEY=...
export OPENAI_MODEL=gpt-5-mini
```

The engine uses the OpenAI Responses API and asks for a structured JSON response containing the report summary. If the API call fails, the engine falls back to the deterministic mock summary so scan delivery does not break during development.

To turn on Gemini-backed summaries:

```bash
export GHOSTFROG_ENGINE_LLM_PROVIDER=gemini
export GEMINI_API_KEY=...
export GEMINI_MODEL=gemini-2.5-flash
```

The Gemini integration uses the official `generateContent` API with structured JSON output. If that API call fails, the engine falls back to the deterministic mock summary so scan delivery does not break during development.

With the bundled DDEV service, Laravel should point `GHOSTFROG_ENGINE_URL` to:

- `http://engine:8001`

If you are running the engine outside DDEV, keep using:

- `http://127.0.0.1:8001`
