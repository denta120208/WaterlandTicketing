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

            #t1 {
                -moz-tab-size: 16; /* Firefox */
                tab-size: 16;
            }

        </style>
    </header>
    <body>
    <br>

    <!--        </div>-->
    <br>
    <br>
    <div class="dataBookingEntry">
        <div class="row" >
            <div class="Customer">
                <table style="width:100%">
                    <tr>
                        <td>
                            <div class="headerataskanan">
                                <p>F-FA/FIN-09</p>
                            </div>
                        </td>
                    </tr>
                </table>
                <p style="text-align:justify;">
                    <table class="table1">
                    <tr>
                        <td colspan="4">
                            <div class="tableCustomer">
                                <p style="text-align: center;">PROFORMA INVOICE</p>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="tableCustomer">
                                <p>
                                    Kepada Yth.<br>
                                    <p style="margin-left: 25px;margin-top: -10px">{{$dataTenant->MD_TENANT_NAME_CHAR}}<br>
                                    {!! $dataTenant->MD_TENANT_ADDRESS1 !!}<br>
                                    {!! $dataTenant->MD_TENANT_CITY_CHAR !!} - {!! $dataTenant->MD_TENANT_POSCODE!!}</p>
                                </p>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="tableCustomer">
                                ID Client &nbsp;&nbsp; : &nbsp; {{$dataPSM->LOT_STOCK_NO}} / {{$dataPSM->SHOP_NAME_CHAR}}<br>
                                Lantai &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{$dataLevel}}<br>
                                Luas &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{$dataLot}}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <div class="tableCustomer">
                                Jatuh Tempo &nbsp;&nbsp; : &nbsp; {{$dateSchedule}}<br>
                                Periode &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; : &nbsp; {{$periodSchedule}}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="tableCustomer">
                                <div style="text-align: left;margin-top: -3px;">{{$dataInvRentSC[0]->DESC_CHAR}} / {{$dataTenant->MD_TENANT_NPWP}}</div><br>
                                <div style="text-align: left;margin-top: -3px;">TAX</div><br>
                                <div style="text-align: left;margin-top: -3px;">TOTAL</div><br>
                            </div>
                        </td>
                        <td colspan="2">
                            <div class="tableCustomer">
                                IDR <div style="text-align: right;margin-top: -20px;">{{number_format($dataInvRentSC[0]->BASE_AMOUNT_NUM - $dataInvRentSC[0]->DISC_NUM,0,'','.')}}</div><br>
                                IDR <div style="text-align: right;margin-top: -20px;">{{number_format($dataInvRentSC[0]->PPN_PRICE_NUM,0,'','.')}}</div><br>
                                IDR <div style="text-align: right;margin-top: -20px;">{{number_format($dataInvRentSC[0]->BILL_AMOUNT,0,'','.')}}</div><br>
                            </div>
                        </td>
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
                                <ol class="num">
                                    <li>
                                        Invoice ini bukan merupakan kwitansi/Bukti Pembayaran<br>
                                        Keterlambatan pembayaran dikenakan penalty 1/1000/hari (1 permil per hari)
                                    </li>
                                    <li>
                                        Pembayaran ditujukan kepada:<br>
                                        {{$dataFinSetup->BM_BANK_ACCOUNT.' '.$dataFinSetup->BM_BANK_KCP_ACCOUNT}}<br>
                                        Rekening Virtual {{$dataPSM->PSM_TRANS_VA}}<br>
                                        <center>atau</center>
                                        {{$dataFinSetup->BM_BANK_ACCOUNT.' '.$dataFinSetup->BM_BANK_KCP_ACCOUNT}}<br>
                                        {{$dataFinSetup->BM_BANK_NAME_ACCOUNT}} No. Rek. {{$dataFinSetup->BM_REK_ACCOUNT}}
                                    </li>
                                    <li>
                                        Cantumkan Nama Tenant & No. Invoice pada slip pembayaran
                                    </li>
                                    <li>
                                        Bukti Pembayaran mohon Telp. ke No. {{$dataProject['TELP_NO_CHAR']}} dan diajukan Kepada Bagian Collection.
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

    </body>
    </html>
