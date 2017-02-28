(function ($) {
    var exportTable = "#acreditacionTable";
    var ignoreColumn = null;
    // no se sobreescribe el namespace, si ya existe
    $.TableExport = $.TableExport || {};
    $.TableExport.init = function (url) {
        $(".export-pdf").on("click", function (e) {
            e.preventDefault();
            $(exportTable).tableExport({
                type: 'pdf',
                escape: 'false',
                ignoreColumn: [36]
                        //ignoreColumn: '[' + ignoreColumn + ']'
            });
        });
        $(".export-excel").on("click", function (e) {
            e.preventDefault();
            $(exportTable).tableExport({
                type: 'excel',
                escape: 'false',
                ignoreColumn: [36]
                        //ignoreColumn: '[' + ignoreColumn + ']'
            });
        });
        $(".export-doc").on("click", function (e) {
            e.preventDefault();
            $(exportTable).tableExport({
                type: 'doc',
                escape: 'false',
                ignoreColumn: [36]
                        //ignoreColumn: '[' + ignoreColumn + ']'
            });
        });
        $(".export-csv").on("click", function (e) {
            e.preventDefault();
            $(exportTable).tableExport({
                type: 'csv',
                escape: 'false',
                ignoreColumn: [36]
                        //ignoreColumn: '[' + ignoreColumn + ']'
            });
        });
        $(".export-txt").on("click", function (e) {
            e.preventDefault();
            $(exportTable).tableExport({
                type: 'txt',
                escape: 'false',
                ignoreColumn: [36]
                        //ignoreColumn: '[' + ignoreColumn + ']'
            });
        });
        $(".export-sql").on("click", function (e) {
            e.preventDefault();
            $(exportTable).tableExport({
                type: 'sql',
                escape: 'false',
                ignoreColumn: [36]
                        //ignoreColumn: '[' + ignoreColumn + ']'
            });
        });
        $(".export-json").on("click", function (e) {
            e.preventDefault();
            $(exportTable).tableExport({
                type: 'json',
                escape: 'false',
                ignoreColumn: [36]
                        //ignoreColumn: '[' + ignoreColumn + ']'
            });
        });
        table = $('#acreditacionTable').dataTable({
            "autoWidth": false,
            //paging: false,
            "language": {
                "url": url
            },
            "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": [36]
                }
            ],
            "aLengthMenu": [[20, 50, 75, 100, -1], [20, 50, 75, 100, "Todas"]], // change per page values here
            "order": [[0, 'asc']]
        });
    };
})(jQuery);

(function ($) {
    var url = '';
    // no se sobreescribe el namespace, si ya existe  
    $.Acreditados = $.Acreditados || {};
    $.Acreditados.init = function (anUrl) {
        url = anUrl;
    };
    $.Acreditados.delete = function (id) {
        swal({
            title: "Seguro quiere eliminar al Personal?",
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
                    data: {'id': id},
                    success: function (data) {
                        location.reload();
                    }
                });
            }
        });
    };
})(jQuery);