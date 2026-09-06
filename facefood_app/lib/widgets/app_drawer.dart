import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../providers/auth_provider.dart';
import '../theme/app_theme.dart';

/// Facefood side navigation: Create Account when logged out, name + logout when in.
class AppDrawer extends StatelessWidget {
  final int currentIndex;
  final ValueChanged<int> onSelectTab;

  const AppDrawer({
    super.key,
    required this.currentIndex,
    required this.onSelectTab,
  });

  void _closeThen(BuildContext context, VoidCallback action) {
    final nav = Navigator.of(context);
    nav.pop();
    action();
  }

  void _openRoute(BuildContext context, String route) {
    final nav = Navigator.of(context);
    nav.pop();
    nav.pushNamed(route);
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final user = auth.user;

    return Drawer(
      backgroundColor: Colors.white,
      child: SafeArea(
        child: Column(
          children: [
            _header(context, auth),
            Expanded(
              child: ListView(
                padding: const EdgeInsets.symmetric(vertical: 8),
                children: [
                  _navTile(
                    context,
                    icon: Icons.home_rounded,
                    label: 'Home',
                    selected: currentIndex == 0,
                    onTap: () => _closeThen(context, () => onSelectTab(0)),
                  ),
                  _navTile(
                    context,
                    icon: Icons.restaurant_menu_rounded,
                    label: 'Menu',
                    selected: currentIndex == 1,
                    onTap: () => _closeThen(context, () => onSelectTab(1)),
                  ),
                  _navTile(
                    context,
                    icon: Icons.local_fire_department_rounded,
                    label: 'Deals',
                    selected: currentIndex == 2,
                    onTap: () => _closeThen(context, () => onSelectTab(2)),
                  ),
                  _navTile(
                    context,
                    icon: Icons.shopping_bag_outlined,
                    label: 'Cart',
                    selected: currentIndex == 3,
                    onTap: () => _closeThen(context, () => onSelectTab(3)),
                  ),
                  const Divider(height: 24, indent: 16, endIndent: 16),
                  if (!auth.isAuthenticated) ...[
                    _navTile(
                      context,
                      icon: Icons.person_add_alt_1_rounded,
                      label: 'Create Account',
                      highlight: true,
                      onTap: () => _openRoute(context, '/register'),
                    ),
                    _navTile(
                      context,
                      icon: Icons.login_rounded,
                      label: 'Log in',
                      onTap: () => _openRoute(context, '/login'),
                    ),
                  ] else ...[
                    _navTile(
                      context,
                      icon: Icons.person_outline_rounded,
                      label: 'Profile',
                      selected: currentIndex == 4,
                      onTap: () => _closeThen(context, () => onSelectTab(4)),
                    ),
                    _navTile(
                      context,
                      icon: Icons.receipt_long_outlined,
                      label: 'My Orders',
                      onTap: () => _openRoute(context, '/orders'),
                    ),
                    _navTile(
                      context,
                      icon: Icons.favorite_border,
                      label: 'Favorites',
                      onTap: () => _openRoute(context, '/favorites'),
                    ),
                  ],
                  _navTile(
                    context,
                    icon: Icons.settings_outlined,
                    label: 'Settings',
                    onTap: () => _openRoute(context, '/settings'),
                  ),
                  _navTile(
                    context,
                    icon: Icons.help_outline,
                    label: 'Help Center',
                    onTap: () => _openRoute(context, '/help'),
                  ),
                  if (auth.isAuthenticated) ...[
                    const Divider(height: 24, indent: 16, endIndent: 16),
                    ListTile(
                      leading: const Icon(Icons.logout_rounded, color: AppColors.popularRed),
                      title: const Text(
                        'Log out',
                        style: TextStyle(color: AppColors.popularRed, fontWeight: FontWeight.w600),
                      ),
                      onTap: () async {
                        final nav = Navigator.of(context);
                        final messenger = ScaffoldMessenger.of(context);
                        await auth.logout();
                        nav.pop();
                        messenger.showSnackBar(const SnackBar(content: Text('Logged out')));
                      },
                    ),
                  ],
                ],
              ),
            ),
            if (user != null)
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
                child: Text(
                  'Signed in as ${user.email}',
                  style: const TextStyle(fontSize: 11, color: AppColors.muted),
                  textAlign: TextAlign.center,
                ),
              ),
          ],
        ),
      ),
    );
  }

  Widget _header(BuildContext context, AuthProvider auth) {
    final user = auth.user;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(20, 20, 20, 20),
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [AppColors.primary, AppColors.primaryDark],
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Facefood',
            style: TextStyle(
              color: Colors.white,
              fontSize: 22,
              fontWeight: FontWeight.w800,
              letterSpacing: -0.4,
            ),
          ),
          const SizedBox(height: 16),
          if (auth.isAuthenticated && user != null) ...[
            Row(
              children: [
                CircleAvatar(
                  radius: 26,
                  backgroundColor: Colors.white,
                  backgroundImage: user.avatarUrl != null ? CachedNetworkImageProvider(user.avatarUrl!) : null,
                  child: user.avatarUrl == null
                      ? Text(
                          user.name.isNotEmpty ? user.name[0].toUpperCase() : '?',
                          style: const TextStyle(
                            color: AppColors.primary,
                            fontWeight: FontWeight.w700,
                            fontSize: 20,
                          ),
                        )
                      : null,
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        user.name,
                        style: const TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.w700,
                          fontSize: 16,
                        ),
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 2),
                      Text(
                        user.email,
                        style: TextStyle(color: Colors.white.withValues(alpha: 0.9), fontSize: 12),
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ] else ...[
            const Text(
              'Order faster with an account',
              style: TextStyle(color: Colors.white, fontWeight: FontWeight.w600, fontSize: 14),
            ),
            const SizedBox(height: 12),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.white,
                  foregroundColor: AppColors.primaryDark,
                  minimumSize: const Size.fromHeight(44),
                  elevation: 0,
                ),
                onPressed: () => _openRoute(context, '/register'),
                child: const Text('Create Account'),
              ),
            ),
            TextButton(
              onPressed: () => _openRoute(context, '/login'),
              child: const Text('Already have an account? Log in', style: TextStyle(color: Colors.white)),
            ),
          ],
        ],
      ),
    );
  }

  Widget _navTile(
    BuildContext context, {
    required IconData icon,
    required String label,
    required VoidCallback onTap,
    bool selected = false,
    bool highlight = false,
  }) {
    final color = highlight || selected ? AppColors.primary : AppColors.text;
    return ListTile(
      leading: Icon(icon, color: color),
      title: Text(
        label,
        style: TextStyle(
          fontWeight: highlight || selected ? FontWeight.w700 : FontWeight.w500,
          color: color,
        ),
      ),
      selected: selected,
      selectedTileColor: AppColors.primarySoft,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      onTap: onTap,
    );
  }
}
