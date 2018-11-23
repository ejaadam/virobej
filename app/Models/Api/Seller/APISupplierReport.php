<?php
namespace App\Models\Api\Seller;
use Illuminate\Database\Eloquent\Model;
use Config;
use DB;

class APISupplierReport extends Model
{

    public function supplier_payment_report ($arr = array(), $count = false)
    {
        extract($arr);
        $payments = DB::table(Config::get('tables.ORDER_SALES_COMMISSION').' as sosc')
                ->where('sosc.is_deleted', 0)
                ->where('sosc.supplier_id', $supplier_id);
        if ((isset($term) && !empty($term)) || !$count)
        {
            $payments->leftjoin(Config::get('tables.SUB_ORDERS').' as so', 'so.sub_order_id', '=', 'sosc.sub_order_id')
                    ->leftjoin(Config::get('tables.ORDER_ITEMS').' as oi', 'oi.order_item_id', '=', 'sosc.order_item_id')
                    ->leftjoin(Config::get('tables.SUPPLIER_PRODUCTS_LIST').' as spi', function($spi)
                    {
                        $spi->on('spi.supplier_product_id', '=', 'oi.supplier_product_id')
							->on('spi.currency_id', '=', 'oi.currency_id');
                    });
        }
        if (isset($from) && !empty($from))
        {
            $payments->whereDate('sosc.created_on', '>=', date('Y-m-d', strtotime($from)));
        }
        if (isset($to) && !empty($to))
        {
            $payments->whereDate('sosc.created_on', '<=', date('Y-m-d', strtotime($to)));
        }
        if (isset($term) && !empty($term))
        {
            $payments->where(function($subquery) use($term)
            {
                $term = '%'.$term.'%';
                //$subquery->where('sosc.remark', 'like', $term)
                $subquery->Where('oi.order_item_code', 'like', $term)
                        ->orWhere('so.sub_order_code', 'like', $term)
                        ->orWhere('spi.product_name', 'like', $term);
            });
        }
        if (isset($start) && isset($length))
        {
            $payments->skip($start)
                    ->take($length);
        }
        if (isset($orderby) && isset($order))
        {
            $payments->orderBy($orderby, $order);
        }
        else
        {
            $payments->orderBy('sosc.osc_id', 'DESC');
        }
        if ($count)
        {
            return $payments->count();
        }
        else
        {
             $payments->leftjoin(Config::get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'sosc.currency_id')
                    ->leftJoin(Config::get('tables.ORDER_SALES_COMMISSION_PAYMENT').' as soscp', 'soscp.osc_id', '=', 'sosc.osc_id')
                    ->leftJoin(Config::get('tables.PAYMENT_STATUS_LOOKUPS').' as p', 'p.payment_status_id', '=', 'soscp.supplier_payment_status_id') 
                    ->selectRaw('so.sub_order_code as order_code, oi.order_item_code, sosc.created_on, sosc.mrp_price, sosc.supplier_sold_price as sold_price, sosc.qty, sosc.supplier_price_sub_total as price_sub_total, sosc.site_commission_sub_total, cur.currency, cur.currency_symbol, p.payment_status, sosc.supplier_discount_per as discount_per, spi.product_name, spi.product_code');
            $payments = $payments->get();
            if (!empty($payments))
            {
                array_walk($payments, function(&$payment)
                {
                    $payment->created_on = date('d-M-Y H:i:s', strtotime($payment->created_on));
                    $payment->mrp_price = $payment->currency_symbol.' '.number_format($payment->mrp_price, 2, ',', '.').' '.$payment->currency;
                    $payment->sold_price = $payment->currency_symbol.' '.number_format($payment->sold_price, 2, ',', '.').' '.$payment->currency;
                    $payment->price_sub_total = $payment->currency_symbol.' '.number_format($payment->price_sub_total, 2, ',', '.').' '.$payment->currency;
                    $payment->site_commission_sub_total = $payment->currency_symbol.' '.number_format($payment->site_commission_sub_total, 2, ',', '.').' '.$payment->currency;
                    $payment->qty = number_format($payment->qty, 0, ',', '.');
                    unset($payment->currency);
                    unset($payment->currency_symbol);
                });
            } 
            return $payments;
        }
    }

    public function getTransactionDetails ($arr = array(), $count = false)
    {
        extract($arr);
        $transaction_log = DB::table(Config::get('tables.ACCOUNT_TRANSACTION').' as a')
                ->where('a.is_deleted', 0)
                ->where('a.account_id', $account_id)
                ->where('a.status', '>', 0);
        if ((isset($term) && !empty($term)) || !$count)
        {
            $transaction_log->leftJoin(Config::get('tables.WALLET_LANG').' as b', 'b.wallet_id', '=', 'a.wallet_id')
                    ->leftJoin(Config::get('tables.STATEMENT_LINE').' as e', 'e.statementline_id', '=', 'a.statementline_id')
                    ->leftjoin(Config::get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'a.currency_id')
                    ->leftjoin(Config::get('tables.PAYMENT_TYPES').' as p', 'p.payment_type_id', '=', 'a.payment_type_id')
                    ->leftJoin(Config::get('tables.ACCOUNT_DETAILS').' as d', 'd.account_id', '=', 'a.from_account_id')
                    ->leftjoin(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'a.from_account_id');
        }
        if (isset($from) && !empty($from))
        {
            $transaction_log->whereDate('a.updated_on', '>=', date('Y-m-d', strtotime($from)));
        }
        else if (isset($to) && !empty($to))
        {
            $transaction_log->whereDate('a.updated_on', '<=', date('Y-m-d', strtotime($to)));
        }
        if (isset($term) && !empty($term))
        {
            $transaction_log->where(function($subquery) use($term)
            {
                $subquery->whereRaw('(am.email like \'%'.$term.'%\'  OR  concat(d.firstname,d.lastname) like \'%'.$term.'%\' OR am.user_code like  \'%'.$term.'%\')');
                /* $subquery->orWhere('concat(d.firstname,d.lastname)', 'like', '%'.$term.'%')
                        ->orWhere('a.remark', 'like', '%'.$term.'%')
                        ->orWhere('d.uname', 'like', '%'.$term.'%'); */
            }); 
        }
        if (isset($ewallet_id) && !empty($ewallet_id))
        {
            $transaction_log->where('a.wallet_id', '=', $ewallet_id);
        }
        if (isset($start) && isset($length))
        {
            $transaction_log->skip($start)->take($length);
        }
        if (isset($orderby) && isset($order))
        {
            $transaction_log->orderBy($orderby, $order);
        }
        else
        {
            $transaction_log->orderBy('a.id', 'desc');
        }
        if ($count)
        {
            return $transaction_log->count();
        }
        else
        {
            $transaction_log->selectRaw('a.updated_on, concat(d.firstname,\' \',d.lastname,\' (\',am.uname,\')\') as full_name, concat(e.statementline,\' \',a.remark) as description, a.transaction_type, b.wallet as wallet_name, cur.currency as currency, cur.currency_symbol, a.transaction_type, a.amt, a.paid_amt, a.handle_amt, a.current_balance, if(a.statementline_id=2,p.payment_type,\'\') as payment_type');
            $logs = $transaction_log->get();
            array_walk($logs, function(&$log)
            {
                if (!empty($log->remark))
                {
                    $log->remark = json_decode($log->remark);
                    $log->remark = Lang::get('transaction_remarks.'.$log->remark->key.'.'.($log->transaction_type == Config::get('constants.TRANSACTION_TYPE.DEBIT') ? 'DEBIT' : 'CREDIT'), $log->remark->data);
                }
                $log->updated_on = date('d-M-Y H:i:s', strtotime($log->updated_on));
                $log->amt = $log->currency_symbol.' '.number_format((($log->transaction_type == Config::get('constants.TRANSACTION_TYPE.DEBIT')) ? (-1 * $log->amt) : $log->amt), 2, '.', ',').' '.$log->currency;
                $log->paid_amt = $log->currency_symbol.' '.number_format($log->paid_amt, 2, '.', ',').' '.$log->currency;
                $log->handle_amt = $log->currency_symbol.' '.number_format($log->handle_amt, 2, '.', ',').' '.$log->currency;
                $log->current_balance = $log->currency_symbol.' '.number_format($log->current_balance, 2, '.', ',').' '.$log->currency;
                $log->transaction_type = (($log->transaction_type == Config::get('constants.TRANSACTION_TYPE.DEBIT'))) ? 'text-danger' : 'text-success';
                unset($log->currency);
                unset($log->currency_symbol);
            }); 
            return $logs;
        }
    }

}
