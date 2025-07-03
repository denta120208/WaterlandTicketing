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

@extends('layouts.mainLayouts')

@section('navbar_header')
    Report Revenue - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Report Revenue
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
                                <div class="col-md" style="overflow-x:auto;">
                                    <div style='text-align: center; margin-bottom:30px;'>
                                        <input type="button" class="btn btn-success" value="Download Excel" onclick="getExcel();">
                                        <a target="_blank" class="btn btn-danger" href="{{ URL('/view_report_revenue_print/'.base64_encode($START_DATE_PARAM).'/'.base64_encode($END_DATE_PARAM)) }}" onclick="window.open(this.href).print(); return false">
                                            Print
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span style="color: red; padding-left: 5px;"><b>* Paid After Discount</b></span>
                                            <?php
                                                DataTables::create(array(
                                                    "name" => "reportTable1",
                                                    "dataSource"=>$report->dataStore("revenue_table1"),
                                                    "themeBase"=>"bs4",
                                                    "showFooter" => true,
                                                    "cssClass"=>array(
                                                        "table"=>"table table-striped table-bordered",
                                                        "td"=>function($row, $colName) {                                                            
                                                            if($colName == "DESC_CHAR") {
                                                                return "dt-nowrap";
                                                            }
                                                            else if($colName == "QTY_INT" || $colName == "PAID" || $colName == "PAID_AVG" || $colName == "PAID_BEFORE_PB1" || $colName == "PAID_PB1" || $colName == "PAID_AVG_BEFORE_PB1") {
                                                                return "dt-nowrap text-right";
                                                            }
                                                            else {
                                                                return "normal-font-size";
                                                            }
                                                        }
                                                    ),
                                                    "columns" => array(
                                                        "indexColumn" => ["label" => "No.", "formatValue" => function($value, $row) { return ""; }, "footerText" => "<b>TOTAL</b>"],
                                                        "DESC_CHAR" => ["label" => "Description", "formatValue" => function($value, $row) { return $value; }, "footerText" => "<b>-</b>"],
                                                        "QTY_INT" => ["label" => "Qty + Free", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "PAID_BEFORE_PB1" => ["label" => "Paid Exclude PB1", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "PAID_PB1" => ["label" => "Paid PB1", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "PAID" => ["label" => "Paid Include PB1", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "PAID_AVG_BEFORE_PB1" => ["label" => "Paid Exclude PB1 (AVG)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footerText" => "<b>-</b>"],
                                                        "PAID_AVG" => ["label" => "Paid Include PB1 (AVG)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footerText" => "<b>-</b>"]
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
        var url = '{{ url("view_report_revenue_excel/start_date_param/end_date_param") }}';
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
            var url = '{{ url("view_report_revenue/start_date_param/end_date_param") }}';
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