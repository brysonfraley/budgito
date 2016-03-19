@extends('layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <h2>{{ $pageTitle }}</h2>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <table class='table table-bordered table-striped'>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transaction Type</th>
                    <th>Amount</th>
                    <th>Category 1</th>
                    <th>Category 2</th>
                    <th>Merchant</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction["date"] }}</td>
                        <td>{{ $transaction["transaction_category2"]["transaction_category1"]["transaction_type"]["name"] }}</td>
                        <td>{{ $transaction["amount"] }}</td>
                        <td>{{ $transaction["transaction_category2"]["transaction_category1"]["name"] }}</td>
                        <td>{{ $transaction["transaction_category2"]["name"] }}</td>
                        <td>{{ $transaction["merchant"] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
