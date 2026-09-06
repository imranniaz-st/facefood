import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';

import '../theme/app_theme.dart';

/// Official promo / landing-style page matching the mockup marketing sections.
class PromoLandingScreen extends StatelessWidget {
  const PromoLandingScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Facefood')),
      body: ListView(
        children: [
          SizedBox(
            height: 280,
            child: Stack(
              fit: StackFit.expand,
              children: [
                CachedNetworkImage(
                  imageUrl: 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=1200&q=80',
                  fit: BoxFit.cover,
                ),
                Container(
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.bottomCenter,
                      end: Alignment.topCenter,
                      colors: [Colors.black.withValues(alpha: 0.7), Colors.transparent],
                    ),
                  ),
                ),
                const Padding(
                  padding: EdgeInsets.all(24),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      Text(
                        'MEGA DEAL',
                        style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800, letterSpacing: 1),
                      ),
                      SizedBox(height: 6),
                      Text(
                        'The Ultimate Combo',
                        style: TextStyle(color: Colors.white, fontSize: 28, fontWeight: FontWeight.w800),
                      ),
                      SizedBox(height: 6),
                      Text(
                        'Limited Time Deals on burgers, pizza & more.',
                        style: TextStyle(color: Colors.white70),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              children: [
                ElevatedButton(
                  onPressed: () => Navigator.of(context).pushNamedAndRemoveUntil('/home', (_) => false),
                  child: const Text('Explore Menu'),
                ),
                const SizedBox(height: 12),
                OutlinedButton(
                  onPressed: () => Navigator.of(context).pushNamedAndRemoveUntil('/home', (_) => false),
                  child: const Text('Limited Time Deals'),
                ),
              ],
            ),
          ),
          Container(
            margin: const EdgeInsets.all(16),
            padding: const EdgeInsets.all(24),
            decoration: BoxDecoration(
              color: AppColors.primary,
              borderRadius: BorderRadius.circular(20),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Get the Facefood App',
                  style: TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w800),
                ),
                const SizedBox(height: 8),
                Text(
                  'Order faster with exclusive app-only offers.',
                  style: TextStyle(color: Colors.white.withValues(alpha: 0.9)),
                ),
                const SizedBox(height: 16),
                const Text('Get it on Google Play', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w600)),
                const SizedBox(height: 6),
                const Text('Download on the App Store', style: TextStyle(color: Colors.white, fontWeight: FontWeight.w600)),
              ],
            ),
          ),
          const Padding(
            padding: EdgeInsets.symmetric(vertical: 24),
            child: Column(
              children: [
                Text(
                  'Facefood',
                  style: TextStyle(color: AppColors.primary, fontWeight: FontWeight.w800, fontSize: 18),
                ),
                SizedBox(height: 8),
                Text('© Facefood. Taste the best fast food in town.', style: TextStyle(color: AppColors.muted, fontSize: 12)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
