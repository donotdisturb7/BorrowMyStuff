<?php
// filepath: /home/dnd/School/ArchitectureLogiciel/TP1-SiteGestionDePret/App/Controller/DashboardController.php
namespace App\Controller;

use App\View\Dashboard\DashboardView;
use App\Model\ItemModel;
use App\Model\LoanModel;
use App\Model\UserModel;

class DashboardController {
    private $itemModel;
    private $loanModel;
    private $userModel;

    public function __construct(ItemModel $itemModel = null, LoanModel $loanModel = null, UserModel $userModel = null) {
        // Create models if not injected
        $this->itemModel = $itemModel ?? new ItemModel();
        $this->loanModel = $loanModel ?? new LoanModel();
        $this->userModel = $userModel ?? new UserModel();
        
        // Ensure user is authenticated
        if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
            header('Location: /login');
            exit;
        }
    }

    public function index() {
        // Get tab from query string
        $activeTab = $_GET['tab'] ?? 'dashboard';
        
        // Get user data
        $userData = [
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role']
        ];
        
        // Get categories for the add item form
        $categories = $this->itemModel->getCategories();
        
        // Check for form errors in session
        $errors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_errors']);
        
        // Check for success message in URL
        if (isset($_GET['success']) && $_GET['success'] === 'true' && !isset($_SESSION['notification'])) {
            $_SESSION['notification'] = [
                'message' => 'L\'opération a été effectuée avec succès.',
                'type' => 'success'
            ];
        }
        
        // Check if user is admin
        $isAdmin = isset($_SESSION['role']) && ($_SESSION['role'] === 'admin');
        
        if ($isAdmin) {
            // Admin dashboard
            
        
            $pendingLoans = $this->loanModel->getPendingLoans();
            
           
            $adminLoans = $this->loanModel->getLoansByBorrower($userData['user_id']);
            
            
            $adminItemLoans = $this->loanModel->getActiveItemLoansByOwner($userData['user_id']);
            
            
            $allItems = $this->itemModel->getAllItems();
            
           
            $stats = [
                'totalItems' => is_array($allItems) ? count($allItems) : 0,
                'availableItems' => is_array($allItems) ? count(array_filter($allItems, function($item) {
                    return $item['available'] == 1;
                })) : 0,
                'pendingLoans' => count($pendingLoans)
            ];
            
            // Render admin dashboard view
            echo DashboardView::renderAdmin([
                'user' => $userData,
                'pendingLoans' => $pendingLoans,
                'adminLoans' => $adminLoans,
                'adminItemLoans' => $adminItemLoans,
                'stats' => $stats,
                'activeTab' => $activeTab,
                'categories' => $categories,
                'errors' => $errors
            ]);
        } else {
            // Regular user dashboard
            $userLoans = $this->loanModel->getLoansByBorrower($userData['user_id']);
            $userItems = $this->itemModel->getItemsByOwnerId($userData['user_id']);
            
            // Render user dashboard view
            echo DashboardView::renderUser([
                'user' => $userData,
                'loans' => $userLoans,
                'items' => $userItems,
                'activeTab' => $activeTab
            ]);
        }
    }
    
    public function approveLoan($id) {
        // Check admin privilege
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }
        
        $adminNotes = $_POST['notes'] ?? '';
        $result = $this->loanModel->updateLoanStatus($id, 'approved', $adminNotes);
        
        // Redirect back to dashboard
        header('Location: /dashboard');
        exit;
    }
    
    public function rejectLoan($id) {
        // Check admin privilege
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }
        
        $adminNotes = $_POST['notes'] ?? '';
        $result = $this->loanModel->updateLoanStatus($id, 'rejected', $adminNotes);
        
        // Redirect back to dashboard
        header('Location: /dashboard');
        exit;
    }
    
    public function loanDetails($id) {
        // Get loan details
        $loan = $this->loanModel->getLoanById($id);
        
        if (!$loan) {
            header('Location: /dashboard');
            exit;
        }
        
        // Check if user can view this loan (admin or involved party)
        $userId = $_SESSION['user_id'] ?? 0;
        $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
        
        if (!$isAdmin && $loan['borrower_id'] != $userId && $loan['owner_id'] != $userId) {
            header('Location: /dashboard');
            exit;
        }
        
        // Render loan details view
        echo DashboardView::renderLoanDetails([
            'loan' => $loan,
            'isAdmin' => $isAdmin
        ]);
    }
}