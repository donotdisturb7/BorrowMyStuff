<?php
namespace App\View\Item;

use App\View\Components\Layout\LayoutView;
use App\View\Components\NavbarView;
use App\View\Components\ItemGridView;
use App\View\Components\SectionTitleView;
use App\View\Components\LoanRequestModalView;

class ItemView {
    /**
     * Helper function pour simplifier htmlspecialchars
     */
    private static function h($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * Affiche la liste des objets
     * 
     * @param array $items Tableau des objets à afficher
     * @param array|null $pagination Données de pagination
     * @return string Contenu HTML
     */
    public static function render($items = [], $pagination = null) {
        // Prepare user data for navbar
        $user = [
            'isAuthenticated' => isset($_SESSION['authenticated']),
            'username' => $_SESSION['username'] ?? '',
            'role' => $_SESSION['role'] ?? ''
        ];
        
        // Build the main content
        ob_start();
        
        // Section title
        echo SectionTitleView::render('Articles disponibles');
        
        // Items grid
        echo ItemGridView::render($items, false, 'Aucun article trouvé.');
        
        // Pagination
        if ($pagination) {
            echo \App\View\Components\PaginationView::render(
                $pagination['currentPage'], 
                $pagination['totalPages'], 
                $_SERVER['REQUEST_URI'] ? strtok($_SERVER['REQUEST_URI'], '?') : '/items'
            );
        }
        
        $content = ob_get_clean();
        
        // Additional scripts
        $scripts = [];
        
        // Add loan request modal for authenticated non-admin users
        if (isset($_SESSION['authenticated']) && $_SESSION['role'] !== 'admin') {
            $scripts[] = '<script src="/js/loan-request.js"></script>';
            $content .= LoanRequestModalView::render();
        }
        
        // Render the complete layout
        return LayoutView::render('Catalogue d\'articles', $content, $user, $scripts);
    }
    
    /**
     * Render items list for admin users with management options
     *
     * @param array $items Array of items to display
     * @param array|null $pagination Données de pagination
     * @return string HTML content
     */
    public static function renderAdmin($items, $pagination = null) {
        // Prepare user data for navbar
        $user = [
            'isAuthenticated' => true,
            'username' => $_SESSION['username'] ?? 'Admin',
            'role' => 'admin'
        ];
        
        // Build the main content
        ob_start();
        
        // Admin header with add button
        echo '<div class="mb-8">';
        echo SectionTitleView::render('Gestion des articles');
        
        // Add new item button
        echo '<div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0 mb-6">';
        echo '<a href="/dashboard?tab=add-item" class="bg-black text-white px-4 py-2 inline-block rounded-md">Ajouter un nouvel article</a>';
        echo '</div>';
        
        // Items grid
        echo ItemGridView::render($items, true, 'Aucun article trouvé. Cliquez sur "Ajouter un nouvel article" pour commencer.');
        
        // Pagination
        if ($pagination) {
            echo \App\View\Components\PaginationView::render(
                $pagination['currentPage'], 
                $pagination['totalPages'], 
                $_SERVER['REQUEST_URI'] ? strtok($_SERVER['REQUEST_URI'], '?') : '/items'
            );
        }
        
        $content = ob_get_clean();
        
        // Additional scripts
        $scripts = ['<script src="/js/admin.js"></script>'];
        
        // Render the complete layout
        return LayoutView::render('Gestion des articles', $content, $user, $scripts);
    }
    
    /**
     * Render a single item card
     * 
     * @param array $item Item data
     * @param bool $isAdmin Whether this is for admin view
     * @return string HTML content
     * @deprecated Use App\View\Components\ItemCardView instead
     */
    private static function renderItemCard($item, $isAdmin) {
        // Ensure image_url is set for compatibility with ItemCardView
        if (!isset($item['image_url']) && isset($item['image'])) {
            $item['image_url'] = $item['image'];
        }
        
        return \App\View\Components\ItemCardView::render($item, $isAdmin);
    }
    
    /**
     * Affiche les détails d'un objet
     * 
     * @param array $item Données de l'objet
     * @param bool $isAdmin Si l'utilisateur est administrateur
     * @return string Contenu HTML
     */
    public static function renderSingle($item) {
        // Prepare user data for navbar
        $user = [
            'isAuthenticated' => isset($_SESSION['authenticated']),
            'username' => $_SESSION['username'] ?? '',
            'role' => $_SESSION['role'] ?? ''
        ];
        
        $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
        
        // Build the main content
        ob_start();
?>
        <div class="max-w-4xl mx-auto px-4 py-8">
            <!-- Breadcrumb -->
            <div class="text-sm text-gray-500 mb-6">
                <a href="/items" class="hover:underline">Articles</a> &gt; <?= self::h($item['name']) ?>
            </div>
            
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                <div class="md:flex">
                    <!-- Image -->
                    <div class="md:w-1/2">
                        <?php if (!empty($item['image_url'])): ?>
                            <?php 
                                // S'assurer que l'URL de l'image a le bon préfixe
                                $imagePath = $item['image_url'];
                                if (strpos($imagePath, '/') === false) {
                                    $imagePath = '/public/img/items/' . $imagePath;
                                }
                            ?>
                            <img src="<?= self::h($imagePath) ?>" 
                                 alt="<?= self::h($item['name']) ?>" 
                                 class="w-full h-64 md:h-full object-cover">
                        <?php else: ?>
                            <div class="h-64 md:h-full bg-gray-100 flex items-center justify-center">
                                <span class="text-gray-400">Pas d'image</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Item details -->
                    <div class="md:w-1/2 p-6">
                        <div class="flex justify-between items-start">
                            <h1 class="text-2xl font-bold"><?= self::h($item['name']) ?></h1>
                            
                            <?php if (!empty($item['category'])): ?>
                                <?= \App\View\Components\BadgeView::render($item['category']) ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-4">
                            <?= \App\View\Components\BadgeView::render(
                                $item['available'] ? 'Disponible' : 'Non disponible',
                                $item['available'] ? 'success' : 'danger'
                            ) ?>
                        </div>
                        
                        <div class="mt-6">
                            <h2 class="text-lg font-semibold mb-2">Description</h2>
                            <p class="text-gray-700"><?= nl2br(self::h($item['description'])) ?></p>
                        </div>
                        
                        <div class="mt-6 text-sm text-gray-500">
                            <p>Ajouté par: <?= self::h($item['owner_name'] ?? 'Inconnu') ?></p>
                            <p>Ajouté le: <span class=""><?= self::h($item['created_at']) ?></span></p>
                        </div>
                        
                        <!-- Action buttons -->
                        <div class="mt-8 flex flex-wrap gap-3">
                            <?php if ($isAdmin): ?>
                                <a href="/items/<?= $item['id'] ?>/edit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-black hover:bg-gray-800">
                                    Modifier
                                </a>
                                <form action="/items/<?= $item['id'] ?>/delete" method="POST" class="inline-block">
                                    <input type="hidden" name="csrf_token" value="<?= self::h($_SESSION['csrf_token']) ?>">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700" data-confirm="Êtes-vous sûr de vouloir supprimer cet article?">
                                        Supprimer
                                    </button>
                                </form>
                            <?php elseif (isset($_SESSION['authenticated']) && $item['available']): ?>
                                <button onclick="openLoanRequestModal(<?= $item['id'] ?>, '<?= addslashes(self::h($item['name'])) ?>')" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-black hover:bg-gray-800">
                                    Emprunter
                                </button>
                            <?php endif; ?>
                            <a href="/items" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Retour à la liste
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
        $content = ob_get_clean();
        
        // Additional scripts
        $scripts = [];
        
        // Add loan request modal for authenticated non-admin users if item is available
        if (isset($_SESSION['authenticated']) && $_SESSION['role'] !== 'admin' && $item['available']) {
            $scripts[] = '<script src="/public/js/loan-request.js"></script>';
            $content .= LoanRequestModalView::render();
        }
        
        // Render the complete layout
        return LayoutView::render(self::h($item['name']) . ' | Catalogue', $content, $user, $scripts);
    }

    /**
     * Affiche la liste des objets en mode grille
     * 
     * @param array $items Tableau des objets à afficher
     * @return string Contenu HTML
     */
    public static function renderGrid($items = []) {
        // Implementation of renderGrid method
    }

    /**
     * Affiche le formulaire de modification d'un objet
     * 
     * @param array $item Données de l'objet
     * @return string Contenu HTML
     */
    public static function renderEditForm($item) {
        // Implementation of renderEditForm method
    }
}