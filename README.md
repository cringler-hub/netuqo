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
| `IONOS_SSH_PRIVATE_KEY`    | Private key of a **dedicated deploy keypair** (see step 6 below) — not your personal key. |
| `IONOS_TARGET_DIR`         | Absolute remote path the app is deployed into (see step 3).            |

## IONOS setup (one-time, manual)

GitHub Actions can push code automatically, but it cannot provision hosting, databases, or
DNS for you. These steps happen once, by hand.

1. **Confirm PHP version.** In the IONOS panel, set the PHP version for the domain/webspace
   to **8.3 or newer** (required by Laravel 13). Older shared-hosting defaults are often
   PHP 8.1 — check and change this explicitly. Also confirm the same PHP version is what
   `php` resolves to over SSH (`php -v` after connecting) — on some IONOS packages the CLI
   default differs from the web-facing version and you need `php8.3` explicitly.

2. **Create the MySQL database.** IONOS panel → Databases → create a new MySQL database +
   user. Note the host, database name, username, password — you'll need them for the
   server's `.env` (step 5).

3. **Point the domain at the right folder, and decide the target path.** `netuqo.com` must
   serve the app's `public/` folder as the webroot — never the app root (that would expose
   `.env`, `app/`, etc.). Connect once via SFTP/SSH (the credentials you already have) and
   check the existing folder layout:
   - If you can set a **custom document root per domain**: deploy the whole app to e.g.
     `/netuqo/` (this becomes `IONOS_TARGET_DIR`) and point `netuqo.com`'s document root at
     `/netuqo/public`.
   - If the docroot is fixed to your webspace root: deploy the app one level above and put a
     forwarding `index.php`/`.htaccess` in the actual web root that includes
     `public/index.php`. Tell me which case you're in and I'll add the forwarding files.

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

   Then, over SSH, create the runtime directories the deploy intentionally never touches
   (see the workflow's `EXCLUDE` list) and make them writable:

   ```bash
   cd /path/to/IONOS_TARGET_DIR
   mkdir -p storage/framework/{cache/data,sessions,testing,views} storage/logs storage/app/public storage/app/private bootstrap/cache
   chmod -R 775 storage bootstrap/cache
   ```

6. **Create a dedicated deploy SSH keypair** (don't reuse your personal login key):

   ```bash
   ssh-keygen -t ed25519 -f netuqo_deploy_key -C "netuqo-ci-deploy" -N ""
   ```

   Add the **public** key (`netuqo_deploy_key.pub`) via the IONOS panel's SSH-key management
   for this webspace user, then store the **private** key (`netuqo_deploy_key`) as the
   `IONOS_SSH_PRIVATE_KEY` GitHub secret. This lets CI deploy without your personal password
   ever touching GitHub, and it can be revoked independently.

7. **First deploy.** Push to `main` (or run the workflow manually via **Actions → Deploy to
   IONOS → Run workflow**) once secrets are set. Watch the Actions log — the workflow syncs
   files, then runs the first `php artisan migrate --force` on the server automatically.

8. **Cron for the scheduler (later phase).** Once Morning Brief / mail import land, add a
   cron job in the IONOS panel calling `php /path/to/app/artisan schedule:run` — check the
   minimum interval your package allows (often 5–15 minutes on shared hosting, not the
   1-minute interval Laravel recommends); not needed for the current foundation.
