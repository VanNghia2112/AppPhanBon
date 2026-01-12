from ultralytics import YOLO

model = YOLO("runs_jmuben/yolov8n_cls_jmuben/weights/last.pt")

model.train(
    data="E:/DACN 2/ai/data",     # CHỈ đặt thư mục gốc data
    epochs=50,
    imgsz=224,
    batch=32,
    project="runs_jmuben",
    name="yolov8n_cls_jmuben",
    resume=True
)
