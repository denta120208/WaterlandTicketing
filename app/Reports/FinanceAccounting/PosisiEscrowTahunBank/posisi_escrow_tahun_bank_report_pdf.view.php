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
            <h1>Posisi Escrow Berdasarkan Tahun & Bank</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Angka Dalam Jutaan</b></span>
        <?php
            Table::create(array(
                "dataStore"=>$report->dataStore('finance_accounting_posisi_escrow_tahun_bank_table1'),
                "headers"=>array(
                    array(
                        ""=>array("colSpan"=>1),
                        "TAHAPAN PENCAIRAN"=>array("colSpan"=>2),
                        "TOTAL"=>array("colSpan"=>2),
                        " "=>array("colSpan"=>1),
                    )
                ),
                "showFooter"=>"bottom",
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "TAHUN"=>array(
                        "label" => "TH ESCROW",
                        "type" => "string",
                    ),
                    "SISA_BUILDING"=>array(
                        "label" => "BUILDING",
                    ),
                    "SISA_CERTIFICATE"=>array(
                        "label" => "CERTIFICATE",
                    ),
                    "TOTAL_NOMINAL"=>array(
                        "label" => "Rp",
                    ),
                    "TOTAL_UNIT"=>array(
                        "label" => "Unit",
                    ),
                    "RENCANA_PENCAIRAN_SELANJUTNYA"=>array(
                        "label" => "RENCANA PENCAIRAN SELANJUTNYA",
                    ),
                    
                    "TAHUN"=>array(
                        "footerText"=>"<p style='text-align: left;'><b>TOTAL</b></p>"
                        // "footerText"=>"<p><b>TOTAL</b></p>"
                    ),
                    "SISA_BUILDING"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "SISA_CERTIFICATE"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "TOTAL_NOMINAL"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "TOTAL_UNIT"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "RENCANA_PENCAIRAN_SELANJUTNYA"=>array(
                        "footerText"=>"<p style='text-align: left;'><b>-</b></p>"
                    ),
                ),
                'options' => [
                    'ordering' => false
                ]
            ));
        ?>
        <br />
        <span style="color: red;"><b>* Angka Dalam Jutaan</b></span>
        <?php
            Table::create(array(
                "dataStore"=>$report->dataStore('finance_accounting_posisi_escrow_tahun_bank_table2'),
                "headers"=>array(
                    array(
                        ""=>array("colSpan"=>1),
                        "TAHAPAN PENCAIRAN"=>array("colSpan"=>2),
                        "TOTAL"=>array("colSpan"=>2),
                    )
                ),
                "showFooter"=>"bottom",
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "BANK"=>array(
                        "label" => "BANK",
                        "type" => "string",
                    ),
                    "SISA_BUILDING"=>array(
                        "label" => "BUILDING",
                    ),
                    "SISA_CERTIFICATE"=>array(
                        "label" => "CERTIFICATE",
                    ),
                    "TOTAL_NOMINAL"=>array(
                        "label" => "Rp",
                    ),
                    "TOTAL_UNIT"=>array(
                        "label" => "Unit",
                    ),
                    
                    "BANK"=>array(
                        "footerText"=>"<p style='text-align: left;'><b>TOTAL</b></p>"
                        // "footerText"=>"<p><b>TOTAL</b></p>"
                    ),
                    "SISA_BUILDING"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "SISA_CERTIFICATE"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "TOTAL_NOMINAL"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "TOTAL_UNIT"=>array(
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