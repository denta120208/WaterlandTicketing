<?php
    use \koolreport\excel\Table;
    use \koolreport\excel\PivotTable;
    use \koolreport\excel\BarChart;
    use \koolreport\excel\PieChart;
    use \koolreport\excel\LineChart;

    $sheet1 = "Konsumsi & Komposisi Listrik";
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
    <div>Teknik</div>
    
    <div cell="B1">
        <?php echo $this->params['bulan_tahun_char'] ?>
    </div>

    <!-- CHART -->
    <div cell="A2">
        <?php
            PieChart::create(array(
                "title"=>"Konsumsi Pemakaian Listrik Gedung (KWH)",
                "dataSource" => $this->dataStore('teknik_konsumsi_komposisi_listrik_pie_chart1'),
                "columns" =>[
                    "DESCRIPTION_CHAR",
                    "NOMINAL_KWH_NUM"=>array(
                        "label"=>"KWH",
                        "annotation"=>function($row) {
                            return $row['NOMINAL_KWH_NUM'];
                        }
                    ),
                ],
                "options"=>array(
                    "is3D"=>true
                )
            ));
        ?>
    </div>
    <!-- CHART -->
    <div cell="I2">
        <?php
            PieChart::create(array(
                "title"=>"Komposisi Pembayaran Listrik Gedung (Rp)",
                "dataSource" => $this->dataStore('teknik_konsumsi_komposisi_listrik_pie_chart2'),
                "columns" =>[
                    "DESCRIPTION_CHAR",
                    "NOMINAL_RP_NUM"=>array(
                        "label"=>"RP",
                        "annotation"=>function($row) {
                            return $row['NOMINAL_RP_NUM'];
                        }
                    ),
                ],
                "options"=>array(
                    "is3D"=>true
                )
            ));
        ?>
    </div>
    <!-- CHART -->
    <div cell="A15">
        <?php
            BarChart::create(array(
                "title"=>"Konsumsi Pemakaian Listrik Gedung (KWH)",
                "dataSource" => $this->dataStore('teknik_konsumsi_komposisi_listrik_pie_chart1'),
                "columns" =>[
                    'DESCRIPTION_CHAR',
                    "NOMINAL_KWH_NUM"=>array(
                        "label"=>"KWH"
                    )
                ],
                'direction' => 'vertical',
            ));
        ?>
    </div>
    <!-- CHART -->
    <div cell="I15">
        <?php
            BarChart::create(array(
                "title"=>"Komposisi Pembayaran Listrik Gedung (Rp)",
                "dataSource" => $this->dataStore('teknik_konsumsi_komposisi_listrik_pie_chart2'),
                "columns" =>[
                    'DESCRIPTION_CHAR',
                    "NOMINAL_RP_NUM"=>array(
                        "label"=>"RP"
                    )
                ],
                'direction' => 'vertical',
            ));
        ?>
    </div>
</div>