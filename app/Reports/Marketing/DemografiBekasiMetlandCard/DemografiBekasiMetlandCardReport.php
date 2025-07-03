<?php

namespace App\Reports\Marketing\DemografiBekasiMetlandCard;
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

class DemografiBekasiMetlandCardReport extends \koolreport\KoolReport {
    use \koolreport\laravel\Friendship;
    use \koolreport\export\Exportable;
    use \koolreport\excel\ExcelExportable;

    function settings()
    {
        $host = env('DB_HOST3');
        $database = env('DB_DATABASE3');
        $username = env('DB_USERNAME3');
        $password = env('DB_PASSWORD3');
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
        $this->marketingdemografibekasimetlandcardchart1();
    }

    function marketingdemografibekasimetlandcardchart1() {
        $currentProject = $this->params['project'];
        $dataProject = DB::select("SELECT * FROM MTLA_MALL.dbo.MD_PROJECT AS a WHERE a.PROJECT_NO_CHAR = $currentProject");
        $prefixDebtor = $dataProject[0]->PREFIX_DEBTOR;
        $dataProject = DB::select("SELECT * FROM ml_Loyalty.dbo.project AS a WHERE a.project_code = '$prefixDebtor'");

        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT d.nm_kec, COUNT(a.id) AS JUMLAH_MEMBER FROM member AS a
            LEFT JOIN project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
            LEFT JOIN member_kec AS d ON d.value_kec = SUBSTRING(a.ktp, 1, 6)
            WHERE a.[status] = 1 AND b.id = :project AND a.created_at <= :end_date AND d.id_kab = '32.75'
            GROUP BY d.nm_kec
        ")
        ->params(array(
            ":project"=>$dataProject[0]->id,
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe($this->dataStore('marketing_demografi_bekasi_metland_card_chart1'));
    }
}