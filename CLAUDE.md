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

# Product enrichment (requires ANTHROPIC_API_KEY)
php artisan products:enrich              # Enrich all products via Claude AI
php artisan products:enrich --product=5  # Single product
php artisan products:enrich --dry-run    # Preview without saving
php artisan products:export-enriched     # Export enriched fields as SQL for production

# Review request emails
php artisan orders:send-review-requests  # Send review emails 7 days after shipping
```

## Architecture

**Stack**: Laravel 10 / PHP 8.1+ / MySQL / Tailwind CSS v4 / Alpine.js / Hotwire Turbo / Vite 5

**Auth**: Custom `AuthController` (no Breeze/Fortify). Session-based. Admin access via `is_admin` boolean on User model + `AdminMiddleware`.

**Payments**: Stripe Checkout. Webhook at `POST /stripe/webhook` (CSRF-excluded). Config via `config/cashier.php` reading `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET`.

**Email**: Brevo (Sendinblue) via `symfony/brevo-mailer` transport. Config: `MAIL_MAILER=brevo` + `BREVO_API_KEY`.

**Shipping**: Zone-based (`config/shipping.php`). FR zone: Colissimo 7.90€, Boxtal relay 5€ (free above 60€), Pickup free. International zone (BE, ES, IT): 80€ free threshold. `OrderService` handles zone detection and method availability. Boxtal parcel point map widget via `@boxtal/parcel-point-map` loaded from CDN.

### Route groups

| Group | File | Prefix | Middleware | Purpose |
|-------|------|--------|------------|---------|
| Web | `routes/web.php` | `/` | `web` | Public storefront, auth, account |
| Admin | `routes/admin.php` | `/admin` | `web, auth, admin` | Back-office CRUD |
| API | `routes/api.php` | `/api` | `api, sanctum` | Minimal |

### Key directories

- `app/Http/Controllers/Admin/` — Admin CRUD controllers (Dashboard, Product, Category, Order, Page, Brand, Discount, Tag, Customer, Setting, Review, Announcement, Shipping)
- `app/Models/` — Eloquent models. Key: Product, ProductCategory (hierarchical), Order, OrderItem, User, DiscountRule, ProductReview, StockNotification, Quiz*, Media, Page, Setting
- `app/Services/` — CartService, DiscountEngine, AddonPriceCalculator, OrderService, BoxtalConnectService
- `app/Mail/` — OrderConfirmation, NewOrderAdmin, OrderShipped, PaymentFailed, BackInStock, ReviewRequest, BilanMinceurRappel
- `app/Console/Commands/` — `Migrate/` (WP legacy import), `EnrichProductFields`, `ExportEnrichedProducts`, `SendReviewRequests`
- `resources/views/admin/` — Admin panel views (TailAdmin-based, dark mode, Alpine.js sidebar)
- `resources/views/layouts/` — Frontend layout

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

### Key services

**CartService**: Session-based cart (key `'cart'`). Item uniqueness = `product_id + md5(serialized_addons)`. Always re-validates addon prices against DB on add to prevent frontend price tampering.

**DiscountEngine**: Applies `DiscountRule` records in `sort_order`. Supports coupon codes (case-insensitive), min/max cart value, min/max quantity, category/product targeting, stackable flag, and date ranges.

**AddonPriceCalculator**: Computes addon totals from DB only (never frontend input). Price types: `fixed` or `percentage` (of base product price).

**OrderService**: Transactional order creation. Validates cart stock against DB, calculates zone-based shipping costs, builds customer notes with relay point info. Methods: `validateCartStock()`, `calculateShipping()`, `getShippingZone()`, `availableMethodsForCountry()`, `createOrder()`.

**BoxtalController**: Caches map token for 50 min (token TTL is 1 hour). Auth via base64(`BOXTAL_ACCESS_KEY:BOXTAL_SECRET_KEY`). Networks: Mondial Relay (`MONR_NETWORK`), Chronopost (`CHRP_NETWORK`).

**BoxtalConnectService**: Pushes orders to Boxtal Connect API (`POST https://api.boxtal.com/v2/orders`) with relay point and network info. Silent logging on failure (no exceptions thrown).

**StripeWebhookController**: Uses `DB::transaction` + `lockForUpdate()` for idempotency on `payment_intent.succeeded` / `payment_intent.payment_failed` events.

### Features

**Quiz system**: Full quiz/survey feature (models: Quiz, QuizQuestion, QuizChoice, QuizAnswer, QuizCompletion, QuizResult). Session-tracked answers, configurable login requirement, point-based result templates, Turbo Frame question navigation.

**Product addons**: Products support configurable add-ons grouped by `ProductAddonFieldGroup`. Addons have `required` flag, `options` array, and `price_type` (fixed or percentage). Addon pricing is always server-side validated.

**Product reviews**: `ProductReview` model with approval workflow (`is_approved`). Admin ReviewController for approve/reject/delete. Route: `POST /boutique/{product}/avis`. Automated review request emails sent 7 days after shipping via `orders:send-review-requests` command.

**Stock notifications**: `StockNotification` model. Users subscribe via `POST /boutique/{product}/alerte-stock`. `BackInStock` mail sent when product is restocked.

**Product enrichment**: AI-powered enrichment of product fields (`team_recommendation`, `benefits`, `usage_instructions`, `composition`, `unit_measure`) using Claude API. Data used in Google Shopping feed.

**Google Shopping feed**: XML feed at `GET /flux-google-shopping.xml` (`GoogleMerchantFeedController`). Excludes Bijoux category (ID 5) and subcategories. Uses enriched product fields.

**Announcement banner**: Sticky banner managed via `Setting` model (key `sticky_banner`). Admin UI at `/admin/announcement`. Stores active status, text, link, and link_label as JSON.

**Bilan Minceur**: Personalized assessment form at `/amincissement/bilan-minceur-personnalise`. Redirects to Planity booking or sends reminder email.

**Discount rules**: `DiscountRule` supports coupon codes, cart value/quantity conditions, product/category targeting, percentage or fixed amounts, date ranges, and stackable combining.

**Media library**: Centralized `Media` model for all assets (filename, path, url, mime_type, width, height, alt, title).

**Sitemap**: Auto-generated at `GET /sitemap.xml` via `SitemapController` using `spatie/laravel-sitemap`.

**Google Analytics**: Spatie analytics package. Credentials at `storage/app/analytics/service-account-credentials.json`. Property ID via `ANALYTICS_PROPERTY_ID` env var.

**Legacy redirects**: `LegacyRedirectController` handles 301 redirects from old WordPress URLs (`/produit/{slug}`, `/categorie-produit/{slug}`).

## Conventions

- All UI text is in **French**
- Routes use French slugs: `/boutique`, `/panier`, `/commande`, `/connexion`, `/inscription`, `/mon-compte`
- Prices stored as `decimal:2` in models, displayed with `number_format($price, 2, ',', ' ') €`
- User model has `'password' => 'hashed'` cast — never use `bcrypt()` when setting password via model (causes double hash)
- Turbo is active on frontend; forms that need full POST (login, checkout) use `data-turbo="false"`
- Admin views use `@extends('admin.layouts.app')` with `@section('content')`, frontend uses `<x-layouts.app>` component (also `<x-layouts.guest>` for auth pages)
- Product categories are hierarchical (parent_id). ShopController filters include parent + children bidirectionally
- Products have `is_active` boolean — non-admins see only active products; admins see all (useful for previewing)
- `Product::currentPrice()` returns `sale_price` if set, otherwise `price`
- Order numbers auto-generated as `CMD-{uniqid}` on creation
- Order model tracks: `relay_point_code`, `relay_network`, `tracking_number`, `tracking_carrier`, `shipped_at`, `gift_wrap`, `gift_type`, `gift_message`
- CartController and QuizController use Turbo Streams for partial page updates
- `/api/boxtal/*` and `/stripe/webhook` are public API routes defined in `routes/web.php` (not `routes/api.php`)
