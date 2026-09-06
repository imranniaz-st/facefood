import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';

import '../models/order_model.dart';
import '../providers/catalog_provider.dart';
import '../theme/app_theme.dart';
import '../utils/format.dart';
import '../utils/user_error.dart';
import '../providers/settings_provider.dart';
import '../widgets/order_summary.dart';

class OrderDetailScreen extends StatefulWidget {
  const OrderDetailScreen({super.key});

  @override
  State<OrderDetailScreen> createState() => _OrderDetailScreenState();
}

class _OrderDetailScreenState extends State<OrderDetailScreen> {
  OrderModel? _order;
  bool _loading = true;
  String? _error;
  bool _started = false;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (_started) return;
    _started = true;
    final args = ModalRoute.of(context)!.settings.arguments;
    if (args is OrderModel) {
      setState(() {
        _order = args;
        _loading = false;
      });
      _refresh(args.id);
    } else if (args is int) {
      _load(args);
    } else {
      setState(() {
        _error = 'Order not found';
        _loading = false;
      });
    }
  }

  Future<void> _load(int id) async {
    try {
      final order = await context.read<CatalogProvider>().fetchOrder(id);
      if (!mounted) return;
      setState(() {
        _order = order;
        _loading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = userFacingError(e, fallback: "Couldn't load this order. Try again.");
        _loading = false;
      });
    }
  }

  Future<void> _refresh(int id) async {
    try {
      final order = await context.read<CatalogProvider>().fetchOrder(id);
      if (!mounted) return;
      setState(() => _order = order);
    } catch (_) {}
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) {
      return const Scaffold(body: Center(child: CircularProgressIndicator(color: AppColors.primary)));
    }
    if (_error != null || _order == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Order')),
        body: Center(child: Text(_error ?? 'Not found')),
      );
    }

    final o = _order!;
    final settings = context.watch<SettingsProvider>().settings;
    return Scaffold(
      appBar: AppBar(title: Text(o.orderNumber)),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: AppColors.primarySoft,
              borderRadius: BorderRadius.circular(16),
            ),
            child: Column(
              children: [
                const Text('Rider verification code', style: TextStyle(color: AppColors.muted)),
                const SizedBox(height: 8),
                Text(
                  o.verificationCode,
                  style: const TextStyle(
                    fontSize: 36,
                    fontWeight: FontWeight.w800,
                    letterSpacing: 8,
                    color: AppColors.primary,
                  ),
                ),
                TextButton.icon(
                  onPressed: () {
                    Clipboard.setData(ClipboardData(text: o.verificationCode));
                    ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Code copied')));
                  },
                  icon: const Icon(Icons.copy, size: 16),
                  label: const Text('Copy'),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          _row('Status', o.status),
          _row('Payment', o.paymentMethod.replaceAll('_', ' ')),
          _row('Delivery', o.deliveryAddress),
          _row('ETA', '${o.estimatedMinutes} mins'),
          if (o.note != null && o.note!.isNotEmpty) _row('Note', o.note!),
          const SizedBox(height: 16),
          const Text('Items', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
          const SizedBox(height: 8),
          ...o.items.map(
            (i) => Padding(
              padding: const EdgeInsets.only(bottom: 8),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text('${i.productName} ×${i.quantity}'),
                        if (i.extras.isNotEmpty)
                          Text(
                            i.extras.map((e) => e.name).join(', '),
                            style: const TextStyle(fontSize: 12, color: AppColors.muted),
                          ),
                      ],
                    ),
                  ),
                  Text(formatMoney(i.lineTotal, settings.currency), style: const TextStyle(fontWeight: FontWeight.w600)),
                ],
              ),
            ),
          ),
          const SizedBox(height: 12),
          OrderSummary(
            subtotal: o.subtotal,
            deliveryFee: o.deliveryFee,
            tax: o.tax,
            total: o.total,
            settings: settings,
            taxLabel: o.taxLabel ?? settings.taxLabel,
            taxPercent: o.taxPercent ?? settings.taxPercent,
          ),
        ],
      ),
    );
  }

  Widget _row(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 10),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(width: 90, child: Text(label, style: const TextStyle(color: AppColors.muted))),
          Expanded(child: Text(value, style: const TextStyle(fontWeight: FontWeight.w600))),
        ],
      ),
    );
  }
}
