@extends('layouts.master')

@section('content')

<div class="row spacing-bottom">
    <div class="col-xs-12">
        <div class="pull-right">
            <a class="btn btn-success" role="button" href="{{ url("accounts/add") }}">Add New Account</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        @foreach ($accounts as $account)
            <div class="panel panel-default">
                <div class="panel-heading">{{ $account["name"] }}</div>

                <div class="panel-body">
                    <table class="table">
                        <thead>
                            <th>Account Type</th>
                            <th>Starting Balance</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $account["account_type"]["name"] }}</td>
                                <td>{{ $account["balance"] }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <a class="btn btn-primary" role="button" href="{{ url(urlencode($account['name'])) }}">Select</a>
                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection
