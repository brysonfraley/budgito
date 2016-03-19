@extends('layouts.master')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h2>{{ $pageTitle }}</h2>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <h3>Category Breakdown</h3>
        <div class="filter_group">	
            <label class="control-label" for="reportrange">Date Range</label>
            <div id="reportrange">
                <i class="glyphicon glyphicon-calendar fa fa-calendar" style="top:0px;"></i>&nbsp;
                <span>Please select...</span><b class="fa fa-caret-down"></b>
            </div>
        </div>
        <div id="category_breakdown"></div>
    </div>
</div>
<script type="text/javascript">
    var currDate = moment();
    var firstTransDate = "2015-08-01";

    $('#reportrange').daterangepicker({
        locale: {
            format: 'YYYY-MM-DD'
        },
        //startDate: dfltStartDate,
        endDate: currDate,
        maxDate: currDate,
        opens: "right",
        ranges: {
         'Today': [currDate, currDate],
         'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
         'Last 7 Days': [moment().subtract(6, 'days'), currDate],
         'Last 30 Days': [moment().subtract(29, 'days'), currDate],
         'This Month': [moment().startOf('month'), moment().endOf('month')],
         'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
         'This Year': [moment().startOf('year'), currDate],
         'Last Year': [moment().startOf('year').subtract(1, 'year'), moment().startOf('year').subtract(1, 'days')],
         'All': [firstTransDate, currDate]
        }
    },
    function(start, end, label) {
            var startFormatted = start.format('YYYY-MM-DD');
            var endFormatted = end.format('YYYY-MM-DD');
            $('#reportrange span').html(startFormatted + ' - ' + endFormatted);

            filterTransactions(startFormatted, endFormatted);
    });

    function filterTransactions(start, end) {
        //var ajax_load="<img src='img/loading.gif' id='loadingImg' \/>";
        //$("#Category_2_badge").html(ajax_load);
        $.ajax({
            cache:false,
            type:'POST',
            url:'{{ url("../data/transactions") }}',
            dataType:'json',
            data:"startDate="+start+"&endDate="+end+"&accountName={{ $accountNameEncoded }}&_token={{ \Session::token() }}",
            success:function(transactions){
                console.log(transactions);
                // empty the transaction breakdown array for sorting;
                //transBrkdwnFiltered_arr = [];

                // setup the header for transactions table
                var breakdown_tbl = "<table class='cat-breakdown-tbl table table-hover table-bordered'><thead><tr><th class='hdr_cat1' data-col-name='Category_1_Name' data-col-datatype='str' data-col-sortorder=''>Category 1<span class='sorter fa'></th><th class='hdr_not' data-col-name='Number_Of_Transactions' data-col-datatype='int' data-col-sortorder=''>Number of Transactions<span class='sorter fa'></th><th class='hdr_ta' data-col-name='Total_Amount' data-col-datatype='flt' data-col-sortorder='desc'>Total Amount<span class='sorter fa fa-caret-down'></span></th></tr></thead><tbody class='cat-breakdown-tblbody'>";

                var key_index = 0; // objects have no order; will use this counter to index transactions in the right sort order 0, 1, 2, etc)
                // loop through each key in transactions object (key=0, 1, 2, 3)
                for (var trans_key in transactions) {
                    breakdown_tbl = breakdown_tbl + "<tr>";	// each key points to a new row/transaction; setup new row in table;
                    var currTrans = transactions[key_index];	// get the transaction/row obj at the current key/index
                    //transBrkdwnFiltered_arr.push(currTrans);	// store the transaction/row obj in an array so we can re-use for client-side sorting;
                    var hdr_count = 0;	// setup a counter to identify the first header/column which recieves a triangle-right icon
                    // loop through the headers/columns of the current transaction/row
                    for (var trans_hdr in currTrans) {
                        // 
                        if (key_index === 0) {

                        }
                        if (hdr_count === 0) {	// first header/column recieves triangle-right icon
                            breakdown_tbl = breakdown_tbl + "<td><span class='fa fa-caret-right'></span>" + currTrans[trans_hdr] + "</td>";
                        }
                        else {	// else, others recieve normal markup
                            breakdown_tbl = breakdown_tbl + "<td>" + currTrans[trans_hdr] + "</td>";
                        }
                        hdr_count += 1;	// increment header counter
                    }
                    breakdown_tbl = breakdown_tbl + "</tr>";	// current row/transaction finished; end row in table;
                    key_index += 1;
                }
                    breakdown_tbl = breakdown_tbl + "</tbody></table>";	// table content finished; end tags;

                    $('#category_breakdown').html(breakdown_tbl);	// set table to div tag;
            }
        });
            //$("#Category_2_badge").html("");
    }
//
//    function transBrkdwn_sort(col_name, col_datatype, col_sortorder) {
//            return transBrkdwnFiltered_arr.sort(function(a, b) {
//                    if (col_datatype === "int") {	// datatype is a integer number
//                            if (col_sortorder === "asce") {	// order is ascending
//                                    return parseInt(a[col_name]) - parseInt(b[col_name]);
//                            }
//                            else {	// else order is descending
//                                    return parseInt(b[col_name]) - parseInt(a[col_name]);
//                            }
//                    }
//                    else if (col_datatype === "flt") {	// datatype is a floating point number
//                            if (col_sortorder === "asce") {	// order is ascending
//                                    return parseFloat(a[col_name]) - parseFloat(b[col_name]);
//                            }
//                            else {	// else order is descending
//                                    return parseFloat(b[col_name]) - parseFloat(a[col_name]);
//                            }
//                    }
//                    else {	// else datatype is a string
//                            if (col_sortorder === "asce") {	// order is ascending
//                                    return a[col_name] > b[col_name];
//                            }
//                            else {	// else order is descending
//                                    return b[col_name] > a[col_name];
//                            }
//                    }
//            });
//    }
//
//
//    $("#trans_breakdown").on("click", "th", function(e) {
//            var col_name = $(this).attr("data-col-name");
//            var col_datatype = $(this).attr("data-col-datatype");
//            var col_sortorder = $(this).attr("data-col-sortorder");
//
//            $(".hdr_cat1").attr("data-col-sortorder", "");
//            $(".hdr_not").attr("data-col-sortorder", "");
//            $(".hdr_ta").attr("data-col-sortorder", "");
//
//            $(".hdr_cat1 > span").removeClass("fa-caret-up");
//            $(".hdr_not > span").removeClass("fa-caret-up");
//            $(".hdr_ta > span").removeClass("fa-caret-up");
//
//            $(".hdr_cat1 > span").removeClass("fa-caret-down");
//            $(".hdr_not > span").removeClass("fa-caret-down");
//            $(".hdr_ta > span").removeClass("fa-caret-down");
//
//            if (col_sortorder === "desc") {
//                    transBrkdwn_sort(col_name, col_datatype, "asce");
//                    $(this).attr("data-col-sortorder", "asce");
//                    $('span', this).addClass("fa-caret-up");
//            }
//            else {
//                    transBrkdwn_sort(col_name, col_datatype, "desc");
//                    $(this).attr("data-col-sortorder", "desc");
//                    $('span', this).addClass("fa-caret-down");
//            }
//            var transBrkdwnTbodySorted = "";
//            for (var i=0; i<transBrkdwnFiltered_arr.length; i++) {
//                    var currTrans = transBrkdwnFiltered_arr[i];
//                    transBrkdwnTbodySorted = transBrkdwnTbodySorted + "<tr><td><span class='fa fa-caret-right'></span>" + currTrans["Category_1_Name"] + "</td><td>" + currTrans["Number_Of_Transactions"] + "</td><td>" + currTrans["Total_Amount"] + "</td></tr>";
//            }
//            $('#trans_breakdown > table > tbody').html(transBrkdwnTbodySorted);
//    });

</script>
@endsection
