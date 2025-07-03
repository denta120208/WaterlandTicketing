<?php
    use \koolreport\excel\Table;
    use \koolreport\excel\PivotTable;
    use \koolreport\excel\BarChart;
    use \koolreport\excel\LineChart;

    $sheet1 = "Revenue Console";
?>
<meta charset="UTF-8">

<?php if(count($this->dataStore('revenue_console_table1')->all()) > 0) { ?>
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
    ?>

    <?php
        $userName = trim(session('first_name') . ' ' . session('last_name'));
    ?>

    <div range="A1:G1">
        <?php
            \koolreport\excel\Text::create(array(
                "text" => "Revenue Console",
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
                            'borderStyle' => 'none',
                            'color' => [
                                'rgb' => '000000',
                            ]
                        ],
                    ],
                ]
            ));
        ?>
    </div>
    <div range="A2:G2">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "Cut Off : " . date('d/m/Y', strtotime($this->cut_off_param)),
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
                        'borderStyle' => 'none',
                        'color' => [
                            'rgb' => '000000',
                        ]
                    ],
                ],
            ]
        ));
        ?>
    </div>
    <div range="A3:G3">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "Printed by " . $userName . " at " . date('d/m/Y H:i'),
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
                        'borderStyle' => 'none',
                        'color' => [
                            'rgb' => '000000',
                        ]
                    ],
                ],
            ]
        ));
        ?>
    </div>

    <div cell="A5">
    <?php
        Table::create(array(
            "dataSource" => $this->dataStore('revenue_console_table1'),
            "showFooter"=>"true",
            "columns"=>array(
                "PROJECT_NAME" => ["label" => "Project", "formatValue" => function($value, $row) { return $value; }, "footerText" => "TOTAL"],
                "ACTUAL_TODAY" => ["label" => "Actual (Today)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "@value"],
                "BUDGET_TODAY" => ["label" => "Budget (Today)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "@value"],
                "ACHIEVEMENT_TODAY" => ["label" => "Achievement (Today) (%)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,2,',','.') . "%"; }, "footerText" => "-"],
                "ACTUAL_YTD" => ["label" => "Actual (YTD)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "@value"],
                "BUDGET_YTD" => ["label" => "Budget (YTD)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "@value"],
                "ACHIEVEMENT_YTD" => ["label" => "Achievement (YTD) (%)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : number_format($value,2,',','.') . "%"; }, "footerText" => "-"]
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
                    if($colName == "PROJECT_NAME") {
                        return [
                            'font' => [
                                'italic' => false,
                                'color' => [
                                    'rgb' => '000000',
                                ]
                            ],
                            'alignment' => [
                                'horizontal' => 'left',
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
                    }
                    else if($colName == "ACTUAL_TODAY" || $colName == "BUDGET_TODAY" || $colName == "ACTUAL_YTD" || $colName == "BUDGET_YTD" || $colName == "ACHIEVEMENT_TODAY" || $colName == "ACHIEVEMENT_YTD") {
                        return [
                            'font' => [
                                'italic' => false,
                                'color' => [
                                    'rgb' => '000000',
                                ]
                            ],
                            'alignment' => [
                                'horizontal' => 'right',
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
                    }
                    else {
                        return [
                            'font' => [
                                'italic' => false,
                                'color' => [
                                    'rgb' => '000000',
                                ]
                            ],
                            'alignment' => [
                                'horizontal' => 'left',
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
                    }
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
<?php } ?>