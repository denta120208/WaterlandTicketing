<?php

namespace App\Http\Controllers\Sales\Report\POSReport;
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

class POSReport extends \koolreport\KoolReport {
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
        $this->postable1();
        $this->posdetailstable1();
    }

    function postable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT a.TRANS_POS_ID_INT,
            a.TRANS_POS_NO_CHAR, a.CUSTOMER_NAME_CHAR, a.QTY_INT, a.TOTAL_HARGA_FLOAT,
            a.TOTAL_PAID_FLOAT, a.TOTAL_CHANGE_FLOAT, a.CASHIER_NAME_CHAR, a.created_at,
            CASE
                WHEN (ISNULL(COUNT(d.TRANS_POS_DETAIL_ID_INT), 0) - a.QTY_INT) < 0
                THEN
                    ISNULL(COUNT(d.TRANS_POS_DETAIL_ID_INT), 0) - a.QTY_INT * -1
                ELSE
                    ISNULL(COUNT(d.TRANS_POS_DETAIL_ID_INT), 0) - a.QTY_INT
                END
            AS QTY_FREE_INT,
            ISNULL(SUM(c.DISCOUNT_PERCENT_FLOAT), 0) AS DISCOUNT_PERCENT_FLOAT,
            ISNULL(SUM(c.DISCOUNT_NOMINAL_FLOAT), 0) AS DISCOUNT_NOMINAL_FLOAT,
            a.TOTAL_PAID_FLOAT AS TOTAL_PAID_FINAL_FLOAT
            FROM TRANS_POS AS a
            LEFT JOIN TRANS_PROMO_POS AS b ON b.TRANS_POS_NOCHAR = a.TRANS_POS_NO_CHAR
            LEFT JOIN MD_PROMO_POS AS c ON c.PROMO_POS_ID_INT = b.PROMO_POS_ID_INT
            LEFT JOIN TRANS_POS_DETAILS AS d ON d.TRANS_POS_NO_CHAR = a.TRANS_POS_NO_CHAR
            WHERE a.PROJECT_NO_CHAR = :project AND a.[STATUS] <> 0
            AND CAST(a.created_at AS DATE) >= :start_date_param AND CAST(a.created_at AS DATE) <= :end_date_param
            GROUP BY a.TRANS_POS_ID_INT, a.TRANS_POS_NO_CHAR, a.CUSTOMER_NAME_CHAR, a.QTY_INT,
            a.TOTAL_HARGA_FLOAT, a.TOTAL_PAID_FLOAT, a.TOTAL_CHANGE_FLOAT, a.CASHIER_NAME_CHAR, a.created_at
            ORDER BY a.TRANS_POS_ID_INT DESC
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date_param"=>$this->params["start_date_param"],
            ":end_date_param"=>$this->params["end_date_param"]
        ))
        ->pipe(new Sort(array(
            "TRANS_POS_ID_INT"=>"desc"
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
        )))
        ->pipe($this->dataStore('pos_report_table1'));
    }

    function posdetailstable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT b.TRANS_POS_DETAIL_ID_INT, b.TRANS_POS_NO_CHAR,
            b.DESC_CHAR, d.DESC_CHAR AS MD_PRODUCT_POS_CATEGORY_DESC_CHAR, b.HARGA_SATUAN_FLOAT, b.TOTAL_HARGA_FLOAT, b.created_at,
            (d.DESC_CHAR + ' - ' + b.DESC_CHAR) AS MD_PRODUCT_POS_WITH_CATEGORY_DESC_CHAR,
            b.QTY_INT
            FROM TRANS_POS AS a
            INNER JOIN TRANS_POS_DETAILS AS b ON b.TRANS_POS_NO_CHAR = a.TRANS_POS_NO_CHAR
            INNER JOIN MD_PRODUCT_POS AS c ON c.MD_PRODUCT_POS_ID_INT = b.MD_PRODUCT_POS_ID_INT
            INNER JOIN MD_PRODUCT_POS_CATEGORY AS d ON d.MD_PRODUCT_POS_CATEGORY_ID_INT = c.MD_PRODUCT_POS_CATEGORY_ID_INT
            WHERE a.[STATUS] <> 0 AND b.PROJECT_NO_CHAR = :project
            AND CAST(a.created_at AS DATE) >= :start_date_param AND CAST(a.created_at AS DATE) <= :end_date_param
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date_param"=>$this->params["start_date_param"],
            ":end_date_param"=>$this->params["end_date_param"]
        ))
        ->pipe(new Sort(array(
            "TRANS_POS_DETAIL_ID_INT"=>"desc"
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
        )))
        ->pipe($this->dataStore('pos_details_report_table1'));
    }
}