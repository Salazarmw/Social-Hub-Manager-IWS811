<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inicio') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Alertas -->
            @if (session('success'))
                <div class="mb-6 rounded-md bg-green-50 p-4 border border-green-200" x-data="{ show: true }" x-show="show">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button @click="show = false" class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-md bg-red-50 p-4 border border-red-200" x-data="{ show: true }" x-show="show">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button @click="show = false" class="inline-flex rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600">
                                    <span class="sr-only">Cerrar</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Composer -->
            <x-composer-card title="Crear publicación">
                <form id="publish-form" method="POST" action="{{ route('posts.store') }}">
                    @csrf
                    <input type="hidden" name="scheduled_date" id="scheduled-date">
                    
                    <div class="flex items-start gap-4">
                        <x-textarea name="content" placeholder="Escribe tu publicación..." :value="old('content')" required minlength="1" maxlength="280" />
                    </div>
                    
                    @error('content')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <!-- Input ocultos para plataformas -->
                    <div class="hidden" id="platforms-container">
                        <input type="checkbox" name="platforms[]" value="twitter" class="platform-checkbox">
                        <input type="checkbox" name="platforms[]" value="reddit" class="platform-checkbox">
                    </div>
                    
                    @error('platforms')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

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
                            <button type="submit" name="action" value="publish"
                                class="px-4 py-2 text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                {{ __('Publicar') }}
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
        <script src="{{ asset('js/post-validation.js') }}"></script>
        <script src="{{ asset('js/platform-selector.js') }}"></script>
    @endpush
</x-app-layout>
