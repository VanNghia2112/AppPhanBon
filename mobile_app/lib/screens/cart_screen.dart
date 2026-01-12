import 'package:flutter/material.dart';
import '../services/cart_service.dart';
import '../screens/checkout_screen.dart';
import '../models/theme/app_style.dart';
import '../helpers/price_helper.dart';

class CartScreen extends StatefulWidget {
  const CartScreen({super.key});

  @override
  State<CartScreen> createState() => _CartScreenState();
}

class _CartScreenState extends State<CartScreen> {
  @override
  Widget build(BuildContext context) {
    final cart = CartService.cart;

    return Scaffold(
      backgroundColor: AppStyle.background,
      appBar: AppBar(
        title: const Text("Giỏ hàng"),
        backgroundColor: AppStyle.primary,
        centerTitle: true,
        elevation: 0,
      ),
      body: cart.isEmpty
          ? const Center(child: Text("Giỏ hàng trống"))
          : Column(
              children: [
                Expanded(
                  child: ListView.builder(
                    padding: const EdgeInsets.only(top: 8),
                    itemCount: cart.length,
                    itemBuilder: (context, index) {
                      final item = cart[index];

                      return Container(
                        margin: const EdgeInsets.symmetric(
                            horizontal: 16, vertical: 8),
                        padding: const EdgeInsets.all(12),
                        decoration: AppStyle.card,
                        child: Row(
                          children: [
                            ClipRRect(
                              borderRadius: BorderRadius.circular(12),
                              child: Image.network(
                                item.product.image,
                                width: 64,
                                height: 64,
                                fit: BoxFit.cover,
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(item.product.name,
                                      style: AppStyle.title),
                                  const SizedBox(height: 4),
                                  Text(
  PriceHelper.format(double.parse(item.product.price)),
  style: AppStyle.price,
),
                                ],
                              ),
                            ),
                            Column(
                              children: [
                                _qtyButton(
                                  icon: Icons.add,
                                  onTap: () {
                                    setState(() {
                                      CartService.addToCart(item.product);
                                    });
                                  },
                                ),
                                Padding(
                                  padding:
                                      const EdgeInsets.symmetric(vertical: 4),
                                  child: Text(
                                    item.quantity.toString(),
                                    style: AppStyle.sectionTitle,
                                  ),
                                ),
                                _qtyButton(
                                  icon: Icons.remove,
                                  onTap: () {
                                    setState(() {
                                      CartService.decreaseQuantity(
                                          item.product.id);
                                    });
                                  },
                                ),
                              ],
                            )
                          ],
                        ),
                      );
                    },
                  ),
                ),

                /// FOOTER
                Container(
                  padding: const EdgeInsets.fromLTRB(20, 16, 20, 20),
                  decoration: const BoxDecoration(
                    color: Colors.white,
                    borderRadius:
                        BorderRadius.vertical(top: Radius.circular(24)),
                  ),
                  child: Column(
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text("Tổng cộng",
                              style: AppStyle.sectionTitle),
                          Text(
                            PriceHelper.format(CartService.totalPrice()),
                            style: AppStyle.price.copyWith(fontSize: 20),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),
                      SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          style: AppStyle.primaryButton.copyWith(
      foregroundColor: MaterialStateProperty.all(Colors.white),
      iconColor: MaterialStateProperty.all(Colors.white),       
    ),
                          onPressed: () {
                            Navigator.push(
                              context,
                              MaterialPageRoute(
                                builder: (_) => const CheckoutScreen(),
                              ),
                            );
                          },
                          child: const Text(
                            "Tiến hành đặt hàng",
                            style: TextStyle(fontSize: 16),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
    );
  }

  Widget _qtyButton({required IconData icon, required VoidCallback onTap}) {
    return InkWell(
      onTap: onTap,
      child: Container(
        width: 32,
        height: 32,
        decoration: BoxDecoration(
          color: AppStyle.primary.withOpacity(0.1),
          borderRadius: BorderRadius.circular(8),
        ),
        child: Icon(icon, size: 18, color: AppStyle.primary),
      ),
    );
  }
}
