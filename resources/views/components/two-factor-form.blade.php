<form method="POST" action="{{ route('2fa.enable') }}" class="space-y-4">
    @csrf
    <label class="block text-sm font-medium text-gray-700">Código de la app</label>
    <input name="code" type="text" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
    <div class="flex justify-end">
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">Confirmar y Activar</button>
    </div>
</form>
