import 'package:intl/intl.dart';

import '../models/store_settings.dart';

String formatMoney(num value, [String currency = 'Rs.']) {
  final symbol = currency.trim().endsWith('.') ? '$currency ' : '$currency ';
  return NumberFormat.currency(
    locale: 'en_PK',
    symbol: symbol,
    decimalDigits: 0,
  ).format(value);
}

/// Back-compat helper; prefer [formatMoney] with store currency.
String formatRs(num value) => formatMoney(value);

String formatMoneyFrom(num value, StoreSettings settings) => formatMoney(value, settings.currency);
