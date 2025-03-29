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
Router::get('/search', [HomeController::class, 'search']);

// Routes de gestion des items
Router::get('/items', [HomeController::class, 'index']);
Router::get('/items/create', function() {
    header('Location: /dashboard?tab=add-item');
    exit;
});
Router::post('/items/store', [ItemController::class, 'store']);

// Routes de gestion des prêts
Router::post('/loans/request', [LoanRequestController::class, 'create']);
Router::post('/loans/{id}/approve', [LoanRequestController::class, 'accept']);
Router::post('/loans/{id}/reject', [LoanRequestController::class, 'reject']);
Router::post('/loans/{id}/cancel', [LoanController::class, 'cancelLoan']);
Router::post('/loans/{id}/return', [LoanController::class, 'markAsReturned']);
  
