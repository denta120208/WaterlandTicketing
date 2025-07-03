<?php

namespace App\Reports\Teknik\Budget;
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

class BudgetReport extends \koolreport\KoolReport {
    use \koolreport\laravel\Friendship;
    use \koolreport\export\Exportable;
    use \koolreport\excel\ExcelExportable;

    public $cut_off = NULL;

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
        $this->cut_off = $this->params["cut_off"];
        $this->teknikbudgettable1();
    }

    function teknikbudgettable1() {
        $dataSP = DB::select("EXEC sp_boc_budget_teknik '".$this->params["cut_off"]."', '".$this->params["project"]."'");
        $this->param['dataSP'] = $dataSP;

        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_budget_teknik @rt_period = :end_date, @project_no = :project
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new CalculatedColumn(array(
            "total_dev_cost_percent"=>function($row){
                $nominal = 0;
                for($i = 0; $i < count($this->param['dataSP']); $i++) {
                    $nominal += $this->param['dataSP'][$i]->DEV_COST;
                }

                if($nominal > 0) {
                    $persen = round((($row["DEV_COST"] / $nominal) * 100), 2);
                }
                else {
                    $persen = 0;
                }

                return $persen;
            },
            "total_perizinan_percent"=>function($row){
                $nominal = 0;
                for($i = 0; $i < count($this->param['dataSP']); $i++) {
                    $nominal += $this->param['dataSP'][$i]->PERIZINAN;
                }

                if($nominal > 0) {
                    $persen = round((($row["PERIZINAN"] / $nominal) * 100), 2);
                }
                else {
                    $persen = 0;
                }

                return $persen;
            },
            "total_fixed_asset_percent"=>function($row){
                $nominal = 0;
                for($i = 0; $i < count($this->param['dataSP']); $i++) {
                    $nominal += $this->param['dataSP'][$i]->FIXED_ASSET;
                }

                if($nominal > 0) {
                    $persen = round((($row["FIXED_ASSET"] / $nominal) * 100), 2);
                }
                else {
                    $persen = 0;
                }

                return $persen;
            },
            "total_konstruksi_percent"=>function($row){
                $nominal = 0;
                for($i = 0; $i < count($this->param['dataSP']); $i++) {
                    $nominal += $this->param['dataSP'][$i]->KONSTRUKSI;
                }

                if($nominal > 0) {
                    $persen = round((($row["KONSTRUKSI"] / $nominal) * 100), 2);
                }
                else {
                    $persen = 0;
                }

                return $persen;
            },
            "total_budget_percent"=>function($row){
                $nominal = 0;
                for($i = 0; $i < count($this->param['dataSP']); $i++) {
                    $nominal += $this->param['dataSP'][$i]->TOTAL_BUDGET;
                }

                if($nominal > 0) {
                    $persen = round((($row["TOTAL_BUDGET"] / $nominal) * 100), 2);
                }
                else {
                    $persen = 0;
                }

                return $persen;
            }
        )))
        ->pipe(new ColumnMeta(array(
            "TAHUN_SPK"=>array(
                'type' => 'string',
            ),
            "DEV_COST"=>array(
                // 'type' => 'number',
            ),
            "PERIZINAN"=>array(
                // 'type' => 'number',
            ),
            "FIXED_ASSET"=>array(
                // 'type' => 'number',
            ),
            "KONSTRUKSI"=>array(
                // 'type' => 'number',
            ),
            "TOTAL_BUDGET"=>array(
                // 'type' => 'number',
            ),
            "total_dev_cost_percent"=>array(
                'type' => 'number',
                "suffix" => "%",
            ),
            "total_perizinan_percent"=>array(
                'type' => 'number',
                "suffix" => "%",
            ),
            "total_fixed_asset_percent"=>array(
                'type' => 'number',
                "suffix" => "%",
            ),
            "total_konstruksi_percent"=>array(
                'type' => 'number',
                "suffix" => "%",
            ),
            "total_budget_percent"=>array(
                'type' => 'number',
                "suffix" => "%",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['DEV_COST'] > 0 ? $row['DEV_COST'] = $row['DEV_COST'] / 1000000 : $row['DEV_COST'] = 0;
                $row['PERIZINAN'] > 0 ? $row['PERIZINAN'] = $row['PERIZINAN'] / 1000000 : $row['PERIZINAN'] = 0;
                $row['FIXED_ASSET'] > 0 ? $row['FIXED_ASSET'] = $row['FIXED_ASSET'] / 1000000 : $row['FIXED_ASSET'] = 0;
                $row['KONSTRUKSI'] > 0 ? $row['KONSTRUKSI'] = $row['KONSTRUKSI'] / 1000000 : $row['KONSTRUKSI'] = 0;
                $row['TOTAL_BUDGET'] > 0 ? $row['TOTAL_BUDGET'] = $row['TOTAL_BUDGET'] / 1000000 : $row['TOTAL_BUDGET'] = 0;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('teknik_budget_table1'));
    }
}