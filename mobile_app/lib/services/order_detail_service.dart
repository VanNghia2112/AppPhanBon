import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/order_detail.dart';

class OrderDetailService {
  final String apiUrl = "http://10.0.2.2/Website-PhanBon/api/order_detail.php";

  Future<List<OrderDetail>> getDetails(int orderId) async {
    final response = await http.get(Uri.parse("$apiUrl?order_id=$orderId"));

    if (response.statusCode == 200) {
      final jsonData = json.decode(response.body);

      if (jsonData['success'] == true) {
        return (jsonData['data'] as List)
            .map((e) => OrderDetail.fromJson(e))
            .toList();
      }
    }
    return [];
  }
}
