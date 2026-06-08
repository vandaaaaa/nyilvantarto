<?php

class ListsView
{
    public static function classList(
        array $years,
        array $classesInYear,
        array $students,
        $classAvg,
        array $subjectAvgs,
        $selectedYear,
        $selectedClassId,
        ?array $selectedClass
    ): void {
        echo '<h1>Listák</h1>';

        
        echo '<form method="get">';
        echo '<input type="hidden" name="view" value="lists">';

        echo '<label>Tanév:</label> ';
        echo '<select name="year" onchange="this.form.submit()">';
        echo '<option value="">Válassz tanévet...</option>';
        foreach ($years as $y) {
            $sel = ((string)$y === (string)$selectedYear) ? 'selected' : '';
            echo "<option value='{$y}' {$sel}>{$y}/" . ($y + 1) . "</option>";
        }
        echo '</select>';

        if ($selectedYear && !empty($classesInYear)) {
            echo ' &nbsp; <label>Osztály:</label> ';
            echo '<select name="class_id" onchange="this.form.submit()">';
            echo '<option value="">Válassz osztályt...</option>';
            foreach ($classesInYear as $c) {
                $cid  = $c['id'];
                $cName = $c['grade'] . '.' . htmlspecialchars($c['letter'], ENT_QUOTES, 'UTF-8');
                $sel  = ((string)$cid === (string)$selectedClassId) ? 'selected' : '';
                echo "<option value='{$cid}' {$sel}>{$cName}</option>";
            }
            echo '</select>';
        }

        echo '</form><br>';

        if (!$selectedClassId || !$selectedClass) {
            return;
        }

       
        $y     = $selectedClass['year'];
        $cName = $y . '/' . ($y + 1) . ' ' . $selectedClass['grade'] . '.' .
                 htmlspecialchars($selectedClass['letter'], ENT_QUOTES, 'UTF-8');
        echo "<h2>{$cName} osztály</h2>";

        
        if ($classAvg !== null) {
            $avg = round((float)$classAvg, 2);
            echo "<p><strong>Osztály tanulmányi átlaga:</strong> {$avg}</p>";
        } else {
            echo '<p>Nincs jegy adat az osztályhoz.</p>';
        }

        
        if (!empty($subjectAvgs)) {
            echo '<h3>Tantárgyi átlagok</h3>';
            echo '<table border="1" cellpadding="5">';
            echo '<tr><th>Tantárgy</th><th>Átlag</th><th>Jegyek száma</th></tr>';
            foreach ($subjectAvgs as $sa) {
                $sName = htmlspecialchars($sa['subject_name'], ENT_QUOTES, 'UTF-8');
                $sAvg  = round((float)$sa['avg_mark'], 2);
                $cnt   = (int)$sa['mark_count'];
                echo "<tr><td>{$sName}</td><td>{$sAvg}</td><td>{$cnt}</td></tr>";
            }
            echo '</table>';
        }

      
        echo '<h3>Tanulók</h3>';
        if (empty($students)) {
            echo '<p>Nincs tanuló ebben az osztályban.</p>';
            return;
        }

        echo '<table border="1" cellpadding="5">';
        echo '<tr><th>Név</th><th>Tanulmányi átlag</th><th>Részletek</th></tr>';
        foreach ($students as $s) {
            $sid   = (int)$s['id'];
            $sName = htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8');
            $sAvg  = ($s['avg_mark'] !== null) ? round((float)$s['avg_mark'], 2) : '–';
            $link  = "index.php?view=lists-student&student_id={$sid}&back_class={$selectedClassId}&back_year={$selectedYear}";
            echo "<tr><td>{$sName}</td><td>{$sAvg}</td><td><a href='{$link}'>Megtekintés</a></td></tr>";
        }
        echo '</table>';
    }

    public static function studentDetail(
        array $student,
        ?array $class,
        $avg,
        array $subjectAvgs,
        array $marks
    ): void {
        $name  = htmlspecialchars($student['name'], ENT_QUOTES, 'UTF-8');
        $birth = htmlspecialchars($student['birth_date'], ENT_QUOTES, 'UTF-8');

        $backClassId = (int)($_GET['back_class'] ?? 0);
        $backYear    = htmlspecialchars($_GET['back_year'] ?? '', ENT_QUOTES, 'UTF-8');
        $backLink    = "index.php?view=lists&year={$backYear}&class_id={$backClassId}";

        echo "<h1>{$name}</h1>";
        echo "<p>Születési dátum: {$birth}</p>";

        if ($class) {
            $y     = $class['year'];
            $cName = $y . '/' . ($y + 1) . ' ' . $class['grade'] . '.' .
                     htmlspecialchars($class['letter'], ENT_QUOTES, 'UTF-8');
            echo "<p>Osztály: {$cName}</p>";
        }

        if ($avg !== null) {
            $avgR = round((float)$avg, 2);
            echo "<p><strong>Tanulmányi átlag:</strong> {$avgR}</p>";
        } else {
            echo '<p>Nincs jegy rögzítve.</p>';
        }

        if (!empty($subjectAvgs)) {
            echo '<h2>Tantárgyi átlagok</h2>';
            echo '<table border="1" cellpadding="5">';
            echo '<tr><th>Tantárgy</th><th>Átlag</th><th>Jegyek száma</th></tr>';
            foreach ($subjectAvgs as $sa) {
                $sName = htmlspecialchars($sa['subject_name'], ENT_QUOTES, 'UTF-8');
                $sAvg  = round((float)$sa['avg_mark'], 2);
                $cnt   = (int)$sa['mark_count'];
                echo "<tr><td>{$sName}</td><td>{$sAvg}</td><td>{$cnt}</td></tr>";
            }
            echo '</table>';
        }

        if (!empty($marks)) {
            echo '<h2>Összes jegy</h2>';
            echo '<table border="1" cellpadding="5">';
            echo '<tr><th>Tantárgy</th><th>Jegy</th></tr>';
            foreach ($marks as $m) {
                $subj = htmlspecialchars($m['subject_name'], ENT_QUOTES, 'UTF-8');
                $mark = (int)$m['mark'];
                echo "<tr><td>{$subj}</td><td>{$mark}</td></tr>";
            }
            echo '</table>';
        }

        echo "<p><a href='{$backLink}'>&larr; Vissza az osztályhoz</a></p>";
    }
}
