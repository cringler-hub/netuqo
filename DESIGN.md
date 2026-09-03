# DESIGN.md

netuqo's visual design system. Adopted 2026-09-03, replacing the "Premium Noir" dark theme
— see DECISIONS.md for why. `resources/css/app.css`'s `@theme` block is the token
implementation; keep it in sync with this file if either changes.

**Only the visual language below was adopted — not the full mockup it was drafted from.**
The mockup (`code.html`, generated via Google Stitch) also showed a restructured navigation
(Heute/Erledigt/Suchen instead of the current five time-horizon views), Settings and profile
affordances, a search bar, and task cards carrying fields netuqo's data model doesn't have
(monetary volume, time-of-day, free-form category tags, situational context text) plus a
"Serene Daily Reflection" progress-ring footer. None of that is built — implementing it would
mean inventing features and a data model netuqo doesn't have, which CLAUDE.md rules out. What
*is* adopted: colors, typography, shape language, spacing rhythm, and elevation — applied to
the existing five screens (Heute, Diese Woche, Diesen Monat, Später, Erledigt) and existing
components (capture bar, task row, area filter), unchanged in structure and functionality.
Ideas for the deferred pieces are logged in BACKLOG.md, not built. As before: no 12-column
grid or sidebar navigation applies here — netuqo stays a single, calm column (`max-w-2xl`).
An icon font (Material Symbols) appeared in the mockup but was not adopted — the app already
expresses icons via inline SVG (delete) and plain characters (✓, ×, +), and CLAUDE.md asks not
to add a dependency without clear need.

---
name: Executive Intelligence
colors:
  background: '#f7f8fa'
  surface: '#ffffff'
  surface-container-low: '#f2f4f6'
  on-surface: '#0b1020'
  on-surface-variant: '#64748b'
  outline: '#76767d'
  outline-variant: '#eaebed'
  primary: '#5b3ce6'
  primary-hover: '#4a2ecf'
  primary-container: '#f1efff'
  success: '#22e0c5'
  success-container: '#e6faf6'
  error: '#ba1a1a'
typography:
  headline-lg:
    fontFamily: Newsreader
    fontSize: 32px
    fontWeight: '500'
    lineHeight: '1.2'
    letterSpacing: -0.015em
  display-lg:
    fontFamily: Newsreader
    fontSize: 44px
    fontWeight: '400'
    lineHeight: '1.2'
    letterSpacing: -0.02em
  body-md:
    fontFamily: Manrope
    fontSize: 15px
    fontWeight: '400'
    lineHeight: '1.6'
  label-sm:
    fontFamily: Manrope
    fontSize: 10px
    fontWeight: '700'
    lineHeight: '1.4'
    letterSpacing: 0.08em
rounded:
  task: 2rem
  full: 9999px
spacing:
  unit: 4px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 40px
  2xl: 64px
---

## Brand & Style

**Editorial Minimalism** meets **Tactile Restraint**. The interface behaves like a calm,
singular chief of staff: unobtrusive, discreetly proactive, profoundly legible. Generous
negative space gives every decision room to breathe; deliberate contrast directs visual
energy strictly toward the current, single most important thing — never toward decoration.

## Colors

A high-fidelity light workspace grounded in warm porcelain surfaces and deep architectural
ink, replacing the previous dark "Premium Noir" palette.

- **Canvas & Surfaces:** Warm white background (`#f7f8fa`) softens eye strain versus a
  sterile pure white; elevated cards/inputs use pure white (`#ffffff`) with a hairline
  border (`#eaebed`) rather than a shadow-heavy look.
- **Ink:** Deep navy (`#0b1020`) carries headlines, body text, and primary button fills.
  Secondary/metadata text uses a muted slate (`#64748b`) to stay quiet.
- **Accent — Electric Violet (`#5b3ce6`):** Used sparingly: focus rings, active filter/chip
  states, hover links. Never used as a generic background fill.
- **Success — Pale Mint (`#22e0c5` / tint `#e6faf6`):** Reserved for completed/resolved
  states (the done-checkbox fill and checkmark) — a quiet confirmation, not an alarm color.
- **Error (`#ba1a1a`):** Overdue tasks and validation errors.

## Typography

A two-family pairing: **Newsreader** for editorial moments, **Manrope** for everything
operational.

- **Headlines (Newsreader):** Screen titles only (e.g. "Heute.") — an intimate, considered
  tone rather than a generic SaaS header.
- **Everything else (Manrope):** Task titles, inputs, chips, metadata — kept as the existing
  body typeface for legibility at a glance; unchanged from before this design refresh.

## Layout & Spacing

Unchanged from before: a single centered reading column (`max-w-2xl`), no sidebar, no grid
system. Spacing follows the same 4px rhythm as before, just realized in the new palette.

## Elevation & Depth

Depth via **hairline borders + soft ambient shadow**, not heavy drop-shadows:

- **Ghost borders:** Cards and inputs are framed by a 1px `#eaebed` border against the
  `#f7f8fa` background.
- **Ambient focus elevation:** The capture bar lifts on focus with a soft, violet-tinted
  diffused shadow (`0 8px 32px rgba(91,60,230,0.08)`).
- **Frosted header:** The top navigation bar is translucent white with a backdrop blur, so
  content integrates invisibly as the page scrolls underneath it.

## Shapes

- **Interactive pills:** Buttons, filter chips, and date/category tags are fully rounded
  (`rounded-full`) — unchanged from before, now in the new palette.
- **Cards:** The capture bar uses a generous `2rem` (`--radius-task`) radius — softer than
  the previous theme's `0.5rem`, in line with the new design's rounder, more tactile feel.

## Components

### Buttons
- **Primary:** Solid ink (`#0b1020`) background, background-colored (near-white) text, pill
  radius. Replaces the previous violet-to-blue gradient button.
- **Filter/toggle chips:** Transparent with a hairline border; active state uses a 10%
  accent-tinted fill with accent-colored text and border (unchanged pattern, new colors).

### Cards (Capture bar)
- White fill, hairline border, `2rem` radius, soft ambient shadow that intensifies on focus
  with a 1.5px violet ring.

### Lists & Task Rows
- Borderless rows with a light hover fill, 12px vertical rhythm between rows. The
  circular completion control is an outlined circle that fills with the mint tint and a
  crisp mint checkmark on completion — no heavy card treatment per row, keeping the list
  dense and calm rather than turning every task into its own bordered card.

### Input Fields
- Frameless within the capture card, or pill-enclosed for inline edits (date, chips).
  Focus state: 1.5px accent ring, no heavy glow.
