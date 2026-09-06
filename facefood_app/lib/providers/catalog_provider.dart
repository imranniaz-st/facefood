import 'package:flutter/foundation.dart';

import '../models/address_model.dart';
import '../models/category_model.dart';
import '../models/deal_model.dart';
import '../models/order_model.dart';
import '../models/product_model.dart';
import '../models/store_settings.dart';
import '../services/api_client.dart';
import '../services/api_services.dart';
import '../utils/user_error.dart';

class CatalogProvider extends ChangeNotifier {
  final CatalogService _catalog;
  final OrderService _orders;
  final FavoriteService _favorites;

  CatalogProvider(ApiClient api)
      : _catalog = CatalogService(api),
        _orders = OrderService(api),
        _favorites = FavoriteService(api);

  List<CategoryModel> categories = [];
  List<ProductModel> popularProducts = [];
  List<ProductModel> products = [];
  List<DealModel> deals = [];
  List<AddressModel> addresses = [];
  List<OrderModel> orderHistory = [];
  List<ProductModel> favoriteProducts = [];
  final Set<int> favoriteIds = {};

  bool loading = false;
  String? error;

  AddressModel? get defaultAddress {
    if (addresses.isEmpty) return null;
    return addresses.firstWhere((a) => a.isDefault, orElse: () => addresses.first);
  }

  Future<void> loadHome() async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      final results = await Future.wait([
        _catalog.categories(),
        _catalog.products(popular: true),
        _catalog.deals(),
      ]);
      categories = results[0] as List<CategoryModel>;
      popularProducts = results[1] as List<ProductModel>;
      deals = results[2] as List<DealModel>;
      if (popularProducts.isEmpty) {
        popularProducts = await _catalog.products();
      }
    } catch (e) {
      error = userFacingError(e, fallback: "Couldn't load menu. Try again.");
    }
    loading = false;
    notifyListeners();
  }

  Future<void> loadProducts({int? categoryId, String? search}) async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      products = await _catalog.products(categoryId: categoryId, search: search);
    } catch (e) {
      error = userFacingError(e, fallback: "Couldn't load menu. Try again.");
    }
    loading = false;
    notifyListeners();
  }

  Future<void> loadDeals() async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      deals = await _catalog.deals();
    } catch (e) {
      error = userFacingError(e, fallback: "Couldn't load deals. Try again.");
    }
    loading = false;
    notifyListeners();
  }

  Future<void> loadAddresses() async {
    try {
      addresses = await _orders.addresses();
      notifyListeners();
    } catch (_) {}
  }

  Future<AddressModel> createAddress(Map<String, dynamic> body) async {
    final address = await _orders.createAddress(body);
    await loadAddresses();
    return address;
  }

  Future<AddressModel> updateAddress(int id, Map<String, dynamic> body) async {
    final address = await _orders.updateAddress(id, body);
    await loadAddresses();
    return address;
  }

  Future<void> deleteAddress(int id) async {
    await _orders.deleteAddress(id);
    await loadAddresses();
  }

  Future<void> setDefaultAddress(int id) async {
    await _orders.updateAddress(id, {'is_default': true});
    await loadAddresses();
  }

  Future<void> loadOrders() async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      orderHistory = await _orders.orders();
    } catch (e) {
      error = userFacingError(e, fallback: "Couldn't load orders. Try again.");
    }
    loading = false;
    notifyListeners();
  }

  Future<OrderModel> fetchOrder(int id) => _orders.order(id);

  Future<QuoteModel> quote(List<Map<String, dynamic>> items) => _orders.quote(items);

  Future<OrderModel> placeOrder({
    required List<Map<String, dynamic>> items,
    required String paymentMethod,
    int? addressId,
    String? deliveryAddress,
    String? note,
  }) {
    return _orders.placeOrder(
      items: items,
      paymentMethod: paymentMethod,
      addressId: addressId,
      deliveryAddress: deliveryAddress,
      note: note,
    );
  }

  Future<void> loadFavorites() async {
    loading = true;
    error = null;
    notifyListeners();
    try {
      favoriteProducts = await _favorites.list();
      favoriteIds
        ..clear()
        ..addAll(favoriteProducts.map((p) => p.id));
    } catch (e) {
      error = userFacingError(e, fallback: "Couldn't load favorites. Try again.");
    }
    loading = false;
    notifyListeners();
  }

  bool isFavorite(int productId) => favoriteIds.contains(productId);

  Future<bool> toggleFavorite(int productId) async {
    final favorited = await _favorites.toggle(productId);
    if (favorited) {
      favoriteIds.add(productId);
      if (!favoriteProducts.any((p) => p.id == productId)) {
        final fromLists = [...popularProducts, ...products].where((p) => p.id == productId);
        if (fromLists.isNotEmpty) {
          favoriteProducts = [fromLists.first, ...favoriteProducts];
        }
      }
    } else {
      favoriteIds.remove(productId);
      favoriteProducts = favoriteProducts.where((p) => p.id != productId).toList();
    }
    notifyListeners();
    return favorited;
  }

  Future<void> removeFavorite(int productId) async {
    await _favorites.remove(productId);
    favoriteIds.remove(productId);
    favoriteProducts = favoriteProducts.where((p) => p.id != productId).toList();
    notifyListeners();
  }
}
