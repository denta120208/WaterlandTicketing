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
            <h1>Data KPR</h1>
        </div>
        <hr/>

        <h5><b>REALISASI AKAD</b></h5>
        <span style="color: red;"><b>* Rp (Dalam Jutaan)</b></span>
        <?php
            PivotTable::create(array(
                "dataStore"=>$report->dataStore('marketing_realisasi_akad_table'),
                'rowCollapseLevels' => array(0),
                'columnCollapseLevels' => array(0),
                'map' => array(
                    'dataHeader' => function($dataField, $fieldInfo) {
                        $v = $dataField;
                        if ($v === 'total_amount - sum')
                            $v = 'Rp (Juta)';
                        else if ($v === 'total_unit - sum')
                            $v = 'Unit';
                        else if ($v === 'total_percent - sum')
                            $v = '%';
                        else if ($v === 'growth_amount - sum')
                            $v = 'Growth';
                        else if ($v === 'growth_unit - sum')
                            $v = 'Growth';
                        return $v;
                    },
                ),
                'hideTotalColumn' => true,
                'showDataHeaders' => true,
                'totalName' => '<strong>TOTAL</strong>',
            ));
        ?>
        <div class="page-break"></div>
        <h5><b>MONITORING APLIKASI KPR</b></h5>
        <span style="color: red;"><b>* Rp (Dalam Jutaan)</b></span>
        <?php
            PivotTable::create(array(
                "dataStore"=>$report->dataStore('marketing_monitoring_aplikasi_kpr_table'),
                'rowCollapseLevels' => array(0),
                'columnCollapseLevels' => array(0),
                'map' => array(
                    'dataHeader' => function($dataField, $fieldInfo) {
                        $v = $dataField;
                        if ($v === 'total_amount - sum')
                            $v = 'Rp (Juta)';
                        else if ($v === 'total_unit - sum')
                            $v = 'Unit';
                        return $v;
                    },
                ),
                'hideTotalColumn' => true,
                'showDataHeaders' => true,
                'totalName' => '<strong>TOTAL</strong>',
            ));
        ?>

        <script type="text/javascript">
            $(document).ready(function() {
                $('.pivot-data-field-content').remove();
            });
        </script>
    </body>
</html>