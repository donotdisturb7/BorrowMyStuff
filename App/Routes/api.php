<?php

use App\Controller\AuthController;
use App\Controller\DashboardController;
use App\Controller\ItemController;
use App\Routes\Router;
use App\Controller\WelcomeController;
use App\Controller\HomeController;
use App\Controller\LoanController;
use App\Controller\LoanRequestController;

// Page d'accueil
Router::get('/', [WelcomeController::class, 'index']);

// Route pour la pagination des objets sur la page d'accueil
Router::get('/page', [HomeController::class, 'index']);

// Routes d'authentification
Router::get('/register', [AuthController::class, 'showSignupForm']);
Router::post('/register', [AuthController::class, 'register']);
Router::get('/login', [AuthController::class, 'showLoginForm']);
Router::post('/login', [AuthController::class, 'login']);
Router::get('/logout', [AuthController::class, 'logOut']);

// Routes du tableau de bord
Router::get('/dashboard', [DashboardController::class, 'index']);

// Routes de recherche et navigation
Router::get('/home', [HomeController::class, 'index']);


// Routes de gestion des items
Router::get('/items', [HomeController::class, 'index']);
Router::get('/items/create', function() {
    // Redirects to add item tab in dashboard for admins
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header('Location: /dashboard?tab=add-item');
        exit;
    }
    // Redirect others to login
    header('Location: /login');
    exit;
});
Router::post('/items/store', [ItemController::class, 'store']);
Router::get('/items/{id}', [ItemController::class, 'show']);
Router::get('/items/{id}/edit', [ItemController::class, 'edit']);
Router::post('/items/{id}/update', [ItemController::class, 'update']);
Router::post('/items/{id}/delete', [ItemController::class, 'delete']);

// Routes de gestion des prêts
Router::post('/loans/request', [LoanRequestController::class, 'create']);
Router::post('/loans/{id}/approve', [LoanRequestController::class, 'accept']);
Router::post('/loans/{id}/reject', [LoanRequestController::class, 'reject']);
Router::post('/loans/{id}/cancel', [LoanController::class, 'cancelLoan']);
Router::post('/loans/{id}/return', [LoanController::class, 'markAsReturned']);

  
