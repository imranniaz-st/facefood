import 'dart:convert';

import 'package:http/http.dart' as http;

import '../config/app_config.dart';

class ApiException implements Exception {
  final String message;
  final int? statusCode;

  ApiException(this.message, {this.statusCode});

  @override
  String toString() => message;
}

class ApiClient {
  String? _token;

  void setToken(String? token) => _token = token;

  Map<String, String> get _headers => {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        if (_token != null && _token!.isNotEmpty) 'Authorization': 'Bearer $_token',
      };

  Uri _uri(String path, [Map<String, String>? query]) {
    final base = AppConfig.resolvedApiBaseUrl.replaceAll(RegExp(r'/$'), '');
    final p = path.startsWith('/') ? path : '/$path';
    return Uri.parse('$base$p').replace(queryParameters: query);
  }

  Future<dynamic> get(String path, {Map<String, String>? query}) async {
    final res = await http.get(_uri(path, query), headers: _headers);
    return _handle(res);
  }

  Future<dynamic> post(String path, {Map<String, dynamic>? body}) async {
    final res = await http.post(
      _uri(path),
      headers: _headers,
      body: body == null ? null : jsonEncode(body),
    );
    return _handle(res);
  }

  Future<dynamic> put(String path, {Map<String, dynamic>? body}) async {
    final res = await http.put(
      _uri(path),
      headers: _headers,
      body: body == null ? null : jsonEncode(body),
    );
    return _handle(res);
  }

  Future<dynamic> delete(String path) async {
    final res = await http.delete(_uri(path), headers: _headers);
    return _handle(res);
  }

  dynamic _handle(http.Response res) {
    dynamic decoded;
    try {
      decoded = res.body.isEmpty ? null : jsonDecode(res.body);
    } catch (_) {
      decoded = null;
    }

    if (res.statusCode >= 200 && res.statusCode < 300) {
      return decoded;
    }

    String message = _friendlyStatus(res.statusCode);
    if (decoded is Map) {
      if (decoded['errors'] is Map) {
        final errors = decoded['errors'] as Map;
        final first = errors.values.first;
        if (first is List && first.isNotEmpty) {
          message = first.first.toString();
        }
      } else if (decoded['message'] != null) {
        final raw = decoded['message'].toString().trim();
        if (raw.isNotEmpty && !_looksTechnical(raw)) {
          message = raw;
        }
      }
    }
    throw ApiException(message, statusCode: res.statusCode);
  }

  String _friendlyStatus(int status) {
    switch (status) {
      case 401:
        return 'Please log in to continue.';
      case 403:
        return "You don't have permission to do that.";
      case 404:
        return "We couldn't find that. Please try again.";
      case 408:
      case 504:
        return 'The request timed out. Please try again.';
      case 422:
        return 'Please check your details and try again.';
      case 429:
        return 'Too many attempts. Please wait a moment.';
      default:
        if (status >= 500) {
          return "We couldn't complete that. Please try again.";
        }
        return 'Something went wrong. Please try again.';
    }
  }

  bool _looksTechnical(String message) {
    final lower = message.toLowerCase();
    const markers = [
      'sqlstate',
      'exception',
      'stack',
      'http://',
      'https://',
      '/api/',
      'laravel',
      'sanctum',
      'breeze',
      'token',
      'unauthenticated',
    ];
    return markers.any(lower.contains);
  }
}
