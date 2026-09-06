import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/auth_provider.dart';
import '../providers/cart_provider.dart';
import '../providers/catalog_provider.dart';
import '../providers/settings_provider.dart';
import '../theme/app_theme.dart';
import '../widgets/cart_item_tile.dart';
import '../widgets/order_summary.dart';

class CartScreen extends StatelessWidget {
  final VoidCallback onCheckout;

  const CartScreen({super.key, required this.onCheckout});

  @override
  Widget build(BuildContext context) {
    final cart = context.watch<CartProvider>();
    final catalog = context.watch<CatalogProvider>();
    final settings = context.watch<SettingsProvider>().settings;
    final address = catalog.defaultAddress;

    return SafeArea(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(20, 16, 20, 8),
            child: Row(
              children: [
                const Expanded(
                  child: Text('My Cart', style: TextStyle(fontSize: 24, fontWeight: FontWeight.w800)),
                ),
                Text(
                  '${cart.itemCount} Item${cart.itemCount == 1 ? '' : 's'}',
                  style: const TextStyle(color: AppColors.muted, fontWeight: FontWeight.w500),
                ),
              ],
            ),
          ),
          Expanded(
            child: cart.items.isEmpty
                ? Center(
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.shopping_bag_outlined, size: 64, color: AppColors.muted.withValues(alpha: 0.5)),
                        const SizedBox(height: 12),
                        const Text('Your cart is empty', style: TextStyle(fontWeight: FontWeight.w600)),
                        const SizedBox(height: 6),
                        const Text('Add something delicious from the menu.', style: TextStyle(color: AppColors.muted)),
                      ],
                    ),
                  )
                : ListView(
                    padding: const EdgeInsets.fromLTRB(16, 8, 16, 16),
                    children: [
                      ...cart.items.map(
                        (item) => CartItemTile(
                          item: item,
                          onIncrement: () => cart.increment(item.lineId),
                          onDecrement: () => cart.decrement(item.lineId),
                        ),
                      ),
                      const SizedBox(height: 8),
                      Container(
                        padding: const EdgeInsets.all(14),
                        decoration: BoxDecoration(
                          color: AppColors.surface,
                          borderRadius: BorderRadius.circular(14),
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.location_on, color: AppColors.primary),
                            const SizedBox(width: 10),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  const Text('Delivery Details', style: TextStyle(fontWeight: FontWeight.w600)),
                                  Text(
                                    address?.fullAddress ?? 'PWD, Islamabad',
                                    style: const TextStyle(fontSize: 12, color: AppColors.muted),
                                  ),
                                  Text(
                                    'EST. ${settings.estimatedDelivery}',
                                    style: const TextStyle(fontSize: 11, color: AppColors.primary, fontWeight: FontWeight.w600),
                                  ),
                                ],
                              ),
                            ),
                            TextButton(
                              onPressed: () async {
                                final auth = context.read<AuthProvider>();
                                if (!auth.isAuthenticated) {
                                  Navigator.of(context).pushNamed('/login');
                                  return;
                                }
                                await catalog.loadAddresses();
                                if (!context.mounted) return;
                                await Navigator.of(context).pushNamed('/addresses/pick');
                              },
                              child: const Text('Change'),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 16),
                      const Text('Order Summary', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                      const SizedBox(height: 10),
                      OrderSummary(
                        subtotal: cart.displaySubtotal(settings),
                        deliveryFee: cart.displayDeliveryFee(settings),
                        tax: cart.displayTax(settings),
                        total: cart.displayTotal(settings),
                        settings: settings,
                        taxLabel: cart.displayTaxLabel(settings),
                        taxPercent: cart.displayTaxPercent(settings),
                      ),
                    ],
                  ),
          ),
          if (cart.items.isNotEmpty)
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
              child: ElevatedButton(
                onPressed: onCheckout,
                child: const Text('Proceed to Checkout'),
              ),
            ),
        ],
      ),
    );
  }
}
