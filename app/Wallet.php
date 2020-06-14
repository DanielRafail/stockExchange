<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Wallet extends Model
{

    protected $table = 'wallets';

    protected $fillable = [
        'email','price',
    ];



}
