<?php
// File: app/views/user/manage.php
include 'app/views/shares/header.php';

// Kết nối database để lấy danh sách users
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT * FROM account ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_OBJ);

// Thống kê
$totalUsers = count($users);
$adminCount = count(array_filter($users, fn($u) => $u->role === 'admin'));
$userCount = count(array_filter($users, fn($u) => $u->role === 'user'));
$salesCount = count(array_filter($users, fn($u) => $u->role === 'sales'));
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Người Dùng</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
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

        .stats-card.admin .icon {
            background-color: #fce4ec;
            color: #c62828;
        }

        .stats-card.user .icon {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .stats-card.sales .icon {
            background-color: #fff3e0;
            color: #e65100;
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

        .btn-add-user {
            background-color: white;
            color: var(--primary-green);
            border: 2px solid white;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-add-user:hover {
            background-color: var(--primary-green);
            color: white;
            border-color: white;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green) 0%, #4CAF50 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .role-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .role-badge.admin {
            background: #ffebee;
            color: #c62828;
            border: 2px solid #ef5350;
        }

        .role-badge.user {
            background: #e8f5e9;
            color: #2e7d32;
            border: 2px solid #4caf50;
        }

        .role-badge.sales {
            background: #fff3e0;
            color: #e65100;
            border: 2px solid #ff9800;
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
            background-color: #dc3545;
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

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--primary-green);
            z-index: 10;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="container">
        <div class="admin-header text-center">
            <h1 class="fw-bold mb-2">
                <i class="bi bi-people-fill me-2"></i>Quản Lý Người Dùng
            </h1>
            <p class="mb-0">Quản lý tất cả tài khoản người dùng trong hệ thống</p>
        </div>
    </div>

    <div class="container mb-5">
        <!-- Statistics Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stats-card total">
                    <div class="icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h6 class="text-muted mb-1">Tổng Người Dùng</h6>
                    <h2 class="mb-0"><?php echo $totalUsers; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card admin">
                    <div class="icon">
                        <i class="bi bi-shield-fill-check"></i>
                    </div>
                    <h6 class="text-muted mb-1">Quản Trị Viên</h6>
                    <h2 class="mb-0"><?php echo $adminCount; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card user">
                    <div class="icon">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h6 class="text-muted mb-1">Người Dùng</h6>
                    <h2 class="mb-0"><?php echo $userCount; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card sales">
                    <div class="icon">
                        <i class="bi bi-cart-fill"></i>
                    </div>
                    <h6 class="text-muted mb-1">Nhân Viên Bán Hàng</h6>
                    <h2 class="mb-0"><?php echo $salesCount; ?></h2>
                </div>
            </div>
        </div>

        <!-- Main User Table -->
        <div class="main-card">
            <div class="card-header-custom">
                <h4 class="mb-0"><i class="bi bi-list-ul me-2"></i>Danh Sách Người Dùng</h4>
                <button class="btn btn-add-user" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-plus-circle me-2"></i>Thêm Người Dùng
                </button>
            </div>
            <div class="card-body p-4">
                <table id="usersTable" class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Avatar</th>
                            <th>Họ Tên</th>
                            <th>Username</th>
                            <th>Vai Trò</th>
                            <th>Ngày Tạo</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><strong>#<?php echo $user->id; ?></strong></td>
                            <td>
                                <div class="user-avatar">
                                    <?php echo strtoupper(substr($user->fullname ?: $user->username, 0, 1)); ?>
                                </div>
                            </td>
                            <td><strong><?php echo htmlspecialchars($user->fullname ?: $user->username); ?></strong></td>
                            <td><code><?php echo htmlspecialchars($user->username); ?></code></td>
                            <td>
                                <span class="role-badge <?php echo $user->role; ?>">
                                    <?php 
                                    $roleIcons = [
                                        'admin' => 'bi-shield-fill-check',
                                        'user' => 'bi-person-fill',
                                        'sales' => 'bi-cart-fill'
                                    ];
                                    $roleNames = [
                                        'admin' => 'Quản trị viên',
                                        'user' => 'Người dùng',
                                        'sales' => 'Nhân viên'
                                    ];
                                    ?>
                                    <i class="bi <?php echo $roleIcons[$user->role] ?? 'bi-person'; ?>"></i>
                                    <?php echo $roleNames[$user->role] ?? ucfirst($user->role); ?>
                                </span>
                            </td>
                            <td>
                                <i class="bi bi-calendar-event"></i>
                                <?php echo date('d/m/Y', strtotime($user->created_at ?? 'now')); ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-action btn-edit" 
                                            onclick='editUser(<?php echo json_encode($user, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-action btn-delete" 
                                            onclick="confirmDelete(<?php echo $user->id; ?>, '<?php echo htmlspecialchars($user->username, ENT_QUOTES); ?>')">
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

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Thêm Người Dùng Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="/Website-PhanBon/User/save" id="addUserForm">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="add_username" name="username" required>
                                    <label for="add_username"><i class="bi bi-at"></i> Username</label>
                                </div>
                                <small class="text-muted">Tối thiểu 4 ký tự, không có khoảng trắng</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="add_fullname" name="fullname" required>
                                    <label for="add_fullname"><i class="bi bi-person"></i> Họ và Tên</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating position-relative">
                                    <input type="password" class="form-control" id="add_password" name="password" required>
                                    <label for="add_password"><i class="bi bi-lock"></i> Mật Khẩu</label>
                                    <i class="bi bi-eye password-toggle" onclick="togglePassword('add_password')"></i>
                                </div>
                                <small class="text-muted">Tối thiểu 6 ký tự</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating position-relative">
                                    <input type="password" class="form-control" id="add_confirm_password" name="confirm_password" required>
                                    <label for="add_confirm_password"><i class="bi bi-lock-fill"></i> Xác Nhận Mật Khẩu</label>
                                    <i class="bi bi-eye password-toggle" onclick="togglePassword('add_confirm_password')"></i>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <select class="form-select" id="add_role" name="role" required>
                                        <option value="user" selected>👤 Người dùng (User)</option>
                                        <option value="sales">🛒 Nhân viên bán hàng (Sales)</option>
                                        <option value="admin">🛡️ Quản trị viên (Admin)</option>
                                    </select>
                                    <label for="add_role"><i class="bi bi-award"></i> Vai Trò</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Tạo Người Dùng
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Chỉnh Sửa Người Dùng</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="/Website-PhanBon/User/update" id="editUserForm">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="edit_username" disabled>
                                    <label for="edit_username"><i class="bi bi-at"></i> Username</label>
                                </div>
                                <small class="text-muted">Username không thể thay đổi</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="edit_fullname" name="fullname" required>
                                    <label for="edit_fullname"><i class="bi bi-person"></i> Họ và Tên</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <select class="form-select" id="edit_role" name="role" required>
                                        <option value="user">👤 Người dùng (User)</option>
                                        <option value="sales">🛒 Nhân viên bán hàng (Sales)</option>
                                        <option value="admin">🛡️ Quản trị viên (Admin)</option>
                                    </select>
                                    <label for="edit_role"><i class="bi bi-award"></i> Vai Trò</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <hr>
                                <h6 class="text-muted"><i class="bi bi-key"></i> Đổi Mật Khẩu (Tùy chọn)</h6>
                                <small class="text-muted">Chỉ nhập nếu muốn thay đổi mật khẩu</small>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating position-relative">
                                    <input type="password" class="form-control" id="edit_new_password" name="new_password">
                                    <label for="edit_new_password"><i class="bi bi-lock"></i> Mật Khẩu Mới</label>
                                    <i class="bi bi-eye password-toggle" onclick="togglePassword('edit_new_password')"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating position-relative">
                                    <input type="password" class="form-control" id="edit_confirm_password" name="confirm_password">
                                    <label for="edit_confirm_password"><i class="bi bi-lock-fill"></i> Xác Nhận Mật Khẩu</label>
                                    <i class="bi bi-eye password-toggle" onclick="togglePassword('edit_confirm_password')"></i>
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
                    <p>Bạn có chắc chắn muốn xóa người dùng <strong id="deleteUserName"></strong>?</p>
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
            $('#usersTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
                },
                order: [[0, 'desc']],
                pageLength: 10
            });
        });

        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling;
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        // Add User Form Validation
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            const username = document.getElementById('add_username').value.trim();
            const password = document.getElementById('add_password').value;
            const confirmPassword = document.getElementById('add_confirm_password').value;

            if (username.length < 4) {
                e.preventDefault();
                alert('⚠️ Username phải có ít nhất 4 ký tự!');
                return false;
            }

            if (password.length < 6) {
                e.preventDefault();
                alert('⚠️ Mật khẩu phải có ít nhất 6 ký tự!');
                return false;
            }

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('⚠️ Mật khẩu xác nhận không khớp!');
                return false;
            }
        });

        // Edit User
        function editUser(user) {
            document.getElementById('edit_id').value = user.id;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_fullname').value = user.fullname || user.username;
            document.getElementById('edit_role').value = user.role;
            
            // Clear password fields
            document.getElementById('edit_new_password').value = '';
            document.getElementById('edit_confirm_password').value = '';
            
            const editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            editModal.show();
        }

        // Edit User Form Validation
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('edit_new_password').value;
            const confirmPassword = document.getElementById('edit_confirm_password').value;

            if (newPassword) {
                if (newPassword.length < 6) {
                    e.preventDefault();
                    alert('⚠️ Mật khẩu mới phải có ít nhất 6 ký tự!');
                    return false;
                }

                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('⚠️ Mật khẩu xác nhận không khớp!');
                    return false;
                }
            }
        });

        // Delete User
        function confirmDelete(id, username) {
            document.getElementById('deleteUserName').textContent = username;
            document.getElementById('confirmDeleteBtn').href = '/Website-PhanBon/User/delete/' + id;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
</body>
</html>
<?php include 'app/views/shares/footer.php'; ?>