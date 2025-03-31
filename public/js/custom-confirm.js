/**
 * Système de confirmation personnalisé
 */

// Fonction pour créer et afficher une modale de confirmation
function showCustomConfirm(message, onConfirm, onCancel) {
    // Créer l'overlay de fond
    const overlay = document.createElement('div');
    overlay.classList.add('fixed', 'inset-0', 'bg-black', 'bg-opacity-50', 'z-50', 'flex', 'items-center', 'justify-center');
    
    // Créer la boîte de dialogue
    const modal = document.createElement('div');
    modal.classList.add('bg-white', 'rounded-lg', 'p-6', 'max-w-md', 'mx-4', 'shadow-xl', 'transform', 'transition-all', 'duration-300', 'scale-95', 'opacity-0');
    
    // Contenu de la modale
    modal.innerHTML = `
        <div class="text-center">
            <svg class="mx-auto mb-4 w-14 h-14 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mb-5 text-lg font-medium text-gray-900">${message}</h3>
            <div class="flex justify-center gap-4">
                <button type="button" id="confirm-cancel-btn" class="py-2 px-4 bg-gray-200 text-gray-900 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Annuler
                </button>
                <button type="button" id="confirm-ok-btn" class="py-2 px-4 bg-black text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Confirmer
                </button>
            </div>
        </div>
    `;
    
    // Ajouter la modale à l'overlay
    overlay.appendChild(modal);
    
    // Ajouter l'overlay au body
    document.body.appendChild(overlay);
    
    // Animation d'entrée après un court délai pour permettre au DOM de se mettre à jour
    setTimeout(() => {
        modal.classList.remove('scale-95', 'opacity-0');
        modal.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    // Gérer les clics sur les boutons
    const confirmBtn = modal.querySelector('#confirm-ok-btn');
    const cancelBtn = modal.querySelector('#confirm-cancel-btn');
    
    confirmBtn.addEventListener('click', () => {
        closeModal();
        if (onConfirm) onConfirm();
    });
    
    cancelBtn.addEventListener('click', () => {
        closeModal();
        if (onCancel) onCancel();
    });
    
    // Fermer la modale
    function closeModal() {
        modal.classList.remove('scale-100', 'opacity-100');
        modal.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            document.body.removeChild(overlay);
        }, 300);
    }
    
    // Retourner la référence à la modale
    return modal;
}

// Fonction pour créer une alerte personnalisée
function showCustomAlert(message, onClose) {
    // Créer l'overlay de fond
    const overlay = document.createElement('div');
    overlay.classList.add('fixed', 'inset-0', 'bg-black', 'bg-opacity-50', 'z-50', 'flex', 'items-center', 'justify-center');
    
    // Créer la boîte de dialogue
    const modal = document.createElement('div');
    modal.classList.add('bg-white', 'rounded-lg', 'p-6', 'max-w-md', 'mx-4', 'shadow-xl', 'transform', 'transition-all', 'duration-300', 'scale-95', 'opacity-0');
    
    // Contenu de la modale
    modal.innerHTML = `
        <div class="text-center">
            <svg class="mx-auto mb-4 w-14 h-14 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mb-5 text-lg font-medium text-gray-900">${message}</h3>
            <button type="button" id="alert-ok-btn" class="py-2 px-4 bg-black text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500">
                OK
            </button>
        </div>
    `;
    
    // Ajouter la modale à l'overlay
    overlay.appendChild(modal);
    
    // Ajouter l'overlay au body
    document.body.appendChild(overlay);
    
    // Animation d'entrée après un court délai pour permettre au DOM de se mettre à jour
    setTimeout(() => {
        modal.classList.remove('scale-95', 'opacity-0');
        modal.classList.add('scale-100', 'opacity-100');
    }, 10);
    
    // Gérer le clic sur le bouton OK
    const okBtn = modal.querySelector('#alert-ok-btn');
    
    okBtn.addEventListener('click', () => {
        closeModal();
        if (onClose) onClose();
    });
    
    // Fermer la modale
    function closeModal() {
        modal.classList.remove('scale-100', 'opacity-100');
        modal.classList.add('scale-95', 'opacity-0');
        
        setTimeout(() => {
            document.body.removeChild(overlay);
        }, 300);
    }
    
    // Retourner la référence à la modale
    return modal;
}

// Remplacer window.alert
window.alert = function(message) {
    showCustomAlert(message);
};

// Remplacer le comportement des formulaires avec onsubmit="return confirm(...)"
document.addEventListener('DOMContentLoaded', () => {
    // Trouver tous les formulaires utilisant confirm dans onsubmit
    const formsWithConfirm = document.querySelectorAll('form[onsubmit*="confirm"]');
    
    formsWithConfirm.forEach(form => {
        // Supprimer l'attribut onsubmit
        const confirmMessage = form.getAttribute('onsubmit').match(/confirm\(['"](.*)['"]\)/)?.[1] || "Êtes-vous sûr?";
        form.removeAttribute('onsubmit');
        
        // Ajouter un nouvel écouteur d'événements
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            showCustomConfirm(confirmMessage, () => {
                // Préserver la page actuelle lors de la soumission du formulaire
                const currentPage = new URLSearchParams(window.location.search).get('page');
                if (currentPage) {
                    // Ajouter un champ caché pour la page
                    const pageInput = document.createElement('input');
                    pageInput.type = 'hidden';
                    pageInput.name = 'current_page';
                    pageInput.value = currentPage;
                    form.appendChild(pageInput);
                }
                form.submit();
            });
        });
    });
    
    // Trouver tous les boutons avec data-confirm
    document.querySelectorAll('[data-confirm]').forEach(element => {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            
            const confirmMessage = element.getAttribute('data-confirm');
            const action = element.getAttribute('data-action') || element.getAttribute('href');
            
            showCustomConfirm(confirmMessage, () => {
                if (element.tagName === 'A') {
                    // Pour les liens, ajouter le paramètre de page à l'URL
                    let url = new URL(action, window.location.origin);
                    const currentPage = new URLSearchParams(window.location.search).get('page');
                    if (currentPage) {
                        url.searchParams.set('current_page', currentPage);
                    }
                    window.location.href = url.toString();
                } else if (element.form) {
                    // Pour les boutons dans un formulaire, ajouter un champ caché pour la page
                    const currentPage = new URLSearchParams(window.location.search).get('page');
                    if (currentPage) {
                        const pageInput = document.createElement('input');
                        pageInput.type = 'hidden';
                        pageInput.name = 'current_page';
                        pageInput.value = currentPage;
                        element.form.appendChild(pageInput);
                    }
                    element.form.submit();
                }
            });
        });
    });
}); 