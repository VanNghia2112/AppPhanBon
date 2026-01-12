import 'package:flutter/material.dart';
import '../models/product.dart';
import '../services/cart_service.dart';
import '../models/theme/app_style.dart';
import '../helpers/price_helper.dart';

class ProductDetailScreen extends StatelessWidget {
  final Product product;

  const ProductDetailScreen({super.key, required this.product});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppStyle.background,
      appBar: AppBar(
        title: Text(product.name),
        backgroundColor: AppStyle.primary,
        elevation: 0,
        centerTitle: true,
      ),
      body: Column(
        children: [
          Stack(
            children: [
              Image.network(
                product.image,
                height: 260,
                width: double.infinity,
                fit: BoxFit.cover,
              ),
              Container(
                height: 260,
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.bottomCenter,
                    end: Alignment.topCenter,
                    colors: [
                      Colors.black.withOpacity(0.6),
                      Colors.transparent,
                    ],
                  ),
                ),
              ),
            ],
          ),

          Expanded(
            child: Container(
              transform: Matrix4.translationValues(0, -24, 0),
              padding: const EdgeInsets.all(20),
              decoration: AppStyle.card,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(product.name,
                      style: AppStyle.sectionTitle),
                  const SizedBox(height: 8),
                  Text(
  PriceHelper.format(double.parse(product.price)),
  style: AppStyle.price.copyWith(fontSize: 22),
),
                  const SizedBox(height: 16),
                  Text(product.description,
                      style: AppStyle.normal),
                  const Spacer(),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      style: AppStyle.primaryButton.copyWith(
      foregroundColor: MaterialStateProperty.all(Colors.white), // chữ + icon
      iconColor: MaterialStateProperty.all(Colors.white),       // nếu bạn dùng Flutter mới
    ),
                      icon: const Icon(Icons.add_shopping_cart),
                      label: const Text(
                        "Thêm vào giỏ hàng",
                        style: TextStyle(fontSize: 16),
                      ),
                      onPressed: () {
                        CartService.addToCart(product);
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(
                            content: Text(
                                "Đã thêm ${product.name} vào giỏ hàng"),
                          ),
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
