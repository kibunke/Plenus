(function ($) {
    // no se sobreescribe el namespace, si ya existe  
    $.DatosOperativo = $.DatosOperativo || {};
    $.DatosOperativo.init = function () {

        //Configurar switches
        $.fn.bootstrapSwitch.defaults.onText = 'SI';
        $.fn.bootstrapSwitch.defaults.offText = 'NO';
        $("#juegosba_acreditacionbundle_personaljuegos_datosOperativo_necesitaTransporte").bootstrapSwitch();
        $("#juegosba_acreditacionbundle_personaljuegos_datosOperativo_necesitaHospedaje").bootstrapSwitch();
        $("#juegosba_acreditacionbundle_personaljuegos_datosOperativo_certificado140908").bootstrapSwitch();
        $("#juegosba_acreditacionbundle_personaljuegos_datosOperativo_certificadoEstablecimientoPrivado").bootstrapSwitch();
        $("#juegosba_acreditacionbundle_personaljuegos_datosOperativo_certificadoLaboral").bootstrapSwitch();
        $("#juegosba_acreditacionbundle_personaljuegos_datosOperativo_esPersonalGestion").bootstrapSwitch();
        $("#juegosba_acreditacionbundle_personaljuegos_datosOperativo_vianda").bootstrapSwitch();
        $("#juegosba_acreditacionbundle_personaljuegos_activo").bootstrapSwitch();

        $('#juegosba_acreditacionbundle_personaljuegos_datosOperativo_necesitaHospedaje').on('switchChange.bootstrapSwitch', function (event, state) {
            if (state) {
                $('div.operativo_NH').show('slow');
                $('#juegosba_acreditacionbundle_personaljuegos_datosOperativo_rangoHospedaje').attr('required', 'required');
            } else {
                $('div.operativo_NH').hide('slow');
                $('#juegosba_acreditacionbundle_personaljuegos_datosOperativo_rangoHospedaje').removeAttr('required');
            }
        });
        $('#juegosba_acreditacionbundle_personaljuegos_datosOperativo_necesitaTransporte').on('switchChange.bootstrapSwitch', function (event, state) {
            if (state) {
                $('div.operativo_NT').show('slow');
                $('#juegosba_acreditacionbundle_personaljuegos_datosOperativo_rangoTransporte').attr('required', 'required');
            } else {
                $('div.operativo_NT').hide('slow');
                $('#juegosba_acreditacionbundle_personaljuegos_datosOperativo_rangoTransporte').removeAttr('required');
            }
        });
        //Se configura la fecha de nacimiento
      
        $('#juegosba_acreditacionbundle_personaljuegos_datosOperativo_rangoHospedaje').daterangepicker({
            format: 'DD/MM/YYYY',
            singleDatePicker: false,
            showDropdowns: true,
            //startDate: moment(),
            //endDate:moment().add(7,'days'),
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
        $('#juegosba_acreditacionbundle_personaljuegos_datosOperativo_rangoTransporte').daterangepicker({
            format: 'DD/MM/YYYY',
            singleDatePicker: false,
            showDropdowns: true,
            //startDate: moment(),
            //endDate:moment().add(7,'days'),
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
    };
})(jQuery);
