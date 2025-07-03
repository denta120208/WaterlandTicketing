<?php

namespace App\Reports\Marketing\DemografiAsalCustomer;
use \koolreport\processes\Filter;
use \koolreport\processes\ColumnMeta;
use \koolreport\pivot\processes\Pivot;
use \koolreport\processes\Map;
use \koolreport\processes\Sort;
use \koolreport\processes\CalculatedColumn;
use \koolreport\processes\AggregatedColumn;
use DateTime;
use DB;

require_once dirname(__FILE__)."/../../../../vendor/koolreport/core/autoload.php";

class DemografiAsalCustomerReport extends \koolreport\KoolReport {
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
        $this->demografi_asal_customer_provinsi_table();
        $this->demografi_asal_customer_kota_table();
    }

    function demografi_asal_customer_provinsi_table() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_demografi_asal_customer @rt_period = :cut_off, @project_no = :project, @group = :kategori
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"],
            ":kategori"=>"PROVINSI"
        ))
        ->pipe(new ColumnMeta(array(
            "TOTAL_RP"=>array(
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"TAHUN",
                "row"=>"PROVINSI"
            ),
            "aggregates"=>array(
                "sum"=>"TOTAL_UNIT, TOTAL_RP",
            )
        )))
        ->pipe($this->dataStore('marketing_demografi_asal_customer_provinsi_table'));
    }
    
    function demografi_asal_customer_kota_table() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_demografi_asal_customer @rt_period = :cut_off, @project_no = :project, @group = :kategori
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"],
            ":kategori"=>"KOTA"
        ))
        ->pipe(new ColumnMeta(array(
            "TOTAL_RP"=>array(
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"TAHUN",
                "row"=>"KOTA"
            ),
            "aggregates"=>array(
                "sum"=>"TOTAL_UNIT, TOTAL_RP",
            )
        )))
        ->pipe($this->dataStore('marketing_demografi_asal_customer_kota_table'));
    }
}
