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



<?php $__env->startSection('navbar_header'); ?>
    Scan Ticket Purchase - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Scan Ticket Purchase
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
                    
                    <div class="row" style="padding-bottom: 5%;">
                        <div class="col-sm-6">
                            <form action="<?php echo e(url('change_camera_setting')); ?>" method="post">
                                <?php echo e(csrf_field()); ?>

                                <div class="form-group">
                                    <label>Camera Setting <span style="color: red;">*</span></label>
                                    <select id="CAM_SETTING" name="CAM_SETTING" class="form-control select2" style="width: 100%;" required>
                                        <option value="CAMERA_DISABLED" <?php echo e($dataCameraSetting->SETTING == "CAMERA_DISABLED" ? "selected" : ""); ?>>Turn Off Camera</option>
                                        <option value="user" <?php echo e($dataCameraSetting->SETTING == "user" ? "selected" : ""); ?>>Kamera Depan</option>
                                        <option value="environment" <?php echo e($dataCameraSetting->SETTING == "environment" ? "selected" : ""); ?>>Kamera Belakang</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <div class="col-sm-6">
                            <form action="<?php echo e(url('input_qr_number')); ?>" method="post">
                                <?php echo e(csrf_field()); ?>

                                <div class="form-group">
                                    <label>QRCode Number <span style="color: red;">*</span></label>
                                    <input id="QR_NUMBER" name="QR_NUMBER" class="form-control" placeholder="Insert QRCode Here" required />
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary float-right">Scan QR Number</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php if($dataCameraSetting->SETTING != "CAMERA_DISABLED"): ?>
                    <form>
                        <div id="reader"></div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script> -->
<script src="<?php echo e(asset('scanningqrcode/html5-qrcode-v2.3.4.min.js')); ?>" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#CAM_SETTING").change(function() {
            $(this).parents("form").submit();
        });
    });

    <?php if($dataCameraSetting->SETTING != "CAMERA_DISABLED") { ?>
        
    const html5QrCode = new Html5Qrcode("reader");
    const qrCodeSuccessCallback = (decodedText, decodedResult) => {
        html5QrCode.pause();
        var timerSwal = 1000;
        $.ajax({
            url: '/scanning_ticket_purchase/' + decodedText,
            type: "GET",
            dataType: "json",
            success: function (data) {
                if(parseInt(data.code) == 200) {
                    let timerInterval
                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome!',
                        html: data.message,
                        timer: timerSwal,
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        showCancelButton: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            // Swal.showLoading()
                        },
                        willClose: () => {
                            clearInterval(timerInterval)
                            html5QrCode.resume();
                        }
                        }).then((result) => {
                        
                        if (result.dismiss === Swal.DismissReason.timer) {
                            console.log('I was closed by the timer')
                        }
                    });
                }
                else if(parseInt(data.code) == 404) {
                    let timerInterval
                    Swal.fire({
                        icon: 'error',
                        title: 'QRCode Invalid!',
                        html: data.message,
                        timer: timerSwal,
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        showCancelButton: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            // Swal.showLoading()
                        },
                        willClose: () => {
                            clearInterval(timerInterval)
                            html5QrCode.resume();
                            // location.reload();
                        }
                        }).then((result) => {
                        
                        if (result.dismiss === Swal.DismissReason.timer) {
                            console.log('I was closed by the timer')
                        }
                    });
                }
            },
            error: function() {
                let timerInterval
                Swal.fire({
                    icon: 'error',
                    title: 'QRCode Invalid!',
                    html: "",
                    timer: timerSwal,
                    timerProgressBar: true,
                    allowOutsideClick: false,
                    showCancelButton: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        // Swal.showLoading()
                    },
                    willClose: () => {
                        clearInterval(timerInterval)
                        html5QrCode.resume();
                        // location.reload();
                    }
                    }).then((result) => {
                    
                    if (result.dismiss === Swal.DismissReason.timer) {
                        console.log('I was closed by the timer')
                    }
                });
            }
        });
    };
    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

    // Kamera Data Ambil Dari DB
    html5QrCode.start({ facingMode: "<?php echo $dataCameraSetting->SETTING; ?>" }, config, qrCodeSuccessCallback);

    // Kamera Depan
    // html5QrCode.start({ facingMode: "user" }, config, qrCodeSuccessCallback);

    // Kamera Belakang
    // html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback);

    html5QrCode.stop().then((ignore) => {
    }).catch((err) => {
    });

    html5QrCode.pause().then((ignore) => {
    }).catch((err) => {
    });

    html5QrCode.resume().then((ignore) => {
    }).catch((err) => {
    });

    <?php } ?>

    const isValidUrl = urlString => {
        try {
            return Boolean(new URL(urlString));
        }
        catch(e) {
            return false;
        }
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/Scanning/ScanTicketPurchase/scan_ticket_purchase.blade.php ENDPATH**/ ?>