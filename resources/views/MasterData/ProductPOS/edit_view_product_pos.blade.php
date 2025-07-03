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
    Form Edit Product POS - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Edit Product POS
@endsection

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('edit_product_pos') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <input type="hidden" name="TXT_ID" class="form-control" id="TXT_ID" value="{{ $dataProductPOS->MD_PRODUCT_POS_ID_INT }}" required>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Category <span style="color: red;">*</span></label>
                                    <select id="DDL_CATEGORY" name="DDL_CATEGORY" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        @foreach($ddlProductPOSCategory as $data)
                                        <option value="{{ $data->MD_PRODUCT_POS_CATEGORY_ID_INT }}" {{ $dataProductPOS->MD_PRODUCT_POS_CATEGORY_ID_INT == $data->MD_PRODUCT_POS_CATEGORY_ID_INT ? "selected" : "" }}>
                                            {{ $data->DESC_CHAR }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Product Name <span style="color: red;">*</span></label>
                                    <input type="text" name="TXT_NAME" class="form-control" id="TXT_NAME" placeholder="Enter Product Name" value="{{ $dataProductPOS->NAMA_PRODUCT }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Price (Include PB1 & PPH) <span style="color: red;">*</span></label>
                                    <input type="number" name="TXT_PRICE" class="form-control" id="TXT_PRICE" placeholder="Enter Price" value="{{ (float) $dataProductPOS->HARGA_SATUAN_FLOAT }}" required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>PB1 (%) <span style="color: red;">*</span></label>
                                    <input type="text" onkeypress="return isNumberWithDecimal(event)" name="TXT_PB1" class="form-control" id="TXT_PB1" placeholder="Enter PB1 (%)" value="{{ (float) $dataProductPOS->PB1_PERCENT_INT }}" required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>PPH (%) <span style="color: red;">*</span></label>
                                    <input type="text" onkeypress="return isNumberWithDecimal(event)" name="TXT_PPH" class="form-control" id="TXT_PPH" placeholder="Enter PPH (%)" value="{{ (float) $dataProductPOS->PPH_PERCENT_INT }}" required>
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