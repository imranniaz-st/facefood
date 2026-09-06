import 'dart:async';

import 'package:flutter/material.dart';

import '../theme/app_theme.dart';

class CountdownTimer extends StatefulWidget {
  final DateTime endsAt;
  final Color? background;

  const CountdownTimer({super.key, required this.endsAt, this.background});

  @override
  State<CountdownTimer> createState() => _CountdownTimerState();
}

class _CountdownTimerState extends State<CountdownTimer> {
  late Timer _timer;
  Duration _remaining = Duration.zero;

  @override
  void initState() {
    super.initState();
    _tick();
    _timer = Timer.periodic(const Duration(seconds: 1), (_) => _tick());
  }

  void _tick() {
    final diff = widget.endsAt.difference(DateTime.now());
    setState(() => _remaining = diff.isNegative ? Duration.zero : diff);
  }

  @override
  void dispose() {
    _timer.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final d = _remaining.inDays;
    final h = _remaining.inHours % 24;
    final m = _remaining.inMinutes % 60;
    final s = _remaining.inSeconds % 60;

    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        _box('$d', 'Days'),
        _sep(),
        _box(h.toString().padLeft(2, '0'), 'Hours'),
        _sep(),
        _box(m.toString().padLeft(2, '0'), 'Min'),
        _sep(),
        _box(s.toString().padLeft(2, '0'), 'Sec'),
      ],
    );
  }

  Widget _sep() => const Padding(
        padding: EdgeInsets.symmetric(horizontal: 4),
        child: Text(':', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white)),
      );

  Widget _box(String value, String label) {
    return Container(
      width: 48,
      padding: const EdgeInsets.symmetric(vertical: 6),
      decoration: BoxDecoration(
        color: widget.background ?? Colors.black.withValues(alpha: 0.45),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Column(
        children: [
          Text(
            value,
            style: const TextStyle(
              color: Colors.white,
              fontWeight: FontWeight.w700,
              fontSize: 14,
            ),
          ),
          Text(
            label,
            style: TextStyle(
              color: Colors.white.withValues(alpha: 0.85),
              fontSize: 9,
            ),
          ),
        ],
      ),
    );
  }
}

/// Compact blue pill countdown for deal cards on light backgrounds.
class LightCountdownTimer extends StatelessWidget {
  final DateTime endsAt;

  const LightCountdownTimer({super.key, required this.endsAt});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(8),
      decoration: BoxDecoration(
        color: AppColors.primarySoft,
        borderRadius: BorderRadius.circular(12),
      ),
      child: CountdownTimer(
        endsAt: endsAt,
        background: AppColors.primary,
      ),
    );
  }
}
