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
            <h1>Occupied Rental & Service Charge (Based On Contract)</h1>
        </div>
        <hr/>

        <h3><b>OCCUPIED RENTAL/MONTH</b></h3>
        <span style="color: red;"><b>* Rental Revenue (Dalam Jutaan), Leased Area (Satuan m2)</b></span>
        <?php
            PivotTable::create(array(
                "dataStore"=>$report->dataStore('marketing_occupied_rental_sc_table1'),
                'rowCollapseLevels' => array(0),
                'columnCollapseLevels' => array(0),
                'map' => array(
                    'dataHeader' => function($dataField, $fieldInfo) {
                        $v = $dataField;
                        if ($v === 'LEASED_AREA - sum')
                            $v = 'LEASED AREA (m2)';
                        else if ($v === 'RENTAL_REVENUE - sum')
                            $v = 'RENTAL REVENUE (JUTA)';
                        else if ($v === 'ARR - sum')
                            $v = 'ARR';
                        return $v;
                    },
                ),
                'hideTotalColumn' => true,
                'showDataHeaders' => true,
                'totalName' => '<strong>TOTAL</strong>',
            ));
        ?>
        <br />
        <h3><b>OCCUPIED SERVICE CHARGE/MONTH</b></h3>
        <span style="color: red;"><b>* SC Revenue (Dalam Jutaan), Leased Area (Satuan m2)</b></span>
        <?php
            PivotTable::create(array(
                "dataStore"=>$report->dataStore('marketing_occupied_rental_sc_table2'),
                'rowCollapseLevels' => array(0),
                'columnCollapseLevels' => array(0),
                'map' => array(
                    'dataHeader' => function($dataField, $fieldInfo) {
                        $v = $dataField;
                        if ($v === 'LEASED_AREA - sum')
                            $v = 'LEASED AREA (m2)';
                        else if ($v === 'SC_REVENUE - sum')
                            $v = 'SC REVENUE (JUTA)';
                        else if ($v === 'ARR - sum')
                            $v = 'ARR';
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