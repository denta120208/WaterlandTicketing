<?php
    use \koolreport\excel\Table;
    use \koolreport\excel\PivotTable;
    use \koolreport\excel\BarChart;
    use \koolreport\excel\LineChart;

    $sheet1 = "Analisa Net Present Value (NPV)";
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

    <!-- TITLE -->
    <div cell="A2">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => 'Harga Jual Tanah Rata2/m2',
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
            ]
        ));
        ?>
    </div>

    <!-- PIVOT -->
    <div cell="A3">
        <?php
            PivotTable::create(array(
                "dataSource" => $this->dataStore('marketing_analisa_npv_table_land'),
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
                // 'totalName' => 'TOTAL',
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

    <!-- KOLOM KOSONG -->
    <div range="A3:A4">
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

    <!-- INFO TOTAL -->
    <div range="D10:E10">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => 'Harga Jual Tanah Rata2/m2',
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
    <div range="F10:G10">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => 'NPV: '.$this->params['npvLand'],
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
    <div range="H10:I10">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => 'All Payment: '.$this->params['paymentLand'],
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

    <!-- TITLE -->
    <div cell="A12">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => 'Harga Jual Bangunan Rata2/m2',
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
            ]
        ));
        ?>
    </div>

    <!-- PIVOT -->
    <div cell="A13">
        <?php
            PivotTable::create(array(
                "dataSource" => $this->dataStore('marketing_analisa_npv_table_build'),
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
                // 'totalName' => 'TOTAL',
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

    <!-- KOLOM KOSONG -->
    <div range="A13:A14">
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

    <!-- INFO TOTAL -->
    <div range="D20:E20">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => 'Harga Jual Building Rata2/m2',
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
    <div range="F20:G20">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => 'NPV: '.$this->params['npvBuild'],
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
    <div range="H20:I20">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => 'All Payment: '.$this->params['paymentBuild'],
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

    <!-- HEADER -->
    <div cell="C3">
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
    <div cell="D3">
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
    <div cell="E3">
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
    <div cell="G3">
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
    <div cell="H3">
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
    <div cell="I3">
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
    <div cell="C13">
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
    <div cell="D13">
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
    <div cell="E13">
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
    <div cell="G13">
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
    <div cell="H13">
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
    <div cell="I13">
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
    <div cell="E10">
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
    <div cell="G10">
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
    <div cell="I10">
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
    <div cell="E20">
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
    <div cell="G20">
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
    <div cell="I20">
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

    <div range="A10:B10">
        * Rp (Dalam Ribuan) | Interest Rate : <?php echo (float) $this->interest_rate . '%'; ?>
    </div>
    <div range="A20:B20">
        * Rp (Dalam Ribuan) | Interest Rate : <?php echo (float) $this->interest_rate . '%'; ?>
    </div>
    
</div>