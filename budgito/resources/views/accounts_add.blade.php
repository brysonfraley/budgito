@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <form name="form_addAccount" id="form_addAccount" method="post" action="{{ url('accounts') }}" class="">
            <input type="hidden" name="_token" value="{{ \Session::token() }}" />
            <div class="form-group">
                <label class="control-label" for="account_type">Account Type</label>
                <div class="controls">
                    <select name="account_type" id="account_type" class="form-control" aria-describedby="account_type_helpBlock">
                        <option selected="selected" value="">Please select...</option>
                        <option value="Checkings">Checkings</option>
                        <option value="Savings">Savings</option>
                    </select>
                    <span id="account_type_badge" class="fieldFeedback badge"></span><span id="account_type_helpBlock" class="helpBlock"></span>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label" for="name">Account Name</label>
                <div class="controls">
                    <input type="text" name="name" id="name" class="form-control" placeholder="Account Name" aria-describedby="name_helpBlock" />
                    <span id="name_badge" class="fieldFeedback badge"></span><span id="name_helpBlock" class="helpBlock"></span>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label" for="balance">Current Balance</label>
                <div class="controls">
                    <input type="text" name="balance" id="balance" class="form-control" placeholder="Current Balance" aria-describedby="balance_helpBlock" />
                    <span id="balance_badge" class="fieldFeedback badge"></span><span id="balance_helpBlock" class="helpBlock"></span>
                </div>
            </div>

            <noscript><input type="button" value="Submit" disabled="disabled" /><br /><span class="reqField">Javascript must be enabled to use this form.</span></noscript>
            <script type="text/javascript" language="javascript">document.write('<input type="submit" name="submit" class="btn btn-primary" id="btnSubmit" value="Add Account" />')</script>
        </form>
    </div>
</div>

@endsection
