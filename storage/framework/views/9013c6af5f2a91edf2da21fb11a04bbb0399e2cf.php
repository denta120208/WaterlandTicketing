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
?>



<?php $__env->startSection('navbar_header'); ?>
    HOMEPAGE - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Homepage
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md">                
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md">
                                <h5><b>DASHBOARD</b></h5>
                            </div>
                        </div>
                        <div class="row"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/watergroup/public_html/metland_water/resources/views/home.blade.php ENDPATH**/ ?>