@extends('Sales.Report.pos.pdfcetak')
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
                        <h1><b><label>POS</label></b></h1>
                        <?php
                            $dataProject = DB::select("SELECT * FROM MD_PROJECT AS a WHERE a.PROJECT_NO_CHAR = '".$project_no."'");
                            $userName = trim(session('first_name') . ' ' . session('last_name'));
                        ?>
                        <h3><b>{{$dataProject[0]->PROJECT_NAME}}</b></h3>
                        <h3><b>{{date('d/m/Y', strtotime($report->start_date_param))}} - {{date('d/m/Y', strtotime($report->end_date_param))}}</b></h3>
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
                    <th>No</th>
                    <th>Transaction Number</th>
                    <th>Customer</th>
                    <th>Qty</th>
                    <th>Qty Free</th>
                    <th>Discount (%)</th>
                    <th>Discount Nominal</th>
                    <th>Price</th>
                    <th>Paid</th>
                    <th>Change</th>
                    <th>Cashier</th>
                    <th>Transaction Date</th>
                </tr>
            </thead>
            <tbody>
            @if(count($dataPOS) > 0)
            <?php $qty = 0; $price = 0; $paid = 0; $change = 0; $qtyFree = 0; $discountPercent = 0; $discountNominal = 0; $deposit = 0; $refund = 0; ?>
            @foreach($dataPOS as $data)
                <tr>
                    <td style="text-align: center;">{{$data['TRANS_POS_ID_INT']}}</td>
                    <td style="text-align: center;">{{$data['TRANS_POS_NO_CHAR']}}</td>
                    <td style="text-align: center;">{{$data['CUSTOMER_NAME_CHAR']}}</td>
                    <td style="text-align: right;">{{number_format($data['QTY_INT'], 0, ",", ".")}}</td>
                    <td style="text-align: right;">{{number_format($data['QTY_FREE_INT'], 0, ",", ".")}}</td>
                    <td style="text-align: right;">{{(float) $data['DISCOUNT_PERCENT_FLOAT']}}%</td>
                    <td style="text-align: right;">{{number_format($data['DISCOUNT_NOMINAL_FLOAT'], 0, ",", ".")}}</td>
                    <td style="text-align: right;">{{number_format($data['TOTAL_HARGA_FLOAT'], 0, ",", ".")}}</td>
                    <td style="text-align: right;">{{number_format($data['TOTAL_PAID_FINAL_FLOAT'], 0, ",", ".")}}</td>
                    <td style="text-align: right;">{{number_format($data['TOTAL_CHANGE_FLOAT'], 0, ",", ".")}}</td>
                    <td style="text-align: center;">{{$data['CASHIER_NAME_CHAR']}}</td>
                    <td style="text-align: center;">{{date('Y-m-d H:i:s', strtotime($data['created_at']))}}</td>
                </tr>
                <?php
                    $qty += $data['QTY_INT'];
                    $price += $data['TOTAL_HARGA_FLOAT'];
                    $paid += $data['TOTAL_PAID_FINAL_FLOAT'];
                    $change += $data['TOTAL_CHANGE_FLOAT'];
                    $qtyFree += $data['QTY_FREE_INT'];
                    $discountPercent += $data['DISCOUNT_PERCENT_FLOAT'];
                    $discountNominal += $data['DISCOUNT_NOMINAL_FLOAT'];
                ?>
            @endforeach
            <tr>
                <td style="text-align: center; font-size: 120%;"><b>TOTAL</b></td>
                <td style="text-align: center; font-size: 120%;"><b></b></td>
                <td style="text-align: center; font-size: 120%;"><b></b></td>
                <td style="text-align: right; font-size: 120%;"><b>{{number_format($qty, 0, ",", ".")}}</b></td>
                <td style="text-align: right; font-size: 120%;"><b>{{number_format($qtyFree, 0, ",", ".")}}</b></td>
                <td style="text-align: right; font-size: 120%;"><b>{{(float) $discountPercent}}%</b></td>
                <td style="text-align: right; font-size: 120%;"><b>{{number_format($discountNominal, 0, ",", ".")}}</b></td>
                <td style="text-align: right; font-size: 120%;"><b>{{number_format($price, 0, ",", ".")}}</b></td>
                <td style="text-align: right; font-size: 120%;"><b>{{number_format($paid, 0, ",", ".")}}</b></td>
                <td style="text-align: right; font-size: 120%;"><b>{{number_format($change, 0, ",", ".")}}</b></td>
                <td style="text-align: center; font-size: 120%;"><b></b></td>
                <td style="text-align: center; font-size: 120%;"><b></b></td>
            </tr>
            @else
            <tr>
                <td style="text-align: center" colspan="12"><b>No data available</b></td>
            </tr>
            @endif
            </tbody>
        </table>
    </body>
</html>