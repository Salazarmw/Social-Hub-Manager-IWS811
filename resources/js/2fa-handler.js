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

// Interceptar todas las peticiones AJAX para verificar requerimientos 2FA
document.addEventListener('DOMContentLoaded', function() {
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        return originalFetch.apply(this, args)
            .then(response => {
                // Si la respuesta es JSON y requiere verificación 2FA
                if (response.headers.get('content-type')?.includes('application/json')) {
                    return response.clone().json().then(data => {
                        if (window.handle2FARequirement(data)) {
                            // Si se requiere 2FA, no procesar la respuesta original
                            return Promise.reject(new Error('2FA_REQUIRED'));
                        }
                        return response;
                    }).catch(error => {
                        if (error.message === '2FA_REQUIRED') {
                            throw error;
                        }
                        // Si no es JSON válido, devolver la respuesta original
                        return response;
                    });
                }
                return response;
            });
    };
    
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (form.dataset.requires2fa === 'true') {
            e.preventDefault();
            
            fetch(form.action, {
                method: form.method,
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!window.handle2FARequirement(data)) {
                    form.submit();
                }
            })
            .catch(error => {
                if (error.message !== '2FA_REQUIRED') {
                    console.error('Error:', error);
                }
            });
        }
    });
});
