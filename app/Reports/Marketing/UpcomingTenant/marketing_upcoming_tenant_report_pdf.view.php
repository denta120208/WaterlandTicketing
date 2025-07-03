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
            <h1>Upcoming Tenant</h1>
            <h1><b>Based on deal <?php echo date('d F Y', strtotime($report->params['start_date'])) ?> - <?php echo date('d F Y', strtotime($report->params['cut_off'])) ?></b></h1>
        </div>
        <hr/>

        <?php
            Table::create(array(
                "dataSource"=> $report->dataStore('marketing_upcoming_tenant_table1'),
                "themeBase"=>"bs4",
                // "showFooter"=>"bottom",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "MD_TENANT_NAME_CHAR"=>array(
                        "label" => "TENANT"
                    ),
                    "PSM_CATEGORY_NAME"=>array(
                        "label" => "KATEGORI"
                    ),
                    "LOT_LEVEL_DESC"=>array(
                        "label" => "LOKASI"
                    ),
                    "LEASED_AREA"=>array(
                        "label" => "LUAS",
                        "suffix" => " m2"
                    ),
                    "PSM_TRANS_BOOKING_DATE"=>array(
                        "label" => "TANGGAL KESEPAKATAN"
                    ),
                    "PSM_TRANS_START_DATE"=>array(
                        "label" => "RENCANA BUKA"
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