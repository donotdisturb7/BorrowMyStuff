<?php

namespace App\View\Components\Common;

/**
 * Composant réutilisable pour afficher des badges d'état
 */
class BadgeView {
    /**
     * Types de badges disponibles
     */
    const TYPE_SUCCESS = 'success';
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_INFO = 'info';
    const TYPE_DEFAULT = 'default';
    
    /**
     * Rendre un badge
     * 
     * @param string $text Texte à afficher dans le badge
     * @param string $type Type de badge (success, error, warning, info, default)
     * @param bool $rounded Si le badge doit être arrondi
     * @return string HTML du badge
     */
    public static function render($text, $type = self::TYPE_DEFAULT, $rounded = true) {
        $bgColor = self::getBackgroundColor($type);
        $textColor = self::getTextColor($type);
        $roundedClass = $rounded ? 'rounded-full' : 'rounded';
        
        ob_start();
?>
<span class="inline-flex items-center px-2.5 py-0.5 <?= $roundedClass ?> text-xs font-medium <?= $bgColor ?> <?= $textColor ?>">
    <?= htmlspecialchars($text) ?>
</span>
<?php
        return ob_get_clean();
    }
    
    /**
     * Rendre un badge de succès
     * 
     * @param string $text Texte à afficher
     * @param bool $rounded Si le badge doit être arrondi
     * @return string HTML du badge
     */
    public static function success($text, $rounded = true) {
        return self::render($text, self::TYPE_SUCCESS, $rounded);
    }
    
    /**
     * Rendre un badge d'erreur
     * 
     * @param string $text Texte à afficher
     * @param bool $rounded Si le badge doit être arrondi
     * @return string HTML du badge
     */
    public static function error($text, $rounded = true) {
        return self::render($text, self::TYPE_ERROR, $rounded);
    }
    
    /**
     * Rendre un badge d'avertissement
     * 
     * @param string $text Texte à afficher
     * @param bool $rounded Si le badge doit être arrondi
     * @return string HTML du badge
     */
    public static function warning($text, $rounded = true) {
        return self::render($text, self::TYPE_WARNING, $rounded);
    }
    
    /**
     * Rendre un badge d'information
     * 
     * @param string $text Texte à afficher
     * @param bool $rounded Si le badge doit être arrondi
     * @return string HTML du badge
     */
    public static function info($text, $rounded = true) {
        return self::render($text, self::TYPE_INFO, $rounded);
    }
    
    /**
     * Obtenir la couleur de fond en fonction du type de badge
     * 
     * @param string $type Type de badge
     * @return string Classe CSS pour la couleur de fond
     */
    private static function getBackgroundColor($type) {
        switch ($type) {
            case self::TYPE_SUCCESS:
                return 'bg-green-100';
            case self::TYPE_ERROR:
                return 'bg-red-100';
            case self::TYPE_WARNING:
                return 'bg-yellow-100';
            case self::TYPE_INFO:
                return 'bg-blue-100';
            case self::TYPE_DEFAULT:
            default:
                return 'bg-gray-100';
        }
    }
    
    /**
     * Obtenir la couleur du texte en fonction du type de badge
     * 
     * @param string $type Type de badge
     * @return string Classe CSS pour la couleur du texte
     */
    private static function getTextColor($type) {
        switch ($type) {
            case self::TYPE_SUCCESS:
                return 'text-green-800';
            case self::TYPE_ERROR:
                return 'text-red-800';
            case self::TYPE_WARNING:
                return 'text-yellow-800';
            case self::TYPE_INFO:
                return 'text-blue-800';
            case self::TYPE_DEFAULT:
            default:
                return 'text-gray-800';
        }
    }
}
