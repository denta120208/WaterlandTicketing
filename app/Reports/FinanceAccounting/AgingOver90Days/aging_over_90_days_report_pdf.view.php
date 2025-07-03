<?php 
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\widgets\google\ColumnChart;
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
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 1%;
        }
    </style>

    <body style="margin:0.5in 1in 0.5in 1in">
        <div class="page-header" style="text-align:right"><i>Finance Accounting Report</i></div>
        <div class="page-footer" style="text-align:right">{pageNum}</div>
        <div class="text-center">
            <h1>Aging > 90 Days</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Angka Dalam Jutaan, Exclude PPN</b></span>
        <?php
            Table::create(array(
                "dataStore"=>$report->dataStore('finance_accounting_aging_over_90_days_table'),
                "headers"=>array(
                    array(
                        ""=>array("colSpan"=>5),
                        "UANG MASUK"=>array("colSpan"=>2),
                        "TUNGGAKAN"=>array("colSpan"=>1),
                        " "=>array("colSpan"=>2),
                    )
                ),
                "showFooter"=>"bottom",
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "NAMA"=>array(
                        "label" => "NAMA",
                    ),
                    "BLOK"=>array(
                        "label" => "BLOK",
                    ),
                    "TAHUN"=>array(
                        "label" => "TAHUN",
                        'type' => 'string',
                    ),
                    "CARA_BAYAR"=>array(
                        "label" => "CARA BAYAR",
                    ),
                    "TOTAL_HARGA"=>array(
                        "label" => "TOTAL HARGA",
                    ),
                    "UANG_MASUK_TOTAL"=>array(
                        "label" => "TOTAL",
                    ),
                    "UANG_MASUK_PERSEN"=>array(
                        "label" => "%",
                        'type' => "string",
                    ),
                    "TUNGGAKAN"=>array(
                        "label" => "> 90 HARI",
                    ),
                    "TERAKHIR_BAYAR"=>array(
                        "label" => "TERAKHIR BAYAR",
                    ),
                    "KET"=>array(
                        "label" => "KET",
                    ),
                    
                    "NAMA"=>array(
                        "footerText"=>"<p style='text-align: left;'><b>TOTAL</b></p>"
                    ),
                    "BLOK"=>array(
                        "footerText"=>"<b>-</b>"
                    ),
                    "TAHUN"=>array(
                        "footerText"=>"<b>-</b>"
                    ),
                    "CARA_BAYAR"=>array(
                        "footerText"=>"<b>-</b>"
                    ),
                    "UANG_MASUK_PERSEN"=>array(
                        "footerText"=>"<b>-</b>"
                    ),
                    "TUNGGAKAN"=>array(
                        "footerText"=>"<b>-</b>"
                    ),
                    "TERAKHIR_BAYAR"=>array(
                        "footerText"=>"<b>-</b>"
                    ),
                    "KET"=>array(
                        "footerText"=>"<b>-</b>"
                    ),
                    "TOTAL_HARGA"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "UANG_MASUK_TOTAL"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "TUNGGAKAN"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                ),
                'options' => [
                    'ordering' => false
                ]
            ));
        ?>
    </body>
</html>