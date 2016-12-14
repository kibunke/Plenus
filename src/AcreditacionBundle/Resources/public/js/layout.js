(function ($) {
    // no se sobreescribe el namespace, si ya existe  
    $.Default = $.Default || {};
    $.Default.init = function () {
        $('#acreditacion_next button').on('click', function (e) {
            var ok = true;
            $('#acreditacion_step [required="required"]').each(function () {
                if (typeof $(this).val() == 'undefinded' || $(this).val() == '' || $(this).val() == null)
                {
                    $(this).parentsUntil('div.form-group').addClass('has-error');
                    ok = false;
                }
            });
            if (ok) {
                $.Default.loading();
                $('#acreditacion-body form').submit();
            } else {
                e.preventDefault();
                toastr['warning']('Aún tiene campos obligatorios sin llenar o con datos erroneos.', 'Cuidado');
            }
        });
        $('#acreditacion_back button').on('click', function (e) {
            $.Default.loading();
        });
    };
    $.Default.loading = function () {
        var el = $('#acreditacionNav').parents(".panel");
        el.block({
            overlayCSS: {backgroundColor: '#fff'},
            message: '<i class="fa fa-spinner fa-spin"></i>',
            css: {border: 'none', color: '#333', background: 'none'}
        });
    };
})(jQuery);