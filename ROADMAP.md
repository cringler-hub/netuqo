# ROADMAP.md

Phases, not a feature wishlist. New ideas go to BACKLOG.md, not here.

| Phase | Timeframe | Product goal | Business goal |
|-------|-----------|---------------|-----------------|
| 0. Foundation | Week 1–2 | Repo, design direction, V0.1 skeleton | Personal usability established |
| 1. Core | Month 1–2 | Heute, Capture, Due, Business/Privat, Done, Search | Daily self-use |
| 2. Private Alpha | Month 3–4 | Polish, Morning Brief, stability | 10–20 design partners |
| 3. Paid Beta | Month 5–8 | Mail Capture, analytics basis, onboarding | First 50–100 paying users |
| 4. Microsoft | Month 7–10 | M365 connector: To Do / Outlook / Calendar context | Proof: Microsoft is complemented, not replaced |
| 5. Work Memory | Month 10–14 | Better history, search, connections | Retention & differentiation |
| 6. Intelligence | Month 13–18 | Natural capture, Ask, first insights | Premium pricing / higher retention |
| 7. Scale | Month 16–21 | Infra, security, support, i18n | 1,000+ paying users (ambitious target) |
| 8. Exit Readiness | Month 20–24 | Docs, data room, IP, buyer-readiness | Strategic optionality |

## Release gates

- **G1** — Self-use at least 5 days/week.
- **G2** — 20 external users, 10–15 regularly active.
- **G3** — First 50–100 pay voluntarily.
- **G4** — Retention and acquisition channel show repeatability.
- **G5** — AI/MCP only once Work Memory has enough real history.
- **G6** — Scaling investment only once a real bottleneck is proven.

## Where we are now (Phase 0)

Done:
- Repository scaffold (Laravel 13 + Blade + Alpine.js + Tailwind v4).
- Design tokens from the Whitepaper wired into `resources/css/app.css`.
- Core data model foundation: `users`, `tasks`, `activities`, `email_imports`.
- CI (tests + lint) and CD (auto-deploy `main` → IONOS) pipeline. Live at netuqo.com.
- Quick Capture: title (required) + optional Business/Privat + optional due date, wired
  to real `tasks` persistence. See DECISIONS.md for why this ships without login.

Next (still Phase 0/1, in small increments — see CLAUDE.md operating model):
1. Complete/Reopen with `activities` logging.
2. Erledigt + Suche (work memory search foundation).
3. Auth (login-only, single user to start) — deliberately deferred, see DECISIONS.md.
4. Morning Brief email (V0.2).
