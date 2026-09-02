# DESIGN.md

netuqo's visual design system. Adopted 2026-09-02, replacing the original light/warm palette
from the Whitepaper — see DECISIONS.md for why. `resources/css/app.css`'s `@theme` block is
the token implementation; keep it in sync with this file if either changes. No 12-column grid
or sidebar navigation from this spec applies here — netuqo stays a single, calm column
(`max-w-2xl`), so only the color/type/shape/component language below was adopted, not the
layout system.

---
name: Premium Noir
colors:
  surface: '#0b1326'
  surface-dim: '#0b1326'
  surface-bright: '#31394d'
  surface-container-lowest: '#060e20'
  surface-container-low: '#131b2e'
  surface-container: '#171f33'
  surface-container-high: '#222a3d'
  surface-container-highest: '#2d3449'
  on-surface: '#dae2fd'
  on-surface-variant: '#cbc3d7'
  inverse-surface: '#dae2fd'
  inverse-on-surface: '#283044'
  outline: '#958ea0'
  outline-variant: '#494454'
  surface-tint: '#d0bcff'
  primary: '#d0bcff'
  on-primary: '#3c0091'
  primary-container: '#a078ff'
  on-primary-container: '#340080'
  inverse-primary: '#6d3bd7'
  secondary: '#adc6ff'
  on-secondary: '#002e6a'
  secondary-container: '#0566d9'
  on-secondary-container: '#e6ecff'
  tertiary: '#ddb8ff'
  on-tertiary: '#490081'
  tertiary-container: '#b175ec'
  on-tertiary-container: '#400071'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#e9ddff'
  primary-fixed-dim: '#d0bcff'
  on-primary-fixed: '#23005c'
  on-primary-fixed-variant: '#5516be'
  secondary-fixed: '#d8e2ff'
  secondary-fixed-dim: '#adc6ff'
  on-secondary-fixed: '#001a42'
  on-secondary-fixed-variant: '#004395'
  tertiary-fixed: '#f0dbff'
  tertiary-fixed-dim: '#ddb8ff'
  on-tertiary-fixed: '#2c0051'
  on-tertiary-fixed-variant: '#62259b'
  background: '#0b1326'
  on-background: '#dae2fd'
  surface-variant: '#2d3449'
typography:
  display-lg:
    fontFamily: hankenGrotesk
    fontSize: 48px
    fontWeight: '700'
    lineHeight: '1.1'
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: hankenGrotesk
    fontSize: 32px
    fontWeight: '600'
    lineHeight: '1.2'
  headline-md:
    fontFamily: hankenGrotesk
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.3'
  body-lg:
    fontFamily: manrope
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: manrope
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  label-md:
    fontFamily: jetbrainsMono
    fontSize: 14px
    fontWeight: '500'
    lineHeight: '1.4'
    letterSpacing: 0.05em
  headline-lg-mobile:
    fontFamily: hankenGrotesk
    fontSize: 28px
    fontWeight: '600'
    lineHeight: '1.2'
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  unit: 4px
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 40px
  2xl: 64px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 48px
---

## Brand & Style

This design system embodies a **Sophisticated High-Tech** personality, drawing inspiration from high-end aerospace and fintech aesthetics. The brand's emotional core is centered on precision, foresight, and quiet confidence. It targets professional users who value efficiency and deep focus.

The visual style is **Premium Noir**, a refined fusion of minimalism and glassmorphism. It leverages deep obsidian surfaces, crisp high-contrast typography, and ethereal gradients to create a sense of infinite depth. The interface feels like a high-performance instrument—clean, intentional, and technologically advanced.

Key attributes:
- **Precision-driven:** Sharp edges and generous letter-spacing.
- **Atmospheric:** Deep dark modes with subtle glow effects.
- **Selective Vibrancy:** Color is used sparingly but impactfully to denote action and status.

## Colors

The palette is anchored in a "Noir" foundation, utilizing deep indigos and blacks to provide maximum contrast for the signature gradient.

- **Primary & Accent:** The core identity uses a vibrant gradient transitioning from `#8B5CF6` (Violet) to `#3B82F6` (Blue). This should be used for primary actions, progress indicators, and key brand moments.
- **Surface Strategy:** Backgrounds utilize a multi-layered dark approach. The base is a deep indigo-black (`#0F172A`), while elevated surfaces use subtle variations to create hierarchy without relying on heavy borders.
- **Functional Colors:** Success, warning, and error states are desaturated to maintain the premium aesthetic, ensuring they don't clash with the primary violet accent.

## Typography

Typography is the cornerstone of this design system's "High-Tech" feel. 

- **Headlines:** Use **Hanken Grotesk** for all headings. Its sharp terminals and modern geometric construction provide an architectural feel. For large display moments, use tight tracking; for smaller headers, allow the font to breathe.
- **Body:** **Manrope** is selected for its exceptional legibility and balanced proportions. It maintains a clean look in dense data environments.
- **Data & Mono:** **JetBrains Mono** is introduced for labels, metadata, and technical readouts to reinforce the systematic, developer-grade aesthetic.

## Layout & Spacing

The layout philosophy follows a **Fluid-Fixed Hybrid** model. Content is contained within a maximum width of 1440px on desktop but remains fluid within smaller breakpoints.

- **Rhythm:** A strict 4px / 8px grid governs all spatial relationships. 
- **Grid:** A 12-column system is used for desktop (24px gutters), collapsing to 6 columns for tablet and 2 columns for mobile.
- **Density:** The system favors high-density layouts for data-heavy views but switches to "Cinematic Density" (increased whitespace) for landing pages and dashboard overviews.

## Elevation & Depth

Depth is conveyed through **Tonal Layering** and **Backdrop Blurs** rather than traditional heavy shadows.

- **Surface Levels:** 
    - `L0`: Base background (`#0F172A`).
    - `L1`: Content cards (10% lighter than base, subtle 1px border at 10% opacity).
    - `L2`: Overlays and modals (using backdrop-filter: blur(12px) and a semi-transparent surface).
- **Glow Effects:** Critical interactive elements (like active states or primary buttons) may emit a soft, 15% opacity violet glow to simulate hardware illumination.

## Shapes

The shape language is **Technical & Precise**. 

- **Radius:** A standard `0.25rem` (4px) radius is used for most components (buttons, inputs) to maintain a sharp, professional look. 
- **Large Containers:** Cards and large sections use `0.5rem` (8px) to soften the overall structure slightly without appearing "bubbly."
- **Interactive States:** Use sharp transitions. Avoid heavy rounding (pill shapes) unless used for specific status chips.

## Components

### Buttons
- **Primary:** Gradient background (Violet to Blue), white text, sharp corners. On hover, the gradient brightness increases.
- **Ghost:** Transparent background with a 1px `#8B5CF6` border. Text is white.

### Input Fields
- **Default:** Dark fill with a subtle bottom border or 1px outline. When focused, the border transitions to the primary violet and a subtle glow is applied.
- **Typography:** Placeholder text uses JetBrains Mono for a technical feel.

### Cards
- **Structure:** No external shadows. Depth is achieved via a slightly lighter fill than the background and a hairline border (`rgba(255,255,255,0.1)`).

### Chips & Tags
- **Style:** Small, all-caps labels using JetBrains Mono. Backgrounds should be low-opacity versions of the status color (e.g., 10% Violet fill with 100% Violet text).

### Navigation
- **Sidebars:** Integrated into the layout with a vertical separator and glassmorphic blurs on mobile. Navigation items use high-contrast white for active states and desaturated indigo for inactive states.