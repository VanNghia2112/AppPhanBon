import 'package:flutter/material.dart';

class AppStyle {
  // ===== COLORS =====
  static const Color primary = Color(0xFF2E7D32);
  static const Color accent = Color(0xFFFF9800);
  static const Color background = Color(0xFFF2F5F2);
  static const Color textDark = Color(0xFF1B1B1B);
  static const Color textGrey = Color(0xFF6B6B6B);

  // ===== TEXT =====
  static const TextStyle title = TextStyle(
    fontSize: 16,
    fontWeight: FontWeight.w600,
    color: textDark,
  );

  static const TextStyle sectionTitle = TextStyle(
    fontSize: 18,
    fontWeight: FontWeight.bold,
    color: textDark,
  );

  static const TextStyle price = TextStyle(
    fontSize: 18,
    fontWeight: FontWeight.bold,
    color: Color(0xFFD32F2F),
  );

  static const TextStyle normal = TextStyle(
    fontSize: 14,
    color: textGrey,
  );

  // ===== CARD =====
  static BoxDecoration card = BoxDecoration(
    color: Colors.white,
    borderRadius: BorderRadius.circular(16),
    boxShadow: [
      BoxShadow(
        color: Colors.black.withOpacity(0.06),
        blurRadius: 12,
        offset: const Offset(0, 6),
      ),
    ],
  );

  // ===== BUTTON =====
  static ButtonStyle primaryButton = ElevatedButton.styleFrom(
    backgroundColor: primary,
    elevation: 3,
    padding: const EdgeInsets.symmetric(vertical: 16),
    shape: RoundedRectangleBorder(
      borderRadius: BorderRadius.circular(14),
    ),
  );

  static ButtonStyle accentButton = ElevatedButton.styleFrom(
    backgroundColor: accent,
    elevation: 3,
    padding: const EdgeInsets.symmetric(vertical: 16),
    shape: RoundedRectangleBorder(
      borderRadius: BorderRadius.circular(14),
    ),
  );
}
