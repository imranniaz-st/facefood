import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/auth_provider.dart';
import '../providers/catalog_provider.dart';
import 'user_error.dart';

Future<void> toggleProductFavorite(BuildContext context, int productId) async {
  final auth = context.read<AuthProvider>();
  if (!auth.isAuthenticated) {
    Navigator.of(context).pushNamed('/login');
    return;
  }

  try {
    final favorited = await context.read<CatalogProvider>().toggleFavorite(productId);
    if (!context.mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(favorited ? 'Added to favorites' : 'Removed from favorites'),
        duration: const Duration(seconds: 1),
      ),
    );
  } catch (e) {
    if (!context.mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(userFacingError(e, fallback: "Couldn't update favorites. Try again.")),
      ),
    );
  }
}
