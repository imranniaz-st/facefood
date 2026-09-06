# Facefood WordPress Plugin

WooCommerce + Elementor integration for the Facefood Laravel API and Flutter mobile app.

## Requirements

- WordPress 6.0+
- PHP 8.1+
- WooCommerce 8+
- Elementor (free or pro)
- Facefood Laravel API running (e.g. `https://app.facefood.cafe/api`)

## Install

1. Zip the `facefood-wordpress` folder or upload it to `wp-content/plugins/facefood-integration/`
2. Activate **Facefood Integration** in WordPress admin
3. Go to **Facefood → Settings**
   - API Base URL: `https://app.facefood.cafe/api`
   - Sync Key: same value as `WORDPRESS_SYNC_KEY` in Laravel `.env`
4. Go to **Facefood → Sync**
   - Click **Import Menu to API** (loads all items from Facefood 2026 PDF menu)
   - Click **Sync Now** (creates/updates WooCommerce products)

## Laravel setup

Add to `backend/.env`:

```env
WORDPRESS_SYNC_KEY=your-long-random-secret-key
WORDPRESS_SITE_URL=https://facefood.cafe
```

Run migrations and seed menu:

```bash
cd backend
php artisan migrate
php artisan db:seed
```

## Elementor widgets

After activation, find these widgets under the **Facefood** category in Elementor:

| Widget | Description |
|--------|-------------|
| Facefood Menu Grid | Product cards with optional extra toppings |
| Facefood Category Menu | Tabbed menu by category with toppings |
| Facefood Deals | Active deals carousel/grid |
| Facefood App Download | Play Store / App Store buttons |
| Facefood Login / Register | Shared Laravel account login & signup |
| Facefood Sign Up Popup | Professional popup for guests (auto-show + floating button) |

## Sign up popup (Elementor)

Add widget **Facefood Sign Up Popup** to any page (e.g. homepage footer section).

- Only shows when user is **not logged in**
- **Sign up** and **Log in** tabs
- Auto-opens after a few seconds (configurable)
- Floating **Sign up** button bottom-right
- Same Laravel account as the mobile app

Shortcode: `[facefood_auth_popup]`  
Optional: `[facefood_auth_popup auto_show="yes" delay="5" default_tab="signup"]`

## Shortcodes (login / register)

| Shortcode | Description |
|-----------|-------------|
| `[facefood_login]` | Login form (same Laravel DB as mobile app) |
| `[facefood_register]` | Create account |
| `[facefood_account]` | Account panel when logged in, login form when not |

One account works on **WordPress**, **WooCommerce**, and the **Flutter app**.

## Security

- All output escaped (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`)
- AJAX protected with WordPress nonces
- Login/register rate limiting by IP
- API errors sanitized — no server file paths shown to visitors
- Laravel API returns safe JSON errors (no stack traces in production)

## API sync endpoints

| Method | Path | Auth |
|--------|------|------|
| GET | `/api/wordpress/catalog` | Sync key header |
| POST | `/api/wordpress/link` | Sync key |
| POST | `/api/wordpress/products/upsert` | Sync key |

Header: `X-Facefood-Sync-Key: YOUR_KEY`

## Menu source

All products match **Menu Facefood 2026** PDF:

- Shawarma, Afghani, Burgers, Roll Paratha, Platters, Wraps
- Fries, Crispy, Kids Meal, On Demand, Extras

Same data is seeded in Laravel (`php artisan db:seed`) and available in the Flutter app.

## Folder structure

```
facefood-wordpress/
├── facefood-integration.php   # Main plugin file
├── includes/                  # Core classes
├── admin/                     # Settings & sync pages
├── elementor/widgets/         # Elementor widgets
├── assets/                    # CSS & JS
└── uninstall.php
```
