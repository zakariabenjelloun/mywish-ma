# 🎁 MyWish.ma — Master Plan v3 (PHP/MySQL)

> **Document de référence v3.0 — Bible du projet**
>
> *La page de votre événement familial. Invitations, cagnotte et souvenirs, tout sur un lien.*
>
> Cette version intègre toutes les décisions prises depuis la v1, notamment :
> - La nouvelle Direction Artistique v2 dark premium
> - Le passage à PHP/MySQL/cPanel comme stack technique

**Dernière mise à jour** : 2026-05-09
**Version** : 3.0

---

## 📑 Sommaire

1. [Executive Summary](#1-executive-summary)
2. [Vision & positionnement](#2-vision--positionnement)
3. [Architecture & Stack technique](#3-architecture--stack-technique)
4. [Modèle de données](#4-modèle-de-données)
5. [Authentification & Privacy](#5-authentification--privacy)
6. [Catalogue d'événements](#6-catalogue-dévénements)
7. [Cagnottes](#7-cagnottes)
8. [Wishlist](#8-wishlist)
9. [Onboarding](#9-onboarding)
10. [Dashboards](#10-dashboards)
11. [Direction Artistique](#11-direction-artistique-v2-dark)
12. [Monétisation](#12-monétisation)
13. [Workflow partenaire](#13-workflow-partenaire)
14. [Modération & sécurité](#14-modération--sécurité)
15. [Notifications](#15-notifications)
16. [Partage & viral loop](#16-partage--viral-loop)
17. [RGPD & pages légales](#17-rgpd--pages-légales)
18. [État d'avancement](#18-état-davancement)
19. [Roadmap & cahier des charges](#19-roadmap--cahier-des-charges)

---

## 1. Executive Summary

### 🎯 Le produit

**MyWish.ma** est une plateforme web (responsive) qui permet de créer en 5 minutes une page web personnalisée pour un événement familial : invitation, RSVP, cagnotte avec preuves de paiement, wishlist, suggestions partenaires, partage WhatsApp.

### 💰 Modèle économique

- **Freemium** : Page gratuite limitée à 15 invités, branding MyWish visible
- **Premium 99 MAD/event** : invités illimités + lien personnalisé
- **Marketplace partenaires** : annuaire payant (99/299/599 MAD/mois)
- **MyWish ne touche jamais l'argent des cagnottes** → pas de licence financière requise au Maroc

### 🎯 Marché cible

- **Primary** : Familles marocaines au Maroc
- **Secondary** : Diaspora marocaine (5M+ personnes)
- **B2B** : Pâtissiers, fleuristes, photographes, wedding planners

### 🚀 Ambition 12 mois

- 800+ événements créés/mois au M12
- Revenu mensuel ~50 000 MAD au M12

### 🏗️ Stack technique

```yaml
Backend:    PHP 8.2+ (vanilla, no framework — keep it simple)
Database:   MySQL 8.0
Frontend:   TailwindCSS (precompiled) + Alpine.js (CDN)
Icons:      Lucide (line, no emojis in functional UI)
Hosting:    OVH Maroc Pro (cPanel) — ~80 MAD/month
Deployment: cPanel Git Version Control + .cpanel.yml
```

**Pourquoi PHP/MySQL** : voir `DECISIONS.md` (décision du 2026-05-09).

---

## 2. Vision & positionnement

### 🌟 Pitch

> **"Khotba, anniversaire, mariage… Tout sur un lien."**

### ✅ Ce que MyWish EST

- Une page-souvenir d'événement, accessible via un simple lien WhatsApp
- Un agrégateur : invitation + RSVP + cagnotte + wishlist
- Un outil festif premium avec animations smooth
- Un annuaire de partenaires
- Un pont diaspora ↔ famille au Maroc

### 🚫 Ce que MyWish N'EST PAS

- Pas une banque / agrégateur de paiement
- Pas un réseau social
- Pas un outil corporate
- Pas une plateforme de cagnotte solidaire / charité

### 👥 Personas (résumé)

| Persona | Description | Offre |
|---------|-------------|-------|
| 👨‍👩‍👧 Salma — Maman organisée | 35 ans Casablanca, anniv enfants | Premium 99 MAD |
| 💍 Yasmine & Karim — Mariés | 28-32 ans Rabat, mariage 200 inv. | Premium + marketplace |
| 👶 Fatima — Grand-mère | 62 ans Marrakech, Sebou | Premium (créé par sa fille) |
| ✈️ Mehdi — Diaspora | 30 ans Lyon, cadeau cousine | Page mariage + Wise |
| 🎂 Nezha — Pâtissière partenaire | Cake designer Casablanca | Annuaire Silver 299 MAD/mois |

---

## 3. Architecture & Stack technique

### 🏛️ Les 4 mondes

```
🌍 PUBLIC (sans login)
   Landing, pages event (lecture), marketplace publique, FAQ, légal

👤 ORGANISATEUR (Google + WhatsApp verif)
   Création event, dashboard, validation contributions, suivi

🎉 INVITÉ (Google + code event)
   Lecture libre, RSVP/contribution avec auth

🤝 PARTENAIRE (sous-domaine partenaires.mywish.ma)
   Inscription, dashboard, abonnement récurrent

🛡️ ADMIN (admin.mywish.ma)
   Modération, stats globales, support
```

### 🛠️ Stack détaillé

```yaml
Backend:
  language: PHP 8.2+
  framework: NONE (vanilla, modern style — classes, namespaces, PSR-4)
  router: Custom Router class (src/Core/Router.php)
  orm: PDO direct with prepared statements
  templating: Plain PHP views with extract()
  autoload: Custom PSR-4 autoloader (no Composer required at MVP)

Frontend:
  styling: TailwindCSS — pre-compiled to public/assets/css/app.css
    (Compiled locally with `npx tailwindcss`, the .css file is committed)
  reactivity: Alpine.js v3 (loaded from CDN)
  icons: Lucide via inline SVG or @lucide/cdn
  fonts: Plus Jakarta Sans + Inter via Google Fonts CDN

Database:
  engine: MySQL 8.0
  client: PHP PDO (prepared statements only)
  migrations: Plain .sql files in database/migrations/
    Numbered: 000_xxx.sql, 001_xxx.sql, etc.
    Tracked in `migrations` table

External services:
  oauth: Google (via custom HTTP, no SDK needed)
  whatsapp: WhatsApp Cloud API (cURL HTTP requests)
  email: PHPMailer with SMTP (Resend or Brevo)
  images: Cloudinary (upload via API)
  payments: Stripe PHP SDK + CMI custom + PayPal SDK

Hosting:
  provider: OVH Maroc Pro (~80 MAD/month)
  panel: cPanel
  php_version: 8.2 (set via cPanel's PHP Selector)
  mysql_version: 8.0
  ssl: Let's Encrypt (free via cPanel AutoSSL)
  cdn: Cloudflare (free + DDoS protection)

Deployment:
  versioning: Git (private GitHub repo)
  deployment_tool: cPanel Git Version Control
  branches:
    - dev → dev.mywish.ma → database_dev
    - main → mywish.ma → database_prod
  config_script: .cpanel.yml
```

### 💰 Coûts mensuels MVP

| Service | Coût/mois |
|---------|-----------|
| Hébergement OVH Maroc Pro | ~80 MAD |
| Domaine mywish.ma | ~13 MAD (pro-rata 150/an) |
| WhatsApp Cloud API | 0 MAD (free tier 1000 conv/mois) |
| Cloudinary | 0 MAD (free tier) |
| Resend (emails) | 0 MAD (free tier 3000/mois) |
| Stripe | 0 fixe + 2.9% + ~1.5 MAD/transaction |
| Cloudflare | 0 MAD (free tier) |
| **Total** | **~93 MAD/mois** |

### 📁 Structure de projet

```
mywish-ma/
├── public/                   ← Web root (= public_html in prod)
│   ├── index.php             ← Single entry point (front controller)
│   ├── .htaccess             ← URL rewriting + security
│   └── assets/               ← CSS, JS, images
├── src/                      ← App code (NEVER directly accessible)
│   ├── Config/Env.php        ← .env loader
│   ├── Core/                 ← Database, Router, View
│   ├── Controllers/
│   ├── Models/
│   ├── Views/                ← PHP templates
│   └── Helpers/
├── database/
│   ├── migrations/           ← Versioned .sql files
│   └── seeds/                ← Test data
├── storage/                  ← Hors Git
│   ├── logs/
│   ├── cache/
│   └── uploads/
├── docs/
├── scripts/
├── .env                      ← NEVER commit
├── .env.example
├── .gitignore
└── .cpanel.yml               ← cPanel deployment script
```

### 🎯 Pourquoi PHP "vanilla" et pas Laravel ?

Voir `DECISIONS.md`. En résumé :
- ✅ Plus simple à maintenir pour un fondateur non-dev
- ✅ Pas de Composer obligatoire (mais possible)
- ✅ Marche sur n'importe quel cPanel sans config spéciale
- ✅ Dev peut être trouvé facilement au Maroc (PHP de base = universel)
- ⚠️ Trade-off : on réécrit les fonctionnalités basiques (auth, sessions, etc.) — mais c'est un investissement raisonnable pour la simplicité

---

## 4. Modèle de données

### 🗄️ Schéma général (MySQL)

Voir `database/migrations/` pour le schéma exact (versionné).

#### Tables principales

| Table | Description |
|-------|-------------|
| `users` | Utilisateurs (organisateurs + invités), auth Google + WhatsApp verif |
| `events` | Pages d'événements (1 par event) |
| `templates` | Templates visuels (6 au MVP) |
| `cagnottes` | Cagnottes liées aux événements (4 types) |
| `payment_methods` | Moyens de paiement de l'organisateur (cash, RIB, CashPlus, Wise) |
| `contributions` | Contributions des invités avec preuve de paiement |
| `wishlist_items` | Cadeaux dans une wishlist (avec réservation) |
| `rsvps` | RSVPs des invités |
| `event_codes` | Codes d'accès événement (modifiables) |
| `event_authorized_users` | Liste des invités autorisés (après saisie code) |
| `event_views` | Stats de vues |
| `partners` | Partenaires marketplace |
| `partner_placements` | Affichage des partenaires sur les pages event |
| `notifications` | File d'attente notifications (email au MVP) |
| `reports` | Signalements modération |
| `subscriptions` | Paiements Premium 99 MAD |
| `migrations` | Tracking des migrations appliquées |

### 🔗 Relations clés

```
users ──1:N──> events (owner)
users ──N:M──> events (via rsvps, contributions, event_authorized_users)
events ──1:N──> cagnottes
events ──1:N──> payment_methods
cagnottes ──1:N──> contributions
cagnottes ──1:N──> wishlist_items (si type='wishlist')
events ──1:N──> rsvps
events ──N:1──> templates
users ──1:1──> partners
partners ──N:M──> events (via partner_placements ciblé)
```

### 📝 Conventions DB

Voir `docs/DATABASE.md` pour les détails (naming, types, indexes, sécurité).

**Règles d'or** :
- ✅ Toutes les tables : InnoDB + utf8mb4 + utf8mb4_unicode_ci
- ✅ Colonnes obligatoires : `id` (auto-increment), `created_at`, `updated_at`
- ✅ Foreign keys avec `ON DELETE` explicite
- ✅ Index sur toutes les FK et colonnes WHERE/ORDER fréquentes

---

## 5. Authentification & Privacy

### 🔐 Auth — Organisateur

```
1. Choix type event (sans login)
2. Au moment de créer/sauvegarder :
   ├─ "Continuer avec Google" → OAuth flow custom (PHP cURL)
   └─ Récupération profil Google (email, nom, avatar)
3. Demande tel : "Ton numéro WhatsApp"
   ├─ Envoi code via WhatsApp Cloud API
   ├─ User saisit code à 6 chiffres
   └─ users.phone_verified = true
4. Compte créé, redirection création event
```

### 🔐 Auth — Invité

```
1. Réception lien WhatsApp → ouverture page (lecture libre)
2. Clic "Je viens" ou "Participer" :
   ├─ Si déjà connecté Google → demande code event uniquement
   ├─ Sinon : Google sign-in + code event
3. Session persistante 30 jours via cookies
```

### 🔒 Règles de privacy (visibilité par type d'info)

| Information | 🌍 Public | 🔐 Auth requise |
|-------------|-----------|-----------------|
| Photo héros + titre | ✅ | — |
| Date / heure | ✅ | — |
| Lieu général ("Casablanca, Anfa") | ✅ | — |
| Lieu précis (adresse + GPS) | ❌ | ✅ |
| Compte à rebours | ✅ | — |
| Mot d'invitation | ✅ | — |
| Pourcentage cagnotte ("42%") | ✅ | — |
| Montant exact récolté | ❌ | ✅ |
| Objectif cagnotte | ✅ | — |
| Nombre RSVP ("23 personnes") | ✅ | — |
| Liste des invités | ❌ | ✅ |
| Liste des contributeurs | ❌ | ✅ |
| Montants individuels | ❌ | ✅ |
| Moyens paiement (RIB...) | ❌ | ✅ |
| Suggestions partenaires | ✅ | — |

### 🛡️ Sécurité technique

- **Prepared statements** sur 100% des requêtes SQL (PDO)
- **CSRF tokens** sur tous les formulaires (helper `csrf_field()`)
- **htmlspecialchars()** sur tous les outputs (helper `e()`)
- **Sessions** : `httponly + samesite=lax + secure (HTTPS)`
- **Tokens d'accès event** : 6 caractères alphanumériques aléatoires
- **Rate limiting** : à implémenter via cPanel ou code custom
- **HTTPS obligatoire** (Let's Encrypt via cPanel)

---

## 6. Catalogue d'événements

### 📋 Les 5 types MVP

| Type | Templates MVP | Cagnottes par défaut |
|------|---------------|-----------------------|
| 🎂 Anniversaire | "Festif Kids" + "Élégant Adulte" | Product Goal ou Cash Goal |
| 💍 Mariage | "Élégant Universel" | Cash Goal (voyage) + Wishlist |
| 💗 Baby shower | "Pastel Doux" | Wishlist puériculture |
| 👶 Naissance | "Doux Universel" | Cash Goal |
| 🌟 Autre/Spontané | "Festif Polyvalent" | Libre choix |

**6 templates** au lancement. **+13** prévus en V1.

---

## 7. Cagnottes

### 💵 Les 4 types

1. **Cash Goal** — "Récolter 5000 MAD"
2. **Product Goal** — "Acheter cette PS5" (avec photo)
3. **Donation Libre** — "Contribuez ce que vous voulez"
4. **Wishlist** — "10 cadeaux possibles" (avec réservation)

### 💳 Flow de paiement

⚠️ **MyWish ne touche JAMAIS l'argent.**

```
1. Invité authentifié clique "Je participe"
2. Modal : choix moyen de paiement
   - Cash le jour J
   - Virement RIB
   - CashPlus
   - Wise (diaspora)
3. Invité paie de son côté (hors MyWish)
4. Retour : "J'ai payé" + upload preuve (capture)
5. Statut : ⏳ "En attente de validation"
6. Notif email à l'organisateur
7. Organisateur valide ou refuse
   - VALIDÉ → contribution comptée ✅
   - REFUSÉ → contribution annulée
```

### 📊 Affichage UX

```
💰 Récolté : 2 300 MAD ✅          📊 42% atteint
⏳ En attente : 800 MAD (3 promesses)
🎯 Objectif : 5 000 MAD
```

→ Validations en clair, promesses en grisé semi-transparent avec badge.

---

## 8. Wishlist

### 🎁 Mécanique

- L'organisateur ajoute **manuellement** des cadeaux (photo + nom + prix estimé + lien externe)
- Les invités peuvent **réserver** un cadeau (évite les doublons)
- Système de réservation avec annulation possible
- L'invité peut marquer comme "Offert" après l'événement

---

## 9. Onboarding

### 🚀 Flow en 6 étapes

| Étape | Description | Obligatoire ? |
|-------|-------------|---------------|
| 0 | Choix type d'event | ✅ Avant login |
| 1 | Auth Google + tel WhatsApp | ✅ |
| 2 | Infos de base (nom, date, lieu) | ✅ |
| 3 | Template visuel | ⏭️ Skippable |
| 4 | Cagnotte | ⏭️ Skippable |
| 5 | Personnalisation (photo, message, code) | ⏭️ Skippable |
| 6 | Publication & partage | ✅ |

**Aperçu live** dès l'étape 2.
**Sauvegarde auto** à chaque champ.
**MVP en 2 minutes** possible si on skip tout.

---

## 10. Dashboards

### 🏠 Dashboard organisateur

Vue unifiée :
```
📁 MES ÉVÉNEMENTS (créés)
🎉 MES PARTICIPATIONS (events où invité)
```

Actions par event : Éditer / Partager / Archiver / Dupliquer / Bloquer participants

### 🎉 Dashboard invité

Pour ceux qui ont participé sans créer :
- Mes participations
- Modifier ma contribution (si pending)
- Modifier mon RSVP (avant J-1)

---

## 11. Direction Artistique v2 Dark

> Voir `docs/DESIGN-SYSTEM.md` pour les détails complets.

**Style** : Dark mode premium, "Calm festivity", inspiré de Linear/Vercel/Arc.

**Couleurs principales** :
- 🍑 Primary : `#EA580C` (peach saturated)
- ✨ Gold : `#FCD34D` (premium accents)
- 🌑 Surfaces : `#0A0A0A` à `#27272A`

**Typographies** : Plus Jakarta Sans + Inter

**Icônes** : Lucide (NO emojis dans l'UI fonctionnelle)

**Règle** : 80% neutres + 15% pêche + 5% or

**Mockups disponibles** : `docs/mockups/01-direction-artistique-v2-dark.html`, `02-landing-dark.html`, `03-page-ibrahim-dark.html`

---

## 12. Monétisation

### 💎 Tableau Gratuit vs Premium

| Feature | 🆓 Gratuit | 💎 Premium 99 MAD |
|---------|------------|---------------------|
| **Invités max** | **15** | ∞ |
| **Branding MyWish** | Visible avec CTA | Footer discret |
| **Lien personnalisé** | ❌ | ✅ |
| 4 types de cagnottes | ✅ | ✅ |
| Wishlist | ✅ | ✅ |
| Templates de base | ✅ | ✅ |
| Suggestions partenaires | ✅ | ✅ |
| Durée page active | 30 jours | 90 jours |
| Archive consultation | ❌ | 1 an |

### 💰 Sources de revenu

1. **Premium 99 MAD/event** (paiement unique via CMI / Stripe / PayPal)
2. **Annuaire partenaires** (Bronze 99 / Silver 299 / Gold 599 MAD/mois)
3. **Branding viral** = acquisition gratuite

### 💸 Process paiement Premium

```
1. Click "Passer à Premium"
2. Modal : CMI / Stripe / PayPal
3. Redirection provider
4. Paiement
5. Webhook → events.is_premium = true + facture PDF
6. Email confirmation
```

---

## 13. Workflow partenaire

Sous-domaine `partenaires.mywish.ma`.

| Tier | Prix/mois | Visibilité |
|------|-----------|------------|
| 🥉 Bronze | 99 MAD | Listé |
| 🥈 Silver | 299 MAD | Featured + Top 3 zone |
| 🥇 Gold | 599 MAD | TOP placement + exclusivité |

**Ciblage** : géo (région) + type événement + démographique.

---

## 14. Modération & sécurité

### Stratégie 3 niveaux

1. **Préventif** : CGU, mots interdits, limite 3 events/jour/user
2. **Réactif** : Bouton signaler, email admin
3. **Automatique** : 3 signalements → suspension auto

**Outils admin** sur `admin.mywish.ma` :
- Liste events / users
- Inbox modération
- Stats globales
- Outils support (impersonate)

---

## 15. Notifications

**MVP** : Email uniquement (via Resend/Brevo SMTP)

Triggers principaux :
- Création event réussie
- 1er RSVP / contribution
- Validation requise (3+ pending)
- J-7, J-1, J+1, J+30

**V2** : WhatsApp avancé + Push (PWA)

---

## 16. Partage & viral loop

### Méthodes
1. **Bouton WhatsApp** principal (message pré-rempli)
2. **Copier le lien**
3. **QR Code** téléchargeable PNG
4. **Email** (mailto:)

### Open Graph
Tags HTML générés dynamiquement avec photo héros 1200x630.

### K-factor cible : **0.5+**

```
1. Salma crée page → partage WhatsApp famille (50 personnes)
2. Voient branding "Crée ta page sur MyWish.ma"
3. 5% (3 personnes) cliquent
4. 1 crée son propre event → boucle
```

---

## 17. RGPD & pages légales

- **Suppression compte** : email confirmation 30j, anonymisation
- **Export données** : ZIP avec profile + events + RSVP + contributions
- **Pages légales** : CGU, Privacy, Mentions légales, Cookies banner

---

## 18. État d'avancement

### ✅ Phase pré-MVP (TERMINÉE)

- ✅ Stratégie produit complète (toutes décisions documentées)
- ✅ Modèle de données défini (15+ tables)
- ✅ Direction Artistique v2 dark validée
- ✅ 3 mockups HTML produits
- ✅ Bootstrap PHP/MySQL/cPanel créé
- ✅ Architecture environnements dev/prod
- ✅ Système de migrations SQL
- ✅ Workflow Git + cPanel défini

### 🚧 Phase MVP (À VENIR)

Voir `docs/ROADMAP.md` pour le détail.

**Sprint 0 — Setup cPanel** (1-2 semaines)

Voir `docs/TODO.md`.

---

## 19. Roadmap & cahier des charges

> Voir `docs/ROADMAP.md` pour le détail complet.

### Phases

- **Phase 1 — MVP** (3-4 mois) : Auth + 5 types events + Premium
- **Phase 2 — V1** (mois 4-6) : Marketplace + +10 templates
- **Phase 3 — V2** (mois 7-12) : Multi-langues + WhatsApp avancé + B2B
- **Phase 4 — V3** (mois 13+) : API + apps natives séparées

### KPIs critiques

| KPI | M3 | M6 | M12 |
|-----|-----|-----|-----|
| Events / mois | 50 | 180 | 800 |
| Conversion gratuit→Premium | 25% | 30% | 35% |
| Partenaires actifs | 3 | 15 | 50 |
| Revenu mensuel (MAD) | 2 500 | 9 000 | 50 000 |

### 🚦 Triggers GO/PIVOT/STOP

- ✅ **GO** : 50+ events/mois au M3
- ⚠️ **PIVOT** : <20 events/mois au M3
- ❌ **STOP** : <10 events/mois au M6

---

## 📝 Notes finales

> **Cette stratégie repose sur 3 piliers fondamentaux** :
> 1. **DISTRIBUTION** — Réseau familial + viral loop branding
> 2. **PRIVACY-FIRST** — Lecture libre, action authentifiée
> 3. **NEUTRALITY** — On ne touche jamais à l'argent

> **Ce document est ta bible pendant 12 mois.**
> Mets-le à jour à chaque décision majeure (voir `DECISIONS.md`).

---

*Document préparé pour MyWish.ma — v3.0 PHP/MySQL/cPanel*
*Date : Mai 2026*
