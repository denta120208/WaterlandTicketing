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
    Form Edit Promo Equipment - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Edit Promo Equipment
@endsection

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('edit_promo_equipment') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <input type="hidden" name="TXT_PROMO_ID" class="form-control" id="TXT_PROMO_ID" value="{{ $dataPromo->PROMO_EQUIPMENT_ID_INT }}" required>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Category <span style="color: red;">*</span></label>
                                    <select id="DDL_CATEGORY" name="DDL_CATEGORY" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        @foreach($ddlDataCategory as $data)
                                        <option value="{{ $data->MD_EQUIPMENT_CATEGORY_ID_INT }}" {{ $dataPromo->MD_EQUIPMENT_CATEGORY_CHAR == $data->MD_EQUIPMENT_CATEGORY_ID_INT ? "selected" : "" }}>
                                            {{ $data->MD_EQUIPMENT_CATEGORY_DESC_CHAR }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Description <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_DESC" class="form-control" id="TXT_DESC" placeholder="Enter Description" value="{{ $dataPromo->DESC_CHAR }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Free Qty <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_EQUIPMENT_FREE" class="form-control" id="TXT_EQUIPMENT_FREE" placeholder="Enter Equipment Free" value="{{ $dataPromo->QTY_FREE_INT }}" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Discount (%) <span style="color: red;">*</span></label>
                                    <input type="text" onkeypress="return isNumberWithDecimal(event)" name="TXT_DISCOUNT_PERCENT" class="form-control" id="TXT_DISCOUNT_PERCENT" placeholder="Enter Discount %" value="{{ (float) $dataPromo->DISCOUNT_PERCENT_FLOAT }}" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Discount Nominal <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_DISCOUNT_NOMINAL" class="form-control" id="TXT_DISCOUNT_NOMINAL" placeholder="Enter Discount Nominal" value="{{ (float) $dataPromo->DISCOUNT_NOMINAL_FLOAT }}" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Start Promo <span style="color: red;">*</span></label>
                                    <input type="datetime-local" name="TXT_START_PROMO" class="form-control" id="TXT_START_PROMO" placeholder="Enter Start Promo" value="{{ $dataPromo->START_PROMO_DATE }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>End Promo <span style="color: red;">*</span></label>
                                    <input type="datetime-local" name="TXT_END_PROMO" class="form-control" id="TXT_END_PROMO" placeholder="Enter End Promo" value="{{ $dataPromo->END_PROMO_DATE }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Payment Method <span style="color: red;">*</span></label>
                                    <select id="DDL_PAYMENT_METHOD" name="DDL_PAYMENT_METHOD" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        <option value="ALL" {{ $dataPromo->PAYMENT_METHOD_CHAR == "ALL" ? "selected" : "" }}>ALL</option>
                                        @foreach($ddlDataPaymentMethod as $data)
                                        <option value="{{ $data->PAYMENT_METHOD_ID_INT }}" {{ $dataPromo->PAYMENT_METHOD_CHAR == $data->PAYMENT_METHOD_ID_INT ? "selected" : "" }}>
                                            {{ $data->PAYMENT_METHOD_DESC_CHAR }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Minimal Qty <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_MIN_QTY" class="form-control" id="TXT_MIN_QTY" placeholder="Enter Minimal Qty" value="{{ $dataPromo->MIN_QTY }}" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Minimal Payment <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_MIN_PAYMENT" class="form-control" id="TXT_MIN_PAYMENT" placeholder="Enter Minimal Payment" value="{{ (float) $dataPromo->MIN_PAYMENT }}" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Maximal Transaction Number <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_MAX_TRX_NUM" class="form-control" id="TXT_MAX_TRX_NUM" placeholder="Enter Maximal Transaction Number" value="{{ $dataPromo->MAX_TRX_NUMBER }}" required>
                                    <small><span style="color: red;"><b><i>Jika Tidak Ada Isi Dengan 0</i></b></span></small>
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