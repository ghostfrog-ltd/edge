# Ghostfrog Ebay Edge

Ghostfrog Ebay Edge is an early-stage SaaS product for eBay sellers. It is designed to scan a keyword or niche, compare competitor listings, and return a structured report showing missing attributes, weak spots, and practical listing actions.

This repository currently contains the Laravel application, public marketing pages, customer workspace, and operator admin area. The Python analysis service is planned next and will plug into the scan pipeline that already exists in the app shell.

## Project Memory

- [DECISION_LOG.md](/Volumes/Bob/www/ghostfrog-ebay-edge/DECISION_LOG.md): the running record of key product, technical, pricing, and ops decisions
- [ACTION_PLAN.md](/Volumes/Bob/www/ghostfrog-ebay-edge/ACTION_PLAN.md): the repo-side action plan and delivery status tracker

## Current Product Shape

- Public landing page with branding, footer, SEO metadata, sitemap, `robots.txt`, and `llms.txt`
- Public information pages:
  - `How It Works`
  - `Terms and Conditions`
  - `Privacy Policy`
  - `Contact`
- Authentication with Laravel Jetstream, Livewire, and Teams
- Customer dashboard
- Scan intake flow
- Scan history and scan detail pages
- Credit ledger model and starter credit flow
- Admin area with:
  - dashboard
  - roadmap
  - users
  - teams
  - scans
  - credits
  - products
  - plans
- Light and dark theme support
- Custom Ghostfrog branding and favicon

## Stack

- PHP 8.2+
- Laravel 12
- Jetstream with Livewire and Teams
- Tailwind CSS
- Vite
- DDEV on OrbStack for local development
- MariaDB and Mailpit via DDEV
- Planned Python/FastAPI analysis service

## Local Development

This project is set up for DDEV running on OrbStack.

### 1. Start OrbStack and DDEV

Open OrbStack on macOS, then make sure Docker is pointing at it:

```bash
docker context use orbstack
ddev start
```

### 2. Install dependencies

```bash
ddev composer install
ddev npm install
```

### 3. Create environment file

```bash
cp .env.example .env
ddev php artisan key:generate
```

### 4. Prepare the database

```bash
ddev php artisan migrate
```

### 5. Run the app

Open:

- [https://ghostfrog-ebay-edge.ddev.site](https://ghostfrog-ebay-edge.ddev.site)

For day-to-day development, use separate terminals for Vite and the queue listener:

```bash
ddev vite
ddev queue
```

The FastAPI engine runs as a DDEV sidecar service and is reachable from Laravel at:

- `http://engine:8001`

You can check its health with:

```bash
ddev exec curl -s http://engine:8001/health
```

When `ddev vite` is running, Vite HMR is exposed through:

- [https://ghostfrog-ebay-edge.ddev.site:5173](https://ghostfrog-ebay-edge.ddev.site:5173)

## Useful Commands

```bash
ddev php artisan test
ddev npm run build
ddev php artisan migrate
ddev php artisan optimize:clear
ddev exec --service=engine python -m unittest discover -s tests -t /var/www/html/engine
```

## Important App Areas

- Public homepage: `/`
- How it works: `/how-it-works`
- Dashboard: `/dashboard`
- New scan: `/scans/new`
- Scan history: `/scans`
- Admin dashboard: `/admin`
- Admin roadmap: `/admin/roadmap`

## Admin Notes

The app has a separate platform admin concept in addition to Jetstream team roles.

- Jetstream roles manage a user inside their team/workspace
- platform admin is for operating the whole Ghostfrog app

The current admin area is intended to support:

- customer support
- scan operations
- credit visibility
- roadmap tracking
- future billing and worker monitoring

## What Is Still Missing

These are the major next steps after the current Laravel shell:

- real billing and plan enforcement
- credit top-ups
- Python/FastAPI bridge
- queue-driven scan processing
- report generation from Python back into Laravel
- LLM-assisted analysis over structured marketplace evidence
- notifications when scans complete

## Deployment Direction

The likely hosting direction is:

- local development with OrbStack and DDEV
- low-cost production deployment on Hetzner
- Laravel app, queue worker, scheduler, and Python service initially sharing one server

Deployment templates now live in:

- `docs/deployment/production-checklist.md`
- `deploy/`

## Status

This is an active build, not a finished product. The app already looks and behaves like a real SaaS shell, but the core Python analysis brain and billing flow are still to come.
