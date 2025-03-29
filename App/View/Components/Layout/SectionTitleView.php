<?php

namespace App\View\Components\Layout;

class SectionTitleView {
    /**
     * Affiche le titre d'une section
     * 
     * @param string $title Le texte du titre
     * @param array $actionButton Bouton d'action optionnel (text, url, classes)
     * @return string Le HTML du titre de section
     */
    public static function render($title, $actionButton = null) {
        ob_start();
?>
    <div class="px-4 py-6 sm:px-0 mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-light text-black">
            <?= htmlspecialchars($title) ?>
        </h2>
        <?php if ($actionButton): ?>
        <a href="<?= htmlspecialchars($actionButton['url']) ?>" class="<?= $actionButton['classes'] ?? 'px-4 py-2 border border-black text-sm font-light text-white bg-black hover:bg-gray-800 focus:outline-none rounded-md' ?>">
            <?= htmlspecialchars($actionButton['text']) ?>
        </a>
        <?php endif; ?>
    </div>
<?php
        return ob_get_clean();
    }
}
