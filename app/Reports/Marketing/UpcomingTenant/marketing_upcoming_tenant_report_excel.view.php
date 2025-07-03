<?php
    use \koolreport\excel\Table;
    use \koolreport\excel\PivotTable;
    use \koolreport\excel\BarChart;
    use \koolreport\excel\LineChart;

    $sheet1 = "Upcoming Tenant";
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

    <!-- TABLE -->
    <div cell="A2">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => 'Based on deal '.date('d F Y', strtotime($report->params['start_date'])).' - '.date('d F Y', strtotime($report->params['cut_off'])),
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
                "dataSource" => $this->dataStore('marketing_upcoming_tenant_table1'),
                // "showFooter"=>"true",
                "columns"=>array(
                    "MD_TENANT_NAME_CHAR"=>array(
                        "label" => "TENANT"
                    ),
                    "PSM_CATEGORY_NAME"=>array(
                        "label" => "KATEGORI"
                    ),
                    "LOT_LEVEL_DESC"=>array(
                        "label" => "LOKASI"
                    ),
                    "LEASED_AREA"=>array(
                        "label" => "LUAS",
                        "suffix" => " m2"
                    ),
                    "PSM_TRANS_BOOKING_DATE"=>array(
                        "label" => "TANGGAL KESEPAKATAN",
                        "type" => "string"
                    ),
                    "PSM_TRANS_START_DATE"=>array(
                        "label" => "RENCANA BUKA",
                        "type" => "string"
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
</div>