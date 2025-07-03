<?php

namespace App\Reports\FinanceAccounting\SalesBacklog;
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

class SalesBacklogReport extends \koolreport\KoolReport {
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
        $this->sales_backlog_table();
    }

    function sales_backlog_table() {
        // $project_id = $this->params["project"];
        // $business_id = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = $project_id");

        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_backlog @rt_period = :cut_off, @project_no = :project
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"],
            // ":business_id"=>$business_id[0]->ID_BUSINESS_INT
        ))
        ->pipe(new ColumnMeta(array(
            "TAHUN"=>array(
                "label" => "BACKLOG ATAS MARKETING SALES",
                "type" => "string",
                "footerText"=>"<p><b>NET BACKLOG</b></p>"
            ),
            "SUM_AMOUNT_CURR"=>array(
                "label" => "JUMLAH",
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
            "UNIT_CURR"=>array(
                "label" => "UNIT",
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
            "SUM_AMOUNT_PLUS1"=>array(
                "label" => "JUMLAH",
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
            "UNIT_PLUS1"=>array(
                "label" => "UNIT",
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
            "SUM_AMOUNT_PLUS2"=>array(
                "label" => "JUMLAH",
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
            "UNIT_PLUS2"=>array(
                "label" => "UNIT",
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
            "SUM_AMOUNT_PLUS3"=>array(
                "label" => "JUMLAH",
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
            "UNIT_PLUS3"=>array(
                "label" => "UNIT",
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
            "TOTAL_AMT_SUM"=>array(
                "label" => "JUMLAH",
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
            "TOTAL_UNIT_SUM"=>array(
                "label" => "UNIT",
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // $row['TYPE_RETENSI'] = strtoupper($row['TYPE_RETENSI']);
                // $row['TITLE'] = "<p style='text-align: left;'>".$row['TITLE']."</p>";
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
        ->pipe($this->dataStore('finance_accounting_sales_backlog_table'));
    }
}
