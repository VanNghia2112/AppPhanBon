<?php include 'app/views/shares/header.php'; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa danh mục</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap');

        body {
            background-color: #f8f9fa;
            font-family: 'Be Vietnam Pro', sans-serif;
        }

        .main-content {
            padding: 40px 0;
        }

        /* Header giống Add sản phẩm */
        .page-header-green {
            background-color: #00A74F;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }

        .page-header-green h1 {
            font-size: 2.25rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5rem;
        }

        .page-header-green p {
            font-size: 1.125rem;
            color: #ffffff;
            opacity: 0.95;
        }

        /* Card form */
        .card {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
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
            box-shadow: 0 0 0 0.25rem rgba(40,167,69,0.2);
        }

        .btn-primary {
            background-color: #28a745;
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

        /* Preview section */
        .preview-section {
            border: 2px dashed #ced4da;
            border-radius: 12px;
            padding: 1.25rem;
            margin-top: 1.5rem;
            background-color: #f8f9fa;
        }

        .preview-title {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 0.75rem;
        }

        .preview-name {
            font-weight: 600;
            color: #212529;
        }

        .preview-desc {
            color: #495057;
            font-size: 0.95rem;
            font-style: italic;
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

        <!-- Header -->
        <div class="page-header-green">
            <h1><i class="bi bi-pencil-square me-2"></i>Chỉnh sửa danh mục</h1>
            <p>Cập nhật thông tin danh mục #<?= $category->id ?></p>
        </div>

        <!-- Hiển thị lỗi nếu có -->
        <?php if (!empty($errors)): ?>
        <div class="alert alert-danger d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div>
                <strong>Có lỗi xảy ra!</strong>
                <ul class="mb-0 mt-1">
                    <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <!-- Form chỉnh sửa danh mục -->
        <form method="POST" action="/Website-PhanBon/Category/update/<?= $category->id ?>" id="editForm">
            <div class="row g-4">

                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="form-section-title">Thông tin danh mục</h5>

                            <div class="mb-3">
                                <label for="categoryName" class="form-label">Tên danh mục<span class="required-star">*</span></label>
                                <input type="text" class="form-control" id="categoryName" name="name"
                                    value="<?= htmlspecialchars($category->name) ?>" required
                                    maxlength="100" placeholder="Nhập tên danh mục..." oninput="updatePreview()">
                            </div>

                            <div class="mb-3">
                                <label for="categoryDesc" class="form-label">Mô tả</label>
                                <textarea class="form-control" id="categoryDesc" name="description"
                                    maxlength="500" rows="5" placeholder="Nhập mô tả chi tiết về danh mục..."
                                    oninput="updatePreview(); updateCharCount()"><?= htmlspecialchars($category->description) ?></textarea>
                                <div class="text-end text-muted"><span id="charCount">0</span>/500 ký tự</div>
                            </div>

                            <div class="preview-section">
                                <div class="preview-title">Xem trước</div>
                                <div class="preview-name" id="previewName"><?= htmlspecialchars($category->name) ?></div>
                                <div class="preview-desc" id="previewDesc"><?= htmlspecialchars($category->description) ?: 'Chưa có mô tả' ?></div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="form-section-title">Hành động</h5>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle-fill me-2"></i>Cập nhật danh mục
                                </button>
                                <a href="/Website-PhanBon/Category/list" class="btn btn-light btn-lg">
                                    <i class="bi bi-arrow-left-circle me-2"></i>Quay lại danh sách
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
        // Cập nhật preview
        function updatePreview() {
            const name = document.getElementById('categoryName').value;
            const desc = document.getElementById('categoryDesc').value;
            document.getElementById('previewName').textContent = name || 'Tên danh mục';
            document.getElementById('previewDesc').textContent = desc || 'Chưa có mô tả';
        }

        // Đếm ký tự mô tả
        function updateCharCount() {
            const desc = document.getElementById('categoryDesc').value;
            document.getElementById('charCount').textContent = desc.length;
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateCharCount();
        });

        // Kiểm tra form trước submit
        document.getElementById('editForm').addEventListener('submit', function(e){
            const name = document.getElementById('categoryName').value.trim();
            if(!name){
                e.preventDefault();
                alert('⚠️ Vui lòng nhập tên danh mục!');
            }
        });
    </script>

</body>
</html>

<?php include 'app/views/shares/footer.php'; ?>
