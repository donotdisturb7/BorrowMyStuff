<?php

namespace App\Controller;

use App\Model\LoanModel;

/**
 * Contrôleur pour gérer les demandes de prêt
 */
class LoanRequestController {
    /**
     * Créer une nouvelle demande de prêt
     * 
     * @return string Redirection vers la page précédente avec un message
     */
    public function create() {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['authenticated']) || $_SESSION['role'] === 'admin') {
            header('Location: /login');
            exit;
        }
        
        // Vérifier si les données du formulaire sont présentes
        if (!isset($_POST['item_id']) || !isset($_POST['start_date']) || !isset($_POST['end_date'])) {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Tous les champs sont requis pour faire une demande de prêt.'
            ];
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '/items');
            exit;
        }
        
        $itemId = (int) $_POST['item_id'];
        $requesterId = (int) $_SESSION['user_id'];
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        
        // Valider les dates
        $today = date('Y-m-d');
        if ($startDate < $today) {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'La date de début doit être aujourd\'hui ou une date future.'
            ];
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '/items');
            exit;
        }
        
        if ($endDate < $startDate) {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'La date de fin doit être après la date de début.'
            ];
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '/items');
            exit;
        }
        
        // Vérifier si l'item existe et est disponible
        $loanModel = new LoanModel();
        $item = $loanModel->getItemById($itemId);
        
        if (!$item || !$item['available']) {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Cet item n\'est pas disponible pour un prêt.'
            ];
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? '/items');
            exit;
        }
        
        // Créer la demande de prêt
        $result = $loanModel->createLoan($itemId, $requesterId, $today, $startDate, $endDate);
        
        if ($result) {
            // Message de succès dans la session
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Votre demande de prêt a été envoyée avec succès.'
            ];
            
            // Rediriger vers le tableau de bord utilisateur au lieu de la liste des items
            header('Location: /dashboard');
            exit;
        } else {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de l\'envoi de votre demande de prêt.'
            ];
            
            // En cas d'erreur, rediriger vers la liste des items
            header('Location: /items');
            exit;
        }
    }
    
    /**
     * Accepter une demande de prêt (pour les administrateurs)
     * 
     * @param int $id ID de la demande de prêt
     * @return string Redirection vers la page des demandes de prêt
     */
    public function accept($id) {
        // Vérifier si l'utilisateur est un administrateur
        if (!isset($_SESSION['authenticated']) || $_SESSION['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
        
        $loanModel = new LoanModel();
        $request = $loanModel->getLoanById($id);
        
        if (!$request) {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Demande de prêt introuvable.'
            ];
            header('Location: /dashboard');
            exit;
        }
        
        // Vérifier si l'item est toujours disponible
        $item = $loanModel->getItemById($request['item_id']);
        if (!$item || !$item['available']) {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Cet item n\'est plus disponible pour un prêt.'
            ];
            header('Location: /dashboard');
            exit;
        }
        
        // Vérifier que l'objet appartient à l'administrateur connecté
        if ($item['owner_id'] != $_SESSION['user_id']) {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Vous ne pouvez accepter que les demandes de prêt pour vos propres objets.'
            ];
            header('Location: /dashboard');
            exit;
        }
        
        // Accepter la demande
        $result = $loanModel->updateLoanStatus($id, 'accepted');
        
        if ($result) {
            // Créer un prêt est géré automatiquement dans updateLoanStatus
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'La demande de prêt a été acceptée avec succès.'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de l\'acceptation de la demande de prêt.'
            ];
        }
        
        header('Location: /dashboard');
        exit;
    }
    
    /**
     * Rejeter une demande de prêt (pour les administrateurs)
     * 
     * @param int $id ID de la demande de prêt
     * @return string Redirection vers la page des demandes de prêt
     */
    public function reject($id) {
        // Vérifier si l'utilisateur est un administrateur
        if (!isset($_SESSION['authenticated']) || $_SESSION['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
        
        $loanModel = new LoanModel();
        $request = $loanModel->getLoanById($id);
        
        if (!$request) {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Demande de prêt introuvable.'
            ];
            header('Location: /dashboard');
            exit;
        }
        
        // Vérifier que l'objet appartient à l'administrateur connecté
        $item = $loanModel->getItemById($request['item_id']);
        if (!$item || $item['owner_id'] != $_SESSION['user_id']) {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Vous ne pouvez rejeter que les demandes de prêt pour vos propres objets.'
            ];
            header('Location: /dashboard');
            exit;
        }
        
        $result = $loanModel->updateLoanStatus($id, 'rejected');
        
        if ($result) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'La demande de prêt a été rejetée.'
            ];
        } else {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors du rejet de la demande de prêt.'
            ];
        }
        
        header('Location: /dashboard');
        exit;
    }
}
