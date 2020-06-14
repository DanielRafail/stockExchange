<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Objects\StockInfo;
use Illuminate\Support\Facades\DB;
use App\Http\Objects\ConversionApi;

class BuyinsController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $date = DB::select('select created_at from users where email =?', [$user->email]);
        $returnHome = DB::select('SELECT * FROM wallets WHERE email = ?', [$user->email]);

        return view('marketbuy')->with([
            'returnHome' => $returnHome[0]->price,
            'date_create' => $date[0]->created_at
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return "create";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
//    private function returnToMenu($user, $all_portfolios, $lastClose, $currentClose, $change, $error, $success) {

        $portfolio = DB::select('SELECT * FROM portfolios WHERE email = ? AND ticker = ?', [$user->email, $request->ticker]);
        $all_portfolios = DB::select('SELECT * FROM portfolios WHERE email = ?', [$user->email]);
        $error = array();
        // Validate user input
        $request->validate([
            'amount' => 'required|integer|min:1',
            'ticker' => 'required',
        ]);
        if (isset($portfolio[0])) {
            return $this->buyStock($request->ticker, $request->amount, true);
        } else {
            if (sizeof($all_portfolios) >= 5) {
                $error['message'] = 'Already own 5 and attempting to buy more';
                return $this->returnToMenu($user, $all_portfolios, $error, null);
            } else {
                return $this->buyStock($request->ticker, $request->amount, false);
            }
        }
        return "store";
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $date = DB::select('select created_at from users where email =?', [$user->email]);
        $returnHome = DB::select('SELECT * FROM wallets WHERE email = ?', [$user->email]);

        return view('marketbuy')->with([
            'returnHome' => $returnHome[0]->price,
            'date_create' => $date[0]->created_at,
            'tickerSelected' => $id
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return "edit";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return "update";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return "destroy";
    }

    /**
     * Buy the stocks
     *
     * @param  int $ticker name of ticker
     * @param  int $amount amount of tickers bought
     */
    private function buyStock($ticker, $amount, $updateItem)
    {
        $user = Auth::user();
        $stock = new StockInfo();
        $converter = new ConversionApi();
        $result = $stock->getTickerinfo($ticker);
        $error = array();
        $success= array();
        $all_portfolios = DB::select('SELECT * FROM portfolios WHERE email = ?', [$user->email]);
        if($result === null){
            $error['message'] = 'Invalid ticker : Does not exist';
            return redirect('Buyin')->with(['error' => $error]);
        }
        $wallets = DB::select('SELECT * FROM wallets WHERE email = ?', [$user->email]);
        $accountMoney = $wallets[0]->price;
        $total = ($converter->convertToUSD($result['native_currency']) * $result['current_price'] * $amount) + 10;
        $currentHoldingNow = $accountMoney - $total;
            if ($accountMoney < $total) {
                $error['message'] = 'Not enough money to buy all those stocks';
                return $this->returnToMenu($user, $all_portfolios, $result['close_yesterday'], $total ,$result['change'], $error, null);
            } else {
                if ($updateItem === true) {
                    $currentAmount = DB::select('SELECT amount from portfolios where email = ? and ticker = ?', [$user->email, $ticker]);
                    $totalAmount = $amount + $currentAmount[0]->amount;
                    DB::update('UPDATE portfolios SET amount = ?,  purchase_date = ? WHERE email = ? AND ticker = ?', [$totalAmount,  date('Y-m-d'), $user->email, $ticker]);
                    $updateTwo = DB::update('UPDATE wallets SET price = ? WHERE email = ?', [$currentHoldingNow, $user->email]);
                    $success['message'] = 'Successfully bought ' . $amount . ' stocks of ' . $ticker;
                    return $this->returnToMenu($user, $all_portfolios, $result['close_yesterday'], $total ,$result['change'], null, $success);
                } else {
                    $insertOne = DB::insert('INSERT into portfolios(company, ticker, amount, current_price, purchase_date, native_currency, total, email, change, close_yesterday)
            values (?,?,?,?,?,?,?,?,?,?)', [$result['company'], $ticker, $amount,
                        $result['current_price'], date('Y-m-d'),$result['native_currency'], $total, $user->email, $result['change'], $result['close_yesterday']]);
                    $updateThree = DB::Update('Update wallets set price = ? where email =?', [$currentHoldingNow, $user->email]);
                    $success['message'] = 'Successfully bought ' . $amount . ' stocks of ' . $ticker;
                    return $this->returnToMenu($user, $all_portfolios, $result['close_yesterday'], $total ,$result['change'], null, $success);
                }
            }
    }

    /**
     * Return to menu
     *
     * @param  User $user the user
     * @param  SQL object containing all the information of a portfolio
     * @return int  $lastclose value of last close
     * @param  int $currentClose current value of the close
     * @param  int $change the change between lastClose and currentClose
     * @param  Array error a string array containing an error message
     * @param  Array success a string array containing a success message
     */
    private function returnToMenu($user, $all_portfolios, $lastClose, $currentClose, $change, $error, $success) {
        $returnHome = DB::select('SELECT * FROM wallets WHERE email = ?',[$user->email]);
        $date = DB::select('select created_at from users where email =?',[$user->email]);

        if (!(is_null($error)) && isset($error['message'])) {
            return view('/home')->with([
                'all_portfolios' => $all_portfolios,
                'lastClose' => $lastClose,
                'currentClose' => $currentClose,
                'change' => $change,
                'returnHome' => $returnHome[0]->price,
                'date_create' => $date[0]->created_at,
                'error' => $error
            ]);
        } else if(!(is_null($success)) && isset($success['message'])){
            return redirect('/home')->with([
                'all_portfolios' => $all_portfolios,
                'lastClose' => $lastClose,
                'currentClose' => $currentClose,
                'change' => $change,
                'returnHome' => $returnHome[0]->price,
                'date_create' => $date[0]->created_at,
                'success' => $success
            ]);
        }else{
            return redirect('/home')->with([
                'all_portfolios' => $all_portfolios,
                'lastClose' => $lastClose,
                'currentClose' => $currentClose,
                'change' => $change,
                'returnHome' => $returnHome[0]->price,
                'date_create' => $date[0]->created_at
            ]);
        }
    }
}
