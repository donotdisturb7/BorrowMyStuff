<?php

namespace App\View\Components\Common;

class PaginationView {
    /**
     * Render pagination controls
     * 
     * @param array $pagination Pagination data (currentPage, totalPages, etc.)
     * @param string $baseUrl Base URL for pagination links (without query params)
     * @param array $queryParams Additional query parameters to preserve
     * @return string The HTML for the pagination controls
     */
    public static function render($pagination = [], $baseUrl = '/', $queryParams = []) {
        if (empty($pagination) || $pagination['totalPages'] <= 1) {
            return '';
        }
        
        $currentPage = $pagination['currentPage'] ?? 1;
        $totalPages = $pagination['totalPages'] ?? 1;
        
        // Build query string for additional parameters
        $queryString = '';
        if (!empty($queryParams)) {
            foreach ($queryParams as $key => $value) {
                if ($key !== 'page') { // Skip page parameter as we'll add it separately
                    $queryString .= '&' . urlencode($key) . '=' . urlencode($value);
                }
            }
        }
        
        ob_start();
?>
    <div class="mt-8 flex justify-center">
        <nav class="inline-flex shadow-sm">
            <?php if ($currentPage > 1): ?>
            <a href="<?= $baseUrl . '?page=' . ($currentPage - 1) . $queryString ?>" class="px-4 py-2 bg-white border border-gray-200 text-sm font-medium text-black hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
            <?php endif; ?>
            
            <?php 
            // Calculate range of pages to show
            $range = 2; // Show 2 pages before and after current page
            $startPage = max(1, $currentPage - $range);
            $endPage = min($totalPages, $currentPage + $range);
            
            // Always show first page
            if ($startPage > 1): ?>
            <a href="<?= $baseUrl . '?page=1' . $queryString ?>" class="px-4 py-2 bg-white border border-gray-200 text-sm font-medium text-black hover:bg-gray-50">
                1
            </a>
            <?php if ($startPage > 2): ?>
            <span class="px-4 py-2 bg-white border border-gray-200 text-sm font-medium text-gray-500">
                ...
            </span>
            <?php endif; 
            endif;
            
            // Show page numbers
            for ($i = $startPage; $i <= $endPage; $i++): ?>
            <a href="<?= $baseUrl . '?page=' . $i . $queryString ?>" class="px-4 py-2 bg-white border border-gray-200 text-sm font-medium <?= $i === $currentPage ? 'bg-black text-white' : 'text-black hover:bg-gray-50' ?>">
                <?= $i ?>
            </a>
            <?php endfor; 
            
            // Always show last page
            if ($endPage < $totalPages): 
            if ($endPage < $totalPages - 1): ?>
            <span class="px-4 py-2 bg-white border border-gray-200 text-sm font-medium text-gray-500">
                ...
            </span>
            <?php endif; ?>
            <a href="<?= $baseUrl . '?page=' . $totalPages . $queryString ?>" class="px-4 py-2 bg-white border border-gray-200 text-sm font-medium text-black hover:bg-gray-50">
                <?= $totalPages ?>
            </a>
            <?php endif; ?>
            
            <?php if ($currentPage < $totalPages): ?>
            <a href="<?= $baseUrl . '?page=' . ($currentPage + 1) . $queryString ?>" class="px-4 py-2 bg-white border border-gray-200 text-sm font-medium text-black hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
            <?php endif; ?>
        </nav>
    </div>
<?php
        return ob_get_clean();
    }
}
