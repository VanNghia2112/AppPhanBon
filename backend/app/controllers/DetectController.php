<?php

class DetectController {

    public function index() {
        // TỰ ĐỘNG KHỞI ĐỘNG AI (nếu chưa chạy)
        $this->ensureAiServerRunning();

        $result = null;

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["image"])) {

    // Lưu file vào uploads
    $fileTmp = $_FILES["image"]["tmp_name"];
    $fileName = time() . "_" . $_FILES["image"]["name"];
    $savePath = __DIR__ . "/../../uploads/" . $fileName;

    move_uploaded_file($fileTmp, $savePath);

    // Gọi AI đúng 1 lần
    $result = $this->callAI($savePath);

    // Chuẩn bị đường dẫn ảnh cho view
    $uploadedImage = "/Website-PhanBon/uploads/" . $fileName;
}
        $uploadedImage = $uploadedImage ?? null;
        require __DIR__ . '/../views/product/detect.php';
    }


    // KIỂM TRA FASTAPI CÓ ĐANG CHẠY KHÔNG
    private function isAPIAlive() {
        $ch = curl_init("http://127.0.0.1:8000/docs");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $code === 200;
    }


    // TỰ ĐỘNG KHỞI ĐỘNG AIMODEL
    private function ensureAiServerRunning() {

        if ($this->isAPIAlive()) return;

        $bat = 'E:\\Xamppp\\htdocs\\website-phanbon\\ai\\run_ai_hidden.bat';

        // chạy ẩn không bật CMD
        pclose(popen("start /B cmd /C \"$bat\"", "r"));

        sleep(2); // đợi AI khởi động
    }


    // GỌI API DỰ ĐOÁN
    private function callAI($imagePath) {
    $url = "http://127.0.0.1:8000/predict";

    $file = curl_file_create($imagePath);
    $payload = ["file" => $file];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        return ["class" => "Lỗi API", "confidence" => 0];
    }

    $data = json_decode($response, true);

    // Tránh lỗi undefined index
    return $data["result"] ?? ["class" => "API trả sai format", "confidence" => 0];
}
}
