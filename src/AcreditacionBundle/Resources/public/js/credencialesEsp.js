(function ($) {
    var table;
    urlExport = '';
    $.CredencialesEsp = $.CredencialesEsp || {};
    $.CredencialesEsp.cargar = function () {
        $('#juegosba_acreditacionbundle_credencialespecial_avatar_archivoInput').click();
    };
    $.CredencialesEsp.capturar = function () {
        $('#fotoCapturador').html('');
        $("#capturadorModal").modal('show');
    };
    $.CredencialesEsp.init = function (url) {
        $.fn.bootstrapSwitch.defaults.onText = 'SI';
        $.fn.bootstrapSwitch.defaults.offText = 'NO';
        $.fn.bootstrapSwitch.defaults.size = 'mini';
        $("[name='juegosba_acreditacionbundle_credencialespecial[accesoSector1]']").bootstrapSwitch();
        $("[name='juegosba_acreditacionbundle_credencialespecial[accesoSector2]']").bootstrapSwitch();
        $("[name='juegosba_acreditacionbundle_credencialespecial[accesoSector3]']").bootstrapSwitch();
        $("[name='juegosba_acreditacionbundle_credencialespecial[accesoSector4]']").bootstrapSwitch();
        $("[name='juegosba_acreditacionbundle_credencialespecial[accesoSector5]']").bootstrapSwitch();

        //configurar la carga dinamica de area
        $('#juegosba_acreditacionbundle_credencialespecial_area').change(function () {
            var $val = $('#juegosba_acreditacionbundle_credencialespecial_area').val();
            if ($val != '') {
                $("#juegosba_acreditacionbundle_credencialespecial_funcion").html('<option selected="selected" value="">Cargando...</option>');
                $("#juegosba_acreditacionbundle_credencialespecial_funcion").load(url, {'idArea': $val}, function () {});
            }
        });

        $('#capturadorModal').on('shown.bs.modal', function (event) {
            $("#fotoCapturador").photobooth().on("image", function (event, dataUrl) {
                $("#contenedorAvatar").html('<img id="personal_avatar" src="' + dataUrl + '" >');
                $('#avatarCaptureContainer textarea:first-child').html(dataUrl.substr(22));
            });
        });
        //Se configura la carga de la imagen
        $('#juegosba_acreditacionbundle_credencialespecial_avatar_archivoInput').change(function () {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var img = $('<img id="personal_avatar" src="" alt="Avatar" />');
                    img.attr('src', e.target.result);
                    $('#contenedorAvatar').html(img);
                };
                reader.readAsDataURL(this.files[0]);
            }
            //Resetear el contenidod e la captura
            $('#avatarCaptureContainer textarea:first-child').html('');
        });
        $('button:submit').on('click', function (e) {
            var ok = true;
            $('[required="required"]').each(function () {
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
    $.CredencialesEsp.initTable = function (url, urlExp) {
        var totales = [0, 0, 0, 0];
        var totales2 = [0, 0, 0, 0];
        urlExport = urlExp;
        table = $('#credencialesEspTable').dataTable({
            "autoWidth": false,
            "language": {
                "url": url
            },
            "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": [6]
                }],
            "aLengthMenu": [[25, 50, 75, 100, -1], [25, 50, 75, 100, "Todas"]], // change per page values here
            "order": [[0, 'asc']],
            "iDisplayLength": 25
        });
    };
    $.CredencialesEsp.export = function () {
        var el = $('#credencialesEspTable').parents(".panel");
        el.block({
            overlayCSS: {backgroundColor: '#fff'},
            message: '<i class="fa fa-spinner fa-spin"></i>',
            css: {border: 'none', color: '#333', background: 'none'}
        });
        $.get(urlExport, function (data) {
            //$("#acreditacionTableExport").tableExport({
            var div = $('<div id="contenedorExport"></div>');
            div.html(data);
            $("#blockContenidoContent").append(div);
            $("#contenedorExport").append(data);
            $("#acreditacionTableExport").tableExport({
                type: 'excel',
                escape: 'false'
            });
            el.unblock();
        });
    };
    $.CredencialesEsp.delete = function (url) {
        swal({
            title: "Seguro quiere eliminar la Credencial?",
            text: "Los cambios serán permanetes!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Si, eliminar!",
            cancelButtonText: "No, cancelar!",
            closeOnConfirm: false,
            closeOnCancel: true,
            html: true
        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    method: "POST",
                    url: url,
                    dataType: 'json',
                    beforeSend: function (xhr) {
                        $('#modalLoading').modal('show');
                    },
                    success: function (data) {
                        location.reload();
                    }
                });
            }
        });
    };
})(jQuery);