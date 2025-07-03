<?php

namespace App\Http\Controllers\Sales\Report\VisitorsReport;
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

class VisitorsReport extends \koolreport\KoolReport {
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
        $this->visitorstable1();
        $this->visitorsdetailstable1();
    }

    function visitorstable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT a.TRANS_TICKET_ID_INT, a.TRANS_TICKET_NOCHAR, a.TRANS_TICKET_COUNT_INT,
            a.QTY_TICKET_INT, a.TOTAL_PRICE_NUM, a.TOTAL_PAID_NUM, a.TOTAL_CHANGE_NUM, a.CASHIER_NAME_CHAR, a.created_at,
            CASE
                WHEN (ISNULL(COUNT(d.TRANS_TICKET_DETAIL_ID_INT), 0) - a.QTY_TICKET_INT) < 0
                THEN
                    ISNULL(COUNT(d.TRANS_TICKET_DETAIL_ID_INT), 0) - a.QTY_TICKET_INT * -1
                ELSE
                    ISNULL(COUNT(d.TRANS_TICKET_DETAIL_ID_INT), 0) - a.QTY_TICKET_INT
                END
            AS TICKET_FREE_INT,
            /*ISNULL(SUM(c.DISCOUNT_PERCENT_FLOAT), 0) AS DISCOUNT_PERCENT_FLOAT,
            ISNULL(SUM(c.DISCOUNT_NOMINAL_FLOAT), 0) AS DISCOUNT_NOMINAL_FLOAT*/
            ISNULL(c.DISCOUNT_PERCENT_FLOAT, 0) AS DISCOUNT_PERCENT_FLOAT,
            ISNULL(c.DISCOUNT_NOMINAL_FLOAT, 0) AS DISCOUNT_NOMINAL_FLOAT,
            --(a.TOTAL_PRICE_NUM + ISNULL(c.DISCOUNT_NOMINAL_FLOAT, 0)) * ((ISNULL(c.DISCOUNT_PERCENT_FLOAT, 0) / 100) + 1) AS TOTAL_PRICE_BEFORE_DISCOUNT
            (
                SELECT PRICE_AMOUNT_TICKET_NUM * QTY_TICKET_INT FROM TRANS_TICKET_PURCHASE WHERE TRANS_TICKET_NOCHAR = a.TRANS_TICKET_NOCHAR
            )
            AS TOTAL_PRICE_BEFORE_DISCOUNT,
            e.MD_PRICE_TICKET_DESC,
            f.MD_GROUP_TICKET_DESC,
            c.DESC_CHAR AS MD_PROMO_TICKET_PURCHASE_DESC_CHAR
            FROM TRANS_TICKET_PURCHASE AS a
            LEFT JOIN TRANS_PROMO_TICKET_PURCHASE AS b ON b.TRANS_TICKET_NOCHAR = a.TRANS_TICKET_NOCHAR
            LEFT JOIN MD_PROMO_TICKET_PURCHASE AS c ON c.PROMO_TICKET_PURCHASE_ID_INT = b.PROMO_TICKET_PURCHASE_ID_INT
            LEFT JOIN TRANS_TICKET_PURCHASE_DETAILS AS d ON d.TRANS_TICKET_NOCHAR = a.TRANS_TICKET_NOCHAR
            LEFT JOIN MD_PRICE_TICKET AS e ON e.MD_PRICE_TICKET_ID_INT = a.MD_PRICE_TICKET_ID_INT
            LEFT JOIN MD_GROUP_TICKET AS f ON f.MD_GROUP_TICKET_ID_INT = a.MD_GROUP_TICKET_ID_INT
            WHERE a.PROJECT_NO_CHAR = :project AND a.[STATUS] = '1' AND CAST(a.created_at AS DATE) >= :start_date_param
            AND CAST(a.created_at AS DATE) <= :end_date_param
            GROUP BY a.TRANS_TICKET_ID_INT, a.TRANS_TICKET_NOCHAR, a.TRANS_TICKET_COUNT_INT,
            a.QTY_TICKET_INT, a.TOTAL_PRICE_NUM, a.TOTAL_PAID_NUM, a.TOTAL_CHANGE_NUM, a.CASHIER_NAME_CHAR, a.created_at,
            DISCOUNT_PERCENT_FLOAT, DISCOUNT_NOMINAL_FLOAT, e.MD_PRICE_TICKET_DESC, f.MD_GROUP_TICKET_DESC, c.DESC_CHAR
            ORDER BY a.TRANS_TICKET_ID_INT DESC
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date_param"=>$this->params["start_date_param"],
            ":end_date_param"=>$this->params["end_date_param"]
        ))
        ->pipe(new Sort(array(
            "TRANS_TICKET_ID_INT"=>"desc"
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
        )))
        ->pipe($this->dataStore('visitors_table1'));
    }

    function visitorsdetailstable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT b.TRANS_TICKET_DETAIL_ID_INT, b.TRANS_TICKET_NOCHAR, b.TRANS_TICKET_DETAIL_COUNT_INT,
            b.NUMBER_TICKET, a.CASHIER_NAME_CHAR, b.IS_SCAN, b.SCAN_BY, b.SCAN_AT, b.created_at
            FROM TRANS_TICKET_PURCHASE AS a
            INNER JOIN TRANS_TICKET_PURCHASE_DETAILS AS b ON b.TRANS_TICKET_NOCHAR = a.TRANS_TICKET_NOCHAR
            WHERE a.PROJECT_NO_CHAR = :project AND a.[STATUS] = '1' AND CAST(a.created_at AS DATE) >= :start_date_param
            AND CAST(a.created_at AS DATE) <= :end_date_param
            ORDER BY a.TRANS_TICKET_ID_INT DESC
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date_param"=>$this->params["start_date_param"],
            ":end_date_param"=>$this->params["end_date_param"]
        ))
        ->pipe(new Sort(array(
            "TRANS_TICKET_DETAIL_ID_INT"=>"desc"
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
        )))
        ->pipe($this->dataStore('visitors_details_table1'));
    }
}