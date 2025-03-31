<?php
namespace App\Controller;

use App\Model\LoanModel;
use App\Model\ItemModel;

class LoanController {
    private $loanModel;
    private $itemModel;
    
    public function __construct(LoanModel $loanModel = null, ItemModel $itemModel = null) {
        $this->loanModel = $loanModel ?? new LoanModel();
        $this->itemModel = $itemModel ?? new ItemModel();
        
        // Ensure user is authenticated
        if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Process a loan request
     */
    public function requestLoan() {
        // Ensure it's a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /items');
            exit;
        }
        
        // Validate input
        $itemId = $_POST['item_id'] ?? 0;
        $startDate = $_POST['start_date'] ?? '';
        $endDate = $_POST['end_date'] ?? '';
        $message = $_POST['message'] ?? '';
        
        // Basic validation
        $errors = [];
        if (empty($itemId)) {
            $errors[] = 'Objet invalide';
        }
        if (empty($startDate)) {
            $errors[] = 'Date de début requise';
        }
        if (empty($endDate)) {
            $errors[] = 'Date de fin requise';
        }
        if (strtotime($startDate) >= strtotime($endDate)) {
            $errors[] = 'La date de fin doit être postérieure à la date de début';
        }
        if (strtotime($startDate) < strtotime('today')) {
            $errors[] = 'La date de début doit être au moins aujourd\'hui';
        }
        
        // Check if item exists and is available
        $item = $this->itemModel->getItemById($itemId);
        if (!$item) {
            $errors[] = 'Objet introuvable';
        } elseif (!$item['available']) {
            $errors[] = 'Cet objet n\'est pas disponible pour un prêt';
        }
        
        if (!empty($errors)) {
            $_SESSION['loan_errors'] = $errors;
            header('Location: /items/' . $itemId);
            exit;
        }
        
        // Create the loan request
        $userId = $_SESSION['user_id'];
        $ownerId = $item['owner_id'];
        
        // Make sure user is not trying to borrow their own item
        if ($userId == $ownerId) {
            $_SESSION['loan_errors'] = ['Vous ne pouvez pas emprunter votre propre objet'];
            header('Location: /items/' . $itemId);
            exit;
        }
        
        // Create loan request
        $result = $this->loanModel->createLoan(
            $itemId,
            $userId,
            date('Y-m-d'),
            $startDate,
            $endDate,
            $message
        );
        
        if ($result) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Votre demande de prêt a été envoyée avec succès.'
            ];
            header('Location: /dashboard');
        } else {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de la création de votre demande.'
            ];
            header('Location: /items/' . $itemId);
        }
        exit;
    }
    
    /**
     * Cancel a loan request
     */
    public function cancelLoan($id) {
        // Ensure it's a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard');
            exit;
        }
        
        // Validate CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['notification'] = [
                'message' => 'Action non autorisée - token CSRF invalide',
                'type' => 'error'
            ];
            header('Location: /dashboard');
            exit;
        }
        
        // Get loan details
        $loan = $this->loanModel->getLoanById($id);
        
        if (!$loan) {
            $_SESSION['notification'] = [
                'message' => 'Demande de prêt introuvable',
                'type' => 'error'
            ];
            header('Location: /dashboard');
            exit;
        }
        
        // Check user permissions
        $userId = $_SESSION['user_id'];
        
        // Normal users can only cancel their own requests or for their own items
        if ($loan['requester_id'] != $userId && $loan['owner_id'] != $userId) {
            $_SESSION['notification'] = [
                'message' => 'Vous n\'êtes pas autorisé à annuler cette demande de prêt',
                'type' => 'error'
            ];
            header('Location: /dashboard');
            exit;
        }
        
        // Cancel the loan
        $result = $this->loanModel->updateLoanStatus($id, 'cancelled');
        
        if ($result['success']) {
            // Update the item's availability
            $this->itemModel->updateItemAvailability($loan['item_id'], true);
            
            $_SESSION['notification'] = [
                'message' => 'La demande de prêt a été annulée avec succès',
                'type' => 'success'
            ];
        } else {
            $_SESSION['notification'] = [
                'message' => 'Erreur lors de l\'annulation de la demande de prêt',
                'type' => 'error'
            ];
        }
        
        header('Location: /dashboard');
        exit;
    }
    
    /**
     * Mark a loan as returned
     */
    public function markAsReturned($id) {
        // Ensure it's a POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /dashboard');
            exit;
        }
        
        // Get the loan
        $loan = $this->loanModel->getLoanById($id);
        
        // Make sure the user is admin or owns the item that was loaned
        $isAdmin = $_SESSION['role'] === 'admin';
        $userId = $_SESSION['user_id'];
        $item = $this->itemModel->getItemById($loan['item_id']);
        
        if (!$loan || ($item['owner_id'] != $userId && !$isAdmin)) {
            header('Location: /dashboard');
            exit;
        }
        
        // Only accepted loans can be marked as returned
        if ($loan['status'] !== 'accepted') {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Seuls les prêts acceptés peuvent être marqués comme retournés.'
            ];
            header('Location: /dashboard');
            exit;
        }
        
        // Update loan status to 'returned'
        $result = $this->loanModel->updateLoanStatus($id, 'returned');
        
        if ($result['success']) {
            // Update item availability
            $this->itemModel->updateItemAvailability($loan['item_id'], true);
            
            $_SESSION['notification'] = [
                'message' => 'L\'objet a été marqué comme retourné avec succès.',
                'type' => 'success'
            ];
        } else {
            $_SESSION['notification'] = [
                'message' => isset($result['error']) ? 
                    'Erreur : ' . $result['error'] : 
                    'Une erreur est survenue lors du retour de l\'objet.',
                'type' => 'error'
            ];
        }
        
        header('Location: /dashboard');
        exit;
    }
} 