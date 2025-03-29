<?php
namespace App\Model;

use App\Config\Database;
use App\Model\Traits\LoanQueryTrait;
use App\Model\Traits\LoanManagementTrait;
use App\Model\Traits\ItemManagementTrait;
use PDO;

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
} 