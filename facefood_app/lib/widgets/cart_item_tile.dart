import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';

import '../models/cart_item.dart';
import '../theme/app_theme.dart';
import 'money_text.dart';

class CartItemTile extends StatelessWidget {
  final CartItem item;
  final VoidCallback onIncrement;
  final VoidCallback onDecrement;

  const CartItemTile({
    super.key,
    required this.item,
    required this.onIncrement,
    required this.onDecrement,
  });

  @override
  Widget build(BuildContext context) {
    final p = item.product;
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(
        children: [
          ClipRRect(
            borderRadius: BorderRadius.circular(10),
            child: SizedBox(
              width: 64,
              height: 64,
              child: p.imageUrl != null
                  ? CachedNetworkImage(imageUrl: p.imageUrl!, fit: BoxFit.cover)
                  : Container(color: AppColors.surface),
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  item.dealTitle ?? p.name,
                  style: const TextStyle(fontWeight: FontWeight.w600),
                ),
                if (item.dealId != null) ...[
                  const SizedBox(height: 2),
                  Text(
                    p.name,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontSize: 12, color: AppColors.muted),
                  ),
                ],
                if (item.extras.isNotEmpty) ...[
                  const SizedBox(height: 2),
                  Text(
                    item.extrasLabel,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontSize: 12, color: AppColors.muted),
                  ),
                ] else if (item.dealId == null) ...[
                  const SizedBox(height: 2),
                  Text(
                    p.description ?? '',
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontSize: 12, color: AppColors.muted),
                  ),
                ],
                const SizedBox(height: 6),
                MoneyText(
                  item.unitPrice,
                  style: const TextStyle(fontWeight: FontWeight.w700, color: AppColors.primary),
                ),
              ],
            ),
          ),
          Container(
            decoration: BoxDecoration(
              color: AppColors.surface,
              borderRadius: BorderRadius.circular(24),
            ),
            child: Row(
              children: [
                IconButton(
                  visualDensity: VisualDensity.compact,
                  onPressed: onDecrement,
                  icon: const Icon(Icons.remove, size: 18),
                ),
                Text('${item.quantity}', style: const TextStyle(fontWeight: FontWeight.w600)),
                IconButton(
                  visualDensity: VisualDensity.compact,
                  onPressed: onIncrement,
                  icon: const Icon(Icons.add, size: 18, color: AppColors.primary),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
