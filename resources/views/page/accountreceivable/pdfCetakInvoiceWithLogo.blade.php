@extends('page.leaseagreement.pdfcetak')
@section('content')

<html>
    <header>
        <script>
            $(document).ready(function()
            {
                $('#cicilan_table').DataTable();
            });
        </script>

        <style>
            .pagebreak { page-break-before: always; }
            @page {
                /*                margin: 10mm;*/
                margin-top: 70.08px;
                margin-bottom: 95px;
                margin-left: 10px;
                margin-right: 75.84px;
            }

            ol.ur {list-style-type: upper-roman;}
            ol.la {list-style-type: lower-alpha;}
            ol.num {list-style-type: decimal;}
            ol.lr {list-style-type: lower-roman;}


            ol {
                list-style: none;
                margin: 0;
                padding: 5px;
            }
            li {
                text-align: justify;
                margin: 0;
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
                text-align:right;
                margin-top:-98px;
            }
            div.headerbawahtengah{
                padding-left: 2em;
                margin-left: -1em;
                text-align:center;
                margin-top: 1em;
                font-size: 12px;
            }
            div.headerbawahtengah2{
                /*    padding-left: 5em;*/
                /*margin-left: 3em;
                text-align:center;*/
                margin-top: 35px;
                font-size: 15px;
                font-family: "Arial Narrow", Arial, sans-serif;
            }

            div.headerbawahtengah3{
                /*    padding-left: 5em;*/
                margin-left: 3em;
                text-align:center;
                /*                margin-top: -1em;*/
                font-size: 16px;
                font-family: "Arial Narrow", Arial, sans-serif;
                margin-bottom: -30px;
            }

            .nomorsurat{
                /*    padding-left: 5em;*/
                /*                margin-left: 3em;*/
                text-align:center;
                margin-top: -1em;
                font-size: 16px;
                font-family: "Arial Narrow", Arial, sans-serif;
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
                /*margin-left: 8em;*/
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
                /*    padding-left: 5em;*/
                margin-left: 5em;
                text-align:center;
                margin-top: 1em;
                font-size: 16px;
                font-family: Calibri, Helvetica, Arial, sans-serif;

            }

            div.headerbawahkiri{
                text-align:left;
                font-size: 16px;
                font-family: Calibri, Helvetica, Arial, sans-serif;
            }
            div.headerataskanan{
                /*margin-top:2em;*/
                text-align:right;
                font-size: 12px;
                font-family: Calibri, Helvetica, Arial, sans-serif;
            }

            div.headerbawahkanan{
                margin-top:-3em;
                text-align:right;
                font-size: 16px;
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
                /*                padding-left: 3em;*/
                font-size: 10px;
                font-family: "Arial Narrow", Arial, sans-serif;

            }
            div.termAndConditionAtas{
                text-align:left;
                font-size: 16px;
                margin-bottom:-1em;
                font-family: "Arial Narrow", Arial, sans-serif;
            }

            div.termAndConditionAtas_1{
                text-align:left;
                font-size: 16px;
                margin-bottom:-30px;
                font-family: "Arial Narrow", Arial, sans-serif;
            }

            div.termAndConditionTtd{
                text-align:left;
                font-size: 16px;
                margin-bottom:2px;
                font-family: "Arial Narrow", Arial, sans-serif;
            }

            div.termAndConditionBawah{
                text-align:left;
                margin-left: 3em;
                font-size: 14px;
                font-family: Calibri, Helvetica, Arial, sans-serif;
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
                font-size: 12px;
                font-family: "Arial Narrow", Arial, sans-serif;
            }

            div.dataBookingEntry{
                font-size: 16px;
                margin-top: -15px;
                font-family: "Arial Narrow", Arial, sans-serif;
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
                /*     margin-bottom: -1000px;  */
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

            li{
                margin-bottom: 10px;
            }

            /*            #table{
                            font-size: 18px;
                            margin-top: -5em;
                            font-family: "Arial Narrow", Arial, sans-serif;
                        }*/

            .header,
            .footer {
                width: 100%;
                text-align: center;
                position: fixed;
            }
            .header {
                top: 0px;
            }
            .footer {
                border-top: 2px solid #000;
                bottom: 0px;
            }
            .pagenum:before {
                content: counter(page);
            }

            div.halaman{
                border-right: 2px solid #000;
                text-align: left;
                font-family: "Arial Narrow", Arial, sans-serif;
                font-size: 16px;
                width: 10%;
            }
            div.notes{
                /*               border-left: 2px solid #000;*/
                margin-top: -20px;
                /*               position: absolute;*/
                text-align: right;
                font-family: "Arial Narrow", Arial, sans-serif;
                font-size: 16px;
                width: 100%;
            }

            table.table1{
                border-collapse: collapse;
                border: 1px solid black;
                width: 100%;
            }

            table.table1 tr{
                border-collapse: collapse;
                border: 1px solid black;
                height: 12px;
            }

            table.table1 td{
                border-collapse: collapse;
                border: 1px solid black;
                padding: 3px;
            }

            table.table3{
                border-collapse: collapse;
                border: 1px solid black;
                width: 100%;
            }

            table.table3 tr{
                border-collapse: collapse;
                border: 1px solid black;
                height: 20px;
            }

            table.table3 td{
                border-collapse: collapse;
                border: 1px solid black;
                padding: 5px;
            }

            #t1 {
                -moz-tab-size: 16; /* Firefox */
                tab-size: 16;
            }

            table.table2{
                border-collapse: collapse;
                border: 1px solid black;
                width: 100%;
            }

            table.table2 tr{
                border-collapse: collapse;
                border: 1px solid black;
                height: 20px;
            }

            table.table2 td{
                border-collapse: collapse;
                border: 1px solid black;
                padding: 15px;
            }

        </style>
    </header>
    <body>
    <br>

    <!--        </div>-->
{{--    <br>--}}
{{--    <br>--}}
    <div class="dataBookingEntry">
        <div class="row" >
            <div class="Customer">
                <table style="width:100%">
                    <tr>
                        <td>
                            <div class="headerataskiri">
                                <img src="{{url('/image/logo/logo_metland.png')}}" width="180" height="20" alt="Logo Metland"/>
                            </div>
                        </td>
                        <td>
                            <div class="headerataskanan">
                                @if($dataProject['PROJECT_NO_CHAR'] == 3)
                                <p>F-FA/FIN-12 Rev-01</p>
                                @elseif($dataProject['PROJECT_NO_CHAR'] == 2)
                                <p>F-FA/FIN-08</p>
                                @else
                                <p>F-FA/FIN-09</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                </table>
                <p style="text-align:justify;">
                    <table class="table1">
                    <tr>
                        <td colspan="4">
                            <div class="tableCustomer">
                                <p style="text-align: center;">
                                    INVOICE<br>
                                    {{$dataInvoice->INVOICE_TRANS_NOCHAR}}
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="tableCustomer">
                                <p>
                                    Kepada Yth.<br>
                                    {!! $tenantName !!}<br>
                                    {!! $tenantAddres !!}<br>
                                    {{$tenantCity}} - {{$tenantPosCode}}
                                </p>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="tableCustomer">
                                ID Client &nbsp;&nbsp; : &nbsp; {{$lotNo}} / {{$shopName}}<br>
                                Lantai &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{$lotLevel}}<br>
                                Luas &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{$sqm}} M2<br>
                            </div>
                        </td>
                    </tr>
{{--                    <tr>--}}
{{--                        <td colspan="4">--}}
{{--                            <div class="tableCustomer">--}}
{{--                                Jatuh Tempo &nbsp;&nbsp; : &nbsp; {{$dateSchedule}}<br>--}}
{{--                                Periode &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{$periodSchedule}}--}}
{{--                            </div>--}}
{{--                        </td>--}}
{{--                    </tr>--}}
                    @if($dataInvoice->INVOICE_AUTOMATION_INT == 1)
                        @if($dataInvoice->INVOICE_TRANS_TYPE == 'UT')
                            <?php
                            $listrik = 0;
                            $air = 0;
                            $gas = 0;
                            ?>
                            @foreach($dataInvoiceDetail as $dataInv)
                                @if($dataInv->UTILS_TYPE_NAME == 'ELECTRICITY')
                                    <?php $listrik += 1; ?>
                                    <tr>
                                        <td><div class="tableCustomer">
                                                PEMAKAIAN LISTRIK
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                METER LWBP
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->BILLING_METER_END_LWBP,2,',','.') !!}  - {!! number_format($dataInv->BILLING_METER_START_LWBP,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: left;margin-top: -3px;">
                                                    BIAYA PEMAKAIAN LWBP
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->BILLING_METER_LWBP_DIFF,2,',','.') !!} x {!! number_format($dataInv->UTILS_LOW_RATE,2,',','.') !!} x {!! number_format($dataInv->UTILS_METER_MULTIPLIER,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    =
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->BILLING_AMOUNT_LWBP,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                METER WBP
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->BILLING_METER_END_WBP,2,',','.') !!}  - {!! number_format($dataInv->BILLING_METER_START_WBP,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: left;margin-top: -3px;">
                                                    BIAYA PEMAKAIAN WBP
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->BILLING_METER_WBP_DIFF,2,',','.') !!} x {!! number_format($dataInv->UTILS_HIGH_RATE,2,',','.') !!} x {!! number_format($dataInv->UTILS_METER_MULTIPLIER,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    =
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->BILLING_AMOUNT_WBP,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: left;margin-top: -3px;">
                                                    HANDLING FEE
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->UTILS_KVA_RATE,2,',','.') !!} Kva
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    =
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->BILLING_HANDLING_FEE_NUM,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: left;margin-top: -3px;">
                                                    BPJU
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->UTILS_BPJU_RATE,2,',','.') !!}%  x {!! number_format(($dataInv->BILLING_AMOUNT_LWBP + $dataInv->BILLING_AMOUNT_WBP + $dataInv->BILLING_HANDLING_FEE_NUM),2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    =
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->BILLING_BPJU_NUM,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: left;margin-top: -3px;">
                                                    LOST FACTOR
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    =
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->BILLING_LOST_FACTOR_NUM,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: left;margin-top: -3px;">
                                                    BILLBOARD
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->BILLING_METER_BILLBOARD_DAY,0,',','.') !!} x {!! number_format($dataInv->BILLING_METER_BILLBOARD_HOUR,0,',','.') !!} x {!! number_format($dataInv->UTILS_BILLBOARD_RATE,0,',','.') !!} Watt
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">

                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: left;margin-top: -3px;">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->UTILS_LOW_RATE,2,',','.') !!} x {!! number_format(($dataInv->BILLING_METER_BILLBOARD_DAY * $dataInv->BILLING_METER_BILLBOARD_HOUR * $dataInv->UTILS_BILLBOARD_RATE)/1000,2,',','.') !!} Kwh
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    =
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->BILLING_BILLBOARD_NUM,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: left;margin-top: -3px;">
                                                    TOTAL LISTRIK
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    =
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->INVOICE_TRANS_DTL_DPP,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: left;margin-top: -3px;">
                                                    PPN LISTRIK
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    =
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->INVOICE_TRANS_DTL_PPN,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @elseif($dataInv->UTILS_TYPE_NAME == 'GAS')
                                    <?php $gas += 1; ?>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                PEMAKAIAN GAS
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->BILLING_METER_END_LWBP,2,',','.') !!} - {!! number_format($dataInv->BILLING_METER_START_LWBP,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                TOTAL BIAYA GAS
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->BILLING_METER_LWBP_DIFF,2,',','.') !!}  x {!! number_format($dataInv->UTILS_LOW_RATE,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    =
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->INVOICE_TRANS_DTL_DPP,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                PPN GAS
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">

                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    =
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->INVOICE_TRANS_DTL_PPN,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @elseif($dataInv->UTILS_TYPE_NAME == 'WATER')
                                    <?php $air += 1; ?>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                PEMAKAIAN AIR
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->BILLING_METER_END_LWBP,2,',','.') !!} - {!! number_format($dataInv->BILLING_METER_START_LWBP,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                TOTAL PEMAKAIAN
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->BILLING_METER_LWBP_DIFF,2,',','.') !!}  x {!! number_format($dataInv->UTILS_LOW_RATE,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    =
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->INVOICE_TRANS_DTL_DPP,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="tableCustomer">
                                                PPN AIR
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">

                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    =
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="tableCustomer">
                                                <div style="text-align: right;margin-top: -3px;">
                                                    {!! number_format($dataInv->INVOICE_TRANS_DTL_PPN,2,',','.') !!}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if($listrik == 0)
                                <tr>
                                    <td><div class="tableCustomer">
                                            PEMAKAIAN LISTRIK
                                        </div>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            METER LWBP
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0  - 0
                                            </div>
                                        </div>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: left;margin-top: -3px;">
                                                BIAYA PEMAKAIAN LWBP
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0 x 0 x 0
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                =
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            METER WBP
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0  - 0
                                            </div>
                                        </div>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: left;margin-top: -3px;">
                                                BIAYA PEMAKAIAN WBP
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0 x 0 x 0
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                =
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: left;margin-top: -3px;">
                                                HANDLING FEE
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0 Kva
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                =
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: left;margin-top: -3px;">
                                                BPJU
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0%  x 0
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                =
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: left;margin-top: -3px;">
                                                LOST FACTOR
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                =
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: left;margin-top: -3px;">
                                                BILLBOARD
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0 x 0 x 0 Watt
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">

                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: left;margin-top: -3px;">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0 x 0 Kwh
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                =
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: left;margin-top: -3px;">
                                                TOTAL LISTRIK
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                =
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: left;margin-top: -3px;">
                                                PPN LISTRIK
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                =
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @if($gas == 0)
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            PEMAKAIAN GAS
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0 - 0
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            TOTAL BIAYA GAS
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0  x 0
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                =
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            PPN GAS
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">

                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                =
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @if($air == 0)
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            PEMAKAIAN AIR
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0 - 0
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            TOTAL PEMAKAIAN
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0  x 0
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                =
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                            PPN AIR
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">

                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                =
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td>
                                    <div class="tableCustomer">
                                        Materai
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            =
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            {!! number_format($dataInvoice->DUTY_STAMP,2,',','.') !!}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="tableCustomer">
                                        TOTAL TAGIHAN
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            =
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            {!! number_format($dataInvoice->INVOICE_TRANS_TOTAL + $dataInvoice->DUTY_STAMP,2,',','.') !!}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td>
                                    <div class="tableCustomer">
                                        {{$dataInvoice->INVOICE_TRANS_DESC_CHAR}} / {{$dataInvoice->INVOICE_FP_NOCHAR}}
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">

                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            =
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            {{number_format($dataInvoice->INVOICE_TRANS_DPP,2,',','.')}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="tableCustomer">
                                        TAX
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">

                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            =
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            {{number_format($dataInvoice->INVOICE_TRANS_PPN,2,',','.')}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @for($i = 2;$i<=17;$i++)
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                =
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endfor
                            <tr>
                                <td>
                                    <div class="tableCustomer">
                                        TOTAL SEBELUM PAJAK
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            =
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            {{number_format($dataInvoice->INVOICE_TRANS_DPP,2,',','.')}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="tableCustomer">
                                        TOTAL PAJAK
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            =
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            {{number_format($dataInvoice->INVOICE_TRANS_PPN,2,',','.')}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="tableCustomer">
                                        TOTAL SETELAH PAJAK
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            =
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            {{number_format($dataInvoice->INVOICE_TRANS_TOTAL,2,',','.')}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @else
                        <?php $jml_baris = 0 ?>
                        @if($dataInvoice->INVOICE_TRANS_TYPE == 'UT')
                            @foreach($dataInvoiceDetail as $dataInv)
                            <tr>
                                <td>
                                    <div class="tableCustomer">
                                        {{$dataInv->UTILS_TYPE_NAME}} / {{$dataInv->INVOICE_TRANS_DTL_DESC}}
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            =
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            {{number_format($dataInv->INVOICE_TRANS_DTL_DPP,2,',','.')}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="tableCustomer">
                                        TAX
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            =
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            {{number_format($dataInv->INVOICE_TRANS_DTL_PPN,2,',','.')}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php $jml_baris += 1 ?>
                            @endforeach
                            @for($i = $jml_baris;$i<=17;$i++)
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                =
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                               0
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endfor
                            <tr>
                                <td>
                                    <div class="tableCustomer">
                                        Materai
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            =
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            {!! number_format($dataInvoice->DUTY_STAMP,2,',','.') !!}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="tableCustomer">
                                        TOTAL TAGIHAN
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            =
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            {!! number_format($dataInvoice->INVOICE_TRANS_TOTAL + $dataInvoice->DUTY_STAMP,2,',','.') !!}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach($dataInvoiceDetail as $dataInv)
                            <tr>
                                <td>
                                    <div class="tableCustomer">
                                        {{$dataInv->INVOICE_TRANS_DTL_DESC}}
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            =
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            {{number_format($dataInv->INVOICE_TRANS_DTL_DPP,2,',','.')}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="tableCustomer">
                                        TAX
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            =
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            {{number_format($dataInv->INVOICE_TRANS_DTL_PPN,2,',','.')}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php $jml_baris += 1 ?>
                            @endforeach
                            @for($i = $jml_baris;$i<=16;$i++)
                                <tr>
                                    <td>
                                        <div class="tableCustomer">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                =
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tableCustomer">
                                            <div style="text-align: right;margin-top: -3px;">
                                                0
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endfor
                            <tr>
                                <td>
                                    <div class="tableCustomer">
                                        TOTAL SEBELUM PAJAK
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            =
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            {{number_format($dataInvoice->INVOICE_TRANS_DPP,2,',','.')}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="tableCustomer">
                                        TOTAL PAJAK
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            =
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            {{number_format($dataInvoice->INVOICE_TRANS_PPN,2,',','.')}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="tableCustomer">
                                        TOTAL SETELAH PAJAK
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            =
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="tableCustomer">
                                        <div style="text-align: right;margin-top: -3px;">
                                            {{number_format($dataInvoice->INVOICE_TRANS_TOTAL,2,',','.')}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endif
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="tableCustomer">
                                Terbilang : {{$terbilangAmount}} Rupiah
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="tableCustomer">
                                <ol class="num" style="margin-left: 10px;">
                                    <li>
                                        Invoice ini bukan merupakan kwitansi/Bukti Pembayaran<br>
                                        Keterlambatan pembayaran dikenakan penalty 1/1000/hari (1 permil per hari)
                                    </li>
                                    <li>
                                        Pembayaran ditujukan kepada:<br>
                                        {{$dataFinSetup->BM_BANK_ACCOUNT}}<br>
                                        Rekening Virtual {{$noVA}}<br>
{{--                                        <center>atau</center>--}}
{{--                                        {{$dataFinSetup->BM_BANK_ACCOUNT.' '.$dataFinSetup->BM_BANK_KCP_ACCOUNT}}<br>--}}
{{--                                        {{$dataFinSetup->BM_BANK_NAME_ACCOUNT}} No. Rek. {{$dataFinSetup->BM_REK_ACCOUNT}}--}}
                                    </li>
                                    <li>
                                        Cantumkan Nama Tenant & No. Invoice pada slip pembayaran
                                    </li>
                                    <li>
                                        Bukti Pembayaran mohon fax ke No. {{$dataProject['TELP_NO_CHAR']}} atau email ke:<br>
                                        {{$dataProject['FIN_EMAIL']}} dan ditujukan kepada bagian collection.
                                    </li>
                                    <li>
                                        Jatuh tempo pembayaran maksimal tanggal 10 setiap bulannya.
                                    </li>
                                </ol>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="tableCustomer" style="text-align: center">
                                {{$dataProject['PROJECT_KOTA']}}, {{$printDate}}
                                <br><br><br><br><br><br><br>
                                <u>{{$dataProject['CHIEF_FINANCE']}}</u><br>
                                Finance Manager
                            </div>
                        </td>
                    </tr>
                    </table>
                </p>
            </div>
        </div>
    </div>
    <br>
    <div class="dataBookingEntry">
        <div class="row">
            <div class="Customer">
                <table>
                    <tr>
                        <?php
                            $project_address = $dataProject['PROJECT_ADDRESS'] == null ? "-" : $dataProject['PROJECT_ADDRESS'];
                            $project_telp = $dataProject['TELP_NO_CHAR'] == null ? "-" : $dataProject['TELP_NO_CHAR'];
                            $project_fax = $dataProject['FAX_NO_CHAR'] == null ? "-" : $dataProject['FAX_NO_CHAR'];
                            $project_logo = $dataProject['logo'] == null ? "-" : $dataProject['logo'];
                        ?>
                        <td>
                            <div class="tableCustomer">
                                <p style="text-align: left;">
                                    <img src="{{url($project_logo)}}" width="100" height="70" alt="Logo"/>
                                </p>
                            </div>
                        </td>
                        <td style="padding-left: 1.5%; width: 100%;">
                            <div class="tableCustomer">
                                <p style="text-align: left; font-size: 100%;">
                                    Kantor : {{$project_address}}<br/>
                                    Telp : {{$project_telp}}, Fax : {{$project_fax}}
                                </p>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
{{--    <br><br>--}}
{{--    <div class="dataBookingEntry">--}}
{{--        <div class="row" >--}}
{{--            <div class="Customer">--}}
{{--                <table class="table1">--}}
{{--                    <tr>--}}
{{--                        <td>--}}
{{--                            <div class="tableCustomer">--}}
{{--                                <p style="text-align: center;">--}}
{{--                                    TANDA TERIMA<br>--}}
{{--                                    No Invoice {{$dataInvoice->INVOICE_TRANS_NOCHAR}}--}}
{{--                                </p>--}}
{{--                            </div>--}}
{{--                        </td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>--}}
{{--                            <div class="tableCustomer">--}}
{{--                                <p style="text-align: justify;">--}}
{{--                                    Telah diterima satu buah invoice untuk:<br><br>--}}
{{--                                    Nama&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp; {!! $tenantName !!}<br>--}}
{{--                                    Alamat&nbsp;&nbsp;&nbsp; :&nbsp; {!! $tenantAddres !!}<br>--}}
{{--                                    {{$tenantCity}} - {{$tenantPosCode}}--}}
{{--                                </p>--}}
{{--                            </div>--}}
{{--                            <div class="tableCustomer">--}}
{{--                                <p style="text-align: left;margin-left: 550px;">--}}
{{--                                    Penerima,--}}
{{--                                    <br><br><br><br>--}}
{{--                                </p>--}}
{{--                                <p style="text-align: left;margin-left: 400px;">--}}
{{--                                    Nama Jelas&nbsp;&nbsp;&nbsp; :<br>--}}
{{--                                    Tanggal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :--}}
{{--                                </p>--}}
{{--                            </div>--}}
{{--                        </td>--}}
{{--                    </tr>--}}
{{--                </table>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    </body>
    </html>
