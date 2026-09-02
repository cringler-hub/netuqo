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

### Required GitHub Secrets

Set these under the repository's **Settings → Secrets and variables → Actions**, ideally
scoped to a `production` environment with a manual-approval rule if you want a safety gate:

| Secret                    | Value                                                                 |
|----------------------------|-------------------------------------------------------------------------|
| `IONOS_SSH_HOST`           | The SFTP/SSH host, e.g. `home26909034.1and1-data.host`.                |
| `IONOS_SSH_PORT`           | `22`                                                                    |
| `IONOS_SSH_USER`           | The SSH username (e.g. `p7622742`).                                    |
| `IONOS_SSH_PASSWORD`       | The account password. This IONOS webspace has no SSH-key management, so we authenticate with the password instead of a keypair — same as your other GitHub secrets. |
| `IONOS_TARGET_DIR`         | **Relative** remote path (no leading `/`), e.g. `netuqo` — see step 3. SSH logs in directly into the account's webspace root (`/homepages/.../htdocs`), so an absolute path like `/netuqo` would point outside the account. |

## IONOS setup (one-time, manual)

GitHub Actions can push code automatically, but it cannot provision hosting, databases, or
DNS for you. These steps happen once, by hand.

1. **PHP version — confirmed.** The account runs PHP 8.4. Note that bare `php` over SSH
   resolves to an ancient PHP 4.4 CGI fallback — the real PHP 8.4 CLI binary on this account
   is `/usr/bin/php8.4-cli`, which is what `deploy.yml` calls explicitly. If you ever run
   artisan commands by hand over SSH, use that full path, not `php`.

2. **Create the MySQL database.** IONOS panel → Databases → create a new MySQL database +
   user. Note the host, database name, username, password — you'll need them for the
   server's `.env` (step 5).

3. **Point the domain at the right folder.** This webspace hosts multiple domains, one
   top-level folder each (`leadscout/`, `messefeedback/`, ...) — same pattern applies to
   netuqo. `netuqo.com` must serve the app's `public/` folder as the webroot, never the app
   root (that would expose `.env`, `app/`, etc.):
   - The `netuqo/` folder gets created automatically by the first deploy (rsync creates
     missing target directories), as a sibling of `leadscout/`, `messefeedback/`, `logs/`.
   - In the IONOS panel under **Domains & SSL → netuqo.com**, set the domain's starting
     directory/document root to `netuqo/public`.
   - `IONOS_TARGET_DIR` (the GitHub secret) is `netuqo` — relative, no leading slash (see
     the secrets table above for why).

4. **Enable HTTPS.** Activate the free SSL/TLS certificate (Let's Encrypt) IONOS offers for
   the domain, and force HTTPS.

5. **Create the server-side `.env` file — once, by hand.** `.env` is intentionally never
   committed or deployed by CI (it holds secrets, and the deploy step excludes it). Connect
   over SFTP/SSH and create `.env` directly in `IONOS_TARGET_DIR` (next to `artisan`), based
   on `.env.example`, with:
   - `APP_ENV=production`, `APP_DEBUG=false`
   - `APP_URL=https://netuqo.com`
   - `APP_KEY=` — generate locally with `php artisan key:generate --show` and paste the
     value in (don't run `key:generate` on the server).
   - `DB_CONNECTION=mysql` + the host/database/username/password from step 2.
   - `MAIL_*` — use the SMTP credentials of a mailbox on your IONOS package, since
     `netuqo.com` will eventually need to send the Morning Brief.

   Then, once the folder exists (after the first deploy, step 7), create the runtime
   directories the deploy intentionally never touches (see the workflow's `EXCLUDE` list)
   and make them writable — over SSH:

   ```bash
   cd netuqo
   mkdir -p storage/framework/{cache/data,sessions,testing,views} storage/logs storage/app/public storage/app/private bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

6. **Shell access — confirmed working.** Verified via a temporary diagnostic workflow:
   real command execution over SSH works on this account (not SFTP-only), and so does
   `rsync` on the server side. `deploy.yml`'s migrate/cache-rebuild step relies on this.

7. **First deploy.** Push to `main` (or run the workflow manually via **Actions → Deploy to
   IONOS → Run workflow**) once secrets are set and the domain's document root (step 3) is
   configured. Watch the Actions log — the workflow syncs files, creating `netuqo/` if it
   doesn't exist yet, then runs `php artisan migrate --force` on the server. **Before this
   first run**, create the server-side `.env` (step 5's first half) so migrate has a
   database to connect to — the `storage/` directories in step 5's second half only need to
   exist by the time the app actually serves a request, so right after this first deploy is
   fine.

8. **Cron for the scheduler (later phase).** Once Morning Brief / mail import land, add a
   cron job in the IONOS panel calling `/usr/bin/php8.4-cli /path/to/app/artisan
   schedule:run` — check the minimum interval your package allows (often 5–15 minutes on
   shared hosting, not the 1-minute interval Laravel recommends); not needed for the current
   foundation.
