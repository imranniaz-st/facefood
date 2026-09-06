import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/auth_provider.dart';
import '../providers/cart_provider.dart';
import '../providers/catalog_provider.dart';
import '../theme/app_theme.dart';
import '../utils/favorite_actions.dart';
import '../widgets/food_card.dart';
import '../widgets/friendly_error.dart';

class FavoritesScreen extends StatefulWidget {
  const FavoritesScreen({super.key});

  @override
  State<FavoritesScreen> createState() => _FavoritesScreenState();
}

class _FavoritesScreenState extends State<FavoritesScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final auth = context.read<AuthProvider>();
      if (auth.isAuthenticated) {
        context.read<CatalogProvider>().loadFavorites();
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final catalog = context.watch<CatalogProvider>();
    final cart = context.read<CartProvider>();

    return Scaffold(
      appBar: AppBar(title: const Text('Favorite Items')),
      body: !auth.isAuthenticated
          ? Center(
              child: Padding(
                padding: const EdgeInsets.all(24),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Icon(Icons.favorite_border, size: 56, color: AppColors.muted),
                    const SizedBox(height: 12),
                    const Text('Save your favorite meals', style: TextStyle(fontWeight: FontWeight.w600)),
                    const SizedBox(height: 6),
                    const Text(
                      'Log in to tap the heart on a meal and find it here.',
                      style: TextStyle(color: AppColors.muted),
                      textAlign: TextAlign.center,
                    ),
                    const SizedBox(height: 16),
                    ElevatedButton(
                      onPressed: () => Navigator.of(context).pushNamed('/login'),
                      child: const Text('Log in'),
                    ),
                  ],
                ),
              ),
            )
          : catalog.loading && catalog.favoriteProducts.isEmpty
              ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
              : catalog.error != null && catalog.favoriteProducts.isEmpty
                  ? Center(
                      child: FriendlyError(
                        message: catalog.error ?? "Couldn't load favorites. Try again.",
                        onRetry: () => catalog.loadFavorites(),
                      ),
                    )
                  : catalog.favoriteProducts.isEmpty
                      ? const Center(
                          child: Column(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(Icons.favorite_border, size: 56, color: AppColors.muted),
                              SizedBox(height: 12),
                              Text('No favorites yet', style: TextStyle(fontWeight: FontWeight.w600)),
                              SizedBox(height: 6),
                              Text(
                                'Tap the heart on a meal to save it here.',
                                style: TextStyle(color: AppColors.muted),
                                textAlign: TextAlign.center,
                              ),
                            ],
                          ),
                        )
                      : RefreshIndicator(
                          color: AppColors.primary,
                          onRefresh: () => catalog.loadFavorites(),
                          child: ListView.builder(
                            padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
                            itemCount: catalog.favoriteProducts.length,
                            itemBuilder: (_, i) {
                              final p = catalog.favoriteProducts[i];
                              return FoodCard(
                                product: p,
                                favorited: true,
                                onFavorite: () => toggleProductFavorite(context, p.id),
                                onAdd: () {
                                  cart.add(p);
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    SnackBar(
                                      content: Text('${p.name} added'),
                                      duration: const Duration(seconds: 1),
                                    ),
                                  );
                                },
                                onTap: () => Navigator.of(context).pushNamed('/product', arguments: p.id),
                              );
                            },
                          ),
                        ),
    );
  }
}
