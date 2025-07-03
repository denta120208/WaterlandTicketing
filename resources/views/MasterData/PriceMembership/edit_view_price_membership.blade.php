<?php
    use \koolreport\widgets\koolphp\Table;
    use \koolreport\widgets\google\BarChart;
    use \koolreport\widgets\google\PieChart;
    use \koolreport\pivot\widgets\PivotTable;
    use \koolreport\widgets\google\ColumnChart;
    use \koolreport\drilldown\LegacyDrillDown;
    use \koolreport\drilldown\DrillDown;
    use \koolreport\widgets\google\LineChart;
    use Illuminate\Support\Str;
?>

@extends('layouts.mainLayouts')

@section('navbar_header')
    Form Edit Price Membership - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Edit Price Membership
@endsection

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('edit_price_membership') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <input type="hidden" name="TXT_ID" class="form-control" id="TXT_ID" value="{{ $dataPriceMembership->MD_PRICE_MEMBERSHIP_ID_INT }}" required>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Group <span style="color: red;">*</span></label>
                                    <select id="DDL_GROUP" name="DDL_GROUP" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        @foreach($ddlGroupMembership as $data)
                                        <option value="{{ $data->MD_GROUP_MEMBERSHIP_ID_INT }}" {{ $data->MD_GROUP_MEMBERSHIP_ID_INT == $dataPriceMembership->MD_GROUP_MEMBERSHIP_ID_INT ? "selected" : "" }}>
                                            {{ $data->DESC_CHAR }} (Jumlah Orang : {{ $data->QTY_INT }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Group Type <span style="color: red;">*</span></label>
                                    <select id="DDL_GROUP_TYPE" name="DDL_GROUP_TYPE" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        @foreach($ddlGroupTypeMembership as $data)
                                        <option value="{{ $data->MD_GROUP_TYPE_MEMBERSHIP_ID_INT }}" {{ $data->MD_GROUP_TYPE_MEMBERSHIP_ID_INT == $dataPriceMembership->MD_GROUP_TYPE_MEMBERSHIP_ID_INT ? "selected" : "" }}>
                                            {{ $data->DESC_CHAR }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Periode <span style="color: red;">*</span></label>
                                    <select id="DDL_PERIODE" name="DDL_PERIODE" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        @foreach($ddlPeriodeMembership as $data)
                                        <option value="{{ $data->MD_PERIODE_MEMBERSHIP_ID_INT }}" {{ $data->MD_PERIODE_MEMBERSHIP_ID_INT == $dataPriceMembership->MD_PERIODE_MEMBERSHIP_ID_INT ? "selected" : "" }}>
                                            {{ $data->DESC_CHAR }} (Jumlah Bulan : {{ $data->PERIODE_IN_MONTH_INT }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Price <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_PRICE" class="form-control" id="TXT_PRICE" placeholder="Enter Price" value="{{ (float) $dataPriceMembership->HARGA_FLOAT }}" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="BTN_SUBMIT" name="BTN_SUBMIT" class="btn btn-primary float-right">Edit</button>
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
    });

    function isNumberWithDecimal(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && ((charCode < 48 && charCode !== 46) || charCode > 57)) {
            return false;
        }
        return true;
    }
</script>
@endsection