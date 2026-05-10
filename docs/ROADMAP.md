# 🗺️ MyWish.ma — Roadmap

> Sprint plan from MVP to V3 (PHP/MySQL/cPanel stack).
>
> **Current phase**: Sprint 0 (Setup)

**Last update**: 2026-05-09

---

## 🎯 Phase overview

```
┌─────────────────────────────────────────────────────────┐
│  PHASE 1 — MVP (3-4 months)                            │
│  Auth + 5 event types + 6 templates + 4 kitty types     │
│  + Premium 99 MAD payment flow                          │
│  Target: 50+ events at M3                               │
├─────────────────────────────────────────────────────────┤
│  PHASE 2 — V1 (Months 4-6)                              │
│  Marketplace partners + 10 more templates               │
│  + QR codes + advanced stats + CSV export               │
│  Target: 200+ events at M6                              │
├─────────────────────────────────────────────────────────┤
│  PHASE 3 — V2 (Months 7-12)                             │
│  PWA + AR/EN i18n + WhatsApp notifs                     │
│  + Calendar sync + B2B (white-label)                    │
│  Target: 800+ events at M12                             │
├─────────────────────────────────────────────────────────┤
│  PHASE 4 — V3 (Months 13-18)                            │
│  REST API + native iOS + Android apps (separate)        │
│  + Push notifications + offline mode                    │
│  + Marketplace e-commerce integration                   │
└─────────────────────────────────────────────────────────┘
```

---

## 📋 Phase 1 — MVP (12-15 weeks)

### Sprint 0 — Project Setup (1-2 weeks) ⬅️ CURRENT

**Goal**: Repo on GitHub + cPanel deployments working + dev environment ready.

- [ ] Create private GitHub repo `mywish-ma`
- [ ] Push initial bootstrap (this!)
- [ ] Create `dev` branch
- [ ] Buy domain `mywish.ma` (~150 MAD/year)
- [ ] Setup Cloudflare DNS
- [ ] Create OVH Maroc Pro hosting account (~80 MAD/month)
- [ ] Create `dev.mywish.ma` subdomain in cPanel
- [ ] Create 2 MySQL databases (dev + prod)
- [ ] Setup SSH access to cPanel
- [ ] Create `.env` files on server (dev + prod)
- [ ] Setup cPanel Git Version Control (2 repos: dev + prod)
- [ ] Configure DEPLOYPATH for each cPanel repo
- [ ] First successful deploy on `dev.mywish.ma`
- [ ] First successful deploy on `mywish.ma`
- [ ] Apply migrations on both databases
- [ ] SSL certificates (Let's Encrypt) on both
- [ ] Test homepage loads on both
- [ ] Test PHP error logging works
- [ ] Document any cPanel quirks in DEPLOYMENT.md

---

### Sprint 1 — Tailwind compilation + base layout (1 week)

**Goal**: Design system compiled and reusable.

- [ ] Setup Tailwind CLI locally (Node.js install on dev machine)
- [ ] Create `tailwind.config.js` with design tokens from `DESIGN-SYSTEM.md`
- [ ] Compile `public/assets/css/app.css` with Tailwind
- [ ] Commit compiled CSS to repo (no Node needed on server)
- [ ] Refactor `src/Views/layouts/default.php` to use Tailwind classes
- [ ] Build Lucide icons via inline SVG helper (`src/Helpers/icons.php`)
- [ ] Setup Alpine.js via CDN in layout
- [ ] Build `partials/header.php` proper (with mobile nav)
- [ ] Build `partials/footer.php` proper
- [ ] Test on mobile (375px viewport)

---

### Sprint 2 — Authentication (1-2 weeks)

**Goal**: Users can sign in with Google + verify phone via WhatsApp.

- [ ] Setup Google OAuth credentials in Google Cloud Console
- [ ] Add `/auth/google` and `/auth/google/callback` routes
- [ ] Implement `AuthController::redirectToGoogle()` (build OAuth URL)
- [ ] Implement `AuthController::handleGoogleCallback()` (exchange code → token → user info)
- [ ] Create `User` model (PDO-based) with CRUD
- [ ] Insert/update user in DB after Google login
- [ ] Setup PHP session for logged-in user
- [ ] Implement `/auth/logout`
- [ ] Setup WhatsApp Cloud API account
- [ ] Implement `WhatsApp::sendVerificationCode($phone, $code)` helper
- [ ] Build phone verification flow (UI + backend)
- [ ] Update `users.phone_verified = true` after success
- [ ] Build basic profile page `/profile`

---

### Sprint 3 — Event creation flow (2 weeks)

**Goal**: Authenticated user can create an event in 5 minutes through 6 steps.

- [ ] Step 0: Choice of event type (5 cards) — public route
- [ ] Step 1: Login redirect (if not authenticated)
- [ ] Step 2: Basic info form (per event type)
- [ ] Step 3: Template selection (placeholder for now)
- [ ] Step 4: Cagnotte config (4 types)
- [ ] Step 5: Personalization (photo, message, code)
- [ ] Step 6: Publish + share screen with link
- [ ] Auto-save on each step (AJAX)
- [ ] Live preview (sticky panel)
- [ ] Save to DB at each step
- [ ] Generate slug + event_code
- [ ] Migrations: `cagnottes`, `payment_methods`, `event_codes`

---

### Sprint 4 — Public event page (2 weeks)

**Goal**: Beautifully rendered event pages at `mywish.ma/event/{slug}`.

- [ ] Public route `/event/{slug}` in router
- [ ] `EventController::show()` with privacy logic (public vs auth view)
- [ ] Build `Views/events/show.php` based on `mockups/03-page-ibrahim-dark.html`
- [ ] Hero with photo + countdown (Alpine.js)
- [ ] CSS animations (countdown, shimmer, glow)
- [ ] Auth modal for participation (if not logged in)
- [ ] Open Graph dynamic tags
- [ ] Loading states + error boundaries
- [ ] Mobile-first responsive

---

### Sprint 5 — RSVP + Kitty contributions (2 weeks)

**Goal**: Guests can RSVP + contribute with proof upload.

- [ ] Migration: `rsvps`, `contributions`, `event_authorized_users`
- [ ] RSVP flow with auth + event code verification
- [ ] Avatar selection UI (12 emoji avatars, Alpine.js)
- [ ] Contribution flow with payment method choice
- [ ] Proof upload to Cloudinary via API
- [ ] Organizer validation interface (`/dashboard/validations`)
- [ ] Email notif to organizer when contribution received
- [ ] Update kitty totals (validated vs pending)

---

### Sprint 6 — Wishlist (1 week)

**Goal**: Wishlist with reservation system.

- [ ] Migration: `wishlist_items`
- [ ] Add wishlist items in event creation
- [ ] Display wishlist on event page
- [ ] Reservation flow (guest authenticated)
- [ ] Mark as "fulfilled" later
- [ ] Email notif to guest who reserved

---

### Sprint 7 — Organizer Dashboard (1 week)

**Goal**: Full visibility on event performance + actions.

- [ ] Dashboard route `/dashboard`
- [ ] Stats cards (views, RSVP, contributions, shares)
- [ ] Pending validations queue
- [ ] Edit event flow
- [ ] Block participant feature
- [ ] Export CSV (guests + contributions)
- [ ] Archive / duplicate event
- [ ] "My participations" tab

---

### Sprint 8 — Premium payment (2 weeks)

**Goal**: Users can upgrade to Premium 99 MAD via 3 payment methods.

- [ ] Stripe PHP SDK integration (test mode first)
- [ ] CMI integration (research Moroccan technical partner)
- [ ] PayPal SDK integration
- [ ] Pricing page + upsell modals
- [ ] Webhook handlers (subscription updates)
- [ ] Migration: `subscriptions`
- [ ] Invoice PDF generation (with TVA)
- [ ] Email confirmation with invoice
- [ ] Premium feature gating (custom slug, unlimited guests)

---

### Sprint 9 — Notifications (1 week)

**Goal**: Email notifications working + scheduled reminders.

- [ ] Setup PHPMailer with SMTP (Resend or Brevo)
- [ ] DNS records (SPF, DKIM, DMARC) in Cloudflare
- [ ] Email templates (HTML + text fallback)
- [ ] Notification triggers:
  - Event created
  - 1st RSVP received
  - Contribution proof received
  - Date/location changed → notify guests
  - J-7, J-1, J+1, J+30 reminders
- [ ] Cron job via cPanel scheduler (runs every 15 min)
- [ ] Migration: `notifications` table (queue)

---

### Sprint 10 — Admin dashboard (1 week)

**Goal**: Tools to operate the platform.

- [ ] Admin route `/admin` (protected by role check)
- [ ] Migration: add `users.role` column ('user', 'admin')
- [ ] Events list + filters
- [ ] Users list + ban/unban
- [ ] Reports inbox (moderation)
- [ ] Migration: `reports` table
- [ ] Global stats (events, revenue, conversion)
- [ ] User impersonation for support

---

### Sprint 11 — Legal & RGPD (1 week)

**Goal**: All legally required pages + RGPD compliance.

- [ ] CGU page
- [ ] Privacy policy page
- [ ] Mentions légales
- [ ] Cookie banner + consent (Alpine.js)
- [ ] Account deletion flow
- [ ] Data export (ZIP) flow
- [ ] FAQ page (15 questions)
- [ ] Contact form
- [ ] About page

---

### Sprint 12 — QA & Beta launch (1 week)

**Goal**: Stable MVP, first real users.

- [ ] Manual QA on critical paths
- [ ] Mobile testing (real devices)
- [ ] Security audit (OWASP Top 10 checklist)
- [ ] Performance: enable Cloudflare caching
- [ ] Beta testers: 10 family/friends
- [ ] Bug fixes
- [ ] Public launch announcement
- [ ] First real events created 🎉

**Total MVP**: ~14-16 weeks (~3.5-4 months)

---

## 📋 Phase 2 — V1 (Months 4-6)

### Sprint 13-14 — Marketplace partners (2 weeks)

- [ ] Subdomain `partenaires.mywish.ma`
- [ ] Partner sign-up + admin approval
- [ ] Partner dashboard (stats, leads, billing)
- [ ] Stripe recurring subscriptions
- [ ] Targeting algorithm (geo + type + demographics)
- [ ] Display partners on event pages
- [ ] Tier-based ranking (Gold > Silver > Bronze)

### Sprint 15 — More templates (2 weeks)

- [ ] +10 templates (Roblox, Royal, Marocain, Boho, etc.)
- [ ] Template marketplace UI

### Sprint 16 — Sharing enhancements (1 week)

- [ ] QR code generator (PNG download)
- [ ] Improved Open Graph rendering
- [ ] Pre-filled WhatsApp messages with emoji + photo

### Sprint 17 — Stats & exports (1 week)

- [ ] Detailed analytics
- [ ] Improved CSV exports
- [ ] Charts/graphs in dashboard (Chart.js via CDN)

**Target**: 200+ events/month at M6

---

## 📋 Phase 3 — V2 (Months 7-12)

- [ ] **PWA**: installable, offline mode, push notifications
- [ ] **i18n**: Arabic (RTL) + English support
- [ ] **WhatsApp notifications**: beyond verification
- [ ] **Google Calendar sync**: auto-add events
- [ ] **B2B**: white-label for wedding planners
- [ ] **Photo gallery**: with Cloudinary AI moderation
- [ ] **+10 more templates**: designer-quality premium options
- [ ] **Sub-events**: link multiple ceremonies
- [ ] **Co-organizers**: 2-3 admins per event

**Target**: 800+ events/month at M12

---

## 📋 Phase 4 — V3 (Months 13-18)

- [ ] **REST API** (separate from web)
- [ ] **iOS app** (native Swift or React Native)
- [ ] **Android app** (native Kotlin or React Native)
- [ ] **Push notifications** (native)
- [ ] **Offline mode** (cached events)
- [ ] **Auto-generated memory video** (Cloudinary AI)
- [ ] **Marketplace e-commerce**: order directly through MyWish (commission)
- [ ] **Regional expansion**: Tunisia, Algeria

**Target**: 5000+ events/month, 200k+ MAD/month, market leader in Maghreb

---

## 📊 KPIs & Triggers

### Phase 1 KPIs (MVP)

| KPI | M1 | M2 | M3 |
|-----|-----|-----|-----|
| Events created (cumulative) | 10 | 30 | 100 |
| Premium conversion rate | - | 10% | 25% |
| Monthly revenue (MAD) | 99 | 500 | 2,500 |
| NPS | - | - | >40 |

### Phase 2 KPIs (V1)

| KPI | M4 | M5 | M6 |
|-----|-----|-----|-----|
| Events / month | 60 | 120 | 200 |
| Premium conversion | 28% | 30% | 32% |
| Active partners | 5 | 10 | 15 |
| Monthly revenue (MAD) | 4,500 | 7,000 | 9,000 |

### Phase 3 KPIs (V2)

| KPI | M9 | M12 |
|-----|-----|-----|
| Events / month | 400 | 800 |
| Premium conversion | 35% | 35% |
| Active partners | 30 | 50 |
| Monthly revenue (MAD) | 25,000 | 50,000 |

### 🚦 Decision triggers

- ✅ **GO** : 50+ events/mois at M3 → continue + invest more
- ⚠️ **PIVOT** : <20 events/mois at M3 → revisit positioning
- ❌ **STOP** : <10 events/mois at M6 → admit non-PMF

---

## 🎯 Out of scope (rejected for now)

- ❌ **Charity / NGO kitties** (different legal regime)
- ❌ **B2B corporate events** (different product needs)
- ❌ **Crowdfunding tiers** (too complex)
- ❌ **Live streaming integration** (cost + complexity)
- ❌ **Plan de table** (V2 wedding feature)
- ❌ **Multi-language Arabic at MVP** (RTL is V2)

---

*Last updated: 2026-05-09*
