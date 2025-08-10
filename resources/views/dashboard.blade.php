<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inicio') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Composer -->
            <x-composer-card title="Crear publicación" badge="Solo visual (no envía)">
                <div class="flex items-start gap-4">
                    <x-textarea placeholder="Escribe tu publicación..." />
                </div>

                <x-actions>
                    <x-slot:left>
                        <x-button-icon text="Medios">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356 10.49A2.25 2.25 0 0019.5 19.5V10.125a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25V19.5a2.25 2.25 0 002.25 2.25h8.25" />
                            </svg>
                        </x-button-icon>

                        <x-button-icon text="Programar">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </x-button-icon>
                    </x-slot:left>

                    <x-slot:right>
                        <button type="button"
                            class="px-4 py-2 text-sm font-medium rounded-md border border-indigo-200 text-indigo-700 bg-indigo-50 hover:bg-indigo-100">
                            {{ __('Guardar en cola') }}
                        </button>

                        <button type="button"
                            class="px-4 py-2 text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm">
                            {{ __('Publicar ahora') }}
                        </button>
                    </x-slot:right>
                </x-actions>
            </x-composer-card>

            <!-- Tarjetas de estadísticas -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-stat-card title="Publicaciones programadas">
                    <div class="text-2xl font-semibold text-gray-800">12</div>
                </x-stat-card>

                <x-stat-card title="En cola">
                    <div class="text-2xl font-semibold text-gray-800">5</div>
                </x-stat-card>

                <x-stat-card title="Cuentas conectadas">
                    <div class="text-2xl font-semibold text-gray-800">3</div>
                </x-stat-card>
            </div>
        </div>
    </div>
</x-app-layout>
