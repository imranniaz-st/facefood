import 'package:flutter/material.dart';

import '../models/store_settings.dart';
import '../theme/app_theme.dart';
import '../utils/format.dart';

class OrderSummary extends StatelessWidget {
  final double subtotal;
  final double deliveryFee;
  final double tax;
  final double total;
  final StoreSettings settings;
  final String? taxLabel;
  final double? taxPercent;

  const OrderSummary({
    super.key,
    required this.subtotal,
    required this.deliveryFee,
    required this.tax,
    required this.total,
    required this.settings,
    this.taxLabel,
    this.taxPercent,
  });

  @override
  Widget build(BuildContext context) {
    final label = taxLabel ?? settings.taxLabel;
    final pct = (taxPercent ?? settings.taxPercent).toStringAsFixed(0);
    final money = (num v) => formatMoney(v, settings.currency);
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(16),
      ),
      child: Column(
        children: [
          _row('Subtotal', money(subtotal)),
          const SizedBox(height: 8),
          _row('Delivery Fee', money(deliveryFee)),
          const SizedBox(height: 8),
          _row('$label $pct%', money(tax)),
          const Padding(
            padding: EdgeInsets.symmetric(vertical: 12),
            child: Divider(height: 1),
          ),
          _row(
            'Total',
            money(total),
            bold: true,
            valueColor: AppColors.totalBlue,
          ),
        ],
      ),
    );
  }

  Widget _row(String label, String value, {bool bold = false, Color? valueColor}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: TextStyle(
            fontWeight: bold ? FontWeight.w700 : FontWeight.w500,
            color: bold ? AppColors.text : AppColors.muted,
          ),
        ),
        Text(
          value,
          style: TextStyle(
            fontWeight: bold ? FontWeight.w700 : FontWeight.w600,
            color: valueColor ?? AppColors.text,
            fontSize: bold ? 17 : 14,
          ),
        ),
      ],
    );
  }
}
