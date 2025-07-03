<?php

namespace App\Reports\Marketing\DataKPR;
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

class DataKPRReport extends \koolreport\KoolReport {
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
        $dataRawPersen = $this->getRawPersenTotalPersen();
        $this->realisasiakadtable($dataRawPersen);
        $this->monitoringaplikasikprtable();
    }

    function realisasiakadtable($dataRawPersen) {
        $this->params['dataRawPersen'] = $dataRawPersen;

        $node = $this->src('sqlDataSources');
        $node->query("
            select a.PROJECT_NO_CHAR,year(a.TGL_AKAD_DATE)as tahun,b.BANK_NAME_CHAR,count(a.kode_stok_unique_id_char) as total_unit,
            cast(sum(a.plafon_amount_int) as numeric) as total_amount,

            ((cast(sum(a.plafon_amount_int) as numeric) - LAG(cast(sum(a.plafon_amount_int) as numeric)) OVER (PARTITION BY b.BANK_NAME_CHAR ORDER BY year(a.TGL_AKAD_DATE) ASC))/
            LAG(cast(sum(a.plafon_amount_int) as numeric)) OVER (PARTITION BY b.BANK_NAME_CHAR ORDER BY year(a.TGL_AKAD_DATE) ASC))*100 AS growth_amount,

            ((cast(count(a.kode_stok_unique_id_char) as numeric) - LAG(cast(count(a.kode_stok_unique_id_char) as numeric)) OVER (PARTITION BY b.BANK_NAME_CHAR ORDER BY year(a.TGL_AKAD_DATE) ASC))/
            LAG(cast(count(a.kode_stok_unique_id_char) as numeric)) OVER (PARTITION BY b.BANK_NAME_CHAR ORDER BY year(a.TGL_AKAD_DATE) ASC))*100 AS growth_unit

            from SA_AKADKREDIT a
            inner join MD_BANK b on a.BANK_ID_INT=b.BANK_ID_INT and a.PROJECT_NO_CHAR=b.PROJECT_NO_CHAR
            where a.PROJECT_NO_CHAR=:project and a.BLOKIR_BANK_STATUS_INT=2
            and a.TGL_AKAD_DATE>:start_date and a.TGL_AKAD_DATE < :end_date
            group by a.PROJECT_NO_CHAR,year(a.TGL_AKAD_DATE),b.BANK_NAME_CHAR
            order by a.PROJECT_NO_CHAR,year(a.TGL_AKAD_DATE),b.BANK_NAME_CHAR
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new CalculatedColumn(array(
            "total_percent"=>function($row){
                $data_sum_total = $this->params['dataRawPersen'];
                $persen = round((($row["total_amount"] / $data_sum_total[$row['tahun']]) * 100), 2);

                return $persen;
            }
        )))
        ->pipe(new ColumnMeta(array(
            "total_sales"=>array(
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
            "total_percent"=>array(
                'type' => 'number',
                "suffix" => "%",
            ),
            "growth_amount"=>array(
                // 'type' => 'number',
                "suffix" => "%",
            ),
            "growth_unit"=>array(
                // 'type' => 'number',
                "suffix" => "%",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // if($row['NAMA_BROKER_CHAR'] == null) {
                //     $row['NAMA_BROKER_CHAR'] = '[ OTHER ]';
                // }
                // else {
                //     $row['NAMA_BROKER_CHAR'] = strtoupper($row['NAMA_BROKER_CHAR']);
                // }
                // Dalam Juta
                $row['BANK_NAME_CHAR'] = strtoupper($row['BANK_NAME_CHAR']);
                $row['total_amount'] = $row['total_amount'] / 1000000;
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"tahun",
                "row"=>"BANK_NAME_CHAR"
            ),
            "aggregates"=>array(
                "sum"=>"total_amount, growth_amount, total_unit, growth_unit, total_percent",
            )
        )))
        ->pipe($this->dataStore('marketing_realisasi_akad_table'));
    }

    function monitoringaplikasikprtable() {
        $this->params["start_date"] = (date('Y', strtotime($this->params["cut_off"])) + 1)."-01-01";

        $node = $this->src('sqlDataSources');
        $node->query("
            select a.PROJECT_NO_CHAR, MONTH(b.TGL_SCHEDULE_DTTIME) as bulan,count(a.kode_stok_unique_id_char) as total_unit,
            cast(sum(b.BILL_AMOUNT) as numeric) as total_amount 
            from SA_BOOKINGENTRY a
            inner join SA_BILLINGSCHEDULE b on a.BOOKING_ENTRY_CODE_INT=b.BOOKING_ENTRY_CODE_INT and a.PROJECT_NO_CHAR=b.PROJECT_NO_CHAR
            where a.BOOKING_ENTRY_APPROVE_INT=1 and a.PROJECT_NO_CHAR=:project
            and b.TRX_TYPE_CODE ='KPR'
            and b.TGL_SCHEDULE_DTTIME >=:start_date and b.TGL_SCHEDULE_DTTIME <:end_date
            group by a.PROJECT_NO_CHAR, MONTH(b.TGL_SCHEDULE_DTTIME)
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["cut_off"],
            ":end_date"=>$this->params["start_date"]
        ))
        ->pipe(new ColumnMeta(array(
            "total_amount"=>array(
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // Dalam Juta
                $row['PROJECT_NO_CHAR'] = "ESTIMASI APLIKASI MASUK ".date('Y', strtotime($this->params["cut_off"]));
                $row['bulan'] = sprintf("%02d", $row['bulan']);
                $row['bulan'] = strtoupper(DateTime::createFromFormat('!m', $row['bulan'])->format('F'));
                $row['total_amount'] = $row['total_amount'] / 1000000;
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"PROJECT_NO_CHAR",
                "row"=>"bulan"
            ),
            "aggregates"=>array(
                "sum"=>"total_amount, total_unit",
            )
        )))
        ->pipe($this->dataStore('marketing_monitoring_aplikasi_kpr_table'));
    }

    function getRawPersenTotalPersen() {
        // Untuk perhitungan persen
        $dataPersen = DB::select("select year(a.TGL_AKAD_DATE)as tahun,
        cast(sum(a.plafon_amount_int) as numeric) as total_amount 
        from SA_AKADKREDIT a
        inner join MD_BANK b on a.BANK_ID_INT=b.BANK_ID_INT and a.PROJECT_NO_CHAR=b.PROJECT_NO_CHAR 
        where a.PROJECT_NO_CHAR='".$this->params["project"]."' and a.BLOKIR_BANK_STATUS_INT=2
        and a.TGL_AKAD_DATE>'".$this->params["start_date"]."' and a.TGL_AKAD_DATE < '".$this->params["cut_off"]."'
        group by year(a.TGL_AKAD_DATE)
        order by year(a.TGL_AKAD_DATE)");

        $total_assoc = array();
        foreach ($dataPersen as $i => $value) {
            $total_assoc[$value->tahun] = $value->total_amount;
        }
        return $total_assoc;
    }
}
