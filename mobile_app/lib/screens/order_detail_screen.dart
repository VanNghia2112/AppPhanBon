import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import '../models/theme/app_style.dart';
import '../helpers/price_helper.dart';

class OrderDetailScreen extends StatefulWidget {
  final int orderId;
  const OrderDetailScreen({super.key, required this.orderId});

  @override
  State<OrderDetailScreen> createState() => _OrderDetailScreenState();
}

class _OrderDetailScreenState extends State<OrderDetailScreen> {
  late Future<Map<String, dynamic>> _data;

  /* =======================
      LẤY CHI TIẾT ĐƠN
  ======================== */
  Future<Map<String, dynamic>> fetchDetail() async {
    final url = Uri.parse(
      "http://10.0.2.2/Website-PhanBon/api/order_detail.php?order_id=${widget.orderId}",
    );

    final res = await http.get(url);
    final json = jsonDecode(res.body);

    if (json is! Map) {
      return {"order": {}, "items": []};
    }

    return {
      "order": json["order"] ?? {},
      "items": json["items"] ?? [],
    };
  }

  /* =======================
        HỦY ĐƠN HÀNG
  ======================== */
  Future<bool> _cancelOrder() async {
    final url = Uri.parse(
      "http://10.0.2.2/Website-PhanBon/api/cancel_order.php",
    );

    final res = await http.post(
      url,
      body: {
        "order_id": widget.orderId.toString(),
      },
    );

    final json = jsonDecode(res.body);
    return json["success"] == true;
  }

  @override
  void initState() {
    super.initState();
    _data = fetchDetail();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppStyle.background,
      appBar: AppBar(
        title: Text("Chi tiết đơn #${widget.orderId}"),
        backgroundColor: AppStyle.primary,
        centerTitle: true,
        elevation: 0,
      ),
      body: FutureBuilder<Map<String, dynamic>>(
        future: _data,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          }

          if (!snapshot.hasData || snapshot.data == null) {
            return const Center(child: Text("Không có dữ liệu"));
          }

          final order =
              snapshot.data!["order"] as Map<String, dynamic>;
          final items =
              snapshot.data!["items"] as List<dynamic>;

          return ListView(
            padding: const EdgeInsets.all(16),
            children: [
              /* =======================
                    THÔNG TIN ĐƠN
              ======================== */
              Container(
                padding: const EdgeInsets.all(16),
                decoration: AppStyle.card,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text("Thông tin đơn hàng",
                        style: AppStyle.sectionTitle),
                    const SizedBox(height: 12),

                    _row("Khách hàng", order["name"]),
                    _row("SĐT", order["phone"]),
                    _row("Địa chỉ", order["address"]),
                    _row("Thanh toán", order["payment_method"]),
                    _row("Trạng thái", order["status"]),
                    _row("Ngày đặt", order["created_at"]),

                    const Divider(height: 24),

                    Text(
                      PriceHelper.format(
                        double.tryParse(
                                order["total"]?.toString() ?? "0") ??
                            0,
                      ),
                      style:
                          AppStyle.price.copyWith(fontSize: 20),
                    ),

                    /* ===== NÚT HỦY ĐƠN ===== */
                    if (order["status"] == "pending") ...[
                      const SizedBox(height: 12),
                      ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.red,
                          minimumSize:
                              const Size.fromHeight(44),
                        ),
                        onPressed: () async {
                          final ok = await _cancelOrder();
                          if (ok && mounted) {
                            Navigator.pop(context, true);
                          }
                        },
                        child: const Text("Hủy đơn hàng"),
                      ),
                    ],
                  ],
                ),
              ),

              const SizedBox(height: 16),

              /* =======================
                    SẢN PHẨM
              ======================== */
              Text("Sản phẩm", style: AppStyle.sectionTitle),
              const SizedBox(height: 8),

              if (items.isEmpty)
                const Text("Không có sản phẩm")
              else
                ...items.map((i) {
                  final String name =
                      i["product_name"]?.toString() ?? "";
                  final int qty =
                      int.tryParse(i["quantity"].toString()) ??
                          0;
                  final double price =
                      double.tryParse(i["price"].toString()) ??
                          0;

                  return Container(
                    margin:
                        const EdgeInsets.only(bottom: 12),
                    padding: const EdgeInsets.all(16),
                    decoration: AppStyle.card,
                    child: Row(
                      mainAxisAlignment:
                          MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment:
                              CrossAxisAlignment.start,
                          children: [
                            Text(name,
                                style: AppStyle.title),
                            Text("SL: $qty",
                                style: AppStyle.normal),
                          ],
                        ),
                        Text(
                          PriceHelper.format(price * qty),
                          style: AppStyle.price,
                        ),
                      ],
                    ),
                  );
                }).toList(),
            ],
          );
        },
      ),
    );
  }

  Widget _row(String label, dynamic value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: Text(
        "$label: ${value ?? ''}",
        style: AppStyle.normal,
      ),
    );
  }
}
