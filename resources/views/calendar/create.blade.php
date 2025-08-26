<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('calendar.index') }}" class="mr-4 text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Crear Horario de Publicación') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('calendar.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Título -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Título del Horario
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-300 @enderror"
                                placeholder="Ej: Publicación matutina de motivación" required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contenido -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Contenido de la Publicación
                            </label>
                            <textarea name="content" id="content" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('content') border-red-300 @enderror"
                                placeholder="Escribe el contenido que se publicará..." required>{{ old('content') }}</textarea>
                            <div class="mt-1 flex justify-between text-sm text-gray-500">
                                <span>Máximo 500 caracteres</span>
                                <span id="char-count">0/500</span>
                            </div>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Plataformas -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Plataformas
                                @if ($connectedAccounts->isEmpty())
                                    <span class="text-sm text-amber-600 font-normal">(Conecta tus cuentas en
                                        Configuración)</span>
                                @endif
                            </label>

                            @if ($connectedAccounts->isEmpty())
                                <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg mb-4">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-amber-400 mr-2" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <div>
                                            <h4 class="text-sm font-medium text-amber-800">No hay cuentas conectadas
                                            </h4>
                                            <p class="text-sm text-amber-700">Conecta al menos una cuenta social en <a
                                                    href="{{ route('settings') }}"
                                                    class="underline hover:no-underline">Configuración</a> para crear
                                                horarios de publicación.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                @forelse($availablePlatforms as $platform)
                                    @php
                                        $isConnected = $connectedAccounts
                                            ->where('provider', $platform['value'])
                                            ->isNotEmpty();
                                    @endphp
                                    <label
                                        class="flex items-center p-3 border rounded-lg cursor-pointer transition-colors
                                        {{ $isConnected ? 'border-gray-300 hover:bg-gray-50' : 'border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed' }}
                                        @error('platforms') border-red-300 @enderror">
                                        <input type="checkbox" name="platforms[]" value="{{ $platform['value'] }}"
                                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                            {{ in_array($platform['value'], old('platforms', [])) ? 'checked' : '' }}
                                            {{ $isConnected ? '' : 'disabled' }}>
                                        <div class="ml-3 flex items-center">
                                            <img src="{{ asset('images/' . $platform['icon']) }}"
                                                alt="{{ $platform['name'] }}" class="w-5 h-5 mr-2">
                                            <span
                                                class="text-sm font-medium {{ $isConnected ? 'text-gray-700' : 'text-gray-400' }}">
                                                {{ $platform['name'] }}
                                                @if (!$isConnected)
                                                    <span class="text-xs">(No conectado)</span>
                                                @endif
                                            </span>
                                        </div>
                                    </label>
                                @empty
                                    <p class="col-span-2 text-sm text-gray-500 text-center py-4">No hay plataformas
                                        disponibles</p>
                                @endforelse
                            </div>
                            @error('platforms')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo de horario -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tipo de Horario
                            </label>
                            <div class="space-y-4">
                                <label class="flex items-start">
                                    <input type="radio" name="is_recurring" value="0"
                                        class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        {{ old('is_recurring', '0') == '0' ? 'checked' : '' }}>
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-700">Horario Específico</span>
                                        <p class="text-sm text-gray-500">Publicar una sola vez en una fecha y hora
                                            específica</p>
                                    </div>
                                </label>

                                <label class="flex items-start">
                                    <input type="radio" name="is_recurring" value="1"
                                        class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300"
                                        {{ old('is_recurring') == '1' ? 'checked' : '' }}>
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-700">Horario Recurrente</span>
                                        <p class="text-sm text-gray-500">Publicar regularmente en días específicos de la
                                            semana</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Configuración para horario específico -->
                        <div id="specific-schedule" class="space-y-4 {{ old('is_recurring') == '1' ? 'hidden' : '' }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="schedule_date" class="block text-sm font-medium text-gray-700 mb-2">
                                        Fecha de Publicación
                                    </label>
                                    <input type="date" name="schedule_date" id="schedule_date"
                                        value="{{ old('schedule_date', $selectedDate ?? '') }}"
                                        min="{{ now()->format('Y-m-d') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('schedule_date') border-red-300 @enderror">
                                    @error('schedule_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="schedule_time" class="block text-sm font-medium text-gray-700 mb-2">
                                        Hora de Publicación
                                    </label>
                                    <input type="time" name="schedule_time" id="schedule_time"
                                        value="{{ old('schedule_time', $selectedTime ?? '09:00') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('schedule_time') border-red-300 @enderror">
                                    @error('schedule_time')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Configuración para horario recurrente -->
                        <div id="recurring-schedule"
                            class="space-y-4 {{ old('is_recurring') != '1' ? 'hidden' : '' }}">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Días de la Semana
                                </label>
                                <div class="grid grid-cols-4 md:grid-cols-7 gap-2">
                                    @php
                                        $days = [
                                            'Domingo',
                                            'Lunes',
                                            'Martes',
                                            'Miércoles',
                                            'Jueves',
                                            'Viernes',
                                            'Sábado',
                                        ];
                                        $shortDays = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                                    @endphp
                                    @foreach ($days as $index => $day)
                                        <label
                                            class="flex flex-col items-center p-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 @error('recurring_days') border-red-300 @enderror">
                                            <input type="checkbox" name="recurring_days[]"
                                                value="{{ $index }}"
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded mb-1"
                                                {{ in_array($index, old('recurring_days', [])) ? 'checked' : '' }}>
                                            <span
                                                class="text-xs font-medium text-gray-700 text-center">{{ $shortDays[$index] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('recurring_days')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="recurring_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Hora de Publicación
                                </label>
                                <input type="time" name="recurring_time" id="recurring_time"
                                    value="{{ old('recurring_time', '09:00') }}"
                                    class="w-full md:w-48 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('recurring_time') border-red-300 @enderror">
                                @error('recurring_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="recurring_start_date"
                                        class="block text-sm font-medium text-gray-700 mb-2">
                                        Fecha de Inicio (Opcional)
                                    </label>
                                    <input type="date" name="recurring_start_date" id="recurring_start_date"
                                        value="{{ old('recurring_start_date') }}" min="{{ now()->format('Y-m-d') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <div>
                                    <label for="recurring_end_date"
                                        class="block text-sm font-medium text-gray-700 mb-2">
                                        Fecha de Fin (Opcional)
                                    </label>
                                    <input type="date" name="recurring_end_date" id="recurring_end_date"
                                        value="{{ old('recurring_end_date') }}" min="{{ now()->format('Y-m-d') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>
                        </div>

                        <!-- Notas -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notas (Opcional)
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Notas adicionales sobre este horario...">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t">
                            <a href="{{ route('calendar.index') }}"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Crear Horario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const isRecurringRadios = document.querySelectorAll('input[name="is_recurring"]');
                const specificSchedule = document.getElementById('specific-schedule');
                const recurringSchedule = document.getElementById('recurring-schedule');
                const contentTextarea = document.getElementById('content');
                const charCount = document.getElementById('char-count');

                // Contador de caracteres
                function updateCharCount() {
                    const currentLength = contentTextarea.value.length;
                    charCount.textContent = `${currentLength}/500`;

                    if (currentLength > 500) {
                        charCount.classList.add('text-red-600');
                    } else {
                        charCount.classList.remove('text-red-600');
                    }
                }

                contentTextarea.addEventListener('input', updateCharCount);
                updateCharCount();

                function toggleScheduleType() {
                    const isRecurring = document.querySelector('input[name="is_recurring"]:checked').value === '1';

                    if (isRecurring) {
                        specificSchedule.classList.add('hidden');
                        recurringSchedule.classList.remove('hidden');

                        document.getElementById('schedule_date').required = false;
                        document.getElementById('schedule_time').required = false;

                        document.querySelectorAll('input[name="recurring_days[]"]').forEach(input => {
                            input.required = false;
                        });
                        document.getElementById('recurring_time').required = true;
                    } else {
                        specificSchedule.classList.remove('hidden');
                        recurringSchedule.classList.add('hidden');

                        document.getElementById('schedule_date').required = true;
                        document.getElementById('schedule_time').required = true;

                        document.querySelectorAll('input[name="recurring_days[]"]').forEach(input => {
                            input.required = false;
                        });
                        document.getElementById('recurring_time').required = false;
                    }
                }

                isRecurringRadios.forEach(radio => {
                    radio.addEventListener('change', toggleScheduleType);
                });

                const recurringStartDate = document.getElementById('recurring_start_date');
                const recurringEndDate = document.getElementById('recurring_end_date');

                recurringStartDate.addEventListener('change', function() {
                    if (this.value) {
                        recurringEndDate.min = this.value;
                    }
                });

                toggleScheduleType();
            });
        </script>
    @endpush
</x-app-layout>
