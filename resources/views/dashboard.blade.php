<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inicio') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Composer de Publicaciones -->
            <div class="bg-white shadow-sm sm:rounded-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">{{ __('Crear publicación') }}</h3>
                    <span class="inline-flex items-center gap-2 text-xs text-gray-500">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        {{ __('Solo visual (no envía)') }}
                    </span>
                </div>

                <div class="px-6 py-5">
                    <div class="flex items-start gap-4">
                        <div class="flex-1">
                            <label for="composer" class="sr-only">{{ __('Escribe tu publicación') }}</label>
                            <textarea id="composer" rows="5" placeholder="{{ __('Escribe tu publicación...') }}" class="w-full resize-y rounded-lg border border-gray-200 focus:border-indigo-400 focus:ring focus:ring-indigo-100 text-gray-800 placeholder:text-gray-400"></textarea>

                            <!-- Acciones -->
                            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <button type="button" class="inline-flex items-center gap-2 px-3 py-2 text-sm rounded-md border border-gray-200 text-gray-700 hover:bg-gray-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356 10.49A2.25 2.25 0 0019.5 19.5V10.125a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25V19.5a2.25 2.25 0 002.25 2.25h8.25" />
                                        </svg>
                                        {{ __('Medios') }}
                                    </button>
                                    <button type="button" class="inline-flex items-center gap-2 px-3 py-2 text-sm rounded-md border border-gray-200 text-gray-700 hover:bg-gray-50">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ __('Programar') }}
                                    </button>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" class="px-4 py-2 text-sm font-medium rounded-md border border-indigo-200 text-indigo-700 bg-indigo-50 hover:bg-indigo-100">
                                        {{ __('Guardar en cola') }}
                                    </button>
                                    <button type="button" class="px-4 py-2 text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm">
                                        {{ __('Publicar ahora') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Indicadores -->
                    <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
                        <span>{{ __('Sugerencia: puedes añadir enlaces o hashtags.') }}</span>
                        <span>{{ __('0/280') }}</span>
                    </div>
                </div>
            </div>

            <!-- Tarjetas de ejemplo del dashboard (opcionales, visual) -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <div class="text-sm text-gray-500">{{ __('Publicaciones programadas') }}</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-800">12</div>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <div class="text-sm text-gray-500">{{ __('En cola') }}</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-800">5</div>
                </div>
                <div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm">
                    <div class="text-sm text-gray-500">{{ __('Cuentas conectadas') }}</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-800">3</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
