@extends('layouts.app')

@section('content')
    {!! Form::open(['action' => 'BuyinsController@store', 'method' => 'POST']) !!}
    @csrf
    <div class="form-check">
        <h1 class="thead-dark">Buy Stock</h1>
        {{Form::label('ticker', 'Select the ticker you wish to buy')}}
        @if(isset($tickerSelected))
            {{Form::text('ticker',$tickerSelected, ['class' => 'form-control', 'placeholder' => 'Ticker'])}}
        @else
            {{Form::text('ticker', '', ['class' => 'form-control', 'placeholder' => 'Ticker'])}}
        @endif
        {{Form::label('amount', 'Amount of stock you wish to buy')}}
        {{Form::number('amount', '', ['class' => 'form-control', 'placeholder' => '#', 'type' => 'number', 'min' => 1])}}
        <br>
        {{Form::submit('Submit', ['class'=>'btn btn-primary'])}}
    </div>
    {!! Form::close() !!}
@endsection
