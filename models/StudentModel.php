<?php

class StudentModel
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
                SELECT s.*, c.year, c.grade, c.letter
                FROM students s
                LEFT JOIN classes c ON s.class_id = c.id
                ORDER BY s.id DESC
            ")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM students WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($class_id, $name, $birth_date)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO students (class_id, name, birth_date)
            VALUES (:class_id, :name, :birth_date)
        ");

        $stmt->execute([
            'class_id' => $class_id,
            'name' => $name,
            'birth_date' => $birth_date
        ]);
    }

    public function update($id, $class_id, $name, $birth_date)
    {
        $stmt = $this->pdo->prepare("
            UPDATE students
            SET class_id = :class_id,
                name = :name,
                birth_date = :birth_date
            WHERE id = :id
        ");

        $stmt->execute([
            'id' => $id,
            'class_id' => $class_id,
            'name' => $name,
            'birth_date' => $birth_date
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM students WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public function getYears()
    {
        return $this->pdo
            ->query("
                SELECT DISTINCT YEAR(birth_date) as year
                FROM students
                ORDER BY year DESC
            ")
            ->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getClassesByBirthYear($year)
    {
        $stmt = $this->pdo->prepare("
            SELECT DISTINCT c.id, c.year, c.grade, c.letter
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE YEAR(s.birth_date) = :year
            ORDER BY c.grade ASC, c.letter ASC
        ");
        $stmt->execute(['year' => $year]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentsByYearAndClass($year, $class_id = null)
    {
        $sql = "
            SELECT s.*, c.year as class_year, c.grade, c.letter
            FROM students s
            LEFT JOIN classes c ON s.class_id = c.id
            WHERE YEAR(s.birth_date) = :year
        ";

        $params = ['year' => $year];

        if ($class_id) {
            $sql .= " AND s.class_id = :class_id";
            $params['class_id'] = $class_id;
        }

        $sql .= " ORDER BY s.birth_date DESC, s.name ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}