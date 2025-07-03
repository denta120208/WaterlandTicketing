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
    Form Edit Promo Membership - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form Edit Promo Membership
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form action="<?php echo e(url('edit_promo_membership')); ?>" method="post">
                        <?php echo e(csrf_field()); ?>

                        <div class="row">
                            <input type="hidden" name="TXT_PROMO_ID" class="form-control" id="TXT_PROMO_ID" value="<?php echo e($promoMemberships->MD_PROMO_MEMBERSHIP_ID_INT); ?>" required>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Group Membership <span style="color: red;">*</span></label>
                                    <select id="MD_GROUP_MEMBERSHIP_ID_INT" name="MD_GROUP_MEMBERSHIP_ID_INT" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <?php $__currentLoopData = $ddlDataGroupMembership; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->MD_GROUP_MEMBERSHIP_ID_INT); ?>" <?php echo e($promoMemberships->MD_GROUP_MEMBERSHIP_ID_INT == $data->MD_GROUP_MEMBERSHIP_ID_INT ? "selected" : ""); ?>>
                                            <?php echo e($data->DESC_CHAR); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>    
                                </div>
                            </div>    
                            <div class="col-sm-6"> 
                                <div class="form-group">
                                    <label>Type Membership <span style="color: red;">*</span></label>
                                    <select id="MD_GROUP_TYPE_MEMBERSHIP_ID_INT" name="MD_GROUP_TYPE_MEMBERSHIP_ID_INT" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <?php $__currentLoopData = $ddlDataTypeMembership; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->MD_GROUP_TYPE_MEMBERSHIP_ID_INT); ?>" <?php echo e($promoMemberships->MD_GROUP_TYPE_MEMBERSHIP_ID_INT == $data->MD_GROUP_TYPE_MEMBERSHIP_ID_INT ? "selected" : ""); ?>>
                                            <?php echo e($data->DESC_CHAR); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>    
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Periode Membership <span style="color: red;">*</span></label>
                                    <select id="MD_PERIODE_MEMBERSHIP_ID_INT" name="MD_PERIODE_MEMBERSHIP_ID_INT" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <?php $__currentLoopData = $ddlDataPeriodeMembership; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->MD_PERIODE_MEMBERSHIP_ID_INT); ?>"  <?php echo e($promoMemberships->MD_PERIODE_MEMBERSHIP_ID_INT == $data->MD_PERIODE_MEMBERSHIP_ID_INT ? "selected" : ""); ?>>
                                            <?php echo e($data->DESC_CHAR); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Payment Method <span style="color: red;">*</span></label>
                                    <select id="PAYMENT_METHOD_ID_INT" name="PAYMENT_METHOD_ID_INT" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <?php $__currentLoopData = $ddlDataPaymentMethod; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->PAYMENT_METHOD_ID_INT); ?>"  <?php echo e($promoMemberships->PAYMENT_METHOD_ID_INT == $data->PAYMENT_METHOD_ID_INT ? "selected" : ""); ?>>
                                            <?php echo e($data->PAYMENT_METHOD_DESC_CHAR); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>    
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Free Days <span style="color: red;">*</span></label>
                                    <input type="number" name="FREE_DAYS_INT" class="form-control" id="FREE_DAYS_INT" placeholder="Enter Discount Nominal" value="<?php echo e($promoMemberships->FREE_DAYS_INT); ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Discount (%) <span style="color: red;">*</span></label>
                                    <input type="text" onkeypress="return isNumberWithDecimal(event)" name="DISCOUNT_PERSEN_FLOAT" class="form-control" id="DISCOUNT_PERSEN_FLOAT" placeholder="Enter Discount (%)" value="<?php echo e($promoMemberships->DISCOUNT_PERSEN_FLOAT); ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Discount Nominal <span style="color: red;">*</span></label>
                                    <input type="text" name="DISCOUNT_NOMINAL_NUM" class="form-control" id="DISCOUNT_NOMINAL_NUM" placeholder="Enter Discount Nominal" value="<?php echo e($promoMemberships->DISCOUNT_NOMINAL_NUM); ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Promo Start <span style="color: red;">*</span></label>
                                    <input type="datetime-local" name="PROMO_START_DTTIME" class="form-control" id="PROMO_START_DTTIME" placeholder="Enter End Promo" value="<?php echo e($promoMemberships->PROMO_START_DTTIME); ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Promo End <span style="color: red;">*</span></label>
                                    <input type="datetime-local" name="PROMO_END_DTTIME" class="form-control" id="PROMO_END_DTTIME" placeholder="Enter End Promo" value="<?php echo e($promoMemberships->PROMO_END_DTTIME); ?>" required>
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
<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/MasterData/PromoMembership/edit_view_promo_membership.blade.php ENDPATH**/ ?>