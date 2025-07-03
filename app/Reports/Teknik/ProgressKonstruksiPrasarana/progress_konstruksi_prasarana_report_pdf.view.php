<?php 
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\widgets\google\ColumnChart;
?>
<html>
    <style>
        .pivot-column-header, .pivot-data-header {
            font-weight: bold;
            text-align: center;
        }
        .table {
            text-align: center;
            min-width: 100%;
            width: 100%;
        }
        th {
            background-color: #081c5c;
            color: white;
        }
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 1%;
        }
    </style>

    <body style="margin:0.5in 1in 0.5in 1in">
        <div class="page-header" style="text-align:right"><i>Legal Report</i></div>
        <div class="page-footer" style="text-align:right">{pageNum}</div>
        <div class="text-center">
            <h1>Progress Konstruksi Prasarana</h1>
        </div>
        <hr/>

        <?php
            Table::create(array(
                "dataStore"=>$report->dataStore('teknik_progress_konstruksi_prasarana_table'),
                "headers"=>array(
                    array(
                        ""=>array("colSpan"=>3),
                        "PROGRESS"=>array("colSpan"=>3),
                        " "=>array("colSpan"=>1),
                    )
                ),
                // "showFooter"=>"bottom",
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                "columns"=>array(
                    "#"=>array(
                        "label"=>"NO",
                        "start"=>1,
                    ),
                    "MD_VENDOR_NAME_CHAR"=>array(
                        "label" => "KONTRAKTOR",
                        "type" => "string",
                        // "footerText"=>"<p><b>NET BACKLOG</b></p>"
                    ),
                    "NAMA_PEKERJAAN"=>array(
                        "label" => "PEKERJAAN",
                        // "footer"=>"sum",
                        // "footerText"=>"<b>@value</b>",
                    ),
                    "Target"=>array(
                        "label" => "TARGET",
                        "suffix" => "%",
                        // "footer"=>"sum",
                        // "footerText"=>"<b>@value</b>",
                    ),
                    "Realisasi"=>array(
                        "label" => "R ".$this->params["cut_off"],
                        "suffix" => "%",
                        // "footer"=>"sum",
                        // "footerText"=>"<b>@value</b>",
                    ),
                    "Target_x_Realisasi"=>array(
                        "label" => "+/-",
                        // "suffix" => "%",
                        "formatValue"=>function($value, $row){
                            $color = number_format($value)<0?"red":"black";
                            return "<p style='color:$color;'>".number_format($value)."%</p>";
                        }
                        // "footer"=>"sum",
                        // "footerText"=>"<b>@value</b>",
                    ),
                    "SPK_TRANS_END_DATE"=>array(
                        "label" => "TARGET SELESAI",
                        // "footer"=>"sum",
                        // "footerText"=>"<b>@value</b>",
                    ),
                ),
                // "paging"=>array(
                //     "pageSize"=>10,
                //     "pageIndex"=>0,
                // ),
                'options' => [
                    'ordering' => false
                ]
            ));
        ?>
    </body>
</html>