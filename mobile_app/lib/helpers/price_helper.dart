import 'package:intl/intl.dart';

class PriceHelper {
  static String format(num price) {
    final formatter = NumberFormat('#,###', 'vi_VN');
    return '${formatter.format(price)} ₫';
  }
}
