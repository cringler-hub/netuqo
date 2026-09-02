# ARCHITECTURE.md

## Stack

| Layer         | Technology                     | Why                                                             |
|---------------|---------------------------------|------------------------------------------------------------------|
| Backend       | Laravel 13 / PHP 8.3+           | One framework for auth, API, mail, scheduler, DB.                |
| Frontend      | Blade + Alpine.js               | Minimal client complexity, still modern interactions.            |
| Styling       | Tailwind CSS v4 (via Vite)      | Direct mapping of design tokens, see `resources/css/app.css`.    |
| Database      | MySQL/MariaDB in production, SQLite for local dev | Robust, cheap, sufficient for the MVP. |
| Hosting       | IONOS webspace (shared hosting) | Existing infrastructure, low operational overhead to start.      |
| Scheduling    | Laravel Scheduler + cron        | Morning Brief, mail import jobs.                                 |
| Mail          | SMTP (out) + IMAP (in, later)   | Outgoing briefing, incoming capture.                              |
| PWA           | Manifest + Service Worker (later) | One codebase for desktop/mobile/tablet.                        |
| CI/CD         | GitHub Actions                  | Test on every push, deploy `main` to IONOS automatically.        |

## Principles

- **Monolith first.** No microservices for hypothetical scale.
- **External systems are connectors, not architecture.** Microsoft 365, IMAP, etc. are
  adapters that normalize onto netuqo's own data model — netuqo's database is always the
  source of truth for its own history and memory.
- **API-first domain layer.** Domain logic lives in models/services, not in controllers,
  so Web, MCP, and future integrations can reuse it.
- **No queues, Redis, search engine, or cloud migration** until there is a real, measured
  bottleneck. IONOS shared hosting has none of these available by default anyway.
- **Ownership everywhere.** Every row belongs to exactly one `user_id`. Every query and
  route is scoped to the authenticated user, from the first migration on — even though
  V0.1 only has one real user. Retrofitting multi-tenancy later is expensive; modeling it
  from day one is free.

## Directory conventions

- `app/Models` — Eloquent models, one per core object (see below).
- `resources/views/components/layouts/app.blade.php` — the single shared HTML shell.
- `resources/views/*.blade.php` — top-level screens (kept to a handful: Heute, Erledigt,
  Einstellungen — see MANIFESTO.md navigation limit).
- `resources/css/app.css` — Tailwind v4 `@theme` block holding the netuqo design tokens.
- `database/migrations` — one thing per migration, additive, never edited after merge.

## Core data model (V0.1 foundation)

| Table          | Purpose                                                             |
|----------------|----------------------------------------------------------------------|
| `users`        | Laravel default + `locale`, `timezone`.                              |
| `tasks`        | `user_id`, `title`, `description`, `area` (business/private), `status`, `due_at`, `source`, `completed_at`. |
| `activities`   | Append-only log per task (`action`, `old_value`, `new_value`) — not shown in the UI, powers later "Ask your Work" / insight features. |
| `email_imports`| Tracks which inbound emails were already turned into a task (dedup by `message_id`). |

`integration_accounts` (Microsoft OAuth tokens etc.) is intentionally **not** modeled yet —
it belongs to the Microsoft-connector phase (Roadmap phase 4), not the foundation.

## What we do not do (guardrails)

- No 1:1 replication of Microsoft functionality.
- No queues/Redis/Elasticsearch/S3 until a real bottleneck forces it.
- No new top-level nav item without a manifesto justification.
- No secrets in Git, ever — environment variables only (see `.env.example`).
- No unreviewed third-party packages added on a whim.

## Deployment shape

GitHub Actions builds the app (`composer install --no-dev`, `npm run build`) and ships the
built `public/build` assets + PHP application to the IONOS webspace via SFTP on every push
to `main`. See `.github/workflows/deploy.yml` and the README section "IONOS Setup" for the
manual, one-time steps required on the IONOS side.
