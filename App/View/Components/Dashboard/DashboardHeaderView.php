<?php

namespace App\View\Components\Dashboard;

class DashboardHeaderView {
    /**
     * Helper function pour simplifier htmlspecialchars
     */
    private static function h($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Render the dashboard header
     * 
     * @param array $user User data
     * @return string The HTML for the header
     */
    public static function render($user) {
        ob_start();
?>
        <header class="fixed top-0 right-0 w-full lg:w-[calc(100%-16rem)] bg-white z-30 border-b border-gray-100">
            <div class="h-16 px-6 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="flex md:hidden">
                        <button id="mobileSidebarToggle" class="text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gray-500 mr-4">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <span id="currentDate" class="text-sm text-gray-600"></span>
                    </div>
                    
                    <div class="ml-4 relative flex-shrink-0">
                        <div>
                            <button id="userMenuButton" type="button" class="flex items-center space-x-3 text-sm rounded-md text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" aria-expanded="false" aria-haspopup="true">
                                <span class="text-sm font-medium"><?= self::h($user['username'] ?? 'Invité') ?></span>
                                <span class="icon-person text-gray-600"></span>
                                <span class="icon-arrow-drop-down text-gray-400"></span>
                            </button>
                        </div>
                        
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                            <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mon profil</a>
                            <a href="/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Paramètres</a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Déconnexion</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
<?php
        return ob_get_clean();
    }
}
