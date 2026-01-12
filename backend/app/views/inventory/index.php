<?php include 'app/views/shares/header.php'; ?>
<?php SessionHelper::start(); ?>

<style>
/* ================= HEADER STYLE GIỐNG ADD PRODUCT ================= */
.page-header-green {
    background-color: #00A74F;
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
}
.page-header-green h1,
.page-header-green p {
    color: #ffffff !important;
}
</style>

<div class="container py-4">

    <!-- Header kiểu giống Add -->
    <div class="page-header-green mb-4">
        <h1><i class="bi bi-box-seam me-2"></i>Quản lý tồn kho</h1>
        <p>Theo dõi tồn kho và cảnh báo sản phẩm sắp hết</p>
    </div>

    <!-- Thông báo flash -->
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
        </div>
    <?php endif; ?>

    <!-- Cảnh báo sản phẩm sắp hết -->
    <?php if (!empty($lowStockProducts)): ?>
        <div class="alert alert-warning">
            <strong>Cảnh báo:</strong> Có sản phẩm sắp hết hàng:
            <ul class="mb-0">
                <?php foreach ($lowStockProducts as $p): ?>
                    <li>
                        <?php echo htmlspecialchars($p->name); ?>
                        (Còn: <strong><?php echo $p->stock; ?></strong>,
                        Ngưỡng: <?php echo $p->min_stock_level; ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="/Website-PhanBon/Inventory/movementForm" class="btn btn-success">
            Cập nhật nhập / xuất / tồn
        </a>
    </div>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Sản phẩm</th>
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Ngưỡng cảnh báo</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">Không có sản phẩm nào</td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $index => $product): ?>
                    <tr class="<?php echo ($product->stock < $product->min_stock_level) ? 'table-warning' : ''; ?>">
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($product->name); ?></td>
                        <td><?php echo number_format($product->price, 0, ',', '.'); ?> VND</td>
                        <td><?php echo $product->stock; ?></td>
                        <td><?php echo $product->min_stock_level; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'app/views/shares/footer.php'; ?>
