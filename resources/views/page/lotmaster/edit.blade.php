@extends('layouts.mainLayouts')

@section('navbar_header')
    Form View / Edit Lot - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form View / Edit Lot
@endsection

@section('content')
<script>
    $(document).ready(function()
    {
        $('#lot_detail_table').DataTable({
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });
    } );

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

    $(function(){
        var table = $('#price_type_table').DataTable({
            order:[]
        });

        $('#price_type_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('price_type').value = checkEmptyStringValidation(data[1]);
            $('#priceTypeModal').modal('hide');
        });

        $('#price_type').on('click',function(){
            $('#priceTypeModal').modal('show');
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

<script type="text/javascript">
    function delItemLotPrice(id){
        $.ajax({
            type: "post",
            url: "{{ route('lot.lotmaster.deleteitemlotprice') }}",
            data: {LOT_STOCK_DTL_ID_INT:id, _token: "{{ csrf_token() }}"},
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

    function getItemLotPrice(id){
        $.ajax({
            type: "post",
            url: "{{ route('lot.lotmaster.getitemLotPrice') }}",
            data: {LOT_STOCK_DTL_ID_INT:id, _token: "{{ csrf_token() }}"},
            dataType: 'json',
            cache: false,
            beforeSend: function(){ $('#loading').modal('show'); },
            success: function( data ) {
                if(data['status'] == 'success'){
                    $("#lot_dtl_id_int").val(data['LOT_STOCK_DTL_ID_INT']);
                    $("#price_type").val(data['LOT_STOCK_TYPE']);
                    $("#price_num").val(data['LOT_STOCK_PRICE_NUM']);
                    $("#insert_id").val('0');
                }else{
                    alert(data['msg']);
                }
                $('#loading').modal('hide');
            }
        });
    };

    $(function(){
        $('#updatePrice').on('click',function(){
            var lot_dtl_id_int = $("#lot_dtl_id_int").val();
            var lot_stock_id_int = $("#lot_stock_id_int").val();
            var price_num = $("#price_num").val();
            var price_type = $("#price_type").val();
            var insert_id = $("#insert_id").val();

            if (price_type === '' || price_num === '')
            {
                alert('Input Failed, Enter All Data Correctly');
                return false;
            }
            else
            {
                $.ajax({
                    type: "post",
                    url: "{{ route('lot.lotmaster.insertupdatelotprice') }}",
                    data: {LOT_STOCK_DTL_ID_INT:lot_dtl_id_int,
                        LOT_STOCK_ID_INT:lot_stock_id_int,
                        LOT_STOCK_PRICE_NUM:price_num,
                        LOT_STOCK_TYPE:price_type,
                        insert_id:insert_id,
                        _token: "{{ csrf_token() }}"},
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
                    <form role="form" method="POST" action="{{ route('lot.lotmaster.saveeditdatalot') }}">
                        @csrf
                        <div class="row" style="padding-left: 5px;">
                            @if($dataLot->ON_RENT_STAT_INT == 1)
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Lot Number*</label>
                                    <input type="text" name="LOT_STOCK_NO" class="form-control" placeholder="Lot Number" id="" readonly="yes" value="<?php echo $dataLot->LOT_STOCK_NO ?>">
                                    <input type="hidden" name="LOT_STOCK_ID_INT" class="form-control" id="lot_stock_id_int" readonly="yes" value="<?php echo $dataLot->LOT_STOCK_ID_INT ?>">
                                </div>
                            </div>
                            @else
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label>Lot Number*</label>
                                    <input type="text" name="LOT_STOCK_NO" class="form-control" placeholder="Lot Number" value="<?php echo $dataLot->LOT_STOCK_NO; ?>">
                                    <input type="hidden" name="LOT_STOCK_ID_INT" class="form-control" id="lot_stock_id_int" value="<?php echo $dataLot->LOT_STOCK_ID_INT; ?>" readonly>
                                </div>
                            </div>
                            @endif
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Zone*</label>
                                    <input type="text" name="LOT_ZONE_DESC" class="form-control" placeholder="Zone" id="zone_desc" value="<?php echo $dataLotZone->LOT_ZONE_DESC; ?>" readonly="yes">
                                    <input type="hidden" name="LOT_ZONE_ID_INT" class="form-control" placeholder="Zone" id="zone_id" value="<?php echo $dataLotZone->LOT_ZONE_ID_INT; ?>" readonly="yes">
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label>Category*</label>
                                    <input type="text" name="PSM_CATEGORY_NAME" class="form-control" placeholder="Category" id="shop_type" value="<?php echo $dataCategory->PSM_CATEGORY_NAME; ?>" readonly="yes">
                                    <input type="hidden" name="PSM_CATEGORY_ID_INT" class="form-control" placeholder="Shop Type" id="shop_type_id" value="<?php echo $dataCategory->PSM_CATEGORY_ID_INT; ?>" readonly="yes">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Type*</label>
                                    <input type="text" name="LOT_TYPE_DESC" class="form-control" placeholder="Type" id="type_desc" value="<?php echo $dataLotType->LOT_TYPE_DESC; ?>" readonly="yes">
                                    <input type="hidden" name="LOT_TYPE_ID_INT" class="form-control" placeholder="Type" id="type_id" value="<?php echo $dataLotType->LOT_TYPE_ID_INT; ?>" readonly="yes">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Level*</label>
                                    <input type="text" name="LOT_LEVEL_DESC" class="form-control" placeholder="Level" id="level_desc" value="<?php echo $dataLotLevel->LOT_LEVEL_DESC; ?>" readonly="yes">
                                    <input type="hidden" name="LOT_LEVEL_ID_INT" class="form-control" placeholder="Level" id="level_id" value="<?php echo $dataLotLevel->LOT_LEVEL_ID_INT; ?>" readonly="yes">
                                </div>
                            </div>
                            @if($dataLot->ON_RENT_STAT_INT == 1)
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>UOM*</label>
                                    <input type="text" name="UNIT_NAME" class="form-control" id="UNIT_NAME" value="<?php echo $dataUom->UNIT_NAME; ?>" placeholder="UOM" readonly>
                                    <input type="hidden" name="id_unit" class="form-control" id="id_unit" value="<?php echo $dataUom->id_unit; ?>" placeholder="UOM" readonly>
                                </div>
                            </div>
                            @else
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>UOM*</label>
                                    <input type="text" name="UNIT_NAME" value="<?php echo $dataUom->UNIT_NAME; ?>" class="form-control" placeholder="UOM" id="uom_desc" readonly="yes">
                                    <input type="hidden" name="id_unit" value="<?php echo $dataUom->id_unit; ?>" class="form-control" placeholder="UOM" id="uom_id" readonly="yes">
                                </div>
                            </div>
                            @endif
                            @if($dataLot->ON_RENT_STAT_INT == 1)
                                <div class="col-lg-2">
                                    <div class="form-group">
                                        <label>Sqm Original*</label>
                                        <input type="number" name="LOT_STOCK_SQMR" class="form-control" placeholder="Sqm" id="sqm" value="<?php echo number_format($dataLot->LOT_STOCK_SQMR,0,'','') ?>" readonly step="any">
                                    </div>
                                </div>
                            @else
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Sqm Original*</label>
                                    <input type="number" name="LOT_STOCK_SQMR" class="form-control" placeholder="Sqm" id="sqm" value="<?php echo number_format($dataLot->LOT_STOCK_SQMR,0,'','') ?>" step="any">
                                </div>
                            </div>
                            @endif
                            @if($dataLot->ON_RENT_STAT_INT == 1)
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Sqm Rent.</label>
                                    <input type="number" name="LOT_STOCK_SQM" class="form-control" placeholder="Sqm" id="sqm" value="<?php echo number_format($dataLot->LOT_STOCK_SQM,0,'',''); ?>" readonly step="any">
                                </div>
                            </div>
                            @else
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Sqm Rent.</label>
                                    <input type="number" name="LOT_STOCK_SQM" class="form-control" placeholder="Sqm" id="sqm" value="<?php echo number_format($dataLot->LOT_STOCK_SQM,0,'',''); ?>" step="any">
                                </div>
                            </div>
                            @endif
                            @if($dataLot->ON_RENT_STAT_INT == 1)
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Sqm Service Charge*</label>
                                    <input type="number" name="LOT_STOCK_SQM_SC" class="form-control" placeholder="Sqm" id="sqm" value="<?php echo number_format($dataLot->LOT_STOCK_SQM_SC,0,'','');?>" readonly="yes" step="any">
                                </div>
                            </div>
                            @else
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <label>Sqm Service Charge*</label>
                                    <input type="number" name="LOT_STOCK_SQM_SC" class="form-control" placeholder="Sqm" id="sqm" value="<?php echo number_format($dataLot->LOT_STOCK_SQM_SC,0,'','');?>" step="any">
                                </div>
                            </div>
                            @endif
                            <div class="col-lg-2">
                                @if($dataLot->ON_RENT_STAT_INT == 1)
                                <div class="form-group">
                                    <label>Release*</label>
                                    <input type="text" name="RELEASE" id="RELEASE" class="form-control" placeholder="Release" value="<?php echo 'RELEASE'; ?>" readonly>
                                    <input type="hidden" name="ON_RELEASE_STAT_INT" id="ON_RELEASE_STAT_INT" class="form-control" placeholder="Release" value="<?php echo $dataLot->ON_RELEASE_STAT_INT; ?>" readonly>
                                </div>
                                @else
                                <div class="form-group">
                                    <label>Release*</label>
                                    <select name="ON_RELEASE_STAT_INT" id="ON_RELEASE_STAT_INT" class="form-control">
                                        <option value="">Please Choose</option>
                                        <option value="0" {{ $dataLot->ON_RELEASE_STAT_INT == "0" ? "selected" : ""}}>UNRELEASE</option>
                                        <option value="1" {{ $dataLot->ON_RELEASE_STAT_INT == "1" ? "selected" : ""}}>RELEASE</option>
                                    </select>
                                </div>
                                @endif
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <a class="btn btn-sm btn-danger" href="{{ URL('/lot/lotmaster/listdatalot/') }}">
                                        <i>
                                            << Back to List
                                        </i>
                                    </a>
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
                                                    <input type="submit" name="submit" value="Save Data" class="btn btn-primary">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br><br>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-lg-12">
                                <h6><b>List Price: </b></h6><br>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Price Type</label>
                                    <input type="text" name="LOT_STOCK_TYPE" class="form-control" id="price_type" placeholder="Price Type" readonly="yes">
                                    <input type="hidden" name="LOT_STOCK_DTL_ID_INT" class="form-control" id="lot_dtl_id_int" readonly="yes">
                                    <input type="hidden" name="insert_id" value="1" class="form-control" id="insert_id" readonly="yes">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Price Amount</label>
                                    <input type="number" name="LOT_STOCK_PRICE_NUM" id="price_num" class="form-control" placeholder="0">
                                </div>
                                <a href="#" class="btn btn-info" data-toggle="modal" name="buttonSave" id="updatePrice" style="float: right;margin-bottom: 10px;">
                                    Insert/Update
                                </a>
                            </div>
                            <div class="col-lg-12">
                                <table class="table-striped table-hover compact" id="lot_detail_table" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Lot</th>
                                        <th>Type</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $no = 1; ?>
                                    @foreach($dataLotDetail as $data)
                                        <tr>
                                            <td>{{$no}}</td>
                                            <td>{{$data->LOT_STOCK_NO}}</td>
                                            <td>{{$data->LOT_STOCK_TYPE}}</td>
                                            <td style="text-align: right;">{{ number_format($data->LOT_STOCK_PRICE_NUM,0,'','.')}}</td>
                                            <td style="text-align:center;">
                                                <i class='fa fa-edit' title='Edit Data' onclick='getItemLotPrice(<?php echo $data->LOT_STOCK_DTL_ID_INT; ?>);'></i>|
                                                <i class='fa fa-trash' title='Delete Data' onclick='delItemLotPrice(<?php echo $data->LOT_STOCK_DTL_ID_INT; ?>);'></i>
                                            </td>
                                        </tr>
                                        <?php $no++; ?>
                                    @endforeach
                                    </tbody>
                                </table>
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
                        @foreach($listCategory as $data)
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

<div class="col-md-3 col-sm-offset-10">
    <div id="priceTypeModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Price Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact" id="price_type_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Type</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Rental</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Service Charge</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

