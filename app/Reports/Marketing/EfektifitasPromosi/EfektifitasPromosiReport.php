<?php

namespace App\Reports\Marketing\EfektifitasPromosi;
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

class EfektifitasPromosiReport extends \koolreport\KoolReport {
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
        $this->efektifitaspromositable();
        $this->efektifitaspromosichart();
        $this->efektifitaspromosichartexcelpdftahun();
        $this->efektifitaspromosichartexcelpdfbulan();
    }

    function efektifitaspromositable() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun, a.SUMBER_INFO,cast(sum(a.NET_BEFORE_TAX_NUM) as numeric) as amount
            from SA_BOOKINGENTRY a
            where a.PROJECT_NO_CHAR=:project and a.BOOKING_ENTRY_APPROVE_INT=1
            and a.TGL_BOOKINGENTRY_DTTIME >=:start_date and a.TGL_BOOKINGENTRY_DTTIME <:end_date
            group by year(a.TGL_BOOKINGENTRY_DTTIME),a.SUMBER_INFO
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "amount"=>array(
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                if($row['SUMBER_INFO'] == null) {
                    $row['SUMBER_INFO'] = '[ OTHER ]';
                } else {
                    $row['SUMBER_INFO'] = strtoupper(str_replace("_", " ", $row['SUMBER_INFO']));
                }
                // Dalam Juta
                $row['amount'] = $row['amount'] / 1000000;
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"tahun",
                "row"=>"SUMBER_INFO"
            ),
            "aggregates"=>array(
                "sum"=>"amount",
            )
        )))
        ->pipe($this->dataStore('marketing_efektifitas_promosi_table'));
    }

    function efektifitaspromosichart() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun, month(a.TGL_BOOKINGENTRY_DTTIME) as bulan, a.SUMBER_INFO,cast(sum(a.NET_BEFORE_TAX_NUM) as numeric) as amount
            from SA_BOOKINGENTRY a
            where a.PROJECT_NO_CHAR=:project and a.BOOKING_ENTRY_APPROVE_INT=1
            and a.TGL_BOOKINGENTRY_DTTIME >=:start_date and a.TGL_BOOKINGENTRY_DTTIME <:end_date
            group by year(a.TGL_BOOKINGENTRY_DTTIME),a.SUMBER_INFO,month(a.TGL_BOOKINGENTRY_DTTIME)
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "amount"=>array(
                'type' => 'number',
                "prefix" => "Rp. ",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                if($row['SUMBER_INFO'] == null) {
                    $row['SUMBER_INFO'] = '[ OTHER ]';
                } else {
                    $row['SUMBER_INFO'] = strtoupper(str_replace("_", " ", $row['SUMBER_INFO']));
                }
                // Merubah angka menjadi format bulan
                // $row['bulan'] = DateTime::createFromFormat('!m', $row['bulan'])->format('F');
                // Jika angkanya 1 digit tambahin 0 di depannya
                $row['bulan'] = sprintf("%02d", $row['bulan']);
                // Dalam Juta
                $row['amount'] = $row['amount'] / 1000000;
                return array($row);
            },
            '{meta}' => function($metaData) {
                $metaData['columns']['tahun'] = array(
                    'label' => 'tahun',
                    'type' => 'string',
                );
                $metaData['columns']['bulan'] = array(
                    'label' => 'bulan',
                    'type' => 'string',
                );
                $metaData['columns']['SUMBER_INFO'] = array(
                    'label' => 'SUMBER_INFO',
                    'type' => 'string',
                );
                return $metaData;
            },
        )))
        ->pipe(new Sort(array(
            "tahun"=>"asc",
            "bulan"=>"asc"
        )))
        ->pipe($this->dataStore('marketing_efektifitas_promosi_chart'));
    }

    function efektifitaspromosichartexcelpdftahun() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,cast(sum(a.NET_BEFORE_TAX_NUM) as numeric) as amount
            from SA_BOOKINGENTRY a
            where a.PROJECT_NO_CHAR=:project and a.BOOKING_ENTRY_APPROVE_INT=1
            and a.TGL_BOOKINGENTRY_DTTIME >=:start_date and a.TGL_BOOKINGENTRY_DTTIME <:end_date
            group by year(a.TGL_BOOKINGENTRY_DTTIME)
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "amount"=>array(
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // Dalam Juta
                $row['amount'] = $row['amount'] / 1000000;
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
        ->pipe($this->dataStore('marketing_efektifitas_promosi_chart_excel_pdf_tahun'));
    }

    function efektifitaspromosichartexcelpdfbulan() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun, month(a.TGL_BOOKINGENTRY_DTTIME) as bulan,cast(sum(a.NET_BEFORE_TAX_NUM) as numeric) as amount
            from SA_BOOKINGENTRY a
            where a.PROJECT_NO_CHAR=:project and a.BOOKING_ENTRY_APPROVE_INT=1
            and YEAR(a.TGL_BOOKINGENTRY_DTTIME) =:end_date
            group by year(a.TGL_BOOKINGENTRY_DTTIME),month(a.TGL_BOOKINGENTRY_DTTIME)
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":end_date"=>date('Y', strtotime($this->params["cut_off"]))
        ))
        ->pipe(new ColumnMeta(array(
            "amount"=>array(
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['bulan'] = sprintf("%02d", $row['bulan']);
                // Dalam Juta
                $row['amount'] = $row['amount'] / 1000000;
                return array($row);
            },
            '{meta}' => function($metaData) {
                $metaData['columns']['tahun'] = array(
                    'label' => 'tahun',
                    'type' => 'string',
                );
                $metaData['columns']['bulan'] = array(
                    'label' => 'bulan',
                    'type' => 'string',
                );
                return $metaData;
            },
        )))
        ->pipe(new Sort(array(
            "tahun"=>"asc",
            "bulan"=>"asc"
        )))
        ->pipe($this->dataStore('marketing_efektifitas_promosi_chart_excel_pdf_bulan'));
    }
}
