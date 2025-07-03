<?php
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\widgets\google\BarChart;
    use \koolreport\widgets\google\PieChart;
    use \koolreport\pivot\widgets\PivotTable;
    use \koolreport\widgets\google\ColumnChart;
    use \koolreport\drilldown\LegacyDrillDown;
    use \koolreport\drilldown\DrillDown;
    use \koolreport\widgets\google\LineChart;
    use \koolreport\barcode\QRCode;
?>

<style>
    td {
        white-space: nowrap;
    }
</style>



<?php $__env->startSection('navbar_header'); ?>
    List Promo Ticket - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    List Promo Ticket
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <div style="padding-left: 5px;">
                        <?php if(session()->has('success')): ?>
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e(session()->get('success')); ?></strong>
                            </div>
                        <?php endif; ?>
                        <?php if(session()->has('error')): ?>
                            <div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e(session()->get('error')); ?></strong>
                            </div>
                        <?php endif; ?>
                        <?php if(session()->has('warning')): ?>
                            <div class="alert alert-warning alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e(session()->get('warning')); ?></strong>
                            </div>
                        <?php endif; ?>
                        <?php if(session()->has('info')): ?>
                            <div class="alert alert-info alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong><?php echo e(session()->get('info')); ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>

                    <form>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <a class="form-control btn btn-info" href="<?php echo e(route('add_new_promo')); ?>">
                                        Add New Promo Ticket
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <br />
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md" style="overflow-x:auto;">                            
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Description</th>
                                        <th>Free Ticket</th>
                                        <th>Discount (%)</th>
                                        <th>Discount Nominal</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Description Group</th>
                                        <th>Description Ticket</th>
                                        <th>Payment Method</th>
                                        <th>Minimal Ticket</th>
                                        <th>Minimal Payment</th>
                                        <th>Maximal Transaction Number</th>
                                        <th>Request By</th>
                                        <th>Request At</th>
                                        <th>Status</th>
                                        <th>Approve SMM</th>
                                        <th>Approve GM</th>
                                        <th>Edit</th>
                                        <th>Cancel</th>
                                        <th>Terminate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    <?php $__currentLoopData = $dataPromo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td style="text-align: center;"><?php echo e($i); ?></td>
                                        <td style="text-align: left;"><?php echo e($data->DESC_CHAR); ?></td>
                                        <td style="text-align: center;"><?php echo e($data->TICKET_FREE_INT); ?></td>
                                        <td style="text-align: center;"><?php echo e((float) $data->DISCOUNT_PERCENT_FLOAT); ?>%</td>
                                        <td style="text-align: center;"><?php echo e(number_format($data->DISCOUNT_NOMINAL_FLOAT, 0, ",", ".")); ?></td>
                                        <td style="text-align: center;"><?php echo e(date('Y-m-d H:i', strtotime($data->START_PROMO_DATE))); ?></td>
                                        <td style="text-align: center;"><?php echo e(date('Y-m-d H:i', strtotime($data->END_PROMO_DATE))); ?></td>
                                        <td style="text-align: center;"><?php echo e($data->MD_GROUP_TICKET_DESC); ?></td>
                                        <td style="text-align: center;"><?php echo e($data->MD_PRICE_TICKET_DESC); ?></td>
                                        <td style="text-align: center;"><?php echo e($data->PAYMENT_METHOD_DESC_CHAR); ?></td>
                                        <td style="text-align: center;"><?php echo e($data->MIN_TICKET_QTY); ?></td>
                                        <td style="text-align: center;"><?php echo e(number_format($data->MIN_TICKET_PAYMENT, 0, ",", ".")); ?></td>
                                        <td style="text-align: center;"><?php echo e($data->MAX_TRX_NUMBER); ?></td>
                                        <td style="text-align: center;"><?php echo e($data->created_by); ?></td>
                                        <td style="text-align: center;"><?php echo e(date('Y-m-d H:i:s', strtotime($data->created_at))); ?></td>
                                        <td style="text-align: center;"><?php echo e($data->DESC_CHAR_STATUS); ?></td>
                                        <td style="text-align: center;">
                                            <!-- Admin Pusat IT, SMM, GM -->
                                            <?php if($data->STATUS == 1 && (session('level') == '99' || session('level') == '11')): ?>
                                            <a href="javascript:void(0)" onclick="swalApprSMMData(<?php echo e($data->PROMO_TICKET_PURCHASE_ID_INT); ?>)" class="btn btn-primary">
                                            <?php elseif($data->COUNT_TRANS_PROMO == 0 && $data->STATUS == 2 && (session('level') == '99' || session('level') == '11')): ?>
                                            <a href="javascript:void(0)" onclick="swalUnapprSMMData(<?php echo e($data->PROMO_TICKET_PURCHASE_ID_INT); ?>)" class="btn btn-danger">
                                            <?php else: ?>
                                            <a href="javascript:void(0)" class="btn btn-default">
                                            <?php endif; ?>
                                                <?php if($data->STATUS <= 1): ?>
                                                <i>Approve</i>
                                                <?php else: ?>
                                                <i>Unapprove</i>
                                                <?php endif; ?>
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <!-- Admin Pusat IT, SMM, GM -->
                                            <?php if($data->STATUS == 2 && (session('level') == '99' || session('level') == '9')): ?>
                                            <a href="javascript:void(0)" onclick="swalApprGMData(<?php echo e($data->PROMO_TICKET_PURCHASE_ID_INT); ?>)" class="btn btn-primary">
                                            <?php elseif($data->COUNT_TRANS_PROMO == 0 && $data->STATUS == 3 && (session('level') == '99' || session('level') == '9')): ?>
                                            <a href="javascript:void(0)" onclick="swalUnapprGMData(<?php echo e($data->PROMO_TICKET_PURCHASE_ID_INT); ?>)" class="btn btn-danger">
                                            <?php else: ?>
                                            <a href="javascript:void(0)" class="btn btn-default">
                                            <?php endif; ?>
                                                <?php if($data->STATUS <= 2): ?>
                                                <i>Approve</i>
                                                <?php else: ?>
                                                <i>Unapprove</i>
                                                <?php endif; ?>
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <!-- Admin Pusat IT, SMM, GM -->
                                            <?php if($data->COUNT_TRANS_PROMO == 0 && $data->STATUS == 1): ?>
                                            <a href="<?php echo e(URL('/edit_view_promo/' . base64_encode($data->PROMO_TICKET_PURCHASE_ID_INT))); ?>" class="btn btn-primary">
                                            <?php else: ?>
                                            <a href="javascript:void(0)" class="btn btn-default">
                                            <?php endif; ?>
                                                <i>
                                                    Edit
                                                </i>
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <!-- Admin Pusat IT, SMM, GM -->
                                            <?php if($data->COUNT_TRANS_PROMO == 0 && $data->STATUS == 1): ?>
                                            <a href="javascript:void(0)" onclick="swalDeleteData(<?php echo e($data->PROMO_TICKET_PURCHASE_ID_INT); ?>)" class="btn btn-danger">
                                            <?php else: ?>
                                            <a href="javascript:void(0)" class="btn btn-default">
                                            <?php endif; ?>
                                                <i>
                                                    Cancel
                                                </i>
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <!-- Admin Pusat IT, SMM, GM -->
                                            <?php if($data->STATUS == 3 && date('Y-m-d H:i:s') <= date('Y-m-d H:i:s', strtotime($data->END_PROMO_DATE)) && (session('level') == '99' || session('level') == '9' || session('level') == '11')): ?>
                                            <a href="javascript:void(0)" onclick="swalTerminateData(<?php echo e($data->PROMO_TICKET_PURCHASE_ID_INT); ?>)" class="btn btn-danger">
                                            <?php else: ?>
                                            <a href="javascript:void(0)" class="btn btn-default">
                                            <?php endif; ?>
                                                <i>
                                                    Terminate
                                                </i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
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

        $("#example1").DataTable({
            "order": [[0, 'desc']],
            "scrollY": true, "scrollX": true,
            "responsive": false, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });

    function swalDeleteData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Cancel</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/delete_promo/" + param1;
            }
        });
    }

    function swalTerminateData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Terminate</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/terminate_promo/" + param1;
            }
        });
    }

    function swalApprSMMData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Approve</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/appr_smm_promo/" + param1;
            }
        });
    }

    function swalApprGMData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Approve</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/appr_gm_promo/" + param1;
            }
        });
    }

    function swalUnapprSMMData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Unapprove</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/unappr_smm_promo/" + param1;
            }
        });
    }

    function swalUnapprGMData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Unapprove</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/unappr_gm_promo/" + param1;
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/MasterData/Promo/promo.blade.php ENDPATH**/ ?>