/*
Author: Deep
File: Datatables Js File
*/

$(document).ready(function () {
    $('#datatable').DataTable();

    // Buttons examples
    var table = $('#datatable-buttons').DataTable({
        lengthChange: false,
        buttons: ['copy', 'excel', 'pdf', 'colvis']
    });

    table.buttons().container().appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
    $(".dataTables_length select").addClass('form-select form-select-sm');
});

function destroyDataTable(tabId) {
    $('#' + tabId).DataTable().destroy();
}

//Added by DEEP BASAK on November 11, 2024
//For Dynamic datatable
function serverSideDataTable(tableId, ajaxUrl, num_of_row, callback = '', formData = ''){

    if ($.fn.dataTable.isDataTable('#' + tableId)) {
        destroyDataTable(tableId)
    }

    if (formData == '') {
        var formData = {
            [$("#token_name").val()]: $("#token_hash").val()
        }
    } else {
        formData[[$('#token_name').val()]] = $('#token_hash').val();
    }

    $("#" + tableId).DataTable({
        dom: "Bfrtip",
        buttons: [
            {
                extend: "copyHtml5",
                text: '<i class="fa fa-copy"></i> Copy',
                titleAttr: "Copy",
            },
            {
                extend: "excelHtml5",
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                titleAttr: "Excel",
            },
            {
                extend: "pdfHtml5",
                text: '<i class="fa fa-file-pdf-o"></i> Pdf',
                titleAttr: "Pdf",
                orientation: "landscape",
                pageSize: "A4",
            }
        ],
        pageLength: num_of_row,
        order: [],
        ajax: {
            data: formData,
            url: ajaxUrl,
            type: "POST",
            beforeSend: function () {
                $('.page_loader').show();
            },
            complete: function (response) {
                $("#token_name").val(response.responseJSON.csrf.csrfName);
                $("#token_hash").val(response.responseJSON.csrf.csrfHash);
                $('.page_loader').hide();
            },
        },
        drawCallback: function () {
            if (callback != "") {
                callback();
            }
        },
        columnDefs: [
            {
                targets: [0],
                orderable: false,
            },
        ],
        processing: true,
        serverSide: true
    });

    $(".dataTables_length select").addClass('form-select form-select-sm');

    $("#"+tableId+"_wrapper").removeClass("table-loading");     //Added by DEEP BASAK on January 09, 2024
}

//Added by DEEP BASAK on November 01, 2023
//For static datatable
function staticDataTable(tableId, columnExport){
    if ($.fn.dataTable.isDataTable('#' + tableId)) {
        destroyDataTable(tabId)
    }

    $("#"+tableId).DataTable({
        dom: "Bfrtip",
        buttons: [
            {
                extend: "copyHtml5",
                text: '<i class="fa fa-copy"></i> Copy',
                titleAttr: "Copy",
                exportOptions: {
                    columns: columnExport // Specify the columns to include (0-based index)
                },
            },
            {
                extend: "excelHtml5",
                text: '<i class="fa fa-file-excel-o"></i> Excel',
                titleAttr: "Excel",
                exportOptions: {
                    columns: columnExport // Specify the columns to include (0-based index)
                },
            },
            {
                extend: "pdfHtml5",
                text: '<i class="fa fa-file-pdf-o"></i> Pdf',
                titleAttr: "Pdf",
                orientation: "landscape",
                pageSize: "A4",
                exportOptions: {
                    columns: columnExport // Specify the columns to include (0-based index)
                },
            },
        ],
        order: [],
        columnDefs: [
            {
                targets: [0],
                orderable: false,
            },
        ],
    });

    $(".dataTables_length select").addClass('form-select form-select-sm');

    $("#"+tableId+"_wrapper").removeClass("table-loading");     //Added by DEEP BASAK on January 09, 2024
}
