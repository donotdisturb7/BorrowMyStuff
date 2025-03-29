<?php

namespace App\View\Components\Layout;

class NavbarView {
    /**
     * Affiche la barre de navigation
     * 
     * @param array $user Données de l'utilisateur (isAuthenticated, username, role)
     * @return string Le HTML de la barre de navigation
     */
    public static function render($user = []) {
        $isAuthenticated = $user['isAuthenticated'] ?? false;
        $username = $user['username'] ?? '';
        $isAdmin = ($user['role'] ?? '') === 'admin';
        
        ob_start();
?>
    <!-- Navigation - Modern fixed version -->
    <header class="fixed inset-x-0 top-0 z-30 mx-auto w-full max-w-screen-md border border-gray-100 bg-white/80 py-4 shadow backdrop-blur-lg md:top-6 md:rounded-3xl lg:max-w-screen-lg">
        <div class="px-6">
            <div class="flex items-center justify-between">
                <div class="flex shrink-0">
                    <a aria-current="page" class="flex items-center" href="<?= $isAuthenticated ? '/home' : '/' ?>">
                        <svg class="h-8 w-auto text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="ml-3 text-xl font-medium text-black">BorrowMyStuff</span>
                    </a>
                </div>
                <div class="hidden md:flex md:items-center md:justify-center md:gap-6">
                    <a class="inline-block rounded-lg px-3 py-2 text-base font-medium text-gray-900 transition-all duration-200 hover:bg-gray-100 hover:text-gray-900" href="<?= $isAuthenticated ? '/home' : '/' ?>">Accueil</a>
                    <?php if ($isAuthenticated): ?>
                    <a class="inline-block rounded-lg px-3 py-2 text-base font-medium text-gray-900 transition-all duration-200 hover:bg-gray-100 hover:text-gray-900" href="/dashboard">Tableau de bord</a>
                    <?php endif; ?>
                    <a class="inline-block rounded-lg px-3 py-2 text-base font-medium text-gray-900 transition-all duration-200 hover:bg-gray-100 hover:text-gray-900" href="/about">À propos</a>
                </div>
                <div class="flex items-center justify-end gap-4">
                    <?php if ($isAuthenticated): ?>
                        <span class="hidden md:inline-block text-base font-medium text-gray-700">Bonjour, <?= htmlspecialchars($username) ?></span>
                        <a class="inline-flex items-center justify-center rounded-xl bg-black px-4 py-2.5 text-base font-semibold text-white shadow-sm transition-all duration-150 hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black" href="/logout">Déconnexion</a>
                    <?php else: ?>
                        <a class="hidden items-center justify-center rounded-xl bg-white px-4 py-2.5 text-base font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 transition-all duration-150 hover:bg-gray-50 sm:inline-flex" href="/login">Se connecter</a>
                        <a class="inline-flex items-center justify-center rounded-xl bg-black px-4 py-2.5 text-base font-semibold text-white shadow-sm transition-all duration-150 hover:bg-gray-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black" href="/register">S'inscrire</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <!-- Spacer to compensate for fixed navbar -->
    <div class="h-20 md:h-32"></div>
<?php
        return ob_get_clean();
    }
}
