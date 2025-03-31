<?php

namespace App\View\Components\Layout;

use App\View\Components\Dashboard\DashboardHeaderView;
use App\View\Components\Dashboard\DashboardSidebarView;

class LayoutView {
    /**
     * Helper function pour simplifier htmlspecialchars
     */
    private static function h($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * Affiche la mise en page principale
     * 
     * @param string $title Titre de la page
     * @param string $content Contenu HTML principal
     * @param array $user Données de l'utilisateur pour la barre de navigation
     * @param array $scripts Scripts additionnels à inclure
     * @param array $cssFiles Fichiers CSS additionnels à inclure
     * @param bool $showNavbar Afficher ou non la barre de navigation
     * @param bool $showFooter Afficher ou non le pied de page
     * @param string $bodyClass Classes CSS additionnelles pour la balise body
     * @return string La page HTML complète
     */
    public static function render(
        $title, 
        $content, 
        $user = [], 
        $scripts = [], 
        $cssFiles = [],
        $showNavbar = true,
        $showFooter = true,
        $bodyClass = 'bg-white min-h-screen flex flex-col'
    ) {
        ob_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Plateforme de gestion de prêts d'objets entre utilisateurs">
    <title><?= self::h($title) ?> | BorrowMyStuff</title>
    
    <!-- Polices -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#000000',
                        secondary: '#FFFFFF'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- CSS Global -->
    <link rel="stylesheet" href="/public/css/global.css">
    
    <!-- CSS Icônes -->
    <link rel="stylesheet" href="/public/css/icons.css">
    
    <!-- Fichiers CSS additionnels -->
    <?php foreach ($cssFiles as $cssFile): ?>
        <link rel="stylesheet" href="<?= self::h($cssFile) ?>">
    <?php endforeach; ?>
    
    <!-- Favicon -->
    <link rel="icon" href="/public/favicon.ico" type="image/x-icon">
</head>
<body class="<?= self::h($bodyClass) ?>">
    <?php if ($showNavbar): ?>
        <?= NavbarView::render($user) ?>
    <?php endif; ?>

    <!-- Affichage des notifications -->
    <?php if (isset($_SESSION['notification'])): ?>
        <?php 
            $notification = $_SESSION['notification'];
            echo \App\View\Components\Common\NotificationView::render(
                $notification['message'] ?? '',
                $notification['type'] ?? 'info'
            );
            // Supprimer la notification pour qu'elle ne s'affiche qu'une fois
            unset($_SESSION['notification']);
        ?>
    <?php endif; ?>

    <!-- Contenu principal -->
    <main class="flex-grow">
        <?= $content ?>
    </main>

    <?php if ($showFooter): ?>
    <!-- Pied de page -->
    <footer class="bg-white border-t border-gray-200 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 text-sm mb-4 md:mb-0">
                    &copy; <?= date('Y') ?> BorrowMyStuff. Tous droits réservés.
                </p>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-500 hover:text-gray-700">Conditions d'utilisation</a>
                    <a href="#" class="text-gray-500 hover:text-gray-700">Politique de confidentialité</a>
                    <a href="#" class="text-gray-500 hover:text-gray-700">Contact</a>
                </div>
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <!-- Scripts communs -->
    <script>
        // Fonction utilitaire pour formater les dates
        function formatDate(dateString) {
            try {
                const date = new Date(dateString);
                if (isNaN(date)) {
                    console.error('Date invalide:', dateString);
                    return dateString;
                }

                const formatter = new Intl.DateTimeFormat('fr-FR', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                // Formater la date et mettre le mois en minuscules
                return formatter.format(date).replace(
                    /^([0-9]+) ([A-Za-zÀ-ÿ]+) ([0-9]+)$/,
                    (match, day, month, year) => `${day} ${month.toLowerCase()} ${year}`
                );
            } catch (error) {
                console.error('Erreur de formatage de la date:', error);
                return dateString;
            }
        }
        
        // Initialiser tous les éléments avec la classe 'format-date'
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.format-date').forEach(element => {
                if (element.textContent) {
                    element.textContent = formatDate(element.textContent.trim());
                }
            });
        });
    </script>
    
    <!-- Script de confirmation personnalisée -->
    <script src="/public/js/custom-confirm.js"></script>
    
    <!-- Scripts additionnels -->
    <?php foreach ($scripts as $script): ?>
        <?= $script ?>
    <?php endforeach; ?>
</body>
</html>
<?php
        return ob_get_clean();
    }
    
    /**
     * Affiche la mise en page du tableau de bord
     * 
     * @param string $title Titre de la page
     * @param string $content Contenu HTML principal
     * @param array $user Données de l'utilisateur
     * @param array $scripts Scripts additionnels à inclure
     * @return string La page HTML complète
     */
    public static function renderDashboard($title, $content, $user = [], $scripts = []) {
        $dashboardContent = self::wrapDashboardContent($content, $user);
        return self::render($title, $dashboardContent, $user, $scripts, [], true, true, 'bg-gray-50 min-h-screen flex flex-col');
    }
    
    /**
     * Enveloppe le contenu avec la barre latérale et l'en-tête du tableau de bord
     * 
     * @param string $content Contenu HTML principal
     * @param array $user Données de l'utilisateur
     * @return string Le contenu enveloppé
     */
    private static function wrapDashboardContent($content, $user) {
        ob_start();
?>
<div class="flex h-screen bg-gray-50">
    <!-- Barre latérale du tableau de bord -->
    <?= DashboardSidebarView::render() ?>
    
    <!-- Zone de contenu principal du tableau de bord -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- En-tête du tableau de bord -->
        <?= DashboardHeaderView::render($user) ?>
        
        <!-- Contenu du tableau de bord -->
        <main class="flex-1 overflow-y-auto p-4">
            <?= $content ?>
        </main>
    </div>
</div>
<?php
        return ob_get_clean();
    }
    
    /**
     * Affiche une mise en page simple sans barre de navigation ni pied de page (pour les pages de connexion/inscription)
     * 
     * @param string $title Titre de la page
     * @param string $content Contenu HTML principal
     * @param array $scripts Scripts additionnels à inclure
     * @return string La page HTML complète
     */
    public static function renderSimple($title, $content, $scripts = []) {
        return self::render($title, $content, [], $scripts, [], false, false);
    }
    
    /**
     * Affiche une page d'erreur
     * 
     * @param string $title Titre de la page
     * @param string $content Contenu HTML de l'erreur
     * @param array $user Données de l'utilisateur pour la barre de navigation
     * @return string La page HTML complète
     */
    public static function renderError($title, $content, $user = []) {
        $errorContent = "<div class='container mx-auto px-4 py-8'>$content</div>";
        return self::render($title, $errorContent, $user, [], [], true, true, 'bg-gray-50 min-h-screen flex flex-col');
    }
}
