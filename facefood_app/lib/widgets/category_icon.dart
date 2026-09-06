import 'package:flutter/material.dart';

import '../models/category_model.dart';
import '../theme/app_theme.dart';

class CategoryIcon extends StatelessWidget {
  final CategoryModel category;
  final VoidCallback? onTap;

  const CategoryIcon({super.key, required this.category, this.onTap});

  IconData get _icon {
    switch (category.icon) {
      case 'burger':
        return Icons.lunch_dining;
      case 'shawarma':
        return Icons.kebab_dining;
      case 'drink':
        return Icons.local_drink;
      case 'kids':
        return Icons.child_care;
      case 'pizza':
        return Icons.local_pizza;
      case 'chicken':
        return Icons.set_meal;
      default:
        return Icons.restaurant;
    }
  }

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: SizedBox(
        width: 78,
        child: Column(
          children: [
            Container(
              width: 64,
              height: 64,
              decoration: BoxDecoration(
                color: AppColors.primarySoft,
                shape: BoxShape.circle,
                border: Border.all(color: AppColors.primary.withValues(alpha: 0.25)),
              ),
              child: Icon(_icon, color: AppColors.primary, size: 30),
            ),
            const SizedBox(height: 8),
            Text(
              category.name,
              textAlign: TextAlign.center,
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
              style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w500),
            ),
          ],
        ),
      ),
    );
  }
}
