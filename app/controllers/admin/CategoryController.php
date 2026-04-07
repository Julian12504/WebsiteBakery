<?php
class CategoryController {
    private $categoryModel;

    public function __construct($categoryModel) {
        $this->categoryModel = $categoryModel;
    }

    public function index() {
        $categories = $this->categoryModel->getAllCategories();
        $msg = $_GET['msg'] ?? '';
        include '../app/views/admin/categories.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($name === '') {
                header('Location: admin.php?url=categories&msg=missing_name');
                exit();
            }

            if ($this->categoryModel->create($name, $description)) {
                header('Location: admin.php?url=categories&msg=created');
                exit();
            }

            header('Location: admin.php?url=categories&msg=create_failed');
            exit();
        }
    }

    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: admin.php?url=categories');
            exit();
        }

        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) {
            header('Location: admin.php?url=categories&msg=not_found');
            exit();
        }

        $categories = $this->categoryModel->getAllCategories();
        $msg = $_GET['msg'] ?? '';
        include '../app/views/admin/categories.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($id <= 0 || $name === '') {
                header('Location: admin.php?url=categories&msg=invalid_input');
                exit();
            }

            if ($this->categoryModel->update($id, $name, $description)) {
                header('Location: admin.php?url=categories&msg=updated');
                exit();
            }

            header('Location: admin.php?url=categories&msg=update_failed');
            exit();
        }
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: admin.php?url=categories&msg=invalid_id');
            exit();
        }

        if ($this->categoryModel->delete($id)) {
            header('Location: admin.php?url=categories&msg=deleted');
            exit();
        }

        header('Location: admin.php?url=categories&msg=delete_failed');
        exit();
    }
}
?>