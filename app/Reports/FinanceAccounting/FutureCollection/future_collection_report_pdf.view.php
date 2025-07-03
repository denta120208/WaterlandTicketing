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
            <h1>Future Collection</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Angka Dalam Jutaan, Exclude PPN</b></span>
        <?php
        Table::create(array(
            "dataStore"=>$report->dataStore('finance_accounting_future_collection_table'),
            "headers"=>array(
                array(
                    ""=>array("colSpan"=>1),
                    "FUTURE COLLECTION"=>array("colSpan"=>5),
                )
            ),
            "showFooter"=>"bottom",
            "themeBase"=>"bs4",
            "cssClass"=>array(
                "table"=>"table table-striped table-bordered"
            ),
            "columns"=>array(
                "TAHUN"=>array(
                    "label" => "",
                    "type" => "string",
                ),
                "sisa1"=>array(
                    "label" => date('Y', strtotime($this->params['cut_off'])),
                ),
                "sisa2"=>array(
                    "label" => (date('Y', strtotime($this->params['cut_off'])) + 1),
                ),
                "sisa3"=>array(
                    "label" => (date('Y', strtotime($this->params['cut_off'])) + 2),
                ),
                "sisa5"=>array(
                    "label" => ">".(date('Y', strtotime($this->params['cut_off'])) + 2),
                ),
                "TOTAL_SISA"=>array(
                    "label" => "TOTAL",
                    "type" => "number",
                ),
                
                "TAHUN"=>array(
                    "footerText"=>"<p style='text-align: left;'><b>NET FUTURE COLLECTION</b></p>"
                ),
                "sisa1"=>array(
                    "footer"=>"sum",
                    "footerText"=>"<b>@value</b>",
                ),
                "sisa2"=>array(
                    "footer"=>"sum",
                    "footerText"=>"<b>@value</b>",
                ),
                "sisa3"=>array(
                    "footer"=>"sum",
                    "footerText"=>"<b>@value</b>",
                ),
                "sisa5"=>array(
                    "footer"=>"sum",
                    "footerText"=>"<b>@value</b>",
                ),
                "TOTAL_SISA"=>array(
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