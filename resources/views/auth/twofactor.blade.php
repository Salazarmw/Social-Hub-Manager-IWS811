<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-lg">
            <div class="text-center">
                <h2 class="mt-6 text-2xl font-extrabold text-gray-900">
                    Verificación en dos pasos
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Ingresa el código generado por tu app de autenticación
                </p>
            </div>
            <form class="mt-8 space-y-6" method="POST" action="{{ route('2fa.verify') }}">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Código 2FA</label>
                    <input id="code" name="code" type="text" required autofocus class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>
                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        Verificar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
