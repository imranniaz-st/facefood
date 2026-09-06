import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import 'providers/auth_provider.dart';
import 'providers/cart_provider.dart';
import 'providers/catalog_provider.dart';
import 'providers/settings_provider.dart';
import 'screens/addresses_screen.dart';
import 'screens/auth_screens.dart';
import 'screens/checkout_screen.dart';
import 'screens/favorites_screen.dart';
import 'screens/help_center_screen.dart';
import 'screens/main_shell.dart';
import 'screens/order_confirmation_screen.dart';
import 'screens/order_detail_screen.dart';
import 'screens/orders_screen.dart';
import 'screens/payment_methods_screen.dart';
import 'screens/personal_info_screen.dart';
import 'screens/product_detail_screen.dart';
import 'screens/promo_landing_screen.dart';
import 'screens/promotions_screen.dart';
import 'screens/settings_screen.dart';
import 'services/api_client.dart';
import 'theme/app_theme.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const FacefoodApp());
}

class FacefoodApp extends StatelessWidget {
  const FacefoodApp({super.key});

  @override
  Widget build(BuildContext context) {
    final api = ApiClient();

    return MultiProvider(
      providers: [
        Provider<ApiClient>.value(value: api),
        ChangeNotifierProvider(create: (_) => AuthProvider(api)),
        ChangeNotifierProvider(create: (_) => SettingsProvider(api)..load()),
        ChangeNotifierProvider(create: (_) => CartProvider()),
        ChangeNotifierProvider(create: (_) => CatalogProvider(api)),
      ],
      child: MaterialApp(
        title: 'Facefood',
        debugShowCheckedModeBanner: false,
        theme: AppTheme.light,
        home: const _Root(),
        routes: {
          '/home': (_) => const MainShell(),
          '/login': (_) => const LoginScreen(),
          '/register': (_) => const RegisterScreen(),
          '/checkout': (_) => const CheckoutScreen(),
          '/order-confirmation': (_) => const OrderConfirmationScreen(),
          '/orders': (_) => const OrdersScreen(),
          '/order-detail': (_) => const OrderDetailScreen(),
          '/product': (_) => const ProductDetailScreen(),
          '/promo': (_) => const PromoLandingScreen(),
          '/favorites': (_) => const FavoritesScreen(),
          '/addresses': (_) => const AddressesScreen(),
          '/addresses/pick': (_) => const AddressesScreen(pickMode: true),
          '/personal-info': (_) => const PersonalInfoScreen(),
          '/promotions': (_) => const PromotionsScreen(),
          '/payment-methods': (_) => const PaymentMethodsScreen(),
          '/help': (_) => const HelpCenterScreen(),
          '/settings': (_) => const SettingsScreen(),
        },
      ),
    );
  }
}

class _Root extends StatelessWidget {
  const _Root();

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    if (auth.bootstrapping) {
      return const Scaffold(
        body: Center(child: CircularProgressIndicator(color: AppColors.primary)),
      );
    }
    // Guests can browse; login required only at checkout / orders / profile actions
    return const MainShell();
  }
}
