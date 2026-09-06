import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../models/deal_model.dart';
import '../providers/cart_provider.dart';
import '../providers/catalog_provider.dart';
import '../providers/settings_provider.dart';
import '../theme/app_theme.dart';
import '../utils/format.dart';

class PromotionsScreen extends StatelessWidget {
  const PromotionsScreen({super.key});

  Future<void> _addDeal(BuildContext context, DealModel deal) async {
    final catalog = context.read<CatalogProvider>();
    final cart = context.read<CartProvider>();
    var product = deal.product;
    if (product == null) {
      final match = [...catalog.popularProducts, ...catalog.products].where((p) => p.id == deal.productId);
      if (match.isNotEmpty) product = match.first;
    }
    if (product == null) {
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
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text('${deal.title} added to cart')),
    );
  }

  @override
  Widget build(BuildContext context) {
    final deals = context.watch<CatalogProvider>().deals;
    final currency = context.watch<SettingsProvider>().settings.currency;

    return Scaffold(
      appBar: AppBar(title: const Text('Promotions & Rewards')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              color: AppColors.primary,
              borderRadius: BorderRadius.circular(16),
            ),
            child: const Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Premium Member', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w700, fontSize: 18)),
                SizedBox(height: 6),
                Text(
                  'You unlock member deals and faster checkout. Explore limited-time offers below.',
                  style: TextStyle(color: Colors.white70, fontSize: 13),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          ElevatedButton(
            onPressed: () => Navigator.of(context).pushNamed('/promo'),
            child: const Text('View Mega Deal Landing'),
          ),
          const SizedBox(height: 16),
          const Text('Active deals', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
          const SizedBox(height: 10),
          if (deals.isEmpty)
            const Padding(
              padding: EdgeInsets.all(24),
              child: Center(child: Text('No active promotions right now.', style: TextStyle(color: AppColors.muted))),
            )
          else
            ...deals.map(
              (d) => Container(
                margin: const EdgeInsets.only(bottom: 10),
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: AppColors.surface,
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.local_offer_outlined, color: AppColors.primary),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(d.title, style: const TextStyle(fontWeight: FontWeight.w600)),
                          Text('Only ${formatMoney(d.dealPrice, currency)}', style: const TextStyle(color: AppColors.muted, fontSize: 12)),
                        ],
                      ),
                    ),
                    TextButton(
                      onPressed: () => _addDeal(context, d),
                      child: const Text('Add to cart'),
                    ),
                  ],
                ),
              ),
            ),
        ],
      ),
    );
  }
}
