<?php

namespace App\Reports\Teknik\KonsumsiKomposisiAir;
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

class KonsumsiKomposisiAirReport extends \koolreport\KoolReport {
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
        $this->params['bulan_tahun_char'] = date('F Y', strtotime("01-".$this->params['bulan']."-".$this->params['tahun']));
        $this->teknikkonsumsikomposisiairpiechart1();
        $this->teknikkonsumsikomposisiairpiechart2();
    }

    function teknikkonsumsikomposisiairpiechart1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            IF :tahun <= '2021'
            BEGIN
                SELECT b.DESCRIPTION_CHAR, SUM(a.NOMINAL_M3_NUM) AS NOMINAL_M3_NUM FROM HIST_AIR AS a
                INNER JOIN HIST_TYPE_AIR AS b ON b.ID_TYPE_AIR_INT = a.ID_TYPE_AIR_INT
                WHERE a.PROJECT_NO_CHAR = :project AND MONTH(a.PERIODE_DATE) = :bulan AND YEAR(a.PERIODE_DATE) = :tahun
                AND (a.ID_TYPE_AIR_INT = 2 OR a.ID_TYPE_AIR_INT = 3)
                GROUP BY b.DESCRIPTION_CHAR
            END
            ELSE
            BEGIN
                SELECT * FROM (
                    SELECT 'TENANT' AS DESCRIPTION_CHAR,
                    SUM(a.BILLING_METER_LWBP_DIFF) AS NOMINAL_M3_NUM
                    FROM UTILS_BILLING AS a
                    WHERE a.PROJECT_NO_CHAR = :project AND MONTH(a.BILLING_DATE) = :bulan AND YEAR(a.BILLING_DATE) = :tahun
                    AND a.BILLING_TYPE = 3
                    AND a.BILLING_STATUS = 4
                ) AS a
                WHERE a.NOMINAL_M3_NUM IS NOT NULL
            END
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":bulan"=>$this->params["bulan"],
            ":tahun"=>$this->params["tahun"]
        ))
        ->pipe($this->dataStore('teknik_konsumsi_komposisi_air_pie_chart1'));
    }

    function teknikkonsumsikomposisiairpiechart2() {
        $node = $this->src('sqlDataSources');
        $node->query("
            IF :tahun <= '2021'
            BEGIN
                SELECT b.DESCRIPTION_CHAR, SUM(a.NOMINAL_RP_NUM) AS NOMINAL_RP_NUM FROM HIST_AIR AS a
                INNER JOIN HIST_TYPE_AIR AS b ON b.ID_TYPE_AIR_INT = a.ID_TYPE_AIR_INT
                WHERE a.PROJECT_NO_CHAR = :project AND MONTH(a.PERIODE_DATE) = :bulan AND YEAR(a.PERIODE_DATE) = :tahun
                AND (a.ID_TYPE_AIR_INT = 2 OR a.ID_TYPE_AIR_INT = 3)
                GROUP BY b.DESCRIPTION_CHAR
            END
            ELSE
            BEGIN
                SELECT * FROM (
                    SELECT 'TENANT' AS DESCRIPTION_CHAR,
                    SUM(a.BILLING_AMOUNT_LWBP) AS NOMINAL_RP_NUM
                    FROM UTILS_BILLING AS a
                    WHERE a.PROJECT_NO_CHAR = :project AND MONTH(a.BILLING_DATE) = :bulan AND YEAR(a.BILLING_DATE) = :tahun
                    AND a.BILLING_TYPE = 3
                    AND a.BILLING_STATUS = 4
                ) AS a
                WHERE a.NOMINAL_RP_NUM IS NOT NULL
            END
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":bulan"=>$this->params["bulan"],
            ":tahun"=>$this->params["tahun"]
        ))
        ->pipe($this->dataStore('teknik_konsumsi_komposisi_air_pie_chart2'));
    }
}