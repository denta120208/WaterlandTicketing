@extends('Sales.Report.visitorsByTime.pdfcetak')
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
                        <h1><b><label>Visitors By Time</label></b></h1>
                        <?php
                            $dataProject = DB::select("SELECT * FROM MD_PROJECT AS a WHERE a.PROJECT_NO_CHAR = '".$project_no."'");
                            $userName = trim(session('first_name') . ' ' . session('last_name'));
                        ?>
                        <h3><b>{{$dataProject[0]->PROJECT_NAME}}</b></h3>
                        <h3><b>{{date('d/m/Y', strtotime($report->start_date_param))}} - {{date('d/m/Y', strtotime($report->end_date_param))}}</b></h3>
                        <h3><b>{{$report->kategori_param}}</b></h3>
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
                    <th rowspan="2">MONTH</th>
                    <th colspan='2'>1</th>
                    <th colspan='2'>2</th>
                    <th colspan='2'>3</th>
                    <th colspan='2'>4</th>
                    <th colspan='2'>5</th>
                    <th colspan='2'>6</th>
                    <th colspan='2'>7</th>
                    <th colspan='2'>8</th>
                    <th colspan='2'>9</th>
                    <th colspan='2'>10</th>
                    <th colspan='2'>11</th>
                    <th colspan='2'>12</th>
                    <th colspan='2'>13</th>
                    <th colspan='2'>14</th>
                    <th colspan='2'>15</th>
                    <th colspan='2'>16</th>
                    <th colspan='2'>17</th>
                    <th colspan='2'>18</th>
                    <th colspan='2'>19</th>
                    <th colspan='2'>20</th>
                    <th colspan='2'>21</th>
                    <th colspan='2'>22</th>
                    <th colspan='2'>23</th>
                    <th colspan='2'>24</th>
                    <th colspan='2'>25</th>
                    <th colspan='2'>26</th>
                    <th colspan='2'>27</th>
                    <th colspan='2'>28</th>
                    <th colspan='2'>29</th>
                    <th colspan='2'>30</th>
                    <th colspan='2'>31</th>
                    <th colspan="3">TOTAL</th>
                </tr>
                <tr>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>TICKET</th>
                    <th>PAID</th>
                    <th>PAID (AVG)</th>
                </tr>
            </thead>
            <tbody>
            @if(count($dataVisitorsByTime) > 0)
            <?php
                $Ticket1 = 0; $Paid1 = 0;
                $Ticket2 = 0; $Paid2 = 0;
                $Ticket3 = 0; $Paid3 = 0;
                $Ticket4 = 0; $Paid4 = 0;
                $Ticket5 = 0; $Paid5 = 0;
                $Ticket6 = 0; $Paid6 = 0;
                $Ticket7 = 0; $Paid7 = 0;
                $Ticket8 = 0; $Paid8 = 0;
                $Ticket9 = 0; $Paid9 = 0;
                $Ticket10 = 0; $Paid10 = 0;
                $Ticket11 = 0; $Paid11 = 0;
                $Ticket12 = 0; $Paid12 = 0;
                $Ticket13 = 0; $Paid13 = 0;
                $Ticket14 = 0; $Paid14 = 0;
                $Ticket15 = 0; $Paid15 = 0;
                $Ticket16 = 0; $Paid16 = 0;
                $Ticket17 = 0; $Paid17 = 0;
                $Ticket18 = 0; $Paid18 = 0;
                $Ticket19 = 0; $Paid19 = 0;
                $Ticket20 = 0; $Paid20 = 0;
                $Ticket21 = 0; $Paid21 = 0;
                $Ticket22 = 0; $Paid22 = 0;
                $Ticket23 = 0; $Paid23 = 0;
                $Ticket24 = 0; $Paid24 = 0;
                $Ticket25 = 0; $Paid25 = 0;
                $Ticket26 = 0; $Paid26 = 0;
                $Ticket27 = 0; $Paid27 = 0;
                $Ticket28 = 0; $Paid28 = 0;
                $Ticket29 = 0; $Paid29 = 0;
                $Ticket30 = 0; $Paid30 = 0;
                $Ticket31 = 0; $Paid31 = 0;
                $totalTicket = 0; $totalPaid = 0;
            ?>
            @foreach($dataVisitorsByTime as $data)
                <tr>
                    <td style="text-align: left">{{strtoupper(DateTime::createFromFormat('!m', $data['MONTH'])->format('F'))}}</td>
                    <td style='text-align: right'>{{number_format($data['1_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['1_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['2_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['2_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['3_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['3_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['4_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['4_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['5_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['5_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['6_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['6_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['7_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['7_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['8_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['8_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['9_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['9_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['10_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['10_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['11_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['11_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['12_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['12_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['13_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['13_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['14_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['14_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['15_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['15_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['16_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['16_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['17_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['17_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['18_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['18_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['19_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['19_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['20_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['20_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['21_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['21_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['22_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['22_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['23_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['23_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['24_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['24_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['25_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['25_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['26_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['26_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['27_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['27_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['28_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['28_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['29_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['29_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['30_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['30_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['31_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['31_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['TOTAL_TICKET'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['TOTAL_PAID'],0,',','.')}}</td>
                    <td style='text-align: right'>{{number_format($data['TOTAL_PAID (AVG)'],0,',','.')}}</td>
                </tr>
                <?php
                    $Ticket1 += $data['1_TICKET']; $Paid1 += $data['1_PAID'];
                    $Ticket2 += $data['2_TICKET']; $Paid2 += $data['2_PAID'];
                    $Ticket3 += $data['3_TICKET']; $Paid3 += $data['3_PAID'];
                    $Ticket4 += $data['4_TICKET']; $Paid4 += $data['4_PAID'];
                    $Ticket5 += $data['5_TICKET']; $Paid5 += $data['5_PAID'];
                    $Ticket6 += $data['6_TICKET']; $Paid6 += $data['6_PAID'];
                    $Ticket7 += $data['7_TICKET']; $Paid7 += $data['7_PAID'];
                    $Ticket8 += $data['8_TICKET']; $Paid8 += $data['8_PAID'];
                    $Ticket9 += $data['9_TICKET']; $Paid9 += $data['9_PAID'];
                    $Ticket10 += $data['10_TICKET']; $Paid10 += $data['10_PAID'];
                    $Ticket11 += $data['11_TICKET']; $Paid11 += $data['11_PAID'];
                    $Ticket12 += $data['12_TICKET']; $Paid12 += $data['12_PAID'];
                    $Ticket13 += $data['13_TICKET']; $Paid13 += $data['13_PAID'];
                    $Ticket14 += $data['14_TICKET']; $Paid14 += $data['14_PAID'];
                    $Ticket15 += $data['15_TICKET']; $Paid15 += $data['15_PAID'];
                    $Ticket16 += $data['16_TICKET']; $Paid16 += $data['16_PAID'];
                    $Ticket17 += $data['17_TICKET']; $Paid17 += $data['17_PAID'];
                    $Ticket18 += $data['18_TICKET']; $Paid18 += $data['18_PAID'];
                    $Ticket19 += $data['19_TICKET']; $Paid19 += $data['19_PAID'];
                    $Ticket20 += $data['20_TICKET']; $Paid20 += $data['20_PAID'];
                    $Ticket21 += $data['21_TICKET']; $Paid21 += $data['21_PAID'];
                    $Ticket22 += $data['22_TICKET']; $Paid22 += $data['22_PAID'];
                    $Ticket23 += $data['23_TICKET']; $Paid23 += $data['23_PAID'];
                    $Ticket24 += $data['24_TICKET']; $Paid24 += $data['24_PAID'];
                    $Ticket25 += $data['25_TICKET']; $Paid25 += $data['25_PAID'];
                    $Ticket26 += $data['26_TICKET']; $Paid26 += $data['26_PAID'];
                    $Ticket27 += $data['27_TICKET']; $Paid27 += $data['27_PAID'];
                    $Ticket28 += $data['28_TICKET']; $Paid28 += $data['28_PAID'];
                    $Ticket29 += $data['29_TICKET']; $Paid29 += $data['29_PAID'];
                    $Ticket30 += $data['30_TICKET']; $Paid30 += $data['30_PAID'];
                    $Ticket31 += $data['31_TICKET']; $Paid31 += $data['31_PAID'];
                    $totalTicket += $data['TOTAL_TICKET']; $totalPaid += $data['TOTAL_PAID'];
                ?>
            @endforeach
            <tr>
                <td style="text-align: center; font-size: 120%;"><b>TOTAL</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket1,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid1,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket2,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid2,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket3,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid3,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket4,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid4,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket5,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid5,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket6,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid6,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket7,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid7,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket8,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid8,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket9,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid9,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket10,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid10,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket11,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid11,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket12,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid12,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket13,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid13,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket14,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid14,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket15,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid15,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket16,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid16,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket17,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid17,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket18,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid18,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket19,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid19,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket20,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid20,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket21,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid21,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket22,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid22,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket23,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid23,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket24,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid24,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket25,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid25,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket26,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid26,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket27,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid27,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket28,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid28,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket29,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid29,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket30,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid30,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Ticket31,0,',','.')}}</b></td>
                <td style='text-align: right; font-size: 120%;'><b>{{number_format($Paid31,0,',','.')}}</b></td>
                <td style="text-align: right; font-size: 120%;"><b>{{number_format($totalTicket,0,',','.')}}</b></td>
                <td style="text-align: right; font-size: 120%;"><b>{{number_format($totalPaid,0,',','.')}}</b></td>
                <td style="text-align: right; font-size: 120%;"><b>-</b></td>
            </tr>
            @else
            <tr>
                <td style="text-align: center" colspan="66"><b>No data available</b></td>
            </tr>
            @endif
            </tbody>
        </table>
    </body>
</html>