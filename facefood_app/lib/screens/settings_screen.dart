import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/auth_provider.dart';
import '../providers/settings_provider.dart';
import '../theme/app_theme.dart';
import '../utils/format.dart';

class SettingsScreen extends StatelessWidget {
  const SettingsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final store = context.watch<SettingsProvider>().settings;

    return Scaffold(
      appBar: AppBar(title: const Text('Settings')),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          _tile(Icons.campaign_outlined, 'Promotions landing', () {
            Navigator.of(context).pushNamed('/promo');
          }),
          _tile(Icons.location_on_outlined, 'Delivery addresses', () {
            if (!auth.isAuthenticated) {
              Navigator.of(context).pushNamed('/login');
              return;
            }
            Navigator.of(context).pushNamed('/addresses');
          }),
          _tile(Icons.info_outline, 'About ${store.storeName}', () {
            showAboutDialog(
              context: context,
              applicationName: store.storeName,
              applicationVersion: '1.0.0',
              applicationLegalese: '© ${store.storeName}',
              children: [
                const SizedBox(height: 12),
                Text(
                  'Food delivery with cash, JazzCash & card. '
                  'Delivery ${formatMoney(store.deliveryFee, store.currency)} · '
                  '${store.taxLabel} ${store.taxPercent.toStringAsFixed(0)}%.',
                ),
              ],
            );
          }),
          ListTile(
            leading: const Icon(Icons.local_shipping_outlined, color: AppColors.primary),
            title: const Text('Delivery & tax'),
            subtitle: Text(
              'Delivery ${formatMoney(store.deliveryFee, store.currency)} · '
              '${store.taxLabel} ${store.taxPercent.toStringAsFixed(0)}% · '
              'Usually ${store.estimatedDelivery}',
              style: const TextStyle(fontSize: 12),
            ),
          ),
          if (auth.isAuthenticated) ...[
            const SizedBox(height: 12),
            TextButton(
              onPressed: () async {
                await auth.logout();
                if (context.mounted) {
                  Navigator.of(context).pop();
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
