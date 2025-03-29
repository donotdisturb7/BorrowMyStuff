<?php

namespace App\View\Components\Common;

/**
 * Composant réutilisable pour afficher des alertes et notifications
 */
class AlertView {
    /**
     * Types d'alertes disponibles
     */
    const TYPE_SUCCESS = 'success';
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_INFO = 'info';
    
    /**
     * Rendre une alerte
     * 
     * @param string $message Message à afficher
     * @param string $type Type d'alerte (success, error, warning, info)
     * @param bool $dismissible Si l'alerte peut être fermée
     * @return string HTML de l'alerte
     */
    public static function render($message, $type = self::TYPE_INFO, $dismissible = true) {
        $bgColor = self::getBackgroundColor($type);
        $textColor = self::getTextColor($type);
        $borderColor = self::getBorderColor($type);
        $icon = self::getIcon($type);
        
        ob_start();
?>
<div class="rounded-md <?= $bgColor ?> p-4 mb-4 border-l-4 <?= $borderColor ?>" role="alert">
    <div class="flex">
        <div class="flex-shrink-0">
            <?= $icon ?>
        </div>
        <div class="ml-3 flex-1">
            <p class="text-sm <?= $textColor ?>">
                <?= htmlspecialchars($message) ?>
            </p>
        </div>
        <?php if ($dismissible): ?>
        <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
                <button type="button" class="inline-flex rounded-md p-1.5 <?= $textColor ?> hover:bg-white hover:bg-opacity-10 focus:outline-none" onclick="this.parentNode.parentNode.parentNode.remove()">
                    <span class="sr-only">Fermer</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php
        return ob_get_clean();
    }
    
    /**
     * Rendre une alerte de succès
     * 
     * @param string $message Message à afficher
     * @param bool $dismissible Si l'alerte peut être fermée
     * @return string HTML de l'alerte
     */
    public static function success($message, $dismissible = true) {
        return self::render($message, self::TYPE_SUCCESS, $dismissible);
    }
    
    /**
     * Rendre une alerte d'erreur
     * 
     * @param string $message Message à afficher
     * @param bool $dismissible Si l'alerte peut être fermée
     * @return string HTML de l'alerte
     */
    public static function error($message, $dismissible = true) {
        return self::render($message, self::TYPE_ERROR, $dismissible);
    }
    
    /**
     * Rendre une alerte d'avertissement
     * 
     * @param string $message Message à afficher
     * @param bool $dismissible Si l'alerte peut être fermée
     * @return string HTML de l'alerte
     */
    public static function warning($message, $dismissible = true) {
        return self::render($message, self::TYPE_WARNING, $dismissible);
    }
    
    /**
     * Rendre une alerte d'information
     * 
     * @param string $message Message à afficher
     * @param bool $dismissible Si l'alerte peut être fermée
     * @return string HTML de l'alerte
     */
    public static function info($message, $dismissible = true) {
        return self::render($message, self::TYPE_INFO, $dismissible);
    }
    
    /**
     * Obtenir la couleur de fond en fonction du type d'alerte
     * 
     * @param string $type Type d'alerte
     * @return string Classe CSS pour la couleur de fond
     */
    private static function getBackgroundColor($type) {
        switch ($type) {
            case self::TYPE_SUCCESS:
                return 'bg-green-50';
            case self::TYPE_ERROR:
                return 'bg-red-50';
            case self::TYPE_WARNING:
                return 'bg-yellow-50';
            case self::TYPE_INFO:
            default:
                return 'bg-blue-50';
        }
    }
    
    /**
     * Obtenir la couleur du texte en fonction du type d'alerte
     * 
     * @param string $type Type d'alerte
     * @return string Classe CSS pour la couleur du texte
     */
    private static function getTextColor($type) {
        switch ($type) {
            case self::TYPE_SUCCESS:
                return 'text-green-700';
            case self::TYPE_ERROR:
                return 'text-red-700';
            case self::TYPE_WARNING:
                return 'text-yellow-700';
            case self::TYPE_INFO:
            default:
                return 'text-blue-700';
        }
    }
    
    /**
     * Obtenir la couleur de la bordure en fonction du type d'alerte
     * 
     * @param string $type Type d'alerte
     * @return string Classe CSS pour la couleur de la bordure
     */
    private static function getBorderColor($type) {
        switch ($type) {
            case self::TYPE_SUCCESS:
                return 'border-green-400';
            case self::TYPE_ERROR:
                return 'border-red-400';
            case self::TYPE_WARNING:
                return 'border-yellow-400';
            case self::TYPE_INFO:
            default:
                return 'border-blue-400';
        }
    }
    
    /**
     * Obtenir l'icône en fonction du type d'alerte
     * 
     * @param string $type Type d'alerte
     * @return string HTML de l'icône
     */
    private static function getIcon($type) {
        switch ($type) {
            case self::TYPE_SUCCESS:
                return '<svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>';
            case self::TYPE_ERROR:
                return '<svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>';
            case self::TYPE_WARNING:
                return '<svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>';
            case self::TYPE_INFO:
            default:
                return '<svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>';
        }
    }
}
