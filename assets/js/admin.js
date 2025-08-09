jQuery(document).ready(function($) {
    $('#data_evento').on('change', function() {
        var date = $(this).val();
        if (!date) {
            alert('Por favor, selecione uma data válida.');
            $(this).focus();
        }
    });

    $('#hora_evento').on('change', function() {
        var time = $(this).val();
        if (time && !/^\d{2}:\d{2}$/.test(time)) {
            alert('Por favor, insira uma hora válida no formato HH:MM.');
            $(this).val('');
            $(this).focus();
        }
    });
});