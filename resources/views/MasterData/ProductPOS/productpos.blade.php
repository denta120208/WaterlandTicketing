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
    div .dt-buttons{
        float : left;
    }
    .dataTables_length{
        float : left;
        padding-left: 10px;
    }
    td {
        white-space: nowrap;
        text-align: center;
    }
</style>

@extends('layouts.mainLayouts')

@section('navbar_header')
    List Product POS - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    List Product POS
@endsection

@section('content')
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

                    <form>
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <a class="form-control btn btn-info" href="{{ route('view_add_product_pos') }}">
                                        Add New Product POS
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <br />
                        <div class="row" style="padding-left: 5px;">
                            <div class="col-md" style="overflow-x:auto;">                            
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Category</th>
                                        <th>Product Name</th>
                                        <th>Price</th>
                                        <th>PB1</th>
                                        <th>PPH</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach($dataProductPOSs as $dataProductPOS)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $dataProductPOS->CATEGORY_DESC_CHAR }}</td>
                                        <td>{{ $dataProductPOS->NAMA_PRODUCT }}</td>
                                        <td>{{ number_format($dataProductPOS->HARGA_SATUAN_FLOAT, 0, ',', '.') }}</td>
                                        <td>{{ (float) $dataProductPOS->PB1_PERCENT_INT . "%" }}</td>
                                        <td>{{ (float) $dataProductPOS->PPH_PERCENT_INT . "%" }}</td>
                                        <td>{{ $dataProductPOS->STATUS_DESC_CHAR }}</td>
                                        <td><a href="{{ route('view_edit_product_pos', base64_encode($dataProductPOS->MD_PRODUCT_POS_ID_INT)) }}" title='Edit' class='btn bg-gradient-primary btn-sm'>Edit</a></td>
                                        <td><a href='javascript:void(0)' title='Delete' onclick="swalDeleteData('{{ $dataProductPOS->MD_PRODUCT_POS_ID_INT }}')" class='btn bg-gradient-danger btn-sm'>Delete</a></td>
                                    </tr>
                                    <?php $i++; ?>
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

        $('#example1').DataTable({
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            pageLength: 10,
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    messageTop: ' List Product POS',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {   
                    extend: 'pdfHtml5',
                    orientation: 'portrait',
                    pageSize: 'A4',
                    messageTop: ' List Product POS',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                },
                {
                    extend: 'print',
                    messageTop: ' List Product POS',
                    exportOptions: {
                        columns: [0, 1, 2]
                    }
                }
            ]
        });
    });

    function swalDeleteData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Delete</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/delete_product_pos/" + param1;
            }
        });
    }
</script>
@endsection