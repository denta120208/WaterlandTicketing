<?php $__env->startSection('navbar_header'); ?>
    Form Discount Schedule Approval - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form Discount Schedule Approval
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

    $(document).ready(function()
    {
        $('#vendor_table').DataTable({
            order : [],
            pageLength : 25,
            scrollX: true
        });
    } );

</script>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12">
                            <table class="table-striped table-hover compact" id="vendor_table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Document</th>
                                        <th>Lot</th>
                                        <th>Shop</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Schedule</th>
                                        <th>Approve</th>
                                        <th>Cancel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                <?php $__currentLoopData = $schedDisc; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($i); ?></td>
                                        <td><?php echo e($data->PSM_SCHED_DISC_NOCHAR); ?></td>
                                        <td><?php echo e($data->LOT_STOCK_NO); ?></td>
                                        <td><?php echo e($data->SHOP_NAME_CHAR); ?></td>
                                        <td><?php echo e($data->PSM_TRANS_DISC_TYPE); ?></td>
                                        <?php if($data->PSM_TRANS_DISC_TYPE == 'Percentation'): ?>
                                            <td><?php echo e(number_format($data->PSM_SCHED_DISC_AMT,2,',','.')); ?>%</td>
                                        <?php else: ?>
                                            <td><?php echo e(number_format($data->PSM_SCHED_DISC_AMT,2,',','.')); ?></td>
                                        <?php endif; ?>
                                        <?php if($data->PSM_SCHED_DISC_TYPE == 'SCHEDULE'): ?>
                                            <td>
                                                <?php
                                                $sched = DB::table('PSM_SCHEDULE')->where('PSM_TRANS_NOCHAR', $data->PSM_TRANS_NOCHAR)
                                                    ->where('PSM_SCHED_DISC_NOCHAR','=',$data->PSM_SCHED_DISC_NOCHAR)
                                                    ->get();

                                                foreach ($sched as $sc)
                                                {
                                                    $dateSched=date_create($sc->TGL_SCHEDULE_DATE);
                                                    echo date_format($dateSched,"d-m-Y").' / '.$sc->DESC_CHAR.'<br>';
                                                }
                                                ?>
                                            </td>
                                        <?php else: ?>
                                            <td>-</td>
                                        <?php endif; ?>
                                        <td class="center">
                                            <a href="#approveModal<?php echo $data->PSM_SCHED_DISC_ID_INT; ?>" class="btn btn-sm btn-success" data-toggle="modal">
                                                <i>
                                                    Approve
                                                </i>
                                            </a>
                                            <div id="approveModal<?php echo $data->PSM_SCHED_DISC_ID_INT; ?>" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure approve this document ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="<?php echo e(URL('/marketing/leaseagreement/approvedatascheddisc/'. $data->PSM_SCHED_DISC_ID_INT)); ?>" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="center">
                                            <a href="#confModal<?php echo $data->PSM_SCHED_DISC_ID_INT; ?>" class="btn btn-sm btn-danger" data-toggle="modal">
                                                <i>
                                                    Cancel
                                                </i>
                                            </a>
                                            <div id="confModal<?php echo $data->PSM_SCHED_DISC_ID_INT; ?>" class="modal fade">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Confirmation</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <p>Are you sure cancel this document ?</p>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                            <a href="<?php echo e(URL('/marketing/leaseagreement/canceldatascheddisc/'. $data->PSM_SCHED_DISC_ID_INT)); ?>" class="btn btn-success">Yes</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <?php $i++; ?>
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

<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/page/leaseagreement/listDataScheduleDiscountAppr.blade.php ENDPATH**/ ?>