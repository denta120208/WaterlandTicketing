<?php
    use \koolreport\excel\Table;
    use \koolreport\excel\PivotTable;
    use \koolreport\excel\BarChart;
    use \koolreport\excel\PieChart;
    use \koolreport\excel\LineChart;

    $sheet1 = "Active Member";
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

    <!-- CHART -->
    <div cell="A2">
        <?php
            PieChart::create(array(
                "title"=>"Active Member Based on ".session('current_project_char_commercial').") (".strtoupper(date('d/m/Y', strtotime(date('Y', strtotime($this->params['cut_off'])).'-01-01')))." - ".strtoupper(date('d/m/Y', strtotime($this->params['cut_off']))).") (BY AGE)",
                "dataSource" => $this->dataStore('marketing_active_member_metland_card_chart1'),
                "columns" =>[
                    "AGE",
                    "JUMLAH_MEMBER"=>array(
                        "label"=>"MEMBER",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['JUMLAH_MEMBER'];
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
                "title"=>"Active Member Based on ".session('current_project_char_commercial').") (YTD ".strtoupper(date('F Y', strtotime($this->params['cut_off']))).") (BY GENDER)",
                "dataSource" => $this->dataStore('marketing_active_member_metland_card_chart2'),
                "columns" =>[
                    "GENDER",
                    "JUMLAH_MEMBER"=>array(
                        "label"=>"MEMBER",
                        "type"=>"number",
                        "annotation"=>function($row) {
                            return $row['JUMLAH_MEMBER'];
                        }
                    ),
                ],
                "options"=>array(
                    "is3D"=>true
                )
            ));
        ?>
    </div>
    
</div>