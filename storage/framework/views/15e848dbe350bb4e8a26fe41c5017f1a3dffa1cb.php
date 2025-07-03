<?php $__env->startSection('navbar_header'); ?>
    Form Upload / Download VA - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form Upload / Download VA
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<style>
    th, td {
        padding: 15px;
    }
</style>

<script>
    function transID(sel) {
        if (sel.value == "1") {
            document.getElementById("trxDate").disabled = false;
        } else if (sel.value == "2") {
            document.getElementById("trxDate").disabled = true;
        }
    }
</script>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('virtualaccount.prosesuploaddownloadva')); ?>" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                        <fieldset>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Transaction</label>
                                        <select name="transaction" id="trxId" class="form-control" onchange="transID(this);">
                                            <option value="1">Download File CSV for Bank</option>
                                        </select>
                                        <input type="hidden" name="PROJECT_NO_CHAR" id="PROJECT_NO_CHAR" class="form-control" placeholder="Transaction Date" value="<?php echo $project_no; ?>" readonly="yes">
                                    </div>
                                    <div class="form-group">
                                        <label>Cut Off</label>
                                        <input type="date" value="<?php echo e($dateFormat); ?>" class="form-control" id="trxDate" name="transaction_date" placeholder="Transaction Date">
                                        <input type="hidden" name="date_trans" id="datetrx" class="form-control" placeholder="Transaction Date" value="<?php echo $dateFormat; ?>" readonly="yes">
                                    </div>
                                    <div class="form-group">
                                        <a href="#nupModal" class="btn btn-primary" data-toggle="modal" name="buttonSave" style="float: right;">
                                            Proses VA
                                        </a>
                                        <div id="nupModal" class="modal fade">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title">Confirmation</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure process this data ?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                                                        <input type="submit" class="btn btn-primary" value="Process" name="buttonSave">
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
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/page/accountreceivable/virtualaccount/uploadDownloadVA.blade.php ENDPATH**/ ?>