<?php
    use \koolreport\excel\Table;
    use \koolreport\excel\PivotTable;
    use \koolreport\excel\BarChart;
    use \koolreport\excel\LineChart;

    $sheet1 = "Equipment";
?>
<meta charset="UTF-8">

<?php if(count($this->dataStore('equipment_report_table1')->all()) > 0) { ?>
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

    <div range="A1:S1">
        <?php
            \koolreport\excel\Text::create(array(
                "text" => "Equipment",
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
    <div range="A2:S2">
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
    <div range="A3:S3">
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
    <div range="A4:S4">
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

    <div cell="A6">
    <?php
        Table::create(array(
            "dataSource" => $this->dataStore('equipment_report_table1'),
            "showFooter"=>"true",
            "columns"=>array(
                "TRANS_RENTAL_EQUIPMENT_ID_INT" => ["label" => "No.", "formatValue" => function($value, $row) { return $value; }, "footerText" => "TOTAL"],
                "TRANS_EQUIPMENT_NO_CHAR" => ["label" => "Transaction Number", "formatValue" => function($value, $row) { return $value; }, "footerText" => "-"],
                "CUSTOMER_NAME_CHAR" => ["label" => "Customer", "formatValue" => function($value, $row) { return $value; }, "footerText" => "-"],
                "NO_TELP_CHAR" => ["label" => "Telp", "formatValue" => function($value, $row) { return $value; }, "footerText" => "-"],
                "QTY_INT" => ["label" => "Qty", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "QTY_FREE_INT" => ["label" => "Qty Free", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "DISCOUNT_PERCENT_FLOAT" => ["label" => "Discount (%)", "formatValue" => function($value, $row) { return $value; }, "footer" => "sum", "footerText" => "@value"],
                "DISCOUNT_NOMINAL_FLOAT" => ["label" => "Discount Nominal", "formatValue" => function($value, $row) { return $value."%"; }, "footer" => "sum", "footerText" => "@value"],
                "TOTAL_HARGA_FLOAT" => ["label" => "Price", "formatValue" => function($value, $row) { return $value == "-" ? "-" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "@value"],
                "DEPOSIT_FLOAT" => ["label" => "Deposit", "formatValue" => function($value, $row) { return $value == "-" ? "-" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "@value"],
                "TOTAL_PAID_FINAL_FLOAT" => ["label" => "Paid", "formatValue" => function($value, $row) { return $value == "-" ? "-" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "@value"],
                "TOTAL_CHANGE_FLOAT" => ["label" => "Change", "formatValue" => function($value, $row) { return $value == "-" ? "-" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "@value"],
                "REFUND_FLOAT" => ["label" => "Refund", "formatValue" => function($value, $row) { return $value == "-" ? "-" : number_format($value,0,',','.'); }, "footer" => "sum", "footerText" => "@value"],
                "REFUND_DESC_CHAR" => ["label" => "Refund Desc", "formatValue" => function($value, $row) { return $value; }, "footerText" => "-"],
                "REFUND_DATE" => ["label" => "Refund Date", "type"=>"string", "formatValue" => function($value, $row) { return $value; }, "footerText" => "-"],
                "CASHIER_NAME_CHAR" => ["label" => "Cashier", "formatValue" => function($value, $row) { return $value; }, "footerText" => "-"],
                "created_at" => ["label" => "Transaction Date", "type"=>"string", "formatValue" => function($value, $row) { return $value; }, "footerText" => "-"],
                "RETUR_BY" => ["label" => "Retur By", "formatValue" => function($value, $row) { return $value; }, "footerText" => "-"],
                "RETUR_AT" => ["label" => "Retur Time", "type"=>"string", "formatValue" => function($value, $row) { return $value; }, "footerText" => "-"]
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
                    if($colName == "TRANS_RENTAL_EQUIPMENT_ID_INT" || $colName == "TRANS_EQUIPMENT_NO_CHAR" || $colName == "CUSTOMER_NAME_CHAR" || $colName == "NO_TELP_CHAR" || $colName == "CASHIER_NAME_CHAR" || $colName == "created_at" || $colName == "RETUR_BY" || $colName == "RETUR_AT" || $colName == "REFUND_DESC_CHAR" || $colName == "REFUND_DATE") {
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
                    }
                    else if($colName == "QTY_INT" || $colName == "TOTAL_HARGA_FLOAT" || $colName == "TOTAL_PAID_FINAL_FLOAT" || $colName == "TOTAL_CHANGE_FLOAT" || $colName == "QTY_FREE_INT" || $colName == "DISCOUNT_PERCENT_FLOAT" || $colName == "DISCOUNT_NOMINAL_FLOAT" || $colName == "REFUND_FLOAT" || $colName == "DEPOSIT_FLOAT") {
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