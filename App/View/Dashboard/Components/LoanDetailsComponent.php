<?php
namespace App\View\Dashboard\Components;

class LoanDetailsComponent {
    /**
     * Helper function pour simplifier htmlspecialchars
     */
    private static function h($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Render loan details
     * 
     * @param array $data Loan data
     * @return string HTML content
     */
    public static function render($data) {
        $loan = $data['loan'];
        $user = $data['user'];
        $isAdmin = isset($user['role']) && $user['role'] === 'admin';
        
        ob_start();
?>
<div class="mb-6">
    <a href="/dashboard" class="text-sm text-primary hover:underline flex items-center">
        <span class="material-icons-outlined text-sm mr-1">arrow_back</span>
        Retour au tableau de bord
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex flex-wrap justify-between items-center">
            <h1 class="text-2xl font-medium text-gray-900 mb-2">Détails de la demande de prêt</h1>
            
            <div>
                <?php if ($loan['status'] === 'pending'): ?>
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-yellow-100 text-yellow-800">En attente</span>
                <?php elseif ($loan['status'] === 'accepted'): ?>
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">Accepté</span>
                <?php elseif ($loan['status'] === 'returned'): ?>
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">Retourné</span>
                <?php elseif ($loan['status'] === 'cancelled'): ?>
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-gray-100 text-gray-800">Annulé</span>
                <?php elseif ($loan['status'] === 'rejected'): ?>
                    <span class="px-3 py-1 text-sm font-medium rounded-full bg-red-100 text-red-800">Refusé</span>
                <?php endif; ?>
            </div>
        </div>
        
        <p class="text-sm text-gray-600 mt-1">
            <strong>Demande créée le :</strong> <?= date('d/m/Y à H:i', strtotime($loan['request_date'])) ?>
        </p>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informations sur l'objet</h2>
                <div class="flex items-start">
                    <?php if (!empty($loan['image_url'])): ?>
                        <?php 
                            // S'assurer que l'URL de l'image a le bon préfixe
                            $imagePath = $loan['image_url'];
                            if (strpos($imagePath, 'https://') !== 0 && strpos($imagePath, '/') === false) {
                                $imagePath = '/public/img/items/' . $imagePath;
                            }
                        ?>
                        <img src="<?= self::h($imagePath) ?>" alt="<?= self::h($loan['item_name']) ?>" class="w-24 h-24 object-cover rounded mr-4">
                    <?php else: ?>
                        <div class="w-24 h-24 bg-gray-200 rounded flex items-center justify-center mr-4">
                            <span class="material-icons-outlined text-gray-400 text-3xl">inventory_2</span>
                        </div>
                    <?php endif; ?>
                    
                    <div>
                        <h3 class="text-xl font-medium"><?= self::h($loan['item_name']) ?></h3>
                        <p class="text-sm text-gray-600 mb-1">
                            <span class="font-medium">Catégorie:</span> <?= self::h($loan['category'] ?? 'Non spécifiée') ?>
                        </p>
                        <p class="text-sm text-gray-600 mb-1">
                            <span class="font-medium">Propriétaire:</span> <?= self::h($loan['owner_name']) ?>
                        </p>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Email:</span> <?= self::h($loan['owner_email']) ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Informations sur l'emprunteur</h2>
                <p><strong>Emprunteur:</strong> <?= self::h($loan['requester_name']) ?></p>
                <p><strong>Email:</strong> <?= self::h($loan['requester_email']) ?></p>
                
                <div class="mt-6">
                    <h3 class="text-md font-medium text-gray-900 mb-2">Période de prêt</h3>
                    <div class="bg-gray-50 p-4 rounded">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Date de début</p>
                                <p class="font-medium"><?= date('d/m/Y', strtotime($loan['start_date'])) ?></p>
                            </div>
                            <div class="border-t-2 border-gray-300 flex-grow mx-4"></div>
                            <div>
                                <p class="text-sm text-gray-600">Date de fin</p>
                                <p class="font-medium"><?= date('d/m/Y', strtotime($loan['end_date'])) ?></p>
                            </div>
                        </div>
                        
                        <?php if ($loan['status'] === 'accepted' && strtotime($loan['end_date']) < time()): ?>
                        <div class="mt-4 bg-red-50 text-red-700 p-3 rounded">
                            <p class="flex items-center">
                                <span class="material-icons-outlined text-sm mr-1">warning</span>
                                Ce prêt est en retard. La date de retour prévue était le <?= date('d/m/Y', strtotime($loan['end_date'])) ?>.
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($loan['notes'])): ?>
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 mb-2">Notes</h2>
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-gray-700"><?= nl2br(self::h($loan['notes'])) ?></p>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Actions</h2>
            
            <div class="flex flex-wrap gap-3">
                <?php if ($loan['status'] === 'pending'): ?>
                    <div class="mt-8 border-t pt-6">
                        <h3 class="font-medium mb-4">Actions:</h3>
                        <div class="flex space-x-4">
                            <?php if ($isAdmin): ?>
                                <form action="/loans/<?= $loan['id'] ?>/approve" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= self::h($_SESSION['csrf_token']) ?>">
                                    <button type="submit" class="bg-black text-white px-4 py-2 rounded font-medium">
                                        Approuver
                                    </button>
                                </form>
                                <form action="/loans/<?= $loan['id'] ?>/reject" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= self::h($_SESSION['csrf_token']) ?>">
                                    <button type="submit" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded font-medium">
                                        Refuser
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if (isset($_SESSION['user_id']) && $loan['requester_id'] == $_SESSION['user_id']): ?>
                                <form action="/loans/<?= $loan['id'] ?>/cancel" method="POST" class="inline-block">
                                    <input type="hidden" name="csrf_token" value="<?= self::h($_SESSION['csrf_token']) ?>">
                                    <button type="submit" data-confirm="Êtes-vous sûr de vouloir annuler cette demande?" class="bg-red-500 text-white px-4 py-2 rounded font-medium">
                                        Annuler
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php elseif ($loan['status'] === 'accepted' && $isAdmin): ?>
                    <div class="mt-8 border-t pt-6">
                        <h3 class="font-medium mb-4">Actions:</h3>
                        <div class="flex space-x-4">
                            <form action="/loans/<?= $loan['id'] ?>/return" method="POST">
                                <input type="hidden" name="csrf_token" value="<?= self::h($_SESSION['csrf_token']) ?>">
                                <button type="submit" class="bg-black text-white px-4 py-2 rounded font-medium">
                                    Marquer comme retourné
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
        return ob_get_clean();
    }
} 