class UserModel {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final String? avatarUrl;
  final bool isPremium;
  final String? location;

  UserModel({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    this.avatarUrl,
    this.isPremium = false,
    this.location,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: (json['id'] as num).toInt(),
      name: json['name'] as String,
      email: json['email'] as String,
      phone: json['phone'] as String?,
      avatarUrl: json['avatar_url'] as String?,
      isPremium: json['is_premium'] == true,
      location: json['location'] as String?,
    );
  }
}
