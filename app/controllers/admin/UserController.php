<?php
class UserController {
    private $userModel;

    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    public function index() {
        $users = $this->userModel->getAllUsers();
        $msg = $_GET['msg'] ?? '';
        include '../app/views/admin/users.php';
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $full_name = trim($_POST['full_name'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $role = isset($_POST['role']) ? (int)$_POST['role'] : 0;

            if ($full_name === '' || $username === '' || $email === '' || $phone === '' || $password === '') {
                header('Location: admin.php?url=users&msg=missing_fields');
                exit();
            }

            if ($this->userModel->usernameExists($username)) {
                header('Location: admin.php?url=users&msg=username_exists');
                exit();
            }

            if ($this->userModel->emailExists($email)) {
                header('Location: admin.php?url=users&msg=email_exists');
                exit();
            }

            if ($this->userModel->phoneExists($phone)) {
                header('Location: admin.php?url=users&msg=phone_exists');
                exit();
            }

            if ($this->userModel->createUser($full_name, $username, $email, $password, $phone, $role)) {
                header('Location: admin.php?url=users&msg=created');
                exit();
            }

            header('Location: admin.php?url=users&msg=create_failed');
            exit();
        }
    }

    public function resetPassword() {
        $id = $_GET['id'] ?? 0;
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: admin.php?url=users&msg=invalid_id');
            exit();
        }

        $newPassword = '123456';
        if ($this->userModel->resetPassword($id, $newPassword)) {
            header('Location: admin.php?url=users&msg=password_reset');
            exit();
        }

        header('Location: admin.php?url=users&msg=password_reset_failed');
        exit();
    }

    public function toggleStatus() {
        $id = $_GET['id'] ?? 0;
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: admin.php?url=users&msg=invalid_id');
            exit();
        }

        $user = $this->userModel->getUserById($id);
        if (!$user) {
            header('Location: admin.php?url=users&msg=user_not_found');
            exit();
        }

        $newStatus = $user['status'] == 1 ? 0 : 1;
        if ($this->userModel->updateStatus($id, $newStatus)) {
            $code = $newStatus === 0 ? 'locked' : 'unlocked';
            header('Location: admin.php?url=users&msg=' . $code);
            exit();
        }

        header('Location: admin.php?url=users&msg=status_update_failed');
        exit();
    }
}
?>