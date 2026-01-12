<?php
// File: app/controllers/UserController.php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';

class UserController
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
        
        // Kiểm tra session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Chỉ admin mới được truy cập
        if (!SessionHelper::isAdmin()) {
            header('Location: /Website-PhanBon/Product/');
            exit();
        }
    }

    /**
     * Hiển thị trang quản lý người dùng (All in One)
     */
    public function manage()
    {
        // Hiển thị thông báo nếu có
        if (isset($_SESSION['success_message'])) {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    alert("✅ ' . $_SESSION['success_message'] . '");
                });
            </script>';
            unset($_SESSION['success_message']);
        }

        if (isset($_SESSION['error_message'])) {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    alert("❌ ' . $_SESSION['error_message'] . '");
                });
            </script>';
            unset($_SESSION['error_message']);
        }

        // Load view
        require_once __DIR__ . '/../views/user/manage.php';
    }

    /**
     * Lưu người dùng mới (từ Modal)
     */
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /Website-PhanBon/User/manage');
            exit();
        }

        try {
            $username = trim($_POST['username'] ?? '');
            $fullname = trim($_POST['fullname'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? 'user';

            // Validation
            if (empty($username) || strlen($username) < 4) {
                throw new Exception('Username phải có ít nhất 4 ký tự');
            }

            if (empty($fullname)) {
                throw new Exception('Họ tên không được để trống');
            }

            if (empty($password) || strlen($password) < 6) {
                throw new Exception('Mật khẩu phải có ít nhất 6 ký tự');
            }

            if ($password !== $confirmPassword) {
                throw new Exception('Mật khẩu xác nhận không khớp');
            }

            // Kiểm tra username đã tồn tại
            $stmt = $this->conn->prepare("SELECT id FROM account WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                throw new Exception('Username đã tồn tại trong hệ thống');
            }

            // Thêm user mới
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->conn->prepare("
                INSERT INTO account (username, fullname, password, role, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            if ($stmt->execute([$username, $fullname, $hashedPassword, $role])) {
                $_SESSION['success_message'] = "Thêm người dùng '$fullname' thành công!";
            } else {
                throw new Exception('Có lỗi khi thêm người dùng');
            }

        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }

        header('Location: /Website-PhanBon/User/manage');
        exit();
    }

    /**
     * Cập nhật thông tin người dùng (từ Modal)
     */
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /Website-PhanBon/User/manage');
            exit();
        }

        try {
            $id = $_POST['id'] ?? 0;
            $fullname = trim($_POST['fullname'] ?? '');
            $role = $_POST['role'] ?? 'user';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Lấy thông tin user hiện tại
            $stmt = $this->conn->prepare("SELECT * FROM account WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$user) {
                throw new Exception('Không tìm thấy người dùng');
            }

            // Validation
            if (empty($fullname)) {
                throw new Exception('Họ tên không được để trống');
            }

            // Không cho phép tự hạ quyền admin của mình
            if ($user->id == $_SESSION['user_id'] && $user->role === 'admin' && $role !== 'admin') {
                throw new Exception('Bạn không thể tự hạ quyền admin của mình!');
            }

            // Nếu có đổi mật khẩu
            if (!empty($newPassword)) {
                if (strlen($newPassword) < 6) {
                    throw new Exception('Mật khẩu mới phải có ít nhất 6 ký tự');
                }
                if ($newPassword !== $confirmPassword) {
                    throw new Exception('Mật khẩu xác nhận không khớp');
                }

                // Cập nhật cả mật khẩu
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $this->conn->prepare("
                    UPDATE account 
                    SET fullname = ?, role = ?, password = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$fullname, $role, $hashedPassword, $id]);
            } else {
                // Chỉ cập nhật thông tin
                $stmt = $this->conn->prepare("
                    UPDATE account 
                    SET fullname = ?, role = ? 
                    WHERE id = ?
                ");
                $stmt->execute([$fullname, $role, $id]);
            }

            $_SESSION['success_message'] = "Cập nhật người dùng '$fullname' thành công!";

        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }

        header('Location: /Website-PhanBon/User/manage');
        exit();
    }

    /**
     * Xóa người dùng
     */
    public function delete($id)
    {
        try {
            // Lấy thông tin user
            $stmt = $this->conn->prepare("SELECT * FROM account WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$user) {
                throw new Exception('Không tìm thấy người dùng');
            }

            // Không cho phép xóa chính mình
            if ($user->id == $_SESSION['user_id']) {
                throw new Exception('Bạn không thể xóa tài khoản của chính mình!');
            }

            // Xóa user
            $stmt = $this->conn->prepare("DELETE FROM account WHERE id = ?");
            if ($stmt->execute([$id])) {
                $_SESSION['success_message'] = "Đã xóa người dùng '{$user->fullname}' thành công!";
            } else {
                throw new Exception('Có lỗi khi xóa người dùng');
            }

        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }

        header('Location: /Website-PhanBon/User/manage');
        exit();
    }

    /**
     * Xem nhật ký hoạt động
     */
    public function activityLogs()
    {
        try {
            $stmt = $this->conn->query("
                SELECT 
                    al.*, 
                    a.username, 
                    a.fullname 
                FROM activity_logs al 
                LEFT JOIN account a ON al.user_id = a.id 
                ORDER BY al.created_at DESC 
                LIMIT 100
            ");
            $logs = $stmt->fetchAll(PDO::FETCH_OBJ);

            require_once __DIR__ . '/../views/user/activity_logs.php';
        } catch (PDOException $e) {
            $_SESSION['error_message'] = 'Chức năng nhật ký hoạt động chưa được cài đặt!';
            header('Location: /Website-PhanBon/User/manage');
            exit();
        }
    }

    /**
     * Reset mật khẩu về mặc định
     */
    public function resetPassword($id)
    {
        try {
            $stmt = $this->conn->prepare("SELECT username, fullname FROM account WHERE id = ?");
            $stmt->execute([$id]);
            $user = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$user) {
                throw new Exception('Không tìm thấy người dùng');
            }

            // Mật khẩu mặc định: username + "123456"
            $defaultPassword = $user->username . '123456';
            $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

            $stmt = $this->conn->prepare("UPDATE account SET password = ? WHERE id = ?");
            if ($stmt->execute([$hashedPassword, $id])) {
                $_SESSION['success_message'] = "Đã reset mật khẩu cho '{$user->fullname}'! Mật khẩu mới: {$defaultPassword}";
            }

        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }

        header('Location: /Website-PhanBon/User/manage');
        exit();
    }
}
?>