<?php include 'app/views/shares/header.php'; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng của bạn</title>
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

        /* === HEADER GIỐNG TRANG ADD === */
        .page-header-green {
            background-color: #00A74F;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-header-green h1,
        .page-header-green p {
            color: #ffffff !important;
        }

        /* === CARD + BUTTON STYLE === */
        .card {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            background-color: #ffffff;
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

        .btn-outline-secondary:hover {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
        }

        #empty-cart-message {
            border: 2px dashed #dee2e6;
        }
    </style>
</head>
<body>
<div class="container main-content">
    <div class="page-header-green">
        <h1><i class="bi bi-cart4 me-2"></i>Giỏ hàng của bạn</h1>
        <p>Kiểm tra và quản lý các sản phẩm bạn đã thêm</p>
    </div>

    <div id="cart-content-wrapper" style="<?php echo empty($cart) ? 'display: none;' : ''; ?>">
        <?php $totalPrice = 0; ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th scope="col" colspan="2">Sản phẩm</th>
                                        <th scope="col" class="text-center">Giá</th>
                                        <th scope="col" class="text-center">Số lượng</th>
                                        <th scope="col" class="text-end">Tạm tính</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody id="cart-body">
                                    <?php if (!empty($cart)): ?>
                                        <?php foreach ($cart as $id => $item): ?>
                                            <?php
                                                $subtotal = $item['price'] * $item['quantity'];
                                                $totalPrice += $subtotal;
                                            ?>
                                            <tr id="product-row-<?php echo $id; ?>">
                                                <td style="width: 100px;">
                                                    <img src="/Website-PhanBon/<?php echo $item['image']; ?>" class="img-fluid rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                                </td>
                                                <td>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo number_format($item['price'], 0, ',', '.'); ?> VND
                                                </td>
                                                <td class="text-center" style="width: 160px;">
                                                    <div class="input-group">
                                                        <button class="btn btn-outline-secondary btn-sm quantity-decrease" data-id="<?php echo $id; ?>">-</button>
                                                        <input type="number" class="form-control text-center quantity-input" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" data-id="<?php echo $id; ?>">
                                                        <button class="btn btn-outline-secondary btn-sm quantity-increase" data-id="<?php echo $id; ?>">+</button>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <strong id="subtotal-<?php echo $id; ?>">
                                                        <?php echo number_format($subtotal, 0, ',', '.'); ?> VND
                                                    </strong>
                                                </td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-danger remove-from-cart" data-id="<?php echo $id; ?>" title="Xóa sản phẩm">&times;</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <a href="/Website-PhanBon/Product" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left-circle me-1"></i> Tiếp tục mua sắm
                </a>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Tóm tắt đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 pb-0">
                                Tổng tạm tính
                                <span id="cart-subtotal"><?php echo number_format($totalPrice, 0, ',', '.'); ?> VND</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                Phí vận chuyển
                                <span>Miễn phí</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 mb-3">
                                <div><strong>Tổng cộng</strong></div>
                                <span id="cart-total"><strong><?php echo number_format($totalPrice, 0, ',', '.'); ?> VND</strong></span>
                            </li>
                        </ul>
                        <a href="/Website-PhanBon/Product/checkout" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-credit-card-fill me-2"></i>Tiến hành thanh toán
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="empty-cart-message" class="text-center p-5 border rounded bg-light" style="<?php echo !empty($cart) ? 'display: none;' : ''; ?>">
        <i class="bi bi-cart-x" style="font-size: 4rem; color: #6c757d;"></i>
        <h3 class="mt-4">Giỏ hàng của bạn đang trống</h3>
        <p class="text-muted">Hãy thêm sản phẩm vào giỏ hàng nhé.</p>
        <a href="/Website-PhanBon/Product" class="btn btn-primary mt-2">Bắt đầu mua sắm</a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateCartQuantity(productId, quantity) {
        fetch('/Website-PhanBon/Product/updateCart', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: productId, quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('subtotal-' + productId).innerHTML = `<strong>${data.itemSubtotal}</strong>`;
                document.getElementById('cart-subtotal').textContent = data.cartTotal;
                document.getElementById('cart-total').innerHTML = `<strong>${data.cartTotal}</strong>`;

                // 🔹 Cập nhật badge giỏ hàng trên header
                if (typeof data.cartCount !== 'undefined') {
                    document.dispatchEvent(new CustomEvent('cartUpdated', {
                        detail: { cartCount: data.cartCount }
                    }));
                }
            } else {
                alert(data.message || 'Có lỗi xảy ra khi cập nhật giỏ hàng.');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function removeCartItem(productId) {
        if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;

        fetch('/Website-PhanBon/Product/removeFromCart', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: productId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById('product-row-' + productId);
                if (row) row.remove();

                document.getElementById('cart-subtotal').textContent = data.cartTotal;
                document.getElementById('cart-total').innerHTML = `<strong>${data.cartTotal}</strong>`;

                // 🔹 Cập nhật badge giỏ hàng trên header
                if (typeof data.cartCount !== 'undefined') {
                    document.dispatchEvent(new CustomEvent('cartUpdated', {
                        detail: { cartCount: data.cartCount }
                    }));
                }

                if (data.cartEmpty) {
                    document.getElementById('cart-content-wrapper').style.display = 'none';
                    document.getElementById('empty-cart-message').style.display = 'block';
                }
            } else {
                alert(data.message || 'Có lỗi xảy ra khi xóa sản phẩm.');
            }
        })
        .catch(error => console.error('Error:', error));
    }

    const cartBody = document.getElementById('cart-body');
    if (cartBody) {
        cartBody.addEventListener('click', function(e) {
            const target = e.target;
            const productId = target.dataset.id;
            if (!productId) return;

            if (target.classList.contains('quantity-increase')) {
                const input = target.previousElementSibling;
                let newQuantity = parseInt(input.value) + 1;
                input.value = newQuantity;
                updateCartQuantity(productId, newQuantity);
            }

            if (target.classList.contains('quantity-decrease')) {
                const input = target.nextElementSibling;
                let newQuantity = parseInt(input.value) - 1;
                if (newQuantity < 1) newQuantity = 1;
                input.value = newQuantity;
                updateCartQuantity(productId, newQuantity);
            }

            if (target.classList.contains('remove-from-cart')) {
                removeCartItem(productId);
            }
        });

        cartBody.addEventListener('change', function(e) {
            const target = e.target;
            if (target.classList.contains('quantity-input')) {
                const productId = target.dataset.id;
                let quantity = parseInt(target.value);
                if (isNaN(quantity) || quantity < 1) {
                    quantity = 1;
                    target.value = 1;
                }
                updateCartQuantity(productId, quantity);
            }
        });
    }
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
</body>
</html>
