(function ($) {
    var table;
    urlExport = '';
    $.ListadoArea = $.ListadoArea || {};
    $.ListadoArea.init = function (url, urlExp) {
        var totales = [0, 0, 0, 0];
        var totales2 = [0, 0, 0, 0];
        urlExport = urlExp;
        table = $('#acreditacionTable').dataTable({
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
            "iDisplayLength": 25,
            "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                for (i = 0; i < 4; i++) {
                    totales[i] = totales[i] + parseInt($('td:eq(' + (i + 2) + ') strong.valor', nRow).text().split('.').join(''));
                    totales2[i] = totales2[i] + parseInt($('td:eq(' + (i + 2) + ') small.valor', nRow).text().split('.').join(''));
                }
            },
            "fnDrawCallback": function () {
                $tfoot = $('#acreditacionTable tfoot');
                for (i = 0; i < 4; i++) {
                    $tfoot.find("td:eq(" + (i + 1) + ") strong.valor").html(totales[i].toFixed(0).split('.').join('').replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1."));
                    $tfoot.find("td:eq(" + (i + 1) + ") small.valor").html(totales2[i].toFixed(0).split('.').join('').replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1."));
                }
                totales = [0, 0, 0, 0];
                totales2 = [0, 0, 0, 0];
            }
        });
    };
    $.ListadoArea.export = function () {
        var el = $('#acreditacionTable').parents(".panel");
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
        //el.unblock();
    };
})(jQuery);