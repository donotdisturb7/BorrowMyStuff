<?php
namespace App\Controller;
use App\View\Welcome\WelcomeView;

class WelcomeController {
    public function index() {
        // Afficher la page d'accueil
        echo WelcomeView::render();
    }
    
}