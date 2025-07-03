<?php

namespace App\Reports\Marketing\ActiveMemberMetlandCard;
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

class ActiveMemberMetlandCardReport extends \koolreport\KoolReport {
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
        $this->marketingactivemembermetlandcardchart1();
        $this->marketingactivemembermetlandcardchart2();
    }

    function marketingactivemembermetlandcardchart1() {
        $currentProject = $this->params['project'];
        $dataProject = DB::select("SELECT * FROM MTLA_MALL.dbo.MD_PROJECT AS a WHERE a.PROJECT_NO_CHAR = $currentProject");
        $prefixDebtor = $dataProject[0]->PREFIX_DEBTOR;
        $dataProject = DB::select("SELECT * FROM ml_Loyalty.dbo.project AS a WHERE a.project_code = '$prefixDebtor'");

        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT '< 17' AS AGE, COUNT(first_name) AS JUMLAH_MEMBER FROM (
                SELECT a.first_name, a.card_no, a.dob, DATEDIFF(YEAR, a.dob, :end_date) AS UMUR
                FROM member AS a
                LEFT JOIN project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
                INNER JOIN post AS c ON c.custno = a.card_no
                WHERE a.[status] = 1 AND b.id = :project AND
                CAST(c.post_date AS DATE) <= :end_date AND CAST(c.post_date AS DATE) >= :start_date
                GROUP BY a.first_name, a.card_no, a.dob
            ) AS AGE_LESS_17
            WHERE UMUR < 17
            UNION ALL
            SELECT '17 - 25' AS AGE, COUNT(first_name) AS JUMLAH_MEMBER FROM (
                SELECT a.first_name, a.card_no, a.dob, DATEDIFF(YEAR, a.dob, :end_date) AS UMUR
                FROM member AS a
                LEFT JOIN project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
                INNER JOIN post AS c ON c.custno = a.card_no
                WHERE a.[status] = 1 AND b.id = :project AND
                CAST(c.post_date AS DATE) <= :end_date AND CAST(c.post_date AS DATE) >= :start_date
                GROUP BY a.first_name, a.card_no, a.dob
            ) AS AGE_1725
            WHERE UMUR >= 17 AND UMUR <= 25
            UNION ALL
            SELECT '26 - 35' AS AGE, COUNT(first_name) AS JUMLAH_MEMBER FROM (
                SELECT a.first_name, a.card_no, a.dob, DATEDIFF(YEAR, a.dob, :end_date) AS UMUR
                FROM member AS a
                LEFT JOIN project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
                INNER JOIN post AS c ON c.custno = a.card_no
                WHERE a.[status] = 1 AND b.id = :project AND
                CAST(c.post_date AS DATE) <= :end_date AND CAST(c.post_date AS DATE) >= :start_date
                GROUP BY a.first_name, a.card_no, a.dob
            ) AS AGE_2635
            WHERE UMUR >= 26 AND UMUR <= 35
            UNION ALL
            SELECT '36 - 45' AS AGE, COUNT(first_name) AS JUMLAH_MEMBER FROM (
                SELECT a.first_name, a.card_no, a.dob, DATEDIFF(YEAR, a.dob, :end_date) AS UMUR
                FROM member AS a
                LEFT JOIN project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
                INNER JOIN post AS c ON c.custno = a.card_no
                WHERE a.[status] = 1 AND b.id = :project AND
                CAST(c.post_date AS DATE) <= :end_date AND CAST(c.post_date AS DATE) >= :start_date
                GROUP BY a.first_name, a.card_no, a.dob
            ) AS AGE_3645
            WHERE UMUR >= 36 AND UMUR <= 45
            UNION ALL
            SELECT '46 - 55' AS AGE, COUNT(first_name) AS JUMLAH_MEMBER FROM (
                SELECT a.first_name, a.card_no, a.dob, DATEDIFF(YEAR, a.dob, :end_date) AS UMUR
                FROM member AS a
                LEFT JOIN project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
                INNER JOIN post AS c ON c.custno = a.card_no
                WHERE a.[status] = 1 AND b.id = :project AND
                CAST(c.post_date AS DATE) <= :end_date AND CAST(c.post_date AS DATE) >= :start_date
                GROUP BY a.first_name, a.card_no, a.dob
            ) AS AGE_4655
            WHERE UMUR >= 46 AND UMUR <= 55
            UNION ALL
            SELECT '> 55' AS AGE, COUNT(first_name) AS JUMLAH_MEMBER FROM (
                SELECT a.first_name, a.card_no, a.dob, DATEDIFF(YEAR, a.dob, :end_date) AS UMUR
                FROM member AS a
                LEFT JOIN project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
                INNER JOIN post AS c ON c.custno = a.card_no
                WHERE a.[status] = 1 AND b.id = :project AND
                CAST(c.post_date AS DATE) <= :end_date AND CAST(c.post_date AS DATE) >= :start_date
                GROUP BY a.first_name, a.card_no, a.dob
            ) AS AGE_OVER_55
            WHERE UMUR > 55
            UNION ALL
            SELECT 'N/A' AS AGE, COUNT(first_name) AS JUMLAH_MEMBER FROM (
                SELECT a.first_name, a.card_no, a.dob, DATEDIFF(YEAR, a.dob, :end_date) AS UMUR
                FROM member AS a
                LEFT JOIN project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
                INNER JOIN post AS c ON c.custno = a.card_no
                WHERE a.[status] = 1 AND b.id = :project AND
                CAST(c.post_date AS DATE) <= :end_date AND CAST(c.post_date AS DATE) >= :start_date
                GROUP BY a.first_name, a.card_no, a.dob
            ) AS AGE_N_A
            WHERE UMUR IS NULL
        ")
        ->params(array(
            ":project"=>$dataProject[0]->id,
            ":start_date"=>date('Y', strtotime($this->params["cut_off"])) . '-01-01',
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe($this->dataStore('marketing_active_member_metland_card_chart1'));
    }

    function marketingactivemembermetlandcardchart2() {
        $currentProject = $this->params['project'];
        $dataProject = DB::select("SELECT * FROM MTLA_MALL.dbo.MD_PROJECT AS a WHERE a.PROJECT_NO_CHAR = $currentProject");
        $prefixDebtor = $dataProject[0]->PREFIX_DEBTOR;
        $dataProject = DB::select("SELECT * FROM ml_Loyalty.dbo.project AS a WHERE a.project_code = '$prefixDebtor'");

        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT ISNULL(CAST(a.GENDER AS VARCHAR), 'N/A') AS GENDER, COUNT(a.card_no) AS JUMLAH_MEMBER FROM (
                SELECT a.first_name, a.card_no, a.dob, DATEDIFF(YEAR, a.dob, :end_date) AS UMUR,
                CASE
                    WHEN a.gander = 'L' THEN 'MALE'
                    WHEN a.gander = 'P' THEN 'FEMALE'
                    WHEN a.gander = 'M' THEN 'MALE'
                    WHEN a.gander = 'F' THEN 'FEMALE' END
                AS GENDER
                FROM member AS a
                LEFT JOIN project AS b ON a.m_pnum = b.code AND a.m_ptype = b.seq
                INNER JOIN post AS c ON c.custno = a.card_no
                WHERE a.[status] = 1 AND b.id = :project AND
                CAST(c.post_date AS DATE) <= :end_date AND CAST(c.post_date AS DATE) >= :start_date
                GROUP BY a.first_name, a.card_no, a.dob, a.gander
            ) AS a
            GROUP BY a.GENDER
        ")
        ->params(array(
            ":project"=>$dataProject[0]->id,
            ":start_date"=>date('Y', strtotime($this->params["cut_off"])) . '-01-01',
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe($this->dataStore('marketing_active_member_metland_card_chart2'));
    }
}