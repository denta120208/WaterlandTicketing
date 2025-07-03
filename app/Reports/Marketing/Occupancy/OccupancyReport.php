<?php

namespace App\Reports\Marketing\Occupancy;
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

class OccupancyReport extends \koolreport\KoolReport {
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
        $this->occupancy_table();
    }

    function occupancy_table() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_tenant_occupancy @rt_period = :cut_off, @project = :project
        ")
        ->params(array(
            ":cut_off"=>$this->params["cut_off"],
            ":project"=>$this->params["project"]
        ))
        ->pipe(new ColumnMeta(array(
            "PSM_CATEGORY_NAME"=>array(
                "type" => "string",
            ),
            "ANCHOR_TENANT"=>array(
                "type" => "number",
            ),
            "NON_ANCHOR_TENANT"=>array(
                "type" => "number",
            ),
            "LEASEABLE_AREA"=>array(
                "type" => "number",
            ),
            "NET_LEASED_LUAS"=>array(
                "type" => "number",
            ),
            "NET_LEASED_LUAS_PERSEN"=>array(
                "type" => "number",
                "suffix" => "%"
            ),
            "AVAILABLE_AREA"=>array(
                "type" => "number",
            ),
            "AVAILABLE_AREA_PERSEN"=>array(
                "type" => "number",
                "suffix" => "%"
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
        )))
        ->pipe($this->dataStore('marketing_accupancy_table'));
    }
}
