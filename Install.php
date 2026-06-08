<?php

class Install
{
    private PDO $pdo;

    private array $subjectNames = [
        'Magyar nyelv és irodalom',
        'Matematika',
        'Történelem',
        'Fizika',
        'Kémia',
        'Biológia',
        'Földrajz',
        'Angol nyelv',
        'Informatika',
        'Testnevelés',
    ];

    private array $firstNames = [
        'Anna', 'Béla', 'Csilla', 'Dávid', 'Eszter',
        'Ferenc', 'Gabriella', 'Henrik', 'Ildikó', 'János',
        'Katalin', 'László', 'Mária', 'Nóra', 'Olivér',
        'Péter', 'Réka', 'Sándor', 'Tímea', 'Ádám',
        'Balázs', 'Dorka', 'Erika', 'Gábor', 'Helga',
        'István', 'Júlia', 'Krisztina', 'Levente', 'Mónika',
    ];

    private array $lastNames = [
        'Kiss', 'Nagy', 'Varga', 'Tóth', 'Horváth',
        'Szabó', 'Kovács', 'Papp', 'Balogh', 'Fekete',
        'Molnár', 'Takács', 'Simon', 'Lukács', 'Farkas',
        'Szűcs', 'Oláh', 'Mészáros', 'Hajdu', 'Fülöp',
    ];

    private array $classLetters = ['A', 'B', 'C', 'D', 'E'];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function generate(): array
    {
        $messages = [];

        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $this->pdo->exec("TRUNCATE TABLE marks");
        $this->pdo->exec("TRUNCATE TABLE students");
        $this->pdo->exec("TRUNCATE TABLE classes");
        $this->pdo->exec("TRUNCATE TABLE subjects");
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

        $subjectIds = $this->generateSubjects();
        $messages[] = count($subjectIds) . ' tantárgy létrehozva.';

        $startYear = (int)date('Y') - 3;
        $years = [$startYear, $startYear + 1, $startYear + 2];

        $totalClasses = 0;
        $totalStudents = 0;
        $totalMarks = 0;

        foreach ($years as $year) {
            $classCount = rand(4, 5);
            $letters = array_slice($this->classLetters, 0, $classCount);
            $grade = rand(9, 12);

            foreach ($letters as $letter) {
                $classId = $this->createClass($year, $grade, $letter);
                $totalClasses++;

                $studentCount = rand(12, 15);
                for ($i = 0; $i < $studentCount; $i++) {
                    $studentId = $this->createStudent($classId, $year);
                    $totalStudents++;

                    $subjectSample = $this->randomSubjects($subjectIds, 5);
                    foreach ($subjectSample as $subjectId) {
                        $markCount = rand(3, 4);
                        for ($j = 0; $j < $markCount; $j++) {
                            $this->createMark($studentId, $subjectId);
                            $totalMarks++;
                        }
                    }
                }
            }
        }

        $messages[] = $totalClasses . ' osztály létrehozva (' . count($years) . ' tanévben).';
        $messages[] = $totalStudents . ' tanuló létrehozva.';
        $messages[] = $totalMarks . ' jegy létrehozva.';

        return $messages;
    }

    private function generateSubjects(): array
    {
        $ids = [];
        $stmt = $this->pdo->prepare("INSERT INTO subjects (name) VALUES (:name)");
        foreach ($this->subjectNames as $name) {
            $stmt->execute(['name' => $name]);
            $ids[] = (int)$this->pdo->lastInsertId();
        }
        return $ids;
    }

    private function createClass(int $year, int $grade, string $letter): int
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO classes (year, grade, letter) VALUES (:year, :grade, :letter)"
        );
        $stmt->execute(['year' => $year, 'grade' => $grade, 'letter' => $letter]);
        return (int)$this->pdo->lastInsertId();
    }

    private function createStudent(int $classId, int $schoolYear): int
    {
        $firstName = $this->firstNames[array_rand($this->firstNames)];
        $lastName  = $this->lastNames[array_rand($this->lastNames)];
        $name = $lastName . ' ' . $firstName;

        $birthYear  = $schoolYear - rand(14, 16);
        $birthMonth = rand(1, 12);
        $birthDay   = rand(1, 28);
        $birthDate  = sprintf('%04d-%02d-%02d', $birthYear, $birthMonth, $birthDay);

        $stmt = $this->pdo->prepare(
            "INSERT INTO students (class_id, name, birth_date) VALUES (:class_id, :name, :birth_date)"
        );
        $stmt->execute(['class_id' => $classId, 'name' => $name, 'birth_date' => $birthDate]);
        return (int)$this->pdo->lastInsertId();
    }

    private function createMark(int $studentId, int $subjectId): void
    {
        $mark = rand(1, 5);
        $stmt = $this->pdo->prepare(
            "INSERT INTO marks (student_id, subject_id, mark) VALUES (:student_id, :subject_id, :mark)"
        );
        $stmt->execute(['student_id' => $studentId, 'subject_id' => $subjectId, 'mark' => $mark]);
    }

    private function randomSubjects(array $subjectIds, int $count): array
    {
        $keys = array_rand($subjectIds, min($count, count($subjectIds)));
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        return array_map(fn($k) => $subjectIds[$k], $keys);
    }
}
