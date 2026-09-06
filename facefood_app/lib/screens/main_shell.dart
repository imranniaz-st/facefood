import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/auth_provider.dart';
import '../providers/cart_provider.dart';
import '../providers/catalog_provider.dart';
import '../providers/settings_provider.dart';
import '../theme/app_theme.dart';
import '../widgets/app_drawer.dart';
import '../widgets/bottom_nav.dart';
import 'cart_screen.dart';
import 'deals_screen.dart';
import 'home_screen.dart';
import 'menu_screen.dart';
import 'profile_screen.dart';

class MainShell extends StatefulWidget {
  const MainShell({super.key});

  @override
  State<MainShell> createState() => _MainShellState();
}

class _MainShellState extends State<MainShell> {
  final _scaffoldKey = GlobalKey<ScaffoldState>();
  int _index = 0;
  bool? _wasAuthenticated;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final catalog = context.read<CatalogProvider>();
      catalog.loadHome();
      context.read<SettingsProvider>().load();
      _syncAuthData();
    });
  }

  void _syncAuthData() {
    final auth = context.read<AuthProvider>();
    final catalog = context.read<CatalogProvider>();
    if (auth.isAuthenticated) {
      catalog.loadAddresses();
      catalog.loadFavorites();
    }
    _wasAuthenticated = auth.isAuthenticated;
  }

  void switchTab(int i) => setState(() => _index = i);

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    if (_wasAuthenticated != null && _wasAuthenticated != auth.isAuthenticated) {
      WidgetsBinding.instance.addPostFrameCallback((_) => _syncAuthData());
    }

    final cartCount = context.watch<CartProvider>().itemCount;
    final pages = [
      HomeScreen(
        onOpenMenu: () => switchTab(1),
        onOpenCart: () => switchTab(3),
        onOpenDeals: () => switchTab(2),
        onOpenDrawer: () => _scaffoldKey.currentState?.openDrawer(),
      ),
      const MenuScreen(),
      DealsScreen(
        onOpenMenu: () => switchTab(1),
        onOpenCart: () => switchTab(3),
      ),
      CartScreen(onCheckout: () {
        Navigator.of(context).pushNamed('/checkout');
      }),
      const ProfileScreen(),
    ];

    return Scaffold(
      key: _scaffoldKey,
      drawer: AppDrawer(
        currentIndex: _index,
        onSelectTab: switchTab,
      ),
      body: IndexedStack(index: _index, children: pages),
      bottomNavigationBar: FacefoodBottomNav(
        currentIndex: _index,
        cartCount: cartCount,
        onTap: switchTab,
      ),
      backgroundColor: AppColors.bg,
    );
  }
}
