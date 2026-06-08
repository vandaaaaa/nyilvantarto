<?php
require_once "Auth.php";
Auth::startSession();

require_once "views/LayoutView.php";
require_once "views/HomeView.php";
require_once "views/SubjectView.php";
require_once "models/SubjectModel.php";
require_once "controllers/SubjectController.php";
require_once "controllers/Router.php";

$pdo = new PDO("mysql:host=localhost;dbname=school;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$view   = $_GET['view'] ?? 'home';
$router = new Router($pdo);


$publicViews = ['login', 'register', 'logout', 'verify-email'];

if (!in_array($view, $publicViews) && !Auth::isLoggedIn()) {
    header("Location: index.php?view=login");
    exit;
}

LayoutView::head();
LayoutView::menu();

$router->handle($view);

LayoutView::footer();
