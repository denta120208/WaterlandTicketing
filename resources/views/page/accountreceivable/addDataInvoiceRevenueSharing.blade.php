@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Add Invoice Revenue Sharing - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Add Invoice Revenue Sharing
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
            $('#lotModal').modal('hide');
        });

        $('#lot_stock_no').on('click',function(){
            $('#lotModal').modal('show');
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
                    <form method="POST" action="{{ route('invoice.saveinvoicerevenuesharing') }}">
                        @csrf
                        <fieldset>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Lot*</label>
                                        <input type="text" name="LOT_STOCK_NO" id="lot_stock_no" class="form-control" placeholder="Lot" readonly>
                                        <input type="hidden" name="LOT_STOCK_ID_INT" id="lot_stock_id" class="form-control" readonly>
                                        <input type="hidden" name="PSM_TRANS_NOCHAR" id="psm_trans_nochar" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Tenant*</label>
                                        <input type="text" name="MD_TENANT_NAME_CHAR" id="md_tenant_name" class="form-control" placeholder="Tenant" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>SQM*</label>
                                        <input type="text" name="LOT_STOCK_SQM" id="lot_stock_sqm" class="form-control" placeholder="SQM" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>Transaction Date*</label>
                                        <input type="date" value="" class="form-control" name="TGL_SCHEDULE_DATE" placeholder="Transaction Date">
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>Due Date*</label>
                                        <input type="date" value="" class="form-control" name="TGL_SCHEDULE_DUE_DATE" placeholder="Due Date">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Invoice Type*</label>
                                        <input type="text" name="INVOICE_TRANS_TYPE_DESC" id="invoice_type_desc" class="form-control" placeholder="Invoice Type" readonly="yes">
                                        <input type="hidden" name="INVOICE_TRANS_TYPE" id="invoice_type" class="form-control" readonly="yes">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label>Amount*</label>
                                        <input type="text" name="INVOICE_TRANS_TOTAL" id="amount" class="form-control" placeholder="0" value="0" readonly="yes">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="padding-left: 5px;">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Description*</label>
                                        <input type="text" name="INVOICE_TRANS_DESC_CHAR" class="form-control" id="inv_desc" placeholder="Description" maxlength="100">
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
                                                        <button type="submit" class="btn btn-primary">Save Data</button>
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
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($billingType as $data)
                            <tr>
                                <td>{{$data->INVOICE_TRANS_TYPE}}</td>
                                <td>{{$data->INVOICE_TRANS_TYPE_DESC}}</td>
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


