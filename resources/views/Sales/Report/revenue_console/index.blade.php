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
    .bg-danger {
        background-color: #f8d7da;
    }
    .bg-warning {
        background-color: #fff3cd;
    }
    .bg-success {
        background-color: #d4edda;
    }
</style>

@extends('layouts.mainLayouts')

@section('navbar_header')
    Report Revenue Console - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Report Revenue Console
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
                                    <label>Cut Off <spans style="color: red;">*</span></label>
                                    <input type="date" class="form-control" id="txtCutOff" value="{{ $CUT_OFF_PARAM }}" required>
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
                                        <a target="_blank" class="btn btn-danger" href="{{ URL('/view_report_revenue_console_print/'.base64_encode($CUT_OFF_PARAM)) }}" onclick="window.open(this.href).print(); return false">
                                            Print
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php
                                                DataTables::create(array(
                                                    "name" => "reportTable1",
                                                    "dataSource"=>$report->dataStore("revenue_console_table1"),
                                                    "themeBase"=>"bs4",
                                                    "showFooter" => true,
                                                    "cssClass"=>array(
                                                        "table"=>"table table-striped table-bordered",
                                                        "td"=>function($row, $colName) {
                                                            if($colName == "PROJECT_NAME") {
                                                                return "dt-nowrap";
                                                            }
                                                            else if($colName == "ACTUAL_TODAY" || $colName == "BUDGET_TODAY" || $colName == "ACTUAL_YTD" || $colName == "BUDGET_YTD" || $colName == "ACHIEVEMENT_TODAY" || $colName == "ACHIEVEMENT_YTD") {
                                                                return "dt-nowrap text-right";
                                                            }
                                                            else {
                                                                return "normal-font-size";
                                                            }
                                                        }
                                                    ),
                                                    "columns" => array(
                                                        "indexColumn" => ["label" => "No.", "formatValue" => function($value, $row) { return ""; }, "footerText" => "<b>TOTAL</b>"],
                                                        "PROJECT_NAME" => ["label" => "Project", "formatValue" => function($value, $row) { return $value; }, "footerText" => "<b>-</b>"],
                                                        "ACTUAL_TODAY" => ["label" => "Actual (Today)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "BUDGET_TODAY" => ["label" => "Budget (Today)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "ACHIEVEMENT_TODAY" => ["label" => "Achievement (Today)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,2,',','.') . "%"; }, "footerText" => "<b>-</b>"],
                                                        "ACTUAL_YTD" => ["label" => "Actual (YTD)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "BUDGET_YTD" => ["label" => "Budget (YTD)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "ACHIEVEMENT_YTD" => ["label" => "Achievement (YTD)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,2,',','.') . "%"; }, "footerText" => "<b>-</b>"]
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
        var cut_off = btoa(document.getElementById("txtCutOff").value);
        var url = '{{ url("view_report_revenue_console_excel/cut_off_param") }}';
        url = url.replace('cut_off_param', cut_off);
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
            var cut_off = btoa(document.getElementById("txtCutOff").value);
            var url = '{{ url("view_report_revenue_console/cut_off_param") }}';
            url = url.replace('cut_off_param', cut_off);
            window.location.href = url;
        }
    }

    function validasi() {
        var cut_off = btoa(document.getElementById("txtCutOff").value);
        var totalField = 1;
        var message = "";

        for(var i = 1; i <= totalField; i++) {
            if (message == "") {
                if(cut_off == "") {
                    message += "Cut Off";
                    cut_off = "DONE";
                }
            }
            else {
                if(cut_off == "") {
                    message += ", Cut Off";
                    cut_off = "DONE";
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