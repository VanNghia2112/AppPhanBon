    <?php include 'app/views/shares/header.php'; ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Quản Lý Sản Phẩm</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
        <style>
            :root {
                --primary-green: #00A74F;
                --primary-hover: #008A42;
                --danger-red: #dc3545;
                --warning-yellow: #ffc107;
            }

            body {
                background-color: #f8f9fa;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }

            .admin-header {
                background: linear-gradient(135deg, var(--primary-green) 0%, #00c853 100%);
                color: white;
                padding: 2.5rem 0;
                margin-bottom: 2rem;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            }

            .admin-header h1 {
                font-weight: 700;
                margin: 0;
            }

            .stats-card {
                background: white;
                border-radius: 12px;
                padding: 1.5rem;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                transition: transform 0.2s;
            }

            .stats-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            }

            .stats-card .icon {
                width: 60px;
                height: 60px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 28px;
                margin-bottom: 1rem;
            }

            .stats-card.total .icon {
                background-color: #e3f2fd;
                color: #1976d2;
            }

            .stats-card.categories .icon {
                background-color: #f3e5f5;
                color: #7b1fa2;
            }

            .stats-card.revenue .icon {
                background-color: #e8f5e9;
                color: var(--primary-green);
            }

            .main-card {
                background: white;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
                overflow: hidden;
            }

            .card-header-custom {
                background-color: var(--primary-green);
                color: white;
                padding: 1.5rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .btn-add-product {
                background-color: white;
                color: var(--primary-green);
                border: 2px solid white;
                font-weight: 600;
                transition: all 0.3s;
            }

            .btn-add-product:hover {
                background-color: var(--primary-green);
                color: white;
                border-color: white;
            }

            .product-image-thumb {
                width: 60px;
                height: 60px;
                object-fit: cover;
                border-radius: 8px;
                border: 2px solid #e9ecef;
            }

            .action-buttons {
                display: flex;
                gap: 0.5rem;
            }

            .btn-action {
                padding: 0.4rem 0.8rem;
                border-radius: 6px;
                font-size: 0.9rem;
                transition: all 0.2s;
            }

            .btn-edit {
                background-color: #17a2b8;
                color: white;
                border: none;
            }

            .btn-edit:hover {
                background-color: #138496;
                transform: scale(1.05);
            }

            .btn-delete {
                background-color: var(--danger-red);
                color: white;
                border: none;
            }

            .btn-delete:hover {
                background-color: #c82333;
                transform: scale(1.05);
            }

            table.dataTable thead th {
                background-color: #f8f9fa;
                font-weight: 600;
                border-bottom: 2px solid var(--primary-green);
            }

            .price-tag {
                background-color: #e8f5e9;
                color: var(--primary-green);
                padding: 0.3rem 0.8rem;
                border-radius: 20px;
                font-weight: 600;
            }

            .modal-header-custom {
                background-color: var(--primary-green);
                color: white;
            }

            .form-floating > .form-control,
            .form-floating > .form-select {
                border: 2px solid #e9ecef;
                border-radius: 8px;
            }

            .form-floating > .form-control:focus,
            .form-floating > .form-select:focus {
                border-color: var(--primary-green);
                box-shadow: 0 0 0 0.2rem rgba(0, 167, 79, 0.15);
            }

            .image-upload-zone {
                border: 2px dashed #ced4da;
                border-radius: 8px;
                padding: 2rem;
                text-align: center;
                cursor: pointer;
                transition: all 0.3s;
            }

            .image-upload-zone:hover {
                border-color: var(--primary-green);
                background-color: #f0f8f4;
            }

            .preview-container {
                position: relative;
                display: inline-block;
            }

            .preview-image {
                max-width: 100%;
                border-radius: 8px;
                border: 2px solid #e9ecef;
            }

            .remove-preview {
                position: absolute;
                top: 10px;
                right: 10px;
                background-color: var(--danger-red);
                color: white;
                border: none;
                border-radius: 50%;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            }

            .category-header-box {
        background-color: #00A74F;
        border-radius: 18px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.12);
    }

        </style>
    </head>
    <body>
        <!-- Admin Header -->
        <!-- Admin Header -->
    <div class="container">
        <div class="category-header-box text-white p-4 mb-4 text-center">
            <h1 class="fw-bold mb-2">
                <i class="bi bi-speedometer2 me-2"></i>Quản Lý Sản Phẩm
            </h1>
            <p class="mb-0">Tổng hợp tất cả sản phẩm và chức năng quản lý</p>
        </div>
    </div>



        <div class="container mb-5">
            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stats-card total">
                        <div class="icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <h6 class="text-muted mb-1">Tổng Sản Phẩm</h6>
                        <h2 class="mb-0"><?php echo count($products); ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card categories">
                        <div class="icon">
                            <i class="bi bi-grid-3x3-gap"></i>
                        </div>
                        <h6 class="text-muted mb-1">Danh Mục</h6>
                        <h2 class="mb-0"><?php echo count($categories); ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card revenue">
                        <div class="icon">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <h6 class="text-muted mb-1">Tổng Giá Trị</h6>
                        <h2 class="mb-0">
                            <?php 
                            $total = 0;
                            foreach($products as $p) {
                                $total += $p->price;
                            }
                            echo number_format($total, 0, ',', '.'); 
                            ?> VNĐ
                        </h2>
                    </div>
                </div>
            </div>

            <!-- Main Product Table -->
            <div class="main-card">
                <div class="card-header-custom">
                    <h4 class="mb-0"><i class="bi bi-list-ul me-2"></i>Danh Sách Sản Phẩm</h4>
                    <button class="btn btn-add-product" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="bi bi-plus-circle me-2"></i>Thêm Sản Phẩm
                    </button>
                </div>
                <div class="card-body p-4">
                    <table id="productsTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Hình Ảnh</th>
                                <th>Tên Sản Phẩm</th>
                                <th>Danh Mục</th>
                                <th>Giá Bán</th>
                                <th>Mô Tả</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product->id; ?></td>
                                <td>
                                    <img src="/Website-PhanBon/<?php echo $product->image; ?>" 
                                        alt="<?php echo htmlspecialchars($product->name); ?>" 
                                        class="product-image-thumb">
                                </td>
                                <td><strong><?php echo htmlspecialchars($product->name); ?></strong></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?php echo htmlspecialchars($product->category_name); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="price-tag">
                                        <?php echo number_format($product->price, 0, ',', '.'); ?> VNĐ
                                    </span>
                                </td>
                                <td><?php echo substr(htmlspecialchars($product->description), 0, 50) . '...'; ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-action btn-edit" 
                                                onclick="editProduct(<?php echo htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8'); ?>)">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-action btn-delete" 
                                                onclick="confirmDelete(<?php echo $product->id; ?>, '<?php echo htmlspecialchars($product->name); ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Product Modal -->
        <div class="modal fade" id="addProductModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Thêm Sản Phẩm Mới</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="/Website-PhanBon/Product/save" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="add_name" name="name" required>
                                        <label for="add_name">Tên Sản Phẩm</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="add_category" name="category_id" required>
                                            <option value="">-- Chọn danh mục --</option>
                                            <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat->id; ?>">
                                                <?php echo htmlspecialchars($cat->name); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="add_category">Danh Mục</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="add_price" name="price" min="0" required>
                                        <label for="add_price">Giá Bán (VNĐ)</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="add_description" name="description" style="height: 120px" required></textarea>
                                        <label for="add_description">Mô Tả Chi Tiết</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Hình Ảnh Sản Phẩm</label>
                                    <input type="file" id="add_image" name="image" class="d-none" accept="image/*" onchange="previewAddImage(event)">
                                    <div class="image-upload-zone" id="add_uploadZone" onclick="document.getElementById('add_image').click()">
                                        <i class="bi bi-cloud-arrow-up" style="font-size: 3rem; color: var(--primary-green);"></i>
                                        <p class="mb-0 mt-2">Nhấn để chọn hình ảnh</p>
                                        <small class="text-muted">PNG, JPG, GIF (Tối đa 5MB)</small>
                                    </div>
                                    <div id="add_previewContainer" class="mt-3" style="display: none;">
                                        <div class="preview-container">
                                            <img id="add_preview" class="preview-image" alt="Preview">
                                            <button type="button" class="remove-preview" onclick="removeAddImage()">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i>Lưu Sản Phẩm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Product Modal -->
        <div class="modal fade" id="editProductModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Chỉnh Sửa Sản Phẩm</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="/Website-PhanBon/Product/update" enctype="multipart/form-data">
                        <input type="hidden" id="edit_id" name="id">
                        <input type="hidden" id="edit_existing_image" name="existing_image">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="edit_name" name="name" required>
                                        <label for="edit_name">Tên Sản Phẩm</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="edit_category" name="category_id" required>
                                            <option value="">-- Chọn danh mục --</option>
                                            <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat->id; ?>">
                                                <?php echo htmlspecialchars($cat->name); ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="edit_category">Danh Mục</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="edit_price" name="price" min="0" required>
                                        <label for="edit_price">Giá Bán (VNĐ)</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="edit_description" name="description" style="height: 120px" required></textarea>
                                        <label for="edit_description">Mô Tả Chi Tiết</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Hình Ảnh Sản Phẩm</label>
                                    <div id="edit_currentImage" class="mb-3">
                                        <img id="edit_current_img" class="preview-image" style="max-width: 200px;" alt="Ảnh hiện tại">
                                    </div>
                                    <input type="file" id="edit_image" name="image" class="d-none" accept="image/*" onchange="previewEditImage(event)">
                                    <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('edit_image').click()">
                                        <i class="bi bi-upload me-1"></i>Thay Đổi Hình Ảnh
                                    </button>
                                    <div id="edit_previewContainer" class="mt-3" style="display: none;">
                                        <div class="preview-container">
                                            <img id="edit_preview" class="preview-image" alt="Preview">
                                            <button type="button" class="remove-preview" onclick="removeEditImage()">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-1"></i>Cập Nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>Xác Nhận Xóa</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Bạn có chắc chắn muốn xóa sản phẩm <strong id="deleteProductName"></strong>?</p>
                        <p class="text-danger mb-0"><i class="bi bi-info-circle me-1"></i>Hành động này không thể hoàn tác!</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Xóa
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        
        <script>
            // Initialize DataTable
            $(document).ready(function() {
                $('#productsTable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
                    },
                    order: [[0, 'desc']],
                    pageLength: 10
                });
            });

            // Add Product Image Preview
            function previewAddImage(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('add_preview').src = e.target.result;
                        document.getElementById('add_uploadZone').style.display = 'none';
                        document.getElementById('add_previewContainer').style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            }

            function removeAddImage() {
                document.getElementById('add_image').value = '';
                document.getElementById('add_uploadZone').style.display = 'block';
                document.getElementById('add_previewContainer').style.display = 'none';
            }

            // Edit Product
            function editProduct(product) {
                document.getElementById('edit_id').value = product.id;
                document.getElementById('edit_name').value = product.name;
                document.getElementById('edit_category').value = product.category_id;
                document.getElementById('edit_price').value = product.price;
                document.getElementById('edit_description').value = product.description;
                document.getElementById('edit_existing_image').value = product.image;
                document.getElementById('edit_current_img').src = '/Website-PhanBon/' + product.image;
                
                const editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
                editModal.show();
            }

            function previewEditImage(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('edit_preview').src = e.target.result;
                        document.getElementById('edit_currentImage').style.display = 'none';
                        document.getElementById('edit_previewContainer').style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            }

            function removeEditImage() {
                document.getElementById('edit_image').value = '';
                document.getElementById('edit_currentImage').style.display = 'block';
                document.getElementById('edit_previewContainer').style.display = 'none';
            }

            // Delete Product
            function confirmDelete(id, name) {
                document.getElementById('deleteProductName').textContent = name;
                document.getElementById('confirmDeleteBtn').href = '/Website-PhanBon/Product/delete/' + id;
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            }
        </script>
    </body>
    </html>
    <?php include 'app/views/shares/footer.php'; ?>