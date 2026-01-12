import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/theme/app_style.dart';
import 'login_screen.dart';
import 'order_list_screen.dart';
import 'edit_profile_screen.dart';


class AccountScreen extends StatelessWidget {
  const AccountScreen({super.key});

  Future<Map<String, String>> loadUser() async {
    final prefs = await SharedPreferences.getInstance();
    return {
      "username": prefs.getString("username") ?? "",
      "fullname": prefs.getString("fullname") ?? "",
      "phone": prefs.getString("phone") ?? "",
      "role": prefs.getString("role") ?? "user",
    };
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppStyle.background,
      appBar: AppBar(
        title: const Text("Tài khoản"),
        backgroundColor: AppStyle.primary,
        elevation: 0,
        centerTitle: true,
      ),
      body: FutureBuilder<Map<String, String>>(
        future: loadUser(),
        builder: (context, snapshot) {
          if (!snapshot.hasData) {
            return const Center(child: CircularProgressIndicator());
          }

          final u = snapshot.data!;
          return Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              children: [
                // ===== USER CARD =====
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(20),
                  decoration: AppStyle.card,
                  child: Column(
                    children: [
                      const CircleAvatar(
                        radius: 36,
                        backgroundColor: AppStyle.primary,
                        child: Icon(Icons.person,
                            size: 36, color: Colors.white),
                      ),
                      const SizedBox(height: 12),
                      Text(u["fullname"]!,
                          style: AppStyle.sectionTitle),
                      const SizedBox(height: 4),
                      Text(u["username"]!, style: AppStyle.normal),
                      const SizedBox(height: 12),
                      Divider(),
                      ListTile(
                        leading: const Icon(Icons.phone),
                        title: const Text("Số điện thoại"),
                        subtitle: Text(u["phone"]!),
                      ),
                      const Divider(),
                      ListTile(
  leading: Icon(Icons.receipt_long, color: AppStyle.primary),
  title: const Text("Đơn hàng của tôi"),
  subtitle: const Text("Xem các đơn đã đặt"),
  trailing: const Icon(Icons.arrow_forward_ios, size: 16),
  onTap: () {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => const OrderListScreen(),
      ),
    );
  },
),
ListTile(
  leading: const Icon(Icons.edit),
  title: const Text("Chỉnh sửa thông tin"),
  trailing: const Icon(Icons.chevron_right),
  onTap: () {
    Navigator.push(
      context,
      MaterialPageRoute(builder: (_) => const EditProfileScreen()),
    );
  },
),
                    ],
                  ),
                ),

                const Spacer(),

                // ===== LOGOUT =====
                ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.red,
                    padding:
                        const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14),
                    ),
                  ),
                  onPressed: () async {
                    final prefs =
                        await SharedPreferences.getInstance();
                    await prefs.clear();

                    Navigator.pushAndRemoveUntil(
                      context,
                      MaterialPageRoute(
                          builder: (_) => const LoginScreen()),
                      (route) => false,
                    );
                  },
                  child: const Text(
                    "Đăng xuất",
                    style: TextStyle(fontSize: 16),
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
