<?php

namespace App\Reports\Teknik\KonsumsiKomposisiListrik;
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

class KonsumsiKomposisiListrikReport extends \koolreport\KoolReport {
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
        $this->teknikkonsumsikomposisilistrikpiechart1();
        $this->teknikkonsumsikomposisilistrikpiechart2();
    }

    function teknikkonsumsikomposisilistrikpiechart1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            IF :tahun <= '2021'
            BEGIN
                SELECT b.DESCRIPTION_CHAR, SUM(a.NOMINAL_KWH_NUM) AS NOMINAL_KWH_NUM FROM HIST_LISTRIK AS a
                INNER JOIN HIST_TYPE_LISTRIK AS b ON b.ID_TYPE_LISTRIK_INT = a.ID_TYPE_LISTRIK_INT
                WHERE a.PROJECT_NO_CHAR = :project AND MONTH(a.PERIODE_DATE) = :bulan AND YEAR(a.PERIODE_DATE) = :tahun
                AND (a.ID_TYPE_LISTRIK_INT = 2 OR a.ID_TYPE_LISTRIK_INT = 3)
                GROUP BY b.DESCRIPTION_CHAR
            END
            ELSE
            BEGIN
                SELECT * FROM (
                    SELECT 'TENANT' AS DESCRIPTION_CHAR,
                    SUM(a.BILLING_METER_LWBP_DIFF + a.BILLING_METER_WBP_DIFF) AS NOMINAL_KWH_NUM
                    FROM UTILS_BILLING AS a
                    WHERE a.PROJECT_NO_CHAR = :project AND MONTH(a.BILLING_DATE) = :bulan AND YEAR(a.BILLING_DATE) = :tahun
                    AND a.BILLING_TYPE = 2
                    AND a.BILLING_STATUS = 4
                ) AS a
                WHERE a.NOMINAL_KWH_NUM IS NOT NULL
            END
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":bulan"=>$this->params["bulan"],
            ":tahun"=>$this->params["tahun"]
        ))
        ->pipe($this->dataStore('teknik_konsumsi_komposisi_listrik_pie_chart1'));
    }

    function teknikkonsumsikomposisilistrikpiechart2() {
        $node = $this->src('sqlDataSources');
        $node->query("
            IF :tahun <= '2021'
            BEGIN
                SELECT b.DESCRIPTION_CHAR, SUM(a.NOMINAL_RP_NUM) AS NOMINAL_RP_NUM FROM HIST_LISTRIK AS a
                INNER JOIN HIST_TYPE_LISTRIK AS b ON b.ID_TYPE_LISTRIK_INT = a.ID_TYPE_LISTRIK_INT
                WHERE a.PROJECT_NO_CHAR = :project AND MONTH(a.PERIODE_DATE) = :bulan AND YEAR(a.PERIODE_DATE) = :tahun
                AND (a.ID_TYPE_LISTRIK_INT = 2 OR a.ID_TYPE_LISTRIK_INT = 3)
                GROUP BY b.DESCRIPTION_CHAR
            END
            ELSE
            BEGIN
                SELECT * FROM (
                    SELECT 'TENANT' AS DESCRIPTION_CHAR,
                    SUM(a.BILLING_AMOUNT_LWBP + a.BILLING_AMOUNT_WBP) AS NOMINAL_RP_NUM
                    FROM UTILS_BILLING AS a
                    WHERE a.PROJECT_NO_CHAR = :project AND MONTH(a.BILLING_DATE) = :bulan AND YEAR(a.BILLING_DATE) = :tahun
                    AND a.BILLING_TYPE = 2
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
        ->pipe($this->dataStore('teknik_konsumsi_komposisi_listrik_pie_chart2'));
    }
}