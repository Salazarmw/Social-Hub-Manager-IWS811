<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cola de Publicaciones') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Filtros -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-medium mb-3">Filtros</h3>
                        <form action="{{ route('queue.index') }}" method="GET" class="flex flex-wrap gap-4">
                            <div class="w-full md:w-auto">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                <select name="status" id="status" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                                    <option value="">Todos</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Procesando</option>
                                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publicado</option>
                                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Fallido</option>
                                </select>
                            </div>
                            <div class="w-full md:w-auto">
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                            </div>
                            <div class="w-full md:w-auto">
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-full">
                            </div>
                            <div class="w-full md:w-auto flex items-end">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Filtrar
                                </button>
                                <a href="{{ route('queue.index') }}" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    Limpiar
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Tabla de publicaciones -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contenido
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha programada
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Plataformas
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($posts as $post)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-normal">
                                            <div class="text-sm text-gray-900 max-w-xs break-words">{{ Str::limit($post->content, 100) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $post->scheduled_date ? $post->scheduled_date->format('d/m/Y H:i') : 'Inmediata' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex space-x-1">
                                                @foreach($post->platforms as $platform)
                                                    <span class="px-2 py-1 text-xs rounded-full {{ $platform == 'twitter' ? 'bg-blue-100 text-blue-800' : ($platform == 'facebook' ? 'bg-indigo-100 text-indigo-800' : 'bg-pink-100 text-pink-800') }}">
                                                        {{ ucfirst($platform) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs rounded-full {{ 
                                                $post->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                ($post->status == 'processing' ? 'bg-blue-100 text-blue-800' : 
                                                ($post->status == 'published' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')) 
                                            }}">
                                                {{ ucfirst($post->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <form action="{{ route('queue.destroy', $post->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de eliminar esta publicación programada?')">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No hay publicaciones programadas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>