<?php
    use \koolreport\excel\Table;
    use \koolreport\excel\PivotTable;
    use \koolreport\excel\BarChart;
    use \koolreport\excel\LineChart;

    $sheet1 = "Visitors By Time";
?>
<meta charset="UTF-8">

<?php if(count($this->dataStore('visitors_by_time_table1')->all()) > 0) { ?>
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
        $dataProject = DB::select("SELECT * FROM MD_PROJECT AS a WHERE a.PROJECT_NO_CHAR = '".$this->project_param."'");
        $userName = trim(session('first_name') . ' ' . session('last_name'));
    ?>

    <div range="A1:BN1">
        <?php
            \koolreport\excel\Text::create(array(
                "text" => "Visitors By Time",
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
    <div range="A2:BN2">
        <?php
            \koolreport\excel\Text::create(array(
                "text" => $dataProject[0]->PROJECT_NAME,
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
    <div range="A3:BN3">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => date('d/m/Y', strtotime($this->start_date_param)) . " - " . date('d/m/Y', strtotime($this->end_date_param)),
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
    <div range="A4:BN4">
        <?php
            \koolreport\excel\Text::create(array(
                "text" => $this->kategori_param,
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
    <div range="A5:BN5">
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

    <div cell="A8">
    <?php
        Table::create(array(
            "dataSource" => $this->dataStore('visitors_by_time_table1'),
            "showFooter"=>"true",
            "columns"=>array(
                "MONTH" => ["label" => "MONTH", "formatValue" => function($value, $row) { return DateTime::createFromFormat('!m', $value)->format('F'); }, "footerText" => "TOTAL"],
                "1_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "1_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "2_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "2_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "3_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "3_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "4_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "4_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "5_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "5_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "6_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "6_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "7_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "7_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "8_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "8_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "9_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "9_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "10_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "10_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "11_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "11_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "12_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "12_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "13_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "13_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "14_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "14_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "15_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "15_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "16_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "16_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "17_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "17_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "18_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "18_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "19_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "19_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "20_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "20_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "21_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "21_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "22_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "22_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "23_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "23_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "24_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "24_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "25_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "25_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "26_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "26_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "27_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "27_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "28_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "28_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "29_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "29_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "30_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "30_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "31_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "31_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "TOTAL_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "TOTAL_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "TOTAL_PAID (AVG)" => ["label" => "Paid (AVG)", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footerText" => "-"]
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
                    if($colName == "MONTH") {
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
                    else if($colName == "1_TICKET" || $colName == "1_PAID" || $colName == "2_TICKET" || $colName == "2_PAID" ||
                            $colName == "3_TICKET" || $colName == "3_PAID" || $colName == "4_TICKET" || $colName == "4_PAID" ||
                            $colName == "5_TICKET" || $colName == "5_PAID" || $colName == "6_TICKET" || $colName == "6_PAID" ||
                            $colName == "7_TICKET" || $colName == "7_PAID" || $colName == "8_TICKET" || $colName == "8_PAID" ||
                            $colName == "9_TICKET" || $colName == "9_PAID" || $colName == "10_TICKET" || $colName == "10_PAID" ||
                            $colName == "11_TICKET" || $colName == "11_PAID" || $colName == "12_TICKET" || $colName == "12_PAID" ||
                            $colName == "13_TICKET" || $colName == "13_PAID" || $colName == "14_TICKET" || $colName == "14_PAID" ||
                            $colName == "15_TICKET" || $colName == "15_PAID" || $colName == "16_TICKET" || $colName == "16_PAID" ||
                            $colName == "17_TICKET" || $colName == "17_PAID" || $colName == "18_TICKET" || $colName == "18_PAID" ||
                            $colName == "19_TICKET" || $colName == "19_PAID" || $colName == "20_TICKET" || $colName == "20_PAID" ||
                            $colName == "21_TICKET" || $colName == "21_PAID" || $colName == "22_TICKET" || $colName == "22_PAID" ||
                            $colName == "23_TICKET" || $colName == "23_PAID" || $colName == "24_TICKET" || $colName == "24_PAID" ||
                            $colName == "25_TICKET" || $colName == "25_PAID" || $colName == "26_TICKET" || $colName == "26_PAID" ||
                            $colName == "27_TICKET" || $colName == "27_PAID" || $colName == "28_TICKET" || $colName == "28_PAID" ||
                            $colName == "29_TICKET" || $colName == "29_PAID" || $colName == "30_TICKET" || $colName == "30_PAID" ||
                            $colName == "31_TICKET" || $colName == "31_PAID" ||
                            $colName == "TOTAL_TICKET" || $colName == "TOTAL_PAID" || $colName == "TOTAL_PAID (AVG)") {
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

    <div range="A7:A8">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "MONTH",
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

    <div range="B7:C7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "1",
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
    <div cell="C7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="D7:E7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "2",
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
    <div cell="E7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="F7:G7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "3",
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
    <div cell="G7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="H7:I7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "4",
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
    <div cell="I7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="J7:K7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "5",
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
    <div cell="K7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="L7:M7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "6",
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
    <div cell="M7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="N7:O7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "7",
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
    <div cell="O7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="P7:Q7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "8",
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
    <div cell="Q7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="R7:S7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "9",
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
    <div cell="S7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="T7:U7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "10",
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
    <div cell="U7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="V7:W7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "11",
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
    <div cell="W7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="X7:Y7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "12",
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
    <div cell="Y7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="Z7:AA7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "13",
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
    <div cell="AA7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="AB7:AC7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "14",
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
    <div cell="AC7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="AD7:AE7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "15",
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
    <div cell="AE7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="AF7:AG7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "16",
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
    <div cell="AG7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="AH7:AI7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "17",
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
    <div cell="AI7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="AJ7:AK7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "18",
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
    <div cell="AK7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="AL7:AM7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "19",
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
    <div cell="AM7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="AN7:AO7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "20",
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
    <div cell="AO7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="AP7:AQ7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "21",
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
    <div cell="AQ7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="AR7:AS7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "22",
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
    <div cell="AS7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="AT7:AU7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "23",
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
    <div cell="AU7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="AV7:AW7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "24",
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
    <div cell="AW7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="AX7:AY7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "25",
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
    <div cell="AY7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="AZ7:BA7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "26",
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
    <div cell="BA7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="BB7:BC7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "27",
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
    <div cell="BC7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="BD7:BE7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "28",
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
    <div cell="BE7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="BF7:BG7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "29",
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
    <div cell="BG7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="BH7:BI7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "30",
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
    <div cell="BI7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="BJ7:BK7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "31",
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
    <div cell="BK7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

    <div range="BL7:BN7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "TOTAL",
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
    <div cell="BM7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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
    <div cell="BN7">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "",
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

</div>
<?php } ?>