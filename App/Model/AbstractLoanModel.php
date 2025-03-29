<?php
namespace App\Model;
use App\Config\Database;
use PDO;
use PDOException;

abstract class AbstractLoanModel {
    protected $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    protected function handleDatabaseError(PDOException $e, $defaultReturn = []) {
        error_log("Database error: " . $e->getMessage());
        return $defaultReturn;
    }

    protected function executeQuery($stmt, $params = [], $fetchMode = PDO::FETCH_ASSOC) {
        try {
            foreach ($params as $key => $value) {
                $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($key, $value, $type);
            }
            $stmt->execute();
            return $stmt->fetchAll($fetchMode);
        } catch (PDOException $e) {
            return $this->handleDatabaseError($e);
        }
    }

    protected function executeSingleFetch($stmt, $params = []) {
        try {
            foreach ($params as $key => $value) {
                $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($key, $value, $type);
            }
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return $this->handleDatabaseError($e, null);
        }
    }

    protected function executeInsert($stmt, $params = []) {
        try {
            foreach ($params as $key => $value) {
                $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($key, $value, $type);
            }
            $success = $stmt->execute();
            return $success ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            return $this->handleDatabaseError($e, false);
        }
    }
} 