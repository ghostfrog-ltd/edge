import argparse
import json
import uuid

from app.reporting import ScanPayload, build_report_payload


def main() -> None:
    parser = argparse.ArgumentParser(description="Preview a Ghostfrog engine report.")
    parser.add_argument("--keyword", required=True)
    parser.add_argument("--marketplace", default="ebay-uk")
    parser.add_argument("--category", default=None)
    parser.add_argument("--notes", default=None)
    args = parser.parse_args()

    payload = ScanPayload(
        keyword=args.keyword,
        marketplace=args.marketplace,
        category=args.category,
        notes=args.notes,
    )

    report = build_report_payload(payload, engine_job_id=f"preview-{uuid.uuid4()}")
    print(json.dumps(report, indent=2))


if __name__ == "__main__":
    main()
