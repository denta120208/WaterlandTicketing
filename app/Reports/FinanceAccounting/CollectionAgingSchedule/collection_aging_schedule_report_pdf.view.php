<?php 
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\widgets\google\ColumnChart;
    session(['indexColorChart' => 1]);
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
        <div class="page-header" style="text-align:right"><i>Finance Accounting Report</i></div>
        <div class="page-footer" style="text-align:right">{pageNum}</div>
        <div class="text-center">
            <h1>Collection & Aging Schedule</h1>
        </div>
        <hr/>

        <span style="color: red;"><b>* Rp (Dalam Jutaan), Exclude PPN</b></span>
        <?php
            Table::create(array(
                "dataStore"=>$this->dataStore('finance_accounting_collection_aging_schedule_table1'),
                "headers"=>array(
                    array(
                        ""=>array("colSpan"=>1),
                        (date('Y', strtotime($this->params['cut_off'])) - 1)=>array("colSpan"=>1),
                        date('Y', strtotime($this->params['cut_off']))=>array("colSpan"=>4),
                    )
                ),
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                'options' => [
                    'ordering' => false
                ]
            ));
        ?>
        <br />
        <?php
            if(count($this->dataStore('finance_accounting_collection_aging_schedule_table2')->data()) > 1) {
                ColumnChart::create(array(
                    "title"=>"Collection & Aging Schedule (Juta)",
                    "dataSource"=>$this->params['collection_aging_schedule_pdf_chart'],
                    "columns"=>array(
                        "category",
                        "jumlah"=>array(
                            "label"=>"Jumlah",
                            "type"=>"number",
                            "style"=>function($row){
                                if(session('indexColorChart') == 1) {
                                    session(['indexColorChart' => session('indexColorChart')+1]);
                                    return "color:#4472c4";
                                }
                                else if(session('indexColorChart') == 2) {
                                    session(['indexColorChart' => session('indexColorChart')+1]);
                                    return "color:#ed7d31";
                                }
                                else {
                                    session(['indexColorChart' => 1]);
                                    return "color:#a5a5a5";
                                }
                            },
                            "annotation"=>function($row)
                            {
                                return number_format($row["jumlah"]);
                            }
                        ),
                    )
                ));
            }
        ?>
        <div class="page-break"></div>
        <span style="color: red;"><b>* Rp (Dalam Jutaan), Exclude PPN</b></span>
        <?php
            Table::create(array(
                "dataStore"=>$this->dataStore('finance_accounting_collection_aging_schedule_table2'),
                "headers"=>array(
                    array(
                        ""=>array("colSpan"=>1),
                        "TOTAL"=>array("colSpan"=>2),
                        "< 30 HARI"=>array("colSpan"=>2),
                        "30 - 60 HARI"=>array("colSpan"=>2),
                        "60 - 90 HARI"=>array("colSpan"=>2),
                        "> 90 HARI"=>array("colSpan"=>2),
                    )
                ),
                "themeBase"=>"bs4",
                "cssClass"=>array(
                    "table"=>"table table-striped table-bordered"
                ),
                'options' => [
                    'ordering' => false
                ]
            ));
        ?>
    </body>
</html>