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

---

## 2026-09-02 — Deploy over SSH/rsync, not FTPS

**Decision:** Confirmed the IONOS Webhosting package includes SFTP+SSH (port 22, not just
plain FTP). Deploy now rsyncs the built app over SSH and runs `php artisan migrate --force`
+ cache rebuilds directly on the server as part of the same CI job, using a dedicated deploy
keypair rather than a password.

**Reason:** SSH access removes the biggest open question from the initial scaffold (how to
run the first and all subsequent migrations without shell access). It's also more reliable
than FTPS for a directory sync (atomic-ish, proper excludes, no protocol quirks).

**Rejected alternative:** Keep the original FTPS workflow and handle migrations manually on
every release — rejected now that SSH is confirmed available; manual migration steps don't
scale past the first deploy and are an easy way to forget a schema change in production.

**Simplicity impact:** None on the product; strictly reduces operational risk/toil for the
person running netuqo.

---

## 2026-09-02 — SSH auth: password, not keypair

**Decision:** Deploy authenticates to IONOS with a password (`IONOS_SSH_PASSWORD` secret),
not the dedicated keypair originally planned.

**Reason:** Checked — this IONOS webspace has no SSH public-key management UI. Password
auth via a GitHub secret is the available option, consistent with how other GitHub secrets
are already handled on this account.

**Rejected alternative:** Blocking the deploy pipeline on IONOS adding key support —
rejected, no such option exists on this package tier.

**Simplicity impact:** None on the product. Slightly less rotable than a keypair; rotate by
changing the account password and the GitHub secret together if ever needed.

---

## 2026-09-02 — Confirmed IONOS deploy environment specifics

**Decision:** Finalized `deploy.yml` against real, verified facts about this IONOS account
(found via a temporary `ionos-check.yml` diagnostic workflow, then removed):

- Real SSH shell access works (not SFTP-only), and `rsync` is present server-side.
- SSH logs in directly at the webspace root (`/homepages/30/d26909034/htdocs`), the same
  place SFTP shows as `/`. So `IONOS_TARGET_DIR` must be a **relative** path (`netuqo`, no
  leading slash) — an absolute `/netuqo` would target the real filesystem root, outside the
  account.
- Bare `php` resolves to an ancient PHP 4.4 CGI fallback. The real PHP 8.4 CLI binary is
  `/usr/bin/php8.4-cli`; `deploy.yml` calls it explicitly instead of relying on `php` in
  `PATH`.

**Reason:** Guessing these would have produced a deploy that fails in confusing ways (wrong
PHP version parsing Laravel 13 code, or rsync writing outside the account). Cheaper to
verify once with a disposable diagnostic workflow than to debug a failed production deploy.

**Simplicity impact:** None on the product; pure deploy-plumbing correctness.

---

## 2026-09-02 — Quick Capture ships before login

**Decision:** Build Capture (title + optional Business/Privat + optional due date) now,
persisted to the real `tasks` table, before Login exists. Every task is still scoped to a
real `user_id` — `Controller::currentUser()` resolves (and lazily creates) a single fixed
"Owner" user until real authentication is built, rather than leaving `user_id` nullable or
unscoped.

**Reason:** User priority call: Capture is the thing to validate first; login is
infrastructure around it. ARCHITECTURE.md's "ownership everywhere" guardrail still holds —
we did not weaken the data model to get here, only deferred the UI for switching users.

**Rejected alternative:** Making `tasks.user_id` nullable until login ships — rejected,
that's exactly the kind of retrofit ARCHITECTURE.md says to avoid; scoping from day one
costs nothing here since there's only ever one row in `users` for now.

**Simplicity impact:** None on the current single-user experience. `currentUser()` is a
single, clearly-commented method to delete once login exists — not spread across the
codebase.

---

## 2026-09-02 — Production 500 on any non-root URL: confirmed hosting bug, not our code

**Finding:** `POST /tasks` (and every other non-root route, including Laravel's own
zero-code `/up` health check) returns a raw Apache 500 ("...encountered while trying to
use an ErrorDocument to handle the request") in production. `GET /` works. Ruled out, in
order, with disposable diagnostic workflows (each removed after use):
- Not app/business logic — the exact `User::firstOrCreate()` + `tasks()->create()` code
  from `TaskController::store` succeeds cleanly when run via `artisan tinker` on the server.
- Not CSRF/session — a route with CSRF verification fully excluded 500s identically.
- Not our Blade views — `view('diag')->render()` succeeds via CLI; the compiled view cache
  is present and correct.
- Not route caching — `route:cache` exits 0, and `route:list` correctly lists every route
  including brand-new ones.
- **Root cause, confirmed:** `GET /index.php` (direct file request) → 200. `GET
  /anything-that-requires-mod_rewrites-internal-forward-to-index.php` → 500, even for a
  path that doesn't exist at all. `/` only works because Apache's `DirectoryIndex` finds
  `index.php` directly, without needing `mod_rewrite`'s internal redirect. The redirect
  itself — `public/.htaccess`'s standard Laravel `RewriteRule ^ index.php [L]` — is what
  breaks on this webspace, most likely a PHP-CGI/`Action`-handler mismatch for internally
  rewritten requests (the web-facing PHP SAPI reports itself as `cgi-fcgi`, consistent with
  this class of legacy-CGI + mod_rewrite issue on shared hosting).

**Why this matters for future sessions:** don't re-diagnose this as an app bug. If a route
other than `/` 500s in production, check this first.

**Simplicity impact:** None; this blocked the app from being usable in production at all
until resolved, independent of any feature work. See the follow-up entry below for the fix.

---

## 2026-09-02 — Fixed: `RewriteBase /` + `Options +FollowSymLinks` in `public/.htaccess`

**Decision:** Added two directives to `public/.htaccess`, above `RewriteEngine On`:

```apache
Options +FollowSymLinks
RewriteBase /
```

This is IONOS's own documented fix for exactly this symptom (internally-rewritten
`mod_rewrite` requests to a front controller failing with a raw 500). **Confirmed working
end-to-end in production**: `GET /up` → 200, and a real `POST /tasks` (with CSRF token)
successfully created a task that then appeared on `/`.

**Why the earlier "hosting bug, needs a support ticket" framing was wrong:** `leadscout`
(this account's other project) is a static site with no PHP at all, and `messefeedback`
(also on this account) is PHP but uses zero `mod_rewrite` — every page is requested by its
real filename (`index.php`, `submit.php`, `danke.php`). Neither had ever exercised
`mod_rewrite`'s internal-redirect-to-PHP path before netuqo. So "nothing else on this
account was broken" didn't mean mod_rewrite worked here — it meant it had never been
tested. Once actually tested and fixed with the directives above, it works fine.

**Rejected alternative:** Waiting on IONOS support / switching PHP execution mode in the
panel — unnecessary once the actual documented `.htaccess` fix was found and verified.

**Simplicity impact:** None on the product. Two lines in a config file Laravel ships by
default anyway; no application code changed.

---

## 2026-09-02 — Heute/Später/Erledigt split, and Complete/Reopen

**Decision:** Tasks can now be marked done (and reopened) from any list, logged to
`activities`. The single task list is split into three pages by nav: **Heute** (open tasks
due today or overdue), **Später** (open tasks with no due date, or due in the future), and
**Erledigt** (done tasks, newest-completed first). "Einstellungen" is removed from the nav —
there are no settings to configure yet, and an empty settings page is exactly the kind of
premature navigation MANIFESTO.md warns against.

**Reason:** User feedback after using Capture live: Heute was showing every open task
regardless of due date, which defeats its purpose as a daily-focus view; there was no way to
complete a task at all. The header/nav markup was also duplicated per-page, so it's now
centralized in `components/layouts/app.blade.php` (parameterized by an `active` prop) and
task rows are a shared `<x-task-row>` component used by all three pages.

**Rejected alternative:** A single filterable list with client-side tabs — rejected, three
plain server-rendered pages with real URLs (`/`, `/later`, `/done`) are simpler to build,
test, and reason about than adding JS-side filtering state, and match the nav-as-navigation
pattern already established.

**Simplicity impact:** Nav stays at 3 items (Heute/Später/Erledigt), same ceiling
MANIFESTO.md sets. Removing the unused Einstellungen link reduces surface rather than adding
to it.

---

## 2026-09-02 — Editable due date, area filter as a query param

**Decision:** A task's due date can be changed after capture (inline on the task row, click
the date to edit, auto-saves via `PATCH /tasks/{task}`). Heute/Später/Erledigt can each be
filtered to Business or Privat via `?area=` on the same URL, shown as three small toggle
links (Alle/Business/Privat) above the list.

**Reason:** User feedback: a wrong or changing due date had no way to be corrected, and with
Business and Privat tasks mixed together there was no way to see just one area.

**Rejected alternative:** A full task-edit screen — rejected, this is exactly one field
(area is set at capture and rarely needs changing after; only due date drifts in practice).
Client-side JS filtering instead of `?area=` — rejected, a plain query param keeps the filter
state in the URL (shareable/bookmarkable, survives a reload) with no added JS state.

**Simplicity impact:** No new screen, no new required field. The filter is off by default
(plain "Alle") so it doesn't add a decision to the default path.

---

## 2026-09-02 — Delete a task

**Decision:** Each task row gets a small trash icon (`DELETE /tasks/{task}`), guarded by a
native browser confirm dialog. Deleting a task cascades to its `activities` rows (existing
FK `cascadeOnDelete`) — no orphaned audit rows.

**Reason:** User feedback: some captured tasks are simply wrong/duplicate and shouldn't be
kept around as "done" just to get them off the list.

**Rejected alternative:** A custom confirm modal — rejected, the native `confirm()` dialog
is one line, needs no new component, and is enough friction to prevent a mis-click.

**Simplicity impact:** None on the default path (capture/complete); adds one icon per row,
consistent with "complete a task = 1 click."

---

## 2026-09-02 — Session/cache off MySQL, overdue tasks labeled

**Decision:** Switched `SESSION_DRIVER` and `CACHE_STORE` from `database` to `file`, both in
`.env.example`/`ionos-bootstrap.yml` (for future installs) and on the live server via a
one-time `ionos-tune-perf.yml` workflow (confirmed applied — SESSION_DRIVER=file,
CACHE_STORE=file live, config cache rebuilt — then removed, same as earlier disposable
diagnostic workflows). Every request was doing a session read + write against MySQL on top
of whatever the request itself needed — that's now local disk I/O instead. Also: an open task
whose due date is before today now shows "Überfällig · <date>" in red on its row (checked
live, not stored — no new column).

**Reason:** User feedback that capturing a task feels slow — directly against MANIFESTO's
"Capture a task < 5 seconds" metric. Session/cache-on-MySQL is a well-known source of
avoidable per-request latency on shared hosting; removing it can only help, and is safe
regardless of the exact bottleneck. Second, separate feedback: overdue tasks were visually
identical to tasks due later today, easy to miss on Heute.

**Rejected alternative:** Diagnosing further (opcache status, DB host round-trip time, etc.)
before changing anything — rejected for now; this fix is unambiguously correct and low-risk,
so it ships first. If task capture still feels slow after this, that's the next thing to
measure, not guess at.

**Simplicity impact:** None on the product. Overdue labeling adds no new field, screen, or
interaction — it's computed from data already captured.

---

## 2026-09-02 — "Heute" quick-date button; perf investigated further, root cause still open

**Decision (shipped):** Added a "Heute" button next to every due-date field (capture form and
inline task-row edit) that fills in today's date in one click, via Alpine `$refs`.

**Decision (investigated, not yet fixed):** The DB/session fix alone didn't make capture feel
noticeably faster, per user feedback. Measured directly on the server via a temporary
`/diag-perf` route and `ionos-perf-check.yml` (both removed after use, per this repo's
disposable-diagnostic convention):

- `artisan tinker` DB round trip (pure CLI, no HTTP): **~25–60ms** per fresh connection to
  `db5021331250.hosting-data.io` — this account's MySQL is a separate managed host, not
  colocated with the webspace, so every request's first query pays a real connection cost.
  Not nothing, but not "several seconds" either.
- The CLI PHP binary (`/usr/bin/php8.4-cli`) has **no OPcache extension at all** —
  `extension_loaded('Zend OPcache')` is false. If the actual web-facing SAPI (this account
  reports itself as `cgi-fcgi`, confirmed earlier this session — a different binary/config
  than the CLI) is the same, every request recompiles the entire Laravel framework from
  source, which is a much bigger cost than anything already fixed. **Could not confirm**
  whether the web SAPI matches, because:
- Every attempt to fetch `/diag-perf` over HTTPS — `curl` from GitHub Actions, `curl` from
  the IONOS server curling its own domain, `curl` forced to TLS 1.2, `curl` without HTTP/2
  ALPN, even PHP's own `file_get_contents()` — fails identically at the TLS handshake with
  `TLS alert, internal error`, before any HTTP data (headers, User-Agent) is even sent. Real
  browsers clearly work fine (the app is in daily use). This looks like a bot-protection
  layer on this shared-hosting account fingerprinting the TLS ClientHello itself (curl vs.
  browser), not the IP-based WAF block page assumed earlier this session for the mod_rewrite
  investigation — that assumption should be revisited if it comes up again.
  `opcache.*` directives are `PHP_INI_SYSTEM`-scope, so even if this were confirmed, a
  `.user.ini` fix (as usually used for per-directory PHP overrides on this account) would be
  silently inert — this can only be fixed, if it needs fixing, from the IONOS panel or a
  support ticket, not from the app or deploy pipeline.

**Not done:** Because the direct web-SAPI OPcache status couldn't be confirmed, no unverified
"fix" (like an inert `.user.ini`) was shipped for it. The genuinely available app-level lever
left on the table — replacing the POST-then-redirect-GET capture flow with an async
(fetch-based) submit, cutting the two full-page round trips down to one — was intentionally
not implemented without asking first: it's the first real complexity/simplicity trade-off in
this investigation (adds client-side JS state Alpine doesn't already have for this), so it's
a product decision, not a pure bug fix.

**Simplicity impact:** None yet — nothing shipped here beyond the one-click date button.

---

## 2026-09-02 — Adopted "Premium Noir" design system, replacing the Whitepaper's light palette

**Decision:** Replaced the original light/warm design tokens (`app.css`'s former "Light, warm,
calm surface. Never technical-cold.") with the "Premium Noir" system: dark obsidian surfaces
(`#0b1326` background), a violet-to-blue accent gradient (`#8b5cf6` → `#3b82f6`) for primary
actions, three typefaces (Hanken Grotesk for headings, Manrope for body, JetBrains Mono for
chips/labels/dates), sharper shapes, hairline borders, and a subtle hover glow on primary
buttons. Full spec now lives in `DESIGN.md`; `resources/css/app.css`'s `@theme` block is the
implementation.

**Reason:** Explicit user direction, given as a full design spec + new logo/wordmark. User
chose full adoption over an adapted/reduced version when asked.

**Rejected alternative:** An adapted, reduced version keeping one typeface and skipping
glow/blur effects — offered as the recommended option (closer to MANIFESTO's simplicity
default) but not chosen; the user wanted the complete system.

**Explicitly not adopted:** The spec's 12-column grid and sidebar-navigation guidance —
netuqo has three screens and stays a single `max-w-2xl` column, so a grid system has nothing
to apply to here. Adopting a visual token language doesn't require also inventing a page
layout the product doesn't need.

**Simplicity impact:** Net neutral to slightly negative on raw simplicity (three font
families instead of one, added glow/gradient CSS) — an explicit trade the user made for
brand identity. No change to information architecture, required fields, or click counts.

---

## 2026-09-02 — Logo added: icon mark only, not the full lockup

**Decision:** The provided `logo.png` (full lockup: icon + "netuqo" wordmark + tagline, on a
white background, no transparency) is used in the header as just the icon mark — cropped,
background keyed to transparent, saved to `public/images/logo-icon.png`. The wordmark stays
the existing styled text (`brand-gradient-text`) next to it. The original file is kept at
`resources/images/logo-source.png` for future re-processing.

**Reason:** The wordmark in the supplied PNG is a dark navy almost identical to the new
Premium Noir background color — on the dark header it was essentially invisible (checked by
compositing it onto `#0b1326` directly). The icon mark (blue gradient) reads cleanly on dark
and carries the brand recognition; recoloring the wordmark pixels in a flattened raster
without the source vector risked a botched edit. Text stays legible and on-brand as-is.

**Rejected alternative:** Using the full lockup as shipped, accepting the illegible wordmark
— rejected as a visible regression, not a style choice.

**Simplicity impact:** None — same header layout, one added `<img>`.

---

## 2026-09-02 — Unified on one typeface; "Heute" heading made time-of-day-independent

**Decision:** Dropped Hanken Grotesk and JetBrains Mono; the whole app now uses Manrope
everywhere (headings, body, chips, dates). Chips/labels/dates keep their distinct look via
`uppercase tracking-wide text-xs`, just without a separate font family. Also: the Heute
page's greeting "Guten Morgen. / Hier ist, was heute wichtig ist." is replaced with "Heute. /
Was ist wichtig." — matching the plain-noun-plus-period heading style already used on
Später/Erledigt.

**Reason:** User feedback: three typefaces read as inconsistent in practice, not "premium."
Separately: a morning greeting is wrong for anyone opening netuqo in the afternoon or
evening — the whole point of Heute is to be right regardless of time of day.

**Rejected alternative:** Keeping the multi-typeface system with better weight/size tuning —
rejected; the user asked for one font, not a refinement of three.

**Simplicity impact:** Net positive — fewer font files to load, one less inconsistency to
reason about. `DESIGN.md` is annotated to note this deviation from the original spec.

---

## 2026-09-02 — Task title editable after capture

**Decision:** Click a task's title to edit it inline (same pattern as the due-date edit
already shipped): a text input replaces the title, saves on Enter or on blur via the
existing `PATCH /tasks/{task}` route (now also accepting `title`).

**Reason:** User feedback — captured titles had no way to be corrected after the fact
(typos, more detail added later), unlike the due date which already got this treatment.

**Rejected alternative:** None considered — this is the same interaction pattern already
established for due dates, just applied to the other editable field.

**Simplicity impact:** None — reuses the existing update endpoint and edit-in-place pattern,
no new screen or concept.

---

## 2026-09-02 — Task area (Business/Privat) editable after capture

**Decision:** Click a task's area chip (or "+ Kategorie" if unset) to reveal Business/Privat
toggle buttons plus a "×" to clear it — same edit-in-place family as title and due date, all
via the existing `PATCH /tasks/{task}` route (now also accepting `area`).

**Reason:** User feedback — area was set-once-at-capture only; a task filed under the wrong
category, or captured without one, had no way to be corrected.

**Rejected alternative:** None considered — same pattern already established for title and
due date, applied to the third and last editable field.

**Simplicity impact:** None — no new screen or concept, reuses the existing update endpoint.
