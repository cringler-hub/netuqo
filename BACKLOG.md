# BACKLOG.md

Idea parking lot. Nothing here gets built automatically — every item needs a deliberate
decision to move into ROADMAP.md first.

Every entry should include:

- **Problem:** what real problem does this solve?
- **Desired outcome:** what becomes easier?
- **Simplest hypothesis:** the smallest version that could test this.
- **Simplicity risk:** what does this cost the interface / the manifesto?
- **Decision:** build / not now, and why.

---

## Known future items (from the Whitepaper, deliberately deferred)

- **Microsoft 365 connector** (Sign-in, To Do sync, Outlook context, Calendar context).
  Simplicity risk: integration-admin UI creep. Decision: not now — after Core phase.
- **Mail → Task import.** Simplicity risk: needs IMAP polling infra + dedup; shared hosting
  cron granularity matters here. Decision: not now — V0.2.
- **Morning Brief email.** Decision: not now — V0.2, after Capture/Focus/Done loop is solid.
- **Natural language capture / "Ask your Work" / MCP toolset.** Decision: not now — only
  after Work Memory has enough real history to be useful (Release Gate G5).
- **`integration_accounts` table** for OAuth tokens. Decision: not now — model it when the
  Microsoft connector actually starts, not before.

## From the 2026-09-03 "Executive Intelligence" design mockup

Only the visual language (colors, typography, shape, elevation) from this mockup was
adopted — see DESIGN.md. The mockup also showed the following, none of it built:

- **3-item nav (Heute/Erledigt/Suchen) replacing the five time-horizon views.** Problem:
  unclear — the current five views map directly to the manifesto's "what needs my attention
  today/this week/this month/later" question; collapsing them wasn't requested independently
  of the visual refresh. Simplicity risk: could genuinely reduce nav count (manifesto target
  is 3–4), but only if Diese Woche/Diesen Monat/Später's job is redesigned, not just hidden.
  Decision: not now — needs its own scoping, not a side effect of a re-skin.
- **Settings + profile avatar in the header.** Problem: none yet — auth/user profiles are
  deliberately deferred (see ROADMAP.md). Decision: not now — build when auth ships.
- **Search ("Suchen" nav item, ⌘K).** This is already ROADMAP.md's next Phase-1 item ("Suche
  — work memory search foundation"). Decision: build it then, as its own real feature, not as
  a decorative nav stub now.
- **Richer task metadata** (monetary volume, time-of-day, free-form category tags like
  "Governance"/"Telefon", situational context text). Problem: unclear — none of this was
  requested; the current model (title, area, due date) matches "five seconds to capture."
  Simplicity risk: high — more fields to fill in, more to show, works against "one required
  field." Decision: not now — would need its own problem statement first, not inferred from
  a mockup.
- **"Serene Daily Reflection" progress-ring + completion-count footer.** Problem: unclear —
  a nice touch, but a new always-visible widget. Simplicity risk: edges toward "dashboard,"
  which MANIFESTO.md/PRODUCT.md explicitly rule out. Decision: not now.
