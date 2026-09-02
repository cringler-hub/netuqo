# DECISIONS.md

Record: date, decision, reason, rejected alternatives, simplicity impact. Append-only.

---

## 2026-09-02 — Repository foundation

**Decision:** Scaffold netuqo as a Laravel 13 monolith (Blade + Alpine.js + Tailwind v4),
deployed via GitHub Actions to IONOS shared webspace over SFTP on every push to `main`.

**Reason:** Matches the whitepaper's architecture (Section 8): one framework for auth,
API, mail and scheduling; existing IONOS infrastructure; no premature microservices.

**Rejected alternatives:**
- SPA framework (React/Vue) with a separate API — rejected: doubles client complexity for
  no benefit at this stage, contradicts "monolith first."
- Managed cloud hosting (e.g. Laravel Forge + DO/AWS) — rejected for V1: higher cost and
  operational surface than the existing IONOS webspace; revisit only at Release Gate G6.

**Simplicity impact:** None on the user-facing product; this is purely a foundation
decision. Keeps future decisions (auth, capture, done) unblocked.

---

## 2026-09-02 — Data model models `users`/`tasks`/`activities`/`email_imports`, not `integration_accounts`

**Decision:** Build the four core tables now; defer `integration_accounts` until the
Microsoft connector phase actually starts.

**Reason:** Manifesto principle 10 — no feature without a simplicity test. An unused table
for external tokens adds migration/maintenance surface with zero present value.

**Rejected alternative:** Modeling `integration_accounts` now "to save time later" —
rejected, this is exactly the kind of speculative complexity the architecture explicitly
warns against.

**Simplicity impact:** None yet; keeps the schema minimal until it's needed.
