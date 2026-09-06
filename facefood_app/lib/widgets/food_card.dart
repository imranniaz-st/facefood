import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';

import '../theme/app_theme.dart';
import '../models/product_model.dart';
import 'money_text.dart';

class FoodCard extends StatelessWidget {
  final ProductModel product;
  final VoidCallback onAdd;
  final VoidCallback? onTap;
  final bool favorited;
  final VoidCallback? onFavorite;

  const FoodCard({
    super.key,
    required this.product,
    required this.onAdd,
    this.onTap,
    this.favorited = false,
    this.onFavorite,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(16),
      child: Container(
        margin: const EdgeInsets.only(bottom: 14),
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.05),
              blurRadius: 12,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Stack(
              children: [
                ClipRRect(
                  borderRadius: BorderRadius.circular(12),
                  child: SizedBox(
                    width: 88,
                    height: 88,
                    child: product.imageUrl != null
                        ? CachedNetworkImage(
                            imageUrl: product.imageUrl!,
                            fit: BoxFit.cover,
                            placeholder: (_, __) => Container(color: AppColors.surface),
                            errorWidget: (_, __, ___) => Container(
                              color: AppColors.surface,
                              child: const Icon(Icons.fastfood, color: AppColors.muted),
                            ),
                          )
                        : Container(
                            color: AppColors.surface,
                            child: const Icon(Icons.fastfood, color: AppColors.muted),
                          ),
                  ),
                ),
                if (onFavorite != null)
                  Positioned(
                    top: 0,
                    right: 0,
                    child: Material(
                      color: Colors.white.withValues(alpha: 0.92),
                      shape: const CircleBorder(),
                      child: IconButton(
                        tooltip: favorited ? 'Remove from favorites' : 'Add to favorites',
                        visualDensity: VisualDensity.compact,
                        padding: EdgeInsets.zero,
                        constraints: const BoxConstraints(minWidth: 28, minHeight: 28),
                        onPressed: onFavorite,
                        icon: Icon(
                          favorited ? Icons.favorite : Icons.favorite_border,
                          size: 16,
                          color: favorited ? AppColors.popularRed : AppColors.muted,
                        ),
                      ),
                    ),
                  ),
              ],
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (product.isPopular)
                    Container(
                      margin: const EdgeInsets.only(bottom: 4),
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                      decoration: BoxDecoration(
                        color: AppColors.popularRed,
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: const Text(
                        'POPULAR',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 10,
                          fontWeight: FontWeight.w700,
                          letterSpacing: 0.4,
                        ),
                      ),
                    ),
                  Text(
                    product.name,
                    style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 15),
                  ),
                  const SizedBox(height: 2),
                  Row(
                    children: [
                      const Icon(Icons.star_rounded, size: 16, color: AppColors.star),
                      const SizedBox(width: 2),
                      Text(
                        product.rating.toStringAsFixed(1),
                        style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w500),
                      ),
                    ],
                  ),
                  const SizedBox(height: 4),
                  Text(
                    product.description ?? '',
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontSize: 12, color: AppColors.muted, height: 1.3),
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      MoneyText(
                        product.price,
                        style: const TextStyle(
                          fontWeight: FontWeight.w700,
                          fontSize: 15,
                          color: AppColors.text,
                        ),
                      ),
                      const Spacer(),
                      _AddButton(onTap: onAdd),
                    ],
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

class _AddButton extends StatelessWidget {
  final VoidCallback onTap;
  const _AddButton({required this.onTap});

  @override
  Widget build(BuildContext context) {
    return Material(
      color: AppColors.primary,
      shape: const CircleBorder(),
      child: InkWell(
        customBorder: const CircleBorder(),
        onTap: onTap,
        child: const SizedBox(
          width: 34,
          height: 34,
          child: Icon(Icons.add, color: Colors.white, size: 20),
        ),
      ),
    );
  }
}
