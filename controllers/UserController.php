<?php
require_once "models/UserModel.php";
require_once "models/StudentModel.php";
require_once "Auth.php";
require_once "views/UserView.php";

class UserController
{
    private UserModel $userModel;
    private StudentModel $studentModel;

    public function __construct(PDO $pdo)
    {
        $this->userModel    = new UserModel($pdo);
        $this->studentModel = new StudentModel($pdo);
    }

    public function handleRequest(string $view): void
    {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_POST['add-user'])) {
                $name      = trim($_POST['name'] ?? '');
                $email     = trim($_POST['email'] ?? '');
                $password  = $_POST['password'] ?? '';
                $role      = $_POST['role'] ?? 'user';
                $studentId = !empty($_POST['student_id']) ? (int)$_POST['student_id'] : null;

                $errors = [];
                if (!$name || !$email || !$password) $errors[] = 'Minden mező kötelező.';
                if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Érvénytelen e-mail cím formátum.';
                if ($email && $this->userModel->emailExists($email)) $errors[] = 'Ez az e-mail cím már foglalt.';
                if (strlen($password) < 6) $errors[] = 'A jelszónak legalább 6 karakter hosszúnak kell lennie.';
                if (!in_array($role, ['admin', 'editor', 'user'])) $errors[] = 'Érvénytelen szerepkör.';

                if ($errors) {
                    UserView::list($this->userModel->getAll(), $this->studentModel->getAll(), $errors);
                    return;
                }

                
                $this->userModel->create($name, $email, $password, $role, $studentId, true);
                header("Location: index.php?view=users");
                exit;
            }

            if (isset($_POST['update-role'])) {
                $id   = (int)$_POST['id'];
                $role = $_POST['role'] ?? 'user';
                if (in_array($role, ['admin', 'editor', 'user'])) {
                    $this->userModel->updateRole($id, $role);
                }
                header("Location: index.php?view=users");
                exit;
            }
        }

        if (isset($_GET['delete-user'])) {
            $id = (int)$_GET['delete-user'];
            if ($id !== Auth::currentUserId()) {
                $this->userModel->delete($id);
            }
            header("Location: index.php?view=users");
            exit;
        }

        UserView::list($this->userModel->getAll(), $this->studentModel->getAll());
    }
}
