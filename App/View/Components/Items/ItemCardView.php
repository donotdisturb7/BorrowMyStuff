<?php

namespace App\View\Components\Items;

use App\View\Components\Item\ItemLoanRequestModal;

class ItemCardView {
    /**
     * Helper function pour simplifier htmlspecialchars
     */
    private static function h($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * Affiche une carte d'objet
     * 
     * @param array $item Les données de l'objet
     * @param bool $isAdmin Si l'utilisateur actuel est administrateur
     * @return string Le HTML de la carte d'objet
     */
    public static function render($item, $isAdmin = false) {
        // Générer un ID unique pour le modal
        $modalId = 'item_modal_' . $item['id'];
        $isAuthenticated = isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
        $isAdminUser = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
        $currentUserId = $_SESSION['user_id'] ?? 0;
        
        // Vérifier si l'utilisateur est le propriétaire de l'objet
        $isOwner = false;
        if ($currentUserId > 0 && isset($item['owner_id'])) {
            $isOwner = (int)$currentUserId === (int)$item['owner_id'];
        }
        
        // Determine if item is available
        $isAvailable = $item['available'] ?? false;
        
        ob_start();
?>
        <figure class="relative h-96 w-full group overflow-hidden rounded-xl">
            <!-- Badge du propriétaire -->
            <?php if (!empty($item['owner_name']) && !$isOwner): ?>
            <div class="absolute top-4 left-4 z-10 bg-black bg-opacity-70 text-white text-xs font-medium px-2.5 py-1.5 rounded backdrop-blur-md">
                Par <?= self::h($item['owner_name']) ?>
            </div>
            <?php endif; ?>
            
            <!-- Item image -->
            <?php if (!empty($item['image_url'])): ?>
                <?php 
                    // S'assurer que l'URL de l'image a le bon préfixe
                    $imagePath = $item['image_url'];
                    if (strpos($imagePath, 'https://') !== 0 && strpos($imagePath, '/') === false) {
                        $imagePath = '/public/img/items/' . $imagePath;
                    }
                ?>
                <img src="<?= self::h($imagePath) ?>" alt="<?= self::h($item['name']) ?>" class="h-full w-full rounded-xl object-cover object-center" />
            <?php else: ?>
            <div class="h-full w-full bg-gray-900 flex items-center justify-center rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <?php endif; ?>
            
            <!-- Figcaption avec effet de flou -->
            <figcaption class="rounded-lg border shadow-sm overflow-hidden bg-white border-stone-200 shadow-stone-950/5 absolute bottom-4 left-1/2 w-[calc(100%-2rem)] -translate-x-1/2 bg-opacity-75 saturate-200 backdrop-blur-md">
                <div class="w-full h-max rounded py-2.5 flex justify-between px-4">
                    <div>
                        <h6 class="font-sans antialiased font-bold text-base md:text-lg lg:text-xl text-current">
                            <a href="/items/<?= $item['id'] ?>" class="hover:text-gray-600">
                                <?= self::h($item['name']) ?>
                            </a>
                        </h6>
                        <p class="font-sans antialiased text-base mt-1 text-stone-600"><?= date('d F Y', strtotime($item['created_at'])) ?></p>
                    </div>
                    <div class="flex flex-col items-end">
                        <?php if (!empty($item['category'])): ?>
                        <p class="font-sans antialiased text-base text-current font-bold"><?= self::h($item['category']) ?></p>
                        <?php endif; ?>
                        <?php if (!$isAvailable): ?>
                        <p class="font-sans antialiased text-base text-red-600 font-bold">Non disponible</p>
                        <?php endif; ?>
                        <?php if ($isOwner): ?>
                        <p class="font-sans antialiased text-xs text-blue-600 font-bold mt-1">Ajouté par vous</p>
                        <?php endif; ?>
                    </div>
                </div>
            </figcaption>
            
            <!-- Actions en overlay au survol -->
            <div class="absolute top-4 right-4 z-10 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <?php if ($isOwner || $isAdminUser): ?>
                <!-- Afficher les actions de propriétaire ou d'administrateur -->
                <div class="flex flex-col gap-2">
                    <a href="/items/<?= $item['id'] ?>/edit" class="rounded-full p-2 bg-white/80 text-black hover:bg-black hover:text-white transition-colors duration-200 backdrop-blur-md" title="Modifier l'objet">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </a>
                    <form action="/items/<?= $item['id'] ?>/delete" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= self::h($_SESSION['csrf_token']) ?>">
                        <button type="submit" class="rounded-full p-2 bg-white/80 text-black hover:bg-red-500 hover:text-white transition-colors duration-200 backdrop-blur-md" title="Supprimer l'objet" data-confirm="Êtes-vous sûr de vouloir supprimer cet article?">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
                <?php elseif ($isAuthenticated): ?>
                <!-- Afficher l'action de voir les détails pour les utilisateurs authentifiés non-propriétaires/non-admins -->
                <a href="/items/<?= $item['id'] ?>" class="rounded-full p-2 bg-white/80 text-black hover:bg-black hover:text-white transition-colors duration-200 backdrop-blur-md" title="Voir les détails">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </a>
                <?php endif; ?>
            </div>
        </figure>
        
        <?php if ($isAuthenticated && !$isOwner && !$isAdminUser && $isAvailable): ?>
        <!-- Modal de demande de prêt (seulement pour les non-propriétaires authentifiés si l'objet est disponible) -->
        <?= \App\View\Components\LoanRequestModalView::render() ?>
        <?php endif; ?>
<?php
        return ob_get_clean();
    }
}
