<?php

namespace App\View\Components\Dashboard;

class DashboardStatsView {
    /**
     * Helper function pour simplifier htmlspecialchars
     */
    private static function h($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Render the dashboard stats cards
     * 
     * @param array $stats Statistics data
     * @return string The HTML for the stats cards
     */
    public static function render($stats) {
        ob_start();
?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border border-gray-100 rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Articles disponibles</p>
                        <h3 class="text-3xl font-bold text-black mt-1"><?= $stats['availableItems'] ?? 0 ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-black/5 rounded-full flex items-center justify-center">
                        <span class="material-icons-outlined text-black">inventory_2</span>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="/items/available" class="text-sm text-black hover:underline">Voir tous les articles →</a>
                </div>
            </div>
            
            <div class="bg-white border border-gray-100 rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total des articles</p>
                        <h3 class="text-3xl font-bold text-black mt-1"><?= $stats['totalItems'] ?? 0 ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-black/5 rounded-full flex items-center justify-center">
                        <span class="material-icons-outlined text-black">category</span>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="/items" class="text-sm text-black hover:underline">Voir tous les articles →</a>
                </div>
            </div>
            
            <div class="bg-white border border-gray-100 rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Prêts en attente</p>
                        <h3 class="text-3xl font-bold text-black mt-1"><?= $stats['pendingLoans'] ?? 0 ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-black/5 rounded-full flex items-center justify-center">
                        <span class="material-icons-outlined text-black">pending_actions</span>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="/loans/pending" class="text-sm text-black hover:underline">Voir les demandes →</a>
                </div>
            </div>
        </div>
<?php
        return ob_get_clean();
    }
}
