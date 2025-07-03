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
            <h1>Konsumsi Air Gedung</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Rp (Dalam Ribuan)</b></span>
        <?php
            PivotTable::create(array(
                "dataStore"=>$this->dataStore('teknik_konsumsi_air_gedung_table1'),
                'rowCollapseLevels' => array(0),
                'columnCollapseLevels' => array(0),
                'map' => array(
                    'dataHeader' => function($dataField, $fieldInfo) {
                        $v = $dataField;
                        if ($v === 'NOMINAL_RP_NUM - sum')
                            $v = 'Rp';
                        else if ($v === 'NOMINAL_M3_NUM - sum')
                            $v = 'M3';
                        return $v;
                    },
                    'columnHeader' => function($colHeader, $headerInfo) {
                        $v = $colHeader;
                        $bulanStr = strtoupper(DateTime::createFromFormat('!m', $v)->format('M'));
                        return $bulanStr;
                    },
                ),
                'hideTotalColumn' => false,
                'hideSubtotalRow' => true,
                'hideSubtotalColumn' => true,
                'hideTotalRow' => true,
                'showDataHeaders' => true,
                'totalName' => '<strong>TOTAL</strong>',
            ));
        ?>
        <br /><br /><br />
        <?php
            LineChart::create(array(
                "title"=>"Konsumsi Air Gedung - Rp (Ribu)",
                "dataSource"=>$report->dataStore('teknik_konsumsi_air_gedung_chart1'),
                "columns"=>array(
                    // "BULAN",
                    "BULAN"=>array(
                        "label"=>"BULAN",
                        "annotation"=>function($row) {
                            $row['BULAN'] = strtoupper(DateTime::createFromFormat('!m', $row['BULAN'])->format('M'));
                            return $row['BULAN'];
                        }
                    ),
                    session('konsumsiAirGedungYear3Chart1')=>array(
                        "label"=>session('konsumsiAirGedungYear3Chart1'),
                    ),
                    session('konsumsiAirGedungYear2Chart1')=>array(
                        "label"=>session('konsumsiAirGedungYear2Chart1'),
                    ),
                    session('konsumsiAirGedungYear1Chart1')=>array(
                        "label"=>session('konsumsiAirGedungYear1Chart1'),
                    ),
                )
            ));
        ?>        
        <?php
            LineChart::create(array(
                "title"=>"Konsumsi Air Gedung - M3",
                "dataSource"=>$report->dataStore('teknik_konsumsi_air_gedung_chart2'),
                "columns"=>array(
                    // "BULAN",
                    "BULAN"=>array(
                        "label"=>"BULAN",
                        "annotation"=>function($row) {
                            $row['BULAN'] = strtoupper(DateTime::createFromFormat('!m', $row['BULAN'])->format('M'));
                            return $row['BULAN'];
                        }
                    ),
                    session('konsumsiAirGedungYear3Chart2')=>array(
                        "label"=>session('konsumsiAirGedungYear3Chart2'),
                    ),
                    session('konsumsiAirGedungYear2Chart2')=>array(
                        "label"=>session('konsumsiAirGedungYear2Chart2'),
                    ),
                    session('konsumsiAirGedungYear1Chart2')=>array(
                        "label"=>session('konsumsiAirGedungYear1Chart2'),
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