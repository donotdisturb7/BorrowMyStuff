<?php

namespace App\View\Error;

use App\View\Components\Layout\LayoutView;

class NotFoundView {
    /**
     * Render the 404 not found page
     * 
     * @return string The HTML for the 404 page
     */
    public static function render() {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Get user data from session
        $user = [];
        if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
            $user = [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role'],
                'isAuthenticated' => true
            ];
        } else {
            $user = ['isAuthenticated' => false];
        }

        // Générer le contenu de la page 404
        $content = self::getErrorContent();
        
        // Utiliser le LayoutView pour générer la page complète avec les données utilisateur
        return LayoutView::renderError('Page non trouvée', $content, $user);
    }
    
    /**
     * Générer le contenu HTML de la page d'erreur 404
     * 
     * @return string Le contenu HTML
     */
    private static function getErrorContent() {
        ob_start();
?>
<div class="flex-1 flex flex-col items-center justify-center p-4 fade-in">
    <div class="text-center max-w-2xl mx-auto">
        <h1 class="text-9xl font-light mb-4">404</h1>
        <p class="text-2xl font-light mb-8">Page non trouvée</p>
        
        <div class="mb-8 text-gray-500">
            <p id="currentDate" class="text-lg"></p>
        </div>
        
        <div class="border-t border-gray-200 pt-8 mt-8">
            <p class="mb-6">La page que vous recherchez n'existe pas ou a été déplacée.</p>
            <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true): ?>
                <a href="/home" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md hover:bg-gray-100 transition-colors">
            <?php else: ?>
                <a href="/" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md hover:bg-gray-100 transition-colors">
            <?php endif; ?>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Retour à l'accueil
            </a>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format current date
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
        const formattedDate = now.toLocaleDateString('fr-FR', options);
        const dateElement = document.getElementById('currentDate');
        if (dateElement) {
            dateElement.textContent = formattedDate;
        }
    });
</script>
<?php
        return ob_get_clean();
    }
}
