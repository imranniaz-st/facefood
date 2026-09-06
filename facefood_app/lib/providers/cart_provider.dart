import 'package:flutter/foundation.dart';

import '../models/cart_item.dart';
import '../models/product_model.dart';
import '../models/store_settings.dart';

class CartProvider extends ChangeNotifier {
  final List<CartItem> _items = [];
  QuoteModel? serverQuote;
  int _lineSeq = 0;

  List<CartItem> get items => List.unmodifiable(_items);

  int get itemCount => _items.fold(0, (sum, i) => sum + i.quantity);

  double get subtotal => _items.fold(0.0, (sum, i) => sum + i.lineTotal);

  double deliveryFeeOf(StoreSettings s) => _items.isEmpty ? 0 : s.deliveryFee;

  double taxOf(StoreSettings s) {
    var tax = 0.0;
    for (final item in _items) {
      final rate = item.product.taxRate ?? s.taxRate;
      tax += item.lineTotal * rate;
    }
    return double.parse(tax.toStringAsFixed(2));
  }

  double totalOf(StoreSettings s) =>
      double.parse((subtotal + deliveryFeeOf(s) + taxOf(s)).toStringAsFixed(2));

  void setServerQuote(QuoteModel? quote) {
    serverQuote = quote;
    notifyListeners();
  }

  double displaySubtotal(StoreSettings s) => serverQuote?.subtotal ?? subtotal;

  double displayDeliveryFee(StoreSettings s) => serverQuote?.deliveryFee ?? deliveryFeeOf(s);

  double displayTax(StoreSettings s) => serverQuote?.tax ?? taxOf(s);

  double displayTotal(StoreSettings s) => serverQuote?.total ?? totalOf(s);

  String displayTaxLabel(StoreSettings s) => serverQuote?.taxLabel ?? s.taxLabel;

  double displayTaxPercent(StoreSettings s) => serverQuote?.taxPercent ?? s.taxPercent;

  void add(
    ProductModel product, {
    int qty = 1,
    List<ProductExtra> extras = const [],
    int? dealId,
    String? dealTitle,
    double? dealPrice,
  }) {
    final extraIds = extras.map((e) => e.id).where((id) => id > 0).toList()..sort();
    final existing = _items.where((item) {
      if (item.product.id != product.id || item.dealId != dealId) return false;
      return listEquals(item.extraIds, extraIds);
    }).toList();

    if (existing.isNotEmpty) {
      existing.first.quantity += qty;
    } else {
      _lineSeq++;
      _items.add(
        CartItem(
          lineId: 'line-$_lineSeq',
          product: product,
          quantity: qty,
          extras: List.unmodifiable(extras),
          dealId: dealId,
          dealTitle: dealTitle,
          dealPrice: dealPrice,
        ),
      );
    }
    serverQuote = null;
    notifyListeners();
  }

  void increment(String lineId) {
    final item = _items.firstWhere((i) => i.lineId == lineId);
    item.quantity++;
    serverQuote = null;
    notifyListeners();
  }

  void decrement(String lineId) {
    final item = _items.firstWhere((i) => i.lineId == lineId);
    if (item.quantity <= 1) {
      _items.removeWhere((i) => i.lineId == lineId);
    } else {
      item.quantity--;
    }
    serverQuote = null;
    notifyListeners();
  }

  void remove(String lineId) {
    _items.removeWhere((i) => i.lineId == lineId);
    serverQuote = null;
    notifyListeners();
  }

  void clear() {
    _items.clear();
    serverQuote = null;
    notifyListeners();
  }

  List<Map<String, dynamic>> toOrderPayload() {
    return _items
        .map(
          (i) => {
            'product_id': i.product.id,
            'quantity': i.quantity,
            if (i.dealId != null) 'deal_id': i.dealId,
            if (i.extraIds.isNotEmpty) 'extra_ids': i.extraIds,
          },
        )
        .toList();
  }
}
