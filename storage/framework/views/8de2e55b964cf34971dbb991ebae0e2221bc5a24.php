<?php $__env->startSection('navbar_header'); ?>
    Form Add Invoice - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form Add Invoice
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<style>
    th, td {
        padding: 15px;
    }
</style>
<script src="https://cdn.ckeditor.com/4.10.0/standard/ckeditor.js"></script>
<script>

    $(document).ready(function()
    {
        $('#tdp_report1').DataTable( {
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });
    } );

</script>
<script>
    $(function(){
        var table = $('#lot_table').DataTable({
            order:[]
        });

        $('#lot_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('lot_stock_id').value = checkEmptyStringValidation(data[0]);
            document.getElementById('psm_trans_nochar').value = checkEmptyStringValidation(data[1]);
            document.getElementById('lot_stock_no').value = checkEmptyStringValidation(data[2]);
            document.getElementById('md_tenant_name').value = checkEmptyStringValidation(data[3]);
            document.getElementById('lot_stock_sqm').value = checkEmptyStringValidation(data[4]);
            document.getElementById('md_tenant_id_int').value = checkEmptyStringValidation(data[5]);
            $('#lotModal').modal('hide');
        });

        $('#lot_stock_no').on('click',function(){
            $('#lotModal').modal('show');
        });
    });

    $(function(){
        var table = $('#tenant_table').DataTable({
            order:[]
        });

        $('#tenant_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('md_tenant_id_int').value = checkEmptyStringValidation(data[0]);
            document.getElementById('md_tenant_name').value = checkEmptyStringValidation(data[1]);
            $('#tenantModal').modal('hide');
        });

        $('#md_tenant_name').on('click',function(){
            $('#tenantModal').modal('show');
        });
    });

    $(function(){
        var table = $('#billing_type_table').DataTable({
            order:[]
        });

        $('#billing_type_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('invoice_type').value = checkEmptyStringValidation(data[0]);
            document.getElementById('invoice_type_desc').value = checkEmptyStringValidation(data[1]);
            document.getElementById('doc_type').value = checkEmptyStringValidation(data[2]);
            $('#billingtypeModal').modal('hide');
        });

        $('#invoice_type_desc').on('click',function(){
            $('#billingtypeModal').modal('show');
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
<style>
    @media  screen and (min-width: 676px) {
        .modal-dialog {
            max-width: 700px; /* New width for default modal */
        }
    }
</style>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('invoice.saveinvoicemanual')); ?>">
                        <?php echo csrf_field(); ?>
                        <fieldset>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Lot</label>
                                        <input type="text" name="LOT_STOCK_NO" id="lot_stock_no" class="form-control" placeholder="Lot" readonly="yes">
                                        <input type="hidden" name="LOT_STOCK_ID_INT" id="lot_stock_id" class="form-control" readonly="yes">
                                        <input type="hidden" name="PSM_TRANS_NOCHAR" id="psm_trans_nochar" class="form-control" readonly="yes">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Tenant*</label>
                                        <input type="text" name="MD_TENANT_NAME_CHAR" id="md_tenant_name" class="form-control" placeholder="Tenant" readonly="yes">
                                        <input type="hidden" name="MD_TENANT_ID_INT" id="md_tenant_id_int" class="form-control" value="0" readonly="yes">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>SQM</label>
                                        <input type="text" name="LOT_STOCK_SQM" id="lot_stock_sqm" class="form-control" placeholder="SQM" readonly="yes">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>Transaction Date*</label>
                                        <input type="date" value="" class="form-control" name="TGL_SCHEDULE_DATE" placeholder="Transaction Date">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>Due Date*</label>
                                        <input type="date" value="" class="form-control" name="TGL_SCHEDULE_DUE_DATE" placeholder="Due Date">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>Transaction Code (Tax)*</label>
                                        <select name="TRANS_CODE" class="form-control">
                                            <option value="01">01</option>
                                            <option value="02">02</option>
                                            <option value="03">03</option>
                                            <option value="04">04</option>
                                            <option value="05">05</option>
                                            <option value="06">06</option>
                                            <option value="07">07</option>
                                            <option value="08">08</option>
                                            <option value="09">09</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>Invoice Type*</label>
                                        <input type="text" name="INVOICE_TRANS_TYPE_DESC" class="form-control" id="invoice_type_desc" placeholder="Invoice Type" readonly="yes">
                                        <input type="hidden" name="INVOICE_TRANS_TYPE" class="form-control" id="invoice_type" readonly="yes">
                                        <input type="hidden" name="DOC_TYPE" class="form-control" id="doc_type" readonly="yes">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Amount*</label>
                                        <input type="text" name="INVOICE_TRANS_TOTAL" id="amount" class="form-control" placeholder="0" value="0" readonly="yes">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Description*</label>
                                        <input type="text" name="INVOICE_TRANS_DESC_CHAR" class="form-control" id="inv_desc" placeholder="Description" maxlength="100">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <a href="#confModal" class="btn btn-primary pull-right" data-toggle="modal" style="float: right;">
                                            Save Data
                                        </a>
                                        <div id="confModal" class="modal fade">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Confirmation</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure save this data ?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
                                                        <input type="submit" class="btn btn-primary" value="Save Data">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3 col-sm-offset-10">
    <div id="lotModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Lot</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact" id="lot_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Lease Doc.</th>
                            <th>Lot</th>
                            <th>Tenant</th>
                            <th>SQM</th>
                            <th>ID Tenant</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $dataLot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($data->LOT_STOCK_ID_INT); ?></td>
                                <td><?php echo e($data->PSM_TRANS_NOCHAR); ?></td>
                                <td><?php echo e($data->LOT_STOCK_NO); ?></td>
                                <td><?php echo e($data->MD_TENANT_NAME_CHAR); ?></td>
                                <td style="text-align: right;"><?php echo e(number_format($data->LOT_STOCK_SQM,0,'','.')); ?></td>
                                <td><?php echo e($data->MD_TENANT_ID_INT); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3 col-sm-offset-10">
    <div id="tenantModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tenant</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact" id="tenant_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tenant</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $tenant; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($data1->MD_TENANT_ID_INT); ?></td>
                                <td><?php echo e($data1->MD_TENANT_NAME_CHAR); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3 col-sm-offset-10">
    <div id="billingtypeModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Billing Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact" id="billing_type_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Billing Type</th>
                            <th>Description</th>
                            <th>Doc. Type</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $billingType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($data->INVOICE_TRANS_TYPE); ?></td>
                                <td><?php echo e($data->INVOICE_TRANS_TYPE_DESC); ?></td>
                                <td>B</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php $__currentLoopData = $secureDepType; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($data1->PSM_SECURE_DEP_TYPE_CODE); ?></td>
                                <td><?php echo e($data1->PSM_SECURE_DEP_TYPE_DESC); ?></td>
                                <td>D</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/page/accountreceivable/addDataInvoiceManual.blade.php ENDPATH**/ ?>