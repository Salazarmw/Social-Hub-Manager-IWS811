/**
 * Calendario popup para programar publicaciones
 */
class CalendarScheduler {
    constructor() {
        this.scheduleButton = document.querySelector('[data-schedule-button]');
        this.publishForm = document.querySelector('#publish-form');
        this.scheduledDateInput = document.querySelector('#scheduled-date');
        this.currentDate = new Date();
        this.currentMonth = this.currentDate.getMonth();
        this.currentYear = this.currentDate.getFullYear();
        
        // Nombres de los meses en español
        this.monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        
        this.init();
    }
    
    init() {
        // Verificar que existen los elementos necesarios
        if (!this.scheduleButton || !this.publishForm || !this.scheduledDateInput) {
            console.warn('Elementos necesarios para el calendario no encontrados');
            return;
        }
        
        this.createModal();
        this.attachEventListeners();
        this.renderCalendar();
        this.setDefaultTime();
    }
    
    createModal() {
        const modalHTML = `
            <div id="calendar-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
                <div class="bg-white rounded-lg shadow-xl p-4 w-full max-w-lg mx-4 transform transition-all">
                    <!-- Header -->
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Programar publicación</h3>
                        <button id="close-calendar-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Calendar Navigation -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-3">
                            <button id="prev-month" class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <span id="month-year-display" class="text-base font-medium text-gray-800"></span>
                            <button id="next-month" class="p-1 rounded-lg hover:bg-gray-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Calendar table -->
                        <table class="w-full border-collapse">
                            <thead>
                                <tr>
                                    <th class="text-xs font-medium text-gray-500 py-2 text-center">Dom</th>
                                    <th class="text-xs font-medium text-gray-500 py-2 text-center">Lun</th>
                                    <th class="text-xs font-medium text-gray-500 py-2 text-center">Mar</th>
                                    <th class="text-xs font-medium text-gray-500 py-2 text-center">Mié</th>
                                    <th class="text-xs font-medium text-gray-500 py-2 text-center">Jue</th>
                                    <th class="text-xs font-medium text-gray-500 py-2 text-center">Vie</th>
                                    <th class="text-xs font-medium text-gray-500 py-2 text-center">Sáb</th>
                                </tr>
                            </thead>
                            <tbody id="calendar-body">
                                <!-- Aquí se generarán las filas del calendario -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Time picker -->
                    <div class="mb-4">
                        <label for="time-picker" class="block text-sm font-medium text-gray-700 mb-2">
                            Seleccionar hora:
                        </label>
                        <input 
                            type="time" 
                            id="time-picker" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                        >
                    </div>
                    
                    <!-- Action buttons -->
                    <div class="flex justify-end space-x-2 relative z-50">
                        <button id="cancel-schedule" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button id="select-date-time" class="px-3 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors">
                            Programar
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Obtener referencias a elementos del modal
        this.modal = document.getElementById('calendar-modal');
        this.calendarBody = document.getElementById('calendar-body');
        this.monthYearDisplay = document.getElementById('month-year-display');
    }
    
    attachEventListeners() {
        // Abrir modal
        this.scheduleButton.addEventListener('click', () => this.openModal());
        
        // Cerrar modal
        document.getElementById('close-calendar-modal').addEventListener('click', () => this.closeModal());
        document.getElementById('cancel-schedule').addEventListener('click', () => this.closeModal());
        
        // Navegación de meses
        document.getElementById('prev-month').addEventListener('click', () => this.previousMonth());
        document.getElementById('next-month').addEventListener('click', () => this.nextMonth());
        
        // Seleccionar fecha y hora
        document.getElementById('select-date-time').addEventListener('click', () => this.selectDateTime());
        
        // Cerrar al hacer clic fuera del modal
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) this.closeModal();
        });
        
        // Cerrar con tecla ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !this.modal.classList.contains('hidden')) {
                this.closeModal();
            }
        });
    }
    
    openModal() {
        this.modal.classList.remove('hidden');
        this.renderCalendar();
    }
    
    closeModal() {
        this.modal.classList.add('hidden');
        this.clearSelection();
    }
    
    previousMonth() {
        this.currentMonth--;
        if (this.currentMonth < 0) {
            this.currentMonth = 11;
            this.currentYear--;
        }
        this.renderCalendar();
    }
    
    nextMonth() {
        this.currentMonth++;
        if (this.currentMonth > 11) {
            this.currentMonth = 0;
            this.currentYear++;
        }
        this.renderCalendar();
    }
    
    renderCalendar() {
        // Actualizar título del mes y año
        this.monthYearDisplay.textContent = `${this.monthNames[this.currentMonth]} ${this.currentYear}`;
        
        // Limpiar calendario previo
        this.calendarBody.innerHTML = '';
        
        const firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
        const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        let date = 1;
        
        // Crear 6 filas (suficiente para cualquier mes)
        for (let week = 0; week < 6; week++) {
            const row = document.createElement('tr');
            
            // Crear 7 celdas por semana
            for (let dayOfWeek = 0; dayOfWeek < 7; dayOfWeek++) {
                const cell = document.createElement('td');
                cell.classList.add('text-center', 'p-1');
                
                // Si estamos en la primera semana y no hemos llegado al primer día del mes
                if (week === 0 && dayOfWeek < firstDay) {
                    // Celda vacía
                    cell.innerHTML = '';
                } 
                // Si ya hemos puesto todos los días del mes
                else if (date > daysInMonth) {
                    // Celda vacía
                    cell.innerHTML = '';
                } 
                // Día válido del mes
                else {
                    const dayButton = this.createDayButton(date, today);
                    cell.appendChild(dayButton);
                    date++;
                }
                
                row.appendChild(cell);
            }
            
            this.calendarBody.appendChild(row);
            
            // Si ya hemos puesto todos los días, no necesitamos más filas
            if (date > daysInMonth) {
                break;
            }
        }
    }
    
    createDayButton(day, today) {
        const button = document.createElement('button');
        button.textContent = day;
        button.classList.add(
            'w-8', 'h-8', 'text-sm', 'rounded', 'transition-all', 
            'flex', 'items-center', 'justify-center'
        );
        
        const cellDate = new Date(this.currentYear, this.currentMonth, day);
        cellDate.setHours(0, 0, 0, 0);
        
        if (cellDate < today) {
            // Fecha pasada - deshabilitada
            button.classList.add('text-gray-300', 'cursor-not-allowed');
            button.disabled = true;
        } else {
            // Fecha futura - seleccionable
            button.classList.add(
                'text-gray-700', 'hover:bg-blue-100', 'hover:text-blue-600', 
                'cursor-pointer', 'focus:outline-none', 'focus:ring-2', 'focus:ring-blue-500'
            );
            
            button.dataset.year = this.currentYear;
            button.dataset.month = this.currentMonth;
            button.dataset.day = day;
            
            // Destacar día actual
            if (this.isToday(cellDate, today)) {
                button.classList.add('bg-blue-50', 'text-blue-600', 'font-medium', 'ring-1', 'ring-blue-200');
            }
            
            // Event listener para seleccionar fecha
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.selectDate(button);
            });
        }
        
        return button;
    }
    
    isToday(cellDate, today) {
        return cellDate.getTime() === today.getTime();
    }
    
    selectDate(button) {
        // Remover selección anterior
        this.clearSelection();
        
        // Seleccionar nueva fecha
        button.classList.add('selected-date', 'bg-blue-600', 'text-white');
        button.classList.remove('hover:bg-blue-100', 'hover:text-blue-600', 'text-gray-700', 'bg-blue-50', 'text-blue-600');
    }
    
    clearSelection() {
        const selected = this.calendarBody.querySelector('.selected-date');
        if (selected) {
            selected.classList.remove('selected-date', 'bg-blue-600', 'text-white');
            
            // Restaurar estilos originales
            const day = parseInt(selected.textContent);
            const today = new Date();
            const cellDate = new Date(this.currentYear, this.currentMonth, day);
            cellDate.setHours(0, 0, 0, 0);
            today.setHours(0, 0, 0, 0);
            
            if (cellDate >= today) {
                selected.classList.add('text-gray-700', 'hover:bg-blue-100', 'hover:text-blue-600');
                
                // Si era el día de hoy, restaurar su estilo especial
                if (this.isToday(cellDate, today)) {
                    selected.classList.add('bg-blue-50', 'text-blue-600', 'font-medium', 'ring-1', 'ring-blue-200');
                    selected.classList.remove('text-gray-700');
                }
            }
        }
    }
    
    selectDateTime() {
        const selectedButton = this.calendarBody.querySelector('.selected-date');
        const timePicker = document.getElementById('time-picker');
        
        if (!selectedButton) {
            this.showAlert('Por favor, selecciona una fecha.');
            return;
        }
        
        if (!timePicker.value) {
            this.showAlert('Por favor, selecciona una hora.');
            return;
        }
        
        const year = parseInt(selectedButton.dataset.year);
        const month = parseInt(selectedButton.dataset.month);
        const day = parseInt(selectedButton.dataset.day);
        const [hours, minutes] = timePicker.value.split(':');
        
        const selectedDateTime = new Date(year, month, day, hours, minutes, 0);
        const formattedDate = this.formatDateForInput(selectedDateTime);
        
        this.scheduledDateInput.value = formattedDate;
        this.closeModal();
        
        // Feedback visual opcional
        this.showSuccessMessage(selectedDateTime);
    }
    
    formatDateForInput(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day} ${hours}:${minutes}:00`;
    }
    
    setDefaultTime() {
        // Establecer hora actual como predeterminada
        const timePicker = document.getElementById('time-picker');
        if (timePicker) {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            timePicker.value = `${hours}:${minutes}`;
        }
    }
    
    showAlert(message) {
        // Simple alert - puedes reemplazar con un toast más elegante
        alert(message);
    }
    
    showSuccessMessage(date) {
        const formatted = date.toLocaleDateString('es-ES', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        console.log(`Publicación programada para: ${formatted}`);
        
        // Opcional: Mostrar mensaje de éxito en la UI
        // this.showToast(`Programado para ${formatted}`);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    new CalendarScheduler();
});