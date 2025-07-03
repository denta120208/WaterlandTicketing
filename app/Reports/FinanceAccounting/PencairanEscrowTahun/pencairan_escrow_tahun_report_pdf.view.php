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
            <h1>Pencairan Escrow Berdasarkan Tahun</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Angka Dalam Jutaan</b></span>
        <?php
            Table::create(array(
                "dataStore"=>$report->dataStore('finance_accounting_pencairan_escrow_tahun_table'),
                "headers"=>array(
                    array(
                        ""=>array("colSpan"=>1),
                        "BUILDING"=>array("colSpan"=>2),
                        "CERTIFICATE"=>array("colSpan"=>2),
                        "TOTAL PENCAIRAN ". strtoupper(date('d M Y', strtotime($this->params['cut_off'])))=>array("colSpan"=>2),
                    )
                ),
                "showFooter"=>"bottom",
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "TAHUN_AKAD"=>array(
                        "label" => "TH ESCROW",
                        "type" => "string",
                    ),
                    "SISA_BUILDING"=>array(
                        "label" => "Rp",
                    ),
                    "UNIT_BUILDING"=>array(
                        "label" => "Unit",
                    ),
                    "SISA_SERTIFIKAT"=>array(
                        "label" => "Rp",
                    ),
                    "UNIT_SERTIFIKAT"=>array(
                        "label" => "Unit",
                    ),
                    "PAID_BLOKIR_AMOUNT_NUM"=>array(
                        "label" => "Rp",
                    ),
                    "UNIT_PAID"=>array(
                        "label" => "Unit",
                    ),
                    
                    "TAHUN_AKAD"=>array(
                        // "footerText"=>"<p style='text-align: left;'><b>TOTAL</b></p>"
                        "footerText"=>"<p><b>TOTAL</b></p>"
                    ),
                    "SISA_BUILDING"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "UNIT_BUILDING"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "SISA_SERTIFIKAT"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "UNIT_SERTIFIKAT"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "PAID_BLOKIR_AMOUNT_NUM"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "UNIT_PAID"=>array(
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