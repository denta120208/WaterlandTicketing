<?php

namespace App\Reports\Marketing\ChannelDistribution;
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

class ChannelDistributionReport extends \koolreport\KoolReport {
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
        $this->channeldistributiontable($dataRawPersen);
        $this->channeldistributionchart();
        $this->channeldistributioncharttahunexcelpdf();
        $this->channeldistributionchartbulanexcelpdf();
    }

    function channeldistributiontable($dataRawPersen) {
        $this->params['dataRawPersen'] = $dataRawPersen;

        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,month(a.TGL_BOOKINGENTRY_DTTIME) as bulan,
            a.BROKER_ID_INT,a.BROKER_AGEN_ID_INT,c.NAMA_AGEN_CHAR,c.NAMA_BROKER_CHAR,     
            count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
            cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales
            from SA_BOOKINGENTRY a
            left join MD_BROKER  c on c.BROKER_ID_INT   = a.BROKER_ID_INT 
            where a.PROJECT_NO_CHAR=:project
            and a.TGL_BOOKINGENTRY_DTTIME>:start_date and a.TGL_BOOKINGENTRY_DTTIME<:end_date
            and a.BOOKING_ENTRY_APPROVE_INT=1
            group by year(a.TGL_BOOKINGENTRY_DTTIME),month(a.TGL_BOOKINGENTRY_DTTIME),a.BROKER_ID_INT,a.BROKER_AGEN_ID_INT,c.NAMA_AGEN_CHAR,c.NAMA_BROKER_CHAR 
            order by year(a.TGL_BOOKINGENTRY_DTTIME),month(a.TGL_BOOKINGENTRY_DTTIME),a.BROKER_ID_INT,a.BROKER_AGEN_ID_INT
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
                if($row['NAMA_BROKER_CHAR'] == null) {
                    $row['NAMA_BROKER_CHAR'] = '[ OTHER ]';
                }
                else {
                    $row['NAMA_BROKER_CHAR'] = strtoupper($row['NAMA_BROKER_CHAR']);
                }
                // Dalam Juta
                $row['total_sales'] = $row['total_sales'] / 1000000;
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"tahun",
                "row"=>"NAMA_BROKER_CHAR"
            ),
            "aggregates"=>array(
                "sum"=>"total_sales, total_sales_percent, total_unit, total_unit_percent",
            )
        )))
        ->pipe($this->dataStore('marketing_channel_distribution_table'));
    }

    function channeldistributionchart() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,month(a.TGL_BOOKINGENTRY_DTTIME) as bulan,
            a.BROKER_ID_INT,a.BROKER_AGEN_ID_INT,c.NAMA_AGEN_CHAR,c.NAMA_BROKER_CHAR,     
            count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
            cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales
            from SA_BOOKINGENTRY a
            left join MD_BROKER  c on c.BROKER_ID_INT   = a.BROKER_ID_INT 
            where a.PROJECT_NO_CHAR=:project
            and a.TGL_BOOKINGENTRY_DTTIME>:start_date and a.TGL_BOOKINGENTRY_DTTIME<:end_date
            and a.BOOKING_ENTRY_APPROVE_INT=1
            group by year(a.TGL_BOOKINGENTRY_DTTIME),month(a.TGL_BOOKINGENTRY_DTTIME),a.BROKER_ID_INT,a.BROKER_AGEN_ID_INT,c.NAMA_AGEN_CHAR,c.NAMA_BROKER_CHAR 
            order by year(a.TGL_BOOKINGENTRY_DTTIME),month(a.TGL_BOOKINGENTRY_DTTIME),a.BROKER_ID_INT,a.BROKER_AGEN_ID_INT
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
                "prefix" => "Rp. ",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                if($row['NAMA_BROKER_CHAR'] == null) {
                    $row['NAMA_BROKER_CHAR'] = '[ OTHER ]';
                }
                else {
                    $row['NAMA_BROKER_CHAR'] = strtoupper($row['NAMA_BROKER_CHAR']);
                    $namaArr = explode(" ", $row['NAMA_BROKER_CHAR']);
                    if(count($namaArr) > 1) {
                        $row['NAMA_BROKER_CHAR'] = $namaArr[0]." ".$namaArr[1];
                    } else {
                        $row['NAMA_BROKER_CHAR'] = $namaArr[0];
                    }
                }
                // Merubah angka menjadi format bulan
                // $row['bulan'] = DateTime::createFromFormat('!m', $row['bulan'])->format('F');
                // Jika angkanya 1 digit tambahin 0 di depannya
                $row['bulan'] = sprintf("%02d", $row['bulan']);
                // Dalam Juta
                $row['total_sales'] = $row['total_sales'] / 1000000;
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
        ->pipe($this->dataStore('marketing_channel_distribution_chart'));
    }

    function channeldistributioncharttahunexcelpdf() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,
            count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
            cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales
            from SA_BOOKINGENTRY a
            left join MD_BROKER  c on c.BROKER_ID_INT   = a.BROKER_ID_INT 
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
                'label' => 'Total Sales',
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
            "total_unit"=>array(
                'label' => 'Total Unit',
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // Dalam Juta
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
        ->pipe($this->dataStore('marketing_channel_distribution_chart_tahun_excel_pdf'));
    }

    function channeldistributionchartbulanexcelpdf() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,month(a.TGL_BOOKINGENTRY_DTTIME) as bulan,
            count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
            cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales
            from SA_BOOKINGENTRY a
            left join MD_BROKER  c on c.BROKER_ID_INT   = a.BROKER_ID_INT 
            where a.PROJECT_NO_CHAR=:project
            and YEAR(a.TGL_BOOKINGENTRY_DTTIME)=:end_date
            and a.BOOKING_ENTRY_APPROVE_INT=1
            group by year(a.TGL_BOOKINGENTRY_DTTIME),month(a.TGL_BOOKINGENTRY_DTTIME)
            order by year(a.TGL_BOOKINGENTRY_DTTIME),month(a.TGL_BOOKINGENTRY_DTTIME)
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":end_date"=>date('Y', strtotime($this->params["cut_off"]))
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
                'label' => 'Total Sales',
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
            "total_unit"=>array(
                'label' => 'Total Unit',
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['bulan'] = sprintf("%02d", $row['bulan']);
                // Dalam Juta
                $row['total_sales'] = $row['total_sales'] / 1000000;
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
        ->pipe($this->dataStore('marketing_channel_distribution_chart_bulan_excel_pdf'));
    }

    function getRawPersenTotalSalesAndUnit() {
        // Untuk perhitungan persen
        $dataPersen = DB::select("select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,
        count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
        cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales
        from SA_BOOKINGENTRY a
        left join MD_BROKER  c on c.BROKER_ID_INT   = a.BROKER_ID_INT 
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
}
