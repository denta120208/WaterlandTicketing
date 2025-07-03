<?php
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\widgets\google\BarChart;
    use \koolreport\widgets\google\PieChart;
    use \koolreport\pivot\widgets\PivotTable;
    use \koolreport\widgets\google\ColumnChart;
    use \koolreport\drilldown\LegacyDrillDown;
    use \koolreport\drilldown\DrillDown;
    use \koolreport\widgets\google\LineChart;
    use Illuminate\Support\Str;
?>



<?php $__env->startSection('navbar_header'); ?>
    Form Add New Promo Equipment - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form Add New Promo Equipment
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form action="<?php echo e(url('save_promo_equipment')); ?>" method="post">
                        <?php echo e(csrf_field()); ?>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Category <span style="color: red;">*</span></label>
                                    <select id="DDL_CATEGORY" name="DDL_CATEGORY" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <?php $__currentLoopData = $ddlDataCategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->MD_EQUIPMENT_CATEGORY_ID_INT); ?>">
                                            <?php echo e($data->MD_EQUIPMENT_CATEGORY_DESC_CHAR); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Description <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_DESC" class="form-control" id="TXT_DESC" placeholder="Enter Description" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Free Qty <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_EQUIPMENT_FREE" class="form-control" id="TXT_EQUIPMENT_FREE" placeholder="Enter Equipment Free" value="0" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Discount (%) <span style="color: red;">*</span></label>
                                    <input type="text" onkeypress="return isNumberWithDecimal(event)" name="TXT_DISCOUNT_PERCENT" class="form-control" id="TXT_DISCOUNT_PERCENT" placeholder="Enter Discount %" value="0" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Discount Nominal <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_DISCOUNT_NOMINAL" class="form-control" id="TXT_DISCOUNT_NOMINAL" placeholder="Enter Discount Nominal" value="0" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Start Promo <span style="color: red;">*</span></label>
                                    <input type="datetime-local" name="TXT_START_PROMO" class="form-control" id="TXT_START_PROMO" placeholder="Enter Start Promo" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>End Promo <span style="color: red;">*</span></label>
                                    <input type="datetime-local" name="TXT_END_PROMO" class="form-control" id="TXT_END_PROMO" placeholder="Enter End Promo" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Payment Method <span style="color: red;">*</span></label>
                                    <select id="DDL_PAYMENT_METHOD" name="DDL_PAYMENT_METHOD" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <option value="ALL">ALL</option>
                                        <?php $__currentLoopData = $ddlDataPaymentMethod; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->PAYMENT_METHOD_ID_INT); ?>">
                                            <?php echo e($data->PAYMENT_METHOD_DESC_CHAR); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Minimal Qty <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_MIN_QTY" class="form-control" id="TXT_MIN_QTY" placeholder="Enter Minimal Qty" value="0" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Minimal Payment <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_MIN_PAYMENT" class="form-control" id="TXT_MIN_PAYMENT" placeholder="Enter Minimal Payment" value="0" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Maximal Transaction Number <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_MAX_TRX_NUM" class="form-control" id="TXT_MAX_TRX_NUM" placeholder="Enter Maximal Transaction Number" value="0" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="BTN_SUBMIT" name="BTN_SUBMIT" class="btn btn-primary float-right">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    function isNumberWithDecimal(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && ((charCode < 48 && charCode !== 46) || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/watergroup/public_html/metland_water/resources/views/MasterData/PromoEquipment/add_new_promo_equipment.blade.php ENDPATH**/ ?>