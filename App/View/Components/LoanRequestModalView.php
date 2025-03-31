<?php

namespace App\View\Components;

/**
 * Composant pour le modal de demande de prêt
 */
class LoanRequestModalView {
    /**
     * Rendre le modal de demande de prêt
     * 
     * @return string HTML du modal
     */
    public static function render() {
        ob_start();
?>
<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 50;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
        background-color: white;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 90%;
        max-width: 500px;
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
    }
</style>

<!-- Modal de demande de prêt -->
<div id="loanRequestModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeLoanRequestModal()">&times;</span>
        <h2 class="text-xl font-medium mb-4">Demande de prêt</h2>
        <p id="modalItemName" class="mb-4"></p>
        
        <form id="loanRequestForm" action="/loans/request" method="POST" class="space-y-4">
            <input type="hidden" id="itemId" name="item_id">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
            
            <div>
                <label for="startDate" class="block text-sm font-medium text-gray-700">Date de début</label>
                <input type="date" id="startDate" name="start_date" required 
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-black focus:border-black sm:text-sm">
            </div>
            
            <div>
                <label for="endDate" class="block text-sm font-medium text-gray-700">Date de fin</label>
                <input type="date" id="endDate" name="end_date" required 
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-black focus:border-black sm:text-sm">
            </div>
            
            <div class="flex justify-end">
                <button type="button" onclick="closeLoanRequestModal()" class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none">
                    Annuler
                </button>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-black hover:bg-gray-800 focus:outline-none">
                    Envoyer la demande
                </button>
            </div>
        </form>
    </div>
</div>

<script>
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
    
    // Fermer le modal si l'utilisateur clique en dehors
    window.onclick = function(event) {
        const modal = document.getElementById("loanRequestModal");
        if (event.target == modal) {
            closeLoanRequestModal();
        }
    }
    
    // Validation du formulaire avant soumission
    document.getElementById("loanRequestForm").addEventListener("submit", function(e) {
        const startDate = document.getElementById("startDate").value;
        const endDate = document.getElementById("endDate").value;
        
        if (startDate > endDate) {
            e.preventDefault();
            alert("La date de fin doit être après la date de début.");
        }
    });
</script>
<?php
        return ob_get_clean();
    }
} 