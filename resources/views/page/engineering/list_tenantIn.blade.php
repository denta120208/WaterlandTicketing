@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Input Meter - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Input Meter
@endsection

@section('content')

@if (Session::has('message'))
    <div class="alert alert-success" id="success-alert">
        {{ Session::get('message') }}
    </div>
@elseif (Session::has('errorFailed'))
    <div class="alert alert-danger" id="success-alert">
        {{ Session::get('errorFailed') }}
    </div>
@endif

<script>
$(document).ready(function()    {
    $('#engineering_table').DataTable({
        pageLength : 25,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: '<?php echo "Data Input Meter "; ?>'
            },
            {
                extend: 'pdfHtml5',
                title: '<?php echo "Data Input Meter "; ?>'
            }
        ]
    });
    $(document).on("click", ".open-electricityModal", function () {
        var id = $(this).data('id');
        var tenat_id = $(this).data('tenat_id');
            $.ajax({
                url: "{{ URL('engineering/find_tenant') }}"+"/"+tenat_id,
                dataType: "json",
                type: "GET",
                data: {
                },
                success: function( data ) {
                    $('#tenant_name').val(data['MD_TENANT_NAME_CHAR']);
                }
            });
        $('#electricityModal').modal('show');
    });
});
</script>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12">
                            <table class="table-striped table-hover compact" id="engineering_table" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Tenant Name</th>
                                    <th>Utility Type</th>
                                    <th>Meters</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1;?>
                                @if($tenants)
                                @foreach($tenants as $data)
                                    <tr>
                                        <td>{{$i}}</td>
                                        <td>{{ $data->MD_TENANT_NAME_CHAR }}</td>
                                        <td></td>
                                        <td>
                                            <a href="{{ route('engineering.meter_input', $data->MD_TENANT_ID_INT) }}"><i class="fa fa-eye"></i></a>
                                        </td>
                                        <?php $i++;?>
                                    </tr>
                                @endforeach
                                @else
                                <tr><td colspan="4">No Data</td></tr>
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

<div class="modal fade" id="electricityModal" tabindex="-1" role="dialog" aria-labelledby="electricityModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="electricityModalLabel">Electricity Meter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('engineering.set_formula') }}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <div class="form-group">
                        <label for="tenant_name">Tenant Name</label>
                        <input class="form-control" type="text" name="tenant_name" id="tenant_name" />
                    </div>
                    <div class="form-group">

                    </div>
                    <div class="form-group">
                        <label for="UTILS_HIGH_RATE">High Rate</label>
                        <input class="form-control" type="text" name="UTILS_HIGH_RATE" id="UTILS_HIGH_RATE" />
                    </div>
                    <div class="form-group">
                        <label for="UTILS_LOW_RATE">Low Rate</label>
                        <input class="form-control" type="text" name="UTILS_LOW_RATE" id="UTILS_LOW_RATE" />
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
@endsection
