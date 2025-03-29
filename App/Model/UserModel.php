<?php 
namespace App\Model;

use App\Config\Database;
use PDO;
use PDOException;

class UserModel {  
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAllUsers() {
        try {
            $stmt = $this->db->query("SELECT * FROM users");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    public function getUserById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    // ADDED: Get user by email method
    public function getUserByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    // ADDED: Check if email exists method
    public function emailExists($email) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    // ADDED: Check if username exists method
    public function usernameExists($username) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    public function createUser($username, $email, $password, $role = 'user') {
        try {
            // Hash the password for security
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, role, created_at) VALUES (:username, :email, :password_hash, :role, NOW())");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password_hash', $hashedPassword);  // Store hashed password
            $stmt->bindParam(':role', $role);
            
            $success = $stmt->execute();
            
            if ($success) {
                return [
                    'success' => true, 
                    'user_id' => $this->db->lastInsertId()
                ];
            }
            
            return ['success' => false];
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error'];
        }
    }

    public function updateUser($id, $data) {
        try {
            $allowedFields = ['username', 'email', 'role'];
            $updates = [];
            $params = [':id' => $id];
            
            foreach ($data as $key => $value) {
                if (in_array($key, $allowedFields)) {
                    // Utiliser des paramètres préparés pour toutes les valeurs
                    $updates[] = "`$key` = :$key";
                    $params[":$key"] = $value;
                }
            }
            
            if (empty($updates)) {
                return ['success' => false, 'error' => 'No valid fields to update'];
            }
            
            $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            
            $success = $stmt->execute($params);
            return ['success' => $success];
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error'];
        }
    }

    public function deleteUser($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return ['success' => $stmt->execute()];
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error'];
        }
    }

    public function validateUser($data) {
        try {
            $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
            $stmt->bindParam(':email', $data['email']);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($user)) {
                // Check if password matches
                if (password_verify($data['password'], $user['password_hash'])) {
                    // Remove password from the returned data for security
                    unset($user['password_hash']);
                    return ['success' => true, 'user' => $user];
                }
            }

            return ['success' => false, 'error' => 'Invalid credentials'];
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error'];
        }
    }
}
?>