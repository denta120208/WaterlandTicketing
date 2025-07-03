<?php 
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\pivot\widgets\PivotTable;
    use \koolreport\widgets\google\ColumnChart;
    use \koolreport\widgets\google\PieChart;
    session(['indexColorChart' => 1]);
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
    </style>

    <body style="margin:0.5in 1in 0.5in 1in">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <div class="page-header" style="text-align:right"><i>Marketing Report</i></div>
        <div class="page-footer" style="text-align:right">{pageNum}</div>
        <div class="text-center">
            <h1>Data Penyebaran Wilayah Member Metland Card</h1>
        </div>
        <hr/>

        <center><h3><b>Data Penyebaran Wilayah (Demografi <?php echo session('current_project_char_commercial') ?>)</b></h3></center>
        <br /><br />
        <?php
            PieChart::create(array(
                "title"=>"",
                "dataSource" => $this->dataStore('marketing_demografi_metland_card_chart1'),
                "columns" =>[
                    "kota",
                    "JUMLAH_MEMBER"=>array(
                        "label"=>"MEMBER",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['JUMLAH_MEMBER'];
                        }
                    ),
                ],
                "options"=>array(
                    "is3D"=>true,
                    "chartArea"=>array(
                        'height'=>'200%'
                    )
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