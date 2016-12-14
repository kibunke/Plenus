(function ($) {
    var exportTable = "#listadoTable";
    var ignoreColumn = 0;
    // no se sobreescribe el namespace, si ya existe
    $.TableExport = $.TableExport || {};
    $.TableExport.init = function (url) {
        $(".export-pdf").on("click", function (e) {
            e.preventDefault();
            $(exportTable).tableExport({
                type: 'pdf',
                escape: 'false',
                ignoreColumn: '[' + ignoreColumn + ']'
            });
        });
        $(".export-excel").on("click", function (e) {
            e.preventDefault();
            $(exportTable).tableExport({
                type: 'excel',
                escape: 'false',
                ignoreColumn: '[' + ignoreColumn + ']'
            });
        });
        $(".export-doc").on("click", function (e) {
            e.preventDefault();
            $(exportTable).tableExport({
                type: 'doc',
                escape: 'false',
                ignoreColumn: '[' + ignoreColumn + ']'
            });
        });
        $(".export-csv").on("click", function (e) {
            e.preventDefault();
            $(exportTable).tableExport({
                type: 'csv',
                escape: 'false',
                ignoreColumn: '[' + ignoreColumn + ']'
            });
        });
        $(".export-txt").on("click", function (e) {
            e.preventDefault();
            $(exportTable).tableExport({
                type: 'txt',
                escape: 'false',
                ignoreColumn: '[' + ignoreColumn + ']'
            });
        });
        $(".export-sql").on("click", function (e) {
            e.preventDefault();
            $(exportTable).tableExport({
                type: 'sql',
                escape: 'false',
                ignoreColumn: '[' + ignoreColumn + ']'
            });
        });
        $(".export-json").on("click", function (e) {
            e.preventDefault();
            $(exportTable).tableExport({
                type: 'json',
                escape: 'false',
                ignoreColumn: '[' + ignoreColumn + ']'
            });
        });
        table = $('#listadoTable').dataTable({
            //paging: false,
            "language": {
                "url": url
            },
            "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": [ignoreColumn]
                }],
            "aLengthMenu": [[20, 50, 75, 100, -1], [20, 50, 75, 100, "Todas"]], // change per page values here
            "order": [[0, 'asc']]
        });
    };
})(jQuery);
