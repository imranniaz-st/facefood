class AddressModel {
  final int id;
  final String label;
  final String line1;
  final String? line2;
  final String city;
  final String? area;
  final double? latitude;
  final double? longitude;
  final bool isDefault;
  final String fullAddress;

  AddressModel({
    required this.id,
    required this.label,
    required this.line1,
    this.line2,
    required this.city,
    this.area,
    this.latitude,
    this.longitude,
    this.isDefault = false,
    required this.fullAddress,
  });

  factory AddressModel.fromJson(Map<String, dynamic> json) {
    return AddressModel(
      id: json['id'] as int,
      label: json['label'] as String? ?? 'Home',
      line1: json['line1'] as String,
      line2: json['line2'] as String?,
      city: json['city'] as String? ?? 'Karachi',
      area: json['area'] as String?,
      latitude: (json['latitude'] as num?)?.toDouble(),
      longitude: (json['longitude'] as num?)?.toDouble(),
      isDefault: json['is_default'] == true,
      fullAddress: json['full_address'] as String? ?? json['line1'] as String,
    );
  }
}
