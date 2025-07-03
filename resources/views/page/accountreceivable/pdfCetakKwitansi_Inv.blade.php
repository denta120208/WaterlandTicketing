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
                                {{$kodeBulan->KODE_BULAN}}<br><br>
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
                                    KUITANSI<br>
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="tableCustomer">
                                Nomor &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{$dataInvoice->INVOICE_TRANS_NOCHAR}}<br>
                                Luas &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{$sqm}} M2<br>
                                Telah Terima Dari &nbsp; : &nbsp; {!! $tenantName !!}<br>
                                Alamat &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp;{!! $tenantAddres !!}<br>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="width:65%">
                            <div class="tableCustomer">
                                <p style="text-align: center;">
                                    KETERANGAN
                                </p>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="tableCustomer">
                                <p style="text-align: center;">
                                    JUMLAH
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="tableCustomer">
                                <p style="text-align: left;">
                                    {{$dataInvoice->INVOICE_TRANS_DESC_CHAR}} / {{$dataInvoice->INVOICE_TRANS_NOCHAR}} / {{$dataInvoice->INVOICE_FP_NOCHAR}}
                                    / {{$dataInvoice->LOT_STOCK_NO}} / {{$shopName}}<br>
                                    PPN<br>
                                    Materai<br>
                                    <b>TOTAL TAGIHAN</b><br>
                                    @if($dataInvoice->DOC_TYPE != 'D')
                                        @if ($dataInvoice->MD_TENANT_PPH_INT == 1)
                                        PPH<br>
                                        @endif
                                    @endif
                                    <b>TOTAL YANG HARUS DIBAYARKAN</b><br>
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
                                    {{ number_format($dataInvoice->DUTY_STAMP,0,'','.') }}<br>
                                    <b>{{number_format(($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN + $dataInvoice->DUTY_STAMP),0,'','.')}}</b><br>
                                    @if($dataInvoice->DOC_TYPE == 'D')
                                        <b>{{number_format((($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN + $dataInvoice->DUTY_STAMP)),0,'','.')}}</b><br>
                                    @else
                                        @if ($dataInvoice->MD_TENANT_PPH_INT == 1)
                                        ({{number_format($dataInvoice->INVOICE_TRANS_PPH,0,'','.')}})<br>
                                        @endif
                                        
                                        @if ($dataInvoice->MD_TENANT_PPH_INT == 1)
                                            <b>{{number_format((($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN + $dataInvoice->DUTY_STAMP) - $dataInvoice->INVOICE_TRANS_PPH),0,'','.')}}</b><br>
                                        @else
                                            <b>{{number_format((($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN + $dataInvoice->DUTY_STAMP)),0,'','.')}}</b><br>
                                        @endif
                                    @endif
                                    @for($i = 1;$i<=20;$i++)
                                        <br>
                                    @endfor
                                </p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="tableCustomer">
                                Terbilang : {{$terbilangAmount}} Rupiah
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="tableCustomer">
                                Total <div style="text-align: right;margin-top: -20px;">
                                    @if ($dataInvoice->MD_TENANT_PPH_INT == 1)
                                        @if($dataInvoice->DOC_TYPE == 'D')
                                        {{number_format(($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN + $dataInvoice->DUTY_STAMP),0,'','.')}}
                                        @else
                                        {{number_format((($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN + $dataInvoice->DUTY_STAMP) - $dataInvoice->INVOICE_TRANS_PPH),0,'','.')}}
                                        @endif
                                    @else
                                        {{number_format((($dataInvoice->INVOICE_TRANS_DPP + $dataInvoice->INVOICE_TRANS_PPN + $dataInvoice->DUTY_STAMP)),0,'','.')}}
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="tableCustomer">
                                <ol class="num" style="margin-left: 10px;">
                                    <li>
                                        Pembayaran dianggap sah bila jumlah tersebut telah diterima oleh Bank kami.
                                    </li>
                                    <li>
                                        Keterlambatan pembayaran dikenakan penalty 1/1000/hari (1 permil per hari).
                                    </li>
                                    <li>
                                        Pembayaran ditujukan kepada:<br>
                                        {{$dataFinSetup->BM_BANK_ACCOUNT}}<br>
                                        Rekening Virtual {{$noVA}}<br>
                                    </li>
                                    <li>
                                        Cantumkan Nama Tenant & No. Invoice pada slip pembayaran
                                    </li>
                                    <li>
                                        Bukti pembayaran mohon dikirim ke WA Business di No. {{$dataProject['WA_NUMBER']}} dan/atau email ke :<br>
                                        {{$dataProject['FIN_EMAIL']}} dan ditujukan kepada bagian collection. Serta asli bukti potong<br>
                                        PPh diatas namakan {{$dataCompany['COMPANY_NAME']}} dengan NPWP {{$dataProject['PROJECT_NPWP']}}<br>
                                        alamat {{$dataProject['PROJECT_ADDRESS']}}<br>
                                        dan asli bukti potong dikirimkan kepada kami selambat-lambatnya 1 (satu) bulan setelah pembayaran.
                                    </li>
                                    <li>
                                        Jatuh tempo pembayaran maksimal tanggal 10 setiap bulannya.
                                    </li>
                                </ol>
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
    @if($dataInvoice->INVOICE_TRANS_TYPE == 'UT')
        <div class="pagebreak"> </div>
        <div class="dataBookingEntry">
            <div class="row" >
                <div class="Customer">
                    <table style="width:100%">
                        <tr>
                            <td>
                                <div class="headerataskanan">
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
                                        DETAIL TAGIHAN<br>
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
                                                        PPJU
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
                                                        {!! number_format($dataInv->BILLING_PPJU_NUM,2,',','.') !!}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="tableCustomer">
                                                    <div style="text-align: left;margin-top: -3px;">
                                                        BIAYA KEHANDALAN
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
                                                        {!! number_format($dataInv->BILLING_AMOUNT_RELIABILITY,2,',','.') !!}
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
{{--                                        <tr>--}}
{{--                                            <td>--}}
{{--                                                <div class="tableCustomer">--}}
{{--                                                    <div style="text-align: left;margin-top: -3px;">--}}
{{--                                                        BPJU--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </td>--}}
{{--                                            <td>--}}
{{--                                                <div class="tableCustomer">--}}
{{--                                                    <div style="text-align: right;margin-top: -3px;">--}}
{{--                                                        {!! number_format($dataInv->UTILS_BPJU_RATE,2,',','.') !!}%  x {!! number_format(($dataInv->BILLING_AMOUNT_LWBP + $dataInv->BILLING_AMOUNT_WBP + $dataInv->BILLING_HANDLING_FEE_NUM),2,',','.') !!}--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </td>--}}
{{--                                            <td>--}}
{{--                                                <div class="tableCustomer">--}}
{{--                                                    <div style="text-align: right;margin-top: -3px;">--}}
{{--                                                        =--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </td>--}}
{{--                                            <td>--}}
{{--                                                <div class="tableCustomer">--}}
{{--                                                    <div style="text-align: right;margin-top: -3px;">--}}
{{--                                                        {!! number_format($dataInv->BILLING_BPJU_NUM,2,',','.') !!}--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </td>--}}
{{--                                        </tr>--}}
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
                                                        ADMINISTRATION
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
                                                        {!! number_format($dataInv->BILLING_ADMIN_NUM,2,',','.') !!}
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
                                            <?php
                                            $converter = new \App\Http\Controllers\Util\utilConverter();
                                            $terbilangAmountDetail = $converter->terbilang(($dataInvoice->INVOICE_TRANS_TOTAL + $dataInvoice->DUTY_STAMP));
                                            ?>
                                            Terbilang : {{$terbilangAmountDetail}} Rupiah
                                        </div>
                                    </td>
                                </tr>
                    </table>
                    </p>
                </div>
            </div>
        </div>
    @endif
    </body>
    </html>
