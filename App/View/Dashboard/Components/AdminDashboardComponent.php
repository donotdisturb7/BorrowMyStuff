<?php
namespace App\View\Dashboard\Components;

use App\View\Components\Dashboard\DashboardStatsView;
use App\View\Components\Dashboard\DashboardLoansTableView;

class AdminDashboardComponent {
    /**
     * Helper function pour simplifier htmlspecialchars
     */
    private static function h($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Render the admin dashboard content
     * 
     * @param array $data Dashboard data
     * @return string HTML content
     */
    public static function render($data) {
        $user = $data['user'];
        $pendingLoans = $data['pendingLoans'] ?? [];
        $stats = $data['stats'] ?? [];
        $adminLoans = $data['adminLoans'] ?? [];
        $adminItemLoans = $data['adminItemLoans'] ?? [];
        
        ob_start();
?>
<!-- Welcome Section -->
<div class="mb-10 fade-in">
    <div class="flex flex-col md:flex-row md:items-end justify-between">
        <div>
            <h1 class="text-3xl md:text-4xl font-light text-black">
                Bienvenue, <span class="font-medium"><?= self::h($user['username']) ?></span>
            </h1>
            <p class="mt-2 text-gray-600">Voici un aperçu de votre système de prêt</p>
        </div>
        <div class="mt-4 md:mt-0 bg-primary text-white px-4 py-2 inline-flex items-center space-x-2 rounded-md">
            <span class="material-icons-outlined text-sm">today</span>
            <span id="currentDate" class="text-sm"></span>
        </div>
    </div>
</div>

<!-- Stats Component -->
<div class="mb-10 fade-in" style="animation-delay: 0.1s">
    <?= DashboardStatsView::render($stats) ?>
</div>

<!-- Loans Table Component -->
<div class="fade-in" style="animation-delay: 0.2s">
    <?= DashboardLoansTableView::render($pendingLoans) ?>
</div>

<!-- Admin's Personal Loans -->
<div class="fade-in mt-10" style="animation-delay: 0.3s">
    <h2 class="text-xl font-medium text-black mb-4">Mes demandes de prêt</h2>
    <?php if (empty($adminLoans)): ?>
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <p class="text-gray-600">Vous n'avez encore fait aucune demande d'emprunt.</p>
            <a href="/items" class="mt-4 inline-flex items-center text-primary hover:underline">
                <i class="fas fa-arrow-right mr-2"></i>
                Parcourir les objets disponibles
            </a>
        </div>
    <?php else: ?>
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Mes demandes en cours</h3>
                <p class="mt-1 text-sm text-gray-600">Liste des objets que vous avez demandé à emprunter.</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Objet</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demandeur</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de demande</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($adminLoans as $loan): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <?php if (!empty($loan['image_url'])): ?>
                                            <?php 
                                                // S'assurer que l'URL de l'image a le bon préfixe
                                                $imagePath = $loan['image_url'];
                                                if (strpos($imagePath, 'https://') !== 0 && strpos($imagePath, '/') === false) {
                                                    $imagePath = '/public/img/items/' . $imagePath;
                                                }
                                            ?>
                                            <img src="<?= self::h($imagePath) ?>" alt="<?= self::h($loan['item_name']) ?>" class="h-10 w-10 rounded object-cover mr-3">
                                        <?php else: ?>
                                            <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center mr-3">
                                                <span class="material-icons-outlined text-gray-400">inventory_2</span>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?= self::h($loan['item_name']) ?></div>
                                            <div class="text-sm text-gray-500">Propriétaire: <?= self::h($loan['owner_name'] ?? 'Inconnu') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= self::h($loan['requester_name']) ?>
                                        <div class="text-sm text-gray-500"><?= self::h($loan['requester_email']) ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= date('d/m/Y', strtotime($loan['request_date'])) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= date('d/m/Y', strtotime($loan['start_date'])) ?> - <?= date('d/m/Y', strtotime($loan['end_date'])) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php if ($loan['status'] === 'pending'): ?>
                                        <form action="/loans/<?= $loan['id'] ?>/cancel" method="POST" class="inline-block">
                                            <input type="hidden" name="csrf_token" value="<?= self::h($_SESSION['csrf_token']) ?>">
                                            <button type="submit" data-confirm="Êtes-vous sûr de vouloir annuler cette demande?" class="text-red-600 hover:text-red-900 ml-2">
                                                Annuler
                                            </button>
                                        </form>
                                    <?php elseif ($loan['status'] === 'accepted'): ?>
                                        <span class="text-gray-500">Retour prévu le <?= date('d/m/Y', strtotime($loan['end_date'])) ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-500">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Loans for Admin's Items -->
<div class="fade-in mt-10" style="animation-delay: 0.4s">
    <h2 class="text-xl font-medium text-black mb-4">Objets actuellement en prêt</h2>
    <?php if (empty($adminItemLoans)): ?>
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <p class="text-gray-600">Aucun de vos objets n'est actuellement prêté.</p>
        </div>
    <?php else: ?>
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="p-6 bg-white border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Vos objets actuellement prêtés</h3>
                <p class="mt-1 text-sm text-gray-600">Liste de vos objets qui sont en cours de prêt.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Objet</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emprunteur</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Retour prévu</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($adminItemLoans as $loan): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <?php if (!empty($loan['image_url'])): ?>
                                            <?php 
                                                // S'assurer que l'URL de l'image a le bon préfixe
                                                $imagePath = $loan['image_url'];
                                                if (strpos($imagePath, 'https://') !== 0 && strpos($imagePath, '/') === false) {
                                                    $imagePath = '/public/img/items/' . $imagePath;
                                                }
                                            ?>
                                            <img src="<?= self::h($imagePath) ?>" alt="<?= self::h($loan['item_name']) ?>" class="h-10 w-10 rounded object-cover mr-3">
                                        <?php else: ?>
                                            <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center mr-3">
                                                <span class="material-icons-outlined text-gray-400">inventory_2</span>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?= self::h($loan['item_name']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= self::h($loan['requester_name']) ?>
                                        <div class="text-sm text-gray-500"><?= self::h($loan['requester_email']) ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?= date('d/m/Y', strtotime($loan['start_date'])) ?> - <?= date('d/m/Y', strtotime($loan['end_date'])) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (strtotime($loan['end_date']) < time()): ?>
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            En retard (<?= date('d/m/Y', strtotime($loan['end_date'])) ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-900">
                                            <?= date('d/m/Y', strtotime($loan['end_date'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form action="/loans/<?= $loan['id'] ?>/return" method="POST" class="inline-block">
                                        <input type="hidden" name="csrf_token" value="<?= self::h($_SESSION['csrf_token']) ?>">
                                        <button type="submit" class="bg-black text-white px-3 py-1.5 rounded text-xs font-medium flex items-center">
                                            <span class="material-icons-outlined text-sm mr-1">check_circle</span>
                                            Marquer comme retourné
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
        return ob_get_clean();
    }
} 