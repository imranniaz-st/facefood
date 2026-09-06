import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/cart_provider.dart';
import '../providers/catalog_provider.dart';
import '../theme/app_theme.dart';
import '../utils/favorite_actions.dart';
import '../widgets/category_icon.dart';
import '../widgets/food_card.dart';
import '../widgets/friendly_error.dart';

class HomeScreen extends StatefulWidget {
  final VoidCallback onOpenMenu;
  final VoidCallback onOpenCart;
  final VoidCallback onOpenDeals;
  final VoidCallback onOpenDrawer;

  const HomeScreen({
    super.key,
    required this.onOpenMenu,
    required this.onOpenCart,
    required this.onOpenDeals,
    required this.onOpenDrawer,
  });

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  final _search = TextEditingController();

  @override
  void dispose() {
    _search.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final catalog = context.watch<CatalogProvider>();
    final cart = context.watch<CartProvider>();

    return SafeArea(
      child: RefreshIndicator(
        color: AppColors.primary,
        onRefresh: () => catalog.loadHome(),
        child: CustomScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          slivers: [
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(16, 8, 16, 0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                          IconButton(
                          tooltip: 'Open menu',
                          onPressed: widget.onOpenDrawer,
                          icon: const Icon(Icons.menu_rounded),
                        ),
                        const Expanded(
                          child: Text(
                            'Facefood',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                              fontSize: 22,
                              fontWeight: FontWeight.w800,
                              color: AppColors.primary,
                              letterSpacing: -0.5,
                            ),
                          ),
                        ),
                        Stack(
                          clipBehavior: Clip.none,
                          children: [
                            IconButton(
                              onPressed: widget.onOpenCart,
                              icon: const Icon(Icons.shopping_bag_outlined),
                            ),
                            if (cart.itemCount > 0)
                              Positioned(
                                right: 8,
                                top: 8,
                                child: Container(
                                  padding: const EdgeInsets.all(4),
                                  decoration: const BoxDecoration(
                                    color: AppColors.badgeRed,
                                    shape: BoxShape.circle,
                                  ),
                                  child: Text(
                                    '${cart.itemCount}',
                                    style: const TextStyle(color: Colors.white, fontSize: 10),
                                  ),
                                ),
                              ),
                          ],
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    TextField(
                      controller: _search,
                      onSubmitted: (q) {
                        catalog.loadProducts(search: q);
                        widget.onOpenMenu();
                      },
                      decoration: InputDecoration(
                        hintText: 'What are you craving?',
                        prefixIcon: const Icon(Icons.search, color: AppColors.muted),
                        filled: true,
                        fillColor: AppColors.surface,
                      ),
                    ),
                    const SizedBox(height: 18),
                    _SpecialPromo(onOrder: widget.onOpenDeals),
                    const SizedBox(height: 22),
                    const Text(
                      'Shop By Category',
                      style: TextStyle(fontSize: 17, fontWeight: FontWeight.w700),
                    ),
                    const SizedBox(height: 12),
                    SizedBox(
                      height: 100,
                      child: ListView.separated(
                        scrollDirection: Axis.horizontal,
                        itemCount: catalog.categories.length,
                        separatorBuilder: (_, __) => const SizedBox(width: 8),
                        itemBuilder: (_, i) {
                          final c = catalog.categories[i];
                          return CategoryIcon(
                            category: c,
                            onTap: () {
                              catalog.loadProducts(categoryId: c.id);
                              widget.onOpenMenu();
                            },
                          );
                        },
                      ),
                    ),
                    const SizedBox(height: 8),
                    Row(
                      children: [
                        const Expanded(
                          child: Text(
                            'Our Most Popular Deals',
                            style: TextStyle(fontSize: 17, fontWeight: FontWeight.w700),
                          ),
                        ),
                        TextButton(
                          onPressed: widget.onOpenMenu,
                          child: const Text('See all'),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
            if (catalog.loading && catalog.popularProducts.isEmpty)
              const SliverFillRemaining(
                child: Center(child: CircularProgressIndicator(color: AppColors.primary)),
              )
            else if (catalog.error != null && catalog.popularProducts.isEmpty)
              SliverFillRemaining(
                child: Center(
                  child: FriendlyError(
                    message: catalog.error ?? "Couldn't load menu. Try again.",
                    onRetry: () => catalog.loadHome(),
                  ),
                ),
              )
            else
              SliverPadding(
                padding: const EdgeInsets.fromLTRB(16, 0, 16, 24),
                sliver: SliverList(
                  delegate: SliverChildBuilderDelegate(
                    (context, i) {
                      final p = catalog.popularProducts[i];
                      return FoodCard(
                        product: p,
                        favorited: catalog.isFavorite(p.id),
                        onFavorite: () => toggleProductFavorite(context, p.id),
                        onAdd: () {
                          cart.add(p);
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(
                              content: Text('${p.name} added to cart'),
                              duration: const Duration(seconds: 1),
                            ),
                          );
                        },
                        onTap: () => Navigator.of(context).pushNamed('/product', arguments: p.id),
                      );
                    },
                    childCount: catalog.popularProducts.length,
                  ),
                ),
              ),
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(16, 0, 16, 32),
                child: _AppPromoBanner(onExplore: widget.onOpenMenu),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _SpecialPromo extends StatelessWidget {
  final VoidCallback onOrder;
  const _SpecialPromo({required this.onOrder});

  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(18),
      child: SizedBox(
        height: 160,
        child: Stack(
          fit: StackFit.expand,
          children: [
            CachedNetworkImage(
              imageUrl: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=1000&q=80',
              fit: BoxFit.cover,
            ),
            Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.centerLeft,
                  end: Alignment.centerRight,
                  colors: [
                    Colors.black.withValues(alpha: 0.75),
                    Colors.black.withValues(alpha: 0.25),
                  ],
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(18),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                    decoration: BoxDecoration(
                      color: AppColors.primary,
                      borderRadius: BorderRadius.circular(6),
                    ),
                    child: const Text(
                      'SPECIAL DEAL',
                      style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.w700),
                    ),
                  ),
                  const Spacer(),
                  const Text(
                    'Taste The Best Fast Food\nIn Town Today!',
                    style: TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.w700,
                      fontSize: 16,
                      height: 1.25,
                    ),
                  ),
                  const SizedBox(height: 10),
                  ElevatedButton(
                    onPressed: onOrder,
                    style: ElevatedButton.styleFrom(
                      minimumSize: const Size(120, 40),
                      padding: const EdgeInsets.symmetric(horizontal: 18),
                    ),
                    child: const Text('Order Now', style: TextStyle(fontSize: 13)),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _AppPromoBanner extends StatelessWidget {
  final VoidCallback onExplore;
  const _AppPromoBanner({required this.onExplore});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: AppColors.primary,
        borderRadius: BorderRadius.circular(18),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Get the Facefood App',
            style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700, fontSize: 18),
          ),
          const SizedBox(height: 6),
          Text(
            'Limited time deals, faster checkout & exclusive combos.',
            style: TextStyle(color: Colors.white.withValues(alpha: 0.9), fontSize: 13),
          ),
          const SizedBox(height: 14),
          Row(
            children: [
              Expanded(
                child: OutlinedButton(
                  onPressed: onExplore,
                  style: OutlinedButton.styleFrom(
                    foregroundColor: Colors.white,
                    side: const BorderSide(color: Colors.white),
                    minimumSize: const Size.fromHeight(42),
                  ),
                  child: const Text('Explore Menu'),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: ElevatedButton(
                  onPressed: onExplore,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.white,
                    foregroundColor: AppColors.primary,
                    minimumSize: const Size.fromHeight(42),
                  ),
                  child: const Text('Mega Deal'),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Text(
            'Google Play  ·  App Store',
            style: TextStyle(color: Colors.white.withValues(alpha: 0.85), fontSize: 12),
          ),
        ],
      ),
    );
  }
}
