@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2>{{ $pageTitle }}</h2>
    </div>
</div>
<form name="form_addTransaction" id="form_addTransaction" method="post" action="{{ url($accountNameEncoded . "/transactions") }}" class="">
    <input type="hidden" name="_token" value="{{ \Session::token() }}" />
    <div class="row">
        <div class="col-xs-6">
            <div class="form-group">
                <label class="control-label" for="transaction_type">Transaction Type</label>
                <div class="controls">
                    <select name="transaction_type" id="transaction_type" class="form-control" aria-describedby="transaction_type_helpBlock">
                        <option selected="selected" value="">Please select...</option>
                        @foreach ($transactionTypes as $transactionType)
                            <option value="{{ $transactionType['id'] }}">{{ $transactionType['name'] }}</option>
                        @endforeach
                    </select>
                    <span id="transaction_type_badge" class="fieldFeedback badge"></span><span id="transaction_type_helpBlock" class="help-block"></span>
                </div>
            </div>

            <script type="text/javascript">
                $(document).on('change', "#transaction_type", function(event) {
                    event.preventDefault();

                    var pleaseSelectOpt = "<option value='' selected='selected'>Please select...<\/option>\n";

                    // reset dropdown chain
                    $("#category2").html(pleaseSelectOpt);


                    var transTypeSelected = $('#transaction_type').val();

                    if (transTypeSelected) {
                        var dataToSend=$('#transaction_type').serialize();
                        var token = "{{ \Session::token() }}";

                        var ajax_load="<img src='{{ url('img/loading.gif') }}' id='loadingImg' \/>";
                        $("#transaction_type_badge").html(ajax_load);

                        $.ajax({
                            cache:false,
                            type:'POST',
                            url:'{{ url("/data/category1s") }}',
                            dataType:'json',
                            data:dataToSend+"&_token="+token,
                            success:function(result){
                                var category1_opts = "";
                                for (var i=0; i<result.length; i++) {
                                    category1_opts += "<option value='"+result[i]["id"]+"'>"+result[i]["name"]+"<\/option>\n";
                                }
                                $("#category1").html(pleaseSelectOpt+category1_opts);
                            }
                        });
                    }
                    else {
                        $("#category1").html(pleaseSelectOpt);
                    }

                    $("#transaction_type_badge").html("");

                });
            </script>
            <div class="form-group">
                <label class="control-label" for="category1">Category 1</label>
                <div class="controls">
                    <select name="category1" id="category1" class="form-control" aria-describedby="category1_helpBlock">
                        <option selected="selected" value="">Please select...</option>
                    </select>
                    <span id="category1_badge" class="fieldFeedback badge"></span><span id="category1_helpBlock" class="help-block"></span>
                </div>
            </div>

            <script type="text/javascript">
                $(document).on('change', "#category1", function(event) {
                    event.preventDefault();
                    var category1Selected = $('#category1').val();
                    var category2_opts = "<option value='' selected='selected'>Please select...<\/option>\n";

                    if (category1Selected) {
                        var dataToSend=$('#category1').serialize();
                        var token = "{{ \Session::token() }}";

                        var ajax_load="<img src='{{ url('img/loading.gif') }}' id='loadingImg' \/>";
                        $("#category1_badge").html(ajax_load);

                        $.ajax({
                            cache:false,
                            type:'POST',
                            url:'{{ url("/data/category2s") }}',
                            dataType:'json',
                            data:dataToSend+"&_token="+token,
                            success:function(result){
                                for (var i=0; i<result.length; i++) {
                                    category2_opts += "<option value='"+result[i]["id"]+"'>"+result[i]["name"]+"<\/option>\n";
                                }
                                $("#category2").html(category2_opts);
                            }
                        });
                    }
                    else {
                        $("#category2").html(category2_opts);
                    }

                    $("#category1_badge").html("");

                });
            </script>
            <div class="form-group">
                <label class="control-label" for="category2">Category 2</label>
                <div class="controls">
                    <select name="category2" id="category2" class="form-control" aria-describedby="category2_helpBlock">
                        <option selected="selected" value="">Please select...</option>
                    </select>
                    <span id="category2_badge" class="fieldFeedback badge"></span>
                    <span id="category2_helpBlock" class="help-block"></span>
                </div>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label class="control-label" for="date">Transaction Date</label>
                <div class="controls">
                    <div class="input-group date">
                        <input type="text" name="date" class="form-control" id="date" placeholder="Date" aria-describedby="date_helpBlock" />
                        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                    </div>
                    <span id="date_badge" class="fieldFeedback badge"></span><span id="date_helpBlock" class="help-block"></span>
                </div>
            </div>
            <script type="text/javascript">
                // datepicker js
                $('.input-group.date').datepicker({
                    format: "yyyy-mm-dd",
                    todayHighlight: true
                });
            </script>

            <div class="form-group">
                <label class="control-label" for="amount">Amount</label>
                <div class="controls">
                    <input type="text" name="amount" id="amount" class="form-control" placeholder="Amount" aria-describedby="amount_helpBlock" />
                    <span id="amount_badge" class="fieldFeedback badge"></span><span id="amount_helpBlock" class="help-block"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="merchant">Merchant</label>
                <div class="controls">
                    <input type="text" name="merchant" id="merchant" class="form-control" placeholder="Merchant" aria-describedby="merchant_helpBlock" />
                    <span id="merchant_badge" class="fieldFeedback badge"></span><span id="merchant_helpBlock" class="help-block"></span>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="description">Description / Tags</label>
                <div class="controls">
                    <textarea name="description" id="description" class="form-control" placeholder="Description / Tags" aria-describedby="description_helpBlock"></textarea>
                    <span id="description_badge" class="fieldFeedback badge"></span><span id="description_helpBlock" class="help-block"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <button type="submit" class="btn btn-primary">Add Transaction</button>
        </div>
    </div>
</form>

@endsection