document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('publish-form');
    const content = form.querySelector('textarea[name="content"]');
    const platformsContainer = document.getElementById('platforms-container');
    const platformCheckboxes = platformsContainer.querySelectorAll('.platform-checkbox');
    const publishButton = form.querySelector('button[type="submit"]');

    // Función para validar el formulario
    function validateForm() {
        let isValid = true;
        let errorMessages = [];

        // Validar contenido
        if (!content.value.trim()) {
            isValid = false;
            errorMessages.push('El contenido de la publicación es obligatorio.');
        }

        // Validar plataformas seleccionadas
        let platformSelected = false;
        platformCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                platformSelected = true;
            }
        });

        if (!platformSelected) {
            isValid = false;
            errorMessages.push('Debes seleccionar al menos una plataforma.');
        }

        return { isValid, errorMessages };
    }

    // Validar antes de enviar
    form.addEventListener('submit', function(e) {
        const { isValid, errorMessages } = validateForm();

        if (!isValid) {
            e.preventDefault();
            alert(errorMessages.join('\n'));
        }
    });

    // Validar cuando se selecciona una plataforma en el modal
    document.addEventListener('platformSelected', function(e) {
        const platforms = e.detail.platforms;
        
        // Limpiar selecciones anteriores
        platformCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });

        // Marcar las plataformas seleccionadas
        platforms.forEach(platform => {
            const checkbox = platformsContainer.querySelector(`input[value="${platform}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
    });
});
