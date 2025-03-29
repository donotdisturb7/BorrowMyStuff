<?php

namespace App\View\Components\Items;

class ItemGridView {
    /**
     * Affiche une grille d'objets
     * 
     * @param array $items Tableau des objets à afficher
     * @param bool $isAdmin Si l'utilisateur actuel est administrateur
     * @param string $emptyMessage Message à afficher quand il n'y a pas d'objets
     * @return string Le HTML de la grille d'objets
     */
    public static function render($items = [], $isAdmin = false, $emptyMessage = 'Aucun article trouvé.') {
        ob_start();
?>
    <?php if (empty($items)): ?>
    <div class="px-4 sm:px-0">
        <div class="bg-white border border-gray-200 overflow-hidden p-6 text-center rounded-lg">
            <p class="text-gray-500 text-lg"><?= $emptyMessage ?> <?= $isAdmin ? 'Cliquez sur "Ajouter un nouvel article" pour commencer.' : '' ?></p>
        </div>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 px-4 sm:px-0">
        <?php foreach ($items as $item): ?>
            <?= ItemCardView::render($item, $isAdmin) ?>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
<?php
        return ob_get_clean();
    }
}
