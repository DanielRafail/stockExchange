<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Portfolio extends Model
{
   protected $table = 'portfolios';

   protected $fillable = [
       'company',
       'ticker',
       'amount',
       'current_price',
       'native_currency',
       'total',
       'email',
       'purchase_date',
       'change',
       'close_yesterday',
   ];

}
