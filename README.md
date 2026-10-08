# Trix UI

A Tailwind v4-first design system and UI kit prototype for building polished, accessible interfaces.

## Overview

Trix UI is a CSS-first component library that follows the principles in the plan document: semantic class names, framework-agnostic HTML/CSS, minimal optional JavaScript, and strong theming support.

This repository contains a working demo and the first standalone stylesheet package build. The package is not published to npm yet.

## Goals

- Tailwind v4 only
- Semantic classes such as `dn-btn`, `dn-card`, `dn-input`
- Framework-agnostic components that work in plain HTML or any UI stack
- Accessible defaults with good focus states and contrast
- Theme support with light/dark mode and reusable semantic tokens
- Small optional JS layer only for behaviors CSS cannot handle cleanly

## Tech stack

- Vite
- Tailwind CSS v4
- Vanilla JavaScript for the demo shell
- CSS variables and `@theme` tokens for styling foundations

## Quick start

1. Install dependencies:

```bash
npm install
```

2. Start the dev server:

```bash
npm run dev
```

3. Build for production:

```bash
npm run build
```

4. Preview the production build:

```bash
npm run preview
```

## How to use this prototype

The demo UI is composed in `src/main.js`. Reusable tokens and `dn-*` component styles live in `src/trix-ui.css`; `src/styles.css` adds only the demo's global page styling.

### Use the built stylesheet in another project

Build Trix UI first:

```bash
npm install
npm run build
```

From a sibling project, install this repository as a local package (replace the path with the location of this checkout):

```bash
npm install file:../diane-tailwind
```

Once published, install it by package name:

```bash
npm install trix-core
```

Import the stylesheet from your app's CSS entry:

```css
@import "trix-core/style.css";
```

Then use the component classes in your HTML:

Example markup:

```html
<button class="dn-btn dn-btn-primary" type="button">Primary action</button>
<button class="dn-btn dn-btn-soft" type="button">Secondary</button>

<input class="dn-input" type="email" placeholder="Email address" />

<div class="dn-card">
  <h2>Card heading</h2>
  <p>Example text inside a styled card component.</p>
</div>
```

The compiled stylesheet includes Tailwind's base reset and Trix UI's semantic styles. Consuming projects do not need Tailwind just to use the components. For plain HTML without a bundler, copy `node_modules/trix-core/dist/trix-ui.css` into your public assets and link it:

```html
<link rel="stylesheet" href="/assets/trix-ui.css" />
```

### Component naming convention

The design language uses a `dn-` prefix to keep it distinct and predictable:

- `dn-btn`
- `dn-btn-primary`
- `dn-btn-soft`
- `dn-btn-ghost`
- `dn-input`
- `dn-card`
- `dn-table`
- `dn-nav`
- `dn-modal-card`

This keeps component styling consistent without overloading generic utility classes.

## Theming

The prototype includes a simple theme system driven by `data-theme` and light/dark preference handling.

Examples:

```html
<html data-theme="default">
```

```html
<html data-theme="ocean">
```

The theme variables are defined in `src/trix-ui.css`. The current presets are `default`, `ocean`, `forest`, and `sunset`. Toggle forced dark mode by adding `class="dark"` to `<html>`.

## Project structure

```text
.
├── src/
│   ├── main.js
│   ├── styles.css
│   ├── trix-ui.css
│   └── library.js
├── index.html
├── tailwind-ui-kit-plan.md
├── README.md
├── package.json
├── vite.config.js
├── .gitignore
└── dist/   (generated after build)
```

## Current status

This is an early prototype, not yet published to npm. `npm run build` creates both the demo site and a standalone stylesheet at `dist/trix-ui.css`. The project currently demonstrates:

- layout + dashboard shell
- semantic button styles
- form controls
- cards and data display
- tables and feed/list patterns
- navbar + modal/demo composition

## Development workflow

Use the repo like this:

1. Add reusable tokens or component classes in `src/trix-ui.css`
2. Render it in `src/main.js`
3. Validate with:

```bash
npm run build
```

4. Iterate on theme and spacing until the component feels consistent across all variants

## Roadmap

Planned expansion includes:

- reusable sidebar variants
- modal and dropdown interactions
- complete auth screens
- dashboard templates
- tables with filters and actions
- browser-based accessibility and visual tests
- package distribution metadata and release workflow

## Notes

This repo is intentionally lightweight and focused on proving the design system direction. It is meant to evolve into a full component library while remaining accessible, simple, and easy to use without framework lock-in.

For the full system direction and product plan, see [tailwind-ui-kit-plan.md](./tailwind-ui-kit-plan.md).
