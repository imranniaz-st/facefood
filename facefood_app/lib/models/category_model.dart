class CategoryModel {
  final int id;
  final String name;
  final String slug;
  final String? icon;
  final String? imageUrl;
  final int? productsCount;

  CategoryModel({
    required this.id,
    required this.name,
    required this.slug,
    this.icon,
    this.imageUrl,
    this.productsCount,
  });

  factory CategoryModel.fromJson(Map<String, dynamic> json) {
    return CategoryModel(
      id: json['id'] as int,
      name: json['name'] as String,
      slug: json['slug'] as String,
      icon: json['icon'] as String?,
      imageUrl: json['image_url'] as String?,
      productsCount: json['products_count'] as int?,
    );
  }
}
