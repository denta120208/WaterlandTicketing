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
    Form Add New Revenue Settings - <b>{{session('current_project_char')}}</b>
@endsection

@section('header_title')
    Form Add New Revenue Settings
@endsection

@section('content')
<div class="container-xxl">
    <div class="row justify-content-center">
        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('save_revenue_settings') }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm">
                                <div class="form-group">
                                    <label>Project <span style="color: red;">*</span></label>
                                    <select id="PROJECT_NO_CHAR" name="PROJECT_NO_CHAR" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                        @foreach($ddlProject as $data)
                                        <option value="{{ $data->PROJECT_NO_CHAR }}" {{ $dataProject->PROJECT_NO_CHAR == $data->PROJECT_NO_CHAR ? "selected" : "" }}>
                                            {{ $data->PROJECT_NAME }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="form-group">
                                    <label>Revenue Date <span style="color: red;">*</span></label>
                                    <input type="date" class="form-control" name="TRANS_REVENUE_DATE" id="TRANS_REVENUE_DATE" max="{{ $yesterdayDate }}" required />
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="form-group">
                                    <label>Category Revenue <span style="color: red;">*</span></label>
                                    <select id="MD_CATEGORY_REVENUE_ID_INT" name="MD_CATEGORY_REVENUE_ID_INT" class="form-control select2" style="width: 100%;" required>
                                        <option value="">--- Not Selected ---</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm">
                                <div class="form-group">
                                    <label>Actual Amount <span style="color: red;">*</span></label>
                                    <input type="text" onkeypress="return isNumberWithDecimal(event)" class="form-control" name="ACTUAL_AMT" id="ACTUAL_AMT" placeholder="Input Actual Amount" value="0" required />
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="form-group">
                                    <label>Budget Amount <span style="color: red;">*</span></label>
                                    <input type="text" onkeypress="return isNumberWithDecimal(event)" class="form-control" name="BUDGET_AMT" id="BUDGET_AMT" placeholder="Input Budget Amount" value="0" required />
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="BTN_SUBMIT" name="BTN_SUBMIT" class="btn btn-primary float-right">Save</button>
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

        $('select[id="PROJECT_NO_CHAR"]').on('change', function() {
            var subID = $(this).val();
            
            if(!isEmpty(subID)) {
                $.ajax({
                    url: '/revenue_settings_get_category_revenue_by_project/' + subID,
                    type: "GET",
                    dataType: "json",
                    success: function (data) {
                        $('select[name="MD_CATEGORY_REVENUE_ID_INT"]').empty().append('<option value="">--- Not Selected ---</option>');
                        $.each(data.dataCategoryRevenue, function(index, item) {
                            $('select[name="MD_CATEGORY_REVENUE_ID_INT"]').append("<option value='" + item.MD_CATEGORY_REVENUE_ID_INT + "'>" + item.MD_CATEGORY_REVENUE_NAME + "</option>");
                        });
                        $('select[name="MD_CATEGORY_REVENUE_ID_INT"]').trigger("change");
                    },
                    error: function() {
                        alert('Error, Please contact Administrator!');
                    }
                });
            }
            else {
                $('select[name="MD_CATEGORY_REVENUE_ID_INT"]').empty().append('<option value="">--- Not Selected ---</option>');
                $('select[name="MD_CATEGORY_REVENUE_ID_INT"]').trigger("change");
            }
        });

        $('#TRANS_REVENUE_DATE').on('change', function() {
            var maxDate = "<?php echo $yesterdayDate; ?>";
            var selectedDate = $(this).val();
            if (selectedDate > maxDate) {
                $(this).val(null);
            }
            $('select[id="MD_CATEGORY_REVENUE_ID_INT"]').trigger("change");
        });

        $('select[id="MD_CATEGORY_REVENUE_ID_INT"]').on('change', function() {
            var subID = $(this).val();
            var revDate = $("#TRANS_REVENUE_DATE").val();

            if(!isEmpty(subID)) {
                if(isEmpty(revDate)) {
                    alert("Revenue Date Cannot Be Empty!");
                }
                else {
                    $.ajax({
                        url: '/revenue_settings_get_category_revenue_source/' + subID + '/' + revDate,
                        type: "GET",
                        dataType: "json",
                        success: function (data) {
                            var FIELD_ACTUAL = data.FIELD_ACTUAL;
                            var FIELD_ACTUAL_VALUE = data.FIELD_ACTUAL_VALUE;
    
                            $('#ACTUAL_AMT').val(FIELD_ACTUAL_VALUE);
    
                            if(FIELD_ACTUAL == "DISABLED") {
                                $('#ACTUAL_AMT').prop('readonly', true);
                            }
                            else {
                                $('#ACTUAL_AMT').prop('readonly', false);
                            }
                        },
                        error: function() {
                            alert('Error, Please contact Administrator!');
                        }
                    });
                }
            }
            else {
                $('#ACTUAL_AMT').prop('readonly', false);
                $('#ACTUAL_AMT').val(0);
            }
        });

        $('select[id="PROJECT_NO_CHAR"]').trigger("change");
    });

    function isNumberWithDecimal(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && ((charCode < 48 && charCode !== 46) || charCode > 57)) {
            return false;
        }
        return true;
    }

    function isEmpty(str) {
        return (!str || str.length === 0 );
    }
</script>
@endsection