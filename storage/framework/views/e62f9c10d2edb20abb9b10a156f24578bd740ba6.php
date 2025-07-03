<?php $__env->startSection('navbar_header'); ?>
    List Promo Membership - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    List Promo Membership
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
                                    <a class="form-control btn btn-info" href="<?php echo e(route('add_new_promo_membership')); ?>">
                                        Add New Promo Membership
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
                                            <th>Group Membership</th>
                                            <th>Type Membership</th>
                                            <th>Periode Membership</th>
                                            <th>Payment Method</th>
                                            <th>Free Days</th>
                                            <th>Discount (%)</th>
                                            <th>Discount Nominal</th>
                                            <th>Promo Start</th>
                                            <th>Promo End</th>
                                            <th>Request By</th>
                                            <th>Request At</th>
                                            <th>Updated By</th>
                                            <th>Updated At</th>
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
                                        <?php $__currentLoopData = $dataMemberships; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $dataMembership): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($index + 1); ?></td>
                                                <td><?php echo e($dataMembership->Grup); ?></td>
                                                <td><?php echo e($dataMembership->Type); ?></td>
                                                <td><?php echo e($dataMembership->Periode); ?></td>
                                                <td><?php echo e($dataMembership->Payment); ?></td>
                                                <td><?php echo e($dataMembership->FREE_DAYS_INT); ?></td>
                                                <td><?php echo e($dataMembership->DISCOUNT_PERSEN_FLOAT); ?>%</td>
                                                <td><?php echo e($dataMembership->DISCOUNT_NOMINAL_NUM); ?></td>
                                                <td><?php echo e($dataMembership->PROMO_START_DTTIME); ?></td>
                                                <td><?php echo e($dataMembership->PROMO_END_DTTIME); ?></td>
                                                <td><?php echo e($dataMembership->REQUEST_BY); ?></td>
                                                <td><?php echo e($dataMembership->REQUEST_AT); ?></td>
                                                <td><?php echo e($dataMembership->UPDATED_BY); ?></td>
                                                <td><?php echo e($dataMembership->UPDATED_AT); ?></td>
                                                <td><?php echo e($dataMembership->Status); ?></td>
                                                <td style="text-align: center;">
                                                    <!-- Admin Pusat IT, SMM, GM -->
                                                    <?php if($dataMembership->MD_PROMO_MEMBERSHIP_STATUS_ID_INT == 1 && (session('level') == '99' || session('level') == '11')): ?>
                                                    <a href="javascript:void(0)" onclick="swalApprSMMData(<?php echo e($dataMembership->MD_PROMO_MEMBERSHIP_ID_INT); ?>)" class="btn btn-primary">
                                                    <?php elseif($dataMembership->MD_PROMO_MEMBERSHIP_STATUS_ID_INT == 2 && (session('level') == '99' || session('level') == '11')): ?>
                                                    <a href="javascript:void(0)" onclick="swalUnapprSMMData(<?php echo e($dataMembership->MD_PROMO_MEMBERSHIP_ID_INT); ?>)" class="btn btn-danger">
                                                    <?php else: ?>
                                                    <a href="javascript:void(0)" class="btn btn-default">
                                                    <?php endif; ?>
                                                        <?php if($dataMembership->MD_PROMO_MEMBERSHIP_STATUS_ID_INT <= 1): ?>
                                                        <i>Approve</i>
                                                        <?php else: ?>
                                                        <i>Unapprove</i>
                                                    <?php endif; ?>
                                                    </a>
                                                </td>
                                                <td style="text-align: center;">
                                                    <!-- Admin Pusat IT, SMM, GM -->
                                                    <?php if($dataMembership->MD_PROMO_MEMBERSHIP_STATUS_ID_INT == 2 && (session('level') == '99' || session('level') == '9')): ?>
                                                    <a href="javascript:void(0)" onclick="swalApprGMData(<?php echo e($dataMembership->MD_PROMO_MEMBERSHIP_ID_INT); ?>)" class="btn btn-primary">
                                                    <?php elseif($dataMembership->MD_PROMO_MEMBERSHIP_STATUS_ID_INT == 3 && (session('level') == '99' || session('level') == '9')): ?>
                                                    <a href="javascript:void(0)" onclick="swalUnapprGMData(<?php echo e($dataMembership->MD_PROMO_MEMBERSHIP_ID_INT); ?>)" class="btn btn-danger">
                                                    <?php else: ?>
                                                    <a href="javascript:void(0)" class="btn btn-default">    
                                                    <?php endif; ?>
                                                        <?php if($dataMembership->MD_PROMO_MEMBERSHIP_STATUS_ID_INT <= 2): ?>
                                                        <i>Approve</i>
                                                        <?php else: ?>
                                                        <i>Unapprove</i>
                                                    <?php endif; ?>
                                                    </a>
                                                </td>
                                                <td style="text-align: center;">
                                                     <!-- Admin Pusat IT, SMM, GM -->                                 
                                                    <?php if($dataMembership->MD_PROMO_MEMBERSHIP_STATUS_ID_INT == 1): ?>
                                                    <a href="<?php echo e(URL('/edit_view_promo_membership/' . base64_encode($dataMembership->MD_PROMO_MEMBERSHIP_ID_INT))); ?>" class="btn btn-primary">
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
                                                    <?php if($dataMembership->MD_PROMO_MEMBERSHIP_STATUS_ID_INT == 1): ?>
                                                    <a href="javascript:void(0)" onclick="swalDeleteData(<?php echo e($dataMembership->MD_PROMO_MEMBERSHIP_ID_INT); ?>)" class="btn btn-danger">
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
                                                    <?php if($dataMembership->MD_PROMO_MEMBERSHIP_STATUS_ID_INT == 3 && (session('level') == '99' || session('level') == '9' || session('level') == '11')): ?>
                                                    <a href="javascript:void(0)" onclick="swalTerminateData(<?php echo e($dataMembership->MD_PROMO_MEMBERSHIP_ID_INT); ?>)" class="btn btn-danger">
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
                window.location.href = "/delete_promo_membership/" + param1;
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
                window.location.href = "/terminate_promo_membership/" + param1;
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
                window.location.href = "/appr_smm_promo_membership/" + param1;
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
                window.location.href = "/appr_gm_promo_membership/" + param1;
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
                window.location.href = "/unappr_smm_promo_membership/" + param1;
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
                window.location.href = "/unappr_gm_promo_membership/" + param1;
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/MasterData/PromoMembership/promo_membership.blade.php ENDPATH**/ ?>