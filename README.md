# Ghostfrog Ebay Edge

Ghostfrog Ebay Edge is an early-stage SaaS product for eBay sellers. It is designed to scan a keyword or niche, compare competitor listings, and return a structured report showing missing attributes, weak spots, and practical listing actions.

This repository currently contains the Laravel application, public marketing pages, customer workspace, and operator admin area. The Python analysis service is planned next and will plug into the scan pipeline that already exists in the app shell.

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
- SQLite for local development right now
- Planned Python/FastAPI analysis service

## Local Development

This project has been set up to run well with Laravel Herd.

### 1. Install dependencies

```bash
composer install
npm install
```

### 2. Create environment file

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Prepare the database

```bash
touch database/database.sqlite
php artisan migrate
```

### 4. Run the app

If you use Herd, link and secure the project directory, then open:

- [https://ghostfrog-ebay-edge.test](https://ghostfrog-ebay-edge.test)

Otherwise you can run it locally with:

```bash
composer run dev
```

That starts:

- the Laravel app server
- the queue listener
- the log tail
- the Vite dev server

## Useful Commands

```bash
php artisan test
npm run build
php artisan migrate
php artisan optimize:clear
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

- local development with Herd
- low-cost production deployment on Hetzner
- Laravel app, queue worker, scheduler, and Python service initially sharing one server

## Status

This is an active build, not a finished product. The app already looks and behaves like a real SaaS shell, but the core Python analysis brain and billing flow are still to come.
