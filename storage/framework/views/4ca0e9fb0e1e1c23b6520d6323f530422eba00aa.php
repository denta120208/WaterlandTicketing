<?php $__env->startSection('navbar_header'); ?>
    List Membership - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    List Membership
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
                                    <a class="form-control btn btn-info" href="<?php echo e(route('add_new_membership')); ?>">
                                        Add New Membership
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
                                            <th>Nama</th>
                                            <th>Unit</th>
                                            <th>KTP</th>
                                            <th>No Telp</th>
                                            <th>Alamat</th>
                                            <th>Tipe Member</th>
                                            <th>Period Member</th>
                                            <th>Harga Member</th>
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

<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/MasterData/Membership/membership.blade.php ENDPATH**/ ?>