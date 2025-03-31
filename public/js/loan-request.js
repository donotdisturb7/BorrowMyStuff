/**
 * Fonctions pour le modal de demande de prêt
 */

// Fonction pour ouvrir le modal
function openLoanRequestModal(itemId, itemName) {
    document.getElementById("loanRequestModal").style.display = "block";
    document.getElementById("itemId").value = itemId;
    document.getElementById("modalItemName").textContent = "Item: " + itemName;
    
    // Définir la date minimale à aujourd'hui
    const today = new Date().toISOString().split("T")[0];
    document.getElementById("startDate").min = today;
    document.getElementById("endDate").min = today;
    
    // Par défaut, définir la date de début à aujourd'hui
    document.getElementById("startDate").value = today;
    
    // Ajouter un événement pour s'assurer que la date de fin est après la date de début
    document.getElementById("startDate").addEventListener("change", function() {
        const startDate = this.value;
        document.getElementById("endDate").min = startDate;
        
        // Si la date de fin est avant la nouvelle date de début, la mettre à jour
        if (document.getElementById("endDate").value < startDate) {
            document.getElementById("endDate").value = startDate;
        }
    });
}

// Fonction pour fermer le modal
function closeLoanRequestModal() {
    document.getElementById("loanRequestModal").style.display = "none";
}

// Initialisation quand le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    // Fermer le modal si l'utilisateur clique en dehors
    window.onclick = function(event) {
        const modal = document.getElementById("loanRequestModal");
        if (event.target == modal) {
            closeLoanRequestModal();
        }
    }
    
    // Validation du formulaire avant soumission
    const form = document.getElementById("loanRequestForm");
    if (form) {
        form.addEventListener("submit", function(e) {
            const startDate = document.getElementById("startDate").value;
            const endDate = document.getElementById("endDate").value;
            
            if (startDate > endDate) {
                e.preventDefault();
                alert("La date de fin doit être après la date de début.");
            }
        });
    }
}); 