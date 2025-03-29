<?php
namespace App\View\Components\Item;

class ItemLoanRequestModal {
    /**
     * Render the loan request modal
     * 
     * @param array $item Item details
     * @param string $modalId Unique ID for the modal
     * @return string HTML content
     */
    public static function render($item, $modalId = 'loanRequestModal') {
        ob_start();
        ?>
        <!-- Item Loan Request Modal -->
        <div id="<?= $modalId ?>" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden overflow-y-auto flex justify-center items-center">
            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-2xl m-4">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-xl font-semibold text-gray-900">
                        <?= htmlspecialchars($item['name']) ?>
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center" onclick="closeModal('<?= $modalId ?>')">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal body -->
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="/<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="rounded-lg w-full object-cover max-h-64">
                            <?php else: ?>
                                <div class="bg-gray-200 rounded-lg w-full h-64 flex items-center justify-center">
                                    <span class="material-icons-outlined text-gray-400 text-5xl">image_not_supported</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">Détails de l'objet</h4>
                            
                            <div class="mb-3">
                                <p class="text-sm text-gray-500 mb-1">Catégorie</p>
                                <p class="text-sm font-medium"><?= htmlspecialchars($item['category'] ?? 'Non spécifié') ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <p class="text-sm text-gray-500 mb-1">Propriétaire</p>
                                <p class="text-sm font-medium"><?= htmlspecialchars($item['owner_name'] ?? 'Non spécifié') ?></p>
                            </div>
                            
                            <div class="mb-3">
                                <p class="text-sm text-gray-500 mb-1">Disponibilité</p>
                                <p class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $item['available'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= $item['available'] ? 'Disponible' : 'Indisponible' ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">Description</h4>
                        <p class="text-gray-600 whitespace-pre-line"><?= htmlspecialchars($item['description']) ?></p>
                    </div>
                    
                    <?php if ($item['available']): ?>
                        <div class="border-t pt-4">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Demander un prêt</h4>
                            <form action="/loans/request" method="POST" id="loanRequestForm">
                                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Date de début *</label>
                                        <input type="date" id="start_date" name="start_date" required 
                                               min="<?= date('Y-m-d') ?>"
                                               class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-black focus:border-black">
                                    </div>
                                    <div>
                                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de fin *</label>
                                        <input type="date" id="end_date" name="end_date" required 
                                               min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                               class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-black focus:border-black">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message (optionnel)</label>
                                    <textarea id="message" name="message" rows="3" 
                                              class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:outline-none focus:ring-black focus:border-black"
                                              placeholder="Expliquez pourquoi vous souhaitez emprunter cet objet..."></textarea>
                                </div>
                                
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-black text-white py-2 px-4 rounded-md hover:bg-gray-800 transition-colors">
                                        Demander le prêt
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="mt-4 p-4 bg-gray-100 rounded-md">
                            <p class="text-gray-700">Cet objet n'est pas disponible actuellement pour un prêt.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <script>
            function openModal(modalId) {
                document.getElementById(modalId).classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
            
            function closeModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
            
            document.addEventListener('DOMContentLoaded', function() {
                // Validate end date is after start date
                const startDateInput = document.getElementById('start_date');
                const endDateInput = document.getElementById('end_date');
                
                if (startDateInput && endDateInput) {
                    startDateInput.addEventListener('change', function() {
                        // Set minimum end date to day after start date
                        const startDate = new Date(this.value);
                        const minEndDate = new Date(startDate);
                        minEndDate.setDate(startDate.getDate() + 1);
                        
                        endDateInput.min = minEndDate.toISOString().split('T')[0];
                        
                        // If current end date is before new start date, reset it
                        if (new Date(endDateInput.value) <= startDate) {
                            endDateInput.value = minEndDate.toISOString().split('T')[0];
                        }
                    });
                }
            });
        </script>
        <?php
        return ob_get_clean();
    }
} 