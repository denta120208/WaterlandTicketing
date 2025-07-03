<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Else_;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Session;
use Redirect;


class SsoController extends Controller
{
    public function token($id,$email)
    {
        $header = array();
        $token=$id;

        $client = new \GuzzleHttp\Client(['verify' => false ]);
        $res = $client->request('POST', 'https://sso.metropolitanland.com/api/detailsWATERGROUP', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
                'Content-Type' => 'application/json'],
            'form_params' => [
                'email' =>$email
            ]
        ]);

        $contents = (string) $res->getBody();
        $data = json_decode($res->getBody(), true);
        $md_project=\DB::table('MD_PROJECT')->where('PROJECT_NO_CHAR',$data['data'][0]['project_no_char'])->get();
        $permision_data = collect([99]);        
        
        Session::push('permision_akses', $permision_data);
        Session::put('id', $data['data'][0]['id']);
        Session::put('first_name', $data['data'][0]['first_name']);
        Session::put('last_name', $data['data'][0]['last_name']);
        Session::put('username', $data['data'][0]['username']);
        Session::put('email', $data['data'][0]['email']);
        Session::put('default_project', $data['data'][0]['project_name']);
        Session::put('level', $data['data'][0]['List_level']);
        Session::put('menu', $data['data'][0]['List_menus']);
        Session::put('proyek', $data['data'][0]['List_proyek']);
        Session::put('default_project_no_char', $data['data'][0]['project_no_char']);
        Session::put('is_active', $data['data'][0]['is_active']);

        // Session::put('Email', $data['data'][0]['email']);
        // Session::put('akses', "99");
        // Session::put('project', $data['data'][0]['List_proyek']);
        // Session::put('kode', $data['data'][0]['project_code']);
        // Session::put('project_no_char', $data['data'][0]['project_no_char']);
        // Session::put('nama', $md_project[0]->PROJECT_NAME);
        return Redirect::to('/');
    }

    public function logout() {
        Session::flush();
        return Redirect::to('https://sso.metropolitanland.com/');
    }
}
