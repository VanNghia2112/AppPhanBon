import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:http/http.dart' as http;
import '../services/cart_service.dart';
import '../models/cart_item.dart';
import '../models/theme/app_style.dart';
import '../helpers/price_helper.dart';

class CheckoutScreen extends StatefulWidget {
  const CheckoutScreen({super.key});

  @override
  State<CheckoutScreen> createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  // ===== FORM CONTROLLERS =====
  final nameCtrl = TextEditingController();
  final phoneCtrl = TextEditingController();
  final addressCtrl = TextEditingController();
  final noteCtrl = TextEditingController();

  String paymentMethod = "COD"; // COD | MOMO
  bool submitting = false;

  @override
  Widget build(BuildContext context) {
    final cartItems = CartService.cart;
    final total = CartService.totalPrice();

    // 👉 kiểm tra bàn phím
    final keyboardOpen =
        MediaQuery.of(context).viewInsets.bottom > 0;

    return Scaffold(
      resizeToAvoidBottomInset: true,
      backgroundColor: AppStyle.background,
      appBar: AppBar(
        title: const Text("Thanh toán"),
        backgroundColor: AppStyle.primary,
        centerTitle: true,
        elevation: 0,
      ),
      body: SafeArea(
        child: Column(
          children: [
            Expanded(
              child: ListView(
                padding: const EdgeInsets.all(16),
                children: [
                  // ================= THÔNG TIN KHÁCH =================
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: AppStyle.card,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          "Thông tin nhận hàng",
                          style: AppStyle.sectionTitle,
                        ),
                        const SizedBox(height: 16),

                        TextField(
                          controller: nameCtrl,
                          decoration: const InputDecoration(
                            labelText: "Họ tên người nhận",
                          ),
                        ),
                        const SizedBox(height: 12),

                        TextField(
                          controller: phoneCtrl,
                          keyboardType: TextInputType.phone,
                          decoration: const InputDecoration(
                            labelText: "Số điện thoại",
                          ),
                        ),
                        const SizedBox(height: 12),

                        TextField(
                          controller: addressCtrl,
                          decoration: const InputDecoration(
                            labelText: "Địa chỉ giao hàng",
                          ),
                        ),
                        const SizedBox(height: 12),

                        TextField(
                          controller: noteCtrl,
                          decoration: const InputDecoration(
                            labelText: "Ghi chú (tuỳ chọn)",
                          ),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 24),

                  // ================= SẢN PHẨM =================
                  const Text("Sản phẩm",
                      style: AppStyle.sectionTitle),
                  const SizedBox(height: 12),

                  ...cartItems.map((p) {
                    return Container(
                      margin:
                          const EdgeInsets.only(bottom: 12),
                      padding: const EdgeInsets.all(12),
                      decoration: AppStyle.card,
                      child: Row(
                        mainAxisAlignment:
                            MainAxisAlignment.spaceBetween,
                        children: [
                          Column(
                            crossAxisAlignment:
                                CrossAxisAlignment.start,
                            children: [
                              Text(p.product.name,
                                  style: AppStyle.title),
                              const SizedBox(height: 4),
                              Text("Số lượng: ${p.quantity}",
                                  style: AppStyle.normal),
                            ],
                          ),
                          Text(
                            PriceHelper.format(
                              double.parse(p.product.price) *
                                  p.quantity,
                            ),
                            style: AppStyle.price,
                          ),
                        ],
                      ),
                    );
                  }),
                ],
              ),
            ),

            // ================= FOOTER (ẨN KHI GÕ) =================
            if (!keyboardOpen)
              Container(
                padding:
                    const EdgeInsets.fromLTRB(20, 16, 20, 20),
                decoration: const BoxDecoration(
                  color: Colors.white,
                  borderRadius:
                      BorderRadius.vertical(
                          top: Radius.circular(24)),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black12,
                      blurRadius: 10,
                      offset: Offset(0, -4),
                    )
                  ],
                ),
                child: Column(
                  crossAxisAlignment:
                      CrossAxisAlignment.start,
                  children: [
                    // ===== TỔNG TIỀN =====
                    Row(
                      mainAxisAlignment:
                          MainAxisAlignment.spaceBetween,
                      children: [
                        const Text("Tổng thanh toán",
                            style:
                                AppStyle.sectionTitle),
                        Text(
                          NumberFormat('#,###')
                                  .format(total) +
                              " đ",
                          style: AppStyle.price
                              .copyWith(fontSize: 20),
                        ),
                      ],
                    ),

                    const SizedBox(height: 12),
                    const Divider(),
                    const SizedBox(height: 8),

                    // ===== PHƯƠNG THỨC =====
                    const Text("Phương thức thanh toán",
                        style:
                            AppStyle.sectionTitle),
                    const SizedBox(height: 8),

                    Container(
                      decoration: BoxDecoration(
                        borderRadius:
                            BorderRadius.circular(14),
                        border: Border.all(
                            color:
                                Colors.grey.shade300),
                      ),
                      child: Column(
                        children: [
                          RadioListTile<String>(
                            value: "COD",
                            groupValue: paymentMethod,
                            onChanged: (v) =>
                                setState(() =>
                                    paymentMethod =
                                        v!),
                            title: const Text(
                                "Tiền mặt khi nhận hàng"),
                            secondary: const Icon(
                                Icons.payments),
                          ),
                          const Divider(height: 1),
                          RadioListTile<String>(
                            value: "MOMO",
                            groupValue: paymentMethod,
                            onChanged: (v) =>
                                setState(() =>
                                    paymentMethod =
                                        v!),
                            title: const Text(
                                "Ví MoMo (mô phỏng)"),
                            secondary: const Icon(
                                Icons
                                    .account_balance_wallet),
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 16),

                    // ===== NÚT =====
                    SizedBox(
                      width: double.infinity,
                      child: ElevatedButton(
                        style:
                            AppStyle.accentButton,
                        onPressed: submitting
                            ? null
                            : () async {
                                setState(() =>
                                    submitting = true);
                                final ok =
                                    await _submitOrder(
                                        cartItems,
                                        paymentMethod);
                                setState(() =>
                                    submitting = false);

                                if (ok) {
                                  ScaffoldMessenger.of(
                                          context)
                                      .showSnackBar(
                                    const SnackBar(
                                      content: Text(
                                          "Đặt hàng thành công!"),
                                    ),
                                  );
                                  CartService.clear();
                                  Navigator.pop(context);
                                }
                              },
                        child: submitting
                            ? const SizedBox(
                                height: 20,
                                width: 20,
                                child:
                                    CircularProgressIndicator(
                                  strokeWidth: 2,
                                  color: Colors.white,
                                ),
                              )
                            : const Text(
                                "Xác nhận thanh toán",
                                style: TextStyle(
                                    fontSize: 16),
                              ),
                      ),
                    ),
                  ],
                ),
              ),
          ],
        ),
      ),
    );
  }

  // ================= SUBMIT ORDER =================
  Future<bool> _submitOrder(
      List<CartItem> items, String method) async {
    if (items.isEmpty) {
      _showError("Giỏ hàng đang trống");
      return false;
    }

    if (nameCtrl.text.isEmpty ||
        phoneCtrl.text.isEmpty ||
        addressCtrl.text.isEmpty) {
      _showError("Vui lòng nhập đầy đủ thông tin");
      return false;
    }

    final url = Uri.parse(
        "http://10.0.2.2/Website-PhanBon/api/order.php");

    final body = {
      "fullname": nameCtrl.text,
      "phone": phoneCtrl.text,
      "address": addressCtrl.text,
      "note": noteCtrl.text,
      "payment_method": method,
      "items": items.map((i) {
        return {
          "product_id": i.product.id,
          "quantity": i.quantity,
        };
      }).toList(),
    };

    try {
      final res = await http.post(
        url,
        headers: {
          "Content-Type": "application/json"
        },
        body: jsonEncode(body),
      );

      final jsonRes = jsonDecode(res.body);
      return jsonRes["success"] == true;
    } catch (e) {
      _showError("Không kết nối được server");
      return false;
    }
  }

  void _showError(String msg) {
    ScaffoldMessenger.of(context)
        .showSnackBar(SnackBar(content: Text(msg)));
  }
}
