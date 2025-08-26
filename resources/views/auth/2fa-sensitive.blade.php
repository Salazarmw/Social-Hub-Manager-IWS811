<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-lg">
            <div class="text-center">
                <h2 class="mt-6 text-2xl font-extrabold text-gray-900">
                    Verificación de Seguridad
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Esta acción requiere verificación adicional. Ingresa el código de tu app de autenticación.
                </p>
            </div>
            <form class="mt-8 space-y-6" method="POST" action="{{ route('2fa.verify.sensitive') }}">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Código 2FA</label>
                    <input id="code" name="code" type="text" required autofocus maxlength="6" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>
                <div class="flex space-x-4">
                    <button type="submit" class="flex-1 flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        Verificar
                    </button>
                    <a href="{{ route('dashboard') }}" class="flex-1 flex justify-center py-3 px-4 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
