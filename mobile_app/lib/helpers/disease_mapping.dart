import 'package:flutter/material.dart';

class DiseaseInfo {
  final String key;
  final String title;
  final String description;
  final List<String> solutions;
  final Color color;

  DiseaseInfo({
    required this.key,
    required this.title,
    required this.description,
    required this.solutions,
    required this.color,
  });
}

class DiseaseMapping {
  static DiseaseInfo fromClass(String disease) {
    switch (disease.toLowerCase()) {

      case "healthy":
        return DiseaseInfo(
          key: "healthy",
          title: "Lá khỏe mạnh",
          description:
              "Lá khỏe mạnh, không phát hiện dấu hiệu bệnh.",
          solutions: [
            "Không cần điều trị",
            "Bón phân hữu cơ định kỳ",
            "NPK 20-20-15",
          ],
          color: Colors.green,
        );

      case "rust":
        return DiseaseInfo(
          key: "rust",
          title: "Rust (Bệnh gỉ sắt)",
          description:
              "Bệnh gỉ sắt khiến lá xuất hiện các đốm nâu đỏ, lá vàng, rụng sớm và làm giảm năng suất nghiêm trọng.",
          solutions: [
            "Cắt bỏ và tiêu hủy lá bệnh",
            "Phun thuốc đặc trị bệnh gỉ sắt",
            "Bón phân cân đối, tránh dư đạm",
          ],
          color: Colors.red,
        );

      case "leaf_spot":
        return DiseaseInfo(
          key: "leaf_spot",
          title: "Leaf Spot (Đốm lá)",
          description:
              "Bệnh đốm lá gây ra các vết đốm nâu hoặc đen trên lá, làm giảm khả năng quang hợp của cây.",
          solutions: [
            "Loại bỏ lá bị nhiễm bệnh",
            "Giữ vườn thông thoáng",
            "Sử dụng thuốc phòng trị nấm",
          ],
          color: Colors.orange,
        );

      case "miner":
        return DiseaseInfo(
          key: "miner",
          title: "Leaf Miner (Sâu đục lá)",
          description:
              "Sâu đục lá tạo các đường ngoằn ngoèo trên bề mặt lá, ảnh hưởng nghiêm trọng đến sinh trưởng.",
          solutions: [
            "Thu gom lá bị hại",
            "Dùng bẫy sinh học",
            "Phun thuốc sinh học phòng trừ sâu",
          ],
          color: Colors.deepOrange,
        );

      case "phoma":
        return DiseaseInfo(
          key: "phoma",
          title: "Phoma (Thối lá)",
          description:
              "Bệnh thối lá do nấm gây ra, thường xuất hiện trong điều kiện ẩm ướt kéo dài.",
          solutions: [
            "Cải thiện thoát nước",
            "Phun thuốc trị nấm Phoma",
            "Không tưới quá ẩm",
          ],
          color: Colors.brown,
        );

      default:
        return DiseaseInfo(
          key: "unknown",
          title: disease,
          description:
              "Phát hiện dấu hiệu bất thường trên lá. Cần theo dõi thêm.",
          solutions: [
            "Theo dõi tình trạng cây",
            "Tham khảo chuyên gia nông nghiệp",
          ],
          color: Colors.grey,
        );
    }
  }
}
