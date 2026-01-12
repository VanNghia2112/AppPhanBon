import '../models/cart_item.dart';
import '../models/product.dart';

class CartService {
  static final List<CartItem> _cart = [];

  static List<CartItem> get cart => _cart;

  static void addToCart(Product product) {
    final index = _cart.indexWhere((item) => item.product.id == product.id);

    if (index >= 0) {
      _cart[index].quantity++;
    } else {
      _cart.add(CartItem(product: product));
    }
  }

  static void removeFromCart(String id) {
    _cart.removeWhere((item) => item.product.id == id);
  }

  static void decreaseQuantity(String id) {
    final index = _cart.indexWhere((item) => item.product.id == id);
    if (index >= 0) {
      if (_cart[index].quantity > 1) {
        _cart[index].quantity--;
      } else {
        _cart.removeAt(index);
      }
    }
  }

  static double totalPrice() {
    double sum = 0;
    for (var item in _cart) {
      sum += double.parse(item.product.price) * item.quantity;
    }
    return sum;
  }

  static void clearCart() {
    _cart.clear();
  }
  
  static void clear() {
    cart.clear();
  }
}