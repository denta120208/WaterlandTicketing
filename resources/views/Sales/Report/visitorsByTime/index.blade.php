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
    thead {
        text-align: center;
    }
    tfoot {
        text-align: right;
    }
</style>

@extends('layouts.mainLayouts')

@section('navbar_header')
    Report Visitors By Time - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Report Visitors By Time
@endsection

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Start Date <spans style="color: red;">*</span></label>
                                    <input type="date" class="form-control" id="txtStartDate" placeholder="Input Start Date..." value="{{ $START_DATE_PARAM }}" required>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>End Date <spans style="color: red;">*</span></label>
                                    <input type="date" class="form-control" id="txtEndDate" placeholder="Input End Date..." value="{{ $END_DATE_PARAM }}" required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Kategori <span style="color: red;">*</span></label>
                                    <select id="ddlKategori" name="ddlKategori" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <option value="Perhari" {{ $KATEGORI_PARAM == "Perhari" ? "selected" : "" }}>Perhari</option>
                                        <option value="Perminggu" {{ $KATEGORI_PARAM == "Perminggu" ? "selected" : "" }}>Perminggu</option>
                                        <option value="Perbulan" {{ $KATEGORI_PARAM == "Perbulan" ? "selected" : "" }}>Perbulan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-1" style="padding-top: 31px;">
                                <div class="form-group">
                                    <input type="button" class="form-control btn btn-info" value="View" onclick="getSubmit();">
                                </div>
                            </div>
                        </div>
                        <br />
                        @if($IS_POST == TRUE)
                            <div class="row">
                                <div class="col-md" style="overflow-x:auto;">
                                    <div style='text-align: center; margin-bottom:30px;'>
                                        <input type="button" class="btn btn-success" value="Download Excel" onclick="getExcel();">
                                        <a target="_blank" class="btn btn-danger" href="{{ URL('/view_report_visitors_by_time_print/'.base64_encode($START_DATE_PARAM).'/'.base64_encode($END_DATE_PARAM).'/'.base64_encode($KATEGORI_PARAM)) }}" onclick="window.open(this.href).print(); return false">
                                            Print
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @if($KATEGORI_PARAM == "Perhari")
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span style="color: red; padding-left: 5px;"><b>* Include PB1 & PPH</b></span>
                                            <?php
                                                DataTables::create(array(
                                                    "name" => "reportTable1",
                                                    "dataSource"=>$report->dataStore("visitors_by_time_table1"),
                                                    "complexHeaders" => true,
                                                    "headerSeparator" => "_",
                                                    "themeBase"=>"bs4",
                                                    "showFooter" => true,
                                                    "cssClass"=>array(
                                                        "table"=>"table table-striped table-bordered",
                                                        "td"=>function($row, $colName) {                                                            
                                                            if($colName == "YEAR") {
                                                                return "dt-nowrap";
                                                            }
                                                            else if($colName == "1_TICKET" || $colName == "1_PAID" || $colName == "2_TICKET" || $colName == "2_PAID" ||
                                                                    $colName == "3_TICKET" || $colName == "3_PAID" || $colName == "4_TICKET" || $colName == "4_PAID" ||
                                                                    $colName == "5_TICKET" || $colName == "5_PAID" || $colName == "6_TICKET" || $colName == "6_PAID" ||
                                                                    $colName == "7_TICKET" || $colName == "7_PAID" || $colName == "8_TICKET" || $colName == "8_PAID" ||
                                                                    $colName == "9_TICKET" || $colName == "9_PAID" || $colName == "10_TICKET" || $colName == "10_PAID" ||
                                                                    $colName == "11_TICKET" || $colName == "11_PAID" || $colName == "12_TICKET" || $colName == "12_PAID" ||
                                                                    $colName == "13_TICKET" || $colName == "13_PAID" || $colName == "14_TICKET" || $colName == "14_PAID" ||
                                                                    $colName == "15_TICKET" || $colName == "15_PAID" || $colName == "16_TICKET" || $colName == "16_PAID" ||
                                                                    $colName == "17_TICKET" || $colName == "17_PAID" || $colName == "18_TICKET" || $colName == "18_PAID" ||
                                                                    $colName == "19_TICKET" || $colName == "19_PAID" || $colName == "20_TICKET" || $colName == "20_PAID" ||
                                                                    $colName == "21_TICKET" || $colName == "21_PAID" || $colName == "22_TICKET" || $colName == "22_PAID" ||
                                                                    $colName == "23_TICKET" || $colName == "23_PAID" || $colName == "24_TICKET" || $colName == "24_PAID" ||
                                                                    $colName == "25_TICKET" || $colName == "25_PAID" || $colName == "26_TICKET" || $colName == "26_PAID" ||
                                                                    $colName == "27_TICKET" || $colName == "27_PAID" || $colName == "28_TICKET" || $colName == "28_PAID" ||
                                                                    $colName == "29_TICKET" || $colName == "29_PAID" || $colName == "30_TICKET" || $colName == "30_PAID" ||
                                                                    $colName == "31_TICKET" || $colName == "31_PAID" ||
                                                                    $colName == "TOTAL_TICKET" || $colName == "TOTAL_PAID" || $colName == "TOTAL_PAID (AVG)") {
                                                                return "dt-nowrap text-right";
                                                            }
                                                            else {
                                                                return "normal-font-size";
                                                            }
                                                        }
                                                    ),
                                                    "columns" => array(
                                                        "MONTH" => ["label" => "MONTH", "formatValue" => function($value, $row) { return DateTime::createFromFormat('!m', $value)->format('F'); }, "footerText" => "<b>TOTAL</b>"],
                                                        "1_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "1_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "2_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "2_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "3_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "3_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "4_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "4_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "5_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "5_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "6_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "6_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "7_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "7_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "8_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "8_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "9_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "9_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "10_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "10_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "11_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "11_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "12_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "12_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "13_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "13_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "14_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "14_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "15_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "15_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "16_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "16_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "17_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "17_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "18_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "18_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "19_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "19_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "20_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "20_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "21_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "21_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "22_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "22_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "23_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "23_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "24_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "24_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "25_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "25_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "26_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "26_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "27_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "27_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "28_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "28_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "29_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "29_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "30_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "30_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "31_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "31_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "TOTAL_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "TOTAL_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "TOTAL_PAID (AVG)" => ["label" => "Paid (AVG)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footerText" => "<b>-</b>"]
                                                    ),
                                                    "options"=>array(
                                                        "scrollX" => true,
                                                        "paging"=>true,
                                                        "searching"=>true,
                                                        'autoWidth' => true,
                                                        "select" => false,
                                                        "order"=>[],
                                                        "ordering" => false
                                                    ),
                                                    "searchOnEnter" => false,
                                                    "searchMode" => "or"
                                                ));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @elseif($KATEGORI_PARAM == "Perminggu")
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span style="color: red; padding-left: 5px;"><b>* Include PB1 & PPH</b></span>
                                            <?php
                                                DataTables::create(array(
                                                    "name" => "reportTable1",
                                                    "dataSource"=>$report->dataStore("visitors_by_time_table1"),
                                                    "complexHeaders" => true,
                                                    "headerSeparator" => "_",
                                                    "themeBase"=>"bs4",
                                                    "showFooter" => true,
                                                    "cssClass"=>array(
                                                        "table"=>"table table-striped table-bordered",
                                                        "td"=>function($row, $colName) {                                                            
                                                            if($colName == "MONTH") {
                                                                return "dt-nowrap";
                                                            }
                                                            else if($colName == "WEEK 1_TICKET" || $colName == "WEEK 1_PAID" || $colName == "WEEK 2_TICKET" || $colName == "WEEK 2_PAID" || $colName == "WEEK 3_TICKET" || $colName == "WEEK 3_PAID" || $colName == "WEEK 4_TICKET" || $colName == "WEEK 4_PAID" || $colName == "WEEK 5_TICKET" || $colName == "WEEK 5_PAID" || $colName == "TOTAL_TICKET" || $colName == "TOTAL_PAID" || $colName == "TOTAL_PAID (AVG)") {
                                                                return "dt-nowrap text-right";
                                                            }
                                                            else {
                                                                return "normal-font-size";
                                                            }
                                                        }
                                                    ),
                                                    "columns" => array(
                                                        "MONTH" => ["label" => "MONTH", "formatValue" => function($value, $row) { return DateTime::createFromFormat('!m', $value)->format('F'); }, "footerText" => "<b>TOTAL</b>"],
                                                        "WEEK 1_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "WEEK 1_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "WEEK 2_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "WEEK 2_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "WEEK 3_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "WEEK 3_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "WEEK 4_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "WEEK 4_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "WEEK 5_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "WEEK 5_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "TOTAL_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "TOTAL_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "TOTAL_PAID (AVG)" => ["label" => "Paid (AVG)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footerText" => "<b>-</b>"]
                                                    ),
                                                    "fastRender" => true,
                                                    "options"=>array(
                                                        "scrollX" => true,
                                                        "paging"=>true,
                                                        "searching"=>true,
                                                        'autoWidth' => true,
                                                        "select" => false,
                                                        "order"=>[],
                                                        "ordering" => false
                                                    ),
                                                    "searchOnEnter" => false,
                                                    "searchMode" => "or"
                                                ));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @elseif($KATEGORI_PARAM == "Perbulan")
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <span style="color: red; padding-left: 5px;"><b>* Include PB1 & PPH</b></span>
                                            <?php
                                                DataTables::create(array(
                                                    "name" => "reportTable1",
                                                    "dataSource"=>$report->dataStore("visitors_by_time_table1"),
                                                    "complexHeaders" => true,
                                                    "headerSeparator" => "_",
                                                    "themeBase"=>"bs4",
                                                    "showFooter" => true,
                                                    "cssClass"=>array(
                                                        "table"=>"table table-striped table-bordered",
                                                        "td"=>function($row, $colName) {                                                            
                                                            if($colName == "YEAR") {
                                                                return "dt-nowrap";
                                                            }
                                                            else if($colName == "JAN_TICKET" || $colName == "JAN_PAID" || $colName == "FEB_TICKET" || $colName == "FEB_PAID" ||
                                                                    $colName == "MAR_TICKET" || $colName == "MAR_PAID" || $colName == "APR_TICKET" || $colName == "APR_PAID" ||
                                                                    $colName == "MAY_TICKET" || $colName == "MAY_PAID" || $colName == "JUN_TICKET" || $colName == "JUN_PAID" ||
                                                                    $colName == "JUL_TICKET" || $colName == "JUL_PAID" || $colName == "AUG_TICKET" || $colName == "AUG_PAID" ||
                                                                    $colName == "SEP_TICKET" || $colName == "SEP_PAID" || $colName == "OCT_TICKET" || $colName == "OCT_PAID" ||
                                                                    $colName == "NOV_TICKET" || $colName == "NOV_PAID" || $colName == "DEC_TICKET" || $colName == "DEC_PAID" ||
                                                                    $colName == "TOTAL_TICKET" || $colName == "TOTAL_PAID" || $colName == "TOTAL_PAID (AVG)") {
                                                                return "dt-nowrap text-right";
                                                            }
                                                            else {
                                                                return "normal-font-size";
                                                            }
                                                        }
                                                    ),
                                                    "columns" => array(
                                                        "YEAR" => ["label" => "YEAR", "formatValue" => function($value, $row) { return $value; }, "footerText" => "<b>TOTAL</b>"],
                                                        "JAN_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "JAN_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "FEB_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "FEB_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "MAR_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "MAR_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "APR_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "APR_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "MAY_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "MAY_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "JUN_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "JUN_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "JUL_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "JUL_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "AUG_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "AUG_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "SEP_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "SEP_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "OCT_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "OCT_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "NOV_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "NOV_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "DEC_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "DEC_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "TOTAL_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "TOTAL_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "<b>@value</b>"],
                                                        "TOTAL_PAID (AVG)" => ["label" => "Paid (AVG)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footerText" => "<b>-</b>"]
                                                    ),
                                                    "options"=>array(
                                                        "scrollX" => true,
                                                        "paging"=>true,
                                                        "searching"=>true,
                                                        'autoWidth' => true,
                                                        "select" => false,
                                                        "order"=>[],
                                                        "ordering" => false
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
        var kategori = btoa(document.getElementById("ddlKategori").value);
        var url = '{{ url("view_report_visitors_by_time_excel/start_date_param/end_date_param/kategori_param") }}';
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
            var url = '{{ url("view_report_visitors_by_time/start_date_param/end_date_param/kategori_param") }}';
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
@endsection