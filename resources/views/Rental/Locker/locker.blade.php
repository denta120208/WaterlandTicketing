<?php
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\widgets\google\BarChart;
    use \koolreport\widgets\google\PieChart;
    use \koolreport\pivot\widgets\PivotTable;
    use \koolreport\widgets\google\ColumnChart;
    use \koolreport\drilldown\LegacyDrillDown;
    use \koolreport\drilldown\DrillDown;
    use \koolreport\widgets\google\LineChart;
    use \koolreport\barcode\QRCode;
?>

<style>
    td {
        white-space: nowrap;
    }
</style>

@extends('layouts.mainLayouts')

@section('navbar_header')
    List Rental Locker - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    List Rental Locker
@endsection

@section('content')
@if(session()->has('urlRedirect'))
    <script>
        window.open('{{session()->get('urlRedirect')}}', "_blank").print();
    </script>
@endif

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
                            @if(session()->has('change'))
                            <script>
                                $(document).ready(function() {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        html: "<h5>Change : <b>{{session()->get('change')}}</b></h5>"
                                    });
                                });
                            </script>
                            @endif
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

                    <form>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <a class="form-control btn btn-info" href="{{ route('rental_locker') }}">
                                        Rental Locker
                                    </a>
                                </div>
                            </div>
                        </div>

                        <br />
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md" style="overflow-x:auto;">
                            <h4><b>Current Rental</b></h4>
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Transaction Number</th>
                                        <th>Customer</th>
                                        <th>Telp</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Paid</th>
                                        <th>Change</th>
                                        <th>Cashier</th>
                                        <th>Transaction Date</th>
                                        <th>Print Receipt</th>
                                        <th>Retur</th>
                                        <th>Cancel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTransLockerRent as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$data->TRANS_RENTAL_LOCKER_ID_INT}}</td>
                                        <td style="text-align: center;">{{$data->TRANS_LOCKER_NO_CHAR}}</td>
                                        <td style="text-align: center;">{{$data->CUSTOMER_NAME_CHAR}}</td>
                                        <td style="text-align: center;">{{$data->NO_TELP_CHAR}}</td>
                                        <td style="text-align: center;">{{number_format($data->QTY_INT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->TOTAL_HARGA_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->TOTAL_PAID_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->TOTAL_CHANGE_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{$data->CASHIER_NAME_CHAR}}</td>
                                        <td style="text-align: center;">{{date('Y-m-d H:i:s', strtotime($data->created_at))}}</td>
                                        <td style="text-align: center;">
                                            <a href="{{ URL('/print_rental_locker/' . $data->TRANS_RENTAL_LOCKER_ID_INT) }}" class="btn btn-primary" onclick="window.open(this.href).print(); return false">
                                                <i>
                                                    Print
                                                </i>
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="javascript:void(0)" onclick="swalReturData({{$data->TRANS_RENTAL_LOCKER_ID_INT}})" class="btn btn-success">
                                                <i>
                                                    Retur
                                                </i>
                                            </a>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="javascript:void(0)" onclick="swalCancelData({{$data->TRANS_RENTAL_LOCKER_ID_INT}})" class="btn btn-danger">
                                                <i>
                                                    Cancel
                                                </i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>

                        <br /><br />
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md" style="overflow-x:auto;">
                            <h4><b>Current Rental Details</b></h4>
                            <table id="example2" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Transaction Number</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Transaction Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTransLockerRentDetails as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$data->TRANS_RENTAL_LOCKER_DETAIL_ID_INT}}</td>
                                        <td style="text-align: center;">{{$data->TRANS_LOCKER_NO_CHAR}}</td>
                                        <td style="text-align: center;">{{$data->DESC_CHAR}}</td>
                                        <td style="text-align: center;">{{number_format($data->HARGA_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{date('Y-m-d H:i:s', strtotime($data->created_at))}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>

                        <br /><br />
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md" style="overflow-x:auto;">
                            <h4><b>Rental Retur (Today)</b></h4>
                            <table id="example3" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Transaction Number</th>
                                        <th>Customer</th>
                                        <th>Telp</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th>Paid</th>
                                        <th>Change</th>
                                        <th>Cashier</th>
                                        <th>Transaction Date</th>
                                        <th>Retur By</th>
                                        <th>Retur Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTransLockerRetur as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$data->TRANS_RENTAL_LOCKER_ID_INT}}</td>
                                        <td style="text-align: center;">{{$data->TRANS_LOCKER_NO_CHAR}}</td>
                                        <td style="text-align: center;">{{$data->CUSTOMER_NAME_CHAR}}</td>
                                        <td style="text-align: center;">{{$data->NO_TELP_CHAR}}</td>
                                        <td style="text-align: center;">{{number_format($data->QTY_INT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->TOTAL_HARGA_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->TOTAL_PAID_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{number_format($data->TOTAL_CHANGE_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{$data->CASHIER_NAME_CHAR}}</td>
                                        <td style="text-align: center;">{{date('Y-m-d H:i:s', strtotime($data->created_at))}}</td>
                                        <td style="text-align: center;">{{$data->RETUR_BY}}</td>
                                        <td style="text-align: center;">{{date('Y-m-d H:i:s', strtotime($data->RETUR_AT))}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>

                        <br /><br />
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md" style="overflow-x:auto;">
                            <h4><b>Rental Retur Details (Today)</b></h4>
                            <table id="example4" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Transaction Number</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Transaction Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dataTransLockerReturDetails as $data)
                                    <tr>
                                        <td style="text-align: center;">{{$data->TRANS_RENTAL_LOCKER_DETAIL_ID_INT}}</td>
                                        <td style="text-align: center;">{{$data->TRANS_LOCKER_NO_CHAR}}</td>
                                        <td style="text-align: center;">{{$data->DESC_CHAR}}</td>
                                        <td style="text-align: center;">{{number_format($data->HARGA_FLOAT, 0, ",", ".")}}</td>
                                        <td style="text-align: center;">{{date('Y-m-d H:i:s', strtotime($data->created_at))}}</td>
                                    </tr>
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

<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#example1").DataTable({
            "order": [[0, 'desc']],
            "scrollY": true, "scrollX": true,
            "responsive": false, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        $("#example2").DataTable({
            "order": [[0, 'desc']],
            "scrollY": true, "scrollX": true,
            "responsive": false, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');

        $("#example3").DataTable({
            "order": [[0, 'desc']],
            "scrollY": true, "scrollX": true,
            "responsive": false, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');

        $("#example4").DataTable({
            "order": [[0, 'desc']],
            "scrollY": true, "scrollX": true,
            "responsive": false, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example4_wrapper .col-md-6:eq(0)');
    });

    function swalReturData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Retur</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/retur_rental_locker/" + param1;
            }
        });
    }

    function swalCancelData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Cancel</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/cancel_rental_locker/" + param1;
            }
        });
    }
</script>
@endsection