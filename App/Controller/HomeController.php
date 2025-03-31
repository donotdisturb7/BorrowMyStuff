<?php

namespace App\Controller;

use App\Model\ItemModel;
use App\View\Home\HomeView;

class HomeController {
    private $itemModel;
    
    public function __construct(ItemModel $itemModel) {
        $this->itemModel = $itemModel;
    }
    
    public function index() {
        // Get current page from query string
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $page = max(1, $page); // Ensure page is at least 1
        
        // Get items with pagination
        $result = $this->itemModel->getPaginatedItems($page, 6);
        
        // Check if user is admin
        $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
        
        // Get user details if logged in
        $userData = [
            'isAuthenticated' => isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true,
            'username' => $_SESSION['username'] ?? null,
            'role' => $_SESSION['role'] ?? 'guest',
            'userId' => $_SESSION['user_id'] ?? null
        ];
        
        // Render the home view
        echo HomeView::render([
            'items' => $result['items'],
            'pagination' => $result['pagination'],
            'isAdmin' => $isAdmin,
            'user' => $userData
        ]);
    }
}