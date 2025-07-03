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
            padding: 0.5%;
        }
    </style>

    <body style="margin:0.5in 1in 0.5in 1in">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <div class="page-header" style="text-align:right"><i>Marketing Report</i></div>
        <div class="page-footer" style="text-align:right">{pageNum}</div>
        <div class="text-center">
            <h1>Marketing Sales</h1>
        </div>
        <hr/>

        <?php
            ColumnChart::create(array(
                "title"=>"All Sales (Unit)",
                "dataSource" => $this->dataStore('marketing_sales_all_sales_chart_excel_pdf'),
                "columns" =>[
                    'PROJECT_NAME',
                    "total_unit"=>array(
                        "label"=>"Unit",                        
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
                            return number_format($row["total_unit"]);
                        }
                    )
                ],
                "options"=>array(
                    "legend"=>"none"
                ),
                // 'xAxisTitle' => 'category',
                // 'yAxisTitle' => 'jumlah',
                // 'stacked' => true,
                'direction' => 'vertical',
            ));
        ?>
        <h3><b>Sales Tahap/Sektor</b></h3>
        <?php
            Table::create(array(
                "dataSource"=> $this->dataStore('marketing_sales_tahap_sektor'),
                "themeBase"=>"bs4",
                "showFooter"=>"bottom",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
            ));
        ?>
        <div class="page-break"></div>
        <span style="color: red;"><b>* Rp (Dalam Jutaan), LT (Satuan m<sup>2</sup>), LB (Satuan m<sup>2</sup>)</b></span>
        <?php
            PivotTable::create(array(
                "dataStore"=>$this->dataStore('marketing_sales_table1'),
                'rowCollapseLevels' => array(0),
                'columnCollapseLevels' => array(0),
                'map' => array(
                    'dataHeader' => function($dataField, $fieldInfo) {
                        $v = $dataField;
                        if ($v === 'total_rp - sum')
                            $v = 'Rp (Juta)';
                        else if ($v === 'total_unit - sum')
                            $v = 'Unit';
                        else if ($v === 'total_lt - sum')
                            $v = 'LT (m'.'<sup>2</sup>)';
                        else if ($v === 'total_lb - sum')
                            $v = 'LB (m'.'<sup>2</sup>)';
                        else if ($v === 'growth_rp - sum')
                            $v = 'Growth';
                        else if ($v === 'growth_unit - sum')
                            $v = 'Growth';
                        else if ($v === 'growth_lt - sum')
                            $v = 'Growth';
                        else if ($v === 'growth_lb - sum')
                            $v = 'Growth';
                        return $v;
                    },
                    'columnHeader' => function($colHeader, $headerInfo) {
                        $v = $colHeader;
                        return $v;
                    },
                ),
                'hideTotalColumn' => true,
                'hideSubtotalRow' => true,
                'hideSubtotalColumn' => true,
                'showDataHeaders' => true,
                'totalName' => '<strong>TOTAL</strong>',
            ));
        ?>
        <div class="page-break"></div>
        <?php            
            ColumnChart::create(array(
                "title"=>"Realisasi (PER TAHUN)",
                "dataSource" => $this->dataStore('marketing_sales_chart_tahun_excel_pdf1'),
                "columns" =>[
                    'tahun',
                    "budget_sales"=>array(
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
                            return number_format($row["budget_sales"]);
                        }
                    )
                ],
                // 'xAxisTitle' => 'category',
                // 'yAxisTitle' => 'jumlah',
                // 'stacked' => true,
                'direction' => 'vertical',
            ));
        ?>
        <?php
            ColumnChart::create(array(
                "title"=>"Realisasi (PER BULAN) (".date('Y', strtotime($this->params['cut_off'])).")",
                "dataSource" => $this->dataStore('marketing_sales_chart_bulan_excel_pdf1'),
                "columns" =>[
                    'bulan',
                    "budget_sales"=>array(
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
                            return number_format($row["budget_sales"]);
                        }
                    )
                ],
                // 'xAxisTitle' => 'category',
                // 'yAxisTitle' => 'jumlah',
                // 'stacked' => true,
                'direction' => 'vertical',
            ));
        ?>
        <span style="color: red;"><b>* Rp (Dalam Jutaan)</b></span>
        <?php
            PivotTable::create(array(
                "dataStore"=>$this->dataStore('marketing_sales_table2'),
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
                'hideTotalRow' => true,
                'totalName' => '<strong>TOTAL</strong>',
            ));
        ?>
        <div class="page-break"></div>
        <?php
            ColumnChart::create(array(
                "title"=>strtoupper("Total Sales S/D ".date('d-M-Y', strtotime($this->params['cut_off']))),
                "dataSource" => $this->dataStore('marketing_sales_chart_excel_pdf2'),
                "columns" =>[
                    'tahun',
                    "total_sales"=>array(
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
                            return number_format($row["total_sales"]);
                        }
                    )
                ],
                // 'xAxisTitle' => 'category',
                // 'yAxisTitle' => 'jumlah',
                // 'stacked' => true,
                'direction' => 'vertical',
            ));
        ?>
        <span style="color: red;"><b>* Rp (Dalam Jutaan)</b></span>
        <?php
            PivotTable::create(array(
                "dataStore"=>$report->dataStore('marketing_sales_table3'),
                'rowCollapseLevels' => array(0),
                'columnCollapseLevels' => array(0),
                'map' => array(
                    'dataHeader' => function($dataField, $fieldInfo) {
                        $v = $dataField;
                        return $v;
                    },
                    'columnHeader' => function($colHeader, $headerInfo) {
                        $v = $colHeader;
                        return $v;
                    },
                ),
                'hideTotalRow' => true,
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