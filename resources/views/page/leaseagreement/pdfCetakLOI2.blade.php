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
            <div class="Customer" style="text-align: center;">
                <b>
                    SURAT KONFIRMASI SEWA<br>
                    {{strtoupper($dataProject['PROJECT_NAME'])}}<br>
                    {{$dataProject['PROJECT_ADDRESS']}}
                </b>
            </div>
            <br><br>
            <div class="Customer">
                No : {{$dataPSM->PSM_TRANS_NOCHAR}}
            </div>
        </div>
        <br><br>
        <div class="Customer">
            <b>I. <u>PEMILIK</u></b>
        </div>
        <div class="Customer" style="margin-left: 10px;">
            <table >
                <tr>
                    <td>
                        <div class="tableCustomer">Nama Perusahaan</div>
                    </td>
                    <td>
                        <div class="tableCustomer">:</div>
                    </td>
                    <td>
                        <div class="tableCustomer"><b>{{$dataCompany['COMPANY_NAME']}}</b></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer">Alamat</div>
                    </td>
                    <td>
                        <div class="tableCustomer">:</div>
                    </td>
                    <td>
                        <div class="tableCustomer">{{$dataCompany['COMPANY_ADDRESS']}}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer">No. NPWP</div>
                    </td>
                    <td>
                        <div class="tableCustomer">:</div>
                    </td>
                    <td>
                        <div class="tableCustomer">{{$dataCompany['COMPANY_NPWP']}}</div>
                    </td>
                </tr>
            </table>
        </div>
        <br><br>
        <div class="Customer" >
            <b>II. <u>PENYEWA</u></b>
        </div>
        <div class="Customer" style="margin-left: 10px;">
            <table >
                <tr>
                    <td>
                        <div class="tableCustomer">Nama </div>
                    </td>
                    <td>
                        <div class="tableCustomer">:</div>
                    </td>
                    <td>
                        <div class="tableCustomer"><b>{{$dataTenant->MD_TENANT_NAME_CHAR}}</b></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer">Alamat </div>
                    </td>
                    <td>
                        <div class="tableCustomer">:</div>
                    </td>
                    <td>
                        <div class="tableCustomer">{!! $dataTenant->MD_TENANT_ADDRESS1 !!}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer">Nama Brand </div>
                    </td>
                    <td>
                        <div class="tableCustomer">:</div>
                    </td>
                    <td>
                        <div class="tableCustomer">{{ $dataPSM->SHOP_NAME_CHAR }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer">Jenis Usaha </div>
                    </td>
                    <td>
                        <div class="tableCustomer">:</div>
                    </td>
                    <td>
                        <div class="tableCustomer"><b>{{ $dataCategory->PSM_CATEGORY_NAME }}</b></div>
                    </td>
                </tr>
            </table>
        </div>
        <br><br>
        <div class="Customer" >
            <b>III. <u>RUANG SEWA</u></b>
        </div>
        <div class="Customer" style="margin-left: 10px;">
            <table>
                <?php $dataLotTotal = 0; ?>
                @foreach($dataLot as $key => $data)
                <tr>
                    <td>
                        @if(count($dataLot) > 1)
                        <div class="tableCustomer">Lokasi {{$key + 1}} / Unit No. </div>
                        @else
                        <div class="tableCustomer">Lokasi / Unit No. </div>
                        @endif
                    </td>
                    <td>
                        <div class="tableCustomer">:</div>
                    </td>
                    <td>
                        <div class="tableCustomer">{{$data['LOT_LEVEL']->LOT_LEVEL_DESC}} No. {{$data['LOT_STOCK']->LOT_STOCK_NO}} dengan Luas ± {{number_format($data['LOT_STOCK']->LOT_STOCK_SQM,2,',','.')}} m²</div>
                    </td>
                </tr>
                <?php $dataLotTotal += (float) $data['LOT_STOCK']->LOT_STOCK_SQM; ?>
                @endforeach
                <tr>
                    <td>
                        <div class="tableCustomer">Total Luas</div>
                    </td>
                    <td>
                        <div class="tableCustomer">:</div>
                    </td>
                    <td>
                        <div class="tableCustomer">± {{number_format($dataLotTotal,2,',','.')}} m²</div>
                    </td>
                </tr>
            </table>
        </div>
        <br><br>
        <div class="Customer" >
            <b>IV. <u>JANGKA WAKTU SEWA</u></b>
        </div>
        <div class="Customer" style="margin-left: 10px;">
            <table>
                <tr>
                    <td>
                        <div class="tableCustomer">Masa Sewa </div>
                    </td>
                    <td>
                        <div class="tableCustomer">:</div>
                    </td>
                    <td>
                        <div class="tableCustomer">{{$masaSewa}}</div>
                    </td>
                </tr>
            </table>
        </div>
        <br><br>
        <div class="Customer" >
            <b>V. <u>HARGA SEWA, UTILITY, UANG JAMINAN SEWA & CARA PEMBAYARAN</u></b>
        </div>
        <div class="Customer" style="margin-left: 10px;">
            <table>
                <tr>
                    <td>
                        <div class="tableCustomer"><b>1. </b></div>
                    </td>
                    <td>
                        <div class="tableCustomer"><b>Harga Sewa </b></div>
                    </td>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                </tr>
                <?php $totalPrice = 0; ?>
                @foreach($dataLotPriceDetails as $key => $data)
                <tr>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td>
                        @if($data->IS_GENERATE_MANUAL == 1)
                        <div class="tableCustomer">{{$masaSewa}}</div>
                        @else
                            @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
                            <div class="tableCustomer">Tahun {{$data->PSM_TRANS_PRICE_YEAR}} (< 1 Bulan)</div>
                            @else
                            <div class="tableCustomer">Tahun {{$data->PSM_TRANS_PRICE_YEAR}} ({{$arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]}} Bulan)</div>
                            @endif
                        @endif
                    </td>
                    <td>
                        <div class="tableCustomer">:</div>
                    </td>
                    <td>
                        @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
                        <div class="tableCustomer">Rp. {{number_format($data->PSM_TRANS_PRICE_RENT_NUM,0,'','.')}},-/m²/(< 1 bulan) (belum termasuk PPN) </div>
                        @else
                        <div class="tableCustomer">Rp. {{number_format($data->PSM_TRANS_PRICE_RENT_NUM,0,'','.')}},-/m²/bulan (belum termasuk PPN) </div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td>
                        @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
                        <?php $subTotal = ($data->PSM_TRANS_PRICE_RENT_NUM * $dataLotTotal) * 1; ?>
                        <div class="tableCustomer">Rp. {{number_format($data->PSM_TRANS_PRICE_RENT_NUM,0,'','.')}},- x {{number_format($dataLotTotal,2,',','.')}} m² x (< 1 bulan) = Rp. {{ number_format($subTotal,0,'','.') }},- (belum termasuk PPN)</div>
                        @else
                        <?php $subTotal = ($data->PSM_TRANS_PRICE_RENT_NUM * $dataLotTotal) * $arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]; ?>
                        <div class="tableCustomer">Rp. {{number_format($data->PSM_TRANS_PRICE_RENT_NUM,0,'','.')}},- x {{number_format($dataLotTotal,2,',','.')}} m² x {{$arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]}} bulan = Rp. {{ number_format($subTotal,0,'','.') }},- (belum termasuk PPN)</div>
                        @endif
                    </td>
                </tr>
                <?php $totalPrice += $data->PSM_TRANS_PRICE_RENT_NUM; ?>
                @endforeach
                <tr>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td>
                        <div class="tableCustomer">Total Sewa</div>
                    </td>
                    <td>
                        <div class="tableCustomer">:</div>
                    </td>
                    <td>
                        <div class="tableCustomer">Rp. {{number_format($dataPSM->PSM_TRANS_NET_BEFORE_TAX,0,'','.')}},- (sudah termasuk PPh dan belum termasuk PPN) </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer"><b>2. </b></div>
                    </td>
                    <td>
                        <div class="tableCustomer"><b>Sisa Pembayaran Sewa </b></div>
                    </td>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td colspan="3">
                        <div class="tableCustomer">
                            Sisa pembayaran dari total harga sewa atau sebesar Rp. {{number_format($dataPSM->PSM_TRANS_NET_BEFORE_TAX,0,'','.')}},- (belum   termasuk PPN),
                            dibayarkan sejak masa sewa dimulai hingga bulan sewa ke – {{number_format($dataPSM->PSM_TRANS_TIME_PERIOD_SCHED,0,'','.')}} ({{$terbilangSchedule}}),
                            sebesar
                            @if(isset($TopSchedule))
                                @Rp {{number_format($TopSchedule->BASE_AMOUNT_NUM,0,'','.')}},-
                            @else
                                @Rp {{number_format(0,0,'','.')}},-
                            @endif
                            per-bulan (belum termasuk PPN).  Sisa pembayaran sewa akan dibayar bulanan
                            selambat-lambatnya setiap tanggal 10 bulan sewa berjalan.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer"><b>3. </b></div>
                    </td>
                    <td>
                        <div class="tableCustomer"><b>Utility </b></div>
                    </td>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td colspan="3">
                        <div class="tableCustomer">
                            Setiap pembayaran Utility baik listrik maupun air akan dikenakan PPh namun belum termasuk PPn.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer"><b>4. </b></div>
                    </td>
                    <td>
                        <div class="tableCustomer"><b>Uang Jaminan </b></div>
                    </td>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td colspan="3">
                        <div class="tableCustomer">
                            Uang jaminan akan dikembalikan pada akhir masa sewa, kecuali uang jaminan fitout yang akan dikembalikan setelah fitout selesai.
                            <br><br>
                            @foreach($dataSecureDep as $secure)
                                <b>- {{$secure->PSM_TRANS_DEPOSIT_DESC}} Rp. {{number_format($secure->PSM_TRANS_DEPOSIT_NUM,0,'','.')}},-</b>
                            @endforeach
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer"><b>5. </b></div>
                    </td>
                    <td>
                        <div class="tableCustomer"><b>Rekening pembayaran </b></div>
                    </td>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td colspan="3">
                        <div class="tableCustomer">
                            Pembayaran ditujukan kepada <b>{{$dataCompany['COMPANY_NAME']}}</b> pada <b>{{$dataFinSetup->BM_BANK_ACCOUNT}}</b> melalui no <b>VA {{$dataPSM->PSM_TRANS_VA}}</b>,
                            Semua pembayaran dinyatakan sah apabila uang telah sepenuhnya berada di dalam rekening tersebut di atas.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer"><b>6. </b></div>
                    </td>
                    <td>
                        <div class="tableCustomer"><b>Bukti Potong Pajak Penghasilan </b></div>
                    </td>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td colspan="3">
                        <div class="tableCustomer">
                            <ol style="text-align: justify">
                                <li>
                                    Penyewa wajib memberikan bukti potong Pajak PPh kepada Pemilik paling lambat akhir
                                    bulan pada bulan berikutnya, setelah Penyewa menerima asli surat tagihan dari
                                    Pemilik (jika tenant PKP).
                                </li>
                                <li>
                                    Jika Pemilik tidak menerima bukti potong Pajak PPh tersebut secara benar sesuai jadwal
                                    di atas, maka akan dianggap sebagai kekurangan pembayaran dan berlaku ketentuan denda
                                    keterlambatan pembayaran.
                                </li>
                            </ol>
                        </div>
                    </td>
                <tr>
                    <td>
                        <div class="tableCustomer"><b>7. </b></div>
                    </td>
                    <td>
                        <div class="tableCustomer"><b>Denda Keterlambatan Pembayaran </b></div>
                    </td>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="tableCustomer"></div>
                    </td>
                    <td colspan="3">
                        <div class="tableCustomer">
                            <ol style="text-align: justify">
                                <li>
                                    Setiap tagihan dari Pemilik, yang wajib dibayar oleh Penyewa, memiliki tanggal jatuh tempo.
                                </li>
                                <li>
                                    Jika tanggal jatuh tempo masing-masing tagihan terlampaui, maka Penyewa akan dikenakan denda
                                    sebesar 1‰ (satu per mill) perhari keterlambatan dari jumlah pembayaran yang sudah jatuh tempo.
                                </li>
                            </ol>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <br><br>
        <div class="Customer" >
            <b>VI. <u>FITOUT</u></b>
        </div>
        <div class="Customer" style="margin-left: 10px;">
            <ol class="num" style="text-align: justify;margin-left: 15px;">
                <li>
                    Penyewa wajib membuat gambar rencana kerja ( layout & section ).<br>
                    Gambar rencana kerja diajukan kepada team Tenant Design Coordinator (TDC) untuk dievaluasi dan memperoleh persetujuan terlebih dahulu.
                </li>
                <li>
                    Penyewa wajib menyelesaikan pekerjaan fitout dan siap membuka usahanya selambat-lambatnya 2 (dua) bulan terhitung sejak tanggal serah terima tempat usaha.
                </li>
                <li>
                    Selama masa fitout, Penyewa membayar semua biaya fasilitas pekerjaan fitout, antara lain :  listrik, air kerja dan telepon.
                </li>
                <li>
                    Penyewa / Kontraktor wajib membayar uang jaminan fitout sebelum pekerjaan fitout dimulai.
                </li>
            </ol>

        </div>
        <br><br>
        <div class="Customer" >
            <b>VII. <u>PERJANJIAN SEWA MENYEWA</u></b>
        </div>
        <div class="Customer" style="margin-left: 25px;">
            <div class="tableCustomer" style="text-align: justify">
                Ketentuan dan persyaratan lain sehubungan dengan sewa menyewa yang belum diatur dalam Surat
                Konfirmasi Sewa ini, akan dituangkan lebih lanjut dalam Perjanjian Sewa Menyewa.<br>

                Penyewa wajib menandatangi Perjanjian Sewa Menyewa selambat-lambatnya 2 (dua) minggu sejak
                tanggal diterimanya Perjanjian Sewa Menyewa. Apabila sampai dengan terlewatinya waktu
                tersebut, penyewa belum dan atau tidak menandatangani perjanjian sewa menyewa dimaksud,
                maka penyewa menyatakan setuju dan dianggap menyetujui ketentuan dalam perjanjian sewa menyewa.
            </div>
        </div>
        <br><br>
        <div class="Customer" >
            <b>VIII. <u>PEMBATALAN SEWA</u></b>
        </div>
        <div class="Customer" style="margin-left: 25px; text-align: justify">
            {{$dataCompany['COMPANY_NAME']}} dapat membatalkan sewanya berdasarkan Surat Konfirmasi Sewa ini, apabila terjadi salah satu dari keadaan di bawah ini :

            <ol class="la" style="margin-left: 10px;">
                <li>
                    Penyewa tidak menandatangani dan mengembalikan Surat Konfirmasi Sewa beserta
                    membayarkan uang muka sewa dalam waktu 14 hari kerja terhitung sejak tanggal
                    dikeluarkannya Surat Konfirmasi Sewa ini.
                </li>
                <li>
                    Penyewa tidak membuka / mengoperasikan usahanya selambat-lambatnya 1 (satu) bulan
                    sejak tanggal dimulainya masa sewa.
                </li>
                <li>
                    Penyewa mengalihkan hak sewanya kepada pihak lain manapun juga tanpa sepengetahuan
                    dan persetujuan tertulis dari {{$dataCompany['COMPANY_NAME']}}
                </li>
                <li>
                    Penyewa atas kehendaknya mengundurkan diri dan membatalkan pemesanan ruang sewa
                    sebelum masa operasional dengan mengajukan surat pengunduran diri secara tertulis.
                </li>
            </ol>

            Sebagai akibat dari pembatalan tersebut di atas maka semua uang yang telah dibayarkan
            oleh Penyewa dinyatakan hangus dan tidak dapat ditarik kembali oleh Penyewa dan menjadi
            hak milik sepenuhnya {{$dataCompany['COMPANY_NAME']}}, kecuali uang jaminan yang telah diberikan
            oleh Penyewa setelah dikurangi dengan semua biaya yang menjadi tanggung jawab Penyewa (bila ada).
        </div>

        <div class="Customer" style="margin-left: 10px;">
            <div class="termAndConditionAtas">
                <p style="text-align:justify;">
                    Dengan ditandatanganinya Surat Konfirmasi Sewa ini, maka Penyewa setuju dan bersedia mematuhi segala
                    persyaratan, ketentuan dan sanksi-sanksi sebagaimana diuraikan dalam Surat Konfirmasi Sewa ini serta
                    dokumen ketentuan lainnya yang telah ditetapkan oleh {{$dataCompany['COMPANY_NAME']}}
                </p>
                <br>
                <p style="text-align:justify;">
                    {{$dataProject['PROJECT_KOTA']}}, {{$dateDocument}}
                </p>
                <center>
                    <table style="width:100%;">
                        <tr>
                            <td><div class="tableCustomer">Penyewa,</div></td>
                            <td></td>
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
                            <td><div class="tableCustomer">Pemilik,</div></td>
                        </tr>
                        <tr>
                            <td><div class="tableCustomer"><b>{{$dataTenant->MD_TENANT_NAME_CHAR}}</b></div></td>
                            <td></td>
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
                            <td><div class="tableCustomer"><b>{{$dataCompany['COMPANY_NAME']}}</b></div></td>
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
                            <td><div class="tableCustomer"><label>'_________________________'</label></div></td>
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
                            <td><div class="tableCustomer"><label>'_________________________'</label></div></td>
                        </tr>
                        <tr>
                            <td><div class="tableCustomer">Nama &nbsp;&nbsp;&nbsp;&nbsp;: {{$dataTenant->MD_TENANT_DIRECTOR}}</div></td>
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
                            <td><div class="tableCustomer">Nama &nbsp;&nbsp;&nbsp;: {{$dataProject->DIR_OPS_NAME}}</div></td>
                        </tr>
                        <tr>
                            <td><div class="tableCustomer">Jabatan &nbsp;: {{$dataTenant->MD_TENANT_DIRECTOR_JOB_TITLE}}</div></td>
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
                            <td><div class="tableCustomer">Jabatan : Direktur</div></td>
                        </tr>
                    </table>
                </center>
            </div>
        </div>
    </div>

    </body>
    </html>
