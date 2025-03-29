<?php

namespace App\Controller;

use App\Model\ItemModel;
use App\View\Item\ItemView;
use App\View\Item\ItemFormView;

class ItemController {
    private $itemModel;
    
    public function __construct(ItemModel $itemModel) {
        $this->itemModel = $itemModel;
        
        // Admin authorization check for protected methods
        if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'DELETE']) && 
            (!isset($_SESSION['authenticated']) || 
             !isset($_SESSION['role']) || 
             $_SESSION['role'] !== 'admin')) {
                
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'Unauthorized. Admin privileges required.']);
            exit;
        }
    }
    
    public function index() {
        $items = $this->itemModel->getAllItems();
        
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            // Admin view with management options
            echo ItemView::renderAdmin($items);
        } else {
            // Regular user view (just displaying items)
            echo ItemView::render($items);
        }
    }
    
    public function show($id) {
        $item = $this->itemModel->getItemById($id);
        
        if (!$item) {
            header('Location: /items');
            exit;
        }
        
        echo ItemView::renderSingle($item);
    }
    
    public function create() {
        // Show item creation form (admin only)
        if (!isset($_SESSION['authenticated']) || $_SESSION['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
        
        echo ItemFormView::render();
    }
    
    public function store() {
        // Handle item creation form submission
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /items/create');
            exit;
        }
        
        // Validation de base des champs
        $errors = [];
        if (empty($_POST['name']) || strlen($_POST['name']) < 3) {
            $errors[] = 'Le nom de l\'objet doit contenir au moins 3 caractères.';
        }
        if (empty($_POST['description']) || strlen($_POST['description']) < 10) {
            $errors[] = 'La description doit contenir au moins 10 caractères.';
        }
        if (empty($_POST['category'])) {
            $errors[] = 'Veuillez sélectionner une catégorie.';
        }
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            header('Location: /dashboard?tab=add-item');
            exit;
        }
        
        // Process image upload if available
        $image = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Vérification stricte du type de fichier
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $detectedType = finfo_file($fileInfo, $_FILES['image']['tmp_name']);
            finfo_close($fileInfo);
            
            if (!in_array($detectedType, $allowedTypes)) {
                $_SESSION['form_errors'] = ['Le fichier doit être une image (JPEG, PNG, GIF ou WEBP).'];
                header('Location: /dashboard?tab=add-item');
                exit;
            }
            
            if ($_FILES['image']['size'] > $maxSize) {
                $_SESSION['form_errors'] = ['L\'image ne doit pas dépasser 5 Mo.'];
                header('Location: /dashboard?tab=add-item');
                exit;
            }
            
            // Création d'un nom de fichier sécurisé avec un hash unique
            $uploadDir = 'public/img/items/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Obtenir l'extension en se basant sur le type MIME détecté
            $extension = match($detectedType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                default => 'jpg'
            };
            
            $fileName = md5(uniqid() . rand(1000, 9999)) . '.' . $extension;
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                // Sécurité supplémentaire : vérifier encore le type après téléchargement
                $checkAgain = finfo_open(FILEINFO_MIME_TYPE);
                $checkType = finfo_file($checkAgain, $uploadPath);
                finfo_close($checkAgain);
                
                if (!in_array($checkType, $allowedTypes)) {
                    unlink($uploadPath); // Supprimer le fichier suspect
                    $_SESSION['form_errors'] = ['Le fichier téléchargé n\'est pas une image valide.'];
                    header('Location: /dashboard?tab=add-item');
                    exit;
                }
                
                $image = $uploadPath;
            } else {
                $_SESSION['form_errors'] = ['Échec du téléchargement de l\'image.'];
                header('Location: /dashboard?tab=add-item');
                exit;
            }
        }
        
        // Sanitize data before storing
        $name = htmlspecialchars(trim($_POST['name']));
        $description = htmlspecialchars(trim($_POST['description']));
        $category = htmlspecialchars(trim($_POST['category'] ?? ''));
        
        $result = $this->itemModel->createItem(
            $name,
            $description,
            $_SESSION['user_id'],
            $category,
            $image,
            isset($_POST['available']) && $_POST['available'] == '1'
        );
        
        if ($result['success']) {
            header('Location: /dashboard');
        } else {
            // Stocker les erreurs dans la session
            $_SESSION['form_errors'] = ['Échec de la création de l\'objet. Veuillez réessayer.'];
            header('Location: /dashboard?tab=add-item');
            exit;
        }
    }
    
    public function edit($id) {
        // Show item edit form (admin only)
        if (!isset($_SESSION['authenticated']) || $_SESSION['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
        
        $item = $this->itemModel->getItemById($id);
        
        if (!$item) {
            header('Location: /items');
            exit;
        }
        
        echo ItemFormView::render($item);
    }
    
    public function update($id) {
        // Handle item update form submission
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /items');
            exit;
        }
        
        // Vérifier que l'objet existe et appartient à l'utilisateur actuel
        $item = $this->itemModel->getItemById($id);
        if (!$item || $item['owner_id'] != $_SESSION['user_id']) {
            header('Location: /items');
            exit;
        }
        
        // Validation de base des champs
        $errors = [];
        if (empty($_POST['name']) || strlen($_POST['name']) < 3) {
            $errors[] = 'Le nom de l\'objet doit contenir au moins 3 caractères.';
        }
        if (empty($_POST['description']) || strlen($_POST['description']) < 10) {
            $errors[] = 'La description doit contenir au moins 10 caractères.';
        }
        if (empty($_POST['category'])) {
            $errors[] = 'Veuillez sélectionner une catégorie.';
        }
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            header('Location: /items/' . $id . '/edit');
            exit;
        }
        
        // Sanitize data before storing
        $name = htmlspecialchars(trim($_POST['name']));
        $description = htmlspecialchars(trim($_POST['description']));
        $category = htmlspecialchars(trim($_POST['category'] ?? ''));
        
        $data = [
            'name' => $name,
            'description' => $description,
            'category' => $category,
            'available' => isset($_POST['available']) && $_POST['available'] == '1'
        ];
        
        // Process image upload if available
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Vérification stricte du type de fichier
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $detectedType = finfo_file($fileInfo, $_FILES['image']['tmp_name']);
            finfo_close($fileInfo);
            
            if (!in_array($detectedType, $allowedTypes)) {
                $_SESSION['form_errors'] = ['Le fichier doit être une image (JPEG, PNG, GIF ou WEBP).'];
                header('Location: /items/' . $id . '/edit');
                exit;
            }
            
            if ($_FILES['image']['size'] > $maxSize) {
                $_SESSION['form_errors'] = ['L\'image ne doit pas dépasser 5 Mo.'];
                header('Location: /items/' . $id . '/edit');
                exit;
            }
            
            // Create upload directory if it doesn't exist
            $uploadDir = 'public/img/items/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Obtenir l'extension en se basant sur le type MIME détecté
            $extension = match($detectedType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                default => 'jpg'
            };
            
            $fileName = md5(uniqid() . rand(1000, 9999)) . '.' . $extension;
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                // Sécurité supplémentaire : vérifier encore le type après téléchargement
                $checkAgain = finfo_open(FILEINFO_MIME_TYPE);
                $checkType = finfo_file($checkAgain, $uploadPath);
                finfo_close($checkAgain);
                
                if (!in_array($checkType, $allowedTypes)) {
                    unlink($uploadPath); // Supprimer le fichier suspect
                    $_SESSION['form_errors'] = ['Le fichier téléchargé n\'est pas une image valide.'];
                    header('Location: /items/' . $id . '/edit');
                    exit;
                }
                
                // Supprimer l'ancienne image si elle existe
                if (!empty($item['image_url']) && file_exists($item['image_url'])) {
                    unlink($item['image_url']);
                }
                
                $data['image_url'] = $uploadPath;
            } else {
                $_SESSION['form_errors'] = ['Échec du téléchargement de l\'image.'];
                header('Location: /items/' . $id . '/edit');
                exit;
            }
        }
        
        $result = $this->itemModel->updateItem($id, $data);
        
        if ($result['success']) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'L\'objet a été mis à jour avec succès.'
            ];
            header('Location: /items');
        } else {
            $_SESSION['form_errors'] = ['Échec de la mise à jour de l\'objet.'];
            header('Location: /items/' . $id . '/edit');
            exit;
        }
    }
    
    public function delete($id) {
        // Handle item deletion
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /items');
            exit;
        }
        
        // Vérifier que l'objet existe et appartient à l'utilisateur actuel
        $item = $this->itemModel->getItemById($id);
        if (!$item || $item['owner_id'] != $_SESSION['user_id']) {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à supprimer cet objet.'
            ];
            header('Location: /items');
            exit;
        }
        
        // Vérifier si l'objet est actuellement prêté
        if (!$item['available']) {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Impossible de supprimer un objet actuellement prêté.'
            ];
            header('Location: /items');
            exit;
        }
        
        // Supprimer l'image associée si elle existe
        if (!empty($item['image_url']) && file_exists($item['image_url'])) {
            unlink($item['image_url']);
        }
        
        $result = $this->itemModel->deleteItem($id);
        
        if ($result['success']) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'L\'objet a été supprimé avec succès.'
            ];
        } else {
            $_SESSION['alert'] = [
                'type' => 'error',
                'message' => 'Une erreur est survenue lors de la suppression de l\'objet.'
            ];
        }
        
        header('Location: /items');
    }
    
    public function search() {
        $query = $_GET['q'] ?? '';
        if (empty($query)) {
            header('Location: /items');
            exit;
        }
        
        $items = $this->itemModel->searchItems($query);
        
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            echo ItemView::renderAdmin($items, $query);
        } else {
            echo ItemView::render($items, $query);
        }
    }
}