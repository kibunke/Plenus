(function ($) {
    // no se sobreescribe el namespace, si ya existe  
    $.DatosTesoreria = $.DatosTesoreria || {};
    $.DatosTesoreria.init = function (url) {

        //configurar e select de empleado provincial
        $('#juegosba_acreditacionbundle_personaljuegos_datosTesoreria_empleadoPublico').select2();
        //configurar el select de partido
        $('#juegosba_acreditacionbundle_personaljuegos_datosTesoreria_pagoProvincia').select2();
        $('#juegosba_acreditacionbundle_personaljuegos_datosTesoreria_pagoPartido').select2();

        //Confguracion del detalle del empleado publico de ser seleccionado
        $('#juegosba_acreditacionbundle_personaljuegos_datosTesoreria_empleadoPublico').on('change', function () { 
            if ($(this).find("option:selected").text() == 'NO') {
                $('div.tesoreria_EP').hide("slow");
                $('#juegosba_acreditacionbundle_personaljuegos_datosTesoreria_legajo').val('');
                $('#juegosba_acreditacionbundle_personaljuegos_datosTesoreria_cbu').val('');
                $('#juegosba_acreditacionbundle_personaljuegos_datosTesoreria_legajo').removeAttr('required');
                $('#juegosba_acreditacionbundle_personaljuegos_datosTesoreria_cbu').removeAttr('required');
            } else {
                $('div.tesoreria_EP').show("slow");
                $('#juegosba_acreditacionbundle_personaljuegos_datosTesoreria_legajo').attr('required', 'required');
                $('#juegosba_acreditacionbundle_personaljuegos_datosTesoreria_cbu').attr('required', 'required');
            }
        });

        //configuracion del pago especifico de ser seleccionado
        $('#juegosba_acreditacionbundle_personaljuegos_datosTesoreria_categoriaPago').on('change', function () {
            var categoria = $(this).find("option:selected").text();
            var categoriaId = $(this).val();
            $('#tesoreria_flecha_pe').hide('slow');
            $('div.tesoreria_PE:last-child').hide("slow", function () {
                $('#acreditacion_pagoEspecifico_monto').hide();
                $('#acreditacion_pagoEspecifico_input').hide();
                if (categoria === '6') {
                    $('#juegosba_acreditacionbundle_personaljuegos_datosTesoreria_pagoEspecifico').attr('required', 'required');
                    $('#acreditacion_pagoEspecifico_input').show();
                    $('div.tesoreria_PE').show("slow");
                } else {
                    $('#juegosba_acreditacionbundle_personaljuegos_datosTesoreria_pagoEspecifico').removeAttr('required');
                    //$('#juegosba_acreditacionbundle_personaljuegos_datosTesoreria_pagoEspecifico').val('');
                    if (categoriaId !== '') {
                        $.post(url, {"id": categoriaId}, function (data) {
                            $('#acreditacion_pagoEspecifico_monto span:first-child').html(data);
                            $('#acreditacion_pagoEspecifico_monto').show();
                            $('div.tesoreria_PE').show("slow");
                        });
                    }
                }
            });
        });
    };
})(jQuery);
       