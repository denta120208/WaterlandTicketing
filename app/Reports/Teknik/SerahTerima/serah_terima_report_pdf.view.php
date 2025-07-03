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
            <h1>Serah Terima</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Rp (Dalam Jutaan)</b></span>
        <?php
            Table::create(array(
                "dataStore"=>$report->dataStore('teknik_serah_terima_table1'),
                "headers"=>array(
                    array(
                        ""=>array("colSpan"=>1),
                        "ST S/D"=>array("colSpan"=>1),
                        date('Y', strtotime($report->cut_off))=>array("colSpan"=>4),
                        (date('Y', strtotime($report->cut_off)) + 1)=>array("colSpan"=>1),
                        "KUMULATIF S/D ".strtoupper(date('M Y', strtotime($report->cut_off)))=>array("colSpan"=>2),
                    )
                ),
                // "showFooter"=>"bottom",
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "TOWER_NAME"=>array(
                        "label" => "KAWASAN",
                        'type' => 'string'
                    ),
                    "REALISASI_COUNT_BACKWARD_YEAR"=>array(
                        "label" => strtoupper(date('M', strtotime($report->cut_off))).' '.(date('Y', strtotime($report->cut_off)) - 1),
                        'type' => 'string'
                    ),
                    "TARGET_COUNT_CURRENT_YEAR"=>array(
                        "label" => "T",
                        'type' => 'string'
                    ),
                    "REALISASI_COUNT_CURRENT_YEAR"=>array(
                        "label" => "R",
                        'type' => 'string'
                    ),
                    "PERSEN_CURRENT_YEAR"=>array(
                        "label" => "P%",
                        'type' => 'string',
                        'suffix' => "%"
                    ),
                    "HUNI_BANGUN_CURRENT_YEAR"=>array(
                        "label" => "HUNI/BANGUN",
                        'type' => 'string'
                    ),
                    "TARGET_ST_NEXT_YEAR"=>array(
                        "label" => "TARGET ST",
                        'type' => 'string'
                    ),
                    "KUMULATIF_CUTOFF_CURRENT_YEAR"=>array(
                        "label" => "REAL ST",
                        'type' => 'string'
                    ),
                    "KUMULATIF_HUNI_BANGUN_CURRENT_YEAR"=>array(
                        "label" => "HUNI/BANGUN",
                        'type' => 'string'
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