@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Add Credit Notes - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Add Credit Notes
@endsection

@section('content')

<style>
    th, td {
        padding: 15px;
    }
</style>

<script src="https://cdn.ckeditor.com/4.10.0/standard/ckeditor.js"></script>
<script>
    $(document).ready(function()
    {
        $('#tdp_report1').DataTable( {
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });
    } );

</script>

<script>
    $(function(){
        var table = $('#lot_table').DataTable({
            order:[]
        });

        $('#lot_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('lot_stock_id').value = checkEmptyStringValidation(data[0]);
            document.getElementById('psm_trans_nochar').value = checkEmptyStringValidation(data[1]);
            document.getElementById('lot_stock_no').value = checkEmptyStringValidation(data[2]);
            document.getElementById('md_tenant_name').value = checkEmptyStringValidation(data[3]);
            document.getElementById('lot_stock_sqm').value = checkEmptyStringValidation(data[4]);
            document.getElementById('md_tenant_id_int').value = checkEmptyStringValidation(data[5]);
            $('#lotModal').modal('hide');
        });

        $('#lot_stock_no').on('click',function(){
            $('#lotModal').modal('show');
        });
    });

    $(function(){
        var table = $('#tenant_table').DataTable({
            order:[]
        });

        $('#tenant_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('md_tenant_id_int').value = checkEmptyStringValidation(data[0]);
            document.getElementById('md_tenant_name').value = checkEmptyStringValidation(data[1]);
            $('#tenantModal').modal('hide');
        });

        $('#md_tenant_name').on('click',function(){
            $('#tenantModal').modal('show');
        });
    });

    $(function(){
        var table = $('#billing_type_table').DataTable({
            order:[]
        });

        $('#billing_type_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('invoice_type').value = checkEmptyStringValidation(data[0]);
            document.getElementById('invoice_type_desc').value = checkEmptyStringValidation(data[1]);
            document.getElementById('doc_type').value = checkEmptyStringValidation(data[2]);
            $('#billingtypeModal').modal('hide');
        });

        $('#invoice_type_desc').on('click',function(){
            $('#billingtypeModal').modal('show');
        });
    });

    function checkEmptyStringValidation(arr){
        var dataArr;

        if(arr == null){
            return dataArr = "";
        }else{
            return dataArr = arr;
        }
    }
</script>
<style>
    @media screen and (min-width: 676px) {
        .modal-dialog {
            max-width: 700px; /* New width for default modal */
        }
    }
</style>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('creditnotes.savecreditnotes') }}">
                        @csrf
                        <fieldset>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Lot</label>
                                        <input type="text" name="LOT_STOCK_NO" class="form-control" id="lot_stock_no" placeholder="Lot" readonly="yes">
                                        <input type="hidden" name="LOT_STOCK_ID_INT" class="form-control" id="lot_stock_id" readonly="yes">
                                        <input type="hidden" name="PSM_TRANS_NOCHAR" class="form-control" id="psm_trans_nochar" readonly="yes">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Tenant*</label>
                                        <input type="text" name="MD_TENANT_NAME_CHAR" class="form-control" id="md_tenant_name" placeholder="Tenant" readonly="yes">
                                        <input type="hidden" name="MD_TENANT_ID_INT" class="form-control" id="md_tenant_id_int" readonly="yes">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>SQM</label>
                                        <input type="text" name="LOT_STOCK_SQM" class="form-control" id="lot_stock_sqm" placeholder="SQM" readonly="yes">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label>Invoice Type*</label>
                                        <input type="text" name="INVOICE_TRANS_TYPE_DESC" id="invoice_type_desc" class="form-control" placeholder="Invoice Type" readonly>
                                        <input type="hidden" name="INVOICE_TRANS_TYPE" id="invoice_type" class="form-control" readonly>
                                        <input type="hidden" name="DOC_TYPE" id="doc_type" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>Transaction Date*</label>
                                        <input type="date" value="" class="form-control" name="CN_TRANS_TRX_DATE" placeholder="Transaction Date">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Amount*</label>
                                        <input type="text" name="CN_TRANS_AMOUNT" value="0" class="form-control" id="amount" placeholder="0" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Description*</label>
                                        <input type="text" name="CN_TRANS_DESC" class="form-control" id="inv_desc" placeholder="Description" maxlength="100">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <a href="#confModal" class="btn btn-primary pull-right" data-toggle="modal" style="float: right;">
                                            Save Data
                                        </a>
                                        <div id="confModal" class="modal fade">
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
                                                        <button type="button" class="btn btn-info" data-dismiss="modal">Cancel</button>
                                                        <input type="submit" name="submit" value="Save Data" class="btn btn-primary">
                                                    </div>
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

<div class="col-md-3 col-sm-offset-10">
    <div id="lotModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Lot</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact" id="lot_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Lease Doc.</th>
                            <th>Lot</th>
                            <th>Tenant</th>
                            <th>SQM</th>
                            <th>ID Tenant</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($dataLot as $data)
                            <tr>
                                <td>{{$data->LOT_STOCK_ID_INT}}</td>
                                <td>{{$data->PSM_TRANS_NOCHAR}}</td>
                                <td>{{$data->LOT_STOCK_NO}}</td>
                                <td>{{$data->MD_TENANT_NAME_CHAR}}</td>
                                <td style="text-align: right;">{{number_format($data->LOT_STOCK_SQM,0,'','.')}}</td>
                                <td>{{$data->MD_TENANT_ID_INT}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3 col-sm-offset-10">
    <div id="tenantModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tenant</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact" id="tenant_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tenant</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($tenant as $data1)
                            <tr>
                                <td>{{$data1->MD_TENANT_ID_INT}}</td>
                                <td>{{$data1->MD_TENANT_NAME_CHAR}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3 col-sm-offset-10">
    <div id="billingtypeModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Billing Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact" id="billing_type_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Billing Type</th>
                            <th>Description</th>
                            <th>Doc. Type</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($billingType as $data)
                            <tr>
                                <td>{{$data->INVOICE_TRANS_TYPE}}</td>
                                <td>{{$data->INVOICE_TRANS_TYPE_DESC}}</td>
                                <td>B</td>
                            </tr>
                        @endforeach
                        @foreach($secureDepType as $data)
                            <tr>
                                <td>{{$data->PSM_SECURE_DEP_TYPE_CODE}}</td>
                                <td>{{$data->PSM_SECURE_DEP_TYPE_DESC}}</td>
                                <td>D</td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


