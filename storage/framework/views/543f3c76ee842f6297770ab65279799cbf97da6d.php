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
    Form Edit Ticket Price - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form Edit Ticket Price
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form action="<?php echo e(url('edit_ticket_price')); ?>" method="post">
                        <?php echo e(csrf_field()); ?>

                        <div class="row">
                            <input type="hidden" name="TXT_ID" class="form-control" id="TXT_ID" value="<?php echo e($dataTicketPrice->MD_PRICE_TICKET_ID_INT); ?>" required>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Group <span style="color: red;">*</span></label>
                                    <select id="DDL_GROUP" name="DDL_GROUP" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <?php $__currentLoopData = $ddlTicketGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($data->MD_GROUP_TICKET_ID_INT); ?>" <?php echo e($dataTicketPrice->MD_GROUP_TICKET_ID_INT == $data->MD_GROUP_TICKET_ID_INT ? "selected" : ""); ?>>
                                            <?php echo e($data->MD_GROUP_TICKET_DESC); ?>

                                        </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Description <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_DESC" class="form-control" id="TXT_DESC" placeholder="Enter Description" value="<?php echo e($dataTicketPrice->MD_PRICE_TICKET_DESC); ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Single Ticket Price (Include PB1 & PPH) <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_TICKET" class="form-control" id="TXT_TICKET" placeholder="Enter Price Ticket" value="<?php echo e($dataTicketPrice->MD_PRICE_TICKET_NUM); ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>PB1 (%) <span style="color: red;">*</span></label>
                                    <input type="text" onkeypress="return isNumberWithDecimal(event)" name="TXT_PB1" class="form-control" id="TXT_PB1" placeholder="Enter PB1 (%)" value="<?php echo e((float) $dataTicketPrice->MD_PRICE_TICKET_PB1_PERCENT_INT); ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>PPH (%) <span style="color: red;">*</span></label>
                                    <input type="text" onkeypress="return isNumberWithDecimal(event)" name="TXT_PPH" class="form-control" id="TXT_PPH" placeholder="Enter PPH (%)" value="<?php echo e((float) $dataTicketPrice->MD_PRICE_TICKET_PPH_PERCENT_INT); ?>" required>
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
<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/watergroup/public_html/metland_water/resources/views/MasterData/TicketPrice/edit_view_ticket_price.blade.php ENDPATH**/ ?>