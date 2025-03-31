<?php
namespace App\Model;

use App\Config\Database;
use App\Model\Traits\LoanQueryTrait;
use App\Model\Traits\LoanManagementTrait;
use App\Model\Traits\ItemManagementTrait;
use PDO;
use PDOException;

/**
 * Unified Loan Model that handles all loan/borrowing related operations
 */
class LoanModel {
    use LoanQueryTrait;
    use LoanManagementTrait;
    use ItemManagementTrait;

    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Supprimer un prêt par ID
     * 
     * @param int $id ID du prêt
     * @return array Résultat de l'opération
     */
    public function deleteLoan($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM demande_pret WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $success = $stmt->execute();
            
            return ['success' => $success];
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Erreur de base de données'];
        }
    }

    /**
     * Supprimer tous les prêts associés à un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @return array Résultat de l'opération
     */
    public function deleteLoansByUserId($userId) {
        try {
            // Supprimer les demandes où l'utilisateur est emprunteur
            $stmt = $this->db->prepare("DELETE FROM demande_pret WHERE requester_id = :user_id");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            // Supprimer les demandes où l'utilisateur est propriétaire d'un objet
            $stmt = $this->db->prepare("
                DELETE dp FROM demande_pret dp
                INNER JOIN items i ON dp.item_id = i.id
                WHERE i.owner_id = :user_id
            ");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return ['success' => true];
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Erreur de base de données'];
        }
    }

    /**
     * Get loans associated with items owned by a specific user
     * 
     * @param int $ownerId ID of the owner
     * @return array Array of loans
     */
    public function getLoansByOwnerId($ownerId) {
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
} 