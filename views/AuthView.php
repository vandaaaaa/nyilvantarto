<?php

class AuthView
{
    public static function loginForm(?string $error = null): void
    {
        $err = $error ? "<p style='color:red'>" . htmlspecialchars($error) . "</p>" : '';
        echo <<<HTML
        <h2>Bejelentkezés</h2>
        $err
        <form method="post" action="index.php?view=login">
            <table>
                <tr>
                    <td><label for="email">E-mail cím:</label></td>
                    <td><input type="email" id="email" name="email" required autofocus></td>
                </tr>
                <tr>
                    <td><label for="password">Jelszó:</label></td>
                    <td><input type="password" id="password" name="password" required></td>
                </tr>
                <tr>
                    <td></td>
                    <td><button type="submit">Bejelentkezés</button></td>
                </tr>
            </table>
        </form>
        <p>Még nincs fiókod? <a href="index.php?view=register">Regisztráció</a></p>
        HTML;
    }

    public static function registerForm(array $errors = []): void
    {
        $errHtml = '';
        if ($errors) {
            $errHtml = '<ul style="color:red">';
            foreach ($errors as $e) {
                $errHtml .= "<li>" . htmlspecialchars($e) . "</li>";
            }
            $errHtml .= '</ul>';
        }

        echo <<<HTML
        <h2>Regisztráció</h2>
        $errHtml
        <form method="post" action="index.php?view=register">
            <table>
                <tr>
                    <td><label for="name">Teljes név:</label></td>
                    <td><input type="text" id="name" name="name" required autofocus maxlength="100"></td>
                </tr>
                <tr>
                    <td><label for="email">E-mail cím:</label></td>
                    <td><input type="email" id="email" name="email" required></td>
                </tr>
                <tr>
                    <td><label for="password">Jelszó:</label></td>
                    <td><input type="password" id="password" name="password" required minlength="6"></td>
                </tr>
                <tr>
                    <td><label for="password2">Jelszó megerősítése:</label></td>
                    <td><input type="password" id="password2" name="password2" required minlength="6"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><button type="submit">Regisztráció</button></td>
                </tr>
            </table>
        </form>
        <p>Már van fiókod? <a href="index.php?view=login">Bejelentkezés</a></p>
        HTML;
    }

    public static function registerSuccess(string $email): void
    {
        $safeEmail = htmlspecialchars($email);
        echo <<<HTML
        <h2>Regisztráció sikeres!</h2>
        <p>Megerősítő e-mailt küldtünk a <strong>$safeEmail</strong> címre.</p>
        <p>Kérjük, kattints a levélben lévő linkre a regisztráció befejezéséhez.</p>
        <p><em>Ha nem találod a levelet, ellenőrizd a spam mappát is.</em></p>
        <p><a href="index.php?view=login">Vissza a bejelentkezéshez</a></p>
        HTML;
    }

    public static function verifyResult(bool $success, string $message = ''): void
    {
        if ($success) {
            echo <<<HTML
            <h2>E-mail megerősítve!</h2>
            <p style="color:green">A regisztrációd sikeresen befejeződött. Most már bejelentkezhetsz.</p>
            <p><a href="index.php?view=login">Bejelentkezés</a></p>
            HTML;
        } else {
            $safeMsg = htmlspecialchars($message);
            echo <<<HTML
            <h2>Megerősítés sikertelen</h2>
            <p style="color:red">$safeMsg</p>
            <p><a href="index.php?view=register">Új regisztráció</a> | <a href="index.php?view=login">Bejelentkezés</a></p>
            HTML;
        }
    }

    public static function profileForm(array $user, array $errors = [], bool $saved = false): void
    {
        $errHtml = '';
        if ($errors) {
            $errHtml = '<ul style="color:red">';
            foreach ($errors as $e) {
                $errHtml .= "<li>" . htmlspecialchars($e) . "</li>";
            }
            $errHtml .= '</ul>';
        }

        $savedMsg  = $saved ? "<p style='color:green'>Adatok mentve!</p>" : '';
        $safeName  = htmlspecialchars($user['name'] ?? '');
        $safeEmail = htmlspecialchars($user['email'] ?? '');

        echo <<<HTML
        <h2>Profilom</h2>
        $savedMsg
        $errHtml
        <form method="post" action="index.php?view=profile">
            <table>
                <tr>
                    <td><label for="name">Teljes név:</label></td>
                    <td><input type="text" id="name" name="name" value="$safeName" required maxlength="100"></td>
                </tr>
                <tr>
                    <td>E-mail cím:</td>
                    <td><strong>$safeEmail</strong> <em>(nem módosítható)</em></td>
                </tr>
                <tr>
                    <td colspan="2"><hr><em>Jelszó változtatáshoz töltsd ki az alábbi mezőket (üresen hagyva nem változik):</em></td>
                </tr>
                <tr>
                    <td><label for="password">Új jelszó:</label></td>
                    <td><input type="password" id="password" name="password" minlength="6"></td>
                </tr>
                <tr>
                    <td><label for="password2">Jelszó megerősítése:</label></td>
                    <td><input type="password" id="password2" name="password2" minlength="6"></td>
                </tr>
                <tr>
                    <td></td>
                    <td><button type="submit">Mentés</button></td>
                </tr>
            </table>
        </form>
        HTML;
    }
}
