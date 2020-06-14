@extends('layouts.app')

@section('content')
    <h1>My Stocks</h1>
    <div class="panel-group" id="accordion">
        @if (isset($all_portfolios) && sizeof($all_portfolios) > 0)
            @foreach ($all_portfolios as $index => $portfolio)
                @if ($portfolio->amount > 0)

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse-{{ $index }}">{{ $portfolio->company }}</a>
                        </h4>
                    </div>
                    <div id="collapse-{{ $index }}" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <table class="table table-striped">
                                <tr>
                                    <th>Amount in portfolio: </th>
                                    <td>{{ $portfolio->amount }}</td>
                                </tr>
                                <tr>
                                    <th>Current value: </th>
                                    <td>{{ $portfolio->current_price }} {{ $portfolio->native_currency }}</td>
                                </tr>
                                <tr>
                                    <th>Last close value: </th>
                                    <td>{{ $portfolio->close_yesterday }} {{ $portfolio->native_currency }}</td>
                                </tr>
                                <tr>
                                    <th>Total worth: </th>
                                    <td>{{ $portfolio->total }} {{ $portfolio->native_currency }}</td>
                                </tr>
                                <tr>
                                    <th>Date purchased: </th>
                                    <td>{{ $portfolio->purchase_date }}</td>
                                </tr>

                                <tr>
                                    <th>Price difference since last close: </th>
                                    <td>{{ $portfolio->change }} %</td>
                                </tr>
                            </table>

                            {!! Form::open(['action' => ['SelloutsController@update', $portfolio->amount], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
                            @csrf
                            <div class="form-group">
                                {{Form::label('amount', 'Amount of stock you wish to sell')}}
                                {{Form::text('amount', '', ['class' => 'form-control', 'placeholder' => '# of Stock'])}}
                            </div>
                            {{Form::hidden('_method','PUT')}}
                            {{Form::hidden('ticker',$portfolio->ticker)}}
                            {{Form::submit('Sell', ['class'=>'btn btn-primary'])}}
                            {!! Form::close() !!}
                            <hr/>
                        </div>

                    </div>
                </div>
                @endif
            @endforeach
        @else
            <p>You do not own any stocks.</p>
        @endif
            <br />
            <a href="{{ URL::previous() }}">Back</a>
    </div>
@endsection
