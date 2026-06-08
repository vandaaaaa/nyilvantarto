<?php

class MarkModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        return $this->pdo
            ->query("
                SELECT m.*, s.name as student_name, sub.name as subject_name
                FROM marks m
                LEFT JOIN students s ON m.student_id = s.id
                LEFT JOIN subjects sub ON m.subject_id = sub.id
                ORDER BY m.id DESC
            ")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($student_id, $subject_id, $mark)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO marks (student_id, subject_id, mark)
            VALUES (:student_id, :subject_id, :mark)
        ");

        $stmt->execute([
            'student_id' => $student_id,
            'subject_id' => $subject_id,
            'mark' => $mark
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM marks WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function getStudentAverage($student_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT AVG(mark) as avg_mark
            FROM marks
            WHERE student_id = :id
        ");

        $stmt->execute(['id' => $student_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['avg_mark'];
    }

    public function getStudentMarks($student_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT m.*, sub.name as subject_name
            FROM marks m
            LEFT JOIN subjects sub ON m.subject_id = sub.id
            WHERE m.student_id = :id
            ORDER BY m.id DESC
        ");

        $stmt->execute(['id' => $student_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentSubjectAverages($student_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT sub.name as subject_name, AVG(m.mark) as avg_mark, COUNT(m.id) as mark_count
            FROM marks m
            LEFT JOIN subjects sub ON m.subject_id = sub.id
            WHERE m.student_id = :id
            GROUP BY m.subject_id, sub.name
            ORDER BY sub.name ASC
        ");
        $stmt->execute(['id' => $student_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClassAverage($class_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT AVG(m.mark) as avg_mark
            FROM marks m
            LEFT JOIN students s ON m.student_id = s.id
            WHERE s.class_id = :class_id
        ");
        $stmt->execute(['class_id' => $class_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['avg_mark'];
    }

    public function getClassSubjectAverages($class_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT sub.name as subject_name, AVG(m.mark) as avg_mark, COUNT(m.id) as mark_count
            FROM marks m
            LEFT JOIN students s ON m.student_id = s.id
            LEFT JOIN subjects sub ON m.subject_id = sub.id
            WHERE s.class_id = :class_id
            GROUP BY m.subject_id, sub.name
            ORDER BY sub.name ASC
        ");
        $stmt->execute(['class_id' => $class_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentsWithAverageByClass($class_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT s.id, s.name, s.birth_date, AVG(m.mark) as avg_mark
            FROM students s
            LEFT JOIN marks m ON m.student_id = s.id
            WHERE s.class_id = :class_id
            GROUP BY s.id, s.name, s.birth_date
            ORDER BY s.name ASC
        ");
        $stmt->execute(['class_id' => $class_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}