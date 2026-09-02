# netuqo

> Simply know what's next.

A radically simple personal focus and work-memory system. Core loop: **Capture → Focus →
Done → Remember**. See `PRODUCT.md` and `MANIFESTO.md` for what netuqo is (and deliberately
is not), `ARCHITECTURE.md` for the stack, `ROADMAP.md` for where we are, and `CLAUDE.md` for
how changes get made in this repo.

## Stack

Laravel 13 (PHP) · Blade + Alpine.js · Tailwind CSS v4 · MySQL (SQLite for local dev) ·
GitHub Actions CI/CD → IONOS Webhosting (SSH).

## Local development

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
touch database/database.sqlite
php artisan migrate
composer dev   # app + queue listener + vite, all together
```

Visit `http://localhost:8000`.

## CI/CD

- **`.github/workflows/ci.yml`** — runs on every push/PR: installs deps, lints with Pint,
  builds assets, runs the test suite.
- **`.github/workflows/deploy.yml`** — runs on every push to `main`: builds a production
  bundle (`composer install --no-dev`, `npm run build`), rsyncs it to the IONOS webspace over
  SSH, then runs `php artisan migrate --force` and rebuilds the config/route/view caches
  directly on the server. Requires the GitHub Secrets listed below.
- **`.github/workflows/ionos-bootstrap.yml`** — manual, one-time (safe to re-run): creates
  the server-side `.env` and `storage/` directory tree. Run once as part of initial setup,
  see below.

### Required GitHub Secrets

Set these under the repository's **Settings → Secrets and variables → Actions**, ideally
scoped to a `production` environment with a manual-approval rule if you want a safety gate:

| Secret                    | Value                                                                 |
|----------------------------|-------------------------------------------------------------------------|
| `IONOS_SSH_HOST`           | The SFTP/SSH host, e.g. `home26909034.1and1-data.host`.                |
| `IONOS_SSH_PORT`           | `22`                                                                    |
| `IONOS_SSH_USER`           | The SSH username (e.g. `p7622742`).                                    |
| `IONOS_SSH_PASSWORD`       | The account password. This IONOS webspace has no SSH-key management, so we authenticate with the password instead of a keypair — same as your other GitHub secrets. |
| `IONOS_TARGET_DIR`         | **Relative** remote path (no leading `/`), e.g. `netuqo` — see step 2. SSH logs in directly into the account's webspace root (`/homepages/.../htdocs`), so an absolute path like `/netuqo` would point outside the account. |
| `IONOS_DB_HOST`             | MySQL host from the IONOS panel (Databases). Only used by `ionos-bootstrap.yml`. |
| `IONOS_DB_DATABASE`         | MySQL database name. |
| `IONOS_DB_USERNAME`         | MySQL username. |
| `IONOS_DB_PASSWORD`         | MySQL password. |

`.github/workflows/ionos-bootstrap.yml` is a **one-time setup workflow**, separate from the
regular deploy — see the sequence below. It creates the server-side `.env` (using the
`IONOS_DB_*` secrets and a freshly generated `APP_KEY`, only if `.env` doesn't already exist
— safe to re-run) and the `storage/`/`bootstrap/cache` directories the regular deploy
intentionally never touches, since those hold runtime state that must survive across
deploys.

## IONOS setup

GitHub Actions can push code and configure the server for you here — this account has real
SSH access, confirmed via a temporary diagnostic run (removed once it answered the open
questions, see `DECISIONS.md`). What's left needs a few clicks in the IONOS panel and, in
order, three GitHub Actions runs. No local install, no terminal needed on your side.

1. **Create the MySQL database**, if not already done. IONOS panel → Databases → create a
   new MySQL database + user, then set the four `IONOS_DB_*` secrets above from it.

2. **Run "Deploy to IONOS" once** (Actions tab → select it → Run workflow, branch `main`
   once merged, or this branch for now). This syncs the app and creates the `netuqo/`
   folder as a sibling of `leadscout/`, `messefeedback/`, `logs/`. **The migrate step will
   fail** on this first run — expected, `.env` doesn't exist yet — the file sync itself
   still succeeds.

3. **Run "IONOS one-time bootstrap" once** (same place). It creates `.env` (production
   config + your DB credentials + a fresh `APP_KEY`) and the `storage/` directory tree with
   correct permissions, directly on the server.

4. **Point the domain at `netuqo/public`.** In the IONOS panel under **Domains & SSL →
   netuqo.com**, set the domain's starting directory/document root to `netuqo/public` — now
   that the folder exists. Never point it at the app root (`netuqo/`), that would expose
   `.env`, `app/`, etc.

5. **Enable HTTPS.** Activate the free SSL/TLS certificate (Let's Encrypt) IONOS offers for
   the domain, and force HTTPS.

6. **Run "Deploy to IONOS" again** (or just push to `main` from now on). This time `.env`
   exists, so `php8.4-cli artisan migrate --force` succeeds against the real database and
   caches get rebuilt. Visit `https://netuqo.com` to confirm.

7. **Cron for the scheduler (later phase).** Once Morning Brief / mail import land, add a
   cron job in the IONOS panel calling `/usr/bin/php8.4-cli /path/to/app/artisan
   schedule:run` — check the minimum interval your package allows (often 5–15 minutes on
   shared hosting, not the 1-minute interval Laravel recommends); not needed for the current
   foundation.

## IONOS-specific `.htaccess` note

`public/.htaccess` includes two directives (`Options +FollowSymLinks` and `RewriteBase /`)
above `RewriteEngine On` that aren't part of Laravel's default `.htaccess`. Without them,
this specific IONOS webspace returns a raw 500 for every URL except the exact domain root —
any request that needs `mod_rewrite`'s internal forward to `index.php` failed, confirmed
with Laravel's own zero-code `/up` route before the fix. See `DECISIONS.md` (2026-09-02
entries) for the full diagnosis. If you ever regenerate `public/.htaccess` from a fresh
Laravel install, re-add these two lines.
