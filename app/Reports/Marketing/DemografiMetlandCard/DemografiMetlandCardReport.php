<?php

namespace App\Reports\Marketing\DemografiMetlandCard;
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

class DemografiMetlandCardReport extends \koolreport\KoolReport {
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
        $this->marketingdemografimetlandcardchart1();
    }

    function marketingdemografimetlandcardchart1() {
        $currentProject = $this->params['project'];
        $dataProject = DB::select("SELECT * FROM MTLA_MALL.dbo.MD_PROJECT AS a WHERE a.PROJECT_NO_CHAR = $currentProject");
        $prefixDebtor = $dataProject[0]->PREFIX_DEBTOR;
        $dataProject = DB::select("SELECT * FROM ml_Loyalty.dbo.project AS a WHERE a.project_code = '$prefixDebtor'");

        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT 'Kota Bekasi' AS kota, COUNT(a.id) AS JUMLAH_MEMBER FROM member AS a
            LEFT JOIN project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
            LEFT JOIN member_kab AS c ON c.value_kab = SUBSTRING(a.ktp, 1, 4)
            WHERE a.[status] = 1 AND b.id = :project AND a.created_at <= :end_date AND c.value_kab = 3275
            UNION ALL
            SELECT 'Kab Bekasi' AS kota, COUNT(a.id) AS JUMLAH_MEMBER FROM member AS a
            LEFT JOIN project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
            LEFT JOIN member_kab AS c ON c.value_kab = SUBSTRING(a.ktp, 1, 4)
            WHERE a.[status] = 1 AND b.id = :project AND a.created_at <= :end_date AND c.value_kab = 3216
            UNION ALL
            SELECT 'DKI Jakarta' AS kota, COUNT(a.id) AS JUMLAH_MEMBER FROM member AS a
            LEFT JOIN project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
            LEFT JOIN member_kab AS c ON c.value_kab = SUBSTRING(a.ktp, 1, 4)
            WHERE a.[status] = 1 AND b.id = :project AND a.created_at <= :end_date
            AND (c.value_kab = 3171 OR c.value_kab = 3172 OR c.value_kab = 3173 OR c.value_kab = 3174 OR c.value_kab = 3175)
            UNION ALL
            SELECT 'Kab Bogor' AS kota, COUNT(a.id) AS JUMLAH_MEMBER FROM member AS a
            LEFT JOIN project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
            LEFT JOIN member_kab AS c ON c.value_kab = SUBSTRING(a.ktp, 1, 4)
            WHERE a.[status] = 1 AND b.id = :project AND a.created_at <= :end_date AND c.value_kab = 3201
            UNION ALL
            SELECT 'Others' AS kota, COUNT(a.id) AS JUMLAH_MEMBER FROM member AS a
            LEFT JOIN project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
            LEFT JOIN member_kab AS c ON c.value_kab = SUBSTRING(a.ktp, 1, 4)
            WHERE a.[status] = 1 AND b.id = :project AND a.created_at <= :end_date
            AND c.value_kab NOT IN (3216,3275,3171,3172,3173,3174,3175,3201)
        ")
        ->params(array(
            ":project"=>$dataProject[0]->id,
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe($this->dataStore('marketing_demografi_metland_card_chart1'));
    }
}