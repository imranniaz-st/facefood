/// Facefood app configuration — **edit this file** to point at your Laravel API.
///
/// Tax, delivery fee, currency, and meal data all come from the API
/// (`GET /settings`, `/products`, `/deals`). Do not hardcode them here.
///
/// ## API base URL (`apiBaseUrl`)
/// Change [apiBaseUrl] once; [ApiClient] and all network calls read only from here.
///
/// | Environment              | Example value                          |
/// |--------------------------|----------------------------------------|
/// | Production               | `https://app.facefood.cafe/api`        |
/// | Android emulator (local) | `http://10.0.2.2:8000/api`             |
/// | iOS simulator / desktop  | `http://127.0.0.1:8000/api`            |
/// | Physical device (LAN)    | `http://192.168.x.x:8000/api`          |
///
/// Optional override:
/// ```bash
/// flutter run --dart-define=API_BASE_URL=http://192.168.1.10:8000/api
/// ```
library;

class AppConfig {
  /// Single source of truth for the Laravel API base URL (include `/api`).
  static const String apiBaseUrl = 'https://app.facefood.cafe/api';

  /// Resolved base URL: `--dart-define=API_BASE_URL=...` if set, else [apiBaseUrl].
  static String get resolvedApiBaseUrl {
    const fromDefine = String.fromEnvironment('API_BASE_URL');
    if (fromDefine.isNotEmpty) return fromDefine;
    return apiBaseUrl;
  }
}
