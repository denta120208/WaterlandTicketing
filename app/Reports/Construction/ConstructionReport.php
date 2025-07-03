<?php

namespace App\Reports\Construction;

require_once dirname(__FILE__)."/../../../vendor/koolreport/core/autoload.php";


class ConstructionReport extends \koolreport\KoolReport {
    use \koolreport\laravel\Friendship;
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
