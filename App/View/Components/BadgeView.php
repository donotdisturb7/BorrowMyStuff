<?php
namespace App\View\Components;

class BadgeView {
    /**
     * Helper function pour simplifier htmlspecialchars
     */
    private static function h($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * Render a badge
     * 
     * @param string $text Badge text
     * @param string $type Type of badge (default, success, warning, danger)
     * @return string HTML for the badge
     */
    public static function render($text, $type = 'default') {
        $colors = [
            'default' => 'bg-gray-100 text-gray-800',
            'success' => 'bg-green-100 text-green-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'danger' => 'bg-red-100 text-red-800',
            'info' => 'bg-blue-100 text-blue-800'
        ];
        
        $colorClass = $colors[$type] ?? $colors['default'];
        
        return sprintf(
            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium %s">%s</span>',
            $colorClass,
            self::h($text)
        );
    }
}