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
            <h1>Konsumsi Gas</h1>
        </div>
        <hr/>

        <center><h2><b>KONSUMSI GAS <?php echo strtoupper(date('Y', strtotime($this->params['cut_off']))) ?></b></h2></center>
        <span style="color: red;"><b>* Rp (Dalam Ribuan)</b></span>
        <?php
            PivotTable::create(array(
                "dataStore"=>$this->dataStore('teknik_konsumsi_gas_table1'),
                'rowCollapseLevels' => array(0),
                'columnCollapseLevels' => array(0),
                'map' => array(
                    'dataHeader' => function($dataField, $fieldInfo) {
                        $v = $dataField;
                        return $v;
                    },
                    'columnHeader' => function($colHeader, $headerInfo) {
                        $v = $colHeader;
                        $bulanStr = strtoupper(DateTime::createFromFormat('!m', $v)->format('M'));
                        return $bulanStr;
                    },
                ),
                'hideTotalColumn' => true,
                'hideSubtotalRow' => true,
                'hideSubtotalColumn' => true,
                'hideTotalRow' => true,
                'showDataHeaders' => false,
                'totalName' => '<strong>TOTAL</strong>',
            ));
        ?>
        <br /><br /><br />
        <?php
            LineChart::create(array(
                "title"=>"Konsumsi Gas ".date('Y', strtotime($report->params['cut_off']))." (Ribu)",
                "dataSource"=>$report->dataStore('teknik_konsumsi_gas_chart1'),
                "columns"=>array(
                    // "BULAN",
                    "BULAN"=>array(
                        "label"=>"BULAN",
                        "annotation"=>function($row) {
                            $row['BULAN'] = strtoupper(DateTime::createFromFormat('!m', $row['BULAN'])->format('M'));
                            return $row['BULAN'];
                        }
                    ),
                    "PENDAPATAN_TENANT"=>array(
                        "label"=>"Pendapatan Tenant",
                    ),
                    "HPP"=>array(
                        "label"=>"HPP",
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