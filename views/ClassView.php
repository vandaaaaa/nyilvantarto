<?php

class ClassView
{
    public static function list($classes)
    {
        echo <<<HTML
            <h1>Osztályok</h1>

            <p><a href="index.php?view=add-class">Új osztály hozzáadása</a></p>

            <table border="1" cellpadding="5">
                <tr>
                    <th>ID</th>
                    <th>Tanév</th>
                    <th>Osztály</th>
                    <th>Műveletek</th>
                </tr>
        HTML;

        foreach ($classes as $c) {
            $id = $c['id'];
            $year = (int)$c['year'];
            $grade = (int)$c['grade'];
            $letter = htmlspecialchars($c['letter'], ENT_QUOTES, 'UTF-8');

            $schoolYear = $year . '/' . ($year + 1);
            $className = $grade . '.' . $letter;

            echo <<<HTML
                <tr>
                    <td>{$id}</td>
                    <td>{$schoolYear}</td>
                    <td>{$className}</td>
                    <td>
                        <a href="index.php?view=edit-class&id={$id}">Módosítás</a> |
                        <a href="index.php?view=classes&delete={$id}" onclick="return confirm('Biztos törlöd?')">Törlés</a>
                    </td>
                </tr>
            HTML;
        }

        echo "</table>";
    }

    public static function addForm($defaults = null)
    {
        $year = $defaults['year'] ?? date('Y');
        $grade = $defaults['grade'] ?? '';
        $letter = $defaults['letter'] ?? '';

        $year = htmlspecialchars((string)$year, ENT_QUOTES, 'UTF-8');
        $grade = htmlspecialchars((string)$grade, ENT_QUOTES, 'UTF-8');
        $letter = htmlspecialchars((string)$letter, ENT_QUOTES, 'UTF-8');

        echo <<<HTML
            <h1>Új osztály hozzáadása</h1>

            <form method="post" action="index.php?view=classes">
                <label>Tanév (kezdő év, pl. 2025 a 2025/2026-hoz):</label><br>
                <input type="number" name="year" value="{$year}"><br><br>

                <label>Évfolyam (pl. 12):</label><br>
                <input type="number" name="grade" value="{$grade}"><br><br>

                <label>Osztály betűjele (pl. A):</label><br>
                <input type="text" name="letter" value="{$letter}" maxlength="2"><br><br>

                <button type="submit" name="add-class">Hozzáadás</button>
                <a href="index.php?view=classes">Mégse</a>
            </form>
        HTML;
    }

    public static function editForm($class)
    {
        $id = (int)$class['id'];
        $year = htmlspecialchars((string)$class['year'], ENT_QUOTES, 'UTF-8');
        $grade = htmlspecialchars((string)$class['grade'], ENT_QUOTES, 'UTF-8');
        $letter = htmlspecialchars((string)$class['letter'], ENT_QUOTES, 'UTF-8');

        echo <<<HTML
            <h1>Osztály módosítása</h1>

            <form method="post" action="index.php?view=classes">
                <input type="hidden" name="id" value="{$id}">

                <label>Tanév (kezdő év):</label><br>
                <input type="number" name="year" value="{$year}"><br><br>

                <label>Évfolyam:</label><br>
                <input type="number" name="grade" value="{$grade}"><br><br>

                <label>Osztály betűjele:</label><br>
                <input type="text" name="letter" value="{$letter}" maxlength="2"><br><br>

                <button type="submit" name="update-class">Mentés</button>
                <a href="index.php?view=classes">Mégse</a>
            </form>
        HTML;
    }
}
