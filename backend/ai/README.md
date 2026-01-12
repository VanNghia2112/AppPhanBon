# Coffee Leaf Disease API (Starter)

This is a **production-ready starter** for a FastAPI service that classifies coffee leaf diseases and returns a **fertilizer suggestion** stub you can customize.

## What you get
- `FastAPI` app with `/predict` endpoint (multipart image upload).
- PyTorch inference pipeline (CPU by default).
- Pluggable label set via `models/labels.txt`.
- Fertilizer recommender **rule stub** in `utils/recommender.py`.
- Training scaffold in `scripts/train.py` (folder-based datasets).
- `Dockerfile` and `requirements.txt`.

## Quickstart
```bash
# 1) Create env
python -m venv .venv && . .venv/bin/activate  # on Windows: .venv\Scripts\activate

# 2) Install
pip install -r requirements.txt

# 3) Run API
uvicorn main:app --reload ------ uvicorn main:app --host 127.0.0.1 --port 9000
# Swagger at: http://127.0.0.1:8000/docs
```

## Model
Place your trained PyTorch weights at `models/model.pt` and your labels at `models/labels.txt` (one class per line).

## Dataset options
- **JMuBEN / JMuBEN2 / BRACOL datasets** (Arabica; public research datasets).
- **RoCoLe** (Robusta; real-world mobile photos).
- **Kaggle: Coffee Leaf Diseases** (multiple curated sets).

Use `scripts/train.py` to fine-tune a pretrained backbone (e.g., ResNet18) with folder structure:
```
data/
  train/Healthy/...
  train/Rust/...
  val/Healthy/...
  val/Rust/...
```
