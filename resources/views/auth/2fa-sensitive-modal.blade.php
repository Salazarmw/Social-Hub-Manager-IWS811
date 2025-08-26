<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="2fa-sensitive-modal">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 text-center mt-4">
                Verificación de Seguridad
            </h3>
            <p class="text-sm text-gray-500 text-center mt-2">
                Esta acción requiere verificación adicional. Ingresa tu código 2FA.
            </p>
            
            <form id="2fa-sensitive-form" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label for="sensitive-code" class="block text-sm font-medium text-gray-700">Código 2FA</label>
                    <input type="text" id="sensitive-code" name="code" required maxlength="6"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="000000">
                    <div id="sensitive-code-error" class="text-red-600 text-sm mt-1 hidden"></div>
                </div>
                
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
                        Verificar
                    </button>
                    <button type="button" onclick="close2FASensitiveModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function close2FASensitiveModal() {
    document.getElementById('2fa-sensitive-modal').remove();
}

document.getElementById('2fa-sensitive-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const errorDiv = document.getElementById('sensitive-code-error');
    
    fetch('{{ route("2fa.verify.sensitive") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': formData.get('_token'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            code: formData.get('code')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            close2FASensitiveModal();
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                location.reload();
            }
        } else {
            errorDiv.textContent = data.message || 'Error en la verificación';
            errorDiv.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        errorDiv.textContent = 'Error de conexión. Intenta nuevamente.';
        errorDiv.classList.remove('hidden');
    });
});
</script>
