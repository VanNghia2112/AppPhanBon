<?php include 'app/views/shares/header.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng #<?php echo $order->id; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7fa;
        }

        .detail-header {
            background: linear-gradient(135deg, #00A74F 0%, #00c853 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .info-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-card h5 {
            color: #00A74F;
            border-bottom: 2px solid #00A74F;
            padding-bottom: 0.5rem;
            margin-bottom: 1rem;
        }

        .info-row {
            display: flex;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            width: 150px;
            color: #6c757d;
        }

        .info-value {
            flex: 1;
            color: #212529;
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .total-section {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
        }

        .grand-total {
            font-size: 1.5rem;
            color: #00A74F;
            font-weight: 700;
            border-top: 2px solid #dee2e6;
            padding-top: 1rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="detail-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-receipt me-2"></i>Chi Tiết Đơn Hàng #<?php echo $order->id; ?></h2>
                    <p class="mb-0">Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?></p>
                </div>
                <a href="/Website-PhanBon/Order/manage" class="btn btn-light">
                    <i class="bi bi-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row">
            <!-- Customer Info -->
            <div class="col-md-6">
                <div class="info-card">
                    <h5><i class="bi bi-person-circle me-2"></i>Thông Tin Khách Hàng</h5>
                    <div class="info-row">
                        <div class="info-label">Họ tên:</div>
                        <div class="info-value"><?php echo htmlspecialchars($order->name); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Số điện thoại:</div>
                        <div class="info-value"><?php echo htmlspecialchars($order->phone); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Địa chỉ:</div>
                        <div class="info-value"><?php echo htmlspecialchars($order->address); ?></div>
                    </div>
                </div>
            </div>

            <!-- Order Info -->
            <div class="col-md-6">
                <div class="info-card">
                    <h5><i class="bi bi-info-circle me-2"></i>Thông Tin Đơn Hàng</h5>
                    <div class="info-row">
                        <div class="info-label">Trạng thái:</div>
                        <div class="info-value">
                            <?php 
                            $statusLabels = [
                                'pending' => ['text' => 'Chờ xử lý', 'class' => 'bg-warning text-dark'],
                                'processing' => ['text' => 'Đang xử lý', 'class' => 'bg-primary'],
                                'shipping' => ['text' => 'Đang giao', 'class' => 'bg-info'],
                                'completed' => ['text' => 'Hoàn thành', 'class' => 'bg-success'],
                                'cancelled' => ['text' => 'Đã hủy', 'class' => 'bg-danger']
                            ];
                            $status = $order->status ?? 'pending';
                            echo '<span class="status-badge ' . $statusLabels[$status]['class'] . '">' . $statusLabels[$status]['text'] . '</span>';
                            ?>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Thanh toán:</div>
                        <div class="info-value">
                            <span class="badge <?php echo ($order->payment_method ?? 'COD') == 'COD' ? 'bg-info' : 'bg-primary'; ?>">
                                <?php echo $order->payment_method ?? 'COD'; ?>
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Phí vận chuyển:</div>
                        <div class="info-value">
                            <strong class="text-danger">
                                <?php echo number_format($order->shipping_fee ?? 0, 0, ',', '.'); ?> VNĐ
                            </strong>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Ghi chú:</div>
                        <div class="info-value">
                            <em><?php echo htmlspecialchars($order->note ?? 'Không có ghi chú'); ?></em>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Form -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="info-card">
                    <h5><i class="bi bi-pencil-square me-2"></i>Cập Nhật Thông Tin</h5>
                    <form method="POST" action="/Website-PhanBon/Order/update">
                        <input type="hidden" name="order_id" value="<?php echo $order->id; ?>">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Phương thức thanh toán</label>
                                <select name="payment_method" class="form-select">
                                    <option value="COD" <?php echo ($order->payment_method ?? 'COD') == 'COD' ? 'selected' : ''; ?>>COD</option>
                                    <option value="Chuyển khoản" <?php echo ($order->payment_method ?? '') == 'Chuyển khoản' ? 'selected' : ''; ?>>Chuyển khoản</option>
                                    <option value="Thẻ tín dụng" <?php echo ($order->payment_method ?? '') == 'Thẻ tín dụng' ? 'selected' : ''; ?>>Thẻ tín dụng</option>
                                    <option value="Ví điện tử" <?php echo ($order->payment_method ?? '') == 'Ví điện tử' ? 'selected' : ''; ?>>Ví điện tử</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phí vận chuyển (VNĐ)</label>
                                <input type="number" name="shipping_fee" class="form-control" value="<?php echo $order->shipping_fee ?? 0; ?>" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-save me-2"></i>Cập Nhật
                                </button>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Ghi chú</label>
                                <textarea name="note" class="form-control" rows="2"><?php echo htmlspecialchars($order->note ?? ''); ?></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="info-card">
                    <h5><i class="bi bi-cart-check me-2"></i>Sản Phẩm Đã Đặt</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Đơn giá</th>
                                    <th class="text-end">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $subtotal = 0;
                                foreach ($orderDetails as $detail): 
                                    $itemTotal = $detail->quantity * $detail->price;
                                    $subtotal += $itemTotal;
                                ?>
                                <tr>
                                    <td>
                                        <img src="/Website-PhanBon/<?php echo $detail->product_image; ?>" 
                                             alt="<?php echo htmlspecialchars($detail->product_name); ?>" 
                                             class="product-image">
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($detail->product_name); ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary"><?php echo $detail->quantity; ?></span>
                                    </td>
                                    <td class="text-end"><?php echo number_format($detail->price, 0, ',', '.'); ?> VNĐ</td>
                                    <td class="text-end">
                                        <strong><?php echo number_format($itemTotal, 0, ',', '.'); ?> VNĐ</strong>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Total Section -->
                    <div class="total-section">
                        <div class="total-row">
                            <div>Tạm tính:</div>
                            <div><strong><?php echo number_format($subtotal, 0, ',', '.'); ?> VNĐ</strong></div>
                        </div>
                        <div class="total-row">
                            <div>Phí vận chuyển:</div>
                            <div><strong class="text-danger"><?php echo number_format($order->shipping_fee ?? 0, 0, ',', '.'); ?> VNĐ</strong></div>
                        </div>
                        <div class="total-row grand-total">
                            <div>TỔNG CỘNG:</div>
                            <div><?php echo number_format($subtotal + ($order->shipping_fee ?? 0), 0, ',', '.'); ?> VNĐ</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include 'app/views/shares/footer.php'; ?>