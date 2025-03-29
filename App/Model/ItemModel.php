<?php 
namespace App\Model;
use App\Config\Database;
use PDO;
use PDOException;

class ItemModel {  // Fixed capitalization
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAllItems() {
        try {
            $stmt = $this->db->query("SELECT i.*, u.username as owner_name FROM items i
                                     LEFT JOIN users u ON i.owner_id = u.id
                                     ORDER BY i.created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get paginated items
     * 
     * @param int $page Current page number
     * @param int $itemsPerPage Number of items per page
     * @return array Array containing items and pagination data
     */
    /**
     * Get random items for carousel
     * 
     * @param int $limit Number of random items to retrieve
     * @return array Array of random items
     */
    public function getRandomItems($limit = 5) {
        try {
            $stmt = $this->db->prepare("SELECT i.*, u.username as owner_name FROM items i
                                       LEFT JOIN users u ON i.owner_id = u.id
                                       WHERE i.image_url IS NOT NULL AND i.image_url != ''
                                       ORDER BY RAND()
                                       LIMIT :limit");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return [];
        }
    }
    
    public function getPaginatedItems($page = 1, $itemsPerPage = 6) {
        try {
            // Calculate offset
            $offset = ($page - 1) * $itemsPerPage;
            
            // Get total count for pagination
            $countStmt = $this->db->query("SELECT COUNT(*) FROM items");
            $totalItems = $countStmt->fetchColumn();
            
            // Get items for current page
            $stmt = $this->db->prepare("SELECT i.*, u.username as owner_name FROM items i
                                       LEFT JOIN users u ON i.owner_id = u.id
                                       ORDER BY i.created_at DESC
                                       LIMIT :limit OFFSET :offset");
            $stmt->bindParam(':limit', $itemsPerPage, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate pagination data
            $totalPages = ceil($totalItems / $itemsPerPage);
            
            return [
                'items' => $items,
                'pagination' => [
                    'currentPage' => $page,
                    'totalPages' => $totalPages,
                    'itemsPerPage' => $itemsPerPage,
                    'totalItems' => $totalItems
                ]
            ];
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    public function getItemsByOwnerId($ownerId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM items WHERE owner_id = :owner_id ORDER BY created_at DESC");
            $stmt->bindParam(':owner_id', $ownerId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    public function getItemById($id) {
        try {
            $stmt = $this->db->prepare("SELECT i.*, u.username as owner_name FROM items i
                                       LEFT JOIN users u ON i.owner_id = u.id
                                       WHERE i.id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return false;
        }
    }

    public function createItem($name, $description, $owner_id, $category = null, $image = null, $available = true) {
        try {
            $stmt = $this->db->prepare("INSERT INTO items (name, description, owner_id, category, image_url, available, created_at) 
                                       VALUES (:name, :description, :owner_id, :category, :image, :available, NOW())");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':owner_id', $owner_id, PDO::PARAM_INT);
            $stmt->bindParam(':category', $category);
            $stmt->bindParam(':image', $image);
            $stmt->bindParam(':available', $available, PDO::PARAM_BOOL);
            
            $success = $stmt->execute();
            
            if ($success) {
                return [
                    'success' => true,
                    'item_id' => $this->db->lastInsertId()
                ];
            }
            
            return ['success' => false];
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function updateItem($id, $data) {
        try {
            $allowedFields = ['name', 'description', 'category', 'image_url', 'available'];
            $updates = [];
            $params = [':id' => $id];
            
            foreach ($data as $key => $value) {
                if (in_array($key, $allowedFields)) {
                    $updates[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
            
            if (empty($updates)) {
                return ['success' => false, 'error' => 'No valid fields to update'];
            }
            
            $sql = "UPDATE items SET " . implode(', ', $updates) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return ['success' => $stmt->execute($params)];
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function deleteItem($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM items WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return ['success' => $stmt->execute()];
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }

    public function searchItems($query) {
        try {
            $searchTerm = "%$query%";
            $stmt = $this->db->prepare("SELECT i.*, u.username as owner_name FROM items i
                                       LEFT JOIN users u ON i.owner_id = u.id
                                       WHERE i.name LIKE :search 
                                       OR i.description LIKE :search
                                       OR i.category LIKE :search
                                       ORDER BY i.created_at DESC");
            $stmt->bindParam(':search', $searchTerm);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Récupère toutes les catégories distinctes des objets
     * @return array Liste des catégories
     */
    public function getCategories() {
        try {
            $stmt = $this->db->query("SELECT DISTINCT category FROM items WHERE category IS NOT NULL AND category != '' ORDER BY category");
            $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            // Si aucune catégorie n'est trouvée, retourner un tableau par défaut
            if (empty($categories)) {
                return ['Livres', 'Électronique', 'Outils', 'Sports', 'Jeux', 'Autres'];
            }
            
            return $categories;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            return ['Livres', 'Électronique', 'Outils', 'Sports', 'Jeux', 'Autres'];
        }
    }
}
?>