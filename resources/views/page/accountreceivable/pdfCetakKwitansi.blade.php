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
                font-size: 16px;
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
                font-size: 15px;
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
                font-size: 16px;
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
                height: 20px;
            }

            table.table1 td{
                border-collapse: collapse;
                border: 1px solid black;
                padding: 15px;
            }

            table.table3{
                border-collapse: collapse;
                border: 0px solid black;
                width: 100%;
            }

            table.table3 tr{
                border-collapse: collapse;
                border: 0px solid black;
                height: 20px;
            }

            table.table3 td{
                border-collapse: collapse;
                border: 0px solid black;
                padding: 5px;
            }

            #t1 {
                -moz-tab-size: 16; /* Firefox */
                tab-size: 16;
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
                            <div class="headerataskanan">
{{--                                <p>{{$page}} F-FA/FIN-09</p>--}}
                            </div>
                        </td>
                    </tr>
                </table>
                <table style="width:100%">
                    <tr>
                        {{--                        <td>--}}
                        {{--                            <div class="headerataskiri">--}}
                        {{--                                <img src="{{url('/image/logo/logo_metland.png')}}" width="200" height="30" alt="Logo Metland"/>--}}
                        {{--                            </div>--}}
                        {{--                        </td>--}}
                        <td>
                            <div class="headerataskanan">
                                {{--                                @if($dataProject['PROJECT_NO_CHAR'] == 3)--}}
                                {{--                                <p>F-FA/FIN-12 Rev-01</p>--}}
                                @if($dataProject['PROJECT_NO_CHAR'] == 2)
                                    <p>F-FA/FIN-04</p>
                                @elseif($dataProject['PROJECT_NO_CHAR'] == 3)
                                    <p>F-FA/FIN-09 Rev.02</p>
                                @elseif($dataProject['PROJECT_NO_CHAR'] == 4)
                                    <p>F-FA/FIN-08</p>
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
                                    KWITANSI<br>
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="tableCustomer">
                                OR No. &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{$noKwitansi}}<br>
                                Luas &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{$sqm}} M2<br>
                                Telah Terima Dari &nbsp; : &nbsp; {!! $tenantName !!}<br>
                                @if($page == 'IN')
                                    @if($dataInvoice->MD_TENANT_PPH_INT == 1)
                                    Jumlah (Rp.) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{number_format((($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN) - $dataInvoice->INVOICE_TRANS_PPH),0,'','.')}}<br>
                                    @else
                                    Jumlah (Rp.) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{number_format($dataInvoice->INVOICE_TRANS_TOTAL,0,'','.')}}<br>
                                    @endif
                                    Terbilang &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{$terbilangAmount}} Rupiah<br>
                                @elseif($page == 'BM')
                                Jumlah (Rp.) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{number_format($dataInvPayment->PAID_BILL_AMOUNT,0,'','.')}}<br>
                                Terbilang &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{$terbilangAmount}} Rupiah<br>
                                @endif
                                Alamat &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;{!! $tenantAddres !!}<br>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width:65%">
                            <div class="tableCustomer">
                                <p style="text-align: center;">
                                    Keterangan
                                </p>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="tableCustomer">
                                <p style="text-align: center;">
                                    Jumlah
                                </p>
                            </div>
                        </td>
                    </tr>
                    @if($page == 'IN')
                        <tr>
                        <td colspan="2">
                            <div class="tableCustomer">
                                <p style="text-align: left;">
                                    {{$dataInvoice->INVOICE_TRANS_DESC_CHAR}} / {{$dataInvoice->INVOICE_TRANS_NOCHAR}} / {{$dataInvoice->INVOICE_FP_NOCHAR}}
                                    / {{$dataInvoice->LOT_STOCK_NO}} / {{$shopName}}<br>
                                    PPN<br>
                                    @if($dataInvoice->MD_TENANT_PPH_INT == 1)
                                        PPH
                                    @endif
                                    @for($i = 1;$i<=20;$i++)
                                        <br>
                                    @endfor
                                </p>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="tableCustomer">
                                <p style="text-align: right;">
                                    {{number_format($dataInvoice->INVOICE_TRANS_DPP,0,'','.')}}<br>
                                    {{number_format($dataInvoice->INVOICE_TRANS_PPN,0,'','.')}}<br>
                                    @if($dataInvoice->MD_TENANT_PPH_INT == 1)
                                    ({{number_format($dataInvoice->INVOICE_TRANS_PPH,0,'','.')}})
                                    @endif
                                    @for($i = 1;$i<=20;$i++)
                                        <br>
                                    @endfor
                                </p>
                            </div>
                        </td>
                    </tr>
                    @elseif($page == 'BM')
                        <tr>
                            <td colspan="2">
                                <div class="tableCustomer">
                                    <p style="text-align: left;">
                                        {{$dataInvoice->INVOICE_TRANS_DESC_CHAR}} / {{$dataInvoice->INVOICE_TRANS_NOCHAR}} / {{$dataInvoice->INVOICE_FP_NOCHAR}}
                                        / {{$dataInvoice->LOT_STOCK_NO}} / {{$shopName}}<br>
                                        @for($i = 1;$i<=23;$i++)
                                            <br>
                                        @endfor
                                    </p>
                                </div>
                            </td>
                            <td colspan="2">
                                <div class="tableCustomer">
                                    <p style="text-align: right;">
                                        {{number_format($dataInvPayment->PAID_BILL_AMOUNT,0,'','.')}}<br>
                                        @for($i = 1;$i<=23;$i++)
                                            <br>
                                        @endfor
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="2">
                            <div class="tableCustomer">
                                Pembayaran dianggap sah bila jumlah tersebut telah diterima oleh Bank kami.
                            </div>
                        </td>
                        @if($page == 'IN')
                            <td colspan="2">
                                <div class="tableCustomer">
                                    @if($dataInvoice->MD_TENANT_PPH_INT == 1)
                                        Total <div style="text-align: right;margin-top: -20px;">{{number_format((($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN) - $dataInvoice->INVOICE_TRANS_PPH),0,'','.')}}</div>
                                    @else
                                        Total <div style="text-align: right;margin-top: -20px;">{{number_format($dataInvoice->INVOICE_TRANS_TOTAL,0,'','.')}}</div>
                                    @endif
                                </div>
                            </td>
                        @elseif($page == 'BM')
                            <td colspan="2">
                                <div class="tableCustomer">
                                    Total <div style="text-align: right;margin-top: -20px;">{{number_format($dataInvPayment->PAID_BILL_AMOUNT,0,'','.')}}</div>
                                </div>
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="tableCustomer">

                            </div>
                        </td>
                        <td colspan="2">
                            <div class="tableCustomer" style="text-align: center">
                                {!! $dataProject['PROJECT_KOTA'] !!}, {{$printDate}}
                                <br><br><br><br><br><br><br><br><br><br><br>
                                <u>{{$dataLead['SPK_ASSIGN_NAME']}}</u><br>
                                {{$dataLead['SPK_ASSIGN_JOB_NAME']}}
                            </div>
                        </td>
                    </tr>
                    </table>
                </p>
            </div>
        </div>
    </div>
    <br><br><br><br><br><br>
    <div class="dataBookingEntry">
        <div class="row" >
            <div class="Customer">
                <table class="table3">
                    <tr>
                        <td>
                            <div class="tableCustomer">
                                <p style="text-align: center;">
                                    TANDA TERIMA<br>
                                    No Kwitansi {{$noKwitansi}}<br>
                                    Faktur Pajak {{$dataInvoice->INVOICE_FP_NOCHAR}}
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="tableCustomer">
                                <p style="text-align: justify;">
                                    Telah diterima satu buah kwitansi berikut dan faktur pajak:<br><br>
                                    Nama&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :&nbsp; {!! $tenantName !!}<br>
                                    Alamat&nbsp;&nbsp;&nbsp; :&nbsp; {!! $tenantAddres !!}<br>
                                    {{$tenantCity}} - {{$tenantPosCode}}
                                </p>
                            </div>
                            <div class="tableCustomer">
                                <p style="text-align: left;margin-left: 550px;">
                                    Penerima,
                                    <br><br><br><br>
                                </p>
                                <p style="text-align: left;margin-left: 400px;">
                                    Nama Jelas&nbsp;&nbsp;&nbsp; :<br>
                                    Tanggal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :
                                </p>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    </body>
    </html>
