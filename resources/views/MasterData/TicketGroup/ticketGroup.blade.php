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
    List Ticket Group - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    List Ticket Group
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
                                    <a class="form-control btn btn-info" href="{{ route('add_new_ticket_group') }}">
                                        Add New Ticket Group
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
                                        <th>Ticket Person</th>
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
                    messageTop: ' List Ticket Group',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    },
                    action: newexportaction
                },
                {   
                    extend: 'pdfHtml5',
                    orientation: 'portrait',
                    pageSize: 'A4',
                    messageTop: ' List Ticket Group',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    },
                    action: newexportaction
                },
                {
                    extend: 'print',
                    messageTop: ' List Ticket Group',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    },
                    action: newexportaction
                }
            ],
            "ajax":{
                "url": "{{ url('listTblTicketGroup') }}",
                "dataType": "json",
                "type": "POST",
                "data": { _token: "{{csrf_token()}}"}
            },
            "columns": [
                { "data": null },
                { "data": "MD_GROUP_TICKET_DESC" },
                { "data": "MD_GROUP_TICKET_PERSON" },
                { "data": "STATUS_DESC_CHAR" },
                { "data": "EDIT" },
                { "data": "CANCEL" }
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

    function swalDeleteData(param1) {
        Swal.fire({
        html: 'Do you want to <b style="color: red;">Delete</b> this data?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        allowOutsideClick: false,
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/delete_ticket_group/" + param1;
            }
        });
    }
</script>
@endsection