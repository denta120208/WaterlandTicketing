<?php

namespace App\Reports\FinanceAccounting\CollectionAgingSchedule;
use \koolreport\processes\Filter;
use \koolreport\processes\ColumnMeta;
use \koolreport\pivot\processes\Pivot;
use \koolreport\processes\Map;
use \koolreport\processes\Sort;
use \koolreport\processes\CalculatedColumn;
use \koolreport\processes\AggregatedColumn;
use \koolreport\datagrid\DataTables;
use \koolreport\widgets\koolphp\Table;
use DateTime;
use DB;

require_once dirname(__FILE__)."/../../../../vendor/koolreport/core/autoload.php";

class CollectionAgingScheduleReport extends \koolreport\KoolReport {
    use \koolreport\laravel\Friendship;
    use \koolreport\export\Exportable;
    use \koolreport\excel\ExcelExportable;

    function settings()
    {
        $host = env('DB_HOST');
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        return array(
            "dataSources" => array(
                "sqlDataSources"=>array(
                    'host' => ''.$host.'',
                    'username' => ''.$username.'',
                    'password' => ''.$password.'',
                    'dbname' => ''.$database.'',
                    'class' => "\koolreport\datasources\SQLSRVDataSource"
                ),
                "arrDataSources"=>array(
                    "class"=>'\koolreport\datasources\ArrayDataSource',
                    "data"=>$this->collection_aging_schedule_excel_chart_raw_data(),
                    "dataFormat"=>"associate",
                ),
            )
        );
    }

    function setup()
    {
        $this->collection_aging_schedule_table1();
        $this->collection_aging_schedule_table2();
        $this->collection_aging_schedule_chart();

        $this->collection_aging_schedule_excel_table1();
        $this->collection_aging_schedule_excel_table2();
        $this->collection_aging_schedule_excel_chart();
        $this->collection_aging_schedule_pdf_chart();
    }

    function collection_aging_schedule_table1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_collection_and_aging_schedule @rt_year_schedule = :year_schedule, @rt_period = :cut_off, @project_no = :project
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"],
            ":year_schedule"=>$this->params["year_schedule"]
        ))
        ->pipe(new ColumnMeta(array(
            "TITLE"=>array(
                "label" => "",
                'type' => 'string',
            ),
            "COLLECTED_BACKWARD1"=>array(
                "label" => "COLLECTED",
                'type' => 'string',
            ),
            "COLLECTED_CURRENT"=>array(
                "label" => "COLLECTED",
                'type' => 'string',
            ),
            "TOTAL_AGING"=>array(
                "label" => "TOTAL AGING",
                'type' => 'string',
            ),
            "TARGET_COLLECTED_CURRENT"=>array(
                "label" => "TARGET COLLECTED",
                'type' => 'string',
            ),
            "COLLECTABILITY_CURRENT"=>array(
                "label" => "COLLECTABILITY",
                'type' => 'string',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                if($row['TITLE'] == "TOTAL") {
                    $row['TITLE'] = "<p><b>".$row['TITLE']."</b></p>";
                    $row['COLLECTED_BACKWARD1'] = "<p><b>".number_format($row['COLLECTED_BACKWARD1'])."</b></p>";
                    $row['COLLECTED_CURRENT'] = "<p><b>".number_format($row['COLLECTED_CURRENT'])."</b></p>";
                    $row['TOTAL_AGING'] = "<p><b>".number_format($row['TOTAL_AGING'])."</b></p>";
                    $row['TARGET_COLLECTED_CURRENT'] = "<p><b>".number_format($row['TARGET_COLLECTED_CURRENT'])."</b></p>";
                    $row['COLLECTABILITY_CURRENT'] = "<p><b>".number_format($row['COLLECTABILITY_CURRENT'])."%</b></p>";
                }
                else {
                    $row['TITLE'] = $row['TITLE'];
                    $row['COLLECTED_BACKWARD1'] = number_format($row['COLLECTED_BACKWARD1']);
                    $row['COLLECTED_CURRENT'] = number_format($row['COLLECTED_CURRENT']);
                    $row['TOTAL_AGING'] = number_format($row['TOTAL_AGING']);
                    $row['TARGET_COLLECTED_CURRENT'] = number_format($row['TARGET_COLLECTED_CURRENT']);
                    $row['COLLECTABILITY_CURRENT'] = number_format($row['COLLECTABILITY_CURRENT'])."%";
                }
                // $row['HARGA_JUAL'] = $row['HARGA_JUAL'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('finance_accounting_collection_aging_schedule_table1'));
    }

    function collection_aging_schedule_table2() {
        $project_id = $this->params["project"];
        $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");

        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_collection_and_aging_schedule_2 @rt_period = :cut_off, @project_no = :project, @business_id = :business_id
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"],
            ":business_id"=>$business_id[0]->ID_BUSINESS_INT
        ))
        ->pipe(new ColumnMeta(array(
            "CARA_BAYAR"=>array(
                "label" => "CARA BAYAR",
                'type' => 'string',
            ),
            "TOTAL_JUMLAH"=>array(
                "label" => "JUMLAH",
                'type' => 'string',
            ),
            "TOTAL_UNITS"=>array(
                "label" => "UNITS",
                'type' => 'string',
            ),
            "UNDER_30_DAYS_JUMLAH"=>array(
                "label" => "JUMLAH",
                'type' => 'string',
            ),
            "UNDER_30_DAYS_UNITS"=>array(
                "label" => "UNITS",
                'type' => 'string',
            ),
            "DAYS_31_UNTIL_60_JUMLAH"=>array(
                "label" => "JUMLAH",
                'type' => 'string',
            ),
            "DAYS_31_UNTIL_60_UNITS"=>array(
                "label" => "UNITS",
                'type' => 'string',
            ),
            "DAYS_61_UNTIL_90_JUMLAH"=>array(
                "label" => "JUMLAH",
                'type' => 'string',
            ),
            "DAYS_61_UNTIL_90_UNITS"=>array(
                "label" => "UNITS",
                'type' => 'string',
            ),
            "OVER_91_JUMLAH"=>array(
                "label" => "JUMLAH",
                'type' => 'string',
            ),
            "OVER_91_UNITS"=>array(
                "label" => "UNITS",
                'type' => 'string',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                if($row['CARA_BAYAR'] == "TOTAL") {
                    $row['CARA_BAYAR'] = "<p><b>".strtoupper($row['CARA_BAYAR'])."</b></p>";
                    $row['TOTAL_JUMLAH'] = "<p><b>".number_format($row['TOTAL_JUMLAH'])."</b></p>";
                    $row['TOTAL_UNITS'] = "<p><b>".number_format($row['TOTAL_UNITS'])."</b></p>";
                    $row['UNDER_30_DAYS_JUMLAH'] = "<p><b>".number_format($row['UNDER_30_DAYS_JUMLAH'])."</b></p>";
                    $row['UNDER_30_DAYS_UNITS'] = "<p><b>".number_format($row['UNDER_30_DAYS_UNITS'])."</b></p>";
                    $row['DAYS_31_UNTIL_60_JUMLAH'] = "<p><b>".number_format($row['DAYS_31_UNTIL_60_JUMLAH'])."</b></p>";
                    $row['DAYS_31_UNTIL_60_UNITS'] = "<p><b>".number_format($row['DAYS_31_UNTIL_60_UNITS'])."</b></p>";
                    $row['DAYS_61_UNTIL_90_JUMLAH'] = "<p><b>".number_format($row['DAYS_61_UNTIL_90_JUMLAH'])."</b></p>";
                    $row['DAYS_61_UNTIL_90_UNITS'] = "<p><b>".number_format($row['DAYS_61_UNTIL_90_UNITS'])."</b></p>";
                    $row['OVER_91_JUMLAH'] = "<p><b>".number_format($row['OVER_91_JUMLAH'])."</b></p>";
                    $row['OVER_91_UNITS'] = "<p><b>".number_format($row['OVER_91_UNITS'])."</b></p>";
                }
                else {
                    $row['CARA_BAYAR'] = strtoupper($row['CARA_BAYAR']);
                    $row['TOTAL_JUMLAH'] = number_format($row['TOTAL_JUMLAH']);
                    $row['TOTAL_UNITS'] = number_format($row['TOTAL_UNITS']);
                    $row['UNDER_30_DAYS_JUMLAH'] = number_format($row['UNDER_30_DAYS_JUMLAH']);
                    $row['UNDER_30_DAYS_UNITS'] = number_format($row['UNDER_30_DAYS_UNITS']);
                    $row['DAYS_31_UNTIL_60_JUMLAH'] = number_format($row['DAYS_31_UNTIL_60_JUMLAH']);
                    $row['DAYS_31_UNTIL_60_UNITS'] = number_format($row['DAYS_31_UNTIL_60_UNITS']);
                    $row['DAYS_61_UNTIL_90_JUMLAH'] = number_format($row['DAYS_61_UNTIL_90_JUMLAH']);
                    $row['DAYS_61_UNTIL_90_UNITS'] = number_format($row['DAYS_61_UNTIL_90_UNITS']);
                    $row['OVER_91_JUMLAH'] = number_format($row['OVER_91_JUMLAH']);
                    $row['OVER_91_UNITS'] = number_format($row['OVER_91_UNITS']);
                }
                // $row['HARGA_JUAL'] = $row['HARGA_JUAL'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('finance_accounting_collection_aging_schedule_table2'));
    }

    function collection_aging_schedule_chart() {
        $project_id = $this->params["project"];
        $cut_off = $this->params["cut_off"];
        $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");
        $business_id = $business_id[0]->ID_BUSINESS_INT;

        $dataRawChart = DB::select("EXEC sp_boc_collection_and_aging_schedule_2 @rt_period = '".$cut_off."', @project_no = $project_id, @business_id = $business_id");

        $dataChart = array(
            array("category"=>"< 30 HARI","jumlah"=>$dataRawChart[count($dataRawChart)-1]->UNDER_30_DAYS_JUMLAH,"unit"=>$dataRawChart[count($dataRawChart)-1]->UNDER_30_DAYS_UNITS),
            array("category"=>"30 - 60 HARI","jumlah"=>$dataRawChart[count($dataRawChart)-1]->DAYS_31_UNTIL_60_JUMLAH, "unit"=>$dataRawChart[count($dataRawChart)-1]->DAYS_31_UNTIL_60_UNITS),
            array("category"=>"60 - 90 HARI","jumlah"=>$dataRawChart[count($dataRawChart)-1]->DAYS_61_UNTIL_90_JUMLAH, "unit"=>$dataRawChart[count($dataRawChart)-1]->DAYS_61_UNTIL_90_UNITS),
            array("category"=>"> 90 HARI","jumlah"=>$dataRawChart[count($dataRawChart)-1]->OVER_91_JUMLAH, "unit"=>$dataRawChart[count($dataRawChart)-1]->OVER_91_UNITS),
        );

        session(['collection_aging_schedule_chart' => $dataChart]);
    }

    function collection_aging_schedule_excel_table1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_collection_and_aging_schedule @rt_year_schedule = :year_schedule, @rt_period = :cut_off, @project_no = :project
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"],
            ":year_schedule"=>$this->params["year_schedule"]
        ))
        ->pipe(new ColumnMeta(array(
            "TITLE"=>array(
                "label" => "",
                'type' => 'string',
            ),
            "COLLECTED_BACKWARD1"=>array(
                "label" => "COLLECTED",
                'type' => 'string',
            ),
            "COLLECTED_CURRENT"=>array(
                "label" => "COLLECTED",
                'type' => 'string',
            ),
            "TOTAL_AGING"=>array(
                "label" => "TOTAL AGING",
                'type' => 'string',
            ),
            "TARGET_COLLECTED_CURRENT"=>array(
                "label" => "TARGET COLLECTED",
                'type' => 'string',
            ),
            "COLLECTABILITY_CURRENT"=>array(
                "label" => "COLLECTABILITY",
                'type' => 'string',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['TITLE'] = $row['TITLE'];
                $row['COLLECTED_BACKWARD1'] = number_format($row['COLLECTED_BACKWARD1']);
                $row['COLLECTED_CURRENT'] = number_format($row['COLLECTED_CURRENT']);
                $row['TOTAL_AGING'] = number_format($row['TOTAL_AGING']);
                $row['TARGET_COLLECTED_CURRENT'] = number_format($row['TARGET_COLLECTED_CURRENT']);
                $row['COLLECTABILITY_CURRENT'] = number_format($row['COLLECTABILITY_CURRENT'])."%";
                // $row['HARGA_JUAL'] = $row['HARGA_JUAL'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('finance_accounting_collection_aging_schedule_excel_table1'));
    }

    function collection_aging_schedule_excel_table2() {
        $project_id = $this->params["project"];
        $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");

        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_collection_and_aging_schedule_2 @rt_period = :cut_off, @project_no = :project, @business_id = :business_id
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"],
            ":business_id"=>$business_id[0]->ID_BUSINESS_INT
        ))
        ->pipe(new ColumnMeta(array(
            "CARA_BAYAR"=>array(
                "label" => "CARA BAYAR",
                'type' => 'string',
            ),
            "TOTAL_JUMLAH"=>array(
                "label" => "JUMLAH",
                'type' => 'string',
            ),
            "TOTAL_UNITS"=>array(
                "label" => "UNITS",
                'type' => 'string',
            ),
            "UNDER_30_DAYS_JUMLAH"=>array(
                "label" => "JUMLAH",
                'type' => 'string',
            ),
            "UNDER_30_DAYS_UNITS"=>array(
                "label" => "UNITS",
                'type' => 'string',
            ),
            "DAYS_31_UNTIL_60_JUMLAH"=>array(
                "label" => "JUMLAH",
                'type' => 'string',
            ),
            "DAYS_31_UNTIL_60_UNITS"=>array(
                "label" => "UNITS",
                'type' => 'string',
            ),
            "DAYS_61_UNTIL_90_JUMLAH"=>array(
                "label" => "JUMLAH",
                'type' => 'string',
            ),
            "DAYS_61_UNTIL_90_UNITS"=>array(
                "label" => "UNITS",
                'type' => 'string',
            ),
            "OVER_91_JUMLAH"=>array(
                "label" => "JUMLAH",
                'type' => 'string',
            ),
            "OVER_91_UNITS"=>array(
                "label" => "UNITS",
                'type' => 'string',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['CARA_BAYAR'] = strtoupper($row['CARA_BAYAR']);
                $row['TOTAL_JUMLAH'] = number_format($row['TOTAL_JUMLAH']);
                $row['TOTAL_UNITS'] = number_format($row['TOTAL_UNITS']);
                $row['UNDER_30_DAYS_JUMLAH'] = number_format($row['UNDER_30_DAYS_JUMLAH']);
                $row['UNDER_30_DAYS_UNITS'] = number_format($row['UNDER_30_DAYS_UNITS']);
                $row['DAYS_31_UNTIL_60_JUMLAH'] = number_format($row['DAYS_31_UNTIL_60_JUMLAH']);
                $row['DAYS_31_UNTIL_60_UNITS'] = number_format($row['DAYS_31_UNTIL_60_UNITS']);
                $row['DAYS_61_UNTIL_90_JUMLAH'] = number_format($row['DAYS_61_UNTIL_90_JUMLAH']);
                $row['DAYS_61_UNTIL_90_UNITS'] = number_format($row['DAYS_61_UNTIL_90_UNITS']);
                $row['OVER_91_JUMLAH'] = number_format($row['OVER_91_JUMLAH']);
                $row['OVER_91_UNITS'] = number_format($row['OVER_91_UNITS']);
                // $row['HARGA_JUAL'] = $row['HARGA_JUAL'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('finance_accounting_collection_aging_schedule_excel_table2'));
    }
    
    function collection_aging_schedule_excel_chart() {
        // $project_id = $this->params["project"];
        // $cut_off = $this->params["cut_off"];
        // $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");
        // $business_id = $business_id[0]->ID_BUSINESS_INT;

        // $dataRawChart = DB::select("EXEC sp_collection_and_aging_schedule_2 @rt_period = '".$cut_off."', @project_no = $project_id, @business_id = $business_id");

        // $dataChart = array(
        //     array("category"=>"< 30 HARI","jumlah"=>$dataRawChart[count($dataRawChart)-1]->UNDER_30_DAYS_JUMLAH,"unit"=>$dataRawChart[count($dataRawChart)-1]->UNDER_30_DAYS_UNITS),
        //     array("category"=>"30 - 60 HARI","jumlah"=>$dataRawChart[count($dataRawChart)-1]->DAYS_31_UNTIL_60_JUMLAH, "unit"=>$dataRawChart[count($dataRawChart)-1]->DAYS_31_UNTIL_60_UNITS),
        //     array("category"=>"60 - 90 HARI","jumlah"=>$dataRawChart[count($dataRawChart)-1]->DAYS_61_UNTIL_90_JUMLAH, "unit"=>$dataRawChart[count($dataRawChart)-1]->DAYS_61_UNTIL_90_UNITS),
        //     array("category"=>"> 90 HARI","jumlah"=>$dataRawChart[count($dataRawChart)-1]->OVER_91_JUMLAH, "unit"=>$dataRawChart[count($dataRawChart)-1]->OVER_91_UNITS),
        // );

        $this->src('arrDataSources')
        ->pipe($this->dataStore('collection_aging_schedule_excel_chart'));
    }

    function collection_aging_schedule_excel_chart_raw_data() {
        $project_id = $this->params["project"];
        $cut_off = $this->params["cut_off"];
        $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");
        $business_id = $business_id[0]->ID_BUSINESS_INT;

        $dataRawChart = DB::select("EXEC sp_boc_collection_and_aging_schedule_2 @rt_period = '".$cut_off."', @project_no = $project_id, @business_id = $business_id");

        $dataChart = array(
            array("category"=>"< 30 HARI","jumlah"=>$dataRawChart[count($dataRawChart)-1]->UNDER_30_DAYS_JUMLAH,"unit"=>$dataRawChart[count($dataRawChart)-1]->UNDER_30_DAYS_UNITS),
            array("category"=>"30 - 60 HARI","jumlah"=>$dataRawChart[count($dataRawChart)-1]->DAYS_31_UNTIL_60_JUMLAH, "unit"=>$dataRawChart[count($dataRawChart)-1]->DAYS_31_UNTIL_60_UNITS),
            array("category"=>"60 - 90 HARI","jumlah"=>$dataRawChart[count($dataRawChart)-1]->DAYS_61_UNTIL_90_JUMLAH, "unit"=>$dataRawChart[count($dataRawChart)-1]->DAYS_61_UNTIL_90_UNITS),
            array("category"=>"> 90 HARI","jumlah"=>$dataRawChart[count($dataRawChart)-1]->OVER_91_JUMLAH, "unit"=>$dataRawChart[count($dataRawChart)-1]->OVER_91_UNITS),
        );

        return $dataChart;
    }

    function collection_aging_schedule_pdf_chart() {
        $project_id = $this->params["project"];
        $cut_off = $this->params["cut_off"];
        $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");
        $business_id = $business_id[0]->ID_BUSINESS_INT;

        $dataRawChart = DB::select("EXEC sp_boc_collection_and_aging_schedule_2 @rt_period = '".$cut_off."', @project_no = $project_id, @business_id = $business_id");

        $dataChart = array(
            array("category"=>"< 30 HARI","jumlah"=>$dataRawChart[count($dataRawChart)-1]->UNDER_30_DAYS_JUMLAH,"unit"=>$dataRawChart[count($dataRawChart)-1]->UNDER_30_DAYS_UNITS),
            array("category"=>"30 - 60 HARI","jumlah"=>$dataRawChart[count($dataRawChart)-1]->DAYS_31_UNTIL_60_JUMLAH, "unit"=>$dataRawChart[count($dataRawChart)-1]->DAYS_31_UNTIL_60_UNITS),
            array("category"=>"60 - 90 HARI","jumlah"=>$dataRawChart[count($dataRawChart)-1]->DAYS_61_UNTIL_90_JUMLAH, "unit"=>$dataRawChart[count($dataRawChart)-1]->DAYS_61_UNTIL_90_UNITS),
            array("category"=>"> 90 HARI","jumlah"=>$dataRawChart[count($dataRawChart)-1]->OVER_91_JUMLAH, "unit"=>$dataRawChart[count($dataRawChart)-1]->OVER_91_UNITS),
        );

        $this->params["collection_aging_schedule_pdf_chart"] = $dataChart;
        // session(['collection_aging_schedule_chart' => $dataChart]);
    }
}
