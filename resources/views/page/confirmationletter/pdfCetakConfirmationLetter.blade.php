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

            table, th, td {
                border: 0px black;
            }
            th, td {
                padding: 5px;
            }
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
                margin-top:2em;
                text-align:right;
                font-size: 8px;
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
                font-size: 16px;
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
                            <div class="headerbawahkiri">
                                <p>
                                    {{$dataProject['PROJECT_KOTA']}}, {{$dateDocument}}<br>
                                    {{$dataSKS->SKS_TRANS_NOCHAR}}<br>
                                    <br><br>

                                    Kepada Yth<br>
                                    <b>{{$dataTenant->MD_TENANT_NAME_CHAR}}</b><br>
                                    {!! $dataTenant->MD_TENANT_ADDRESS1 !!}
                                </p>
                            </div>
                        </td>
                    </tr>
                </table>
                <p style="text-align:justify;">
                    <table style="width:30%">
                    <tr>
                        <td>
                            <div class="tableCustomer">
                                <b>Up :</b>
                            </div>
                        </td>
                        <td>
                            <div class="tableCustomer">
                                <b>{{$dataTenant->MD_TENANT_DIRECTOR}}</b>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="tableCustomer">
                                <b></b>
                            </div>
                        </td>
                        <td>
                            <div class="tableCustomer">
                                <b>{{$dataTenant->MD_TENANT_DIRECTOR_JOB_TITLE}}</b>
                            </div>
                        </td>
                    </tr>
                    </table>
                </p>
                <p style="text-align:justify;">
                    Berikut kami sampaikan harga sewa yang dapat kami setujui adalah dengan rincian sebagai breikut :
                </p>
                <table style="width:100%">
                    <tr>
                        <td style="width: 130px">
                            <div class="tableCustomer">
                                {!! Form::label('','Lokasi')!!}
                            </div>
                        </td>
                        <td>
                            <div class="tableCustomer">
                                {!! Form::label('',':')!!}
                            </div>
                        </td>
                        <td>
                            <div class="tableCustomer">
                                {!! Form::label('',$dataLot->LOT_STOCK_NO)!!}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 130px">
                            <div class="tableCustomer">
                                {!! Form::label('','Luas')!!}
                            </div>
                        </td>
                        <td>
                            <div class="tableCustomer">
                                {!! Form::label('',':')!!}
                            </div>
                        </td>
                        <td>
                            <div class="tableCustomer">
                                {!! Form::label('',$dataLot->LOT_STOCK_SQM)!!} m2 (akan dilakukan ukur ulang di lapangan)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 130px">
                            <div class="tableCustomer">
                                {!! Form::label('','Harga sewa')!!}
                            </div>
                        </td>
                        <td>
                            <div class="tableCustomer">
                                {!! Form::label('',':')!!}
                            </div>
                        </td>
                        <td>
                            <div class="tableCustomer">
                                {!! Form::label('','Rp. '.number_format(($dataSKS->SKS_TRANS_RENT_NUM),0,'','.'))!!},-/m2/bulan (belum termasuk pajak)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 130px">
                            <div class="tableCustomer">
                                {!! Form::label('','Service Charge	 + PL')!!}
                            </div>
                        </td>
                        <td>
                            <div class="tableCustomer">
                                {!! Form::label('',':')!!}
                            </div>
                        </td>
                        <td>
                            <div class="tableCustomer">
                                {!! Form::label('','Rp. '.number_format(($dataSKS->SKS_TRANS_SC_NUM),0,'','.'))!!},-/m2/bulan (belum termasuk pajak)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 130px">
                            <div class="tableCustomer">
                                {!! Form::label('','Masa sewa')!!}
                            </div>
                        </td>
                        <td>
                            <div class="tableCustomer">
                                {!! Form::label('',':')!!}
                            </div>
                        </td>
                        <td>
                            <div class="tableCustomer">
                                {!! Form::label('',$masaSewa)!!}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 130px">
                            <div class="tableCustomer">
                                {!! Form::label('','Deposit')!!}
                            </div>
                        </td>
                        <td>
                            <div class="tableCustomer">
                                {!! Form::label('',':')!!}
                            </div>
                        </td>
                        <td>
                            <div class="tableCustomer">
                                {!! Form::label('',$deposit)!!}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 130px">
                            <div class="tableCustomer">
                                {!! Form::label('','Cara Pembayaran')!!}
                            </div>
                        </td>
                        <td>
                            <div class="tableCustomer">
                                {!! Form::label('',':')!!}
                            </div>
                        </td>
                        <td>
                            <div class="tableCustomer">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="tableCustomer">
                                {!!$dataSKS->SKS_TRANS_DESCRIPTION!!}
                            </div>
                        </td>
                    </tr>
                </table>

                <div class="termAndConditionAtas">
                    <p style="text-align:justify;">
                        Demikian kami sampaikan, kami tunggu kabar baik dari pihak Bapak/Ibu secepatnya. Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.
                    </p>
                    <center>
                        <table style="width:100%;">
                            <tr>
                                <td><div class="tableCustomer">Hormat Kami,</div></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><div class="tableCustomer">Menyetujui,</div></td>
                            </tr>
                            <tr>
                                <td><br><br><br><br><br><br><br><br><br></td>
                                <td><br><br><br><br><br><br><br><br><br></td>
                                <td><br><br><br><br><br><br><br><br><br></td>
                                <td><br><br><br><br><br><br><br><br><br></td>
                                <td><br><br><br><br><br><br><br><br><br></td>
                                <td><br><br><br><br><br><br><br><br><br></td>
                                <td><br><br><br><br><br><br><br><br><br></td>
                                <td><br><br><br><br><br><br><br><br><br></td>
                                <td><br><br><br><br><br><br><br><br><br></td>
                                <td><br><br><br><br><br><br><br><br><br></td>
                                <td><br><br><br><br><br><br><br><br><br></td>
                                <td><br><br><br><br><br><br><br><br><br></td>
                                <td><br><br><br><br><br><br><br><br><br></td>
                                <td><br><br><br><br><br><br><br><br><br></td>
                                <td><br><br><br><br><br><br><br><br><br></td>
                                <td><br><br><br><br><br><br><br><br><br></td>
                                <td><br><br><br><br><br><br><br><br><br></td>
                            </tr>
                            <tr>
                                <td><div class="tableCustomer"><u>{!! Form::label('','Himawan Mursalim')!!}</u></div></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><div class="tableCustomer">{!! Form::label('','_____________')!!}</div></td>
                            </tr>
                            <tr>
                                <td><div class="tableCustomer">{!! Form::label('','Wakil Direktur Div. Mal')!!}</div></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                        </table>
                    </center>
                </div>
            </div>
        </div>
    </div>

    </body>
    </html>
