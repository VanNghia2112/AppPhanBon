import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;

class AiService {
  final String apiUrl = "http://10.0.2.2:9000/predict";

  Future<Map<String, dynamic>> predict(File imageFile) async {
    var request = http.MultipartRequest("POST", Uri.parse(apiUrl));

    request.files.add(
      await http.MultipartFile.fromPath('file', imageFile.path),
    );

    var response = await request.send();

    if (response.statusCode != 200) {
      throw Exception("API Error: ${response.statusCode}");
    }

    final jsonString = await response.stream.bytesToString();
    final data = json.decode(jsonString);

    return data;
  }
}
