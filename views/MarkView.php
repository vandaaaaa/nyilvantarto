<?php

class MarkView
{
    public static function list($students, $subjects, $marks, $selectedStudent, $avg): void
    {
        echo '<h1>Jegyek</h1>';

        echo '<form method="get">
                <input type="hidden" name="view" value="marks">
                <select name="student_id" onchange="this.form.submit()">
                <option value="">Válassz tanulót</option>';

        foreach ($students as $s) {
            $id   = $s['id'];
            $name = htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8');
            $sel  = ($id == $selectedStudent) ? 'selected' : '';
            echo "<option value='{$id}' {$sel}>{$name}</option>";
        }

        echo '</select></form><br>';

        if (!$selectedStudent) return;

        echo '<h3>Új jegy</h3>';
        echo '<form method="post" action="index.php?view=marks">
                <input type="hidden" name="student_id" value="' . $selectedStudent . '">
                <select name="subject_id">';

        foreach ($subjects as $sub) {
            $sid  = $sub['id'];
            $name = htmlspecialchars($sub['name'], ENT_QUOTES, 'UTF-8');
            echo "<option value='{$sid}'>{$name}</option>";
        }

        echo '</select>
              <input type="number" name="mark" min="1" max="5">
              <button type="submit" name="add-mark">Hozzáadás</button>
              </form><br>';

        echo '<table border="1" cellpadding="5">
                <tr>
                    <th>Tantárgy</th>
                    <th>Jegy</th>
                    <th></th>
                </tr>';

        foreach ($marks as $m) {
            $id      = $m['id'];
            $subject = htmlspecialchars($m['subject_name'], ENT_QUOTES, 'UTF-8');
            $mark    = $m['mark'];
            echo "<tr>
                    <td>{$subject}</td>
                    <td>{$mark}</td>
                    <td>
                        <a href='index.php?view=marks&student_id={$selectedStudent}&delete={$id}'>Törlés</a>
                    </td>
                  </tr>";
        }

        echo '</table>';

        if ($avg) {
            $avg = round($avg, 2);
            echo "<h3>Átlag: {$avg}</h3>";
        }
    }


    public static function listReadOnly(array $marks, $avg, $selectedStudent, array $students = []): void
    {
        echo '<h1>Jegyek</h1>';

        if (!empty($students)) {
            echo '<form method="get">
                    <input type="hidden" name="view" value="marks">
                    <select name="student_id" onchange="this.form.submit()">
                    <option value="">Válassz tanulót</option>';
            foreach ($students as $s) {
                $id   = $s['id'];
                $name = htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8');
                $sel  = ($id == $selectedStudent) ? 'selected' : '';
                echo "<option value='{$id}' {$sel}>{$name}</option>";
            }
            echo '</select></form><br>';
        }

        if (!$selectedStudent) {
            echo '<p>Válassz tanulót a jegyek megtekintéséhez.</p>';
            return;
        }

        if (empty($marks)) {
            echo '<p>Ehhez a tanulóhoz még nincsenek jegyek rögzítve.</p>';
            return;
        }

        echo '<table border="1" cellpadding="5">
                <tr>
                    <th>Tantárgy</th>
                    <th>Jegy</th>
                </tr>';

        foreach ($marks as $m) {
            $subject = htmlspecialchars($m['subject_name'], ENT_QUOTES, 'UTF-8');
            $mark    = (int)$m['mark'];
            echo "<tr><td>{$subject}</td><td>{$mark}</td></tr>";
        }

        echo '</table>';

        if ($avg) {
            $avgRounded = round((float)$avg, 2);
            echo "<h3>Átlag: {$avgRounded}</h3>";
        }
    }
}
