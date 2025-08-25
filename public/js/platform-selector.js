// Modal de selección de plataformas
document.addEventListener('DOMContentLoaded', function() {
    const mediaButton = document.querySelector('[data-media-button]');
    const modal = document.getElementById('platform-selector-modal');
    
    if (mediaButton && modal) {
        mediaButton.addEventListener('click', function() {
            modal.classList.remove('hidden');
        });
    }
});

function closePlatformModal() {
    document.getElementById('platform-selector-modal').classList.add('hidden');
}

function applyPlatformSelection() {
    // Obtener las plataformas seleccionadas del modal
    const selectedPlatforms = Array.from(document.querySelectorAll('#platform-selector-modal input[type="checkbox"]:checked'))
        .map(checkbox => checkbox.value);
    
    // Actualizar los checkboxes originales
    document.querySelectorAll('#publish-form input[name="platforms[]"]').forEach(checkbox => {
        checkbox.checked = selectedPlatforms.includes(checkbox.value);
    });
    
    // Cerrar el modal
    closePlatformModal();
}
