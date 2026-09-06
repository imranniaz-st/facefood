import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';

import '../models/order_model.dart';
import '../providers/settings_provider.dart';
import '../theme/app_theme.dart';
import '../utils/format.dart';

class OrderConfirmationScreen extends StatelessWidget {
  const OrderConfirmationScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final order = ModalRoute.of(context)!.settings.arguments as OrderModel;
    final currency = order.currency ?? context.watch<SettingsProvider>().settings.currency;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Order Confirmed'),
        automaticallyImplyLeading: false,
      ),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              color: AppColors.primarySoft,
              borderRadius: BorderRadius.circular(20),
            ),
            child: Column(
              children: [
                const Icon(Icons.check_circle, color: AppColors.primary, size: 56),
                const SizedBox(height: 10),
                const Text('Order Verification', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 18)),
                const SizedBox(height: 6),
                Text('Order #${order.orderNumber}', style: const TextStyle(color: AppColors.muted)),
              ],
            ),
          ),
          const SizedBox(height: 20),
          Container(
            padding: const EdgeInsets.all(20),
            decoration: BoxDecoration(
              border: Border.all(color: AppColors.primary, width: 2),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Column(
              children: [
                const Text(
                  'Share this key with your rider to verify your order',
                  textAlign: TextAlign.center,
                  style: TextStyle(color: AppColors.muted),
                ),
                const SizedBox(height: 12),
                Text(
                  order.verificationCode,
                  style: const TextStyle(
                    fontSize: 42,
                    fontWeight: FontWeight.w800,
                    letterSpacing: 8,
                    color: AppColors.primary,
                  ),
                ),
                TextButton.icon(
                  onPressed: () {
                    Clipboard.setData(ClipboardData(text: order.verificationCode));
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(content: Text('Code copied')),
                    );
                  },
                  icon: const Icon(Icons.copy, size: 16),
                  label: const Text('Copy code'),
                ),
              ],
            ),
          ),
          const SizedBox(height: 20),
          _info('Delivery', order.deliveryAddress),
          _info('Payment', order.paymentMethod.replaceAll('_', ' ')),
          _info('Total', formatMoney(order.total, currency)),
          _info('Status', order.status),
          const SizedBox(height: 24),
          ElevatedButton(
            onPressed: () => Navigator.of(context).pushNamedAndRemoveUntil('/home', (_) => false),
            child: const Text('Back to Home'),
          ),
          const SizedBox(height: 10),
          OutlinedButton(
            onPressed: () => Navigator.of(context).pushNamed('/orders'),
            child: const Text('View My Orders'),
          ),
        ],
      ),
    );
  }

  Widget _info(String label, String value) {
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
