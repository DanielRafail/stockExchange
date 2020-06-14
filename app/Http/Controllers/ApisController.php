<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Objects\ConversionApi;
use Illuminate\Support\Facades\DB;
use App\Http\Objects\StockInfo;

/**
 * Class ApisController contains all the api logic.
 * @author Kajal Bordhon
 * @package App\Http\Controllers
 */
class ApisController extends Controller
{

    /**
     * Return all the stocks in the portfolio.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function allstocks(Request $request)
    {
        $user = auth('api')->user(); //returns null if not valid
        if (!$user) {
            return response()->json(['error' => 'invalid_token'], 401);
        } else {
            $portfolios = DB::select('SELECT * FROM portfolios WHERE email = ?', [$user->email]);
            return response()->json($portfolios, 200);
        }

    }

    /**
     * Cash left in the wallet.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cash(Request $request)
    {
        $user = auth('api')->user(); //returns null if not valid
        if (!$user) {
            return response()->json(['error' => 'invalid_token'], 401);
        } else {
            $price = DB::select('SELECT * FROM wallets WHERE email = ?', [$user->email]);
            $cash = $price[0]->price;
            return response()->json(['cash' => $cash], 200);
        }
    }

    /**
     * Sell a specified stock.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sell(Request $request)
    {
        $user = auth('api')->user(); //returns null if not valid
        if (!$user) {
            return response()->json(['error' => 'invalid_token'], 401);
        } else {
            $portfolio = DB::select('SELECT * FROM portfolios WHERE email = ? AND ticker = ?', [$user->email, $request['ticker']]);
            $wallets = DB::select('SELECT * FROM wallets WHERE email = ?', [$user->email]);

            if (!isset($portfolio[0])) {
                return response()->json(['error' => 'You do not own this company\'s stock'], 400);
            }

            if (!(is_integer($request['amount']) || isset($request['ticker']))) {
                return response()->json(['error' => 'invalid ticker or quantity'], 400);
            }

            // Check user requested amount exceeds what was found in the database
            if ($portfolio[0]->amount < $request['amount']) {
                // User tried to sell more than what they owned.
                return response()->json(['error' => 'invalid ticker or quantity'], 400);

                // Check if user has enough money to pay the transaction fee (10$)
            } else if ($wallets[0]->price < 10) {
                // User did not have enough to pay the fee.
                return response()->json(['error' => 'insufficient cash'], 403);

            } else {
                $amountToSell = $request['amount'];
                $this->updateDatabase($user, $portfolio, $amountToSell);
                // Return cash left
                $price = DB::select('SELECT * FROM wallets WHERE email = ?', [$user->email]);
                $cash = $price[0]->price;
                return response()->json(['cashleft' => $cash], 200);
            }
        }
    }

    /**
     * Buy a specified stock.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buy(Request $request)
    {
        $user = auth('api')->user(); //returns null if not valid
        if (!$user) {
            return response()->json(['error' => 'invalid_token'], 401);
        } else {
            $portfolio = DB::select('SELECT * FROM portfolios WHERE email = ? AND ticker = ?', [$user->email, $request->ticker]);
            $all_portfolios = DB::select('SELECT * FROM portfolios WHERE email = ?', [$user->email]);

            if (!(is_integer($request['amount']) || isset($request['ticker']))) {
                return response()->json(['error' => 'invalid ticker or quantity'], 400);
            }

            if (isset($portfolio[0])) {
                return $this->buyStock($request->ticker, $request->amount, true, $user);
            } else {
                if (sizeof($all_portfolios) >= 5) {
                    $error['message'] = 'Already own 5 and attempting to buy more';
                    return $this->returnToMenu($user, $all_portfolios, $error, null);
                } else {
                    return $this->buyStock($request->ticker, $request->amount, false, $user);
                }
            }
        }
    }


    /**
     * Used by sell api.
     * @param $user
     * @param $portfolio
     * @param $amount
     */
    private function updateDatabase($user, $portfolio, $amount)
    {
        // Update Portfolio database with new amount of stocks held
        $newAmount = $portfolio[0]->amount - $amount;
        DB::update('UPDATE portfolios SET amount = ? WHERE email = ? AND ticker = ?', [$newAmount, $user->email, $portfolio[0]->ticker]);

        // Keep a reference to the stock's price and native currency to be used in the conversion api as both of these will not be available after deleting the records
        $ticker_native_currency = $portfolio[0]->native_currency;
        $ticker_price = $portfolio[0]->current_price;

        // Delete any stocks that the user does not own, meaning the user sold all of one stock that he/she owned
        DB::delete('DELETE FROM portfolios WHERE email = ? AND amount = ?', [$user->email, 0]);

        // Re-query the database for changes to be added to the view
        $wallets = DB::select('SELECT * FROM wallets WHERE email = ?', [$user->email]);

        // Add values (in USD) to the user's wallet based on what and how much was sold
        $converter = new ConversionApi();
        $fundsAdded = $amount * ($ticker_price * $converter->convertToUSD($ticker_native_currency));
        $fee = 10;
        DB::update('UPDATE wallets SET price = ? WHERE email = ?', [$wallets[0]->price - $fee + $fundsAdded, $user->email]);

    }

    /**
     * Used by the buy api.
     * @param $ticker
     * @param $amount
     * @param $updateItem
     * @param $user
     * @return \Illuminate\Http\JsonResponse
     */
    private function buyStock($ticker, $amount, $updateItem, $user)
    {
        $stock = new StockInfo();
        $converter = new ConversionApi();
        $result = $stock->getTickerinfo($ticker);
        $wallets = DB::select('SELECT * FROM wallets WHERE email = ?', [$user->email]);
        $accountMoney = $wallets[0]->price;
        $total = ($converter->convertToUSD($result['native_currency']) * $result['current_price'] * $amount) + 10;
        $currentHoldingNow = $accountMoney - $total;

        if (is_null($result)) {
            return response()->json(['error' => 'invalid ticker or quantity'], 400);
        } else {
            if ($accountMoney < $total) {
                return response()->json(['error' => 'insufficient cash'], 403);
            } else {
                if ($updateItem === true) {
                    $currentAmount = DB::select('SELECT amount from portfolios where email = ? and ticker = ?', [$user->email, $ticker]);
                    $totalAmount = $amount + $currentAmount[0]->amount;
                    DB::update('UPDATE portfolios SET amount = ?,  purchase_date = ? WHERE email = ? AND ticker = ?', [$totalAmount,  date('Y-m-d'), $user->email, $ticker]);
                    DB::update('UPDATE wallets SET price = ? WHERE email = ?', [$currentHoldingNow, $user->email]);
                    // Return cash left
                    $price = DB::select('SELECT * FROM wallets WHERE email = ?', [$user->email]);
                    $cash = $price[0]->price;
                    return response()->json(['cashleft' => $cash], 200);
                } else {
                    $insertOne = DB::insert('INSERT into portfolios(company, ticker, amount, current_price, purchase_date, native_currency, total, email, change, close_yesterday) 
            values (?,?,?,?,?,?,?,?,?,?)', [$result['company'], $ticker, $amount,
                        $result['current_price'], date('Y-m-d'),$result['native_currency'], $total, $user->email, $result['change'], $result['close_yesterday']]);
                    $updateThree = DB::Update('Update wallets set price = ? where email =?', [$currentHoldingNow, $user->email]);
                    $success['message'] = 'Successfully bought ' . $amount . ' stocks of ' . $ticker;
                    // Return cash left
                    $price = DB::select('SELECT * FROM wallets WHERE email = ?', [$user->email]);
                    $cash = $price[0]->price;
                    return response()->json(['cashleft' => $cash], 200);
                }
            }
        }
    }


}
