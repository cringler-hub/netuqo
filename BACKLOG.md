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
