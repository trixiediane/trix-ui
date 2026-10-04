# Plan: Tailwind UI Kit (Components, Blocks, Layouts, Templates, Themes)

**Core rule: Tailwind only.** No Alpine.js, no JS framework dependency. Everything is HTML + Tailwind classes + CSS. Interactivity comes from native HTML and modern CSS (see Section 5).

## 1. Define the product first

Decide these before writing code, because they shape everything else.

| Decision | Recommendation |
|---|---|
| **What is it?** | A design system on top of Tailwind: themes + components + blocks + full-page templates |
| **Tailwind version** | v4 only (CSS-first `@theme`, no `tailwind.config.js` needed). Don't spend time on v3 compatibility. |
| **Distribution** | Hybrid: an npm package (tokens + themes + base styles + component CSS) **and** a copy-paste registry for blocks/templates (shadcn-style) |
| **Dependencies** | Zero JS dependencies. Tailwind + native HTML/CSS only. |
| **Framework support** | Plain HTML + Tailwind classes is the source of truth. Blade/Livewire, Vue, React wrappers are optional, markup-only, and come later. |
| **Interactivity** | Native HTML (`<details>`, `<dialog>`, Popover API, `:has()`, `peer`/`group`) instead of a JS library |
| **Target user** | Devs who want a professional look without designing from scratch |
| **Differentiator** | Pick one: dashboards/admin focus, best theming, best a11y, or Laravel-first |

A narrower niche (for example "admin and dashboard kit") is much easier to finish than "everything."

## 2. Architecture (6 layers)

1. **Tokens**: colors, type, spacing, radius, shadows, motion, defined as CSS variables in `@theme`
2. **Themes**: named sets of semantic token overrides (brand color, radius, font, density) applied via `data-theme`
3. **Base**: opinionated resets, typography defaults, focus rings, scrollbars, form defaults
4. **Components**: Button, Input, Card, Modal, and so on (atomic, reusable)
5. **Blocks**: composed sections (pricing, stats row, data table with toolbar)
6. **Layouts and templates**: app shell, sidebar layout, auth layout, and full example pages (dashboards)

Rule: each layer only depends on the layers below it. Components never hardcode colors; they only use semantic tokens, so every theme works automatically.

## 3. Design system foundations

- **Color**: a neutral scale, a primary scale (11 steps, OKLCH), and semantic colors (success, warning, danger, info).
- **Semantic tokens**: `--color-surface`, `--color-surface-raised`, `--color-border`, `--color-fg`, `--color-fg-muted`, `--color-primary`, `--color-primary-fg`, `--color-ring`. Components use only these.
- **Typography**: one sans stack (Inter or Geist), a mono font, and a type scale with line-heights.
- **Radius, shadow, spacing**: 3–4 radius steps and 4–5 soft elevation levels. Consistency here is what makes it look "professional."
- **Density**: optional `compact` / `comfortable` mode via a variable (great for dashboards).
- **Motion**: a few durations and easings, with `prefers-reduced-motion` respected.

## 4. Theming system

Two independent axes, so they can be combined freely:

| Axis | Values | Controlled by |
|---|---|---|
| **Color mode** | `light`, `dark`, `system` | `class="dark"` or `data-mode` on `<html>` (or pure CSS via `prefers-color-scheme`) |
| **Theme** | `default`, `ocean`, `forest`, `sunset`, `violet`, `mono`, `high-contrast` | `data-theme="..."` on `<html>` |

Every theme works in both light and dark. That means 7 themes x 2 modes = 14 combinations, all driven by variables.

### 4.1 What a theme controls

- **Brand colors**: the primary scale and ring/focus color
- **Neutrals**: warm, cool, or pure gray tint
- **Radius**: sharp (`0.25rem`), rounded (`0.5rem`), soft (`0.75rem`), pill for buttons and inputs
- **Font**: sans family (and optional heading font)
- **Shadow style**: flat (borders only), soft, or elevated
- **Density**: compact or comfortable default
- **Chart palette**: 5–6 chart colors so chart libraries can match the theme

### 4.2 Preset themes (v1)

| Theme | Primary | Neutrals | Feel |
|---|---|---|---|
| **Default** | Indigo | Cool gray | Clean SaaS, safe default |
| **Ocean** | Blue / cyan | Slate | Calm, corporate |
| **Forest** | Emerald | Stone | Natural, finance/health |
| **Sunset** | Orange / rose | Warm gray | Friendly, energetic |
| **Violet** | Violet / fuchsia | Zinc | Creative, modern |
| **Mono** | Black / white | Pure gray | Minimal, Vercel-like |
| **High contrast** | Strong blue | Pure gray | Accessibility (WCAG AAA targets) |

### 4.3 How it works (CSS)

```css
/* tokens: defaults live in @theme */
@theme {
  --color-primary: oklch(0.55 0.22 270);
  --color-primary-fg: oklch(0.99 0 0);
  --radius-md: 0.5rem;
  --font-sans: "Inter", system-ui, sans-serif;
}

/* semantic tokens, light */
:root {
  --color-surface: oklch(1 0 0);
  --color-fg: oklch(0.21 0.02 265);
  --color-border: oklch(0.92 0.01 265);
}

/* semantic tokens, dark */
.dark {
  --color-surface: oklch(0.19 0.02 265);
  --color-fg: oklch(0.96 0.01 265);
  --color-border: oklch(0.3 0.02 265);
}

/* theme override: only the variables that change */
[data-theme="ocean"] {
  --color-primary: oklch(0.58 0.15 240);
  --radius-md: 0.375rem;
}

[data-theme="ocean"].dark {
  --color-primary: oklch(0.7 0.14 235);
}
```

Swapping the brand color is a single variable change. Adding a new theme is one small CSS block.

### 4.4 Theme switching without a JS library

- **System mode**: pure CSS with `@media (prefers-color-scheme: dark)`, no JS at all.
- **Theme switcher (CSS-only)**: a group of radio inputs inside the page, with root selectors like `html:has(#theme-ocean:checked) { ... }` applying the theme. Works with zero JS, but the choice resets on reload.
- **Persistence (optional)**: remembering the choice across reloads needs a tiny inline vanilla script (about 5 lines, no library) in `<head>`. This is the only JS in the kit, it is optional, and it is documented as a copy-paste snippet. Without it, the kit still works; users just set `data-theme` themselves (e.g. server-side from a cookie, which fits Laravel well).
- **Scoped themes**: `data-theme` can be set on any element, so a single card or section can use a different theme (nice for docs previews).
- **Forced colors / high contrast**: respect `forced-colors` and `prefers-contrast`.

### 4.5 Theme builder (docs site)

- Pick a primary color, neutral tint, radius, font, and density with live preview on real components
- Auto-generate the 11-step OKLCH scale from one base color
- Contrast checker per token pair (warn when below AA)
- Export as a CSS snippet, a `@theme` block, or JSON for the registry
- The builder lives on the docs site only, so it doesn't add JS to the package itself

### 4.6 Custom theme API

```html
<html data-theme="my-brand" class="dark">
```

```css
[data-theme="my-brand"] {
  --color-primary: oklch(0.6 0.2 30);
  --radius-md: 0.75rem;
}
```

Users can create a theme by overriding only what they need. Everything else falls back to the default.

### 4.7 Theme quality rules

- Every theme must pass WCAG AA contrast in both modes (checked in CI)
- Every component and block is screenshot-tested in all themes
- No component may use a raw color (`bg-indigo-600`); only semantic tokens (`bg-primary`)

## 5. Interactivity strategy (Tailwind only, no Alpine)

Use native HTML features plus Tailwind v4 variants (`peer-*`, `group-*`, `has-*`, `open:`, `starting:`, `target:`).

| Component | Tailwind-only technique |
|---|---|
| Accordion / FAQ | `<details>` / `<summary>` with `open:` and `group-open:` variants (`name` attribute for exclusive groups) |
| Modal / Dialog | Native `<dialog>`, opened with HTML invoker attributes (`command` / `commandfor`), styled with `backdrop:` and `starting:` for transitions |
| Drawer / slide-over | `<dialog>` or Popover API, slide animation via `starting:` + `transition-discrete` |
| Dropdown menu, Popover | Popover API (`popover` + `popovertarget`), positioned with CSS anchor positioning |
| Tooltip | `group-hover` / `group-focus-visible` with `role="tooltip"` |
| Tabs | Radio inputs + `peer-checked` / `has-checked`, or `:target` |
| Mobile sidebar toggle | Checkbox + `peer-checked`, or `<dialog>` for the drawer |
| Collapsible sidebar | Checkbox + `has-checked` on the layout wrapper |
| Switch, Checkbox, Radio | Native input + `peer` styling |
| Toast | `popover="manual"` + CSS animation for auto-dismiss, close via `popovertargetaction="hide"` |
| Alert dismiss, Badge removal | Popover hide action or `:has()` |
| Progress, Skeleton, Spinner | Pure CSS animations |
| Dark mode / theme | CSS media queries, `:has()` on radios (see 4.4) |

### What can't be done with CSS only

Be upfront about this in the docs. These need real JavaScript, so they are handled differently:

- **Combobox / Autocomplete, Multi-select, Tag input**
- **Date picker, Time picker, Calendar logic**
- **Command palette (⌘K)**: needs keyboard shortcut and filtering
- **Data table sorting, filtering, selection**
- **File upload preview**, **Slider tooltip value**, **Toast auto-queueing**

Approach for these: ship the **styled markup and states only** (with documented ARIA and `data-*` hooks), mark them "bring your own behavior" in the docs, and keep them out of the MVP. Users can wire them with their own JS (or Livewire/Vue/React) without the kit forcing a dependency.

### Browser support note

Popover API, `<dialog>` invoker commands, `:has()`, and CSS anchor positioning are only in recent browsers. Decide the support target early (for example "last 2 versions of evergreen browsers"), check current support before building each component, and add simple fallbacks (`group-hover`, `:focus-within`) where it's cheap.

## 6. Component inventory

**Phase 1: Core (MVP), all achievable without JS**
- Button (variants: solid, soft, outline, ghost, link, destructive; sizes; loading state via CSS; icon-only)
- Input, Textarea, Select (native), Checkbox, Radio, Switch, Label, Help/Error text
- Card, Badge, Avatar, Divider
- Alert, Toast (popover-based)
- Tabs, Breadcrumb, Pagination (styled links)
- Table (basic, styled)
- Tooltip, Dropdown menu, Modal/Dialog, Drawer
- Accordion, Progress, Skeleton, Spinner
- **Theme switcher** (CSS-only, with optional persistence snippet)

**Phase 2: Advanced (markup + styling; behavior is bring-your-own)**
- Combobox / Multi-select, Date picker, Time picker
- Command palette (⌘K)
- Stepper, Popover variants
- File upload / dropzone
- Data table (visual states: sort icons, selected rows, empty state, toolbar)
- Tag input, Slider, Rating (CSS-only star rating is possible with radios)
- Empty state, Stat card, Timeline, Calendar layout
- **Appearance settings panel** (theme, mode, radius, density)

## 7. Blocks (composed sections)

- **Navigation**: top navbar, mega menu, mobile nav, user menu
- **Sidebars**: collapsible, icon-only rail, nested groups (via `<details>`), workspace switcher
- **Page headers**: title + actions + breadcrumbs + tabs
- **Stats and KPIs**: stat cards, sparkline cards (inline SVG), comparison cards
- **Data**: styled tables, list views, kanban columns (layout only), activity feeds
- **Forms**: settings forms, multi-step wizard layout, filter bars
- **Auth**: login, register, forgot/reset password, 2FA, split-screen variants
- **Marketing (optional)**: hero, features, pricing, testimonials, CTA, FAQ, footer
- **Feedback**: 404/500 pages, empty states, onboarding checklist
- **Overlays**: confirm dialog, slide-over detail panel, notifications panel
- **Settings**: appearance settings block (theme, mode, density, radius)

## 8. Layouts

- App shell with a fixed sidebar
- App shell with a top nav
- Sidebar + secondary panel (like email or settings)
- Centered auth layout
- Split-screen auth layout
- Docs/content layout (sidebar + TOC)
- Full-height "workspace" layout (header + sidebar + scrollable main)

All layouts should be responsive: mobile drawer sidebar (checkbox or dialog), tablet collapse, desktop expanded. All layouts include a slot for the theme switcher.

## 9. Full examples and templates

1. **Analytics dashboard** (KPIs, chart areas, recent activity, table)
2. **Admin/CRUD panel** (users table, detail slide-over, create/edit forms)
3. **E-commerce admin** (orders, products, customers)
4. **SaaS settings area** (profile, billing, team, API keys, appearance)
5. **Project management** (kanban layout, tasks list, calendar layout)
6. **Auth flow set**
7. **Landing page** (if you include marketing blocks)

Every template ships with a live theme switcher so users can preview all themes on real pages.

**Charts:** a chart library is real JS, so it stays out of the package. Provide chart *containers and style guidance* plus the theme chart palette as CSS variables. For static or simple charts, use inline SVG / CSS-only bars and sparklines. Document how to connect Chart.js or ApexCharts for users who want them.

## 10. Repo structure (monorepo)

```
/packages
  /tokens        → CSS variables, @theme, color scales
  /themes        → preset themes (default, ocean, forest…), optional persistence snippet
  /core          → base styles + component CSS (the npm package, CSS only)
  /registry      → JSON registry of blocks/templates/themes for CLI copy-paste
  /cli           → `npx yourkit add sidebar-01` and `npx yourkit add theme ocean`
  /blade         → (later, optional) Blade components, markup-only wrappers
  /vue           → (later, optional) markup-only wrappers
  /react         → (later, optional) markup-only wrappers
/apps
  /docs          → documentation + live previews + theme builder
  /examples      → full templates (dashboard, etc.)
```

Start with **tokens + themes + core + docs**. Framework wrappers come later and stay optional.

## 11. Tooling

- **Monorepo**: pnpm workspaces + Turborepo
- **Docs site**: Astro Starlight or VitePress, with live preview, copy-code button, and a global theme switcher
- **Component dev**: use the docs site with isolated previews (Storybook is optional)
- **Testing**: Playwright (visual regression across themes and modes, plus keyboard and dialog/popover interaction), axe-core (a11y checks in CI)
- **Linting**: Prettier with `prettier-plugin-tailwindcss`, Stylelint (block raw colors in component CSS)
- **Build**: Tailwind CLI / Vite with Lightning CSS for the CSS output
- **Releases**: Changesets, semantic versioning, and a CHANGELOG
- **CI**: GitHub Actions for lint, test, build, size check, theme contrast check, and docs deploy

## 12. Quality standards (non-negotiable)

- **Accessibility**: WCAG 2.2 AA, with keyboard navigation, focus management, ARIA, and contrast checks per theme. Prefer native elements (`<dialog>`, `<details>`, `<button>`) since they give a11y for free.
- **Dark mode and themes**: every component and block is tested in both modes and in all preset themes
- **Responsive**: mobile-first, tested at 360 / 768 / 1024 / 1440
- **RTL support**: use logical properties (`ms-*`, `ps-*`) from day one, because retrofitting is painful
- **Performance**: ship only the CSS used; track bundle size per component; themes are variables only, so they add almost no weight
- **Consistency checklist** per component: states (hover, focus, active, disabled, loading, error), sizes, variants, dark mode, themes, docs, and tests

## 13. Documentation

- Getting started (install, setup, theming in 5 minutes)
- **Theming guide**: how themes work, switching modes, creating a custom theme, changing brand color, fonts, radius, density
- **Theme gallery**: all presets with live previews and copy buttons
- **Theme builder**: interactive generator (see 4.5)
- **"No JS" guide**: how each interactive component works with native HTML/CSS, and which components are bring-your-own-behavior
- Each component: live demo, variants, classes, accessibility notes, copy-paste code
- Blocks gallery with filters (category, light/dark, theme)
- Template gallery with live demos and a "use this template" starter repo
- Browser support table
- Migration and upgrade guides
- Figma file with the same tokens and themes (optional but a big plus for adoption)

## 14. Roadmap

Assuming solo and part-time, roughly:

| Phase | Duration | Deliverable |
|---|---|---|
| 0. Planning | 1 week | Name, niche, browser support target, tokens, visual direction, moodboard |
| 1. Foundation | 2–3 weeks | Monorepo, tokens, base styles, docs shell, CI |
| 1.5 Theming | 1–2 weeks | Semantic tokens, light/dark, 3 starter themes (default, ocean, mono), CSS-only switcher |
| 2. Core components | 4–6 weeks | ~25 Phase 1 components (no JS), all themes and modes, fully documented |
| 3. Layouts and blocks | 4–6 weeks | App shells, navs, sidebars, ~30 blocks |
| 4. Templates | 3–4 weeks | Analytics dashboard, admin panel, auth set |
| 4.5 Theme builder | 1–2 weeks | Docs theme builder, remaining presets, theme export and registry |
| 5. Beta | 2–3 weeks | Public release, feedback, bug fixing |
| 6. v1.0 | ongoing | Phase 2 components (styling only), optional Blade/Vue/React wrappers, community themes |

## 15. Distribution and business (optional)

- **Open source core (MIT)**, with premium templates/blocks/themes as a paid tier
- Landing page with live demos and a theme switcher, plus a Product Hunt launch and posts in Laravel/Vue/Tailwind communities
- Naming: check npm, GitHub org, and domain availability early
- Licensing for paid parts: per-developer or per-project, clearly written

## 16. Risks and how to avoid them

| Risk | Mitigation |
|---|---|
| Scope creep | Lock the Phase 1 list. Nothing gets added until it ships. |
| Inconsistent design | Tokens first, and a component checklist before merging |
| Themes break components | Semantic tokens only, lint rule against raw colors, visual tests in every theme |
| Tailwind-only limits (no JS) | Use native HTML/CSS where possible, mark the rest "bring your own behavior" and be clear in the docs |
| Newer browser features not supported everywhere | Set a support target, check support per component, add cheap fallbacks |
| Too many themes to maintain | Ship 3 first, generate the rest from the theme builder, accept community themes later |
| Competing with Tailwind UI, shadcn, and Flowbite | Pick a clear niche (zero-JS, theming, dashboards) and a better DX (CLI, theming, docs) |
| Maintenance burden | Automated visual and a11y tests from the start |
| Tailwind major changes | Pin versions, stay on v4, follow the release notes |

## 17. First 2 weeks (actionable)

1. Pick the name and niche, and write a 1-page vision doc.
2. Decide the browser support target (it decides which native features you can use).
3. Collect 10–15 reference UIs (Linear, Vercel, Stripe, Radix, shadcn) and define the "feel" (spacing, radius, shadow).
4. Set up the monorepo with `tokens`, `themes`, and `core`.
5. Define the color scales and semantic tokens, with light and dark modes.
6. Create the **default theme** and one alternate theme (e.g. `ocean`) to prove the override system works.
7. Build **Button, Input, Card, Badge** end to end (variants, states, dark mode, both themes, docs page). This becomes your template for all the others.
8. Prototype one native-interactive component (a `<dialog>` modal or a `<details>` accordion) to validate the no-JS approach.
9. Set up the docs site with live preview, plus the theme and mode switcher.
10. Add CI (lint, build, axe checks, contrast check).
11. Review and adjust the component checklist based on what hurt.

## Next steps (pick one to go deeper)

- The full token + theme file (`@theme` with complete color scales for all presets)
- The exact folder structure and package.json setups
- The complete Phase 1 component spec (with the native HTML/CSS technique per component)
- The theme builder spec
