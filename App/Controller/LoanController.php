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
            $_SESSION['loan_success'] = 'Votre demande de prêt a été envoyée avec succès.';
            header('Location: /dashboard');
        } else {
            $_SESSION['loan_errors'] = ['Une erreur est survenue lors de la création de votre demande.'];
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
        
        // Get the loan
        $loan = $this->loanModel->getLoanById($id);
        
        // Make sure the user owns this loan request
        if (!$loan || $loan['requester_id'] != $_SESSION['user_id']) {
            header('Location: /dashboard');
            exit;
        }
        
        // Only pending loans can be cancelled
        if ($loan['status'] !== 'pending') {
            $_SESSION['loan_errors'] = ['Cette demande ne peut plus être annulée car elle a déjà été traitée.'];
            header('Location: /dashboard');
            exit;
        }
        
        // Update loan status to 'cancelled'
        $result = $this->loanModel->updateLoanStatus($id, 'cancelled');
        
        if ($result) {
            $_SESSION['loan_success'] = 'Votre demande de prêt a été annulée.';
        } else {
            $_SESSION['loan_errors'] = ['Une erreur est survenue lors de l\'annulation de votre demande.'];
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
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Ce prêt ne peut pas être marqué comme retourné.'
            ];
            header('Location: /dashboard');
            exit;
        }
        
        // Update loan status to 'returned'
        $result = $this->loanModel->updateLoanStatus($id, 'returned');
        
        if ($result) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Le prêt a été marqué comme retourné avec succès.'
            ];
        } else {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors du marquage du prêt comme retourné.'
            ];
        }
        
        header('Location: /dashboard');
        exit;
    }
} 