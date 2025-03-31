<?php
namespace App\View\Components\Common;

class NotificationView {
    /**
     * Helper function pour simplifier htmlspecialchars
     */
    private static function h($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Render a notification that appears in the top left corner
     * 
     * @param string $message The notification message
     * @param string $type The type of notification (success, error, warning, info)
     * @param int $duration Duration in milliseconds before auto-hiding (0 to disable)
     * @return string HTML for the notification
     */
    public static function render($message, $type = 'success', $duration = 5000) {
        // Determine icon and colors based on type
        $icon = match($type) {
            'success' => 'check_circle',
            'error' => 'error',
            'warning' => 'warning',
            'info' => 'info',
            default => 'info'
        };
        
        $bgColor = match($type) {
            'success' => 'bg-green-100 border-green-500',
            'error' => 'bg-red-100 border-red-500',
            'warning' => 'bg-yellow-100 border-yellow-500',
            'info' => 'bg-blue-100 border-blue-500',
            default => 'bg-blue-100 border-blue-500'
        };
        
        $textColor = match($type) {
            'success' => 'text-green-800',
            'error' => 'text-red-800',
            'warning' => 'text-yellow-800',
            'info' => 'text-blue-800',
            default => 'text-blue-800'
        };
        
        $iconColor = match($type) {
            'success' => 'text-green-500',
            'error' => 'text-red-500',
            'warning' => 'text-yellow-500',
            'info' => 'text-blue-500',
            default => 'text-blue-500'
        };
        
        $id = 'notification-' . uniqid();
        
        ob_start();
?>
<div id="<?= $id ?>" class="fixed top-4 right-4 z-50 flex items-center max-w-sm p-4 rounded-lg shadow <?= $bgColor ?> border-l-4 transform transition-transform duration-300 translate-x-0 opacity-100" role="alert">
    <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg <?= $iconColor ?>">
        <i class="fas <?= $icon ?>"></i>
    </div>
    <div class="ml-3 text-sm font-normal <?= $textColor ?>"><?= self::h($message) ?></div>
    <button type="button" class="ml-auto -mx-1.5 -my-1.5 rounded-lg p-1.5 inline-flex items-center justify-center h-8 w-8 <?= $textColor ?> hover:bg-gray-200" data-dismiss-target="#<?= $id ?>" aria-label="Close" onclick="closeNotification('<?= $id ?>')">
        <i class="fas fa-times"></i>
    </button>
    <script>
        function closeNotification(id) {
            const notification = document.getElementById(id);
            notification.classList.add("opacity-0", "translate-x-full");
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
        
        <?php if ($duration > 0): ?>
        setTimeout(() => closeNotification("<?= $id ?>"), <?= $duration ?>);
        <?php endif; ?>
    </script>
</div>
<?php
        return ob_get_clean();
    }
} 