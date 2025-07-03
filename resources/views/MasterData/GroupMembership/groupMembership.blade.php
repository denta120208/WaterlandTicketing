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
    List Group Membership - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    List Group Membership
@endsection

@section('content')
<div class="modal fade" id="modal-save">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ url('save_group_membership') }}" method="post">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h4 class="modal-title">Save Group Membership</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Description <span style="color: red;">*</span></label>
                                <input type="text" name="TXT_DESC" class="form-control" id="TXT_DESC" placeholder="Enter Description" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Qty <span style="color: red;">*</span></label>
                                <input type="number" name="TXT_QTY" class="form-control" id="TXT_QTY" placeholder="Enter Qty" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer right-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-edit">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ url('edit_group_membership') }}" method="post">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h4 class="modal-title">Edit Group Membership</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="TXT_ID_EDIT" class="form-control" id="TXT_ID_EDIT" placeholder="Enter ID" readonly required>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Description <span style="color: red;">*</span></label>
                                <input type="text" name="TXT_DESC_EDIT" class="form-control" id="TXT_DESC_EDIT" placeholder="Enter Description" required>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Qty <span style="color: red;">*</span></label>
                                <input type="number" name="TXT_QTY_EDIT" class="form-control" id="TXT_QTY_EDIT" placeholder="Enter Qty" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer right-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                                    <a class="form-control btn btn-info" href="javascript:void(0)" onclick="showModalSave()">
                                        Add New Group Membership
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
                                        <th>Description</th>
                                        <th>Qty</th>
                                        <th>Status</th>
                                        <th>Edit</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
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
            "processing": true,
            "serverSide": true,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            pageLength: 10,
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    messageTop: ' List Group Membership',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    },
                    action: newexportaction
                },
                {   
                    extend: 'pdfHtml5',
                    orientation: 'portrait',
                    pageSize: 'A4',
                    messageTop: ' List Group Membership',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    },
                    action: newexportaction
                },
                {
                    extend: 'print',
                    messageTop: ' List Group Membership',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    },
                    action: newexportaction
                }
            ],
            "ajax":{
                "url": "{{ url('listTblGroupMembership') }}",
                "dataType": "json",
                "type": "POST",
                "data": { _token: "{{csrf_token()}}"}
            },
            "columns": [
                { "data": null },
                { "data": "DESC_CHAR" },
                { "data": "QTY_INT" },
                { "data": "DESC_CHAR_STATUS" },
                { "data": "EDIT" },
                { "data": "HAPUS" }
            ],
            "columnDefs": [
                {
                    "render": function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                    "targets": 0
                }
            ]
        });
    });

    function newexportaction(e, dt, button, config) {
        var self = this;
        var oldStart = dt.settings()[0]._iDisplayStart;
        dt.one('preXhr', function (e, s, data) {
            data.start = 0;
            data.length = dt.page.info().recordsTotal;
            dt.one('preDraw', function (e, settings) {
                if (button[0].className.indexOf('buttons-copy') >= 0) {
                    $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                    $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                    $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                    $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                        $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                        $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-print') >= 0) {
                    $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                }
                dt.one('preXhr', function (e, s, data) {
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });

                setTimeout(dt.ajax.reload, 0);
                return false;
            });
        });

        dt.ajax.reload();
    };

    function showModalSave() {
        $('#TXT_DESC').val(null);
        $('#TXT_QTY').val(null);
        $('#modal-save').modal('show');
    }

    function showModalEdit(param1) {
        $('#TXT_ID_EDIT').val(null);
        $('#TXT_DESC_EDIT').val(null);
        $('#TXT_QTY_EDIT').val(null);
        $.ajax({
            url: '/get_group_membership/' + param1,
            type: "GET",
            dataType: "json",
            success: function (data) {
                $('#TXT_ID_EDIT').val(param1);
                $('#TXT_DESC_EDIT').val(data.dataGroupMembership.DESC_CHAR);
                $('#TXT_QTY_EDIT').val(data.dataGroupMembership.QTY_INT);
            },
            error: function() {
                alert('Error, Please contact Administrator!');
            }
        });
        $('#modal-edit').modal('show');
    }

    function swalDeleteData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Delete</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/delete_group_membership/" + param1;
            }
        });
    }
</script>
@endsection