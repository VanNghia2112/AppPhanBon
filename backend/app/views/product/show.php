<?php include 'app/views/shares/header.php'; ?>
<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
            <h2 class="mb-0">Chi tiết sản phẩm</h2>
        </div>
        <div class="card-body">
            <?php if ($product): ?>
            <div class="row">
                <div class="col-md-6">
                    <?php if (!empty($product->image)): ?>
                        <img src="/Website-PhanBon/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" 
                             class="img-fluid rounded" 
                             alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php else: ?>
                        <img src="/Website-PhanBon/images/no-image.png" 
                             class="img-fluid rounded" 
                             alt="Không có ảnh">
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <h3 class="card-title text-dark font-weight-bold">
                        <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                    </h3>
                    <p class="card-text">
                        <?php echo nl2br(htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8')); ?>
                    </p>
                    <p class="text-danger font-weight-bold h4">
                        💰 <?php echo number_format($product->price, 0, ',', '.'); ?> VND
                    </p>
                    <p>
                        <strong>Danh mục:</strong>
                        <span class="badge bg-info text-white">
                            <?php echo !empty($product->category_name) 
                                ? htmlspecialchars($product->category_name, ENT_QUOTES, 'UTF-8') 
                                : 'Chưa có danh mục'; ?>
                        </span>
                    </p>

                    <div class="mt-4">
                        <button class="btn btn-success px-4 btn-cart" 
                                id="addToCartBtn" 
                                data-id="<?php echo $product->id; ?>">
                            ➕ Thêm vào giỏ hàng
                        </button>
                        <a href="/Website-PhanBon/Product" class="btn btn-secondary px-4 ml-2">
                            Quay lại danh sách
                        </a>
                    </div>
                </div>
            </div>
            <?php else: ?>
                <div class="alert alert-danger text-center">
                    <h4>Không tìm thấy sản phẩm!</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ✅ Script thêm sản phẩm vào giỏ hàng qua AJAX -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const addBtn = document.getElementById("addToCartBtn");
    if (!addBtn) return;

    addBtn.addEventListener("click", () => {
        const productId = addBtn.dataset.id;
        addBtn.disabled = true;
        const originalText = addBtn.innerHTML;
        addBtn.innerHTML = "⏳ Đang thêm...";

        fetch(`/Website-PhanBon/Product/addToCart/${productId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // 🔔 Gửi sự kiện cập nhật giỏ hàng cho header
                    document.dispatchEvent(new CustomEvent("cartUpdated", {
                        detail: { cartCount: data.cartCount }
                    }));

                    addBtn.innerHTML = "✅ Đã thêm vào giỏ hàng";
                    setTimeout(() => {
                        addBtn.innerHTML = originalText;
                        addBtn.disabled = false;
                    }, 1500);
                } else {
                    alert(data.message || "Không thể thêm sản phẩm vào giỏ.");
                    addBtn.innerHTML = originalText;
                    addBtn.disabled = false;
                }
            })
            .catch(err => {
                console.error("Lỗi:", err);
                alert("Đã xảy ra lỗi khi thêm giỏ hàng!");
                addBtn.innerHTML = originalText;
                addBtn.disabled = false;
            });
    });
});
</script>

