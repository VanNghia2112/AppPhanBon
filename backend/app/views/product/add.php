<?php include 'app/views/shares/header.php'; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm mới</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap');
        
        body {
            background-color: #f8f9fa; /* Nền xám nhạt hiện đại */
            font-family: 'Be Vietnam Pro', sans-serif;
        }

        .main-content {
            padding: 40px 0;
        }
        
        /* === BẮT ĐẦU THAY ĐỔI CSS === */
        /* Thêm style cho tiêu đề màu xanh */
        .page-header-green {
            background-color: #00A74F;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
        }
        
        /* Đảm bảo chữ bên trong 100% là màu trắng */
        .page-header-green h1,
        .page-header-green p {
            color: #ffffff !important; 
        }
        /* === KẾT THÚC THAY ĐỔI CSS === */
        
        .page-header h1 {
            font-weight: 600;
            color: #212529;
        }
        
        .page-header p {
            color: #6c757d;
        }
        
        .card {
            border: 1px solid #dee2e6; /* Viền mảnh, tinh tế */
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); /* Đổ bóng nhẹ nhàng */
            background-color: #ffffff;
        }

        .card-body {
            padding: 2rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.2);
        }
        
        .btn-primary {
            background-color: #28a745; /* Màu xanh lá cây hiện đại */
            border-color: #28a745;
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px);
        }
        
        .btn-light {
            background-color: #f8f9fa;
            border-color: #dee2e6;
            color: #212529;
            font-weight: 500;
        }
        
        .required-star {
            color: #dc3545;
            margin-left: 2px;
        }
        
        /* Image Upload Styles */
        .image-upload-container {
            border: 2px dashed #ced4da;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            background-color: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .image-upload-container:hover {
            border-color: #28a745;
            background-color: #e9f5ec;
        }
        .upload-icon { font-size: 40px; color: #28a745; }
        .upload-text { color: #495057; font-weight: 500; }
        
        .image-preview-box { display: none; text-align: center; }
        .preview-img {
            max-width: 100%;
            border-radius: 10px;
            border: 1px solid #dee2e6;
            margin-bottom: 1rem;
        }
        
        .form-section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container main-content">
        <div class="page-header page-header-green mb-5">
            <h1><i class="bi bi-box-seam me-2"></i>Thêm sản phẩm mới</h1>
            <p>Điền thông tin chi tiết cho sản phẩm của bạn</p>
        </div>

        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>
                <strong>Có lỗi xảy ra!</strong>
                <ul class="mb-0 mt-1">
                    <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <form method="POST" action="/Website-PhanBon/Product/save" enctype="multipart/form-data">
            <div class="row g-4">
                
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="form-section-title">Thông tin chung</h5>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Tên Sản Phẩm<span class="required-star">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Danh Mục<span class="required-star">*</span></label>
                                <select id="category_id" name="category_id" class="form-select" required>
                                    <option value="">-- Chọn danh mục --</option>
                                    <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category->id; ?>">
                                        <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="price" class="form-label">Giá Bán (VNĐ)<span class="required-star">*</span></label>
                                <input type="number" class="form-control" id="price" name="price" min="0" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Mô Tả Chi Tiết<span class="required-star">*</span></label>
                                <textarea class="form-control" id="description" name="description" rows="8" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="form-section-title">Hình ảnh sản phẩm</h5>
                            <input type="file" id="image" name="image" class="d-none" accept="image/*" onchange="previewImage(event)">
                            
                            <div class="image-upload-container" id="uploadBox" onclick="document.getElementById('image').click()">
                                <div class="upload-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                                <p class="upload-text mb-1">Nhấn để tải ảnh lên</p>
                                <small class="text-muted">PNG, JPG, GIF (Tối đa 5MB)</small>
                            </div>
                            
                            <div class="image-preview-box mt-3" id="previewBox">
                                <img id="preview" class="preview-img" alt="Xem trước hình ảnh">
                                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeImage()">
                                    <i class="bi bi-trash me-1"></i> Xóa ảnh này
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                             <h5 class="form-section-title">Hành động</h5>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle-fill me-2"></i>Lưu Sản Phẩm
                                </button>
                                <a href="/Website-PhanBon/Product/" class="btn btn-light btn-lg">
                                    <i class="bi bi-arrow-left-circle me-2"></i>Quay lại
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                    document.getElementById('uploadBox').style.display = 'none';
                    document.getElementById('previewBox').style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        }
        
        function removeImage() {
            document.getElementById('image').value = '';
            document.getElementById('uploadBox').style.display = 'block';
            document.getElementById('previewBox').style.display = 'none';
            document.getElementById('preview').src = '';
        }
    </script>
</body>
</html>

<?php include 'app/views/shares/footer.php'; ?>