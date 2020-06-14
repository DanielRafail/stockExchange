<?php 
/**
 * I was wrong Kajal, please ignore this Jaya don't worry about it.  
 * This line of comments is to say that kajal was right don't know what i was planning telling him he was wrong.
 * 
 * The following class will convert a given currency to USD price because *Bald Eagle Screeches*!
 * 
 */


    namespace App\Http\Objects;


 class ConversionApi
 {

    /**
     * returns the price of a given item in its USD equivalent currency
     * 
     * @author George 
     * @param $nativeCurrency
     * @return double
     */
     function convertToUSD($nativeCurrency)
     {


        $url = 'https://www.freeforexapi.com/api/live?pairs=USD'.$nativeCurrency;
        $response = file_get_contents($url);
        $decoded = json_decode($response);

        $currentTab = 'USD'.$nativeCurrency;
        $conversionRate = $decoded->rates->$currentTab->rate;
        return $conversionRate;
     }
 }
