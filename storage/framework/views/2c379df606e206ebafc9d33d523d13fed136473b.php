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
    use \koolreport\datagrid\DataTables;
    use Illuminate\Support\Str;
?>

<style>
    .normal-font-size {
        font-size: 100%;
    }
    .dt-nowrap {
        font-size: 100%;
        white-space: nowrap;
    }
    tfoot td:nth-child(1) {
        text-align: left;
    }
    tfoot td:nth-child(2) {
        text-align: left;
    }
    thead {
        text-align: center;
    }
    tfoot {
        text-align: right;
    }
</style>



<?php $__env->startSection('navbar_header'); ?>
    Report Revenue By Payment Method - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Report Revenue By Payment Method
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Start Date <spans style="color: red;">*</span></label>
                                    <input type="date" class="form-control" id="txtStartDate" placeholder="Input Start Date..." value="<?php echo e($START_DATE_PARAM); ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>End Date <spans style="color: red;">*</span></label>
                                    <input type="date" class="form-control" id="txtEndDate" placeholder="Input End Date..." value="<?php echo e($END_DATE_PARAM); ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Kategori <span style="color: red;">*</span></label>
                                    <select id="ddlKategori" name="ddlKategori" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <option value="ALL" <?php echo e($KATEGORI_PARAM == "ALL" ? "selected" : ""); ?>>ALL</option>
                                        <option value="Ticket" <?php echo e($KATEGORI_PARAM == "Ticket" ? "selected" : ""); ?>>Ticket</option>
                                        <option value="Equipment" <?php echo e($KATEGORI_PARAM == "Equipment" ? "selected" : ""); ?>>Equipment</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2" style="padding-top: 31px;">
                                <div class="form-group">
                                    <input type="button" class="form-control btn btn-info" value="View Data" onclick="getSubmit();">
                                </div>
                            </div>
                        </div>
                        <br />
                        <?php if($IS_POST == TRUE): ?>
                            <div class="row">
                                <div class="col-md" style="overflow-x:auto;">
                                    <div style='text-align: center; margin-bottom:30px;'>
                                        <input type="button" class="btn btn-success" value="Download Excel" onclick="getExcel();">
                                        <a target="_blank" class="btn btn-danger" href="<?php echo e(URL('/view_report_rev_ticket_by_payment_method_print/'.base64_encode($START_DATE_PARAM).'/'.base64_encode($END_DATE_PARAM).'/'.base64_encode($KATEGORI_PARAM))); ?>" onclick="window.open(this.href).print(); return false">
                                            Print
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span style="color: red; padding-left: 5px;"><b>* Include PB1 & PPH</b></span>
                                            <?php
                                                DataTables::create(array(
                                                    "name" => "reportTable1",
                                                    "dataSource"=>$report->dataStore("revenue_ticket_by_payment_method_table1"),
                                                    "themeBase"=>"bs4",
                                                    "showFooter" => true,
                                                    "cssClass"=>array(
                                                        "table"=>"table table-striped table-bordered",
                                                        "td"=>function($row, $colName) {                                                            
                                                            if($colName == "PAYMENT_METHOD_DESC_CHAR") {
                                                                return "dt-nowrap";
                                                            }
                                                            else if($colName == "QTY_TICKET_INT" || $colName == "TICKET_FREE_INT" || $colName == "DISCOUNT_NOMINAL_FLOAT" || $colName == "TOTAL") {
                                                                return "dt-nowrap text-right";
                                                            }
                                                            else {
                                                                return "normal-font-size";
                                                            }
                                                        }
                                                    ),
                                                    "columns" => array(
                                                        "indexColumn" => ["label" => "No.", "formatValue" => function($value, $row) { return ""; }, "footerText" => "<b>TOTAL</b>"],
                                                        "PAYMENT_METHOD_DESC_CHAR" => ["label" => "Payment Method", "formatValue" => function($value, $row) { return $value; }, "footerText" => "<b>-</b>"],
                                                        "QTY_TICKET_INT" => ["label" => "Qty", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "TICKET_FREE_INT" => ["label" => "Qty Free", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "DISCOUNT_NOMINAL_FLOAT" => ["label" => "Total Discount", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "TOTAL" => ["label" => "Total After Discount", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"]
                                                    ),
                                                    "fastRender" => false,
                                                    "options"=>array(
                                                        "scrollX" => false,
                                                        "paging"=>true,
                                                        "searching"=>true,
                                                        'autoWidth' => true,
                                                        "select" => false,
                                                        "order"=>array(
                                                            array(0,"asc")
                                                        )
                                                    ),
                                                    "onReady" => "function() {
                                                        reportTable1.on( 'order.dt search.dt', function () {
                                                            reportTable1.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                                                                cell.innerHTML = i+1;
                                                            } );
                                                        } ).draw();
                                                    }",
                                                    "searchOnEnter" => false,
                                                    "searchMode" => "or"
                                                ));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
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
    });

    function getExcel() {
        var start_date = btoa(document.getElementById("txtStartDate").value);
        var end_date = btoa(document.getElementById("txtEndDate").value);
        var kategori = btoa(document.getElementById("ddlKategori").value);
        var url = '<?php echo e(url("view_report_rev_ticket_by_payment_method_excel/start_date_param/end_date_param/kategori_param")); ?>';
        url = url.replace('start_date_param', start_date);
        url = url.replace('end_date_param', end_date);
        url = url.replace('kategori_param', kategori);
        window.location.href = url;
    }

    function getSubmit() {
        message = validasi();
        isValid = false;
        if(message == "") {
            isValid = true;
        }
        
        if(isValid == false) {
            Swal.fire(
                'Failed',
                message,
                'error'
            );
        }
        else {
            var start_date = btoa(document.getElementById("txtStartDate").value);
            var end_date = btoa(document.getElementById("txtEndDate").value);
            var kategori = btoa(document.getElementById("ddlKategori").value);
            var url = '<?php echo e(url("view_report_rev_ticket_by_payment_method/start_date_param/end_date_param/kategori_param")); ?>';
            url = url.replace('start_date_param', start_date);
            url = url.replace('end_date_param', end_date);
            url = url.replace('kategori_param', kategori);
            window.location.href = url;
        }
    }

    function validasi() {
        var start_date = btoa(document.getElementById("txtStartDate").value);
        var end_date = btoa(document.getElementById("txtEndDate").value);
        var kategori = btoa(document.getElementById("ddlKategori").value);
        var totalField = 3;
        var message = "";

        for(var i = 1; i <= totalField; i++) {
            if (message == "") {
                if(start_date == "") {
                    message += "Start Date";
                    start_date = "DONE";
                }
                else if(end_date == "") {
                    message += "End Date";
                    end_date = "DONE";
                }
                else if(kategori == "") {
                    message += "Kategori";
                    kategori = "DONE";
                }
            }
            else {
                if(start_date == "") {
                    message += ", Start Date";
                    start_date = "DONE";
                }
                else if(end_date == "") {
                    message += ", End Date";
                    end_date = "DONE";
                }
                else if(kategori == "") {
                    message += ", Kategori";
                    kategori = "DONE";
                }
            }

            if(message != "" && i == totalField) {
                message += " Tidak Boleh Kosong!";
            }
        }

        return message;
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/watergroup/public_html/metland_water/resources/views/Sales/Report/revenueTicketByPaymentMethod/index.blade.php ENDPATH**/ ?>