<?php include 'app/views/shares/header.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Sản Phẩm</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #19a953; 
            --primary-hover: #148441;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #6c757d;
            --text-color: #343a40;
            --border-radius: 0.75rem;
        }

        body {
            background-color: var(--light-gray);
            color: var(--text-color);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .main-container {
            padding-top: 2rem;
            padding-bottom: 4rem;
        }

        .form-card {
            background-color: white;
            border: 1px solid var(--medium-gray);
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .card-header-custom {
            background-color: var(--primary-color);
            color: white;
            padding: 1.5rem;
            border-bottom: 1px solid var(--primary-hover);
        }

        .card-header-custom h2 {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 600;
        }
        
        .card-header-custom .product-id {
            background-color: rgba(255,255,255,0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 50rem;
            font-size: 0.9rem;
            display: inline-block;
            margin-top: 0.5rem;
        }

        .card-body-custom {
            padding: 2rem;
        }

        /* === BẮT ĐẦU SỬA LỖI TẠI ĐÂY === */
        .form-floating > .form-control,
        .form-floating > .form-select {
            height: calc(3.5rem + 2px);
            padding-top: 1.625rem;
            padding-bottom: 0.625rem;
        }
        /* === KẾT THÚC SỬA LỖI === */

        .form-floating > label {
            padding: 1rem 0.75rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-2px);
        }

        .btn-secondary {
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
        }

        .image-section {
            background-color: var(--light-gray);
            border: 1px solid var(--medium-gray);
            border-radius: var(--border-radius);
            padding: 1.5rem;
        }
        
        .upload-box {
            border: 2px dashed var(--medium-gray);
            border-radius: var(--border-radius);
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .upload-box:hover {
            background-color: #e2e6ea;
            border-color: var(--dark-gray);
        }

        .upload-box .upload-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
        }

        .image-preview-wrapper {
            display: none;
            position: relative;
        }
        
        .image-preview-wrapper .preview-img {
            max-width: 100%;
            border-radius: var(--border-radius);
        }
        
        .image-preview-wrapper .btn-remove-image {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
    </style>
</head>
<body>
    <div class="container main-container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-11">
                <div class="form-card">
                    <div class="card-header-custom">
                        <h2>
                            <i class="bi bi-pencil-square me-2"></i> Chỉnh Sửa Sản Phẩm
                        </h2>
                        <div class="product-id">
                            <i class="bi bi-hash"></i> ID: <?php echo $product->id; ?>
                        </div>
                    </div>
                    <div class="card-body-custom">
                        
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

                        <form method="POST" action="/Website-PhanBon/Product/update" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <input type="hidden" name="id" value="<?php echo $product->id; ?>">
                            <input type="hidden" name="existing_image" value="<?php echo $product->image; ?>">
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="bi bi-info-circle-fill text-primary me-2"></i>Thông tin chung</h5>
                                    
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Tên Sản Phẩm" value="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>" required minlength="5" maxlength="100">
                                        <label for="name">Tên Sản Phẩm</label>
                                        <div class="invalid-feedback">Vui lòng nhập tên sản phẩm (từ 5-100 ký tự).</div>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="category_id" name="category_id" required>
                                            <option value="" disabled <?php echo empty($product->category_id) ? 'selected' : ''; ?>>-- Chọn danh mục --</option>
                                            <?php foreach ($categories as $category): ?>
                                            <option value="<?php echo $category->id; ?>" <?php echo $category->id == $product->category_id ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="category_id">Danh Mục</label>
                                        <div class="invalid-feedback">Vui lòng chọn một danh mục.</div>
                                    </div>
                                    
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="price" name="price" placeholder="Giá Bán" step="1000" min="0" value="<?php echo htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'); ?>" required>
                                        <label for="price">Giá Bán (VNĐ)</label>
                                        <div class="invalid-feedback">Giá bán phải là một số lớn hơn hoặc bằng 0.</div>
                                    </div>

                                    <div class="form-floating">
                                        <textarea class="form-control" placeholder="Mô Tả Sản Phẩm" id="description" name="description" style="height: 150px" required minlength="10"><?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
                                        <label for="description">Mô Tả Sản Phẩm</label>
                                        <div class="invalid-feedback">Mô tả cần ít nhất 10 ký tự.</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="bi bi-image-fill text-primary me-2"></i>Hình ảnh sản phẩm</h5>
                                    <div class="image-section">
                                        
                                        <label for="image" class="form-label fw-bold">Tải lên hình ảnh mới</label>
                                        <div class="upload-box" id="uploadBox" onclick="document.getElementById('image').click();">
                                            <div class="upload-icon"><i class="bi bi-cloud-arrow-up"></i></div>
                                            <p class="mb-0 fw-semibold">Nhấn để chọn ảnh</p>
                                            <small class="text-muted">JPG, PNG, GIF (Tối đa 5MB)</small>
                                        </div>
                                        <input type="file" id="image" name="image" class="form-control d-none" accept="image/*">
                                        
                                        <div class="image-preview-wrapper mt-3" id="previewBox">
                                            <img id="preview" class="preview-img" alt="Xem trước ảnh mới">
                                            <button type="button" class="btn btn-danger btn-sm btn-remove-image" onclick="removeImage()">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                        <div class="form-text mt-2">
                                            <i class="bi bi-info-circle"></i> Bỏ trống nếu bạn muốn giữ lại hình ảnh hiện tại.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                                <a href="/Website-PhanBon/Product/" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i> Hủy
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save-fill me-1"></i> Lưu Thay Đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // ... (JavaScript của bạn giữ nguyên)
    </script>
</body>
</html>
<?php include 'app/views/shares/footer.php'; ?>