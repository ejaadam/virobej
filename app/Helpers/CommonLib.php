<?php

namespace App\Helpers;

use DB;
use Illuminate\Support\Facades\Config;
use Mail;
use Log;

class CommonLib
{

    // function to get gravatar photo/image from gravatar.com using email id.
    public function getGravatarURL ($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array())
    {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img)
        {
            $url = '<img src="'.$url.'"';
            foreach ($atts as $key=> $val)
                $url .= ' '.$key.'="'.$val.'"';
            $url .= ' />';
        }
        return $url;
    }    

    public static function currency_format ($amount, $currency = '', $concat = true, $with_code = false, $length=0)
    {		//print_r($currency);exit;
        $currency_id = 0;
        $currency_code = '';
        if (is_object($currency))
        {
            $currency = !empty(array_filter((array) $currency)) ? $currency : 1;
        }
        elseif (is_array($currency))
        {
            $currency = !empty(array_filter($currency)) ? (object) $currency : 1;
            if (isset($currency->currency_code))
            {
                $currency_code = $currency->currency_code;
            }
        }
        else
        {
            //$currency = 1; / USD /
            //$currency = 2; / INR /
        }

        if ((is_integer($currency) && $currency > 0) || (is_string($currency_code) && !empty($currency_code)))
        {
            $currency_id = is_integer($currency) ? $currency : 0;
			//session()->forget('currency_list');
            if (!session()->has('currency_listt') || (session()->has('currency_listt') && $currency_id > 0))
            {
                $currencies = session()->has('currency_listt') ? session()->get('currency_listt') : [];
				if (empty($currencies) || (!empty($currencies) && !isset($currencies[$currency_id])))
                {					
					//return $currency_id;
                    $qry = DB::table(Config::get('tables.CURRENCIES'))
                            ->select('currency as currency_code', 'currency_symbol', 'decimal_places');
                    if ($currency_id > 0)
                    {
                        $qry->where('currency_id', $currency_id);
                    }
                    else if (!empty($currency_code))
                    {
                        $qry->where('currency', $currency_code);
                    }					
                    $currency = $qry->first();
                    session()->put('currency_listt', [$currency_id=>$currency]);
                }
            }
            if (session()->has('currency_listt'))
            {
                $currencies = session()->get('currency_listt');
                if (isset($currencies[$currency_id]))
                {
                    $currency = $currencies[$currency_id];
                }
            }
        }		
        if (!empty($currency))
        {
            $currency->amount = number_format($amount, $currency->decimal_places, '.', ',');
			$currency->amount= !empty($length) ? str_pad($currency->amount,strlen($currency->amount)+($length-1),'0',STR_PAD_LEFT ) : $currency->amount;
            //unset($currency->decimal_places);
            return $concat ? $currency->currency_symbol.' '.(isset($currency->value_type) && !empty($currency->value_type) ? $currency->value_type : '').$currency->amount.($with_code ? ' '.$currency->currency_code : '') : $currency;
        }
        else
        {
            return false;
        }
    }      

    function boundingCoordinates ($lat, $lng, $distance, $distance_unit = null)
    {
        //$distance = 10;
        if ($distance_unit == 1 || $distance_unit == null)
        {
            // earth's radius in km = ~6371
            $radius = 6371.01;
        }
        else
        {
            // earth's radius in Miles = ~3959
            $radius = 3959;
        }
        // latitude boundaries
        $maxlat = $lat + rad2deg($distance / $radius);
        $minlat = $lat - rad2deg($distance / $radius);

        // longitude boundaries (longitude gets smaller when latitude increases)
        $maxlng = $lng + rad2deg($distance / $radius / cos(deg2rad($lat)));
        $minlng = $lng - rad2deg($distance / $radius / cos(deg2rad($lat)));

        return ['minlat'=>$minlat, 'maxlat'=>$maxlat, 'minlng'=>$minlng, 'maxlng'=>$maxlng];
    }

    public static function amount_with_decimal2 ($amt)
    {
        $amt = floatval(trim($amt));
        $decimal_places = 2;
        $decimal_val = explode('.', $amt);
        if (isset($decimal_val[1]))
        {
            $decimal = rtrim($decimal_val[1], 0);
            if (strlen($decimal) > 2)
                $decimal_places = strlen($decimal);
            if ($decimal_places > 8)
                $decimal_places = 8;
        }
        return number_format($amt, $decimal_places, '.', ',');
    }

    public function export ($filename, array $columns = array(), array $data = array())
    {
        $headers = array(
            'Pragma'=>'public',
            'Expires'=>'public',
            'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
            'Cache-Control'=>'private',
            'Content-Disposition'=>'attachment; filename='.$filename.'-'.date('dMYHis').'.xls',
            'Content-Transfer-Encoding'=>' binary'
        );
        return (object) ['body'=>\View::make('export-layout', ['data'=>$data, 'columns'=>$columns, 'title'=>$filename]), 'headers'=>$headers];
    }
	
	public static function validetIFSC($ifsc_code)
    {
        $settings = config('services.ifsc');        
        $qstr =
            'api_key='.$settings['key'].            
            '&ifsc='.$ifsc_code.
            '&format=json';
        if (!empty(trim($ifsc_code)))
        {
			$url = $settings['url'].$qstr;            
			$ch = curl_init($url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);            
            curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            if ($result === FALSE)
            {
                Log::error('Notification Sending failed:  '.curl_error($ch));
                die('SMS Sending failed: '.curl_error($ch));
            }
            curl_close($ch);			
            return $result;
        }
        return false;
    }

}
