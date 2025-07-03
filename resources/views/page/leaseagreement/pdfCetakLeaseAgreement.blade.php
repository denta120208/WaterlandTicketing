<html>
    <style>
        html * {
            font-family: "Arial Narrow", Arial, sans-serif;
            /* page-break-inside: avoid; */
        }
        .pagebreak { 
            page-break-before: always; 
        }
    </style>

    <h3 style="text-align: center;">PERJANJIAN SEWA MENYEWA <br />No. {{ $dataPSM->NO_KONTRAK_NOCHAR }}</h3>
    <hr />
    <p style="text-align: justify;">Perjanjian Sewa Menyewa tenant {{ $dataTenant->MD_TENANT_NAME_CHAR }} ini dibuat dan ditandatangani pada hari {{ $hariChar }}, tanggal {{ date('d/m/Y', strtotime($dataPSM->NO_KONTRAK_DATE)) }} oleh dan antara:</p>
    <table style="height: 36px; width: 100%; border-collapse: collapse;" border="0">
    <tbody>
        <tr style="height: 18px;">
        <td style="width: 11.2723%; height: 18px;">Nama</td>
        <td style="width: 2.99952%; height: 18px;">:</td>
        <td style="width: 85.7281%; height: 18px;">
            <strong>Anhar Sudradjat</strong>
        </td>
        </tr>
        <tr style="height: 18px;">
        <td style="width: 11.2723%; height: 18px;">Jabatan</td>
        <td style="width: 2.99952%; height: 18px;">:</td>
        <td style="width: 85.7281%; height: 18px;">{{ $jabatanDir }}</td>
        </tr>
    </tbody>
    </table>
    <p style="text-align: justify;">Dalam hal ini menjalankan jabatannya tersebut diatas karenanya sah bertindak untuk dan atas nama perseroan terbatas <strong>{{ $dataCompany->COMPANY_NAME }}, </strong>berkedudukan dan beralamat di {{ $dataCompany->COMPANY_ADDRESS }}, selanjutnya perseroan ini berikut para pengganti dan/atau penerima haknya disebut <strong>&ldquo;PEMILIK&rdquo;</strong>. </p>
    <table style="width: 100%; border-collapse: collapse;" border="0">
    <tbody>
        <tr>
        <td style="width: 23.1736%;">Nama</td>
        <td style="width: 3.28981%;">:</td>
        <td style="width: 73.5365%;">
            <strong>{{ $dataTenant->MD_TENANT_DIRECTOR }}</strong>
        </td>
        </tr>
        <tr>
        <td style="width: 23.1736%;">No. KTP</td>
        <td style="width: 3.28981%;">:</td>
        <td style="width: 73.5365%;">{{ $dataTenant->MD_TENANT_NIK }}</td>
        </tr>
        <tr>
        <td style="width: 23.1736%;">No. NPWP</td>
        <td style="width: 3.28981%;">:</td>
        <td style="width: 73.5365%;">{{ $dataTenant->MD_TENANT_NPWP }}</td>
        </tr>
        <tr>
        <td style="width: 23.1736%;">Jabatan/Kapasitas</td>
        <td style="width: 3.28981%;">:</td>
        <td style="width: 73.5365%;">{{ $dataTenant->MD_TENANT_DIRECTOR_JOB_TITLE }}</td>
        </tr>
    </tbody>
    </table>
    <p style="text-align: justify;">Dalam hal ini menjalankan kapasitasnya tersebut karenanya sah bertindak untuk dan atas nama <strong>{{ $dataTenant->MD_TENANT_NAME_CHAR }}</strong> berkedudukan dan beralamat di {{ strip_tags($dataTenant->MD_TENANT_ADDRESS1) }}, selanjutnya yang bersangkutan berikut para pengganti dan/atau penerima haknya disebut <strong>&ldquo;PENYEWA&rdquo;</strong>. </p>
    <p style="text-align: justify;">
    <strong>PEMILIK</strong> dan <strong>PENYEWA</strong> masing-masing disebut <strong>&ldquo;Pihak&rdquo;</strong> dan secara bersama-sama disebut <strong>&ldquo;Para Pihak&rdquo;</strong>.
    </p>
    <p style="text-align: justify;">
    <strong>Para Pihak</strong> dengan ini terlebih dahulu menerangkan sebagai berikut bahwa:
    </p>
    <p style="text-align: justify;">A. PEMILIK adalah pemilik sebidang tanah Hak Guna Bangunan terletak di {{ $dataProject->PROJECT_ADDRESS }} dan dikenal sebagai <strong>{{ strtoupper($dataProject->PROJECT_NAME) }}</strong>. </p>
    <p style="text-align: justify;">B. PENYEWA hendak menyewa dari PEMILIK dan PEMILIK setuju untuk menyewakan Ruangan (sebagaimana didefinisikan dalam Perjanjian ini) kepada PENYEWA dan untuk maksud tersebut, PEMILIK dan PENYEWA telah menandatangani Surat Pemesanan Ruang Sewa tanggal {{ date('d/m/Y', strtotime($dataPSM->NO_KONTRAK_DATE)) }}, No. {{ $dataPSM->NO_KONTRAK_NOCHAR }} yang memuat pokok-pokok ketentuan dan persyaratan dasar sewa Ruangan (selanjutnya disebut <strong>&ldquo;Surat Konfirmasi Sewa&rdquo;</strong>). </p>
    <p style="text-align: justify;">Selanjutnya Para Pihak telah mufakat dan setuju untuk mengikatkan diri dalam hubungan sewa menyewa Ruangan serta untuk selanjutnya dituangkan dalam Perjanjian ini dengan syarat-syarat dan ketentuan-ketentuan sebagai berikut:</p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 1</strong>
    <br />
    <strong>D E F I N I S I</strong>
    </h3>
    <p style="text-align: justify;">Untuk maksud Perjanjian ini istilah-istilah berikut ini mempunyai arti sebagai berikut, kecuali konteksnya mensyaratkan pengertian lain :</p>
    <p style="text-align: justify;">a. <strong>&ldquo;Gedung&rdquo;</strong> berarti gedung {{ strtoupper($dataProject->PROJECT_NAME) }} pusat pertokoan dan perbelanjaan milik PEMILIK yang dibangun di {{ $dataProject->PROJECT_ADDRESS }}. </p>
    <p style="text-align: justify;">b. <strong>&ldquo;Ruangan&rdquo;</strong> berarti ruangan dalam Gedung yang terletak di <strong>{{ $dataPSMLevel }} {{ $dataLot }}</strong> seluas kurang lebih <strong>{{ (float) $dataLotSqm }} m&sup2; ({{ $dataLotSqmChar }} meter persegi)</strong> yang akan dipergunakan PENYEWA sebagai tempat usaha PENYEWA. </p>
    <p style="text-align: justify;">c. <strong>&ldquo;Jangka Waktu Sewa&rdquo;</strong> berarti jangka waktu sewa Ruangan oleh PENYEWA sebagaimana diuraikan dalam Pasal 3 Perjanjian ini, sampai berakhirnya atau sampai diakhirinya jangka waktu sewa oleh salah satu Pihak berdasarkan ketentuan dalam Perjanjian ini. </p>
    <p style="text-align: justify;">d. <strong>&ldquo;Perjanjian&rdquo;</strong> berarti Perjanjian Sewa Menyewa ini berikut segala lampirannya yang menjadi satu kesatuan dengan Perjanjian Sewa Menyewa ini sebagaimana diubah, diperbaharui dan/ atau ditambah dari waktu ke waktu dengan persetujuan Para Pihak. </p>
    <p style="text-align: justify;">e. <strong>&ldquo;Service Charge&rdquo;</strong> berarti biaya untuk pemeliharaan Gedung, perijinan, perbaikan-perbaikan Gedung, bahan-bahan cadangan untuk penggantian serta perlengkapan dan peralatan untuk Gedung, <em>Management</em>/pengelolaan Gedung tarif-tarif dan asuransi Gedung. </p>
    <p style="text-align: justify;">f. <strong>&ldquo;Uang Jaminan&rdquo;</strong> atau <em>security deposit</em> berarti sejumlah uang yang dibayarkan PENYEWA kepada <strong>PEMILIK</strong> sebagai bentuk jaminan atas pelunasan (kewajiban) PENYEWA yang tertunggak (bila ada) yang nilainya sebagaimana disepakati dalam Pasal 9 Perjanjian ini. </p>
    <p style="text-align: justify;">g. <strong>&ldquo;Uang Sewa&rdquo;</strong> berarti uang sewa sebagaimana diuraikan dalam Pasal 5 Perjanjian ini. </p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 2</strong>
    <br />OBJEK SEWA
    </h3>
    <p style="text-align: justify;">Berdasarkan syarat-syarat dan ketentuan-ketentuan dalam Perjanjian, PEMILIK dengan ini menyewakan kepada PENYEWA dan PENYEWA menyewa Ruangan dari PEMILIK selama Jangka Waktu Sewa untuk dipergunakan PENYEWA sebagai tempat kegiatan usahanya.</p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 3</strong>
    <br />JANGKA WAKTU SEWA dan PERPANJANGAN
    </h3>
    <p style="text-align: justify;">3.1 Jangka Waktu Sewa adalah <strong>{{ $masaSewa }}</strong> terhitung sejak tanggal <strong>{{ $startDateIndo }}</strong> sampai dengan tanggal <strong>{{ $endDateIndo }}</strong> dan kecuali diperpanjang atau diakhiri lebih dahulu oleh salah satu Pihak berdasarkan ketentuan dalam Perjanjian ini. Apabila setelah Perjanjian ini berakhir terdapat kesepakatan lain, maka Para Pihak akan membuat Surat Kesepakatan dan/atau Berita Acara terkait dengan kesepakatan tersebut yang menjadi satu kesatuan dengan Perjanjian ini dan/atau membuat addendum terkait perubahan tersebut. </p>
    <p style="text-align: justify;">3.2 PENYEWA diberi kesempatan untuk memperpanjang Jangka Waktu Sewa dengan cara mengajukan permohonan tertulis kepada PEMILIK yang harus diterima oleh PEMILIK sedikitnya <strong>6 (enam) bulan</strong> sebelum berakhirnya Jangka Waktu Sewa. Jika PEMILIK tidak menerima permohonan tersebut tepat pada waktunya, PEMILIK berhak menarik kesimpulan bahwa PENYEWA tidak menghendaki memperpanjang Jangka Waktu Sewa, dan PEMILIK berhak untuk menyewakan Ruangan kepada pihak lain. PEMILIK berhak untuk menerima atau menolak permohonan perpanjangan Jangka Waktu Sewa tersebut. Dalam hal PEMILIK menyetujui perpanjangan Jangka Waktu Sewa tersebut, PEMILIK berhak menentukan syarat dan ketentuan lain bagi Jangka Waktu Sewa yang diperpanjang itu, baik mengenai Jangka Waktu Sewa, Uang Sewa maupun syarat-syarat lain, mengenai sewa menyewa Ruangan itu. <br />Apabila PENYEWA tidak dapat menyetujui syarat-syarat baru atau PENYEWA tidak membayar uang muka untuk Uang Sewa untuk perpanjangan Jangka Waktu sewa sedikitnya 3 (tiga) bulan sebelum berakhirnya Jangka Waktu Sewa yang sedang berlangsung PEMILIK berhak menentukan Jangka Waktu Sewa tidak dapat diperpanjang dan dengan sendirinya berakhir pada tanggal yang telah disebut dalam Pasal 3 ayat 3.1. </p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 4</strong>
    <br />
    <strong>SYARAT PENEMPATAN RUANGAN</strong>
    </h3>
    <p style="text-align: justify;">4.1. PENYEWA berhak menempati Ruangan jika telah memenuhi semua ketentuan di bawah ini:</p>
    <ol style="text-align: justify;">
    <li style="text-align: justify;">Perjanjian serta dokumen lain yang disyaratkan dalam Perjanjian telah ditandatangani sebagaimana mestinya.</li>
    <li style="text-align: justify;">Uang Sewa yang ditentukan dalam Pasal 5 ayat 5.1 di bawah ini, beserta denda-denda (jika kondisinya perpanjangan sewa), semua uang jaminan <em>service charge</em> dan uang jaminan listrik dan air, termaksud tapi tidak terbatas dalam Pasal 9 ayat 9.1. dan ayat 9.2. di bawah ini, maupun biaya lainnya yang masih terhutang (jika kondisinya perpanjangan) telah diterima oleh PEMILIK dari PENYEWA sebagaimana mestinya. </li>
    </ol>
    <p style="text-align: justify;">4.2. PEMILIK memberi hak kepada PENYEWA untuk mengatur tata ruang dalam Ruangan selama jangka waktu yang disepakati kemudian oleh Para Pihak dan sepanjang Ruangan tidak digunakan oleh PENYEWA untuk maksud komersial, PEMILIK tidak akan mengenakan Uang Sewa selama jangka waktu penataan Ruangan tersebut.</p>
    <p style="text-align: justify;">4.3. PENYEWA membebaskan PEMILIK dari tanggung jawab dan/atau gugatan dan/atau tuntutan apapun jika PENYEWA terlambat menempati Ruangan disebabkan oleh kegagalan PENYEWA dalam menyelesaikan penataan Ruangan berdasarkan ketentuan yang ditetapkan dalam Pasal 4 ayat 4.2 Perjanjian ini.</p>
    <p style="text-align: justify;">4.4. PENYEWA wajib untuk merenovasi interior dan eksterior Ruangan setiap 5 (lima) tahun atau sewaktu-waktu sebelum 5 (lima) tahun apabila dianggap perlu oleh PEMILIK selama Jangka Waktu Sewa.</p>
    <p style="text-align: justify;">4.5. Apabila sebagian atau seluruh ruangan diperlukan oleh PEMILIK untuk kepentingan Mall, maka PENYEWA bersedia dan berkewajiban menyerahkan sebagian atau keseluruhan Ruangan yang dimaksud kepada PEMILIK dan terhadap nilai sewa Ruangan tersebut akan dihitung kembali dengan ketentuan hal tersebut akan dimusyawarahkan terlebih dahulu dengan PENYEWA, satu lain hal semata demi kebaikan Para Pihak.</p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 5</strong>
    <br />
    <strong>UANG SEWA DAN MEKANISME PEMBAYARAN</strong>
    </h3>
    <p style="text-align: justify;">5.1 Uang Sewa untuk Ruangan selama Jangka Waktu Sewa disepakati oleh Para Pihak sebesar :</p>
    <table style="width: 100%; border-collapse: collapse;" border="0">
    <tbody>
        <?php $totalPrice = 0; ?>
        @foreach($dataLotPriceDetails as $key => $data)
        <tr>
        @if($data->IS_GENERATE_MANUAL == 1)
        <td style="width: 25.735%;">{{ $masaSewa }}</td>
        @else
            @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
            <td style="width: 25.735%;">Tahun {{$data->PSM_TRANS_PRICE_YEAR}} (< 1 Bulan)</td>
            @else
            <td style="width: 25.735%;">Tahun {{$data->PSM_TRANS_PRICE_YEAR}} ({{$arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]}} Bulan)</td>
            @endif
        @endif
        <td style="width: 2.1728%;">:</td>
        @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
        <td style="width: 72.0921%;">Rp. {{number_format($data->PSM_TRANS_PRICE_RENT_NUM,0,'','.')}},-/m²/(< 1 bulan) (belum termasuk PPN)</td>
        @else
        <td style="width: 72.0921%;">Rp. {{number_format($data->PSM_TRANS_PRICE_RENT_NUM,0,'','.')}},-/m²/bulan (belum termasuk PPN)</td>
        @endif
        </tr>
        <tr>
        <td style="width: 25.735%;"></td>
        <td style="width: 2.1728%;"></td>
        <td style="width: 72.0921%;">
        @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
        <?php $subTotal = ($data->PSM_TRANS_PRICE_RENT_NUM * $dataLotSqm) * 1; ?>
        Rp. {{number_format($data->PSM_TRANS_PRICE_RENT_NUM,0,'','.')}},- x {{number_format($dataLotSqm,2,',','.')}} m² x (< 1 bulan) = Rp. {{ number_format($subTotal,0,'','.') }},- (belum termasuk PPN)
        @else
        <?php $subTotal = ($data->PSM_TRANS_PRICE_RENT_NUM * $dataLotSqm) * $arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]; ?>
        Rp. {{number_format($data->PSM_TRANS_PRICE_RENT_NUM,0,'','.')}},- x {{number_format($dataLotSqm,2,',','.')}} m² x {{$arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]}} bulan = Rp. {{ number_format($subTotal,0,'','.') }},- (belum termasuk PPN)
        @endif
        </td>
        </tr>
        <?php $totalPrice += $subTotal; ?>
        @endforeach
        <tr>
        <td style="width: 25.735%;">Total</td>
        <td style="width: 2.1728%;">:</td>
        <td style="width: 72.0921%;">Rp. {{number_format($dataPSM->PSM_TRANS_NET_BEFORE_TAX,0,'','.')}},- (belum termasuk PPN)</td>
        </tr>
    </tbody>
    </table>
    <p style="text-align: justify;">5.2 Pembayaran akan dilakukan setiap tanggal 10 setiap bulannya, lewat tanggal ini akan dikenakan denda sebesar 1&permil; (satu per mill) perhari keterlambatan dari jumlah pembayaran yang sudah jatuh tempo.</p>
    <p style="text-align: justify;">5.3 Dalam hal pembayaran dilakukan dengan cek atau giro bilyet, maka pembayaran baru dianggap sah apabila jumlah uang yang tertulis dalam cek atau giro bilyet yang bersangkutan sudah diterima oleh PEMILIK atau sudah dipindah bukukan ke dalam rekening Bank PEMILIK dengan benar.</p>
    <p style="text-align: justify;">5.4 Pada dasarnya semua pembayaran harus dilakukan dalam mata uang yang disebut dalam Pasal 5.1.</p>
    <p style="text-align: justify;">5.5 Setiap pembayaran harus dilakukan melalui transfer ke Virtual Account {{ $dataFinSetup->BM_BANK_ACCOUNT }} No. {{ $dataPSM->PSM_TRANS_VA }} a.n. {{ $dataFinSetup->BM_BANK_NAME_ACCOUNT }} dan/atau menggunakan Bilyet Giro yang wajib mencantumkan Virtual Account {{ $dataFinSetup->BM_BANK_ACCOUNT }} No. {{ $dataPSM->PSM_TRANS_VA }}. a.n. {{ $dataFinSetup->BM_BANK_NAME_ACCOUNT }} yang harus diterima langsung oleh PEMILIK, di kantor PEMILIK di {{ $dataProject->PROJECT_ADDRESS }}.</p>
    <p style="text-align: justify;">5.6 Kecuali disepakati berbeda oleh Para Pihak, pembayaran yang dilakukan tidak menurut ketentuan dalam Perjanjian ini tidak berlaku dan tidak mengikat bagi PEMILIK, PENYEWA dianggap belum melakukan pembayaran sebagaimana mestinya.</p>
    <p style="text-align: justify;">5.7 PENYEWA wajib memberikan bukti potong Pajak PPh kepada PEMILIK paling lambat akhir bulan pada bulan berikutnya setelah PENYEWA menerima asli surat tagihan ( <em>invoice</em>) dari PEMILIK dan apabila PEMILIK tidak menerima bukti potong tersebut secara benar, maka akan dianggap sebagai kekurangan pembayaran. </p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 6</strong>
    <br />
    <strong>PENATAAN RUANGAN</strong>
    </h3>
    <p style="text-align: justify;">6.1. Penataan Ruangan dan perubahannya dilakukan oleh PENYEWA atas biaya PENYEWA dan harus diselesaikan oleh PENYEWA sesuai Pasal 4 ayat 4.2. Perjanjian ini. <br />Rencana Gambar penataan Ruangan harus disetujui tertulis lebih dahulu oleh PEMILIK. </p>
    <p style="text-align: justify;">6.2. Semua tindakan baik yang disengaja maupun yang tidak disengaja, kelalaian atau kealpaan wakil-wakil atau karyawan PENYEWA sendiri dalam melaksanakan penataan Ruangan yang menyebabkan kerusakan pada Ruangan Instalasi Listrik, Air, <em>Air Conditioning</em>, telepon, dianggap sebagai tindakan kelalaian atau kealpaan PENYEWA dan sepenuhnya menjadi tanggung jawab PENYEWA. </p>
    <p style="text-align: justify;">6.3. PENYEWA wajib membayar ganti rugi kepada PEMILIK atas semua kerugian yang diderita oleh PEMILIK sebagai akibat dari penataan Ruangan atau dari adanya tuntutan dan/atau gugatan pihak ketiga berkenaan dengan pelaksanaan penataan Ruangan tersebut.</p>
    <p style="text-align: justify;">6.4. Penyerahan Ruangan sewa kepada PENYEWA dalam kondisi: Ruangan tanpa Plafon ( <em>ceiling</em>), lantai bare, tanpa <em>shop front</em>, instalasi hanya sampai penyambungan daya KWH meter. </p>
    <p style="text-align: justify;">6.5. Setiap rencana design ruangan dan tampak muka ruangan sewa harus mengikuti petunjuk dekorasi ruangan yang akan diberikan oleh PEMILIK dan mendapatkan persetujuan tertulis oleh PEMILIK.</p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 7</strong>
    <br />
    <strong>PENGGUNAAN RUANGAN</strong>
    </h3>
    <p style="text-align: justify;">7.1 PENYEWA hanya diperkenankan untuk menggunakan Ruangan untuk menjalankan usahanya dengan nama <strong>{{ $dataPSM->SHOP_NAME_CHAR }}</strong>, PENYEWA menjamin kepada PEMILIK bahwa PENYEWA telah mengurus, memperoleh, dan memperbaharui semua bentuk perizinan dan persetujuan yang diperlukan untuk menjalankan usahanya tersebut dari pihak pemerintah Republik Indonesia yang berwenang. </p>
    <p style="text-align: justify;">7.2 PENYEWA wajib mendapatkan persetujuan tertulis lebih dahulu dari PEMILIK, jika PENYEWA hendak memakai Ruangan untuk maksud lain dari pada yang telah ditetapkan di atas.</p>
    <p style="text-align: justify;">7.3 Jika PENYEWA menggunakan Ruangan menyimpang dari tujuan yang diuraikan dalam Pasal 7.1. di atas tanpa persetujuan tertulis sebelumnya dari PEMILIK dan PEMILIK telah memberikan teguran tertulis mengenai hal tersebut sebanyak 3 (tiga) kali kepada PENYEWA namun PENYEWA tetap tidak memberitahukan kepada PEMILIK perihal penggunaan Ruangan selain dari yang dimaksud dalam Pasal 7.1 di atas, maka PEMILIK berhak dan dengan ini diberi kuasa oleh PENYEWA untuk menutup Ruangan atau mematikan aliran listrik dan memutuskan sewa menyewa, sampai PENYEWA menyatakan secara tertulis kepada PEMILIK bahwa Ruangan akan dipergunakan untuk jenis usaha yang telah ditetapkan dalam sewa menyewa ini dan selama penutupan Ruangan tersebut PENYEWA tetap berkewajiban membayar semua Uang Sewa, <em>service charge</em>, keamanan, biaya listrik dan biaya lainnya sesuai dengan apa yang ditetapkan dalam Perjanjian ini. </p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 8</strong>
    <br />
    <strong>F A S I L I T A S</strong>
    </h3>
    <p style="text-align: justify;">8.1 PEMILIK setuju untuk menyediakan fasilitas sebagai berikut:</p>
    <ol style="list-style-type: lower-alpha;">
    <li style="text-align: justify;">
        <strong>ALIRAN LISTRIK</strong>
        <br />Ruangan akan dilengkapi aliran listrik dengan daya <strong>..........</strong> dengan menggunakan tarif yang berlaku di Gedung sesuai dengan pemakaian listrik PENYEWA yang tertera dalam meteran pencatat listrik. Bilamana PENYEWA ingin menambah daya listrik tersebut, maka PENYEWA wajib membayar biaya penambahan daya listrik kepada PEMILIK dalam jumlah yang sewaktu-waktu ditetapkan oleh PEMILIK.
    </li>
    <li style="text-align: justify;">
        <strong>AIR CONDITIONING</strong>
        <br />Ruangan juga akan dihubungkan dengan <em>Air Conditioning</em> sentral <em>ducting</em>, dan <em>Air Conditioning</em> bekerja selama 12 (dua belas) jam kerja yang ditetapkan PEMILIK, terkecuali ruang yang memperoleh izin khusus dari PEMILIK dapat berlaku <em>Air Conditioning</em> melebihi 12 (dua belas) jam kerja. Biaya operasional dari <em>sentral ducting</em> maupun tindakan perbaikan apabila <em>sentral ducting</em> mengalami kerusakan menjadi tanggung jawab PEMILIK.
    </li>
    <li style="text-align: justify;">
        <strong>TELEPON</strong>
        <br />Penyewa membayarkan Deposit telepon sebesar <strong>Rp {{ number_format($dataSecDepTelp,0,'','.') }},- ({{ empty($dataSecDepTelpChar) ? "Nol" : $dataSecDepTelpChar }} Rupiah)</strong> dimana biaya tersebut akan diperhitungkan kembali pada akhir masa sewa. Pemasangan tersebut di luar biaya penarikan kabel.
    </li>
    <li style="text-align: justify;">
        <strong>AIR</strong>
        <br />Air disediakan oleh <strong>PEMILIK</strong> pada tempat-tempat tertentu di dalam Gedung dan dapat dipergunakan oleh PENYEWA bersama-sama dengan para penyewa lainnya, kecuali untuk penyewa yang mengusahakan restaurant atau salon kecantikan.
    </li>
    <li style="text-align: justify;">
        <strong>KEBERSIHAN</strong>
        <br />PEMILIK setuju memelihara kebersihan ditempat-tempat umum Gedung antara lain, pintu masuk dan jalan-jalan menuju Ruangan, <em>lift</em>, lorong, tangga serta toilet, bagian luar Gedung, termasuk teras, pekarangan muka dan dinding luar, saluran air, halaman parkir mobil, serta tempat pembuangan sampah.
    </li>
    <li style="text-align: justify;">
        <strong>PARKIR</strong>
        <br />PEMILIK menyediakan tempat parkir dan semua kendaraan harus diparkir ditempat parkir yang disediakan sesuai dengan peraturan tentang parkir yang ditentukan <strong>PEMILIK</strong>.
    </li>
    <li style="text-align: justify;">
        <strong>KEAMANAN</strong>
        <br />PEMILIK setuju mengadakan suatu tim keamanan selama 24 (dua puluh empat) jam setiap hari untuk menjaga keamanan di dalam dan di halaman Gedung termasuk menjaga keamanan terhadap aktivitas bongkar muat ( <em>loading</em>) yang dilakukan PENYEWA. PEMILIK tidak bertanggung jawab atas keamanan di dalam Ruangan PENYEWA selama dalam waktu operasional Ruangan.
    </li>
    <li style="text-align: justify;">
        <strong>PERLINDUNGAN TERHADAP KEBAKARAN</strong>
        <br />PEMILIK setuju untuk melengkapi Gedung, di tempat umum menyediakan alat-alat pemadam kebakaran yang disyaratkan menurut peraturan perundang-undangan yang berlaku.
    </li>
    <li style="text-align: justify;">
        <strong>SOUND SYSTEM, LIFT, ESCALATOR, GENERATOR</strong>
        <br />PEMILIK juga menyediakan <em>Sound System</em>, <em>Lift</em>, <em>Escalator</em>, di tempat-tempat umum dan di dalam Gedung serta generator listrik untuk (sebagian) penerangan ditempat umum apabila terjadi pemadaman aliran listrik Perusahaan Listrik Negara, satu dan lain menurut peraturan yang berlaku.
    </li>
    </ol>
    <p style="text-align: justify;">8.2 Fasilitas tersebut dalam ayat 8.1. sub-sub huruf a, b, d, e, f dan sub i Pasal ini, tersedia bagi PENYEWA mulai hari Senin sampai dengan hari Minggu, sejak pukul 10.00 (sepuluh) Waktu Indonesia Barat sampai dengan selambat-lambatnya pukul 22.00 (dua puluh dua) Waktu Indonesia Barat (selanjutnya akan disebut <strong>&ldquo;Waktu Usaha&rdquo;</strong>). </p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 9</strong>
    <br />
    <strong>UANG JAMINAN, SERVICE CHARGE DAN BIAYA TAMBAHAN</strong>
    </h3>
    <p style="text-align: justify;">9.1 Sehubungan dengan penyediaan fasilitas yang dimaksudkan dalam Pasal 8.1. sub-sub huruf b, d, e, g, h dan sub i Perjanjian ini, sebagaimana telah disepakati sebelumnya dalam Surat Konfirmasi atau Ketentuan-Ketentuan Pokok Sewa bahwa selama Jangka Waktu Sewa PENYEWA setuju dan wajib membayar kepada PEMILIK <em>Service Charge</em> sebesar : </p>
    <table style="width: 100%; border-collapse: collapse;" border="0">
    <tbody>
        <?php $totalPriceSC = 0; ?>
        @foreach($dataLotPriceDetails as $key => $data)
        <tr>
        @if($data->IS_GENERATE_MANUAL == 1)
        <td style="width: 25.735%;">{{ $masaSewa }}</td>
        @else
            @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
            <td style="width: 25.735%;">Tahun {{$data->PSM_TRANS_PRICE_YEAR}} (< 1 Bulan)</td>
            @else
            <td style="width: 25.735%;">Tahun {{$data->PSM_TRANS_PRICE_YEAR}} ({{$arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]}} Bulan)</td>
            @endif
        @endif
        <td style="width: 2.1728%;">:</td>
        @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
        <td style="width: 72.0921%;">Rp. {{number_format($data->PSM_TRANS_PRICE_SC_NUM,0,'','.')}},-/m²/(< 1 bulan) (belum termasuk PPN)</td>
        @else
        <td style="width: 72.0921%;">Rp. {{number_format($data->PSM_TRANS_PRICE_SC_NUM,0,'','.')}},-/m²/bulan (belum termasuk PPN)</td>
        @endif
        </tr>
        <tr>
        <td style="width: 25.735%;"></td>
        <td style="width: 2.1728%;"></td>
        <td style="width: 72.0921%;">
            @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
            <?php $subTotalSC = (($data->PSM_TRANS_PRICE_SC_NUM) * $dataLotSqmSC) * 1; ?>
            Rp. {{number_format($data->PSM_TRANS_PRICE_SC_NUM,0,'','.')}},- x {{number_format($dataLotSqmSC,2,',','.')}} m² x (< 1 bulan) = Rp. {{ number_format($subTotalSC,0,'','.') }},- (belum termasuk PPN)
            @else
            <?php $subTotalSC = (($data->PSM_TRANS_PRICE_SC_NUM) * $dataLotSqmSC) * $arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]; ?>
            Rp. {{number_format($data->PSM_TRANS_PRICE_SC_NUM,0,'','.')}},- x {{number_format($dataLotSqmSC,2,',','.')}} m² x {{$arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]}} bulan = Rp. {{ number_format($subTotalSC,0,'','.') }},- (belum termasuk PPN)
            @endif
        </td>
        </tr>
        <?php $totalPriceSC += $subTotalSC; ?>
        @endforeach
        <tr>
        <td style="width: 25.735%;">Total</td>
        <td style="width: 2.1728%;">:</td>
        <td style="width: 72.0921%;">Rp. {{number_format($totalPriceSC,0,'','.')}},- (belum termasuk PPN)</td>
        </tr>
    </tbody>
    </table>
    <p style="text-align: justify;">wajib dibayar selambat-lambatnya setiap tanggal 10 (sepuluh) setiap bulan berikutnya dan untuk pertama kali <em>Service Charge</em> wajib dibayar oleh PENYEWA selambatnya pada tanggal sesuai jadwal pembayaran terlampir. PEMILIK sewaktu-waktu dapat meninjau kembali tarif <em>Service Charge</em> tersebut. PEMILIK akan memberitahukan secara tertulis kepada PENYEWA bilamana terjadi kenaikan tarif <em>Service Charge</em>, dengan menyebutkan jumlah <em>Service Charge</em> yang akan berlaku, sedikitnya 1 (satu) bulan sebelum berlakunya kenaikan itu. </p>
    <p style="text-align: justify;">9.2 Dalam <em>Service Charge</em> tersebut di atas tidak termasuk biaya penyambungan dan pemakaian pesawat telepon, listrik, air, biaya parkir (terkecuali untuk kendaraan yang diberikan fasilitas bebas bea parkir oleh PEMILIK sebagaimana yang tercantum dalam Pasal 8.1 huruf f Perjanjian ini) dan PPN. </p>
    <p style="text-align: justify;">9.3 PENYEWA wajib membayar Uang Jaminan kepada PEMILIK sebesar 3 (tiga) kali jumlah <em>Service Charge</em> dan <em>Promotion Levy</em> dengan nilai <strong>Rp {{ number_format($dataSecDepSC,0,'','.') }},- ({{ empty($dataSecDepSCChar) ? "Nol" : $dataSecDepSCChar }} Rupiah)</strong> sebagaimana yang tercantum dalam jadwal pembayaran terlampir dan Uang Jaminan tersebut wajib dibayar oleh PENYEWA. </p>
    <p style="text-align: justify;">9.4 PEMILIK berhak untuk mempergunakan Uang Jaminan tersebut guna pembayaran tunggakan <em>Service Charge</em> dan jumlah uang lainnya yang terhutang oleh PENYEWA sewaktu-waktu (bila ada) dan setelah diperhitungkan terlebih dahulu dengan tunggakan pembayaran PENYEWA tersebut (bila ada), maka seluruh nilai Uang Jaminan setelah diperhitungkan dengan tunggakan apabila ada akan dikembalikan secara penuh kepada PENYEWA oleh PEMILIK selambat-lambatnya 30 (tiga puluh) hari setelah berakhirnya Jangka Waktu Sewa. </p>
    <p style="text-align: justify;">9.5 Biaya pemakaian listrik, air dipungut terhitung mulai tanggal dimulainya Jangka Waktu Sewa dan biaya tersebut adalah sebesar banyaknya pemakaian aliran listrik dan air menurut meteran yang tercatat yang dipasang dalam Ruangan dan wajib dilunasi oleh PENYEWA selambat-lambatnya pada tanggal 10 setiap bulan, untuk biaya pemakaian pesawat telepon, PENYEWA wajib membayar setiap bulan kepada PT TELKOM / Bank yang ditunjuk oleh PT TELKOM.</p>
    <p style="text-align: justify;">9.6 Keterlambatan PENYEWA menempati Ruangan ataupun diakhirinya Perjanjian ini lebih awal atas kehendak PENYEWA atau karena sebab lain tidak membebaskan PENYEWA dari kewajibannya untuk membayar Uang Sewa, Service Charge dan biaya lain yang wajib dibayar oleh PENYEWA berdasarkan Perjanjian ini sesuai dengan penggunaan Ruangan dan pemakaian fasilitas selama periode sewa yang telah dijalani PENYEWA.</p>
    <p style="text-align: justify;">9.7 PENYEWA harus membayar biaya tambahan kepada PEMILIK apabila PENYEWA lalai membersihkan Ruangan sehingga PEMILIK harus membersihkan Ruangan.</p>
    <p style="text-align: justify;">9.8 PEMILIK berhak menentukan sendiri besarnya biaya tambahan sebagaimana dimaksud ayat 9.8 Pasal ini, sesuai nilai yang berlaku di Gedung.</p>
    <p style="text-align: justify;">9.9 Biaya tambahan sebagaimana dimaksud ayat 9.8 Pasal ini wajib dibayar segera dan sekaligus oleh PENYEWA ketika ditagih oleh PEMILIK.</p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 10</strong>
    <br />
    <strong>PAJAK &ndash; PAJAK</strong>
    </h3>
    <p style="text-align: justify;">10.1 Semua pajak dibebankan sesuai peraturan yang berlaku dari Dirjen Pajak dan/atau instansi yang berwenang lainnya, dan pungutan lain seperti Iuran yang berkenaan dengan usaha PENYEWA, seluruhnya harus ditanggung dan dibayar oleh PENYEWA tepat pada waktunya.</p>
    <p style="text-align: justify;">10.2 Para Pihak sepakat bahwa segala pajak termasuk namun tidak terbatas pada PPN dan PPh atas pembayaran yang telah disetorkan oleh PENYEWA kepada PEMILIK tidak dapat dikembalikan karena alasan apapun termasuk namun tidak terbatas pada saat PENYEWA melakukan pemutusan/penghentian/pengakhiran sewa sebelum berakhirnya Jangka Waktu Sewa dalam Perjanjian ini.</p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 11</strong>
    <br />
    <strong>KEWAJIBAN-KEWAJIBAN DAN TANGGUNG JAWAB PENYEWA</strong>
    </h3>
    <p style="text-align: justify;">Selama Perjanjian ini berlangsung, maka kewajiban yang harus dipenuhi PENYEWA adalah:</p>
    <p style="text-align: justify;">
    <strong>11.1 PEMBAYARAN UANG SEWA, SERVICE CHARGE DAN BIAYA-BIAYA LAIN:</strong>
    </p>
    <ol style="list-style-type: lower-alpha; text-align: justify;">
    <li>PENYEWA wajib membayar Uang Sewa, <em>Service Charge</em> dan biaya pemakaian listrik, telepon, air serta biaya lainnya yang menjadi kewajibannya berdasarkan Perjanjian ini tepat pada waktunya. </li>
    <li>Jika PENYEWA terlambat membayar jumlah tersebut di atas, PENYEWA dikenakan denda sebesar 1&permil; (satu per mill) dari jumlah yang tertunggak untuk setiap hari keterlambatan terhitung sejak tanggal pembayaran itu jatuh waktu tempo sampai dengan tanggal seluruh pembayaran yang tertunggak tersebut dilunasi sebagaimana mestinya.</li>
    <li>Jika keterlambatan pembayaran jumlah tersebut dalam sub (a) di atas, melampaui 30 (tiga puluh) hari sejak saat pembayaran itu, PEMILIK berhak memutuskan aliran listrik dalam Ruangan. PEMILIK hanya setuju untuk menghubungkan kembali aliran listrik setelah seluruh jumlah yang terhutang telah dilunasi sebagaimana mestinya. Dalam hal demikian PENYEWA wajib membayar biaya pemasangan kembali aliran listrik sesuai peraturan yang berlaku.</li>
    </ol>
    <p style="text-align: justify;">
    <strong>11.2 PENGGUNAAN RUANGAN</strong>
    </p>
    <ol style="list-style-type: lower-alpha;">
    <li style="text-align: justify;">PENYEWA wajib mempergunakan Ruangan sebagai tempat kegiatan usahanya tersebut dalam Pasal 7.1 Perjanjian ini dengan memperhatikan Waktu Usaha, yaitu PENYEWA wajib melaksanakan kegiatan usaha selama Waktu Usaha, kecuali ada pemberitahuan tertulis dari PEMILIK. Apabila PENYEWA bermaksud melaksanakan kegiatan usahanya di luar Waktu Usaha atau menutup Ruangan selama Waktu Usaha, maka hal tersebut dapat dilaksanakan setelah memperoleh persetujuan tertulis terlebih dahulu dari <strong>PEMILIK</strong>
    </li>
    <li style="text-align: justify;">PENYEWA berjanji bahwa Ruangan baik sebagian maupun seluruhnya, tidak dipergunakan untuk melakukan perbuatan yang bertentangan dengan hukum, termasuk tapi tidak terbatas pada tempat judi atau, tempat perbuatan maksiat.</li>
    <li style="text-align: justify;">PENYEWA berjanji akan beritikad baik dan wajib memperhatikan dan mentaati perundang-undangan yang berlaku yang berhubungan dengan kegiatan usaha PENYEWA dan pemakaian Ruangan, pelanggaran terhadap peraturan-peraturan yang dimaksud adalah sepenuhnya menjadi tanggung jawab PENYEWA sendiri serta PENYEWA membebaskan PEMILIK dari segala tuntutan dan/atau gugatan dan/atau dari pihak lain berkenaan dengan kegiatan usaha dan/atau penggunaan Ruangan tersebut.</li>
    <li style="text-align: justify;">PENYEWA berjanji untuk mencegah agar dalam Ruangan tidak terjadi perbuatan yang dapat menimbulkan kerusakan atau keributan atau gangguan pada PEMILIK atau para penyewa lainnya dalam Gedung.</li>
    <li style="text-align: justify;">PENYEWA berjanji untuk tidak meyimpan, menimbun, mengizinkan, atau membiarkan disimpan bahan-bahan yang mudah terbakar, bahan peledak, senjata api, bensin, gas LPG atau bahan bakar lainnya atau barang-barang berbahaya dalam Ruangan kecuali dengan izin tertulis dari PEMILIK.</li>
    <li style="text-align: justify;">Pemasangan, penambahan, dan perubahan instalasi listrik harus seizin PEMILIK dan dilakukan oleh PEMILIK atas beban biaya PENYEWA.</li>
    <li style="text-align: justify;">PENYEWA dilarang meletakan barang melebihi beban sebesar 400 kilogram persatu meter persegi dalam Ruangan.</li>
    <li style="text-align: justify;">PENYEWA dilarang menambah/mengubah/membongkar dinding partisi Ruangan tanpa izin tertulis terlebih dahulu dari PEMILIK. Setiap penambahan/perubahan/pembongkaran yang disetujui oleh PEMILIK dilaksanakan atas beban biaya PENYEWA.</li>
    <li style="text-align: justify;">PENYEWA dilarang menggantungkan barang dagangannya sedemikian rupa hingga dapat merusak dinding partisi.</li>
    <li style="text-align: justify;">PENYEWA dilarang mengubah instalasi <em>plumbing</em> dan <em>ducting Air Conditioner</em> berikut perlengkapannya yang berada di dalam Ruangan. </li>
    <li style="text-align: justify;">PENYEWA dilarang untuk mengatur sendiri &ldquo; <em>grill louvres</em>&rdquo; dari <em>Air Conditioner Diffuser</em> atau memainkan &ldquo; <em>lever</em>&rdquo; dari <em>drill louveres</em> tersebut. PENYEWA wajib menjaga agar <em>Air Conditioner Diffuser</em> dalam Ruangan tidak terhalang oleh benda-benda dalam Ruangan. </li>
    <li style="text-align: justify;">PENYEWA wajib segera melaporkan kepada PEMILIK jika terjadi kerusakan pada <em>Air Conditioner Diffuser</em>. </li>
    <li style="text-align: justify;">Segala pekerjaan reparasi dalam Ruangan, saluran listrik serta Air Conditioner hanya boleh dilakukan oleh PEMILIK atau wakilnya, atau pihak yang ditunjuk PEMILIK.</li>
    <li style="text-align: justify;">PENYEWA hanya dapat memasang <em>intercom</em> dengan izin tertulis lebih dahulu dari PEMILIK dengan persetujuan Perusahaan Umum Telekomunikasi atas biaya PENYEWA. </li>
    <li style="text-align: justify;">Pemindahan <em>telephone</em> dan <em>telex</em> hanya dapat dilaksanakan oleh PEMILIK dengan persetujuan Perusahaan Umum Telekomunikasi atas biaya PENYEWA. </li>
    <li style="text-align: justify;">PENYEWA wajib memelihara semua bagian Ruangan termasuk semua pintu dan perkakas serta barang-barang yang melekat pada Ruangan kepunyaan PEMILIK dalam keadaan baik dan bersih, serta menggantikan bagian yang rusak atau mengadakan perbaikan, semua itu atas biaya PENYEWA, jika hilangnya atau rusaknya tersebut karena kelalaian PENYEWA, pegawai, wakil, tamu atau kontraktor PENYEWA.</li>
    <li style="text-align: justify;">PENYEWA dilarang meletakkan atau meninggalkan pada lorong, gang atau jalan-jalan manapun dari Gedung yang dipergunakan secara bersama dengan PENYEWA lainnya atau bagian manapun dari Gedung tersebut seperti; perkakas atau inventaris, sampah atau barang apapun yang menghambat atau merintangi pemakaian bersama dari fasilitas tersebut.</li>
    <li style="text-align: justify;">Tidak mempergunakan Ruangan baik sebagian maupun seluruhnya untuk laboratorium atau bengkel, kecuali telah disetujui tertulis lebih dahulu oleh PEMILIK.</li>
    <li style="text-align: justify;">PENYEWA dilarang mempergunakan Ruangan sebagai tempat untuk menginap pada malam hari atau setelah ditutupnya Ruangan, kecuali telah disetujui lebih dahulu oleh PEMILIK.</li>
    <li style="text-align: justify;">PENYEWA harus mengizinkan PEMILIK atau wakilnya, tanpa perlu memberitahukan PENYEWA lebih dahulu untuk setiap saat masuk, dan melihat keadaan Ruangan dan berhak sewaktu-waktu memeriksa pemakaian listrik atau/air pada Ruangan.</li>
    <li style="text-align: justify;">PENYEWA dengan ini membebaskan PEMILIK dari segala tuntutan dan/atau gugatan dan/atau ganti rugi atas kehilangan, kerusakan atau kerugian atas barang milik PENYEWA yang karena kebakaran, banjir, gempa bumi, angin topan, huru-hara, pencurian atau sebab-sebab lainnya. Oleh karena itu PENYEWA setuju dan berjanji untuk mengasuransikan sendiri seluruh barang milik PENYEWA yang berada di dalam Ruangan.</li>
    </ol>
    <p style="text-align: justify;">
    <strong>11.3 KEAMANAN DAN KETERTIBAN</strong>
    </p>
    <ol style="list-style-type: lower-alpha;">
    <li style="text-align: justify;">PENYEWA dilarang memasuki Gedung sebelum dan sesudah Waktu Usaha, tanpa seizin management dan petugas keamanan Gedung.</li>
    <li style="text-align: justify;">PENYEWA dilarang secara sendiri-sendiri atau berkelompok berada didalam Ruangan melebihi batas Waktu Usaha di dalam Gedung, kecuali telah mendapat persetujuan dari PEMILIK.</li>
    <li style="text-align: justify;">PENYEWA diizinkan melakukan bongkar muat barang-barang milik PENYEWA dengan mengeluarkan atau memasukan benda/barang keluar/kedalam Ruangan 1 (satu) jam sebelum dan sesudah Waktu Usaha.</li>
    <li style="text-align: justify;">PENYEWA dilarang membongkar pasang/muat barang-barang dilorong-lorong (coridor) dan jalan-jalan serta tempat umum lainnya pada Gedung tersebut.</li>
    <li style="text-align: justify;">PENYEWA dilarang memasang spanduk dan alat promosi lainnya di luar Ruangan tanpa izin tertulis terlebih dahulu dari PEMILIK, serta apabila telah mendapat izin tertulis resmi dari PEMILIK, PENYEWA bertanggung jawab terhadap isi materi alat promosi tersebut dan membebaskan PEMILIK dari segala gugatan dan/atau tuntutan dari pihak lain berkaitan dengan isi materi alat promosi tersebut.</li>
    <li style="text-align: justify;">PENYEWA diharuskan mempergunakan lori pengangkut barang beroda empat yang rodanya terbuat dari karet.</li>
    <li style="text-align: justify;">PENYEWA dilarang melakukan kegiatan dekorasi dilorong-lorong ( <em>coridor</em>) ataupun ditempat-tempat lain yang bukan merupakan bagian Ruangan yang disewa. </li>
    <li style="text-align: justify;">PENYEWA dilarang melakukan kegiatan service besar/kecil dan mencuci kendaraannya di lokasi parkir.</li>
    <li style="text-align: justify;">PENYEWA dilarang meninggalkan (menginapkan) kendaraan di lokasi parkir, kecuali atas persetujuan tertulis dari PEMILIK terlebih dahulu.</li>
    <li style="text-align: justify;">PENYEWA dilarang memonopoli lift barang (cargo lift) untuk kepentingan sendiri.</li>
    <li style="text-align: justify;">PENYEWA dilarang berjualan dihalaman parkir dengan memakai mobil, membagikan selebaran-selebaran tanpa persetujuan tertulis dari PEMILIK.</li>
    </ol>
    <p style="text-align: justify;">
    <strong>11.4 KESELAMATAN GEDUNG</strong>
    </p>
    <ol style="list-style-type: lower-alpha;">
    <li style="text-align: justify;">PENYEWA dilarang menumpuk/menyusun barang dagangannya hingga menutupi atau dapat menghalangi penggunaan peralatan pencegahan dan penanggulangan kebakaran.</li>
    <li style="text-align: justify;">PENYEWA dilarang menghubungkan beberapa peralatan listrik pada satu titik stop kontak.</li>
    <li style="text-align: justify;">PENYEWA harus memadamkan lampu penerangan dan peralatan listrik yang dipergunakan dalam ruangan pada saat mengakhiri kegiatannya dan menyalakan 1 (satu) titik lampu yang menyala selama 24 (dua puluh empat) jam.</li>
    <li style="text-align: justify;">PENYEWA tidak diperbolehkan memasak atau memanaskan makanan/minuman di dalam Ruangan kecuali restaurant dan lain sebagainya, kookplaat.</li>
    <li style="text-align: justify;">PENYEWA wajib menyediakan Alat Pemadam Kebakaran (APK) portable sesuai dengan ketentuan ukuran standard Fire Safety dalam Ruangan.</li>
    <li style="text-align: justify;">PEMILIK sewaktu-waktu berhak memeriksa peralatan pencegahan dan penanggulangan kebakaran di dalam Ruangan tanpa perlu memberitahukan PENYEWA terlebih dahulu selama Waktu Usaha.</li>
    <li style="text-align: justify;">PENYEWA dilarang menaruh/menutupi pintu tangga darurat dan hydrant box dengan barang atau benda apapun juga.</li>
    <li style="text-align: justify;">PENYEWA dilarang mengubah instalasi <em>Sprinkle</em>, <em>Heat Detector</em> dan instalasi listrik, air berikut perlengkapannya yang berada di dalam Ruangan. </li>
    <li style="text-align: justify;">Penyewa wajib menggunakan <em>Rolling Door Full Peforated</em>. </li>
    <li style="text-align: justify;">Penyewa tidak diperbolehkan memasak atau memanaskan makanan/minuman di dalam Ruangan 30 (tiga puluh) menit sebelum penutupan operasional Mall.</li>
    <li style="text-align: justify;">Penyewa wajib mempunyai dan menggunakan <em>Emergency Lamp</em>. </li>
    </ol>
    <p style="text-align: justify;">
    <strong>11.5 KEBERSIHAN</strong>
    </p>
    <ol style="list-style-type: lower-alpha;">
    <li style="text-align: justify;">PENYEWA harus menyediakan tempat sampah di Ruangan.</li>
    <li style="text-align: justify;">PENYEWA harus menampung/memasukan sampah/kotoran Ruangan ke dalam container bertutup yang dilapisi dengan kantong plastik dengan memisahkan antara sampah basah dan kering, sampah tidak boleh ada di dalam ruangan saat operasional Mall tutup, Penyewa wajib membuang sampah/kotoran tersebut sebelum tutup operasional ke TPS ( <em>garbage</em>). </li>
    <li style="text-align: justify;">PENYEWA dilarang menyapu/mengeluarkan sampah/kotoran Ruangan ke lorong ( <em>coridor</em>) atau jalanan atau tempat umum dalam Gedung, harus menggunakan alat penyedot listrik. </li>
    <li style="text-align: justify;">PENYEWA dilarang membuang sisa makanan atau minuman kedalam bak sampah di lorong (coridor) dan pot tanaman/bunga dalam Gedung.</li>
    <li style="text-align: justify;">PENYEWA hanya dibenarkan membersihkan peralatan makanan/minumannya di wasbak yang telah disediakan oleh PEMILIK.</li>
    <li style="text-align: justify;">PENYEWA wajib memperbaiki dan membersihkan Gedung apabila Gedung mengalami kerusakan/kotor yang disebabkan pengangkutan barang milik PENYEWA dari dan ke Ruangan.</li>
    <li style="text-align: justify;">PENYEWA wajib melakukan pekerjaan <em>pest and rodent control</em> yang meliputi <em>spraying</em>, <em>gel baiting</em>, <em>cold fog</em> dan <em>glue trap</em> sekurang-kurangnya dua kali dalam satu bulan. </li>
    <li style="text-align: justify;">Apabila PENYEWA tidak melakukan pekerjaan <em>pest and rodent control</em> sebagaimana ketentuan diatas sehingga PEMILIK harus menunjuk pihak ketiga untuk mengerjakan pekerjaan tersebut yang wajib dilakukan satu kali dalam seminggu, maka dengan ini PENYEWA wajib membayar biaya tambahan kepada pihak ketiga yang ditunjuk oleh PEMILIK untuk melakukan pekerjaan tersebut paling lambat satu bulan setelah tagihan diterima oleh PENYEWA. </li>
    <li style="text-align: justify;">Segala kerusakan yang diakibatkan oleh hama di area PENYEWA bukan menjadi tanggung jawab PEMILIK.</li>
    <li style="text-align: justify;">Ketentuan dalam sub huruf g, h, dan i ayat ini hanya berlaku dan mengikat penyewa yang menjalankan usaha <em>restaurant</em>, <em>food and beverages</em> atau <em>supermarket</em>/pasar swalayan/hypermart. </li>
    </ol>
    <p style="text-align: justify;">
    <strong>11.6 TEKNIK</strong>
    </p>
    <ol style="list-style-type: lower-alpha;">
    <li style="text-align: justify;">PENYEWA dilarang merusak dan/atau mengubah MCB Box, Panel Distributor Listrik dan telepon serta instalasi lainnya.</li>
    <li style="text-align: justify;">PENYEWA dilarang mengubah dan mengganti peralatan dan perlengkapan sound system yang berada di langit-langit, lorong-lorong (coridor) dan tempat-tempat lainnya.</li>
    <li style="text-align: justify;">PENYEWA dilarang menggunakan stop kontak yang berada di luar Ruangan dan untuk kebutuhan dekorasi dan mencoba/menguji barang dagangannya.</li>
    <li style="text-align: justify;">PENYEWA hanya dibenarkan mengangkut barang/benda yang berbobot max 500 Kg (lima ratus kilogram) dengan lift barang (cargo lift).</li>
    <li style="text-align: justify;">PENYEWA dilarang mengangkut barang melalui <em>escalator</em>. </li>
    <li style="text-align: justify;">PENYEWA dilarang menyambung instalasi listrik dari luar Ruangan Penyewa.</li>
    </ol>
    <p style="text-align: justify;">
    <strong>11.7 PERATURAN TATA TERTIB</strong>
    </p>
    <p style="text-align: justify;">PENYEWA berjanji untuk beritikad baik dan mentaati semua peraturan yang sekarang berlaku maupun yang akan ditetapkan kemudian oleh PEMILIK mengenai tata tertib lingkungan Gedung.</p>
    <p style="text-align: justify;">
    <strong>11.8 PENYEWA BERTANGGUNG JAWAB ATAS KERUGIAN PEMILIK</strong>
    </p>
    <p style="text-align: justify;">PENYEWA bertanggung jawab penuh atas kerugian yang diderita oleh <strong>PEMILIK</strong> yang terbukti disebabkan karena kesalahan/kelalaian PENYEWA dalam melangsungkan kegiatan usahanya dan/atau penggunaan Ruangan dan/atau melaksanakan ketentuan Perjanjian. </p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 12</strong>
    <br />
    <strong>KEWAJIBAN DAN TANGGUNG JAWAB PEMILIK</strong>
    </h3>
    <p style="text-align: justify;">Sehubungan dengan Perjanjian ini, PEMILIK berkewajiban selama Jangka Waktu Sewa:</p>
    <p style="text-align: justify;">12.1. Menjaga dan memelihara semua fasilitas yang disediakan untuk PENYEWA seperti yang tercantum dalam Perjanjian agar senantiasa dalam keadaan bersih, terpelihara baik dan berjalan dengan baik.</p>
    <p style="text-align: justify;">12.2. Menjaga dan memelihara semua fasilitas yang dipergunakan bersama dengan para PENYEWA lain. Seperti Ruangan pintu masuk, <em>Lift</em>, <em>Escalator</em>, gang-gang/lorong-lorong ( <em>coridor</em>) serta kamar kecil ( <em>toilet</em>) agar tetap dalam keadaan baik dan bersih. </p>
    <p style="text-align: justify;">12.3. Apabila segala fasilitas yang disediakan oleh PEMILIK berdasarkan Pasal ini mengalami kerusakan, maka PEMILIK berkewajiban untuk memperbaikinya selambat-lambatnya 1 (satu) hari setelah PENYEWA memberitahukan PEMILIK perihal kerusakan yang terjadi, dengan biaya perbaikan menjadi tanggung jawab PEMILIK.</p>
    <p style="text-align: justify;">12.4. PEMILIK wajib mengasuransikan Gedung tersebut (tidak termasuk barang-barang PENYEWA yang berada dalam Ruangan) terhadap bahaya kebakaran, sambaran petir, korsleting, kerusuhan dan pemogokan, banjir dan kerusakan akibat air.</p>
    <p style="text-align: justify;">12.5. Dengan memperhatikan ketentuan dalam Pasal 8 Perjanjian ini, PEMILIK wajib menjalankan <em>lift</em>, <em>Escalator</em>, <em>Air Condition central</em> selama Waktu Usaha, kecuali untuk waktu yang dibutuhkan guna memperbaiki kerusakan-kerusakan atau <em>service Lift</em>, <em>Escalator</em>, <em>Air Condition central</em> tersebut, untuk memastikan tetap dalam keadaan berfungsi. </p>
    <p style="text-align: justify;">12.6. PEMILIK tidak bertanggung jawab atas akibat-akibat yang timbul karena kerusakan <em>Lift</em>, <em>Escalator</em>, <em>Air Condition central</em> dan lain-lain atau kecelakaan yang diakibatkan atas kelalaian PENYEWA atau karena sebab-sebab di luar kemampuan PEMILIK. </p>
    <p style="text-align: justify;">12.7. PEMILIK menjamin, selama PENYEWA memenuhi kewajiban atau ketentuan dalam Perjanjian ini, PENYEWA dapat mempergunakan Ruangan dengan tenteram dalam arti bahwa PENYEWA tidak akan mendapatkan klaim, keberatan, tuntutan atau gangguan maupun gugatan dalam bentuk apapun dan dalam jumlah berapapun dari pihak ketiga yang menyatakan mempunyai hak untuk menempati Ruangan.</p>
    <p style="text-align: justify;">12.8 Apabila dianggap perlu, PEMILIK berkewajiban mengeluarkan Pengumuman, Surat Edaran, Perintah, Peringatan dan/ atau sejenisnya secara tertulis kepada PENYEWA dalam rangka menjalankan fungsi dan tanggungjawabnya untuk menjaga ketertiban, kenyamanan, keamanan dan keselamatan berkenaan dengan kegiatan usaha dan/atau penggunaan ruangan PENYEWA dengan pihak ketiga lainnya.</p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 13</strong>
    <br />
    <strong>PEMINDAHAN HAK SEWA</strong>
    </h3>
    <p style="text-align: justify;">13.1 PENYEWA tidak berhak untuk memindahkan hak sewanya atau hal lainnya baik sebagian maupun seluruhnya tanpa adanya persetujuan tertulis terlebih dahulu dari PEMILIK.</p>
    <p style="text-align: justify;">13.2 Tanpa persetujuan tersebut segala pemindahan hak sewa atau penyerahan hak lainnya berdasarkan Perjanjian ini kepada pihak lain adalah tidak sah dan tidak dapat mengikat PEMILIK, PEMILIK dalam hal demikian berhak untuk segera mengakhiri Perjanjian, dan PENYEWA wajib mengosongkan Ruangan dan mengembalikan kepada PEMILIK paling lambat 7 (hari) kalender atas permintaan pertama PEMILIK. Jika PENYEWA tidak mengosongkan Ruangan, maka PENYEWA dengan ini, yang berlaku pada waktunya memberi kuasa kepada PEMILIK untuk mengosongkan dengan cara mengeluarkan semua barang-barang serta peralatan PENYEWA, bila perlu dengan bantuan Pihak yang Berwajib. PEMILIK tidak berkewajiban untuk memberi pertanggungjawaban atas pelaksanaan kuasa tersebut.</p>
    <p style="text-align: justify;">13.3 Apabila pemindahan hak sewa ini kepada pihak lain disetujui oleh PEMILIK, PENYEWA dikenakan biaya administrasi sebesar 20% (dua puluh persen) dari seluruh harga sewa, yang harus dibayar oleh PENYEWA kepada PEMILIK segera atas tagihan pertama PEMILIK. Pihak yang menerima hak sewa tersebut wajib menandatangani surat perjanjian/kesepakatan yang maksud dan isinya ditentukan oleh PEMILIK yang antara lain mewajibkan PENYEWA baru untuk mentaati dan terikat pada semua syarat dan ketentuan dalam Perjanjian ini.</p>
    <p style="text-align: justify;">13.4 Pemindahan hak sewa ini tidak dapat dilakukan sebelum seluruh Uang Sewa dan kewajiban-kewajiban membayar lainnya telah dilunasi oleh PENYEWA. Pemindahan hak sewa hanya berlaku untuk seluruh sisa jangka waktu sewa dan harus meliputi seluruh Ruangan.</p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 14</strong>
    <br />
    <strong>FORCE MAJEURE</strong>
    </h3>
    <p style="text-align: justify;">14.1 Yang dimaksud dengan keadaan memaksa atau Force Majeure adalah peristiwa yang terjadi di luar kekuasaan Para Pihak yang dapat mengakibatkan terhambatnya pelaksanaan kewajiban salah satu Pihak atau Para Pihak, termasuk namun tidak terbatas pada:</p>
    <ol style="list-style-type: lower-alpha; text-align: justify;">
    <li>Gempa bumi, angin topan, tsunami, banjir, letusan gunung berapi, tanah longsor, sambaran petir, kebakaran, ledakan benda angkasa;</li>
    <li>Peperangan, huru hara, terorisme, kerusuhan, pemogokan massal, demonstrasi, pemberontakan, termasuk namun tidak terbatas pada kekacauan sosial lainnya yang nyatanya terjadi, baik di lokasi/sekitar lokasi sewa menyewa ini maupun di lokasi lain namun berdampak pada terhambatnya pelaksanaan kewajiban salah satu Pihak atau Para Pihak.</li>
    </ol>
    <p style="text-align: justify;">14.2 Apabila peristiwa Force Majeure yang terjadi berdampak pada kerusakan Ruangan dan/atau Gedung maka PEMILIK akan memperbaiki Ruangan dan/atau Gedung, sepanjang hal tersebut dapat diperbaiki dan/atau ditutup oleh asuransi, selama dilakukan perbaikan dan PENYEWA tidak dapat menggunakan Ruangan sebagaimana mestinya, maka PENYEWA dibebaskan dari pembayaran Uang Sewa. Apabila kerusakan tersebut tidak dapat diperbaiki dan/atau ditutup oleh suatu asuransi, PENYEWA mempunyai hak opsi untuk menghentikan Perjanjian ini. PEMILIK berkewajiban mengembalikan Uang Sewa yang telah dibayarkan dimuka untuk Masa Sewa yang belum dijalankan jika peristiwa Force Majeure terjadi.</p>
    <p style="text-align: justify;">14.3 Apabila peristiwa Force Majeure yang terjadi berdampak pada tidak dapat beroperasinya Ruangan dan/atau Gedung untuk sementara waktu maka PENYEWA wajib memelihara seluruh barang-barang termasuk namun tidak terbatas pada barang dagangan milik PENYEWA yang terdapat pada Ruangan dan/atau Gedung yang tidak dapat beroperasi sementara waktu. PEMILIK tidak bertanggung jawab atas akibat-akibat yang timbul pada barang-barang PENYEWA yang terdapat pada Ruangan dan/atau Gedung yang tidak dapat beroperasi sementara waktu.</p>
    <p style="text-align: justify;">14.4 Segala akibat yang ditimbulkan dari terjadinya peristiwa Force Majeure menjadi beban dan tanggung jawab masing-masing Pihak.</p>
    <p style="text-align: justify;">14.5 Para Pihak dibebaskan dari pelaksanaan dan tidak akan ditafsirkan sebagai wanprestasi berkenaan dengan kewajiban berdasarkan Perjanjian ini di mana kegagalan untuk melaksanakan kewajiban tersebut disebabkan oleh Force Majeure dengan ketentuan Pihak yang mengalami Force Majeure telah memberitahukan secara tertulis dan menjelaskan tentang Force Majeure tersebut kepada Pihak lainnya disertai dokumen pendukung terhadap kejadian Force Majeure tersebut selambat-lambatnya dalam jangka waktu 14 (empat belas) hari setelah terjadinya Force Majeure.</p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 15</strong>
    <br />
    <strong>SANKSI, PENGAKHIRAN PERJANJIAN DAN AKIBATNYA</strong>
    </h3>
    <p style="text-align: justify;">15.1 Jika terjadi satu peristiwa tersebut di bawah ini, PEMILIK berhak dengan segera mengakhiri Perjanjian ini dengan cara memberikan pemberitahuan tertulis kepada PENYEWA tentang pengakhiran Perjanjian 30 (tiga puluh) hari kalender sebelum tanggal pengakhiran yang diinginkan PEMILIK.</p>
    <p style="text-align: justify;">15.2 Peristiwa-peristiwa yang dimaksud dalam ayat 15.1. Pasal ini adalah:</p>
    <ol style="list-style-type: lower-alpha; text-align: justify;">
    <li>Jika PENYEWA tidak mentaati dan/atau memenuhi salah satu atau lebih ketentuan-ketentuan yang termaksud dalam Perjanjian, dengan tidak mengurangi ketentuan dalam Pasal 11.1 Perjanjian ini, setelah memperoleh peringatan tertulis 3 (tiga) kali berturut-turut dari PEMILIK dalam jangka waktu 30 (tiga puluh) hari; atau</li>
    <li>Jika PENYEWA tidak mentaati, dan/atau mengisi Ruangan dengan usaha/barang yang tidak sesuai kesepakatan awal, usaha/barang terlarang, atau ditutup atas perintah instansi yang berwenang dan PENYEWA tidak dapat melaksanakan tindakan-tindakan untuk menyelesaikan secara tuntas keadaan tersebut dalam waktu 30 (tiga puluh) hari setelah ada surat teguran atau perintah atau tindakan lainnya oleh pihak yang berwenang, atau.</li>
    <li>Barang-barang PENYEWA disita oleh instansi yang berwenang; atau.</li>
    <li>PENYEWA dinyatakan pailit berdasarkan ketetapan Pengadilan yang berkekuatan tetap.</li>
    <li>PENYEWA tidak membuka atau tidak menggunakan Ruangan selama 30 (tiga puluh) hari kalender berturut-turut, tanpa izin tertulis lebih dahulu dari PEMILIK.</li>
    </ol>
    <p style="text-align: justify;">15.3 Setelah PEMILIK memberitahukan tentang pengakhiran Perjanjian kepada PENYEWA sesuai ayat 15.1. Pasal ini, PEMILIK berhak untuk segera menyewakan kembali Ruangan tersebut kepada pihak lain yang ditunjuk PEMILIK tanpa perlu adanya persetujuan apapun dari PENYEWA.</p>
    <p style="text-align: justify;">15.4 Jika Perjanjian diakhiri sesuai ayat 15.1. Pasal ini, PEMILIK berhak memutuskan aliran listrik dan <em>Air Conditioning</em> dalam Ruangan tersebut. </p>
    <p style="text-align: justify;">15.5 Pemberitahuan mengenai berakhirnya Perjanjian berdasarkan ketentuan dalam Perjanjian ini harus dilakukan oleh PEMILIK kepada PENYEWA dengan surat POS tercatat dan berlaku sejak tanggal tanda terima yang dibubuhkan oleh petugas kantor pos untuk pengiriman surat pemberitahuan kealamat tersebut dalam Pasal 18.4 atau melalui email PEMILIK kepada PENYEWA berlaku sejak tanggal pengiriman email tersebut.</p>
    <p style="text-align: justify;">15.6 Sebagai akibat pengakhiran yang disebabkan oleh peristiwa sebagaimana yang diuraikan dalam Pasal 15 Perjanjian ini, Uang Sewa yang telah dibayarkan dimuka oleh PENYEWA kepada PEMILIK dan yang benar telah diterima oleh PEMILIK untuk sewa yang belum dijalani PENYEWA berikut Uang Jaminan dan Deposit Telepon, akan dikembalikan oleh PEMILIK kepada PENYEWA setelah dikurangi dengan biaya pengakhiran sebesar 20% (dua puluh persen) dari seluruh Uang Sewa sebagaimana disebut dalam Pasal 5 Perjanjian ini berikut denda-denda, biaya penagihan, dan biaya-biaya lain yang terhutang, dan apabila sebagian jumlah Uang Sewa, Uang Jaminan dan Deposit Telepon yang telah dibayarkan dimuka itu oleh PENYEWA dan benar diterima oleh PEMILIK belum mencukupi untuk pembayaran jumlah yang tertunggak itu, PENYEWA wajib untuk membayar kekurangan itu kepada PEMILIK.</p>
    <p style="text-align: justify;">15.7 Apabila selama Jangka Waktu Sewa berlangsung PENYEWA ingin mengembalikan sebagian Ruangan kepada PEMILIK, maka PENYEWA wajib mengirimkan surat pemberitahuan kepada PEMILIK paling lambat 3 (tiga) bulan sebelum tanggal pengembalian yang diinginkan PENYEWA, serta PENYEWA wajib mendapatkan persetujuan tertulis dari PEMILIK. Dan akibat dari Pengembalian tersebut, PENYEWA akan dikenakan biaya pengembalian Ruangan sebesar 20% dari seluruh Uang Sewa Ruangan yang dikembalikan tersebut</p>
    <p style="text-align: justify;">15.8 Uang Sewa, Uang Jaminan dan Deposit Telepon yang harus dikembalikan oleh PEMILIK kepada PENYEWA akan dikembalikan oleh PEMILIK kepada PENYEWA selambat-lambatnya 30 (tiga puluh) hari kalender setelah Jangka Waktu Sewa diakhiri.</p>
    <p style="text-align: justify;">15.9 Jika PENYEWA ingin mengakhiri Jangka Waktu Sewa, PENYEWA harus memberitahukan niatnya itu sedikitnya 6 (enam) bulan sebelum tanggal pengakhiran yang dikehendaki PENYEWA, dengan cara diatur dalam Pasal 15 ini atau ditentukan lain oleh PEMILIK pada waktu itu.</p>
    <p style="text-align: justify;">15.10 Apabila Perjanjian ini berakhir karena berakhirnya Jangka Waktu Sewa sebagaimana ditetapkan dalam Pasal 3.1 Perjanjian ini atau karena diakhiri oleh PENYEWA sesuai dengan Pasal 15 di atas, PENYEWA wajib untuk menyerahkan kembali kepada PEMILIK Ruangan dalam kondisi kosong dan/atau seperti pada saat serah terima awal atau dapat pula ditetapkan berdasarkan kesepakatan Para Pihak. Ruangan wajib diserahkan dalam keadaan baik, bersih serta kosong dari barang-barang PENYEWA selambat-lambatnya 14 (empat belas) hari sesudah Perjanjian berakhir atau diakhiri, tanpa PEMILIK wajib membayar ganti rugi dalam bentuk apapun juga kepada PENYEWA.</p>
    <p style="text-align: justify;">15.11 Dalam hal PENYEWA lalai untuk menyerahkan kembali Ruangan tersebut kepada PEMILIK, maka untuk setiap hari kelalaian atau keterlambatan dalam penyerahan kembali Ruangan itu, dalam keadaan tersebut dalam Pasal 15.10. diatas, PENYEWA wajib membayar uang denda kepada PEMILIK yang ditetapkan sebesar 1% (satu persen) dari seluruh harga sewa setiap hari keterlambatan, terhitung sejak PENYEWA seharusnya menyerahkan Ruangan tersebut dan Ruangan tersebut diterima kembali dalam keadaan kosong dan baik oleh PEMILIK. Denda ini harus dibayar oleh PENYEWA segera dan sekaligus atas tagihan pertama PEMILIK.</p>
    <p style="text-align: justify;">15.12 Tanpa mengurangi ketentuan-ketentuan yang diuraikan diatas, PENYEWA dengan ini sekarang dan untuk kemudian pada waktunya memberi kuasa dan wewenang penuh kepada PEMILIK untuk melakukan tindakan dan langkah-langkah yang dianggap baik oleh PEMILIK guna mengambil dan mengosongkan sendiri atau untuk mengosongkan Ruangan yang bersangkutan dari siapapun juga yang memakai/menguasai serta memindahkan semua dan setiap serta barang-barang milik /kepunyaan siapapun juga yang berada/disimpan dalam ruangan Ruangan dan berhak untuk kemudian menjual barang-barang dengan harga yang ditetapkan PEMILIK, serta mempergunakan hasilnya guna membayar ongkos-ongkos penguasaan kembali Ruangan serta membayar ongkos penjualan barang-barang PENYEWA dan akhirnya melunaskan semua tunggakan PENYEWA kepada PEMILIK berdasarkan Perjanjian. PEMILIK jika dipandang perlu, juga berhak untuk mempergunakan bantuan alat-alat kekuasaaan Negara, semuanya atas ongkos/biaya PENYEWA. <br />Dalam kejadian tersebut PENYEWA membebaskan PEMILIK untuk memberikan tanggung jawab sebagai seorang pemegang kuasa dan PEMILIK juga tidak dapat diwajibkan untuk membayar suatu ganti rugi berupa apapun, baik kepada PENYEWA maupun kepada pihak lain yang mendalihkan berhak atas seluruh atau sebagian barang dalam Ruangan. <br />PENYEWA dengan ini, secara tegas membebaskan ( <em>vrijwaring</em>) PEMILIK terhadap pihak ketiga dan berjanji untuk membayar ganti rugi kepada PEMILIK, karena sebab apapun dirugikan oleh pihak ketiga dalam rangka pelaksanaan ketentuan ini. </p>
    <p style="text-align: justify;">15.13 Sepanjang masih diperlukan, PENYEWA dengan ini pula sekarang untuk dikemudian hari, melepaskan semua dan setiap haknya (sepanjang hak-hak tersebut masih dimilikinya) untuk mengajukan tuntutan/gugatan berupa apapun juga terhadap PEMILIK baik mengenai pengambilan dan pengosongan atas Ruangan maupun pemindahan, dan penjualan barang-barang termaksud diatas, yang dilakukan atau suruh dilakukan oleh PEMILIK dengan cara yang diuraikan diatas, maupun mengenai segala akibat yang timbul sehubungan dengan pemindahan dan penjualan barang-barang tersebut serta pengambilan dan pengosongan Ruangan tersebut.</p>
    <p style="text-align: justify;">15.14 Perjanjian ini tidak akan berakhir karena dibubarkannya PEMILIK dan/atau dijualnya atau dengan beralihnya hak PEMILIK atas Gedung kepada pihak ketiga, sebelum Jangka Waktu Sewa berakhir, atau diakhiri menurut ketentuan Perjanjian ini. Dalam hal mana penerimaan atau pengganti hak wajib memenuhi kewajiban-kewajiban serta terikat pada ketentuan-ketentuan yang termaksud dalam Perjanjian ini.</p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 16</strong>
    <br />
    <strong>DOMISILI HUKUM DAN PENYELESAIAN PERSELISIHAN</strong>
    </h3>
    <p style="text-align: justify;">16.1 Perjanjian ini tunduk pada dan oleh karenanya harus ditafsirkan berdasarkan hukum Negara Republik Indonesia.</p>
    <p style="text-align: justify;">16.2 Jika terjadi perselisihan di antara Para Pihak, maka akan diselesaikan terlebih dulu di antara Para Pihak secara musyawarah untuk mufakat.</p>
    <p style="text-align: justify;">16.3 Jika penyelesaian perselisihan secara musyawarah untuk mufakat tersebut tidak dapat menyelesaikan perselisihan yang timbul, maka Para Pihak sepakat untuk menyelesaikannya melalui Pengadilan Negeri {{ $dataProject->PROJECT_KOTA }}.</p>
    <p style="text-align: justify;">16.4. Tentang Perjanjian dan segala akibatnya, PENYEWA memilih tempat kedudukan hukum yang tetap dan seumumnya di Kepaniteraan Pengadilan Negeri {{ $dataProject->PROJECT_KOTA }}.</p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">
    <strong>PASAL 17</strong>
    <br />
    <strong>PERNYATAAN DAN JAMINAN PENYEWA</strong>
    </h3>
    <p style="text-align: justify;">PENYEWA menyatakan dan menjamin kepada PEMILIK bahwa:</p>
    <p style="text-align: justify;">17.1 PENYEWA akan beritikad baik dalam melakukan kegiatan usaha.</p>
    <p style="text-align: justify;">17.2 PENYEWA wajib mendaftarkan dan mengurus semua barang dan/atau jasa yang diproduksi dan/atau diperdagangkan dalam Perizinan Produk Standar Nasional Indonesia (SNI).</p>
    <p style="text-align: justify;">17.3 PENYEWA menjamin semua barang dan/atau jasa yang diproduksi dan/atau diperdagangkan sudah terdaftar dan layak sebagai produk barang dan/atau jasa sesuai dengan ketentuan Standar Nasional Indonesia (SNI) serta PENYEWA menjamin bahwa barang dan/atau Jasa yang dijual tidak melanggar ketentuan peraturan perundang-undangan tentang Hak Atas Kekayaan Intelektual yang berlaku.</p>
    <p style="text-align: justify;">17.4 PENYEWA menjamin mutu barang dan/atau jasa yang diproduksi dan/atau diperdagangkan berdasarkan ketentuan Standar Nasional Indonesia (SNI) mutu barang dan/atau jasa yang berlaku.</p>
    <p style="text-align: justify;">17.5 Segala perizinan yang berkaitan dengan kualitas produk barang dan/atau jasa yang diproduksi dan/atau diperdagangkan menjadi tanggung jawab PENYEWA.</p>
    <p style="text-align: justify;">17.6 PENYEWA bertanggung jawab penuh atas barang dan/atau jasa yang diproduksi dan/atau diperdagangkan.</p>
    <p style="text-align: justify;">17.7 PENYEWA memberikan informasi yang benar, jelas dan jujur mengenai kondisi dan jaminan barang dan/atau jasa serta memberi penjelasan penggunaan, perbaikan dan pemeliharaan kepada konsumen.</p>
    <p style="text-align: justify;">17.8 PENYEWA memperlakukan atau melayani konsumen secara benar dan jujur serta tidak diskriminatif.</p>
    <p style="text-align: justify;">17.9 PENYEWA memberi kesempatan kepada konsumen untuk menguji, dan/atau mencoba barang dan/atau jasa tertentu serta memberi jaminan dan/atau garansi atas barang yang dibuat dan/atau diperdagangkan.</p>
    <p style="text-align: justify;">17.10 PENYEWA membebaskan PEMILIK dari segala akibat hukum apabila di kemudian hari ditemukan dan diketahui bahwa barang dan/atau jasa yang diproduksi dan/atau diperdagangkan tidak memiliki izin dan tidak sesuai dengan ketentuan Standar Nasional Indonesia (SNI).</p>
    <p style="text-align: justify;">&nbsp;</p>
    <h3 style="text-align: center;">PASAL 18 <br />LAIN - LAIN </h3>
    <p style="text-align: justify;">18.1 Semua lampiran yang disebutkan dalam Perjanjian atau lampiran-lampiran/perubahan isi ketentuan perjanjian/perjanjian tambahan/addendum Perjanjian yang akan dibuat dikemudian hari oleh Para Pihak merupakan satu kesatuan dan bagian yang tidak terpisahkan dari Perjanjian ini.</p>
    <p style="text-align: justify;">18.2 Mengenai segala hal yang belum ataupun tidak cukup diatur dalam Perjanjian ini akan disepakati kemudian dan dituangkan dalam sebuah addendum yang ditandatangani bersama sebagai tanda persetujuan Para Pihak.</p>
    <p style="text-align: justify;">18.3 Semua kuasa dan wewenang yang diberikan oleh PENYEWA kepada PEMILIK dalam Perjanjian ini tidak dapat ditarik kembali dan tidak akan berakhir karena sebab apapun termasuk tetapi tidak terbatas pada sebab-sebab yang tersebut dalam Pasal-pasal 1813, 1814 dan Pasal 1816 K.U.H. PERDATA dan PENYEWA setuju untuk menyampingkan ketentuan-ketentuan tersebut.</p>
    <p style="text-align: justify;">18.4 Pemberitahuan yang harus diberikan berhubungan dengan Perjanjian wajib diberitahukan secara tertulis dan dikirim langsung dengan mendapat suatu tanda terima dan/atau dikirim dengan pos tercatat kepada alamat-alamat sebagai berikut:</p>
    <table style="height: 36px; width: 100%; border-collapse: collapse;" border="0">
    <tbody>
        <tr style="height: 18px;">
        <td style="width: 17.741%; height: 18px; vertical-align: top; text-align: left;">
            <strong>PEMILIK</strong>
        </td>
        <td style="width: 2.89861%; height: 18px; vertical-align: top; text-align: left;">
            <strong>:</strong>
        </td>
        <td style="width: 79.3603%; height: 18px;">
            <strong>{{ $dataCompany->COMPANY_NAME }}</strong>
            <br />Unit {{ $dataProject->PROJECT_NAME }} <br />{{ $dataProject->PROJECT_ADDRESS }}
        </td>
        </tr>
        <tr style="height: 18px;">
        <td style="width: 17.741%; height: 18px; vertical-align: top; text-align: left;">
            <strong>PENYEWA</strong>
        </td>
        <td style="width: 2.89861%; height: 18px; vertical-align: top; text-align: left;">
            <strong>:</strong>
        </td>
        <td style="width: 79.3603%; height: 18px;">
            <strong>{{ $dataTenant->MD_TENANT_NAME_CHAR }}</strong>
            <br />{{ strip_tags($dataTenant->MD_TENANT_ADDRESS1) }}
        </td>
        </tr>
    </tbody>
    </table>
    <p style="text-align: justify;">Jika terjadi perubahan alamat yang tercantum dalam Perjanjian maka Pihak yang mengubah alamat wajib untuk memberitahukan perubahan tersebut kepada Pihak lainnya dalam jangka waktu 7 (tujuh) hari setelah perubahan alamat itu terjadi dan dilakukan. Segala akibat yang timbul karena resiko dan tanggungan Pihak yang mengubah alamat yang bersangkutan.</p>
    <p style="text-align: justify;">Demikian Para Pihak membuat dan menandatangani Perjanjian ini di {{ $dataProject->PROJECT_KOTA }} sebagai tanda persetujuan Para Pihak oleh wakil-wakil yang sah dan berwenang dari Para Pihak pada hari, tanggal, dan tahun yang disebut pada awal Perjanjian ini dalam rangkap 2 (dua) yang isi dan bunyinya sama, keduanya bermaterai cukup dan masing-masing memiliki kekuatan hukum yang sama.</p>
    <p style="text-align: justify;">&nbsp;</p>
    <div style="page-break-inside: avoid;">
        <table style="height: 181px; width: 100%; border-collapse: collapse;" border="0">
        <tbody>
            <tr style="height: 18px;">
            <td style="width: 45.0274%; height: 18px;">
                <strong>PEMILIK</strong>
            </td>
            <td style="width: 49.2254%; height: 18px;">
                <strong>PENYEWA</strong>
            </td>
            </tr>
            <tr style="height: 18px;">
            <td style="width: 45.0274%; height: 18px;">
                <strong>{{ $dataCompany->COMPANY_NAME }}</strong>
            </td>
            <td style="width: 49.2254%; height: 18px;">
                <strong>{{ $dataTenant->MD_TENANT_NAME_CHAR }}</strong>
            </td>
            </tr>
            <tr style="height: 140px;">
            <td style="width: 45.0274%; height: 127px;">&nbsp;</td>
            <td style="width: 49.2254%; height: 127px;">&nbsp;</td>
            </tr>
            <tr style="height: 18px;">
            <td style="width: 45.0274%; height: 18px;">
                <strong>ANHAR SUDRADJAT</strong>
            </td>
            <td style="width: 49.2254%; height: 18px;">
                <strong>{{ $dataTenant->MD_TENANT_DIRECTOR }}</strong>
            </td>
            </tr>
        </tbody>
        </table>
        <p>&nbsp;</p>
        <table style="height: 181px; width: 100%; border-collapse: collapse;" border="0">
        <tbody>
            <tr style="height: 18px;">
            <td style="width: 45.0274%; height: 18px;">
                <strong>SAKSI I</strong>
            </td>
            <td style="width: 49.2254%; height: 18px;">
                <strong>SAKSI II</strong>
            </td>
            </tr>
            <tr style="height: 140px;">
            <td style="width: 45.0274%; height: 127px;">&nbsp;</td>
            <td style="width: 49.2254%; height: 127px;">&nbsp;</td>
            </tr>
            <tr style="height: 18px;">
            <td style="width: 45.0274%; height: 18px;">
                <strong>{{ $dataProject->GM_NAME }}</strong>
            </td>
            <td style="width: 49.2254%; height: 18px;">
                <strong>{{ $dataProject->CHIEF_MARKETING }}</strong>
            </td>
            </tr>
        </tbody>
        </table>
    </div>
    <div class="pagebreak"></div>
    <h3 style="text-align: center;">
    <strong>LAMPIRAN PERJANJIAN SEWA MENYEWA</strong>
    <br />
    <strong>No. {{ $dataPSM->NO_KONTRAK_NOCHAR }}</strong>
    </h3>
    <hr />
    <table style="width: 100%; border-collapse: collapse;" border="0">
    <tbody>
        <tr>
        <td style="width: 22.6886%;">Penyewa</td>
        <td style="width: 2.44877%;">:</td>
        <td style="width: 74.8626%;">{{ $dataPSM->SHOP_NAME_CHAR }}</td>
        </tr>
        <tr>
        <td style="width: 22.6886%;">Lokasi</td>
        <td style="width: 2.44877%;">:</td>
        <td style="width: 74.8626%;">{{ $dataProject->PROJECT_NAME }}, ({{ $dataPSMLevel }}), ({{ $dataLot }})</td>
        </tr>
        <tr>
        <td style="width: 22.6886%;">Luas</td>
        <td style="width: 2.44877%;">:</td>
        <td style="width: 74.8626%;">{{ (float) $dataLotSqm }} m2</td>
        </tr>
        <tr>
        <td style="width: 22.6886%;">Masa Sewa</td>
        <td style="width: 2.44877%;">:</td>
        <td style="width: 74.8626%;">{{ $startDateIndo }} s/d {{ $endDateIndo }}</td>
        </tr>
        <tr>
        <td style="width: 22.6886%; vertical-align: top; text-align: left;">Harga Sewa</td>
        <td style="width: 2.44877%; vertical-align: top; text-align: left;">:</td>
        <td style="width: 74.8626%;">
            <table style="width: 100%; border-collapse: collapse;" border="0">
            <tbody>
                @foreach($dataLotPriceDetails as $key => $data)
                <tr>
                @if($data->IS_GENERATE_MANUAL == 1)
                <td style="width: 16%;">{{ $masaSewa }}</td>
                @else
                    @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
                    <td style="width: 16%;">Tahun {{$data->PSM_TRANS_PRICE_YEAR}} (< 1 Bulan)</td>
                    @else
                    <td style="width: 16%;">Tahun {{$data->PSM_TRANS_PRICE_YEAR}} ({{$arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]}} Bulan)</td>
                    @endif
                @endif
                <td style="width: 1%;">:</td>
                @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
                <td style="width: 41.212%;">Rp. {{number_format($data->PSM_TRANS_PRICE_RENT_NUM,0,'','.')}},-/m²/(< 1 bulan) (belum termasuk PPN)</td>
                @else
                <td style="width: 41.212%;">Rp. {{number_format($data->PSM_TRANS_PRICE_RENT_NUM,0,'','.')}},-/m²/bulan (belum termasuk PPN)</td>
                @endif
                </tr>
                @endforeach
            </tbody>
            </table>
        </td>
        </tr>
        <tr>
        <td style="width: 22.6886%; vertical-align: top; text-align: left;">Total Sewa</td>
        <td style="width: 2.44877%; vertical-align: top; text-align: left;">:</td>
        <td style="width: 74.8626%;">
            <table style="width: 100%; border-collapse: collapse;" border="0">
            <tbody>
                <?php $totalPrice2 = 0; ?>
                @foreach($dataLotPriceDetails as $key => $data)
                <tr>
                @if($data->IS_GENERATE_MANUAL == 1)
                <td style="width: 16%;">{{ $masaSewa }}</td>
                @else
                    @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
                    <td style="width: 16%;">Tahun {{$data->PSM_TRANS_PRICE_YEAR}} (< 1 Bulan)</td>
                    @else
                    <td style="width: 16%;">Tahun {{$data->PSM_TRANS_PRICE_YEAR}} ({{$arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]}} Bulan)</td>
                    @endif
                @endif
                <td style="width: 1%;">:</td>
                <td style="width: 41.212%;">
                    @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
                    <?php $subTotal2 = ($data->PSM_TRANS_PRICE_RENT_NUM * $dataLotSqm) * 1; ?>
                    Rp. {{number_format($data->PSM_TRANS_PRICE_RENT_NUM,0,'','.')}},- x {{number_format($dataLotSqm,2,',','.')}} m² x (< 1 bulan) = Rp. {{ number_format($subTotal2,0,'','.') }},-
                    @else
                    <?php $subTotal2 = ($data->PSM_TRANS_PRICE_RENT_NUM * $dataLotSqm) * $arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]; ?>
                    Rp. {{number_format($data->PSM_TRANS_PRICE_RENT_NUM,0,'','.')}},- x {{number_format($dataLotSqm,2,',','.')}} m² x {{$arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]}} bulan = Rp. {{ number_format($subTotal2,0,'','.') }},-
                    @endif
                </td>
                </tr>
                <?php $totalPrice2 += $subTotal2; ?>
                @endforeach
                <tr>
                <td style="width: 16%;">Sub Total</td>
                <td style="width: 1%;">:</td>
                <td style="width: 41.212%;">Rp. {{number_format($dataPSM->PSM_TRANS_NET_BEFORE_TAX,0,'','.')}},-</td>
                </tr>
                <tr>
                <td style="width: 16%;">PPN ({{ round(($dataPSM->PSM_TRANS_PPN / $dataPSM->PSM_TRANS_NET_BEFORE_TAX) * 100, 0) }}%)</td>
                <td style="width: 1%;">:</td>
                <td style="width: 41.212%;">Rp. {{number_format($dataPSM->PSM_TRANS_PPN,0,'','.')}},-</td>
                </tr>
                <tr>
                <td style="width: 16%;">Total</td>
                <td style="width: 1%;">:</td>
                <td style="width: 41.212%;">Rp. {{number_format($dataPSM->PSM_TRANS_PRICE,0,'','.')}},-</td>
                </tr>
            </tbody>
            </table>
        </td>
        </tr>
        <tr>
        <td style="width: 22.6886%; vertical-align: top; text-align: left;">Service Charge + Promotion Levy</td>
        <td style="width: 2.44877%; vertical-align: top; text-align: left;">:</td>
        <td style="width: 74.8626%;">
            <table style="width: 100%; border-collapse: collapse;" border="0">
            <tbody>
                @foreach($dataLotPriceDetails as $key => $data)
                <tr>
                @if($data->IS_GENERATE_MANUAL == 1)
                <td style="width: 16%;">{{ $masaSewa }}</td>
                @else
                    @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
                    <td style="width: 16%;">Tahun {{$data->PSM_TRANS_PRICE_YEAR}} (< 1 Bulan)</td>
                    @else
                    <td style="width: 16%;">Tahun {{$data->PSM_TRANS_PRICE_YEAR}} ({{$arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]}} Bulan)</td>
                    @endif
                @endif
                <td style="width: 1%;">:</td>
                @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
                <td style="width: 41.212%;">Rp. {{number_format($data->PSM_TRANS_PRICE_SC_NUM,0,'','.')}},-/m²/(< 1 bulan) (belum termasuk PPN)</td>
                @else
                <td style="width: 41.212%;">Rp. {{number_format($data->PSM_TRANS_PRICE_SC_NUM,0,'','.')}},-/m²/bulan (belum termasuk PPN)</td>
                @endif
                </tr>
                @endforeach
            </tbody>
            </table>
        </td>
        </tr>
    </tbody>
    </table>
    <p>A. PEMBAYARAN SELAMA MASA PENGGUNAAN RUANGAN <br />DP Sebesar {{ (float) $dataPSM->PSM_TRANS_DP_PERSEN }}% x Rp {{ number_format($dataPSM->PSM_TRANS_PRICE,0,'','.') }} = Rp {{ number_format($dataPSM->PSM_TRANS_PRICE * ($dataPSM->PSM_TRANS_DP_PERSEN / 100),0,'','.') }},- (sudah termasuk PPN) </p>
    <?php
        $dataPembayaranSewaSisaPersen = 100 - $dataPSM->PSM_TRANS_DP_PERSEN;
        $dataPembayaranSewaIncPPN = ((100-$dataPSM->PSM_TRANS_DP_PERSEN) / 100) * $dataPSM->PSM_TRANS_PRICE;
    ?>
    <p>B. PEMBAYARAN SEWA MENYEWA <br />Sisa {{ $dataPembayaranSewaSisaPersen }}% x Rp {{ number_format($dataPSM->PSM_TRANS_PRICE,0,'','.') }} = Rp {{ number_format($dataPembayaranSewaIncPPN,0,'','.') }},- (sudah termasuk PPN) diangsur selama {{ $dataPSM->PSM_TRANS_TIME_PERIOD_SCHED }} bulan sebesar @ Rp {{ number_format($dataPembayaranSewaIncPPN / $dataPSM->PSM_TRANS_TIME_PERIOD_SCHED,0,'','.') }},- </p>
    <p>C. UANG JAMINAN SEWA <br />3 bulan uang jaminan <em>Service Charge</em> atau Rp {{ number_format($dataSecDepSC,0,'','.') }},- dibayar sekaligus. </p>
    <p>D. PEMBAYARAN <em>SERVICE CHARGE + PROMOTION LEVY</em>
    <br />Membayar <em>Service Charge</em> dan <em>Promotion Levy</em> setiap bulannya sebesar :
    </p>
    <table style="width: 100%; border-collapse: collapse;" border="0">
    <tbody>
        <?php $totalPriceSC2 = 0; ?>
        @foreach($dataLotPriceDetails as $key => $data)
        <tr>
        @if($data->IS_GENERATE_MANUAL == 1)
        <td style="width: 16%;">{{ $masaSewa }}</td>
        @else
            @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
            <td style="width: 16%;">Tahun {{$data->PSM_TRANS_PRICE_YEAR}} (< 1 Bulan)</td>
            @else
            <td style="width: 16%;">Tahun {{$data->PSM_TRANS_PRICE_YEAR}} ({{$arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]}} Bulan)</td>
            @endif
        @endif
        <td style="width: 1%;">:</td>
        <td style="width: 41.212%;">
            @if($arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR] < 1)
            <?php $subTotalSC2 = ($data->PSM_TRANS_PRICE_SC_NUM * $dataLotSqmSC) * 1; ?>
            Rp. {{number_format($data->PSM_TRANS_PRICE_SC_NUM,0,'','.')}},- x {{number_format($dataLotSqmSC,2,',','.')}} m² x (< 1 bulan) = Rp. {{ number_format($subTotalSC2,0,'','.') }},-
            @else
            <?php $subTotalSC2 = ($data->PSM_TRANS_PRICE_SC_NUM * $dataLotSqmSC) * $arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]; ?>
            Rp. {{number_format($data->PSM_TRANS_PRICE_SC_NUM,0,'','.')}},- x {{number_format($dataLotSqmSC,2,',','.')}} m² x {{$arrDiffMonthYear[$data->PSM_TRANS_PRICE_YEAR]}} bulan = Rp. {{ number_format($subTotalSC2,0,'','.') }},-
            @endif
        </td>
        </tr>
        <?php $totalPriceSC2 += $subTotalSC2; ?>
        @endforeach
        <tr>
        <td style="width: 16%;">Sub Total</td>
        <td style="width: 1%;">:</td>
        <td style="width: 41.212%;">Rp. {{number_format($totalPriceSC2,0,'','.')}},-</td>
        </tr>
        <tr>
        <td style="width: 16%;">PPN ({{ $dataProject->PPNBM_NUM * 100 }}%)</td>
        <td style="width: 1%;">:</td>
        <td style="width: 41.212%;">Rp. {{number_format($totalPriceSC2 * $dataProject->PPNBM_NUM,0,'','.')}},-</td>
        </tr>
        <tr>
        <td style="width: 16%;">Total</td>
        <td style="width: 1%;">:</td>
        <td style="width: 41.212%;">Rp. {{number_format($totalPriceSC2 * $dataProject->DPPBM_NUM,0,'','.')}},-</td>
        </tr>
    </tbody>
    </table>
    <p>E. UANG JAMINAN TELPON (DEPOSIT TELPON) <br />PENYEWA harus membayar uang jaminan telpon sebesar Rp {{ number_format($dataSecDepTelp,0,'','.') }},-. Uang jaminan telpon akan dikembalikan pada akhir Jangka Waktu Sewa. </p>
    <p>Uang jaminan yang telah dibayarkan dianggap sah, apabila ada bukti pembayaran dari {{ $dataProject->PROJECT_NAME }}.</p>
    <p>&nbsp;</p>
    <div style="page-break-inside: avoid;">
        <table style="height: 151px; width: 100%; border-collapse: collapse;" border="0">
        <tbody>
            <tr style="height: 18px;">
            <td style="width: 50%; height: 18px;">
                <strong>PEMILIK</strong>
            </td>
            <td style="width: 50%; height: 18px;">
                <strong>PENYEWA</strong>
            </td>
            </tr>
            <tr style="height: 18px;">
            <td style="width: 50%; height: 18px;">
                <strong>{{ $dataCompany->COMPANY_NAME }}</strong>
            </td>
            <td style="width: 50%; height: 18px;">
                <strong>{{ $dataTenant->MD_TENANT_NAME_CHAR }}</strong>
            </td>
            </tr>
            <tr style="height: 140px;">
            <td style="width: 50%; height: 97px;">&nbsp;</td>
            <td style="width: 50%; height: 97px;">&nbsp;</td>
            </tr>
            <tr style="height: 18px;">
            <td style="width: 50%; height: 18px;">
                <strong>ANHAR SUDRADJAT</strong>
            </td>
            <td style="width: 50%; height: 18px;">
                <strong>{{ $dataTenant->MD_TENANT_DIRECTOR }}</strong>
            </td>
            </tr>
        </tbody>
        </table>
    </div>
</html>