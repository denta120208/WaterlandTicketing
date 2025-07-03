<?php

namespace App\Reports\Teknik\KonsumsiGas;
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

class KonsumsiGasReport extends \koolreport\KoolReport {
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
        $this->teknikkonsumsigastable1();
        $this->teknikkonsumsigaschart1();
    }

    function teknikkonsumsigastable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_konsumsi_gas @rt_period_akhir = :end_date, @rt_period_awal = :start_date, @project = :project
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "BULAN"=>array(
                'type' => 'string',
            ),
            "NOMINAL_NUM"=>array(
                // 'type' => 'number',
            ),
            "DESCRIPTION_CHAR"=>array(
                // 'type' => 'number',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                if ($row['DESCRIPTION_CHAR'] == "HPP (Rp)") {
                    $row['NOMINAL_NUM'] = $row['NOMINAL_NUM'] / 1000;
                }
                if ($row['DESCRIPTION_CHAR'] == "Pendapatan Tenant (Rp)") {
                    $row['NOMINAL_NUM'] = $row['NOMINAL_NUM'] / 1000;
                }
                // if ($row['DESCRIPTION_CHAR'] == "Volume (m3)") {
                //     $row['NOMINAL_NUM'] = $row['NOMINAL_NUM'] / 1000;
                // }
                $row['BULAN'] = sprintf("%02d", $row['BULAN']);
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"BULAN",
                "row"=>"DESCRIPTION_CHAR"
            ),
            "aggregates"=>array(
                "sum"=>"NOMINAL_NUM",
            )
        )))
        ->pipe($this->dataStore('teknik_konsumsi_gas_table1'));
    }

    function teknikkonsumsigaschart1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_konsumsi_gas_chart @rt_period_akhir = :end_date, @rt_period_awal = :start_date, @project = :project
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['PENDAPATAN_TENANT'] = $row['PENDAPATAN_TENANT'] / 1000;
                $row['HPP'] = $row['HPP'] / 1000;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('teknik_konsumsi_gas_chart1'));

        // session(['konsumsiAirTenantYear1Chart1' => (int) date('Y', strtotime($this->params["cut_off"]))]);
        // session(['konsumsiAirTenantYear2Chart1' => (int) (date('Y', strtotime($this->params["cut_off"]))-1)]);
        // session(['konsumsiAirTenantYear3Chart1' => (int) (date('Y', strtotime($this->params["cut_off"]))-2)]);
    }
}