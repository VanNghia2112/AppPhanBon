<?php include 'app/views/shares/header.php'; ?>
<?php SessionHelper::start(); ?>

<div class="container py-4">
    <h2 class="mb-4">Cập nhật nhập / xuất / tồn</h2>

    <form action="/Website-PhanBon/Inventory/saveMovement" method="post" class="card p-3">
        <div class="mb-3">
            <label class="form-label">Sản phẩm</label>
            <select name="product_id" class="form-select" required>
                <option value="">-- Chọn sản phẩm --</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?php echo $p->id; ?>">
                        <?php echo htmlspecialchars($p->name); ?> (Hiện có: <?php echo $p->stock; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Loại cập nhật</label>
            <select name="type" class="form-select" required>
                <option value="import">Nhập hàng mới</option>
                <option value="export">Xuất (hủy, hỏng, trả lại…)</option>
                <option value="adjust">Điều chỉnh tồn kho</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Số lượng</label>
            <input type="number" name="quantity" class="form-control" required min="1">
        </div>

        <div class="mb-3">
            <label class="form-label">Ghi chú / Lý do</label>
            <textarea name="reason" class="form-control" rows="2" placeholder="VD: Nhập đợt hàng tháng 11, xuất do hỏng..."></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Lưu cập nhật</button>
        <a href="/Website-PhanBon/Inventory/index" class="btn btn-secondary">Quay lại tồn kho</a>
    </form>
</div>

<?php include 'app/views/shares/footer.php'; ?>
