class StoreSettings {
  final double taxRate;
  final double taxPercent;
  final String taxLabel;
  final double deliveryFee;
  final String estimatedDelivery;
  final int estimatedMinutes;
  final String currency;
  final String currencyCode;
  final String storeName;

  const StoreSettings({
    required this.taxRate,
    required this.taxPercent,
    required this.taxLabel,
    required this.deliveryFee,
    required this.estimatedDelivery,
    required this.estimatedMinutes,
    required this.currency,
    required this.currencyCode,
    required this.storeName,
  });

  factory StoreSettings.fromJson(Map<String, dynamic> json) {
    final rate = (json['tax_rate'] as num?)?.toDouble() ?? 0.15;
    return StoreSettings(
      taxRate: rate,
      taxPercent: (json['tax_percent'] as num?)?.toDouble() ?? (rate * 100),
      taxLabel: json['tax_label'] as String? ?? 'GST',
      deliveryFee: (json['delivery_fee'] as num?)?.toDouble() ?? 50,
      estimatedDelivery: json['estimated_delivery'] as String? ?? '30-45 MINS',
      estimatedMinutes: (json['estimated_minutes'] as num?)?.toInt() ?? 40,
      currency: json['currency'] as String? ?? 'Rs.',
      currencyCode: json['currency_code'] as String? ?? 'PKR',
      storeName: json['store_name'] as String? ?? 'Facefood',
    );
  }

  static const fallback = StoreSettings(
    taxRate: 0.15,
    taxPercent: 15,
    taxLabel: 'GST',
    deliveryFee: 50,
    estimatedDelivery: '30-45 MINS',
    estimatedMinutes: 40,
    currency: 'Rs.',
    currencyCode: 'PKR',
    storeName: 'Facefood',
  );
}

class QuoteModel {
  final double subtotal;
  final double deliveryFee;
  final double tax;
  final double taxRate;
  final double taxPercent;
  final String taxLabel;
  final double total;
  final String currency;
  final String estimatedDelivery;

  QuoteModel({
    required this.subtotal,
    required this.deliveryFee,
    required this.tax,
    required this.taxRate,
    required this.taxPercent,
    required this.taxLabel,
    required this.total,
    required this.currency,
    required this.estimatedDelivery,
  });

  factory QuoteModel.fromJson(Map<String, dynamic> json) {
    return QuoteModel(
      subtotal: (json['subtotal'] as num).toDouble(),
      deliveryFee: (json['delivery_fee'] as num).toDouble(),
      tax: (json['tax'] as num).toDouble(),
      taxRate: (json['tax_rate'] as num?)?.toDouble() ?? 0,
      taxPercent: (json['tax_percent'] as num?)?.toDouble() ?? 0,
      taxLabel: json['tax_label'] as String? ?? 'GST',
      total: (json['total'] as num).toDouble(),
      currency: json['currency'] as String? ?? 'Rs.',
      estimatedDelivery: json['estimated_delivery'] as String? ?? '',
    );
  }
}
