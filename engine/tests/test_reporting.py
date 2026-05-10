import unittest
import os

from app.reporting import ScanPayload, build_report_payload, infer_family


class ReportingTest(unittest.TestCase):
    def setUp(self) -> None:
        os.environ["GHOSTFROG_ENGINE_LLM_PROVIDER"] = "mock"

    def test_infers_ipad_family(self) -> None:
        self.assertEqual(infer_family("Apple iPad Air 5", "Tablets"), "ipad")

    def test_builds_apple_specific_report(self) -> None:
        payload = ScanPayload(
            keyword="Apple iPad Pro 11",
            marketplace="ebay-uk",
            category="Tablets",
            schema_audit=[
                {
                    "aspect_name": "Battery Cycle Count",
                    "requirement_level": "recommended",
                    "coverage_percent": 10,
                }
            ],
            intelligence_gathering=[
                {
                    "headline": "Battery cycle count keeps showing up in competitor detail text, which suggests buyers are actively looking for a stronger longevity signal.",
                    "action": "Add battery cycle count or the closest battery-health equivalent high in the listing so buyers do not have to ask.",
                }
            ],
            notes="Focus on battery and screen condition.",
        )

        report = build_report_payload(payload, "gf-preview")

        self.assertEqual(report["status"], "completed")
        self.assertEqual(len(report["missing_three"]), 3)
        self.assertEqual(report["missing_three"][0]["evidence_source"], "Schema audit")
        self.assertGreaterEqual(report["missing_three"][0]["priority_score"], 70)
        self.assertIn("Battery Cycle Count (recommended)", report["missing_attributes"])
        self.assertIn("eBay", report["summary"])
        self.assertTrue(any("battery" in insight.lower() for insight in report["competitor_insights"]))
        self.assertTrue(any("battery cycle count" in insight.lower() for insight in report["voc_insights"]))
        self.assertFalse(any("Focus on battery and screen condition." in insight for insight in report["competitor_insights"]))
        self.assertEqual(report["schema_audit"][0]["aspect_name"], "Battery Cycle Count")
        self.assertIn("llm_status", report["report_meta"])
        self.assertIn("llm_error", report["report_meta"])
        self.assertIn("ranking_status", report["report_meta"])
        self.assertIn("ranking_rationale", report["report_meta"])
        self.assertIn("quality_score", report["report_meta"])
        self.assertIn("confidence_score", report["report_meta"])
        self.assertEqual(report["report_meta"]["marketplace_focus"], "ebay")

    def test_fail_keyword_triggers_failure_payload(self) -> None:
        payload = ScanPayload(keyword="fail ipad scan", marketplace="ebay-uk")
        report = build_report_payload(payload, "gf-preview")

        self.assertEqual(report["status"], "failed")


if __name__ == "__main__":
    unittest.main()
