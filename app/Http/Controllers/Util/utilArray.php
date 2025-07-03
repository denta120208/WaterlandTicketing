<?php namespace App\Http\Controllers\Util;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use App\Http\Controllers\Controller;

/**
 * Description of arrayUtil
 *
 * @author rizky
 */
class utilArray extends Controller {
    public function array_push_assoc($array, $key, $value){
        $array[$key] = $value;
        return $array;
    }
}
