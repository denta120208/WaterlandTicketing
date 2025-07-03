<?php
namespace App\Navigations;

use Session, DB, Log;
use Illuminate\Support\Str;

class MenuBuildNav {
    public static function menus() {
        $now = date('Y-m-d H:i:s');
        $day = date("N", strtotime($now));
        $menu = array();
        $menus = Session::get('menu');

        $query = DB::select(DB::raw("
            SELECT DISTINCT * FROM (
                SELECT DISTINCT p.* FROM MD_MENUS AS p
                LEFT JOIN MD_MENUS AS c ON p.ID_MENU = c.PARENT_ID
                LEFT JOIN MD_MENUS AS d ON c.ID_MENU = d.PARENT_ID
                LEFT JOIN MD_MENUS AS e ON d.ID_MENU = e.PARENT_ID
                LEFT JOIN MD_MENUS AS f ON e.ID_MENU = f.PARENT_ID
                LEFT JOIN MD_MENUS AS g ON f.ID_MENU = g.PARENT_ID
                WHERE g.ID_MENU IN (" . $menus . ")
                UNION ALL
                SELECT DISTINCT p.* FROM MD_MENUS AS p
                LEFT JOIN MD_MENUS AS c ON p.ID_MENU = c.PARENT_ID
                LEFT JOIN MD_MENUS AS d ON c.ID_MENU = d.PARENT_ID
                LEFT JOIN MD_MENUS AS e ON d.ID_MENU = e.PARENT_ID
                LEFT JOIN MD_MENUS AS f ON e.ID_MENU = f.PARENT_ID
                WHERE f.ID_MENU IN (" . $menus . ")
                UNION ALL
                SELECT DISTINCT p.* FROM MD_MENUS AS p
                LEFT JOIN MD_MENUS AS c ON p.ID_MENU = c.PARENT_ID
                LEFT JOIN MD_MENUS AS d ON c.ID_MENU = d.PARENT_ID
                LEFT JOIN MD_MENUS AS e ON d.ID_MENU = e.PARENT_ID
                WHERE e.ID_MENU IN (" . $menus . ")
                UNION ALL
                SELECT DISTINCT p.* FROM MD_MENUS AS p
                LEFT JOIN MD_MENUS AS c ON p.ID_MENU = c.PARENT_ID
                LEFT JOIN MD_MENUS AS d ON c.ID_MENU = d.PARENT_ID
                WHERE d.ID_MENU IN (" . $menus . ")
                UNION ALL
                SELECT DISTINCT p.* FROM MD_MENUS AS p
                LEFT JOIN MD_MENUS AS c ON p.ID_MENU = c.PARENT_ID
                WHERE c.ID_MENU IN (" . $menus . ")
                UNION ALL
                SELECT * FROM MD_MENUS WHERE ID_MENU IN (" . $menus . ")
            ) AS a
            ORDER BY a.IS_PARENT, a.ORDER_BY
        "));

        if (!empty($query)) {
            $i = 1;
            foreach ($query as $row) {
                $menu[$i]['id'] = $row->ID_MENU;
                $menu[$i]['title'] = $row->NAMA_MENU;
                $menu[$i]['uri'] = $row->URI_MENU;
                $menu[$i]['icon'] = $row->ICON_MENU;
                $menu[$i]['parent'] = $row->PARENT_ID;
                $menu[$i]['is_parent'] = $row->IS_PARENT;
                $menu[$i]['show'] = $row->SHOW_MENU;
                $i++;
            }
        }

        $html_out = "";

        for ($i = 1; $i <= count($menu); $i++) {
            if (is_array($menu[$i])) {    // must be by construction but let's keep the errors home
                if ($menu[$i]['show'] && $menu[$i]['parent'] == 0) {    // are we allowed to see this menu?
                    $uri = $menu[$i]['uri'];
                    if ($menu[$i]['is_parent'] == TRUE) {
                        $html_out .= ' <li class="nav-item has-treeview">
                          <a href="javascript:void(0)" class="nav-link">
                          <i class="nav-icon '.$menu[$i]['icon'].'"></i>
                        <p>
                      ' . $menu[$i]['title'] . '
                       <i class="fas fa-angle-left right"></i>
                        </p></a>';
                    } else {
                        $html_out .= '<li class="nav-item"><a href="' . url($uri) . '"><p>' . $menu[$i]['title'] . '</p></a>';
                    }
                    $html_out .= self::get_childs($menu, $menu[$i]['id'], "");
                    $html_out .= '</li>';
                }
            }
        }

        return $html_out;
    }

    public static function get_childs($menu, $parent_id, $level) {
        $has_subcats = FALSE;
        $html_out = '';
        $html_out .= "<ul class='nav nav-treeview" . $level . "'>";

        for ($i = 1; $i <= count($menu); $i++) {
            if (is_array($menu[$i])) {
                if ($menu[$i]['show'] && $menu[$i]['parent'] == $parent_id) { // are we allowed to see this menu?
                    $has_subcats = TRUE;
                    $uri = $menu[$i]['uri'];
                    if ($menu[$i]['is_parent'] == TRUE) {
                        $html_out .= '<li><a href="javascript:void(0)">' . $menu[$i]['title'] . '<span class="fa arrow"></span></a>';
                    } else {
                        if($uri=='javascript:void(0)'){
                            $html_out .= '<li class="nav-item has-treeview"><a href="javascript:void(0)" class="nav-link">
                                     <i class="nav-icon fas fa-th"></i> <i class="fas fa-angle-left right"></i></i><p>' . $menu[$i]['title'] . '</p> </a>';
                        }
                        else{
                            $html_out .= '<li class="nav-item"><a href="' . url($uri) . '" id=menuId' . $menu[$i]['id'] . ' class="nav-link">
                                       <i class="far fa-circle nav-icon "></i><p>' . $menu[$i]['title'] . '</p> </a>';
                        }
                    }
                    $html_out .= self::get_childs($menu, $menu[$i]['id'], "");
                    $html_out .= '</li>';
                }
            }
        }
        $html_out .= '</ul>';
        return ($has_subcats) ? $html_out : FALSE;
    }
}
