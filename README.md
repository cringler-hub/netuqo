# netuqo

> Simply know what's next.

A radically simple personal focus and work-memory system. Core loop: **Capture → Focus →
Done → Remember**. See `PRODUCT.md` and `MANIFESTO.md` for what netuqo is (and deliberately
is not), `ARCHITECTURE.md` for the stack, `ROADMAP.md` for where we are, and `CLAUDE.md` for
how changes get made in this repo.

## Stack

Laravel 13 (PHP) · Blade + Alpine.js · Tailwind CSS v4 · MySQL (SQLite for local dev) ·
GitHub Actions CI/CD → IONOS shared webspace.

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
  bundle (`composer install --no-dev`, `npm run build`) and ships it to the IONOS webspace
  over FTPS. Requires the GitHub Secrets listed below.

### Required GitHub Secrets

Set these under the repository's **Settings → Secrets and variables → Actions**, ideally
scoped to a `production` environment with a manual-approval rule if you want a safety gate:

| Secret                  | Value                                                             |
|--------------------------|--------------------------------------------------------------------|
| `IONOS_FTP_SERVER`       | Your IONOS FTP/SFTP host, e.g. `access.your-domain.ionos.de` or an IP. |
| `IONOS_FTP_USERNAME`     | The FTP username from the IONOS control panel.                    |
| `IONOS_FTP_PASSWORD`     | The FTP password.                                                  |
| `IONOS_FTP_TARGET_DIR`   | The remote directory to deploy into, e.g. `/netuqo-app/`.          |

## IONOS setup (one-time, manual)

GitHub Actions can push code automatically, but it cannot provision hosting, databases, or
DNS for you. These steps happen once, by hand, in the IONOS control panel.

1. **Confirm PHP version.** In the IONOS panel, set the PHP version for the domain/webspace
   to **8.3 or newer** (required by Laravel 13). Older shared-hosting defaults are often
   PHP 8.1 — check and change this explicitly.

2. **Create the MySQL database.** IONOS panel → Databases → create a new MySQL database +
   user. Note the host, database name, username, password — you'll need them for the
   server's `.env` (step 5).

3. **Point the domain at the right folder.** `netuqo.com` must serve the app's `public/`
   folder as the webroot — never the app root (that would expose `.env`, `app/`, etc.).
   - If your IONOS package lets you set a **custom starting directory** per domain: deploy
     the whole app to e.g. `/netuqo-app/` and point `netuqo.com`'s document root at
     `/netuqo-app/public`.
   - If it does **not** support a custom document root (some basic Webhosting plans only
     serve from the fixed root folder): deploy the app one level above the web root and use
     a forwarding `index.php`/`.htaccess` in the actual web root that includes
     `public/index.php`. This is a common Laravel-on-shared-hosting workaround — tell me
     which case you're in and I'll add the forwarding files.
   - This is exactly why `IONOS_FTP_TARGET_DIR` is a secret, not hardcoded: it depends on
     which of the two applies to your package.

4. **Enable HTTPS.** Activate the free SSL/TLS certificate (Let's Encrypt) IONOS offers for
   the domain, and force HTTPS.

5. **Create the server-side `.env` file — once, by hand.** `.env` is intentionally never
   committed or deployed by CI (it holds secrets). On first deploy, use IONOS's File Manager
   or an FTP client to create `.env` directly in the deployed app's root directory (next to
   `artisan`), based on `.env.example`, with:
   - `APP_ENV=production`, `APP_DEBUG=false`
   - `APP_URL=https://netuqo.com`
   - `APP_KEY=` — generate locally with `php artisan key:generate --show` and paste the
     value in (don't run `key:generate` on the server if you can avoid it).
   - `DB_CONNECTION=mysql` + the host/database/username/password from step 2.
   - `MAIL_*` — use the SMTP credentials of a mailbox on your IONOS package (Mail Basic or
     similar), since `netuqo.com` will need to send the eventual Morning Brief.
   - This file only needs to be created once; it is not overwritten by later deploys since
     CI's exclude list skips `.env`.

6. **Run the first migration.** Laravel needs `php artisan migrate --force` run once against
   the production database. **This is the open question in the setup — see below**, because
   it depends on whether your IONOS package includes SSH access.

7. **Storage permissions.** `storage/` and `bootstrap/cache/` must be writable by the PHP
   process. On IONOS this is usually fine by default for files uploaded via your own FTP
   account; if you hit "permission denied" errors, `chmod -R 775 storage bootstrap/cache`
   via FTP client or File Manager.

8. **Cron for the scheduler (later phase).** Once Morning Brief / mail import land, add a
   cron job in the IONOS panel calling
   `php /path/to/app/artisan schedule:run` — check the minimum interval your package allows
   (often 5–15 minutes on shared hosting, not the 1-minute Laravel recommends); not needed
   for the current foundation.

## Open questions before the deploy pipeline can be finalized

See the chat for the specific questions about your IONOS package, domain status, and
whether SSH access is available — the answers change step 3 and step 6 above concretely.
