import '../models/address_model.dart';
import '../models/category_model.dart';
import '../models/deal_model.dart';
import '../models/order_model.dart';
import '../models/product_model.dart';
import '../models/store_settings.dart';
import '../models/user_model.dart';
import 'api_client.dart';

class SettingsService {
  final ApiClient api;

  SettingsService(this.api);

  Future<StoreSettings> fetch() async {
    final data = await api.get('/settings') as Map<String, dynamic>;
    final json = data.containsKey('data') ? data['data'] as Map<String, dynamic> : data;
    return StoreSettings.fromJson(json);
  }
}

class AuthService {
  final ApiClient api;

  AuthService(this.api);

  Future<({UserModel user, String token})> login(String email, String password) async {
    final data = await api.post('/login', body: {
      'email': email,
      'password': password,
    }) as Map<String, dynamic>;

    final token = data['token'] as String?;
    if (token == null || token.isEmpty) {
      throw ApiException("Couldn't log in. Please try again.");
    }
    api.setToken(token);
    return (user: _parseUser(data), token: token);
  }

  Future<({UserModel user, String token})> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
    String? phone,
  }) async {
    final data = await api.post('/register', body: {
      'name': name,
      'email': email,
      'password': password,
      'password_confirmation': passwordConfirmation,
      if (phone != null && phone.isNotEmpty) 'phone': phone,
    }) as Map<String, dynamic>;

    final token = data['token'] as String?;
    if (token == null || token.isEmpty) {
      throw ApiException("Couldn't create your account. Please try again.");
    }
    api.setToken(token);
    return (user: _parseUser(data), token: token);
  }

  UserModel _parseUser(Map<String, dynamic> data) {
    dynamic user = data['user'];
    if (user is Map && user['data'] is Map) {
      user = user['data'];
    }
    if (user is! Map) {
      throw ApiException("Couldn't complete sign-in. Please try again.");
    }
    return UserModel.fromJson(Map<String, dynamic>.from(user));
  }

  Future<UserModel> me() async {
    final data = await api.get('/user') as Map<String, dynamic>;
    final userJson = data.containsKey('data') ? data['data'] as Map<String, dynamic> : data;
    return UserModel.fromJson(userJson);
  }

  Future<UserModel> updateProfile(Map<String, dynamic> body) async {
    final data = await api.put('/user', body: body) as Map<String, dynamic>;
    final userJson = data.containsKey('data') ? data['data'] as Map<String, dynamic> : data;
    return UserModel.fromJson(userJson);
  }

  Future<void> logout() async {
    try {
      await api.post('/logout');
    } catch (_) {}
    api.setToken(null);
  }
}

class CatalogService {
  final ApiClient api;

  CatalogService(this.api);

  List<Map<String, dynamic>> _list(dynamic data) {
    if (data is Map && data['data'] is List) {
      return (data['data'] as List).cast<Map<String, dynamic>>();
    }
    if (data is List) return data.cast<Map<String, dynamic>>();
    return [];
  }

  Future<List<CategoryModel>> categories() async {
    final data = await api.get('/categories');
    return _list(data).map(CategoryModel.fromJson).toList();
  }

  Future<List<ProductModel>> products({
    int? categoryId,
    String? search,
    bool popular = false,
  }) async {
    final query = <String, String>{};
    if (categoryId != null) query['category_id'] = '$categoryId';
    if (search != null && search.isNotEmpty) query['search'] = search;
    if (popular) query['popular'] = '1';
    final data = await api.get('/products', query: query.isEmpty ? null : query);
    return _list(data).map(ProductModel.fromJson).toList();
  }

  Future<ProductModel> product(int id) async {
    final data = await api.get('/products/$id') as Map<String, dynamic>;
    final json = data.containsKey('data') ? data['data'] as Map<String, dynamic> : data;
    return ProductModel.fromJson(json);
  }

  Future<List<DealModel>> deals() async {
    final data = await api.get('/deals');
    return _list(data).map(DealModel.fromJson).toList();
  }
}

class OrderService {
  final ApiClient api;

  OrderService(this.api);

  List<Map<String, dynamic>> _list(dynamic data) {
    if (data is Map && data['data'] is List) {
      return (data['data'] as List).cast<Map<String, dynamic>>();
    }
    if (data is List) return data.cast<Map<String, dynamic>>();
    return [];
  }

  Future<List<AddressModel>> addresses() async {
    final data = await api.get('/addresses');
    return _list(data).map(AddressModel.fromJson).toList();
  }

  Future<AddressModel> createAddress(Map<String, dynamic> body) async {
    final data = await api.post('/addresses', body: body) as Map<String, dynamic>;
    final json = data.containsKey('data') ? data['data'] as Map<String, dynamic> : data;
    return AddressModel.fromJson(json);
  }

  Future<AddressModel> updateAddress(int id, Map<String, dynamic> body) async {
    final data = await api.put('/addresses/$id', body: body) as Map<String, dynamic>;
    final json = data.containsKey('data') ? data['data'] as Map<String, dynamic> : data;
    return AddressModel.fromJson(json);
  }

  Future<void> deleteAddress(int id) async {
    await api.delete('/addresses/$id');
  }

  Future<List<OrderModel>> orders() async {
    final data = await api.get('/orders');
    return _list(data).map(OrderModel.fromJson).toList();
  }

  Future<OrderModel> order(int id) async {
    final data = await api.get('/orders/$id') as Map<String, dynamic>;
    final json = data.containsKey('data') ? data['data'] as Map<String, dynamic> : data;
    return OrderModel.fromJson(json);
  }

  Future<QuoteModel> quote(List<Map<String, dynamic>> items) async {
    final data = await api.post('/orders/quote', body: {'items': items}) as Map<String, dynamic>;
    final json = data.containsKey('data') ? data['data'] as Map<String, dynamic> : data;
    return QuoteModel.fromJson(json);
  }

  Future<OrderModel> placeOrder({
    required List<Map<String, dynamic>> items,
    required String paymentMethod,
    int? addressId,
    String? deliveryAddress,
    String? note,
  }) async {
    final data = await api.post('/orders', body: {
      'items': items,
      'payment_method': paymentMethod,
      if (addressId != null) 'address_id': addressId,
      if (deliveryAddress != null) 'delivery_address': deliveryAddress,
      if (note != null && note.isNotEmpty) 'note': note,
    }) as Map<String, dynamic>;
    final json = data.containsKey('data') ? data['data'] as Map<String, dynamic> : data;
    return OrderModel.fromJson(json);
  }
}

class FavoriteService {
  final ApiClient api;

  FavoriteService(this.api);

  List<Map<String, dynamic>> _list(dynamic data) {
    if (data is Map && data['data'] is List) {
      return (data['data'] as List).cast<Map<String, dynamic>>();
    }
    if (data is List) return data.cast<Map<String, dynamic>>();
    return [];
  }

  Future<List<ProductModel>> list() async {
    final data = await api.get('/favorites');
    return _list(data).map(ProductModel.fromJson).toList();
  }

  Future<bool> toggle(int productId) async {
    final data = await api.post('/favorites/toggle', body: {
      'product_id': productId,
    }) as Map<String, dynamic>;
    return data['favorited'] == true;
  }

  Future<void> remove(int productId) async {
    await api.delete('/favorites/$productId');
  }
}
