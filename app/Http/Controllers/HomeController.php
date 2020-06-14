<?php
/**
 * Controller for the home page, using
 */

namespace App\Http\Controllers;

use App\Http\Objects\StockInfo;
use Illuminate\Http\Request;
use App\Http\Objects\ConversionApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @author George Ilias
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @author George Ilias
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stockInfo = new StockInfo();
        $convert = new ConversionApi();
        $user = Auth::user();

        $returnHome = DB::select('select price from wallets where email = :email', ['email' => $user->email]);
        $date = DB::select('select created_at from users where email = :email', ['email' => $user->email]);
        $portfolio = DB::select('select * from portfolios where email = :email', ['email' => $user->email]);

        $totalLastClose = 0;
        $totalCurrentPrice = 0;

        $revenueAgencyChange = 0;


        foreach ($portfolio as $item) {
            $tickerValue = $stockInfo->getTickerinfo($item->ticker);

            $multiplier = $convert->convertToUSD($item->native_currency);
            $currentValueUSDLastClose = $multiplier * $tickerValue['close_yesterday'];
            $currentValueUSDCurrent = $multiplier * $tickerValue['current_price'];
            $totalLastClose += $item->amount * $currentValueUSDLastClose;
            $totalCurrentPrice += $item->amount * $currentValueUSDCurrent;

        }

        if ($totalCurrentPrice != 0 && $totalLastClose != 0) {

            $revenueAgencyChange = $totalLastClose / $totalCurrentPrice;
        }

        return view('home')->with(['returnHome' => $returnHome[0]->price, 'date_create' => $date[0]->created_at, 'all_portfolios' => $portfolio, 'lastClose' => $totalLastClose, 'currentClose' => $totalCurrentPrice, 'change' => $revenueAgencyChange]);

    }
}
