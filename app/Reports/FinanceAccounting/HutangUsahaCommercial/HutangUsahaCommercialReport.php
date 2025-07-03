<?php

namespace App\Reports\FinanceAccounting\HutangUsahaCommercial;
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

class HutangUsahaCommercialReport extends \koolreport\KoolReport {
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
        $this->hutangusahacommercialtable1();
    }

    function hutangusahacommercialtable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_hutang_usaha @rt_period_akhir = :cut_off, @rt_period_awal = :start_date, @project = :project
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":start_date"=>$this->params["start_date"],
            ":project"=>$this->params["project"]
        ))
        ->pipe(new ColumnMeta(array(
            "TAHUN"=>array(
                "type" => "string",
            ),
            "KETERANGAN"=>array(
                "label" => "KETERANGAN",
            ),
            "BALANCE_INT"=>array(
                "label" => "BALANCE_INT",
            ),
            "GROWTH"=>array(
                "label" => "GROWTH",
                "suffix" => "%"
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['BALANCE_INT'] = $row['BALANCE_INT'] / 1000;
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"TAHUN",
                "row"=>"KETERANGAN"
            ),
            "aggregates"=>array(
                "sum"=>"BALANCE_INT, GROWTH",
            )
        )))
        ->pipe($this->dataStore('finance_accounting_hutang_usaha_commercial_table1'));
    }
}
