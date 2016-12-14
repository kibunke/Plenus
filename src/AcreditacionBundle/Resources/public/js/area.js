(function ($) {
    function totalCatPag() {
        var aux = 0;
        $('table td input.cpCupos').each(function () {
            aux += parseInt($(this).val());
        });
        return aux;
    }

    var urlDelete = '';
    // no se sobreescribe el namespace, si ya existe  
    $.Area = $.Area || {};
    $.Area.initMask = function () {
        $('.money').mask('00000000', {reverse: true});
        $('.areaContador').mask('000', {reverse: true});
    };
    $.Area.init = function (url) {
        //Inizializacion de los botones
        $('table div.row div.areaCuposButtonMinus').on('click', function () {
            var elem = $(this).next().find('input');
            if (parseInt(elem.val()) > 0) {
                elem.val(parseInt(elem.val()) - 1);
            }
        });
        $('table div.row div.areaCuposButtonPlus').on('click', function () {
            var elem = $(this).prev().find('input');
            if (elem.hasClass('aCupos')) {
                if (parseInt(elem.val()) < 999) {
                    elem.val(parseInt(elem.val()) + 1);
                }
            }
            if (elem.hasClass('htCupos')) {
                if (parseInt(elem.val()) === parseInt($('table td.acreditacionCupoContainer input').val())) {
                    toastr['error']('Se ha alcanzado el Cupo Máximo de acreditación del personal.', 'Cuidado');
                }
                if ((parseInt(elem.val()) < 999) && (parseInt(elem.val()) < parseInt($('table td.acreditacionCupoContainer input').val()))) {
                    elem.val(parseInt(elem.val()) + 1);
                }
            }
            if (elem.hasClass('cpCupos')) {
                var total = totalCatPag();
                if (totalCatPag() === parseInt($('table td.acreditacionCupoContainer input').val())) {
                    toastr['error']('La suma de los cupos de Categorías de Pago ha alcanzado el Máximo de acreditación del personal.', 'Cuidado');
                }
                if ((parseInt(elem.val()) < 999) && (totalCatPag() < parseInt($('table td.acreditacionCupoContainer input').val()))) {
                    elem.val(parseInt(elem.val()) + 1);
                }
            }
        });
        urlDelete = url;
        $('#juegosba_acreditacionbundle_area_submit').on('click', function (e) {
            var ok = true;
            $('form [required="required"]').each(function () {
                if (typeof $(this).val() == 'undefinded' || $(this).val() == '' || $(this).val() == null)
                {
                    $(this).parentsUntil('div.form-group').addClass('has-error');
                    ok = false;
                }
            });
            if (ok) {
                $.Default.loading();
                $('form').submit();
            } else {
                e.preventDefault();
                toastr['warning']('Aún tiene campos obligatorios sin llenar o con datos erroneos.', 'Cuidado');
            }
        });
    };
    $.Area.delete = function (id) {
        swal({
            title: "Seguro quiere eliminar el Área?",
            text: "Los cambios serán permanetes!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Si, eliminar!",
            cancelButtonText: "No, cancelar!",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    // la URL para la petición
                    url: urlDelete,
                    // la información a enviar
                    // (también es posible utilizar una cadena de datos)
                    data: {"id": id},
                    beforeSend: function (xhr) {
                        $('#modalLoading').modal('show');
                    },
                    // especifica si será una petición POST o GET
                    type: 'POST',
                    // el tipo de información que se espera de respuesta
                    dataType: 'json',
                    // código a ejecutar si la petición es satisfactoria;
                    // la respuesta es pasada como argumento a la función
                    success: function (data) {
                        location.reload();
                    }
                });
            }
        });
    };
})(jQuery);