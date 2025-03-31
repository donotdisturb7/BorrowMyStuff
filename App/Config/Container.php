<?php

namespace App\Config;

use App\Config\Database;
use App\Controller\AuthController;
use App\Controller\DashboardController;
use App\Controller\WelcomeController;
use App\Core\App;
use App\Helper\Validator;
use App\Model\UserModel;
use App\Services\AuthService;
use App\Services\Container;
use App\View\Dashboard\DashboardView;
use App\View\Welcome\WelcomeView;
use App\Controller\HomeController;
use App\Model\ItemModel;
use App\Controller\ItemController;
use App\Controller\ErrorController;
use App\Controller\LoanController;
use App\Controller\LoanRequestController;
use App\Model\LoanModel;

$container = new Container();


$container->set(Validator::class, function () {
    return new Validator();
});

$container->set(Database::class, function () {
    return new Database();
});


$container->set(UserModel::class, function ($container) {
    $db = $container->get(Database::class);
    return new UserModel($db);
});

$container->set(AuthService::class, function ($container) {
    $validator = $container->get(Validator::class);
    $model = $container->get(UserModel::class);
    return new AuthService($model, $validator);
});

$container->set(AuthController::class, function ($container) {
    $service = $container->get(AuthService::class);
    return new AuthController($service);
});

$container->set(DashboardController::class, function () {
    return new DashboardController();
});

$container->set(DashboardView::class, function () {
    return new DashboardView();
});

$container->set(WelcomeController::class, function () {
    return new WelcomeController();
});


$container->set(HomeController::class, function ($container) {
    $itemModel = $container->get(ItemModel::class);
    return new HomeController($itemModel);
});

$container->set(ItemModel::class, function () {
    return new ItemModel();
});
    
$container->set(ItemController::class, function ($container) {
    $itemModel = $container->get(ItemModel::class);
    return new ItemController($itemModel);
});

$container->set(ErrorController::class, function () {
    return new ErrorController();
});

$container->set(LoanController::class, function ($container) {
    $loanModel = $container->get(LoanModel::class);
    return new LoanController($loanModel);
});

$container->set(LoanRequestController::class, function ($container) {
    return new LoanRequestController();
});

$container->set(LoanModel::class, function () {
    return new LoanModel();
});

// Register Cache Service
$container->set(\App\Services\CacheService::class, function () {
    return new \App\Services\CacheService();
});

// Register Auth Middleware
$container->set(\App\Middleware\AuthMiddleware::class, function () {
    return new \App\Middleware\AuthMiddleware();
});

// Register Rate Limiter
$container->set(\App\Middleware\RateLimiter::class, function () {
    return new \App\Middleware\RateLimiter();
});

App::setContainer($container);