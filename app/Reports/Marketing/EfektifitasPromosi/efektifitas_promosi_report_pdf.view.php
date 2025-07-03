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
    </style>

    <body style="margin:0.5in 1in 0.5in 1in">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <div class="page-header" style="text-align:right"><i>Marketing Report</i></div>
        <div class="page-footer" style="text-align:right">{pageNum}</div>
        <div class="text-center">
            <h1>Efektifitas Promosi</h1>
        </div>
        <hr/>

        <?php            
            ColumnChart::create(array(
                "title"=>"Efektifitas Promosi (PER TAHUN)",
                "dataSource" => $this->dataStore('marketing_efektifitas_promosi_chart_excel_pdf_tahun'),
                "columns" =>[
                    'tahun',
                    "amount"=>array(
                        "style"=>function($row){
                            if(session('indexColorChart') == 1) {
                                session(['indexColorChart' => session('indexColorChart')+1]);
                                return "color:#4472c4";
                            }
                            else if(session('indexColorChart') == 2) {
                                session(['indexColorChart' => session('indexColorChart')+1]);
                                return "color:#ed7d31";
                            }
                            else {
                                session(['indexColorChart' => 1]);
                                return "color:#a5a5a5";
                            }
                        },
                        "annotation"=>function($row)
                        {
                            return number_format($row["amount"]);
                        }
                    )
                ],
                "options"=>array(
                    "legend"=>"none"
                )
                // 'xAxisTitle' => 'category',
                // 'yAxisTitle' => 'jumlah',
                // 'stacked' => true,
                // 'direction' => 'vertical',
            ));
        ?>
        <br />
        <?php            
            ColumnChart::create(array(
                "title"=>"Efektifitas Promosi ".date('Y', strtotime($this->params['cut_off']))." (PER BULAN)",
                "dataSource" => $this->dataStore('marketing_efektifitas_promosi_chart_excel_pdf_bulan'),
                "columns" =>[
                    'bulan',
                    "amount"=>array(
                        "style"=>function($row){
                            if(session('indexColorChart') == 1) {
                                session(['indexColorChart' => session('indexColorChart')+1]);
                                return "color:#4472c4";
                            }
                            else if(session('indexColorChart') == 2) {
                                session(['indexColorChart' => session('indexColorChart')+1]);
                                return "color:#ed7d31";
                            }
                            else {
                                session(['indexColorChart' => 1]);
                                return "color:#a5a5a5";
                            }
                        },
                        "annotation"=>function($row)
                        {
                            return number_format($row["amount"]);
                        }
                    )
                ],
                "options"=>array(
                    "legend"=>"none"
                )
                // 'xAxisTitle' => 'category',
                // 'yAxisTitle' => 'jumlah',
                // 'stacked' => true,
                // 'direction' => 'vertical',
            ));
        ?>
        <br />
        <?php
            PivotTable::create(array(
                "dataStore"=>$this->dataStore('marketing_efektifitas_promosi_table'),
                'rowCollapseLevels' => array(0),
                'columnCollapseLevels' => array(0),
                'map' => array(
                    'dataHeader' => function($dataField, $fieldInfo) {
                        $v = $dataField;
                        // if ($v === 'total_sales - sum')
                        //     $v = 'Rp (Juta)';
                        // else if ($v === 'total_unit - sum')
                        //     $v = 'Unit';
                        // else if ($v === 'total_sales_percent - sum')
                        //     $v = 'Rp (%)';
                        // else if ($v === 'total_unit_percent - sum')
                        //     $v = 'Unit (%)';
                        return $v;
                    },
                ),
                'hideTotalColumn' => true,
                'showDataHeaders' => false,
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