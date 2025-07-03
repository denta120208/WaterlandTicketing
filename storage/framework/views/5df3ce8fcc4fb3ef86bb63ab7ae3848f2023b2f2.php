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
    Form Edit Equipment - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form Edit Equipment
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form action="<?php echo e(url('edit_equipment')); ?>" method="post">
                        <?php echo e(csrf_field()); ?>

                        <div class="row">
                            <input type="hidden" name="TXT_ID" class="form-control" id="TXT_ID" value="<?php echo e($dataEquipment->MD_EQUIPMENT_ID_INT); ?>" required>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Group <span style="color: red;">*</span></label>
                                    <select id="DDL_GROUP" name="DDL_GROUP" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <?php $__currentLoopData = $ddlEquipment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->MD_EQUIPMENT_CATEGORY_ID_INT); ?>" <?php echo e($dataEquipment->MD_EQUIPMENT_CATEGORY_ID_INT == $data->MD_EQUIPMENT_CATEGORY_ID_INT ? "selected" : ""); ?>>
                                            <?php echo e($data->MD_EQUIPMENT_CATEGORY_DESC_CHAR); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Asset Number <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_ASSET_NUMBER" class="form-control" id="TXT_ASSET_NUMBER" placeholder="Enter Asset Number" value="<?php echo e($dataEquipment->EQUIPMENT_ASSET_NUMBER); ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Price <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_PRICE" class="form-control" id="TXT_PRICE" placeholder="Enter Price" value="<?php echo e((float) $dataEquipment->HARGA_SATUAN_FLOAT); ?>" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="BTN_SUBMIT" name="BTN_SUBMIT" class="btn btn-primary float-right">Edit</button>
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
<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/MasterData/Equipment/edit_view_equipment.blade.php ENDPATH**/ ?>