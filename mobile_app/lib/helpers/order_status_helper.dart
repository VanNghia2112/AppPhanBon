import 'package:flutter/material.dart';

class OrderStatusHelper {
  /// Chuẩn hóa status (tránh null, chữ hoa/thường)
  static String _normalize(String? status) {
    return status?.toLowerCase().trim() ?? "";
  }

  /// Text hiển thị
  static String getText(String status) {
    switch (_normalize(status)) {
      case "pending":
      case "processing":
        return "Đang xử lý";

      case "shipping":
      case "delivering":
        return "Đang giao";

      case "completed":
      case "done":
        return "Hoàn thành";

      case "cancelled":
      case "canceled":
        return "Đã hủy";

      default:
        return "Chờ xử lý";
    }
  }

  /// Màu sắc
  static Color getColor(String status) {
    switch (_normalize(status)) {
      case "pending":
      case "processing":
        return Colors.orange;

      case "shipping":
      case "delivering":
        return Colors.blue;

      case "completed":
      case "done":
        return Colors.green;

      case "cancelled":
      case "canceled":
        return Colors.red;

      default:
        return Colors.grey;
    }
  }
}
