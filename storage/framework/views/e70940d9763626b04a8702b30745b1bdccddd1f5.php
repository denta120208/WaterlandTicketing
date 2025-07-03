<?php $__env->startSection('navbar_header'); ?>
    Form Edit Data Tenant - <b><?php echo e(session('current_project_char')); ?></b>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('header_title'); ?>
    Form Edit Data Tenant
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<script src="https://cdn.ckeditor.com/4.10.0/standard/ckeditor.js"></script>
<?php if($errors->any()): ?>
    <ul class="alert alert-danger">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
<?php endif; ?>

<?php if(Session::has('message')): ?>
    <div class="alert alert-success" id="success-alert">
        <?php echo e(Session::get('message')); ?>

    </div>
<?php endif; ?>
<script>
    $(function()
    {
        $("#startDate" ).datepicker({
            dateFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true,
            onClick : function(date){
                document.getElementById('startDate').value = date;
            }
        });
    });
</script>
<script>
    $(document).ready(function()    {
        $('#engineering_table').DataTable({
            pageLength : 25
        });
    });
</script>
<script type="text/javascript">
    function delItem(id){
        $.ajax({
            type: "post",
            url: "<?php echo e(route('masterdata.tenant.deleteitemaddresstenant')); ?>",
            data: {MD_TENANT_TAX_ID_INT:id, _token: "<?php echo e(csrf_token()); ?>"},
            dataType: 'json',
            cache: false,
            beforeSend: function(){ $('#loading').modal('show'); },
            success: function (response) {
                if(response['Success']){
                    document.location.reload(true);
                }else{
                    alert(response['Error']);
                }
                $('#loading').modal('hide');
            },
            error: function() {
                alert('Error, Please contact Administrator!');
            }
        });
    };

    function getItem(id){
        $.ajax({
            type: "post",
            url: "<?php echo e(route('masterdata.tenant.getitemaddresstenant')); ?>",
            data: {MD_TENANT_TAX_ID_INT:id, _token: "<?php echo e(csrf_token()); ?>"},
            dataType: 'json',
            cache: false,
            beforeSend: function(){ $('#loading').modal('show'); },
            success: function( data ) {
                if(data['status'] == 'success'){
                    $("#address_tax").val(data['MD_TENANT_ADDRESS_TAX']);
                    $("#address_tax_id").val(data['MD_TENANT_TAX_ID_INT']);

                    $("#insert_id").val('0');
                }else{
                    alert(data['msg']);
                }
                $('#loading').modal('hide');
            }
        });
    };

    $(function(){
        $('#update').on('click',function(){
            var address_tax = $("#address_tax").val();
            var address_tax_id = $("#address_tax_id").val();
            var tenant_nochar = $("#tenant_nochar").val();
            var insert_id = $("#insert_id").val();

            if (address_tax === '')
            {
                alert('Input Failed, Enter All Data Correctly');
                return false;
            }
            else
            {
                $.ajax({
                    type: "post",
                    url: "<?php echo e(route('masterdata.tenant.saveaddresstenant')); ?>",
                    data: {MD_TENANT_ADDRESS_TAX:address_tax,
                        MD_TENANT_NOCHAR:tenant_nochar,
                        MD_TENANT_TAX_ID_INT:address_tax_id,
                        insert_id:insert_id,
                        _token: "<?php echo e(csrf_token()); ?>"},
                    dataType: 'json',
                    cache: false,
                    beforeSend: function(){ $('#loading').modal('show'); },
                    success: function (response) {
                        if(response['Success']){
                            document.location.reload(true);
                        }else{
                            alert(response['Error']);
                        }
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
        });
    });
</script>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('masterdata.tenant.editdatatenant')); ?>">
                        <fieldset>
                            <h3 class="bold" style="padding-left: 5px;">
                                Basic Data
                            </h3>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Company*</label>
                                        <input type="text" name="MD_TENANT_NAME_CHAR" class="form-control" placeholder="Name" maxlength="200" value="<?php echo $dataTenant->MD_TENANT_NAME_CHAR; ?>" />
                                        <input type="hidden" name="MD_TENANT_NOCHAR" class="form-control" placeholder="Name" id="tenant_nochar" readonly="yes" value="<?php echo $dataTenant->MD_TENANT_NOCHAR; ?>" />
                                        <input type="hidden" name="MD_TENANT_ID_INT" class="form-control" placeholder="Name" id="tenant_id" readonly="yes" value="<?php echo $dataTenant->MD_TENANT_ID_INT; ?>" />
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Email*</label>
                                        <input type="text" name="MD_TENANT_EMAIL" class="form-control" placeholder="Email" maxlength="60" value="<?php echo $dataTenant->MD_TENANT_EMAIL; ?>" />
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>NIK</label>
                                        <input type="text" name="MD_TENANT_NIK" class="form-control" placeholder="NIK" maxlength="40" value="<?php echo $dataTenant->MD_TENANT_NIK; ?>" />
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Owner/PIC Company*</label>
                                        <input type="text" name="MD_TENANT_DIRECTOR" class="form-control" placeholder="Owner/PIC Company" maxlength="50" value="<?php echo $dataTenant->MD_TENANT_DIRECTOR ?>">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Job Title Owner/PIC Company*</label>
                                        <input type="text" name="MD_TENANT_DIRECTOR_JOB_TITLE" class="form-control" placeholder="Job Title Owner/PIC Company" maxlength="50" value="<?php echo $dataTenant->MD_TENANT_DIRECTOR_JOB_TITLE ?>">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>NPWP*</label>
                                        <input type="text" name="MD_TENANT_NPWP" class="form-control" placeholder="NPWP" maxlength="30" value="<?php echo $dataTenant->MD_TENANT_NPWP ?>">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Telephone*</label>
                                        <input type="text" name="MD_TENANT_TELP" class="form-control" placeholder="Telephone" maxlength="60" value="<?php echo $dataTenant->MD_TENANT_TELP ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Address*</label>
                                        <textarea name="MD_TENANT_ADDRESS1" id="MD_TENANT_ADDRESS1" class="form-control" size="67x3" placeholder="Address" maxlength="100">
                                            <?php echo $dataTenant->MD_TENANT_ADDRESS1; ?>
                                        </textarea>
                                        <script>
                                            CKEDITOR.replace('MD_TENANT_ADDRESS1');
                                        </script>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>City*</label>
                                        <input type="text" name="MD_TENANT_CITY_CHAR" class="form-control" placeholder="City" maxlength="20" value="<?php echo $dataTenant->MD_TENANT_CITY_CHAR; ?>">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Post Code*</label>
                                        <input type="text" name="MD_TENANT_POSCODE" class="form-control" placeholder="Post Code" maxlength="35" value="<?php echo $dataTenant->MD_TENANT_POSCODE; ?>">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>PPH Status*</label>
                                        <select name="MD_TENANT_PPH_INT" class="form-control">
                                        <option value="">Please Choose</option>
                                        <option value="1" <?php echo $dataTenant->MD_TENANT_PPH_INT == 1 ? 'selected' : ''; ?>>Potong Tenant</option>
                                        <option value="0" <?php echo $dataTenant->MD_TENANT_PPH_INT == 0 ? 'selected' : ''; ?>>Potong Sendiri</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Branded Status*</label>
                                        <select name="MD_TENANT_BRANDED_INT" class="form-control">
                                        <option value="">Please Choose</option>
                                        <option value="1" <?php echo $dataTenant->MD_TENANT_BRANDED_INT == 1 ? 'selected' : ''; ?>>Branded</option>
                                        <option value="0" <?php echo $dataTenant->MD_TENANT_BRANDED_INT == 0 ? 'selected' : ''; ?>>Local</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Email Notif Invoice 1</label>
                                        <input type="text" name="MD_TENANT_EMAIL_INVOICE1" id="MD_TENANT_EMAIL_INVOICE1" value="<?php echo $dataTenant->MD_TENANT_EMAIL_INVOICE1 ?>" class="form-control" placeholder="Email Notif Invoice 1" maxlength="60">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Email Notif Invoice 2</label>
                                        <input type="text" name="MD_TENANT_EMAIL_INVOICE2" id="MD_TENANT_EMAIL_INVOICE2" value="<?php echo $dataTenant->MD_TENANT_EMAIL_INVOICE2 ?>" class="form-control" placeholder="Email Notif Invoice 2" maxlength="60">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Email Notif Invoice 3</label>
                                        <input type="text" name="MD_TENANT_EMAIL_INVOICE3" id="MD_TENANT_EMAIL_INVOICE3" value="<?php echo $dataTenant->MD_TENANT_EMAIL_INVOICE3 ?>" class="form-control" placeholder="Email Notif Invoice 3" maxlength="60">
                                    </div>
                                </div>
                            </div>
                            <br><br>
                            <h3 class="bold" style="padding-left: 5px;">
                                Address Tax
                            </h3>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Address Tax*</label>
                                        <input type="text" name="MD_TENANT_ADDRESS_TAX" class="form-control" id="address_tax" placeholder="Address Tax">
                                        <input type="hidden" name="MD_TENANT_TAX_ID_INT" class="form-control" id="address_tax_id" value="0" readonly>
                                        <input type="hidden" name="insert_id" class="form-control" id="insert_id" value="1" readonly>
                                        <br>
                                        <a href="#" class="btn btn-info" data-toggle="modal" name="buttonSave" id="update" style="float: right;">
                                            Insert/Update
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-md-12">
                                    <table class="table-striped table-hover compact" id="engineering_table" cellspacing="0" width="100%">
                                        <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Address Tax</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $i = 1;?>
                                        <?php $__currentLoopData = $dataAddressTax; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($i); ?></td>
                                                <td><?php echo e($data->MD_TENANT_ADDRESS_TAX); ?></td>
                                                <?php if($data->JML_PSM > 0): ?>
                                                    <td style="text-align:center;">
                                                        <i class='fa fa-edit' title='Edit Data' onclick='getItem(<?php echo $data->MD_TENANT_TAX_ID_INT; ?>);'></i>
                                                    </td>
                                                <?php else: ?>
                                                    <td style="text-align:center;">
                                                        <i class='fa fa-edit' title='Edit Data' onclick='getItem(<?php echo $data->MD_TENANT_TAX_ID_INT; ?>);'></i>|
                                                        <i class='fa fa-trash' title='Delete Data' onclick='delItem(<?php echo $data->MD_TENANT_TAX_ID_INT; ?>);'></i>
                                                    </td>
                                                <?php endif; ?>
                                                <?php $i++;?>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <br><br>
                            <h3 class="bold" style="padding-left: 5px;">
                                Additional Data
                            </h3>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Contact Person</label>
                                        <input type="text" name="MD_TENANT_CP_NAME" id="MD_TENANT_CP_NAME" class="form-control" placeholder="Contact Person" maxlength="20" value="<?php echo $dataTenant->MD_TENANT_CP_NAME; ?>">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Contact Person Telephone</label>
                                        <input type="text" name="MD_TENANT_CP_NO_TELP" id="MD_TENANT_CP_NO_TELP" class="form-control" placeholder="Contact Person Telephone" maxlength="20" value="<?php echo $dataTenant->MD_TENANT_CP_NO_TELP; ?>">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Contact Person Handphone</label>
                                        <input type="text" name="MD_TENANT_CP_NO_HP" id="MD_TENANT_CP_NO_HP" class="form-control" placeholder="Contact Person Handphone" maxlength="20" value="<?php echo $dataTenant->MD_TENANT_CP_NO_HP; ?>">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Contact Person Email</label>
                                        <input type="text" name="MD_TENANT_CP_NO_EMAIL" id="MD_TENANT_CP_NO_EMAIL" class="form-control" placeholder="Contact Person Email" maxlength="20" value="<?php echo $dataTenant->MD_TENANT_CP_NO_EMAIL; ?>">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Bank Name</label>
                                        <input type="text" name="MD_TENANT_BANK_NAME" id="MD_TENANT_BANK_NAME" class="form-control" placeholder="Bank Account Name" maxlength="20" value="<?php echo $dataTenant->MD_TENANT_BANK_NAME; ?>">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Bank Location (KCP)</label>
                                        <input type="text" name="MD_TENANT_BANK_LOCATION" id="MD_TENANT_BANK_LOCATION" class="form-control" placeholder="Bank Location Name" maxlength="50" value="<?php echo $dataTenant->MD_TENANT_BANK_LOCATION; ?>">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Account Number</label>
                                        <input type="text" name="MD_TENANT_BANK_ACCOUNT" id="MD_TENANT_BANK_ACCOUNT" class="form-control" placeholder="Bank Account Number" maxlength="35" value="<?php echo $dataTenant->MD_TENANT_BANK_ACCOUNT; ?>">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Account Name</label>
                                        <input type="text" name="MD_TENANT_BANK_ACCOUNT_NAME" id="MD_TENANT_BANK_ACCOUNT_NAME" class="form-control" placeholder="Bank Account Owners Name" maxlength="35" value="<?php echo $dataTenant->MD_TENANT_BANK_ACCOUNT_NAME; ?>">
                                    </div>
                                    <a href="#confModalCustOnly" class="btn btn-primary" style="float: right;" data-toggle="modal">
                                        Save Tenant
                                    </a>
                                    <div id="confModalCustOnly" class="modal fade">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title">Confirmation</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure save this data ?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
                                                    <input type="submit" class="btn btn-primary" name="buttonSave" id="saveCustomer" value="Save Data Tenant">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.mainLayouts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/trialwatergroup.metropolitanland.com/html/metland_water/resources/views/page/masterdata/tenant/editDataTenant.blade.php ENDPATH**/ ?>