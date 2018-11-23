<?php
namespace App\Models\Seller;
use DB;
use Illuminate\Database\Eloquent\Model;
use Config;
use URL;

class ProductOrder extends Model
{

    public function getSubOrderDetails ($arr = array())
    {
        extract($arr);
        $order = DB::table(Config::get('tables.SUB_ORDERS').' as pso')
                ->leftjoin(Config::get('tables.ORDERS').' as po', 'po.order_id', '=', 'pso.order_id')
                ->join(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'po.account_id')
                ->leftjoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as s', 's.supplier_id', '=', 'pso.supplier_id')
                ->leftjoin(Config::get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'pso.currency_id')
                ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as psos', 'psos.order_status_id', '=', 'pso.sub_order_status_id')
                ->leftjoin(Config::get('tables.ORDER_APPROVAL_STATUS').' as oas', 'oas.approval_status_id', '=', 'pso.approval_status_id')
                ->where('po.is_deleted', Config::get('constants.OFF'))
                ->where('pso.is_deleted', Config::get('constants.OFF'))
                ->where('pso.sub_order_code', $sub_order_code)
                ->selectRaw('pso.created_on as ordered_on,pso.sub_order_code,pso.qty,pso.net_pay,pso.approval_status_id,pso.sub_order_status_id,po.order_code,concat(am.firstname,\' \',am.lastname) as customer_name,s.supplier_code,s.company_name,c.currency,c.currency_symbol,psos.status as sub_order_status,psos.order_status_class as sub_order_status_class,oas.status as approval_status');
        $order = $order->first();
        if (!empty($order))
        {
            $order->ordered_on = date('d-M-Y H:i:s', strtotime($order->ordered_on));
            $order->qty = number_format($order->qty, 0, '.', ',');
            $order->net_pay = $order->currency_symbol.' '.number_format($order->net_pay, 2, '.', ',').' '.$order->currency;
            $order->actions = [];
            $order->actions['DETAILS'] = [
                'title'=>'Details',
                'class'=>'order-details',
                'data'=>[
                ],
                'url'=>URL::to('admin/products/orders/suppliers/'.$order->sub_order_code)
            ];
            $approval_actions = DB::table(Config::get('tables.ORDER_APPROVAL_STATUS_SETTINGS').' as ss')
                    ->join(Config::get('tables.ORDER_APPROVAL_STATUS').' as oas', function($ss)
                    {
                        $ss->on('oas.approval_status_id', '=', 'ss.to_approval_status_id');
                    })
                    ->where('ss.from_approval_status_id', $order->approval_status_id)
                    ->where('ss.account_type_id', $account_type_id)
                    ->selectRaw('oas.status,oas.status_key,ss.is_comment_required,1 as type');
            $status_actions = DB::table(Config::get('tables.ORDER_STATUS_SETTINGS').' as ss')
                    ->join(Config::get('tables.ORDER_STATUS_LOOKUP').' as oas', function($ss)
                    {
                        $ss->on('oas.order_status_id', '=', 'ss.to_order_status_id');
                    })
                    ->where('ss.from_order_status_id', $order->sub_order_status_id)
                    ->where('ss.account_type_id', $account_type_id)
                    ->selectRaw('oas.status,oas.status_key,ss.is_comment_required,2 as type');
            $actions = $approval_actions->unionAll($status_actions)->get();
            array_walk($actions, function($action) use(&$order)
            {
                $order->actions[$action->status_key] = [
                    'title'=>$action->status,
                    'data'=>[
                        'status'=>$action->status_key,
                        'sub_order_code'=>$order->sub_order_code
                    ],
                    'url'=>URL::to($action->type == 1 ? 'admin/products/orders/suppliers/update-approval-status' : 'admin/products/orders/suppliers/update-status')
                ];
            });
            unset($order->currency);
            unset($order->currency_symbol);
            unset($order->approval_status_id);
            unset($order->sub_order_status_id);
        }
        return $order;
    }

    public function get_order_particulars_list ($arr = array())
    {
        extract($arr);
        $orders = DB::table(Config::get('tables.ORDER_ITEMS').' as op')
                ->leftjoin(Config::get('tables.ORDERS').' as po', 'po.order_id', '=', 'op.order_id')
                ->leftjoin(Config::get('tables.SUB_ORDERS').' as pso', 'pso.sub_order_id', '=', 'op.sub_order_id')
                ->leftjoin(Config::get('tables.SUPPLIER_PRODUCTS_LIST').' as spi', 'spi.supplier_product_id', '=', 'op.supplier_product_id')
                ->leftjoin(Config::get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'op.currency_id')
                ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as pois', 'pois.order_status_id', '=', 'op.order_item_status_id')
                ->leftjoin(Config::get('tables.PAYMENT_TYPES').' as p', 'p.payment_type_id', '=', 'po.payment_type_id')
                ->leftjoin(Config::get('tables.PAYMENT_STATUS_LOOKUPS').' as ps', 'ps.payment_status_id', '=', 'po.payment_status_id')
                ->leftjoin(Config::get('tables.COURIER_MODE_LOOKUPS').' as cm', 'cm.mode_id', '=', 'op.mode_id')
                ->where('op.is_deleted', Config::get('constants.OFF'))
                ->where('po.is_deleted', Config::get('constants.OFF'))
                ->where('pso.is_deleted', Config::get('constants.OFF'));
        if (isset($order_code) && !empty($order_code))
        {
            $orders->where('po.order_code', $order_code);
        }
        if (isset($order_id) && !empty($order_id))
        {
            $orders->where('po.order_id', $order_id);
        }
        if (isset($sub_order_code) && !empty($sub_order_code))
        {
            $orders->where('pso.sub_order_code', $sub_order_code);
        }
        if (isset($sub_order_id) && !empty($sub_order_id))
        {
            $orders->where('pso.sub_order_id', $sub_order_id);
        }
        if (isset($search_term) && !empty($search_term))
        {
            $orders->whereRaw('(spi.category like \'%'.$search_term.'%\')');
        }
        if (isset($start) && isset($length))
        {
            $orders->skip($start)->take($length);
        }
        if (isset($orderby))
        {
            $orders->orderby($orderby, $order);
        }
        if (isset($counts) && !empty($counts))
        {
            return $orders->count();
        }
        else
        {
            return $orders->selectRaw('op.*,po.order_code,pso.sub_order_code,spi.product_name,spi.product_code,spi.category_id,spi.brand_id,spi.category,pso.supplier_id,spi.replacement_service_policy_id,spi.brand_name,c.currency,c.currency_symbol,pois.status as order_item_status,pois.order_status_class as order_item_status_class,p.payment_type,ps.payment_status,ps.payment_status_class,cm.mode')->get();
        }
    }

    public function order_shipping_details ($data = array())
    {
        $query = DB::table(Config::get('tables.ORDER_SHIPPING_DETAILS').' as spsl')
                ->leftJoin(Config::get('tables.LOCATION_COUNTRY').' as lo', 'lo.country_id', '=', 'spsl.country_id')
                ->leftJoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'spsl.state_id');
        if (isset($order_id) && !empty($order_id))
        {
            $query->where('spsl.order_id', $order_id);
        }
        if (isset($sub_order_id) && !empty($sub_order_id))
        {
            $query->where('spsl.sub_order_id', $sub_order_id);
        }
        return $query->selectRaw('spsl.*,lo.country,ls.state')
                        ->first();
    }

    public function get_return_list ($arr = array())
    {

        $res = DB::table(Config::get('tables.ORDER_RETURN_MST').' as mst')
                ->leftJoin(Config::get('tables.ORDER_RETURN_TYPES_LOOKUP').' as rrl', 'mst.return_type_id', '=', 'rrl.return_type_id')
                ->leftJoin(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'mst.account_id')
                ->leftJoin(Config::get('tables.ACCOUNT_LOGIN_MST').' as lmst', 'lmst.account_id', '=', 'mst.account_id')
                ->leftJoin(Config::get('tables.ORDER_RETURN_REQUEST_TYPES_LOOKUP').' as rrtl', 'rrtl.request_type_id', '=', 'mst.request_type_id')
                ->leftJoin(Config::get('tables.SUB_ORDERS').' as op', 'mst.sub_order_code', '=', 'op.sub_order_code')
                ->leftJoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as psos', 'psos.order_status_id', '=', 'op.sub_order_status_id')
                ->leftJoin(Config::get('tables.LOCATION_STATE').' as state', 'mst.state', '=', 'state.state_id')
                ->leftJoin(Config::get('tables.LOCATION_COUNTRY').' as con', 'mst.country', '=', 'con.country_id')
                ->leftJoin(Config::get('tables.LOCATION_CITY').' as city', 'mst.city', '=', 'city.city_id')
                ->leftJoin(Config::get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'op.currency_id')
                ->leftJoin(Config::get('tables.SUPPLIER_PRODUCTS_LIST').' as spi', function($query)use($arr)
                {
                    $query->on('spi.supplier_product_id', '=', 'mst.supplier_product_id')
                    ->where('spi.supplier_id', '=', $arr['supplier_id']);
                })
                ->where('op.supplier_id', $arr['supplier_id'])
                ->selectRaw('city.city ,state.state as state_name,con.country as country_name,c.currency_symbol,psos.status as order_status,psos.order_status_class as order_class,op.created_on as order_created_on,mst.*,op.amount,op.net_pay,rrtl.category,rrl.return_type, concat(am.firstname,\' \',am.lastname) as fullname,spi.product_name,rrl.return_type_id');
        $res->where('op.is_deleted', Config::get('constants.OFF'));

        $res->orderby('mst.created_on', 'desc');

        if (!empty($arr['filterTerms']))
        {
            $subsql = '';
            $arr['filterTerms'] = !is_array($arr['filterTerms']) ? array(
                $arr['filterTerms']) : $arr['filterTerms'];
            if (in_array('full_name', $arr['filterTerms']))
            {
                $subsql[] = 'CONCAT(am.firstname,\' \',am.lastname) LIKE (\'%'.$arr['search_term'].'%\')';
            }

            if (array_key_exists('order_code', $arr['filterTerms']))
            {
                $subsql[] = 'spo.sub_order_code = \''.$arr['filterTerms']['order_code'].'\'';
            }
            if (!empty($subsql))
            {
                $res->whereRaw('('.implode(' OR ', $subsql).')');
            }
        }
        if (isset($arr['status_list']))
        {
            $res->where('spo.sub_order_status_id', $arr['status_list']);
        }
        if (!empty($arr['search_term']))
        {
            $res->whereRaw('(CONCAT(am.firstname,\' \',am.lastname)like \'%'.$arr['search_term'].'%\'  OR  spo.sub_order_code  like \'%'.$arr['search_term'].'%\' )');
        }
        if (isset($arr['order_id']) && !empty($arr['order_id']))
        {
            $res->where('op.order_id', $arr['order_id']);
        }
        if (!empty($arr['start_date']) && empty($arr['end_date']))
        {
            $res->whereRaw('DATE(mst.created_on) >=\''.date('Y-m-d', strtotime($arr['start_date'])).'\'');
        }
        else if (empty($arr['start_date']) && !empty($arr['end_date']))
        {
            $res->whereRaw('DATE(mst.created_on) <=\''.date('Y-m-d', strtotime($arr['end_date'])).'\'');
        }
        else if ((!empty($arr['start_date'])) && (!empty($arr['end_date'])))
        {
            $res->whereRaw('DATE(mst.created_on) >=\''.date('Y-m-d', strtotime($arr['start_date'])).'\'');
            $res->whereRaw('DATE(mst.created_on) <=\''.date('Y-m-d', strtotime($arr['end_date'])).'\'');
        }
        if (isset($arr['start']) && isset($arr['length']))
        {
            $res->skip($arr['start'])->take($arr['length']);
        }
//        if (isset($arr['orderby']))
//        {
//            $res->orderby($arr['orderby'], $arr['order']);
//        }
        else
        {
            $res->orderby('mst.created_on', 'asc');
            return $res->first();
        }
        if (isset($arr['counts']) && $arr['counts'] == true)
        {
            return $res->count();
        }
        else
        {
            return $res->get();
        }
    }

}
