<?php

namespace App\Reports\Teknik\HutangBangun;
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

class HutangBangunReport extends \koolreport\KoolReport {
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
        $this->teknikhutangbanguntable1();
    }

    function teknikhutangbanguntable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_hutang_bangun @rt_period = :end_date, @project_no = :project
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "TAHUN"=>array(
                'type' => 'string',
            ),
            "SPK_KELUAR_DEV_COST"=>array(
                // 'type' => 'number',
            ),
            "CASHOUT_DEV_COST"=>array(
                // 'type' => 'number',
            ),
            "SPK_KELUAR_PERIZINAN"=>array(
                // 'type' => 'number',
            ),
            "CASHOUT_PERIZINAN"=>array(
                // 'type' => 'number',
            ),
            "SPK_KELUAR_FIXED_ASSET"=>array(
                // 'type' => 'number',
            ),
            "CASHOUT_FIXED_ASSET"=>array(
                // 'type' => 'number',
            ),
            "SPK_KELUAR_KONSTRUKSI"=>array(
                // 'type' => 'number',
            ),
            "CASHOUT_KONSTRUKSI"=>array(
                // 'type' => 'number',
            ),
            "TOTAL_SPK_KELUAR"=>array(
                // 'type' => 'number',
            ),
            "TOTAL_CASHOUT"=>array(
                // 'type' => 'number',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['SPK_KELUAR_DEV_COST'] > 0 ? $row['SPK_KELUAR_DEV_COST'] = $row['SPK_KELUAR_DEV_COST'] / 1000000 : $row['SPK_KELUAR_DEV_COST'] = 0;
                $row['CASHOUT_DEV_COST'] > 0 ? $row['CASHOUT_DEV_COST'] = $row['CASHOUT_DEV_COST'] / 1000000 : $row['CASHOUT_DEV_COST'] = 0;
                
                $row['SPK_KELUAR_PERIZINAN'] > 0 ? $row['SPK_KELUAR_PERIZINAN'] = $row['SPK_KELUAR_PERIZINAN'] / 1000000 : $row['SPK_KELUAR_PERIZINAN'] = 0;
                $row['CASHOUT_PERIZINAN'] > 0 ? $row['CASHOUT_PERIZINAN'] = $row['CASHOUT_PERIZINAN'] / 1000000 : $row['CASHOUT_PERIZINAN'] = 0;
                
                $row['SPK_KELUAR_FIXED_ASSET'] > 0 ? $row['SPK_KELUAR_FIXED_ASSET'] = $row['SPK_KELUAR_FIXED_ASSET'] / 1000000 : $row['SPK_KELUAR_FIXED_ASSET'] = 0;
                $row['CASHOUT_FIXED_ASSET'] > 0 ? $row['CASHOUT_FIXED_ASSET'] = $row['CASHOUT_FIXED_ASSET'] / 1000000 : $row['CASHOUT_FIXED_ASSET'] = 0;
                
                $row['SPK_KELUAR_KONSTRUKSI'] > 0 ? $row['SPK_KELUAR_KONSTRUKSI'] = $row['SPK_KELUAR_KONSTRUKSI'] / 1000000 : $row['SPK_KELUAR_KONSTRUKSI'] = 0;
                $row['CASHOUT_KONSTRUKSI'] > 0 ? $row['CASHOUT_KONSTRUKSI'] = $row['CASHOUT_KONSTRUKSI'] / 1000000 : $row['CASHOUT_KONSTRUKSI'] = 0;
                
                $row['TOTAL_SPK_KELUAR'] > 0 ? $row['TOTAL_SPK_KELUAR'] = $row['TOTAL_SPK_KELUAR'] / 1000000 : $row['TOTAL_SPK_KELUAR'] = 0;
                $row['TOTAL_CASHOUT'] > 0 ? $row['TOTAL_CASHOUT'] = $row['TOTAL_CASHOUT'] / 1000000 : $row['TOTAL_CASHOUT'] = 0;
                
                return array($row);
            },
        )))
        ->pipe($this->dataStore('teknik_hutang_bangun_table1'));
    }
}