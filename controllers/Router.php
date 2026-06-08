<?php
require_once "Auth.php";
require_once "controllers/SubjectController.php";
require_once "controllers/ClassController.php";
require_once "controllers/MarkController.php";
require_once "controllers/AuthController.php";
require_once "views/HomeView.php";

class Router
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function handle(string $view): void
    {
        switch ($view) {

          
            case 'login':
            case 'register':
            case 'logout':
            case 'verify-email':
            case 'profile':
                $controller = new AuthController($this->pdo);
                $controller->handleRequest($view);
                break;

           
            case 'users':
                Auth::requireAdmin();
                require_once "controllers/UserController.php";
                $controller = new UserController($this->pdo);
                $controller->handleRequest($view);
                break;

           
            case 'subjects':
                Auth::requireLogin();
                $controller = new SubjectController($this->pdo);
                $controller->handleRequest($view);
                break;

            case 'add-subject':
            case 'edit-subject':
                Auth::requireEdit();
                $controller = new SubjectController($this->pdo);
                $controller->handleRequest($view);
                break;

           
            case 'classes':
                Auth::requireLogin();
                $controller = new ClassController($this->pdo);
                $controller->handleRequest($view);
                break;

            case 'add-class':
            case 'edit-class':
                Auth::requireEdit();
                $controller = new ClassController($this->pdo);
                $controller->handleRequest($view);
                break;

         
            case 'students':
            case 'students-by-year':
            case 'classes-by-year':
                Auth::requireLogin();
                require_once "controllers/StudentController.php";
                $controller = new StudentController($this->pdo);
                $controller->handleRequest($view);
                break;

            case 'add-student':
            case 'edit-student':
                Auth::requireEdit();
                require_once "controllers/StudentController.php";
                $controller = new StudentController($this->pdo);
                $controller->handleRequest($view);
                break;

    
            case 'marks':
                Auth::requireLogin();
                $controller = new MarkController($this->pdo);
                $controller->handleRequest($view);
                break;

         
            case 'lists':
            case 'lists-student':
                Auth::requireLogin();
                require_once "controllers/ListsController.php";
                $controller = new ListsController($this->pdo);
                $controller->handleRequest($view);
                break;

            
            case 'maintenance':
            case 'maintenance-generate':
            case 'maint-subjects':
            case 'maint-classes':
            case 'maint-students':
            case 'maint-marks':
                Auth::requireAdmin();
                require_once "controllers/MaintenanceController.php";
                require_once "views/MaintenanceView.php";
                if ($view === 'maintenance-generate' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
                    MaintenanceView::generateConfirm();
                } else {
                    $controller = new MaintenanceController($this->pdo);
                    $controller->handleRequest($view);
                }
                break;

            default:
                HomeView::render();
        }
    }
}
