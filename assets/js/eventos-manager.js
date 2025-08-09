jQuery(document).ready(function($) {
    if ($('#eventos-calendario').length) {
        if (typeof $.fn.fullCalendar === 'undefined') {
            console.error('FullCalendar is not loaded.');
            return;
        }
        loadEventosCalendario();
    }

    function loadEventosCalendario() {
        $('#eventos-calendario').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            defaultView: 'month',
            editable: false,
            events: function(start, end, timezone, callback) {
                $.ajax({
                    url: eventosManager.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'get_eventos_calendario',
                        security: eventosManager.nonce,
                        start: start.format('YYYY-MM-DD'),
                        end: end.format('YYYY-MM-DD')
                    },
                    success: function(response) {
                        if (response.success) {
                            callback(response.data);
                        } else {
                            console.error('Failed to load calendar events:', response);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', error);
                    }
                });
            },
            eventColor: '#3498db',
            eventTextColor: '#ffffff',
            timeFormat: 'H:mm',
            locale: 'pt-br',
            eventRender: function(event, element) {
                element.find('.fc-title').prepend('<span class="evento-tipo-badge">' + event.tipo + '</span> ');
            }
        });
    }
});