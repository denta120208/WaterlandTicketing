<?php

namespace App\Reports\Marketing\MarketingSales;
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

class MarketingSalesReport extends \koolreport\KoolReport {
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
        $this->marketingsalestable1();
        $this->marketingsaleschart1();
        $this->marketingsalestable2();
        $this->marketingsaleschart2();
        $this->marketingsalestable3();
        $this->marketingsalescharttahunexcelpdf1();
        $this->marketingsaleschartbulanexcelpdf1();
        $this->marketingsaleschartexcelpdf2();
        $this->marketingsalestableexcelpdf3();
        $this->marketingsalestabletahapsektor();
        $this->marketingsalesallsaleschart();
        $this->marketingsalesallsaleschartexcelpdf();
        $this->marketingsalestabletahapsektorexcel();
    }

    function marketingsalestable1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun ,b.UNIT_TYPE,count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
            cast(sum(b.unit_lt) as numeric) as total_lt,cast(sum(b.unit_lb) as numeric) as total_lb,cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_rp,
            
            ((cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) - LAG(cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric)) OVER (PARTITION BY b.UNIT_TYPE ORDER BY year(a.TGL_BOOKINGENTRY_DTTIME) ASC))/
            LAG(cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric)) OVER (PARTITION BY b.UNIT_TYPE ORDER BY year(a.TGL_BOOKINGENTRY_DTTIME) ASC))*100 AS growth_rp,

            ((cast(count(a.KODE_STOK_UNIQUE_ID_CHAR) as numeric) - LAG(cast(count(a.KODE_STOK_UNIQUE_ID_CHAR) as numeric)) OVER (PARTITION BY b.UNIT_TYPE ORDER BY year(a.TGL_BOOKINGENTRY_DTTIME) ASC))/
            LAG(cast(count(a.KODE_STOK_UNIQUE_ID_CHAR) as numeric)) OVER (PARTITION BY b.UNIT_TYPE ORDER BY year(a.TGL_BOOKINGENTRY_DTTIME) ASC))*100 AS growth_unit,

            ((cast(sum(b.unit_lt) as numeric) - LAG(cast(sum(b.unit_lt) as numeric)) OVER (PARTITION BY b.UNIT_TYPE ORDER BY year(a.TGL_BOOKINGENTRY_DTTIME) ASC))/
            LAG(cast(sum(b.unit_lt) as numeric)) OVER (PARTITION BY b.UNIT_TYPE ORDER BY year(a.TGL_BOOKINGENTRY_DTTIME) ASC))*100 AS growth_lt,

            CASE WHEN sum(b.unit_lb) = 0 OR ISNULL(LAG(cast(sum(b.unit_lb) as numeric)) OVER (PARTITION BY b.UNIT_TYPE ORDER BY year(a.TGL_BOOKINGENTRY_DTTIME) ASC), 0) = 0 THEN NULL ELSE
            ((cast(sum(b.unit_lb) as numeric) - LAG(cast(sum(b.unit_lb) as numeric)) OVER (PARTITION BY b.UNIT_TYPE ORDER BY year(a.TGL_BOOKINGENTRY_DTTIME) ASC))/
            LAG(cast(sum(b.unit_lb) as numeric)) OVER (PARTITION BY b.UNIT_TYPE ORDER BY year(a.TGL_BOOKINGENTRY_DTTIME) ASC))*100
            END AS growth_lb

            from SA_BOOKINGENTRY a
            inner join MD_STOCK b on a.KODE_STOK_UNIQUE_ID_CHAR=b.NOUNIT_CHAR 
            where a.PROJECT_NO_CHAR=:project
            and a.TGL_BOOKINGENTRY_DTTIME>:start_date and a.TGL_BOOKINGENTRY_DTTIME<:end_date
            and a.BOOKING_ENTRY_APPROVE_INT=1
            group by year(a.TGL_BOOKINGENTRY_DTTIME),b.UNIT_TYPE
            order by year(a.TGL_BOOKINGENTRY_DTTIME),b.UNIT_TYPE
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "tahun"=>array(
                'type' => 'string',
                // "prefix" => "Rp. ",
            ),
            "UNIT_TYPE"=>array(
                'type' => 'string',
                // "prefix" => "Rp. ",
            ),
            "total_rp"=>array(
                'type' => 'number',
                // "decimals" => 0,
                // "dec_point" => ",",
                // "thousand_sep" => ".",
                // "prefix" => "Rp. ",
            ),
            "total_lb"=>array(
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
            "total_lt"=>array(
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
            "total_unit"=>array(
                'type' => 'number',
                // "prefix" => "Rp. ",
            ),
            "growth_rp"=>array(
                // 'type' => 'number',
                "suffix" => "%",
            ),
            "growth_unit"=>array(
                // 'type' => 'number',
                "suffix" => "%",
            ),
            "growth_lt"=>array(
                // 'type' => 'number',
                "suffix" => "%",
            ),
            "growth_lb"=>array(
                // 'type' => 'number',
                "suffix" => "%",
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                if($row['UNIT_TYPE'] == "RKN") {
                    $row['UNIT_TYPE'] = 'RUKO';
                }
                else if($row['UNIT_TYPE'] == "KAVL") {
                    $row['UNIT_TYPE'] = 'KAVLING';
                }
                else if($row['UNIT_TYPE'] == "KAVC") {
                    $row['UNIT_TYPE'] = 'KAVLING COMMERCIAL';
                }
                else if($row['UNIT_TYPE'] == "RMH") {
                    $row['UNIT_TYPE'] = 'RUMAH';
                }
                else {
                    $row['UNIT_TYPE'] = $row['UNIT_TYPE'];
                }
                // // Dalam Juta                
                $row['total_rp'] = $row['total_rp'] / 1000000;
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"tahun",
                "row"=>"UNIT_TYPE"
            ),
            "aggregates"=>array(
                "sum"=>"total_rp, growth_rp, total_unit, growth_unit, total_lt, growth_lt, total_lb, growth_lb",
            )
        )))
        ->pipe($this->dataStore('marketing_sales_table1'));
    }

    function marketingsalestable2() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select tahun,bulan,sum(rumah_land_sales+rumah_building_sales+kav_land_sales+ruko_land_sales) as budget_sales
            from SA_SALES_BUDGET 
            where PROJECT_NO_CHAR=:project and tahun>=:end_date
            group by PROJECT_NO_CHAR, tahun,bulan
            order by PROJECT_NO_CHAR, tahun,bulan
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":end_date"=>date('Y', strtotime($this->params["cut_off"]))
        ))
        ->pipe(new ColumnMeta(array(
            "budget_sales"=>array(
                'type' => 'number',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // // Dalam Juta
                $row['bulan'] = sprintf("%02d", $row['bulan']);
                $row['budget_sales'] = $row['budget_sales'] / 1000000;
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"bulan",
                "row"=>"tahun"
            ),
            "aggregates"=>array(
                "sum"=>"budget_sales",
            )
        )))
        ->pipe($this->dataStore('marketing_sales_table2'));
    }

    function marketingsaleschart1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select tahun,bulan,sum(rumah_land_sales+rumah_building_sales+kav_land_sales+ruko_land_sales) as budget_sales
            from SA_SALES_BUDGET 
            where PROJECT_NO_CHAR=:project and tahun>=:end_date
            group by PROJECT_NO_CHAR, tahun,bulan
            order by PROJECT_NO_CHAR, tahun,bulan
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":end_date"=>date('Y', strtotime($this->params["cut_off"]))
        ))
        ->pipe(new ColumnMeta(array(
            "total_sales"=>array(
                'type' => 'number',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['bulan'] = sprintf("%02d", $row['bulan']);
                $row['budget_sales'] = $row['budget_sales'] / 1000000;
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
        ->pipe($this->dataStore('marketing_sales_chart1'));
    }

    function marketingsalescharttahunexcelpdf1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select tahun,sum(rumah_land_sales+rumah_building_sales+kav_land_sales+ruko_land_sales) as budget_sales
            from SA_SALES_BUDGET 
            where PROJECT_NO_CHAR=:project and tahun>=:end_date
            group by PROJECT_NO_CHAR,tahun
            order by PROJECT_NO_CHAR,tahun
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":end_date"=>date('Y', strtotime($this->params["cut_off"]))
        ))
        ->pipe(new ColumnMeta(array(
            "budget_sales"=>array(
                'label'=>'Budget Sales',
                'type' => 'number',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['budget_sales'] = $row['budget_sales'] / 1000000;
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
        ->pipe($this->dataStore('marketing_sales_chart_tahun_excel_pdf1'));
    }

    function marketingsaleschartbulanexcelpdf1() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select tahun,bulan,sum(rumah_land_sales+rumah_building_sales+kav_land_sales+ruko_land_sales) as budget_sales
            from SA_SALES_BUDGET 
            where PROJECT_NO_CHAR=:project and tahun=:end_date
            group by PROJECT_NO_CHAR, tahun,bulan
            order by PROJECT_NO_CHAR, tahun,bulan
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":end_date"=>date('Y', strtotime($this->params["cut_off"]))
        ))
        ->pipe(new ColumnMeta(array(
            "budget_sales"=>array(
                'label'=>'Budget Sales',
                'type' => 'number',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['bulan'] = sprintf("%02d", $row['bulan']);
                $row['budget_sales'] = $row['budget_sales'] / 1000000;
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
        ->pipe($this->dataStore('marketing_sales_chart_bulan_excel_pdf1'));
    }

    function marketingsalestable3() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
            cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales, 'SALES (M)' as sales_temp
            from SA_BOOKINGENTRY a
            inner join MD_STOCK b on a.KODE_STOK_UNIQUE_ID_CHAR=b.NOUNIT_CHAR 
            where a.PROJECT_NO_CHAR=:project
            and a.TGL_BOOKINGENTRY_DTTIME>:start_date and a.TGL_BOOKINGENTRY_DTTIME<:end_date
            and a.BOOKING_ENTRY_APPROVE_INT=1
            group by year(a.TGL_BOOKINGENTRY_DTTIME)
            order by year(a.TGL_BOOKINGENTRY_DTTIME)
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>(date('Y', strtotime($this->params["cut_off"]))-8)."-01-01",
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "total_sales"=>array(
                'type' => 'number',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // Dalam Juta
                $row['total_sales'] = $row['total_sales'] / 1000000;
                $row['sales_temp'] = "<p><b>".$row['sales_temp']."</b></p>";
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"tahun",
                "row"=>"sales_temp"
            ),
            "aggregates"=>array(
                "sum"=>"total_sales",
            )
        )))
        ->pipe($this->dataStore('marketing_sales_table3'));
    }

    function marketingsalestableexcelpdf3() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
            cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales, 'SALES (M)' as sales_temp
            from SA_BOOKINGENTRY a
            inner join MD_STOCK b on a.KODE_STOK_UNIQUE_ID_CHAR=b.NOUNIT_CHAR 
            where a.PROJECT_NO_CHAR=:project
            and a.TGL_BOOKINGENTRY_DTTIME>:start_date and a.TGL_BOOKINGENTRY_DTTIME<:end_date
            and a.BOOKING_ENTRY_APPROVE_INT=1
            group by year(a.TGL_BOOKINGENTRY_DTTIME)
            order by year(a.TGL_BOOKINGENTRY_DTTIME)
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>(date('Y', strtotime($this->params["cut_off"]))-8)."-01-01",
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "total_sales"=>array(
                'type' => 'number',
            ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // Dalam Juta
                $row['total_sales'] = $row['total_sales'] / 1000000;
                $row['sales_temp'] = $row['sales_temp'];
                return array($row);
            },
        )))
        ->pipe(new Pivot(array(
            "dimensions"=>array(
                "column"=>"tahun",
                "row"=>"sales_temp"
            ),
            "aggregates"=>array(
                "sum"=>"total_sales",
            )
        )))
        ->pipe($this->dataStore('marketing_sales_table_excel_pdf3'));
    }

    function marketingsaleschart2() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
            cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales, 'SALES (M)' as sales_temp
            from SA_BOOKINGENTRY a
            inner join MD_STOCK b on a.KODE_STOK_UNIQUE_ID_CHAR=b.NOUNIT_CHAR 
            where a.PROJECT_NO_CHAR=:project
            and a.TGL_BOOKINGENTRY_DTTIME>:start_date and a.TGL_BOOKINGENTRY_DTTIME<:end_date
            and a.BOOKING_ENTRY_APPROVE_INT=1
            group by year(a.TGL_BOOKINGENTRY_DTTIME)
            order by year(a.TGL_BOOKINGENTRY_DTTIME)
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>(date('Y', strtotime($this->params["cut_off"]))-8)."-01-01",
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "total_sales"=>array(
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
        ->pipe($this->dataStore('marketing_sales_chart2'));
    }

    function marketingsaleschartexcelpdf2() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select year(a.TGL_BOOKINGENTRY_DTTIME) as tahun,count(a.KODE_STOK_UNIQUE_ID_CHAR) as total_unit,
            cast(sum(a.NET_BEFORE_TAX_NUM ) as numeric) as total_sales, 'SALES (M)' as sales_temp
            from SA_BOOKINGENTRY a
            inner join MD_STOCK b on a.KODE_STOK_UNIQUE_ID_CHAR=b.NOUNIT_CHAR 
            where a.PROJECT_NO_CHAR=:project
            and a.TGL_BOOKINGENTRY_DTTIME>:start_date and a.TGL_BOOKINGENTRY_DTTIME<:end_date
            and a.BOOKING_ENTRY_APPROVE_INT=1
            group by year(a.TGL_BOOKINGENTRY_DTTIME)
            order by year(a.TGL_BOOKINGENTRY_DTTIME)
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>(date('Y', strtotime($this->params["cut_off"]))-8)."-01-01",
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            "total_sales"=>array(
                'label'=>'Total Sales',
                'type' => 'number',
            ),
            "total_unit"=>array(
                'label'=>'Total Unit',
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
        ->pipe($this->dataStore('marketing_sales_chart_excel_pdf2'));
    }

    function marketingsalestabletahapsektor() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT YEAR(a.TGL_BOOKINGENTRY_DTTIME) AS [TAHUN],
            COUNT(e.STAGE_NAME_CHAR) AS [TAHAP], COUNT(d.SEKTOR_NAME) AS [SEKTOR]
            FROM SA_BOOKINGENTRY AS a
            LEFT JOIN MD_STOCK AS b ON a.ID_UNIT_CHAR = b.ID_UNIT_STOCK_INT
            LEFT JOIN MD_TOWER_APT AS c ON b.ID_TOWER_INT = c.ID_TOWER_INT
            LEFT JOIN MD_SEKTOR AS d ON b.ID_SEKTOR_INT = d.ID_SEKTOR_INT
            LEFT JOIN MD_STAGE AS e ON c.ID_STAGE_INT = e.ID_STAGE_INT
            WHERE a.BOOKING_ENTRY_APPROVE_INT = 1 AND a.PROJECT_NO_CHAR = :project AND
            a.TGL_BOOKINGENTRY_DTTIME >= :start_date AND a.TGL_BOOKINGENTRY_DTTIME <= :end_date
            GROUP BY YEAR(a.TGL_BOOKINGENTRY_DTTIME)
            ORDER BY YEAR(a.TGL_BOOKINGENTRY_DTTIME) ASC
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                // if($row['UNIT_TYPE'] == "RKN") {
                //     $row['UNIT_TYPE'] = 'RUKO';
                // }
                // // Dalam Juta                
                // $row['NET_BEFORE_TAX_NUM'] = $row['NET_BEFORE_TAX_NUM'] / 1000000;
                return array($row);
            },
            '{meta}' => function($meta) {
                return $meta;
            }
        )))
        ->pipe(new Transpose2())
        ->pipe(new ColumnRename(array(
            "c0"=>""
        )))
        ->pipe(new ColumnMeta(array(
            ""=>array(
                'label' => '',
                "footerText"=>"<p><b>TOTAL</b></p>"
            ),
            (date('Y', strtotime($this->params["cut_off"]))-1)=>array(
                'label' => (date('Y', strtotime($this->params["cut_off"]))-1),
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
            date('Y', strtotime($this->params["cut_off"]))=>array(
                'label' => date('Y', strtotime($this->params["cut_off"])),
                "footer"=>"sum",
                "footerText"=>"<b>@value</b>",
            ),
        )))
        ->pipe($this->dataStore('marketing_sales_tahap_sektor'));

        session(['marketingsalestabletahapsektorproject' => $this->params["project"]]);
        session(['marketingsalestabletahapsektorstartdate' => $this->params["start_date"]]);
        session(['marketingsalestabletahapsektorcutoff' => $this->params["cut_off"]]);
    }

    function marketingsalestabletahapsektorexcel() {
        $node = $this->src('sqlDataSources');
        $node->query("
            SELECT YEAR(a.TGL_BOOKINGENTRY_DTTIME) AS [TAHUN],
            COUNT(e.STAGE_NAME_CHAR) AS [TAHAP], COUNT(d.SEKTOR_NAME) AS [SEKTOR]
            FROM SA_BOOKINGENTRY AS a
            LEFT JOIN MD_STOCK AS b ON a.ID_UNIT_CHAR = b.ID_UNIT_STOCK_INT
            LEFT JOIN MD_TOWER_APT AS c ON b.ID_TOWER_INT = c.ID_TOWER_INT
            LEFT JOIN MD_SEKTOR AS d ON b.ID_SEKTOR_INT = d.ID_SEKTOR_INT
            LEFT JOIN MD_STAGE AS e ON c.ID_STAGE_INT = e.ID_STAGE_INT
            WHERE a.BOOKING_ENTRY_APPROVE_INT = 1 AND a.PROJECT_NO_CHAR = :project AND
            a.TGL_BOOKINGENTRY_DTTIME >= :start_date AND a.TGL_BOOKINGENTRY_DTTIME <= :end_date
            GROUP BY YEAR(a.TGL_BOOKINGENTRY_DTTIME)
            ORDER BY YEAR(a.TGL_BOOKINGENTRY_DTTIME) ASC
        ")
        ->params(array(
            ":project"=>$this->params["project"],
            ":start_date"=>$this->params["start_date"],
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                return array($row);
            },
            '{meta}' => function($meta) {
                return $meta;
            }
        )))
        ->pipe(new Transpose2())
        ->pipe(new ColumnRename(array(
            "c0"=>""
        )))
        ->pipe(new ColumnMeta(array(
            ""=>array(
                'label' => '',
                "footerText"=>"TOTAL"
            ),
            (date('Y', strtotime($this->params["cut_off"]))-1)=>array(
                'label' => (date('Y', strtotime($this->params["cut_off"]))-1),
                "footer"=>"sum",
                "footerText"=>"@value",
            ),
            date('Y', strtotime($this->params["cut_off"]))=>array(
                'label' => date('Y', strtotime($this->params["cut_off"])),
                "footer"=>"sum",
                "footerText"=>"@value",
            ),
        )))
        ->pipe($this->dataStore('marketing_sales_tahap_sektor_excel'));

        session(['marketingsalestabletahapsektorprojectexcel' => $this->params["project"]]);
        session(['marketingsalestabletahapsektorstartdateexcel' => $this->params["start_date"]]);
        session(['marketingsalestabletahapsektorcutoffexcel' => $this->params["cut_off"]]);
    }

    function marketingsalesallsaleschart() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select b.PROJECT_NAME, year(a.TGL_BOOKINGENTRY_DTTIME) as thn, month(a.TGL_BOOKINGENTRY_DTTIME) as bln,count(a.KODE_STOK_UNIQUE_ID_CHAr) as total_unit
            from SA_BOOKINGENTRY a
            inner join MD_PROJECT b on a.PROJECT_NO_CHAR=b.PROJECT_NO_CHAR
            where a.BOOKING_ENTRY_APPROVE_INT=1
            and b.PROJECT_NO_CHAR in (2,3,4,5,6,7,20,8,22) 
            and a.TGL_BOOKINGENTRY_DTTIME <:end_date
            group by a.PROJECT_NO_CHAR,b.PROJECT_NAME,year(a.TGL_BOOKINGENTRY_DTTIME), month(a.TGL_BOOKINGENTRY_DTTIME)
            order by b.PROJECT_NAME,year(a.TGL_BOOKINGENTRY_DTTIME),  month(a.TGL_BOOKINGENTRY_DTTIME)
        ")
        ->params(array(
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            // "total_sales"=>array(
            //     'type' => 'number',
            // ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['bln'] = sprintf("%02d", $row['bln']);
                return array($row);
            },
            '{meta}' => function($metaData) {
                $metaData['columns']['thn'] = array(
                    'label' => 'thn',
                    'type' => 'string',
                );
                $metaData['columns']['bln'] = array(
                    'label' => 'bln',
                    'type' => 'string',
                );
                return $metaData;
            },
        )))
        ->pipe(new Sort(array(
            "thn"=>"asc",
            "bln"=>"asc"
        )))
        ->pipe($this->dataStore('marketing_sales_all_sales_chart'));
    }

    function marketingsalesallsaleschartexcelpdf() {
        $node = $this->src('sqlDataSources');
        $node->query("
            select b.PROJECT_NAME, year(a.TGL_BOOKINGENTRY_DTTIME) as thn, month(a.TGL_BOOKINGENTRY_DTTIME) as bln,count(a.KODE_STOK_UNIQUE_ID_CHAr) as total_unit
            from SA_BOOKINGENTRY a
            inner join MD_PROJECT b on a.PROJECT_NO_CHAR=b.PROJECT_NO_CHAR
            where a.BOOKING_ENTRY_APPROVE_INT=1
            and b.PROJECT_NO_CHAR in (2,3,4,5,6,7,20,8,22) 
            and a.TGL_BOOKINGENTRY_DTTIME <:end_date
            group by a.PROJECT_NO_CHAR,b.PROJECT_NAME,year(a.TGL_BOOKINGENTRY_DTTIME), month(a.TGL_BOOKINGENTRY_DTTIME)
            order by b.PROJECT_NAME,year(a.TGL_BOOKINGENTRY_DTTIME),  month(a.TGL_BOOKINGENTRY_DTTIME)
        ")
        ->params(array(
            ":end_date"=>$this->params["cut_off"]
        ))
        ->pipe(new ColumnMeta(array(
            // "total_sales"=>array(
            //     'type' => 'number',
            // ),
        )))
        ->pipe(new Map(array(
            '{value}' => function($row, $metaData) {
                $row['bln'] = sprintf("%02d", $row['bln']);
                return array($row);
            },
            '{meta}' => function($metaData) {
                $metaData['columns']['thn'] = array(
                    'label' => 'thn',
                    'type' => 'string',
                );
                $metaData['columns']['bln'] = array(
                    'label' => 'bln',
                    'type' => 'string',
                );
                return $metaData;
            },
        )))
        ->pipe(new Sort(array(
            "thn"=>"asc",
            "bln"=>"asc"
        )))
        ->pipe(new Group(array(
            "by"=>"PROJECT_NAME",
            "sum"=>"total_unit"
        )))
        ->pipe($this->dataStore('marketing_sales_all_sales_chart_excel_pdf'));
    }
}
