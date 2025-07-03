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
    List Ticket Purchase - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    List Ticket Purchase
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
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <a class="form-control btn btn-info" href="<?php echo e(route('buy_ticket_purchase')); ?>">
                                        Buy Ticket
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <br />
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md" style="overflow-x:auto;">
                            <h4><b>Transactions (Today)</b></h4>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Transaction Number</th>
                                        <th>Qty</th>
                                        <th>Qty Free</th>
                                        <th>Discount (%)</th>
                                        <th>Discount Nominal</th>
                                        <th>Price</th>
                                        <th>Paid</th>
                                        <th>Change</th>
                                        <th>Cashier</th>
                                        <th>Transaction Date</th>
                                        <th>Print Ticket</th>
                                        <th>Print Receipt</th>
                                        <th>Cancel Ticket</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $dataTicket; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td style="text-align: center;"><?php echo e($data->TRANS_TICKET_COUNT_INT); ?></td>
                                        <td style="text-align: center;"><?php echo e($data->TRANS_TICKET_NOCHAR); ?></td>
                                        <td style="text-align: center;"><?php echo e(number_format($data->QTY_TICKET_INT, 0, ",", ".")); ?></td>
                                        <td style="text-align: center;"><?php echo e(number_format($data->TICKET_FREE_INT, 0, ",", ".")); ?></td>
                                        <td style="text-align: center;"><?php echo e((float) $data->DISCOUNT_PERCENT_FLOAT); ?>%</td>
                                        <td style="text-align: center;"><?php echo e(number_format($data->DISCOUNT_NOMINAL_FLOAT, 0, ",", ".")); ?></td>
                                        <td style="text-align: center;"><?php echo e(number_format($data->TOTAL_PRICE_NUM, 0, ",", ".")); ?></td>
                                        <td style="text-align: center;"><?php echo e(number_format($data->TOTAL_PAID_NUM, 0, ",", ".")); ?></td>
                                        <td style="text-align: center;"><?php echo e(number_format($data->TOTAL_CHANGE_NUM, 0, ",", ".")); ?></td>
                                        <td style="text-align: center;"><?php echo e($data->CASHIER_NAME_CHAR); ?></td>
                                        <td style="text-align: center;"><?php echo e(date('Y-m-d H:i:s', strtotime($data->created_at))); ?></td>
                                        <td style="text-align: center;">
                                            <a href="<?php echo e(URL('/print_ticket_purchase/' . $data->TRANS_TICKET_ID_INT)); ?>" class="btn btn-primary" onclick="window.open(this.href, '_blank', 'noreferrer'); return false">
                                                <i>
                                                    Print
                                                </i>
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="<?php echo e(URL('/print_receipt_ticket_purchase/' . $data->TRANS_TICKET_ID_INT)); ?>" class="btn btn-success" onclick="window.open(this.href, '_blank', 'noreferrer'); return false">
                                                <i>
                                                    Print
                                                </i>
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="javascript:void(0)" onclick="swalCancelData(<?php echo e($data->TRANS_TICKET_ID_INT); ?>)" class="btn btn-danger">
                                                <i>
                                                    Cancel
                                                </i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                            </div>
                        </div>

                        <br /><br />
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md" style="overflow-x:auto;">
                            <h4><b>Transactions Details (Today)</b></h4>
                            <table id="example2" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Transaction Number</th>
                                        <th>Ticket Number</th>
                                        <th>Cashier</th>
                                        <th>Transaction Date</th>
                                        <th>Scanned</th>
                                        <th>Scanned By</th>
                                        <th>Scanned Time</th>
                                        <th>Print Ticket</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $dataTicketDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td style="text-align: center;"><?php echo e($data->TRANS_TICKET_DETAIL_COUNT_INT); ?></td>
                                        <td style="text-align: center;"><?php echo e($data->TRANS_TICKET_NOCHAR); ?></td>
                                        <td style="text-align: center;"><?php echo e($data->NUMBER_TICKET); ?></td>
                                        <td style="text-align: center;"><?php echo e($data->CASHIER_NAME_CHAR); ?></td>
                                        <td style="text-align: center;"><?php echo e(date('Y-m-d H:i:s', strtotime($data->created_at))); ?></td>
                                        <td style="text-align: center;"><?php echo e($data->IS_SCAN == 0 ? "NO" : "YES"); ?></td>
                                        <td style="text-align: center;"><?php echo e($data->SCAN_BY == NULL ? "NONE" : $data->SCAN_BY); ?></td>
                                        <td style="text-align: center;"><?php echo e($data->SCAN_AT == NULL ? "NONE" : date('Y-m-d H:i:s', strtotime($data->SCAN_AT))); ?></td>
                                        <td style="text-align: center;">
                                            <a href="<?php echo e(URL('/print_ticket_purchase_one/' . $data->TRANS_TICKET_DETAIL_ID_INT)); ?>" class="btn btn-primary" onclick="window.open(this.href, '_blank', 'noreferrer'); return false">
                                                <i>
                                                    Print
                                                </i>
                                            </a>
                                        </td>
                                    </tr>
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

        $("#example2").DataTable({
            "order": [[0, 'desc']],
            "scrollY": true, "scrollX": true,
            "responsive": false, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
    });

    function swalCancelData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Cancel</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/cancel_ticket_purchase/" + param1;
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/watergroup/public_html/metland_water/resources/views/Sales/TicketPurchase/ticket_purchase.blade.php ENDPATH**/ ?>