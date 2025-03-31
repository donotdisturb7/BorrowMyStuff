<?php

namespace App\View\Home;

use App\View\Components\Items\ItemCardView;
use App\View\Components\Layout\NavbarView;
use App\View\Components\Common\PaginationView;
use App\View\Components\Layout\SectionTitleView;
use App\View\Components\Items\ItemGridView;
use App\View\Components\Layout\LayoutView;

class HomeView {
    /**
     * Helper function pour simplifier htmlspecialchars
     */
    private static function h($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    public static function render($data = []) {
        $items = $data['items'] ?? [];
        $isAdmin = $data['isAdmin'] ?? false;
        $user = $data['user'] ?? [];
       
        $pagination = $data['pagination'] ?? [];
        $carouselItems = $data['carouselItems'] ?? [];
        
        // Prepare content for the layout
        ob_start();
        
        // Items section
        echo '<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">';
        
        
        
        // Section title with optional action button
        
        $actionButton = $isAdmin ? ['text' => 'Ajouter un nouvel article', 'url' => '/dashboard?tab=add-item'] : null;
        echo SectionTitleView::render('Articles disponibles', $actionButton);
        
        // Items grid
        echo ItemGridView::render($items, $isAdmin);
        
        // Pagination
        if (!empty($pagination) && $pagination['totalPages'] > 1) {
            echo PaginationView::render($pagination, '/page', );
        }
        
        echo '</div>';
        
        // Item Detail Modal
        ?>
        <!-- Item Detail Modal -->
        <div id="itemModal" class="fixed inset-0 bg-black bg-opacity-30 overflow-y-auto h-full w-full hidden z-50 flex justify-center items-center">
            <div class="relative mx-auto p-5 border border-gray-200 w-full max-w-2xl bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                        <h3 class="text-xl font-light text-black" id="modal-item-name"></h3>
                        <button onclick="closeItemModal()" class="text-gray-400 hover:text-black">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="p-2">
                        <p class="text-gray-700 text-base" id="modal-item-description"></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
        
        // Get the main content
        $content = ob_get_clean();
        
        // Render the complete layout
        return LayoutView::render(
            'BorrowMyStuff',
            $content,
            $user,
            [
                '<script>
                    function openItemModal(id, name, description) {
                        document.getElementById("modal-item-name").textContent = name;
                        document.getElementById("modal-item-description").textContent = description;
                        document.getElementById("itemModal").classList.remove("hidden");
                    }
                    
                    function closeItemModal() {
                        document.getElementById("itemModal").classList.add("hidden");
                    }
                </script>'
            ]
        );
    }
}
