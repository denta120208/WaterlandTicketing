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
    Visitors Counter - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Visitors Counter
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
                        <?php echo e(csrf_field()); ?>

                        <div class="row" style="padding-left: 8px; padding-right: 8px;">
                            <a href="javascript:void(0)" style="text-align: center; padding: 40px 30px;" id="BTN_MINUS" name="BTN_MINUS" class="col-sm-1 btn btn-danger float-right small-box"><i class="fa fa-minus"></i></a>
                            <div class="col-sm-10">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3 id="lblVisitorsNow">74</h3>
                                        <p>Visitors Now</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-person-stalker"></i>
                                    </div>
                                </div>
                            </div>
                            <a href="javascript:void(0)" style="text-align: center; padding: 40px 30px;" id="BTN_PLUS" name="BTN_PLUS" class="col-sm-1 btn btn-success float-right small-box"><i class="fa fa-plus"></i></a>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3 id="lblVisitorsIn">74</h3>
                                        <p>Visitors In</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-plus-circled"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3 id="lblVisitorsOut">74</h3>
                                        <p>Visitors Out</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-minus-circled"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Visitors In</label>
                                    <input type="number" name="TXT_VISITORS_IN" onkeypress="return event.charCode >= 48 && event.charCode <= 57" class="form-control" id="TXT_VISITORS_IN" placeholder="Enter Visitors In" value="0"><br />
                                    <a href="javascript:void(0)" id="BTN_VISITORS_IN" name="BTN_VISITORS_IN" class="btn btn-info float-right">Save Visitors In</a>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Visitors Out</label>
                                    <input type="number" name="TXT_VISITORS_OUT" onkeypress="return event.charCode >= 48 && event.charCode <= 57" class="form-control" id="TXT_VISITORS_OUT" placeholder="Enter Visitors Out" value="0"><br />
                                    <a href="javascript:void(0)" id="BTN_VISITORS_OUT" name="BTN_VISITORS_OUT" class="btn btn-info float-right">Save Visitors Out</a>
                                </div>
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

        $.ajax({
            url: '/get_visitors_counter',
            type: "GET",
            dataType: "json",
            success: function (data) {
                var dataVisitorsCounterIn = parseInt(data.dataVisitorsCounterIn[0]['AMOUNT_INT']);
                var dataVisitorsCounterOut = parseInt(data.dataVisitorsCounterOut[0]['AMOUNT_INT']);
                $('#lblVisitorsNow').html(dataVisitorsCounterIn - dataVisitorsCounterOut);
                $('#lblVisitorsIn').html(dataVisitorsCounterIn);
                $('#lblVisitorsOut').html(dataVisitorsCounterOut);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please contact Administrator!'
                });
            }
        });

        $('#BTN_MINUS').on('click', function() {
            var lblVisitorsNow = parseInt($('#lblVisitorsNow').html());
            if(lblVisitorsNow > 0) {
                $.ajax({
                    url: '/send_visitors_counter_minus/' + btoa("1"),
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        var dataVisitorsCounterIn = parseInt(data.dataVisitorsCounterIn[0]['AMOUNT_INT']);
                        var dataVisitorsCounterOut = parseInt(data.dataVisitorsCounterOut[0]['AMOUNT_INT']);
                        $('#lblVisitorsNow').html(dataVisitorsCounterIn - dataVisitorsCounterOut);
                        $('#lblVisitorsIn').html(dataVisitorsCounterIn);
                        $('#lblVisitorsOut').html(dataVisitorsCounterOut);
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Please contact Administrator!'
                        });
                    }
                });
            }
        });

        $('#BTN_PLUS').on('click', function() {
            $.ajax({
                url: '/send_visitors_counter_plus/' + btoa("1"),
                type: "GET",
                dataType: "json",
                success: function (data) {
                    var dataVisitorsCounterIn = parseInt(data.dataVisitorsCounterIn[0]['AMOUNT_INT']);
                    var dataVisitorsCounterOut = parseInt(data.dataVisitorsCounterOut[0]['AMOUNT_INT']);
                    $('#lblVisitorsNow').html(dataVisitorsCounterIn - dataVisitorsCounterOut);
                    $('#lblVisitorsIn').html(dataVisitorsCounterIn);
                    $('#lblVisitorsOut').html(dataVisitorsCounterOut);
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please contact Administrator!'
                    });
                }
            });
        });

        $('#BTN_VISITORS_OUT').on('click', function() {
            if(isEmpty($('#TXT_VISITORS_OUT').val())) {
                $('#TXT_VISITORS_OUT').val("0");
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Cannot input empty value!'
                });
            }
            else {
                var lblVisitorsNow = parseInt($('#lblVisitorsNow').html());
                var txtVisitorsOut = parseInt($('#TXT_VISITORS_OUT').val());

                if(lblVisitorsNow >= txtVisitorsOut && lblVisitorsNow > 0 && txtVisitorsOut > 0) {
                    $.ajax({
                        url: '/send_visitors_counter_minus/' + btoa(txtVisitorsOut),
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            var dataVisitorsCounterIn = parseInt(data.dataVisitorsCounterIn[0]['AMOUNT_INT']);
                            var dataVisitorsCounterOut = parseInt(data.dataVisitorsCounterOut[0]['AMOUNT_INT']);
                            $('#lblVisitorsNow').html(dataVisitorsCounterIn - dataVisitorsCounterOut);
                            $('#lblVisitorsIn').html(dataVisitorsCounterIn);
                            $('#lblVisitorsOut').html(dataVisitorsCounterOut);
                            $('#TXT_VISITORS_OUT').val("0");
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Please contact Administrator!'
                            });
                        }
                    });
                }
                else if(txtVisitorsOut === 0) {
                    $('#TXT_VISITORS_OUT').val("0");
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'The value of Visitors In cannot be zero!'
                    });
                }
                else {
                    $('#TXT_VISITORS_OUT').val("0");
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'The value of Visitors Out is greater than Visitors Now!'
                    });
                }
            }
        });

        $('#BTN_VISITORS_IN').on('click', function() {
            if(isEmpty($('#TXT_VISITORS_IN').val())) {
                $('#TXT_VISITORS_IN').val("0");
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Cannot input empty value!'
                });
            }
            else {
                var lblVisitorsNow = parseInt($('#lblVisitorsNow').html());
                var txtVisitorsIn = parseInt($('#TXT_VISITORS_IN').val());

                if(txtVisitorsIn > 0) {
                    $.ajax({
                        url: '/send_visitors_counter_plus/' + btoa(txtVisitorsIn),
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            var dataVisitorsCounterIn = parseInt(data.dataVisitorsCounterIn[0]['AMOUNT_INT']);
                            var dataVisitorsCounterOut = parseInt(data.dataVisitorsCounterOut[0]['AMOUNT_INT']);
                            $('#lblVisitorsNow').html(dataVisitorsCounterIn - dataVisitorsCounterOut);
                            $('#lblVisitorsIn').html(dataVisitorsCounterIn);
                            $('#lblVisitorsOut').html(dataVisitorsCounterOut);
                            $('#TXT_VISITORS_IN').val("0");
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Please contact Administrator!'
                            });
                        }
                    });
                }
                else {
                    $('#TXT_VISITORS_IN').val("0");
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'The value of Visitors In cannot be zero!'
                    });
                }
            }
        });
    });

    function isEmpty(str) {
        return (!str || str.length === 0 );
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/watergroup/public_html/metland_water/resources/views/Scanning/VisitorsCounter/visitors_counter.blade.php ENDPATH**/ ?>