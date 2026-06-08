<?php
require_once 'models/ClassModel.php';
require_once 'models/StudentModel.php';
require_once 'models/SubjectModel.php';
require_once 'models/MarkModel.php';
require_once 'views/MaintenanceView.php';
require_once 'Install.php';

class MaintenanceController
{
    private PDO $pdo;
    private ClassModel $classModel;
    private StudentModel $studentModel;
    private SubjectModel $subjectModel;
    private MarkModel $markModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo          = $pdo;
        $this->classModel   = new ClassModel($pdo);
        $this->studentModel = new StudentModel($pdo);
        $this->subjectModel = new SubjectModel($pdo);
        $this->markModel    = new MarkModel($pdo);
    }

    public function handleRequest(string $view): void
    {
       
        if ($view === 'maintenance-generate' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $install  = new Install($this->pdo);
            $messages = $install->generate();
            MaintenanceView::generateResult($messages);
            return;
        }

        
        if ($view === 'maint-subjects') {
            $this->handleSubjects();
            return;
        }

        
        if ($view === 'maint-classes') {
            $this->handleClasses();
            return;
        }

     
        if ($view === 'maint-students') {
            $this->handleStudents();
            return;
        }

        
        if ($view === 'maint-marks') {
            $this->handleMarks();
            return;
        }

        
        MaintenanceView::home();
    }

   
    private function handleSubjects(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['add-subject'])) {
                $this->subjectModel->create(trim($_POST['name'] ?? ''));
                $this->redirect('maint-subjects');
            }
            if (isset($_POST['update-subject'])) {
                $this->subjectModel->update((int)$_POST['id'], trim($_POST['name'] ?? ''));
                $this->redirect('maint-subjects');
            }
        }
        if (isset($_GET['delete'])) {
            $this->subjectModel->delete((int)$_GET['delete']);
            $this->redirect('maint-subjects');
        }
        if (isset($_GET['edit'])) {
            $subject = $this->subjectModel->find((int)$_GET['edit']);
            MaintenanceView::subjectForm($subject);
            return;
        }
        $subjects = $this->subjectModel->getAll();
        MaintenanceView::subjectList($subjects);
    }

 
    private function handleClasses(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $year   = (int)($_POST['year'] ?? 0);
            $grade  = (int)($_POST['grade'] ?? 0);
            $letter = mb_strtoupper(trim($_POST['letter'] ?? ''), 'UTF-8');

            if (isset($_POST['add-class'])) {
                $this->classModel->create($year, $grade, $letter);
                $this->redirect('maint-classes');
            }
            if (isset($_POST['update-class'])) {
                $this->classModel->update((int)$_POST['id'], $year, $grade, $letter);
                $this->redirect('maint-classes');
            }
        }
        if (isset($_GET['delete'])) {
            $this->classModel->delete((int)$_GET['delete']);
            $this->redirect('maint-classes');
        }
        if (isset($_GET['edit'])) {
            $class = $this->classModel->find((int)$_GET['edit']);
            MaintenanceView::classForm($class);
            return;
        }
        $classes = $this->classModel->getAll();
        MaintenanceView::classList($classes);
    }

  
    private function handleStudents(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $classId = (int)($_POST['class_id'] ?? 0);
            $name    = trim($_POST['name'] ?? '');
            $birth   = $_POST['birth_date'] ?? '';

            if (isset($_POST['add-student'])) {
                $this->studentModel->create($classId, $name, $birth);
                $this->redirect('maint-students');
            }
            if (isset($_POST['update-student'])) {
                $this->studentModel->update((int)$_POST['id'], $classId, $name, $birth);
                $this->redirect('maint-students');
            }
        }
        if (isset($_GET['delete'])) {
            $this->studentModel->delete((int)$_GET['delete']);
            $this->redirect('maint-students');
        }
        if (isset($_GET['edit'])) {
            $student = $this->studentModel->find((int)$_GET['edit']);
            $classes = $this->classModel->getAll();
            MaintenanceView::studentForm($classes, $student);
            return;
        }
        $students = $this->studentModel->getAll();
        $classes  = $this->classModel->getAll();
        MaintenanceView::studentList($students, $classes);
    }


    private function handleMarks(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['add-mark'])) {
                $this->markModel->create(
                    (int)$_POST['student_id'],
                    (int)$_POST['subject_id'],
                    (int)$_POST['mark']
                );
                $this->redirect('maint-marks');
            }
        }
        if (isset($_GET['delete'])) {
            $this->markModel->delete((int)$_GET['delete']);
            $this->redirect('maint-marks');
        }
        $marks    = $this->markModel->getAll();
        $students = $this->studentModel->getAll();
        $subjects = $this->subjectModel->getAll();
        MaintenanceView::markList($marks, $students, $subjects);
    }

    private function redirect(string $view): void
    {
        header("Location: index.php?view={$view}");
        exit;
    }
}
