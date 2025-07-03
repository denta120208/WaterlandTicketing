<?php 
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\pivot\widgets\PivotTable;
    use \koolreport\widgets\google\ColumnChart;
    use \koolreport\widgets\google\PieChart;
?>
<html>
    <style>
        .pivot-column-header, .pivot-data-header {
            font-weight: bold;
            text-align: center;
        }
        .table {
            text-align: center;
            min-width: 100%;
            width: 100%;
        }
        th {
            background-color: #081c5c;
            color: white;
        }
        .pivot-column {
            background-color: #081c5c;
            color: white;
            font-weight: bold;
            text-align: center;
        }
    </style>

    <body style="margin:0.5in 1in 0.5in 1in">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <div class="page-header" style="text-align:right"><i>Teknik Report</i></div>
        <div class="page-footer" style="text-align:right">{pageNum}</div>
        <div class="text-center">
            <h1>Konsumsi & Komposisi Air (<?php echo $this->params['bulan_tahun_char'] ?>)</h1>
        </div>
        <hr/>

        <center><h5><b>Konsumsi Pemakaian Air Gedung (M3)</b></h5></center>
        <?php
            PieChart::create(array(
                "title"=>"",
                "dataSource" => $this->dataStore('teknik_konsumsi_komposisi_air_pie_chart1'),
                "columns" =>[
                    "DESCRIPTION_CHAR",
                    "NOMINAL_M3_NUM"=>array(
                        "label"=>"M3",
                        "annotation"=>function($row) {
                            return $row['NOMINAL_M3_NUM'];
                        }
                    ),
                ],
                "options"=>array(
                    "is3D"=>true,
                )
            ));
        ?>
        <center><h5><b>Komposisi Pembayaran Air Gedung (Rp)</b></h5></center>
        <?php
            PieChart::create(array(
                "title"=>"",
                "dataSource" => $this->dataStore('teknik_konsumsi_komposisi_air_pie_chart2'),
                "columns" =>[
                    "DESCRIPTION_CHAR",
                    "NOMINAL_RP_NUM"=>array(
                        "label"=>"RP",
                        "annotation"=>function($row) {
                            return $row['NOMINAL_RP_NUM'];
                        }
                    ),
                ],
                "options"=>array(
                    "is3D"=>true,
                )
            ));
        ?>
        <?php
            session(['indexColorChart' => 1]);
            ColumnChart::create(array(
                "title"=>"Konsumsi Pemakaian Air Gedung (M3)",
                "dataSource" => $this->dataStore('teknik_konsumsi_komposisi_air_pie_chart1'),
                "columns" =>[
                    "DESCRIPTION_CHAR",
                    "NOMINAL_M3_NUM"=>array(
                        "label"=>"M3",
                        "style"=>function($row){
                            if(session('indexColorChart') == 1) {
                                session(['indexColorChart' => session('indexColorChart')+1]);
                                return "color:#4472c4";
                            }
                            else if(session('indexColorChart') == 2) {
                                session(['indexColorChart' => session('indexColorChart')+1]);
                                return "color:#ed7d31";
                            }
                            else {
                                session(['indexColorChart' => 1]);
                                return "color:#a5a5a5";
                            }
                        },
                        "annotation"=>function($row) {
                            return number_format($row['NOMINAL_M3_NUM']);
                        }
                    ),
                ],
                "options"=>array(
                    "legend"=>"none"
                ),
                // 'xAxisTitle' => 'category',
                // 'yAxisTitle' => 'jumlah',
                // 'stacked' => true,
                'direction' => 'vertical',
            ));
        ?>
        <?php
            session(['indexColorChart' => 1]);
            ColumnChart::create(array(
                "title"=>"Komposisi Pembayaran Air Gedung (Rp)",
                "dataSource" => $this->dataStore('teknik_konsumsi_komposisi_air_pie_chart2'),
                "columns" =>[
                    "DESCRIPTION_CHAR",
                    "NOMINAL_RP_NUM"=>array(
                        "label"=>"RP",
                        "style"=>function($row){
                            if(session('indexColorChart') == 1) {
                                session(['indexColorChart' => session('indexColorChart')+1]);
                                return "color:#4472c4";
                            }
                            else if(session('indexColorChart') == 2) {
                                session(['indexColorChart' => session('indexColorChart')+1]);
                                return "color:#ed7d31";
                            }
                            else {
                                session(['indexColorChart' => 1]);
                                return "color:#a5a5a5";
                            }
                        },
                        "annotation"=>function($row) {
                            return number_format($row['NOMINAL_RP_NUM']);
                        }
                    ),
                ],
                "options"=>array(
                    "legend"=>"none"
                ),
                // 'xAxisTitle' => 'category',
                // 'yAxisTitle' => 'jumlah',
                // 'stacked' => true,
                'direction' => 'vertical',
            ));
        ?>

        <script type="text/javascript">
            $(document).ready(function() {
                $('.pivot-data-field-content').remove();
            });
        </script>
    </body>
</html>