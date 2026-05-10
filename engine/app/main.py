import json
import os
import ssl
import time
import uuid
from datetime import datetime, timezone
from threading import Lock, Thread
from typing import Optional
from urllib import request as urllib_request
from urllib.parse import urlparse

from fastapi import FastAPI, Header, HTTPException
from pydantic import BaseModel, Field

from app.env import load_project_env
from app.reporting import ScanPayload, build_report_payload


load_project_env()

app = FastAPI(title="Ghostfrog Engine", version="0.1.0")

ENGINE_SHARED_SECRET = os.getenv("GHOSTFROG_ENGINE_SHARED_SECRET", "ghostfrog-engine-secret")
CALLBACK_SECRET = os.getenv("GHOSTFROG_ENGINE_CALLBACK_SECRET", "ghostfrog-callback-secret")
SIMULATED_DELAY_SECONDS = float(os.getenv("GHOSTFROG_ENGINE_SIMULATED_DELAY_SECONDS", "1.5"))
CALLBACK_TIMEOUT_SECONDS = float(os.getenv("GHOSTFROG_ENGINE_CALLBACK_TIMEOUT_SECONDS", "10"))
ENGINE_STARTED_AT = datetime.now(timezone.utc)
ENGINE_STATE_LOCK = Lock()
ENGINE_STATE = {
    "dispatches_total": 0,
    "active_jobs": 0,
    "callbacks_completed": 0,
    "callbacks_failed": 0,
    "last_dispatch_at": None,
    "last_completed_at": None,
    "last_failed_at": None,
    "last_job_duration_ms": None,
    "last_scan_id": None,
    "last_callback_error": None,
}


def utc_now_iso() -> str:
    return datetime.now(timezone.utc).isoformat()


def update_engine_state(**updates) -> None:
    with ENGINE_STATE_LOCK:
        ENGINE_STATE.update(updates)


def bump_engine_state(field: str, amount: int = 1) -> int:
    with ENGINE_STATE_LOCK:
        ENGINE_STATE[field] = int(ENGINE_STATE.get(field, 0)) + amount

        return int(ENGINE_STATE[field])


class DispatchScanRequest(BaseModel):
    scan_id: int
    team_id: int
    user_id: int
    keyword: str
    marketplace: str
    scan_type: Optional[str] = None
    category: Optional[str] = None
    ebay_category_id: Optional[str] = None
    competitor_store_url: Optional[str] = None
    schema_audit: list[dict] = Field(default_factory=list)
    intelligence_gathering: list[dict] = Field(default_factory=list)
    notes: Optional[str] = None
    evidence_count: int = 0
    callback_url: str = Field(..., min_length=1)

def send_callback(callback_url: str, payload: dict) -> None:
    body = json.dumps(payload).encode("utf-8")
    req = urllib_request.Request(
        callback_url,
        data=body,
        headers={
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-Ghostfrog-Callback-Secret": CALLBACK_SECRET,
        },
        method="POST",
    )

    parsed_url = urlparse(callback_url)
    insecure_local_hosts = {"127.0.0.1", "localhost"}
    ssl_context = None

    if parsed_url.scheme == "https" and (
        parsed_url.hostname in insecure_local_hosts or (parsed_url.hostname or "").endswith(".test")
    ):
        ssl_context = ssl._create_unverified_context()

    handlers = [urllib_request.ProxyHandler({})]

    if ssl_context is not None:
        handlers.append(urllib_request.HTTPSHandler(context=ssl_context))

    opener = urllib_request.build_opener(*handlers)

    with opener.open(req, timeout=CALLBACK_TIMEOUT_SECONDS) as response:
        response.read()


def process_scan_async(payload: DispatchScanRequest, engine_job_id: str) -> None:
    started_at = time.monotonic()
    time.sleep(SIMULATED_DELAY_SECONDS)
    try:
        callback_payload = build_report_payload(
            ScanPayload(
                keyword=payload.keyword,
                marketplace=payload.marketplace,
                scan_type=payload.scan_type,
                category=payload.category,
                ebay_category_id=payload.ebay_category_id,
                competitor_store_url=payload.competitor_store_url,
                schema_audit=payload.schema_audit,
                intelligence_gathering=payload.intelligence_gathering,
                notes=payload.notes,
                evidence_count=payload.evidence_count,
            ),
            engine_job_id,
        )
        send_callback(payload.callback_url, callback_payload)
        update_engine_state(
            callbacks_completed=bump_engine_state("callbacks_completed"),
            last_completed_at=utc_now_iso(),
            last_job_duration_ms=int((time.monotonic() - started_at) * 1000),
            last_scan_id=payload.scan_id,
            last_callback_error=None,
        )
    except Exception as exc:
        update_engine_state(
            callbacks_failed=bump_engine_state("callbacks_failed"),
            last_failed_at=utc_now_iso(),
            last_job_duration_ms=int((time.monotonic() - started_at) * 1000),
            last_scan_id=payload.scan_id,
            last_callback_error=str(exc),
        )
        raise
    finally:
        remaining_jobs = max(0, bump_engine_state("active_jobs", -1))
        update_engine_state(active_jobs=remaining_jobs)


@app.get("/health")
def health() -> dict:
    with ENGINE_STATE_LOCK:
        state = ENGINE_STATE.copy()

    provider_name = os.getenv("GHOSTFROG_ENGINE_LLM_PROVIDER", "mock").strip().lower() or "mock"
    configured_model = {
        "openai": os.getenv("OPENAI_MODEL", "gpt-5-mini").strip() or "gpt-5-mini",
        "gemini": os.getenv("GEMINI_MODEL", "gemini-2.5-flash").strip() or "gemini-2.5-flash",
    }.get(provider_name, "deterministic")

    return {
        "ok": True,
        "version": "0.2.0",
        "configured_provider": provider_name,
        "configured_model": configured_model,
        "simulated_delay_seconds": SIMULATED_DELAY_SECONDS,
        "uptime_seconds": int((datetime.now(timezone.utc) - ENGINE_STARTED_AT).total_seconds()),
        **state,
    }


@app.post("/api/v1/scans/dispatch")
def dispatch_scan(
    payload: DispatchScanRequest,
    x_ghostfrog_engine_secret: Optional[str] = Header(default=None),
) -> dict:
    if x_ghostfrog_engine_secret != ENGINE_SHARED_SECRET:
        raise HTTPException(status_code=401, detail="Invalid engine secret.")

    engine_job_id = f"gf-{uuid.uuid4()}"
    update_engine_state(
        dispatches_total=bump_engine_state("dispatches_total"),
        active_jobs=bump_engine_state("active_jobs"),
        last_dispatch_at=utc_now_iso(),
        last_scan_id=payload.scan_id,
    )

    worker = Thread(target=process_scan_async, args=(payload, engine_job_id), daemon=True)
    worker.start()

    return {
        "accepted": True,
        "status": "processing",
        "engine_job_id": engine_job_id,
    }
