# Decision Log

This file tracks active decisions so the project keeps its context over time.

## How To Use

- Add a new entry whenever we make a meaningful product, technical, pricing, or operations decision.
- Prefer short notes over long narratives.
- Update an existing entry when a decision is revised or replaced.
- Link to the relevant file or area of the app when helpful.

## Entry Template

```md
## YYYY-MM-DD - Decision title

- Status: proposed | active | replaced
- Area: product | engineering | design | ops | pricing
- Decision: What we chose.
- Why: Why we chose it.
- Impact: What changes because of this.
- Related: paths, routes, services, tickets, or notes
```

## Active Decisions

## 2026-05-10 - Local development uses DDEV on OrbStack

- Status: active
- Area: ops
- Decision: The local development environment is now standardized on DDEV running on OrbStack instead of Laravel Herd.
- Why: This gives the Laravel app, database, Mailpit, and Python engine a reproducible multi-service local stack.
- Impact: Local URLs, startup commands, PHP runtime, and engine wiring now follow the DDEV setup.
- Related: `.ddev/`, `.env.example`, `README.md`, `engine/README.md`

## 2026-05-10 - Local DDEV PHP version is 8.4

- Status: active
- Area: engineering
- Decision: The DDEV web container uses PHP 8.4.
- Why: The current installed Composer dependency set requires PHP 8.4 at runtime.
- Impact: Local development and container validation should assume PHP 8.4 until the dependency constraint changes.
- Related: `.ddev/config.yaml`, `composer.lock`

## 2026-05-10 - Engine runs as a DDEV sidecar service

- Status: active
- Area: engineering
- Decision: The FastAPI engine runs inside DDEV as its own service and Laravel talks to it over the internal hostname `engine`.
- Why: This keeps the app and analysis service in one local environment and makes the queue-to-engine loop easier to validate.
- Impact: `GHOSTFROG_ENGINE_URL` points at `http://engine:8001` in local setup.
- Related: `.ddev/docker-compose.engine.yaml`, `config/services.php`, `engine/app/main.py`

## 2026-05-10 - Internal service traffic bypasses OrbStack proxy variables

- Status: active
- Area: engineering
- Decision: Laravel-to-engine and engine-to-web internal requests explicitly bypass inherited proxy environment variables.
- Why: OrbStack proxy settings caused internal container-to-container requests to fail with `502 Bad Gateway`.
- Impact: Local service-to-service calls should be more reliable inside DDEV.
- Related: `app/Services/Engine/GhostfrogEngineClient.php`, `engine/app/main.py`

## 2026-05-10 - Product positioning focuses on eBay market intelligence

- Status: active
- Area: product
- Decision: Ghostfrog Ebay Edge is positioned as a market-intelligence and listing-gap product for eBay sellers, not a compliance-advice tool.
- Why: This keeps the offer clearer and lower-liability while still promising commercial value.
- Impact: Messaging, report design, and roadmap decisions should reinforce actionable listing intelligence.
- Related: `ghostfrog-ebay-edge.md`, `app/Http/Controllers/BriefController.php`, `resources/views/pricing.blade.php`

## 2026-05-10 - Monetization uses subscriptions plus scan-credit top-ups

- Status: active
- Area: pricing
- Decision: The commercial model uses monthly plans plus one-off credit top-ups.
- Why: This fits uneven seller usage better than subscription-only pricing and makes heavier users easier to monetize.
- Impact: One successful scan consumes one credit, with reservation and refund behavior handled in the billing and scan pipeline.
- Related: `config/billing.php`, `app/Services/Billing/TeamBillingFulfillmentService.php`, `app/Services/Scans/ScanPipelineService.php`

## Open Questions

## 2026-05-10 - How differentiated are the reports in real customer hands?

- Status: proposed
- Area: product
- Decision: We still need to prove whether scan outputs feel specific and valuable enough to drive repeat paid usage.
- Why: Profitability depends more on retention and report quality than on the app shell itself.
- Impact: Customer testing, category focus, and report QA should stay high priority.
- Related: `engine/app/reporting.py`, `app/Services/Ebay/`, `resources/views/scans/show.blade.php`

## 2026-05-10 - First remote hosting box uses a simple Ubuntu VPS stack

- Status: active
- Area: ops
- Decision: The first remote server uses a simple Ubuntu VPS with Nginx, PHP-FPM, MariaDB, per-site vhosts, and systemd services where needed instead of a heavier container control plane.
- Why: Early traffic is expected to be low across a small number of sites, so a simple stack is cheaper and easier to debug.
- Impact: Each app can be hosted under `/var/www`, with app-specific Nginx vhosts and optional queue/worker services.
- Related: `docs/deployment/production-checklist.md`, `deploy/`, remote host `91.99.113.93`

## 2026-05-10 - Fuzzynode domain and TLS are live on the first VPS

- Status: active
- Area: ops
- Decision: `fuzzynode.com` now points at the first VPS, `www.fuzzynode.com` is the preferred public host, and TLS is handled with Let's Encrypt on the origin.
- Why: This gives us a real public entrypoint and a stable production base before the Ghostfrog app code is deployed.
- Impact: The server is ready to swap from the holding page to the live Laravel app as soon as the repo is pulled and configured.
- Related: remote host `91.99.113.93`, `/etc/nginx/sites-available/fuzzynode.com.conf`, Let's Encrypt cert for `fuzzynode.com`

## 2026-05-10 - Production code will be pulled from GitHub with a server deploy key

- Status: active
- Area: ops
- Decision: The production server will access the GitHub repo using a dedicated SSH deploy key stored under the `deploy` user.
- Why: This keeps deployment simple and avoids copying app code to the server manually.
- Impact: App deployment should use `git clone` and later `git pull` from the server.
- Related: `/home/deploy/.ssh/id_github_deploy`, GitHub repo deploy keys
