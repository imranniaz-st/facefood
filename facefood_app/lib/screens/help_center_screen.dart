import 'package:flutter/material.dart';

import '../theme/app_theme.dart';

class HelpCenterScreen extends StatelessWidget {
  const HelpCenterScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Help Center')),
      body: ListView(
        padding: const EdgeInsets.all(20),
        children: [
          _card(
            Icons.email_outlined,
            'Email support',
            'support@facefood.pk',
            'We usually reply within a few hours.',
          ),
          _card(
            Icons.phone_outlined,
            'Call / WhatsApp',
            '+92 300 1234567',
            'Daily 10:00 AM – 11:00 PM',
          ),
          _card(
            Icons.help_outline,
            'Order issues',
            'Use My Orders → open an order',
            'Share the 4-digit rider verification code if asked.',
          ),
          _card(
            Icons.payments_outlined,
            'Payments',
            'COD · JazzCash · Card',
            'Choose your method at checkout. No card details stored in-app.',
          ),
          const SizedBox(height: 8),
          const Text(
            'Facefood — taste the best fast food in town.',
            textAlign: TextAlign.center,
            style: TextStyle(color: AppColors.muted, fontSize: 12),
          ),
        ],
      ),
    );
  }

  Widget _card(IconData icon, String title, String subtitle, String body) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.surface,
        borderRadius: BorderRadius.circular(14),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: AppColors.primary),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: const TextStyle(fontWeight: FontWeight.w700)),
                const SizedBox(height: 2),
                Text(subtitle, style: const TextStyle(color: AppColors.primary, fontWeight: FontWeight.w600)),
                const SizedBox(height: 6),
                Text(body, style: const TextStyle(color: AppColors.muted, fontSize: 13)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
