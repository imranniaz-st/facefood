import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../models/product_model.dart';
import '../providers/cart_provider.dart';
import '../providers/catalog_provider.dart';
import '../providers/settings_provider.dart';
import '../services/api_client.dart';
import '../services/api_services.dart';
import '../theme/app_theme.dart';
import '../utils/favorite_actions.dart';
import '../utils/format.dart';
import '../utils/user_error.dart';

class ProductDetailScreen extends StatefulWidget {
  const ProductDetailScreen({super.key});

  @override
  State<ProductDetailScreen> createState() => _ProductDetailScreenState();
}

class _ProductDetailScreenState extends State<ProductDetailScreen> {
  int _qty = 1;
  bool _loading = true;
  String? _error;
  ProductModel? _product;
  bool _started = false;
  bool _togglingFav = false;
  final Set<int> _selectedExtraIds = {};

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (_started) return;
    _started = true;
    final id = ModalRoute.of(context)!.settings.arguments as int;
    _load(id);
  }

  Future<void> _load(int id) async {
    try {
      final catalog = context.read<CatalogProvider>();
      final local = [...catalog.popularProducts, ...catalog.products, ...catalog.favoriteProducts]
          .where((p) => p.id == id);
      if (local.isNotEmpty && mounted) {
        setState(() {
          _product = local.first;
          _loading = false;
        });
      }
      final api = context.read<ApiClient>();
      final p = await CatalogService(api).product(id);
      if (!mounted) return;
      setState(() {
        _product = p;
        _loading = false;
        _error = null;
      });
    } catch (e) {
      if (!mounted) return;
      if (_product != null) return;
      setState(() {
        _error = userFacingError(e, fallback: "Couldn't load this item. Try again.");
        _loading = false;
      });
    }
  }

  Future<void> _toggleFavorite() async {
    final product = _product;
    if (product == null || _togglingFav) return;
    setState(() => _togglingFav = true);
    try {
      await toggleProductFavorite(context, product.id);
    } finally {
      if (mounted) setState(() => _togglingFav = false);
    }
  }

  List<ProductExtra> get _selectedExtras {
    final product = _product;
    if (product == null) return const [];
    return product.extras.where((e) => _selectedExtraIds.contains(e.id)).toList();
  }

  double get _extrasTotal => _selectedExtras.fold(0.0, (sum, extra) => sum + extra.price);

  @override
  Widget build(BuildContext context) {
    if (_loading) {
      return const Scaffold(body: Center(child: CircularProgressIndicator(color: AppColors.primary)));
    }
    if (_error != null || _product == null) {
      return Scaffold(
        appBar: AppBar(),
        body: Center(
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Text(_error ?? "We couldn't find that item.", textAlign: TextAlign.center),
          ),
        ),
      );
    }

    final p = _product!;
    final favorited = context.watch<CatalogProvider>().isFavorite(p.id);
    final currency = context.watch<SettingsProvider>().settings.currency;
    final lineTotal = (p.price + _extrasTotal) * _qty;

    return Scaffold(
      body: CustomScrollView(
        slivers: [
          SliverAppBar(
            expandedHeight: 260,
            pinned: true,
            actions: [
              IconButton(
                tooltip: favorited ? 'Remove from favorites' : 'Add to favorites',
                onPressed: _togglingFav ? null : _toggleFavorite,
                icon: Icon(
                  favorited ? Icons.favorite : Icons.favorite_border,
                  color: favorited ? AppColors.popularRed : null,
                ),
              ),
            ],
            flexibleSpace: FlexibleSpaceBar(
              background: p.imageUrl != null
                  ? CachedNetworkImage(imageUrl: p.imageUrl!, fit: BoxFit.cover)
                  : Container(color: AppColors.surface),
            ),
          ),
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (p.isPopular)
                    Container(
                      margin: const EdgeInsets.only(bottom: 8),
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                      decoration: BoxDecoration(
                        color: AppColors.popularRed,
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: const Text(
                        'POPULAR',
                        style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.w700),
                      ),
                    ),
                  Text(p.name, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w800)),
                  const SizedBox(height: 6),
                  Row(
                    children: [
                      const Icon(Icons.star_rounded, color: AppColors.star),
                      Text(' ${p.rating}', style: const TextStyle(fontWeight: FontWeight.w600)),
                      const Spacer(),
                      Text(
                        formatMoney(p.price, currency),
                        style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w800, color: AppColors.primary),
                      ),
                    ],
                  ),
                  const SizedBox(height: 14),
                  Text(p.description ?? '', style: const TextStyle(color: AppColors.muted, height: 1.45)),
                  if (p.ingredients != null && p.ingredients!.isNotEmpty) ...[
                    const SizedBox(height: 12),
                    const Text('Ingredients', style: TextStyle(fontWeight: FontWeight.w700)),
                    const SizedBox(height: 4),
                    Text(p.ingredients!, style: const TextStyle(color: AppColors.muted, height: 1.4)),
                  ],
                  if (p.calories != null || p.spiceLevel != null || p.prepTimeMinutes != null) ...[
                    const SizedBox(height: 12),
                    Wrap(
                      spacing: 8,
                      runSpacing: 8,
                      children: [
                        if (p.calories != null) _chip('${p.calories} kcal'),
                        if (p.spiceLevel != null) _chip(p.spiceLevel!),
                        if (p.prepTimeMinutes != null) _chip('${p.prepTimeMinutes} min'),
                      ],
                    ),
                  ],
                  if (p.extras.isNotEmpty) ...[
                    const SizedBox(height: 22),
                    const Text('Extra Toppings', style: TextStyle(fontWeight: FontWeight.w800, fontSize: 18)),
                    const SizedBox(height: 4),
                    const Text(
                      'Choose extras — each one has its own price.',
                      style: TextStyle(color: AppColors.muted, fontSize: 13),
                    ),
                    const SizedBox(height: 10),
                    ...p.extras.map((extra) {
                      final selected = _selectedExtraIds.contains(extra.id);
                      return Padding(
                        padding: const EdgeInsets.only(bottom: 8),
                        child: Material(
                          color: selected ? AppColors.primarySoft : AppColors.surface,
                          borderRadius: BorderRadius.circular(14),
                          child: InkWell(
                            borderRadius: BorderRadius.circular(14),
                            onTap: extra.id <= 0
                                ? null
                                : () {
                                    setState(() {
                                      if (selected) {
                                        _selectedExtraIds.remove(extra.id);
                                      } else {
                                        _selectedExtraIds.add(extra.id);
                                      }
                                    });
                                  },
                            child: Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
                              child: Row(
                                children: [
                                  Icon(
                                    selected ? Icons.check_box : Icons.check_box_outline_blank,
                                    color: selected ? AppColors.primary : AppColors.muted,
                                  ),
                                  const SizedBox(width: 10),
                                  Expanded(
                                    child: Text(
                                      extra.name,
                                      style: const TextStyle(fontWeight: FontWeight.w600),
                                    ),
                                  ),
                                  Text(
                                    '+ ${formatMoney(extra.price, currency)}',
                                    style: TextStyle(
                                      fontWeight: FontWeight.w700,
                                      color: selected ? AppColors.primary : AppColors.muted,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      );
                    }),
                  ],
                  const SizedBox(height: 24),
                  Row(
                    children: [
                      const Text('Quantity', style: TextStyle(fontWeight: FontWeight.w600)),
                      const Spacer(),
                      IconButton(
                        onPressed: _qty > 1 ? () => setState(() => _qty--) : null,
                        icon: const Icon(Icons.remove_circle_outline),
                      ),
                      Text('$_qty', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                      IconButton(
                        onPressed: () => setState(() => _qty++),
                        icon: const Icon(Icons.add_circle_outline, color: AppColors.primary),
                      ),
                    ],
                  ),
                  const SizedBox(height: 20),
                  ElevatedButton(
                    onPressed: () {
                      context.read<CartProvider>().add(
                            p,
                            qty: _qty,
                            extras: _selectedExtras,
                          );
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(content: Text('${p.name} added to cart')),
                      );
                      Navigator.pop(context);
                    },
                    child: Text('Add to Cart — ${formatMoney(lineTotal, currency)}'),
                  ),
                  const SizedBox(height: 12),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _chip(String label) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: AppColors.primarySoft,
        borderRadius: BorderRadius.circular(20),
      ),
      child: Text(label, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.primary)),
    );
  }
}
