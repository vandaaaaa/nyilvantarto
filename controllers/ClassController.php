<?php
require_once "models/ClassModel.php";
require_once "views/ClassView.php";
require_once "Auth.php";

class ClassController
{
    private ClassModel $model;

    public function __construct(PDO $pdo)
    {
        $this->model = new ClassModel($pdo);
    }

    private function normalizeLetter($letter): string
    {
        $letter = trim((string)$letter);
        if (function_exists('mb_strtoupper')) {
            $letter = mb_strtoupper($letter, 'UTF-8');
        } else {
            $letter = strtoupper($letter);
        }
        return $letter;
    }

    public function handleRequest(string $view)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Auth::requireEdit();

            if (isset($_POST['add-class'])) {
                $year   = (int)($_POST['year'] ?? 0);
                $grade  = (int)($_POST['grade'] ?? 0);
                $letter = $this->normalizeLetter($_POST['letter'] ?? '');
                $this->model->create($year, $grade, $letter);
                header("Location: index.php?view=classes");
                exit;
            }

            if (isset($_POST['update-class'])) {
                $id     = (int)($_POST['id'] ?? 0);
                $year   = (int)($_POST['year'] ?? 0);
                $grade  = (int)($_POST['grade'] ?? 0);
                $letter = $this->normalizeLetter($_POST['letter'] ?? '');
                $this->model->update($id, $year, $grade, $letter);
                header("Location: index.php?view=classes");
                exit;
            }
        }

        if (isset($_GET['delete'])) {
            Auth::requireEdit();
            $this->model->delete((int)$_GET['delete']);
            header("Location: index.php?view=classes");
            exit;
        }

        switch ($view) {
            case 'classes':
                $classes = $this->model->getAll();
                ClassView::list($classes);
                break;

            case 'add-class':
                Auth::requireEdit();
                ClassView::addForm();
                break;

            case 'edit-class':
                Auth::requireEdit();
                $class = $this->model->find((int)($_GET['id'] ?? 0));
                ClassView::editForm($class);
                break;
        }
    }
}
