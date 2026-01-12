import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/theme/app_style.dart';

class EditProfileScreen extends StatefulWidget {
  const EditProfileScreen({super.key});

  @override
  State<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends State<EditProfileScreen> {
  final nameCtrl = TextEditingController();
  final phoneCtrl = TextEditingController();

  int? userId;
  bool saving = false;

  @override
  void initState() {
    super.initState();
    _loadUser();
  }

  // ===== LOAD USER FROM LOCAL =====
  Future<void> _loadUser() async {
    final prefs = await SharedPreferences.getInstance();
    setState(() {
      userId = prefs.getInt("user_id");
      nameCtrl.text = prefs.getString("user_name") ?? "";
      phoneCtrl.text = prefs.getString("user_phone") ?? "";
    });
  }

  // ===== SAVE PROFILE =====
  Future<void> _saveProfile() async {
    if (nameCtrl.text.isEmpty || phoneCtrl.text.isEmpty) {
      _showError("Vui lòng nhập đầy đủ thông tin");
      return;
    }

    if (userId == null) {
      _showError("Không xác định được người dùng");
      return;
    }

    setState(() => saving = true);

    final url = Uri.parse(
      "http://10.0.2.2/Website-PhanBon/api/update_profile.php",
    );

    try {
      final res = await http.post(
        url,
        headers: {"Content-Type": "application/json"},
        body: jsonEncode({
          "user_id": userId,          // 🔑 BẮT BUỘC
          "name": nameCtrl.text,
          "phone": phoneCtrl.text,
        }),
      );

      final json = jsonDecode(res.body);

      if (json["success"] == true) {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString("user_name", nameCtrl.text);
        await prefs.setString("user_phone", phoneCtrl.text);

        Navigator.pop(context, true);
      } else {
        _showError("Cập nhật thất bại");
      }
    } catch (e) {
      _showError("Không kết nối được server");
    }

    setState(() => saving = false);
  }

  void _showError(String msg) {
    ScaffoldMessenger.of(context)
        .showSnackBar(SnackBar(content: Text(msg)));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Chỉnh sửa thông tin"),
        backgroundColor: AppStyle.primary,
        centerTitle: true,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            TextField(
              controller: nameCtrl,
              decoration: const InputDecoration(labelText: "Họ tên"),
            ),
            const SizedBox(height: 12),
            TextField(
              controller: phoneCtrl,
              keyboardType: TextInputType.phone,
              decoration:
                  const InputDecoration(labelText: "Số điện thoại"),
            ),
            const SizedBox(height: 24),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                style: AppStyle.accentButton,
                onPressed: saving ? null : _saveProfile,
                child: saving
                    ? const SizedBox(
                        height: 22,
                        width: 22,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          color: Colors.white,
                        ),
                      )
                    : const Text("Lưu thay đổi"),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
