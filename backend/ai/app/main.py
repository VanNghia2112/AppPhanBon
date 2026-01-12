from fastapi import FastAPI, UploadFile, File
from fastapi.middleware.cors import CORSMiddleware
from predict import predict_image

app = FastAPI()

# Cho phép frontend call API
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


@app.get("/")
def home():
    return {"message": "JMUBEN Coffee Disease API is running!"}


@app.post("/predict")
async def predict(file: UploadFile = File(...)):
    # Đọc file ảnh
    bytes_data = await file.read()

    # Gửi sang mô hình dự đoán
    result = predict_image(bytes_data)

    return {
        "filename": file.filename,
        "result": result
    }
