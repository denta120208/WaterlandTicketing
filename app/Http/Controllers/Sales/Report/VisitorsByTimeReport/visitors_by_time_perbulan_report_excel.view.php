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

    <div range="A1:AB1">
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
    <div range="A2:AB2">
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
    <div range="A3:AB3">
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
    <div range="A4:AB4">
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
    <div range="A5:AB5">
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
                "YEAR" => ["label" => "YEAR", "formatValue" => function($value, $row) { return $value; }, "footerText" => "TOTAL"],
                "JAN_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "JAN_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "FEB_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "FEB_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "MAR_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "MAR_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "APR_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "APR_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "MAY_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "MAY_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "JUN_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "JUN_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "JUL_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "JUL_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "AUG_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "AUG_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "SEP_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "SEP_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "OCT_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "OCT_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "NOV_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "NOV_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
                "DEC_TICKET" => ["label" => "Ticket", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "DEC_PAID" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "0" : $value; }, "footer" => "sum", "footerText" => "@value"],
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
                    if($colName == "YEAR") {
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
                    else if($colName == "JAN_TICKET" || $colName == "JAN_PAID" || $colName == "FEB_TICKET" || $colName == "FEB_PAID" ||
                            $colName == "MAR_TICKET" || $colName == "MAR_PAID" || $colName == "APR_TICKET" || $colName == "APR_PAID" ||
                            $colName == "MAY_TICKET" || $colName == "MAY_PAID" || $colName == "JUN_TICKET" || $colName == "JUN_PAID" ||
                            $colName == "JUL_TICKET" || $colName == "JUL_PAID" || $colName == "AUG_TICKET" || $colName == "AUG_PAID" ||
                            $colName == "SEP_TICKET" || $colName == "SEP_PAID" || $colName == "OCT_TICKET" || $colName == "OCT_PAID" ||
                            $colName == "NOV_TICKET" || $colName == "NOV_PAID" || $colName == "DEC_TICKET" || $colName == "DEC_PAID" ||
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
            "text" => "YEAR",
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
            "text" => "JAN",
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
            "text" => "FEB",
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
            "text" => "MAR",
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
            "text" => "APR",
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
            "text" => "MAY",
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
            "text" => "JUN",
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
            "text" => "JUL",
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
            "text" => "AUG",
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
            "text" => "SEP",
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
            "text" => "OCT",
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
            "text" => "NOV",
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
            "text" => "DEC",
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

    <div range="Z7:AB7">
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
    <div cell="AB7">
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