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
            <h1>Hutang Bangun</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Rp (Dalam Jutaan)</b></span>
        <?php
            Table::create(array(
                "dataStore"=>$report->dataStore('teknik_hutang_bangun_table1'),
                "headers"=>array(
                    array(
                        ""=>array("colSpan"=>1),
                        "Development Cost"=>array("colSpan"=>2),
                        "Perizinan"=>array("colSpan"=>2),
                        "Fixed Asset"=>array("colSpan"=>2),
                        "Construction Cost"=>array("colSpan"=>2),
                        "Total"=>array("colSpan"=>2),
                    )
                ),
                "showFooter"=>"bottom",
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "TAHUN"=>array(
                        "label" => "Tahun",
                        'type' => 'string',
                        "footerText"=>"<p><b>TOTAL</b></p>"
                    ),
                    "SPK_KELUAR_DEV_COST"=>array(
                        "label" => "SPK Keluar",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "CASHOUT_DEV_COST"=>array(
                        "label" => "Rencana Cash Out",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "SPK_KELUAR_PERIZINAN"=>array(
                        "label" => "SPK Keluar",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "CASHOUT_PERIZINAN"=>array(
                        "label" => "Rencana Cash Out",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "SPK_KELUAR_FIXED_ASSET"=>array(
                        "label" => "SPK Keluar",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "CASHOUT_FIXED_ASSET"=>array(
                        "label" => "Rencana Cash Out",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "SPK_KELUAR_KONSTRUKSI"=>array(
                        "label" => "SPK Keluar",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "CASHOUT_KONSTRUKSI"=>array(
                        "label" => "Rencana Cash Out",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "TOTAL_SPK_KELUAR"=>array(
                        "label" => "SPK Keluar",
                        'type' => 'number',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>"
                    ),
                    "TOTAL_CASHOUT"=>array(
                        "label" => "Rencana Cash Out",
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