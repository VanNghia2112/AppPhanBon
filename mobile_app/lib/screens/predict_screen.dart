import 'dart:io';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../services/ai_service.dart';

/// =======================
/// MODEL MAPPING BỆNH
/// =======================
class DiseaseInfo {
  final String title;
  final String description;
  final List<String> solutions;
  final Color color;

  DiseaseInfo({
    required this.title,
    required this.description,
    required this.solutions,
    required this.color,
  });
}

DiseaseInfo getDiseaseInfo(String disease) {
  switch (disease.toLowerCase()) {
    case "healthy":
      return DiseaseInfo(
        title: "Lá khỏe mạnh",
        description: "Lá khỏe mạnh, không phát hiện dấu hiệu bệnh.",
        solutions: [
          "Không cần điều trị",
          "Bón phân hữu cơ định kỳ",
          "NPK 20-20-15",
        ],
        color: Colors.green,
      );

    case "rust":
      return DiseaseInfo(
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

/// =======================
/// SCREEN
/// =======================
class PredictScreen extends StatefulWidget {
  const PredictScreen({super.key});

  @override
  State<PredictScreen> createState() => _PredictScreenState();
}

class _PredictScreenState extends State<PredictScreen> {
  File? _image;
  String? _disease;
  double? _confidence;

  final picker = ImagePicker();
  final aiService = AiService();

  Future pickImage() async {
    final picked = await picker.pickImage(source: ImageSource.gallery);
    if (picked != null) {
      setState(() {
        _image = File(picked.path);
        _disease = null;
        _confidence = null;
      });
    }
  }

  Future detect() async {
    if (_image == null) return;

    final response = await aiService.predict(_image!);
    final result = response["result"];

    setState(() {
      _disease = result["class"];
      _confidence = (result["confidence"] as num).toDouble();
    });
  }

  @override
  Widget build(BuildContext context) {
    final info = _disease != null ? getDiseaseInfo(_disease!) : null;

    return Scaffold(
      appBar: AppBar(
        title: const Text("AI Nhận diện bệnh"),
        backgroundColor: Colors.green,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: [
            /// ẢNH
            Card(
              elevation: 4,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
              ),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(16),
                child: _image == null
                    ? Container(
                        height: 200,
                        color: Colors.grey[200],
                        alignment: Alignment.center,
                        child: const Text("Chưa chọn ảnh"),
                      )
                    : Image.file(
                        _image!,
                        height: 220,
                        width: double.infinity,
                        fit: BoxFit.cover,
                      ),
              ),
            ),

            const SizedBox(height: 20),

            /// BUTTONS
            Row(
              children: [
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: pickImage,
                    icon: const Icon(Icons.image),
                    label: const Text("Chọn ảnh"),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.green,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: detect,
                    icon: const Icon(Icons.science),
                    label: const Text("Nhận diện"),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.orange,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                    ),
                  ),
                ),
              ],
            ),

            const SizedBox(height: 24),

            /// KẾT QUẢ
            if (info != null && _confidence != null)
              Card(
                elevation: 4,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Chip(
                        label: Text(
                          "Độ tin cậy: ${(_confidence! * 100).toStringAsFixed(2)}%",
                          style: const TextStyle(color: Colors.white),
                        ),
                        backgroundColor: Colors.redAccent,
                      ),
                      const SizedBox(height: 12),
                      Text(
                        info.title,
                        style: TextStyle(
                          fontSize: 22,
                          fontWeight: FontWeight.bold,
                          color: info.color,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(info.description),
                      const SizedBox(height: 16),
                      Text(
                        "Gợi ý xử lý & phân bón phù hợp:",
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          color: Colors.green[700],
                        ),
                      ),
                      const SizedBox(height: 10),
                      ...info.solutions.map((s) => Container(
                            margin: const EdgeInsets.only(bottom: 8),
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: Colors.green[50],
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Row(
                              children: [
                                const Icon(Icons.check_circle,
                                    color: Colors.green),
                                const SizedBox(width: 8),
                                Expanded(child: Text(s)),
                              ],
                            ),
                          )),
                    ],
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }
}
