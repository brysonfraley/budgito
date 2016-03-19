@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2>{{ $pageTitle }}</h2>
    </div>
</div>
<form id='form-budgets' name='form-budgets' class='form-inline' method="post" action="{{ url($accountNameEncoded . "/budgets") }}" class="">
    <input type="hidden" name="_token" value="{{ \Session::token() }}" />
    <div class="row">
        <div class="col-xs-12">
            <button type="submit" class="btn btn-success pull-right spacing-bottom-20">Save Budgets</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class='panel panel-default panel-budgets'>
                <div class='panel-body'>
                    <div class='row'>
                        <div class='col-lg-6'>
                            <h4 class='panel-title' style="margin-left:20px;"><strong>Category</strong></h4>
                        </div>
                        <div class='col-lg-6'>
                            <h4 class="panel-title"><strong>Monthly Amount</strong></h4>
                        </div>
                    </div>
                </div>
            </div>
            @foreach ($categoryBudgets[0]["transaction_category1s"] as $category1)
                <div class='panel panel-default panel-budgets'>
                    <div class='panel-heading' id="heading_{{ $category1["id"] }}" data-target='#collapseCat1_{{ $category1["id"] }}' 
                         role='button' data-toggle='collapse' aria-expanded='false' 
                         aria-controls='collapseCat1_{{ $category1["id"] }}'>
                        <div class='row'>
                            <div class='col-lg-6'>
                                <i class='fa fa-angle-right fa-fw' id="angle_{{ $category1["id"] }}"></i>
                                <h4 class='panel-title'>{{ $category1["name"] }}</h4>
                            </div>
                            <div class='col-lg-6'>
                                <h4 class="panel-title">$&nbsp;{{ number_format($category1["budget_amount"], 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    <div class='panel-body collapse' id='collapseCat1_{{ $category1["id"] }}' aria-expanded='false'>
                        <div class='list-group'>
                            <!--<div class='list-group-item'>
                                <div class='row'>
                                    <div class='col-lg-6'>
                                        <h5><strong>Category 2</strong></h5>
                                    </div>
                                    <div class='col-lg-6'>
                                        <h5><strong>Monthly Amount</strong></h5>
                                    </div>
                                </div>
                            </div>-->
                            @foreach ($category1["transaction_category2s"] as $category2) 
                                <div class='list-group-item'>
                                    <div class='row'>
                                        <div class='col-lg-6'>{{ $category2["name"] }}</div>
                                        <div class='col-lg-6 form-group'>
                                            <div class='input-group'>
                                                <div class='input-group-addon'>$</div>
                                                <input type='text' class='form-control' id='category2_{{ $category2["id"] }}' 
                                                       name='category2_{{ $category2["id"] }}' placeholder='0'
                                                       value='{{ empty($category2["budgets"]) ? "" : $category2["budgets"]["amount"] }}' />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <button type="submit" class="btn btn-success pull-right spacing-top-20">Save Budgets</button>
        </div>
    </div>
</form>
<script type="text/javascript">
    // update caret/angle when a category1 is clicked;
    $('.panel-heading').on('click', function(){
        var headingId = $(this).attr("id");
        var headingIdNumber = headingId.split("_")[1];
        var angleElem = $('#angle_'+headingIdNumber);
        if (angleElem.hasClass("fa-angle-right")) {
            angleElem.removeClass("fa-angle-right");
            angleElem.addClass("fa-angle-down");
        }
        else {
            angleElem.removeClass("fa-angle-down");
            angleElem.addClass("fa-angle-right");
        }
    });
</script>
@endsection
