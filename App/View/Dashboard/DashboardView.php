<?php
namespace App\View\Dashboard;

use App\View\Components\Dashboard\DashboardHeaderView;
use App\View\Components\Dashboard\DashboardSidebarView;
use App\View\Components\Dashboard\DashboardStatsView;
use App\View\Components\Dashboard\DashboardLoansTableView;
use App\View\Item\ItemFormTabView;

class DashboardView {
    /**
     * Render admin dashboard
     */
    public static function renderAdmin($data) {
        // Extract data
        $user = $data['user'];
        $pendingLoans = $data['pendingLoans'];
        $stats = $data['stats'];
        $activeTab = $data['activeTab'] ?? 'dashboard';
        $categories = $data['categories'] ?? [];
        $errors = $data['errors'] ?? [];
        $adminLoans = $data['adminLoans'] ?? [];
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Administration - Système de Prêt</title>
            <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <script src="https://cdn.tailwindcss.com"></script>
            <script>
                tailwind.config = {
                    theme: {
                        extend: {
                            colors: {
                                primary: '#000000',
                                secondary: '#FFFFFF'
                            }
                        }
                    }
                }
            </script>
            <style>
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                .fade-in {
                    animation: fadeIn 0.5s ease-in-out;
                }
            </style>
        </head>
        <body class="bg-gray-50 min-h-screen">
            <!-- Overlay for mobile menu -->
            <div class="overlay fixed inset-0 bg-black/50 z-30 hidden opacity-0 transition-opacity duration-300" id="overlay"></div>
            
            <!-- Sidebar Component -->
            <?= DashboardSidebarView::render(true) ?>
            
            <!-- Header Component -->
            <?= DashboardHeaderView::render($user) ?>
            
            <!-- Main Content -->
            <div class="lg:ml-64 min-h-screen pt-16 pb-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <!-- Welcome Section -->
                    <div class="mb-10 fade-in">
                        <div class="flex flex-col md:flex-row md:items-end justify-between">
                            <div>
                                <h1 class="text-3xl md:text-4xl font-light text-black">
                                    Bienvenue, <span class="font-medium"><?= htmlspecialchars($user['username']) ?></span>
                                </h1>
                                <p class="mt-2 text-gray-600">Voici un aperçu de votre système de prêt</p>
                            </div>
                            <div class="mt-4 md:mt-0 bg-primary text-white px-4 py-2 inline-flex items-center space-x-2 rounded-md">
                                <span class="material-icons-outlined text-sm">today</span>
                                <span id="currentDate" class="text-sm"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Afficher les messages d'alerte généraux -->
                    <?php if (isset($_SESSION['alert'])): ?>
                        <div class="mb-6 <?= $_SESSION['alert']['type'] === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-700' ?> rounded-lg p-4 fade-in">
                            <p class="flex items-center">
                                <i class="fas <?= $_SESSION['alert']['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-2"></i>
                                <?= htmlspecialchars($_SESSION['alert']['message']) ?>
                            </p>
                        </div>
                        <?php unset($_SESSION['alert']); ?>
                    <?php endif; ?>
                    
                    <!-- Afficher les messages de succès -->
                    <?php if (isset($_SESSION['loan_success'])): ?>
                        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 fade-in">
                            <p class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <?= htmlspecialchars($_SESSION['loan_success']) ?>
                            </p>
                        </div>
                        <?php unset($_SESSION['loan_success']); ?>
                    <?php endif; ?>
                    
                    <!-- Tabs -->
                    <div class="mb-6 border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <a href="/dashboard" 
                               class="<?= $activeTab === 'dashboard' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> 
                                      whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                <i class="fas fa-chart-line mr-2"></i>Tableau de bord
                            </a>
                            <a href="/dashboard?tab=add-item" 
                               class="<?= $activeTab === 'add-item' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?> 
                                      whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                <i class="fas fa-plus mr-2"></i>Ajouter un objet
                            </a>
                        </nav>
                    </div>
                    
                    <?php if ($activeTab === 'dashboard'): ?>
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
                                    <h3 class="text-lg font-medium text-gray-900">Demandes de prêt en attente pour vos objets</h3>
                                    <p class="mt-1 text-sm text-gray-600">Liste des demandes concernant vos objets qui nécessitent votre approbation.</p>
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
                                                                <img src="/<?= htmlspecialchars($loan['image_url']) ?>" alt="<?= htmlspecialchars($loan['item_name']) ?>" class="h-10 w-10 rounded-full object-cover mr-3">
                                                            <?php else: ?>
                                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                                    <span class="material-icons-outlined text-gray-400">inventory_2</span>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div>
                                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($loan['item_name']) ?></div>
                                                                <div class="text-sm text-gray-500">Propriétaire: <?= htmlspecialchars($loan['owner_name'] ?? 'Inconnu') ?></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($loan['requester_name']) ?></div>
                                                        <div class="text-xs text-gray-500"><?= htmlspecialchars($loan['requester_email']) ?></div>
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
                                                            <form action="/loans/<?= $loan['id'] ?>/cancel" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande?');">
                                                                <button type="submit" class="text-red-600 hover:text-red-900">
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
                        <?php if (empty($data['adminItemLoans'])): ?>
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
                                            <?php foreach ($data['adminItemLoans'] as $loan): ?>
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <?php if (!empty($loan['image_url'])): ?>
                                                                <img src="/<?= htmlspecialchars($loan['image_url']) ?>" alt="<?= htmlspecialchars($loan['item_name']) ?>" class="h-10 w-10 rounded object-cover mr-3">
                                                            <?php else: ?>
                                                                <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center mr-3">
                                                                    <span class="material-icons-outlined text-gray-400">inventory_2</span>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div>
                                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($loan['item_name']) ?></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($loan['requester_name']) ?></div>
                                                        <div class="text-xs text-gray-500"><?= htmlspecialchars($loan['requester_email']) ?></div>
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
                    <?php else: ?>
                        <!-- Add Item Form -->
                        <div class="fade-in">
                            <?= ItemFormTabView::render(null, $errors, $categories) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <script>
                // Mobile menu functionality
                document.addEventListener('DOMContentLoaded', function() {
                    const mobileMenuButton = document.getElementById('mobile-menu-button');
                    const sidebar = document.querySelector('.sidebar');
                    const overlay = document.getElementById('overlay');

                    function toggleMobileMenu() {
                        sidebar.classList.toggle('-translate-x-full');
                        sidebar.classList.toggle('translate-x-0');
                        overlay.classList.toggle('hidden');
                        setTimeout(() => overlay.classList.toggle('opacity-0'), 0);
                        document.body.classList.toggle('overflow-hidden');
                    }

                    if (mobileMenuButton) {
                        mobileMenuButton.addEventListener('click', toggleMobileMenu);
                    }
                    
                    if (overlay) {
                        overlay.addEventListener('click', toggleMobileMenu);
                    }

                    // Format current date
                    const now = new Date();
                    const options = { year: 'numeric', month: 'long', day: 'numeric' };
                    const formattedDate = now.toLocaleDateString('fr-FR', options);
                    const dateElement = document.getElementById('currentDate');
                    if (dateElement) {
                        dateElement.textContent = formattedDate;
                    }
                });
            </script>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    /**
     * Render user dashboard
     */
    public static function renderUser($data) {
        // Extract data
        $user = $data['user'];
        $loans = $data['loans'] ?? [];
        
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Mon tableau de bord - Système de Prêt</title>
            <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <script src="https://cdn.tailwindcss.com"></script>
            <script>
                tailwind.config = {
                    theme: {
                        extend: {
                            colors: {
                                primary: '#000000',
                                secondary: '#FFFFFF'
                            }
                        }
                    }
                }
            </script>
            <style>
                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                .fade-in {
                    animation: fadeIn 0.5s ease-in-out;
                }
            </style>
        </head>
        <body class="bg-gray-50 min-h-screen">
            <!-- Overlay for mobile menu -->
            <div class="overlay fixed inset-0 bg-black/50 z-30 hidden opacity-0 transition-opacity duration-300" id="overlay"></div>
            
            <!-- Sidebar Component -->
            <?= DashboardSidebarView::render(false) ?>
            
            <!-- Header Component -->
            <?= DashboardHeaderView::render($user) ?>
            
            <!-- Main Content -->
            <div class="lg:ml-64 min-h-screen pt-16 pb-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <!-- Welcome Section -->
                    <div class="mb-10 fade-in">
                        <div class="flex flex-col md:flex-row md:items-end justify-between">
                            <div>
                                <h1 class="text-3xl md:text-4xl font-light text-black">
                                    Bienvenue, <span class="font-medium"><?= htmlspecialchars($user['username']) ?></span>
                                </h1>
                                <p class="mt-2 text-gray-600">Consultez vos demandes de prêt</p>
                            </div>
                            <div class="mt-4 md:mt-0 bg-primary text-white px-4 py-2 inline-flex items-center space-x-2 rounded-md">
                                <span class="material-icons-outlined text-sm">today</span>
                                <span id="currentDate" class="text-sm"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Afficher les messages de succès -->
                    <?php if (isset($_SESSION['loan_success'])): ?>
                        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 fade-in">
                            <p class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <?= htmlspecialchars($_SESSION['loan_success']) ?>
                            </p>
                        </div>
                        <?php unset($_SESSION['loan_success']); ?>
                    <?php endif; ?>
                    
                    <!-- Afficher les erreurs -->
                    <?php if (isset($_SESSION['loan_errors']) && !empty($_SESSION['loan_errors'])): ?>
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 fade-in">
                            <?php foreach ($_SESSION['loan_errors'] as $error): ?>
                                <p class="flex items-center mb-1">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    <?= htmlspecialchars($error) ?>
                                </p>
                            <?php endforeach; ?>
                        </div>
                        <?php unset($_SESSION['loan_errors']); ?>
                    <?php endif; ?>
                    
                    <!-- My Loans -->
                    <div class="fade-in mb-10">
                        <h2 class="text-xl font-medium text-black mb-4">Mes demandes de prêt</h2>
                        <?php if (empty($loans)): ?>
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
                                    <h3 class="text-lg font-medium text-gray-900">Demandes de prêt en attente pour vos objets</h3>
                                    <p class="mt-1 text-sm text-gray-600">Liste des demandes concernant vos objets qui nécessitent votre approbation.</p>
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
                                            <?php foreach ($loans as $loan): ?>
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="flex items-center">
                                                            <?php if (!empty($loan['image_url'])): ?>
                                                                <img src="/<?= htmlspecialchars($loan['image_url']) ?>" alt="<?= htmlspecialchars($loan['item_name']) ?>" class="h-10 w-10 rounded-full object-cover mr-3">
                                                            <?php else: ?>
                                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                                    <span class="material-icons-outlined text-gray-400">inventory_2</span>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div>
                                                                <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($loan['item_name']) ?></div>
                                                                <div class="text-sm text-gray-500">Propriétaire: <?= htmlspecialchars($loan['owner_name'] ?? 'Inconnu') ?></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($loan['requester_name']) ?></div>
                                                        <div class="text-xs text-gray-500"><?= htmlspecialchars($loan['requester_email']) ?></div>
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
                                                            <form action="/loans/<?= $loan['id'] ?>/cancel" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande?');">
                                                                <button type="submit" class="text-red-600 hover:text-red-900">
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
                    
                    <!-- Active Loans -->
                    <div class="fade-in">
                        <h2 class="text-xl font-medium text-black mb-4">Objets actuellement empruntés</h2>
                        <?php 
                        $activeLoans = array_filter($loans, function($loan) {
                            return $loan['status'] === 'accepted';
                        });
                        
                        if (empty($activeLoans)): 
                        ?>
                            <div class="bg-white p-6 rounded-lg shadow-sm">
                                <p class="text-gray-600">Vous n'avez actuellement aucun objet emprunté.</p>
                            </div>
                        <?php else: ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <?php foreach ($activeLoans as $loan): ?>
                                    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
                                        <?php if (!empty($loan['image_url'])): ?>
                                            <img src="/<?= htmlspecialchars($loan['image_url']) ?>" alt="<?= htmlspecialchars($loan['item_name']) ?>" class="w-full h-48 object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                                <span class="material-icons-outlined text-gray-400 text-4xl">inventory_2</span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="p-4">
                                            <h3 class="text-lg font-medium text-black"><?= htmlspecialchars($loan['item_name']) ?></h3>
                                            <p class="text-sm text-gray-600 mt-1">Propriétaire: <?= htmlspecialchars($loan['owner_name'] ?? 'Inconnu') ?></p>
                                            <div class="mt-4">
                                                <p class="text-xs text-gray-500">Date de retour:</p>
                                                <p class="text-sm font-medium <?= strtotime($loan['end_date']) < time() ? 'text-red-600' : 'text-gray-900' ?>">
                                                    <?= date('d/m/Y', strtotime($loan['end_date'])) ?>
                                                    <?php if (strtotime($loan['end_date']) < time()): ?>
                                                        <span class="text-red-600 font-bold"> (En retard)</span>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <script>
                // Mobile menu functionality
                document.addEventListener('DOMContentLoaded', function() {
                    const mobileMenuButton = document.getElementById('mobile-menu-button');
                    const sidebar = document.querySelector('.sidebar');
                    const overlay = document.getElementById('overlay');

                    function toggleMobileMenu() {
                        sidebar.classList.toggle('-translate-x-full');
                        sidebar.classList.toggle('translate-x-0');
                        overlay.classList.toggle('hidden');
                        setTimeout(() => overlay.classList.toggle('opacity-0'), 0);
                        document.body.classList.toggle('overflow-hidden');
                    }

                    if (mobileMenuButton) {
                        mobileMenuButton.addEventListener('click', toggleMobileMenu);
                    }
                    
                    if (overlay) {
                        overlay.addEventListener('click', toggleMobileMenu);
                    }

                    // Format current date
                    const now = new Date();
                    const options = { year: 'numeric', month: 'long', day: 'numeric' };
                    const formattedDate = now.toLocaleDateString('fr-FR', options);
                    const dateElement = document.getElementById('currentDate');
                    if (dateElement) {
                        dateElement.textContent = formattedDate;
                    }
                });
            </script>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}