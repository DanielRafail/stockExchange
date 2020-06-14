<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PortfoliosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = Auth::user();
        DB::table('portfolios')->insert([
            'company' => 'Alphabet Inc',
            'ticker' => 'GOOGL',
            'amount' => 3,
            'current_price' => lcg_value(),
            'native_currency' => 'CAD',
            'total' => lcg_value(),
            'email' => 'notNick@gmail.com',
            'purchase_date' => Carbon\Carbon::now()->toDateTimeString(),
            'change' => lcg_value(),
            'close_yesterday' => lcg_value()

        ]);

        DB::table('portfolios')->insert([
            'company' => 'ASUSTEK COMPUTE',
            'ticker' => 'ASUUY',
            'amount' => 3,
            'current_price' => lcg_value(),
            'native_currency' => 'CAD',
            'total' => lcg_value(),
            'email' => 'notNick@gmail.com',
            'purchase_date' => Carbon\Carbon::now()->toDateTimeString(),
            'change' => lcg_value(),
            'close_yesterday' => lcg_value()

        ]);

        DB::table('portfolios')->insert([
            'company' => 'Lockheed Martin Corporation',
            'ticker' => 'LMT',
            'amount' => 3,
            'current_price' => lcg_value(),
            'native_currency' => 'CAD',
            'total' => lcg_value(),
            'email' => 'notNick@gmail.com',
            'purchase_date' => Carbon\Carbon::now()->toDateTimeString(),
            'change' => lcg_value(),
            'close_yesterday' => lcg_value()

        ]);

        DB::table('portfolios')->insert([
            'company' => 'Medifirst Solutions Inc',
            'ticker' => 'MFST',
            'amount' => 3,
            'current_price' => lcg_value(),
            'native_currency' => 'CAD',
            'total' => lcg_value(),
            'email' => 'notNick@gmail.com',
            'purchase_date' => Carbon\Carbon::now()->toDateTimeString(),
            'change' => lcg_value(),
            'close_yesterday' => lcg_value()

        ]);

        DB::table('portfolios')->insert([
            'company' => 'iShares Core S&P Mid Cap ETF',
            'ticker' => 'IJH',
            'amount' => 3,
            'current_price' => lcg_value(),
            'native_currency' => 'CAD',
            'total' => lcg_value(),
            'email' => 'notNick@gmail.com',
            'purchase_date' => Carbon\Carbon::now()->toDateTimeString(),
            'change' => lcg_value(),
            'close_yesterday' => lcg_value()

        ]);
    }
}
