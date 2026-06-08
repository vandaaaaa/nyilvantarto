<?php
require_once "models/MarkModel.php";
require_once "models/StudentModel.php";
require_once "models/SubjectModel.php";
require_once "views/MarkView.php";
require_once "Auth.php";

class MarkController
{
    private MarkModel $model;
    private StudentModel $studentModel;
    private SubjectModel $subjectModel;

    public function __construct(PDO $pdo)
    {
        $this->model        = new MarkModel($pdo);
        $this->studentModel = new StudentModel($pdo);
        $this->subjectModel = new SubjectModel($pdo);
    }

    public function handleRequest(string $view)
    {
        $canEdit = Auth::canEdit();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Auth::requireEdit();

            if (isset($_POST['add-mark'])) {
                $student = (int)$_POST['student_id'];
                $subject = (int)$_POST['subject_id'];
                $mark    = (int)$_POST['mark'];
                $this->model->create($student, $subject, $mark);
                header("Location: index.php?view=marks&student_id=" . $student);
                exit;
            }
        }

        if (isset($_GET['delete'])) {
            Auth::requireEdit();
            $student = $_GET['student_id'];
            $this->model->delete($_GET['delete']);
            header("Location: index.php?view=marks&student_id=" . $student);
            exit;
        }

      
        $students   = $this->studentModel->getAll();
        $student_id = $_GET['student_id'] ?? null;
        $marks      = [];
        $avg        = null;

        if ($student_id) {
            $marks = $this->model->getStudentMarks($student_id);
            $avg   = $this->model->getStudentAverage($student_id);
        }

        $subjects = $this->subjectModel->getAll();

        if ($canEdit) {
            MarkView::list($students, $subjects, $marks, $student_id, $avg);
        } else {
      
            MarkView::listReadOnly($marks, $avg, $student_id, $students);
        }
    }
}
