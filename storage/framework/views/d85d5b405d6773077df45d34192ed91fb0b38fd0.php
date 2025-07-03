<?php $__env->startSection('navbar_header'); ?>
    Form Lot - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form Lot
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

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-2">
                            <a href="<?php echo e(route('lot.lotmaster.viewadddatalot')); ?>" class="btn bg-gradient-success btn-sm">
                                Entry Lot Master
                            </a>
                        </div>
                    </div>
                    <br><br>
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12">
                            <table class="table-striped table-hover compact" id="optutility_list" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Lot</th>
                                        <th>Type</th>
                                        <th>Level</th>
                                        <th>Zone</th>
                                        <th>Sqm</th>
                                        <th>Release</th>
                                        <th>Rent</th>
                                        <th>View/Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php $__currentLoopData = $listDataLot; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($no); ?></td>
                                            <td><?php echo e($data->LOT_STOCK_NO); ?></td>
                                            <td><?php echo e($data->LOT_TYPE_DESC); ?></td>
                                            <td><?php echo e($data->LOT_LEVEL_DESC); ?></td>
                                            <td><?php echo e($data->LOT_ZONE_DESC); ?></td>
                                            <td style="text-align: right;"><?php echo e(number_format($data->LOT_STOCK_SQM,2,',','.')); ?></td>
                                            <td><?php echo e($data->ON_RELEASE_STAT_INT); ?></td>
                                            <td><?php echo e($data->ON_RENT_STAT_INT); ?></td>
                                            <td class="center">
                                                <a class="btn btn-sm btn-warning" href="<?php echo e(URL('/lot/lotmaster/vieweditdatalot/' . $data->LOT_STOCK_ID_INT)); ?>">
                                                    <i>
                                                        View/Edit
                                                    </i>
                                                </a>
                                            </td>
                                            <?php if($data->ON_RENT_STAT_INT == 'RENT'): ?>
                                            <td class="center">
                                                <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                    <i>
                                                        Delete
                                                    </i>
                                                </a>
                                            </td>
                                            <?php else: ?>
                                            <td class="center">
                                                <a href="#deleteLot<?php echo $data->LOT_STOCK_ID_INT; ?>" class="btn btn-sm btn-danger" data-toggle="modal">
                                                    <i>
                                                        Delete
                                                    </i>
                                                </a>
                                                <div id="deleteLot<?php echo $data->LOT_STOCK_ID_INT; ?>" class="modal fade">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Confirmation</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <p>Are you sure delete this document ?</p>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                                <a href="<?php echo e(URL('/lot/lotmaster/deletedatalot/'.$data->LOT_STOCK_ID_INT)); ?>" class="btn btn-success">Yes</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php $no += 1; ?>
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

<script type="text/javascript">
    function formSubmit()
    {
        $("#deleteForm").submit();
    }
</script>
<script>
    $(document).ready(function(){
        $('#optutility_list').DataTable( {
            order : [],
            pageLength : 25,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: '<?php echo "Data Lot"; ?>'
                },
                {
                    extend: 'pdfHtml5',
                    footer: true,
                    title: '<?php echo "Data Lot"; ?>'
                }
            ]
        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    });
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/page/lotmaster/list.blade.php ENDPATH**/ ?>