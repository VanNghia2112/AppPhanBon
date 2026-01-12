<?php include 'app/views/shares/header.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root { --primary-color: #00A74F; --secondary-color: #00c853; --danger-color: #dc3545; --warning-color: #ffc107; }
        body { background-color: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .page-header { background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: white; padding: 2.5rem 0; margin-bottom: 2rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .stats-card { background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: transform 0.2s; border-left: 4px solid; }
        .stats-card:hover { transform: translateY(-5px); box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
        .stats-card.total { border-color: #6c757d; } .stats-card.pending { border-color: var(--warning-color); }
        .stats-card.completed { border-color: #28a745; } .stats-card.cancelled { border-color: var(--danger-color); }
        .stats-card .icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 1rem; }
        .main-card { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; }
        .card-header-custom { background-color: var(--primary-color); color: white; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        .status-badge { padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600; }
        .status-pending { background-color: #fff3cd; color: #856404; } .status-processing { background-color: #cfe2ff; color: #084298; }
        .status-shipping { background-color: #d1ecf1; color: #0c5460; } .status-completed { background-color: #d1e7dd; color: #0f5132; }
        .status-cancelled { background-color: #f8d7da; color: #842029; }
        .btn-action { padding: 0.4rem 0.8rem; border-radius: 6px; font-size: 0.9rem; transition: all 0.2s; margin: 0 0.2rem; }
        table.dataTable thead th { background-color: #f8f9fa; font-weight: 600; border-bottom: 2px solid var(--primary-color); }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="container">
            <h1><i class="bi bi-receipt-cutoff me-2"></i>Quản Lý Đơn Hàng</h1>
            <p class="mb-0 mt-2">Theo dõi và quản lý tất cả đơn hàng của khách hàng</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stats-card total">
                    <div class="icon" style="background-color: #e9ecef; color: #495057;"><i class="bi bi-receipt"></i></div>
                    <h6 class="text-muted mb-1">Tổng Đơn Hàng</h6>
                    <h2 class="mb-0"><?php echo $statistics->total_orders ?? 0; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card pending">
                    <div class="icon" style="background-color: #fff3cd; color: #856404;"><i class="bi bi-clock-history"></i></div>
                    <h6 class="text-muted mb-1">Chờ Xử Lý</h6>
                    <h2 class="mb-0"><?php echo $statistics->pending_orders ?? 0; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card completed">
                    <div class="icon" style="background-color: #d1e7dd; color: #0f5132;"><i class="bi bi-check-circle"></i></div>
                    <h6 class="text-muted mb-1">Hoàn Thành</h6>
                    <h2 class="mb-0"><?php echo $statistics->completed_orders ?? 0; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card cancelled">
                    <div class="icon" style="background-color: #f8d7da; color: #842029;"><i class="bi bi-x-circle"></i></div>
                    <h6 class="text-muted mb-1">Đã Hủy</h6>
                    <h2 class="mb-0"><?php echo $statistics->cancelled_orders ?? 0; ?></h2>
                </div>
            </div>
        </div>

        <div class="main-card">
            <div class="card-header-custom">
                <h4 class="mb-0"><i class="bi bi-list-ul me-2"></i>Danh Sách Đơn Hàng</h4>
            </div>
            <div class="card-body p-4">
                <table id="ordersTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Khách Hàng</th>
                            <th>SĐT</th>
                            <th>Tổng Tiền</th>
                            <th>Thanh Toán</th>
                            <th>Trạng Thái</th>
                            <th>Ngày Đặt</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                        <?php foreach ($orders as $order): ?>
                        <tr id="order-row-<?php echo $order->id; ?>">
                            <td><strong>#<?php echo $order->id; ?></strong></td>
                            <td><?php echo htmlspecialchars($order->name); ?></td>
                            <td><?php echo htmlspecialchars($order->phone); ?></td>
                            <td>
                                <strong class="text-success">
                                    <?php echo number_format($order->total_amount + ($order->shipping_fee ?? 0), 0, ',', '.'); ?> VNĐ
                                </strong>
                            </td>
                            <td>
                                <?php 
                                $pm = $order->payment_method ?? 'COD';
                                if($pm == 'MOMO') echo '<span class="badge" style="background:#a50064">MoMo</span>';
                                elseif($pm == 'VNPAY') echo '<span class="badge bg-primary">VNPAY</span>';
                                else echo '<span class="badge bg-info">COD</span>';
                                ?>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn status-badge status-<?php echo $order->status ?? 'pending'; ?> dropdown-toggle" 
                                            type="button" data-bs-toggle="dropdown">
                                        <?php 
                                        $statusLabels = ['pending'=>'Chờ xử lý', 'processing'=>'Đang xử lý', 'shipping'=>'Đang giao', 'completed'=>'Hoàn thành', 'cancelled'=>'Đã hủy'];
                                        echo $statusLabels[$order->status ?? 'pending'] ?? 'Chờ xử lý';
                                        ?>
                                    </button>
                                    <ul class="dropdown-menu dropdown-status">
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $order->id; ?>, 'pending')">Chờ xử lý</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $order->id; ?>, 'processing')">Đang xử lý</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $order->id; ?>, 'shipping')">Đang giao</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $order->id; ?>, 'completed')">Hoàn thành</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="updateStatus(<?php echo $order->id; ?>, 'cancelled')">Hủy đơn</a></li>
                                    </ul>
                                </div>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-danger btn-action" onclick="openDeleteModal(<?php echo $order->id; ?>)" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Xác Nhận Xóa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa đơn hàng <strong id="deleteOrderIdText"></strong> không?</p>
                    <p class="text-danger small"><i class="bi bi-exclamation-triangle"></i> Hành động này không thể hoàn tác.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Xóa Đơn Hàng</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Biến lưu ID đơn hàng đang chọn xóa
        let orderIdToDelete = null;
        let table;

        $(document).ready(function() {
            table = $('#ordersTable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json' },
                order: [[0, 'desc']]
            });

            // Gán sự kiện click cho nút xác nhận trong Modal
            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                if(orderIdToDelete) {
                    executeDelete(orderIdToDelete);
                }
            });
        });

        // Hàm mở Modal
        function openDeleteModal(orderId) {
            orderIdToDelete = orderId;
            document.getElementById('deleteOrderIdText').innerText = '#' + orderId;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        // Hàm thực thi Xóa bằng AJAX
        function executeDelete(orderId) {
            const btn = document.getElementById('confirmDeleteBtn');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Đang xóa...';
            btn.disabled = true;

            fetch('/Website-PhanBon/Product/deleteOrder', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: orderId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 1. Ẩn Modal
                    const modalEl = document.getElementById('deleteModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    modalInstance.hide();

                    // 2. Xóa dòng khỏi bảng Datatable (để không cần F5)
                    const row = $('#order-row-' + orderId);
                    table.row(row).remove().draw();

                    // 3. Thông báo nhỏ (Optional)
                    alert('Đã xóa đơn hàng thành công!');
                } else {
                    alert('Lỗi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi kết nối server.');
            })
            .finally(() => {
                // Reset nút bấm
                btn.innerHTML = originalText;
                btn.disabled = false;
                orderIdToDelete = null;
            });
        }

        function updateStatus(orderId, status) {
            if (confirm('Xác nhận cập nhật trạng thái đơn hàng?')) {
                fetch('/Website-PhanBon/Product/updateOrderStatus', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: orderId, status: status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) { location.reload(); } 
                    else { alert('Cập nhật thất bại: ' + data.message); }
                });
            }
        }
    </script>
</body>
</html>
<?php include 'app/views/shares/footer.php'; ?>