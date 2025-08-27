<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Calendario de Publicaciones') }}
            </h2>
            <a href="{{ route('calendar.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nuevo Horario
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Controles del calendario -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <h3 id="calendar-title" class="text-lg font-medium text-gray-900">Vista del Calendario</h3>
                            <div class="flex items-center space-x-2">
                                <button id="calendar-today"
                                    class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                                    Hoy
                                </button>
                                <button id="calendar-prev"
                                    class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>
                                <button id="calendar-next"
                                    class="p-2 text-gray-400 hover:text-gray-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <select id="calendar-view"
                                class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="dayGridMonth">Mes</option>
                                <option value="timeGridWeek">Semana</option>
                                <option value="timeGridDay">Día</option>
                            </select>
                        </div>
                    </div>

                    <!-- Leyenda -->
                    <div class="flex items-center space-x-6 mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700">Leyenda:</h4>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Horarios Recurrentes</span>
                            <div class="w-3 h-3 bg-blue-500 rounded"></div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Horarios Específicos</span>
                            <div class="w-3 h-3 bg-green-600 rounded"></div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Publicaciones Programadas</span>
                            <div class="w-3 h-3 bg-yellow-500 rounded"></div>
                        </div>
                    </div>

                    <!-- Contenedor del calendario -->
                    <div id="calendar" class="w-full h-auto min-h-[600px]" style="max-height: 80vh; overflow: hidden;">
                    </div>
                </div>
            </div>

            <!-- Resumen de horarios activos -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Horarios Activos</h3>
                    @if ($schedules->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($schedules as $schedule)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="font-medium text-gray-900 truncate">{{ $schedule->title }}</h4>
                                        <div class="flex items-center space-x-1">
                                            @foreach ($schedule->platforms as $platform)
                                                <span
                                                    class="inline-block w-2 h-2 rounded-full {{ $platform == 'twitter' ? 'bg-blue-400' : 'bg-orange-400' }}"
                                                    title="{{ ucfirst($platform) }}"></span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $schedule->content }}</p>

                                    @if ($schedule->is_recurring)
                                        <div class="text-xs text-blue-600 mb-2">
                                            <svg class="inline w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Recurrente: {{ $schedule->recurring_days_text }}
                                            <br>{{ $schedule->recurring_time->format('H:i') }}
                                        </div>
                                    @else
                                        <div class="text-xs text-green-600 mb-2">
                                            <svg class="inline w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $schedule->schedule_date->format('d/m/Y') }} a las
                                            {{ $schedule->schedule_time->format('H:i') }}
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('calendar.edit', $schedule->id) }}"
                                            class="text-xs text-indigo-600 hover:text-indigo-800">
                                            Editar
                                        </a>
                                        <form action="{{ route('calendar.destroy', $schedule->id) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('¿Estás seguro de eliminar este horario?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:text-red-800">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">
                            No tienes horarios programados.
                            <a href="{{ route('calendar.create') }}" class="text-indigo-600 hover:text-indigo-800">
                                Crea tu primer horario
                            </a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar detalles del evento -->
    <div id="event-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4 transform transition-all">
            <div class="flex justify-between items-center mb-4">
                <h3 id="event-modal-title" class="text-lg font-semibold text-gray-900"></h3>
                <button id="close-event-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div id="event-modal-content"></div>

            <div id="event-modal-actions" class="flex justify-end space-x-3 mt-6"></div>
        </div>
    </div>

    @push('scripts')
        <!-- FullCalendar CSS y JS -->
        <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/es.global.min.js'></script>

        <!-- CSS personalizado para el calendario -->
        <style>
            /* Estilo para el scroll en vistas de tiempo */
            .fc-timegrid-axis-cushion,
            .fc-timegrid-slot-label-cushion {
                font-size: 0.75rem;
                font-weight: 500;
            }

            .fc-scroller {
                overflow-y: auto !important;
                max-height: 600px !important;
                scrollbar-width: thin;
                scrollbar-color: #cbd5e1 #f1f5f9;
            }

            .fc-scroller::-webkit-scrollbar {
                width: 8px;
            }

            .fc-scroller::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 4px;
            }

            .fc-scroller::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }

            .fc-scroller::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }

            .fc-event {
                font-size: 0.75rem;
                border-radius: 4px;
                border: none !important;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                cursor: pointer;
            }

            .fc-event-title {
                font-weight: 600;
                padding: 2px 4px;
            }

            /* Estilo para eventos recurrentes */
            .recurring-event {
                background: linear-gradient(135deg, #3B82F6, #1D4ED8) !important;
            }

            /* Estilo para eventos específicos */
            .specific-event {
                background: linear-gradient(135deg, #059669, #047857) !important;
            }

            /* Estilo para publicaciones programadas */
            .scheduled-post-event {
                background: linear-gradient(135deg, #F59E0B, #D97706) !important;
            }

            .fc-timegrid-slot {
                border-top: 1px solid #f3f4f6;
                height: 24px;
            }

            .fc-timegrid-slot-label {
                border-right: 1px solid #e5e7eb;
                background-color: #fafafa;
            }

            /* Destacar las horas principales */
            .fc-timegrid-slot[data-time="00:00:00"],
            .fc-timegrid-slot[data-time="06:00:00"],
            .fc-timegrid-slot[data-time="12:00:00"],
            .fc-timegrid-slot[data-time="18:00:00"] {
                border-top: 2px solid #d1d5db;
            }

            /* Estilo para el día actual */
            .fc-day-today {
                background-color: rgba(59, 130, 246, 0.05) !important;
            }

            .fc-timegrid-col-frame {
                min-height: 1440px;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const calendarEl = document.getElementById('calendar');
                const calendarTitle = document.getElementById('calendar-title');

                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'es',
                    timeZone: 'local',
                    headerToolbar: false,
                    height: 'auto',

                    // Configuración de eventos
                    events: {
                        url: '{{ route('calendar.events') }}',
                        failure: function() {
                            console.error('Hubo un error al cargar los eventos');
                        }
                    },

                    // Configuración visual
                    dayMaxEvents: 3, // Máximo 3 eventos por día antes de mostrar "+X more"
                    moreLinkClick: 'popover',

                    // Configuración de fechas
                    firstDay: 1, // Empezar por lunes

                    // Configuración de horarios para vistas de tiempo
                    slotMinTime: '00:00:00', // Mostrar desde las 00:00
                    slotMaxTime: '24:00:00', // Hasta las 24:00 (medianoche siguiente)
                    slotDuration: '00:30:00',
                    scrollTime: '08:00:00', // Scroll inicial a las 8 AM en vistas de tiempo
                    allDaySlot: false,

                    // Configuración de interactividad
                    editable: false,
                    selectable: true,
                    selectMirror: true,

                    // Configuración para diferentes vistas
                    views: {
                        dayGridMonth: {
                            dayMaxEvents: 3,
                            moreLinkClick: 'popover'
                        },
                        timeGridWeek: {
                            slotLabelFormat: {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false
                            },
                            eventMinHeight: 20,
                            slotMinTime: '00:00:00',
                            slotMaxTime: '24:00:00',
                            scrollTime: '08:00:00',
                            allDaySlot: false
                        },
                        timeGridDay: {
                            slotLabelFormat: {
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false
                            },
                            eventMinHeight: 30,
                            slotMinTime: '00:00:00',
                            slotMaxTime: '24:00:00',
                            scrollTime: '08:00:00',
                            allDaySlot: false
                        }
                    },

                    // Event handlers
                    select: function(info) {
                        // Permitir crear nuevo evento haciendo clic en una fecha
                        const date = info.start.toISOString().split('T')[0];
                        const time = info.start.getHours().toString().padStart(2, '0') + ':' + info.start
                            .getMinutes().toString().padStart(2, '0');
                        window.location.href = `{{ route('calendar.create') }}?date=${date}&time=${time}`;
                    },

                    eventClick: function(info) {
                        showEventDetails(info.event);
                    },

                    // Personalizar el rendering de eventos
                    eventDidMount: function(info) {
                        info.el.setAttribute('title', info.event.extendedProps.content || '');
                    },

                    // Actualizar el título cuando cambie la vista
                    datesSet: function(dateInfo) {
                        updateCalendarTitle(dateInfo);
                    }
                });

                calendar.render();

                // Función para actualizar el título del calendario
                function updateCalendarTitle(dateInfo) {
                    const view = calendar.view;
                    const date = dateInfo.start;

                    let titleText = '';

                    switch (view.type) {
                        case 'dayGridMonth':
                            titleText = date.toLocaleDateString('es-ES', {
                                month: 'long',
                                year: 'numeric'
                            });
                            break;
                        case 'timeGridWeek':
                            const weekStart = dateInfo.start;
                            const weekEnd = new Date(dateInfo.end);
                            weekEnd.setDate(weekEnd.getDate() - 1); // Ajustar el final de la semana

                            if (weekStart.getMonth() === weekEnd.getMonth()) {
                                titleText =
                                    `${weekStart.getDate()} - ${weekEnd.getDate()} de ${weekStart.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' })}`;
                            } else {
                                titleText =
                                    `${weekStart.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' })} - ${weekEnd.toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' })}`;
                            }
                            break;
                        case 'timeGridDay':
                            titleText = date.toLocaleDateString('es-ES', {
                                weekday: 'long',
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric'
                            });
                            break;
                        default:
                            titleText = 'Vista del Calendario';
                    }

                    calendarTitle.textContent = titleText.charAt(0).toUpperCase() + titleText.slice(1);
                }

                // Controles personalizados
                document.getElementById('calendar-prev').addEventListener('click', () => {
                    calendar.prev();
                });

                document.getElementById('calendar-next').addEventListener('click', () => {
                    calendar.next();
                });

                document.getElementById('calendar-today').addEventListener('click', () => {
                    calendar.today();
                });

                document.getElementById('calendar-view').addEventListener('change', (e) => {
                    calendar.changeView(e.target.value);
                });

                // Modal de eventos
                const eventModal = document.getElementById('event-modal');
                const closeModal = document.getElementById('close-event-modal');

                closeModal.addEventListener('click', () => {
                    eventModal.classList.add('hidden');
                });

                eventModal.addEventListener('click', (e) => {
                    if (e.target === eventModal) {
                        eventModal.classList.add('hidden');
                    }
                });

                function showEventDetails(event) {
                    const modal = document.getElementById('event-modal');
                    const title = document.getElementById('event-modal-title');
                    const content = document.getElementById('event-modal-content');
                    const actions = document.getElementById('event-modal-actions');

                    title.textContent = event.title;

                    let contentHTML = '';
                    if (event.extendedProps.content) {
                        contentHTML += `<p class="text-gray-700 mb-3">${event.extendedProps.content}</p>`;
                    }

                    if (event.extendedProps.platforms) {
                        contentHTML +=
                            '<div class="mb-3"><span class="text-sm font-medium text-gray-700">Plataformas:</span><div class="flex space-x-2 mt-1">';
                        event.extendedProps.platforms.forEach(platform => {
                            const bgColor = platform === 'twitter' ? 'bg-blue-100 text-blue-800' :
                                'bg-orange-100 text-orange-800';
                            contentHTML +=
                                `<span class="px-2 py-1 text-xs rounded-full ${bgColor}">${platform}</span>`;
                        });
                        contentHTML += '</div></div>';
                    }

                    contentHTML += `<p class="text-sm text-gray-500">📅 ${event.start.toLocaleString('es-ES')}</p>`;

                    content.innerHTML = contentHTML;

                    // Botones de acción según el tipo de evento
                    let actionsHTML = '';
                    if (event.extendedProps.type === 'schedule') {
                        const scheduleId = event.extendedProps.schedule_id;
                        actionsHTML = `
                            <a href="/calendar/${scheduleId}/edit" class="px-4 py-2 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                Editar
                            </a>
                        `;
                    } else if (event.extendedProps.type === 'scheduled_post') {
                        const postId = event.extendedProps.post_id;
                        actionsHTML = `
                            <a href="/queue" class="px-4 py-2 text-sm bg-yellow-600 text-white rounded hover:bg-yellow-700">
                                Ver en Cola
                            </a>
                        `;
                    }

                    actions.innerHTML = actionsHTML;
                    modal.classList.remove('hidden');
                }
            });
        </script>
    @endpush
</x-app-layout>
