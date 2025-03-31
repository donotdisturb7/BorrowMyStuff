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
                   u.username as owner_name,
                   u2.username as requester_name, u2.email as requester_email
            FROM demande_pret dp
            JOIN items i ON dp.item_id = i.id
            JOIN users u ON i.owner_id = u.id
            JOIN users u2 ON dp.requester_id = u2.id
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
        try {
            $this->db->beginTransaction();

            // Vérifier si la table a une colonne admin_notes
            $checkColumn = $this->db->query("SHOW COLUMNS FROM demande_pret LIKE 'admin_notes'");
            $hasNotesColumn = $checkColumn && $checkColumn->rowCount() > 0;
            
            // Mettre à jour la demande de prêt
            if ($hasNotesColumn && $notes !== null) {
                $sql = "UPDATE demande_pret SET status = :status, admin_notes = :notes WHERE id = :id";
                $params = [':status' => $status, ':id' => $loanId, ':notes' => $notes];
            } else {
                $sql = "UPDATE demande_pret SET status = :status WHERE id = :id";
                $params = [':status' => $status, ':id' => $loanId];
            }
            
            $stmt = $this->db->prepare($sql);
            $success = $stmt->execute($params);
            
            if (!$success) {
                throw new \Exception("Échec de la mise à jour du statut de la demande de prêt");
            }
            
            // Si l'emprunt est accepté, créer un enregistrement dans la table prets
            if ($status === 'accepted') {
                $this->createActiveLoan($loanId);
                $this->updateItemAvailability($loanId, false);
            } 
            // Si l'objet est retourné
            else if ($status === 'returned') {
                // Récupérer l'ID de l'item
                $loan = $this->getLoanById($loanId);
                if (!$loan) {
                    throw new \Exception("Prêt introuvable");
                }
                
                // Mettre à jour le statut dans la table prets
                $sql = "UPDATE prets SET status = 'returned' 
                       WHERE item_id = :item_id 
                       AND borrower_id = :borrower_id 
                       AND status = 'ongoing'";
                $stmt = $this->db->prepare($sql);
                $success = $stmt->execute([
                    ':item_id' => $loan['item_id'],
                    ':borrower_id' => $loan['requester_id']
                ]);
                
                if (!$success) {
                    throw new \Exception("Échec de la mise à jour du statut dans la table prets");
                }
                
                // Rendre l'objet à nouveau disponible
                if (!$this->updateItemAvailability($loan['item_id'], true)) {
                    throw new \Exception("Échec de la mise à jour de la disponibilité de l'objet");
                }
            }
            
            $this->db->commit();
            return ['success' => true];
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error updating loan status: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
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