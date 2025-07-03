<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Else_;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Session;
use Redirect;
use Illuminate\Routing\Redirector;
use DB;


class WaterGroupController extends Controller
{
    public function change_project($id) {
        session(['current_project' => $id]);
        $currentProject = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = '".$id."'");
        session(['current_project_char' => strtoupper($currentProject[0]->PROJECT_NAME)]);
        // return Redirect::back();
        return redirect()->route('home');
    }
}
