<?php 
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\pivot\widgets\PivotTable;
    use \koolreport\widgets\google\ColumnChart;
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
            <h1>Growth Updated Member Metland Card</h1>
        </div>
        <hr/>

        <?php
            Table::create(array(
                "dataSource"=> $this->dataStore('marketing_growth_member_metland_card_table1'),
                "themeBase"=>"bs4",
                "showFooter"=>"bottom",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "project"=>array(
                        'label' => '',
                        "footerText"=>"<b>TOTAL</b>"
                    ),
                    "JUMLAH_MEMBER"=>array(
                        'label' => 'MEMBER',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                )
            ));
        ?>
        <div class="page-break"></div>
        <h5><b>NEW MEMBER <?php echo session('current_project_char_commercial') ?> YTD <?php echo strtoupper(date('M Y', strtotime($this->params['cut_off']))) ?></b></h5>
        <?php
            Table::create(array(
                "dataSource"=> $this->dataStore('marketing_growth_member_metland_card_table2'),
                "themeBase"=>"bs4",
                "showFooter"=>"bottom",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "bulan"=>array(
                        'label' => 'BULAN',
                        'formatValue'=>function($value, $row){
                            // Merubah angka menjadi format bulan
                            $value = strtoupper(DateTime::createFromFormat('!m', $value)->format('F'));
                            return $value;
                        },
                        "footerText"=>"<b>TOTAL</b>"
                    ),
                    "JUMLAH_MEMBER"=>array(
                        'label' => 'JUMLAH',
                        "footer"=>"sum",
                        "footerText"=>"<b>@value</b>",
                    ),
                )
            ));
        ?>
        <div class="page-break"></div>
        <?php
            ColumnChart::create(array(
                "title"=>"Growth Updated Member Metland Card (".date('Y', strtotime($this->params['cut_off'])).")",
                "dataSource" => session('growth_member_metland_card_chart1'),
                "columns" =>[
                    "bulan"=>array(
                        'formatValue'=>function($value, $row){
                            // Merubah angka menjadi format bulan
                            $value = strtoupper(DateTime::createFromFormat('!m', $value)->format('M'));
                            return $value;
                        }
                    ),
                    "GrandMetropolitan Mall"=>array(
                        "label"=>"GMM",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['GrandMetropolitan Mall'];
                        }
                    ),
                    "Metropolitan Mall"=>array(
                        "label"=>"MM",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['Metropolitan Mall'];
                        }
                    ),
                    "Mall Metropolitan Cileungsi"=>array(
                        "label"=>"MMC",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['Mall Metropolitan Cileungsi'];
                        }
                    ),
                    "Hotel"=>array(
                        "label"=>"HOTEL",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['Hotel'];
                        }
                    ),
                    "Residential"=>array(
                        "label"=>"RESIDENTIAL",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['Residential'];
                        }
                    ),
                    "EVENT"=>array(
                        "label"=>"EVENT",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['EVENT'];
                        }
                    ),
                    "Head Office"=>array(
                        "label"=>"HEAD OFFICE",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['Head Office'];
                        }
                    ),
                ],
                // 'xAxisTitle' => 'category',
                // 'yAxisTitle' => 'jumlah',
                // 'stacked' => true,
                'direction' => 'vertical',
            ));
        ?>

        <script type="text/javascript">
            $(document).ready(function() {
                $('.pivot-data-field-content').remove();
            });
        </script>
    </body>
</html>