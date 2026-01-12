import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'login_screen.dart';

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({super.key});

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final userCtrl = TextEditingController();
  final passCtrl = TextEditingController();
  final nameCtrl = TextEditingController();
  final phoneCtrl = TextEditingController();
  bool loading = false;

  Future<void> register() async {
    setState(() => loading = true);

    final url = Uri.parse("http://10.0.2.2/Website-PhanBon/api/register.php");

    final res = await http.post(url, body: {
      "username": userCtrl.text,
      "password": passCtrl.text,
      "fullname": nameCtrl.text,
      "phone": phoneCtrl.text,
    });

    final data = json.decode(res.body);
    setState(() => loading = false);

    if (data["success"]) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Đăng ký thành công!")),
      );

      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => const LoginScreen()),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(data["message"])),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text("Đăng ký"), backgroundColor: Colors.green),
      body: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            TextField(controller: nameCtrl, decoration: InputDecoration(labelText: "Họ tên")),
            TextField(controller: phoneCtrl, decoration: InputDecoration(labelText: "Số điện thoại")),
            TextField(controller: userCtrl, decoration: InputDecoration(labelText: "Tên đăng nhập")),
            TextField(controller: passCtrl, obscureText: true, decoration: InputDecoration(labelText: "Mật khẩu")),
            SizedBox(height: 20),
            ElevatedButton(
              onPressed: loading ? null : register,
              style: ElevatedButton.styleFrom(backgroundColor: Colors.green),
              child: loading
                  ? CircularProgressIndicator(color: Colors.white)
                  : Text("Đăng ký"),
            ),
          ],
        ),
      ),
    );
  }
}
