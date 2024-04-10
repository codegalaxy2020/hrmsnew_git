<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <?php if(has_permission('hrm','','create')){ ?>
                        <div class="_buttons">
                            
                            <a href="<?php echo admin_url('hrm/insurance'); ?>" class="btn btn-info mright5 pull-left display-block"><?php echo _l('add_insurrance'); ?></a>

                        </div>

                        <div class="clearfix"></div>
                        <br>

                        <?php } ?>

                        <div class="row filter_by">

                           <?php  $pro = $this->hrm_model->get_staff();?>
                           <div  class="col-md-3 leads-filter-column pull-left">
                          
                            </div> 
                            
                        </div>
                        <br>
                        <div class="row">
                           <div class="col-md-12" id="small-table">
                              <div class="panel_s">
                                 <div class="panel-body">
                                <div class="clearfix"></div>

                                 <!-- if hrmcontract id found in url -->
                                 <div class="tab-content">
                                 <!-- start -->
                                 <div role="tabpanel" class="tab-pane active" id="tab_list_insurance">
                                    <table id="table_insurance" class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Insurance Book Number</th>
                                                <th>Health Insurance Number</th>
                                                <th>Amount</th>
                                                <th>Approved Amount</th>
                                                <th>Actions</th> <!-- Added for edit and delete buttons -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Data will be populated here via AJAX -->
                                        </tbody>
                                    </table>
                                </div>


                                
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-7 small-table-right-col">
                              <div id="hrm_contract" class="hide">
                              </div>
                           </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approveModalLabel">Edit Approved Amount</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="approvedAmountForm">
                    <div class="form-group">
                        <label for="approvedAmount">Approved Amount:</label>
                        <input type="number" class="form-control" id="approvedAmount" name="approvedAmount">
                        <input type="hidden" id="approveModalid" name="approveModalid">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveApprovedAmount">Save changes</button>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>

$(function(){
    var tree_dep = $('#hrm_derpartment_tree').comboTree({
              source : <?php echo ''.$dep_tree; ?>
            });
    var ContractsServerParams = {
        "hrm_deparment": "input[name='hrm_deparment']",
        "hrm_staff"    : "select[name='staff[]']",
        "hrm_from_month"    : "select[name='from_month[]']",
     };

    table_insurance = $('table.table-table_insurance');
    initDataTable(table_insurance,admin_url + 'hrm/table_insurance', undefined, undefined, ContractsServerParams);

    $('#hrm_derpartment_tree').on('change', function() {
                $('#hrm_deparment').val(tree_dep.getSelectedItemsId());
                table_insurance.DataTable().ajax.reload()
                    .columns.adjust()
                    .responsive.recalc();
      });
    $('#staff').on('change', function() {
                table_insurance.DataTable().ajax.reload().columns.adjust().responsive.recalc();
      });
    $('#from_month').on('change', function() {
                table_insurance.DataTable().ajax.reload().columns.adjust().responsive.recalc();
      });

});

    
</script>
<script>
    $(document).ready(function() {
        // Fetch data using AJAX when the document is ready
        $.ajax({
            url: '<?php echo admin_url("hrm/table_insurance"); ?>',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Populate table with fetched data
                console.log(data);
                $.each(data, function(index, item) {
                    $('#table_insurance tbody').append(`
                        <tr>
                            <td>${item.insurance_book_num}</td>
                            <td>${item.health_insurance_num}</td>
                            <td>${item.amount}</td>
                            <td style="color: #ff0909;">${item.approved_amount ? item.approved_amount : "Waiting for admin approval"}</td>

                            <td>
                            <?php if (is_admin()) { ?>
                            <button class="btn btn-primary btn-sm approve-insurance" data-toggle="modal" data-target="#approveModal" data-approved-amount="${item.approved_amount}" data-insurance-id="${item.insurance_id}">Approved</button>
                            <?php } ?>
                            <button class="btn btn-primary btn-sm edit-insurance" data-toggle="modal" data-target="#editModal" data-insurance-id="${item.insurance_id}">Edit</button>
                            <button class="btn btn-danger btn-sm delete-insurance" data-toggle="modal" data-target="#deleteModal" data-insurance-id="${item.insurance_id}">Delete</button>
                            </td>
                        </tr>
                    `);
                });
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
    $('#table_insurance').on('click', '.edit-insurance', function() {
    var insuranceId = $(this).data('insurance-id');
    // Redirect to edit page with insurance ID
    window.location.href = '<?php echo admin_url("hrm/insurance/") ?>' + insuranceId;
});

// Handle delete button click event
$('#table_insurance').on('click', '.delete-insurance', function() {
    var insuranceId = $(this).data('insurance-id');
    
    // Perform AJAX request to delete the insurance item
    $.ajax({
        url: '<?php echo admin_url("hrm/delete_insurance/") ?>' + insuranceId,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            // Handle success response
            if (response.success) {
                // Refresh the page or update the table
                location.reload();
            } else {
                // Handle error response
                console.error(response.message);
                alert("Failed to delete item: " + response.message);
            }
        },
        error: function(xhr, status, error) {
            // Handle error
            console.error(error);
            alert("Failed to delete item");
        }
    });
});


</script>
<script>
    $(document).ready(function() {
        $(document).on('click', '.approve-insurance', function() {
            // alert('ggg');
            var insuranceId = $(this).data('insurance-id');
            var approved = $(this).data('approved-amount');
            $('#approveModalid').val(insuranceId);
            $('#approvedAmount').val(approved);
        });

        $('#saveApprovedAmount').click(function(e) {
            e.preventDefault(); 
            var approvedAmount = $('#approvedAmount').val();
            var insuranceId = $('#approveModalid').val();

            // AJAX call to submit data
            $.ajax({
                url: '<?php echo admin_url("hrm/update_insorance"); ?>',
                method: 'POST',
                data: {
                    approvedAmount: approvedAmount,
                    insuranceId: insuranceId
                },
                success: function(response) {
                    console.log(response);
                    $('#approveModal').modal('hide');
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });
    });
</script>
</body>
</html>
