<?php
    use \koolreport\excel\Table;
    use \koolreport\excel\PivotTable;
    use \koolreport\excel\BarChart;
    use \koolreport\excel\LineChart;

    $sheet1 = "Posisi Mutasi Escrow";
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
    ?>
    <div>Finance Accounting</div>

    <div range="M2:O2">
	    * Angka Dalam Jutaan
    </div>

    <div range="A2:A4">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => 'BANK',
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

    <div range="B2:C3">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "SALDO 31-".substr(strtoupper(DateTime::createFromFormat('!m', '12')->format('F')), 0, 3)."-".(date('Y', strtotime($this->params['cut_off'])) - 1),
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

    <div range="D2:E3">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "PENAMBAHAN S/D ".strtoupper(date("d-M-Y", strtotime($this->params['cut_off']))),
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

    <div range="F2:G3">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "PENCAIRAN S/D ".strtoupper(date("d-M-Y", strtotime($this->params['cut_off']))),
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

    <div range="H2:I3">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "POSISI ESCROW ".strtoupper(date("d-M-Y", strtotime($this->params['cut_off']))),
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

    <div range="J2:K3">
        <?php
        \koolreport\excel\Text::create(array(
            "text" => "PROYEKSI PENC ".date('Y', strtotime($this->params['cut_off'])),
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

    <div cell="A4">
        <?php
        Table::create(array(
            "dataSource" => $this->dataStore('finance_accounting_posisi_mutasi_escrow_excel_table'),
            "showFooter"=>"true",
            "columns"=>array(
                "BANK_NAME_CHAR"=>array(
                    "label" => "BANK",
                    "type" => "string",
                ),
                "SISA_UPTO_LS"=>array(
                    "label" => "Rp",
                ),
                "UNIT_UPTO_LS"=>array(
                    "label" => "Unit",
                ),
                "SISA_UPTO"=>array(
                    "label" => "Rp",
                ),
                "UNIT_UPTO"=>array(
                    "label" => "Unit",
                ),
                "SA_REALISASI_AMOUNT_INT"=>array(
                    "label" => "Rp",
                ),
                "UNIT_PAY"=>array(
                    "label" => "Unit",
                ),
                "SISA_ESCROW"=>array(
                    "label" => "Rp",
                ),
                "UNIT_ESCROW"=>array(
                    "label" => "Unit",
                ),
                "BLOKIR_AMOUNT_INT"=>array(
                    "label" => "Rp",
                ),
                "UNIT_PROYEKSI"=>array(
                    "label" => "Unit",
                ),
                
                "BANK_NAME_CHAR"=>array(
                    "footerText"=>"-"
                ),
                "SISA_UPTO_LS"=>array(
                    "footer"=>"sum",
                    "footerText"=>"@value",
                ),
                "UNIT_UPTO_LS"=>array(
                    "footer"=>"sum",
                    "footerText"=>"@value",
                ),
                "SISA_UPTO"=>array(
                    "footer"=>"sum",
                    "footerText"=>"@value",
                ),
                "UNIT_UPTO"=>array(
                    "footer"=>"sum",
                    "footerText"=>"@value",
                ),
                "SA_REALISASI_AMOUNT_INT"=>array(
                    "footer"=>"sum",
                    "footerText"=>"@value",
                ),
                "UNIT_PAY"=>array(
                    "footer"=>"sum",
                    "footerText"=>"@value",
                ),
                "SISA_ESCROW"=>array(
                    "footer"=>"sum",
                    "footerText"=>"@value",
                ),
                "UNIT_ESCROW"=>array(
                    "footer"=>"sum",
                    "footerText"=>"@value",
                ),
                "BLOKIR_AMOUNT_INT"=>array(
                    "footer"=>"sum",
                    "footerText"=>"@value",
                ),
                "UNIT_PROYEKSI"=>array(
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

    <!-- HEADER -->
    <div cell="C2">
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
    <div cell="C3">
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
    <div cell="E2">
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
    <div cell="F3">
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
    <div cell="G2">
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
    <div cell="H3">
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
    <div cell="I2">
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
    <div cell="J3">
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
    <div cell="K2">
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
    <div cell="K3">
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
    <div cell="K4">
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
    
</div>