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
                margin-top: 50.08px;
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
    <br>
    <br>
    <div class="dataBookingEntry">
        <div class="row" >
            <div class="Customer">
                <table style="width:100%">
                    <tr>
                        <td>
                            <div class="headerataskiri">
                                {{-- <img src="{{url($project_logo)}}" width="110" height="70" alt="Logo"/> --}}
                            </div>
                        </td>
                        <td>
                            <div class="headerataskanan">
                                {{-- <img src="{{url('/image/logo/logo_metland.png')}}" width="180" height="30" alt="Logo Metland"/> --}}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row" >
            <div class="Customer" style="text-align: center">
                <b><u><h5>BERITA ACARA BUKA TOKO</h5></u></b>
            </div>
        </div>
        <div class="row" >
            <div class="Customer">
                <b><u>Dengan ini telah dibuka Toko:</u></b>
                <br><br>
                <table style="margin-left: -55px;">
                    <tr>
                        <td><div class="Customer">Nama Toko/Restoran</div></td>
                        <td><div class="Customer" style="margin-left: -50px;">:</div></td>
                        <td><div class="Customer" style="margin-left: -55px;">{{$dataPSM->SHOP_NAME_CHAR}}</div></td>
                    </tr>
                    @foreach($dataLot as $key => $data)
                    <tr>
                        <td><div class="Customer">Lantai & No. Unit {{count($dataLot) > 1 ? $key+1 : ""}}</div></td>
                        <td><div class="Customer" style="margin-left: -50px;">:</div></td>
                        <td><div class="Customer" style="margin-left: -55px;">{{$dataLotLevel[$key]->LOT_LEVEL_DESC}} No Unit {{$data->LOT_STOCK_NO}}</div></td>
                    </tr>
                    @endforeach
                    <tr>
                        <td><div class="Customer">No. Telp. Toko/Restoran</div></td>
                        <td><div class="Customer" style="margin-left: -50px;">:</div></td>
                        <td><div class="Customer" style="margin-left: -55px;"></div></td>
                    </tr>
                    <tr>
                        <td><div class="Customer">Nama Perusahaan</div></td>
                        <td><div class="Customer" style="margin-left: -50px;">:</div></td>
                        <td><div class="Customer" style="margin-left: -55px;">{{$dataTenant->MD_TENANT_NAME_CHAR}}</div></td>
                    </tr>
                    <tr>
                        <td><div class="Customer">Alamat Perusahaan</div></td>
                        <td><div class="Customer" style="margin-left: -50px;">:</div></td>
                        <td><div class="Customer" style="margin-left: -55px;">{!! $dataTenant->MD_TENANT_ADDRESS1 !!}</div></td>
                    </tr>
                    <tr>
                        <td><div class="Customer">No. Telp. Perusahaan</div></td>
                        <td><div class="Customer" style="margin-left: -50px;">:</div></td>
                        @if($dataTenant->MD_TENANT_TELP == 'NONE')
                            <td><div class="Customer" style="margin-left: -55px;"></div></td>
                        @else
                            <td><div class="Customer" style="margin-left: -55px;">{{$dataTenant->MD_TENANT_TELP}}</div></td>
                        @endif
                    </tr>
                    <tr>
                        <td><div class="Customer">Hari & Tanggal Mulai Operasional</div></td>
                        <td><div class="Customer" style="margin-left: -50px;">:</div></td>
                        <td><div class="Customer" style="margin-left: -55px;">{{$dateFormatHari}}, {{$dateDocument}}</div></td>
                    </tr>
                    <tr>
                        <td><div class="Customer">Nama Toko/Restoran Manajer</div></td>
                        <td><div class="Customer" style="margin-left: -50px;">:</div></td>
                        <td><div class="Customer" style="margin-left: -55px;"></div></td>
                    </tr>
                    <tr>
                        <td><div class="Customer">No. Telp./Handphone</div></td>
                        <td><div class="Customer" style="margin-left: -50px;">:</div></td>
                        <td><div class="Customer" style="margin-left: -55px;"></div></td>
                    </tr>
                    <tr>
                        <td><div class="Customer">Jumlah Karyawan Toko/Restoran</div></td>
                        <td><div class="Customer" style="margin-left: -50px;">:</div></td>
                        <td><div class="Customer" style="margin-left: -55px;"></div></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="Customer" style="margin-left: 10px;">
            <div class="termAndConditionAtas">
                <p style="text-align:justify;margin-left: -10px;">
                    Melalui form ini telah disetujui dan telah di survey oleh pihak Manajemen bahwa tanggal
                    pertama kali operasional Toko/Restoran Bapak/Ibu telah disesuaikan dengan kondisi operasional
                    unit.
                </p>
                <br>
                <p style="text-align:justify;margin-left: -10px;">
                    {{$dataProject['PROJECT_KOTA']}}, {{$dateDocument}}
                </p>
            </div>
            <br>
            <table style="margin-left: -60px;">
                <tr>
                    <td><div class="Customer">Dibuat Oleh,</div></td>
                    <td><div class="Customer">Disetujui Oleh,</div></td>
                    <td><div class="Customer">Diketahui Oleh,</div></td>
                </tr>
                <tr>
                    <td><br><br><br><br><br><br><br><br><br><br></td>
                    <td><br><br><br><br><br><br><br><br><br><br></td>
                    <td><br><br><br><br><br><br><br><br><br><br></td>
                </tr>
                <tr>
                    <td><div class="Customer">__________________</div></td>
                    <td><div class="Customer">__________________</div></td>
                    <td><div class="Customer">__________________</div></td>
                </tr>
                <tr>
                    <td><div class="Customer">Tenant Relation</div></td>
                    <td><div class="Customer">Penyewa</div></td>
                    <td><div class="Customer">Marketing & Communication Manager</div></td>
                </tr>
            </table>
        </div>
    </div>

    </body>
    </html>
