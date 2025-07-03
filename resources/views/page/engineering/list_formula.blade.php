@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Util Formula - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Util Formula
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
        buttons:
            [{
                extend: 'csv',
                exportOptions :{
                columns:[1,2,3,4,5,6,7]
                }
            },
            {
                extend: 'pdf',
                exportOptions :{
                columns:[1,2,3,4,5,6,7]
                }
            }]
    });
    $(document).on("click", ".open-formModal", function () {
        var id = $(this).data('id');
        if(id == '0'){
            $('.modal-body #formulaForm').get(0).setAttribute('action', '{{ route("engineering.set_formula") }}');
            $('.modal-body #formulaForm').get(0).setAttribute('method', 'POST');
        }else{
            $('.modal-body #formulaForm').get(0).setAttribute('action', '{{ route("engineering.edit_formula") }}');
            $('.modal-body #formulaForm').get(0).setAttribute('method', 'GET');
            $('.modal-body #formulaForm').append('<input type="hidden" name="ID_U_FORMULA" id="ID_U_FORMULA" value="'+ id +'" />');
            $.ajax({
                url: "{{ URL('engineering/find_formula') }}"+"/"+id,
                dataType: "json",
                type: "GET",
                data: {
                    // id:id,
                },
                success: function( data ) {
                    //alert(data['UTILS_LOST_FACTOR_FIXAMT']);
                    console.log(data['ID_U_FORMULA']);
                    $('#NAME_U_FORMULA').val(data['NAME_U_FORMULA']);
                    $('#UTILS_TYPE').val(data['UTILS_TYPE']);
                    $('#UTILS_CATEGORY_ID_INT').val(data['UTILS_CATEGORY_ID_INT']);
                    $('#UTILS_KVA_RATE').val(data['UTILS_KVA_RATE']);
                    $('#UTILS_BPJU_RATE').val(data['UTILS_BPJU_RATE']);
                    $('#UTILS_LOST_FACTOR_RATE').val(data['UTILS_LOST_FACTOR_RATE']);
                    $('#UTILS_LOST_FACTOR_FIXAMT').val(data['UTILS_LOST_FACTOR_FIXAMT']);
                    $('#UTILS_LOW_RATE').val(data['UTILS_LOW_RATE']);
                    $('#UTILS_HIGH_RATE').val(data['UTILS_HIGH_RATE']);
                    $('#UTILS_BILLBOARD_RATE').val(data['UTILS_BILLBOARD_RATE']);
                    $('#UTILS_HANDLING_FEE_RATE').val(data['UTILS_HANDLING_FEE_RATE']);
                    $('#UTILS_HANDLING_FEE_FIXAMT').val(data['UTILS_HANDLING_FEE_FIXAMT']);
                    $('#UTILS_RELIABILITY_RATE').val(data['UTILS_RELIABILITY_RATE']);
                    $('#UTILS_ADMIN_RATE').val(data['UTILS_ADMIN_RATE']);
                    $('#UTILS_PPJU_RATE').val(data['UTILS_PPJU_RATE']);

                }
            });
        }
        $('#formModal').modal('show');
    });
});
</script>
<style>
    @media screen and (min-width: 676px) {
        .modal-dialog {
            max-width: 700px; /* New width for default modal */
        }
    }
</style>

<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-lg-4 mb-2">
                            <a class="btn btn-success open-formModal" href="#formModal" role="button" data-toggle="modal" data-id="0">New Formula</a>
                        </div>
                    </div>
                    <div class="row" style="padding-left: 5px;">
                        <div class="col-md-12">
                            <table class="table-striped table-hover compact" id="engineering_table" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Formula Name</th>
                                    <th>Type</th>
                                    <th>Category</th>
                                    <th>Low Amount</th>
                                    <th>High Amount</th>
                                    <th>View/Edit</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1;?>
                                @if($utils)
                                @foreach($utils as $data)
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $data->NAME_U_FORMULA }}</td>
                                        <td>{{ $data->utils_type->UTILS_TYPE_NAME }}</td>
                                        <td>{{ $data->utils_category->UTILS_CATEGORY_NAME }}</td>
                                        <td>Rp {{ number_format($data->UTILS_LOW_RATE,2,',','.') }}</td>
                                        <td>Rp {{ number_format($data->UTILS_HIGH_RATE,2,',','.') }}</td>
                                        <td class="center">
                                            <a class="btn btn-sm btn-warning open-formModal" href="#formModal" data-toggle="modal" data-id="{{ $data->ID_U_FORMULA }}"><i>View/Edit</i></a>
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

<?php
$uType = \App\Model\Engineerings\UtilsType::pluck('UTILS_TYPE_NAME', 'id')->prepend('-=Pilih=-', '0');
$uCategory = \App\Model\Engineerings\UtilsCategory::pluck('UTILS_CATEGORY_NAME', 'UTILS_CATEGORY_ID_INT')->prepend('-=Pilih=-', '0');
?>
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalLabel">Utility Formula</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formulaForm">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="NAME_U_FORMULA">Formula Name</label>
                                <input class="form-control" type="text" name="NAME_U_FORMULA" id="NAME_U_FORMULA" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Type</label>
                                <select name="UTILS_TYPE" id="UTILS_TYPE" class="form-control">
                                    @foreach($uType as $ut)
                                        <option value="{{ $ut }}">{{ $ut }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Category</label>
                                <select name="UTILS_CATEGORY_ID_INT" id="UTILS_CATEGORY_ID_INT" class="form-control">
                                    @foreach($uCategory as $uc)
                                        <option value="{{ $uc }}">{{ $uc }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="UTILS_HANDLING_FEE_RATE">Handling Fee Amount</label>
                                <input class="form-control" type="text" name="UTILS_HANDLING_FEE_RATE" id="UTILS_HANDLING_FEE_RATE" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="UTILS_HANDLING_FEE_FIXAMT">Handling Fee Fix Amount</label>
                                <input class="form-control" type="text" name="UTILS_HANDLING_FEE_FIXAMT" id="UTILS_HANDLING_FEE_FIXAMT" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="UTILS_LOST_FACTOR_RATE">Lost Factor Rate(%)</label>
                                <input class="form-control" type="text" name="UTILS_LOST_FACTOR_RATE" id="UTILS_LOST_FACTOR_RATE" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="UTILS_LOST_FACTOR_FIXAMT">Lost Factor Fix Amount</label>
                                <input class="form-control" type="text" name="UTILS_LOST_FACTOR_FIXAMT" id="UTILS_LOST_FACTOR_FIXAMT" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="UTILS_LOW_RATE">Low Amount</label>
                                <input class="form-control" type="text" name="UTILS_LOW_RATE" id="UTILS_LOW_RATE" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="UTILS_HIGH_RATE">High Amount</label>
                                <input class="form-control" type="text" name="UTILS_HIGH_RATE" id="UTILS_HIGH_RATE" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="UTILS_LOW_RATE">KVA</label>
                                <input class="form-control" type="text" name="UTILS_KVA_RATE" id="UTILS_KVA_RATE" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="UTILS_BPJU_RATE">BPJU Rate(%)</label>
                                <input class="form-control" type="text" name="UTILS_BPJU_RATE" id="UTILS_BPJU_RATE" formnovalidate  />
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="UTILS_BILLBOARD_RATE">Billboard Amount</label>
                                <input class="form-control" type="text" name="UTILS_BILLBOARD_RATE" id="UTILS_BILLBOARD_RATE" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="UTILS_PPJU_RATE">PPJU(%)</label>
                                <input class="form-control" type="text" name="UTILS_PPJU_RATE" id="UTILS_PPJU_RATE" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="UTILS_RELIABILITY_RATE">Reliability Amount</label>
                                <input class="form-control" type="text" name="UTILS_RELIABILITY_RATE" id="UTILS_RELIABILITY_RATE" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="UTILS_ADMIN_RATE">Administration(%)</label>
                                <input class="form-control" type="text" name="UTILS_ADMIN_RATE" id="UTILS_ADMIN_RATE" />
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" style="float: right">Save changes</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
@endsection