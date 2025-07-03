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
            <h1>Pembatalan</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Rp (Dalam Jutaan)</b></span>
        <?php 
            PivotTable::create(array(
                "dataStore"=>$report->dataStore('marketing_pembatalan1'),
                'rowCollapseLevels' => array(0),
                'columnCollapseLevels' => array(0),
                'map' => array(
                    'dataHeader' => function($dataField, $fieldInfo) {
                        $v = $dataField;
                        if ($v === 'total_sales - sum')
                            $v = 'Rp (Juta)';
                        else if ($v === 'total_unit - sum')
                            $v = 'Unit';
                        return $v;
                    },
                ),
                'hideTotalColumn' => true,
                'showDataHeaders' => true,
                'totalName' => '<strong>TOTAL PEMBATALAN</strong>',
            ));
        ?>
        <br />
        <span style="color: red;"><b>* Rp (Dalam Jutaan)</b></span>
        <?php
            PivotTable::create(array(
                "dataStore"=>$report->dataStore('marketing_pembatalan2'),
                'rowCollapseLevels' => array(0),
                'columnCollapseLevels' => array(0),
                'map' => array(
                    'dataHeader' => function($dataField, $fieldInfo) {
                        $v = $dataField;
                        if ($v === 'total_sales - sum')
                            $v = 'Rp (Juta)';
                        else if ($v === 'total_unit - sum')
                            $v = 'Unit';
                        return $v;
                    },
                ),
                'hideTotalColumn' => true,
                'showDataHeaders' => true,
                'totalName' => '<strong>TOTAL PEMBATALAN</strong>',
            ));
        ?>

        <script type="text/javascript">
            $(document).ready(function() {
                $('.pivot-data-field-content').remove();
            });
        </script>
    </body>
</html>