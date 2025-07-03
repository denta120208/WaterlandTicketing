<?php 
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\pivot\widgets\PivotTable;
    use \koolreport\widgets\google\ColumnChart;
    use \koolreport\widgets\google\PieChart;
    use \koolreport\widgets\google\LineChart;
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
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 0.3%;
        }
    </style>

    <body style="margin:0.5in 1in 0.5in 1in">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <div class="page-header" style="text-align:right"><i>Teknik Report</i></div>
        <div class="page-footer" style="text-align:right">{pageNum}</div>
        <div class="text-center">
            <h1>Hutang Bayar</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Rp (Dalam Jutaan)</b></span>
        <?php
            Table::create(array(
                "dataStore"=>$report->dataStore('teknik_hutang_bayar_table1'),
                "headers"=>array(
                    array(
                        ""=>array("colSpan"=>1),
                        "Development Cost"=>array("colSpan"=>3),
                        "Perizinan"=>array("colSpan"=>3),
                        "Fixed Asset"=>array("colSpan"=>3),
                        "Construction Cost"=>array("colSpan"=>3),
                        "Dev Cost, Izin, Const Cost + Fixed Asset"=>array("colSpan"=>2),
                        "Serapan"=>array("colSpan"=>1),
                    )
                ),
                "showFooter"=>"bottom",
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "SPK_TRANS_TRX_DATE_YEAR"=>array(
                        "label" => "Tahun SPK",
                        'type' => 'string',
                        "footerText"=>"<p><b>TOTAL</b></p>"
                    ),
                    "HUTANG_BAYAR_DEV_COST"=>array(
                        "label" => "Hutang Bayar Per ".date('d M', strtotime('1990-12-31')).' '.(date('Y', strtotime($report->cut_off)) - 1),
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "TERBAYAR_DEV_COST"=>array(
                        "label" => "Terbayar Di ".date('Y', strtotime($report->cut_off)),
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "SISA_HUTANG_DEV_COST"=>array(
                        "label" => "Sisa Hutang Bayar ".date('M Y', strtotime($report->cut_off)),
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "HUTANG_BAYAR_PERIZINAN"=>array(
                        "label" => "Hutang Bayar Per ".date('d M', strtotime('1990-12-31')).' '.(date('Y', strtotime($report->cut_off)) - 1),
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "TERBAYAR_PERIZINAN"=>array(
                        "label" => "Terbayar Di ".date('Y', strtotime($report->cut_off)),
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "SISA_HUTANG_PERIZINAN"=>array(
                        "label" => "Sisa Hutang Bayar ".date('M Y', strtotime($report->cut_off)),
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "HUTANG_BAYAR_FIXED_ASSET"=>array(
                        "label" => "Hutang Bayar Per ".date('d M', strtotime('1990-12-31')).' '.(date('Y', strtotime($report->cut_off)) - 1),
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "TERBAYAR_FIXED_ASSET"=>array(
                        "label" => "Terbayar Di ".date('Y', strtotime($report->cut_off)),
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "SISA_HUTANG_FIXED_ASSET"=>array(
                        "label" => "Sisa Hutang Bayar ".date('M Y', strtotime($report->cut_off)),
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "HUTANG_BAYAR_KONSTRUKSI"=>array(
                        "label" => "Hutang Bayar Per ".date('d M', strtotime('1990-12-31')).' '.(date('Y', strtotime($report->cut_off)) - 1),
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "TERBAYAR_KONSTRUKSI"=>array(
                        "label" => "Terbayar Di ".date('Y', strtotime($report->cut_off)),
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "SISA_HUTANG_KONSTRUKSI"=>array(
                        "label" => "Sisa Hutang Bayar ".date('M Y', strtotime($report->cut_off)),
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "TERBAYAR_ALL"=>array(
                        "label" => "Terbayar s/d ".date('M Y', strtotime($report->cut_off)),
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "SISA_HUTANG_ALL"=>array(
                        "label" => "Total Sisa Hutang Bayar ".date('M Y', strtotime($report->cut_off)),
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "SERAPAN_NEXT_YEAR"=>array(
                        "label" => (date('Y', strtotime($report->cut_off)) + 1),
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                ),
                'options' => [
                    'ordering' => false
                ]
            ));
        ?>

        <script type="text/javascript">
            $(document).ready(function() {
                $('.pivot-data-field-content').remove();
            });
        </script>
    </body>
</html>