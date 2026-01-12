from ultralytics import YOLO
from PIL import Image
import io

# Load model JMUBEN
model = YOLO("E:/DACN 2/ai/scripts/runs_jmuben/yolov8n_cls_jmuben/weights/best.pt")

# Danh sách tên class theo đúng thứ tự mô hình
CLASS_NAMES = ["BrownSpot", "Healthy", "Miner", "Phoma", "Rust"]


def predict_image(file_bytes: bytes):
    # Chuyển bytes → ảnh
    image = Image.open(io.BytesIO(file_bytes)).convert("RGB")

    # Dự đoán
    results = model(image)[0]

    # Lấy class top1
    top_class = CLASS_NAMES[results.probs.top1]
    confidence = float(results.probs.top1conf)

    return {
        "class": top_class,
        "confidence": confidence
    }
