# 📋 Changelog

All notable changes to MyWish.ma will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Planned
- Sprint 0: Project setup (in progress)

---

## [0.1.0-bootstrap] — 2026-05-09

### Added
- Initial project bootstrap
- `CLAUDE.md` for Claude Code context
- `README.md` with project overview
- `docs/MASTER-PLAN.md` v2.0 — full product strategy
- `docs/DESIGN-SYSTEM.md` — complete design tokens & components
- `docs/DECISIONS.md` — decision log (ADR-style)
- `docs/ROADMAP.md` — sprint plan from MVP to V3
- `docs/TODO.md` — Sprint 0 task list
- `docs/CHANGELOG.md` — this file
- `docs/mockups/` — 3 HTML mockups (DA, landing, page Ibrahim)
- `.github/workflows/` — CI + deploy GitHub Actions
- `.github/ISSUE_TEMPLATE/` — bug report, feature request, decision templates
- `.github/PULL_REQUEST_TEMPLATE.md`
- `scripts/setup.sh` — auto-setup script
- `scripts/new-decision.sh` — log a new decision quickly
- `scripts/new-sprint.sh` — start a new sprint
- `.env.example` — environment variables template
- `.gitignore` — standard Next.js ignore
- `SETUP-GUIDE.md` — installation guide

### Decisions captured
- Brand: MyWish.ma
- Stack: Next.js 14 + Supabase + Vercel
- Design: Dark mode "Calm festivity" with peach + gold
- Pricing: Free (15 guests) + Premium 99 MAD
- Marketplace: Subscription directory (Bronze/Silver/Gold)
- Auth: Google OAuth only, WhatsApp phone verification
- Privacy: Public reads, auth-required actions
- Languages: French only at MVP, AR/EN in V2

---

## Format reference

### Types of changes

- **Added** — for new features
- **Changed** — for changes in existing functionality
- **Deprecated** — for soon-to-be removed features
- **Removed** — for now removed features
- **Fixed** — for any bug fixes
- **Security** — for vulnerability patches

### Versioning

- **Major** (X.0.0): breaking changes (e.g., schema migration)
- **Minor** (0.X.0): new features (backwards-compatible)
- **Patch** (0.0.X): bug fixes (backwards-compatible)

Pre-MVP versions: `0.x.0-bootstrap`, `0.x.0-mvp`, etc.
First public release: `1.0.0`.
