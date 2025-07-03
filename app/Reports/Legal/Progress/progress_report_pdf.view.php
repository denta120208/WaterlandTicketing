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
            <h1>Progress SPT, SPPJB, AJB</h1>
        </div>
        <hr/>

        <div class="row">
            <div class="col-md">
                <br />
                <?php
                    Table::create(array(
                        "dataStore"=>$report->dataStore('legal_progress_table1'),
                        // "headers"=>array(
                        //     array(
                        //         ""=>array("colSpan"=>1),
                        //         "AJB"=>array("colSpan"=>2),
                        //     )
                        // ),
                        "showFooter"=>"bottom",
                        "themeBase"=>"bs4",
                        "cssClass"=>array(
                            "table"=>"table table-striped table-bordered"
                        ),
                        "columns"=>array(
                            "UNIT_TYPE"=>array(
                                "label" => "TIPE UNIT",
                                'type' => 'string',
                                "footerText"=>"<p><b>TOTAL</b></p>"
                            ),
                            "DESC_SALESTYPE_CHAR"=>array(
                                "label" => "METODE PEMBAYARAN",
                                "footerText"=>"-"
                            ),
                            "sudah_ajb"=>array(
                                "label" => "SUDAH AJB",
                                'type' => 'number',
                                "footer"=>"sum",
                                "footerText"=>"<b>@value</b>"
                            ),
                            "blm_ajb"=>array(
                                "label" => "BELUM AJB",
                                'type' => 'number',
                                "footer"=>"sum",
                                "footerText"=>"<b>@value</b>"
                            ),
                            "blm_ajb_lunas"=>array(
                                "label" => "BELUM AJB SUDAH LUNAS",
                                'type' => 'number',
                                "footer"=>"sum",
                                "footerText"=>"<b>@value</b>"
                            ),
                            "blm_ajb_blm_lunas"=>array(
                                "label" => "BELUM AJB BELUM LUNAS",
                                'type' => 'number',
                                "footer"=>"sum",
                                "footerText"=>"<b>@value</b>"
                            ),
                        ),
                        'options' => [
                            'ordering' => false
                        ]
                    ));
                ?>
                <br />
                <?php
                    Table::create(array(
                        "dataStore"=>$report->dataStore('legal_progress_table2'),
                        "headers"=>array(
                            array(
                                ""=>array("colSpan"=>1),
                                "AJB"=>array("colSpan"=>2),
                            )
                        ),
                        "showFooter"=>"bottom",
                        "themeBase"=>"bs4",
                        "cssClass"=>array(
                            "table"=>"table table-striped table-bordered"
                        ),
                        "columns"=>array(
                            "tahun"=>array(
                                "label" => "TAHUN",
                                'type' => 'string',
                                "footerText"=>"<p><b>TOTAL</b></p>"
                            ),
                            "ajb"=>array(
                                "label" => "SUDAH AJB",
                                'type' => 'number',
                                "footer"=>"sum",
                                "footerText"=>"<b>@value</b>",
                            ),
                            "blm_ajb"=>array(
                                "label" => "BELUM AJB",
                                'type' => 'number',
                                "footer"=>"sum",
                                "footerText"=>"<b>@value</b>",
                            ),
                        ),
                        'options' => [
                            'ordering' => false
                        ]
                    ));
                ?>
            </div>
        </div>
    </body>
</html>