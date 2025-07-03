<?php
$menu = explode(",", session('menu'));
$project = explode(",", session('proyek'));
$level = session('level');
$default_project_no_char = session('default_project_no_char');

if(empty(session('id'))) {
  header("Location: http://watergroup.metropolitanland.com/logout");
  die();
}
else {
if(session('level') != NULL) {
    $project_arr_raw = $project;
    $project_arr = array();
    for($i = 0; $i < count($project_arr_raw); $i++) {
      $project_tmp = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR = '".$project_arr_raw[$i]."'");
      array_push($project_arr, $project_tmp[0]->PROJECT_NO_CHAR);
      if(empty(session('current_project'))) {
        session(['current_project' => $project_tmp[0]->PROJECT_NO_CHAR]);          
        session(['current_project_char' => strtoupper($project_tmp[0]->PROJECT_NAME)]);
      }
    }

    $project_arr_tmp = $project_arr;
    $project_arr_tmp = implode("','",$project_arr_tmp);
    $proyek = DB::select("SELECT * FROM MD_PROJECT WHERE PROJECT_NO_CHAR IN ('".$project_arr_tmp."')");
    session(['isLogin' => 1]);
  }
  else {
    header("Location: http://watergroup.metropolitanland.com/logout");
    die();
  }
}

use App\Navigations\MenuBuildNav;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?php echo csrf_token(); ?>" />
  <title>Water Group</title>
  <link rel="icon" href="{{asset('adminlte/dist/img/favicon.ico')}}" type="image/x-icon">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/fontawesome-free/css/all.min.css')}}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/jqvmap/jqvmap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('adminlte/dist/css/adminlte.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/daterangepicker/daterangepicker.css')}}">
  <!-- summernote -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/summernote/summernote-bs4.min.css')}}">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/select2/css/select2.min.css')}}">
  <link rel="stylesheet" href="{{asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.3.4/sweetalert2.min.css">
  <!-- JQuery -->
  <script src="{{asset('adminlte/plugins/jquery/jquery.min.js')}}"></script>
  <!-- Datepicker -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/css/datepicker.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/js/bootstrap-datepicker.min.js"></script>
  <!-- DataTables -->
  <link rel="stylesheet" href="{{asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">

  <style>
    #profileImage {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      background: #1c8282;
      font-size: 25px;
      color: #fff;
      text-align: center;
      line-height: 35px;
      font-weight: bold;
    }
  </style>
</head>

<div class="container">
  <div class="modal fade" id="changeProject" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Change Project</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="col-md">
              <div class="form-group">
                  <label>Proyek</label>
                  <select class="form-control select2" id="ddlChangeProject" name="ddlChangeProject" style="width: 100%;" onchange="changeProject()" required>
                      @foreach($proyek as $proyek)
                          @if(session('current_project') == $proyek->PROJECT_NO_CHAR)
                          <option value="{{$proyek->PROJECT_NO_CHAR}}" selected="selected">{{strtoupper($proyek->PROJECT_NAME)}}</option>
                          @else
                          <option value="{{$proyek->PROJECT_NO_CHAR}}">{{strtoupper($proyek->PROJECT_NAME)}}</option>
                          @endif
                      @endforeach
                  </select>
              </div>
          </div>
        </div>
        <!-- <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" onclick="changeProject();">Change</button>
        </div> -->
      </div>
    </div>
  </div>
</div>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="{{asset('adminlte/dist/img/AdminLTELogo.png')}}" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="javascript:void(0)" class="nav-link">@yield('navbar_header')</a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="javascript:void(0)" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('logout') }}" class="btn btn-block btn-danger">Logout</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="javascript:void(0)" class="brand-link">
      <img src="{{asset('adminlte/dist/img/logo_metland.png')}}" alt="Logo" class="brand-image" style="opacity: .8; padding-right: 20px;">
      <span class="brand-text font-weight-light"><h5><b>WATER GROUP</b></h5></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Version Apps -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex" style="text-align: center !important;">
        <div class="info">
          <a href="javascript:void(0)" class="d-block">VERSION : 1.0.0</a>
        </div>
      </div>
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <div id="profileImage"></div>
          <!-- <img src="{{asset('adminlte/dist/img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image"> -->
        </div>
        <div class="info">
          <a href="javascript:void(0)" class="d-block" id="first_name"><?php echo strtoupper(session('first_name')) ?></a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
              
          <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link" data-toggle="modal" data-target="#changeProject">
              <i class="nav-icon fas fa-copy"></i>
              <p>
                Change Project
              </p>
            </a>
          </li>

          <li class="nav-item">
            @if(session('menuParentActive') == "homepage")
            <a href="{{ route('home') }}" class="nav-link active">
            @else
            <a href="{{ route('home') }}" class="nav-link">
            @endif
              <i class="nav-icon fas fa-th"></i>
              <p>
                Homepage
              </p>
            </a>
          </li>

          @if(array_search("4", $menu) !== false)
            @if(session('menuParentActive') == "masterData")
            <li class="nav-item menu-open">
              <a href="javascript:void(0)" class="nav-link active">
            @else
            <li class="nav-item">
              <a href="javascript:void(0)" class="nav-link">
            @endif
                <i class="nav-icon fas fa-table"></i>
                <p>
                  Master Data
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "equipment")
                  <a href="{{ route('equipment') }}" class="nav-link active">
                  @else
                  <a href="{{ route('equipment') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Equipment</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "equipment_category")
                  <a href="{{ route('equipmentCategory') }}" class="nav-link active">
                  @else
                  <a href="{{ route('equipmentCategory') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Equipment Category</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "payment_method")
                  <a href="{{ route('paymentMethod') }}" class="nav-link active">
                  @else
                  <a href="{{ route('paymentMethod') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Payment Method</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "promo_equipment")
                  <a href="{{ route('promoEquipment') }}" class="nav-link active">
                  @else
                  <a href="{{ route('promoEquipment') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Promo Equipment</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "promo")
                  <a href="{{ route('promo') }}" class="nav-link active">
                  @else
                  <a href="{{ route('promo') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Promo Ticket</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "ticket_group")
                  <a href="{{ route('ticketGroup') }}" class="nav-link active">
                  @else
                  <a href="{{ route('ticketGroup') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Ticket Group</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "ticket_price")
                  <a href="{{ route('ticketPrice') }}" class="nav-link active">
                  @else
                  <a href="{{ route('ticketPrice') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Ticket Price</p>
                  </a>
                </li>
              </ul>
              {{-- <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "group_membership")
                  <a href="{{ route('groupMembership') }}" class="nav-link active">
                  @else
                  <a href="{{ route('groupMembership') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Group Membership</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "group_type_membership")
                  <a href="{{ route('groupTypeMembership') }}" class="nav-link active">
                  @else
                  <a href="{{ route('groupTypeMembership') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Group Type Membership</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "periode_membership")
                  <a href="{{ route('periodeMembership') }}" class="nav-link active">
                  @else
                  <a href="{{ route('periodeMembership') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Periode Membership</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "price_membership")
                  <a href="{{ route('priceMembership') }}" class="nav-link active">
                  @else
                  <a href="{{ route('priceMembership') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Price Membership</p>
                  </a>
                </li>
              </ul> --}}
            </li>
          @endif
          @if(array_search("1", $menu) !== false)
            @if(session('menuParentActive') == "sales")
            <li class="nav-item menu-open">
              <a href="javascript:void(0)" class="nav-link active">
            @else
            <li class="nav-item">
              <a href="javascript:void(0)" class="nav-link">
            @endif
                <i class="nav-icon fas fa-table"></i>
                <p>
                  Sales
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "ticket_purchase")
                  <a href="{{ route('ticket_purchase') }}" class="nav-link active">
                  @else
                  <a href="{{ route('ticket_purchase') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Ticket Purchase</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                @if(session('menuSubActive') == "sales_report")
                <li class="nav-item menu-open">
                @else
                <li class="nav-item">
                @endif
                  <a href="javascript:void(0)" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Report <i class="right fas fa-angle-left"></i></p>
                  </a>
                  <ul class="nav nav-treeview">
                    @if(session('menuSubSubActive') == "visitors")
                    <li class="nav-item menu-open">
                      <a href="{{ route('report_visitors') }}" class="nav-link active">
                    @else
                    <li class="nav-item">
                      <a href="{{ route('report_visitors') }}" class="nav-link">
                    @endif
                        <i class="far fa-circle nav-icon"></i>
                        <p>Visitors</p>
                      </a>
                    </li>
                  </ul>
                  <ul class="nav nav-treeview">
                    @if(session('menuSubSubActive') == "visitors_by_time")
                    <li class="nav-item menu-open">
                      <a href="{{ route('report_visitors_by_time') }}" class="nav-link active">
                    @else
                    <li class="nav-item">
                      <a href="{{ route('report_visitors_by_time') }}" class="nav-link">
                    @endif
                        <i class="far fa-circle nav-icon"></i>
                        <p>Visitors By Time</p>
                      </a>
                    </li>
                  </ul>
                  <ul class="nav nav-treeview">
                    @if(session('menuSubSubActive') == "revenue")
                    <li class="nav-item menu-open">
                      <a href="{{ route('report_revenue') }}" class="nav-link active">
                    @else
                    <li class="nav-item">
                      <a href="{{ route('report_revenue') }}" class="nav-link">
                    @endif
                        <i class="far fa-circle nav-icon"></i>
                        <p>Revenue</p>
                      </a>
                    </li>
                  </ul>
                  <ul class="nav nav-treeview">
                    @if(session('menuSubSubActive') == "rev_ticket_by_payment_method")
                    <li class="nav-item menu-open">
                      <a href="{{ route('report_rev_ticket_by_payment_method') }}" class="nav-link active">
                    @else
                    <li class="nav-item">
                      <a href="{{ route('report_rev_ticket_by_payment_method') }}" class="nav-link">
                    @endif
                        <i class="far fa-circle nav-icon"></i>
                        <p>Revenue Ticket By Payment Method</p>
                      </a>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
          @endif
          @if(array_search("2", $menu) !== false)
            @if(session('menuParentActive') == "scanning")
            <li class="nav-item menu-open">
              <a href="javascript:void(0)" class="nav-link active">
            @else
            <li class="nav-item">
              <a href="javascript:void(0)" class="nav-link">
            @endif
                <i class="nav-icon fas fa-table"></i>
                <p>
                  Scanning
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "scan_ticket_purchase")
                  <a href="{{ route('scan_ticket_purchase') }}" class="nav-link active">
                  @else
                  <a href="{{ route('scan_ticket_purchase') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Scan Ticket Purchase</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "visitors_counter")
                  <a href="{{ route('visitors_counter') }}" class="nav-link active">
                  @else
                  <a href="{{ route('visitors_counter') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Visitors Counter</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                @if(session('menuSubActive') == "scanning_report")
                <li class="nav-item menu-open">
                @else
                <li class="nav-item">
                @endif
                  <a href="javascript:void(0)" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Report <i class="right fas fa-angle-left"></i></p>
                  </a>
                  <ul class="nav nav-treeview">
                    @if(session('menuSubSubActive') == "visitors_login")
                    <li class="nav-item menu-open">
                      <a href="{{ route('report_visitors_login') }}" class="nav-link active">
                    @else
                    <li class="nav-item">
                      <a href="{{ route('report_visitors_login') }}" class="nav-link">
                    @endif
                        <i class="far fa-circle nav-icon"></i>
                        <p>Visitors Login</p>
                      </a>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
          @endif
          @if(array_search("3", $menu) !== false)
            @if(session('menuParentActive') == "rental")
            <li class="nav-item menu-open">
              <a href="javascript:void(0)" class="nav-link active">
            @else
            <li class="nav-item">
              <a href="javascript:void(0)" class="nav-link">
            @endif
                <i class="nav-icon fas fa-table"></i>
                <p>
                  Rental
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <!-- <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "swimming_equipment")
                  <a href="{{ route('swimming_equipment') }}" class="nav-link active">
                  @else
                  <a href="{{ route('swimming_equipment') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Swimming Equipment</p>
                  </a>
                </li>
              </ul> -->
              <!-- <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "locker")
                  <a href="{{ route('locker') }}" class="nav-link active">
                  @else
                  <a href="{{ route('locker') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Locker</p>
                  </a>
                </li>
              </ul> -->
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  @if(session('menuSubActive') == "rental_equipment")
                  <a href="{{ route('rentEquipment') }}" class="nav-link active">
                  @else
                  <a href="{{ route('rentEquipment') }}" class="nav-link">
                  @endif
                    <i class="far fa-circle nav-icon"></i>
                    <p>Equipment</p>
                  </a>
                </li>
              </ul>
              <ul class="nav nav-treeview">
                @if(session('menuSubActive') == "rental_report")
                <li class="nav-item menu-open">
                @else
                <li class="nav-item">
                @endif
                  <a href="javascript:void(0)" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Report <i class="right fas fa-angle-left"></i></p>
                  </a>
                  <ul class="nav nav-treeview">
                    @if(session('menuSubSubActive') == "equipment_report")
                    <li class="nav-item menu-open">
                      <a href="{{ route('report_equipment') }}" class="nav-link active">
                    @else
                    <li class="nav-item">
                      <a href="{{ route('report_equipment') }}" class="nav-link">
                    @endif
                        <i class="far fa-circle nav-icon"></i>
                        <p>Equipment</p>
                      </a>
                    </li>
                  </ul>
                  <!-- <ul class="nav nav-treeview">
                    @if(session('menuSubSubActive') == "swimming_equipment_report")
                    <li class="nav-item menu-open">
                      <a href="{{ route('report_swimming_equipment') }}" class="nav-link active">
                    @else
                    <li class="nav-item">
                      <a href="{{ route('report_swimming_equipment') }}" class="nav-link">
                    @endif
                        <i class="far fa-circle nav-icon"></i>
                        <p>Swimming Equipment</p>
                      </a>
                    </li>
                  </ul> -->
                  <!-- <ul class="nav nav-treeview">
                    @if(session('menuSubSubActive') == "locker_report")
                    <li class="nav-item menu-open">
                      <a href="{{ route('report_locker') }}" class="nav-link active">
                    @else
                    <li class="nav-item">
                      <a href="{{ route('report_locker') }}" class="nav-link">
                    @endif
                        <i class="far fa-circle nav-icon"></i>
                        <p>Locker</p>
                      </a>
                    </li>
                  </ul> -->
                </li>
              </ul>
            </li>
          @endif
          @if(array_search("5", $menu) !== false)
            @if(session('menuParentActive') == "marketing")
            <li class="nav-item menu-open">
              <a href="javascript:void(0)" class="nav-link active">
            @else
            <li class="nav-item">
              <a href="javascript:void(0)" class="nav-link">
            @endif
                <i class="nav-icon fas fa-table"></i>
                <p>
                  Marketing
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                @if(session('menuSubActive') == "letter_of_intent")
                <li class="nav-item menu-open">
                @else
                <li class="nav-item">
                @endif
                  <a href="javascript:void(0)" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Letter Of Intent <i class="right fas fa-angle-left"></i></p>
                  </a>
                  <ul class="nav nav-treeview">
                    @if(session('menuSubSubActive') == "list_letter_of_intent")
                    <li class="nav-item menu-open">
                      <a href="{{ route('marketing.leaseagreement.viewlistdatanew') }}" class="nav-link active">
                    @else
                    <li class="nav-item">
                      <a href="{{ route('marketing.leaseagreement.viewlistdatanew') }}" class="nav-link">
                    @endif
                        <i class="far fa-circle nav-icon"></i>
                        <p>List Letter Of Intent</p>
                      </a>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
          @endif
          @if(array_search("6", $menu) !== false)
            @if(session('menuParentActive') == "finance_in_flow")
            <li class="nav-item menu-open">
              <a href="javascript:void(0)" class="nav-link active">
            @else
            <li class="nav-item">
              <a href="javascript:void(0)" class="nav-link">
            @endif
                <i class="nav-icon fas fa-table"></i>
                <p>
                  Finance In Flow
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                @if(session('menuSubActive') == "invoice")
                <li class="nav-item menu-open">
                @else
                <li class="nav-item">
                @endif
                  <a href="javascript:void(0)" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>Invoice <i class="right fas fa-angle-left"></i></p>
                  </a>
                  <ul class="nav nav-treeview">
                    @if(session('menuSubSubActive') == "generate_invoice")
                    <li class="nav-item menu-open">
                      <a href="{{ route('invoice.listgenerateinvoice') }}" class="nav-link active">
                    @else
                    <li class="nav-item">
                      <a href="{{ route('invoice.listgenerateinvoice') }}" class="nav-link">
                    @endif
                        <i class="far fa-circle nav-icon"></i>
                        <p>Generate Invoice</p>
                      </a>
                    </li>
                  </ul>
                </li>
              </ul>
            </li>
          @endif
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-12">
            <h1 class="m-0">@yield('header_title')</h1>
          </div>
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    @yield('content')
    <!-- /.content -->
  </div>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{asset('adminlte/plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset('adminlte/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- ChartJS -->
<script src="{{asset('adminlte/plugins/chart.js/Chart.min.js')}}"></script>
<!-- Sparkline -->
<script src="{{asset('adminlte/plugins/sparklines/sparkline.js')}}"></script>
<!-- JQVMap -->
<script src="{{asset('adminlte/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<!-- jQuery Knob Chart -->
<script src="{{asset('adminlte/plugins/jquery-knob/jquery.knob.min.js')}}"></script>
<!-- daterangepicker -->
<script src="{{asset('adminlte/plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{asset('adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>
<!-- Summernote -->
<script src="{{asset('adminlte/plugins/summernote/summernote-bs4.min.js')}}"></script>
<!-- overlayScrollbars -->
<script src="{{asset('adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('adminlte/dist/js/adminlte.js')}}"></script>
<!-- Datepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/js/bootstrap-datepicker.min.js"></script>
<!-- AdminLTE for demo purposes -->
<!-- <script src="{{asset('adminlte/dist/js/demo.js')}}"></script> -->
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{asset('adminlte/dist/js/pages/dashboard.js')}}"></script>
<!-- Select2 -->
<script src="{{asset('adminlte/plugins/select2/js/select2.full.min.js')}}"></script>
<!-- DataTables  & Plugins -->
<script src="{{asset('adminlte/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/jszip/jszip.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/pdfmake/vfs_fonts.js')}}"></script>
<script src="{{asset('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
<script>
  $(function () {
    $('.select2').select2({
      theme: 'bootstrap4'
    });
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    });
  });
</script>
<!-- Sweetalert2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.3.4/sweetalert2.min.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    var intials = $('#first_name').text().charAt(0);
    var profileImage = $('#profileImage').text(intials);
  });

  function changeProject() {
      var proyek = document.getElementById("ddlChangeProject").value;
      var url = '{{ url("change_project/proyek") }}';
      url = url.replace('proyek', proyek);
      window.location.href = url;
  }
</script>
</body>
</html>
