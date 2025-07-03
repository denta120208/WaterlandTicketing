<?php

namespace App\Reports\Marketing\UpcomingTenant;
use \koolreport\processes\Filter;
use \koolreport\processes\ColumnMeta;
use \koolreport\pivot\processes\Pivot;
use \koolreport\processes\Map;
use \koolreport\processes\Sort;
use \koolreport\processes\CalculatedColumn;
use \koolreport\processes\AggregatedColumn;
use DateTime;
use DB;

require_once dirname(__FILE__)."/../../../../vendor/koolreport/core/autoload.php";

class UpcomingTenantReport extends \koolreport\KoolReport {
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
        $this->upcomingtenanttable1();
    }

    function upcomingtenanttable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT a.MD_TENANT_NAME_CHAR, d.PSM_CATEGORY_NAME, e.LOT_LEVEL_DESC,
            ISNULL(c.LOT_STOCK_SQM, 0) AS LEASED_AREA, b.PSM_TRANS_BOOKING_DATE, b.PSM_TRANS_START_DATE
            FROM MD_TENANT AS a
            LEFT JOIN PSM_TRANS AS b ON b.MD_TENANT_ID_INT = a.MD_TENANT_ID_INT
            LEFT JOIN LOT_STOCK AS c ON c.LOT_STOCK_ID_INT = b.LOT_STOCK_ID_INT
            LEFT JOIN PSM_CATEGORY AS d ON d.PSM_CATEGORY_ID_INT = b.PSM_CATEGORY_ID_INT
            LEFT JOIN LOT_LEVEL AS e ON e.LOT_LEVEL_ID_INT = c.LOT_LEVEL_ID_INT
            WHERE b.PROJECT_NO_CHAR = :project
            AND b.PSM_TRANS_BOOKING_DATE >= :start_date AND b.PSM_TRANS_BOOKING_DATE <= :end_date
            AND c.ON_RENT_STAT_INT = 1
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "MD_TENANT_NAME_CHAR"=>array(
                "label" => "TENANT"
            ),
            "PSM_CATEGORY_NAME"=>array(
                "label" => "KATEGORI"
            ),
            "LOT_LEVEL_DESC"=>array(
                "label" => "LOKASI"
            ),
            "LEASED_AREA"=>array(
                "label" => "LUAS",
                "suffix" => " m2"
            ),
            "PSM_TRANS_BOOKING_DATE"=>array(
                "label" => "TANGGAL KESEPAKATAN"
            ),
            "PSM_TRANS_START_DATE"=>array(
                "label" => "RENCANA BUKA"
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                if($row['PSM_TRANS_BOOKING_DATE'] == 0) {
                    $row['PSM_TRANS_BOOKING_DATE'] = "-";
                } else {
                    $row['PSM_TRANS_BOOKING_DATE'] = date('d F Y', strtotime($row['PSM_TRANS_BOOKING_DATE']));
                }

                if($row['PSM_TRANS_START_DATE'] == 0) {
                    $row['PSM_TRANS_START_DATE'] = "-";
                } else {
                    $row['PSM_TRANS_START_DATE'] = date('d F Y', strtotime($row['PSM_TRANS_START_DATE']));
                }
                return array($row);
            },
        )))
        ->pipe($this->dataStore('marketing_upcoming_tenant_table1'));
    }
}
