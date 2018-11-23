<?php
namespace App\Models\Seller;
use App\Models\BaseModel;
use DB;
use Illuminate\Database\Eloquent\Model;
use Config;
use URL;
use Lang;

class Cashback extends BaseModel
{
	public function isCachbackEnabled (array $arr = array())
    {		
		$store_id = null;
        extract($arr);
        $dt = getGTZ();		
        return DB::table(config('tables.CASHBACK_SETTINGS'))
                        ->where('supplier_id', $supplier_id)
                         ->Where(function($sqry) use ($supplier_id, $store_id)
                        {
                            $sqry->whereNull('store_id')->orWhere('store_id', $store_id);
                        })
                        ->where('shop_and_earn', config('constants.ON'))
                        ->Where(function($sqry) use ($dt)
                        {
                            $sqry->where('is_cashback_period', config('constants.OFF'))
                            ->orWhere(function($sqry2) use ($dt)
                            {
                                $sqry2->where('is_cashback_period', config('constants.ON'));
                                $sqry2->whereRaw('DATE(cashback_start) <= \''.$dt.'\' AND  DATE(cashback_end)>=\''.$dt.'\'');
                            }); 
                        }) 
                        ->exists();
    }    
	
	public function searchCustomer (array $arr = array())
    {	
        extract($arr);
        return DB::table(config('tables.ACCOUNT_MST').' as um')
                        ->Join(config('tables.ACCOUNT_DETAILS').' as ud', 'ud.account_id', '=', 'um.account_id')
                        ->leftJoin(config('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'um.account_id')
                        ->leftJoin(config('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'ap.currency_id')                        
                        ->where('um.is_deleted', config('constants.OFF'))
                        ->where('um.account_type_id', Config::get('constants.ACCOUNT_TYPE.USER'))
                        ->selectRaw('um.account_id, um.uname, um.mobile, concat_ws(\' \',ud.firstname,ud.lastname) as full_name, ap.currency_id as user_currency, um.email, c.currency as code, c.currency_symbol') 
                        ->where('um.mobile', $mobile)                    						
                        ->first();
    }
	
	public function creditCashback (array $arr)
    {	
        extract($arr);		
		$wallet_id = Config::get('constants.CASHBACK_WALLET');		       
        if (!empty($arr))
        {
            if (!isset($order_id))
            {
                $order_id = DB::table(config('tables.ORDERS'))
                        ->insertGetID([
                    'account_id'=>$account_id,                                        
                    'currency_id'=>$currency_id,
                    'net_pay'=>$bill_amount,
                    'payment_type_id'=>$payment_type,   
                    'created_on'=>getGTZ(),
                ]);
                if (!empty($order_id))
                {
                    $this->updateOrderCount($supplier_id, $store_id);
                    $update['order_code'] = config('constants.ORDER_CODE_PREFIX').$order_id;
                    DB::table(config('tables.ORDERS'))
                            ->where('order_id', $order_id)
                            ->update($update);
                }
                $pay['order_id'] = $order_id;
                $pay['payment_type_id'] = $payment_type;
                $pay['to_wallet_id'] = $wallet_id;
                $pay['from_currency_id'] = $currency_id;
                $pay['from_amount'] = $bill_amount;
                $pay['to_currency_id'] = $currency_id;
                $pay['to_amount'] = $bill_amount;
                $pay['status'] = config('constants.ON');
                $pay['created_on'] = getGTZ();
                $pay['updated_by'] = $account_id;
                $pay_id = DB::table(config('tables.PAY'))
                        ->insertGetID($pay); 
            }
            if ($order_id)
            {
                $arr['order_id'] = $order_id;
                if ($res = $this->updateAccountTransaction([
                            'from_account_id'=>$customer_id,
                            'to_account_id'=>$account_id,
                            'to_wallet_id'=>$wallet_id,
                            'currency_id'=>$currency_id,
                            'amt'=>$bill_amount,
                            'relation_id'=>$pay_id,
                            'payment_type_id'=>$payment_type,
                            'transaction_for'=>'ORDER_PAYMENT'], false, true, true))
                {	
					return true;
                    /* if ($cashback_amt = $this->commonObj->updateOrderCommission($order_id))
                    {
                        $this->commonObj->releaseOrderCommission($order_id);
                        
                        return (object) ['cashback_amt'=>$cashback_amt, 'order_id'=>$order_id, 'user_currency'=>$user_currency];
                    }
                    else
                    {
                        return false;
                    } */
                }
            } 
			return true;
        }
        return false;
    }
	
	public function updateOrderCount ($supplier_id, $store_id = '')
    {
        if (!DB::table(config('tables.SUPPLIER_STORE_SALES'))
                        ->where('supplier_id', $supplier_id)
                        ->where('store_id', $store_id)
                        ->orwhereNull('store_id')
                        ->exists())
        {
            return DB::table(config('tables.SUPPLIER_STORE_SALES'))
                            ->insert(['supplier_id'=>$supplier_id, 'store_id'=>$store_id, 'order_count'=>1]);
        }
        else
        {
            return DB::table(config('tables.SUPPLIER_STORE_SALES'))
							->where('supplier_id', $supplier_id)
                            ->where('store_id', $store_id)
							->orwhereNull('store_id')
                            ->increment('order_count');
        }
    }

}
