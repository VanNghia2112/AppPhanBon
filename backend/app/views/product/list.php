<?php include 'app/views/shares/header.php'; ?>
<?php SessionHelper::start(); ?>

<?php
    // ===== PHÂN TRANG: Mỗi trang 8 sản phẩm =====
    $itemsPerPage = 8;
    $currentPage = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;

    $totalItems = (isset($products) && is_array($products)) ? count($products) : 0;
    $totalPages = $totalItems > 0 ? (int)ceil($totalItems / $itemsPerPage) : 1;

    if ($currentPage > $totalPages) {
        $currentPage = $totalPages;
    }

    $offset = ($currentPage - 1) * $itemsPerPage;
    $productsPage = $totalItems > 0 ? array_slice($products, $offset, $itemsPerPage) : [];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Sản Phẩm</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* ... toàn bộ CSS giữ nguyên như bạn đã viết ... */
        @import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap');
        body { background-color: #ffffff; font-family: 'Be Vietnam Pro', sans-serif; }
        .page-header { text-align: center; margin-bottom: 40px; }
        .page-header h1 { font-weight: 700; color: #1B5E20; }
        .page-header p { color: #388E3C; }
        .search-input {
            border: 2px solid #ddd; border-radius: 50px; padding: 15px 25px; font-size: 1rem;
        }
        .search-input:focus {
            border-color: #27ae60; box-shadow: 0 0 0 0.25rem rgba(39, 174, 96, 0.2);
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        @media (max-width: 992px) { .product-grid { grid-template-columns: repeat(3, 1fr); } }
        @media (max-width: 768px) { .product-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 576px) { .product-grid { grid-template-columns: 1fr; } }

        .product-card {
            background: #f8f9fa; border-radius: 12px; transition: box-shadow 0.3s ease;
            opacity: 0; animation: fadeIn 0.5s ease forwards; overflow: hidden;
            text-align: center; display: flex; flex-direction: column; position: relative;
        }
        .product-card:hover { box-shadow: 0 10px 25px rgba(0,0,0,0.08); }
        @keyframes fadeIn { from {opacity: 0; transform: translateY(20px);} to {opacity: 1; transform: translateY(0);} }
        .product-image-container { position: relative; width: 100%; padding-top: 100%; background-color: #ffffff; }
        .product-image {
            position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
            max-width: 90%; max-height: 90%; object-fit: contain; transition: transform 0.4s ease;
        }
        .product-card:hover .product-image { transform: translate(-50%, -50%) scale(1.05); }
        .product-content { padding: 15px 10px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .product-title a { font-size: 0.95rem; font-weight: 500; color: #343a40; text-decoration: none; transition: color 0.3s ease; }
        .product-title a:hover { color: #007bff; }
        .product-price { font-size: 1rem; font-weight: 600; color: #28a745; margin-top: 8px; }

        .product-actions {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            background-color: rgba(255,255,255,0.85); backdrop-filter: blur(5px);
            opacity: 0; transition: opacity 0.3s ease;
        }
        .product-card:hover .product-actions { opacity: 1; }

        .btn-action-group a, .btn-action-group button {
            display: flex; align-items: center; justify-content: center;
            width: 40px; height: 40px; margin: 0 5px; border-radius: 50%;
            color: #fff; text-decoration: none; border: none; transition: transform 0.2s ease;
        }
        .btn-action-group a:hover, .btn-action-group button:hover { transform: scale(1.1); }

        .btn-cart { background-color: #28a745; }
        .btn-edit { background-color: #007bff; }
        .btn-delete { background-color: #dc3545; }

        .toast-notification {
            position: fixed; bottom: 30px; right: 30px; background: #28a745; color: white;
            padding: 15px 25px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 9999; display: flex; align-items: center; gap: 10px;
            animation: slideIn 0.3s ease;
        }

        #bannerCarousel img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 16px;
        }
        .carousel-indicators [data-bs-target] { background-color: #28a745; }
        .carousel-control-prev-icon,
        .carousel-control-next-icon { filter: invert(1) brightness(1.5); }

        @keyframes slideIn { from {transform: translateX(400px); opacity: 0;} to {transform: translateX(0); opacity: 1;} }
        @keyframes slideOut { from {transform: translateX(0); opacity: 1;} to {transform: translateX(400px); opacity: 0;} }
    </style>
</head>

<body>
<div class="container py-5">

    <!-- Banner -->
    <div id="bannerCarousel" class="carousel slide mb-5 shadow-sm rounded-4 overflow-hidden" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="/Website-PhanBon/uploads/banner1.webp" class="d-block w-100" alt="Banner 1">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="2"></button>
        </div>
    </div>

    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="search-box flex-grow-1" style="min-width: 250px;">
            <div class="input-group input-group-lg">
                <span class="input-group-text bg-white border-end-0"
                      style="border: 2px solid #ddd; border-right: none; border-radius: 50px 0 0 50px;">
                    <i class="bi bi-search" style="color: #27ae60;"></i>
                </span>
                <input type="text" class="form-control search-input border-start-0"
                       placeholder="Tìm kiếm sản phẩm..." id="searchInput" style="border-left: none;">
            </div>
        </div>

        <?php if (SessionHelper::isAdmin()): ?>
            <a href="/Website-PhanBon/Product/add" class="btn btn-success">Thêm sản phẩm mới</a>
        <?php endif; ?>
    </div>

    <div id="productsList" class="product-grid">
        <?php if ($totalItems === 0): ?>
            <div class="col-12 text-center py-5" style="grid-column: 1 / -1;">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                <p class="mt-3 text-muted">Không có sản phẩm nào để hiển thị</p>
            </div>
        <?php else: ?>
            <?php foreach ($productsPage as $index => $product): ?>
                <div class="product-card"
                     data-product-name="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>"
                     style="animation-delay: <?php echo $index * 0.05; ?>s;">
                    <div class="product-image-container">
                        <?php if (!empty($product->image)): ?>
                            <img src="/Website-PhanBon/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>"
                                 alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>"
                                 class="product-image">
                        <?php else: ?>
                            <div class="no-image d-flex align-items-center justify-content-center h-100" style="font-size: 3rem;">📦</div>
                        <?php endif; ?>

                        <div class="product-actions">
                            <div class="btn-action-group d-flex">
                                <?php if (SessionHelper::isAdmin()): ?>
                                    <a href="/Website-PhanBon/Product/edit/<?php echo $product->id; ?>" class="btn-edit" title="Sửa">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="/Website-PhanBon/Product/delete/<?php echo $product->id; ?>" class="btn-delete"
                                       title="Xóa" onclick="return confirmDelete('<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>');">
                                        <i class="bi bi-trash3-fill"></i>
                                    </a>
                                <?php endif; ?>

                                <button class="btn-cart add-to-cart-btn" data-id="<?php echo $product->id; ?>" title="Thêm vào giỏ">
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="product-content">
                        <div class="product-title">
                            <a href="/Website-PhanBon/Product/show/<?php echo $product->id; ?>">
                                <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </div>
                        <div class="product-price">
                            <?php echo number_format($product->price, 0, ',', '.'); ?> VND
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo max(1, $currentPage - 1); ?>" tabindex="-1">«</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo min($totalPages, $currentPage + 1); ?>">»</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

<script>
// 🔐 Trạng thái đăng nhập từ PHP đưa xuống JS
const IS_LOGGED_IN = <?php echo SessionHelper::isLoggedIn() ? 'true' : 'false'; ?>;

// ======== HIỂN THỊ TOAST ========
function showToast(message) {
    const oldToast = document.querySelector('.toast-notification');
    if (oldToast) oldToast.remove();
    const toast = document.createElement('div');
    toast.className = 'toast-notification';
    toast.innerHTML = `<i class="bi bi-check-circle-fill"></i>${message}`;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

// ======== XÁC NHẬN XÓA ========
function confirmDelete(productName) {
    return confirm(`Bạn có chắc chắn muốn xóa sản phẩm:\n"${productName}" không?\n\nHành động này không thể hoàn tác!`);
}

// ======== TÌM KIẾM SẢN PHẨM TRONG TRANG HIỆN TẠI ========
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    document.querySelectorAll('.product-card').forEach(card => {
        const name = card.getAttribute('data-product-name').toLowerCase();
        card.style.display = name.includes(searchTerm) ? 'flex' : 'none';
    });
});

// ======== THÊM VÀO GIỎ HÀNG (YÊU CẦU ĐĂNG NHẬP) ========
document.addEventListener('click', function(event) {
    const button = event.target.closest('.add-to-cart-btn');
    if (!button) return;
    event.preventDefault();

    // 🔐 Nếu chưa đăng nhập thì không cho thêm giỏ
    if (!IS_LOGGED_IN) {
        if (confirm('Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng.\n\nBạn có muốn chuyển đến trang đăng nhập không?')) {
            window.location.href = '/Website-PhanBon/account/login';
        }
        return;
    }

    const productId = button.dataset.id;
    const icon = button.querySelector('i');
    button.disabled = true;
    icon.classList.replace('bi-cart-plus', 'bi-arrow-clockwise');

    fetch(`/Website-PhanBon/Product/addToCart/${productId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                icon.classList.remove('bi-arrow-clockwise');
                icon.classList.add('bi-check-lg');
                showToast('✓ Đã thêm vào giỏ hàng!');

                const event = new CustomEvent('cartUpdated', { detail: { cartCount: data.cartCount } });
                document.dispatchEvent(event);

                setTimeout(() => {
                    icon.classList.remove('bi-check-lg');
                    icon.classList.add('bi-cart-plus');
                    button.disabled = false;
                }, 1500);
            } else {
                throw new Error(data.message || 'Có lỗi xảy ra');
            }
        })
        .catch(err => {
            alert('Lỗi: ' + err.message);
            icon.classList.remove('bi-arrow-clockwise');
            icon.classList.add('bi-cart-plus');
            button.disabled = false;
        });
});
</script>

</body>
</html>

<?php include 'app/views/shares/footer.php'; ?>
