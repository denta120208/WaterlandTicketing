<?php

namespace App\Reports;

require_once dirname(__FILE__)."/../../vendor/koolreport/core/autoload.php";


class MyReport extends \koolreport\KoolReport {
    use \koolreport\laravel\Friendship;
    // By adding above statement, you have claim the friendship between two frameworks
    // As a result, this report will be able to accessed all databases of Laravel
    // There are no need to define the settings() function anymore
    // while you can do so if you have other datasources rather than those
    // defined in Laravel.

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
