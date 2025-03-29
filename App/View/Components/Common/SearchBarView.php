<?php

namespace App\View\Components\Common;

class SearchBarView {
    /**
     * Render a search bar
     * 
     * @param string $currentQuery Current search query if any
     * @param string $placeholder Placeholder text
     * @param string $action Form action URL
     * @return string The HTML for the search bar
     */
    public static function render($currentQuery = '', $placeholder = 'Rechercher des objets...', $action = '/search') {
        ob_start();
?>
    <div class="px-4 sm:px-0 mb-8">
        <form action="<?= $action ?>" method="GET" class="max-w-3xl mx-auto">
            <div class="relative flex items-center">
                <input type="text" name="q" placeholder="<?= htmlspecialchars($placeholder) ?>" 
                       value="<?= htmlspecialchars($currentQuery) ?>"
                       class="block w-full pl-5 pr-12 py-3 border-2 border-black bg-white placeholder-gray-500 focus:outline-none focus:ring-0 text-base rounded-md">
                <button type="submit" class="absolute right-0 top-0 bottom-0 px-4 flex items-center justify-center bg-black text-white rounded-r-md hover:bg-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <span class="ml-2 hidden sm:inline-block">Rechercher</span>
                </button>
            </div>
        </form>
    </div>
<?php
        return ob_get_clean();
    }
}
