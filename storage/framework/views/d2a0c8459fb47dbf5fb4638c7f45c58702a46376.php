<?php $__env->startSection('navbar_header'); ?>
    Form Discount Schedule - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form Discount Schedule
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

<script>
    $(document).ready(function() {
        $('#vendor_table').DataTable({
            order : [],
            pageLength : 25,
            scrollX: true
        });
    });
</script>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <br><br>
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12">
                            <table class="table-striped table-hover compact" id="vendor_table" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Document</th>
                                    <th>Shop</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Process</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i = 1;
                                ?>
                                <?php $__currentLoopData = $schedDisc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($i); ?></td>
                                        <td><?php echo e($data->PSM_SCHED_DISC_NOCHAR); ?></td>
                                        <td><?php echo e($data->SHOP_NAME_CHAR); ?></td>
                                        <td><?php echo e($data->PSM_TRANS_DISC_TYPE); ?></td>
                                        <?php if($data->PSM_TRANS_DISC_TYPE == 'Percentation'): ?>
                                            <td><?php echo e(number_format($data->PSM_SCHED_DISC_AMT,2,',','.')); ?>%</td>
                                        <?php else: ?>
                                            <td><?php echo e(number_format($data->PSM_SCHED_DISC_AMT,2,',','.')); ?></td>
                                        <?php endif; ?>
                                        <td><?php echo e($data->PSM_TRANS_DISC_STATUS_INT); ?></td>
                                        <?php if($data->PSM_TRANS_DISC_STATUS_INT == 'APPROVE'): ?>
                                        <td class="center">
                                            <a class="btn btn-sm btn-success" href="<?php echo e(URL('/marketing/leaseagreement/viewprocessdiscount/' . $data->PSM_SCHED_DISC_ID_INT)); ?>">
                                                <i>
                                                    Process
                                                </i>
                                            </a>
                                        </td>
                                        <?php else: ?>
                                        <td class="center">
                                            <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                <i>
                                                    Process
                                                </i>
                                            </a>
                                        </td>
                                        <?php endif; ?>
                                        <?php
                                        $i++;
                                        ?>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/page/leaseagreement/listDataScheduleDiscount.blade.php ENDPATH**/ ?>