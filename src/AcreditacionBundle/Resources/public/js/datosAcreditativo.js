/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($) {
    // no se sobreescribe el namespace, si ya existe  
    $.DatosAcreditativo = $.DatosAcreditativo || {};
    $.DatosAcreditativo.cargar = function () {
        $('#juegosba_acreditacionbundle_personaljuegos_avatar_archivoInput').click();
    };
    $.DatosAcreditativo.capturar = function () {
        $('#fotoCapturador').html('');
        $("#capturadorModal").modal('show');
    };
    $.DatosAcreditativo.init = function (url) {
        $('#capturadorModal').on('shown.bs.modal', function (event) {
            $("#fotoCapturador").photobooth().on("image", function (event, dataUrl) {
               $("#contenedorAvatar").html('<img id="personal_avatar" src="' + dataUrl + '" >');
               $('#avatarCaptureContainer textarea:first-child').html(dataUrl.substr(22));
            });
        });

        //configurar la carga dinamica de area
        $('#juegosba_acreditacionbundle_personaljuegos_area').change(function () {
            var $val = $('#juegosba_acreditacionbundle_personaljuegos_area').val();
            if ($val != '') {
                $("#juegosba_acreditacionbundle_personaljuegos_funcion").html('<option selected="selected" value="">Cargando...</option>');
                $("#juegosba_acreditacionbundle_personaljuegos_funcion").load(url, {'idArea': $val}, function () {});
            }
        });
        //Se configura la carga de la imagen
        $('#juegosba_acreditacionbundle_personaljuegos_avatar_archivoInput').change(function () {
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
        //Configurar switches
        $.fn.bootstrapSwitch.defaults.onText = 'SI';
        $.fn.bootstrapSwitch.defaults.offText = 'NO';
        $.fn.bootstrapSwitch.defaults.size = 'mini';
        $("[name='juegosba_acreditacionbundle_personaljuegos[accesoSector1]']").bootstrapSwitch();
        $("[name='juegosba_acreditacionbundle_personaljuegos[accesoSector2]']").bootstrapSwitch();
        $("[name='juegosba_acreditacionbundle_personaljuegos[accesoSector3]']").bootstrapSwitch();
        $("[name='juegosba_acreditacionbundle_personaljuegos[accesoSector4]']").bootstrapSwitch();
        $("[name='juegosba_acreditacionbundle_personaljuegos[accesoSector5]']").bootstrapSwitch();
    };
})(jQuery);
