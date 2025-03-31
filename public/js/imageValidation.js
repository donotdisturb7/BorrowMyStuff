/**
 * Validation côté client pour les téléchargements d'images
 */
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    
    if (imageInput) {
        // Ajoute un écouteur d'événement sur le changement de fichier
        imageInput.addEventListener('change', function() {
            const maxSize = 2 * 1024 * 1024; // 2MB
            const fileSize = this.files[0]?.size || 0;
            const fileType = this.files[0]?.type || '';
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            // Vérifie le type de fichier
            if (this.files.length > 0 && !allowedTypes.includes(fileType)) {
                alert('Le fichier doit être une image (JPEG, PNG, GIF ou WEBP).');
                this.value = ''; // Vide l'input
                const preview = document.getElementById('image-preview');
                if (preview) preview.classList.add('hidden');
                return;
            }
            
            // Vérifie la taille du fichier
            if (this.files.length > 0 && fileSize > maxSize) {
                alert('L\'image ne doit pas dépasser 2 Mo.');
                this.value = ''; // Vide l'input
                const preview = document.getElementById('image-preview');
                if (preview) preview.classList.add('hidden');
                return;
            }
            
            // Afficher la taille du fichier
            if (this.files.length > 0) {
                const fileSizeInfo = document.getElementById('file-size-info');
                if (fileSizeInfo) {
                    const sizeMB = (fileSize / (1024 * 1024)).toFixed(2);
                    fileSizeInfo.textContent = `Taille du fichier: ${sizeMB} Mo`;
                    fileSizeInfo.classList.remove('hidden');
                    
                    // Colorer selon la taille
                    if (fileSize > maxSize * 0.8) {
                        fileSizeInfo.classList.add('text-yellow-600');
                        fileSizeInfo.classList.remove('text-green-600');
                    } else {
                        fileSizeInfo.classList.add('text-green-600');
                        fileSizeInfo.classList.remove('text-yellow-600');
                    }
                }
            }
        });
    }
}); 