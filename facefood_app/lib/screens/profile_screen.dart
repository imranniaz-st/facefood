import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/auth_provider.dart';
import '../providers/catalog_provider.dart';
import '../theme/app_theme.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  void _requireAuth(BuildContext context, VoidCallback action) {
    final auth = context.read<AuthProvider>();
    if (!auth.isAuthenticated) {
      Navigator.of(context).pushNamed('/login');
      return;
    }
    action();
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final user = auth.user;

    return SafeArea(
      child: ListView(
        padding: const EdgeInsets.fromLTRB(20, 16, 20, 24),
        children: [
          const Text('Profile', textAlign: TextAlign.center, style: TextStyle(fontSize: 20, fontWeight: FontWeight.w700)),
          const SizedBox(height: 20),
          Center(
            child: Column(
              children: [
                CircleAvatar(
                  radius: 42,
                  backgroundColor: AppColors.primarySoft,
                  backgroundImage: user?.avatarUrl != null
                      ? CachedNetworkImageProvider(user!.avatarUrl!)
                      : null,
                  child: user?.avatarUrl == null
                      ? const Icon(Icons.person, size: 42, color: AppColors.primary)
                      : null,
                ),
                const SizedBox(height: 12),
                Text(
                  user?.name ?? 'Facefood Guest',
                  style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700),
                ),
                if (user?.email != null) ...[
                  const SizedBox(height: 4),
                  Text(user!.email, style: const TextStyle(color: AppColors.muted, fontSize: 13)),
                ],
                if (auth.isAuthenticated) ...[
                  const SizedBox(height: 6),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: const Color(0xFFFFF3CD),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: const Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.star, size: 14, color: AppColors.star),
                        SizedBox(width: 4),
                        Text('Premium Member', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600)),
                      ],
                    ),
                  ),
                ],
                if (user?.location != null) ...[
                  const SizedBox(height: 6),
                  Text(user!.location!, style: const TextStyle(color: AppColors.muted, fontSize: 12)),
                ],
              ],
            ),
          ),
          const SizedBox(height: 24),
          if (!auth.isAuthenticated) ...[
            ElevatedButton(
              onPressed: () => Navigator.of(context).pushNamed('/register'),
              child: const Text('Create Account'),
            ),
            const SizedBox(height: 10),
            OutlinedButton(
              onPressed: () => Navigator.of(context).pushNamed('/login'),
              child: const Text('Log in'),
            ),
            const SizedBox(height: 16),
          ],
          _tile(Icons.person_outline, 'Personal Information', () {
            _requireAuth(context, () => Navigator.of(context).pushNamed('/personal-info'));
          }),
          _tile(Icons.receipt_long_outlined, 'My Orders', () {
            _requireAuth(context, () => Navigator.of(context).pushNamed('/orders'));
          }),
          _tile(Icons.favorite_border, 'Favorite Items', () {
            _requireAuth(context, () {
              context.read<CatalogProvider>().loadFavorites();
              Navigator.of(context).pushNamed('/favorites');
            });
          }),
          _tile(Icons.location_on_outlined, 'Delivery Addresses', () {
            _requireAuth(context, () => Navigator.of(context).pushNamed('/addresses'));
          }),
          _tile(Icons.card_giftcard, 'Promotions & Rewards', () {
            context.read<CatalogProvider>().loadDeals();
            Navigator.of(context).pushNamed('/promotions');
          }),
          _tile(Icons.payment, 'Payment Methods', () {
            Navigator.of(context).pushNamed('/payment-methods');
          }),
          _tile(Icons.help_outline, 'Help Center', () {
            Navigator.of(context).pushNamed('/help');
          }),
          _tile(Icons.settings_outlined, 'Settings', () {
            Navigator.of(context).pushNamed('/settings');
          }),
          if (auth.isAuthenticated) ...[
            const SizedBox(height: 12),
            TextButton(
              onPressed: () async {
                await auth.logout();
                if (context.mounted) {
                  ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Logged out')));
                }
              },
              child: const Text('Log Out', style: TextStyle(color: AppColors.popularRed, fontWeight: FontWeight.w600)),
            ),
          ],
        ],
      ),
    );
  }

  Widget _tile(IconData icon, String title, VoidCallback onTap) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Material(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(14),
        child: ListTile(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
          leading: Icon(icon, color: AppColors.primary),
          title: Text(title, style: const TextStyle(fontWeight: FontWeight.w500)),
          trailing: const Icon(Icons.chevron_right, color: AppColors.muted),
          onTap: onTap,
        ),
      ),
    );
  }
}
