<?php

class MaintenanceView
{
    public static function home(): void
    {
        echo '<h1>Karbantartás</h1>';
        echo '<ul>';
        echo '<li><a href="index.php?view=maintenance-generate">Adatok generálása</a> – törli a meglévő adatokat és új mintaadatokat hoz létre</li>';
        echo '<li><a href="index.php?view=maint-subjects">Tantárgyak</a></li>';
        echo '<li><a href="index.php?view=maint-classes">Osztályok</a></li>';
        echo '<li><a href="index.php?view=maint-students">Tanulók</a></li>';
        echo '<li><a href="index.php?view=maint-marks">Jegyek</a></li>';
        echo '</ul>';
    }


    public static function generateConfirm(): void
    {
        echo '<h1>Adatok generálása</h1>';
        echo '<p><strong>Figyelem!</strong> Ez a művelet törli az összes meglévő adatot (tantárgyak, osztályok, tanulók, jegyek) és újakat generál!</p>';
        echo '<form method="post" action="index.php?view=maintenance-generate">';
        echo '<button type="submit" name="confirm-generate" onclick="return confirm(\'Biztosan törli az összes adatot és újat generál?\')">Generálás indítása</button> ';
        echo '<a href="index.php?view=maintenance">Mégse</a>';
        echo '</form>';
    }

    public static function generateResult(array $messages): void
    {
        echo '<h1>Adatgenerálás eredménye</h1>';
        echo '<ul>';
        foreach ($messages as $msg) {
            echo '<li>' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        echo '</ul>';
        echo '<p><a href="index.php?view=maintenance">Vissza a karbantartáshoz</a></p>';
    }

  
    public static function subjectList(array $subjects): void
    {
        echo '<h1>Tantárgyak karbantartása</h1>';
        echo '<p>';
        echo '<a href="index.php?view=maint-subjects&edit=0">Új tantárgy</a> | ';
        echo '<a href="index.php?view=maintenance">Vissza</a>';
        echo '</p>';

        echo '<table border="1" cellpadding="5">';
        echo '<tr><th>ID</th><th>Név</th><th>Műveletek</th></tr>';
        foreach ($subjects as $s) {
            $id   = (int)$s['id'];
            $name = htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8');
            echo "<tr><td>{$id}</td><td>{$name}</td><td>";
            echo "<a href='index.php?view=maint-subjects&edit={$id}'>Módosítás</a> | ";
            echo "<a href='index.php?view=maint-subjects&delete={$id}' onclick=\"return confirm('Törli a tantárgyat?')\">Törlés</a>";
            echo "</td></tr>";
        }
        echo '</table>';
    }

    public static function subjectForm(?array $subject = null): void
    {
        $id   = $subject['id'] ?? 0;
        $name = htmlspecialchars($subject['name'] ?? '', ENT_QUOTES, 'UTF-8');
        $title = $id ? 'Tantárgy módosítása' : 'Új tantárgy';

        echo "<h1>{$title}</h1>";
        echo '<form method="post" action="index.php?view=maint-subjects">';
        if ($id) {
            echo "<input type='hidden' name='id' value='{$id}'>";
        }
        echo '<label>Tantárgy neve:</label><br>';
        echo "<input type='text' name='name' value='{$name}' required><br><br>";
        $submitName = $id ? 'update-subject' : 'add-subject';
        echo "<button type='submit' name='{$submitName}'>Mentés</button> ";
        echo '<a href="index.php?view=maint-subjects">Mégse</a>';
        echo '</form>';
    }

  
    public static function classList(array $classes): void
    {
        echo '<h1>Osztályok karbantartása</h1>';
        echo '<p>';
        echo '<a href="index.php?view=maint-classes&edit=0">Új osztály</a> | ';
        echo '<a href="index.php?view=maintenance">Vissza</a>';
        echo '</p>';

        echo '<table border="1" cellpadding="5">';
        echo '<tr><th>ID</th><th>Tanév</th><th>Osztály</th><th>Műveletek</th></tr>';
        foreach ($classes as $c) {
            $id        = (int)$c['id'];
            $schoolYear = $c['year'] . '/' . ($c['year'] + 1);
            $className  = $c['grade'] . '.' . htmlspecialchars($c['letter'], ENT_QUOTES, 'UTF-8');
            echo "<tr><td>{$id}</td><td>{$schoolYear}</td><td>{$className}</td><td>";
            echo "<a href='index.php?view=maint-classes&edit={$id}'>Módosítás</a> | ";
            echo "<a href='index.php?view=maint-classes&delete={$id}' onclick=\"return confirm('Törli az osztályt?')\">Törlés</a>";
            echo "</td></tr>";
        }
        echo '</table>';
    }

    public static function classForm(?array $class = null): void
    {
        $id     = $class['id'] ?? 0;
        $year   = htmlspecialchars((string)($class['year'] ?? date('Y')), ENT_QUOTES, 'UTF-8');
        $grade  = htmlspecialchars((string)($class['grade'] ?? ''), ENT_QUOTES, 'UTF-8');
        $letter = htmlspecialchars($class['letter'] ?? '', ENT_QUOTES, 'UTF-8');
        $title  = $id ? 'Osztály módosítása' : 'Új osztály';

        echo "<h1>{$title}</h1>";
        echo '<form method="post" action="index.php?view=maint-classes">';
        if ($id) {
            echo "<input type='hidden' name='id' value='{$id}'>";
        }
        echo '<label>Tanév (kezdő év, pl. 2024):</label><br>';
        echo "<input type='number' name='year' value='{$year}' required><br><br>";
        echo '<label>Évfolyam:</label><br>';
        echo "<input type='number' name='grade' value='{$grade}' min='1' max='13' required><br><br>";
        echo '<label>Osztály betűjele:</label><br>';
        echo "<input type='text' name='letter' value='{$letter}' maxlength='2' required><br><br>";
        $submitName = $id ? 'update-class' : 'add-class';
        echo "<button type='submit' name='{$submitName}'>Mentés</button> ";
        echo '<a href="index.php?view=maint-classes">Mégse</a>';
        echo '</form>';
    }

    public static function studentList(array $students, array $classes): void
    {
        echo '<h1>Tanulók karbantartása</h1>';
        echo '<p>';
        echo '<a href="index.php?view=maint-students&edit=0">Új tanuló</a> | ';
        echo '<a href="index.php?view=maintenance">Vissza</a>';
        echo '</p>';

        echo '<table border="1" cellpadding="5">';
        echo '<tr><th>ID</th><th>Név</th><th>Születési dátum</th><th>Osztály</th><th>Műveletek</th></tr>';
        foreach ($students as $s) {
            $id    = (int)$s['id'];
            $name  = htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8');
            $birth = htmlspecialchars($s['birth_date'], ENT_QUOTES, 'UTF-8');
            $cls   = '';
            if (!empty($s['year'])) {
                $cls = $s['year'] . '/' . ($s['year'] + 1) . ' ' . $s['grade'] . '.' . $s['letter'];
            }
            echo "<tr><td>{$id}</td><td>{$name}</td><td>{$birth}</td><td>{$cls}</td><td>";
            echo "<a href='index.php?view=maint-students&edit={$id}'>Módosítás</a> | ";
            echo "<a href='index.php?view=maint-students&delete={$id}' onclick=\"return confirm('Törli a tanulót?')\">Törlés</a>";
            echo "</td></tr>";
        }
        echo '</table>';
    }

    public static function studentForm(array $classes, ?array $student = null): void
    {
        $id      = $student['id'] ?? 0;
        $name    = htmlspecialchars($student['name'] ?? '', ENT_QUOTES, 'UTF-8');
        $birth   = htmlspecialchars($student['birth_date'] ?? '', ENT_QUOTES, 'UTF-8');
        $classId = $student['class_id'] ?? 0;
        $title   = $id ? 'Tanuló módosítása' : 'Új tanuló';

        echo "<h1>{$title}</h1>";
        echo '<form method="post" action="index.php?view=maint-students">';
        if ($id) {
            echo "<input type='hidden' name='id' value='{$id}'>";
        }
        echo '<label>Név:</label><br>';
        echo "<input type='text' name='name' value='{$name}' required><br><br>";
        echo '<label>Születési dátum:</label><br>';
        echo "<input type='date' name='birth_date' value='{$birth}' required><br><br>";
        echo '<label>Osztály:</label><br>';
        echo '<select name="class_id" required>';
        foreach ($classes as $c) {
            $cid       = $c['id'];
            $className = $c['year'] . '/' . ($c['year'] + 1) . ' ' . $c['grade'] . '.' . $c['letter'];
            $sel       = ($cid == $classId) ? 'selected' : '';
            echo "<option value='{$cid}' {$sel}>" . htmlspecialchars($className, ENT_QUOTES, 'UTF-8') . "</option>";
        }
        echo '</select><br><br>';
        $submitName = $id ? 'update-student' : 'add-student';
        echo "<button type='submit' name='{$submitName}'>Mentés</button> ";
        echo '<a href="index.php?view=maint-students">Mégse</a>';
        echo '</form>';
    }

    
    public static function markList(array $marks, array $students, array $subjects): void
    {
        echo '<h1>Jegyek karbantartása</h1>';
        echo '<p><a href="index.php?view=maintenance">Vissza</a></p>';

        echo '<h2>Új jegy hozzáadása</h2>';
        echo '<form method="post" action="index.php?view=maint-marks">';
        echo '<label>Tanuló:</label><br>';
        echo '<select name="student_id" required>';
        echo '<option value="">Válassz...</option>';
        foreach ($students as $s) {
            $sid   = $s['id'];
            $sname = htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8');
            echo "<option value='{$sid}'>{$sname}</option>";
        }
        echo '</select><br><br>';
        echo '<label>Tantárgy:</label><br>';
        echo '<select name="subject_id" required>';
        echo '<option value="">Válassz...</option>';
        foreach ($subjects as $sub) {
            $subId   = $sub['id'];
            $subName = htmlspecialchars($sub['name'], ENT_QUOTES, 'UTF-8');
            echo "<option value='{$subId}'>{$subName}</option>";
        }
        echo '</select><br><br>';
        echo '<label>Jegy (1–5):</label><br>';
        echo '<input type="number" name="mark" min="1" max="5" required><br><br>';
        echo '<button type="submit" name="add-mark">Hozzáadás</button>';
        echo '</form>';

        echo '<h2>Összes jegy</h2>';
        echo '<table border="1" cellpadding="5">';
        echo '<tr><th>ID</th><th>Tanuló</th><th>Tantárgy</th><th>Jegy</th><th>Törlés</th></tr>';
        foreach ($marks as $m) {
            $id      = (int)$m['id'];
            $sName   = htmlspecialchars($m['student_name'], ENT_QUOTES, 'UTF-8');
            $subName = htmlspecialchars($m['subject_name'], ENT_QUOTES, 'UTF-8');
            $mark    = (int)$m['mark'];
            echo "<tr><td>{$id}</td><td>{$sName}</td><td>{$subName}</td><td>{$mark}</td>";
            echo "<td><a href='index.php?view=maint-marks&delete={$id}' onclick=\"return confirm('Törli a jegyet?')\">Törlés</a></td>";
            echo "</tr>";
        }
        echo '</table>';
    }
}
