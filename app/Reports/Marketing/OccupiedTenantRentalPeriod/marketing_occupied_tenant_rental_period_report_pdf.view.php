<?php 
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\pivot\widgets\PivotTable;
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
        .pivot-column {
            background-color: #081c5c;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 1%;
        }
    </style>

    <body style="margin:0.5in 1in 0.5in 1in">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <div class="page-header" style="text-align:right"><i>Marketing Report</i></div>
        <div class="page-footer" style="text-align:right">{pageNum}</div>
        <div class="text-center">
            <h1>Occupied Tenant Based On Rental Period</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Leased Area (Satuan m2)</b></span>
        <?php
            Table::create(array(
                "dataSource"=> $report->dataStore('marketing_occupied_tenant_rental_period_table1'),
                "themeBase"=>"bs4",
                "showFooter"=>"bottom",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "PERIODE_SEWA"=>array(
                        "label" => "PERIODE SEWA",
                        'type' => 'string',
                        "footerText"=>"<b>TOTAL</b>"
                    ),
                    "JUMLAH_TENANT"=>array(
                        "label" => "JUMLAH TENANT",
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "LEASED_AREA"=>array(
                        "label" => "LEASED AREA (m2)",
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "total_tenant_rental_period_percent"=>array(
                        "label" => "%",
                        "suffix" => "%",
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                )
            ));
        ?>

        <script type="text/javascript">
            $(document).ready(function() {
                $('.pivot-data-field-content').remove();
            });
        </script>
    </body>
</html>