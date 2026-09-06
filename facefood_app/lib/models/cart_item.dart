import 'product_model.dart';

class CartItem {
  final String lineId;
  final ProductModel product;
  int quantity;
  final int? dealId;
  final String? dealTitle;
  final double? dealPrice;
  final List<ProductExtra> extras;

  CartItem({
    required this.lineId,
    required this.product,
    this.quantity = 1,
    this.dealId,
    this.dealTitle,
    this.dealPrice,
    this.extras = const [],
  });

  List<int> get extraIds => extras.map((e) => e.id).where((id) => id > 0).toList()..sort();

  double get extrasTotal => extras.fold(0.0, (sum, extra) => sum + extra.price);

  double get basePrice => dealPrice ?? product.price;

  double get unitPrice => basePrice + extrasTotal;

  double get lineTotal => unitPrice * quantity;

  String get extrasLabel => extras.map((e) => e.name).join(', ');
}
