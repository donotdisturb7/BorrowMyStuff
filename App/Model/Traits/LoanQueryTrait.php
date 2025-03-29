<?php
namespace App\Model\Traits;

use PDO;
use PDOException;

trait LoanQueryTrait {
    protected function handleDatabaseError(PDOException $e, $defaultReturn = []) {
        error_log("Database error: " . $e->getMessage());
        return $defaultReturn;
    }

    protected function executeQuery($sql, $params = [], $fetchMode = PDO::FETCH_ASSOC) {
        try {
            $stmt = $this->db->prepare($sql);
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

    protected function executeSingleFetch($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
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

    protected function executeInsert($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
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