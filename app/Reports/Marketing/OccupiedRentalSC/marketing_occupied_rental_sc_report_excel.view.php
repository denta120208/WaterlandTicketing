<?php
    use \koolreport\excel\Table;
    use \koolreport\excel\PivotTable;
    use \koolreport\excel\BarChart;
    use \koolreport\excel\LineChart;

    $sheet1 = "Occupied Rental & SC";
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

    <div range="B2:D2">
        * Rental Revenue (Dalam Jutaan), Leased Area (Satuan m2)
    </div>
    <div range="B9:D9">
        * SC Revenue (Dalam Jutaan), Leased Area (Satuan m2)
    </div>

    <!-- TABLE -->
    <div cell="A2">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => 'Occupied Rental/Month',
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
            PivotTable::create(array(
                "dataSource" => $this->dataStore('marketing_occupied_rental_sc_table1'),
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
    <div range="A3:A4">
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
    <?php for($i = array_search('C', $alphabet); $i <= array_search('D', $alphabet); $i++) { ?>
    <div cell="<?php echo $alphabet[$i] ?>3">
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
    <?php for($i = array_search('F', $alphabet); $i <= array_search('G', $alphabet); $i++) { ?>
    <div cell="<?php echo $alphabet[$i] ?>3">
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

    <!-- TABLE -->
    <div cell="A9">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => 'Occupied Service Charge/Month',
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
    <div cell="A10">
        <?php
            PivotTable::create(array(
                "dataSource" => $this->dataStore('marketing_occupied_rental_sc_table2'),
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
    <div range="A10:A11">
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
    <?php for($i = array_search('C', $alphabet); $i <= array_search('D', $alphabet); $i++) { ?>
    <div cell="<?php echo $alphabet[$i] ?>10">
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
    <?php for($i = array_search('F', $alphabet); $i <= array_search('G', $alphabet); $i++) { ?>
    <div cell="<?php echo $alphabet[$i] ?>10">
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