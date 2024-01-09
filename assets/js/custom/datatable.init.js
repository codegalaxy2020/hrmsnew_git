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

//Added by DEEP BASAK on November 01, 2023
//For static datatable
function staticDataTable(tableId){
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
