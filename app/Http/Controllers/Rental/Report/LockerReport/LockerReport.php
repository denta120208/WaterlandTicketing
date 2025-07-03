<?php

namespace App\Http\Controllers\Rental\Report\LockerReport;
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

require_once dirname(__FILE__)."/../../../../../../vendor/koolreport/core/autoload.php";

class LockerReport extends \koolreport\KoolReport {
    use \koolreport\laravel\Friendship;
    use \koolreport\export\Exportable;
    use \koolreport\excel\ExcelExportable;
    use \koolreport\cloudexport\Exportable;

    public $project_param = NULL;
    public $start_date_param = NULL;
    public $end_date_param = NULL;

    function settings()
    {
        $host = env('DB_HOST');
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        return array(
            "dataSources"=>array(
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
        $this->project_param = $this->params["project"];
        $this->start_date_param = $this->params["start_date_param"];
        $this->end_date_param = $this->params["end_date_param"];
        $this->lockertable1();
        $this->lockerdetailstable1();
    }

    function lockertable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT * FROM TRANS_RENTAL_LOCKER AS a
            WHERE a.PROJECT_NO_CHAR = :project
            AND a.[STATUS] <> '0'
            AND CAST(a.created_at AS DATE) >= :start_date_param AND CAST(a.created_at AS DATE) <= :end_date_param
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date_param"=>$this->params["start_date_param"],
            ":end_date_param"=>$this->params["end_date_param"]
        ))
        ->pipe(new Sort(array(
            "TRANS_RENTAL_LOCKER_ID_INT"=>"desc"
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
        )))
        ->pipe($this->dataStore('locker_report_table1'));
    }

    function lockerdetailstable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT b.TRANS_RENTAL_LOCKER_DETAIL_ID_INT, b.TRANS_LOCKER_NO_CHAR,
            b.DESC_CHAR, b.HARGA_FLOAT, b.created_at
            FROM TRANS_RENTAL_LOCKER AS a
            INNER JOIN TRANS_RENTAL_LOCKER_DETAILS AS b ON b.TRANS_LOCKER_NO_CHAR = a.TRANS_LOCKER_NO_CHAR
            INNER JOIN MD_LOCKER AS c ON c.MD_LOCKER_ID_INT = b.MD_LOCKER_ID_INT
            WHERE a.[STATUS] <> 0 AND b.PROJECT_NO_CHAR = :project
            AND CAST(a.created_at AS DATE) >= :start_date_param AND CAST(a.created_at AS DATE) <= :end_date_param
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date_param"=>$this->params["start_date_param"],
            ":end_date_param"=>$this->params["end_date_param"]
        ))
        ->pipe(new Sort(array(
            "TRANS_RENTAL_LOCKER_DETAIL_ID_INT"=>"desc"
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
        )))
        ->pipe($this->dataStore('locker_details_report_table1'));
    }
}