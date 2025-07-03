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
  <link rel="icon" href="<?php echo e(asset('adminlte/dist/img/favicon.ico')); ?>" type="image/x-icon">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo e(asset('adminlte/plugins/fontawesome-free/css/all.min.css')); ?>">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="<?php echo e(asset('adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')); ?>">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo e(asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css')); ?>">
  <!-- JQVMap -->
  <link rel="stylesheet" href="<?php echo e(asset('adminlte/plugins/jqvmap/jqvmap.min.css')); ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo e(asset('adminlte/dist/css/adminlte.min.css')); ?>">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?php echo e(asset('adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')); ?>">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo e(asset('adminlte/plugins/daterangepicker/daterangepicker.css')); ?>">
  <!-- summernote -->
  <link rel="stylesheet" href="<?php echo e(asset('adminlte/plugins/summernote/summernote-bs4.min.css')); ?>">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo e(asset('adminlte/plugins/select2/css/select2.min.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')); ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.3.4/sweetalert2.min.css">
  <!-- JQuery -->
  <script src="<?php echo e(asset('adminlte/plugins/jquery/jquery.min.js')); ?>"></script>
  <!-- Datepicker -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/css/datepicker.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/js/bootstrap-datepicker.min.js"></script>
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo e(asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')); ?>">

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
                      <?php $__currentLoopData = $proyek; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proyek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <?php if(session('current_project') == $proyek->PROJECT_NO_CHAR): ?>
                          <option value="<?php echo e($proyek->PROJECT_NO_CHAR); ?>" selected="selected"><?php echo e(strtoupper($proyek->PROJECT_NAME)); ?></option>
                          <?php else: ?>
                          <option value="<?php echo e($proyek->PROJECT_NO_CHAR); ?>"><?php echo e(strtoupper($proyek->PROJECT_NAME)); ?></option>
                          <?php endif; ?>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
    <img class="animation__shake" src="<?php echo e(asset('adminlte/dist/img/AdminLTELogo.png')); ?>" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="javascript:void(0)" class="nav-link"><?php echo $__env->yieldContent('navbar_header'); ?></a>
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
        <a href="<?php echo e(route('logout')); ?>" class="btn btn-block btn-danger" onclick="removeAllSession()">Logout</a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="javascript:void(0)" class="brand-link">
      <img src="<?php echo e(asset('adminlte/dist/img/logo_metland.png')); ?>" alt="Logo" class="brand-image" style="opacity: .8; padding-right: 20px;">
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
          <!-- <img src="<?php echo e(asset('adminlte/dist/img/user2-160x160.jpg')); ?>" class="img-circle elevation-2" alt="User Image"> -->
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

          <li class="nav-item has-treeview">
            <a href="<?php echo e(route('home')); ?>" id="menuIdHomepage" class="nav-link">
              <i class="nav-icon fas fa-th"></i>
              <p>
                Homepage
              </p>
            </a>
          </li>

          <?php echo MenuBuildNav::menus(); ?>

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
            <h1 class="m-0"><?php echo $__env->yieldContent('header_title'); ?></h1>
          </div>
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <?php echo $__env->yieldContent('content'); ?>
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
<script src="<?php echo e(asset('adminlte/plugins/jquery/jquery.min.js')); ?>"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo e(asset('adminlte/plugins/jquery-ui/jquery-ui.min.js')); ?>"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?php echo e(asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')); ?>"></script>
<!-- ChartJS -->
<script src="<?php echo e(asset('adminlte/plugins/chart.js/Chart.min.js')); ?>"></script>
<!-- Sparkline -->
<script src="<?php echo e(asset('adminlte/plugins/sparklines/sparkline.js')); ?>"></script>
<!-- JQVMap -->
<script src="<?php echo e(asset('adminlte/plugins/jqvmap/jquery.vmap.min.js')); ?>"></script>
<script src="<?php echo e(asset('adminlte/plugins/jqvmap/maps/jquery.vmap.usa.js')); ?>"></script>
<!-- jQuery Knob Chart -->
<script src="<?php echo e(asset('adminlte/plugins/jquery-knob/jquery.knob.min.js')); ?>"></script>
<!-- daterangepicker -->
<script src="<?php echo e(asset('adminlte/plugins/moment/moment.min.js')); ?>"></script>
<script src="<?php echo e(asset('adminlte/plugins/daterangepicker/daterangepicker.js')); ?>"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?php echo e(asset('adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')); ?>"></script>
<!-- Summernote -->
<script src="<?php echo e(asset('adminlte/plugins/summernote/summernote-bs4.min.js')); ?>"></script>
<!-- overlayScrollbars -->
<script src="<?php echo e(asset('adminlte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')); ?>"></script>
<!-- AdminLTE App -->
<script src="<?php echo e(asset('adminlte/dist/js/adminlte.js')); ?>"></script>
<!-- Datepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.2.0/js/bootstrap-datepicker.min.js"></script>
<!-- AdminLTE for demo purposes -->
<!-- <script src="<?php echo e(asset('adminlte/dist/js/demo.js')); ?>"></script> -->
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo e(asset('adminlte/dist/js/pages/dashboard.js')); ?>"></script>
<!-- Select2 -->
<script src="<?php echo e(asset('adminlte/plugins/select2/js/select2.full.min.js')); ?>"></script>
<!-- DataTables  & Plugins -->
<script src="<?php echo e(asset('adminlte/plugins/datatables/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')); ?>"></script>
<script src="<?php echo e(asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js')); ?>"></script>
<script src="<?php echo e(asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')); ?>"></script>
<script src="<?php echo e(asset('adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js')); ?>"></script>
<script src="<?php echo e(asset('adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')); ?>"></script>
<script src="<?php echo e(asset('adminlte/plugins/jszip/jszip.min.js')); ?>"></script>
<script src="<?php echo e(asset('adminlte/plugins/pdfmake/pdfmake.min.js')); ?>"></script>
<script src="<?php echo e(asset('adminlte/plugins/pdfmake/vfs_fonts.js')); ?>"></script>
<script src="<?php echo e(asset('adminlte/plugins/datatables-buttons/js/buttons.html5.min.js')); ?>"></script>
<script src="<?php echo e(asset('adminlte/plugins/datatables-buttons/js/buttons.print.min.js')); ?>"></script>
<script src="<?php echo e(asset('adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js')); ?>"></script>
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
  // sessionStorage.setItem('activeMenu', $(this).attr('id'));

  $(document).ready(function() {
    var intials = $('#first_name').text().charAt(0);
    var profileImage = $('#profileImage').text(intials);

    $('.nav-pills .nav-link').click(function() {
        activeMenu = $(this).attr('id');
        if(activeMenu !== undefined) {
          sessionStorage.setItem('activeMenu', activeMenu);
        }
    });

    var activeMenu = sessionStorage.getItem('activeMenu');
    $('#' + activeMenu).addClass('active');
    $('#' + activeMenu).parents().closest('.has-treeview').addClass('menu-open');
  });

  function changeProject() {
      var proyek = document.getElementById("ddlChangeProject").value;
      var url = '<?php echo e(url("change_project/proyek")); ?>';
      url = url.replace('proyek', proyek);
      window.location.href = url;
  }

  function removeAllSession() {
    sessionStorage.removeItem("activeMenu");
  }
</script>
</body>
</html>
<?php /**PATH /home/watergroup/public_html/metland_water/resources/views/layouts/mainLayouts.blade.php ENDPATH**/ ?>