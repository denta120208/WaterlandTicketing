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

    <!-- TABLE -->
    <div cell="A2">
        <?php
            Table::create(array(
                "dataSource" => $this->dataStore('marketing_growth_member_metland_card_table1'),
                "showFooter"=>"true",
                "columns"=>array(
                    "project"=>array(
                        'label' => '',
                        "footerText"=>"TOTAL"
                    ),
                    "JUMLAH_MEMBER"=>array(
                        'label' => 'MEMBER',
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

    <!-- TABLE -->
    <div cell="D2">
        <?php
            Table::create(array(
                "dataSource" => $this->dataStore('marketing_growth_member_metland_card_table2'),
                "showFooter"=>"true",
                "columns"=>array(
                    "bulan"=>array(
                        'label' => 'BULAN',
                        "footerText"=>"TOTAL"
                    ),
                    "JUMLAH_MEMBER"=>array(
                        'label' => 'JUMLAH',
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
                "title"=>"Growth Updated Member Metland Card (".date('Y', strtotime($this->params['cut_off'])).")",
                "dataSource" => $this->dataStore('growth_member_metland_card_excel_chart1'),
                "columns" =>[
                    "bulan"=>array(
                        'formatValue'=>function($value, $row){
                            // Merubah angka menjadi format bulan
                            $value = strtoupper(DateTime::createFromFormat('!m', $value)->format('M'));
                            return $value;
                        }
                    ),
                    "GrandMetropolitan Mall"=>array(
                        "label"=>"GMM",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['GrandMetropolitan Mall'];
                        }
                    ),
                    "Metropolitan Mall"=>array(
                        "label"=>"MM",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['Metropolitan Mall'];
                        }
                    ),
                    "Mall Metropolitan Cileungsi"=>array(
                        "label"=>"MMC",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['Mall Metropolitan Cileungsi'];
                        }
                    ),
                    "Hotel"=>array(
                        "label"=>"HOTEL",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['Hotel'];
                        }
                    ),
                    "Residential"=>array(
                        "label"=>"RESIDENTIAL",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['Residential'];
                        }
                    ),
                    "EVENT"=>array(
                        "label"=>"EVENT",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['EVENT'];
                        }
                    ),
                    "Head Office"=>array(
                        "label"=>"HEAD OFFICE",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['Head Office'];
                        }
                    ),
                ],
                'direction' => 'vertical',
            ));
        ?>
    </div>
</div>