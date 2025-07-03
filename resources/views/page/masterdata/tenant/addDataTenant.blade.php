@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Add Data Tenant - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Add Data Tenant
@endsection

@section('content')

<script src="https://cdn.ckeditor.com/4.10.0/standard/ckeditor.js"></script>
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

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('masterdata.tenant.adddatatenant') }}">
                        @csrf
                        <fieldset>
                            <h3 class="bold" style="padding-left: 5px;">
                                Basic Data
                            </h3>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Company*</label>
                                        <input type="text" name="MD_TENANT_NAME_CHAR" id="MD_TENANT_NAME_CHAR" class="form-control" placeholder="Name" maxlength="200">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Email*</label>
                                        <input type="text" name="MD_TENANT_EMAIL" id="MD_TENANT_EMAIL" class="form-control" placeholder="Email" maxlength="60">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>NIK</label>
                                        <input type="text" name="MD_TENANT_NIK" id="MD_TENANT_NIK" class="form-control" placeholder="NIK" maxlength="40">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Owner/PIC Company*</label>
                                        <input type="text" name="MD_TENANT_DIRECTOR" id="MD_TENANT_DIRECTOR" class="form-control" placeholder="Owner/PIC Company" maxlength="50">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Job Title Owner/PIC Company*</label>
                                        <input type="text" name="MD_TENANT_DIRECTOR_JOB_TITLE" id="MD_TENANT_DIRECTOR_JOB_TITLE" class="form-control" placeholder="Job Title Owner/PIC Company" maxlength="50">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>NPWP*</label>
                                        <input type="text" name="MD_TENANT_NPWP" id="MD_TENANT_NPWP" class="form-control" placeholder="NPWP" maxlength="30">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Telephone*</label>
                                        <input type="text" name="MD_TENANT_TELP" id="MD_TENANT_TELP" class="form-control" placeholder="Telephone" maxlength="60">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Address*</label>
                                        <textarea id="MD_TENANT_ADDRESS1" name="MD_TENANT_ADDRESS1" class="form-control" size="67x3" placeholder="Address" maxlength="100"></textarea>
                                        <script>
                                            CKEDITOR.replace('MD_TENANT_ADDRESS1');
                                        </script>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>City*</label>
                                        <input type="text" name="MD_TENANT_CITY_CHAR" id="MD_TENANT_CITY_CHAR" class="form-control" placeholder="City" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Post Code*</label>
                                        <input type="text" name="MD_TENANT_POSCODE" id="MD_TENANT_POSCODE" class="form-control" placeholder="Post Code" maxlength="35">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>PPH Status*</label>
                                        <select name="MD_TENANT_PPH_INT" class="form-control">
                                        <option value="0">Potong Sendiri</option>
                                        <option value="1">Potong Tenant</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Branded Status*</label>
                                        <select name="MD_TENANT_BRANDED_INT" class="form-control">
                                        <option value="0">Local</option>
                                        <option value="1">Branded</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Email Notif Invoice 1</label>
                                        <input type="text" name="MD_TENANT_EMAIL_INVOICE1" class="form-control" placeholder="Email Notif Invoice 1" maxlength="60">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Email Notif Invoice 2</label>
                                        <input type="text" name="MD_TENANT_EMAIL_INVOICE2" class="form-control" placeholder="Email Notif Invoice 2" maxlength="60">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Email Notif Invoice 3</label>
                                        <input type="text" name="MD_TENANT_EMAIL_INVOICE3" class="form-control" placeholder="Email Notif Invoice 3" maxlength="60">
                                    </div>
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
                                        <input type="text" name="MD_TENANT_CP_NAME" id="MD_TENANT_CP_NAME" class="form-control" placeholder="Contact Person" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Contact Person Telephone</label>
                                        <input type="text" name="MD_TENANT_CP_NO_TELP" id="MD_TENANT_CP_NO_TELP" class="form-control" placeholder="Contact Person Telephone" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Contact Person Handphone</label>
                                        <input type="text" name="MD_TENANT_CP_NO_HP" id="MD_TENANT_CP_NO_HP" class="form-control" placeholder="Contact Person Handphone" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Contact Person Email</label>
                                        <input type="text" name="MD_TENANT_CP_NO_EMAIL" id="MD_TENANT_CP_NO_EMAIL" class="form-control" placeholder="Contact Person Email" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Bank Name</label>
                                        <input type="text" id="MD_TENANT_BANK_NAME" name="MD_TENANT_BANK_NAME" class="form-control" placeholder="Bank Account Name" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Bank Location (KCP)</label>
                                        <input type="text" id="MD_TENANT_BANK_LOCATION" name="MD_TENANT_BANK_LOCATION" class="form-control" placeholder="Bank Location Name" maxlength="50">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Account Number</label>
                                        <input type="text" id="MD_TENANT_BANK_ACCOUNT" name="MD_TENANT_BANK_ACCOUNT" class="form-control" placeholder="Bank Account Number" maxlength="35">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label>Account Name</label>
                                        <input type="text" id="MD_TENANT_BANK_ACCOUNT_NAME" name="MD_TENANT_BANK_ACCOUNT_NAME" class="form-control" placeholder="Bank Account Owners Name" maxlength="35">
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
@endsection


