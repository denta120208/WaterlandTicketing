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
    Form Edit Payment Method - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Edit Payment Method
@endsection

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('edit_payment_method') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <input type="hidden" name="TXT_ID" class="form-control" id="TXT_ID" value="{{ $dataPaymentMethod->PAYMENT_METHOD_ID_INT }}" required>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Category <span style="color: red;">*</span></label>
                                    <select id="DDL_CATEGORY" name="DDL_CATEGORY" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        @foreach($ddlPaymentMethodCategory as $data)
                                        <option value="{{ $data->PAYMENT_METHOD_CATEGORY_ID_INT }}" {{ $data->PAYMENT_METHOD_CATEGORY_ID_INT == $dataPaymentMethod->PAYMENT_METHOD_CATEGORY_ID_INT ? "selected" : "" }}>
                                            {{ $data->CATEGORY_NAME_CHAR }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Description <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_DESC" class="form-control" id="TXT_DESC" placeholder="Enter Description" value="{{ $dataPaymentMethod->PAYMENT_METHOD_DESC_CHAR }}" required>
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