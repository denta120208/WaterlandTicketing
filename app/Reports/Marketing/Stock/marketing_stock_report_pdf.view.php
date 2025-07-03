<?php 
    use \koolreport\widgets\koolphp\Table;
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
            <h1>Stock</h1>
        </div>
        <hr/>

        <?php
            Table::create(array(
                "dataStore"=>$report->dataStore('marketing_stock_pdf_table'),
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                'options' => [
                    'ordering' => false
                ]
                // 'map' => array(
                //     'dataHeader' => function($dataField, $fieldInfo) {
                //         $v = $dataField;
                //         if ($v === 'TOTAL_UNIT - sum')
                //             $v = 'Unit';
                //         else if ($v === 'TOTAL_RP - sum')
                //             $v = 'Rp (Juta)';
                //         return $v;
                //     },
                // ),
            ));
        ?>
        <script type="text/javascript">
            $(document).ready(function() {
                $('.pivot-data-field-content').remove();
            });
        </script>
    </body>
</html>