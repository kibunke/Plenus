(function ($) {
    var urlDelete = '';
    $.FuncionJuegos = $.FuncionJuegos || {};
    $.FuncionJuegos.init = function (url) {
        urlDelete = url;
        $('#juegosba_acreditacionbundle_funcionjuegos_submit').on('click', function (e) {
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
    $.FuncionJuegos.delete = function (id) {
        swal({
            title: "Seguro quiere eliminar la Función?",
            text: "Los cambios serán permanetes!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Si, eliminar!",
            cancelButtonText: "No, cancelar!",
            closeOnConfirm: true,
            closeOnCancel: true,
            html: true
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
