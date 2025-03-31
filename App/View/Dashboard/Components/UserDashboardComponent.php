<?php
namespace App\View\Dashboard\Components;

class UserDashboardComponent {
    /**
     * Helper function pour simplifier htmlspecialchars
     */
    private static function h($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Render user dashboard content
     * 
     * @param array $data Dashboard data including user and loans
     * @return string HTML content
     */
    public static function render($data) {
        $user = $data['user'];
        $loans = $data['loans'] ?? [];
        $userItems = $data['userItems'] ?? [];
        
        ob_start();
?>
<!-- Welcome Section -->
<div class="mb-10 fade-in">
    <div class="flex flex-col md:flex-row md:items-end justify-between">
        <div>
            <h1 class="text-3xl md:text-4xl font-light text-black">
                Bienvenue, <span class="font-medium"><?= self::h($user['username']) ?></span>
            </h1>
            <p class="mt-2 text-gray-600">Voici le récapitulatif de vos emprunts et objets</p>
        </div>
        <div class="mt-4 md:mt-0 bg-primary text-white px-4 py-2 inline-flex items-center space-x-2 rounded-md">
            <span class="material-icons-outlined text-sm">today</span>
            <span id="currentDate" class="text-sm"></span>
        </div>
    </div>
</div>

<!-- User's Activity Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8 fade-in" style="animation-delay: 0.1s">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                <span class="material-icons-outlined">inventory_2</span>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Mes objets</p>
                <h3 class="text-2xl font-medium text-gray-900"><?= count($userItems) ?></h3>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                <span class="material-icons-outlined">handshake</span>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Emprunts actifs</p>
                <h3 class="text-2xl font-medium text-gray-900">
                    <?= count(array_filter($loans, function($loan) { return $loan['status'] === 'accepted'; })) ?>
                </h3>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                <span class="material-icons-outlined">pending</span>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Demandes en attente</p>
                <h3 class="text-2xl font-medium text-gray-900">
                    <?= count(array_filter($loans, function($loan) { return $loan['status'] === 'pending'; })) ?>
                </h3>
            </div>
        </div>
    </div>
</div>

<!-- My Loans Section -->
<div class="mb-10 fade-in" style="animation-delay: 0.2s">
    <h2 class="text-xl font-medium text-black mb-4">Mes emprunts</h2>
    
    <?php if (empty($loans)): ?>
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <p class="text-gray-600">Vous n'avez aucun emprunt en cours.</p>
            <a href="/items" class="mt-4 inline-flex items-center text-primary hover:underline">
                <i class="fas fa-arrow-right mr-2"></i>
                Parcourir les objets disponibles
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php foreach ($loans as $loan): ?>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="relative">
                        <?php if (!empty($loan['image_url'])): ?>
                            <?php 
                                // S'assurer que l'URL de l'image a le bon préfixe
                                $imagePath = $loan['image_url'];
                                if (strpos($imagePath, 'https://') !== 0 && strpos($imagePath, '/') === false) {
                                    $imagePath = '/public/img/items/' . $imagePath;
                                }
                            ?>
                            <img src="<?= self::h($imagePath) ?>" alt="<?= self::h($loan['item_name']) ?>" class="w-full h-48 object-cover">
                        <?php else: ?>
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="material-icons-outlined text-gray-400 text-4xl">inventory_2</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="absolute top-2 right-2">
                            <?php if ($loan['status'] === 'pending'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                            <?php elseif ($loan['status'] === 'accepted'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Accepté</span>
                            <?php elseif ($loan['status'] === 'returned'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Retourné</span>
                            <?php elseif ($loan['status'] === 'cancelled'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Annulé</span>
                            <?php elseif ($loan['status'] === 'rejected'): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Refusé</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="text-lg font-medium text-black"><?= self::h($loan['item_name']) ?></h3>
                        <p class="text-sm text-gray-600 mt-1">Propriétaire: <?= self::h($loan['owner_name'] ?? 'Inconnu') ?></p>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="flex justify-between items-center text-sm">
                                <div>
                                    <span class="font-medium">Période:</span><br>
                                    <?= date('d/m/Y', strtotime($loan['start_date'])) ?> - <?= date('d/m/Y', strtotime($loan['end_date'])) ?>
                                </div>
                                
                                <?php if ($loan['status'] === 'pending'): ?>
                                    <form action="/loans/<?= $loan['id'] ?>/cancel" method="POST">
                                        <input type="hidden" name="csrf_token" value="<?= self::h($_SESSION['csrf_token']) ?>">
                                        <button type="submit" data-confirm="Êtes-vous sûr de vouloir annuler cette demande?" class="text-red-600 hover:text-red-900 ml-2">
                                            Annuler
                                        </button>
                                    </form>
                                <?php elseif ($loan['status'] === 'accepted' && strtotime($loan['end_date']) < time()): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">En retard</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- My Items Section -->
<div class="fade-in" style="animation-delay: 0.3s">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-medium text-black">Mes objets</h2>
        <a href="/dashboard?tab=add-item" class="text-primary hover:underline flex items-center">
            <span class="material-icons-outlined text-sm mr-1">add_circle</span>
            Ajouter un objet
        </a>
    </div>
    
    <?php if (empty($userItems)): ?>
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <p class="text-gray-600">Vous n'avez encore ajouté aucun objet.</p>
            <a href="/dashboard?tab=add-item" class="mt-4 inline-flex items-center text-primary hover:underline">
                <i class="fas fa-plus mr-2"></i>
                Ajouter votre premier objet
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <?php foreach ($userItems as $item): ?>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="relative">
                        <?php if (!empty($item['image_url'])): ?>
                            <?php if (strpos($item['image_url'], 'https://') === 0): ?>
                                <img src="<?= self::h($item['image_url']) ?>" alt="<?= self::h($item['name']) ?>" class="w-full h-48 object-cover">
                            <?php else: ?>
                                <img src="/<?= self::h($item['image_url']) ?>" alt="<?= self::h($item['name']) ?>" class="w-full h-48 object-cover">
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <span class="material-icons-outlined text-gray-400 text-4xl">inventory_2</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="absolute top-2 right-2">
                            <?php if ($item['available']): ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Disponible</span>
                            <?php else: ?>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Non disponible</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="text-lg font-medium text-black"><?= self::h($item['name']) ?></h3>
                        <p class="text-sm text-gray-500"><?= self::h($item['category'] ?? 'Non classé') ?></p>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between">
                            <a href="/items/<?= $item['id'] ?>" class="text-primary hover:underline text-sm">
                                Voir les détails
                            </a>
                            <a href="/items/<?= $item['id'] ?>/edit" class="text-primary hover:underline text-sm">
                                Modifier
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php
        return ob_get_clean();
    }
} 