<?php

class SubjectView
{
    public static function list($subjects)
    {
        echo <<<HTML
            <h1>Tantárgyak</h1>

            <p><a href="index.php?view=add-subject">Új tantárgy hozzáadása</a></p>

            <table border="1" cellpadding="5">
                <tr>
                    <th>ID</th>
                    <th>Név</th>
                    <th>Műveletek</th>
                </tr>
        HTML;

        foreach ($subjects as $s) {
            $id = $s['id'];
            $name = htmlspecialchars($s['name'], ENT_QUOTES, 'UTF-8');

            echo <<<HTML
                <tr>
                    <td>{$id}</td>
                    <td>{$name}</td>
                    <td>
                        <a href="index.php?view=edit-subject&id={$id}">Módosítás</a> |
                        <a href="index.php?view=subjects&delete={$id}"
                           onclick="return confirm('Biztos törlöd?')">Törlés</a>
                    </td>
                </tr>
            HTML;
        }

        echo "</table>";
    }

    public static function addForm()
    {
        echo <<<HTML
            <h1>Új tantárgy hozzáadása</h1>

            <form method="post" action="index.php?view=subjects">
                <label>Tantárgy neve:</label><br>
                <input type="text" name="name"><br><br>

                <button type="submit" name="add-subject">Hozzáadás</button>
                <a href="index.php?view=subjects">Mégse</a>
            </form>
        HTML;
    }

    public static function editForm($subject)
    {
        $id = $subject['id'];
        $name = htmlspecialchars($subject['name'], ENT_QUOTES, 'UTF-8');

        echo <<<HTML
            <h1>Tantárgy módosítása</h1>

            <form method="post" action="index.php?view=subjects">
                <input type="hidden" name="id" value="{$id}">

                <label>Új név:</label><br>
                <input type="text" name="name" value="{$name}"><br><br>

                <button type="submit" name="update-subject">Mentés</button>
                <a href="index.php?view=subjects">Mégse</a>
            </form>
        HTML;
    }
}
