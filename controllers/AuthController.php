<?php
require_once "models/UserModel.php";
require_once "Auth.php";
require_once "views/AuthView.php";

class AuthController
{
    private UserModel $userModel;
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo       = $pdo;
        $this->userModel = new UserModel($pdo);
    }

    public function handleRequest(string $view): void
    {
        switch ($view) {

            case 'login':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->processLogin();
                } else {
                    AuthView::loginForm();
                }
                break;

            case 'logout':
                Auth::logout();
                header("Location: index.php?view=login");
                exit;

            case 'register':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->processRegister();
                } else {
                    AuthView::registerForm();
                }
                break;

            case 'verify-email':
                $this->processVerifyEmail();
                break;

            case 'profile':
                Auth::requireLogin();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->processProfile();
                } else {
                    $user = $this->userModel->findById(Auth::currentUserId());
                    AuthView::profileForm($user);
                }
                break;
        }
    }

    private function processLogin(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            AuthView::loginForm('Kérlek töltsd ki az összes mezőt.');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            AuthView::loginForm('Érvénytelen e-mail cím formátum.');
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            AuthView::loginForm('Hibás e-mail cím vagy jelszó.');
            return;
        }

        if (!$user['email_verified']) {
            AuthView::loginForm('Az e-mail cím nincs megerősítve. Ellenőrizd a postaládádat!');
            return;
        }

        Auth::login($user);
        header("Location: index.php?view=home");
        exit;
    }

    private function processRegister(): void
    {
        $name      = trim($_POST['name'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $password  = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        $errors = [];

        if (!$name || !$email || !$password || !$password2) {
            $errors[] = 'Minden mező kötelező.';
        }

        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Érvénytelen e-mail cím formátum.';
        }

        if (strlen($password) < 6) {
            $errors[] = 'A jelszónak legalább 6 karakter hosszúnak kell lennie.';
        }

        if ($password !== $password2) {
            $errors[] = 'A két jelszó nem egyezik.';
        }

        if (!$errors && $this->userModel->emailExists($email)) {
            $errors[] = 'Ez az e-mail cím már regisztrálva van.';
        }

        if ($errors) {
            AuthView::registerForm($errors);
            return;
        }

      
        $token        = bin2hex(random_bytes(32));
        $tokenExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $this->userModel->create($name, $email, $password, 'user', null, false, $token, $tokenExpires);

      
        $sent = $this->sendVerificationEmail($email, $name, $token);

        if ($sent) {
            AuthView::registerSuccess($email);
        } else {
           
            AuthView::registerForm(['Regisztráció sikeres, de az e-mail küldés sikertelen. Kérj segítséget!']);
        }
    }

    private function processVerifyEmail(): void
    {
        $token = trim($_GET['token'] ?? '');

        if (!$token) {
            AuthView::verifyResult(false, 'Hiányzó token.');
            return;
        }

        $user = $this->userModel->findByVerifyToken($token);

        if (!$user) {
            AuthView::verifyResult(false, 'Érvénytelen vagy lejárt megerősítő link.');
            return;
        }

        $this->userModel->verifyEmail($user['id']);
        AuthView::verifyResult(true);
    }

    private function processProfile(): void
    {
        $id       = Auth::currentUserId();
        $name     = trim($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';
        $password2= $_POST['password2'] ?? '';

        $errors = [];

        if (!$name) {
            $errors[] = 'A név nem lehet üres.';
        }

        if ($password && strlen($password) < 6) {
            $errors[] = 'A jelszónak legalább 6 karakter hosszúnak kell lennie.';
        }

        if ($password && $password !== $password2) {
            $errors[] = 'A két jelszó nem egyezik.';
        }

        if ($errors) {
            $user = $this->userModel->findById($id);
            AuthView::profileForm($user, $errors);
            return;
        }

        $this->userModel->updateProfile($id, $name, $password ?: null);

        
        $_SESSION['name'] = $name;

        header("Location: index.php?view=profile&saved=1");
        exit;
    }

    private function sendVerificationEmail(string $email, string $name, string $token): bool
    {
   
        $mailerFile = __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
        $smtpFile   = __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
        $excFile    = __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';

        if (!file_exists($mailerFile)) {
     
            return $this->sendVerificationEmailNative($email, $name, $token);
        }

        require_once $excFile;
        require_once $smtpFile;
        require_once $mailerFile;

        $baseUrl = $this->getBaseUrl();
        $link    = $baseUrl . "index.php?view=verify-email&token=" . urlencode($token);

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'localhost'; // MailHog
            $mail->Port       = 1025;
            $mail->SMTPAuth   = false;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom('noreply@iskola.hu', 'Iskolai Nyilvántartó');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = 'E-mail cím megerősítése';
            $mail->Body    = "
                <p>Kedves <strong>" . htmlspecialchars($name) . "</strong>!</p>
                <p>Köszönjük a regisztrációt! Kérjük, erősítsd meg az e-mail címed az alábbi linkre kattintva:</p>
                <p><a href=\"$link\">$link</a></p>
                <p>A link 24 óráig érvényes.</p>
                <p>Ha nem te regisztráltál, hagyd figyelmen kívül ezt az e-mailt.</p>
            ";
            $mail->AltBody = "Megerősítő link: $link";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("PHPMailer hiba: " . $mail->ErrorInfo);
            return false;
        }
    }

    private function sendVerificationEmailNative(string $email, string $name, string $token): bool
    {
        $baseUrl = $this->getBaseUrl();
        $link    = $baseUrl . "index.php?view=verify-email&token=" . urlencode($token);

        $subject = '=?UTF-8?B?' . base64_encode('E-mail cím megerősítése') . '?=';
        $message = "Kedves $name!\r\n\r\n";
        $message .= "Erősítsd meg az e-mail címed:\r\n$link\r\n\r\n";
        $message .= "A link 24 óráig érvényes.";
        $headers  = "From: noreply@iskola.hu\r\nContent-Type: text/plain; charset=UTF-8";

        return mail($email, $subject, $message, $headers);
    }

    private function getBaseUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $path   = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        $path   = rtrim($path, '/') . '/';
        return "$scheme://$host$path";
    }
}
