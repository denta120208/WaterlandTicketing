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
    Form Edit Category Revenue - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Edit Category Revenue
@endsection

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('edit_category_revenue') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="MD_CATEGORY_REVENUE_ID_INT" value="{{ $dataCategoryRevenue->MD_CATEGORY_REVENUE_ID_INT }}" required>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Category Revenue Name <span style="color: red;">*</span></label>
                                    <input type="text" name="MD_CATEGORY_REVENUE_NAME" class="form-control" id="MD_CATEGORY_REVENUE_NAME" placeholder="Enter Category Revenue Name" value="{{ $dataCategoryRevenue->MD_CATEGORY_REVENUE_NAME }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Category Revenue Source Data <span style="color: red;">*</span></label>
                                    <select id="MD_CATEGORY_REVENUE_SOURCE_ID_INT" name="MD_CATEGORY_REVENUE_SOURCE_ID_INT" class="form-control select2" style="width: 100%;" required>
                                        @foreach($ddlCategoryRevenueSource as $data)
                                        <option value="{{ $data->MD_CATEGORY_REVENUE_SOURCE_ID_INT }}" {{ $dataCategoryRevenue->MD_CATEGORY_REVENUE_SOURCE_ID_INT == $data->MD_CATEGORY_REVENUE_SOURCE_ID_INT ? "selected" : "" }}>
                                            {{ $data->SOURCE_NAME }}
                                        </option>
                                        @endforeach
                                    </select>
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
</script>
@endsection