<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Objects\StockInfo;
use Illuminate\Support\Facades\DB;

/**
 * Class StockQuotesController let's you look up tickers and buy them.
 * @author Kajal Bordhon
 * @package App\Http\Controllers
 */
class StockQuotesController extends Controller
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

        return view('StockQuotes.index')->with([
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
        $date = DB::select('select created_at from users where email =?', [$user->email]);
        $returnHome = DB::select('SELECT * FROM wallets WHERE email = ?', [$user->email]);

        $stock = new StockInfo();
        $result = $stock->getTickerinfo($request->ticker);
        if (is_null($result)) {
            $result = array();
            $result['error'] = 'Invalid Ticker';
        }

        return view('StockQuotes.index')->with([
            'returnHome' => $returnHome[0]->price,
            'date_create' => $date[0]->created_at,
            'info' => $result
        ]);


        // return view("StockQuotes.index")->with('info',$result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect('Buyin/' . $id);
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

}

