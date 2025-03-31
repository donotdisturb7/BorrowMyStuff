<?php
namespace App\View\Dashboard;

use App\View\Components\Dashboard\DashboardHeaderView;
use App\View\Components\Dashboard\DashboardSidebarView;
use App\View\Components\Common\NotificationView;
use App\View\Item\ItemFormTabView;
use App\View\Dashboard\Components\AdminDashboardComponent;
use App\View\Dashboard\Components\UserDashboardComponent;
use App\View\Dashboard\Components\LoanDetailsComponent;

class DashboardView {
    /**
     * Helper function pour simplifier htmlspecialchars
     */
    private static function h($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * Render admin dashboard
     * 
     * @param array $data Dashboard data
     * @return string HTML content
     */
    public static function renderAdmin($data) {
        $user = $data['user'];
        $activeTab = $data['activeTab'] ?? 'dashboard';
        $categories = $data['categories'] ?? [];
        $errors = $data['errors'] ?? [];
        
        $content = '';
        
        // Render the appropriate content based on the active tab
        switch ($activeTab) {
            case 'dashboard':
                $content = AdminDashboardComponent::render($data);
                break;
            case 'add-item':
                $content = ItemFormTabView::render(null, $errors, $categories);
                break;
            case 'loan-details':
                $content = LoanDetailsComponent::render($data);
                break;
            default:
                $content = AdminDashboardComponent::render($data);
        }
        
        return self::renderDashboardLayout(
            $user,
            $activeTab, 
            $content,
            true
        );
    }

    /**
     * Render user dashboard
     * 
     * @param array $data Dashboard data
     * @return string HTML content
     */
    public static function renderUser($data) {
        $user = $data['user'];
        $activeTab = $data['activeTab'] ?? 'dashboard';
        $categories = $data['categories'] ?? [];
        $errors = $data['errors'] ?? [];
        
        return self::renderDashboardLayout(
            $user,
            $activeTab, 
            $activeTab === 'dashboard' 
                ? UserDashboardComponent::render($data) 
                : ItemFormTabView::render(null, $errors, $categories),
            false
        );
    }
    
    /**
     * Render loan details
     * 
     * @param array $data Loan details data
     * @return string HTML content
     */
    public static function renderLoanDetails($data) {
        return LoanDetailsComponent::render($data);
    }
    
    /**
     * Render dashboard layout
     * 
     * @param array $user User data
     * @param string $activeTab Active tab
     * @param string $content Tab content
     * @param bool $isAdmin Whether user is admin
     * @return string HTML content
     */
    private static function renderDashboardLayout($user, $activeTab, $content, $isAdmin = false) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord | BorrowMyStuff</title>
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        .primary {
            color: #4f46e5;
        }
        .bg-primary {
            background-color: #4f46e5;
        }
        .border-primary {
            border-color: #4f46e5;
        }
        /* Sidebar animation */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }
            .sidebar.open {
                transform: translateX(0);
            }
                }
            </style>
        </head>
        <body class="bg-gray-50 min-h-screen">
            <!-- Overlay for mobile menu -->
            <div class="overlay fixed inset-0 bg-black/50 z-30 hidden opacity-0 transition-opacity duration-300" id="overlay"></div>
            
            <!-- Sidebar Component -->
    <?= DashboardSidebarView::render($isAdmin) ?>
            
            <!-- Header Component -->
            <?= DashboardHeaderView::render($user) ?>
    
    <!-- Notifications -->
    <?php if (isset($_SESSION['notification'])): ?>
        <?= NotificationView::render($_SESSION['notification']['message'], $_SESSION['notification']['type']) ?>
        <?php unset($_SESSION['notification']); ?>
    <?php endif; ?>
            
            <!-- Main Content -->
            <div class="lg:ml-64 min-h-screen pt-16 pb-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <?php if ($activeTab !== 'loan-details'): ?>
                    <!-- Tabs -->
                    <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 overflow-x-auto">
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
                        <?php endif; ?>
            
            <!-- Afficher les erreurs de formulaire -->
            <?php if (!empty($errors)): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 fade-in">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2 text-xl"></i>
                        <h3 class="font-medium">Erreur(s) dans le formulaire</h3>
                    </div>
                    <?php foreach ($errors as $error): ?>
                        <p class="ml-6 flex items-center mb-1">
                            <i class="fas fa-arrow-right mr-2 text-sm"></i>
                            <?= htmlspecialchars($error) ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <!-- Content -->
            <?= $content ?>
                </div>
            </div>

    <!-- Scripts -->
            <script>
        // Toggle sidebar for mobile
        document.getElementById('menu-button').addEventListener('click', function() {
                    const sidebar = document.querySelector('.sidebar');
                    const overlay = document.getElementById('overlay');

            sidebar.classList.toggle('open');
                        overlay.classList.toggle('hidden');
            
            // Add a slight delay to the opacity transition for a smoother effect
            if (overlay.classList.contains('hidden')) {
                overlay.style.opacity = 0;
            } else {
                setTimeout(() => {
                    overlay.style.opacity = 1;
                }, 50);
            }
        });
        
        // Close sidebar when clicking on overlay
        document.getElementById('overlay').addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('overlay');
            
            sidebar.classList.remove('open');
            overlay.style.opacity = 0;
            
            setTimeout(() => {
                overlay.classList.add('hidden');
            }, 300);
                });
            </script>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}