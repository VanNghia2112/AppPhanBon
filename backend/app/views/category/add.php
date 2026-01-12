<?php include 'app/views/shares/header.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Danh Mục Mới</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap');

        body {
            background-color: #f8f9fa;
            font-family: 'Be Vietnam Pro', sans-serif;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 3rem auto;
            padding: 0 1rem;
        }

        /* ✅ Header giống các trang khác (xanh lá, chữ trắng, bo góc) */
        .page-header-green {
            background-color: #00A74F;
            border-radius: 16px;
            padding: 3rem 2rem;
            margin-bottom: 2.5rem;
            text-align: center;
            color: white;
            box-shadow: 0 10px 25px rgba(0, 167, 79, 0.25);
        }

        .page-header-green h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-header-green p {
            opacity: 0.95;
            font-size: 1.1rem;
            margin: 0;
        }

        /* Form Card */
        .form-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 25px;
            border-bottom: 3px solid #00A74F;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #2d3748;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .required-mark {
            color: #dc3545;
            margin-left: 4px;
        }

        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: white;
            color: #2d3748;
        }

        .form-control:focus {
            outline: none;
            border-color: #00A74F;
            box-shadow: 0 0 0 4px rgba(0, 167, 79, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
            font-family: inherit;
        }

        .input-helper {
            font-size: 13px;
            color: #718096;
            margin-top: 6px;
        }

        .char-count {
            text-align: right;
            font-size: 12px;
            color: #a0aec0;
            margin-top: 4px;
        }

        /* Buttons */
        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 30px;
            justify-content: flex-end;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-success {
            background: #00A74F;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 167, 79, 0.3);
        }

        .btn-success:hover {
            background: #009344;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 167, 79, 0.4);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .page-header-green {
                padding: 2rem 1.5rem;
            }

            .page-header-green h1 {
                font-size: 1.8rem;
            }

            .form-card {
                padding: 25px 20px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- ✅ Header giống các trang khác -->
        <div class="page-header-green">
            <h1><i class="bi bi-folder-plus me-2"></i>Thêm danh mục mới</h1>
            <p>Tạo danh mục sản phẩm phân bón cho cửa hàng của bạn</p>
        </div>

        <div class="form-card">
            <h2 class="form-title">Thông tin danh mục</h2>

            <form method="POST" action="/Website-PhanBon/Category/store">
                <div class="form-group">
                    <label>
                        Tên danh mục <span class="required-mark">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        class="form-control" 
                        placeholder="VD: Phân Bón Hữu Cơ, Thuốc Bảo Vệ Thực Vật..." 
                        required
                        maxlength="100"
                        id="nameInput">
                    <div class="input-helper">Nhập tên danh mục rõ ràng và dễ hiểu</div>
                </div>

                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea 
                        name="description" 
                        class="form-control" 
                        placeholder="Nhập mô tả chi tiết về danh mục sản phẩm này..."
                        maxlength="500"
                        id="descInput"></textarea>
                    <div class="char-count"><span id="charCount">0</span>/500 ký tự</div>
                </div>

                <div class="button-group">
                    <a href="/Website-PhanBon/Category/list" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Hủy
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Lưu danh mục
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Đếm ký tự mô tả
        const descInput = document.getElementById('descInput');
        const charCount = document.getElementById('charCount');
        descInput.addEventListener('input', () => {
            charCount.textContent = descInput.value.length;
        });

        // Hiệu ứng nút lưu
        const form = document.querySelector('form');
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('.btn-success');
            submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Đang lưu...';
            submitBtn.style.opacity = '0.7';
        });
    </script>
</body>
</html>
<?php include 'app/views/shares/footer.php'; ?>
