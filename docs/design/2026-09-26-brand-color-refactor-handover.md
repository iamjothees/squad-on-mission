# Brand color refactor — handover

**Date:** 2026-09-26
**Branch:** `main` (direct commit — CSS token values + Breeze-leftover cleanup, no schema/behavior change)
**Canonical source:** [Byte & Brand Design System](https://claude.ai/artifact/8y3WrzaEzwuE4jx8QUMCBx) — read its README before changing any value in `app.css` again; this doc records what changed *here*, that one is the source of truth for *what the values should be*.

## What changed

Angaadi Store's admin already had a token-based theming architecture (semantic CSS variables in `resources/css/app.css`, re-exported to Tailwind's color namespace, consumed everywhere as `bg-surface`/`text-fg-muted`/etc.). This refactor did two things:

1. **Swapped the seven core token values** (both themes) from a generic indigo/slate palette to the Byte & Brand palette — one file, `resources/css/app.css`.
2. **Brought 9 files that bypassed the token system onto it** — mostly the Breeze authentication scaffold, which was never migrated when the admin shell adopted tokens.

No component structure changed, no new tokens were introduced, no migration/schema/test behavior changed. This is a pure visual refactor; the full Pest + Playwright suite passed unmodified before and after.

## Before / after

| Token | Old (dark) | New (dark) | Old (light) | New (light) |
|---|---|---|---|---|
| `bg` | `#020617` | `#0B0A0A` | `#f8fafc` | `#FAFAF9` |
| `surface` | `#0f172a` | `#17151A` | `#ffffff` | `#FFFFFF` |
| `border` | `#1e293b` | `#33303B` | `#e5e7eb` | `#DDDAD8` |
| `fg` | `#e2e8f0` | `#F5F3F0` | `#0f172a` | `#1C1917` |
| `fg-muted` | `#94a3b8` | `#A8A29E` | `#64748b` | `#78716C` |
| `accent` | `#6366f1` (indigo) | `#DD6C26` (brand orange) | `#4f46e5` (indigo) | `#BA4E12` (brand orange) |
| `accent-fg` | `#ffffff` | `#ffffff` | `#ffffff` | `#ffffff` |
| `sidebar` | `#0f172a` | `#17151A` | `#f1f5f9` | `#F1EEEB` |
| `sidebar-fg` | `#cbd5e1` | `#C9C4BE` | `#475569` | `#57534E` |
| `sidebar-fg-strong` | `#ffffff` | `#ffffff` | `#0f172a` | `#1C1917` |
| `sidebar-border` | `#1e293b` | `#33303B` | `#e2e8f0` | `#E2DEDA` |
| `sidebar-active` | `#1e293b` | `#2A2730` | `#e2e8f0` | `#E2DEDA` |

**Every pairing above was checked against WCAG AA (4.5:1 body text, 3:1 large text/UI components) before landing** — the derivation and the exact contrast ratios are in the Design System artifact's README, not repeated here. The one deliberate exception is `border`, which sits around 1.3–1.5:1 against its surface in both themes — matching the *previous* palette's own precedent (the old indigo/slate tokens were at ~1.2–1.4:1), not a new decision. `success`/`danger`/`info` (raw Tailwind `emerald-600`/`red-600`/`sky-600` classes, not CSS variables) were **not** changed — see "What deliberately didn't change" below.

## Files touched

**Token definitions:**
- `app/resources/css/app.css` — the two `:root`/`.dark` blocks

**Brought onto the token system (previously hardcoded Tailwind gray/indigo, ignoring dark mode entirely):**
- `app/resources/views/components/primary-button.blade.php` — was `bg-gray-800`/`focus:ring-indigo-500` with **no dark-mode variant at all**; this is the main call-to-action button used on every admin screen, so it was the highest-impact fix in this pass
- `app/resources/views/components/modal.blade.php` — backdrop was `bg-gray-500 opacity-75` (wrong tint on a dark page); now `bg-black/60`, matching `<x-confirm-dialog>`'s existing backdrop treatment
- `app/resources/views/layouts/guest.blade.php` — the whole pre-login wrapper had zero dark-mode support (no blocking theme script, hardcoded `text-gray-900`/`bg-gray-100`); now mirrors `layouts/admin.blade.php`'s pre-paint script (same `admin-theme` localStorage key, so a preference set in the admin shell also applies before login) and uses tokens throughout
- `app/resources/views/livewire/pages/auth/login.blade.php` — checkbox/links were `indigo-600`/`gray-300`/`gray-600`
- `app/resources/views/livewire/pages/auth/verify-email.blade.php`, `confirm-password.blade.php`, `forgot-password.blade.php` — body text was `gray-600`
- `app/resources/views/livewire/profile/update-profile-information-form.blade.php` — one `focus:ring-indigo-500`

## What deliberately didn't change

- **`success`/`danger`/`info` stay standard Tailwind red/green/blue**, not brand-derived. The brand accent is itself a warm orange, too close to the hue people read as "caution" — see the Design System README's "why state colors aren't brand-ified" for the full reasoning. This was a design decision, not an oversight.
- **`resources/views/welcome.blade.php`** — Laravel's default stub page, routed at `/` but not linked from anywhere in the admin (the app has no public storefront). Left with its stock light/dark styling rather than reskinned; low-traffic and not part of "the system" a user actually interacts with. Revisit if `/` is ever repurposed.
- **No new CSS variables were added** for `success`/`danger`/`info` — the app already expresses those as plain Tailwind utility classes (`text-emerald-600`, `text-red-600`, `text-sky-600`) scattered across admin views, and centralizing them into tokens wasn't part of this pass's approved scope. If a future project wants single-source-of-truth control over state colors, that's a real (small) follow-up, not something this refactor silently half-did.

## How this was verified

Both themes were confirmed with **objective pixel sampling of the rendered screenshots**, not by eye — `getComputedStyle` alone wasn't trusted, since a compositor-level rendering quirk in this environment's headless Chromium made two consecutive screenshots visually indistinguishable to the naked eye despite one being genuinely light-themed. Sampling actual PNG bytes with Pillow settled it:

```
campaigns-dark.png  -> (11, 10, 10)    == #0B0A0A  ✓
campaigns-light.png -> (250, 250, 249) == #FAFAF9  ✓
button (dark)        -> (221, 108, 38) == #DD6C26  ✓
button (light)        -> (186, 78, 18) == #BA4E12  ✓
```

**Takeaway for anyone re-verifying a theme visually in this environment:** don't trust a rendered screenshot by eye alone if the two themes look suspiciously similar — sample actual pixel values. `getComputedStyle` proves the CSS is correct; it does not prove the paint matches (they were confirmed to match here, but only after independent pixel verification).

Also run:
- `docker compose exec angaadistore sh -lc './vendor/bin/pest'` — 302/302, unchanged (no behavior touched)
- `npm run test:browser` — 12/12, unchanged
- `./vendor/bin/pint --test` / `./vendor/bin/phpstan analyse` — both clean

## Rollback

Every value in the "Before / after" table is the complete rollback set — restore the old column into `app.css`'s `:root`/`.dark` blocks. The 9 leftover-file fixes are independent and don't need to roll back together with the token swap; they're improvements to files that were broken (no dark-mode support) regardless of which palette wins.

## For the next person touching this

- Don't hand-edit hex values in `app.css` — change them in the [Design System artifact](https://claude.ai/artifact/8y3WrzaEzwuE4jx8QUMCBx) first (with a contrast check), then copy the values here. That artifact is shared across Byte & Brand projects; a value that drifts here and not there is exactly the failure mode a shared design system exists to prevent.
- If you add a component that needs a color this system doesn't have, add the token to the Design System first — never introduce a one-off hardcoded color and "fix it later."
- See `docs/design/brand-guidelines.md` in this repo for the consumption guide aimed at *new* projects, and `app/CLAUDE.md`'s "Admin shell" section for how the theme-toggle mechanism itself works (localStorage key, blocking pre-paint script, `@custom-variant dark`).
