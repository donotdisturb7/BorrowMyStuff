<?php
namespace App\View\Item;

class ItemFormTabView {
    /**
     * Affiche le formulaire de création ou de modification d'un objet dans un onglet
     */
    public static function render($item = null, $errors = [], $categories = []) {
        $isEdit = !empty($item) && isset($item['id']);
        $title = $isEdit ? 'Modifier l\'objet : ' . htmlspecialchars($item['name']) : 'Ajouter un nouvel objet';
        $action = $isEdit ? '/items/' . $item['id'] . '/update' : '/items/store';
        
        $html = '<div class="bg-white shadow-sm rounded-lg p-6">
                    <h2 class="text-2xl font-semibold text-gray-900 mb-6">' . $title . '</h2>';
        
        // Affichage des erreurs
        if (!empty($errors)) {
            $html .= '<div class="bg-red-50 border border-red-200 text-red-700 p-4 mb-6 rounded-lg">';
            foreach ($errors as $error) {
                $html .= '<p class="flex items-center"><i class="fas fa-exclamation-circle mr-2"></i>' . htmlspecialchars($error) . '</p>';
            }
            $html .= '</div>';
        }
        
        $html .= '<form action="' . $action . '" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom de l\'objet *</label>
                            <input type="text" id="name" name="name" value="' . htmlspecialchars($item['name'] ?? '') . '" required 
                                   class="w-full border border-gray-300 px-3 py-2 rounded-md focus:ring-2 focus:ring-black focus:border-black">
                        </div>
                        
                        <div class="mb-4">
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Catégorie *</label>
                            <select id="category" name="category" required 
                                    class="w-full border border-gray-300 px-3 py-2 rounded-md focus:ring-2 focus:ring-black focus:border-black">
                                <option value="">Sélectionnez une catégorie</option>';
                                foreach ($categories as $cat) {
                                    $selected = (isset($item['category']) && $item['category'] === $cat) ? ' selected' : '';
                                    $html .= '<option value="' . htmlspecialchars($cat) . '"' . $selected . '>' . htmlspecialchars($cat) . '</option>';
                                }
        $html .= '</select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                        <textarea id="description" name="description" rows="4" required 
                                  class="w-full border border-gray-300 px-3 py-2 rounded-md focus:ring-2 focus:ring-black focus:border-black">' . 
                                  htmlspecialchars($item['description'] ?? '') . '</textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="mb-4">
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image</label>';
                            
        if ($isEdit && !empty($item['image'])) {
            $html .= '<div class="mb-3 p-3 bg-gray-50 inline-block rounded-lg">
                            <img src="/' . htmlspecialchars($item['image']) . '" alt="Image actuelle" class="max-w-[200px] rounded">
                            <p class="text-sm text-gray-600 mt-2"><i class="fas fa-info-circle mr-1"></i>Image actuelle</p>
                        </div>';
        }
                            
        $html .= '<input type="file" id="image" name="image" accept="image/*" 
                         class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 
                                file:border-0 file:text-sm file:bg-black file:text-white hover:file:bg-gray-800 
                                file:rounded-md cursor-pointer">
                        </div>
                        
                        <div class="flex items-center mb-4">
                            <input type="checkbox" id="available" name="available" value="1" ' . 
                            (($isEdit && !$item['available']) ? '' : 'checked') . ' 
                            class="h-4 w-4 border-gray-300 text-black focus:ring-black rounded">
                            <label for="available" class="ml-2 block text-sm text-gray-700">Disponible pour le prêt</label>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                        <button type="submit" class="px-6 py-2 bg-black text-white hover:bg-gray-800 
                                                  transition-colors rounded-md flex items-center">
                            <i class="fas fa-' . ($isEdit ? 'save' : 'plus') . ' mr-2"></i>' . 
                            ($isEdit ? 'Mettre à jour' : 'Ajouter l\'objet') . '
                        </button>
                        ' . ($isEdit ? '<a href="/dashboard" 
                           class="px-6 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 
                                  transition-colors rounded-md flex items-center">
                            <i class="fas fa-times mr-2"></i>Annuler
                        </a>' : '') . '
                    </div>
                </form>
            </div>';
        
        return $html;
    }
} 