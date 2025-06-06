<?php
namespace App\View\Item;

class ItemFormView {
    /**
     * Helper function pour simplifier htmlspecialchars
     */
    private static function h($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Affiche le formulaire de création ou de modification d'un objet
     *
     * @param array|null $item Données de l'objet pour la modification, null pour la création
     * @param array $errors Erreurs de validation
     * @param array $categories Liste des catégories disponibles
     * @return string Contenu HTML
     */
    public static function render($item = null, $errors = [], $categories = []) {
        $isEdit = !empty($item) && isset($item['id']);
        $title = $isEdit ? 'Modifier l\'objet : ' . self::h($item['name']) : 'Ajouter un nouvel objet';
        $action = $isEdit ? '/items/' . $item['id'] . '/update' : '/items/store';
        
        ob_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - Catalogue d'objets</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#4F46E5",
                        secondary: "#1F2937"
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-semibold text-gray-900">Gestion des Objets</h1>
                <nav>
                    <ul class="flex space-x-6">
                        <li><a href="/" class="text-gray-600 hover:text-primary transition-colors"><i class="fas fa-home mr-1"></i> Accueil</a></li>
                        <li><a href="/items" class="text-gray-600 hover:text-primary transition-colors"><i class="fas fa-box mr-1"></i> Objets</a></li>
                        <li><a href="/dashboard" class="text-gray-600 hover:text-primary transition-colors"><i class="fas fa-chart-line mr-1"></i> Tableau de bord</a></li>
                        <li><a href="/logout" class="text-gray-600 hover:text-primary transition-colors"><i class="fas fa-sign-out-alt mr-1"></i> Déconnexion</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    
    <main class="container mx-auto px-4 py-8">
        <div class="text-sm text-gray-600 mb-6">
            <a href="/items" class="hover:text-primary"><i class="fas fa-box mr-1"></i>Objets</a> &gt; 
            <?php if ($isEdit): ?>
                <a href="/items/<?= $item['id'] ?>" class="hover:text-primary"><?= self::h($item['name']) ?></a> &gt; Modifier
            <?php else: ?>
                Ajouter
            <?php endif; ?>
        </div>
        
        <div class="max-w-2xl mx-auto bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-900 mb-6"><?= $title ?></h2>
            
            <?php if (!empty($errors)): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 mb-6 rounded-lg">
                    <?php foreach ($errors as $error): ?>
                        <p class="flex items-center"><i class="fas fa-exclamation-circle mr-2"></i><?= self::h($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <form action="<?= $action ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= self::h($_SESSION['csrf_token']) ?>">
                
                <?php if (isset($_GET["page"])): ?>
                    <input type="hidden" name="current_page" value="<?= intval($_GET["page"]) ?>">
                <?php endif; ?>
                
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom de l'objet *</label>
                    <input type="text" id="name" name="name" value="<?= self::h($item['name'] ?? '') ?>" required 
                           class="w-full border border-gray-300 px-3 py-2 rounded-md focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <div class="mb-4">
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Catégorie *</label>
                    <select id="category" name="category" required 
                            class="w-full border border-gray-300 px-3 py-2 rounded-md focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="">Sélectionnez une catégorie</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= self::h($cat) ?>" <?= (isset($item['category']) && $item['category'] === $cat) ? 'selected' : '' ?>><?= self::h($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <textarea id="description" name="description" rows="5" required 
                              class="w-full border border-gray-300 px-3 py-2 rounded-md focus:ring-2 focus:ring-primary focus:border-primary"><?= self::h($item['description'] ?? '') ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    
                    <?php if ($isEdit && !empty($item['image'])): ?>
                        <div class="mb-3 p-3 bg-gray-50 inline-block rounded-lg">
                            <img src="/<?= self::h($item['image']) ?>" alt="Image actuelle" class="max-w-[200px] rounded">
                            <p class="text-sm text-gray-600 mt-2"><i class="fas fa-info-circle mr-1"></i>Image actuelle. Téléchargez une nouvelle image pour la remplacer.</p>
                        </div>
                    <?php endif; ?>
                    
                    <input type="file" id="image" name="image" accept="image/*" 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 
                                  file:border-0 file:text-sm file:bg-primary file:text-white hover:file:bg-primary/90 
                                  file:rounded-md cursor-pointer">
                </div>
                
                <div class="flex items-center mb-4">
                    <input type="checkbox" id="available" name="available" value="1" 
                           <?= (($isEdit && !$item['1']) ? '' : 'checked') ?> 
                           class="h-4 w-4 border-gray-300 text-primary focus:ring-primary rounded">
                    <label for="available" class="ml-2 block text-sm text-gray-700">Disponible pour le prêt</label>
                </div>
                
                <div class="flex space-x-4 pt-4">
                    <button type="submit" class="px-6 py-2 bg-primary text-white hover:bg-primary/90 
                                              transition-colors rounded-md flex items-center">
                        <i class="fas fa-<?= $isEdit ? 'save' : 'plus' ?> mr-2"></i>
                        <?= $isEdit ? 'Mettre à jour' : 'Ajouter l\'objet' ?>
                    </button>
                    <a href="<?= $isEdit ? '/items/' . $item['id'] : '/items' ?>" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 
                              transition-colors rounded-md flex items-center">
                        <i class="fas fa-times mr-2"></i>Annuler
                    </a>
                </div>
            </form>
        </div>
    </main>
    

</body>
</html>
<?php
        return ob_get_clean();
    }
}