import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

window.handle2FARequirement = function(response) {
    if (response.requires_2fa_verification) {
        if (response.view) {
            document.body.insertAdjacentHTML('beforeend', response.view);
        } else {
            window.location.href = '/2fa/verify-sensitive';
        }
        return true;
    }
    return false;
};

document.addEventListener('DOMContentLoaded', function() {
    // Para formularios que requieren verificación 2FA
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.dataset.requires2fa === 'true') {
            e.preventDefault();
            
            // Obtener el token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const formData = new FormData(form);
            
            // Realizar petición AJAX para verificar si necesita 2FA
            fetch(form.action, {
                method: form.method,
                headers: {
                    'X-CSRF-Token': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }
                
                return response.json().catch(() => {
                    form.submit();
                });
            })
            .then(data => {
                if (data) {
                    if (!window.handle2FARequirement(data)) {
                        // Si no requiere 2FA, procesar la respuesta
                        if (data.success !== undefined) {
                            if (data.success) {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    location.reload();
                                }
                            } else {
                                console.error('Error:', data.message || 'Error desconocido');
                            }
                        } else {
                            form.submit();
                        }
                    }
                }
            })
            .catch(error => {
                if (error.message !== '2FA_REQUIRED') {
                    console.error('Error:', error);
                    // En caso de error, enviar el formulario normalmente
                    form.submit();
                }
            });
        }
    });
});
