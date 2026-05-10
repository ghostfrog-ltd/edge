# Action Plan

This file is the working project action plan for Ghostfrog Ebay Edge.

It complements [DECISION_LOG.md](/Volumes/Bob/www/ghostfrog-ebay-edge/DECISION_LOG.md):

- `DECISION_LOG.md` explains what we decided and why.
- `ACTION_PLAN.md` tracks what is done, what is next, and what still needs proving.

The public-facing version of this lives in the app's "Tasks and delivery status" section on `/how-it-works`, but this file is the repo-side source we can update as we build.

## Status Keys

- `Complete`: delivered and working in the current build
- `In Progress`: partially delivered or still being tightened
- `Next`: important near-term work
- `Later`: valuable, but not on the immediate critical path

## Current Snapshot

- Product: eBay market-intelligence SaaS for sellers
- Commercial model: subscriptions plus scan-credit top-ups
- Local environment: DDEV on OrbStack
- Current commercial risk: proving report quality and repeat usage

## Delivered

### Core app chassis

- `Complete` Laravel app scaffold, branding, public pages, and authenticated workspace
- `Complete` Jetstream auth with Teams enabled
- `Complete` Customer dashboard and navigation shell
- `Complete` Admin area for platform operations

### Credits and billing

- `Complete` Credit ledger model and reservation/refund loop
- `Complete` Subscription and top-up catalog configuration
- `Complete` Stripe-oriented fulfillment flow for subscriptions and credit boosts
- `Complete` Public pricing page and workspace billing screen

### Scan workflow

- `Complete` Scan intake flow
- `Complete` Scan history and scan detail views
- `Complete` Queue-to-engine dispatch flow
- `Complete` Failed-scan refund behavior

### Reports and delivery

- `Complete` Structured scan report persistence
- `Complete` PDF export for completed reports
- `Complete` Inbox notification flow
- `Complete` Email notification flow

### Engine and intelligence pipeline

- `Complete` FastAPI bridge and callback contract
- `Complete` Worker handoff from Laravel to Python
- `Complete` Live eBay evidence collection
- `Complete` Schema audit layer
- `Complete` Buyer-friction / intelligence-gathering layer
- `Complete` "Missing 3" synthesis output
- `Complete` LLM-assisted report ranking/summarization path
- `Complete` Worker health and monitoring hooks

### Environment and delivery operations

- `Complete` DDEV + OrbStack local setup
- `Complete` Python engine sidecar in local development
- `Complete` Internal service-to-service networking fixes for OrbStack proxy behavior
- `Complete` Production deployment templates in `deploy/` and `docs/deployment/`
- `Complete` First Ubuntu VPS bootstrap with Nginx, PHP 8.4, MariaDB, swap, firewall, fail2ban, deploy user, and multi-site Nginx template

## In Progress

### Product proof

- `In Progress` Proving that reports are specific, useful, and repeat-worthy for real sellers
- `In Progress` Tightening differentiation so output feels stronger than generic AI advice
- `In Progress` Aligning README and internal docs with the newer engine/billing reality of the project

## Next

### Commercial validation

- `Next` Define the first narrow customer segment to win
- `Next` Run human trials with real seller keywords and real listing decisions
- `Next` Capture examples where a report clearly changed a listing outcome
- `Next` Identify which plan and customer type has the best retention potential

### Report quality

- `Next` Review output quality across a small set of categories and reject weak/general reports
- `Next` Add clearer evidence-backed reasoning inside reports so users trust the recommendations
- `Next` Decide which categories Ghostfrog should intentionally be best at first

### Go-to-market

- `Next` Build a concrete path to the first 10 to 20 paying customers
- `Next` Decide whether the primary wedge is solo power sellers, agencies, or team-based operators
- `Next` Turn strong example reports into marketing proof

### Remote launch

- `Next` Point the production domain and add the first real Nginx vhost
- `Complete` Point `fuzzynode.com` at the first VPS and enable Let's Encrypt TLS
- `Complete` Prepare a GitHub deploy-key flow for pulling production code onto the server
- `Next` Deploy the Ghostfrog codebase onto the VPS
- `Next` Create the production database and `.env`
- `Next` Enable the Laravel queue worker, scheduler, and Python engine services for Ghostfrog
- `Next` Add SSL and validate the live app end to end

## Later

### Product expansion

- `Later` Reuse the SaaS factory pattern for additional marketplace or business-intelligence products
- `Later` Expand the engine into more verticals only after Ghostfrog has real retention

### Operational maturity

- `Later` Add deeper analytics around scan usage, retention, and report performance
- `Later` Add stronger admin tooling for support workflows and billing investigation

## Notes

- The current app UI already exposes a roadmap-style status view at `/how-it-works`.
- When the app roadmap and this file diverge, update both together.
- Prefer editing this file when priorities change, and recording the reason in `DECISION_LOG.md`.
