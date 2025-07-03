<?php

namespace App\Reports\Teknik\SerahTerima;
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

class SerahTerimaReport extends \koolreport\KoolReport {
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
        $this->teknikserahterimatable1();
        $this->teknikserahterimatableexcel1();
    }

    function teknikserahterimatable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_serah_terima_teknik @rt_period = :end_date, @project_no = :project
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "TOWER_NAME"=>array(
                'type' => 'string',
            ),
            "REALISASI_COUNT_BACKWARD_YEAR"=>array(
                // 'type' => 'number',
            ),
            "TARGET_COUNT_CURRENT_YEAR"=>array(
                // 'type' => 'number',
            ),
            "REALISASI_COUNT_CURRENT_YEAR"=>array(
                // 'type' => 'number',
            ),
            "PERSEN_CURRENT_YEAR"=>array(
                // 'type' => 'number',
            ),
            "HUNI_BANGUN_CURRENT_YEAR"=>array(
                // 'type' => 'number',
            ),
            "TARGET_ST_NEXT_YEAR"=>array(
                // 'type' => 'number',
            ),
            "KUMULATIF_CUTOFF_CURRENT_YEAR"=>array(
                // 'type' => 'number',
            ),
            "KUMULATIF_HUNI_BANGUN_CURRENT_YEAR"=>array(
                // 'type' => 'number',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                if($row['TOWER_NAME'] == "TOTAL ST") {
                    $row['TOWER_NAME'] = "<b>".$row['TOWER_NAME']."</b>";
                    $row['REALISASI_COUNT_BACKWARD_YEAR'] = "<b>".number_format($row['REALISASI_COUNT_BACKWARD_YEAR'])."</b>";
                    $row['TARGET_COUNT_CURRENT_YEAR'] = "<b>".number_format($row['TARGET_COUNT_CURRENT_YEAR'])."</b>";
                    $row['REALISASI_COUNT_CURRENT_YEAR'] = "<b>".number_format($row['REALISASI_COUNT_CURRENT_YEAR'])."</b>";
                    $row['PERSEN_CURRENT_YEAR'] = "<b>".number_format($row['PERSEN_CURRENT_YEAR'])."</b>";
                    $row['HUNI_BANGUN_CURRENT_YEAR'] = "<b>".number_format($row['HUNI_BANGUN_CURRENT_YEAR'])."</b>";
                    $row['TARGET_ST_NEXT_YEAR'] = "<b>".number_format($row['TARGET_ST_NEXT_YEAR'])."</b>";
                    $row['KUMULATIF_CUTOFF_CURRENT_YEAR'] = "<b>".number_format($row['KUMULATIF_CUTOFF_CURRENT_YEAR'])."</b>";
                    $row['KUMULATIF_HUNI_BANGUN_CURRENT_YEAR'] = "<b>".number_format($row['KUMULATIF_HUNI_BANGUN_CURRENT_YEAR'])."</b>";
                }
                else {
                    $row['TOWER_NAME'] = $row['TOWER_NAME'];
                    $row['REALISASI_COUNT_BACKWARD_YEAR'] = number_format($row['REALISASI_COUNT_BACKWARD_YEAR']);
                    $row['TARGET_COUNT_CURRENT_YEAR'] = number_format($row['TARGET_COUNT_CURRENT_YEAR']);
                    $row['REALISASI_COUNT_CURRENT_YEAR'] = number_format($row['REALISASI_COUNT_CURRENT_YEAR']);
                    $row['PERSEN_CURRENT_YEAR'] = number_format($row['PERSEN_CURRENT_YEAR']);
                    $row['HUNI_BANGUN_CURRENT_YEAR'] = number_format($row['HUNI_BANGUN_CURRENT_YEAR']);
                    $row['TARGET_ST_NEXT_YEAR'] = number_format($row['TARGET_ST_NEXT_YEAR']);
                    $row['KUMULATIF_CUTOFF_CURRENT_YEAR'] = number_format($row['KUMULATIF_CUTOFF_CURRENT_YEAR']);
                    $row['KUMULATIF_HUNI_BANGUN_CURRENT_YEAR'] = number_format($row['KUMULATIF_HUNI_BANGUN_CURRENT_YEAR']);
                }
                return array($row);
            },
        )))
        ->pipe($this->dataStore('teknik_serah_terima_table1'));
    }

    function teknikserahterimatableexcel1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_boc_serah_terima_teknik @rt_period = :end_date, @project_no = :project
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "TOWER_NAME"=>array(
                'type' => 'string',
            ),
            "REALISASI_COUNT_BACKWARD_YEAR"=>array(
                // 'type' => 'number',
            ),
            "TARGET_COUNT_CURRENT_YEAR"=>array(
                // 'type' => 'number',
            ),
            "REALISASI_COUNT_CURRENT_YEAR"=>array(
                // 'type' => 'number',
            ),
            "PERSEN_CURRENT_YEAR"=>array(
                // 'type' => 'number',
            ),
            "HUNI_BANGUN_CURRENT_YEAR"=>array(
                // 'type' => 'number',
            ),
            "TARGET_ST_NEXT_YEAR"=>array(
                // 'type' => 'number',
            ),
            "KUMULATIF_CUTOFF_CURRENT_YEAR"=>array(
                // 'type' => 'number',
            ),
            "KUMULATIF_HUNI_BANGUN_CURRENT_YEAR"=>array(
                // 'type' => 'number',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['TOWER_NAME'] = $row['TOWER_NAME'];
                $row['REALISASI_COUNT_BACKWARD_YEAR'] = number_format($row['REALISASI_COUNT_BACKWARD_YEAR']);
                $row['TARGET_COUNT_CURRENT_YEAR'] = number_format($row['TARGET_COUNT_CURRENT_YEAR']);
                $row['REALISASI_COUNT_CURRENT_YEAR'] = number_format($row['REALISASI_COUNT_CURRENT_YEAR']);
                $row['PERSEN_CURRENT_YEAR'] = number_format($row['PERSEN_CURRENT_YEAR']);
                $row['HUNI_BANGUN_CURRENT_YEAR'] = number_format($row['HUNI_BANGUN_CURRENT_YEAR']);
                $row['TARGET_ST_NEXT_YEAR'] = number_format($row['TARGET_ST_NEXT_YEAR']);
                $row['KUMULATIF_CUTOFF_CURRENT_YEAR'] = number_format($row['KUMULATIF_CUTOFF_CURRENT_YEAR']);
                $row['KUMULATIF_HUNI_BANGUN_CURRENT_YEAR'] = number_format($row['KUMULATIF_HUNI_BANGUN_CURRENT_YEAR']);
                return array($row);
            },
        )))
        ->pipe($this->dataStore('teknik_serah_terima_table_excel1'));
    }
}