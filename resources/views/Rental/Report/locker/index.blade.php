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
        text-align: center;
    }
    thead {
        text-align: center;
    }
    tfoot {
        text-align: right;
    }
</style>

@extends('layouts.mainLayouts')

@section('navbar_header')
    Report Locker - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Report Locker
@endsection

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label>Start Date <spans style="color: red;">*</span></label>
                                    <input type="date" class="form-control" id="txtStartDate" placeholder="Input Start Date..." value="{{ $START_DATE_PARAM }}" required>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label>End Date <spans style="color: red;">*</span></label>
                                    <input type="date" class="form-control" id="txtEndDate" placeholder="Input End Date..." value="{{ $END_DATE_PARAM }}" required>
                                </div>
                            </div>
                            <div class="col-sm-2" style="padding-top: 31px;">
                                <div class="form-group">
                                    <input type="button" class="form-control btn btn-info" value="View Data" onclick="getSubmit();">
                                </div>
                            </div>
                        </div>
                        <br />
                        @if($IS_POST == TRUE)
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-md" style="overflow-x:auto;">
                                            <div style='text-align: center; margin-bottom:30px;'>
                                                <input type="button" class="btn btn-success" value="Download Excel" onclick="getExcel();">
                                                <a target="_blank" class="btn btn-danger" href="{{ URL('/view_report_locker_print/'.base64_encode($START_DATE_PARAM).'/'.base64_encode($END_DATE_PARAM)) }}" onclick="window.open(this.href).print(); return false">
                                                    Print
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <h4><b>Transactions</b></h4>
                                            <?php
                                                DataTables::create(array(
                                                    "name" => "reportTable1",
                                                    "dataSource"=>$report->dataStore("locker_report_table1"),
                                                    "themeBase"=>"bs4",
                                                    "showFooter" => true,
                                                    "cssClass"=>array(
                                                        "table"=>"table table-striped table-bordered",
                                                        "td"=>function($row, $colName) {                                                            
                                                            if($colName == "TRANS_RENTAL_LOCKER_ID_INT" || $colName == "TRANS_LOCKER_NO_CHAR" || $colName == "CUSTOMER_NAME_CHAR" || $colName == "NO_TELP_CHAR" || $colName == "CASHIER_NAME_CHAR" || $colName == "created_at" || $colName == "RETUR_BY" || $colName == "RETUR_AT") {
                                                                return "dt-nowrap text-center";
                                                            }
                                                            else if($colName == "QTY_INT" || $colName == "TOTAL_HARGA_FLOAT" || $colName == "TOTAL_PAID_FLOAT" || $colName == "TOTAL_CHANGE_FLOAT") {
                                                                return "dt-nowrap text-right";
                                                            }
                                                            else {
                                                                return "normal-font-size";
                                                            }
                                                        }
                                                    ),
                                                    "columns" => array(
                                                        "TRANS_RENTAL_LOCKER_ID_INT" => ["label" => "No.", "formatValue" => function($value, $row) { return $value; }, "footerText" => "<b>TOTAL</b>"],
                                                        "TRANS_LOCKER_NO_CHAR" => ["label" => "Transaction Number", "formatValue" => function($value, $row) { return $value; }, "footerText" => "<b>-</b>"],
                                                        "CUSTOMER_NAME_CHAR" => ["label" => "Customer", "formatValue" => function($value, $row) { return $value; }, "footerText" => "<b>-</b>"],
                                                        "NO_TELP_CHAR" => ["label" => "Telp", "formatValue" => function($value, $row) { return $value; }, "footerText" => "<b>-</b>"],
                                                        "QTY_INT" => ["label" => "Qty", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "TOTAL_HARGA_FLOAT" => ["label" => "Price", "formatValue" => function($value, $row) { return $value == "-" ? "-" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "TOTAL_PAID_FLOAT" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "-" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "TOTAL_CHANGE_FLOAT" => ["label" => "Change", "formatValue" => function($value, $row) { return $value == "-" ? "-" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "CASHIER_NAME_CHAR" => ["label" => "Cashier", "formatValue" => function($value, $row) { return $value; }, "footerText" => "<b>-</b>"],
                                                        "created_at" => ["label" => "Transaction Date", "formatValue" => function($value, $row) { return date('Y-m-d H:i:s', strtotime($value)); }, "footerText" => "<b>-</b>"],
                                                        "RETUR_BY" => ["label" => "Retur By", "formatValue" => function($value, $row) { return $value; }, "footerText" => "<b>-</b>"],
                                                        "RETUR_AT" => ["label" => "Retur Time", "formatValue" => function($value, $row) { return $value == "-" ? "-" : date('Y-m-d H:i:s', strtotime($value)); }, "footerText" => "<b>-</b>"]
                                                    ),
                                                    "options"=>array(
                                                        "scrollX" => true,
                                                        "paging"=>true,
                                                        "searching"=>true,
                                                        'autoWidth' => true,
                                                        "select" => false,
                                                        "order"=>array(
                                                            array(0,"desc")
                                                        )
                                                    ),
                                                    "searchOnEnter" => false,
                                                    "searchMode" => "or"
                                                ));
                                            ?>
                                        </div>
                                    </div>
                                    <br /><br />
                                    <div class="row">
                                        <div class="col-md" style="overflow-x:auto;">
                                            <div style='text-align: center; margin-bottom:30px;'>
                                                <input type="button" class="btn btn-success" value="Download Excel" onclick="getDetailsExcel();">
                                                <a target="_blank" class="btn btn-danger" href="{{ URL('/view_report_locker_details_print/'.base64_encode($START_DATE_PARAM).'/'.base64_encode($END_DATE_PARAM)) }}" onclick="window.open(this.href).print(); return false">
                                                    Print
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <h4><b>Transactions Details</b></h4>
                                            <?php
                                                DataTables::create(array(
                                                    "name" => "reportTable2",
                                                    "dataSource"=>$report->dataStore("locker_details_report_table1"),
                                                    "themeBase"=>"bs4",
                                                    "showFooter" => true,
                                                    "cssClass"=>array(
                                                        "table"=>"table table-striped table-bordered",
                                                        "td"=>function($row, $colName) {
                                                            if($colName == "TRANS_RENTAL_LOCKER_DETAIL_ID_INT" || $colName == "TRANS_LOCKER_NO_CHAR" || $colName == "DESC_CHAR" || $colName == "created_at") {
                                                                return "dt-nowrap text-center";
                                                            }
                                                            else if($colName == "HARGA_FLOAT") {
                                                                return "dt-nowrap text-right";
                                                            }
                                                            else {
                                                                return "normal-font-size";
                                                            }
                                                        }
                                                    ),
                                                    "columns" => array(
                                                        "TRANS_RENTAL_LOCKER_DETAIL_ID_INT" => ["label" => "No.", "formatValue" => function($value, $row) { return $value; }, "footerText" => "<b>TOTAL</b>"],
                                                        "TRANS_LOCKER_NO_CHAR" => ["label" => "Transaction Number", "formatValue" => function($value, $row) { return $value; }, "footerText" => "<b>-</b>"],
                                                        "DESC_CHAR" => ["label" => "Description", "formatValue" => function($value, $row) { return $value; }, "footerText" => "<b>-</b>"],
                                                        "HARGA_FLOAT" => ["label" => "Price", "formatValue" => function($value, $row) { return $value == "-" ? "-" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "created_at" => ["label" => "Transaction Date", "formatValue" => function($value, $row) { return date('Y-m-d H:i:s', strtotime($value)); }, "footerText" => "<b>-</b>"]
                                                    ),
                                                    "options"=>array(
                                                        "scrollX" => false,
                                                        "paging"=>true,
                                                        "searching"=>true,
                                                        'autoWidth' => true,
                                                        "select" => false,
                                                        "order"=>array(
                                                            array(0,"desc")
                                                        )
                                                    ),
                                                    "searchOnEnter" => false,
                                                    "searchMode" => "or"
                                                ));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
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
        var url = '{{ url("view_report_locker_excel/start_date_param/end_date_param") }}';
        url = url.replace('start_date_param', start_date);
        url = url.replace('end_date_param', end_date);
        window.location.href = url;
    }

    function getDetailsExcel() {
        var start_date = btoa(document.getElementById("txtStartDate").value);
        var end_date = btoa(document.getElementById("txtEndDate").value);
        var url = '{{ url("view_report_locker_details_excel/start_date_param/end_date_param") }}';
        url = url.replace('start_date_param', start_date);
        url = url.replace('end_date_param', end_date);
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
            var url = '{{ url("view_report_locker/start_date_param/end_date_param") }}';
            url = url.replace('start_date_param', start_date);
            url = url.replace('end_date_param', end_date);
            window.location.href = url;
        }
    }

    function validasi() {
        var start_date = btoa(document.getElementById("txtStartDate").value);
        var end_date = btoa(document.getElementById("txtEndDate").value);
        var totalField = 2;
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
            }

            if(message != "" && i == totalField) {
                message += " Tidak Boleh Kosong!";
            }
        }

        return message;
    }
</script>
@endsection