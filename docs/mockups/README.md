# 🎨 Mockups

Visual mockups of MyWish.ma — open these HTML files in your browser to see the design system in action.

These are the **source of truth** for visual implementation. When asking Claude Code to implement a feature, reference the relevant mockup.

---

## 📦 Available mockups

### 1. Direction Artistique v2 Dark — `01-direction-artistique-v2-dark.html`

**What it is**: Complete visual style guide showcasing all design tokens and components.

**Use it for**:
- Reference for color palette
- Reference for typography
- Reference for components (buttons, inputs, badges, progress bars, etc.)
- Reference for icons (Lucide showcase)
- Reference for animations (fade, scale, slide, glow)

**Key sections**:
- Section 1: Color palette (Brand / Surfaces / Text / Semantic)
- Section 2: Typography scale
- Section 3: Lucide icons used in the project
- Section 4: Components (buttons, inputs, badges, progress)
- Section 5: Mobile navigation patterns
- Section 6: Real examples (event cards)
- Section 7: Animation patterns
- Section 8: Before/after (v1 light vs v2 dark)

---

### 2. Landing page — `02-landing-dark.html`

**What it is**: Marketing landing page at `mywish.ma`.

**Use it for**:
- Hero section design
- Social proof layout
- "How it works" 3-step section
- Examples grid
- Features cards
- Pricing cards (Free vs Premium)
- FAQ accordion
- Final CTA
- Footer

**Implementation priority**: Sprint 4 (Public event page) prep.

---

### 3. Page d'événement — `03-page-ibrahim-dark.html`

**What it is**: Real event page mockup (Ibrahim's birthday).

**Use it for**:
- Event hero with photo + countdown
- Invitation card with RSVP buttons
- Cagnotte section with progress bar + Ibrahim→PS5 animation
- Avatar selection grid (Roblox-style)
- Amount input with presets
- Contributor list with status badges
- Tabs (presents/confirmed)
- Share buttons

**Implementation priority**: Sprint 4-5 (Public event page + RSVP/Kitty).

---

## 🎯 How to use these mockups

### When asking Claude Code to implement

Good prompts:

```
> "Implement the landing page based on docs/mockups/02-landing-dark.html.
   Convert each section to a Next.js + Tailwind component.
   Use design tokens from docs/DESIGN-SYSTEM.md (no hardcoded colors).
   Lucide icons (already in the mockup as SVG, replace with React components)."
```

```
> "Build the EventHero component shown in docs/mockups/03-page-ibrahim-dark.html.
   Props: photo URL, hero name, age (or year for non-birthday), date, time, location, target.
   Match the visual exactly."
```

### When validating a design choice

> If a feature isn't in the mockups → discuss in chat first, **don't guess**.
> If the mockup conflicts with `DESIGN-SYSTEM.md` → mockup wins for layout, design system wins for tokens.

---

## 🔄 When to update mockups

Update these HTML files when:
- ✅ A major design decision changes (e.g., new component, new pattern)
- ✅ You add a new screen type (e.g., dashboard, admin panel)
- ✅ User testing reveals a needed change

**Don't** update mockups for:
- ❌ Small tweaks (use `docs/DESIGN-SYSTEM.md` updates instead)
- ❌ Per-implementation adjustments (handle in code)

---

## 🆕 Adding new mockups

When adding a new mockup:

1. Name with sequential prefix: `04-`, `05-`, etc.
2. Use the same dark theme + design tokens
3. Add an entry to this README
4. Commit with: `git commit -am "docs: add mockup for [screen name]"`
5. Reference in `MASTER-PLAN.md` if it's a major screen

Suggested next mockups (in priority order):
- `04-onboarding-create-event-dark.html` — 6-step onboarding flow
- `05-dashboard-organizer-dark.html` — Organizer's main dashboard
- `06-modal-contribution-with-proof.html` — Contribution flow with proof upload
- `07-admin-dashboard-dark.html` — Admin panel
- `08-partner-dashboard-dark.html` — Partner panel

---

## 💡 Pro tip

When in doubt during implementation:

```bash
# Open the mockup in your browser
open docs/mockups/02-landing-dark.html

# Open dev tools → inspect any element
# See the exact spacing, colors, animations used
```

These mockups are **production-quality HTML/CSS** — you can literally copy values from them.

---

*Last updated: 2026-05-09*
