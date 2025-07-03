<?php

namespace App\Reports\FinanceAccounting\AgingOver90Days;
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

class AgingOver90DaysReport extends \koolreport\KoolReport {
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
            )
        );
    }

    function setup()
    {
        $this->aging_over_90_days_table();
        $this->aging_over_90_days_excel_table();
    }

    function aging_over_90_days_table() {
        $project_id = $this->params["project"];
        $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");

        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_aging_over_90_days @rt_period = :cut_off, @project_no = :project, @business_id = :business_id
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"],
            ":business_id"=>$business_id[0]->ID_BUSINESS_INT
        ))
        ->pipe(new ColumnMeta(array(
            "NAMA"=>array(
                "label" => "NAMA",
            ),
            "BLOK"=>array(
                "label" => "BLOK",
            ),
            "TAHUN"=>array(
                "label" => "TAHUN",
                'type' => 'string',
            ),
            "CARA_BAYAR"=>array(
                "label" => "CARA BAYAR",
            ),
            "TOTAL_HARGA"=>array(
                "label" => "TOTAL HARGA",
            ),
            "UANG_MASUK_TOTAL"=>array(
                "label" => "TOTAL",
            ),
            "UANG_MASUK_PERSEN"=>array(
                "label" => "%",
                'type' => "string",
            ),
            "TUNGGAKAN"=>array(
                "label" => "> 90 HARI",
            ),
            "TERAKHIR_BAYAR"=>array(
                "label" => "TERAKHIR BAYAR",
            ),
            "KET"=>array(
                "label" => "KET",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['UANG_MASUK_PERSEN'] = number_format($row['UANG_MASUK_PERSEN'])."%";
                $row['NAMA'] = "<p style='text-align: left;'>".$row['NAMA']."</p>";
                $row['KET'] = "<p style='text-align: left;'>".$row['KET']."</p>";
                // if($row['TITLE'] == "TOTAL") {
                //     $row['TITLE'] = "<p><b>".$row['TITLE']."</b></p>";
                //     $row['COLLECTED_BACKWARD1'] = "<p><b>".number_format($row['COLLECTED_BACKWARD1'])."</b></p>";
                //     $row['COLLECTED_CURRENT'] = "<p><b>".number_format($row['COLLECTED_CURRENT'])."</b></p>";
                //     $row['TOTAL_AGING'] = "<p><b>".$row['TOTAL_AGING']."</b></p>";
                //     $row['TARGET_COLLECTED_CURRENT'] = "<p><b>".number_format($row['TARGET_COLLECTED_CURRENT'])."</b></p>";
                //     $row['COLLECTABILITY_CURRENT'] = "<p><b>".$row['COLLECTABILITY_CURRENT']."%</b></p>";
                // }
                // else {
                //     $row['TITLE'] = $row['TITLE'];
                //     $row['COLLECTED_BACKWARD1'] = number_format($row['COLLECTED_BACKWARD1']);
                //     $row['COLLECTED_CURRENT'] = number_format($row['COLLECTED_CURRENT']);
                //     $row['TOTAL_AGING'] = $row['TOTAL_AGING'];
                //     $row['TARGET_COLLECTED_CURRENT'] = number_format($row['TARGET_COLLECTED_CURRENT']);
                //     $row['COLLECTABILITY_CURRENT'] = $row['COLLECTABILITY_CURRENT']."%";
                // }
                // $row['HARGA_JUAL'] = $row['HARGA_JUAL'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('finance_accounting_aging_over_90_days_table'));
    }

    function aging_over_90_days_excel_table() {
        $project_id = $this->params["project"];
        $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");

        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_aging_over_90_days @rt_period = :cut_off, @project_no = :project, @business_id = :business_id
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"],
            ":business_id"=>$business_id[0]->ID_BUSINESS_INT
        ))
        ->pipe(new ColumnMeta(array(
            "NAMA"=>array(
                "label" => "NAMA",
            ),
            "BLOK"=>array(
                "label" => "BLOK",
            ),
            "TAHUN"=>array(
                "label" => "TAHUN",
                'type' => 'string',
            ),
            "CARA_BAYAR"=>array(
                "label" => "CARA BAYAR",
            ),
            "TOTAL_HARGA"=>array(
                "label" => "TOTAL HARGA",
            ),
            "UANG_MASUK_TOTAL"=>array(
                "label" => "TOTAL",
            ),
            "UANG_MASUK_PERSEN"=>array(
                "label" => "%",
                'type' => "string",
            ),
            "TUNGGAKAN"=>array(
                "label" => "> 90 HARI",
            ),
            "TERAKHIR_BAYAR"=>array(
                "label" => "TERAKHIR BAYAR",
            ),
            "KET"=>array(
                "label" => "KET",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['UANG_MASUK_PERSEN'] = number_format($row['UANG_MASUK_PERSEN'])."%";
                $row['NAMA'] = $row['NAMA'];
                $row['KET'] = $row['KET'];
                // if($row['TITLE'] == "TOTAL") {
                //     $row['TITLE'] = "<p><b>".$row['TITLE']."</b></p>";
                //     $row['COLLECTED_BACKWARD1'] = "<p><b>".number_format($row['COLLECTED_BACKWARD1'])."</b></p>";
                //     $row['COLLECTED_CURRENT'] = "<p><b>".number_format($row['COLLECTED_CURRENT'])."</b></p>";
                //     $row['TOTAL_AGING'] = "<p><b>".$row['TOTAL_AGING']."</b></p>";
                //     $row['TARGET_COLLECTED_CURRENT'] = "<p><b>".number_format($row['TARGET_COLLECTED_CURRENT'])."</b></p>";
                //     $row['COLLECTABILITY_CURRENT'] = "<p><b>".$row['COLLECTABILITY_CURRENT']."%</b></p>";
                // }
                // else {
                //     $row['TITLE'] = $row['TITLE'];
                //     $row['COLLECTED_BACKWARD1'] = number_format($row['COLLECTED_BACKWARD1']);
                //     $row['COLLECTED_CURRENT'] = number_format($row['COLLECTED_CURRENT']);
                //     $row['TOTAL_AGING'] = $row['TOTAL_AGING'];
                //     $row['TARGET_COLLECTED_CURRENT'] = number_format($row['TARGET_COLLECTED_CURRENT']);
                //     $row['COLLECTABILITY_CURRENT'] = $row['COLLECTABILITY_CURRENT']."%";
                // }
                // $row['HARGA_JUAL'] = $row['HARGA_JUAL'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('finance_accounting_aging_over_90_days_excel_table'));
    }
}
