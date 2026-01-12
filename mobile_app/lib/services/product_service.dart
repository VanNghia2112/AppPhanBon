import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/product.dart';

class ProductService {
  final String apiUrl = "http://10.0.2.2/Website-PhanBon/api/products.php";

  Future<List<Product>> getProducts() async {
  final response = await http.get(Uri.parse(apiUrl));

  if (response.statusCode == 200) {
    final jsonBody = json.decode(response.body);

    List data = jsonBody['data']; // Lấy đúng mảng sản phẩm

    return data.map((item) => Product.fromJson(item)).toList();
  } else {
    throw Exception("Failed to load products");
  }
}
}
