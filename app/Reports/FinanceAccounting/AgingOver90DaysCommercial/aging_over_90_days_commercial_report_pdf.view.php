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
            <h1>Aging > 90 Days (Commercial)</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Angka Dalam Jutaan</b></span>
        <?php
            Table::create(array(
                "dataStore"=>$this->dataStore('finance_accounting_aging_over_90_days_commercial_table1'),
                "showFooter"=>"bottom",
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "DESC_TYPE"=>array(
                        "label" => "DESCRIPTION",
                        "type" => "string",
                        "footerText"=>"<b>TOTAL</b>"
                    ),
                    "AGING_BACKWARD_YEAR"=>array(
                        "label" => (date('Y', strtotime($this->params['cut_off']))-1),
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "AGING_CURRENT_YEAR"=>array(
                        "label" => date('Y', strtotime($this->params['cut_off'])),
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "PERSEN"=>array(
                        "label" => "VAR (%)",
                        "suffix" => "%",
                        "footerText"=>"<b>-</b>",
                    ),
                ),
                'options' => [
                    'ordering' => false
                ]
            ));
        ?>
    </body>
</html>