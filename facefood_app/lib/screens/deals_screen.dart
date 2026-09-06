import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../models/deal_model.dart';
import '../models/product_model.dart';
import '../providers/cart_provider.dart';
import '../providers/catalog_provider.dart';
import '../providers/settings_provider.dart';
import '../theme/app_theme.dart';
import '../utils/format.dart';
import '../widgets/countdown_timer.dart';
import '../widgets/friendly_error.dart';

class DealsScreen extends StatefulWidget {
  final VoidCallback onOpenMenu;
  final VoidCallback? onOpenCart;

  const DealsScreen({super.key, required this.onOpenMenu, this.onOpenCart});

  @override
  State<DealsScreen> createState() => _DealsScreenState();
}

class _DealsScreenState extends State<DealsScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<CatalogProvider>().loadDeals();
    });
  }

  Future<void> _addDeal(DealModel deal) async {
    final catalog = context.read<CatalogProvider>();
    final cart = context.read<CartProvider>();
    try {
      ProductModel? product = deal.product;
      if (product == null) {
        final fromLists = [...catalog.popularProducts, ...catalog.products]
            .where((p) => p.id == deal.productId);
        if (fromLists.isNotEmpty) {
          product = fromLists.first;
        } else {
          await catalog.loadProducts();
          final match = catalog.products.where((p) => p.id == deal.productId);
          if (match.isNotEmpty) product = match.first;
        }
      }
      if (product == null) {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text("Couldn't add this deal. Try again.")),
        );
        return;
      }
      cart.add(
        product,
        dealId: deal.id,
        dealTitle: deal.title,
        dealPrice: deal.dealPrice,
      );
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('${deal.title} added to cart'),
          action: widget.onOpenCart == null
              ? null
              : SnackBarAction(label: 'View cart', onPressed: widget.onOpenCart!),
        ),
      );
    } catch (_) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text("Couldn't add this deal. Try again.")),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final catalog = context.watch<CatalogProvider>();

    return SafeArea(
      child: RefreshIndicator(
        color: AppColors.primary,
        onRefresh: () => catalog.loadDeals(),
        child: ListView(
          padding: const EdgeInsets.fromLTRB(16, 16, 16, 24),
          children: [
            const Text('Special Deals', style: TextStyle(fontSize: 24, fontWeight: FontWeight.w800)),
            const SizedBox(height: 4),
            const Text('Limited-time offers — grab them before they expire.', style: TextStyle(color: AppColors.muted)),
            const SizedBox(height: 16),
            _WelcomeCard(onOrder: widget.onOpenMenu),
            const SizedBox(height: 14),
            if (catalog.loading && catalog.deals.isEmpty)
              const Padding(
                padding: EdgeInsets.all(40),
                child: Center(child: CircularProgressIndicator(color: AppColors.primary)),
              )
            else if (catalog.error != null && catalog.deals.isEmpty)
              FriendlyError(
                message: catalog.error ?? "Couldn't load deals. Try again.",
                onRetry: () => catalog.loadDeals(),
              )
            else if (catalog.deals.isEmpty)
              const Padding(
                padding: EdgeInsets.all(40),
                child: Center(child: Text('No special deals right now.', style: TextStyle(color: AppColors.muted))),
              )
            else
              ...catalog.deals.map(
                (d) => Padding(
                  padding: const EdgeInsets.only(bottom: 14),
                  child: _DealCard(deal: d, onAddToCart: () => _addDeal(d)),
                ),
              ),
          ],
        ),
      ),
    );
  }
}

class _WelcomeCard extends StatelessWidget {
  final VoidCallback onOrder;
  const _WelcomeCard({required this.onOrder});

  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(18),
      child: SizedBox(
        height: 180,
        child: Stack(
          fit: StackFit.expand,
          children: [
            CachedNetworkImage(
              imageUrl: 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=1000&q=80',
              fit: BoxFit.cover,
            ),
            Container(color: Colors.black.withValues(alpha: 0.45)),
            Padding(
              padding: const EdgeInsets.all(18),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'WELCOME FOOD',
                    style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 12, letterSpacing: 1),
                  ),
                  const SizedBox(height: 6),
                  const Text(
                    'TODAY SPECIAL FOOD',
                    style: TextStyle(color: Colors.white, fontWeight: FontWeight.w800, fontSize: 22),
                  ),
                  const Spacer(),
                  ElevatedButton(
                    onPressed: onOrder,
                    child: const Text('ORDER NOW'),
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

class _DealCard extends StatelessWidget {
  final DealModel deal;
  final VoidCallback onAddToCart;

  const _DealCard({required this.deal, required this.onAddToCart});

  Color get _badge {
    if (deal.badgeColor == null) return AppColors.primary;
    try {
      final hex = deal.badgeColor!.replaceFirst('#', '');
      return Color(int.parse('FF$hex', radix: 16));
    } catch (_) {
      return AppColors.primary;
    }
  }

  @override
  Widget build(BuildContext context) {
    final currency = context.watch<SettingsProvider>().settings.currency;
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.06),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      clipBehavior: Clip.antiAlias,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          SizedBox(
            height: 150,
            child: Stack(
              fit: StackFit.expand,
              children: [
                if (deal.imageUrl != null)
                  CachedNetworkImage(imageUrl: deal.imageUrl!, fit: BoxFit.cover)
                else
                  Container(color: _badge),
                Container(
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.bottomCenter,
                      end: Alignment.topCenter,
                      colors: [
                        Colors.black.withValues(alpha: 0.55),
                        Colors.black.withValues(alpha: 0.05),
                      ],
                    ),
                  ),
                ),
                Positioned(
                  left: 12,
                  top: 12,
                  child: CountdownTimer(endsAt: deal.endsAt),
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.fromLTRB(14, 12, 14, 14),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton.icon(
                    onPressed: onAddToCart,
                    icon: const Icon(Icons.add_shopping_cart, size: 18),
                    label: const Text('Add to cart'),
                  ),
                ),
                const SizedBox(height: 10),
                Text(
                  deal.title,
                  style: const TextStyle(fontWeight: FontWeight.w800, fontSize: 17),
                ),
                if (deal.description != null && deal.description!.isNotEmpty) ...[
                  const SizedBox(height: 4),
                  Text(
                    deal.description!,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(color: AppColors.muted, fontSize: 13),
                  ),
                ],
                const SizedBox(height: 8),
                Row(
                  children: [
                    Text(
                      formatMoney(deal.dealPrice, currency),
                      style: const TextStyle(
                        color: AppColors.primary,
                        fontWeight: FontWeight.w800,
                        fontSize: 16,
                      ),
                    ),
                    if (deal.originalPrice != null && deal.originalPrice! > deal.dealPrice) ...[
                      const SizedBox(width: 8),
                      Text(
                        formatMoney(deal.originalPrice!, currency),
                        style: const TextStyle(
                          color: AppColors.muted,
                          decoration: TextDecoration.lineThrough,
                          fontSize: 13,
                        ),
                      ),
                    ],
                  ],
                ),
                if (deal.tags.isNotEmpty) ...[
                  const SizedBox(height: 8),
                  Wrap(
                    spacing: 6,
                    children: deal.tags
                        .take(3)
                        .map(
                          (t) => Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                            decoration: BoxDecoration(
                              color: AppColors.primarySoft,
                              borderRadius: BorderRadius.circular(6),
                            ),
                            child: Text(
                              t,
                              style: const TextStyle(
                                color: AppColors.primary,
                                fontSize: 10,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ),
                        )
                        .toList(),
                  ),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }
}
