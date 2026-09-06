import '../services/api_client.dart';

bool _looksTechnical(String message) {
  final lower = message.toLowerCase();
  const markers = [
    'api',
    'laravel',
    'sanctum',
    'breeze',
    'token',
    'sqlstate',
    'exception',
    'stack',
    'http://',
    'https://',
    '/api/',
    'status code',
    '422',
    '500',
    '401',
    '403',
    '404',
    'request failed',
    'unauthenticated',
    'category_id',
    'tax_percent',
    'sql',
  ];
  return markers.any(lower.contains);
}

String userFacingError(
  Object error, {
  String fallback = 'Something went wrong. Please try again.',
}) {
  if (error is ApiException) {
    switch (error.statusCode) {
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
        final validation = error.message.trim();
        if (validation.isNotEmpty && !_looksTechnical(validation)) {
          return validation;
        }
        return 'Please check your details and try again.';
      case 429:
        return 'Too many attempts. Please wait a moment.';
      default:
        if (error.statusCode != null && error.statusCode! >= 500) {
          return "We couldn't complete that. Please try again.";
        }
    }
    final message = error.message.trim();
    if (message.isNotEmpty && !_looksTechnical(message)) {
      return message;
    }
  }

  return fallback;
}
