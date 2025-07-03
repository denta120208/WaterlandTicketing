@extends('Sales.Report.revenue_console.pdfcetak')
@section('content_report')

<html>
    <header>
        <style>
            @page { margin: 10mm;}
            table, th, td {
                border: 0px black;
            }
            th, td {
                padding: 1px;
            }
            div.headeratas{
                padding-left: 2em;
                margin-left: 9em;
                text-align:center;
                margin-top:-1em;
                font-size: 10px
            }
            div.headeratas5{
                padding-left: 2em;
                margin-left: 9em;
                text-align:center;
                margin-top:5em;
                font-size: 10px
            }
            div.headeratastengah{
                padding-left: 2em;
                /*margin-left: 8em;*/
                text-align:left;
                margin-top:-1em;
                margin-left:20em;
                font-size: 10px
            }
            div.headerbawahtengah{
                padding-left: 2em;
                margin-left: -1em;
                text-align:center;
                margin-top: 1em;
                font-size: 12px;
            }
            div.headerbawahtengah2{
                margin-top: 1em;
                font-size: 12px;
            }
            div.headerbawahtengah3{
                margin-top: 1em;
                font-size: 14px;
            }
            div.headerataskiri{
                text-align:left;
                margin-right:-7em;
                margin-top: -1em;
            }
            div.headerataskiri5{
                text-align:left;
                margin-right:-7em;
                margin-top: -30px;
                font-size: 9px;
            }
            div.headeratastengah5{
                padding-left: 2em;
                text-align:left;
                margin-top: -30px;
                margin-left:20em;
                font-size: 9px;
            }
            div.headerataskanan5{
                margin-top:-30px;
                text-align:right;
                font-size: 9px;
            }
            div.headerbawahtengah5{
                margin-left: 5em;
                text-align:center;
                margin-top: 1em;
                font-size: 12px;
            }
            div.headerbawahkiri{
                text-align:left; 
            }
            div.headerataskanan{
                margin-top:2em;
                text-align:right;
                font-size: 8px;
            }
            div.headerbawahkanan{
                margin-top:-3em;
                text-align:right;
                font-size: 8px;
            }
            table.kananatas{
                align:right;
            }
            div.tanggalbawah{
                 text-align: left;
                 font-size: 11px;
            }
            div.ttdatas{
               margin-top:0em;
               text-align: left;
               font-size: 11px;
            }
            div.ttdbawah{
               margin-top:2em;
               text-align: left;
            }
            div.salesbawah{
               margin-top:0em;
               margin-right:8em;
               margin-bottom:5em;
               text-align: right;
            }
            div.bawah{
               margin-right:6em;
               text-align: right;
            }
            div.tableCustomer{
                text-align:left;
                margin-left: 2em;
                padding-left: 2em;
            }
            div.termAndConditionAtas{
                text-align:left;
                font-size: 10px;
                margin-bottom:-1em;
            }
            div.termAndConditionBawah{
                 text-align:left;
                 margin-left: 3em;
                 font-size: 10px;
            }
            div.termAndConditionNextPage{
                 text-align:left;
                 padding: 1em;
                 font-size: 12px;
                 margin-top:0em;
            }
            div.rekeningAtasTermConditions{
                 text-align:center;
                 margin-left: 2em;
                 font-size: 12px;
            }
            div.rekeningBawahTermConditions{
                 text-align:center;
                 margin-left: -2em;
                 font-size: 12px;
            }
            div.Customer{
                padding-left: 3em;
                font-size: 11px;
            }
            div.dataBookingEntry{
                 font-size: 11px;
                 margin-top: -5em;
            }
            div.page-break {
                page-break-after: always;
            }
            div.tabelCicilan{
                font-size: 13px;
                text-align: center;
                padding: 1em;
                padding-bottom: 1em;
            }
            div.parafTermCondition{
                 text-align: right;
                 font-size: 11px;
            }
            thead{
                font-size: 11px;
                text-align: center;
                margin-bottom: 1em;  
            }
            tbody{
                font-size: 11px;
                text-align: left;
            }
            table {
                border-collapse: collapse;
            }
            table, th, td {
                border: 1px solid black;
                padding: 7px;
            }
            #watermark p {
                position: absolute;
                top: 0;
                left: 0;
                color: #0c0c0c;
                margin-top: 300;
                margin-left: 200;
                font-size: 130;
                font-family: "Arial Black", "Arial Bold";
                opacity: 0.15;
                pointer-events: none;
            }
        </style>
    </header>
    <body>
        <div class="row">
            <div id="watermark">
                <b><p>Metland</p></b>
            </div>
        </div>
        <div class="row">
            <div>
                <center>
                    <div class="headerbawahtengah">
                        <h1><b><label>Revenue Console</label></b></h1>
                        <?php
                            $userName = trim(session('first_name') . ' ' . session('last_name'));
                        ?>
                        <h3><b>Cut Off : {{ date('d/m/Y', strtotime($report->cut_off_param)) }}</b></h3>
                        <h3><b>Printed by {{$userName}} at {{date('d/m/Y H:i')}}</b></h3>
                    </div>
                </center>
            </div>
            <hr />
        </div>
        <br>
        <table class="stripe hover compact" id="data_bill_payment" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Project</th>
                    <th>Actual (Today)</th>
                    <th>Budget (Today)</th>
                    <th>Achievement (Today)</th>
                    <th>Actual (YTD)</th>
                    <th>Budget (YTD)</th>
                    <th>Achievement (YTD)</th>
                </tr>
            </thead>
            <tbody>
            @if(count($dataRevenueConsole) > 0)
            <?php $i = 1; $totalActualToday = 0; $totalBudgetToday = 0; $totalActualYTD = 0; $totalBudgetYTD = 0; ?>
            @foreach($dataRevenueConsole as $data)
                <tr>
                    <td style="text-align: left">{{ $i }}</td>
                    <td style="text-align: left">{{ $data['PROJECT_NAME'] }}</td>
                    <td style="text-align: right">{{number_format($data['ACTUAL_TODAY'],0,',','.')}}</td>
                    <td style="text-align: right">{{number_format($data['BUDGET_TODAY'],0,',','.')}}</td>
                    <td style="text-align: right">{{number_format($data['ACHIEVEMENT_TODAY'],2,',','.')}}%</td>
                    <td style="text-align: right">{{number_format($data['ACTUAL_YTD'],0,',','.')}}</td>
                    <td style="text-align: right">{{number_format($data['BUDGET_YTD'],0,',','.')}}</td>
                    <td style="text-align: right">{{number_format($data['ACHIEVEMENT_YTD'],2,',','.')}}%</td>
                </tr>
                <?php $i++; $totalActualToday += $data['ACTUAL_TODAY']; $totalBudgetToday += $data['BUDGET_TODAY']; $totalActualYTD += $data['ACTUAL_YTD']; $totalBudgetYTD += $data['BUDGET_YTD']; ?>
            @endforeach
            <tr>
                    <td style="text-align: left; font-size: 120%;"><b>TOTAL</b></td>
                    <td style="text-align: left; font-size: 120%;"><b>-</b></td>
                    <td style="text-align: right; font-size: 120%;"><b>{{ number_format($totalActualToday,0,',','.') }}</b></td>
                    <td style="text-align: right; font-size: 120%;"><b>{{ number_format($totalBudgetToday,0,',','.') }}</b></td>
                    <td style="text-align: right; font-size: 120%;"><b>-</b></td>
                    <td style="text-align: right; font-size: 120%;"><b>{{ number_format($totalActualYTD,0,',','.') }}</b></td>
                    <td style="text-align: right; font-size: 120%;"><b>{{ number_format($totalBudgetYTD,0,',','.') }}</b></td>
                    <td style="text-align: right; font-size: 120%;"><b>-</b></td>
                </tr>
            @else
            <tr>
                <td style="text-align: center" colspan="8"><b>No data available</b></td>
            </tr>
            @endif
            </tbody>
        </table>
    </body>
</html>