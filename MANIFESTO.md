# MANIFESTO.md

Non-negotiable simplicity principles. Every change is checked against this list before
it ships. When in doubt, this file wins over a feature request.

**01 Simplicity is the product** — Simplicity is not a design style, it is the most
important product value.

**02 Built for one** — Every decision optimizes the productivity of a single person.
No team features, ever.

**03 One input** — The user never has to decide first which system something belongs to.

**04 Five seconds to capture** — In the default case, the title is the only required field.

**05 Today matters** — The main view answers exactly one question: what needs my attention
today?

**06 Done matters** — Completed items are not discarded. They become part of the work
memory.

**07 Do not make users manage the system** — No mandatory tags, workflows, projects, or
cleanup rituals.

**08 AI reduces complexity** — More intelligence must lead to less interface, never more.

**09 Complexity below the surface** — Integrations and data logic are allowed to be
complex. The UI stays calm.

**10 No feature without a simplicity test** — New functionality must reduce complexity for
the user, or clearly justify why it doesn't.

## Simplicity metrics (hold the line)

| Metric                    | Target                        |
|----------------------------|-------------------------------|
| Capture a task              | < 5 seconds                   |
| Understand "Heute"           | < 10 seconds                  |
| Complete a task              | 1 click                       |
| Required fields              | 1 (title)                     |
| Main navigation entries      | max 3–4                       |
| Setup to first task          | < 60 seconds                  |
| New feature                  | ideally no new main screen    |

## Pre-release checklist

- Can a new user capture a task without explanation?
- Is the title still the only required field?
- Is "Heute" understandable in < 10 seconds?
- Is the core action reachable in one click?
- Was a new screen actually necessary?
- Does mobile work without separate reasoning?
- Are ownership and core-behavior tests green?
- No secrets/PII in logs or the repo?
- Does the change contradict any manifesto principle?
- Can something be removed before something new ships?
