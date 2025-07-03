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
            <h1>Posisi Mutasi Escrow</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Angka Dalam Jutaan</b></span>
        <?php
            Table::create(array(
                "dataStore"=>$report->dataStore('finance_accounting_posisi_mutasi_escrow_table'),
                "headers"=>array(
                    array(
                        ""=>array("colSpan"=>1),
                        "SALDO 31-".substr(strtoupper(DateTime::createFromFormat('!m', '12')->format('F')), 0, 3)."-".(date('Y', strtotime($this->params['cut_off'])) - 1)
                        =>array("colSpan"=>2),
                        "PENAMBAHAN S/D ".strtoupper(date("d-M-Y", strtotime($this->params['cut_off'])))=>array("colSpan"=>2),
                        "PENCAIRAN S/D ".strtoupper(date("d-M-Y", strtotime($this->params['cut_off'])))=>array("colSpan"=>2),
                        "POSISI ESCROW ".strtoupper(date("d-M-Y", strtotime($this->params['cut_off'])))=>array("colSpan"=>2),
                        "PROYEKSI PENC ".date('Y', strtotime($this->params['cut_off']))=>array("colSpan"=>2),
                    )
                ),
                "showFooter"=>"bottom",
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "BANK_NAME_CHAR"=>array(
                        "label" => "BANK",
                        "type" => "string",
                    ),
                    "SISA_UPTO_LS"=>array(
                        "label" => "Rp",
                    ),
                    "UNIT_UPTO_LS"=>array(
                        "label" => "Unit",
                    ),
                    "SISA_UPTO"=>array(
                        "label" => "Rp",
                    ),
                    "UNIT_UPTO"=>array(
                        "label" => "Unit",
                    ),
                    "SA_REALISASI_AMOUNT_INT"=>array(
                        "label" => "Rp",
                    ),
                    "UNIT_PAY"=>array(
                        "label" => "Unit",
                    ),
                    "SISA_ESCROW"=>array(
                        "label" => "Rp",
                    ),
                    "UNIT_ESCROW"=>array(
                        "label" => "Unit",
                    ),
                    "BLOKIR_AMOUNT_INT"=>array(
                        "label" => "Rp",
                    ),
                    "UNIT_PROYEKSI"=>array(
                        "label" => "Unit",
                    ),
                    
                    "BANK_NAME_CHAR"=>array(
                        "footerText"=>"<p style='text-align: left;'><b>-</b></p>"
                    ),
                    "SISA_UPTO_LS"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "UNIT_UPTO_LS"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "SISA_UPTO"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "UNIT_UPTO"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "SA_REALISASI_AMOUNT_INT"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "UNIT_PAY"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "SISA_ESCROW"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "UNIT_ESCROW"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "BLOKIR_AMOUNT_INT"=>array(
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "UNIT_PROYEKSI"=>array(
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