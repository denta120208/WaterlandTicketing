@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Letter Of Intent - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Letter Of Intent
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
            pageLength : 10,
            scrollX: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',                    
                    title: '<?php echo "Data Lease Agreement "; ?>'
                },
                {
                    extend: 'pdfHtml5',
                    footer: true,
                    title: '<?php echo "Data Lease Agreement "; ?>'
                }
            ]
        });
    });
</script>

<style>
    th, td {
        padding-right: 10px;
    }
</style>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <div style="padding-left: 5px;">
                        @if(session()->has('success'))
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ session()->get('success') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('error'))
                            <div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ session()->get('error') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('warning'))
                            <div class="alert alert-warning alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ session()->get('warning') }}</strong>
                            </div>
                        @endif
                        @if(session()->has('info'))
                            <div class="alert alert-info alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ session()->get('info') }}</strong>
                            </div>
                        @endif
                    </div>

                    <div class="row" style="padding-left: 5px;">
                        <div class="col-lg-4 mb-2">
                            <a class="btn btn-success" href="{!! URL::route('marketing.leaseagreement.viewadddataleaseAgreement') !!}" role="button">
                                Add Data Letter Of Intent
                            </a>
                        </div>
                    </div>
                    <br><br>
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12" style="overflow-x:auto;">
                            <table class="table-striped table-hover compact" id="vendor_table" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Document</th>
                                        <th>BAST</th>
                                        <th>Lease Agreement</th>
                                        <th>Name</th>
                                        <th>Shop Name</th>
                                        <th>Base Amount</th>
                                        <th>PPN</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Print</th>
                                        <th>Print Lease Agreement</th>
                                        <th>View/Edit</th>
                                        <th>InActive</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach($PSMData as $data)
                                        <tr>
                                            <td>{{$i}}</td>
                                            <td>{{$data->PSM_TRANS_NOCHAR}}</td>
                                            <td>{{$data->NO_BAST_NOCHAR}}</td>
                                            <td>{{$data->NO_KONTRAK_NOCHAR}}</td>
                                            <td>{{$data->MD_TENANT_NAME_CHAR}}</td>
                                            <td>{{$data->SHOP_NAME_CHAR}}</td>
                                            <td style="text-align: right;">{{number_format($data->PSM_TRANS_NET_BEFORE_TAX,0,'','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->PSM_TRANS_PPN,0,'','.')}}</td>
                                            <td style="text-align: right;">{{number_format($data->PSM_TRANS_PRICE,0,'','.')}}</td>
                                            <td style="text-align: center;">{{$data->PSM_TRANS_STATUS_INT}}</td>
                                            @if($data->PSM_TRANS_STATUS_INT == 'APPROVE')
                                                <td class="center">
                                                    <a class="btn btn-sm btn-info" href="{{ URL('/marketing/leaseagreement/printloi/' . $data->PSM_TRANS_ID_INT) }}" onclick="window.open(this.href).print(); return false">
                                                        <i>
                                                            Print
                                                        </i>
                                                    </a>
                                                </td>
                                                @if($data->NO_KONTRAK_NOCHAR == NULL)
                                                <td class="center">
                                                    <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                        <i>
                                                            Print
                                                        </i>
                                                    </a>
                                                </td>
                                                @else
                                                <td class="center">
                                                    <a class="btn btn-sm btn-info" href="{{ URL('/marketing/leaseagreement/printleaseagreement/' . $data->PSM_TRANS_ID_INT) }}" onclick="window.open(this.href).print(); return false">
                                                        <i>
                                                            Print
                                                        </i>
                                                    </a>
                                                </td>
                                                @endif
                                                <td class="center">
                                                    <a class="btn btn-sm btn-warning" href="{{ URL('/marketing/leaseagreement/view_edit_data/' . $data->PSM_TRANS_ID_INT) }}">
                                                        <i>
                                                            View/Edit
                                                        </i>
                                                    </a>
                                                </td>
                                                <td class="center">
                                                    <a href="#confModal{!!$data->PSM_TRANS_ID_INT!!}" class="btn btn-sm btn-danger" data-toggle="modal">
                                                        <i>
                                                            InActive
                                                        </i>
                                                    </a>
                                                    <div id="confModal{!!$data->PSM_TRANS_ID_INT!!}" class="modal fade">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Confirmation</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <p>Are you sure inactive this document ?</p>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
                                                                    <a href="{{ URL('/marketing/leaseagreement/inactivedatapsm/'. $data->PSM_TRANS_ID_INT) }}" class="btn btn-success">Yes</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            @else
                                                <td class="center">
                                                    <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                        <i>
                                                            Print
                                                        </i>
                                                    </a>
                                                </td>
                                                <td class="center">
                                                    <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                        <i>
                                                            Print
                                                        </i>
                                                    </a>
                                                </td>
                                                <td class="center">
                                                    <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                        <i>
                                                            View/Edit
                                                        </i>
                                                    </a>
                                                </td>
                                                <td class="center">
                                                    <a class="btn btn-sm btn-default" href="javascript:void(0)">
                                                        <i>
                                                            InActive
                                                        </i>
                                                    </a>
                                                </td>
                                            @endif
                                        </tr>
                                        <?php $i += 1; ?>
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
