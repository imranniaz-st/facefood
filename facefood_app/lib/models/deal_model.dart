import 'product_model.dart';

class DealModel {
  final int id;
  final int productId;
  final String title;
  final String? description;
  final double dealPrice;
  final double? originalPrice;
  final String? imageUrl;
  final String? badgeColor;
  final List<String> tags;
  final DateTime endsAt;
  final int secondsRemaining;
  final ProductModel? product;

  DealModel({
    required this.id,
    required this.productId,
    required this.title,
    this.description,
    required this.dealPrice,
    this.originalPrice,
    this.imageUrl,
    this.badgeColor,
    this.tags = const [],
    required this.endsAt,
    required this.secondsRemaining,
    this.product,
  });

  factory DealModel.fromJson(Map<String, dynamic> json) {
    final tagsJson = json['tags'];
    ProductModel? product;
    final productJson = json['product'];
    if (productJson is Map<String, dynamic>) {
      product = ProductModel.fromJson(productJson);
    } else if (productJson is Map && productJson['data'] is Map) {
      product = ProductModel.fromJson(Map<String, dynamic>.from(productJson['data'] as Map));
    }

    return DealModel(
      id: json['id'] as int,
      productId: json['product_id'] as int,
      title: json['title'] as String,
      description: json['description'] as String?,
      dealPrice: (json['deal_price'] as num).toDouble(),
      originalPrice: (json['original_price'] as num?)?.toDouble(),
      imageUrl: json['image_url'] as String?,
      badgeColor: json['badge_color'] as String?,
      tags: tagsJson is List ? tagsJson.map((e) => e.toString()).toList() : const [],
      endsAt: DateTime.parse(json['ends_at'] as String),
      secondsRemaining: (json['seconds_remaining'] as num?)?.toInt() ?? 0,
      product: product,
    );
  }
}
