jQuery(document).ready(function($) {
    // Itera sobre cada instância de calendário na página
    $('.em-calendario-wrapper').each(function() {
        const calendarWrapper = $(this);
        let currentDate = new Date();

        const header = calendarWrapper.find('.em-mes-ano');
        const weekDaysContainer = calendarWrapper.find('.em-dias-semana');
        const daysGrid = calendarWrapper.find('.em-dias-grid');
        const calendarView = calendarWrapper.data('view');

        // Nomes para internacionalização
        const monthNames = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
        const weekDayNames = ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb"];

        function renderCalendar() {
            daysGrid.html(''); // Usar .html('') é um pouco mais rápido que .empty()
            weekDaysContainer.empty();

            const month = currentDate.getMonth();
            const year = currentDate.getFullYear();

            // Define o cabeçalho (Mês Ano)
            header.text(monthNames[month] + ' ' + year);

            // Renderiza os dias da semana
            weekDayNames.forEach(day => {
                weekDaysContainer.append(`<span>${day}</span>`);
            });

            const firstDayOfMonth = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            // Preenche os dias vazios no início do mês
            for (let i = 0; i < firstDayOfMonth; i++) {
                daysGrid.append('<div class="em-dia-celula em-other-month"></div>');
            }

            // Renderiza os dias do mês
            for (let i = 1; i <= daysInMonth; i++) {
                const dayCell = $(`<div class="em-dia-celula" data-date="${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}"><span>${i}</span></div>`);
                
                // Marca o dia de hoje
                const today = new Date();
                if (i === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                    dayCell.addClass('today');
                }
                daysGrid.append(dayCell);
            }

            loadEventsForMonth(year, month);
        }

        function loadEventsForMonth(year, month) {
            const startDate = `${year}-${String(month + 1).padStart(2, '0')}-01`;
            const endDate = new Date(year, month + 1, 0).toISOString().split('T')[0];

            $.ajax({
                url: eventosManager.ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_eventos_calendario',
                    security: eventosManager.nonce,
                    start: startDate,
                    end: endDate
                },
                success: function(response) {
                    if (response.success) {
                        markEventsOnCalendar(response.data);
                    }
                },
                error: function() {
                    console.error('Erro ao carregar eventos.');
                }
            });
        }

        function markEventsOnCalendar(events) {
            // 1. Agrupar eventos por data
            const eventsByDate = events.reduce((acc, event) => {
                const eventDate = event.start.split('T')[0];
                if (!acc[eventDate]) {
                    acc[eventDate] = [];
                }
                acc[eventDate].push(event);
                return acc;
            }, {});

            // 2. Limpar eventos antigos e renderizar os novos
            daysGrid.find('.em-dia-celula').each(function() {
                const cell = $(this);
                const date = cell.data('date');
                
                // Limpa conteúdo de eventos anteriores
                cell.find('.em-event-list').remove();
                cell.removeClass('has-event');

                if (eventsByDate[date]) {
                    cell.addClass('has-event');
                    if (calendarView === 'full') {
                        const eventList = $('<div class="em-event-list"></div>');
                        eventsByDate[date].forEach(event => {
                            const eventEl = $(`<a href="${event.url}" class="em-event"></a>`);
                            eventEl.text(event.title);
                            eventList.append(eventEl);
                        });
                        cell.append(eventList);
                    } else if (calendarView === 'widget') {
                        // Para o widget, torna o dia clicável, levando ao primeiro evento do dia.
                        const eventUrl = eventsByDate[date][0].url;
                        const daySpan = cell.find('span');
                        daySpan.wrap(`<a href="${eventUrl}"></a>`);
                    }
                }
            });
        }


        // Navegação
        calendarWrapper.find('.em-nav-btn').on('click', function() {
            const direction = $(this).data('nav');
            const currentMonth = currentDate.getMonth();

            if (direction === 'prev') {
                currentDate.setMonth(currentMonth - 1);
            } else if (direction === 'next') {
                currentDate.setMonth(currentMonth + 1);
            } else if (direction === 'today') {
                currentDate = new Date();
            }
            renderCalendar();
        });

        // Botão de Tela Cheia
        calendarWrapper.find('.em-view-btn[data-view="fullscreen"]').on('click', function() {
            calendarWrapper.toggleClass('em-fullscreen-mode');
            $('body').toggleClass('em-fullscreen-active');

            if (calendarWrapper.hasClass('em-fullscreen-mode')) {
                // Adiciona botão de fechar
                calendarWrapper.append('<button class="em-close-fullscreen-btn">&times;</button>');
            } else {
                // Remove botão de fechar
                calendarWrapper.find('.em-close-fullscreen-btn').remove();
            }
        });

        // Fechar tela cheia com o botão 'X' (evento delegado)
        calendarWrapper.on('click', '.em-close-fullscreen-btn', function() {
            calendarWrapper.removeClass('em-fullscreen-mode');
            $('body').removeClass('em-fullscreen-active');
            $(this).remove();
        });

        // Renderização inicial
        renderCalendar();
    });
});