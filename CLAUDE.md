# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Dev server
php artisan serve              # HTTP at :8000
npm run dev                    # Vite HMR for assets

# Build
npm run build                  # Production assets

# Database
php artisan migrate            # Run migrations
php artisan tinker             # Interactive REPL

# Tests
php artisan test               # All tests
php artisan test tests/Feature/ShopTest.php  # Single test

# Cache clear (useful when debugging)
php artisan cache:clear && php artisan view:clear && php artisan route:clear

# Code formatting
./vendor/bin/pint
```

## Architecture

**Stack**: Laravel 10 / PHP 8.1+ / MySQL / Tailwind CSS v4 / Alpine.js / Hotwire Turbo / Vite 5

**Auth**: Custom `AuthController` (no Breeze/Fortify). Session-based. Admin access via `is_admin` boolean on User model + `AdminMiddleware`.

**Payments**: Stripe Checkout. Webhook at `POST /stripe/webhook` (CSRF-excluded). Config via `config/cashier.php` reading `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`.

**Shipping**: 3 modes (Colissimo 7.90€, Boxtal relay 5€, Pickup free). Boxtal parcel point map widget via `@boxtal/parcel-point-map` loaded from CDN. Config in `config/shipping.php`.

### Route groups

| Group | File | Prefix | Middleware | Purpose |
|-------|------|--------|------------|---------|
| Web | `routes/web.php` | `/` | `web` | Public storefront, auth, account |
| Admin | `routes/admin.php` | `/admin` | `web, auth, admin` | Back-office CRUD |
| API | `routes/api.php` | `/api` | `api, sanctum` | Minimal |

### Key directories

- `app/Http/Controllers/Admin/` — Admin CRUD controllers (Dashboard, Product, Category, Order, Page)
- `app/Models/` — 22 Eloquent models. Key: Product, ProductCategory (hierarchical), Order, OrderItem, User
- `resources/views/admin/` — Admin panel views (TailAdmin-based, dark mode, Alpine.js sidebar)
- `resources/views/layouts/` — Frontend layout
- `app/Console/Commands/Migrate/` — WP legacy import commands (`migrate:wp-orders`, `migrate:wp-customers`, etc.)

### Vite entry points

```
resources/css/app.css    → Frontend styles
resources/js/app.js      → Frontend JS (Turbo + Alpine)
resources/css/admin.css  → Admin styles (separate theme tokens)
resources/js/admin.js    → Admin JS (Alpine only, no Turbo)
```

### Database connections

- **mysql** (default): Laravel app database (`institut_laravel`)
- **wp_legacy**: WordPress database (`institut_db`, prefix `mod352_`) for data migration

## Conventions

- All UI text is in **French**
- Routes use French slugs: `/boutique`, `/panier`, `/commande`, `/connexion`, `/inscription`, `/mon-compte`
- Prices stored as `decimal:2` in models, displayed with `number_format($price, 2, ',', ' ') €`
- User model has `'password' => 'hashed'` cast — never use `bcrypt()` when setting password via model (causes double hash)
- Turbo is active on frontend; forms that need full POST (login, checkout) use `data-turbo="false"`
- Admin views use `@extends('admin.layouts.app')` with `@section('content')`, frontend uses `<x-layouts.app>` component
- Product categories are hierarchical (parent_id). ShopController filters include parent + children bidirectionally
