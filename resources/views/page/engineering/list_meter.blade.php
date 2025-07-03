@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Util Meter - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Util Meter
@endsection

@section('content')

<script>
$(document).ready(function()    {
    $('#engineering_meter').DataTable({
        pageLength : 25,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                //footer: true,
                title: '<?php echo "Data List Meter "; ?>'
            },
            {
                extend: 'pdfHtml5',
                //footer: true,
                title: '<?php echo "Data List Meter "; ?>'
            }
        ]
    });
    $(document).on("click", ".open-meterModal", function () {
        var id = $(this).data('id');
        console.log('Id:'+id);
        //dd('test');

        if(id == '0'){
            $('.modal-body #meterForm').get(0).setAttribute('action', '{{ route("engineering.set_meter") }}');
            $('.modal-body #meterForm').get(0).setAttribute('method', 'POST');
        }else{
            $('.modal-body #meterForm').get(0).setAttribute('action', '{{ route("engineering.edit_meter") }}');
            $('.modal-body #meterForm').get(0).setAttribute('method', 'GET');
            $('.modal-body #meterForm').append('<input type="hidden" name="ID_METER" id="ID_METER" value="'+ id +'" />');
            $.ajax({
                url: "{{ URL('engineering/find_meter') }}"+"/"+id,
                dataType: "json",
                type: "GET",
                data: {
                    // id:id,
                },
                success: function( data ) {
                    console.log(data['ID_METER']);
                    $('#UTILS_METER_CHAR').val(data['UTILS_METER_CHAR']);
                    $('#UTILS_METER_TYPE').val(data['UTILS_METER_TYPE']);
                    // $('#UTILS_METER_DT_TYPE').val(data['UTILS_METER_DT_TYPE']);
                    $('#METER_STAND_START_LWBP').val(data['METER_STAND_START_LWBP']);
                    $('#METER_STAND_START_WBP').val(data['METER_STAND_STAMETER_STAND_START_WBPRT']);
                }
            });
        }
        //$('#meterModal').modal('show');
    });
});
</script>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-lg-4 mb-2">
                            <a class="btn btn-success open-meterModal" href="#meterModal" role="button" data-toggle="modal" data-id="0"> Add Meter</a>
                        </div>
                    </div>
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12">
                            <table class="table-striped table-hover compact" id="engineering_meter" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Meter Name</th>
                                    <th>Meter Type</th>
                                    <th>Meter Multiplier</th>
                                    <th>Meter Start LWBP</th>
                                    <th>Meter Start WBP</th>
                                    <th>Edit</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1;?>
                                @if($utils)
                                @foreach($utils as $data)
                                    <tr>
                                        <td>{{$i}}</td>
                                        <td>{{ $data->UTILS_METER_CHAR }}</td>
                                        <td>{{ $data->utils_meter_type->UTILS_TYPE_NAME }}</td>
                                        <td style="text-align: right;">{{ $data->UTILS_METER_MULTIPLIER }}</td>
                                        <td style="text-align: right;">{{ number_format($data->METER_STAND_START_LWBP,0,'','.') }}</td>
                                        <td style="text-align: right;">{{ number_format($data->METER_STAND_START_WBP,0,'','.') }}</td>
                                        <?php $dataUtilTenantCount = DB::table('UTILS_TENANTS')->where('ID_METER', $data->ID_METER)->where('PROJECT_NO_CHAR', session('current_project'))->count(); ?>
                                        @if($dataUtilTenantCount > 0)
                                        <td style="text-align: center;">
                                            <a class="btn btn-info btn-sm" href="javascript:void(0)" onclick="editData('<?php echo $data->ID_METER ?>','<?php echo $data->UTILS_METER_CHAR ?>','<?php echo $data->UTILS_METER_TYPE ?>','<?php echo $data->UTILS_METER_MULTIPLIER ?>','<?php echo (float) $data->METER_STAND_START_LWBP ?>','<?php echo (float) $data->METER_STAND_START_WBP ?>')">
                                                Edit
                                            </a>
                                        </td>
                                        @else
                                        <td style="text-align: center;">
                                            <a class="btn btn-default btn-sm" href="javascript:void(0)">Edit</a>
                                        </td>
                                        @endif
                                        <?php $i++;?>
                                    </tr>
                                @endforeach
                                @else
                                <tr><td colspan="5">No Data</td></tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $uType = \App\Model\Engineerings\UtilsType::pluck('UTILS_TYPE_NAME', 'id')->prepend('-=Pilih=-', '0');?>
<div class="modal fade" id="meterModal" tabindex="-1" role="dialog" aria-labelledby="meterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="meterModalLabel">Utility Meter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('engineering.set_meter') }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <div class="form-group">
                        <label for="UTILS_METER_CHAR">Meter ID</label>
                        <input class="form-control" type="text" name="UTILS_METER_CHAR" id="UTILS_METER_CHAR" />
                    </div>
                    <div class="form-group">
                        <label>Meter Type</label>
                        <select name="UTILS_METER_TYPE" id="UTILS_METER_TYPE" class="form-control">
                            @foreach($uType as $ut)
                                <option value="{{ $ut }}">{{ $ut }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="METER_MULTIPLIER">Meter Multiplier</label>
                        <input class="form-control" type="number" name="METER_MULTIPLIER" id="METER_MULTIPLIER" />
                    </div>
                    <div class="form-group">
                        <label for="METER_STAND_START_LWBP">Meter Start LWBP</label>
                        <input class="form-control" type="text" name="METER_STAND_START_LWBP" id="METER_STAND_START_LWBP" />
                    </div>
                    <div class="form-group">
                        <label for="METER_STAND_START_WBP">Meter Start WBP</label>
                        <input class="form-control" type="text" name="METER_STAND_START_WBP" id="METER_STAND_START_WBP" />
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" style="float: right;">Save Changes</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-modal" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Utility Meter</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form method="POST" action="{{route('engineering.edit_meter2')}}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Meter ID</label>
                    <input class="form-control" type="hidden" name="ID_METER_EDIT" id="ID_METER_EDIT" required readonly/>
                    <input class="form-control" type="text" name="UTILS_METER_CHAR_EDIT" id="UTILS_METER_CHAR_EDIT" required/>
                </div>
                <div class="form-group">
                    <label>Meter Type</label>
                    <select name="UTILS_METER_TYPE_EDIT" class="custom-select select2-info" id="UTILS_METER_TYPE_EDIT" style="width: 100%;" required>
                        <option value="">-- NOT SELECTED --</option>
                        @foreach($ddlMeterType as $data)
                            <option value="{{$data->id}}">{{ $data->UTILS_TYPE_NAME }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Meter Multiplier</label>
                    <input class="form-control" type="number" name="METER_MULTIPLIER_EDIT" id="METER_MULTIPLIER_EDIT" required/>
                </div>
                <div class="form-group">
                    <label>Meter Start LWBP</label>
                    <input class="form-control" type="text" name="METER_STAND_START_LWBP_EDIT" id="METER_STAND_START_LWBP_EDIT" required/>
                </div>
                <div class="form-group">
                    <label>Meter Start WBP</label>
                    <input class="form-control" type="text" name="METER_STAND_START_WBP_EDIT" id="METER_STAND_START_WBP_EDIT" required/>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
    $(function() {
        $('#UTILS_METER_TYPE_EDIT').select2();
    });

    function editData(ID_METER, UTILS_METER_CHAR, UTILS_METER_TYPE, UTILS_METER_MULTIPLIER, METER_STAND_START_LWBP, METER_STAND_START_WBP) {
        $("#ID_METER_EDIT").val(ID_METER);
        $("#UTILS_METER_CHAR_EDIT").val(UTILS_METER_CHAR);
        $("#UTILS_METER_TYPE_EDIT").val(UTILS_METER_TYPE);
        $("#UTILS_METER_TYPE_EDIT").trigger('change');
        $("#METER_MULTIPLIER_EDIT").val(UTILS_METER_MULTIPLIER);
        $("#METER_STAND_START_LWBP_EDIT").val(METER_STAND_START_LWBP);
        $("#METER_STAND_START_WBP_EDIT").val(METER_STAND_START_WBP);
        $('#edit-modal').modal('show');
    }
</script>
@endsection
