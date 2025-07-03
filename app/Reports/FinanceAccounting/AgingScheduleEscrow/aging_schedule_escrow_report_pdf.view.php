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
            <h1>Aging Schedule Atas Escrow</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Angka Dalam Jutaan</b></span>
        <?php
            Table::create(array(
                "dataStore"=>$report->dataStore('finance_accounting_aging_schedule_escrow_table'),
                "headers"=>array(
                    array(
                        ""=>array("colSpan"=>1),
                        "0 - 30 HARI"=>array("colSpan"=>2),
                        "31 - 60 HARI"=>array("colSpan"=>2),
                        "61 - 90 HARI"=>array("colSpan"=>2),
                        "> 90 HARI"=>array("colSpan"=>2),
                        "TOTAL"=>array("colSpan"=>2),
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