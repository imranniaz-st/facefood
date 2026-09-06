import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/cart_provider.dart';
import '../providers/catalog_provider.dart';
import '../theme/app_theme.dart';
import '../utils/favorite_actions.dart';
import '../widgets/food_card.dart';
import '../widgets/friendly_error.dart';

class MenuScreen extends StatefulWidget {
  const MenuScreen({super.key});

  @override
  State<MenuScreen> createState() => _MenuScreenState();
}

class _MenuScreenState extends State<MenuScreen> {
  int? _selectedCategoryId;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<CatalogProvider>().loadProducts();
    });
  }

  Future<void> _filter(int? categoryId) async {
    setState(() => _selectedCategoryId = categoryId);
    await context.read<CatalogProvider>().loadProducts(categoryId: categoryId);
  }

  @override
  Widget build(BuildContext context) {
    final catalog = context.watch<CatalogProvider>();
    final cart = context.read<CartProvider>();
    final items = catalog.products;

    return SafeArea(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Padding(
            padding: EdgeInsets.fromLTRB(20, 16, 20, 4),
            child: Text('Our Menu', style: TextStyle(fontSize: 24, fontWeight: FontWeight.w800)),
          ),
          const Padding(
            padding: EdgeInsets.symmetric(horizontal: 20),
            child: Text(
              'Discover our irresistible fast food made with top-tier quality ingredients.',
              style: TextStyle(color: AppColors.muted, fontSize: 13),
            ),
          ),
          const SizedBox(height: 14),
          SizedBox(
            height: 42,
            child: ListView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              children: [
                _chip('All', _selectedCategoryId == null, () => _filter(null)),
                ...catalog.categories.map(
                  (c) => _chip(c.name, _selectedCategoryId == c.id, () => _filter(c.id)),
                ),
              ],
            ),
          ),
          const SizedBox(height: 12),
          Expanded(
            child: catalog.loading && items.isEmpty
                ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
                : catalog.error != null && items.isEmpty
                    ? Center(
                        child: FriendlyError(
                          message: catalog.error ?? "Couldn't load menu. Try again.",
                          onRetry: () => _filter(_selectedCategoryId),
                        ),
                      )
                    : items.isEmpty
                        ? const Center(
                            child: Text('No items in this category.', style: TextStyle(color: AppColors.muted)),
                          )
                        : RefreshIndicator(
                            color: AppColors.primary,
                            onRefresh: () => _filter(_selectedCategoryId),
                            child: ListView.builder(
                              padding: const EdgeInsets.fromLTRB(16, 0, 16, 24),
                              itemCount: items.length,
                              itemBuilder: (_, i) {
                                final p = items[i];
                                return FoodCard(
                                  product: p,
                                  favorited: catalog.isFavorite(p.id),
                                  onFavorite: () => toggleProductFavorite(context, p.id),
                                  onAdd: () {
                                    cart.add(p);
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(content: Text('${p.name} added'), duration: const Duration(seconds: 1)),
                                    );
                                  },
                                  onTap: () => Navigator.of(context).pushNamed('/product', arguments: p.id),
                                );
                              },
                            ),
                          ),
          ),
        ],
      ),
    );
  }

  Widget _chip(String label, bool selected, VoidCallback onTap) {
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: ChoiceChip(
        label: Text(label),
        selected: selected,
        onSelected: (_) => onTap(),
        selectedColor: AppColors.primary,
        labelStyle: TextStyle(
          color: selected ? Colors.white : AppColors.text,
          fontWeight: FontWeight.w600,
          fontSize: 13,
        ),
        backgroundColor: AppColors.surface,
        showCheckmark: false,
        padding: const EdgeInsets.symmetric(horizontal: 6),
      ),
    );
  }
}
