<?php

class ClassModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        return $this->pdo
            ->query("SELECT * FROM classes ORDER BY year DESC, grade ASC, letter ASC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM classes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($year, $grade, $letter)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO classes (year, grade, letter)
            VALUES (:year, :grade, :letter)
        ");

        $stmt->execute([
            'year' => $year,
            'grade' => $grade,
            'letter' => $letter,
        ]);
    }

    public function update($id, $year, $grade, $letter)
    {
        $stmt = $this->pdo->prepare("
            UPDATE classes
            SET year = :year, grade = :grade, letter = :letter
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $id,
            'year' => $year,
            'grade' => $grade,
            'letter' => $letter,
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM classes WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function getYears()
    {
        return $this->pdo
            ->query("
                SELECT DISTINCT year
                FROM classes
                ORDER BY year DESC
            ")
            ->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getClassesByYear($year)
    {
        $stmt = $this->pdo->prepare("
            SELECT c.*, s.id as student_id, s.name as student_name
            FROM classes c
            LEFT JOIN students s ON s.class_id = c.id
            WHERE c.year = :year
            ORDER BY c.grade ASC, c.letter ASC, s.name ASC
        ");

        $stmt->execute(['year' => $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}