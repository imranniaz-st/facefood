import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../models/address_model.dart';
import '../providers/auth_provider.dart';
import '../providers/catalog_provider.dart';
import '../theme/app_theme.dart';
import '../utils/user_error.dart';

class AddressesScreen extends StatefulWidget {
  /// When true, tapping an address sets it as default and pops.
  final bool pickMode;

  const AddressesScreen({super.key, this.pickMode = false});

  @override
  State<AddressesScreen> createState() => _AddressesScreenState();
}

class _AddressesScreenState extends State<AddressesScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (context.read<AuthProvider>().isAuthenticated) {
        context.read<CatalogProvider>().loadAddresses();
      }
    });
  }

  Future<void> _showForm({AddressModel? existing}) async {
    final label = TextEditingController(text: existing?.label ?? 'Home');
    final line1 = TextEditingController(text: existing?.line1 ?? '');
    final line2 = TextEditingController(text: existing?.line2 ?? '');
    final area = TextEditingController(text: existing?.area ?? '');
    final city = TextEditingController(text: existing?.city ?? 'Islamabad');
    var isDefault = existing?.isDefault ?? false;

    final saved = await showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) {
        return Padding(
          padding: EdgeInsets.only(
            left: 20,
            right: 20,
            top: 20,
            bottom: MediaQuery.of(ctx).viewInsets.bottom + 20,
          ),
          child: StatefulBuilder(
            builder: (ctx, setModal) {
              return SingleChildScrollView(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Text(
                      existing == null ? 'Add Address' : 'Edit Address',
                      style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700),
                    ),
                    const SizedBox(height: 14),
                    TextField(controller: label, decoration: const InputDecoration(labelText: 'Label')),
                    const SizedBox(height: 10),
                    TextField(controller: line1, decoration: const InputDecoration(labelText: 'Street / building')),
                    const SizedBox(height: 10),
                    TextField(controller: line2, decoration: const InputDecoration(labelText: 'Line 2 (optional)')),
                    const SizedBox(height: 10),
                    TextField(controller: area, decoration: const InputDecoration(labelText: 'Area')),
                    const SizedBox(height: 10),
                    TextField(controller: city, decoration: const InputDecoration(labelText: 'City')),
                    const SizedBox(height: 8),
                    SwitchListTile(
                      contentPadding: EdgeInsets.zero,
                      title: const Text('Set as default'),
                      value: isDefault,
                      activeThumbColor: AppColors.primary,
                      onChanged: (v) => setModal(() => isDefault = v),
                    ),
                    const SizedBox(height: 12),
                    ElevatedButton(
                      onPressed: () async {
                        if (line1.text.trim().isEmpty) return;
                        final body = {
                          'label': label.text.trim(),
                          'line1': line1.text.trim(),
                          'line2': line2.text.trim().isEmpty ? null : line2.text.trim(),
                          'area': area.text.trim().isEmpty ? null : area.text.trim(),
                          'city': city.text.trim().isEmpty ? 'Islamabad' : city.text.trim(),
                          'is_default': isDefault,
                        };
                        final catalog = context.read<CatalogProvider>();
                        try {
                          if (existing == null) {
                            await catalog.createAddress(body);
                          } else {
                            await catalog.updateAddress(existing.id, body);
                          }
                          if (ctx.mounted) Navigator.pop(ctx, true);
                        } catch (e) {
                          if (ctx.mounted) {
                            ScaffoldMessenger.of(ctx).showSnackBar(
                              SnackBar(
                                content: Text(
                                  userFacingError(e, fallback: "Couldn't save this address. Try again."),
                                ),
                              ),
                            );
                          }
                        }
                      },
                      child: Text(existing == null ? 'Save Address' : 'Update Address'),
                    ),
                  ],
                ),
              );
            },
          ),
        );
      },
    );

    label.dispose();
    line1.dispose();
    line2.dispose();
    area.dispose();
    city.dispose();

    if (saved == true && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Address saved')));
    }
  }

  @override
  Widget build(BuildContext context) {
    final auth = context.watch<AuthProvider>();
    final catalog = context.watch<CatalogProvider>();

    if (!auth.isAuthenticated) {
      return Scaffold(
        appBar: AppBar(title: const Text('Addresses')),
        body: Center(
          child: ElevatedButton(
            onPressed: () => Navigator.of(context).pushNamed('/login'),
            child: const Text('Login to manage addresses'),
          ),
        ),
      );
    }

    return Scaffold(
      appBar: AppBar(
        title: Text(widget.pickMode ? 'Select Address' : 'Delivery Addresses'),
      ),
      floatingActionButton: FloatingActionButton(
        backgroundColor: AppColors.primary,
        onPressed: () => _showForm(),
        child: const Icon(Icons.add, color: Colors.white),
      ),
      body: catalog.addresses.isEmpty
          ? const Center(
              child: Padding(
                padding: EdgeInsets.all(24),
                child: Text(
                  'No saved addresses yet.\nTap + to add one.',
                  textAlign: TextAlign.center,
                  style: TextStyle(color: AppColors.muted),
                ),
              ),
            )
          : ListView.separated(
              padding: const EdgeInsets.fromLTRB(16, 12, 16, 88),
              itemCount: catalog.addresses.length,
              separatorBuilder: (_, __) => const SizedBox(height: 10),
              itemBuilder: (_, i) {
                final a = catalog.addresses[i];
                return Material(
                  color: a.isDefault ? AppColors.primarySoft : AppColors.surface,
                  borderRadius: BorderRadius.circular(14),
                  child: InkWell(
                    borderRadius: BorderRadius.circular(14),
                    onTap: () async {
                      if (widget.pickMode) {
                        await catalog.setDefaultAddress(a.id);
                        if (context.mounted) Navigator.pop(context, a);
                        return;
                      }
                    },
                    child: Padding(
                      padding: const EdgeInsets.all(14),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Icon(
                            a.isDefault ? Icons.location_on : Icons.location_on_outlined,
                            color: AppColors.primary,
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    Text(a.label, style: const TextStyle(fontWeight: FontWeight.w700)),
                                    if (a.isDefault) ...[
                                      const SizedBox(width: 8),
                                      Container(
                                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                        decoration: BoxDecoration(
                                          color: AppColors.primary,
                                          borderRadius: BorderRadius.circular(8),
                                        ),
                                        child: const Text(
                                          'DEFAULT',
                                          style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.w700),
                                        ),
                                      ),
                                    ],
                                  ],
                                ),
                                const SizedBox(height: 4),
                                Text(a.fullAddress, style: const TextStyle(color: AppColors.muted, fontSize: 13)),
                              ],
                            ),
                          ),
                          if (!widget.pickMode) ...[
                            IconButton(
                              onPressed: () => _showForm(existing: a),
                              icon: const Icon(Icons.edit_outlined, size: 20),
                            ),
                            IconButton(
                              onPressed: () async {
                                await catalog.deleteAddress(a.id);
                              },
                              icon: const Icon(Icons.delete_outline, size: 20, color: AppColors.popularRed),
                            ),
                          ],
                        ],
                      ),
                    ),
                  ),
                );
              },
            ),
    );
  }
}
