(function ($) {
    var urlJasonEsp = '';
    var table;
    // no se sobreescribe el namespace, si ya existe  
    $.Acreditacion = $.Acreditacion || {};
    $.Acreditacion.init = function (urlJE) {
        urlJasonEsp = urlJE;
        $('#parametrosContent button').on('click', function (e) {
            var ok = true;
            $('#parametrosContent [required="required"]').each(function () {
                if (typeof $(this).val() == 'undefinded' || $(this).val() == '' || $(this).val() == null)
                {
                    $(this).parentsUntil('div.form-group').addClass('has-error');
                    ok = false;
                }
            });
            if (ok) {
                $.Default.loading();
                $('#parametrosContent form').submit();
            } else {
                e.preventDefault();
                toastr['warning']('Aún tiene campos obligatorios sin llenar o con datos erroneos.', 'Cuidado');
            }
        });
        //Se configura la fecha de nacimiento
        $('#juegosba_acreditacionbundle_juegosparametros_rangoFechaTrabajo').daterangepicker({
            format: 'DD/MM/YYYY',
            singleDatePicker: false,
            showDropdowns: true,
            startDate: moment(),
            endDate: moment().add(7, 'days'),
            locale: {
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
                fromLabel: 'Desde',
                toLabel: 'Hasta',
                daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                firstDay: 1
            }
        });

        $('#juegosba_acreditacionbundle_juegosparametros_fechaLimiteAcreditacion').daterangepicker({
            format: 'DD/MM/YYYY',
            singleDatePicker: true,
            showDropdowns: true,
            startDate: moment(),
            locale: {
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
                fromLabel: 'Desde',
                toLabel: 'Hasta',
                daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                firstDay: 1
            }
        });

        $('#juegosba_acreditacionbundle_juegosparametros_fechaLimiteHosteleria').daterangepicker({
            format: 'DD/MM/YYYY',
            singleDatePicker: true,
            showDropdowns: true,
            startDate: moment(),
            locale: {
                applyLabel: 'Aplicar',
                cancelLabel: 'Cancelar',
                fromLabel: 'Desde',
                toLabel: 'Hasta',
                daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                firstDay: 1
            }
        });

        table = $('table.acreditacionTablas').dataTable({
            "autoWidth": false,
            "language": {
                "url": urlJasonEsp
            },
            "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": [3]
                }],
            "aLengthMenu": [[5, 10, 20, 50, -1], [5, 10, 20, 50, "Todas"]], // change per page values here
            "order": [[0, 'asc']]
        });
    };


})(jQuery);