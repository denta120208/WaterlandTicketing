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
            <h1>Analisa NPV</h1>
        </div>
        <hr/>

        <h5><b>Harga Jual Tanah Rata2/m2</b></h5>
        <span style="color: red;"><b>* Rp (Dalam Ribuan) | Interest Rate : <?php echo (float) $this->interest_rate . '%'; ?></b></span>
        <?php
            PivotTable::create(array(
                "dataStore"=>$this->dataStore('marketing_analisa_npv_table_land'),
                'rowCollapseLevels' => array(0),
                'columnCollapseLevels' => array(0),
                'map' => array(
                    'dataHeader' => function($dataField, $fieldInfo) {
                        $v = $dataField;
                        if ($v === 'NPV_TANAH - sum')
                            $v = 'NPV (Rp/m2)';
                        else if ($v === 'ALL_PAYMENT_TANAH - sum')
                            $v = 'All Payment (Rp/m2)';
                        else if ($v === 'GROWTH_NPV - sum')
                            $v = 'Growth NPV (%)';
                        else if ($v === 'GROWTH_ALL_PAYMENT - sum')
                            $v = 'Growth All Payment (%)';
                        return $v;
                    },
                ),
                'hideTotalColumn' => true,
                'showDataHeaders' => true,
                'hideTotalRow' => true,
                // 'totalName' => '<strong>TOTAL</strong>',
            ));
        ?>
        <p style="text-align: right;">
            <b style="border-style: groove;">
                &emsp; Harga Jual Tanah Rata2/m2 
                &emsp;&emsp;&emsp;
                NPV: <?php echo $this->params["npvLand"] ?> 
                &emsp;&emsp;&emsp;
                All Payment: <?php echo $this->params["paymentLand"] ?> &emsp;
            </b>
        </p>
        <br />
        <h5><b>Harga Jual Bangunan Rata2/m2</b></h5>
        <span style="color: red;"><b>* Rp (Dalam Ribuan) | Interest Rate : <?php echo (float) $this->interest_rate . '%'; ?></b></span>
        <?php
            PivotTable::create(array(
                "dataStore"=>$this->dataStore('marketing_analisa_npv_table_build'),
                'rowCollapseLevels' => array(0),
                'columnCollapseLevels' => array(0),
                'map' => array(
                    'dataHeader' => function($dataField, $fieldInfo) {
                        $v = $dataField;
                        if ($v === 'NPV_BANGUNAN - sum')
                            $v = 'NPV (Rp/m2)';
                        else if ($v === 'ALL_PAYMENT_BANGUNAN - sum')
                            $v = 'All Payment (Rp/m2)';
                        else if ($v === 'GROWTH_NPV - sum')
                            $v = 'Growth NPV (%)';
                        else if ($v === 'GROWTH_ALL_PAYMENT - sum')
                            $v = 'Growth All Payment (%)';
                        return $v;
                    },
                ),
                'hideTotalColumn' => true,
                'showDataHeaders' => true,
                'hideTotalRow' => true,
                // 'totalName' => '<strong>TOTAL</strong>',
            ));
        ?>
        <p style="text-align: right;">
            <b style="border-style: groove;">
                &emsp; Harga Jual Building Rata2/m2 
                &emsp;&emsp;&emsp;
                NPV: <?php echo $this->params["npvBuild"] ?> 
                &emsp;&emsp;&emsp;
                All Payment: <?php echo $this->params["paymentBuild"] ?> &emsp;
            </b>
        </p>

        <script type="text/javascript">
            $(document).ready(function() {
                $('.pivot-data-field-content').remove();
            });
        </script>
    </body>
</html>