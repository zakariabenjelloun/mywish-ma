# 🎨 MyWish.ma — Design System v2

> **Source of truth** for all visual design decisions.
>
> Reference this document for every new component, page, or feature.

**Version**: 2.0 (Dark Premium)
**Last update**: 2026-05-09

---

## 📑 Table of contents

1. [Philosophy](#philosophy)
2. [Color tokens](#color-tokens)
3. [Typography](#typography)
4. [Spacing & layout](#spacing--layout)
5. [Border radius](#border-radius)
6. [Shadows](#shadows)
7. [Iconography](#iconography)
8. [Components](#components)
9. [Animations](#animations)
10. [Mobile patterns](#mobile-patterns)
11. [Accessibility](#accessibility)
12. [Tailwind config](#tailwind-config)

---

## Philosophy

### "Calm festivity" — Premium dark mode

MyWish is festive but **never noisy**. Inspired by:
- **Linear** — for the type system precision
- **Vercel** — for the dark mode balance
- **Chipotle iOS** — for the warm photo-driven approach
- **Arc** — for the premium feel

### Core principles

1. **80/15/5 rule**: 80% neutrals, 15% peach, 5% gold
2. **Type does the heavy lifting**: bold weights + tight letter-spacing
3. **Lucide icons, never emojis** in functional UI
4. **Smooth animations**, never bouncy
5. **Mobile-first**: design for 375px viewport first
6. **Premium feel**: glow, shimmer, glassmorphism — used sparingly

---

## Color tokens

### Brand colors

| Token | Hex | Usage |
|-------|-----|-------|
| `--primary` | `#EA580C` | Main CTAs, accent text |
| `--primary-soft` | `#FB923C` | Hover states, gradients |
| `--primary-deep` | `#C2410C` | Pressed states, deep gradients |
| `--primary-bg` | `rgba(234, 88, 12, 0.08)` | Subtle backgrounds, badges |
| `--primary-border` | `rgba(234, 88, 12, 0.22)` | Premium borders |

### Gold accents (premium)

| Token | Hex | Usage |
|-------|-----|-------|
| `--gold` | `#FCD34D` | Premium badges, highlights |
| `--gold-soft` | `#FDE68A` | Soft gold accents |
| `--gold-bg` | `rgba(252, 211, 77, 0.1)` | Premium card backgrounds |
| `--gold-border` | `rgba(252, 211, 77, 0.22)` | Premium borders |

### Dark surfaces

| Token | Hex | Usage |
|-------|-----|-------|
| `--bg-deep` | `#0A0A0A` | Main app background |
| `--bg-raised` | `#18181B` | Cards, surfaces |
| `--bg-high` | `#27272A` | Hover states, modals |
| `--bg-overlay` | `rgba(255, 255, 255, 0.04)` | Subtle overlays |

### Borders

| Token | Hex | Usage |
|-------|-----|-------|
| `--border` | `#2D2D30` | Subtle separators |
| `--border-strong` | `#3F3F46` | Prominent borders, inputs |

### Text

| Token | Hex | Usage |
|-------|-----|-------|
| `--text-primary` | `#FAFAF9` | Headings, main content |
| `--text-secondary` | `#A1A1AA` | Descriptions, sub-text |
| `--text-muted` | `#71717A` | Metadata, hints |
| `--text-ghost` | `#52525B` | Disabled, deepest muted |

### Semantic

| Token | Hex | Usage |
|-------|-----|-------|
| `--success` | `#34D399` | Validations, confirmed |
| `--success-bg` | `rgba(52, 211, 153, 0.1)` | Success backgrounds |
| `--warning` | `#FBBF24` | Pending, attention |
| `--warning-bg` | `rgba(251, 191, 36, 0.1)` | Warning backgrounds |
| `--danger` | `#F87171` | Errors, destructive |
| `--danger-bg` | `rgba(248, 113, 113, 0.1)` | Error backgrounds |

### Application of 80/15/5

```
┌────────────────────────────────────────┐
│  80% — Dark surfaces                   │
│  bg-deep, bg-raised, bg-high           │
│  Most of the screen, calm              │
├────────────────────────────────────────┤
│  15% — Peach (primary)                 │
│  CTAs, accents, brand elements         │
│  Visible but not overwhelming          │
├────────────────────────────────────────┤
│  5% — Gold (accent)                    │
│  Premium highlights, badges            │
│  Used sparingly for impact             │
└────────────────────────────────────────┘
```

---

## Typography

### Font families

```css
--font-display: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
--font-body: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
```

Load via Google Fonts:
```html
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
```

### Type scale

| Level | Size | Weight | Letter-spacing | Line-height | Usage |
|-------|------|--------|----------------|-------------|-------|
| Display | 64-76px | 800 | -0.04em | 0.95 | Hero titles |
| H1 | 38-48px | 800 | -0.025em | 1.1 | Page titles |
| H2 | 26-32px | 700-800 | -0.02em | 1.2 | Section titles |
| H3 | 18-20px | 700 | -0.01em | 1.3 | Subsections, card titles |
| Body | 15px | 400 | normal | 1.55 | Main content |
| Small | 13px | 400 | normal | 1.5 | Secondary content |
| Tiny | 11px | 600 | 0.08em | 1.4 | Labels, eyebrow text (uppercase) |

### Font feature settings

```css
font-feature-settings: 'cv02', 'cv03', 'cv04', 'cv11';
```

(For improved Inter rendering — alternate digit shapes, etc.)

### Examples

```html
<!-- Display (hero) -->
<h1 class="font-display text-6xl font-extrabold tracking-tighter leading-none">
  Khotba, anniversaire, mariage…
</h1>

<!-- H2 (section) -->
<h2 class="font-display text-3xl font-bold tracking-tight">
  Cagnotte PS5
</h2>

<!-- Body -->
<p class="text-base text-zinc-300 leading-relaxed">
  Description text here...
</p>

<!-- Tiny / eyebrow -->
<span class="text-xs font-semibold uppercase tracking-widest text-zinc-500">
  Le héros du jour
</span>
```

---

## Spacing & layout

### 4px grid

| Token | Pixels |
|-------|--------|
| `--s-1` | 4px |
| `--s-2` | 8px |
| `--s-3` | 12px |
| `--s-4` | 16px |
| `--s-5` | 20px |
| `--s-6` | 24px |
| `--s-8` | 32px |
| `--s-10` | 40px |
| `--s-12` | 48px |
| `--s-16` | 64px |
| `--s-20` | 80px |
| `--s-24` | 96px |

### Container widths

```css
.container-mobile { max-width: 480px; }   /* event pages, mobile-first */
.container-md { max-width: 720px; }       /* forms, FAQs */
.container-lg { max-width: 1100px; }      /* design system docs */
.container-xl { max-width: 1280px; }      /* landing page */
```

---

## Border radius

| Token | Pixels | Usage |
|-------|--------|-------|
| `--r-sm` | 8px | Small buttons, presets |
| `--r-md` | 12px | Standard buttons, inputs |
| `--r-lg` | 16px | Cards, badges (large) |
| `--r-xl` | 20px | Feature cards |
| `--r-2xl` | 24px | Section cards, modals |
| `--r-3xl` | 32px | Hero photos, large containers |
| `--r-full` | 9999px | Pills, circular avatars |

---

## Shadows

```css
/* Soft elevation */
--shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);
--shadow-md: 0 4px 16px rgba(0, 0, 0, 0.4);
--shadow-lg: 0 16px 48px rgba(0, 0, 0, 0.5);
--shadow-card: 0 8px 24px rgba(0, 0, 0, 0.4);

/* Brand glows */
--shadow-glow: 0 0 32px rgba(234, 88, 12, 0.3);
--shadow-glow-lg: 0 0 64px rgba(234, 88, 12, 0.4);
--shadow-gold-glow: 0 0 32px rgba(252, 211, 77, 0.3);

/* Deep contrast */
--shadow-deep: 0 24px 64px rgba(0, 0, 0, 0.6);
```

### When to use glow

- ✅ Primary CTA buttons (always)
- ✅ Featured/active elements
- ✅ Premium tier indicators
- ❌ Don't overuse — saves impact for important elements

---

## Iconography

### System: Lucide

**Library**: [Lucide React](https://lucide.dev) (open source, line-style icons)

```bash
npm install lucide-react
```

### Stroke weights

```typescript
// In components
import { Calendar, MapPin, Heart, Gift } from 'lucide-react';

<Calendar size={18} strokeWidth={1.8} />  // Standard
<Calendar size={14} strokeWidth={2} />    // Small
<Calendar size={28} strokeWidth={1.5} />  // Hero/decorative
```

### Common icons used in MyWish

| Icon | Usage |
|------|-------|
| `Gift` | Cagnotte, contribution, brand |
| `Calendar` | Date display, scheduling |
| `MapPin` | Location |
| `Clock` | Time, countdown |
| `Users` | RSVP count, participants |
| `Heart` | Wishlist, love |
| `Star` | Favorite, premium, rating |
| `Cake` | Birthday events |
| `Check` | Validated, success |
| `XCircle` | Refused, cancel |
| `Plus` | Add, create |
| `Edit` | Modify |
| `Trash` | Delete |
| `Share2` | Share |
| `Link` | Copy link |
| `UserPlus` | Invite |
| `Sparkles` | Premium, special |
| `Info` | Help, information |

### ❌ NEVER

- Don't use emojis in functional UI (buttons, navigation, form labels)
- Don't mix Lucide with other icon libraries (Heroicons, FontAwesome)
- Don't use inconsistent stroke widths within the same view

### ✅ OK

- Decorative emojis in titles or marketing badges
- Emoji avatars (Roblox-style: 🦸 🥷 🧙) — they're identity, not UI
- Country flag emoji (🇲🇦) for clear locale indication

---

## Components

### Buttons

```css
/* Base */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 20px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 14px;
  letter-spacing: -0.005em;
  transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Primary */
.btn-primary {
  background: var(--primary);
  color: white;
  box-shadow: var(--shadow-glow);
}
.btn-primary:hover {
  background: var(--primary-soft);
  box-shadow: var(--shadow-glow-lg);
  transform: translateY(-1px);
}

/* Secondary */
.btn-secondary {
  background: var(--bg-high);
  color: var(--text-primary);
  border: 1px solid var(--border-strong);
}
.btn-secondary:hover {
  border-color: var(--primary);
  color: var(--primary-soft);
}

/* Ghost */
.btn-ghost {
  background: transparent;
  color: var(--text-secondary);
}
.btn-ghost:hover {
  background: var(--bg-overlay);
  color: var(--text-primary);
}

/* Danger */
.btn-danger {
  background: var(--bg-high);
  color: var(--danger);
  border: 1px solid var(--border-strong);
}
.btn-danger:hover {
  border-color: var(--danger);
  background: var(--danger-bg);
}

/* Sizes */
.btn-sm { padding: 8px 12px; font-size: 13px; }
.btn-lg { padding: 16px 24px; font-size: 15px; }
.btn-xl { padding: 20px 32px; font-size: 16px; font-weight: 700; }
```

### Inputs

```css
.input {
  width: 100%;
  padding: 12px 16px;
  background: var(--bg-deep);
  border: 1.5px solid var(--border-strong);
  border-radius: 12px;
  font-size: 15px;
  color: var(--text-primary);
  transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
  outline: none;
}
.input:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.15);
}
.input::placeholder {
  color: var(--text-muted);
}
```

### Cards

```css
.card {
  background: var(--bg-raised);
  border: 1px solid var(--border);
  border-radius: 24px;
  padding: 24px;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}
.card:hover {
  border-color: var(--border-strong);
  background: var(--bg-high);
}
```

### Badges

```css
.badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  border-radius: 9999px;
  font-size: 12px;
  font-weight: 600;
  border: 1px solid transparent;
}

.badge-primary { background: var(--primary-bg); color: var(--primary-soft); border-color: var(--primary-border); }
.badge-gold { background: var(--gold-bg); color: var(--gold); border-color: var(--gold-border); }
.badge-success { background: var(--success-bg); color: var(--success); }
.badge-warning { background: var(--warning-bg); color: var(--warning); }
.badge-danger { background: var(--danger-bg); color: var(--danger); }
.badge-neutral { background: var(--bg-high); color: var(--text-secondary); }
```

### Progress bars

```css
.progress-track {
  height: 10px;
  background: var(--bg-high);
  border-radius: 9999px;
  overflow: hidden;
}
.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--primary-deep), var(--primary), var(--gold));
  border-radius: 9999px;
  position: relative;
  overflow: hidden;
}
/* Shimmer effect */
.progress-fill::after {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  animation: shimmer 2.5s infinite;
}
@keyframes shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}
```

---

## Animations

### Easing

**Always use**: `cubic-bezier(0.4, 0, 0.2, 1)` (smooth easing)

```css
--t-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
--t-base: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
--t-smooth: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
```

### Approved animations

```css
/* Fade in/out — for toasts, notifications */
@keyframes fadeInOut {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.4; }
}

/* Scale pulse — for CTAs, attention */
@keyframes scalePulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.08); }
}

/* Slide up — for entry */
@keyframes slideUp {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
}

/* Glow pulse — for premium */
@keyframes glow {
  0%, 100% { box-shadow: 0 0 16px rgba(234, 88, 12, 0.3); }
  50% { box-shadow: 0 0 32px rgba(234, 88, 12, 0.6); }
}

/* Shimmer — for progress bars */
@keyframes shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

/* Pulse dot — for status indicators */
@keyframes pulse-dot {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
```

### ❌ NEVER use

- `bounce` (childish)
- `wiggle` (looks broken)
- Excessive parallax
- Auto-playing carousels
- Anything > 500ms duration unless explicitly designed

---

## Mobile patterns

### Tab bar (bottom nav)

5-item navigation, native iOS/Android style:

```
[Home] [Events] [+ Create (elevated)] [RSVPs] [Profile]
```

The "+ Create" item is **elevated** — circular, primary color, pulled up out of the bar.

### Touch targets

- Minimum **44x44px** for all interactive elements
- Spacing **8px minimum** between adjacent buttons

### Safe areas

```css
padding-top: env(safe-area-inset-top);
padding-bottom: env(safe-area-inset-bottom);
```

(For iPhone notch/home indicator support)

### Viewport

```html
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="theme-color" content="#0A0A0A">
```

---

## Accessibility

### Contrast ratios

All text must pass **WCAG AA**:
- Normal text: ≥ 4.5:1 contrast
- Large text (≥18px or ≥14px bold): ≥ 3:1 contrast

Check with [WebAIM Contrast Checker](https://webaim.org/resources/contrastchecker/).

### Focus states

```css
*:focus-visible {
  outline: 2px solid var(--primary);
  outline-offset: 2px;
}
```

Never use `outline: none` without providing an alternative focus indicator.

### Screen reader support

- All buttons must have accessible labels (visible or `aria-label`)
- Lucide icons used as buttons need `aria-label`
- Decorative icons should have `aria-hidden="true"`

### Reduced motion

```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    transition-duration: 0.01ms !important;
  }
}
```

---

## Tailwind config

Sample `tailwind.config.ts`:

```typescript
import type { Config } from 'tailwindcss';

const config: Config = {
  content: ['./src/**/*.{ts,tsx}'],
  darkMode: 'class', // we use dark by default
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#EA580C',
          soft: '#FB923C',
          deep: '#C2410C',
        },
        gold: {
          DEFAULT: '#FCD34D',
          soft: '#FDE68A',
        },
        bg: {
          deep: '#0A0A0A',
          raised: '#18181B',
          high: '#27272A',
        },
      },
      fontFamily: {
        display: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
        body: ['Inter', 'system-ui', 'sans-serif'],
      },
      letterSpacing: {
        'tighter': '-0.025em',
        'tightest': '-0.04em',
      },
      boxShadow: {
        'glow': '0 0 32px rgba(234, 88, 12, 0.3)',
        'glow-lg': '0 0 64px rgba(234, 88, 12, 0.4)',
        'gold-glow': '0 0 32px rgba(252, 211, 77, 0.3)',
      },
      animation: {
        'shimmer': 'shimmer 2.5s infinite',
        'pulse-dot': 'pulse-dot 2s ease-in-out infinite',
        'glow': 'glow 2.5s ease-in-out infinite',
      },
      keyframes: {
        shimmer: {
          '0%': { transform: 'translateX(-100%)' },
          '100%': { transform: 'translateX(100%)' },
        },
        'pulse-dot': {
          '0%, 100%': { opacity: '1' },
          '50%': { opacity: '0.5' },
        },
        glow: {
          '0%, 100%': { boxShadow: '0 0 16px rgba(234, 88, 12, 0.3)' },
          '50%': { boxShadow: '0 0 32px rgba(234, 88, 12, 0.6)' },
        },
      },
      transitionTimingFunction: {
        'smooth': 'cubic-bezier(0.4, 0, 0.2, 1)',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('tailwindcss-animate'),
  ],
};

export default config;
```

---

## Reference: live mockups

See `/docs/mockups/`:
- `01-direction-artistique-v2-dark.html` — Full design system showcase
- `02-landing-dark.html` — Landing page in production look
- `03-page-ibrahim-dark.html` — Real event page mockup

Open these in a browser to see the design system in action with live animations.

---

*Design System v2 — Last updated 2026-05-09*
