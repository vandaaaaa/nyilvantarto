<?php
require_once "models/StudentModel.php";
require_once "models/ClassModel.php";
require_once "views/StudentView.php";
require_once "Auth.php";

class StudentController
{
    private StudentModel $model;
    private ClassModel $classModel;

    public function __construct(PDO $pdo)
    {
        $this->model      = new StudentModel($pdo);
        $this->classModel = new ClassModel($pdo);
    }

    public function handleRequest(string $view)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Auth::requireEdit();

            $name     = trim($_POST['name'] ?? '');
            $birth    = $_POST['birth_date'] ?? '';
            $class_id = (int)($_POST['class_id'] ?? 0);

            if (!$name || !$birth || !$class_id) {
                die("Minden mező kötelező");
            }

            if (isset($_POST['add-student'])) {
                $this->model->create($class_id, $name, $birth);
            }

            if (isset($_POST['update-student'])) {
                $this->model->update((int)$_POST['id'], $class_id, $name, $birth);
            }

            header("Location: index.php?view=students");
            exit;
        }

        if (isset($_GET['delete'])) {
            Auth::requireEdit();
            $this->model->delete((int)$_GET['delete']);
            header("Location: index.php?view=students");
            exit;
        }

        switch ($view) {
            case 'students':
                $students = $this->model->getAll();
                StudentView::list($students);
                break;

            case 'students-by-year':
                $years    = $this->model->getYears();
                $year     = $_GET['year'] ?? null;
                $class_id = $_GET['class_id'] ?? null;
                $classes  = [];
                $students = [];
                if ($year) {
                    $classes  = $this->model->getClassesByBirthYear($year);
                    $students = $this->model->getStudentsByYearAndClass($year, $class_id);
                }
                StudentView::listByYear($years, $classes, $students, $year, $class_id);
                break;

            case 'classes-by-year':
                $years = $this->classModel->getYears();
                $year  = $_GET['year'] ?? null;
                $data  = $year ? $this->classModel->getClassesByYear($year) : [];
                StudentView::classesByYear($years, $data, $year);
                break;

            case 'add-student':
                Auth::requireEdit();
                $classes = $this->classModel->getAll();
                StudentView::form($classes);
                break;

            case 'edit-student':
                Auth::requireEdit();
                $student = $this->model->find((int)$_GET['id']);
                $classes = $this->classModel->getAll();
                StudentView::form($classes, $student);
                break;
        }
    }
}
