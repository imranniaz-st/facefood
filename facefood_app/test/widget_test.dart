import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:provider/provider.dart';

import 'package:facefood_app/providers/auth_provider.dart';
import 'package:facefood_app/providers/cart_provider.dart';
import 'package:facefood_app/providers/catalog_provider.dart';
import 'package:facefood_app/services/api_client.dart';
import 'package:facefood_app/theme/app_theme.dart';

void main() {
  testWidgets('App theme builds', (tester) async {
    final api = ApiClient();
    await tester.pumpWidget(
      MultiProvider(
        providers: [
          Provider<ApiClient>.value(value: api),
          ChangeNotifierProvider(create: (_) => AuthProvider(api)),
          ChangeNotifierProvider(create: (_) => CartProvider()),
          ChangeNotifierProvider(create: (_) => CatalogProvider(api)),
        ],
        child: MaterialApp(
          theme: AppTheme.light,
          home: const Scaffold(body: Text('Facefood')),
        ),
      ),
    );
    expect(find.text('Facefood'), findsOneWidget);
  });
}
