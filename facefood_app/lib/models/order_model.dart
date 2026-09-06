import 'product_model.dart';

class OrderItemModel {
  final int id;
  final int? productId;
  final int? dealId;
  final String productName;
  final double unitPrice;
  final int quantity;
  final double lineTotal;
  final String? imageUrl;
  final List<ProductExtra> extras;

  OrderItemModel({
    required this.id,
    this.productId,
    this.dealId,
    required this.productName,
    required this.unitPrice,
    required this.quantity,
    required this.lineTotal,
    this.imageUrl,
    this.extras = const [],
  });

  factory OrderItemModel.fromJson(Map<String, dynamic> json) {
    final extrasJson = json['extras'];
    return OrderItemModel(
      id: json['id'] as int,
      productId: json['product_id'] as int?,
      dealId: json['deal_id'] as int?,
      productName: json['product_name'] as String,
      unitPrice: (json['unit_price'] as num).toDouble(),
      quantity: json['quantity'] as int,
      lineTotal: (json['line_total'] as num).toDouble(),
      imageUrl: json['image_url'] as String?,
      extras: extrasJson is List
          ? extrasJson.whereType<Map<String, dynamic>>().map(ProductExtra.fromJson).toList()
          : const [],
    );
  }
}

class OrderModel {
  final int id;
  final String orderNumber;
  final String deliveryAddress;
  final double subtotal;
  final double deliveryFee;
  final double tax;
  final double total;
  final String paymentMethod;
  final String status;
  final String verificationCode;
  final String? note;
  final int estimatedMinutes;
  final String? estimatedDelivery;
  final String? taxLabel;
  final double? taxPercent;
  final String? currency;
  final List<OrderItemModel> items;
  final DateTime? createdAt;

  OrderModel({
    required this.id,
    required this.orderNumber,
    required this.deliveryAddress,
    required this.subtotal,
    required this.deliveryFee,
    required this.tax,
    required this.total,
    required this.paymentMethod,
    required this.status,
    required this.verificationCode,
    this.note,
    required this.estimatedMinutes,
    this.estimatedDelivery,
    this.taxLabel,
    this.taxPercent,
    this.currency,
    required this.items,
    this.createdAt,
  });

  factory OrderModel.fromJson(Map<String, dynamic> json) {
    final itemsJson = json['items'] as List<dynamic>? ?? [];
    return OrderModel(
      id: json['id'] as int,
      orderNumber: json['order_number'] as String,
      deliveryAddress: json['delivery_address'] as String,
      subtotal: (json['subtotal'] as num).toDouble(),
      deliveryFee: (json['delivery_fee'] as num).toDouble(),
      tax: (json['tax'] as num).toDouble(),
      total: (json['total'] as num).toDouble(),
      paymentMethod: json['payment_method'] as String,
      status: json['status'] as String,
      verificationCode: json['verification_code'] as String,
      note: json['note'] as String?,
      estimatedMinutes: json['estimated_minutes'] as int? ?? 40,
      estimatedDelivery: json['estimated_delivery'] as String?,
      taxLabel: json['tax_label'] as String?,
      taxPercent: (json['tax_percent'] as num?)?.toDouble(),
      currency: json['currency'] as String?,
      items: itemsJson
          .map((e) => OrderItemModel.fromJson(e as Map<String, dynamic>))
          .toList(),
      createdAt: json['created_at'] != null
          ? DateTime.tryParse(json['created_at'] as String)
          : null,
    );
  }
}
