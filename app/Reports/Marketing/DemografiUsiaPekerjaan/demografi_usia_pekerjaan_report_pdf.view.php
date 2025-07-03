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
    </style>

    <body style="margin:0.5in 1in 0.5in 1in">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <div class="page-header" style="text-align:right"><i>Marketing Report</i></div>
        <div class="page-footer" style="text-align:right">{pageNum}</div>
        <div class="text-center">
            <h1>Demografi Usia & Pekerjaan</h1>
        </div>
        <hr/>

        <?php
            ColumnChart::create(array(
                "title"=>"Demografi Pekerjaan",
                "dataSource"=>session('demografi_usia_pekerjaan_chart_pekerjaan'),
                "columns"=>array(
                    "PEKERJAAN_CHAR",
                    (date('Y', strtotime($this->params['cut_off']))-1)=>array(
                        "label"=>(date('Y', strtotime($this->params['cut_off']))-1),
                        "type"=>"number",
                        "annotation"=>function($row)
                        {
                            return number_format($row[(date('Y', strtotime(session('demografi_usia_pekerjaan_chart_cut_off')))-1)]);
                        }
                    ),
                    date('Y', strtotime($this->params['cut_off']))=>array(
                        "label"=>date('Y', strtotime($this->params['cut_off'])),
                        "type"=>"number",
                        "annotation"=>function($row)
                        {
                            if(array_key_exists(date('Y', strtotime($this->params['demografi_usia_pekerjaan_chart_cut_off'])), $row)) {
                                return number_format($row[date('Y', strtotime($this->params['demografi_usia_pekerjaan_chart_cut_off']))]);
                            }
                            else {
                                return number_format(0);
                            }
                        }
                    ),
                )
            ));
        ?>
        <br />
        <?php
            ColumnChart::create(array(
                "title"=>"Demografi Usia",
                "dataSource"=>session('demografi_usia_pekerjaan_chart_age'),
                "columns"=>array(
                    "TAHUN",
                    "17-30"=>array(
                        "label"=>"17-30",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['17-30'];
                        }
                    ),
                    "31-40"=>array(
                        "label"=>"31-40",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['31-40'];
                        }
                    ),
                    "41-55"=>array(
                        "label"=>"41-55",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['41-55'];
                        }
                    ),
                    ">56"=>array(
                        "label"=>"> 56",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['>56'];
                        }
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