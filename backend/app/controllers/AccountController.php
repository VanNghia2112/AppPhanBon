<?php
require_once 'app/config/database.php';
require_once 'app/models/AccountModel.php';

class AccountController
{
    private $accountModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);

        // Đảm bảo luôn có session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Hiển thị trang đăng ký
    public function register()
    {
        // Có thể truyền thêm biến $errors nếu cần
        include_once 'app/views/account/register.php';
    }

    // Hiển thị trang đăng nhập
    public function login()
    {
        // Có thể truyền thêm biến $error nếu cần
        include_once 'app/views/account/login.php';
    }

    // Xử lý lưu tài khoản (đăng ký)
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username         = $_POST['username']        ?? '';
            $fullName         = $_POST['fullname']        ?? '';
            $password         = $_POST['password']        ?? '';
            $confirmPassword  = $_POST['confirmpassword'] ?? '';
            $role             = $_POST['role']            ?? 'user';

            $errors = [];

            // Validate đơn giản
            if (empty($username))  $errors['username']   = "Vui lòng nhập username!";
            if (empty($fullName))  $errors['fullname']   = "Vui lòng nhập fullname!";
            if (empty($password))  $errors['password']   = "Vui lòng nhập password!";
            if ($password !== $confirmPassword) {
                $errors['confirmPass'] = "Mật khẩu và xác nhận chưa khớp!";
            }

            if (!in_array($role, ['admin', 'user'])) {
                $role = 'user';
            }

            // Kiểm tra tài khoản đã tồn tại chưa
            if ($this->accountModel->getAccountByUsername($username)) {
                $errors['account'] = "Tài khoản này đã được đăng ký!";
            }

            if (count($errors) > 0) {
                // Trả lại view đăng ký cùng lỗi
                include_once 'app/views/account/register.php';
            } else {
                $result = $this->accountModel->save($username, $fullName, $password, $role);

                if ($result) {
                    header('Location: /Website-PhanBon/account/login');
                    exit;
                } else {
                    $errors['system'] = "Có lỗi xảy ra, vui lòng thử lại!";
                    include_once 'app/views/account/register.php';
                }
            }
        }
    }

    // Xử lý đăng xuất
    public function logout()
    {
        // Đảm bảo session đã khởi tạo ở __construct

        // Xóa thông tin đăng nhập
        unset($_SESSION['username']);
        unset($_SESSION['role']);

        // XÓA LUÔN GIỎ HÀNG KHI ĐĂNG XUẤT
        unset($_SESSION['cart']);

        // Nếu dùng thêm id user thì có thể:
        // unset($_SESSION['user_id']);

        // Nếu muốn "sạch" hoàn toàn:
        // $_SESSION = [];
        // session_destroy();

        // Điều hướng về trang sản phẩm
        header('Location: /Website-PhanBon/Product');
        exit;
    }

    // Xử lý kiểm tra đăng nhập
    public function checkLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $account = $this->accountModel->getAccountByUsername($username);

            if ($account && password_verify($password, $account->password)) {
                // Đảm bảo đã có session từ __construct

                // Lưu thông tin vào session
                $_SESSION['username'] = $account->username;
                $_SESSION['role']     = $account->role;
                // Nếu có id trong DB:
                // $_SESSION['user_id']  = $account->id;

                // Chuyển về trang sản phẩm
                header('Location: /Website-PhanBon/Product');
                exit;
            } else {
                if ($account) {
                    $error = "Mật khẩu không đúng!";
                } else {
                    $error = "Không tìm thấy tài khoản!";
                }

                // Trả lại view login cùng thông báo lỗi
                include_once 'app/views/account/login.php';
                exit;
            }
        }
    }
}
?>
