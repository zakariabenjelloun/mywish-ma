# 📝 MyWish.ma — Decisions Log

> Chronological log of all major decisions taken during the project.
>
> [ADR (Architecture Decision Record)](https://adr.github.io/) style log.
>
> **Format**: `## YYYY-MM-DD — Title`

---

## 2026-05-09 — Switch from Next.js stack to PHP/MySQL/cPanel stack

**Context**: Initial bootstrap was Next.js 14 + Supabase + Vercel. After consideration, founder decided to switch to PHP/MySQL/cPanel.

**Decision**: Move to **PHP 8.2+ vanilla (no framework) + MySQL 8 + TailwindCSS precompiled + Alpine.js + cPanel hosting (OVH Maroc)**.

**Reasoning**:
- ✅ Founder is **not a developer** — PHP is more accessible to learn and to debug
- ✅ Cheaper and simpler hosting at Maroc (OVH Maroc Pro ~80 MAD/month with cPanel)
- ✅ Easier to find PHP developers in Maroc if hiring later
- ✅ No build step on server (compiled CSS only)
- ✅ Direct integration with CMI (Maroccan payment) — more PHP tutorials
- ✅ Workflow via cPanel Git Version Control = visual interface, no DevOps complexity
- ⚠️ Trade-off : code won't be reusable for React Native mobile app (but mobile is V3+, not a priority)

**Why vanilla PHP, not Laravel?**
- Founder is not dev — Laravel adds learning curve (Composer, artisan, Eloquent, Blade...)
- Vanilla PHP runs on ANY cPanel without configuration
- Code is simpler to debug for non-devs
- For a project this size, framework overhead isn't justified
- Custom mini-Router (~50 lines) covers our needs

**Impact**:
- Full bootstrap rebuilt for PHP
- Database: PostgreSQL → MySQL (similar enough, schema adapted)
- Auth: NextAuth → custom Google OAuth with cURL
- Frontend: React → PHP templates + Alpine.js
- Hosting: Vercel → OVH Maroc cPanel
- CI/CD: GitHub Actions deploy → cPanel Git Version Control + .cpanel.yml
- Documentation rebuilt: CLAUDE.md, MASTER-PLAN, ROADMAP, DEPLOYMENT, DATABASE
- 100% of strategic decisions PRESERVED (pricing, branding, design, etc.)
- Mockups (HTML) PRESERVED unchanged

---

## 2026-05-09 — Two environments: dev + prod with separate databases

**Context**: Need clear separation between testing and production.

**Decision**: 2 environments managed via 2 GitHub branches + 2 cPanel Git repos:
- `dev` branch → `dev.mywish.ma` subdomain → `database_dev`
- `main` branch → `mywish.ma` (root) → `database_prod`

**Reasoning**:
- Standard practice in modern web dev
- Allows testing changes (especially DB migrations) before exposing to real users
- cPanel Git Version Control supports this natively
- Cost is minimal (one cPanel can host both)

**Impact**:
- Setup includes creating both environments from day 1
- All workflow docs reference dev → prod flow
- `.cpanel.yml` is identical for both (DEPLOYPATH varies)

---

## 2026-05-09 — `.env` file for secrets (excluded from Git)

**Context**: How to manage DB credentials, API keys, etc.

**Decision**: Use a `.env` file at the project root, gitignored. Loaded by custom `Env` class (no Composer needed).

**Reasoning**:
- Industry standard pattern
- Different environments need different credentials
- NEVER expose secrets in Git history
- Custom loader avoids Composer dependency

**Impact**:
- `.gitignore` excludes `.env`
- `.env.example` committed as template
- `src/Config/Env.php` handles loading
- On cPanel, `.env` must be created manually via SSH/file manager
- `.htaccess` blocks web access to `.env`

---

## 2026-05-09 — Numbered SQL migrations in `database/migrations/`

**Context**: How to track and version DB schema changes?

**Decision**: Plain `.sql` files numbered `NNN_description.sql`. A `migrations` table tracks which have been applied.

**Reasoning**:
- Simple, version-controlled, transparent
- No framework dependency (no `php artisan migrate`)
- Can be applied via phpMyAdmin (cPanel UI) or SSH or custom script
- Clear history of every DB change

**Impact**:
- `database/migrations/` folder created
- `database/migrations/000_create_migrations_table.sql` is mandatory first
- Every schema change = new numbered file
- `database/migrations/README.md` documents the process
- See `docs/DATABASE.md` for full conventions

---

## 2026-05-09 — `.cpanel.yml` for automated deployment

**Context**: How to deploy from cPanel Git Version Control to the right web folder?

**Decision**: Use `.cpanel.yml` script that runs on "Deploy HEAD Commit". Copies `public/` contents + `src/` + `database/` to DEPLOYPATH.

**Reasoning**:
- Native cPanel feature (no extra tools)
- Works without SSH for deployment
- One file works for both dev and prod (DEPLOYPATH varies)
- Simple bash commands inside YAML

**Impact**:
- `.cpanel.yml` committed at repo root
- Documented in `docs/DEPLOYMENT.md`
- Required setting "Deployment Path" in cPanel UI for each repo

---

## 2026-05-09 — Front controller pattern (single index.php entry)

**Context**: How to handle routing in vanilla PHP?

**Decision**: Single `public/index.php` file as front controller. All requests go through it via `.htaccess` rewriting. Custom mini-Router class handles routes.

**Reasoning**:
- Modern PHP best practice (used by all frameworks)
- Centralized error handling, session start, autoloader
- Clean URLs (`/event/abc-xyz` instead of `/event.php?slug=abc-xyz`)
- Easy to add middleware later

**Impact**:
- `public/index.php` ~100 lines
- `public/.htaccess` rewrites everything to `index.php`
- `src/Core/Router.php` ~100 lines
- All other PHP files outside `public/` (security)

---

## 2026-05-09 — Visual mockups produced (3 deliverables, PRESERVED from previous bootstrap)

**Context**: Need to validate visual direction before implementation.

**Decision**: 3 HTML mockups, kept verbatim across stack changes:
1. `01-direction-artistique-v2-dark.html` — Full design system showcase
2. `02-landing-dark.html` — Landing page mywish.ma
3. `03-page-ibrahim-dark.html` — Live event page (Ibrahim's birthday)

**Reasoning**: HTML mockups are stack-agnostic — same target whether implemented in React or PHP.

**Impact**:
- Mockups stored in `/docs/mockups/`
- Used as reference by Claude Code during implementation
- Templates in `src/Views/` will translate these mockups to PHP

---

## 2026-05-09 — Switch from light v1 to dark v2 design system

**Context**: First attempt (light "Calm festivity") felt outdated and pastel.

**Decision**: Complete redesign to dark mode premium with:
- Saturated peach `#EA580C`
- Gold accent `#FCD34D`
- Dark surfaces `#0A0A0A` to `#27272A`
- Lucide icons (no emojis in functional UI)
- Plus Jakarta Sans + Inter fonts
- Smooth animations (cubic-bezier)

**Reasoning**: Inspired by Linear, Vercel, Arc, Chipotle iOS. Modern apps in 2026 default to dark for premium feel.

**Impact**: Documented in `DESIGN-SYSTEM.md`. Mockups updated.

---

## 2026-05-09 — Brand: MyWish.ma

**Context**: Need memorable brand name.

**Decision**: **MyWish.ma**

**Reasoning**:
- Short, memorable, works in FR/EN/AR contexts
- ".ma" anchors to Morocco
- Emotional ("your wish") vs technical
- Domain available

---

## 2026-05-09 — Branding visible on ALL pages (free + paid)

**Context**: Initial idea was "Premium = no branding". User pushed back.

**Decision**: MyWish branding visible on **both** free AND paid pages, with different treatments:
- **Free**: Visible badge "Created on MyWish.ma → Create yours"
- **Paid**: Discrete footer "Made on MyWish.ma ❤️"

**Reasoning**: Viral acquisition is the #1 lever. Like "Powered by Shopify".

**Impact**: Pricing simplified, viral loop strengthened.

---

## 2026-05-09 — Pricing: Free + Premium 99 MAD only

**Context**: Multiple pricing tiers were considered.

**Decision**: Two tiers only:
- 🆓 **Free**: 15 guests max, branding visible
- 💎 **Premium 99 MAD/event**: unlimited guests, custom slug

Wedding events: same 99 MAD price.

**Reasoning**: Simplicity > segmentation. Volume + marketplace > per-unit margin.

---

## 2026-05-09 — Marketplace = subscription directory (not commission)

**Context**: Initial plan was 10-15% commission on partner sales.

**Decision**: Subscription-based partner directory:
- 🥉 Bronze 99 MAD/month
- 🥈 Silver 299 MAD/month
- 🥇 Gold 599 MAD/month

**Reasoning**: Predictable MRR, no tracking complexity, easier to scale (Yelp model).

---

## 2026-05-09 — No SMS — WhatsApp only for verifications

**Context**: Initial assumption was SMS for phone verification.

**Decision**: **WhatsApp Cloud API only**, no SMS anywhere.

**Reasoning**:
- Moroccan users open WhatsApp 10x more than SMS
- WhatsApp Cloud API: 1000 free conversations/month
- Better UX (richer messages possible)
- Diaspora often doesn't have local SIM cards

---

## 2026-05-09 — Privacy model: 2-tier (public read / auth action)

**Context**: How much info should be visible without login?

**Decision**: 2-tier privacy model:
- **Public** (anyone with link): photo, title, date, general location, % of kitty, count of confirmed RSVPs, partner suggestions
- **Auth required** (Google + event code): precise address, exact kitty amount, list of guests, list of contributors, individual amounts, payment methods

**Reasoning**:
- Public reads preserve viral loop (link shareable)
- Auth-gated actions prevent fraud
- Curiosity = engagement
- Privacy by default for sensitive data

---

## 2026-05-09 — Google Sign-In only (no email/password, no Apple)

**Context**: Multiple auth methods possible.

**Decision**: **Google OAuth only** at MVP, both for organizers and guests.

**Reasoning**:
- 95%+ of Morocco internet users have Gmail
- Reduces friction (no password creation)
- Reduces auth complexity (one provider)
- Apple Sign-In can be added in V2 if iOS users complain

---

## 2026-05-09 — All data in DB, zero localStorage for user data

**Context**: Original Ibrahim page used localStorage for device-based identity.

**Decision**: With auth introduction, **100% of user data lives in the database**. PHP sessions only for auth state.

**Reasoning**: Multi-device support, recovery, anti-fraud, notifications.

---

## 2026-05-09 — No co-organizers at MVP (single owner per event)

**Context**: Should multiple people manage one event?

**Decision**: **No co-organizers at MVP**. Single owner.

**Reasoning**: Simplicity. Permission model adds complexity. Most family events have one main organizer.

---

## 2026-05-09 — No sub-events at MVP (1 event = 1 page)

**Context**: Should weddings (Henné + Mariage + Soirée) be one event with sub-events?

**Decision**: **No sub-events at MVP**. Each ceremony = separate event.

**Reasoning**: Simpler data model, avoids permission complexity per sub-event.

---

## 2026-05-09 — No photo gallery at MVP

**Context**: Should guests upload photos?

**Decision**: **No photo gallery at MVP**.

**Reasoning**: Storage costs, moderation complexity, not core value prop.

---

## 2026-05-09 — Kitty payments: validation flow with proof

**Context**: How to handle that MyWish doesn't touch the money?

**Decision**: **Promise → Proof → Validation** flow:
1. Guest clicks "I contribute" + chooses payment method
2. Guest pays externally (cash, RIB, CashPlus, Wise)
3. Guest uploads proof (screenshot/photo)
4. Status: ⏳ "Pending validation"
5. Organizer reviews + validates (or rejects)
6. Status: ✅ "Validated" → counted in totals

Pending contributions are visible (greyed/badged "En attente"), not hidden.

**Reasoning**:
- MyWish never holds funds → no licenses, no KYC
- Proof prevents fraudulent claims
- Pending visibility = good UX
- Organizer has full control

**Impact**:
- `contributions.status` enum: pending/validated/rejected/cash_promise
- Cloudinary stores proof images
- Email notif to organizer when proof uploaded

---

## 2026-05-09 — French only at MVP

**Context**: Should we support FR + AR + EN at MVP?

**Decision**: **French only at MVP**. AR + EN in V2.

**Reasoning**: French covers 90%+ of target users. AR (RTL) requires UX rework.

---

## 2026-05-09 — Wishlist: manual entry with reservation system

**Context**: Should wishlist items come from a partner catalog?

**Decision**: **Manual entry by organizer**, with reservation system. Partner catalog integration in V2.

**Reasoning**: Simpler to engineer. Reservation is the key feature.

---

## 2026-05-09 — Email-only notifications at MVP

**Context**: Email vs WhatsApp vs Push?

**Decision**: **Email only at MVP** (via SMTP, Resend or Brevo). WhatsApp + Push in V2.

**Reasoning**: Email is reliable, no API limits at our scale, faster to ship.

---

## 2026-05-09 — 5 event types + 6 templates at MVP

**Context**: How many event types and templates to ship?

**Decision**:
- **5 event types**: Anniversaire, Mariage, Baby shower, Naissance, Autre/Spontané
- **6 templates** total: 2 anniversaire (kids + adulte) + 1 each for the other types

**Reasoning**: 80/20 — these 5 types cover 95% of family events.

---

## Template for new decisions

```markdown
## YYYY-MM-DD — Decision title

**Context**: What's the situation that led to this decision.

**Decision**: What we decided.

**Reasoning**: Why we chose this option.

**Impact**:
- What changes in code/design/product
- Any blockers or follow-ups
```

---

*Last updated: 2026-05-09*
