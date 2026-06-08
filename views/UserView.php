<?php

class UserView
{
    public static function list(array $users, array $students, array $errors = []): void
    {
        $errHtml = '';
        if ($errors) {
            $errHtml = '<ul style="color:red">';
            foreach ($errors as $e) {
                $errHtml .= "<li>" . htmlspecialchars($e) . "</li>";
            }
            $errHtml .= '</ul>';
        }

        $roleLabels = ['admin' => 'Rendszergazda', 'editor' => 'Szerkesztő', 'user' => 'Felhasználó'];

        $rows = '';
        foreach ($users as $u) {
            $id          = (int)$u['id'];
            $name        = htmlspecialchars($u['name'] ?? '');
            $email       = htmlspecialchars($u['email']);
            $roleLabel   = $roleLabels[$u['role']] ?? $u['role'];
            $studentName = $u['student_name'] ? htmlspecialchars($u['student_name']) : '–';
            $verified    = $u['email_verified'] ? '✓' : '✗';

            $roleOptions = '';
            foreach ($roleLabels as $val => $label) {
                $sel = $u['role'] === $val ? 'selected' : '';
                $roleOptions .= "<option value=\"$val\" $sel>$label</option>";
            }

            $rows .= <<<HTML
            <tr>
                <td>$id</td>
                <td>$name</td>
                <td>$email</td>
                <td>$verified</td>
                <td>
                    <form method="post" action="index.php?view=users" style="display:inline">
                        <input type="hidden" name="id" value="$id">
                        <select name="role">$roleOptions</select>
                        <button type="submit" name="update-role">Mentés</button>
                    </form>
                </td>
                <td>$studentName</td>
                <td><a href="index.php?view=users&delete-user=$id" onclick="return confirm('Biztosan törlöd?')">Törlés</a></td>
            </tr>
            HTML;
        }

    
        $studentOptions = '<option value="">– nincs –</option>';
        foreach ($students as $s) {
            $sId   = (int)$s['id'];
            $sName = htmlspecialchars($s['name']);
            $studentOptions .= "<option value=\"$sId\">$sName</option>";
        }

        $roleOptionsNew = '';
        foreach ($roleLabels as $val => $label) {
            $roleOptionsNew .= "<option value=\"$val\">$label</option>";
        }

        echo <<<HTML
        <h2>Felhasználók kezelése</h2>
        $errHtml

        <h3>Felhasználók listája</h3>
        <table border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Név</th>
                    <th>E-mail cím</th>
                    <th>Megerősített</th>
                    <th>Szerepkör</th>
                    <th>Kapcsolt diák</th>
                    <th>Műveletek</th>
                </tr>
            </thead>
            <tbody>
                $rows
            </tbody>
        </table>

        <hr>
        <h3>Új felhasználó hozzáadása</h3>
        <form method="post" action="index.php?view=users">
            <table>
                <tr>
                    <td>Teljes név:</td>
                    <td><input type="text" name="name" required maxlength="100"></td>
                </tr>
                <tr>
                    <td>E-mail cím:</td>
                    <td><input type="email" name="email" required maxlength="100"></td>
                </tr>
                <tr>
                    <td>Jelszó:</td>
                    <td><input type="password" name="password" required minlength="6"></td>
                </tr>
                <tr>
                    <td>Szerepkör:</td>
                    <td><select name="role">$roleOptionsNew</select></td>
                </tr>
                <tr>
                    <td>Kapcsolt diák (opcionális):</td>
                    <td><select name="student_id">$studentOptions</select></td>
                </tr>
                <tr>
                    <td></td>
                    <td><button type="submit" name="add-user">Hozzáadás</button></td>
                </tr>
            </table>
        </form>
        HTML;
    }
}
