(function ($) {
    // no se sobreescribe el namespace, si ya existe  
    $.DatosPersonales = $.DatosPersonales || {};
    $.DatosPersonales.init = function () {
        //Se configura la fecha de nacimiento
        /*$('#juegosba_acreditacionbundle_personaljuegos_datosPersonales_fechaNacimiento').daterangepicker({
         format: 'DD/MM/YYYY',
         singleDatePicker: true,
         showDropdowns: true,
         startDate: '01/01/1990',
         endDate: '01/01/1990',
         locale: {
         daysOfWeek: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
         monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
         firstDay: 1
         }
         });*/
        $('.dpersonalFecha').mask('00/00/0000',{placeholder: "__/__/____"});
    };
})(jQuery);
