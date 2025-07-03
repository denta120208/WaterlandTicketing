@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Discount Schedule - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Discount Schedule
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
    $(document).ready(function() {
        $('#vendor_table').DataTable({
            order : [],
            pageLength : 25,
            scrollX: true
        });
    });
</script>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <br><br>
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12">
                            <table class="table-striped table-hover compact" id="vendor_table" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Document</th>
                                    <th>Shop</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Process</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i = 1;
                                ?>
                                @foreach($schedDisc as $data)
                                    <tr>
                                        <td>{{$i}}</td>
                                        <td>{{$data->PSM_SCHED_DISC_NOCHAR}}</td>
                                        <td>{{$data->SHOP_NAME_CHAR}}</td>
                                        <td>{{$data->PSM_TRANS_DISC_TYPE}}</td>
                                        @if($data->PSM_TRANS_DISC_TYPE == 'Percentation')
                                            <td>{{number_format($data->PSM_SCHED_DISC_AMT,2,',','.')}}%</td>
                                        @else
                                            <td>{{number_format($data->PSM_SCHED_DISC_AMT,2,',','.')}}</td>
                                        @endif
                                        <td>{{$data->PSM_TRANS_DISC_STATUS_INT}}</td>
                                        @if($data->PSM_TRANS_DISC_STATUS_INT == 'APPROVE')
                                        <td class="center">
                                            <a class="btn btn-sm btn-success" href="{{ URL('/marketing/leaseagreement/viewprocessdiscount/' . $data->PSM_SCHED_DISC_ID_INT) }}">
                                                <i>
                                                    Process
                                                </i>
                                            </a>
                                        </td>
                                        @else
                                        <td class="center">
                                            <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                <i>
                                                    Process
                                                </i>
                                            </a>
                                        </td>
                                        @endif
                                        <?php
                                        $i++;
                                        ?>
                                    </tr>
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
@endsection
