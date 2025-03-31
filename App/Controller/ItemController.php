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
        // Récupérer la page actuelle depuis la requête
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;
        
        // Récupérer les éléments paginés
        $result = $this->itemModel->getPaginatedItems($page, 12); // 12 éléments par page
        
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            // Admin view with management options
            echo ItemView::renderAdmin($result['items'], null, $result['pagination']);
        } else {
            // Regular user view (just displaying items)
            echo ItemView::render($result['items'], null, $result['pagination']);
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
        
        $categories = $this->itemModel->getCategories();
        echo ItemFormView::render(null, [], $categories);
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
            $maxSize = 2 * 1024 * 1024; // 2MB
            
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $detectedType = finfo_file($fileInfo, $_FILES['image']['tmp_name']);
            finfo_close($fileInfo);
            
            if (!in_array($detectedType, $allowedTypes)) {
                $_SESSION['notification'] = [
                    'message' => 'Le fichier doit être une image (JPEG, PNG, GIF ou WEBP).',
                    'type' => 'error'
                ];
                $_SESSION['form_errors'] = ['Le fichier doit être une image (JPEG, PNG, GIF ou WEBP).'];
                header('Location: /dashboard?tab=add-item');
                exit;
            }
            
            if ($_FILES['image']['size'] > $maxSize) {
                $_SESSION['notification'] = [
                    'message' => 'L\'image ne doit pas dépasser 2 Mo.',
                    'type' => 'error'
                ];
                $_SESSION['form_errors'] = ['L\'image ne doit pas dépasser 2 Mo.'];
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
                
                // Stocke uniquement le chemin relatif dans la base de données
                $image = $fileName;
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
            isset($_POST['available']) ? 1 : 0  // Conversion explicite en entier
        );
        
        if ($result['success']) {
            // Ajouter un message de succès dans la session avec un nom spécifique pour le dashboard
            $_SESSION['notification'] = [
                'message' => 'L\'objet a été ajouté avec succès.',
                'type' => 'success'
            ];
            
            // Ajouter également dans loan_success pour être sûr que le message s'affiche
            $_SESSION['loan_success'] = 'L\'objet a été ajouté avec succès.';
            
            // Rediriger avec un paramètre pour forcer l'affichage
            header('Location: /dashboard?success=true');
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
        
        $categories = $this->itemModel->getCategories();
        echo ItemFormView::render($item, [], $categories);
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
            'available' => isset($_POST['available']) ? 1 : 0  
        ];
        
        // Process image upload if available
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Vérification stricte du type de fichier
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $detectedType = finfo_file($fileInfo, $_FILES['image']['tmp_name']);
            finfo_close($fileInfo);
            
            if (!in_array($detectedType, $allowedTypes)) {
                $_SESSION['notification'] = [
                    'message' => 'Le fichier doit être une image (JPEG, PNG, GIF ou WEBP).',
                    'type' => 'error'
                ];
                $_SESSION['form_errors'] = ['Le fichier doit être une image (JPEG, PNG, GIF ou WEBP).'];
                header('Location: /items/' . $id . '/edit');
                exit;
            }
            
            if ($_FILES['image']['size'] > $maxSize) {
                $_SESSION['notification'] = [
                    'message' => 'L\'image ne doit pas dépasser 2 Mo.',
                    'type' => 'error'
                ];
                $_SESSION['form_errors'] = ['L\'image ne doit pas dépasser 2 Mo.'];
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
                if (!empty($item['image_url'])) {
                    $imagePath = 'public/img/items/' . $item['image_url'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                
                // Stocke uniquement le nom du fichier dans la base de données
                $data['image_url'] = $fileName;
            } else {
                $_SESSION['form_errors'] = ['Échec du téléchargement de l\'image.'];
                header('Location: /items/' . $id . '/edit');
                exit;
            }
        }
        
        $result = $this->itemModel->updateItem($id, $data);
        
        if ($result['success']) {
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'L\'objet a été mis à jour avec succès.'
            ];
            
            // Rediriger vers la page d'origine si spécifiée
            $currentPage = isset($_POST['current_page']) ? intval($_POST['current_page']) : null;
            
            if ($currentPage) {
                header('Location: /items?page=' . $currentPage);
            } else {
                header('Location: /items');
            }
        } else {
            $_SESSION['form_errors'] = ['Échec de la mise à jour de l\'objet.'];
            header('Location: /items/' . $id . '/edit');
            exit;
        }
    }
    
    public function delete($id) {
        // Ensure method is POST or DELETE
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }
        
        // Check for CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }
        
        // Get the item details
        $item = $this->itemModel->getItemById($id);
        
        if (!$item) {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'Item not found']);
            exit;
        }
        
        // Check if user has permission to delete this item
        // Only admins or item owners can delete items
        if ($_SESSION['role'] !== 'admin' && $item['owner_id'] != $_SESSION['user_id']) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'You do not have permission to delete this item']);
            exit;
        }
        
        // Delete the item
        $result = $this->itemModel->deleteItem($id);
        
        if ($result['success']) {
            // Delete image file if it exists
            if (!empty($item['image_url']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $item['image_url'])) {
                unlink($_SERVER['DOCUMENT_ROOT'] . '/' . $item['image_url']);
            }
            
            // Set success message and redirect
            $_SESSION['notification'] = [
                'message' => 'L\'objet a été supprimé avec succès.',
                'type' => 'success'
            ];
        } else {
            // Set error message
            $_SESSION['notification'] = [
                'message' => 'Erreur lors de la suppression de l\'objet: ' . ($result['error'] ?? 'Une erreur inconnue est survenue'),
                'type' => 'error'
            ];
        }
        
        // Redirect back to items list
        header('Location: /items');
        exit;
    }
}