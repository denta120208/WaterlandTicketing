<?php

namespace App\Reports\Marketing\Pembatalan;
use \koolreport\processes\Filter;
use \koolreport\processes\ColumnMeta;
use \koolreport\pivot\processes\Pivot;
use \koolreport\processes\Map;

require_once dirname(__FILE__)."/../../../../vendor/koolreport/core/autoload.php";

class PembatalanReport extends \koolreport\KoolReport {
    use \koolreport\laravel\Friendship;
    use \koolreport\export\Exportable;
    use \koolreport\excel\ExcelExportable;

    function settings()
    {
        $host = env('DB_HOST');
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
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
        $this->pembatalan1();
        $this->pembatalan2();
    }

    function pembatalan1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
            cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales
            from SA_BOOKINGENTRY a 
            inner join MD_SALESTYPE c on c.ID_SALESTYPE_INT  = a.SALES_TYPE_CHAR 
            where a.PROJECT_NO_CHAR=:project
            and a.TGL_BOOKINGENTRY_DTTIME>:start_date and a.TGL_BOOKINGENTRY_DTTIME<:end_date
            and a.BOOKING_ENTRY_APPROVE_INT=1
            group by year(a.TGL_BOOKINGENTRY_DTTIME)
            order by year(a.TGL_BOOKINGENTRY_DTTIME)
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "total_sales"=>array(
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['total_sales'] = $row['total_sales'] / 1000000;
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"tahun",
                // "row"=>"DESC_REASON_CODE_APT"
            ),
            "aggregates"=>array(
                "sum"=>"total_sales, total_unit",
            )
        )))
        ->pipe($this->dataStore('marketing_pembatalan1'));
    }

    function pembatalan2() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(CANCEL_APPROVE_DTTIME) as tahun,b.ID_REASON_CODE_APT,b.DESC_REASON_CODE_APT,
            count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
            cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales
            from SA_CANCELLATION a
            inner join MD_REASON_CODE_APT b on b.ID_REASON_CODE_APT=a.ID_REASON_CODE_APT
            where PROJECT_NO_CHAR=:project and CANCEL_APPROVE_DTTIME between :start_date and :end_date and b.ID_REASON_CODE_APT NOT IN (6, 7)
            group by year(CANCEL_APPROVE_DTTIME),b.ID_REASON_CODE_APT,b.DESC_REASON_CODE_APT
            order by year(CANCEL_APPROVE_DTTIME),b.ID_REASON_CODE_APT,b.DESC_REASON_CODE_APT
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "total_sales"=>array(
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['total_sales'] = $row['total_sales'] / 1000000;
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"tahun",
                "row"=>"DESC_REASON_CODE_APT"
            ),
            "aggregates"=>array(
                "sum"=>"total_sales, total_unit",
            )
        )))
        ->pipe($this->dataStore('marketing_pembatalan2'));
    }

    // function setup()
    // {
    //     $node = $this->src('sqlDataSources');
    //     $node->query("
    //         select year(CANCEL_APPROVE_DTTIME) as tahun,b.ID_REASON_CODE_APT,b.DESC_REASON_CODE_APT,
    //         count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
    //         cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales
    //         from SA_CANCELLATION a
    //         inner join MD_REASON_CODE_APT b on b.ID_REASON_CODE_APT=a.ID_REASON_CODE_APT
    //         where PROJECT_NO_CHAR=:project and CANCEL_APPROVE_DTTIME between :start_date and :end_date
    //         group by year(CANCEL_APPROVE_DTTIME),b.ID_REASON_CODE_APT,b.DESC_REASON_CODE_APT
    //         order by year(CANCEL_APPROVE_DTTIME),b.ID_REASON_CODE_APT,b.DESC_REASON_CODE_APT
    //     ")
    //     ->params(array(
    //         ":project"=>$this->params["project"],
    //         ":start_date"=>$this->params["start_date"],
    //         ":end_date"=>$this->params["end_date"]
    //     ))
    //     ->pipe($this->dataStore('marketing_pembatalan2'));
    // }
    
    // By adding above statement, you have claim the friendship between two frameworks
    // As a result, this report will be able to accessed all databases of Laravel
    // There are no need to define the settings() function anymore
    // while you can do so if you have other datasources rather than those
    // defined in Laravel.

    /*
    SELECT SR,LeaseStart,LeaseEnd,
    DATEDIFF(DAY,LeaseStart,CASE WHEN GETDATE()<LeaseEnd THEN GETDATE() ELSE LeaseEnd END) DaysSoFar,
    DATEDIFF(DAY,LeaseStart,LeaseEnd) TotalLease,
    CAST((DATEDIFF(DAY,LeaseStart,
    CASE
        WHEN GETDATE()<LeaseEnd
        THEN GETDATE()
        ELSE LeaseEnd
       END)*1.00)/
    (DATEDIFF(DAY,LeaseStart,LeaseEnd)*1.00)*100.00 AS DECIMAL(6,2)) AS Percentage
    FROM @table
    */
//    function settings(){
//        return array(
//            "dataSources"=>array(
//                "data"=>array(
//                    "class"=>'\koolreport\datasources\ArrayDataSource',
//                    "data"=>$this->params["data"],
//                    "dataFormat"=>"table",
//                )
//            )
//        );
//    }
//    function setup(){
//        //Prepare data
//        $this->src("data")
//            ->pipe(new ColumnMeta(array(
//                "income"=>array(
//                    "type"=>"number",
//                    "prefix"=>"$"
//                )
//            )))
//            ->saveTo($source);
//
//        //Save orginal data
//        $source->pipe($this->dataStore("origin"));
//
//        //Pipe through process to get result
//        $source->pipe(new Transpose())
//            ->pipe($this->dataStore("result"));
//    }
}
