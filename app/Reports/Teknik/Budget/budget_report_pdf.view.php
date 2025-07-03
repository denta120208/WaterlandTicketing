<?php 
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\pivot\widgets\PivotTable;
    use \koolreport\widgets\google\ColumnChart;
    use \koolreport\widgets\google\PieChart;
    use \koolreport\widgets\google\LineChart;
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
        .pivot-column {
            background-color: #081c5c;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 0.3%;
        }
    </style>

    <body style="margin:0.5in 1in 0.5in 1in">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <div class="page-header" style="text-align:right"><i>Teknik Report</i></div>
        <div class="page-footer" style="text-align:right">{pageNum}</div>
        <div class="text-center">
            <h1>Budget</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Rp (Dalam Jutaan)</b></span>
        <?php
            Table::create(array(
                "dataStore"=>$report->dataStore('teknik_budget_table1'),
                "headers"=>array(
                    array(
                        ""=>array("colSpan"=>1),
                        "Development Cost"=>array("colSpan"=>2),
                        "Perizinan"=>array("colSpan"=>2),
                        "Fixed Asset"=>array("colSpan"=>2),
                        "Construction Cost"=>array("colSpan"=>2),
                        "Total Budget ".(date('Y', strtotime($report->cut_off)))=>array("colSpan"=>2),
                    )
                ),
                "showFooter"=>"bottom",
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "TAHUN_SPK"=>array(
                        "label" => "Tahun SPK",
                        'type' => 'string',
                        "footerText"=>"<p><b>TOTAL</b></p>"
                    ),
                    "DEV_COST"=>array(
                        "label" => "Cash Out",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "total_dev_cost_percent"=>array(
                        "label" => "%",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "PERIZINAN"=>array(
                        "label" => "Cash Out",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "total_perizinan_percent"=>array(
                        "label" => "%",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "FIXED_ASSET"=>array(
                        "label" => "Cash Out",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "total_fixed_asset_percent"=>array(
                        "label" => "%",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "KONSTRUKSI"=>array(
                        "label" => "Cash Out",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "total_konstruksi_percent"=>array(
                        "label" => "%",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "TOTAL_BUDGET"=>array(
                        "label" => "Cash Out",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "total_budget_percent"=>array(
                        "label" => "%",
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

        <script type="text/javascript">
            $(document).ready(function() {
                $('.pivot-data-field-content').remove();
            });
        </script>
    </body>
</html>