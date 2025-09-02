@extends('layouts.app')

@section('title', 'Calendário')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Eventos</h5>
                    @if(Auth::user()->is_admin || Auth::user()->hasPermission('create_calendar_events'))
                    <button class="btn btn-pipa-red" data-bs-toggle="modal" data-bs-target="#eventModal">
                        <i class="fas fa-plus me-2"></i> Novo Evento
                    </button>
                    @endif
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para criar/editar evento -->
@if(Auth::user()->is_admin || Auth::user()->hasPermission('create_calendar_events'))
<div class="modal fade" id="eventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="eventForm">
                <div class="modal-body">
                    <input type="hidden" id="eventId">
                    <div class="mb-3">
                        <label for="title" class="form-label">Título *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Data Início *</label>
                                <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">Data Fim *</label>
                                <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="color" class="form-label">Cor do Evento (Opcional)</label>
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            <div class="color-option" data-color="#087c04" style="background-color: #087c04;"></div>
                            <div class="color-option" data-color="#4a6fdc" style="background-color: #4a6fdc;"></div>
                            <div class="color-option" data-color="#e67e22" style="background-color: #e67e22;"></div>
                            <div class="color-option" data-color="#b82424" style="background-color: #b82424;"></div>
                            <div class="color-option" data-color="#9b59b6" style="background-color: #9b59b6;"></div>
                            <div class="color-option" data-color="#34495e" style="background-color: #34495e;"></div>
                            <div class="color-option" data-color="#ff6b6b" style="background-color: #ff6b6b;"></div>
                            <div class="color-option" data-color="#48dbfb" style="background-color: #48dbfb;"></div>
                            <div class="color-option" data-color="#1dd1a1" style="background-color: #1dd1a1;"></div>
                            <div class="color-option" data-color="#feca57" style="background-color: #feca57;"></div>
                            <div class="color-option" data-color="#5f27cd" style="background-color: #5f27cd;"></div>
                            <div class="color-option" data-color="#00d2d3" style="background-color: #00d2d3;"></div>
                            <div class="color-option" data-color="" style="background-color: #f8f9fa; border: 1px solid #dee2e6;">
                                <i class="fas fa-times text-muted"></i>
                            </div>
                        </div>
                        <div class="input-group mt-2">
                            <span class="input-group-text">Cor personalizada:</span>
                            <input type="color" class="form-control form-control-color" id="customColor" value="#087c04">
                            <button type="button" class="btn btn-outline-secondary" id="applyCustomColor">Aplicar</button>
                        </div>
                        <small class="form-text text-muted">Selecione uma cor para o evento ou deixe em branco para usar a cor padrão</small>
                        <input type="hidden" id="color" name="color" value="">
                    </div>
                    <div class="mb-3">
                        <label for="visibility" class="form-label">Visibilidade *</label>
                        <select class="form-select" id="visibility" name="visibility" required>
                            <option value="private">Privado</option>
                            <option value="public">Público</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="participants" class="form-label">Participantes</label>
                        <select class="form-select" id="participants" name="participants[]" multiple>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Selecione os usuários que podem visualizar este evento (segure Ctrl para selecionar múltiplos)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-pipa-red">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Modal para visualizar evento -->
<div class="modal fade" id="viewEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3" id="viewColorBadge" style="height: 8px; border-radius: 4px; display: none;"></div>
                <h6 id="viewTitle" class="mb-2"></h6>
                <p id="viewDescription" class="text-muted"></p>
                <div class="row mt-3">
                    <div class="col-6">
                        <strong>Início:</strong>
                        <span id="viewStartDate"></span>
                    </div>
                    <div class="col-6">
                        <strong>Fim:</strong>
                        <span id="viewEndDate"></span>
                    </div>
                </div>
                <div class="mt-3">
                    <strong>Visibilidade:</strong>
                    <span id="viewVisibility"></span>
                </div>
                <div class="mt-2">
                    <strong>Criado por:</strong>
                    <span id="viewCreatedBy"></span>
                </div>
                <div class="mt-3" id="viewParticipantsSection">
                    <strong>Participantes:</strong>
                    <ul id="viewParticipants" class="mb-0 mt-2 ps-3"></ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                @if(Auth::user()->is_admin || Auth::user()->hasPermission('edit_calendar_events'))
                <button type="button" class="btn btn-pipa-red" id="editEventBtn">Editar</button>
                @endif
                @if(Auth::user()->is_admin || Auth::user()->hasPermission('delete_calendar_events'))
                <button type="button" class="btn btn-danger" id="deleteEventBtn">Excluir</button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    #calendar {
        max-width: 100%;
        margin: 0 auto;
        height: 700px;
        font-family: 'Inter', sans-serif;
    }
    
    .fc-event {
        cursor: pointer;
        border: none;
        border-radius: 6px;
        padding: 2px 4px;
        font-weight: 500;
        font-size: 0.85rem;
        /* Garantir que o evento não ultrapasse os limites */
        max-width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .fc-event-title {
        font-weight: 600;
        /* Forçar quebra de texto */
        white-space: normal;
        word-break: break-word;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Limitar a 2 linhas */
        -webkit-box-orient: vertical;
    }
    
    /* Container do dia - garantir que eventos não ultrapassem */
    .fc-daygrid-day-frame {
        padding: 4px;
        overflow: hidden;
    }
    
    .fc-daygrid-day-events {
        /* Limitar altura máxima para eventos */
        max-height: 120px;
        overflow-y: auto;
    }
    
    .fc-daygrid-event {
        /* Garantir que eventos não ultrapassem a célula do dia */
        margin-bottom: 2px;
        max-width: 100%;
    }
    
    .fc-daygrid-event-harness {
        /* Limitar o container do evento */
        max-width: 100%;
    }
    
    .fc-daygrid-block-event .fc-event-time,
    .fc-daygrid-block-event .fc-event-title {
        /* Garantir que texto não ultrapasse */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: block;
    }
    
    .fc-day-today {
        background-color: rgba(184, 36, 36, 0.1) !important;
    }
    
    /* Restante do CSS permanece igual */
    .fc-daygrid-day-dot {
        display: none;
    }
    
    .fc-toolbar {
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    
    .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-color);
    }
    
    .fc-button {
        background: var(--gradient-primary);
        border: none;
        border-radius: 8px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
        transition: var(--transition-base);
    }
    
    .fc-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(184, 36, 36, 0.25);
    }
    
    .fc-button-primary:not(:disabled).fc-button-active, 
    .fc-button-primary:not(:disabled):active {
        background: var(--gradient-primary);
        border: none;
    }
    
    .fc-button:focus {
        box-shadow: none;
    }
    
    .fc-daygrid-day-number {
        font-weight: 600;
        color: var(--text-color);
    }
    
    .fc-col-header-cell {
        background: rgba(184, 36, 36, 0.05);
    }
    
    .fc-col-header-cell-cushion {
        color: var(--text-color);
        font-weight: 600;
        text-transform: uppercase;
        padding: 8px;
    }
    
    .color-option {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        cursor: pointer;
        transition: var(--transition-base);
        border: 2px solid transparent;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .color-option:hover, .color-option.selected {
        transform: scale(1.2);
        border-color: var(--dark-color);
    }
    
    /* Remover hover branco dos eventos */
    .fc-event:hover {
        transform: none;
        box-shadow: none;
    }
    
    .fc-event .fc-event-main {
        color: white !important;
    }
    
    .fc-event .fc-event-main-frame {
        display: flex;
        align-items: center;
    }
    
    .fc-event-time {
        font-weight: 600;
        margin-right: 4px;
        /* Limitar tamanho do tempo */
        flex-shrink: 0;
    }
    
    /* Ajustes para modo escuro */
    [data-bs-theme="dark"] .fc-toolbar-title,
    [data-bs-theme="dark"] .fc-daygrid-day-number,
    [data-bs-theme="dark"] .fc-col-header-cell-cushion {
        color: #f8f9fa;
    }
    
    [data-bs-theme="dark"] .fc-theme-standard td,
    [data-bs-theme="dark"] .fc-theme-standard th {
        border-color: #444;
    }
    
    [data-bs-theme="dark"] .fc-daygrid-day-frame {
        background-color: #2b2b2b;
    }
    
    /* Melhorias para eventos coloridos */
    .fc-event {
        transition: none; /* Remover transição */
    }
    
    .fc-daygrid-event {
        border-left: 4px solid transparent;
    }
    
    /* Estilo para eventos sem cor definida */
    .fc-event.no-color {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        color: #212529 !important;
    }
    
    [data-bs-theme="dark"] .fc-event.no-color {
        background-color: #3a3a3a; /* Alterado para um cinza mais claro */
        border-color: #555;
        color: #f8f9fa !important;
    }
    
    /* Estilo para o seletor de cor personalizada */
    .form-control-color {
        height: 38px;
    }

    .fc .fc-list-event:hover td {
        background-color: transparent !important;
    }

    .fc .fc-list-event.fc-event-forced-url:hover td {
        background-color: transparent !important;
    }

    /* Se precisar, também remova qualquer outra estilização de hover */
    .fc .fc-list-event:hover {
        background-color: transparent !important;
    }

    /* Manter a consistência com o tema escuro */
    [data-bs-theme="dark"] .fc .fc-list-event:hover td,
    [data-bs-theme="dark"] .fc .fc-list-event.fc-event-forced-url:hover td,
    [data-bs-theme="dark"] .fc .fc-list-event:hover {
        background-color: transparent !important;
    }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt-br.js'></script>
<script>
    // Variáveis globais
    let calendar;
    let currentEventId = null;

    document.addEventListener('DOMContentLoaded', function() {
        initializeCalendar();
        setupEventListeners();
    });

    function initializeCalendar() {
        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'pt-br',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            events: {
                url: '/admin/calendar/events',
                failure: function() {
                    showToast('Erro ao carregar eventos', 'error');
                }
            },
            eventClick: function(info) {
                viewEvent(info.event);
            },
            dateClick: function(info) {
                @if(Auth::user()->is_admin || Auth::user()->hasPermission('create_calendar_events'))
                createEvent(info.dateStr);
                @endif
            },
            eventContent: function(arg) {
                // Personalizar a aparência dos eventos
                let timeText = arg.timeText ? `<div class="fc-event-time">${arg.timeText}</div>` : '';
                let titleText = `<div class="fc-event-title">${arg.event.title}</div>`;
                
                return {
                    html: `<div class="fc-event-main-frame">${timeText}${titleText}</div>`
                };
            },

        eventDidMount: function(info) {
            // Aplicar a cor do evento de forma discreta (apenas borda e pequeno indicador)
            if (info.event.extendedProps.color) {
                // Adicionar uma borda lateral colorida discreta
                info.el.style.borderLeft = `4px solid ${info.event.extendedProps.color}`;
                info.el.style.borderRadius = '4px';
                
                // Adicionar um pequeno indicador de cor no canto superior direito
                const colorIndicator = document.createElement('div');
                colorIndicator.style.position = 'absolute';
                colorIndicator.style.top = '2px';
                colorIndicator.style.right = '2px';
                colorIndicator.style.width = '8px';
                colorIndicator.style.height = '8px';
                colorIndicator.style.backgroundColor = info.event.extendedProps.color;
                colorIndicator.style.borderRadius = '50%';
                info.el.appendChild(colorIndicator);
                
                // Manter o fundo padrão do sistema
                info.el.classList.add('no-color');
            } else {
                // Se não houver cor definida, usar estilo padrão
                info.el.classList.add('no-color');
            }
        }
        });
        calendar.render();
    }

    function setupEventListeners() {
        // Seleção de cor
        $('.color-option').click(function() {
            $('.color-option').removeClass('selected');
            $(this).addClass('selected');
            const selectedColor = $(this).data('color');
            $('#color').val(selectedColor);
            
            // Visualização instantânea da cor selecionada
            if (selectedColor) {
                $('#eventModal .modal-header').css('border-bottom', `3px solid ${selectedColor}`);
            } else {
                $('#eventModal .modal-header').css('border-bottom', '');
            }
        });

        // Aplicar cor personalizada
        $('#applyCustomColor').click(function() {
            const customColor = $('#customColor').val();
            $('.color-option').removeClass('selected');
            $('#color').val(customColor);
            $('#eventModal .modal-header').css('border-bottom', `3px solid ${customColor}`);
        });

        // Formulário de evento
        $('#eventForm').submit(function(e) {
            e.preventDefault();
            saveEvent();
        });

        // Botões de ação
        $('#editEventBtn').click(function() {
            editEvent(currentEventId);
        });

        $('#deleteEventBtn').click(function() {
            deleteEvent(currentEventId);
        });
        
        // Reset do header do modal quando fechado
        $('#eventModal').on('hidden.bs.modal', function() {
            $('#eventModal .modal-header').css('border-bottom', '');
        });
        
        $('#viewEventModal').on('hidden.bs.modal', function() {
            $('#viewEventModal .modal-header').css('border-bottom', '');
        });
    }

    function viewEvent(event) {
        fetch(`/admin/calendar/events/${event.id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao carregar evento');
                }
                return response.json();
            })
            .then(data => {
                $('#viewTitle').text(data.event.title);
                $('#viewDescription').text(data.event.description || 'Sem descrição');
                $('#viewStartDate').text(new Date(data.event.start_date).toLocaleString('pt-BR'));
                $('#viewEndDate').text(new Date(data.event.end_date).toLocaleString('pt-BR'));
                $('#viewVisibility').text(data.event.visibility === 'public' ? 'Público' : 'Privado');
                $('#viewCreatedBy').text(data.created_by);
                
                // Aplicar cor do evento se existir
                if (data.event.color) {
                    $('#viewColorBadge').css({
                        'background-color': data.event.color,
                        'display': 'block'
                    });
                    $('#viewEventModal .modal-header').css('border-bottom', `3px solid ${data.event.color}`);
                } else {
                    $('#viewColorBadge').hide();
                    $('#viewEventModal .modal-header').css('border-bottom', '');
                }
                
                // Mostrar participantes
                $('#viewParticipants').empty();
                if (data.participants && data.participants.length > 0) {
                    data.participants.forEach(participant => {
                        $('#viewParticipants').append(`<li>${participant.name}</li>`);
                    });
                    $('#viewParticipantsSection').show();
                } else {
                    $('#viewParticipantsSection').hide();
                }
                
                // Mostrar/ocultar botões de ação baseado nas permissões
                @if(Auth::user()->is_admin || Auth::user()->hasPermission('edit_calendar_events'))
                $('#editEventBtn').show();
                @else
                $('#editEventBtn').hide();
                @endif
                
                @if(Auth::user()->is_admin || Auth::user()->hasPermission('delete_calendar_events'))
                $('#deleteEventBtn').show();
                @else
                $('#deleteEventBtn').hide();
                @endif
                
                currentEventId = event.id;
                $('#viewEventModal').modal('show');
            })
            .catch(error => {
                console.error('Erro:', error);
                showToast('Erro ao carregar evento', 'error');
            });
    }

    function createEvent(dateStr) {
        $('#eventForm')[0].reset();
        $('#eventId').val('');
        
        // Selecionar a opção sem cor por padrão
        $('.color-option').removeClass('selected');
        $('.color-option[data-color=""]').addClass('selected');
        $('#color').val('');
        $('#eventModal .modal-header').css('border-bottom', '');
        
        // Obter data/hora atual
        const now = new Date();
        
        // Definir data/hora de início como agora
        const startDate = new Date(now);
        
        // Definir data/hora de fim como uma hora depois
        const endDate = new Date(now);
        endDate.setHours(endDate.getHours() + 1);
        
        // Se foi clicado em uma data específica no calendário, usar essa data
        if (dateStr) {
            const clickedDate = new Date(dateStr);
            startDate.setFullYear(clickedDate.getFullYear());
            startDate.setMonth(clickedDate.getMonth());
            startDate.setDate(clickedDate.getDate());
            
            endDate.setFullYear(clickedDate.getFullYear());
            endDate.setMonth(clickedDate.getMonth());
            endDate.setDate(clickedDate.getDate());
        }
        
        $('#start_date').val(formatDateForInput(startDate));
        $('#end_date').val(formatDateForInput(endDate));
        
        $('#participants').val(null);
        $('#eventModal .modal-title').text('Novo Evento');
        $('#eventModal').modal('show');
    }

    function formatDateForInput(date) {
        // Ajustar para o fuso horário local
        const localDate = new Date(date.getTime() - (date.getTimezoneOffset() * 60000));
        return localDate.toISOString().slice(0, 16);
    }

    function editEvent(eventId) {
        fetch(`/admin/calendar/events/${eventId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao carregar evento para edição');
                }
                return response.json();
            })
            .then(data => {
                $('#eventId').val(data.event.id);
                $('#title').val(data.event.title);
                $('#description').val(data.event.description || '');
                
                // Format dates correctly for datetime-local input
                const startDate = new Date(data.event.start_date);
                const endDate = new Date(data.event.end_date);
                
                // Usar a função corrigida para formatar as datas
                $('#start_date').val(formatDateForInput(startDate));
                $('#end_date').val(formatDateForInput(endDate));
                
                // Definir cor do evento
                $('.color-option').removeClass('selected');
                if (data.event.color) {
                    // Verificar se a cor é uma das opções padrão
                    const matchingOption = $(`.color-option[data-color="${data.event.color}"]`);
                    if (matchingOption.length > 0) {
                        matchingOption.addClass('selected');
                    } else {
                        // Se for uma cor personalizada, definir no seletor de cor personalizada
                        $('#customColor').val(data.event.color);
                    }
                    $('#color').val(data.event.color);
                    $('#eventModal .modal-header').css('border-bottom', `3px solid ${data.event.color}`);
                } else {
                    $(`.color-option[data-color=""]`).addClass('selected');
                    $('#color').val('');
                    $('#eventModal .modal-header').css('border-bottom', '');
                }
                
                $('#visibility').val(data.event.visibility);
                
                // Definir participantes
                if (data.event.participants && data.event.participants.length > 0) {
                    $('#participants').val(data.event.participants);
                } else {
                    $('#participants').val(null);
                }
                
                $('#eventModal .modal-title').text('Editar Evento');
                $('#eventModal').modal('show');
                $('#viewEventModal').modal('hide');
            })
            .catch(error => {
                console.error('Erro:', error);
                showToast('Erro ao carregar evento para edição', 'error');
            });
    }

    function saveEvent() {
        const formData = $('#eventForm').serializeArray();
        const eventId = $('#eventId').val();
        const url = eventId ? `/admin/calendar/events/${eventId}` : '/admin/calendar/events';
        const method = eventId ? 'PUT' : 'POST';

        // Converter para objeto
        const data = {};
        formData.forEach(item => {
            if (item.name === 'participants[]') {
                if (!data.participants) data.participants = [];
                data.participants.push(item.value);
            } else {
                data[item.name] = item.value;
            }
        });

        // Se não há participantes, definir como array vazio
        if (!data.participants) {
            data.participants = [];
        }

        // CORREÇÃO: Garantir que o campo color seja enviado mesmo quando vazio
        // O valor deve ser uma string vazia se não houver cor selecionada
        if (typeof data.color === 'undefined') {
            data.color = '';
        }

        // Mostrar loading no botão
        const submitButton = $('#eventForm').find('button[type="submit"]');
        const originalText = submitButton.html();
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvando...');

        fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || 'Erro ao salvar evento');
                });
            }
            return response.json();
        })
        .then(responseData => {
            if (responseData.success) {
                $('#eventModal').modal('hide');
                showToast(responseData.message, 'success');
                
                // Recarregar eventos do calendário
                calendar.refetchEvents();
            } else {
                throw new Error(responseData.error || 'Erro ao salvar evento');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showToast(error.message, 'error');
        })
        .finally(() => {
            // Restaurar botão
            submitButton.prop('disabled', false).html(originalText);
        });
    }

    function deleteEvent(eventId) {
        if (confirm('Tem certeza que deseja excluir este evento?')) {
            // Mostrar loading no botão
            const deleteButton = $('#deleteEventBtn');
            const originalText = deleteButton.html();
            deleteButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Excluindo...');

            fetch(`/admin/calendar/events/${eventId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.message || 'Erro ao excluir evento');
                    });
                }
                return response.json();
            })
            .then(data => {
                $('#viewEventModal').modal('hide');
                showToast('Evento excluído com sucesso!', 'success');
                
                // Recarregar eventos do calendário
                calendar.refetchEvents();
            })
            .catch(error => {
                console.error('Erro:', error);
                showToast(error.message, 'error');
            })
            .finally(() => {
                // Restaurar botão
                deleteButton.prop('disabled', false).html(originalText);
            });
        }
    }

    function showToast(message, type = 'success') {
        // Remover toasts anteriores
        $('.alert-toast').remove();
        
        // Ícone baseado no tipo
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        
        // Criar toast
        const toast = $(`
            <div class="alert ${alertClass} alert-dismissible fade show alert-toast position-fixed" 
                style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                <i class="fas ${icon} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(toast);
        
        // Remover automaticamente após 5 segundos
        setTimeout(() => {
            toast.alert('close');
        }, 5000);
    }
</script>
@endpush