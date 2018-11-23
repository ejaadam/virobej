<?php
namespace App\Models\Api;
use DB;
use App\Models\BaseModel;
use Config;
use CommonLib;
use App\Helpers\CommonNotifSettings;

class CommonModel extends BaseModel
{

    public function getCurrencyInfo ($currency_id)
    {
        return DB::table($this->config->get('tables.CURRENCIES'))
                        ->where('currency_id', $currency_id)
                        ->selectRaw('currency as code, currency_symbol, min_amount')
                        ->first();
    }
    
    /* Find Country Details */
	public function country_details ($country)
    {
	    if (!empty($country))
        {
            $wdata = '';
            if (isset($country))
            {
                if (is_numeric($country))
                {
                    $wdata['country_id'] = $country;
                }
                else if (preg_match('/(^[A-Z]{2}$)/', $country))
                {
                    $wdata['iso2'] = $country;
                }
                else if (preg_match('/([A-Za-z]$)/', $country))
                {
                    $wdata['country'] = $country;
                }
            }
            if (!empty($wdata))
            {
                $res = DB::table($this->config->get('tables.LOCATION_COUNTRY').' as lc')				
						->where('lc.status', $this->config->get('constants.ON'))                
						->where('lc.operate', $this->config->get('constants.ON'))					
						->where($wdata)
						->selectRaw('phonecode as phone_code, country_id, iso2 as country_code, country, distance_unit, currency_id')
						->first(); 
                if ($res)
                {
                    return $res;
                }
            }
        }
        return NULL;
    }
	
	public function getLocationInfo (array $arr = array(), $with_values = false)
    {	
        $pincode = null;
        $locality = null;
        $country = null;
        $state = null;
        $district = null;
        extract($arr);
        if (isset($lat) && isset($lng))
        {
            $ch = curl_init();
            $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key='.$this->config->get('services.google.map_api_key').'&result_type=country|administrative_area_level_1|administrative_area_level_2|sublocality|locality|postal_code';
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            if ($result === FALSE)
            {
                die('Curl failed: '.curl_error($ch));
            }
            curl_close($ch);
            $result = json_decode($result);
			//print_r($result);exit;
            if (!empty($result) && !empty($result->results[0]->address_components))
            {
                foreach ($result->results[0]->address_components as $c)
                {
                    if (in_array('country', $c->types))
                    {
                        $country = $c->long_name;
                    }
                    if (in_array('administrative_area_level_1', $c->types))
                    {
                        $state = $c->long_name;
                    }
                    if (in_array('administrative_area_level_2', $c->types))
                    {
                        $district = $c->long_name;
                    }
                    if (in_array('sublocality', $c->types))
                    {
                        $locality = $c->long_name;
                    }
                    if (empty($locality) && in_array('locality', $c->types))
                    {
                        $locality = $c->long_name;
                    }
                    if (in_array('postal_code', $c->types))
                    {
                        $pincode = $c->long_name;
                    } 
                }
            }
        }
		//return $locality;
        if ((isset($pincode) && !empty($pincode)) || (isset($country) && !empty($country)) || (isset($country_id) && !empty($country_id)))
        {			
			$query = DB::table($this->config->get('tables.LOCATION_COUNTRY').' as lc')
                    ->leftJoin($this->config->get('tables.LOCATION_STATE').' as ls', 'ls.country_id', '=', 'lc.country_id')
                    ->leftJoin($this->config->get('tables.LOCATION_DISTRICTS').' as ld', 'ld.state_id', '=', 'ls.state_id')
                    ->leftJoin($this->config->get('tables.LOCATION_PINCODES').' as lp', 'lp.district_id', '=', 'ld.district_id')
                    ->leftJoin($this->config->get('tables.LOCATION_CITY').' as ll', 'll.pincode_id', '=', 'lp.pincode_id')
                    ->selectRaw('lc.country,lc.country_id,lc.currency_id,ls.state_id,ld.district_id,lc.distance_unit, lc.iso2 as country_code,lc.phonecode');
					
            if (!empty($country))
            {
                $query->where(DB::raw('REPLACE(lc.country,\' \',\'\')'), str_replace(' ', '', $country));
            }
            if (!empty($country_id))
            {
                $query->where('lc.country_id', $country_id);
            }
             if (!empty($locality_id))
            {
                $query->leftjoin($this->config->get('tables.LOCATION_POPULAR_LOCALITIES').' as lpl', 'lpl.locality_id', '=', 'll.city_id')
                        ->where('ll.city_id', $locality_id)
                        ->addSelect('lpl.latitude as lat', 'lpl.longitude as lng');
            }
           
            $query->where(function($sq) use($state, $district, $pincode, $locality, $pincode)
            {
				if (!empty($pincode))
                {
                    $sq->orWhere(function($s) use($pincode)
                    {
                        $s->whereNull('lp.pincode')
                                ->orWhere(DB::raw('REPLACE(lp.pincode,\' \',\'\')'), str_replace(' ', '', $pincode))
								->addSelect(DB::raw('group_concat(ll.city_id) as locality_id'));
                    });
                } else {
					$sq->addSelect(DB::raw('ll.city_id as locality_id, ll.city as locality'));
				} 
				
                if (!empty($state))
                {
                    $sq->orWhere(function($s) use($state)
                    {
                        $s->whereNull('ls.state')
                                ->orWhere(DB::raw('REPLACE(ls.state,\' \',\'\')'), str_replace(' ', '', $state));
                    });
                } 
                if (!empty($district))
                {
                    $sq->orWhere(function($s) use($district)
                    {
                        $s->whereNull('ld.district')
                                ->orWhere(DB::raw('REPLACE(ld.district,\' \',\'\')'), str_replace(' ', '', $district));
                    });
                } 

                if (!empty($locality))
                {
                    $sq->orWhere(function($s) use($locality)
                    {
                        $s->whereNull('ll.city')
                                ->orWhere(DB::raw('REPLACE(ll.city,\' \',\'\')'), 'like', '%'.str_replace(' ', '', $locality).'%');
                    });
                } 
            });
			
            $locations = $query->orderby('city')->first();
            if (!empty($locations))
            {
                $locations->locality = isset($locations->locality) ? $locations->locality : (!empty($locality) ? $locality : null);
                $locations->locality_id = isset($locations->locality_id) ? $locations->locality_id : (!empty($locality_id) ? $locality_id : null);
                //$locations->locality_id = !$with_values ? explode(',', $locations->locality_id)[0] : explode(',', $locations->locality_id);
                $locations->lat = isset($lat) ? $lat : (isset($locations->lat) ? $locations->lat : null);
                $locations->lng = isset($lng) ? $lng : (isset($locations->lng) ? $locations->lng : null);
            }
            return $locations;           
        }
        return false;
    }
	
	public function getNearestPopularLocation (array $arr = array())
    {	
        extract($arr);
        $sbSql = DB::table($this->config->get('tables.LOCATION_POPULAR_LOCALITIES'))
                ->where('status', config('constants.ACTIVE'));
		if (!empty($boundries) || isset($boundries))
        {
            $sbSql->whereBetween('latitude', [$boundries->minlat, $boundries->maxlat]);
            $sbSql->whereBetween('longitude', [$boundries->minlng, $boundries->maxlng]);
        }        
        $res = $sbSql->selectRaw('locality_id, locality, country_id, state_id, district_id, latitude as lat, longitude as lng, group_concat(DISTINCT district_id) as district_id')
					->first();  
		
		if ($res->district_id) {	
			$res->district_id = explode(',', $res->district_id);		
			return $res;
		} 	
		return false;
    }
	
    public function getPGRdetails ($id)
    {			
        return DB::table(Config('tables.PAYMENT_GATEWAY_RESPONSE'))
                        ->where('id', $id)
                        ->first();
    }
	
	/* public function getAccountLogToken ($account_log_id)
    {
        return DB::table(Config('tables.ACCOUNT_LOG'))
                        ->where('account_log_id', $account_log_id)
                        ->where('is_deleted', Config('constants.OFF'))
                        ->value('token');
    } */
	
	public function saveResponse (array $pgr = array(), array $arr = array(), $withSuccessResponse = false)
    {	
        extract($arr);
        $pgr_details = DB::table(config('tables.PAYMENT_GATEWAY_RESPONSE'))
                ->where('id', $pgr['id'])
                ->first();				
        if (!empty($pgr_details))
        {
            $op = [];
            $op['status'] = $this->statusCode = Config('httperr.UN_PROCESSABLE');
            $op['purpose'] = $pgr_details->purpose;			
            if ($pgr_details->status != config('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.CONFIRMED'))
            {					
                $payment_status = $pgr['payment_status'];				
                $pgr['payment_status'] = Config('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.'.$pgr['payment_status']);
				$pgr['response'] = json_encode($pgr['response']);								
                $pgr = array_filter($pgr);				
                DB::beginTransaction();
                $pgr['updated_on'] = getGTZ();				
                DB::table(Config('tables.PAYMENT_GATEWAY_RESPONSE'))
                        ->where('id', $pgr['id'])
                        ->update($pgr);
						
                $pgr_details = DB::table(Config('tables.PAYMENT_GATEWAY_RESPONSE'))
                        ->where('id', $pgr['id'])
                        ->first();					
                $pgr_details->pay_mode = trans('general.payment_modes.'.$pgr_details->pay_mode_id);		  				
                switch ($pgr_details->purpose)
                {
                    case $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PAY'):
                        $this->paymodel = new User\PayModel($this);
                        $op = $this->paymodel->confirmPay($pgr_details->relative_post_id, $payment_status);							
                        $this->updateSessionFile($pgr_details->account_log_id, ['PAY'], true);
                        break;                    
                }
                if ($op['status'] == $this->statusCode = $this->config->get('httperr.SUCCESS'))
                {
                    DB::table($this->config->get('tables.PAYMENT_GATEWAY_RESPONSE'))
                            ->where('id', $pgr_details->id)
                            ->update(['status'=>$this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.CONFIRMED')]);
                }
				
            }			
			else if ($pgr_details->status == $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.STATUS.CONFIRMED'))
            {
                switch ($pgr_details->purpose)
                {
                    case $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.PAY'):
                        $this->paymodel = new User\PayModel($this);
                        $op = $this->paymodel->getPayResponse($pgr_details->relative_post_id, false, $pgr['response']['firstname']);						
                        break;                    
                    case $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.PURPOSE.ADD-MONEY'):
                        if (DB::table($this->config->get('tbl.ADD_MONEY'))
                                        ->where('am_id', $pgr_details->relative_post_id)
                                        ->where('status', $this->config->get('constants.PAYMENT_GATEWAY_RESPONSE.CONFIRMED'))
                                        ->exists())
                        {
                            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                            $op['msg'] = trans('general.money_added', ['amount'=>CommonLib::currency_format($pgr_details->amount, $pgr_details->currency_id)]);
                        }
                        else
                        {
                            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                            $op['msg'] = trans('user/cashback.payment_failed', []);
                            $op['title'] = trans('user/cashback.payment_failed_title');
                        }
                        break;
                }
            }
            else
            {
                $op['status'] = $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
                $op['msg'] = trans('user/cashback.payment_already_done', ['amount'=>CommonLib::currency_format($pgr_details->amount, $pgr_details->currency_id)]);
                $op['title'] = trans('user/cashback.payment_already_done_title');
            }		
            
            DB::commit();
            return $op;
        }
        DB::rollback();
        return false;
    }
	
	public function updateSessionFile ($log_id = null, array $arr = array(), $remove = false, $userSession = true)
    {
        $qry = DB::table($this->config->get('tables.ACCOUNT_LOG'))
                ->where('is_deleted', $this->config->get('constants.OFF'));
        if (is_array($log_id))
        {
            $qry->whereIn('account_log_id', $log_id);
        }
        else
        {
            $qry->where('account_log_id', $log_id);
        }
        $tokens = $qry->lists('token');
        foreach ($tokens as $token)
        {
            $token = explode('-', $token);
            $path = $this->config->get('session.files').'/'.$token[0];
            if (file_exists($path))
            {
                $data = unserialize(file_get_contents($path));
                foreach ($arr as $k=> $d)
                {
                    if (!$remove)
                    {
                        if ($userSession)
                        {
                            $data['user'][$k] = $d;
                        }
                        else
                        {
                            $data[$k] = $d;
                        }
                    }
                    else
                    {
                        if ($userSession)
                        {
                            if (isset($data['user'][$d]))
                            {
                                unset($data['user'][$d]);
                            }
                        }
                        else
                        {
                            if (isset($data[$d]))
                            {
                                unset($data[$d]);
                            }
                        }
                    }
                }
                file_put_contents($path, serialize($data));
            }
        }
        return true;
    }
	
	public function updateOrderCommission ($order_id)
    {			
        $is_cashback_for_redeem = 0;
        $order_qry = DB::table(Config('tables.ORDERS').' as mo')
                ->join(Config('tables.PAY').' as p', 'p.order_id', '=', 'mo.order_id')
				->join(Config('tables.ADDRESS_MST').' as at', function($at){
					$at->on('at.relative_post_id', '=', 'mo.supplier_id')
                       ->where('at.post_type', '=', Config('constants.ACCOUNT_TYPE.SELLER'));
				})
				/* ->join(Config('tables.ACCOUNT_TAX_DETAILS').' as at', function($at)
                {
                    $at->on('at.relative_postid', '=', 'mo.supplier_id')
                    ->where('at.post_type', '=', Config('constants.ACCOUNT_TYPE.SELLER'))
                    ->where('at.status', '=', Config('constants.ON'))
                    ->where('at.is_deleted', '=', Config('constants.OFF'));
                }) */
                ->where('mo.order_id', $order_id)
                ->groupby('p.order_id')                	
				->selectRaw('at.country_id, at.state_id, mo.supplier_id, mo.store_id, p.from_currency_id, mo.order_id, mo.currency_id, mo.pay_through, p.from_amount, sum(if(p.payment_type_id='.Config('constants.PAYMENT_TYPES.CASH').',p.from_amount,0)) as store_recd_amt, sum(if(p.payment_type_id!='.Config('constants.PAYMENT_TYPES.CASH').',p.from_amount,0)) as sys_recd_amt, mo.bill_amount');
        $details = DB::table(DB::raw('('.$order_qry->tosql().') as od'))
                ->join(Config('tables.PROFIT_SHARING').' as mps', 'mps.supplier_id', '=', 'od.supplier_id')                
                ->addBinding($order_qry->getBindings())
                ->where(function($s)
                {
                    $s->whereNull('mps.store_id')
                    ->orWhere('mps.store_id', 'od.store_id');
                })
                ->where('mps.status', '=', Config('constants.ON'))
                ->where('mps.is_deleted', '=', Config('constants.OFF'))
                ->selectRaw('od.*, mps.profit_sharing, mps.cashback_on_pay, mps.cashback_on_redeem, mps.cashback_on_shop_and_earn')
                ->orderBY('mps.store_id', 'DESC')
                ->first();
	    if (!empty($details))
        {
            $redeemed = DB::table(Config('tables.REDEEMS'))
                    ->where('order_id', $order_id)
                    ->value('from_amount');
            $tot_tax = 0;
            $charges = [];
            
            $cashback_profit = ($details->bill_amount / 100) * $details->profit_sharing;
            if ($cashback_profit > 0)
            {				
                list($tot_tax, $taxes) = $this->getTax([
                    'country_id'=>$details->country_id,
                    'state_id'=>$details->state_id,
                    'amount'=>$cashback_profit,
                    'currency_id'=>$details->currency_id,
                    'statementline_id'=>Config('stline.ORDER_PAYMENT_COMMISSION.DEBIT'),
                ]);
				
                $commission = [];				
                $commission['currency_id'] = $details->currency_id;
                $commission['commission_per'] = $details->profit_sharing;
                $commission['system_comm'] = $cashback_profit;
                $commission['tax'] = $tot_tax;
                $commission['tax_info'] = json_encode($taxes);
                $commission['handling_charges'] = $transaction_charge = $details->bill_amount * (Config('constants.TRANSACTION_CHARGE') / 100);
                $commission['net_amt'] = $details->bill_amount - ($cashback_profit + $tot_tax + $transaction_charge);
                $commission['sys_recd_amt'] = $details->sys_recd_amt;
                $commission['store_recd_amt'] = $details->store_recd_amt;
                $commission['net_bill_amt'] = $details->bill_amount;
                //$commission['system_settlement'] = $details->store_recd_amt - ($details->bill_amount - $commission['net_amt']);
				$commission['system_settlement'] = ($cashback_profit + $tot_tax + $transaction_charge) - $details->sys_recd_amt;
                //$commission['mr_settlement'] = $details->sys_recd_amt - $commission['net_amt'];
				$commission['mr_settlement'] = $commission['net_amt'] - $details->store_recd_amt;
                $commission['status'] = Config('constants.ORDER.COMMISSION.STATUS.PENDING');
                $commission['created_on'] = getGTZ();
                if (DB::table(Config('tables.ORDER_COMMISSION'))
                                ->where('order_id', $details->order_id)
                                ->exists())
                {
                    DB::table(Config('tables.ORDER_COMMISSION'))
                            ->where('order_id', $details->order_id)
                            ->update($commission);
                }
                else
                {
                    $commission['order_id'] = $details->order_id;
                    DB::table(Config('tables.ORDER_COMMISSION'))
                            ->insertGetID($commission);
                }

                $cashback_amt = (($details->bill_amount - $redeemed) / 100) * $details->{Config('constants.CASHBACK_ON.'.$details->pay_through)};				
                if (($is_cashback_for_redeem && $redeemed) || (!$redeemed && $cashback_amt > 0))
                {
                    $cashback_amt = $cashback_amt * $this->get_currency_rate($details->currency_id, $details->from_currency_id);
                    $cashback = [];                    
                    $cashback['currency_id'] = $details->from_currency_id;                    
                    $cashback['transaction_id'] = $this->generateTransactionID();
                    $cashback['cashback_amount'] = $cashback_amt;
                    $cashback['updated_on'] = $cashback['created_on'] = getGTZ();
                    $cashback['status'] = Config('constants.CASHBACK_STATUS.PENDING');
                    if (DB::table(Config('tables.CASHBACKS'))
                                    ->where('order_id', $details->order_id)
                                    ->exists())
                    {
                        $res = DB::table(Config('tables.CASHBACKS'))
                                ->where('order_id', $details->order_id)
                                ->update($cashback);
                    }
                    else
                    {
                        $cashback['order_id'] = $details->order_id;
                        $res = DB::table(Config('tables.CASHBACKS'))
                                ->insertGetID($cashback);
                    }
                    return $res ? $cashback_amt : false;
                }
                return true;
            }
        }
        return false;
    }
	
	
	public function releaseOrderCommission ($order_id)
    {								
        $order_details = DB::table(Config('tables.ORDERS').' as mo')
                ->join(Config('tables.ORDER_COMMISSION').' as oc', function($oc)
                {
                    $oc->on('oc.order_id', '=', 'mo.order_id')
                    ->where('oc.status', '=', Config('constants.ORDER.COMMISSION.STATUS.PENDING'));
                })
                ->join(Config('tables.SUPPLIER_MST').' as m', 'm.supplier_id', '=', 'mo.supplier_id')
                ->join(Config('tables.STORES').' as ms', 'ms.store_id', '=', 'mo.store_id')
				->join(Config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'mo.account_id')
                ->leftjoin(Config('tables.ACCOUNT_RATINGS').' as r', function($join)
                {
                    $join->on('r.relative_post_id', '=', 'mo.store_id')
                    ->on('r.account_id', '=', 'mo.account_id')
                    ->where('r.post_type_id', '=', Config('constants.POST_TYPE.STORE'));
                })
                ->where('mo.bill_amount', function($pa) use($order_id)
                {
                    return $pa->from(Config('tables.PAY').' as p')
                            ->where('p.status', Config('constants.PAY_STATUS.CONFIRMED'))
                            ->selectRaw('sum(to_amount) as amount')
                            ->where('p.is_deleted', Config('constants.OFF'))
                            ->where('p.order_id', $order_id)
                            ->value('amount');
                })
                ->selectRaw('mo.supplier_id, mo.store_id, mo.bill_amount, r.rating, mo.order_id, oc.comm_id, mo.order_code, oc.net_bill_amt, oc.mr_settlement, oc.system_comm, mo.account_id as user_account_id, m.account_id, ms.store_name, oc.currency_id, oc.commission_per, oc.tax_info, CONCAT(ad.firstname,\' \',ad.lastname) as full_name')
                ->where('mo.order_id', $order_id)
                ->first();			
			
        if ($order_details)
        {
			
            DB::table(Config('tables.ORDERS'))
                    ->where('order_id', $order_details->order_id)
                    ->update([
						'paid_amount'=>$order_details->bill_amount,
                        'status'=>Config('constants.ORDER.STATUS.PAID'),
                        'payment_status'=>Config('constants.ORDER.PAYMENT_STATUS.PAID')
            ]);
            $order_details->tax_info = json_decode($order_details->tax_info, true);
            $order_payment = [
				'supplier_id'=>$order_details->supplier_id,
                'store_id'=>$order_details->store_id,
                'currency_id'=>$order_details->currency_id,
                'amt'=>$order_details->bill_amount,
                'relation_id'=>$order_details->order_id,
				'debit_source_account_id'=>$order_details->user_account_id,
                'credit_source_account_id'=>$order_details->user_account_id,
				'debit_remark_data'=>['order_code'=>$order_details->order_code, 'bill_amount'=>CommonLib::currency_format($order_details->bill_amount, $order_details->currency_id, true, true), 'store_name'=>$order_details->store_name, 'full_name'=>$order_details->full_name],
				'credit_remark_data'=>['order_code'=>$order_details->order_code, 'bill_amount'=>CommonLib::currency_format($order_details->bill_amount, $order_details->currency_id, true, true), 'store_name'=>$order_details->store_name, 'full_name'=>$order_details->full_name],               
            ];
			$handle = [
                'amt'=>$order_details->system_comm                
            ];            
            $taxes = [];
            foreach ($order_details->tax_info as $tax)
            {
                $taxes[] = [
                    'tax_id'=>$tax['tax_id'],
                    'amt'=>$tax['tax_amt']
                ];
            }
			if ($order_details->mr_settlement < 0)
            {
				$order_payment['paidamt'] = -1 * $order_details->mr_settlement;
                $order_payment['from_account_id'] = $order_details->account_id;
                $order_payment['from_wallet_id'] = $this->config->get('constants.WALLETS.VIM');
                $order_payment['from_handle'][] = $handle;
                $order_payment['from_taxes'] = $taxes;
                $order_payment['transaction_for'] = 'ORDER_PAYMENT_COMMISSION';
            }
            else
            {
				$order_payment['paidamt'] = $order_details->mr_settlement;
                $order_payment['to_wallet_id'] = $this->config->get('constants.WALLETS.VIM');
                $order_payment['to_account_id'] = $order_details->account_id;
                $order_payment['to_handle'][] = $handle;
                $order_payment['to_taxes'] = $taxes;
                $order_payment['transaction_for'] = 'ORDER_PAYMENT_TO_MERCHANT';
            }       
			//print_r($order_details);print_r($order_payment);exit;
			if ($this->updateAccountTransaction($order_payment))
            {
                DB::table($this->config->get('tables.ORDER_COMMISSION'))
                        ->where('comm_id', $order_details->comm_id)
                        ->update([
                            'status'=>$this->config->get('constants.ORDER.COMMISSION.STATUS.PAID'),
                            'updated_on'=>getGTZ()
                ]);
				$no_credit = DB::table($this->config->get('tables.ACCOUNT_BALANCE').' as ab')
                        ->where('ab.account_id', '=', $order_details->account_id)
                        ->where('ab.currency_id', '=', $order_details->currency_id)
                        ->where('ab.wallet_id', '=', $this->config->get('constants.WALLETS.VIM'))
                        ->where('ab.current_balance', '>', 0)
                        ->exists();
						
                $this->creditCustomerCashback(['order_id'=>$order_details->order_id], !$no_credit);
                return true;
            }            
        }
        return false;
    }
	
	public function creditCustomerCashback (array $arr = array(), $check_balance = true)
    {
        extract($arr);
        DB::beginTransaction();
         $cashback = DB::table($this->config->get('tables.CASHBACKS').' as c')
                 ->join($this->config->get('tables.ORDERS').' as mo', 'mo.order_id', '=', 'c.order_id')
				 ->join($this->config->get('tables.ORDER_COMMISSION').' as oc', function($oc)
                {
                    $oc->on('oc.order_id', '=', 'mo.order_id')
                    ->where('oc.status', '=', $this->config->get('constants.ORDER.COMMISSION.STATUS.PAID'));
                })
                ->join($this->config->get('tables.SUPPLIER_MST').' as m', 'm.supplier_id', '=', 'mo.supplier_id')
                ->join($this->config->get('tables.STORES').' as ms', 'ms.store_id', '=', 'mo.store_id')
                ->leftjoin($this->config->get('tables.ACCOUNT_RATINGS').' as r', function($join)
                {
                    $join->on('r.relative_post_id', '=', 'mo.order_id')
                    ->on('r.account_id', '=', 'mo.account_id')
                    ->where('r.post_type_id', '=', $this->config->get('constants.POST_TYPE.ORDER'));
                })  
                ->selectRaw('m.account_id as supplier_account_id, c.cashback_id, c.currency_id, c.cashback_amount, mo.account_id, mo.order_code, mo.bill_amount, COALESCE(ms.store_name,m.company_name) as store_name')                
                ->where('c.status', $this->config->get('constants.CASHBACK_STATUS.PENDING'));
		if (isset($order_id))
        {
            $cashback->where('c.order_id', $order_id);
        }
        else if (isset($cashback_id))
        {
            $cashback->where('c.cashback_id', $cashback_id);
        }	
		if ($check_balance)
        {
            $cashback->join($this->config->get('tables.ACCOUNT_BALANCE').' as ab', function($ab)
            {
                $ab->on('ab.account_id', '=', 'mo.account_id')
                        ->on('ab.currency_id', '=', 'c.currency_id')
                        ->where('ab.wallet_id', '=', $this->config->get('constants.WALLETS.VIM'))
                        ->where('ab.current_balance', '>', 0);
            });
        }	
        $cashback = $cashback->first();
		
        if (!empty($cashback))
        {
            $wallets = DB::table($this->config->get('tables.WALLET').' as w')
                    ->Join($this->config->get('tables.WALLET_LANG').' as wl', function($join)
                    {
                        $join->on('wl.wallet_id', '=', 'w.wallet_id');                        
                    })
                    ->where('w.cashback_perc', '>', 0)
                    ->select('w.cashback_perc', 'w.wallet_id', 'wl.wallet')
                    ->get();
				
            if (!empty($wallets))
            {
                $cashbacks = [];
                $is_instant = $this->getSetting($this->config->get('constants.CASHBACK_INSTANT_CREDIT'));
                $is_credited = 0;
                if (($is_instant == $this->config->get('constants.ON')))
                {
                    foreach ($wallets as $wallet)
                    {
						if ($wallet->cashback_perc > 0)
                        {
                            $credit_amt = ($cashback->cashback_amount * $wallet->cashback_perc) / 100;
                            if ($trans_id = $this->updateAccountTransaction([
                                'to_account_id'=>$cashback->account_id,
								'credit_source_account_id'=>$cashback->supplier_account_id,
                                'to_wallet_id'=>$wallet->wallet_id,
                                'currency_id'=>$cashback->currency_id,
                                'amt'=>$credit_amt,
                                'relation_id'=>$cashback->cashback_id,
                                'credit_remark_data'=>['store_name'=>$cashback->store_name, 'order_code'=>$cashback->order_code, 'bill_amount'=>CommonLib::currency_format($cashback->bill_amount, $cashback->currency_id, true, true)],
                                'transaction_for'=>'CASHBACK']))
                            {
                                $is_credited++;
                                /* $cashbacks[] = [
                                    'trans_id'=>$trans_id,
                                    'cashback_amount'=>CommonLib::currency_format($credit_amt, $cashback->currency_id, true, true),
                                    'wallet'=>$wallet->wallet
                                ];  */
                            }
                        }                       
                    }
					
                    if ($is_credited == count($wallets))
                    {
                        DB::table($this->config->get('tables.CASHBACKS'))
                                ->where('cashback_id', $cashback->cashback_id)
                                ->update(['status'=>$this->config->get('constants.CASHBACK_STATUS.CONFIRMED'),
                                    'updated_on'=>getGTZ()]);
						/* CommonNotifSettings::notify('USER.GET_CASHBACK', $cashback->account_id, $this->config->get('constants.ACCOUNT_TYPE.USER'), [
                            'store'=>$cashback->store_name,
                            'order_id'=>$cashback->order_code,
                            'bill_amount'=>CommonLib::currency_format($cashback->bill_amount, $cashback->currency_id, true, true),
                            'cashbacks'=>$cashbacks,
                            'date'=>showUTZ('d-M-Y'),
                        ], true, false, false, true, false);    */                     
                    }
                    //$this->addReferralEarnings($this->config->get('constants.EARNINGS.COMMISSION_TYPE.REFERRAL_CASHBACK'), ['account_id'=>$cashback->account_id, 'amount'=>$cashback->cashback_amount, 'currency_id'=>$cashback->currency_id, 'relative_id'=>$order_id]);
                    DB::commit();
                    return true;
                }
            }
        }
        DB::rollback();
        return false;
    }
	
	public function updateOrderCount ($store_id)
    {		
        if (DB::table($this->config->get('tables.STORES'))
                        ->where('store_id', $store_id)
                        ->exists())
        {
            return DB::table($this->config->get('tables.STORES'))
                            ->where('store_id', $store_id)
                            ->increment('order_count');
        }
        return false;
    }
	
	public function get_currency_code ($currency_id)
    {		
        return DB::table($this->config->get('tables.CURRENCIES'))
                        ->where('currency_id', $currency_id)
                        ->value('currency');
    }
	
	public function get_user_balance ($account_id, $wallet_id, $currency_id)
    {
        fetch:
        $result = DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
                ->where(array(
                    'account_id'=>$account_id,
                    'wallet_id'=>$wallet_id,
                    'currency_id'=>$currency_id))
                ->first();

        if (empty($result))
        {
            $curresult = DB::table($this->config->get('tables.CURRENCIES'))
                    ->where('currency_id', $currency_id)
                    ->where('status', $this->config->get('constants.ON'))
                    ->count();
            $ewalresult = DB::table($this->config->get('tables.WALLET'))
                    ->where(array(
                        'wallet_id'=>$wallet_id,
                        'status'=>$this->config->get('constants.ON')))
                    ->count();

            if ($curresult && $ewalresult)
            {
                $insert['account_id'] = $account_id;
                $insert['current_balance'] = '0';
                $insert['tot_credit'] = '0 ';
                $insert['tot_debit'] = '0';
                $insert['currency_id'] = $currency_id;
                $insert['wallet_id'] = $wallet_id;
                $status = DB::table($this->config->get('tables.ACCOUNT_BALANCE'))
                        ->insert($insert);
                goto fetch;
            }
        }
        return $result;
    }
	
	public function getDecimalPlaces ($currency_id)
    {
        return DB::table($this->config->get('tables.CURRENCIES'))
                        ->where('currency_id', $currency_id)
                        ->value('decimal_places');
    }
	
	/* Store Review List */
	public function storeReviewsList (array $arr = array(), $count = false)
    {	
        extract($arr);
        $reviews = DB::table($this->config->get('tables.ACCOUNT_RATINGS').' as ar')
                ->join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'ar.account_id')
                ->where('ar.relative_post_id', $store_id)
                ->where('ar.rating', '>', 0)
                ->whereNotNull('ar.feedback')
                ->whereRaw('ar.feedback <> ""')
                ->whereIn('ar.status_id', [$this->config->get('constants.ACTIVE')])
                ->whereIn('ar.is_abused', [$this->config->get('constants.OFF')])
                ->whereIn('ar.is_verified', [$this->config->get('constants.ACTIVE')])
                ->where('ar.is_like', $this->config->get('constants.OFF')) 
                ->where('ar.post_type_id', $this->config->get('constants.RATING_POST_TYPE.STORE'));

        if (isset($orderby) && isset($order))
        {
            $reviews->orderby($orderby, $order);
        }
        else
        {
            $reviews->orderby('ar.created_on', 'DESC');
        }
        if ($count)
        {
            return $reviews->count();
        }
        else
        {
            if (isset($length))
            {
                $reviews->skip($start)->take($length);
            }
            $reviews = $reviews->selectRaw('ar.feedback, ar.rating, ar.created_on, concat(ad.firstname,\' \',ad.lastname) as full_name, ad.profile_img')->get();
            foreach ($reviews as &$review)
            {
                $review->profile_img = asset($this->config->get('path.ACCOUNT.PROFILE_IMG.WEB.100x100').$review->profile_img);
                $review->feedback = !empty($review->feedback) ? $review->feedback : trans('general.rating.'.$review->rating);
                $review->created_on = (!empty($review->created_on) && $review->created_on != '0000-00-00 00:00:00') ? showUTZ($review->created_on, 'd-M-Y') : '';
            }
            return $reviews;
        }
    }
	
	public function getPaymentGateWayDetails ($payment_code)
    {	
        $payment_details = DB::table($this->config->get('tables.PAYMENT_TYPES').' as pt')
                ->where('payment_key', $payment_code)
                ->value('gateway_settings');
        $settings = json_decode($payment_details);
        return $settings->status ? $settings->live : $settings->sandbox;
    }
}
