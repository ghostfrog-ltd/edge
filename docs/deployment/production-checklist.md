# Production Checklist

This checklist is for a single Ubuntu 24.04 VPS running:

- Nginx
- PHP-FPM
- PostgreSQL
- Laravel
- one Laravel queue worker
- Laravel scheduler via cron
- the FastAPI engine on `127.0.0.1:8001`

## 1. Point the Domain

Point your live domain to the VPS IP with DNS `A` records.

Set the production URL in `.env`:

```env
APP_URL=https://your-domain.com
```

## 2. Install System Packages

Update the server:

```bash
sudo apt update && sudo apt upgrade -y
```

Install the main packages:

```bash
sudo apt install -y \
    nginx \
    git \
    unzip \
    curl \
    composer \
    postgresql postgresql-contrib \
    python3 python3-venv python3-pip \
    nodejs npm \
    php8.3-fpm php8.3-cli php8.3-pgsql php8.3-curl php8.3-mbstring \
    php8.3-xml php8.3-zip php8.3-bcmath php8.3-intl php8.3-gd
```

If your distro ships a newer PHP package set, use that instead. The app requires PHP `8.2+`.

## 3. Create a Deploy User

Create a dedicated app user if you do not already have one:

```bash
sudo adduser deploy
sudo usermod -aG www-data deploy
```

## 4. Clone the App

Example path:

```bash
sudo mkdir -p /var/www
sudo chown deploy:www-data /var/www
cd /var/www
git clone <your-repo-url> ghostfrog
cd ghostfrog
```

## 5. Create the Database

Open PostgreSQL:

```bash
sudo -u postgres psql
```

Create the DB and user:

```sql
CREATE DATABASE ghostfrog;
CREATE USER ghostfrog_app WITH PASSWORD 'replace-me';
GRANT ALL PRIVILEGES ON DATABASE ghostfrog TO ghostfrog_app;
\q
```

## 6. Create the Production Environment File

Copy the deployment template:

```bash
cp deploy/env/.env.production.example .env
```

Then fill in all real values.

Important values to set:

- `APP_NAME`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL`
- `APP_KEY`
- all `DB_*`
- all `MAIL_*`
- all `STRIPE_*`
- `SUPPORT_RECIPIENTS`
- `GHOSTFROG_ENGINE_*`
- `GHOSTFROG_ENGINE_LLM_PROVIDER`
- `GEMINI_API_KEY` or `OPENAI_API_KEY`
- `EBAY_CLIENT_ID`
- `EBAY_CLIENT_SECRET`

Generate the app key if you have not set one yet:

```bash
php artisan key:generate
```

## 7. Install App Dependencies

Install PHP dependencies:

```bash
composer install --no-dev --optimize-autoloader
```

Install frontend dependencies and build assets:

```bash
npm ci
npm run build
```

## 8. Run Migrations and Optimizations

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
```

## 9. Set File Permissions

```bash
sudo chown -R deploy:www-data /var/www/ghostfrog
sudo find /var/www/ghostfrog -type f -exec chmod 664 {} \;
sudo find /var/www/ghostfrog -type d -exec chmod 775 {} \;
sudo chmod -R 775 /var/www/ghostfrog/storage /var/www/ghostfrog/bootstrap/cache
```

## 10. Configure Nginx

Copy the example config:

```bash
sudo cp deploy/nginx/ghostfrog.conf.example /etc/nginx/sites-available/ghostfrog
```

Edit it for:

- your domain
- your app path
- your PHP-FPM socket version

Enable it:

```bash
sudo ln -s /etc/nginx/sites-available/ghostfrog /etc/nginx/sites-enabled/ghostfrog
sudo nginx -t
sudo systemctl reload nginx
```

## 11. Add SSL

Install Certbot if needed:

```bash
sudo apt install -y certbot python3-certbot-nginx
```

Issue the certificate:

```bash
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

## 12. Set Up the Python Engine

Create the virtualenv and install the engine dependencies:

```bash
cd /var/www/ghostfrog/engine
python3 -m venv .venv
source .venv/bin/activate
pip install --upgrade pip
pip install -r requirements.txt
```

Copy the systemd unit:

```bash
sudo cp /var/www/ghostfrog/deploy/systemd/ghostfrog-engine.service.example /etc/systemd/system/ghostfrog-engine.service
```

Edit:

- `User`
- `Group`
- `WorkingDirectory`
- `EnvironmentFile`
- `ExecStart`

Then enable it:

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now ghostfrog-engine
sudo systemctl status ghostfrog-engine
```

## 13. Set Up the Laravel Queue Worker

Copy the queue worker unit:

```bash
sudo cp /var/www/ghostfrog/deploy/systemd/ghostfrog-queue.service.example /etc/systemd/system/ghostfrog-queue.service
```

Edit:

- `User`
- `Group`
- `WorkingDirectory`
- `EnvironmentFile`
- `ExecStart`

Then enable it:

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now ghostfrog-queue
sudo systemctl status ghostfrog-queue
```

## 14. Add the Laravel Scheduler Cron

Copy the cron line:

```bash
crontab -e
```

Paste the contents of:

- `deploy/cron/ghostfrog-scheduler.cron`

This app may not use many scheduled tasks yet, but production should still have the scheduler in place.

## 15. Stripe Webhook

In Stripe, set the live webhook endpoint to:

```text
https://your-domain.com/stripe/webhook
```

Make sure the webhook secret is saved in:

```env
STRIPE_WEBHOOK_SECRET=whsec_...
```

## 16. Smoke Test Before Announcing It

Check:

- homepage loads
- signup works
- login works
- billing page loads
- Stripe checkout returns cleanly
- webhook lands
- scan submits
- submission page polls and redirects
- scan completes
- inbox notification appears
- email notification arrives
- PDF downloads
- admin pages load

## 17. Useful Ops Commands

Restart the queue worker:

```bash
sudo systemctl restart ghostfrog-queue
```

Restart the engine:

```bash
sudo systemctl restart ghostfrog-engine
```

Check engine health:

```bash
curl -sS http://127.0.0.1:8001/health
```

Tail Laravel logs:

```bash
tail -f /var/www/ghostfrog/storage/logs/laravel.log
```

## 18. First Upgrades After Launch

Once you have real users, the first infrastructure upgrades worth considering are:

- Redis for cache and queues
- a second worker process
- database backups
- off-server file backup
- uptime monitoring
- log shipping
