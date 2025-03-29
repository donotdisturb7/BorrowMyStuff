<?php
namespace App\Model;
use PDO;

/**
 * Legacy Loan Model for handling old loan requests
 * @deprecated This entire class is deprecated. Use LoanModel instead.
 */
class LegacyLoanModel extends AbstractLoanModel {
    /**
     * Get all loan requests from the legacy system
     * @deprecated Use LoanModel::getPendingLoans() instead
     */
    public function getAllDemands() {
        $stmt = $this->db->prepare("SELECT * FROM demande_pret");
        return $this->executeQuery($stmt);
    }
    
    /**
     * Get all loan requests with details
     * @deprecated Use LoanModel::getAllLoans() instead
     */
    public function getAllLoanRequests() {
        $sql = "
            SELECT dp.*, 
                   i.name as item_name, i.image as item_image,
                   u.username as requester_name, u.email as requester_email
            FROM demande_pret dp
            JOIN items i ON dp.item_id = i.id
            JOIN users u ON dp.requester_id = u.id
            ORDER BY dp.created_at DESC
        ";
        $stmt = $this->db->prepare($sql);
        return $this->executeQuery($stmt);
    }
    
    /**
     * Get pending loan requests
     * @deprecated Use LoanModel::getPendingLoans() instead
     */
    public function getPendingLoanRequests() {
        $sql = "
            SELECT dp.*, 
                   i.name as item_name, i.image as item_image,
                   u.username as requester_name, u.email as requester_email
            FROM demande_pret dp
            JOIN items i ON dp.item_id = i.id
            JOIN users u ON dp.requester_id = u.id
            WHERE dp.status = 'pending'
            ORDER BY dp.created_at DESC
        ";
        $stmt = $this->db->prepare($sql);
        return $this->executeQuery($stmt);
    }
    
    /**
     * Get a specific loan request by ID
     * @deprecated Use LoanModel::getLoanById() instead
     */
    public function getDemandById($id) {
        $stmt = $this->db->prepare("SELECT * FROM demande_pret WHERE id = :id");
        return $this->executeSingleFetch($stmt, [':id' => $id]);
    }
    
    /**
     * Create a loan request in the legacy system
     * @deprecated Use LoanModel::createLoan() instead
     */
    public function createDemand($item_id, $requester_id, $request_date, $status = 'pending') {
        $sql = "INSERT INTO demande_pret (item_id, requester_id, request_date, status) VALUES (:item_id, :requester_id, :request_date, :status)";
        $stmt = $this->db->prepare($sql);
        return $this->executeInsert($stmt, [
            ':item_id' => $item_id,
            ':requester_id' => $requester_id,
            ':request_date' => $request_date,
            ':status' => $status
        ]);
    }
    
    /**
     * Delete a loan request
     * @deprecated Use LoanModel::deleteLoan() instead
     */
    public function deleteDemand($id) {
        $stmt = $this->db->prepare("DELETE FROM demande_pret WHERE id = :id");
        return $this->executeInsert($stmt, [':id' => $id]);
    }
} 