import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/theme/app_style.dart';
import '../helpers/price_helper.dart';
import 'order_detail_screen.dart';
import '../helpers/order_status_helper.dart';

class OrderListScreen extends StatefulWidget {
  const OrderListScreen({super.key});

  @override
  State<OrderListScreen> createState() => _OrderListScreenState();
}

class _OrderListScreenState extends State<OrderListScreen> {
  late Future<List<dynamic>> _orders;

  Future<List<dynamic>> fetchOrders() async {
    final prefs = await SharedPreferences.getInstance();
    final phone = prefs.getString("phone") ?? "";

    final url = Uri.parse(
      "http://10.0.2.2/Website-PhanBon/api/orders_by_phone.php?phone=$phone",
    );

    final res = await http.get(url);
    return jsonDecode(res.body);
  }

  @override
  void initState() {
    super.initState();
    _orders = fetchOrders();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppStyle.background,
      appBar: AppBar(
        title: const Text("Đơn hàng của tôi"),
        backgroundColor: AppStyle.primary,
        centerTitle: true,
        elevation: 0,
      ),
      body: FutureBuilder<List<dynamic>>(
        future: _orders,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (!snapshot.hasData || snapshot.data!.isEmpty) {
            return const Center(child: Text("Chưa có đơn hàng nào"));
          }

          final orders = snapshot.data!;

          return ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: orders.length,
            itemBuilder: (context, index) {
              final o = orders[index];
              final status = o["status"]?.toString() ?? "";

              return GestureDetector(
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => OrderDetailScreen(
                        orderId: int.parse(o["id"].toString()),
                      ),
                    ),
                  ).then((_) {
                    // refresh khi quay lại
                    setState(() {
                      _orders = fetchOrders();
                    });
                  });
                },
                child: Container(
                  margin: const EdgeInsets.only(bottom: 16),
                  padding: const EdgeInsets.all(16),
                  decoration: AppStyle.card,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // ===== MÃ ĐƠN + TRẠNG THÁI =====
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            "Đơn hàng #${o["id"]}",
                            style: AppStyle.sectionTitle,
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 12,
                              vertical: 6,
                            ),
                            decoration: BoxDecoration(
                              color: OrderStatusHelper
                                  .getColor(status)
                                  .withOpacity(0.15),
                              borderRadius: BorderRadius.circular(20),
                            ),
                            child: Text(
                              OrderStatusHelper.getText(status),
                              style: TextStyle(
                                color:
                                    OrderStatusHelper.getColor(status),
                                fontWeight: FontWeight.w600,
                                fontSize: 13,
                              ),
                            ),
                          ),
                        ],
                      ),

                      const SizedBox(height: 8),

                      // ===== NGÀY ĐẶT =====
                      Text(
                        "Ngày đặt: ${o["created_at"]}",
                        style: AppStyle.normal,
                      ),

                      const SizedBox(height: 8),

                      // ===== TỔNG TIỀN =====
                      Text(
                        PriceHelper.format(
                          double.parse(o["total"].toString()),
                        ),
                        style: AppStyle.price.copyWith(fontSize: 18),
                      ),
                    ],
                  ),
                ),
              );
            },
          );
        },
      ),
    );
  }
}
