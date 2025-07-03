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
        <div class="page-header" style="text-align:right"><i>Marketing Report</i></div>
        <div class="page-footer" style="text-align:right">{pageNum}</div>
        <div class="text-center">
            <h1>Occupancy</h1>
        </div>
        <hr/>

        <?php
            Table::create(array(
                "dataStore"=>$report->dataStore('marketing_accupancy_table'),
                "headers"=>array(
                    array(
                        ""=>array("colSpan"=>4),
                        "NETT LEASED"=>array("colSpan"=>2),
                        "AVAILABLE AREA"=>array("colSpan"=>2),
                    )
                ),
                "showFooter"=>"bottom",
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "PSM_CATEGORY_NAME"=>array(
                        "label" => "TENANT CATEGORY",
                        "type" => "string",
                        "footerText"=>"<b>TOTAL</b>"
                    ),
                    "ANCHOR_TENANT"=>array(
                        "label" => "ANCHOR TENANTS (>=500m2)",
                        "type" => "number",
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "NON_ANCHOR_TENANT"=>array(
                        "label" => "NON ANCHOR TENANTS (<500m2)",
                        "type" => "number",
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "LEASEABLE_AREA"=>array(
                        "label" => "LEASEABLE AREA (m2)",
                        "type" => "number",
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "NET_LEASED_LUAS"=>array(
                        "label" => "LUAS",
                        "type" => "number",
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "NET_LEASED_LUAS_PERSEN"=>array(
                        "label" => "%",
                        "type" => "number",
                        "suffix" => "%",
                        "footerText"=>"<b>-</b>"
                    ),
                    "AVAILABLE_AREA"=>array(
                        "label" => "LUAS",
                        "type" => "number",
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                    "AVAILABLE_AREA_PERSEN"=>array(
                        "label" => "%",
                        "type" => "number",
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