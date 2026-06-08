<?php

class StudentView
{
    public static function list($students)
    {
        echo '<h1>Tanulók</h1>';
        echo '<p>
                <a href="index.php?view=add-student">Új tanuló</a> |
                <a href="index.php?view=students-by-year">Születési év szerint</a> |
                <a href="index.php?view=classes-by-year">Osztályok év szerint</a>
              </p>';

        echo '<table border="1" cellpadding="5">
                <tr>
                    <th>ID</th>
                    <th>Név</th>
                    <th>Születési dátum</th>
                    <th>Osztály</th>
                    <th>Műveletek</th>
                </tr>';

        foreach ($students as $s) {
            $id = (int)$s['id'];
            $name = htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8');
            $birth = htmlspecialchars($s['birth_date'], ENT_QUOTES, 'UTF-8');

            $class = '';
            if (!empty($s['year'])) {
                $class = $s['year'].'/'.($s['year']+1).' '.$s['grade'].'.'.$s['letter'];
            }

            echo "<tr>
                    <td>{$id}</td>
                    <td>{$name}</td>
                    <td>{$birth}</td>
                    <td>{$class}</td>
                    <td>
                        <a href='index.php?view=edit-student&id={$id}'>Módosítás</a> |
                        <a href='index.php?view=students&delete={$id}'>Törlés</a>
                    </td>
                  </tr>";
        }

        echo '</table>';
    }

    public static function listByYear($years, $classes, $students, $selectedYear, $selectedClass)
    {
        echo '<h1>Tanulók születési év szerint</h1>';

        echo '<form method="get">
                <input type="hidden" name="view" value="students-by-year">
                <select name="year" onchange="this.form.submit()">';
        echo '<option value="">Válassz évet...</option>';

        foreach ($years as $year) {
            $sel = ($year == $selectedYear) ? 'selected' : '';
            echo "<option value='{$year}' {$sel}>{$year}</option>";
        }

        echo '</select>';

        if ($selectedYear) {
            echo '<select name="class_id" onchange="this.form.submit()">';
            echo '<option value="">Minden osztály</option>';

            foreach ($classes as $c) {
                $cid = $c['id'];
                $className = $c['year'].'/'.($c['year']+1).' '.$c['grade'].'.'.$c['letter'];
                $sel = ($cid == $selectedClass) ? 'selected' : '';
                echo "<option value='{$cid}' {$sel}>{$className}</option>";
            }

            echo '</select>';
        }

        echo '</form><br>';

        if ($selectedYear) {
            echo '<table border="1" cellpadding="5">
                    <tr>
                        <th>ID</th>
                        <th>Név</th>
                        <th>Születési dátum</th>
                        <th>Osztály</th>
                    </tr>';

            foreach ($students as $s) {
                $id = (int)$s['id'];
                $name = htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8');
                $birth = htmlspecialchars($s['birth_date'], ENT_QUOTES, 'UTF-8');

                $class = '';
                if (!empty($s['class_year'])) {
                    $class = $s['class_year'].'/'.($s['class_year']+1).' '.$s['grade'].'.'.$s['letter'];
                }

                echo "<tr>
                        <td>{$id}</td>
                        <td>{$name}</td>
                        <td>{$birth}</td>
                        <td>{$class}</td>
                      </tr>";
            }

            echo '</table>';
        }
    }

    public static function classesByYear($years, $data, $selectedYear)
    {
        echo '<h1>Osztályok év szerint</h1>';
        echo '<form method="get">
                <input type="hidden" name="view" value="classes-by-year">
                <select name="year" onchange="this.form.submit()">';
        echo '<option value="">Válassz...</option>';

        foreach ($years as $year) {
            $sel = ($year == $selectedYear) ? 'selected' : '';
            echo "<option value='{$year}' {$sel}>{$year}/".($year+1)."</option>";
        }

        echo '</select></form><br>';

        if (!$selectedYear) return;

        $grouped = [];
        foreach ($data as $row) {
            $key = $row['grade'].'.'.$row['letter'];
            $grouped[$key][] = $row;
        }

        foreach ($grouped as $class => $rows) {
            echo "<h3>{$class}</h3><ul>";
            foreach ($rows as $r) {
                if ($r['student_id']) {
                    $name = htmlspecialchars($r['student_name'], ENT_QUOTES, 'UTF-8');
                    echo "<li>{$name}</li>";
                }
            }
            echo "</ul>";
        }
    }

    public static function form($classes, $student = null)
    {
        $id = $student['id'] ?? '';
        $name = htmlspecialchars($student['name'] ?? '', ENT_QUOTES, 'UTF-8');
        $birth = htmlspecialchars($student['birth_date'] ?? '', ENT_QUOTES, 'UTF-8');
        $selectedClass = $student['class_id'] ?? '';

        echo '<h1>Tanuló</h1>';
        echo '<form method="post" action="index.php?view=students">';

        if ($student) {
            echo '<input type="hidden" name="id" value="'.$id.'">';
            echo '<input type="hidden" name="update-student" value="1">';
        } else {
            echo '<input type="hidden" name="add-student" value="1">';
        }

        echo '<label>Név:</label><br>';
        echo '<input type="text" name="name" value="'.$name.'" required><br><br>';

        echo '<label>Születési dátum:</label><br>';
        echo '<input type="date" name="birth_date" value="'.$birth.'" required><br><br>';

        echo '<label>Osztály:</label><br>';
        echo '<select name="class_id" required>';

        foreach ($classes as $c) {
            $cid = $c['id'];
            $className = $c['year'].'/'.($c['year']+1).' '.$c['grade'].'.'.$c['letter'];
            $sel = ($cid == $selectedClass) ? 'selected' : '';
            echo "<option value='{$cid}' {$sel}>{$className}</option>";
        }

        echo '</select><br><br>';
        echo '<button type="submit">Mentés</button>';
        echo '</form>';
    }
}