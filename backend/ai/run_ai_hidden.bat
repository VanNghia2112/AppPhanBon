@echo off
cd /d "E:\Xamppp\htdocs\website-phanbon\ai\app"
call ..\.venv\Scripts\activate
uvicorn main:app --host 127.0.0.1 --port 8000
