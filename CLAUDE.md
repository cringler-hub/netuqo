# CLAUDE.md

You are developing netuqo, a radically simple personal focus system.

Before implementing anything, read `PRODUCT.md`, `MANIFESTO.md`, `ARCHITECTURE.md`, and
`ROADMAP.md`. They are the product's memory across sessions — don't re-derive or
reinterpret what netuqo is; it's already decided there.

Core product principle: **simplicity is the product.**
Core loop: **Capture → Focus → Done → Remember.**

## Rules

- Do not invent features. Do not add project management, team collaboration, dashboards,
  kanban boards, complex navigation, or unnecessary configuration.
- Do not build ahead of the current ROADMAP.md phase. New ideas go to BACKLOG.md, not
  into code — see "spontane Ideen" guardrail below.
- For every requested change:
  1. Identify the user problem.
  2. Check whether it conflicts with MANIFESTO.md.
  3. Prefer using an existing screen/component over adding a new one.
  4. Explain briefly which files will change.
  5. Implement only the agreed scope.
  6. Add/update relevant tests.
  7. Run tests (`composer test` / `php artisan test`) and `vendor/bin/pint`.
  8. Report changes and any simplicity risks.
- Do not refactor unrelated code in the same change.
- Do not add dependencies unless clearly necessary — check `composer.json`/`package.json`
  first; most things should not need a new package.
- Never commit secrets. `.env` is gitignored; only `.env.example` (with placeholders) is
  tracked.
- Build the simplest correct implementation for the current requirement, nothing more.

## Do not

- "Build the whole app" as one mega-prompt — work in small, reviewable slices.
- Develop directly against the IONOS production deploy — everything ships through
  `main` via CI, never by hand-editing files on the webspace.
- Add unreviewed packages.
- Do unrelated refactors in the same change.
- Invent features that weren't requested.
- Implement spontaneous ideas directly — put them in `BACKLOG.md` instead.

## Local dev

```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
touch database/database.sqlite   # local dev only; production uses MySQL, see ARCHITECTURE.md
php artisan migrate
composer dev   # runs the app + queue + vite together
```

## Useful prompts

See `docs/prompts.md` for the ready-to-use "feature idea" and "simplicity audit" prompts
from the Whitepaper — use those templates before writing code for a new idea.
