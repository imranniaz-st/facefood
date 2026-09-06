import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/catalog_provider.dart';
import '../providers/settings_provider.dart';
import '../theme/app_theme.dart';
import '../utils/format.dart';
import '../widgets/friendly_error.dart';

class OrdersScreen extends StatefulWidget {
  const OrdersScreen({super.key});

  @override
  State<OrdersScreen> createState() => _OrdersScreenState();
}

class _OrdersScreenState extends State<OrdersScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      context.read<CatalogProvider>().loadOrders();
    });
  }

  @override
  Widget build(BuildContext context) {
    final catalog = context.watch<CatalogProvider>();
    final currency = context.watch<SettingsProvider>().settings.currency;

    return Scaffold(
      appBar: AppBar(title: const Text('My Orders')),
      body: catalog.loading && catalog.orderHistory.isEmpty
          ? const Center(child: CircularProgressIndicator(color: AppColors.primary))
          : catalog.error != null && catalog.orderHistory.isEmpty
              ? Center(
                  child: FriendlyError(
                    message: catalog.error ?? "Couldn't load orders. Try again.",
                    onRetry: () => catalog.loadOrders(),
                  ),
                )
              : catalog.orderHistory.isEmpty
                  ? const Center(
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(Icons.receipt_long_outlined, size: 56, color: AppColors.muted),
                          SizedBox(height: 12),
                          Text('No orders yet', style: TextStyle(fontWeight: FontWeight.w600)),
                          SizedBox(height: 6),
                          Text('Your past orders will show up here.', style: TextStyle(color: AppColors.muted)),
                        ],
                      ),
                    )
                  : RefreshIndicator(
                      color: AppColors.primary,
                      onRefresh: () => catalog.loadOrders(),
                      child: ListView.separated(
                        padding: const EdgeInsets.all(16),
                        itemCount: catalog.orderHistory.length,
                        separatorBuilder: (_, __) => const SizedBox(height: 10),
                        itemBuilder: (_, i) {
                          final o = catalog.orderHistory[i];
                          return Material(
                            color: AppColors.surface,
                            borderRadius: BorderRadius.circular(14),
                            child: InkWell(
                              borderRadius: BorderRadius.circular(14),
                              onTap: () => Navigator.of(context).pushNamed('/order-detail', arguments: o),
                              child: Padding(
                                padding: const EdgeInsets.all(14),
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                      children: [
                                        Expanded(
                                          child: Text(o.orderNumber, style: const TextStyle(fontWeight: FontWeight.w700)),
                                        ),
                                        Text(formatMoney(o.total, currency), style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w700)),
                                      ],
                                    ),
                                    const SizedBox(height: 4),
                                    Text('Status: ${o.status}', style: const TextStyle(color: AppColors.muted, fontSize: 13)),
                                    Text('Code: ${o.verificationCode}', style: const TextStyle(fontWeight: FontWeight.w600)),
                                    Text(o.deliveryAddress, style: const TextStyle(fontSize: 12, color: AppColors.muted)),
                                  ],
                                ),
                              ),
                            ),
                          );
                        },
                      ),
                    ),
    );
  }
}
