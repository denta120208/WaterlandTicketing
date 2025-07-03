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
            <h1>Collection & Aging Schedule (Commercial)</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Angka Dalam Jutaan</b></span>
        <?php
            Table::create(array(
                "dataStore"=>$this->dataStore('finance_accounting_collection_aging_schedule_commercial_table1'),
                "headers"=>array(
                    array(
                        ""=>array("colSpan"=>1),
                        (date('Y', strtotime($this->params['cut_off']))-1)=>array("colSpan"=>1),
                        date('Y', strtotime($this->params['cut_off']))=>array("colSpan"=>4),
                    )
                ),
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
                    "COLLECTED_BACKWARD_YEAR"=>array(
                        "label" => "COLLECTED",
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "COLLECTED_CURRENT_YEAR"=>array(
                        "label" => "COLLECTED",
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "AGING_CURRENT_YEAR"=>array(
                        "label" => "TOTAL AGING",
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "TARGET_COLLECTED_CURRENT_YEAR"=>array(
                        "label" => "TARGET COLLECTED",
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "COLLECTABILITY_CURRENT_YEAR"=>array(
                        "label" => "COLLECTABILITY",
                        "suffix" => "%",
                        "footerText"=>"<b>-</b>"
                    ),
                ),
                'options' => [
                    'ordering' => false
                ]
            ));
        ?>
    </body>
</html>