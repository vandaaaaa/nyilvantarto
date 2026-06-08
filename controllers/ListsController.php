<?php
require_once 'models/ClassModel.php';
require_once 'models/StudentModel.php';
require_once 'models/MarkModel.php';
require_once 'views/ListsView.php';

class ListsController
{
    private ClassModel $classModel;
    private StudentModel $studentModel;
    private MarkModel $markModel;

    public function __construct(PDO $pdo)
    {
        $this->classModel   = new ClassModel($pdo);
        $this->studentModel = new StudentModel($pdo);
        $this->markModel    = new MarkModel($pdo);
    }

    public function handleRequest(string $view): void
    {
        if ($view === 'lists-student') {
            $this->showStudentDetail();
            return;
        }

        
        $years   = $this->classModel->getYears();
        $year    = $_GET['year'] ?? null;
        $classId = $_GET['class_id'] ?? null;

        $classesInYear = [];
        $students      = [];
        $classAvg      = null;
        $subjectAvgs   = [];
        $selectedClass = null;

        if ($year) {
            $allClasses = $this->classModel->getAll();
            foreach ($allClasses as $c) {
                if ((string)$c['year'] === (string)$year) {
                    $classesInYear[] = $c;
                }
            }
        }

        if ($classId) {
            $selectedClass = $this->classModel->find((int)$classId);
            $students      = $this->markModel->getStudentsWithAverageByClass((int)$classId);
            $classAvg      = $this->markModel->getClassAverage((int)$classId);
            $subjectAvgs   = $this->markModel->getClassSubjectAverages((int)$classId);
        }

        ListsView::classList(
            $years,
            $classesInYear,
            $students,
            $classAvg,
            $subjectAvgs,
            $year,
            $classId,
            $selectedClass
        );
    }

    private function showStudentDetail(): void
    {
        $studentId = (int)($_GET['student_id'] ?? 0);
        $student   = $this->studentModel->find($studentId);

        if (!$student) {
            echo '<p>A tanuló nem található.</p>';
            echo '<p><a href="index.php?view=lists">Vissza</a></p>';
            return;
        }

        $avg         = $this->markModel->getStudentAverage($studentId);
        $subjectAvgs = $this->markModel->getStudentSubjectAverages($studentId);
        $marks       = $this->markModel->getStudentMarks($studentId);
        $class       = $this->classModel->find((int)$student['class_id']);

        ListsView::studentDetail($student, $class, $avg, $subjectAvgs, $marks);
    }
}
