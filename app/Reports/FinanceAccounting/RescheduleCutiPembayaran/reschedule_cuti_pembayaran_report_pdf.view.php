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
            <h1>Penerapan Kebijakan Reschedule & Cuti Pembayaran</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Rp (Dalam Jutaan)</b></span>
        <?php
        Table::create(array(
            "dataStore"=>$this->dataStore('finance_accounting_reschedule_cuti_pembayaran_table_pdf'),
            "headers"=>array(
                array(
                    ""=>array("colSpan"=>1),
                    "COLLECTION SEHARUSNYA (SEBELUM PENERAPAN KEBIJAKAN)"=>array("colSpan"=>2),
                    "COLLECTION AKTUAL (SETELAH PENERAPAN KEBIJAKAN)"=>array("colSpan"=>2),
                    "+/-"=>array("colSpan"=>2),
                    "TOTAL UNIT YANG MENERIMA KEBIJAKAN"=>array("colSpan"=>2),
                )
            ),
            "showFooter"=>"bottom",
            "columns"=>array(
                "TITLE"=>array(
                    "label" => "DARI PENJUALAN",
                ),
                "UNIT_BEFORE"=>array(
                    "label" => "UNIT",
                ),
                "PAID_BILL_AMOUNT_NUM_HIST"=>array(
                    "label" => "COLLECTION RP (JUTA)",
                ),
                "UNIT_AFTER"=>array(
                    "label" => "UNIT",
                ),
                "PAID_BILL_AMOUNT_NUM"=>array(
                    "label" => "COLLECTION RP (JUTA)",
                ),
                "PERSEN"=>array(
                    "label" => "%",
                    "type" => "number",
                    "suffix"=> "%",
                ),
                "DIFF_AMOUNT"=>array(
                    "label" => "RP (JUTA)",
                ),
                "UNIT_REQ"=>array(
                    "label" => "UNIT",
                ),
                "AMOUNT_REQ"=>array(
                    "label" => "HARGA JUAL RP (JUTA)",
                ),
                
                "TITLE"=>array(
                    "footerText"=>"<p style='text-align: left;'><b>TOTAL</b></p>"
                ),
                "UNIT_BEFORE"=>array(
                    "footer"=>"sum",
                    "footerText"=>"<b>@value</b>",
                ),
                "PAID_BILL_AMOUNT_NUM_HIST"=>array(
                    "footer"=>"sum",
                    "footerText"=>"<b>@value</b>",
                ),
                "UNIT_AFTER"=>array(
                    "footer"=>"sum",
                    "footerText"=>"<b>@value</b>",
                ),
                "PAID_BILL_AMOUNT_NUM"=>array(
                    "footer"=>"sum",
                    "footerText"=>"<b>@value</b>",
                ),
                "PERSEN"=>array(
                    "footer"=>"sum",
                    "footerText"=>"<b>@value</b>",
                ),
                "DIFF_AMOUNT"=>array(
                    "footer"=>"sum",
                    "footerText"=>"<b>@value</b>",
                ),
                "UNIT_REQ"=>array(
                    "footer"=>"sum",
                    "footerText"=>"<b>@value</b>",
                ),
                "AMOUNT_REQ"=>array(
                    "footer"=>"sum",
                    "footerText"=>"<b>@value</b>",
                ),
            ),
            "themeBase"=>"bs4",
            "cssClass"=>array(
                "table"=>"table table-striped table-bordered"
            )
        ));
        ?>
    </body>
</html>