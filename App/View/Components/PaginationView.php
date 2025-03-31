<?php

namespace App\View\Components;

class PaginationView {
    /**
     * Affiche un composant de pagination
     * 
     * @param int $currentPage Page actuelle
     * @param int $totalPages Nombre total de pages
     * @param string $baseUrl URL de base pour les liens de pagination (sans le paramètre page)
     * @param int $maxPageLinks Nombre maximum de liens de page à afficher (impair de préférence)
     * @return string HTML de la pagination
     */
    public static function render($currentPage, $totalPages, $baseUrl, $maxPageLinks = 5) {
        if ($totalPages <= 1) {
            return '';
        }
        
        // S'assurer que l'URL se termine par ? ou & pour ajouter des paramètres
        if (strpos($baseUrl, '?') === false) {
            $baseUrl .= '?';
        } else if (!str_ends_with($baseUrl, '?') && !str_ends_with($baseUrl, '&')) {
            $baseUrl .= '&';
        }
        
        // Calculer le nombre de liens à afficher avant et après la page courante
        $sideLinks = floor($maxPageLinks / 2);
        
        // Calculer les limites des pages à afficher
        $startPage = max(1, $currentPage - $sideLinks);
        $endPage = min($totalPages, $currentPage + $sideLinks);
        
        // Ajuster si on est proche du début ou de la fin
        if ($startPage > 1 && $endPage < $totalPages) {
            if ($startPage == 2) {
                $endPage = min($totalPages, $endPage + 1);
            } else if ($endPage == $totalPages - 1) {
                $startPage = max(1, $startPage - 1);
            }
        }
        
        // Assurer qu'on a le bon nombre de liens si possible
        if ($endPage - $startPage + 1 < $maxPageLinks && $totalPages > $maxPageLinks) {
            if ($startPage > 1) {
                $startPage = max(1, $startPage - ($maxPageLinks - ($endPage - $startPage + 1)));
            }
            if ($endPage < $totalPages) {
                $endPage = min($totalPages, $endPage + ($maxPageLinks - ($endPage - $startPage + 1)));
            }
        }
        
        ob_start();
?>
<div class="flex items-center justify-center py-6">
    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
        <!-- Bouton précédent -->
        <?php if ($currentPage > 1): ?>
            <a href="<?= $baseUrl ?>page=<?= $currentPage - 1 ?>" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                <span class="sr-only">Précédent</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                </svg>
            </a>
        <?php else: ?>
            <span class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-300 ring-1 ring-inset ring-gray-300 focus:outline-offset-0 cursor-not-allowed">
                <span class="sr-only">Précédent</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                </svg>
            </span>
        <?php endif; ?>
        
        <!-- Afficher le lien vers la première page si nécessaire -->
        <?php if ($startPage > 1): ?>
            <a href="<?= $baseUrl ?>page=1" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                1
            </a>
            <?php if ($startPage > 2): ?>
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">
                    ...
                </span>
            <?php endif; ?>
        <?php endif; ?>
        
        <!-- Pages à afficher -->
        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <?php if ($i == $currentPage): ?>
                <span aria-current="page" class="relative z-10 inline-flex items-center bg-black px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black">
                    <?= $i ?>
                </span>
            <?php else: ?>
                <a href="<?= $baseUrl ?>page=<?= $i ?>" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                    <?= $i ?>
                </a>
            <?php endif; ?>
        <?php endfor; ?>
        
        <!-- Afficher le lien vers la dernière page si nécessaire -->
        <?php if ($endPage < $totalPages): ?>
            <?php if ($endPage < $totalPages - 1): ?>
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">
                    ...
                </span>
            <?php endif; ?>
            <a href="<?= $baseUrl ?>page=<?= $totalPages ?>" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                <?= $totalPages ?>
            </a>
        <?php endif; ?>
        
        <!-- Bouton suivant -->
        <?php if ($currentPage < $totalPages): ?>
            <a href="<?= $baseUrl ?>page=<?= $currentPage + 1 ?>" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                <span class="sr-only">Suivant</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                </svg>
            </a>
        <?php else: ?>
            <span class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-300 ring-1 ring-inset ring-gray-300 focus:outline-offset-0 cursor-not-allowed">
                <span class="sr-only">Suivant</span>
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                </svg>
            </span>
        <?php endif; ?>
    </nav>
</div>
<?php
        return ob_get_clean();
    }
} 