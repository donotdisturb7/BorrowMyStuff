<?php

namespace App\Controller;

use App\Services\AuthService;
use App\View\Login\SignInView;
use App\View\Login\SignUpView;

class AuthController {
    
    public function __construct(private AuthService $service) {
    }

    public function showSignupForm($errors = null) {
     
        echo SignUpView::render($errors);
    }

    public function showLoginForm($errors = null) {
        
        echo SignInView::render($errors);
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $validationResult = $this->service->validateRegistration($_POST);

            if (!$validationResult['success']) {
                $this->showSignupForm($validationResult['errors']);
                return;
            }

            $result = $this->service->register($_POST);

            if (!$result['success']) {
                $this->showSignupForm($result['errors']);
                return;
            }
            
            // Redirect to login page after successful registration
            header('Location: /login');
            exit;
        } else {
            // Show the form for GET requests
            $this->showSignupForm();
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $validationResult = $this->service->validateLogin($_POST);

            if (!$validationResult['success']) {
                $this->showLoginForm($validationResult['errors']);
                return;
            }

            $result = $this->service->login($_POST);

            if (!$result['success']) {
                $errors = $result['errors'] ?? '';
                $this->showLoginForm($errors);
                return;
            }
            
            // Régénération de l'ID de session après connexion
            session_regenerate_id(true);
            
            // Redirect to dashboard after successful login
            header('Location: /home');
            exit;
        } else {
            // Show the form for GET requests
            $this->showLoginForm();
        }
    }

    public function logOut() {
        // Clean user data from session
        $_SESSION = [];
        
        // Regenerate and destroy session ID
        session_regenerate_id(true);
        setcookie(session_name(), '', time() - 42000, '/');
        session_destroy();
        
        // Remove any other cookies set by the application
        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - 3600, '/');
        }
        
        header('Location: /login');
        exit;
    }
}