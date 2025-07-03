<?php

namespace App\Reports\FinanceAccounting\CollectionAgingScheduleCommercial;
use \koolreport\processes\Filter;
use \koolreport\processes\ColumnMeta;
use \koolreport\pivot\processes\Pivot;
use \koolreport\processes\Map;
use \koolreport\processes\Sort;
use \koolreport\processes\CalculatedColumn;
use \koolreport\processes\AggregatedColumn;
use \koolreport\processes\Transpose;
use \koolreport\processes\Transpose2;
use \koolreport\processes\ColumnRename;
use \koolreport\processes\Group;
use DateTime;
use DB;

require_once dirname(__FILE__)."/../../../../vendor/koolreport/core/autoload.php";

class CollectionAgingScheduleCommercialReport extends \koolreport\KoolReport {
    use \koolreport\laravel\Friendship;
    use \koolreport\export\Exportable;
    use \koolreport\excel\ExcelExportable;

    function settings()
    {
        $host = env('DB_HOST2');
        $database = env('DB_DATABASE2');
        $username = env('DB_USERNAME2');
        $password = env('DB_PASSWORD2');
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
        $this->collectionagingschedulecommercialtable1();
    }

    function collectionagingschedulecommercialtable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_collection_aging_schedule @rt_period_akhir = :cut_off, @rt_period_awal = :start_date, @rt_backward_year = :backward_date, @project = :project
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":start_date"=>$this->params["start_date"],
            ":backward_date"=>$this->params["backward_date"],
            ":project"=>$this->params["project"],
        ))
        ->pipe(new ColumnMeta(array(
            "DESC_TYPE"=>array(
                "label" => "DESCRIPTION",
                "type" => "string",
            ),
            "COLLECTED_BACKWARD_YEAR"=>array(
                "label" => "COLLECTED",
            ),
            "COLLECTED_CURRENT_YEAR"=>array(
                "label" => "COLLECTED",
            ),
            "AGING_CURRENT_YEAR"=>array(
                "label" => "TOTAL AGING",
            ),
            "TARGET_COLLECTED_CURRENT_YEAR"=>array(
                "label" => "TARGET COLLECTED",
            ),
            "COLLECTABILITY_CURRENT_YEAR"=>array(
                "label" => "COLLECTABILITY",
                "suffix" => "%",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['COLLECTED_BACKWARD_YEAR'] = $row['COLLECTED_BACKWARD_YEAR'] / 1000000;
                $row['COLLECTED_CURRENT_YEAR'] = $row['COLLECTED_CURRENT_YEAR'] / 1000000;
                $row['AGING_CURRENT_YEAR'] = $row['AGING_CURRENT_YEAR'] / 1000000;
                $row['TARGET_COLLECTED_CURRENT_YEAR'] = $row['TARGET_COLLECTED_CURRENT_YEAR'] / 1000000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('finance_accounting_collection_aging_schedule_commercial_table1'));
    }
}
