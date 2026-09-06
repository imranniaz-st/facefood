class ProductExtra {
  final int id;
  final String name;
  final double price;

  ProductExtra({
    required this.id,
    required this.name,
    required this.price,
  });

  factory ProductExtra.fromJson(Map<String, dynamic> json) {
    return ProductExtra(
      id: (json['id'] as num?)?.toInt() ?? 0,
      name: json['name'] as String? ?? '',
      price: (json['price'] as num?)?.toDouble() ?? 0,
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
        'price': price,
      };
}

class ProductModel {
  final int id;
  final int categoryId;
  final String name;
  final String? description;
  final String? ingredients;
  final double price;
  final double? taxRate;
  final String? imageUrl;
  final double rating;
  final int? calories;
  final String? spiceLevel;
  final int? prepTimeMinutes;
  final List<ProductExtra> extras;
  final bool isPopular;
  final bool isAvailable;

  ProductModel({
    required this.id,
    required this.categoryId,
    required this.name,
    this.description,
    this.ingredients,
    required this.price,
    this.taxRate,
    this.imageUrl,
    required this.rating,
    this.calories,
    this.spiceLevel,
    this.prepTimeMinutes,
    this.extras = const [],
    this.isPopular = false,
    this.isAvailable = true,
  });

  factory ProductModel.fromJson(Map<String, dynamic> json) {
    final extrasJson = json['extras'];
    return ProductModel(
      id: json['id'] as int,
      categoryId: json['category_id'] as int? ?? 0,
      name: json['name'] as String,
      description: json['description'] as String?,
      ingredients: json['ingredients'] as String?,
      price: (json['price'] as num).toDouble(),
      taxRate: (json['tax_rate'] as num?)?.toDouble(),
      imageUrl: json['image_url'] as String?,
      rating: (json['rating'] as num?)?.toDouble() ?? 4.5,
      calories: (json['calories'] as num?)?.toInt(),
      spiceLevel: json['spice_level'] as String?,
      prepTimeMinutes: (json['prep_time_minutes'] as num?)?.toInt(),
      extras: extrasJson is List
          ? extrasJson.whereType<Map<String, dynamic>>().map(ProductExtra.fromJson).toList()
          : const [],
      isPopular: json['is_popular'] == true,
      isAvailable: json['is_available'] != false,
    );
  }
}
