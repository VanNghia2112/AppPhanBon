import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import 'product_list_screen.dart';
import 'register_screen.dart'; // 👉 nhớ import

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final phoneCtrl = TextEditingController();
  final passCtrl = TextEditingController();
  bool loading = false;

  Future<void> login() async {
    setState(() => loading = true);

    final url = Uri.parse("http://10.0.2.2/Website-PhanBon/api/login.php");

    final res = await http.post(url, body: {
      "username": phoneCtrl.text,
      "password": passCtrl.text,
    });

    final data = json.decode(res.body);

    if (data["success"]) {
      SharedPreferences prefs = await SharedPreferences.getInstance();
      prefs.setString("phone", data["phone"]);
      prefs.setString("username", data["username"]);
      prefs.setString("fullname", data["fullname"]);
      prefs.setString("role", data["role"]);

      Navigator.pushReplacement(
        context,
        MaterialPageRoute(builder: (_) => ProductListScreen()),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text("Sai tài khoản hoặc mật khẩu")),
      );
    }

    setState(() => loading = false);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text("Đăng nhập"),
        backgroundColor: Colors.green,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            TextField(
              controller: phoneCtrl,
              decoration: InputDecoration(labelText: "Tên đăng nhập"),
            ),
            TextField(
              controller: passCtrl,
              obscureText: true,
              decoration: InputDecoration(labelText: "Mật khẩu"),
            ),

            SizedBox(height: 20),

            ElevatedButton(
              onPressed: login,
              child: loading
                  ? CircularProgressIndicator()
                  : Text("Đăng nhập"),
            ),

            // 👉👉 Nút Đăng ký HERE
            TextButton(
              onPressed: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => RegisterScreen()),
                );
              },
              child: Text(
                "Chưa có tài khoản? Đăng ký ngay",
                style: TextStyle(color: Colors.blue),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
