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
        <div class="page-header" style="text-align:right"><i>Legal Report</i></div>
        <div class="page-footer" style="text-align:right">{pageNum}</div>
        <div class="text-center">
            <h1>IMB & Sertifikat</h1>
        </div>
        <hr/>

        <h5><b>IMB</b></h5>
        <?php
        Table::create(array(
            "dataStore"=>$this->dataStore('legal_imb_sertifikat_table1'),
            "showFooter"=>"bottom",
            "themeBase"=>"bs4",
            "cssClass"=>array(
                "table"=>"table table-striped table-bordered"
            ),
            "columns"=>array(
                "tahun"=>array(
                    "label" => "TAHUN",
                    'type' => 'string',
                    "footerText"=>"<p><b>TOTAL</b></p>"
                ),
                "IMB"=>array(
                    "label" => "SUDAH IMB",
                    'type' => 'number',
                    "footer"=>"sum",
                    "footerText"=>"<b>@value</b>"
                ),
                "blm_IMB"=>array(
                    "label" => "BELUM IMB",
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
        <br />
        <h5><b>SERTIFIKAT</b></h5>
        <?php
            Table::create(array(
                "dataStore"=>$this->dataStore('legal_imb_sertifikat_table2'),
                "showFooter"=>"bottom",
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "tahun"=>array(
                        "label" => "TAHUN",
                        'type' => 'string',
                        "footerText"=>"<p><b>TOTAL</b></p>"
                    ),
                    "sudah_sertifikat"=>array(
                        "label" => "SUDAH SERTIFIKAT",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "blm_sertifikat"=>array(
                        "label" => "BELUM SERTIFIKAT",
                        'type' => 'number',
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