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
    Form Edit Holiday - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Edit Holiday
@endsection

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('edit_holiday') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <input type="hidden" name="HOLIDAY_ID_INT" class="form-control" id="HOLIDAY_ID_INT" value="{{ $dataHoliday->HOLIDAY_ID_INT }}" required>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Holiday Name <span style="color: red;">*</span></label>
                                    <input type="text" name="HOLIDAY_NAME" class="form-control" id="HOLIDAY_NAME" placeholder="Enter Holiday Name" value="{{ $dataHoliday->HOLIDAY_NAME }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Holiday Date <span style="color: red;">*</span></label>
                                    <input type="date" name="HOLIDAY_DATE" class="form-control" id="HOLIDAY_DATE" value="{{ $dataHoliday->HOLIDAY_DATE }}" required>
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