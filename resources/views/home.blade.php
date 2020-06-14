@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <p>Total Last Close Portfolio Price: <span class="money">{{$lastClose}} $USD</span></p>
            <p>Total Current Portfolio Price: <span class = "money">{{$currentClose}} $USD</span></p>
            <p>Change From Last Close: <span class = "money">{{$change}}%</span></p>
            <div class="card">
                <div class="card-header">
                    <h3>Dashboard</h3>
                    <a href="{{ url('/Buyin') }}">Buy</a>
                    <a href="{{ url('/Sellout') }}">Sell</a>
                    <a href="{{ url('/StockQuote') }}">Stock Quote</a>
                </div>
            </div>
            <table class="table-hover table-light table-bordered">
                <tr>
                    <td>Company</td><td>Ticker</td>
                    <td>Native Currency</td>
                    <td>Stocks Owned</td>
                    <td>Price Per Stock</td>
                </tr>
                @foreach($all_portfolios as $items)
                    <tr>
                        <td>{{ $items->company }}</td>
                        <td>{{ $items->ticker }}</td>
                        <td>{{ $items->native_currency }}</td>
                        <td>{{ $items->amount }}</td>
                        <td>{{ $items->current_price }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection
