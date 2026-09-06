# Facefood

Food delivery app: **Laravel Breeze API** + **Flutter** (blue UI).

Tax, delivery fee, currency, ETA, meals, and deals are all **managed in Laravel**. Flutter only reads `AppConfig.apiBaseUrl` plus API data.

## Folder structure

```
facefood/
├── backend/          # Laravel API (Breeze + Sanctum tokens)
├── facefood_app/     # Flutter client
└── README.md
```

## Run the API

```bash
cd backend
cp .env.example .env   # if needed
touch database/database.sqlite
php artisan key:generate
php artisan migrate --seed
php artisan serve --host=0.0.0.0 --port=8000
```

Demo user (also **admin**): `demo@facefood.pk` / `password`

API base: `http://127.0.0.1:8000/api`

### Store settings (tax / delivery)

| Action | How |
|--------|-----|
| Read (app) | `GET /api/settings` (public) |
| Change tax to 15% | `php artisan facefood:setting tax_percent 15` |
| Change delivery fee | `php artisan facefood:setting delivery_fee 50` |
| List | `php artisan facefood:setting --list` |
| Change via API | `PUT /api/settings` as admin (Sanctum token) |

```bash
# Example: GST 18%, delivery Rs. 80
php artisan facefood:setting tax_percent 18
php artisan facefood:setting delivery_fee 80

# Or API (login first, demo user is admin)
curl -X PUT http://127.0.0.1:8000/api/settings \
  -H "Authorization: Bearer TOKEN" -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"tax_percent":15,"delivery_fee":50,"tax_label":"GST","currency":"Rs.","estimated_delivery":"30-45 MINS"}'
```

Orders always compute **subtotal + delivery_fee + tax** on the server from these settings (optional per-product `tax_rate` override). Flutter shows API values; checkout also calls `POST /api/orders/quote`.

### Add a meal

```bash
# 1) Login as demo admin
TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/login \
  -H 'Content-Type: application/json' -H 'Accept: application/json' \
  -d '{"email":"demo@facefood.pk","password":"password"}' | php -r 'echo json_decode(stream_get_contents(STDIN))->token;')

# 2) POST a product (category_id from GET /api/categories)
curl -X POST http://127.0.0.1:8000/api/products \
  -H "Authorization: Bearer $TOKEN" -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "category_id": 1,
    "name": "Signature Milk Shake",
    "description": "Thick vanilla shake with chocolate drizzle.",
    "ingredients": "Milk, ice cream, chocolate",
    "price": 350,
    "image_url": "https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=800",
    "rating": 4.7,
    "calories": 420,
    "spice_level": "mild",
    "prep_time_minutes": 5,
    "is_popular": true,
    "extras": [{"name": "Extra scoop", "price": 80}]
  }'
```

Same pattern for `POST /api/categories` and `POST /api/deals` (`title`, `deal_price`, `ends_at`, `product_id`, `tags`, `image_url`).

Or add rows in `database/seeders/ProductSeeder.php` and run `php artisan db:seed`.

### Main endpoints

| Method | Path | Auth | Notes |
|--------|------|------|-------|
| GET | `/settings` | no | Tax, delivery fee, currency, ETA |
| PUT | `/settings` | admin | Update store settings |
| POST | `/login`, `/register`, `/logout` | mix | Sanctum token auth |
| GET | `/categories`, `/products`, `/deals` | no | Catalog |
| POST/PUT/DELETE | `/categories`, `/products`, `/deals` | admin | Add/edit meals & deals |
| GET/PUT | `/user` | yes | Profile |
| CRUD | `/addresses` | yes | Delivery addresses |
| GET/POST/DELETE | `/favorites`, `/favorites/toggle` | yes | Favorite products |
| POST | `/orders/quote` | no | Server totals preview |
| GET/POST | `/orders`, `/orders/{id}` | yes | Orders + 4-digit rider code |

## Run Flutter

**Single API URL** (the only client config):

- File: `facefood_app/lib/config/app_config.dart`
- Variable: `AppConfig.apiBaseUrl`

| Target | `apiBaseUrl` value |
|--------|--------------------|
| Linux / Chrome / iOS sim | `http://127.0.0.1:8000/api` |
| Android emulator | `http://10.0.2.2:8000/api` |
| Physical phone | `http://YOUR_LAN_IP:8000/api` |

```bash
cd facefood_app
flutter pub get
flutter run -d linux    # or chrome / android
flutter build apk --debug
```

Debug APK: `facefood_app/build/app/outputs/flutter-apk/app-debug.apk`

### Android APK & Play Store

Full guide (keystore, release signing, APK, AAB, Play Console submit):

→ **[ANDROID.md](ANDROID.md)**

## Screens

Home · Menu · Deals · Cart · Profile (5-tab nav) · Checkout · Order confirmation · Order detail · Favorites · Addresses · Personal info · Promotions · Payment methods · Help · Settings · Login/Register · Promo landing
