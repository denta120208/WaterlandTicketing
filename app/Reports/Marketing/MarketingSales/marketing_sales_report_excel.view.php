<?php
    use \koolreport\excel\Table;
    use \koolreport\excel\PivotTable;
    use \koolreport\excel\BarChart;
    use \koolreport\excel\LineChart;

    $sheet1 = "Table";
    $sheet2 = "Chart";
?>
<meta charset="UTF-8">

<div sheet-name="<?php echo $sheet1; ?>">
    <?php
    $styleArray = [
        'font' => [
            'name' => 'Calibri', //'Verdana', 'Arial'
            'size' => 30,
            'bold' => true,
            'italic' => FALSE,
            'underline' => 'none', //'double', 'doubleAccounting', 'single', 'singleAccounting'
            'strikethrough' => FALSE,
            'superscript' => false,
            'subscript' => false,
            'color' => [
                'rgb' => '000000',
                'argb' => 'FF000000',
            ]
        ],
        'alignment' => [
            'horizontal' => 'general',//left, right, center, centerContinuous, justify, fill, distributed
            'vertical' => 'bottom',//top, center, justify, distributed
            'textRotation' => 0,
            'wrapText' => false,
            'shrinkToFit' => false,
            'indent' => 0,
            'readOrder' => 0,
        ],
        'borders' => [
            'top' => [
                'borderStyle' => 'none', //dashDot, dashDotDot, dashed, dotted, double, hair, medium, mediumDashDot, mediumDashDotDot, mediumDashed, slantDashDot, thick, thin
                'color' => [
                    'rgb' => '808080',
                    'argb' => 'FF808080',
                ]
            ],
            //left, right, bottom, diagonal, allBorders, outline, inside, vertical, horizontal
        ],
        'fill' => [
            'fillType' => 'none', //'solid', 'linear', 'path', 'darkDown', 'darkGray', 'darkGrid', 'darkHorizontal', 'darkTrellis', 'darkUp', 'darkVertical', 'gray0625', 'gray125', 'lightDown', 'lightGray', 'lightGrid', 'lightHorizontal', 'lightTrellis', 'lightUp', 'lightVertical', 'mediumGray'
            'rotation' => 90,
            'color' => [
                'rgb' => 'A0A0A0',
                'argb' => 'FFA0A0A0',
            ],
            'startColor' => [
                'rgb' => 'A0A0A0',
                'argb' => 'FFA0A0A0',
            ],
            'endColor' => [
                'argb' => 'FFFFFF',
                'argb' => 'FFFFFFFF',
            ],
        ],
    ];
    $headerStyle = [
        'font' => [
            'italic' => false,
            'bold' => true,
            'color' => [
                'rgb' => '000000',
            ]
        ],
        'alignment' => [
            'horizontal' => 'center',
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => 'thin',
                'color' => [
                    'rgb' => '000000',
                ]
            ],
        ],
    ];
    $cellStyle = [
        'font' => [
            'italic' => false,
            'color' => [
                'rgb' => '000000',
            ]
        ],
        'alignment' => [
            'horizontal' => 'center',
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => 'thin',
                'color' => [
                    'rgb' => '000000',
                ]
            ],
        ],
    ];
    ?>
    <div>Marketing</div>

    <div range="A15:B15">
        * Rp (Dalam Jutaan)
    </div>
    <div range="A20:B20">
        * Rp (Dalam Jutaan)
    </div>

    <!-- PIVOT -->
    <div cell="A2">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => 'SALES TAHAP/SEKTOR',
            "excelStyle" => [
                'font' => [
                    'name' => 'Calibri', //'Verdana', 'Arial'
                    'bold' => true,
                    'italic' => FALSE,
                    'color' => [
                        'rgb' => '000000',
                    ]
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                    'wrapText' => true,
                ],
            ]
        ));
        ?>
    </div>
    <div cell="A3">
        <?php
            Table::create(array(
                "dataSource" => $this->dataStore('marketing_sales_tahap_sektor_excel'),
                "showFooter"=>"true",
                "columns"=>array(
                    ""=>array(
                        "footerText"=>"TOTAL"
                    ),
                    (date('Y', strtotime(session('marketingsalestabletahapsektorcutoffexcel')))-1)=>array(
                        "label" => (date('Y', strtotime(session('marketingsalestabletahapsektorcutoffexcel')))-1),
                        "formatValue"=>function($value,$row) {
                            $nilai = number_format($value,0,',','.');
                            return str_replace('.', '', $nilai);
                        },
                        "footer"=>"sum",
                        "footerText"=>"@value",
                    ),
                    date('Y', strtotime(session('marketingsalestabletahapsektorcutoffexcel')))=>array(
                        "label" => date('Y', strtotime(session('marketingsalestabletahapsektorcutoffexcel'))),
                        "formatValue"=>function($value,$row) {
                            $nilai = number_format($value,0,',','.');
                            return str_replace('.', '', $nilai);
                        },
                        "footer"=>"sum",
                        "footerText"=>"@value",
                    ),
                ),
                "excelStyle" => [
                    "header" => function($colName) { 
                        return [
                            'font' => [
                                'italic' => false,
                                'bold' => true,
                                'color' => [
                                    'rgb' => '000000',
                                ]
                            ],
                            'alignment' => [
                                'horizontal' => 'center',
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => 'thin',
                                    'color' => [
                                        'rgb' => '000000',
                                    ]
                                ],
                            ],
                        ]; 
                    },
                    "bottomHeader" => function($colName) {
                        return [
                            
                        ];
                    },
                    "cell" => function($colName, $value, $row) { 
                        return [
                            'font' => [
                                'italic' => false,
                                'color' => [
                                    'rgb' => '000000',
                                ]
                            ],
                            'alignment' => [
                                'horizontal' => 'center',
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => 'thin',
                                    'color' => [
                                        'rgb' => '000000',
                                    ]
                                ],
                            ],
                        ]; 
                    },
                    "footer" => function($colName, $footerValue) {
                        return [
                            'font' => [
                                'italic' => false,
                                'bold' => true,
                                'color' => [
                                    'rgb' => '000000',
                                ]
                            ],
                            'alignment' => [
                                'horizontal' => 'center',
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => 'thin',
                                    'color' => [
                                        'rgb' => '000000',
                                    ]
                                ],
                            ],
                        ];
                    },
                ]
            ));
        ?>
    </div>

    <!-- PIVOT -->
    <div cell="A8">
        <?php
            PivotTable::create(array(
                "dataSource" => $this->dataStore('marketing_sales_table1'),
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
                            $v = 'LT (m2)';
                        else if ($v === 'total_lb - sum')
                            $v = 'LB (m2)';
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
                    'dataCell' => function($value, $cellInfo) {                        
                        if($cellInfo["fieldName"] == "total_rp - sum") {
                            $cellInfo["formattedValue"] = number_format($value,0,',','.');
                            $cellInfo["formattedValue"] = str_replace('.', '', $cellInfo["formattedValue"]);
                        }
                        else if($cellInfo["fieldName"] == "total_unit - sum") {
                            $cellInfo["formattedValue"] = number_format($value,0,',','.');
                            $cellInfo["formattedValue"] = str_replace('.', '', $cellInfo["formattedValue"]);
                        }
                        else if($cellInfo["fieldName"] == "total_lt - sum") {
                            $cellInfo["formattedValue"] = number_format($value,0,',','.');
                            $cellInfo["formattedValue"] = str_replace('.', '', $cellInfo["formattedValue"]);
                        }
                        else if($cellInfo["fieldName"] == "total_lb - sum") {
                            $cellInfo["formattedValue"] = number_format($value,0,',','.');
                            $cellInfo["formattedValue"] = str_replace('.', '', $cellInfo["formattedValue"]);
                        }
                        return $cellInfo["formattedValue"];
                    },
                ),
                'hideTotalColumn' => true,
                'hideSubtotalRow' => true,
                'hideSubtotalColumn' => true,
                'showDataHeaders' => true,
                'totalName' => 'TOTAL',
                'excelStyle' => array(
                    'dataField' => function($dataFields) use ($cellStyle) {
                        return $cellStyle;
                    },
                    'dataHeader' => function($dataFields, $fieldInfo) use ($headerStyle) {
                        return $headerStyle;
                    },
                    'columnHeader' => function($header, $headerInfo) use ($headerStyle) {
                        return $headerStyle;
                    },
                    'rowHeader' => function($header, $headerInfo) use ($cellStyle) {
                        return $cellStyle;
                    },
                    'dataCell' => function($value, $cellInfo) use ($cellStyle) {                    
                        return $cellStyle;
                    },
                )
            ));
        ?>
    </div>

    <!-- PIVOT -->
    <div cell="A16">
        <?php
            PivotTable::create(array(
                "dataSource" => $this->dataStore('marketing_sales_table2'),
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
                    'dataCell' => function($value, $cellInfo) {
                        if($cellInfo["fieldName"] == "budget_sales - sum") {
                            $cellInfo["formattedValue"] = number_format($value,0,',','.');
                            $cellInfo["formattedValue"] = str_replace('.', '', $cellInfo["formattedValue"]);
                        }
                        return $cellInfo["formattedValue"];
                    },
                ),
                'hideTotalRow' => true,
                'totalName' => 'TOTAL',
                'excelStyle' => array(
                    'dataField' => function($dataFields) use ($cellStyle) {
                        return $cellStyle;
                    },
                    'dataHeader' => function($dataFields, $fieldInfo) use ($headerStyle) {
                        return $headerStyle;
                    },
                    'columnHeader' => function($header, $headerInfo) use ($headerStyle) {
                        return $headerStyle;
                    },
                    'rowHeader' => function($header, $headerInfo) use ($cellStyle) {
                        return $cellStyle;
                    },
                    'dataCell' => function($value, $cellInfo) use ($cellStyle) {                    
                        return $cellStyle;
                    },
                )
            ));
        ?>
    </div>

    <!-- PIVOT -->
    <div cell="A21">
        <?php
            PivotTable::create(array(
                "dataSource" => $this->dataStore('marketing_sales_table_excel_pdf3'),
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
                    'dataCell' => function($value, $cellInfo) {
                        if($cellInfo["fieldName"] == "total_sales - sum") {
                            $cellInfo["formattedValue"] = number_format($value,0,',','.');
                            $cellInfo["formattedValue"] = str_replace('.', '', $cellInfo["formattedValue"]);
                        }
                        return $cellInfo["formattedValue"];
                    },
                ),
                'hideTotalRow' => true,
                'totalName' => 'TOTAL',
                'excelStyle' => array(
                    'dataField' => function($dataFields) use ($cellStyle) {
                        return $cellStyle;
                    },
                    'dataHeader' => function($dataFields, $fieldInfo) use ($headerStyle) {
                        return $headerStyle;
                    },
                    'columnHeader' => function($header, $headerInfo) use ($headerStyle) {
                        return $headerStyle;
                    },
                    'rowHeader' => function($header, $headerInfo) use ($cellStyle) {
                        return $cellStyle;
                    },
                    'dataCell' => function($value, $cellInfo) use ($cellStyle) {                    
                        return $cellStyle;
                    },
                )
            ));
        ?>
    </div>

    <!-- HEADER -->
    <div range="A8:A9">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => '',
            "excelStyle" => [
                'font' => [
                    'name' => 'Calibri', //'Verdana', 'Arial'
                    'bold' => true,
                    'italic' => FALSE,
                    'color' => [
                        'rgb' => '000000',
                    ]
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => [
                            'rgb' => '000000',
                        ]
                    ],
                ],
            ]
        ));
        ?>
    </div>
    <div cell="A16">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => '',
            "excelStyle" => [
                'font' => [
                    'name' => 'Calibri', //'Verdana', 'Arial'
                    'bold' => true,
                    'italic' => FALSE,
                    'color' => [
                        'rgb' => '000000',
                    ]
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => [
                            'rgb' => '000000',
                        ]
                    ],
                ],
            ]
        ));
        ?>
    </div>
    <div cell="A21">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => '',
            "excelStyle" => [
                'font' => [
                    'name' => 'Calibri', //'Verdana', 'Arial'
                    'bold' => true,
                    'italic' => FALSE,
                    'color' => [
                        'rgb' => '000000',
                    ]
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => [
                            'rgb' => '000000',
                        ]
                    ],
                ],
            ]
        ));
        ?>
    </div>
    <?php $alphabet = range('A', 'Z'); ?>
    <?php for($i = array_search('C', $alphabet); $i <= array_search('I', $alphabet); $i++) { ?>
    <div cell="<?php echo $alphabet[$i] ?>8">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => '',
            "excelStyle" => [
                'font' => [
                    'name' => 'Calibri',
                    'bold' => true,
                    'italic' => FALSE,
                    'color' => [
                        'rgb' => '000000',
                    ]
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => [
                            'rgb' => '000000',
                        ]
                    ],
                ],
            ]
        ));
        ?>
    </div>
    <?php } ?>
    <?php for($i = array_search('K', $alphabet); $i <= array_search('Q', $alphabet); $i++) { ?>
    <div cell="<?php echo $alphabet[$i] ?>8">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => '',
            "excelStyle" => [
                'font' => [
                    'name' => 'Calibri',
                    'bold' => true,
                    'italic' => FALSE,
                    'color' => [
                        'rgb' => '000000',
                    ]
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => [
                            'rgb' => '000000',
                        ]
                    ],
                ],
            ]
        ));
        ?>
    </div>
    <?php } ?>
    
</div>

<div sheet-name="<?php echo $sheet2; ?>">
    <?php
    $styleArray = [
        'font' => [
            'name' => 'Calibri', //'Verdana', 'Arial'
            'size' => 30,
            'bold' => true,
            'italic' => FALSE,
            'underline' => 'none', //'double', 'doubleAccounting', 'single', 'singleAccounting'
            'strikethrough' => FALSE,
            'superscript' => false,
            'subscript' => false,
            'color' => [
                'rgb' => '000000',
                'argb' => 'FF000000',
            ]
        ],
        'alignment' => [
            'horizontal' => 'general',//left, right, center, centerContinuous, justify, fill, distributed
            'vertical' => 'bottom',//top, center, justify, distributed
            'textRotation' => 0,
            'wrapText' => false,
            'shrinkToFit' => false,
            'indent' => 0,
            'readOrder' => 0,
        ],
        'borders' => [
            'top' => [
                'borderStyle' => 'none', //dashDot, dashDotDot, dashed, dotted, double, hair, medium, mediumDashDot, mediumDashDotDot, mediumDashed, slantDashDot, thick, thin
                'color' => [
                    'rgb' => '808080',
                    'argb' => 'FF808080',
                ]
            ],
            //left, right, bottom, diagonal, allBorders, outline, inside, vertical, horizontal
        ],
        'fill' => [
            'fillType' => 'none', //'solid', 'linear', 'path', 'darkDown', 'darkGray', 'darkGrid', 'darkHorizontal', 'darkTrellis', 'darkUp', 'darkVertical', 'gray0625', 'gray125', 'lightDown', 'lightGray', 'lightGrid', 'lightHorizontal', 'lightTrellis', 'lightUp', 'lightVertical', 'mediumGray'
            'rotation' => 90,
            'color' => [
                'rgb' => 'A0A0A0',
                'argb' => 'FFA0A0A0',
            ],
            'startColor' => [
                'rgb' => 'A0A0A0',
                'argb' => 'FFA0A0A0',
            ],
            'endColor' => [
                'argb' => 'FFFFFF',
                'argb' => 'FFFFFFFF',
            ],
        ],
    ];
    $headerStyle = [
        'font' => [
            'italic' => false,
            'bold' => true,
            'color' => [
                'rgb' => '000000',
            ]
        ],
        'alignment' => [
            'horizontal' => 'center',
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => 'thin',
                'color' => [
                    'rgb' => '000000',
                ]
            ],
        ],
    ];
    $cellStyle = [
        'font' => [
            'italic' => false,
            'color' => [
                'rgb' => '000000',
            ]
        ],
        'alignment' => [
            'horizontal' => 'center',
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => 'thin',
                'color' => [
                    'rgb' => '000000',
                ]
            ],
        ],
    ];
    ?>
    <div>Marketing</div>

    <!-- CHART -->
    <div cell="A2">
        <?php
            BarChart::create(array(
                "title"=>"All Sales (Unit)",
                "dataSource" => $this->dataStore('marketing_sales_all_sales_chart_excel_pdf'),
                "columns" =>[
                    'PROJECT_NAME',
                    "total_unit"=>array(
                        "label"=>"Unit"
                    )
                ],
                'direction' => 'vertical',
            ));
        ?>
    </div>

    <!-- CHART -->
    <div cell="A15">
        <?php            
            BarChart::create(array(
                "title"=>"Realisasi (PER TAHUN)",
                "dataSource" => $this->dataStore('marketing_sales_chart_tahun_excel_pdf1'),
                "columns" =>[
                    'tahun',
                    "budget_sales"
                ],
                // 'xAxisTitle' => 'category',
                // 'yAxisTitle' => 'jumlah',
                // 'stacked' => true,
                'direction' => 'vertical',
            ));
        ?>
    </div>

    <!-- CHART -->
    <div cell="I2">
        <?php
            BarChart::create(array(
                "title"=>"Realisasi (PER BULAN) (".date('Y', strtotime($this->params['cut_off'])).")",
                "dataSource" => $this->dataStore('marketing_sales_chart_bulan_excel_pdf1'),
                "columns" =>[
                    'bulan',
                    "budget_sales"
                ],
                // 'xAxisTitle' => 'category',
                // 'yAxisTitle' => 'jumlah',
                // 'stacked' => true,
                'direction' => 'vertical',
            ));
        ?>
    </div>

    <!-- CHART -->
    <div cell="I15">
        <?php
            BarChart::create(array(
                "title"=>strtoupper("Total Sales S/D ".date('d-M-Y', strtotime($this->params['cut_off']))),
                "dataSource" => $this->dataStore('marketing_sales_chart_excel_pdf2'),
                "columns" =>[
                    'tahun',
                    "total_sales"
                ],
                // 'xAxisTitle' => 'category',
                // 'yAxisTitle' => 'jumlah',
                // 'stacked' => true,
                'direction' => 'vertical',
            ));
        ?>
    </div>
    
</div>