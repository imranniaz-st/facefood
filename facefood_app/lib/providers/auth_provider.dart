import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../models/user_model.dart';
import '../services/api_client.dart';
import '../services/api_services.dart';
import '../utils/user_error.dart';

class AuthProvider extends ChangeNotifier {
  final ApiClient api;
  late final AuthService _auth;

  UserModel? user;
  String? token;
  bool bootstrapping = true;
  bool loading = false;
  String? error;

  AuthProvider(this.api) {
    _auth = AuthService(api);
    _bootstrap();
  }

  bool get isAuthenticated => token != null && token!.isNotEmpty;

  Future<void> _bootstrap() async {
    final prefs = await SharedPreferences.getInstance();
    token = prefs.getString('auth_token');
    if (token != null) {
      api.setToken(token);
      try {
        user = await _auth.me();
      } catch (_) {
        await logout();
      }
    }
    bootstrapping = false;
    notifyListeners();
  }

  Future<bool> login(String email, String password) async {
    error = null;
    loading = true;
    notifyListeners();
    try {
      final result = await _auth.login(email, password);
      user = result.user;
      token = result.token;
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('auth_token', token!);
      loading = false;
      notifyListeners();
      return true;
    } catch (e) {
      error = userFacingError(e, fallback: "Couldn't log in. Check your email and password.");
      loading = false;
      notifyListeners();
      return false;
    }
  }

  Future<bool> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
    String? phone,
  }) async {
    error = null;
    loading = true;
    notifyListeners();
    try {
      final result = await _auth.register(
        name: name,
        email: email,
        password: password,
        passwordConfirmation: passwordConfirmation,
        phone: phone,
      );
      user = result.user;
      token = result.token;
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('auth_token', token!);
      loading = false;
      notifyListeners();
      return true;
    } catch (e) {
      error = userFacingError(e, fallback: "Couldn't create your account. Please try again.");
      loading = false;
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    await _auth.logout();
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    token = null;
    user = null;
    notifyListeners();
  }

  Future<void> refreshProfile() async {
    if (!isAuthenticated) return;
    user = await _auth.me();
    notifyListeners();
  }

  Future<bool> updateProfile({
    String? name,
    String? email,
    String? phone,
    String? location,
  }) async {
    error = null;
    loading = true;
    notifyListeners();
    try {
      user = await _auth.updateProfile({
        if (name != null) 'name': name,
        if (email != null) 'email': email,
        if (phone != null) 'phone': phone,
        if (location != null) 'location': location,
      });
      loading = false;
      notifyListeners();
      return true;
    } catch (e) {
      error = userFacingError(e, fallback: "Couldn't update your profile. Please try again.");
      loading = false;
      notifyListeners();
      return false;
    }
  }
}
