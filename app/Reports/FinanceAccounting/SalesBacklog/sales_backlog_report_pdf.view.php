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
            <h1>Sales Backlog</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Angka Dalam Jutaan</b></span>
        <?php
            Table::create(array(
                "dataStore"=>$report->dataStore('finance_accounting_sales_backlog_table'),
                "headers"=>array(
                    array(
                        ""=>array("colSpan"=>1, "rowSpan"=>2),
                        "PENGAKUAN PENJUALAN ATAS BACKLOG"=>array("colSpan"=>8),
                        "SALDO PER ".strtoupper(date('M Y', strtotime($this->params['cut_off'])))=>array("colSpan"=>2, "rowSpan"=>2),
                    ),
                    array(
                        (date('Y', strtotime($this->params['cut_off'])))=>array("colSpan"=>2),
                        (date('Y', strtotime($this->params['cut_off'])) + 1)=>array("colSpan"=>2),
                        (date('Y', strtotime($this->params['cut_off'])) + 2)=>array("colSpan"=>2),
                        "> ".(date('Y', strtotime($this->params['cut_off'])) + 2)=>array("colSpan"=>2),
                    )
                ),
                "showFooter"=>"bottom",
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                'options' => [
                    'ordering' => false
                ]
            ));
        ?>
    </body>
</html>