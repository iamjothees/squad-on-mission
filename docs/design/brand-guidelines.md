# Byte & Brand — color & theming guidelines for new projects

This is the quick-start version. The canonical, always-current source is the
**[Byte & Brand Design System](https://claude.ai/artifact/8y3WrzaEzwuE4jx8QUMCBx)** — its README has the
full derivation, every token's contrast ratio, and live component previews in both themes. When
this file and that artifact disagree, the artifact wins; update this file to match, not the other
way around.

## The one rule

**One brand color, everywhere else neutral.** Byte & Brand is a single warm orange (`accent`) on a
disciplined near-black/off-white neutral scale — that's the whole identity, sampled directly from
[byteandbrand.online](https://byteandbrand.online). Don't add a second brand color, a gradient, or
a multi-color palette to "make it pop." If a project genuinely needs a second accent, that's a
deliberate design decision to make explicitly (and add to the Design System first) — not a default
to reach for.

## Every project needs both themes

Dark **and** light, always — dark is the brand's primary/default expression (the marketing site
defaults to it and stays there), but light must be fully supported, not an afterthought. Every
token below has a validated value for both.

## The tokens

| Token | Dark | Light | Use for |
|---|---|---|---|
| `bg` | `#0B0A0A` | `#FAFAF9` | page background |
| `surface` | `#17151A` | `#FFFFFF` | cards, panels, rows, modals |
| `border` | `#33303B` | `#DDDAD8` | dividers (decorative — see note below) |
| `fg` | `#F5F3F0` | `#1C1917` | primary text, headings |
| `fg-muted` | `#A8A29E` | `#78716C` | secondary text, placeholders |
| **`accent`** | `#DD6C26` | `#BA4E12` | **the brand color** — primary buttons, focus rings, links, selected states |
| `accent-fg` | `#FFFFFF` | `#FFFFFF` | text/icons on an accent-filled surface |
| `success` | `#059669` | `#059669` | positive state (unbranded — see below) |
| `danger` | `#DC2626` | `#DC2626` | destructive actions, errors (unbranded) |
| `info` | `#0284C7` | `#0284C7` | neutral informational state (unbranded) |

A project with a navigation rail (sidebar/nav) also gets `sidebar`, `sidebar-fg`,
`sidebar-fg-strong`, `sidebar-border`, `sidebar-active` — see the Design System's `tokens.json`
for values; they follow the same pattern (a visually distinct rail, not identical to `surface`).

### Why state colors aren't brand-colored

The brand orange sits close to the hue people already read as "warning" — an orange "success"
badge would fight that instinct. Brand identity lives in `accent` and nowhere else; state colors
stay the conventional green/red/blue every user already recognizes.

### Why `border` looks so faint

It's deliberately low-contrast (~1.3–1.5:1) — a card divider is decorative, not a control
boundary. Meaningful UI boundaries (input edges, checkboxes, focus indicators) must still hit 3:1
per WCAG 1.4.11 — get that from a 2px `accent` focus ring or a visible `border` on the control
itself, never rely on this token alone for something a user needs to *operate*.

## Setting it up (Tailwind v4)

```css
@custom-variant dark (&:where(.dark, .dark *));

:root {
  color-scheme: light;
  --bg: #FAFAF9;
  --surface: #FFFFFF;
  --border: #DDDAD8;
  --fg: #1C1917;
  --fg-muted: #78716C;
  --accent: #BA4E12;
  --accent-fg: #FFFFFF;
}

.dark {
  color-scheme: dark;
  --bg: #0B0A0A;
  --surface: #17151A;
  --border: #33303B;
  --fg: #F5F3F0;
  --fg-muted: #A8A29E;
  --accent: #DD6C26;
  --accent-fg: #FFFFFF;
}

@theme inline {
  --color-bg: var(--bg);
  --color-surface: var(--surface);
  --color-border: var(--border);
  --color-fg: var(--fg);
  --color-fg-muted: var(--fg-muted);
  --color-accent: var(--accent);
  --color-accent-fg: var(--accent-fg);
}
```

**The two-step indirection matters.** Define raw values under plain names (`--bg`) on `:root`/`.dark`,
then re-export to Tailwind's namespace via `@theme inline`. Naming the `:root` variable the same as
its `@theme inline` output (`--color-x: var(--color-x)`) is a self-reference Tailwind silently
drops — this is the single most common way this pattern breaks.

Then use plain utilities everywhere: `bg-surface`, `text-fg-muted`, `border-border`,
`bg-accent text-accent-fg`. **Never hardcode `dark:bg-slate-900` or similar** — every component
must switch theme by reading these tokens, not by carrying its own dark-mode override. If a
project ships with any hardcoded `gray-*`/`slate-*`/`indigo-*` color classes, that's tech debt from
day one, not a shortcut — see Angaadi Store's own handover doc
(`docs/design/2026-09-26-brand-color-refactor-handover.md`) for what a full backlog of that debt
looks like months later.

**Toggle `.dark` on `<html>` from a blocking inline `<head>` script reading `localStorage`**, not
from `prefers-color-scheme` alone — people should be able to override their OS preference
per-app, and a blocking script avoids a flash of the wrong theme on load:

```html
<script>
  (function () {
    var preference = localStorage.getItem('admin-theme') ?? 'system';
    var dark = preference === 'dark'
      || (preference === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
    document.documentElement.classList.toggle('dark', dark);
  })();
</script>
```

Use the exact key `admin-theme` if this is a Byte & Brand internal tool a person might use
alongside other Byte & Brand admin panels in the same browser — sharing the key means a theme
preference set once applies everywhere, which is the point.

## Not using Tailwind?

The `:root`/`.dark` block above is framework-agnostic CSS custom properties — drop it into any
global stylesheet and reference `var(--bg)`, `var(--accent)`, etc. directly. Skip the
`@theme inline` step; that's Tailwind-specific.

## Accessibility is not optional

Every pairing in the table above passed WCAG AA (4.5:1 body text, 3:1 large text/UI components)
before being added — checked with the actual relative-luminance formula, not eyeballed. **If you
add a new token or a new color combination, run it through a contrast checker against every
surface it will sit on before shipping.** The Design System artifact's README has the exact
numbers for every existing pairing, including why `accent` uses a slightly different value in each
theme (the vivid brand orange doesn't clear AA text contrast against white without darkening).

## Components

The Design System has two starter components (`Button`, `StatusBadge`) with live previews and
usage guidelines — read them before building your own from scratch. Extract a new component into
the shared system once a **second** project needs the same pattern, not speculatively.

## Changing any of this

This is a living system. Need a token that doesn't exist yet? Add it to the Design System first
(with a contrast check against every surface it'll touch), *then* consume it in your project —
never invent a one-off color locally and backfill the system later. If you change an existing
value (not just add one), say what consumed it, so every other project sharing this system can
tell whether the change affects them.
