<?php
namespace App\Model\Traits;

trait LoanManagementTrait {
    public function getPendingLoans() {
        $sql = "
            SELECT dp.*, 
                   i.name as item_name,
                   i.image_url,
                   u.username as requester_name, u.email as requester_email
            FROM demande_pret dp
            JOIN items i ON dp.item_id = i.id
            JOIN users u ON dp.requester_id = u.id
            WHERE dp.status = 'pending'
            ORDER BY dp.created_at DESC
        ";
        return $this->executeQuery($sql);
    }

    public function getLoansByBorrower($borrowerId) {
        $sql = "
            SELECT dp.*, 
                   i.name as item_name,
                   i.image_url,
                   u.username as owner_name
            FROM demande_pret dp
            JOIN items i ON dp.item_id = i.id
            JOIN users u ON i.owner_id = u.id
            WHERE dp.requester_id = :requester_id
            ORDER BY dp.created_at DESC
        ";
        return $this->executeQuery($sql, [':requester_id' => $borrowerId]);
    }

    public function getLoansByOwner($ownerId) {
        $sql = "
            SELECT dp.*, 
                   i.name as item_name,
                   i.image_url,
                   u.username as requester_name, u.email as requester_email
            FROM demande_pret dp
            JOIN items i ON dp.item_id = i.id
            JOIN users u ON dp.requester_id = u.id
            WHERE i.owner_id = :owner_id
            ORDER BY dp.created_at DESC
        ";
        return $this->executeQuery($sql, [':owner_id' => $ownerId]);
    }

    public function createLoan($itemId, $requesterId, $requestDate, $startDate, $endDate, $message = '') {
        $sql = "
            INSERT INTO demande_pret (item_id, requester_id, request_date, start_date, end_date, status, created_at)
            VALUES (:item_id, :requester_id, :request_date, :start_date, :end_date, 'pending', NOW())
        ";
        $params = [
            ':item_id' => $itemId,
            ':requester_id' => $requesterId,
            ':request_date' => $requestDate,
            ':start_date' => $startDate,
            ':end_date' => $endDate
        ];
        
        $id = $this->executeInsert($sql, $params);
        return $id ? ['success' => true, 'loan_id' => $id] : ['success' => false];
    }

    public function updateLoanStatus($loanId, $status, $notes = null) {
        // Vérifier si la table a une colonne admin_notes
        try {
            $checkColumn = $this->db->query("SHOW COLUMNS FROM demande_pret LIKE 'admin_notes'");
            $hasNotesColumn = $checkColumn && $checkColumn->rowCount() > 0;
            
            if ($hasNotesColumn && $notes !== null) {
                $sql = "UPDATE demande_pret SET status = :status, admin_notes = :notes WHERE id = :id";
                $params = [':status' => $status, ':id' => $loanId, ':notes' => $notes];
            } else {
                $sql = "UPDATE demande_pret SET status = :status WHERE id = :id";
                $params = [':status' => $status, ':id' => $loanId];
            }
            
            $success = $this->executeInsert($sql, $params);
            
            // Si l'emprunt est accepté, créer un enregistrement dans la table prets
            if ($success && $status === 'accepted') {
                $this->createActiveLoan($loanId);
                $this->updateItemAvailability($loanId, false);
            } else if ($success && $status === 'returned') {
                $this->updateItemAvailability($loanId, true);
            }
            
            return ['success' => (bool)$success];
        } catch (\Exception $e) {
            error_log("Error checking admin_notes column: " . $e->getMessage());
            // Procéder sans la colonne admin_notes
            $sql = "UPDATE demande_pret SET status = :status WHERE id = :id";
            $params = [':status' => $status, ':id' => $loanId];
            $success = $this->executeInsert($sql, $params);
            
            if ($success && $status === 'accepted') {
                $this->createActiveLoan($loanId);
                $this->updateItemAvailability($loanId, false);
            } else if ($success && $status === 'returned') {
                $this->updateItemAvailability($loanId, true);
            }
            
            return ['success' => (bool)$success];
        }
    }

    /**
     * Crée un enregistrement dans la table prets lorsqu'un prêt est accepté
     */
    private function createActiveLoan($demandId) {
        try {
            // Récupérer les infos de la demande
            $loan = $this->getLoanById($demandId);
            if (!$loan) return false;
            
            // Créer l'enregistrement dans la table prets
            $sql = "
                INSERT INTO prets (item_id, borrower_id, start_date, end_date, status, created_at)
                VALUES (:item_id, :borrower_id, :start_date, :end_date, 'ongoing', NOW())
            ";
            $params = [
                ':item_id' => $loan['item_id'],
                ':borrower_id' => $loan['requester_id'],
                ':start_date' => $loan['start_date'],
                ':end_date' => $loan['end_date']
            ];
            
            return $this->executeInsert($sql, $params);
        } catch (\Exception $e) {
            error_log("Error creating active loan: " . $e->getMessage());
            return false;
        }
    }

    public function getLoanById($loanId) {
        $sql = "
            SELECT dp.*, 
                   i.name as item_name,
                   i.image_url,
                   u.username as requester_name, u.email as requester_email
            FROM demande_pret dp
            JOIN items i ON dp.item_id = i.id
            JOIN users u ON dp.requester_id = u.id
            WHERE dp.id = :id
        ";
        return $this->executeSingleFetch($sql, [':id' => $loanId]);
    }

    public function getAllLoans() {
        $sql = "
            SELECT dp.*, 
                   i.name as item_name,
                   i.image_url,
                   u.username as requester_name, u.email as requester_email
            FROM demande_pret dp
            JOIN items i ON dp.item_id = i.id
            JOIN users u ON dp.requester_id = u.id
            ORDER BY dp.created_at DESC
        ";
        return $this->executeQuery($sql);
    }

    public function getPendingLoansByOwner($ownerId) {
        $sql = "
            SELECT dp.*, 
                   i.name as item_name,
                   i.image_url,
                   u.username as requester_name, u.email as requester_email
            FROM demande_pret dp
            JOIN items i ON dp.item_id = i.id
            JOIN users u ON dp.requester_id = u.id
            WHERE dp.status = 'pending' AND i.owner_id = :owner_id
            ORDER BY dp.created_at DESC
        ";
        return $this->executeQuery($sql, [':owner_id' => $ownerId]);
    }

    public function getActiveItemLoansByOwner($ownerId) {
        $sql = "
            SELECT dp.*, 
                   i.name as item_name,
                   i.image_url,
                   u.username as requester_name, u.email as requester_email
            FROM demande_pret dp
            JOIN items i ON dp.item_id = i.id
            JOIN users u ON dp.requester_id = u.id
            WHERE i.owner_id = :owner_id AND dp.status = 'accepted'
            ORDER BY dp.end_date ASC
        ";
        return $this->executeQuery($sql, [':owner_id' => $ownerId]);
    }
} 