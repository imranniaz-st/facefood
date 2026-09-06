import 'package:flutter/material.dart';

import '../theme/app_theme.dart';

/// Polished map placeholder (no Google Maps API key required).
class MapPlaceholder extends StatelessWidget {
  final String address;
  final double height;

  const MapPlaceholder({super.key, required this.address, this.height = 140});

  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(14),
      child: SizedBox(
        height: height,
        width: double.infinity,
        child: Stack(
          fit: StackFit.expand,
          children: [
            CustomPaint(painter: _MapGridPainter()),
            Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [
                    const Color(0xFFE8F4F8),
                    AppColors.primarySoft,
                    const Color(0xFFD6EEF8),
                  ],
                ),
              ),
            ),
            // Fake roads
            Positioned(
              left: 0,
              right: 0,
              top: height * 0.45,
              child: Container(height: 10, color: Colors.white.withValues(alpha: 0.7)),
            ),
            Positioned(
              top: 0,
              bottom: 0,
              left: MediaQuery.sizeOf(context).width * 0.35,
              child: Container(width: 8, color: Colors.white.withValues(alpha: 0.55)),
            ),
            const Center(
              child: Icon(Icons.location_on, color: AppColors.primary, size: 42),
            ),
            Positioned(
              left: 12,
              right: 12,
              bottom: 10,
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.92),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Text(
                  address,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w500),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _MapGridPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()
      ..color = const Color(0xFFB0D4E3).withValues(alpha: 0.4)
      ..strokeWidth = 1;
    const step = 28.0;
    for (double x = 0; x < size.width; x += step) {
      canvas.drawLine(Offset(x, 0), Offset(x, size.height), paint);
    }
    for (double y = 0; y < size.height; y += step) {
      canvas.drawLine(Offset(0, y), Offset(size.width, y), paint);
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
