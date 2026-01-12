<?php
require_once('app/config/database.php');
require_once('app/models/CategoryModel.php');

class CategoryController
{
    private $categoryModel;
    private $db;

    public function __construct()
    {
        $this->db = (new database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    // 📋 Danh sách danh mục
    public function list()
    {
        $categories = $this->categoryModel->getCategories();
        include 'app/views/category/list.php';
    }

    // ➕ Hiển thị form thêm danh mục
    public function add()
    {
        include 'app/views/category/add.php';
    }

    // 💾 Xử lý thêm danh mục
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'] ?? '';

            $this->categoryModel->addCategory($name, $description);
            header("Location: /Website-PhanBon/Category/list");
            exit;
        }
    }

    // ✏️ Hiển thị form sửa
    public function edit($id)
    {
        $category = $this->categoryModel->getCategoryById($id);
        include 'app/views/category/edit.php';
    }

    // 🔄 Cập nhật danh mục
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'] ?? '';

            $this->categoryModel->updateCategory($id, $name, $description);
            header("Location: /Website-PhanBon/Category/list");
            exit;
        }
    }

    // ❌ Xóa danh mục
    public function delete($id)
    {
        $this->categoryModel->deleteCategory($id);
        header("Location: /Website-PhanBon/Category/list");
        exit;
    }
}
?>
