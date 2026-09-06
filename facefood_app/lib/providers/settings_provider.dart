import 'package:flutter/foundation.dart';

import '../models/store_settings.dart';
import '../services/api_client.dart';
import '../services/api_services.dart';
import '../utils/user_error.dart';

class SettingsProvider extends ChangeNotifier {
  final SettingsService _service;

  SettingsProvider(ApiClient api) : _service = SettingsService(api);

  StoreSettings settings = StoreSettings.fallback;
  bool loaded = false;
  String? error;

  Future<void> load() async {
    try {
      settings = await _service.fetch();
      error = null;
      loaded = true;
    } catch (e) {
      error = userFacingError(e, fallback: "Couldn't load store settings. Try again.");
      loaded = true;
    }
    notifyListeners();
  }
}
