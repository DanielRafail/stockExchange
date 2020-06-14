@extends('layouts.app')

@section('content')

    {!! Form::open(['action' => 'StockQuotesController@store', 'method' => 'POST']) !!}
    @csrf
    <div class="form-check">
        <h1 class="thead-dark">Get Stock Quote</h1>
        {{Form::text('ticker', '', ['class' => 'form-control', 'placeholder' => 'ticker'])}}
        <br>
        {{Form::submit('Submit', ['class'=>'btn btn-primary'])}}
    </div>
    {!! Form::close() !!}


    @if(isset($info))
        <div class="form-check">
        <br>
        @if(isset($info['error']))
            <h3 style="color:red;">{{$info['error']}}</h3>
        @else
            <table class="table table-striped">
                <tr>
                    <th>Company: </th>
                    <td>{{$info['company']}}</td>
                </tr>
                <tr>
                    <th>Ticker: </th>
                    <td>{{$info['ticker']}}</td>
                </tr>
                <tr>
                    <th>Price: </th>
                    <td>{{$info['current_price']}}</td>
                </tr>
                <tr>
                    <th>Native Currency: </th>
                    <td>{{$info['native_currency']}}</td>
                </tr>
            </table>
            <a href="/StockQuote/{{$info['ticker']}}" class="btn btn-outline-success">Buy Stock</a>
        @endif
        </div>
    @endif

@endsection


