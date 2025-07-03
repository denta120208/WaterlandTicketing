@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Add Lot - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Add Lot
@endsection

@section('content')
<script>
    $(function(){
        var table = $('#zone_table').DataTable({
            order:[]
        });

        $('#zone_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('zone_desc').value = checkEmptyStringValidation(data[0]);
            document.getElementById('zone_id').value = checkEmptyStringValidation(data[1]);
            $('#zoneModal').modal('hide');
        });

        $('#zone_desc').on('click',function(){
            $('#zoneModal').modal('show');
        });
    });

    $(function(){
        var table = $('#type_table').DataTable({
            order:[]
        });

        $('#type_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('type_desc').value = checkEmptyStringValidation(data[0]);
            document.getElementById('type_id').value = checkEmptyStringValidation(data[1]);
            $('#typeModal').modal('hide');
        });

        $('#type_desc').on('click',function(){
            $('#typeModal').modal('show');
        });
    });

    $(function(){
        var table = $('#level_table').DataTable({
            order:[]
        });

        $('#level_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('level_desc').value = checkEmptyStringValidation(data[0]);
            document.getElementById('level_id').value = checkEmptyStringValidation(data[1]);
            $('#levelModal').modal('hide');
        });

        $('#level_desc').on('click',function(){
            $('#levelModal').modal('show');
        });
    });

    $(function(){
        var table = $('#uom_table').DataTable({
            order:[]
        });

        $('#uom_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('uom_desc').value = checkEmptyStringValidation(data[0]);
            document.getElementById('uom_id').value = checkEmptyStringValidation(data[2]);
            $('#uomModal').modal('hide');
        });

        $('#uom_desc').on('click',function(){
            $('#uomModal').modal('show');
        });
    });

    $(function(){
        var table = $('#shop_type_table').DataTable({
            order:[]
        });

        $('#shop_type_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('shop_type').value = checkEmptyStringValidation(data[0]);
            document.getElementById('shop_type_id').value = checkEmptyStringValidation(data[1]);
            $('#shopTypeModal').modal('hide');
        });

        $('#shop_type').on('click',function(){
            $('#shopTypeModal').modal('show');
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

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form role="form" method="POST" action="{{ route('lot.lotmaster.savedatalot') }}">
                        @csrf
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Lot Number*</label>
                                    <input type="text" name="LOT_STOCK_NO" class="form-control" placeholder="Lot Number">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Zone*</label>
                                    <input type="text" name="LOT_ZONE_DESC" class="form-control" placeholder="Zone" id="zone_desc" readonly>
                                    <input type="hidden" name="LOT_ZONE_ID_INT" class="form-control" id="zone_id" readonly>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Category*</label>
                                    <input type="text" name="PSM_CATEGORY_NAME" class="form-control" id="shop_type" name="PSM_CATEGORY_NAME" placeholder="Catgory" readonly="yes">
                                    <input type="hidden" name="PSM_CATEGORY_ID_INT" class="form-control" id="shop_type_id" name="PSM_CATEGORY_ID_INT" value="0" placeholder="Shop Type" readonly="yes">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Type*</label>
                                    <input type="text" name="LOT_TYPE_DESC" id="type_desc" class="form-control" placeholder="Type" readonly>
                                    <input type="hidden" name="LOT_TYPE_ID_INT" id="type_id" class="form-control" placeholder="Type" readonly>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Level*</label>
                                    <input type="text" name="LOT_LEVEL_DESC" id="level_desc" class="form-control" placeholder="Level" readonly>
                                    <input type="hidden" name="LOT_LEVEL_ID_INT" id="level_id" class="form-control" placeholder="Level" readonly>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>UOM*</label>
                                    <input type="text" name="UNIT_NAME" class="form-control" placeholder="UOM" id="uom_desc" readonly>
                                    <input type="hidden" name="id_unit" class="form-control" placeholder="UOM" id="uom_id" readonly>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Sqm Original*</label>
                                    <input type="number" name="LOT_STOCK_SQMR" value="0" class="form-control" placeholder="Sqm Original" id="sqm_ori" step="any">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Sqm Rent.*</label>
                                    <input type="number" name="LOT_STOCK_SQM" value="0" class="form-control" placeholder="Sqm Rent." id="sqm_rent" step="any">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Sqm Service Charge*</label>
                                    <input type="number" name="LOT_STOCK_SQM_SC" class="form-control" placeholder="Sqm Service Charge" id="sqm_sc" value="0" step="any">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Release*</label>
                                    <select class="form-control" name="ON_RELEASE_STAT_INT" id="">
                                        <option value="">Please Choose</option>
                                        <option value="0">UNRELEASE</option>
                                        <option value="1">RELEASE</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <a href="#confModal" class="btn btn-primary" data-toggle="modal" style="float: right;">
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
                                                    <input type="submit" value="Save Data" class="btn btn-primary">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3 col-sm-offset-10">
    <div id="zoneModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Zone</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact " id="zone_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Zone</th>
                            <th>ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($listLotZone as $zone)
                            <tr>
                                <td>{{$zone->LOT_ZONE_DESC}}</td>
                                <td>{{$zone->LOT_ZONE_ID_INT}}</td>
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
    <div id="typeModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact " id="type_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Type</th>
                            <th>ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($listLotType as $type)
                            <tr>
                                <td>{{$type->LOT_TYPE_DESC}}</td>
                                <td>{{$type->LOT_TYPE_ID_INT}}</td>
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
    <div id="levelModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Level</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact " id="level_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Level</th>
                            <th>ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($listLotLevel as $level)
                            <tr>
                                <td>{{$level->LOT_LEVEL_DESC}}</td>
                                <td>{{$level->LOT_LEVEL_ID_INT}}</td>
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
    <div id="uomModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">UOM</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact " id="uom_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>UOM</th>
                            <th>Code</th>
                            <th>ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($listUom as $uom)
                            <tr>
                                <td>{{$uom->UNIT_NAME}}</td>
                                <td>{{$uom->UNIT_CODE}}</td>
                                <td>{{$uom->id_unit}}</td>
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
    <div id="shopTypeModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Category</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact" id="shop_type_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Category</th>
                            <th>ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($dataCategory as $data)
                            <tr>
                                <td>{{$data->PSM_CATEGORY_NAME}}</td>
                                <td>{{$data->PSM_CATEGORY_ID_INT}}</td>
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

