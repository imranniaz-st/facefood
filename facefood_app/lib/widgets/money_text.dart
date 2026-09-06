import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/settings_provider.dart';
import '../utils/format.dart';

class MoneyText extends StatelessWidget {
  final num value;
  final TextStyle? style;

  const MoneyText(this.value, {super.key, this.style});

  @override
  Widget build(BuildContext context) {
    final currency = context.watch<SettingsProvider>().settings.currency;
    return Text(formatMoney(value, currency), style: style);
  }
}
