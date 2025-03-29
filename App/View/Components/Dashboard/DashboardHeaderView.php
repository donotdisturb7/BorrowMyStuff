<?php

namespace App\View\Components\Dashboard;

class DashboardHeaderView {
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
                    <button class="lg:hidden text-black focus:outline-none" id="mobile-menu-button">
                        <span class="material-icons-outlined">menu</span>
                    </button>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <span id="currentDate" class="text-sm text-gray-600"></span>
                    </div>
                    
                    <div class="relative group">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                <span class="material-icons-outlined text-gray-600">person</span>
                            </div>
                            <span class="text-sm font-medium text-gray-700 hidden md:block"><?= htmlspecialchars($user['username']) ?></span>
                            <span class="material-icons-outlined text-gray-400">arrow_drop_down</span>
                        </button>
                        
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
