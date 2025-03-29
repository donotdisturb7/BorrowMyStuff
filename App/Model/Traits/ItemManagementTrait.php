<?php
namespace App\Model\Traits;

trait ItemManagementTrait {
    private function updateItemAvailability($loanId, $available) {
        // Get the item ID from the loan
        $sql = "SELECT item_id FROM demande_pret WHERE id = :loan_id";
        $result = $this->executeSingleFetch($sql, [':loan_id' => $loanId]);
        
        if (!$result) return;
        
        // Update the item availability
        $sql = "UPDATE items SET available = :available WHERE id = :item_id";
        $this->executeInsert($sql, [
            ':available' => $available,
            ':item_id' => $result['item_id']
        ]);
    }
    
    public function updateItemAvailabilityById($itemId, $available) {
        $sql = "UPDATE items SET available = :available WHERE id = :item_id";
        return $this->executeInsert($sql, [
            ':available' => $available,
            ':item_id' => $itemId
        ]);
    }
    
    public function getItemById($id) {
        $sql = "SELECT i.*, u.username as owner_name FROM items i
                LEFT JOIN users u ON i.owner_id = u.id
                WHERE i.id = :id";
        return $this->executeSingleFetch($sql, [':id' => $id]);
    }
} 