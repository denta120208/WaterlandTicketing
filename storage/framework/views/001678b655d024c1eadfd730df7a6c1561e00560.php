<?php $__env->startSection('navbar_header'); ?>
    Form List Invoice - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form List Invoice
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php if(Session::has('message')): ?>
    <div class="alert alert-success" id="success-alert">
        <?php echo e(Session::get('message')); ?>

    </div>
<?php elseif(Session::has('errorFailed')): ?>
    <div class="alert alert-danger" id="success-alert">
        <?php echo e(Session::get('errorFailed')); ?>

    </div>
<?php endif; ?>

<style>
th, td {
    padding: 5px;
}

.vertical-text {
    float: left;
    transform: rotate(270deg);
    padding: 1em;
    font-size: 1em;
    color: black;
}
</style>

<script type="text/javascript">
    $(function(){
        var table = $('#type_inv_table').DataTable({
            order:[]
        });

        $('#type_inv_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('type_inv').value = checkEmptyStringValidation(data[0]);
            document.getElementById('type_inv_char').value = checkEmptyStringValidation(data[1]);
            $('#typeinvModal').modal('hide');
        });

        $('#type_inv_char').on('click',function(){
            $('#typeinvModal').modal('show');
        });
    });

    function checkEmptyStringValidation(arr){
        var dataArr;

        if(arr == null){
             return dataArr = "";
        }else{
            return dataArr = arr;
        }
    }
</script>
<script type="text/javascript">
    function filterColumn ( i )
    {
         $('#billingpayment_sales_report').DataTable().column( i ).search(
                $('#col'+i+'_filter').val()
                ).draw();
    }

     $(function()
    {
        $( "#startDate" ).datepicker({
              dateFormat: "yy-mm-dd",
              changeMonth: true,
              changeYear: true,
              onClick : function(date){
               document.getElementById('startDate').value = date;
              }
        });
    });

    $(function()
    {
        $( "#endDate" ).datepicker({
             dateFormat: "yy-mm-dd",
              changeMonth: true,
              changeYear: true,
               onClick : function(date){
               document.getElementById('endDate').value = date;
              }

        });
    });

    $(document).ready(function() {
       $.fn.DataTable.ext.search.push(
            function(settings, data, dataindex) {
                var startDate = Date.parse($('#startDate').val());
                var endDate = Date.parse($('#endDate').val());
                var dateColumn = Date.parse( data[0] ) || 0; // use data for the date column

                if ( ( isNaN( startDate ) && isNaN( endDate ) ) ||
                    ( isNaN( startDate ) && dateColumn <= endDate ) ||
                    ( startDate <= dateColumn   && isNaN( endDate ) ) ||
                    ( startDate <= dateColumn   && dateColumn <= endDate ) )
                {
                    return true;
                }
                    return false;
            }
        );

       $('#tdp_report').DataTable( {
           order : [],
           scrollX : "1500px",
           pageLength : 25,
           dom: 'Bfrtip',
           buttons: [
               {
                   extend: 'excelHtml5',
                   title: '<?php echo "Data Invoice "; ?>'
               },
               {
                   extend: 'pdfHtml5',
                   footer: true,
                   title: '<?php echo "Data Invoice "; ?>'
               }
           ]
       });
    });
</script>

<div class="modal fade" id="edit-pph-modal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Change PPH Status</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form method="POST" action="<?php echo e(route('invoice.changepphstatusinvoice')); ?>">
            <?php echo csrf_field(); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-5">PPH Status <span style="color: red;">*</span></label>
                    <div class="col-sm-12">
                        <input type="hidden" class="form-control" name="INVOICE_ID" id="INVOICE_ID" style="width: 100%;" readonly />
                        <input type="hidden" class="form-control" name="INVOICE_TRANS_NOCHAR" id="INVOICE_TRANS_NOCHAR" style="width: 100%;" readonly />
                        <select name="PPH_STATUS_POST" class="custom-select select2-info" id="PPH_STATUS_POST" style="width: 100%;" required>
                            <option value="">-- NOT SELECTED --</option>
                            <option value="0">Potong Sendiri</option>
                            <option value="1">Potong Tenant</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Change</button>
            </div>
        </form>
    </div>
  </div>
</div>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('invoice.viewlistdatainvoice')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Invoice</label>
                                    <input type="text" name="INVOICE_TRANS_NOCHAR" id="INVOICE_TRANS_NOCHAR" class="form-control" placeholder="Invoice">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Tenant</label>
                                    <input type="text" name="MD_TENANT_NAME_CHAR" id="MD_TENANT_NAME_CHAR" class="form-control" placeholder="Tenant">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Type</label>
                                    <input type="text" name="TYPE_INV_CHAR" id="type_inv_char" class="form-control" readonly>
                                    <input type="hidden" name="TYPE_INV" id="type_inv" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="startDate" placeholder="Start date">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="endDate" placeholder="Start date">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="INVOICE_STATUS_INT" id="INVOICE_STATUS_INT" class="form-control">
                                        <option value="ALL">ALL STATUS</option>
                                        <option value="1">INVOICE</option>
                                        <option value="2">REQ. PAYMENT</option>
                                        <option value="3">PARTIAL PAYMENT</option>
                                        <option value="4">PAID</option>
                                    </select>
                                </div>
                                <div class="form-group" style="float: right;">
                                    <input type="submit" class="btn btn-primary" name="viewReport" id="viewReport" value="View Data">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-lg-2">
                                <a class="btn btn-success" href="<?php echo URL::route('invoice.viewadddatainvoicemanual'); ?>" role="button">
                                    Add Data Invoice
                                </a>
                            </div>
                            <div class="col-lg-3">
                                <a class="btn btn-success" href="<?php echo URL::route('invoice.viewadddatarevenuesharing'); ?>" role="button">
                                    Add Data Revenue Sharing
                                </a>
                            </div>
                        </div>
                    </form>
                    <?php if($dataListInv <> 0): ?>
                        <br><br>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md-12">
                                <table class="table-striped table-hover compact" id="tdp_report" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Invoice</th>
                                            <th>Lot</th>
                                            <th>Tenant</th>
                                            <th>Shop Name</th>
                                            <th>Bill Date</th>
                                            <th>Description</th>
                                            <th>PPH Status</th>
                                            <th>DPP</th>
                                            <th>PPN</th>
                                            <th>Bill Amount</th>
                                            <th>Stamp</th>
                                            <th>Status</th>
                                            <th>View/Edit</th>
                                            <th>Posting</th>
                                            <th>Invoice</th>
                                            <th>Paid</th>
                                            <th>Void</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; ?>
                                        <?php $__currentLoopData = $dataInvoice; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td style="text-align: center;"><?php echo e($i); ?></td>
                                                <td><?php echo e($data->INVOICE_TRANS_NOCHAR); ?></td>
                                                <td><?php echo e($data->LOT_STOCK_NO); ?></td>
                                                <td><?php echo e($data->MD_TENANT_NAME_CHAR); ?></td>
                                                <td><?php echo e($data->SHOP_NAME_CHAR); ?></td>
                                                <td><?php echo e($data->TGL_SCHEDULE_DATE); ?></td>
                                                <td><?php echo e($data->INVOICE_TRANS_DESC_CHAR); ?></td>
                                                <td><?php echo e($data->MD_TENANT_PPH_INT); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->INVOICE_TRANS_DPP,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->INVOICE_TRANS_PPN,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->INVOICE_TRANS_TOTAL,0,'','')); ?></td>
                                                <td style="text-align: right;"><?php echo e(number_format($data->DUTY_STAMP,0,'','')); ?></td>
                                                <td><?php echo e($data->INVOICE_STATUS_INT); ?></td>
                                                <?php if(($data->INVOICE_STATUS_INT == 'PAID' || $data->INVOICE_STATUS_INT == 'PARTIAL PAYMENT' || $data->INVOICE_STATUS_INT == 'REQ. PAYMENT') && $data->JOURNAL_STATUS_INT == 1): ?>
                                                    <td class="center">
                                                        <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                            <i>
                                                                View/Edit
                                                            </i>
                                                        </a>
                                                    </td>
                                                <?php else: ?>
                                                    <?php if($data->INVOICE_TRANS_TYPE == 'RS'): ?>
                                                        <td class="center">
                                                            <a class="btn btn-sm btn-warning" href="<?php echo e(URL('/invoice/vieweditdatainvoicerevenuesharing/' . $data->INVOICE_TRANS_ID_INT)); ?>">
                                                                <i>
                                                                    View/Edit
                                                                </i>
                                                            </a>
                                                        </td>
                                                    <?php else: ?>
                                                        <td class="center">
                                                            <a class="btn btn-sm btn-warning" href="<?php echo e(URL('/invoice/vieweditdatainvoicemanual/' . $data->INVOICE_TRANS_ID_INT.'/'. $data->DOC_TYPE)); ?>">
                                                                <i>
                                                                    View/Edit
                                                                </i>
                                                            </a>
                                                        </td>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php if($data->INVOICE_STATUS_INT == 'VOID'): ?>
                                                    <td class="center">
                                                        <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                            <i>
                                                                Posting
                                                            </i>
                                                        </a>
                                                    </td>
                                                <?php else: ?>
                                                    <?php if($data->JOURNAL_STATUS_INT == 0): ?>
                                                        <td class="center">
                                                            <a href="#postingModal<?php echo $data->INVOICE_TRANS_ID_INT; ?>" class="btn btn-sm btn-success pull-left" data-toggle="modal">
                                                                <i>
                                                                    Posting
                                                                </i>
                                                            </a>
                                                            <div id="postingModal<?php echo $data->INVOICE_TRANS_ID_INT; ?>" class="modal fade">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h4 class="modal-title">Confirmation</h4>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <div class="form-group">
                                                                                <p>Are you sure posting this document ?</p>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                                            <a href="<?php echo e(URL('/invoice/postinginvoice/'. $data->INVOICE_TRANS_ID_INT)); ?>" class="btn btn-success">Yes</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    <?php else: ?>
                                                        <td class="center">
                                                            <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                                <i>
                                                                    Posting
                                                                </i>
                                                            </a>
                                                        </td>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php if($data->INVOICE_STATUS_INT == 'VOID'): ?>
                                                    <td class="center">
                                                        <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                            <i>
                                                                Invoice
                                                            </i>
                                                        </a>
                                                    </td>
                                                <?php else: ?>
                                                    <?php if($data->JOURNAL_STATUS_INT == 0): ?>
                                                        <td class="center">
                                                            <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                                <i>
                                                                    Invoice
                                                                </i>
                                                            </a>
                                                        </td>
                                                    <?php else: ?>
                                                        <td class="center">
                                                            <a class="btn btn-sm btn-info" href="<?php echo e(URL('/invoice/printinvoicekwitansi/INV/' . $data->INVOICE_TRANS_ID_INT)); ?>" onclick="window.open(this.href).print(); return false">
                                                                <i>
                                                                    Invoice
                                                                </i>
                                                            </a>
                                                        </td>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php if($data->INVOICE_STATUS_INT == 'VOID'): ?>
                                                    <td class="center">
                                                        <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                            <i>
                                                                Receipt
                                                            </i>
                                                        </a>
                                                    </td>
                                                <?php endif; ?>
                                                <?php if(($data->INVOICE_STATUS_INT == 'VOID' || $data->INVOICE_STATUS_INT == 'REQ. PAYMENT')): ?>
                                                    <td class="center">
                                                        <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                            <i>
                                                                Paid
                                                            </i>
                                                        </a>
                                                    </td>
                                                <?php else: ?>
                                                    <?php if($data->JOURNAL_STATUS_INT == 0): ?>
                                                        <td class="center">
                                                            <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                                <i>
                                                                    Paid
                                                                </i>
                                                            </a>
                                                        </td>
                                                    <?php else: ?>
                                                        <td class="center">
                                                            <a class="btn btn-sm btn-success" href="<?php echo e(URL('/invoice/viewpaidinvoice/' . $data->INVOICE_TRANS_ID_INT)); ?>">
                                                                <i>
                                                                    Paid
                                                                </i>
                                                            </a>
                                                        </td>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?php if($data->INVOICE_STATUS_INT == 'VOID' || $data->INVOICE_STATUS_INT == 'REQ. PAYMENT' ||
                                                    $data->INVOICE_STATUS_INT == 'PARTIAL PAYMENT' || $data->INVOICE_STATUS_INT == 'PAID' ||
                                                    $data->ACC_JOURNAL_APPROVED_INT == 1 || $data->IS_EXPORT_FAKTUR == 1): ?>
                                                    <td class="center">
                                                        <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                            <i>
                                                                Void
                                                            </i>
                                                        </a>
                                                    </td>
                                                <?php else: ?>
                                                    <td class="center">
                                                        <a href="#voidModal<?php echo $data->INVOICE_TRANS_ID_INT; ?>" class="btn btn-sm btn-danger" data-toggle="modal">
                                                            <i>
                                                                Void
                                                            </i>
                                                        </a>
                                                        <div id="voidModal<?php echo $data->INVOICE_TRANS_ID_INT; ?>" class="modal fade">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">Confirmation</h4>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <p>Are you sure void this transaction ?</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                                        <a href="<?php echo e(URL('/invoice/voidinvoice/'. $data->INVOICE_TRANS_ID_INT)); ?>" class="btn btn-success">Yes</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                            <?php $i++; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3 col-sm-offset-10">
    <div id="typeinvModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact" id="type_inv_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Billing Type</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $billingType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($data->INVOICE_TRANS_TYPE); ?></td>
                                <td><?php echo e($data->INVOICE_TRANS_TYPE_DESC); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $secureDepType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($data1->PSM_SECURE_DEP_TYPE_CODE); ?></td>
                                <td><?php echo e($data1->PSM_SECURE_DEP_TYPE_DESC); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#PPH_STATUS_POST').select2();
    });

    function editDataPPHStatus(INVOICE_TRANS_ID_INT, MD_TENANT_PPH_ID_INT, INVOICE_TRANS_NOCHAR) {
        if(MD_TENANT_PPH_ID_INT === 'Potong Tenant') {
            MD_TENANT_PPH_ID_INT = 1;
        }
        else {
            MD_TENANT_PPH_ID_INT = 0;
        }

        $("#INVOICE_ID").val(INVOICE_TRANS_ID_INT);
        $("#INVOICE_TRANS_NOCHAR").val(INVOICE_TRANS_NOCHAR);
        $("#PPH_STATUS_POST").val(MD_TENANT_PPH_ID_INT);
        $("#PPH_STATUS_POST").trigger("change");
        $('#edit-pph-modal').modal('show');
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/page/accountreceivable/listDataInvoice.blade.php ENDPATH**/ ?>