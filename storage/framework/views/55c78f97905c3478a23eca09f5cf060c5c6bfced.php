<?php $__env->startSection('navbar_header'); ?>
    Form List Transaction Faktur - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form List Transaction Faktur
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<script>
    $(document).ready(function()    {
        $('#tax_invoice_table').DataTable({
            //dom: 'Bfrtip',
            scrollX : "2000px",
            pageLength : 5,
            order: [],
            // order : [],
            pageLength : 25,
            // //scrollX: true,
            //scrollY:"500px",
            // scrollCollapse: true,
            paging: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    //footer: true,
                    title: '<?php echo "List Transaction Faktur ".$dataProject['PROJECT_CODE']." Periode : ".$startDate.' S/D '.$endDate; ?>'
                },
                {
                    extend: 'pdfHtml5',
                    // footer: true,
                    title: '<?php echo "List Transaction Faktur ".$dataProject['PROJECT_CODE']." Periode : ".$startDate.' S/D '.$endDate; ?>'
                }
            ]
        });
    });
</script>
<script>
    function checkAll(ele) {
        var checkboxes = document.getElementsByTagName('input');
        if (ele.checked) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                    //document.getElementById(i).remove();
                }
            }
            document.getElementById('all').remove();
            var tz = $('<div />')
            tz.append($("<input />", { type: 'hidden', name: 'selectall', value: 'all', class: 'form-control',id: 'all'}))
            tz.appendTo('#temp-form');
        } else {
            for (var i = 0; i < checkboxes.length; i++) {
                console.log(i)
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                    //document.getElementById(i).remove();
                }
            }
            document.getElementById('all').remove();
            var tz = $('<div />')
            tz.append($("<input />", { type: 'hidden', name: 'selectall', value: 'none', class: 'form-control',id: 'all'}))
            tz.appendTo('#temp-form');
        }
    }

    function selected(ele,billid){
        //alert(billid);
        if (ele.checked) {
            var tz = $('<div />')
            tz.append($("<input />", { type: 'hidden', name: 'billing[]', value: billid, class: 'form-control',id: billid}))
            tz.appendTo('#temp-form');
        } else {
            //alert(ele);
            document.getElementById(billid).remove();
        }
    }
</script>

<div class="modal fade" id="edit-trx-code-modal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Edit Transaction Code</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form method="POST" action="<?php echo e(route('tf.listTransactionFakturEditTrxCode')); ?>">
            <?php echo csrf_field(); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-5">Transaction Code *</label>
                    <div class="col-sm-12">
                        <input type="hidden" class="form-control" name="INVOICE_ID_EDIT" id="INVOICE_ID_EDIT" style="width: 100%;" placeholder="Invoice ID" required readonly />
                        <input type="hidden" class="form-control" name="INVOICE_NO_FAKTUR_EDIT" id="INVOICE_NO_FAKTUR_EDIT" style="width: 100%;" placeholder="Invoice FP Number" required readonly />
                        <input type="text" class="form-control" name="TRX_CODE_EDIT" id="TRX_CODE_EDIT" style="width: 100%;" placeholder="Transaction Code" pattern="\d{3}|\d{3}" title="Must Be 3 Digit Number" onkeypress="return isNumber(event)" required />
                        <label style="color: red;">* Must Be 3 Digit Number</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Edit</button>
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
                    <form method="POST" action="<?php echo e(route('accounting.tax.viewlisttransaksifaktur')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Start Date*</label>
                                    <input type="date" class="form-control" id="startDate" name="START_DATE" placeholder="Start Date">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>End Date*</label>
                                    <input type="date" class="form-control" id="endDate" name="END_DATE" placeholder="End Date">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Status*</label>
                                    <select id="CATEGORY" name="CATEGORY" class="form-control">
                                        <option value="ALL">ALL</option>
                                        <option value="ACTIVE">ACTIVE</option>
                                        <option value="E-FAKTUR">E-FAKTUR</option>
                                    </select>
                                    <br>
                                    <input type="submit" value="View Data" class="btn btn-primary" style="float:right">
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php if($dataFaktur == 1): ?>
                    <form method="POST" action="<?php echo e(route('accounting.tax.exportdatafaktur')); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" class="form-control" id="startDate" name="startDate" value="<?php echo $startDate; ?>" readonly>
                        <input type="hidden" class="form-control" id="endDate" name="endDate" value="<?php echo $endDate; ?>" readonly>
                        <input type="hidden" class="form-control" id="category" name="category" value="<?php echo $category; ?>" readonly>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover dataTable  display compact" id="tax_invoice_table" style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th style="width: 10px;">No.</th>
                                        <th><input type="checkbox" onchange="checkAll(this)" name="billingid[]"/></th>
                                        <th>Invoice</th>
                                        <th>Trx Code</th>
                                        <th>FP Number</th>
                                        <th>Doc Date</th>
                                        <th>Description</th>
                                        <th>Nama Tenant</th>
                                        <th>NIK</th>
                                        <th>NPWP</th>
                                        <th>Address</th>
                                        <th>DPP</th>
                                        <th>PPN</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1;?>
                                    <?php $__currentLoopData = $dataTransFaktur; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td style="text-align: right"><?php echo e($i); ?></td>
                                            <td><input name="billingid[]" type="checkbox" onchange="selected(this,<?php echo $data->INVOICE_TRANS_ID_INT;  ?>)" value="<?php echo $data->INVOICE_TRANS_ID_INT;  ?>" id="idbilling"></td>
                                            <td><?php echo e($data->INVOICE_TRANS_NOCHAR); ?></td>
                                            <?php if($data->INVOICE_FP_NOCHAR == ""): ?>
                                            <td></td>
                                            <?php else: ?>
                                            <td><a href="javascript:void(0)" onclick="editDataTrxCode('<?php echo $data->INVOICE_TRANS_ID_INT ?>','<?php echo $data->KODE_PAJAK.''.$data->PEMBETULAN_PAJAK ?>','<?php echo $data->INVOICE_FP_NOCHAR ?>')"><?php echo e($data->KODE_PAJAK); ?><?php echo e($data->PEMBETULAN_PAJAK); ?></a></td>
                                            <?php endif; ?>
                                            <td><?php echo e($data->INVOICE_FP_NOCHAR); ?></td>
                                            <td><?php echo e($data->TGL_SCHEDULE_DATE); ?></td>
                                            <td><?php echo e($data->INVOICE_TRANS_DESC_CHAR); ?></td>
                                            <td><?php echo e($data->MD_TENANT_NAME_CHAR); ?></td>
                                            <td><?php echo e($data->MD_TENANT_NIK); ?></td>
                                            <td><?php echo e($data->MD_TENANT_NPWP); ?></td>
                                            <td><?php echo e($data->MD_TENANT_ADDRESS_TAX); ?></td>
                                            <td style="text-align: right;"><?php echo e(number_format($data->INVOICE_TRANS_DPP,0,'','')); ?></td>
                                            <td style="text-align: right;"><?php echo e(number_format($data->INVOICE_TRANS_PPN,0,'','')); ?></td>
                                            <td style="text-align: right;"><?php echo e(number_format($data->INVOICE_TRANS_TOTAL,0,'','')); ?></td>
                                            <td><?php echo e($data->IS_EXPORT_FAKTUR); ?></td>
                                            <?php $i++;?>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                                <div id="temp-form">
                                    <input type="hidden" name="selectall" value="none" class="form-control" id="all">
                                    <input type="hidden" name="billing" value="0" class="form-control" id="0">
                                </div>
                                <div class="form-group">
                                    <a href="#confModal" class="btn btn-primary pull-right" data-toggle="modal" style="float: right;">
                                        Generate Data
                                    </a>
                                    <div id="confModal" class="modal fade">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Confirmation</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure generate this data ?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                    <input type="submit" class="btn btn-success" value="Yes">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function editDataTrxCode(INVOICE_TRANS_ID_INT, INVOICE_FP_TRX_CODE, INVOICE_FP_NOCHAR) {
        $("#INVOICE_ID_EDIT").val(INVOICE_TRANS_ID_INT);
        $("#TRX_CODE_EDIT").val(INVOICE_FP_TRX_CODE);
        $("#INVOICE_NO_FAKTUR_EDIT").val(INVOICE_FP_NOCHAR);
        $('#edit-trx-code-modal').modal('show');
    }

    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/page/accounting/tax/listDataTransaksiFaktur.blade.php ENDPATH**/ ?>