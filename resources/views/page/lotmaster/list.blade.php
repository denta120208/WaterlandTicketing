@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Lot - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Lot
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

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-2">
                            <a href="{{ route('lot.lotmaster.viewadddatalot') }}" class="btn bg-gradient-success btn-sm">
                                Entry Lot Master
                            </a>
                        </div>
                    </div>
                    <br><br>
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12">
                            <table class="table-striped table-hover compact" id="optutility_list" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Lot</th>
                                        <th>Type</th>
                                        <th>Level</th>
                                        <th>Zone</th>
                                        <th>Sqm</th>
                                        <th>Release</th>
                                        <th>Rent</th>
                                        <th>View/Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    @foreach($listDataLot as $data)
                                        <tr>
                                            <td>{{ $no }}</td>
                                            <td>{{ $data->LOT_STOCK_NO }}</td>
                                            <td>{{ $data->LOT_TYPE_DESC }}</td>
                                            <td>{{ $data->LOT_LEVEL_DESC }}</td>
                                            <td>{{ $data->LOT_ZONE_DESC }}</td>
                                            <td style="text-align: right;">{{ number_format($data->LOT_STOCK_SQM,2,',','.') }}</td>
                                            <td>{{ $data->ON_RELEASE_STAT_INT }}</td>
                                            <td>{{ $data->ON_RENT_STAT_INT }}</td>
                                            <td class="center">
                                                <a class="btn btn-sm btn-warning" href="{{ URL('/lot/lotmaster/vieweditdatalot/' . $data->LOT_STOCK_ID_INT) }}">
                                                    <i>
                                                        View/Edit
                                                    </i>
                                                </a>
                                            </td>
                                            @if($data->ON_RENT_STAT_INT == 'RENT')
                                            <td class="center">
                                                <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                    <i>
                                                        Delete
                                                    </i>
                                                </a>
                                            </td>
                                            @else
                                            <td class="center">
                                                <a href="#deleteLot{!!$data->LOT_STOCK_ID_INT!!}" class="btn btn-sm btn-danger" data-toggle="modal">
                                                    <i>
                                                        Delete
                                                    </i>
                                                </a>
                                                <div id="deleteLot{!!$data->LOT_STOCK_ID_INT!!}" class="modal fade">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Confirmation</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <p>Are you sure delete this document ?</p>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                                <a href="{{ URL('/lot/lotmaster/deletedatalot/'.$data->LOT_STOCK_ID_INT) }}" class="btn btn-success">Yes</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            @endif
                                        </tr>
                                        <?php $no += 1; ?>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function formSubmit()
    {
        $("#deleteForm").submit();
    }
</script>
<script>
    $(document).ready(function(){
        $('#optutility_list').DataTable( {
            order : [],
            pageLength : 25,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: '<?php echo "Data Lot"; ?>'
                },
                {
                    extend: 'pdfHtml5',
                    footer: true,
                    title: '<?php echo "Data Lot"; ?>'
                }
            ]
        });

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    });
</script>
@endsection

