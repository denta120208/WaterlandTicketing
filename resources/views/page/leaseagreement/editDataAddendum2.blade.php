@extends('layouts.mainLayouts')

@section('navbar_header')
    @if($dataAddendum->PSM_ADD_DOC_TYPE == 'RVS')
        Form Edit Data Letter Of Intent Addendum Revision Agreement - <b>{{session('current_project_char')}}</b>
    @else
        Form Edit Data Letter Of Intent Addendum Renewal Agreement - <b>{{session('current_project_char')}}</b>
    @endif
@endsection

@section('header_title')
    @if($dataAddendum->PSM_ADD_DOC_TYPE == 'RVS')
        Form Edit Data Letter Of Intent Addendum Revision Agreement
    @else
        Form Edit Data Letter Of Intent Addendum Renewal Agreement
    @endif
@endsection

@section('content')

@if ($errors->any())
    <ul class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

@if (Session::has('message'))
    <div class="alert alert-success" id="success-alert">
        {{ Session::get('message') }}
    </div>
@endif
<script src="https://cdn.ckeditor.com/4.10.0/standard/ckeditor.js"></script>
<script>
    $(document).ready(function()
    {
        $('#vendor_table').DataTable({
            order : [],
            scrollY:"700px",
            scrollCollapse: true,
            paging: false,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    footer: true,
                    title: '<?php echo "List Schedule ".$tenantData->MD_TENANT_NAME_CHAR." Lot ".empty($lotData->LOT_STOCK_NO) ? "-" : $lotData->LOT_STOCK_NO ?>'
                },
                {
                    extend: 'pdfHtml5',
                    footer: true,
                    title: '<?php echo "List Schedule ".$tenantData->MD_TENANT_NAME_CHAR." Lot ".empty($lotData->LOT_STOCK_NO) ? "-" : $lotData->LOT_STOCK_NO ?>'
                }
            ]
        });
    });
</script>
<script>
    $(document).ready(function(){
        $('#LOT_STOCK_NO').select2();
    });

    $(document).ready(function()
    {
        var table = $('#tenant_table').DataTable({
            order:[]
        });

        $('#tenant_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('tenant_name').value = checkEmptyStringValidation(data[0]);
            document.getElementById('tenant_id').value = checkEmptyStringValidation(data[1]);
            $('#tenantModal').modal('hide');
        });
        
        $('#tenant_name').on('click',function(){
            $('#tenantModal').modal('show');
        });

        $('#commission_table').DataTable({
            order : [],
            pageLength : 25,
            scrollX: true
        });

        $('#data_rentsclot_table').DataTable({
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });

        $('#data_rentscamt_table').DataTable({
            order : [],
            scrollY:"500px",
            scrollCollapse: true,
            paging: false
        });

        $('#saveLot').on('click',function() {
            var psm_trans_add_id_int = "<?php echo $dataAddendum->PSM_TRANS_ADD_ID_INT; ?>";
            var psm_trans_add_nochar = "<?php echo $dataAddendum->PSM_TRANS_ADD_NOCHAR; ?>";
            var psm_trans_nochar = "<?php echo $dataAddendum->PSM_TRANS_NOCHAR; ?>";
            var lot = $("#LOT_STOCK_NO").val().toString();

            if(lot == '') {
                alert('Input Failed, Select Lot Number Correctly');
                return false;
            }
            else {
                $.ajax({
                    type: "post",
                    url: "{{ route('marketing.leaseagreement.insertrentsclotadd') }}",
                    data: {
                        PSM_TRANS_ADD_ID_INT:psm_trans_add_id_int,
                        PSM_TRANS_ADD_NOCHAR:psm_trans_add_nochar,
                        PSM_TRANS_NOCHAR:psm_trans_nochar,
                        LOT_STOCK_ID_INT:lot,
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    cache: false,
                    beforeSend: function() {
                        $('#loading').modal('show');
                    },
                    success: function (response) {
                        if(response['Success']) {
                            document.location.reload(true);
                        } else {
                            alert(response['Error']);
                        }
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
        });

        $('#saveRentScAmt').on('click',function() {
            var tableArr = $('#data_rentscamt_table').DataTable().rows().data().toArray();
            var tableArrLength = $('#data_rentscamt_table').DataTable().rows().data().toArray().length;
            var isExists = false;
            for(i = 0; i < tableArrLength; i++) {
                if(parseInt(tableArr[i][1]) == parseInt($("#year_int").val())) {
                    isExists = true;
                    break;
                }
            }

            if(isExists) {
                alert('Input Failed, Tahun sudah ada. Silahkan input tahun yang lain!');
            }
            else {
                var psm_trans_add_id_int = "<?php echo $dataAddendum->PSM_TRANS_ADD_ID_INT; ?>";
                var psm_trans_add_nochar = "<?php echo $dataAddendum->PSM_TRANS_ADD_NOCHAR; ?>";
                var psm_trans_nochar = "<?php echo $dataAddendum->PSM_TRANS_NOCHAR; ?>";
                var year_int = parseInt($("#year_int").val());
                var rent_amount = parseFloat($("#rent_amount").val());
                var sc_amount = parseFloat($("#sc_amount").val());
                
                var start_date = new Date("<?php echo $dataAddendum->PSM_TRANS_START_DATE; ?>");
                var end_date = new Date("<?php echo $dataAddendum->PSM_TRANS_END_DATE; ?>");

                if(isNaN(year_int) || isNaN(rent_amount) || isNaN(sc_amount)) {
                    alert('Input Failed, Enter All Data Correctly');
                    return false;
                }
                else if(year_int < parseInt(start_date.getFullYear()) || year_int > parseInt(end_date.getFullYear())) {
                    alert('Input Failed, Year tidak bisa lewat dari Start Date & End Date');
                    return false;
                }
                else {
                    $.ajax({
                        type: "post",
                        url: "{{ route('marketing.leaseagreement.insertrentscamtadd') }}",
                        data: {
                            PSM_TRANS_ADD_ID_INT:psm_trans_add_id_int,
                            PSM_TRANS_ADD_NOCHAR:psm_trans_add_nochar,
                            PSM_TRANS_NOCHAR:psm_trans_nochar,
                            PSM_TRANS_PRICE_YEAR:year_int,
                            PSM_TRANS_PRICE_RENT_NUM:rent_amount,
                            PSM_TRANS_PRICE_SC_NUM:sc_amount,
                            PSM_TRANS_START_DATE:"<?php echo $dataAddendum->PSM_TRANS_START_DATE; ?>",
                            PSM_TRANS_END_DATE:"<?php echo $dataAddendum->PSM_TRANS_END_DATE; ?>",
                            _token: "{{ csrf_token() }}"
                        },
                        dataType: 'json',
                        cache: false,
                        beforeSend: function() {
                            $('#loading').modal('show');
                        },
                        success: function (response) {
                            if(response['Success']) {
                                document.location.reload(true);
                            } else {
                                alert(response['Error']);
                            }
                        },
                        error: function() {
                            alert('Error, Please contact Administrator!');
                        }
                    });
                }
            }
        });

        $('#saveDisc').on('click',function() {
            var psm_trans_add_id_int = "<?php echo $dataAddendum->PSM_TRANS_ADD_ID_INT; ?>";
            var psm_trans_add_nochar = "<?php echo $dataAddendum->PSM_TRANS_ADD_NOCHAR; ?>";
            var psm_trans_nochar = "<?php echo $dataAddendum->PSM_TRANS_NOCHAR; ?>";
            var disc_persen = parseFloat($("#disc_persen").val());
            var disc_amt = parseFloat($("#disc_amt").val());

            if (isNaN(disc_persen) || isNaN(disc_amt)) {
                alert('Input Failed, Enter All Data Correctly');
                return false;
            }
            else {
                $.ajax({
                    type: "post",
                    url: "{{ route('marketing.leaseagreement.insertrentscdiscadd') }}",
                    data: {
                        PSM_TRANS_DISKON_PERSEN:disc_persen,
                        PSM_TRANS_DISKON_NUM:disc_amt,
                        PSM_TRANS_ADD_ID_INT:psm_trans_add_id_int,
                        PSM_TRANS_ADD_NOCHAR:psm_trans_add_nochar,
                        PSM_TRANS_NOCHAR:psm_trans_nochar,
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    cache: false,
                    beforeSend: function() {
                        $('#loading').modal('show');
                    },
                    success: function (response) {
                        if(response['Success']) {
                            document.location.reload(true);
                        } else {
                            alert(response['Error']);
                        }
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
        });

        $('#disc_persen').on('keyup',function() {
            var disc_persen = parseFloat($("#disc_persen").val()) / 100;
            var net_before_tax_real = parseFloat($("#net_before_tax_real").val());
            var disc_amt = net_before_tax_real * disc_persen;
            $("#disc_amt").val(disc_amt);
        });

        $('#disc_amt').on('keyup',function() {
            var disc_amt = parseFloat($("#disc_amt").val());
            var net_before_tax_real = parseFloat($("#net_before_tax_real").val());
            var disc_persen = (disc_amt / net_before_tax_real) * 100;
            $("#disc_persen").val(disc_persen);
        });
    });

    function delItemRentSCAmt(id){
        $.ajax({
            type: "post",
            url: "{{ route('marketing.leaseagreement.deleteitemrentscamtadd') }}",
            data: {
                PSM_TRANS_ADD_PRICE_ID_INT:id,
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            cache: false,
            beforeSend: function() {
                $('#loading').modal('show');
            },
            success: function (response) {
                if(response['Success']) {
                    document.location.reload(true);
                } else {
                    alert(response['Error']);
                }
                $('#loading').modal('hide');
            },
            error: function() {
                alert('Error, Please contact Administrator!');
            }
        });
    };

    $(document).ready(function()
    {
        $('#schedule_table').DataTable({
            order : []
        });
    } );

    $(function(){
        var table = $('#trx_table').DataTable({
            order:[]
        });

        $('#trx_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('trx_desc').value = checkEmptyStringValidation(data[0]);
            document.getElementById('trx_code').value = checkEmptyStringValidation(data[1]);
            $('#trxModal').modal('hide');
        });

        $('#trx_desc').on('click',function(){
            $('#trxModal').modal('show');
        });
    });

    $(function(){
        var table = $('#sales_type_table').DataTable({
            order:[]
        });

        $('#sales_type_table tbody').on('click', 'tr', function ()
        {
            var data = table.row( this ).data();
            document.getElementById('sales_desc').value = checkEmptyStringValidation(data[0]);
            document.getElementById('sales_id').value = checkEmptyStringValidation(data[1]);
            $('#salesModal').modal('hide');
        });

        $('#sales_desc').on('click',function(){
            $('#salesModal').modal('show');
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

    $(function()
    {
        $('#price_rent_meter').on('change',function(){
            var price_rent_meter = parseFloat($("#price_rent_meter").val());
            var disk_amount = parseFloat($("#disk_amount").val());
            var sqm_rent = parseFloat($("#sqm_rent").val());

            var total_price_rent = (price_rent_meter * sqm_rent);

            var net_before_tax_real = parseInt(total_price_rent);

            var dpp = net_before_tax_real - disk_amount;
            var ppn = dpp * 0.1;
            var total = dpp + ppn;

            document.getElementById("net_before_tax").value = dpp.formatMoney(0);
            document.getElementById("net_before_tax_real").value = net_before_tax_real.formatMoney(0);

            document.getElementById("price_tax").value = ppn.formatMoney(0);
            document.getElementById("price_total").value = total.formatMoney(0);
        });
    });

    $(function()
    {
        $('#disk_persen').on('change',function(){
            var disk_persen = parseInt($("#disk_persen").val());
            var net_before_tax_real = parseInt($("#net_before_tax_real").val());

            var disk_amount = (disk_persen / 100) * net_before_tax_real;

            var dpp = net_before_tax_real - disk_amount;
            var ppn = dpp * 0.1;
            var total = dpp + ppn;

            document.getElementById("disk_amount").value = disk_amount;

            document.getElementById("net_before_tax").value = dpp.formatMoney(0);

            document.getElementById("price_tax").value = ppn.formatMoney(0);

            document.getElementById("price_total").value = total.formatMoney(0);
        });
    });

    $(function()
    {
        $('#disk_amount').on('change',function(){
            var disk_amount = parseInt($("#disk_amount").val());

            var net_before_tax_real = parseInt($("#net_before_tax_real").val());

            var disk_persen = (disk_amount/net_before_tax_real) * 100;

            var dpp = net_before_tax_real - disk_amount;
            var ppn = dpp * 0.1;
            var total = dpp + ppn;

            document.getElementById("disk_persen").value = disk_persen.formatMoney(2);

            document.getElementById("net_before_tax").value = dpp.formatMoney(0);

            document.getElementById("price_tax").value = ppn.formatMoney(0);

            document.getElementById("price_total").value = total.formatMoney(0);
        });
    });

    Number.prototype.formatMoney = function (c, d, t)
    {
        var n = this,
            c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d == undefined ? "." : d,
            t = t == undefined ? "." : t,
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
        return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
    };
</script>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <fieldset>
                        <div class="row">
                            <div class="col-12">
                                <div class="card card-info card-tabs" >
                                    <div class="card-header p-0 pt-1">
                                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill"
                                                href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                                                aria-selected="true">Data Header Transaction</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="tab-content" id="custom-tabs-one-tabContent" style="padding-left: 5px; padding-right: 5px;">
                                        {{-- General --}}
                                        <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel" aria-labelledby="custom-tabs-one-home-tab">
                                            <form action="{{ URL::route('marketing.leaseagreement.editdataaddendum') }}" method="post">
                                            @csrf
                                            <br>
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <label>Addendum Document*</label>
                                                    <input type="text" name="PSM_TRANS_ADD_NOCHAR" class="form-control" id="psm_trans_nochar" value="<?php echo $dataAddendum->PSM_TRANS_ADD_NOCHAR; ?>" readonly="yes">
                                                </div>
                                                <div class="col-lg-4">
                                                    <label>Lease Document*</label>
                                                    <input type="text" name="PSM_TRANS_NOCHAR" class="form-control" id="psm_trans_nochar" value="<?php echo $dataPSM->PSM_TRANS_NOCHAR; ?>" readonly="yes">
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <input type="hidden" name="PSM_TRANS_ID_INT" id="psm_trans_id" value="<?php echo $dataPSM->PSM_TRANS_ID_INT; ?>" class="form-control" readonly>
                                                <input type="hidden" name="ADD_TYPE" value="<?php echo $ADD_TYPE; ?>" class="form-control" readonly>
                                                @if($dataAddendum->LOT_STOCK_NO == NULL)
                                                <div class="col-lg-11">
                                                    <div class="form-group">
                                                        <label>Lot Number*</label>
                                                        <select id="LOT_STOCK_NO" name="LOT_STOCK_NO[]" class="form-control select2" multiple="multiple" data-placeholder="Select Lot" style="width: 100%;">                                        
                                                            @foreach($lotData as $data)
                                                            <option value="{{ $data->LOT_STOCK_ID_INT }}" {{ in_array($data->LOT_STOCK_ID_INT, $lotDataCurr) ? "selected" : "" }}>
                                                                {{ $data->LOT_STOCK_NO }} - SQM RT : {{ (float) $data->LOT_STOCK_SQM }} - SQM SC : {{ (float) $data->LOT_STOCK_SQM_SC }}
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1" style="padding-right: 25px; padding-top: 31px;">
                                                    <div class="form-group">
                                                        <a href="javascript:void(0)" class="btn btn-info" name="btnsavelot" id="saveLot" style="float: right; margin-bottom: 20px;">
                                                            Save
                                                        </a>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-12" style="padding-bottom: 20px;">
                                                    <label>Lot Number</label>
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <table class="table-striped table-hover compact" id="data_rentsclot_table" cellspacing="0" width="100%">
                                                                <thead>
                                                                <tr>
                                                                    <th>No.</th>
                                                                    <th>Lot</th>
                                                                    <th>SQM RT</th>
                                                                    <th>SQM SC</th>
                                                                    <th>Edit SQM RT</th>
                                                                    <th>Edit SQM SC</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php $no = 1; ?>
                                                                @foreach($lotData as $data)
                                                                    <tr>
                                                                        <td style="width: 5px;">{{ $no }}</td>
                                                                        <td>{{ $data->LOT_STOCK_NO }}</td>
                                                                        <td>{{ (float) $data->LOT_STOCK_SQM }}</td>
                                                                        <td>{{ (float) $data->LOT_STOCK_SQM_SC }}</td>
                                                                        <td>
                                                                            <a href="#editSqmRt{!! $data->PSM_TRANS_ADD_LOT_ID_INT !!}" data-toggle="modal">
                                                                                <b>Edit</b>
                                                                            </a>
                                                                            <div id="editSqmRt{!! $data->PSM_TRANS_ADD_LOT_ID_INT !!}" class="modal fade">
                                                                                <div class="modal-dialog">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h4 class="modal-title">Edit SQM RT</h4>
                                                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                                        </div>
                                                                                        </form>
                                                                                        <form method="POST" action="{{route('marketing.leaseagreement.editsqmrtaddendum')}}">
                                                                                            @csrf
                                                                                            <div class="modal-body">
                                                                                                <div class="form-group">
                                                                                                    <label class="col-sm-5">Edit SQM RT *</label>
                                                                                                    <div class="col-sm-12">
                                                                                                        <input type="hidden" class="form-control" name="ADD_TYPE_PSM_RT" id="ADD_TYPE_PSM_RT" style="width: 100%;" value="{{ $ADD_TYPE }}" required />
                                                                                                        <input type="hidden" class="form-control" name="ID_PSM_RT_NOCHAR" id="ID_PSM_RT_NOCHAR" style="width: 100%;" value="{{ $dataAddendum->PSM_TRANS_ADD_NOCHAR }}" required />
                                                                                                        <input type="hidden" class="form-control" name="ID_PSM_RT" id="ID_PSM_RT" style="width: 100%;" value="{{ $dataAddendum->PSM_TRANS_ADD_ID_INT }}" required />
                                                                                                        <input type="hidden" class="form-control" name="SQM_RT_ID" id="SQM_RT_ID" style="width: 100%;" value="{{ $data->PSM_TRANS_ADD_LOT_ID_INT }}" required />
                                                                                                        <input type="number" class="form-control" name="SQM_RT_NUM" id="SQM_RT_NUM" style="width: 100%;" placeholder="Enter SQM RT" required />
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                                <button type="submit" class="btn btn-primary">Edit</button>
                                                                                            </div>
                                                                                        </form>
                                                                                        <form action="{{ URL::route('marketing.leaseagreement.editdataaddendum') }}" method="post">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <a href="#editSqmSc{!! $data->PSM_TRANS_ADD_LOT_ID_INT !!}" data-toggle="modal">
                                                                                <b>Edit</b>
                                                                            </a>
                                                                            <div id="editSqmSc{!! $data->PSM_TRANS_ADD_LOT_ID_INT !!}" class="modal fade">
                                                                                <div class="modal-dialog">
                                                                                    <div class="modal-content">
                                                                                        <div class="modal-header">
                                                                                            <h4 class="modal-title">Edit SQM SC</h4>
                                                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                                        </div>
                                                                                        </form>
                                                                                        <form method="POST" action="{{route('marketing.leaseagreement.editsqmscaddendum')}}">
                                                                                            @csrf
                                                                                            <div class="modal-body">
                                                                                                <div class="form-group">
                                                                                                    <label class="col-sm-5">Edit SQM SC *</label>
                                                                                                    <div class="col-sm-12">
                                                                                                        <input type="hidden" class="form-control" name="ADD_TYPE_PSM_SC" id="ADD_TYPE_PSM_SC" style="width: 100%;" value="{{ $ADD_TYPE }}" required />
                                                                                                        <input type="hidden" class="form-control" name="ID_PSM_SC_NOCHAR" id="ID_PSM_SC_NOCHAR" style="width: 100%;" value="{{ $dataAddendum->PSM_TRANS_ADD_NOCHAR }}" required />
                                                                                                        <input type="hidden" class="form-control" name="ID_PSM_SC" id="ID_PSM_SC" style="width: 100%;" value="{{ $dataAddendum->PSM_TRANS_ADD_ID_INT }}" required />
                                                                                                        <input type="hidden" class="form-control" name="SQM_SC_ID" id="SQM_SC_ID" style="width: 100%;" value="{{ $data->PSM_TRANS_ADD_LOT_ID_INT }}" required />
                                                                                                        <input type="number" class="form-control" name="SQM_SC_NUM" id="SQM_SC_NUM" style="width: 100%;" placeholder="Enter SQM SC" required />
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                                                <button type="submit" class="btn btn-primary">Edit</button>
                                                                                            </div>
                                                                                        </form>
                                                                                        <form action="{{ URL::route('marketing.leaseagreement.editdataaddendum') }}" method="post">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <?php $no += 1; ?>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Tenant*</label>
                                                        <input type="text" name="MD_TENANT_NAME_CHAR" value="<?php echo $tenantData->MD_TENANT_NAME_CHAR; ?>" class="form-control" id="tenant_name" placeholder="Tenant" readonly>
                                                        <input type="hidden" name="MD_TENANT_ID_INT" value="<?php echo $tenantData->MD_TENANT_ID_INT; ?>" class="form-control" id="tenant_id" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Shop Name*</label>
                                                        <input type="text" name="SHOP_NAME_CHAR" value="<?php echo $dataAddendum->SHOP_NAME_CHAR; ?>" class="form-control" placeholder="Shop Name">
                                                    </div>
                                                </div>
                                                @if($dataPSM->PSM_CATEGORY_ID_INT == 0)
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Shop Type*</label>
                                                        <input type="text" name="PSM_CATEGORY_NAME" class="form-control" id="shop_type" placeholder="Shop Type" readonly>
                                                        <input type="hidden" name="PSM_CATEGORY_ID_INT" value="0" class="form-control" id="shop_type_id" placeholder="Shop Type" readonly>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Shop Type*</label>
                                                        <input type="text" name="PSM_CATEGORY_NAME" value="<?php echo $categoryData->PSM_CATEGORY_NAME; ?>" class="form-control" id="shop_type" placeholder="Shop Type" readonly>
                                                        <input type="hidden" name="PSM_CATEGORY_ID_INT" value="<?php echo $categoryData->PSM_CATEGORY_ID_INT; ?>" class="form-control" id="shop_type_id" placeholder="Shop Type" readonly>
                                                    </div>
                                                </div>
                                                @endif
                                                @if($dataAddendum->PSM_ADD_DOC_TYPE == 'RVS')
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Type*</label>
                                                        <input type="text" name="MD_SALES_TYPE_DESC" value="<?php echo $salesTypedata->MD_SALES_TYPE_DESC; ?>" class="form-control" placeholder="Type" readonly>
                                                        <input type="hidden" name="MD_SALES_TYPE_ID_INT" value="<?php echo $salesTypedata->MD_SALES_TYPE_ID_INT; ?>" class="form-control" readonly>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Type*</label>
                                                        <input type="text" name="MD_SALES_TYPE_DESC" value="<?php echo $salesTypedata->MD_SALES_TYPE_DESC; ?>" class="form-control" id="sales_desc" placeholder="Type" readonly>
                                                        <input type="hidden" name="MD_SALES_TYPE_ID_INT" value="<?php echo $salesTypedata->MD_SALES_TYPE_ID_INT; ?>" class="form-control" id="sales_id" readonly>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Booking Date*</label>
                                                        <input type="date" value="{{$dataAddendum->PSM_TRANS_BOOKING_DATE}}" class="form-control" id="startDate" name="PSM_TRANS_BOOKING_DATE" placeholder="Booking Date" readonly="yes">
                                                    </div>
                                                </div>
                                                @if($dataAddendum->PSM_ADD_DOC_TYPE == 'RVS')
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Start Date*</label>
                                                        <input type="date" value="{{$dataAddendum->PSM_TRANS_START_DATE}}" class="form-control" name="PSM_TRANS_START_DATE" placeholder="Start Date" readonly="yes">
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Start Date*</label>
                                                        <input type="date" value="{{$dataAddendum->PSM_TRANS_START_DATE}}" class="form-control" name="PSM_TRANS_START_DATE" placeholder="Start Date" <?php echo count($dataRentSCAmt) > 0 ? "readonly" : "" ?>>
                                                    </div>
                                                </div>
                                                @endif
                                                @if($dataAddendum->PSM_ADD_DOC_TYPE == 'RVS')
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>End Date*</label>
                                                        <input type="date" value="{{$dataAddendum->PSM_TRANS_END_DATE}}" class="form-control" name="PSM_TRANS_END_DATE" placeholder="End Date" readonly="yes">
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>End Date*</label>
                                                        <input type="date" value="{{$dataAddendum->PSM_TRANS_END_DATE}}" class="form-control" name="PSM_TRANS_END_DATE" placeholder="End Date" <?php echo count($dataRentSCAmt) > 0 ? "readonly" : "" ?>>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Virtual Account*</label>
                                                        <input type="number" name="PSM_TRANS_VA" value="<?php echo $dataAddendum->PSM_TRANS_VA ?>" class="form-control" id="PSM_TRANS_VA" placeholder="Virtual Account">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                @if($dataAddendum->PSM_ADD_DOC_TYPE == 'RVS')
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Down Payment(%)*</label>
                                                        <input type="number" name="PSM_TRANS_DP_PERSEN" value="<?php echo number_format($dataAddendum->PSM_TRANS_DP_PERSEN,0); ?>" class="form-control" id="PSM_TRANS_DP_PERSEN" placeholder="Down Payment(%)" readonly="yes">
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Down Payment(%)*</label>
                                                        <input type="number" name="PSM_TRANS_DP_PERSEN" id="down_payment" class="form-control" placeholder="Down Payment(%)" value="<?php echo number_format($dataAddendum->PSM_TRANS_DP_PERSEN,0); ?>" >
                                                    </div>
                                                </div>
                                                @endif
                                                @if($dataAddendum->PSM_ADD_DOC_TYPE == 'RVS')
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Down Payment Period (Month)*</label>
                                                        <input type="number" name="PSM_TRANS_DP_PERIOD" value="<?php echo $dataAddendum->PSM_TRANS_DP_PERIOD; ?>" class="form-control" placeholder="0" readonly="yes">
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Down Payment Period (Month)*</label>
                                                        <input type="number" name="PSM_TRANS_DP_PERIOD" id="PSM_TRANS_DP_PERIOD" class="form-control" placeholder="0" value="<?php echo $dataAddendum->PSM_TRANS_DP_PERIOD; ?>" >
                                                    </div>
                                                </div>
                                                @endif
                                                @if($dataAddendum->PSM_ADD_DOC_TYPE == 'RVS')
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Payment Period (Month)*</label>
                                                        <input type="number" name="PSM_TRANS_TIME_PERIOD_SCHED" value="<?php echo $dataAddendum->PSM_TRANS_TIME_PERIOD_SCHED; ?>" class="form-control" placeholder="Time Period" readonly="yes" id="PSM_TRANS_TIME_PERIOD_SCHED">
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Payment Period (Month)*</label>
                                                        <input type="number" name="PSM_TRANS_TIME_PERIOD_SCHED" id="PSM_TRANS_TIME_PERIOD_SCHED" class="form-control" placeholder="Time Period" value="<?php echo $dataAddendum->PSM_TRANS_TIME_PERIOD_SCHED ?>">
                                                    </div>
                                                </div>
                                                @endif
                                                @if($dataAddendum->PSM_ADD_DOC_TYPE == 'RVS')
                                                <div class="col-lg-12" style="padding-top: 10px; padding-bottom: 25px;">
                                                    <div class="row" style="padding-left: 10px; padding-right: 10px;">
                                                        <div class="col-lg-12">
                                                            <h6><b>Rent / Service Charge Amount: </b></h6><br>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <table class="table-striped table-hover compact" id="data_rentscamt_table" cellspacing="0" width="100%">
                                                                <thead>
                                                                <tr>
                                                                    <th>No.</th>
                                                                    <th>Year</th>
                                                                    <th>Rent Amt (For Month) / m2</th>
                                                                    <th>SC Amt (For Month) / m2</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php $no = 1; ?>
                                                                @foreach($dataRentSCAmt as $data)
                                                                    <tr>
                                                                        <td style="text-align: right; width: 5px;">{{ $no }}</td>
                                                                        <td>{{ $data->PSM_TRANS_PRICE_YEAR }}</td>
                                                                        <td>{{ number_format($data->PSM_TRANS_PRICE_RENT_NUM,0,',','.') }}</td>
                                                                        <td>{{ number_format($data->PSM_TRANS_PRICE_SC_NUM,0,',','.') }}</td>
                                                                    </tr>
                                                                    <?php $no += 1; ?>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-12" style="padding-top: 10px; padding-bottom: 25px;">
                                                    <div class="row" style="padding-left: 10px; padding-right: 10px;">
                                                        <div class="col-lg-12">
                                                            <h6><b>Rent / Service Charge Amount: </b></h6><br>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="form-group">
                                                                <label>Year *</label>
                                                                <input type="number" name="YEAR_INT" value="<?php echo date('Y', strtotime($dataAddendum->PSM_TRANS_START_DATE)); ?>" class="form-control" placeholder="Enter Year" id="year_int">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="form-group">
                                                                <label>Rent Amount (For Month) / m2 *</label>
                                                                <input type="number" name="RENT_AMOUNT" value="0" class="form-control" placeholder="Enter Rent Amount" id="rent_amount">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="form-group">
                                                                <label>Service Charge Amount (For Month) / m2 *</label>
                                                                <input type="number" name="SC_AMOUNT" id="sc_amount" value="0" class="form-control" placeholder="Enter Service Charge Amount">
                                                            </div>
                                                            <a href="javascript:void(0)" class="btn btn-info" name="btnsaverentscamt" id="saveRentScAmt" style="float: right; margin-bottom: 20px;">
                                                                Insert
                                                            </a>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <table class="table-striped table-hover compact" id="data_rentscamt_table" cellspacing="0" width="100%">
                                                                <thead>
                                                                <tr>
                                                                    <th>No.</th>
                                                                    <th>Year</th>
                                                                    <th>Rent Amt (For Month) / m2</th>
                                                                    <th>SC Amt (For Month) / m2</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php $no = 1; ?>
                                                                @foreach($dataRentSCAmt as $data)
                                                                    <tr>
                                                                        <td style="text-align: right; width: 5px;">{{ $no }}</td>
                                                                        <td>{{ $data->PSM_TRANS_PRICE_YEAR }}</td>
                                                                        <td>{{ number_format($data->PSM_TRANS_PRICE_RENT_NUM,0,',','.') }}</td>
                                                                        <td>{{ number_format($data->PSM_TRANS_PRICE_SC_NUM,0,',','.') }}</td>
                                                                        <td style="text-align: center;">
                                                                            <i class='fa fa-trash' title='Delete Data' onclick='delItemRentSCAmt(<?php echo $data->PSM_TRANS_ADD_PRICE_ID_INT; ?>);'></i>
                                                                        </td>
                                                                    </tr>
                                                                    <?php $no += 1; ?>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                @if($dataAddendum->PSM_ADD_DOC_TYPE == 'RVS')
                                                <div class="col-lg-12" style="padding-top: 10px; padding-bottom: 25px;">
                                                    <div class="row" style="padding-left: 10px; padding-right: 10px;">
                                                        <div class="col-lg-5">
                                                            <div class="form-group">
                                                                <label>Disc (%) *</label>
                                                                <input type="number" name="DISC_PERSEN" id="disc_persen" class="form-control" placeholder="Enter Disc (%)" value="<?php echo (float) $dataAddendum->PSM_TRANS_DISKON_PERSEN; ?>" readonly="true">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-5">
                                                            <div class="form-group">
                                                                <label>Disc Amount *</label>
                                                                <input type="number" name="DISC_AMT" id="disc_amt" class="form-control" placeholder="Enter Disc Amount" value="<?php echo (float) $dataAddendum->PSM_TRANS_DISKON_NUM; ?>" readonly="true">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-12" style="padding-top: 10px; padding-bottom: 25px;">
                                                    <div class="row" style="padding-left: 10px; padding-right: 10px;">
                                                        <div class="col-lg-5">
                                                            <div class="form-group">
                                                                <label>Disc (%) *</label>
                                                                <input type="number" name="DISC_PERSEN" value="<?php echo (float) $dataAddendum->PSM_TRANS_DISKON_PERSEN ?>" class="form-control" placeholder="Enter Disc (%)" id="disc_persen">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-5">
                                                            <div class="form-group">
                                                                <label>Disc Amount *</label>
                                                                <input type="number" name="DISC_AMT" value="<?php echo (float) $dataAddendum->PSM_TRANS_DISKON_NUM ?>" class="form-control" placeholder="Enter Disc Amount" id="disc_amt">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-1" style="padding-right: 25px; padding-top: 31px;">
                                                            <div class="form-group">
                                                                <a href="javascript:void(0)" class="btn btn-info" name="btnsavedisc" id="saveDisc" style="float: right; margin-bottom: 20px;">
                                                                    Save
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                @if($dataAddendum->PSM_ADD_DOC_TYPE == 'RVS')
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Generate Schedule*</label>
                                                        <select name="PSM_TRANS_GENERATE_BILLING" class="form-control" disabled="yes">
                                                            <option value="1" <?php if($dataPSM->PSM_TRANS_GENERATE_BILLING==1) echo "selected";?>>Automatically</option>
                                                            <option value="0" <?php if($dataPSM->PSM_TRANS_GENERATE_BILLING==0) echo "selected";?>>Manual</option>
                                                        </select>
                                                        <input type="hidden" name="PSM_TRANS_GENERATE_BILLING" value="<?php echo $dataAddendum->PSM_TRANS_GENERATE_BILLING;?>" class="form-control" readonly="yes">
                                                    </div>
                                                </div>
                                                @else
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Generate Schedule*</label>
                                                        <select name="PSM_TRANS_GENERATE_BILLING" class="form-control">
                                                            <option value="1" <?php echo $dataPSM->PSM_TRANS_GENERATE_BILLING == '1' ? 'selected' : '' ?>>Automatically</option>
                                                            <option value="0" <?php echo $dataPSM->PSM_TRANS_GENERATE_BILLING == '0' ? 'selected' : '' ?>>Manual</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Price Before Tax*</label>
                                                        <input type="text" name="PSM_TRANS_NET_BEFORE_TAX" class="form-control" id="net_before_tax" placeholder="Price Before Tax" value="<?php echo number_format($dataAddendum->PSM_TRANS_NET_BEFORE_TAX,0,'','.'); ?>" readonly>
                                                        <input type="hidden" name="PSM_TRANS_NET_BEFORE_TAX_REAL" class="form-control" id="net_before_tax_real" placeholder="Price Before Tax" value="<?php echo $dataAddendum->PSM_TRANS_NET_BEFORE_TAX; ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Price Tax*</label>
                                                        <input type="text" name="PSM_TRANS_PPN" class="form-control" id="price_tax" placeholder="Price Tax" value="<?php echo number_format($dataAddendum->PSM_TRANS_PPN,0,'','.'); ?>" readonly>
                                                        <input type="hidden" name="PSM_TRANS_PPN_REAL" class="form-control" id="price_tax_real" placeholder="Price Tax" value="<?php echo $dataAddendum->PSM_TRANS_PPN; ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Price After Tax*</label>
                                                        <input type="text" name="PSM_TRANS_PRICE" class="form-control" id="price_total" placeholder="Price After Tax" readonly value="<?php echo number_format($dataAddendum->PSM_TRANS_PRICE,0,'','.'); ?>">
                                                        <input type="hidden" name="PSM_TRANS_PRICE_REAL" class="form-control" id="price_total_real" placeholder="Price After Tax" readonly value="<?php echo $dataAddendum->PSM_TRANS_PRICE; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Grace Period Type</label>
                                                        <select name="PSM_TRANS_GRASS_TYPE" class="form-control">
                                                            <option value="">Please Choose</option>
                                                            <option value="SOT" <?php if($dataAddendum->PSM_TRANS_GRASS_TYPE == "SOT"){ echo 'selected';} ?>>Start of Contract</option>
                                                            <option value="EOT" <?php if($dataAddendum->PSM_TRANS_GRASS_TYPE == "EOT"){ echo 'selected';} ?>>End of Contract</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>Grace Period (Month)</label>
                                                        <input type="number" name="PSM_TRANS_GRASS_PERIOD" class="form-control" placeholder="0" value="<?php echo $dataAddendum->PSM_TRANS_GRASS_PERIOD; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <h6><b>Especially for the type of Revenue Sharing :</b></h6><br>
                                                    <h6><b>Revenue Sharing Rate :</b></h6>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label>Minimum Amount Charge</label>
                                                        <input type="number" name="PSM_MIN_AMT" class="form-control" placeholder="0" value="<?php echo $dataAddendum->PSM_MIN_AMT; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-8">
                                                    <div class="form-group">
                                                        <label>Low Amount</label>
                                                        <input type="number" name="PSM_REVENUE_LOW_NUM" class="form-control" placeholder="0" value="<?php echo $dataAddendum->PSM_REVENUE_LOW_NUM; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Low Rate (%)</label>
                                                        <input type="number" name="PSM_REVENUE_LOW_RATE" class="form-control" placeholder="0" value="<?php echo $dataAddendum->PSM_REVENUE_LOW_RATE; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="form-group">
                                                        <label>High Amount</label>
                                                        <input type="number" name="PSM_REVENUE_HIGH_NUM" class="form-control" placeholder="0" value="<?php echo $dataAddendum->PSM_REVENUE_HIGH_NUM; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>High Rate (%)</label>
                                                        <input type="number" name="PSM_REVENUE_HIGH_RATE" class="form-control" placeholder="0" value="<?php echo $dataAddendum->PSM_REVENUE_HIGH_RATE; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <h6><b>Investment :</b></h6>
                                                </div>
                                                <div class="col-lg-8">
                                                    <div class="form-group">
                                                        <label>Investment Amount</label>
                                                        <input type="number" name="PSM_INVEST_NUM" class="form-control" placeholder="0" value="<?php echo $dataAddendum->PSM_INVEST_NUM; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <div class="form-group">
                                                        <label>Investment Rate (%)</label>
                                                        <input type="number" name="PSM_INVEST_RATE" class="form-control" placeholder="0" value="<?php echo $dataAddendum->PSM_INVEST_RATE; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label>Description Payment*</label>
                                                        <textarea name="PSM_TRANS_DESCRIPTION" class="form-control form-control-sm" size="50x3" placeholder="Description"><?php echo $dataAddendum->PSM_TRANS_DESCRIPTION; ?></textarea>
                                                        <script>
                                                            CKEDITOR.replace('PSM_TRANS_DESCRIPTION');
                                                        </script>
                                                    </div>
                                                    <br>
                                                    <div class="form-group">
                                                        <a class="btn btn-sm btn-danger" href="{{ URL('/marketing/leaseagreement/viewlistdatanew/') }}">
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
                                                                        <button type="submit" class="btn btn-primary">Save Data</button>
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
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-3 col-sm-offset-10">
    <div id="salesModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Sales Type</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-hover dataTable  display compact "
                           id="sales_type_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Sales Type</th>
                            <th>ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($dataSalesType as $salesType)
                            <tr>
                                <td>{{$salesType->MD_SALES_TYPE_DESC}}</td>
                                <td>{{$salesType->MD_SALES_TYPE_ID_INT}}</td>
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
                    <table class="table table-bordered table-hover dataTable  display compact "
                           id="tenant_table" style="padding: 0.5em;">
                        <thead>
                        <tr>
                            <th>Tenant</th>
                            <th>ID</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($dataTenant as $tenant)
                            <tr>
                                <td>{{$tenant->MD_TENANT_NAME_CHAR}}</td>
                                <td>{{$tenant->MD_TENANT_ID_INT}}</td>
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


