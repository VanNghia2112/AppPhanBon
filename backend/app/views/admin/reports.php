<?php
// File: app/views/admin/reports.php
include 'app/views/shares/header.php';

// Kết nối database
$db = (new Database())->getConnection();

// Lấy dữ liệu thống kê
try {
    // 1. Tổng quan cơ bản
    $stmt = $db->query("SELECT COUNT(*) as total FROM product");
    $totalProducts = $stmt->fetch(PDO::FETCH_OBJ)->total;

    $stmt = $db->query("SELECT COUNT(*) as total FROM account");
    $totalUsers = $stmt->fetch(PDO::FETCH_OBJ)->total;

    // Kiểm tra cấu trúc bảng orders
    $stmt = $db->query("SHOW TABLES LIKE 'orders'");
    $ordersTableExists = $stmt->rowCount() > 0;

    if ($ordersTableExists) {
        // Đếm tổng đơn hàng
        $stmt = $db->query("SELECT COUNT(*) as total FROM orders");
        $totalOrders = $stmt->fetch(PDO::FETCH_OBJ)->total;

        // Tính tổng doanh thu từ order_details thay vì total_amount
        $stmt = $db->query("SHOW TABLES LIKE 'order_details'");
        $orderDetailsExists = $stmt->rowCount() > 0;

        if ($orderDetailsExists) {
            $stmt = $db->query("
                SELECT COALESCE(SUM(od.quantity * od.price), 0) as total
                FROM order_details od
                JOIN orders o ON od.order_id = o.id
                WHERE o.status = 'completed'
            ");
            $totalRevenue = $stmt->fetch(PDO::FETCH_OBJ)->total;

            // Doanh thu theo tháng
            $stmt = $db->query("
                SELECT 
                    DATE_FORMAT(o.created_at, '%Y-%m') as month,
                    COALESCE(SUM(od.quantity * od.price), 0) as revenue,
                    COUNT(DISTINCT o.id) as order_count
                FROM orders o
                LEFT JOIN order_details od ON o.id = od.order_id
                WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY month
                ORDER BY month ASC
                LIMIT 6
            ");
            $monthlyRevenue = $stmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            $totalRevenue = 0;
            $monthlyRevenue = [];
        }

        // Đơn hàng theo trạng thái
        $stmt = $db->query("
            SELECT 
                status,
                COUNT(*) as count
            FROM orders
            GROUP BY status
        ");
        $ordersByStatus = $stmt->fetchAll(PDO::FETCH_OBJ);
    } else {
        $totalOrders = 0;
        $totalRevenue = 0;
        $monthlyRevenue = [];
        $ordersByStatus = [];
    }

    // Top sản phẩm bán chạy
    $stmt = $db->query("SHOW TABLES LIKE 'order_details'");
    $orderDetailsExists = $stmt->rowCount() > 0;

    if ($orderDetailsExists) {
        $stmt = $db->query("
            SELECT 
                p.name,
                p.price,
                COALESCE(SUM(od.quantity), 0) as total_sold,
                COALESCE(SUM(od.quantity * od.price), 0) as revenue
            FROM product p
            LEFT JOIN order_details od ON p.id = od.product_id
            GROUP BY p.id, p.name, p.price
            ORDER BY total_sold DESC, p.price DESC
            LIMIT 10
        ");
    } else {
        // Nếu chưa có order_details, lấy theo giá
        $stmt = $db->query("
            SELECT 
                name,
                price,
                0 as total_sold,
                0 as revenue
            FROM product
            ORDER BY price DESC
            LIMIT 10
        ");
    }
    $topProducts = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Sản phẩm theo danh mục
    $stmt = $db->query("
        SELECT 
            c.name as category_name,
            COUNT(p.id) as product_count
        FROM category c
        LEFT JOIN product p ON c.id = p.category_id
        GROUP BY c.id, c.name
        ORDER BY product_count DESC
    ");
    $productsByCategory = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Tính % tăng trưởng (giả lập)
    $productGrowth = "+12%";
    $userGrowth = "+8%";
    $orderGrowth = $totalOrders > 0 ? "+5%" : "0%";
    $revenueGrowth = $totalRevenue > 0 ? "+25%" : "0%";

} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Lỗi database: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo Cáo Chi Tiết - Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-green: #00A74F;
            --primary-hover: #008A42;
        }

        body {
            background-color: #f8f9fa;
        }

        .admin-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, #00c853 100%);
            border-radius: 18px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.12);
            color: white;
            padding: 2.5rem;
            margin-bottom: 2rem;
        }

        .stats-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s;
            border-left: 5px solid;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        .stats-card.products { border-left-color: #2196F3; }
        .stats-card.users { border-left-color: #9C27B0; }
        .stats-card.orders { border-left-color: #FF9800; }
        .stats-card.revenue { border-left-color: var(--primary-green); }

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

        .stats-card.products .icon { background-color: #e3f2fd; color: #2196F3; }
        .stats-card.users .icon { background-color: #f3e5f5; color: #9C27B0; }
        .stats-card.orders .icon { background-color: #fff3e0; color: #FF9800; }
        .stats-card.revenue .icon { background-color: #e8f5e9; color: var(--primary-green); }

        .chart-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }

        .chart-card h5 {
            color: var(--primary-green);
            font-weight: 700;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .table-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .table-header {
            background: var(--primary-green);
            color: white;
            padding: 1.5rem;
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid var(--primary-green);
        }

        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }

        .btn-export {
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-export:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(33, 150, 243, 0.4);
            color: white;
        }

        .progress-bar {
            background-color: var(--primary-green);
        }

        .empty-data {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .empty-data i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <div class="container my-4">
        <!-- Header -->
        <div class="admin-header text-center">
            <h1 class="fw-bold mb-2">
                <i class="bi bi-file-earmark-bar-graph me-2"></i>Báo Cáo Chi Tiết
            </h1>
            <p class="mb-0">Phân tích và thống kê toàn diện hệ thống</p>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stats-card products">
                    <div class="icon"><i class="bi bi-box-seam"></i></div>
                    <h6 class="text-muted mb-1">Tổng Sản Phẩm</h6>
                    <h2 class="mb-0"><?php echo number_format($totalProducts); ?></h2>
                    <small class="text-success"><i class="bi bi-arrow-up"></i> <?php echo $productGrowth; ?></small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card users">
                    <div class="icon"><i class="bi bi-people"></i></div>
                    <h6 class="text-muted mb-1">Người Dùng</h6>
                    <h2 class="mb-0"><?php echo number_format($totalUsers); ?></h2>
                    <small class="text-success"><i class="bi bi-arrow-up"></i> <?php echo $userGrowth; ?></small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card orders">
                    <div class="icon"><i class="bi bi-receipt"></i></div>
                    <h6 class="text-muted mb-1">Đơn Hàng</h6>
                    <h2 class="mb-0"><?php echo number_format($totalOrders); ?></h2>
                    <small class="<?php echo $totalOrders > 0 ? 'text-success' : 'text-muted'; ?>">
                        <i class="bi bi-<?php echo $totalOrders > 0 ? 'arrow-up' : 'dash'; ?>"></i> 
                        <?php echo $orderGrowth; ?>
                    </small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card revenue">
                    <div class="icon"><i class="bi bi-currency-dollar"></i></div>
                    <h6 class="text-muted mb-1">Doanh Thu</h6>
                    <h2 class="mb-0"><?php echo number_format($totalRevenue, 0, ',', '.'); ?> đ</h2>
                    <small class="<?php echo $totalRevenue > 0 ? 'text-success' : 'text-muted'; ?>">
                        <i class="bi bi-<?php echo $totalRevenue > 0 ? 'arrow-up' : 'dash'; ?>"></i> 
                        <?php echo $revenueGrowth; ?>
                    </small>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Từ ngày</label>
                    <input type="date" class="form-control" id="fromDate">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Đến ngày</label>
                    <input type="date" class="form-control" id="toDate">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Loại báo cáo</label>
                    <select class="form-select">
                        <option>Tất cả</option>
                        <option>Doanh thu</option>
                        <option>Đơn hàng</option>
                        <option>Sản phẩm</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-success w-100">
                        <i class="bi bi-funnel"></i> Lọc
                    </button>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12 text-end">
                    <a href="/Website-PhanBon/Admin/exportExcel" class="btn btn-export">
                        <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                    </a>
                    <a href="/Website-PhanBon/Admin/exportPDF" class="btn btn-export ms-2">
                        <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-4 mb-4">
            <!-- Revenue Chart -->
            <div class="col-md-8">
                <div class="chart-card">
                    <h5><i class="bi bi-graph-up"></i> Doanh Thu 6 Tháng Gần Nhất</h5>
                    <?php if (count($monthlyRevenue) > 0): ?>
                        <canvas id="revenueChart"></canvas>
                    <?php else: ?>
                        <div class="empty-data">
                            <i class="bi bi-graph-up"></i>
                            <p>Chưa có dữ liệu doanh thu</p>
                            <small class="text-muted">Dữ liệu sẽ được hiển thị khi có đơn hàng</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Orders by Status -->
            <div class="col-md-4">
                <div class="chart-card">
                    <h5><i class="bi bi-pie-chart"></i> Đơn Hàng Theo Trạng Thái</h5>
                    <?php if (count($ordersByStatus) > 0): ?>
                        <canvas id="statusChart"></canvas>
                    <?php else: ?>
                        <div class="empty-data">
                            <i class="bi bi-pie-chart"></i>
                            <p>Chưa có đơn hàng</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Products by Category Chart -->
        <div class="row g-4 mb-4">
            <div class="col-md-12">
                <div class="chart-card">
                    <h5><i class="bi bi-bar-chart"></i> Sản Phẩm Theo Danh Mục</h5>
                    <?php if (count($productsByCategory) > 0): ?>
                        <canvas id="categoryChart"></canvas>
                    <?php else: ?>
                        <div class="empty-data">
                            <i class="bi bi-bar-chart"></i>
                            <p>Chưa có dữ liệu danh mục</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Top Products Table -->
        <div class="table-card">
            <div class="table-header">
                <h5><i class="bi bi-trophy"></i> Top 10 Sản Phẩm</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Hạng</th>
                            <th>Tên Sản Phẩm</th>
                            <th>Giá</th>
                            <th>Đã Bán</th>
                            <th>Doanh Thu</th>
                            <th style="width: 25%;">Hiệu Suất</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (count($topProducts) > 0):
                            $rank = 1;
                            $maxSold = max(1, $topProducts[0]->total_sold);
                            foreach ($topProducts as $product): 
                                $percentage = $maxSold > 0 ? ($product->total_sold / $maxSold) * 100 : 0;
                        ?>
                        <tr>
                            <td>
                                <?php if ($rank <= 3): ?>
                                    <span class="badge bg-warning">🏆 #<?php echo $rank; ?></span>
                                <?php else: ?>
                                    <strong>#<?php echo $rank; ?></strong>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($product->name); ?></strong></td>
                            <td><?php echo number_format($product->price, 0, ',', '.'); ?> đ</td>
                            <td><span class="badge bg-info"><?php echo $product->total_sold; ?> SP</span></td>
                            <td><strong class="text-success"><?php echo number_format($product->revenue, 0, ',', '.'); ?> đ</strong></td>
                            <td>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?php echo $percentage; ?>%">
                                        <?php echo round($percentage); ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            $rank++;
                            endforeach;
                        else:
                        ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2">Chưa có dữ liệu sản phẩm</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if (count($monthlyRevenue) > 0): ?>
        // Revenue Chart
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: [<?php foreach ($monthlyRevenue as $data) echo "'Tháng " . date('m/Y', strtotime($data->month . '-01')) . "',"; ?>],
                datasets: [{
                    label: 'Doanh Thu (VNĐ)',
                    data: [<?php foreach ($monthlyRevenue as $data) echo $data->revenue . ','; ?>],
                    borderColor: '#00A74F',
                    backgroundColor: 'rgba(0, 167, 79, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: true, position: 'top' } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: value => new Intl.NumberFormat('vi-VN').format(value) + ' đ' }
                    }
                }
            }
        });
        <?php endif; ?>

        <?php if (count($ordersByStatus) > 0): ?>
        // Status Chart
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: [<?php 
                    foreach ($ordersByStatus as $status) {
                        $names = ['pending'=>'Chờ xử lý','processing'=>'Đang xử lý','completed'=>'Hoàn thành','cancelled'=>'Đã hủy'];
                        echo "'" . ($names[$status->status] ?? ucfirst($status->status)) . "',";
                    } 
                ?>],
                datasets: [{
                    data: [<?php foreach ($ordersByStatus as $status) echo $status->count . ','; ?>],
                    backgroundColor: ['#FF9800', '#2196F3', '#00A74F', '#f44336']
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
        <?php endif; ?>

        <?php if (count($productsByCategory) > 0): ?>
        // Category Chart
        new Chart(document.getElementById('categoryChart'), {
            type: 'bar',
            data: {
                labels: [<?php foreach ($productsByCategory as $cat) echo "'" . htmlspecialchars($cat->category_name) . "',"; ?>],
                datasets: [{
                    label: 'Số Sản Phẩm',
                    data: [<?php foreach ($productsByCategory as $cat) echo $cat->product_count . ','; ?>],
                    backgroundColor: '#00A74F',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
        <?php endif; ?>

        // Set default dates
        const today = new Date();
        const lastMonth = new Date(today);
        lastMonth.setMonth(lastMonth.getMonth() - 1);
        
        document.getElementById('toDate').valueAsDate = new Date();
        document.getElementById('fromDate').valueAsDate = lastMonth;
    </script>
</body>
</html>
<?php include 'app/views/shares/footer.php'; ?>