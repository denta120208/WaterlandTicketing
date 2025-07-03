<?php

namespace App\Http\Controllers\Sales\Report\VisitorsByTimeReport;
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

class VisitorsByTimeReport extends \koolreport\KoolReport {
    use \koolreport\laravel\Friendship;
    use \koolreport\export\Exportable;
    use \koolreport\excel\ExcelExportable;
    use \koolreport\cloudexport\Exportable;

    public $project_param = NULL;
    public $start_date_param = NULL;
    public $end_date_param = NULL;
    public $kategori_param = NULL;

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
        $this->kategori_param = $this->params["kategori_param"];
        if($this->kategori_param == "Perhari") {
            $this->visitorsByTimePerharitable1();
        }
        else if($this->kategori_param == "Perminggu") {
            $this->visitorsByTimePerminggutable1();
        }
        else if($this->kategori_param == "Perbulan") {
            $this->visitorsByTimePerbulantable1();
        }
    }

    function visitorsByTimePerharitable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_report_pengunjung_perhari @start_date = :start_date_param, @end_date = :end_date_param, @project = :project
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date_param"=>$this->params["start_date_param"],
            ":end_date_param"=>$this->params["end_date_param"]
        ))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
        )))
        ->pipe($this->dataStore('visitors_by_time_table1'));
    }

    function visitorsByTimePerminggutable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_report_pengunjung_perminggu @start_date = :start_date_param, @end_date = :end_date_param, @project = :project
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date_param"=>$this->params["start_date_param"],
            ":end_date_param"=>$this->params["end_date_param"]
        ))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
        )))
        ->pipe($this->dataStore('visitors_by_time_table1'));
    }

    function visitorsByTimePerbulantable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            EXEC sp_report_pengunjung_perbulan @start_date = :start_date_param, @end_date = :end_date_param, @project = :project
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date_param"=>$this->params["start_date_param"],
            ":end_date_param"=>$this->params["end_date_param"]
        ))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
        )))
        ->pipe($this->dataStore('visitors_by_time_table1'));
    }
}