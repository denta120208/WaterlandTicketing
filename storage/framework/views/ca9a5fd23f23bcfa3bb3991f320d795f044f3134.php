<?php $__env->startSection('navbar_header'); ?>
    Form Master Data Tenant - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form Master Data Tenant
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
        $(document).ready(function()
        {
            $('#vendor_table').DataTable({
                pageLength : 25,
                dom: 'Bfrtip',
                buttons:
                [
                    {
                        extend: 'excelHtml5',
                        title : '<?php echo "List AP Purchase Order"; ?>'
                    },
                    {
                        extend: 'csv',
                        exportOptions :{
                            columns:[1,2,3,4,5,6,7]
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions :{
                            columns:[1,2,3,4,5,6,7]
                        }
                    }
                ]
            });
        });
    });
</script>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-lg-4 mb-2">
                            <a class="btn btn-success" href="<?php echo URL::route('masterdata.tenant.viewadddatatenant'); ?>" role="button">
                                Add Data Tenant
                            </a>
                        </div>
                    </div>
                    <br><br>
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12">
                            <table class="table-striped table-hover compact" id="vendor_table" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Document</th>
                                    <th>Name</th>
                                    <th>PPH Status</th>
                                    <th>NPWP</th>
                                    <th>Telp.</th>
                                    <th>View/Edit</th>
                                    <th>Delete</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                <?php $__currentLoopData = $tenant; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($i); ?></td>
                                        <td><?php echo e($data->MD_TENANT_NOCHAR); ?></td>
                                        <td><?php echo e($data->MD_TENANT_NAME_CHAR); ?></td>
                                        <?php if($data->MD_TENANT_PPH_INT == 1): ?>
                                            <td>Potong Tenant</td>
                                        <?php elseif($data->MD_TENANT_PPH_INT == 0): ?>
                                            <td>Potong Sendiri</td>
                                        <?php else: ?>
                                            <td>NONE</td>
                                        <?php endif; ?>
                                        <td><?php echo e($data->MD_TENANT_NPWP); ?></td>
                                        <td><?php echo e($data->MD_TENANT_TELP); ?></td>
                                        <td class="center">
                                            <a class="btn btn-sm btn-warning" href="<?php echo e(URL('/master_data/tenant/view_edit_data/' . $data->MD_TENANT_ID_INT)); ?>">
                                                <i>
                                                    View/Edit
                                                </i>
                                            </a>
                                        </td>
                                        <td class="center">
                                            <a href="#confModal<?php echo $data->MD_TENANT_ID_INT; ?>" class="btn btn-sm btn-danger" data-toggle="modal">
                                                <i>
                                                    Delete
                                                </i>
                                            </a>
                                            <div id="confModal<?php echo $data->MD_TENANT_ID_INT; ?>" class="modal fade">
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
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                            <a href="<?php echo e(URL('/master_data/tenant/deletedatatenant/'. $data->MD_TENANT_ID_INT)); ?>" class="btn btn-success">Delete</a>
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

<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/page/masterdata/tenant/listDataTenant.blade.php ENDPATH**/ ?>