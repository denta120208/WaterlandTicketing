<?php

namespace App\Reports\Marketing\PaymentMethod;
use \koolreport\processes\Filter;
use \koolreport\processes\ColumnMeta;
use \koolreport\pivot\processes\Pivot;
use \koolreport\processes\Map;
use \koolreport\processes\Sort;
use \koolreport\processes\CalculatedColumn;
use DB;

require_once dirname(__FILE__)."/../../../../vendor/koolreport/core/autoload.php";

class PaymentMethodReport extends \koolreport\KoolReport {
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
        $dataRawPersen = $this->getRawPersenTotalSalesAndUnit();
        $this->payment_method_table1($dataRawPersen);
        $this->payment_method_table2();
        $this->paymentmethodchart();
        $this->paymentmethodcharttahunexcelpdf();
        $this->paymentmethodcharttipeexcelpdf();
    }

    function payment_method_table1($dataRawPersen) {
        $this->params['dataRawPersen'] = $dataRawPersen;

        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,a.SALES_TYPE_CHAR,c.CODE_SALESTYPE_CHAR,  
            count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
            cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales
            from SA_BOOKINGENTRY a
            inner join MD_SALESTYPE c on c.ID_SALESTYPE_INT  = a.SALES_TYPE_CHAR 
            where a.PROJECT_NO_CHAR=:project
            and a.TGL_BOOKINGENTRY_DTTIME>:start_date and a.TGL_BOOKINGENTRY_DTTIME<:end_date
            and a.BOOKING_ENTRY_APPROVE_INT=1
            group by year(a.TGL_BOOKINGENTRY_DTTIME),a.SALES_TYPE_CHAR,c.CODE_SALESTYPE_CHAR
            order by year(a.TGL_BOOKINGENTRY_DTTIME),a.SALES_TYPE_CHAR,c.CODE_SALESTYPE_CHAR
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new CalculatedColumn(array(
            "total_sales_percent"=>function($row){
                $data_sum_total_sales = $this->params['dataRawPersen']['total_sales_assoc'];
                $data_sum_total_unit = $this->params['dataRawPersen']['total_unit_assoc'];
                $persen = round((($row["total_sales"] / $data_sum_total_sales[$row['tahun']]) * 100), 2);

                return $persen;
            },
            "total_unit_percent"=>function($row){
                $data_sum_total_sales = $this->params['dataRawPersen']['total_sales_assoc'];
                $data_sum_total_unit = $this->params['dataRawPersen']['total_unit_assoc'];
                $persen = round((($row["total_unit"] / $data_sum_total_unit[$row['tahun']]) * 100), 2);

                return $persen;
            }
        )))
        ->pipe(new ColumnMeta(array(
            "total_sales"=>array(
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
            "total_sales_percent"=>array(
                'type' => 'number',
                "suffix" => "%",
            ),
            "total_unit_percent"=>array(
                'type' => 'number',
                "suffix" => "%",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['CODE_SALESTYPE_CHAR'] = strtoupper($row['CODE_SALESTYPE_CHAR']);
                $row['total_sales'] = $row['total_sales'] / 1000000;
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"tahun",
                "row"=>"CODE_SALESTYPE_CHAR"
            ),
            "aggregates"=>array(
                "sum"=>"total_sales, total_sales_percent, total_unit, total_unit_percent",
            )
        )))
        ->pipe($this->dataStore('marketing_payment_method_table1'));
    }

    function payment_method_table2() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,a.SALES_TYPE_CHAR,c.CODE_SALESTYPE_CHAR,  
            count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
            cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales
            from SA_BOOKINGENTRY a
            inner join MD_SALESTYPE c on c.ID_SALESTYPE_INT  = a.SALES_TYPE_CHAR 
            where a.PROJECT_NO_CHAR=:project
            and a.TGL_BOOKINGENTRY_DTTIME>:start_date and a.TGL_BOOKINGENTRY_DTTIME<:end_date
            and a.BOOKING_ENTRY_APPROVE_INT=1
            group by year(a.TGL_BOOKINGENTRY_DTTIME),a.SALES_TYPE_CHAR,c.CODE_SALESTYPE_CHAR
            order by year(a.TGL_BOOKINGENTRY_DTTIME),a.SALES_TYPE_CHAR,c.CODE_SALESTYPE_CHAR
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>date('Y', strtotime($this->params["cut_off"]))."-01-01",
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
                $row['CODE_SALESTYPE_CHAR'] = strtoupper($row['CODE_SALESTYPE_CHAR']);
                $row['total_sales'] = $row['total_sales'] / 1000000;
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"tahun",
                "row"=>"CODE_SALESTYPE_CHAR"
            ),
            "aggregates"=>array(
                "sum"=>"total_sales, total_unit",
            )
        )))
        ->pipe($this->dataStore('marketing_payment_method_table2'));
    }

    function paymentmethodchart() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,a.SALES_TYPE_CHAR,c.CODE_SALESTYPE_CHAR,  
            count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
            cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales
            from SA_BOOKINGENTRY a
            inner join MD_SALESTYPE c on c.ID_SALESTYPE_INT  = a.SALES_TYPE_CHAR 
            where a.PROJECT_NO_CHAR=:project
            and a.TGL_BOOKINGENTRY_DTTIME>:start_date and a.TGL_BOOKINGENTRY_DTTIME<:end_date
            and a.BOOKING_ENTRY_APPROVE_INT=1
            group by year(a.TGL_BOOKINGENTRY_DTTIME),a.SALES_TYPE_CHAR,c.CODE_SALESTYPE_CHAR
            order by year(a.TGL_BOOKINGENTRY_DTTIME),a.SALES_TYPE_CHAR,c.CODE_SALESTYPE_CHAR
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "total_sales"=>array(
                'type' => 'number',
                "prefix" => "Rp. ",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['CODE_SALESTYPE_CHAR'] = strtoupper($row['CODE_SALESTYPE_CHAR']);
                $row['total_sales'] = $row['total_sales'] / 1000000;
                return array($row);
            },
            '{meta}' => function($metaData) {
                $metaData['columns']['tahun'] = array(
                    'label' => 'tahun',
                    'type' => 'string',
                );
                return $metaData;
            },
        )))
        ->pipe(new Sort(array(
            "tahun"=>"asc"
        )))
        ->pipe($this->dataStore('marketing_payment_method_chart'));
    }

    function paymentmethodcharttahunexcelpdf() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,
            count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
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
                'label' => 'Total Sales',
                'type' => 'number',
            ),
            "total_unit"=>array(
                'label' => 'Total Unit',
                'type' => 'number',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['total_sales'] = $row['total_sales'] / 1000000;
                return array($row);
            },
            '{meta}' => function($metaData) {
                $metaData['columns']['tahun'] = array(
                    'label' => 'tahun',
                    'type' => 'string',
                );
                return $metaData;
            },
        )))
        ->pipe(new Sort(array(
            "tahun"=>"asc"
        )))
        ->pipe($this->dataStore('marketing_payment_method_chart_tahun_excel_pdf'));
    }

    function paymentmethodcharttipeexcelpdf() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,a.SALES_TYPE_CHAR,c.CODE_SALESTYPE_CHAR,  
            count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
            cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales
            from SA_BOOKINGENTRY a
            inner join MD_SALESTYPE c on c.ID_SALESTYPE_INT  = a.SALES_TYPE_CHAR 
            where a.PROJECT_NO_CHAR=:project
            and YEAR(a.TGL_BOOKINGENTRY_DTTIME)=:end_date
            and a.BOOKING_ENTRY_APPROVE_INT=1
            group by year(a.TGL_BOOKINGENTRY_DTTIME),a.SALES_TYPE_CHAR,c.CODE_SALESTYPE_CHAR
            order by year(a.TGL_BOOKINGENTRY_DTTIME),a.SALES_TYPE_CHAR,c.CODE_SALESTYPE_CHAR
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":end_date"=>date('Y', strtotime($this->params["cut_off"]))
        ))
        ->pipe(new ColumnMeta(array(
            "total_sales"=>array(
                'label' => 'Total Sales',
                'type' => 'number',
            ),
            "total_unit"=>array(
                'label' => 'Total Unit',
                'type' => 'number',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['CODE_SALESTYPE_CHAR'] = strtoupper($row['CODE_SALESTYPE_CHAR']);
                $row['total_sales'] = $row['total_sales'] / 1000000;
                return array($row);
            },
            '{meta}' => function($metaData) {
                $metaData['columns']['tahun'] = array(
                    'label' => 'tahun',
                    'type' => 'string',
                );
                return $metaData;
            },
        )))
        ->pipe(new Sort(array(
            "tahun"=>"asc"
        )))
        ->pipe($this->dataStore('marketing_payment_method_chart_tipe_excel_pdf'));
    }

    function getRawPersenTotalSalesAndUnit() {
        // Untuk perhitungan persen
        $dataPersen = DB::select("select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,
        count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
        cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales
        from SA_BOOKINGENTRY a
        --inner join MD_STOCK b on a.KODE_STOK_UNIQUE_ID_CHAR=b.NOUNIT_CHAR 
        inner join MD_SALESTYPE c on c.ID_SALESTYPE_INT  = a.SALES_TYPE_CHAR 
        where a.PROJECT_NO_CHAR='".$this->params["project"]."'
        and a.TGL_BOOKINGENTRY_DTTIME>'".$this->params["start_date"]."' and a.TGL_BOOKINGENTRY_DTTIME<'".$this->params["cut_off"]."'
        and a.BOOKING_ENTRY_APPROVE_INT=1
        group by year(a.TGL_BOOKINGENTRY_DTTIME)
        order by year(a.TGL_BOOKINGENTRY_DTTIME)");

        $total_sales_assoc = array();
        $total_unit_assoc = array();
        $total_assoc = array();
        foreach ($dataPersen as $i => $value) {
            $total_sales_assoc[$value->tahun] = $value->total_sales;
            $total_unit_assoc[$value->tahun] = $value->total_unit;
        }
        $total_assoc["total_sales_assoc"] = $total_sales_assoc;
        $total_assoc["total_unit_assoc"] = $total_unit_assoc;
        return $total_assoc;
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
