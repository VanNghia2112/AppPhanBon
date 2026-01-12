from ultralytics import YOLO

# Load mô hình JMUBEN
model = YOLO("runs_jmuben/yolov8n_cls_jmuben/weights/best.pt")

# Chọn ảnh bất kỳ để test
img_path = "E:/DACN 2/ai/data/coffee5/Healthy/C11P14H1.jpg"  # <-- thay bằng ảnh thật

# Dự đoán
results = model(img_path)

# In kết quả
for r in results:
    probs = r.probs  # xác suất từng class
    print("Predicted class:", r.names[probs.top1])
    print("All probabilities:", probs)
