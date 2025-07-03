<?php

namespace App\Reports\Teknik\HutangBayar;
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

class HutangBayarReport extends \koolreport\KoolReport {
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
        $this->teknikhutangbayartable1();
    }

    function teknikhutangbayartable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_hutang_bayar @end_period = :end_date, @project_no_char = :project
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "SPK_TRANS_TRX_DATE_YEAR"=>array(
                'type' => 'string',
            ),
            "HUTANG_BAYAR_DEV_COST"=>array(
                // 'type' => 'number',
            ),
            "TERBAYAR_DEV_COST"=>array(
                // 'type' => 'number',
            ),
            "SISA_HUTANG_DEV_COST"=>array(
                // 'type' => 'number',
            ),
            "HUTANG_BAYAR_PERIZINAN"=>array(
                // 'type' => 'number',
            ),
            "TERBAYAR_PERIZINAN"=>array(
                // 'type' => 'number',
            ),
            "SISA_HUTANG_PERIZINAN"=>array(
                // 'type' => 'number',
            ),
            "HUTANG_BAYAR_FIXED_ASSET"=>array(
                // 'type' => 'number',
            ),
            "TERBAYAR_FIXED_ASSET"=>array(
                // 'type' => 'number',
            ),
            "SISA_HUTANG_FIXED_ASSET"=>array(
                // 'type' => 'number',
            ),
            "HUTANG_BAYAR_KONSTRUKSI"=>array(
                // 'type' => 'number',
            ),
            "TERBAYAR_KONSTRUKSI"=>array(
                // 'type' => 'number',
            ),
            "SISA_HUTANG_KONSTRUKSI"=>array(
                // 'type' => 'number',
            ),
            "TERBAYAR_ALL"=>array(
                // 'type' => 'number',
            ),
            "SISA_HUTANG_ALL"=>array(
                // 'type' => 'number',
            ),
            "SERAPAN_NEXT_YEAR"=>array(
                // 'type' => 'number',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['HUTANG_BAYAR_DEV_COST'] > 0 ? $row['HUTANG_BAYAR_DEV_COST'] = $row['HUTANG_BAYAR_DEV_COST'] / 1000000 : $row['HUTANG_BAYAR_DEV_COST'] = 0;
                $row['TERBAYAR_DEV_COST'] > 0 ? $row['TERBAYAR_DEV_COST'] = $row['TERBAYAR_DEV_COST'] / 1000000 : $row['TERBAYAR_DEV_COST'] = 0;
                $row['SISA_HUTANG_DEV_COST'] > 0 ? $row['SISA_HUTANG_DEV_COST'] = $row['SISA_HUTANG_DEV_COST'] / 1000000 : $row['SISA_HUTANG_DEV_COST'] = 0;

                $row['HUTANG_BAYAR_PERIZINAN'] > 0 ? $row['HUTANG_BAYAR_PERIZINAN'] = $row['HUTANG_BAYAR_PERIZINAN'] / 1000000 : $row['HUTANG_BAYAR_PERIZINAN'] = 0;
                $row['TERBAYAR_PERIZINAN'] > 0 ? $row['TERBAYAR_PERIZINAN'] = $row['TERBAYAR_PERIZINAN'] / 1000000 : $row['TERBAYAR_PERIZINAN'] = 0;
                $row['SISA_HUTANG_PERIZINAN'] > 0 ? $row['SISA_HUTANG_PERIZINAN'] = $row['SISA_HUTANG_PERIZINAN'] / 1000000 : $row['SISA_HUTANG_PERIZINAN'] = 0;

                $row['HUTANG_BAYAR_FIXED_ASSET'] > 0 ? $row['HUTANG_BAYAR_FIXED_ASSET'] = $row['HUTANG_BAYAR_FIXED_ASSET'] / 1000000 : $row['HUTANG_BAYAR_FIXED_ASSET'] = 0;
                $row['TERBAYAR_FIXED_ASSET'] > 0 ? $row['TERBAYAR_FIXED_ASSET'] = $row['TERBAYAR_FIXED_ASSET'] / 1000000 : $row['TERBAYAR_FIXED_ASSET'] = 0;
                $row['SISA_HUTANG_FIXED_ASSET'] > 0 ? $row['SISA_HUTANG_FIXED_ASSET'] = $row['SISA_HUTANG_FIXED_ASSET'] / 1000000 : $row['SISA_HUTANG_FIXED_ASSET'] = 0;

                $row['HUTANG_BAYAR_KONSTRUKSI'] > 0 ? $row['HUTANG_BAYAR_KONSTRUKSI'] = $row['HUTANG_BAYAR_KONSTRUKSI'] / 1000000 : $row['HUTANG_BAYAR_KONSTRUKSI'] = 0;
                $row['TERBAYAR_KONSTRUKSI'] > 0 ? $row['TERBAYAR_KONSTRUKSI'] = $row['TERBAYAR_KONSTRUKSI'] / 1000000 : $row['TERBAYAR_KONSTRUKSI'] = 0;
                $row['SISA_HUTANG_KONSTRUKSI'] > 0 ? $row['SISA_HUTANG_KONSTRUKSI'] = $row['SISA_HUTANG_KONSTRUKSI'] / 1000000 : $row['SISA_HUTANG_KONSTRUKSI'] = 0;

                $row['TERBAYAR_ALL'] > 0 ? $row['TERBAYAR_ALL'] = $row['TERBAYAR_ALL'] / 1000000 : $row['TERBAYAR_ALL'] = 0;
                $row['SISA_HUTANG_ALL'] > 0 ? $row['SISA_HUTANG_ALL'] = $row['SISA_HUTANG_ALL'] / 1000000 : $row['SISA_HUTANG_ALL'] = 0;
                $row['SERAPAN_NEXT_YEAR'] > 0 ? $row['SERAPAN_NEXT_YEAR'] = $row['SERAPAN_NEXT_YEAR'] / 1000000 : $row['SERAPAN_NEXT_YEAR'] = 0;
                return array($row);
            },
        )))
        ->pipe($this->dataStore('teknik_hutang_bayar_table1'));
    }
}