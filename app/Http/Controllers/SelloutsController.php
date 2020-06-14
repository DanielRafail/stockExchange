<?php

namespace App\Http\Controllers;

use App\Http\Objects\ConversionApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Objects\StockInfo;

class SelloutsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @author George Ilias and Nick Trinh
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $all_portfolios = DB::select('SELECT * FROM portfolios WHERE email = ?', [$user->email]);
        $stock = new StockInfo();

        // Update stock information from the database to the latest changes
        foreach ($all_portfolios as $portfolio) {
            $tickerInfo = $stock->getTickerinfo($portfolio->ticker);
            $new_price = $tickerInfo['current_price'];
            $new_total = $tickerInfo['current_price'] * $portfolio->amount;
            $change = $tickerInfo['change'];
            $close_yesterday = $tickerInfo['close_yesterday'];

            // UPDATING COMPANY AND NATIVE TEMPORARILY since we are adding fake data manually and they do not represent actual tickers
            $updateResults = DB::update('UPDATE portfolios SET current_price = ?, total = ?, change = ?, company = ?, native_currency = ?, close_yesterday = ? WHERE ticker = ?',
                [
                    $new_price,
                    $new_total,
                    $change,
                    $tickerInfo['company'],
                    $tickerInfo['native_currency'],
                    $close_yesterday,
                    $portfolio->ticker
                ]);

        }

        $all_portfolios = DB::select('SELECT * FROM portfolios WHERE email = ?', [$user->email]);

        $error = array();
        // Return view with updated view
        return $this->returnToSellout($user, $all_portfolios, $error);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $portfolio = DB::select('SELECT * FROM portfolios WHERE email = ? AND ticker = ?', [$user->email, $request->ticker]);
        $all_portfolios = DB::select('SELECT * FROM portfolios WHERE email = ?', [$user->email]);
        $wallets = DB::select('SELECT * FROM wallets WHERE email = ?', [$user->email]);
        $error = array();

        // Validate user input
        $request->validate([
            'amount' => 'required|integer|min:1',
            'ticker' => 'required',

        ]);

        // Check user requested amount exceeds what was found in the database
        if ($portfolio[0]->amount < $request['amount']) {
            // User tried to sell more than what they owned so return unchanged view
            $error['message'] = 'Attempted to sell more than amount held';
            return $this->returnToSellout($user, $all_portfolios, $error);

        // Check if user has enough money to pay the transaction fee (10$)
        } else if ($wallets[0]->price < 10) {
            // User did not have enough to pay the fee so return unchanged view
            $error['message'] = 'Insufficient funds to pay transaction fee of 10$ USD';
            return $this->returnToSellout($user, $all_portfolios, $error);

        } else {
            $amountToSell = $request['amount'];
            $this->updateDatabase($user, $portfolio, $amountToSell);
            $all_portfolios = DB::select('SELECT * FROM portfolios WHERE email = ?', [$user->email]);
            // Return view with updated view
            return $this->returnToSellout($user, $all_portfolios, $error);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function returnToSellout($user, $all_portfolios, $error) {
        $returnHome = DB::select('SELECT * FROM wallets WHERE email = ?',[$user->email]);
        $date = DB::select('select created_at from users where email =?',[$user->email]);

        if (isset($error['message'])) {
            return view('sellout')->with([
                'all_portfolios' => $all_portfolios,
                'returnHome' => $returnHome[0]->price,
                'date_create' => $date[0]->created_at,
                'error' => $error
            ]);
        } else {
            return view('sellout')->with([
                'all_portfolios' => $all_portfolios,
                'returnHome' => $returnHome[0]->price,
                'date_create' => $date[0]->created_at
            ]);
        }
    }

    private function updateDatabase($user, $portfolio, $amount) {
        // Update Portfolio database with new amount of stocks held
        $newAmount = $portfolio[0]->amount - $amount;
        $portfolioRowsUpdated = DB::update('UPDATE portfolios SET amount = ? WHERE email = ? AND ticker = ?', [$newAmount, $user->email, $portfolio[0]->ticker]);

        // Keep a reference to the stock's price and native currency to be used in the conversion api as both of these will not be available after deleting the records
        $ticker_native_currency = $portfolio[0]->native_currency;
        $ticker_price = $portfolio[0]->current_price;

        // Delete any stocks that the user does not own, meaning the user sold all of one stock that he/she owned
        $rowsDeleted = DB::delete('DELETE FROM portfolios WHERE email = ? AND amount = ?', [$user->email, 0]);

        // Re-query the database for changes to be added to the view
        $wallets = DB::select('SELECT * FROM wallets WHERE email = ?', [$user->email]);

        // Add values (in USD) to the user's wallet based on what and how much was sold
        $converter = new ConversionApi();
        $fundsAdded = $amount * ($ticker_price * $converter->convertToUSD($ticker_native_currency));
        $fee = 10;
        $walletRowsUpdated = DB::update('UPDATE wallets SET price = ? WHERE email = ?', [$wallets[0]->price - $fee + $fundsAdded, $user->email]);

    }
}
