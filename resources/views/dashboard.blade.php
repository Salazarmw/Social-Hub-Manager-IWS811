<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inicio') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Composer -->
            <x-composer-card title="Crear publicación">
                <form id="publish-form" method="POST" action="{{ route('posts.store') }}">
                    @csrf
                    <input type="hidden" name="scheduled_date" id="scheduled-date">
                    
                    <div class="flex items-start gap-4">
                        <x-textarea name="content" placeholder="Escribe tu publicación..." />
                    </div>
                    
                    <!-- Input ocultos para plataformas -->
                    <div class="hidden">
                        <input type="checkbox" name="platforms[]" value="twitter">
                        <input type="checkbox" name="platforms[]" value="reddit">
                    </div>

                    <x-actions>
                        <x-slot:left>
                            <x-button-icon type="button" data-media-button text="Plataformas">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6 13.5V3.75m0 9.75a1.5 1.5 0 010 3m0-3a1.5 1.5 0 000 3m0 3.75V16.5m12-3V3.75m0 9.75a1.5 1.5 0 010 3m0-3a1.5 1.5 0 000 3m0 3.75V16.5m-6-9V3.75m0 3.75a1.5 1.5 0 010 3m0-3a1.5 1.5 0 000 3m0 9.75V10.5" />
                                </svg>
                            </x-button-icon>

                            <x-button-icon type="button" data-schedule-button text="Programar">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" 
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </x-button-icon>
                        </x-slot:left>

                        <x-slot:right>
                            <button type="submit" name="action" value="queue"
                                class="px-4 py-2 text-sm font-medium rounded-md border border-indigo-200 text-indigo-700 bg-indigo-50 hover:bg-indigo-100">
                                {{ __('Guardar en cola') }}
                            </button>

                            <button type="submit" name="action" value="publish"
                                class="px-4 py-2 text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm">
                                {{ __('Publicar ahora') }}
                            </button>
                        </x-slot:right>
                    </x-actions>
                </form>
            </x-composer-card>

            <!-- Tarjetas de estadísticas -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-stat-card title="Publicaciones programadas">
                    <div class="text-2xl font-semibold text-gray-800">{{ $stats['scheduled'] }}</div>
                </x-stat-card>

                <x-stat-card title="En cola">
                    <div class="text-2xl font-semibold text-gray-800">{{ $stats['queued'] }}</div>
                </x-stat-card>

                <x-stat-card title="Cuentas conectadas">
                    <div class="text-2xl font-semibold text-gray-800">{{ $stats['accounts'] }}</div>
                </x-stat-card>
            </div>
        </div>
    </div>

    <!-- Modal de selección de plataformas -->
    <x-platform-selector-modal />

    @push('scripts')
        <script src="{{ asset('js/calendar.js') }}"></script>
        <script src="{{ asset('js/platform-selector.js') }}"></script>
    @endpush
</x-app-layout>
