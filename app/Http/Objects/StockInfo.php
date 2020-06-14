<?php

namespace App\Http\Objects;

/**
 * I choose World Trading Data, since it does not have an limit per minute.
 * Also, it has a lot more extra information.
 * Which is useful for portfolio insert.
 * @author Kajal Bordhon
 * @package App\Http\Objects
 */
class StockInfo
{
    /**
     * Returns an associative with 'company''ticker'''current_price'','native_currency','close_yesterday' and 'change'.
     * ATTENTION: It will return null for invalid tickers.
     * @param $ticker
     * @return array|null
     */
    public function getTickerinfo($ticker)
    {
        $url = 'https://www.worldtradingdata.com/api/v1/stock?symbol=' . $ticker . '&api_token=wwqiutajs8Ta48tW5Qydx8FFlO55wApIQCf5txnEaE2eOzpIwUU9pHPeja8s';
        $response = file_get_contents($url);
        $obj = json_decode($response);
        $result = null;

        if (isset($obj->data)) {
            $arr = ($obj->data);

            foreach ($arr as $summerChild) {
                $result = array();
                $result['company'] = $summerChild->name;
                $result['ticker'] = $summerChild->symbol;
                $result['current_price'] = $summerChild->price;
                $result['native_currency'] = $summerChild->currency;
                $result['close_yesterday'] = $summerChild->close_yesterday;
                $result['change'] = $summerChild->change_pct;
            }
        }
        return $result;
    }

}

