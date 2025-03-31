<?php

namespace App\View\Components\Dashboard;

class DashboardSidebarView {
    /**
     * Render the dashboard sidebar
     * 
     * @param boolean $isAdmin Whether the user is an admin
     * @return string The HTML for the sidebar
     */
    public static function render($isAdmin = false) {
        ob_start();
?>
        <aside class="sidebar fixed inset-y-0 left-0 w-64 bg-black text-white overflow-y-auto transition-transform duration-300 z-40 transform -translate-x-full lg:translate-x-0">
            <div class="p-6 border-b border-white/10">
                <h2 class="text-2xl font-light">BorrowMyStuff</h2>
            </div>
            
            <nav class="mt-6 px-4">
                <div class="space-y-1">
                    <a href="/home" class="group flex items-center px-2 py-3 text-sm font-medium rounded-md hover:bg-white/10 transition-colors">
                        <span class="icon-home mr-3 text-white/70 group-hover:text-white"></span>
                        Page d'accueil
                    </a>
                    <a href="/dashboard" class="group flex items-center px-2 py-3 text-sm font-medium rounded-md hover:bg-white/10 transition-colors">
                        <span class="icon-dashboard mr-3 text-white/70 group-hover:text-white"></span>
                        Tableau de bord
                    </a>
                    <a href="/items" class="group flex items-center px-2 py-3 text-sm font-medium rounded-md hover:bg-white/10 transition-colors">
                        <span class="icon-inventory-2 mr-3 text-white/70 group-hover:text-white"></span>
                        Catalogue d'articles
                    </a>
                </div>
                
                <?php if ($isAdmin): ?>
                <div class="mt-8">
                    <h3 class="px-3 text-xs font-semibold text-white/50 uppercase tracking-wider">
                        Administration
                    </h3>
                    <div class="mt-2 space-y-1">
                        <a href="/users" class="group flex items-center px-2 py-3 text-sm font-medium rounded-md hover:bg-white/10 transition-colors">
                            <span class="icon-person mr-3 text-white/70 group-hover:text-white"></span>
                            Utilisateurs
                        </a>
                        <a href="/settings" class="group flex items-center px-2 py-3 text-sm font-medium rounded-md hover:bg-white/10 transition-colors">
                            <span class="fa fa-cog mr-3 text-white/70 group-hover:text-white"></span>
                            Paramètres
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </nav>
            
            <div class="mt-auto p-4 border-t border-white/10">
                <a href="/logout" class="group flex items-center px-2 py-3 text-sm font-medium rounded-md hover:bg-white/10 transition-colors">
                    <span class="icon-power-settings-new mr-3 text-white/70 group-hover:text-white"></span>
                    Déconnexion
                </a>
            </div>
        </aside>
<?php
        return ob_get_clean();
    }
}
