<?php
namespace App\Models\Admin;
use DB;
use Illuminate\Database\Eloquent\Model;
use Config;
use URL;

class SupplierProductOrder extends Model
{

    public function __construct (&$commonObj)
    {
        parent::__construct();
        $this->commonObj = $commonObj;
    }

    /**
     * Get Order List.
     *
     * @param  array  $arr
     * @param  bool  $count
     * @return array
     */
    public function getOrderList ($arr = array(), $count = false)
    {
        extract($arr);
        $orderQuery = DB::table(Config::get('tables.ORDERS').' as po')
                ->join(Config::get('tables.PAY').' as pa', 'pa.order_id', '=', 'po.order_id')
                ->join(Config::get('tables.ORDER_SHIPPING_DETAILS').' as sd', 'sd.order_id', '=', 'po.order_id')
                ->where('po.is_deleted', Config::get('constants.OFF'));
        if (isset($search_term) && !empty($search_term))
        {
            $search_term = '%'.$search_term.'%';
            $orderQuery->where(function($st)
            {
                $st->where('po.order_code', 'like', $search_term);
            });
        }
        if (isset($from) && !empty($from))
        {
            $orderQuery->where('po.created_on', '>=', date('Y-m-d', strtotime($from)));
        }
        if (isset($to) && !empty($to))
        {
            $orderQuery->where('po.created_on', '<=', date('Y-m-d', strtotime($to)));
        }
        if (isset($order_status_id) && !is_null($order_status_id))
        {
            if (is_array($order_status_id))
            {
                $orderQuery->whereIn('po.order_status_id', $order_status_id);
            }
            else
            {
                $orderQuery->where('po.order_status_id', $order_status_id);
            }
        }
        if (isset($partner_id) && !empty($partner_id))
        {
            $orderQuery->where('po.partner_id', $partner_id);
        }
        if (isset($start) && isset($length))
        {
            $orderQuery->skip($start)->take($length);
        }
        if (isset($orderby) && !empty($orderby))
        {
            $orderQuery->orderby($orderby, $order);
        }
        else
        {
            $orderQuery->orderby('po.created_on', 'DESC');
        }
        if ($count)
        {
            return $orderQuery->count();
        }
        else
        {
            $orders = $orderQuery->leftjoin(Config::get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'po.currency_id')
                    ->leftjoin(Config::get('tables.PAYMENT_TYPES').' as p', 'p.payment_type_id', '=', 'pa.payment_type_id')
                    ->leftjoin(Config::get('tables.PAYMENT_STATUS_LOOKUPS').' as ps', 'ps.payment_status_id', '=', 'po.payment_status')
                    ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as os', 'os.order_status_id', '=', 'po.order_status_id')
                    ->leftjoin(Config::get('tables.ORDER_APPROVAL_STATUS').' as oas', 'oas.approval_status_id', '=', 'po.approval_status_id')
                    ->selectRaw('po.order_code,sd.full_name as customer_name,po.qty,po.sub_total,po.net_pay,c.currency,c.currency_symbol,p.payment_type,ps.payment_status,ps.payment_status_class,po.approval_status_id,po.order_status_id,os.status as order_status,os.order_status_class,po.created_on as ordered_on,oas.status as approval_status')
                    ->get();
            array_walk($orders, function(&$order) use($account_type_id)
            {
                $order->ordered_on = date('d-M-Y H:i:s', strtotime($order->ordered_on));
                $order->qty = number_format($order->qty, 0, '.', ',');
                $order->sub_total = implode(' ', [$order->currency_symbol, number_format($order->sub_total, 2, '.', ','), $order->currency]);
                $order->net_pay = implode(' ', [$order->currency_symbol, number_format($order->net_pay, 2, '.', ','), $order->currency]);
                $order->actions = [];
                $order->actions['DETAILS'] = [
                    'title'=>'Details',
                    'class'=>'order-details',
                    'data'=>[
                    ],
                    'url'=>URL::to('admin/products/orders/'.$order->order_code)
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
                        ->where('ss.from_order_status_id', $order->order_status_id)
                        ->where('ss.account_type_id', $account_type_id)
                        ->selectRaw('oas.status,oas.status_key,ss.is_comment_required,2 as type');
                $actions = $approval_actions->unionAll($status_actions)->get();
                array_walk($actions, function($action) use(&$order)
                {
                    $order->actions[$action->status_key] = [
                        'title'=>$action->status,
                        'data'=>[
                            'status'=>$action->status_key,
                            'order_code'=>$order->order_code
                        ],
                        'url'=>URL::to($action->type == 1 ? 'admin/products/orders/update-approval-status' : 'admin/products/orders/update-status')
                    ];
                });
                unset($order->currency);
                unset($order->currency_symbol);
                unset($order->approval_status_id);
                unset($order->order_status_id);
            });
            return $orders;
        }
    }

    public function getOrderDetails ($arr = array(), $detailed = false)
    {
        extract($arr);
        $orderQuery = DB::table(Config::get('tables.ORDERS').' as po')
                ->join(Config::get('tables.ORDER_SHIPPING_DETAILS').' as sd', 'sd.order_id', '=', 'po.order_id')
                ->leftjoin(Config::get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'po.currency_id')
                ->leftjoin(Config::get('tables.PAYMENT_TYPES').' as p', 'p.payment_type_id', '=', 'po.payment_type_id')
                ->leftjoin(Config::get('tables.PAYMENT_STATUS_LOOKUPS').' as ps', 'ps.payment_status_id', '=', 'po.payment_status_id')
                ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as os', 'os.order_status_id', '=', 'po.order_status_id')
                ->leftjoin(Config::get('tables.ORDER_APPROVAL_STATUS').' as oas', 'oas.approval_status_id', '=', 'po.approval_status_id')
                ->selectRaw('po.order_code,sd.full_name as customer_name,po.qty,po.sub_total,po.net_pay,c.currency,c.currency_symbol,p.payment_type,ps.payment_status,ps.payment_status_class,po.approval_status_id,po.order_status_id,os.status as order_status,os.order_status_class,po.created_on as ordered_on,oas.status as approval_status')
                ->where('po.is_deleted', Config::get('constants.OFF'));
        $orderQuery->where('po.order_code', $order_code);
        $order = $orderQuery->first();
        if (!empty($order))
        {
            $order->ordered_on = date('d-M-Y H:i:s', strtotime($order->ordered_on));
            $order->qty = number_format($order->qty, 0, '.', ',');
            $order->sub_total = implode(' ', [$order->currency_symbol, number_format($order->sub_total, 2, '.', ','), $order->currency]);
            $order->net_pay = implode(' ', [$order->currency_symbol, number_format($order->net_pay, 2, '.', ','), $order->currency]);
            $order->actions = [];
            $order->actions['DETAILS'] = [
                'title'=>'Details',
                'class'=>'order-details',
                'data'=>[
                ],
                'url'=>URL::to('admin/products/orders/'.$order->order_code)
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
                    ->where('ss.from_order_status_id', $order->order_status_id)
                    ->where('ss.account_type_id', $account_type_id)
                    ->selectRaw('oas.status,oas.status_key,ss.is_comment_required,2 as type');
            $actions = $approval_actions->unionAll($status_actions)->get();
            array_walk($actions, function($action) use(&$order)
            {
                $order->actions[$action->status_key] = [
                    'title'=>$action->status,
                    'data'=>[
                        'status'=>$action->status_key,
                        'order_code'=>$order->order_code
                    ],
                    'url'=>URL::to($action->type == 1 ? 'admin/products/orders/update-approval-status' : 'admin/products/orders/update-status')
                ];
            });
            unset($order->currency);
            unset($order->currency_symbol);
            unset($order->approval_status_id);
            unset($order->order_status_id);
        }
        return $order;
    }

    public function updateOrderStatus ($arr = array())
    {
        $comments = null;
        extract($arr);
        $OrderQuery = DB::table(Config::get('tables.ORDERS').' as o')
                ->join(Config::get('tables.ORDER_STATUS_SETTINGS').' as ss', function($ss) use($order_status_id, $account_type_id)
                {
                    $ss->on('ss.from_order_status_id', '=', 'o.order_status_id')
                    ->where('ss.to_order_status_id', '=', $order_status_id)
                    ->where('ss.account_type_id', '=', $account_type_id);
                })
                ->where('o.order_code', $order_item_code)
                ->select('o.order_id');
        $Order = $OrderQuery->first();
        if ($Order)
        {
            DB::beginTransaction();
            $data = [];
            $data['order_status_id'] = $order_status_id;
            $data['account_type_id'] = $account_type_id;
            $data['account_id'] = $account_id;
            $data['comments'] = $comments;
            $sub_order_codes = DB::table(Config::get('tables.SUB_ORDERS'))
                    ->where('order_id', $Order->order_id)
                    ->where('is_deleted', Config::get('constants.OFF'))
                    ->where('sub_order_status_id', '!=', $order_status_id)
                    ->lists('sub_order_code');
            foreach ($sub_order_codes as $sub_order_code)
            {
                $data['sub_order_code'] = $sub_order_code;
                $this->updateSubOrderStatus($data);
            }
            if (DB::table(Config::get('tables.ORDERS'))
                            ->where('order_id', $Order->order_id)
                            ->where('order_status_id', $order_status_id)
                            ->exists())
            {
                DB::commit();
                return true;
            }
            DB::rollback();
        }
        return false;
    }

    public function updateSubOrderStatus ($arr = array())
    {
        $res = false;
        $comments = null;
        extract($arr);
        $SubOrder = DB::table(Config::get('tables.SUB_ORDERS').' as so')
                ->join(Config::get('tables.ORDER_STATUS_SETTINGS').' as ss', function($ss) use($order_status_id, $account_type_id)
                {
                    $ss->on('ss.from_order_status_id', '=', 'so.sub_order_status_id')
                    ->where('ss.to_order_status_id', '=', $order_status_id)
                    ->where('ss.account_type_id', '=', $account_type_id);
                })
                ->where('so.is_deleted', Config::get('constants.OFF'))
                ->select('so.sub_order_id')
                ->where('so.sub_order_code', $sub_order_code)
                ->first();
        if ($SubOrder)
        {
            DB::beginTransaction();
            $data = [];
            $data['order_item_status_id'] = $order_status_id;
            $data['account_type_id'] = $account_type_id;
            $data['account_id'] = $account_id;
            $data['comments'] = $comments;
            $order_item_codes = DB::table(Config::get('tables.ORDER_ITEMS'))
                    ->where('sub_order_id', $SubOrder->sub_order_id)
                    ->where('is_deleted', Config::get('constants.OFF'))
                    ->where('order_item_status_id', '!=', $order_status_id)
                    ->lists('order_item_code');
            foreach ($order_item_codes as $order_item_code)
            {
                $data['order_item_code'] = $order_item_code;
                $this->updateOrderItemStatus($data);
            }
            if (DB::table(Config::get('tables.SUB_ORDERS'))
                            ->where('sub_order_id', $SubOrder->sub_order_id)
                            ->where('sub_order_status_id', $order_status_id)
                            ->exists())
            {
                DB::commit();
                return true;
            }
            DB::rollback();
        }
        return $res;
    }

    public function getSubOrderList ($arr = array(), $count = false)
    {
        extract($arr);
        $orders = DB::table(Config::get('tables.SUB_ORDERS').' as pso')
                ->leftjoin(Config::get('tables.ORDERS').' as po', 'po.order_id', '=', 'pso.order_id')
                ->join(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'po.account_id')
                ->leftjoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as s', 's.supplier_id', '=', 'pso.supplier_id')
                ->leftjoin(Config::get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'pso.currency_id')
                ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as psos', 'psos.order_status_id', '=', 'pso.sub_order_status_id')
                ->leftjoin(Config::get('tables.ORDER_APPROVAL_STATUS').' as oas', 'oas.approval_status_id', '=', 'pso.approval_status_id')
                ->where('po.is_deleted', Config::get('constants.OFF'))
                ->where('pso.is_deleted', Config::get('constants.OFF'));
        if (isset($order_code) && !empty($order_code))
        {
            $orders->where('po.order_code', $order_code);
        }
        if (isset($search_term) && !empty($search_term))
        {
            $orders->whereRaw('(pc.category like \'%'.$search_term.'%\')');
        }
        if (isset($order_status_id) && !empty($order_status_id))
        {
            if (is_array($order_status_id))
            {
                $orders->whereIn('pso.sub_order_status_id', $order_status_id);
            }
            else
            {
                $orders->where('pso.sub_order_status_id', $order_status_id);
            }
        }
        if (isset($supplier_id) && !empty($supplier_id))
        {
            $orders->where('pso.supplier_id', $supplier_id);
        }
        if (isset($start) && isset($length))
        {
            $orders->skip($start)->take($length);
        }
        if (isset($orderby))
        {
            $orders->orderby($orderby, $order);
        }
        if ($count)
        {
            return $orders->count();
        }
        else
        {
            $orders = $orders->selectRaw('pso.created_on as ordered_on,pso.sub_order_code,pso.qty,pso.net_pay,pso.approval_status_id,pso.sub_order_status_id,po.order_code,concat(am.firstname,\' \',am.lastname) as customer_name,s.supplier_code,s.company_name,c.currency,c.currency_symbol,psos.status as sub_order_status,psos.order_status_class as sub_order_status_class,oas.status as approval_status')
                    ->get();
            array_walk($orders, function(&$order) use ($account_type_id)
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
            });
            return $orders;
        }
    }

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
                ->selectRaw('pso.created_on as ordered_on, pso.sub_order_code, pso.qty, pso.net_pay, pso.approval_status_id, pso.sub_order_status_id, po.order_code, concat(am.firstname,\' \',am.lastname) as customer_name, s.supplier_code, s.company_name, c.currency, c.currency_symbol, psos.status as sub_order_status, psos.order_status_class as sub_order_status_class, oas.status as approval_status');
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
                ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi', 'spi.supplier_product_id', '=', 'op.supplier_product_id')
                ->leftjoin(Config::get('tables.PRODUCTS').' as pr', 'pr.product_id', '=', 'spi.product_id')
                ->leftjoin(Config::get('tables.PRODUCT_DETAILS').' as pi', 'pi.product_id', '=', 'spi.product_id')
                ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES').' as pc', 'pc.category_id', '=', 'pr.category_id')
                ->leftjoin(Config::get('tables.PRODUCT_BRANDS').' as pb', 'pb.brand_id', '=', 'pr.brand_id')
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
            $orders->whereRaw('(pc.category like \'%'.$search_term.'%\')');
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
            return $orders->selectRaw('op.*,po.order_code,pso.sub_order_code,pr.product_name,pi.product_code,pr.category_id,pr.brand_id,pc.category,pso.supplier_id,pc.replacement_service_policy_id,pb.brand_name,c.currency,c.currency_symbol,pois.status as order_item_status,pois.order_status_class as order_item_status_class,p.payment_type,ps.payment_status,ps.payment_status_class,cm.mode')->get();
        }
    }

    public function orderItemDetails ($arr = array())
    {
        extract($arr);
        $query = DB::table(Config::get('tables.ORDER_ITEMS').' as op')
                ->leftjoin(Config::get('tables.ORDERS').' as po', 'po.order_id', '=', 'op.order_id')
                ->leftjoin(Config::get('tables.SUB_ORDERS').' as pso', 'pso.sub_order_id', '=', 'op.sub_order_id')
                ->leftjoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as s', 's.supplier_id', '=', 'pso.supplier_id')
                ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi', 'spi.supplier_product_id', '=', 'op.supplier_product_id')
                ->leftjoin(Config::get('tables.PRODUCTS').' as pi', 'pi.product_id', '=', 'spi.product_id')
                ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES').' as pc', 'pc.category_id', '=', 'pi.category_id')
                ->leftjoin(Config::get('tables.PRODUCT_BRANDS').' as pb', 'pb.brand_id', '=', 'pi.brand_id')
                ->leftjoin(Config::get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'op.currency_id')
                ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as pos', 'pos.order_status_id', '=', 'po.order_status_id')
                ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as psos', 'psos.order_status_id', '=', 'pso.sub_order_status_id')
                ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as pois', 'pois.order_status_id', '=', 'op.order_item_status_id')
                ->leftjoin(Config::get('tables.PAYMENT_TYPES').' as p', 'p.payment_type_id', '=', 'po.payment_type_id')
                ->leftjoin(Config::get('tables.PAYMENT_STATUS_LOOKUPS').' as ps', 'ps.payment_status_id', '=', 'ps.payment_status_id')
                ->leftjoin(Config::get('tables.COURIER_MODE_LOOKUPS').' as cm', 'cm.mode_id', '=', 'op.mode_id')
                ->where('op.is_deleted', Config::get('constants.OFF'))
                ->selectRaw('op.*,po.order_code,pso.sub_order_code,pi.product_name,pi.product_code,pi.category_id,pi.brand_id,pc.category,op.replacement_due_days,pb.brand_name,s.supplier_id,s.supplier_code,s.company_name,c.currency,c.currency_symbol,po.order_status_id,pos.status as order_status,pos.order_status_class,pso.sub_order_status_id,psos.status as sub_order_status,psos.order_status_class as sub_order_status_class,pois.status as order_item_status,pois.order_status_class as order_item_status_class,p.payment_type,ps.payment_status,ps.payment_status_class,cm.mode');
        if (isset($order_item_id))
        {
            $query->where('op.order_item_id', $order_item_id);
        }
        if (isset($order_item_code))
        {
            $query->where('op.order_item_code', $order_item_code);
        }
        return $query->first();
    }

    /*
     * Function Name        : order_item_courier_info
     * Params               : order_item_id, replacement_time
     * Returns              : order_item_courier_info OR FALSE
     */

    public function save_courier_zone ($arr = array())
    {
        // print_r($arr); exit;
        extract($arr);
        return DB::table(Config::get('tables.COURIER_SERVICE_PROVIDERS').' as ocp')
                        ->where('courier_id', $courier_id)
                        ->update(array(
                            'zones'=>$zones));
    }

    public function order_item_courier_info ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.ORDER_SHIPMENT_COURIER_LOG').' as ocp')
                        ->leftjoin(Config::get('tables.COURIER_SERVICE_PROVIDERS').' as c', 'c.courier_id', '=', 'ocp.courier_id')
                        ->where('order_item_id', $order_item_id)
                        ->where('replacement_time', $replacement_time)
                        ->select('ocp.*', 'c.courier')
                        ->first();
    }

    public static function order_item_status_info ($arr = array())
    {
        $account_type_id = Config::get('constants.ACCOUNT_TYPE.USER');
        extract($arr);
        $query = DB::table(Config::get('tables.ORDER_STATUS_LOOKUP').' as ss')
                ->leftJoin(Config::get('tables.ORDER_STATUS_ACCOUNT_TYPE_MESSAGES').' as sl', function($ss) use($account_type_id)
                {
                    $ss->on('sl.order_status_id', '=', 'ss.order_status_id')
                    ->where('sl.account_type_id', '=', $account_type_id)
                    ->where('sl.is_visible', '=', Config::get('constants.ON'));
                })
                ->leftJoin(Config::get('tables.ORDER_STATUS_LOG').' as osl', function($condition) use($order_item_id, $replacement_time)
                {
                    $condition->on('osl.to_order_status_id', '=', 'ss.order_status_id')
                    ->where('osl.order_item_id', '=', $order_item_id)
                    ->where('osl.replacement_time', '=', $replacement_time);
                })
                ->where(function($subquery) use($order_item_status_id)
                {
                    $subquery->where('sl.is_visible', Config::get('constants.ON'))
                    ->orWhere('sl.order_status_id', $order_item_status_id);
                })
                ->selectRaw('osl.updated_on,ss.status,if(osl.to_order_status_id<='.$order_item_status_id.',1,0) as completed,if(osl.to_order_status_id<='.$order_item_status_id.',sl.completed_msg,sl.pending_msg) as status_msg');
        if ($order_item_status_id == Config::get('constants.ORDER_STATUS.CANCELLED'))
        {
            $query->whereNotNull('osl.updated_on');
        }
        return $query->get();
    }

    /*
     * Function Name        : updateOrderItemStatus
     * Params               : order_item_code, order_item_status_id,account_id,account_type_id,comments(optional)
     * Returns              : Detail OR FALSE
     */

    public function updateOrderItemStatus ($arr = array())
    {
        $order_item_log_id = $l2 = $order_log_id = false;
        extract($arr);
        $comments = (isset($comments) && !empty($comments)) ? $comments : '';
        $OID = $this->orderItemDetails(['order_item_code'=>$order_item_code]);
        if (!empty($OID))
        {
            $update_order_particulars_fields = array();
            $update_order_particulars_fields['order_item_status_id'] = $order_item_status_id;
            $update_order_particulars_fields['updated_on'] = date('Y-m-d H:i:s');
            $update_order_particulars_fields['updated_by'] = $account_id;
            DB::beginTransaction();
            $update_order_particulars = DB::table(Config::get('tables.ORDER_ITEMS'))
                    ->where('order_item_id', $OID->order_item_id);
            switch ($order_item_status_id)
            {
                case Config::get('constants.ORDER_STATUS.PLACED'):
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    if ($upstatus)
                    {
                        DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT'))
                                ->increment('commited_stock', $OID->qty)
                                ->where('supplier_product_id', $OID->supplier_product_id);
                    }
                    break;
                case Config::get('constants.ORDER_STATUS.APPROVED'):
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.PLACED')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    break;
                case Config::get('constants.ORDER_STATUS.PACKED'):
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.APPROVED')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    break;
                case Config::get('constants.ORDER_STATUS.DISPATCHED'):
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.PACKED')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    if ($upstatus)
                    {
                        DB::table(Config::get('tables.ORDER_SHIPMENT_COURIER_LOG'))
                                ->insert($shipment);
                        DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT'))
                                ->where('supplier_product_id', $OID->supplier_product_id)
                                ->update(array(
                                    'stock_on_hand'=>DB::raw('stock_on_hand - '.$OID->qty),
                                    'commited_stock'=>DB::raw('commited_stock - '.$OID->qty),
                                    'sold_items'=>DB::raw('sold_items + '.$OID->qty)));
                    }

                    break;
                case Config::get('constants.ORDER_STATUS.IN_SHIPPING'):
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.DISPATCHED')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    break;
                case Config::get('constants.ORDER_STATUS.REACHED_HUB'):
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.IN_SHIPPING')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    break;
                case Config::get('constants.ORDER_STATUS.DELIVERED'):
                    $update_order_particulars_fields['replacement_due_date'] = date('Y-m-d H:i:s', strtotime('+'.$OID->replacement_due_days.' days'));
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.REACHED_HUB')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    break;
                case Config::get('constants.ORDER_STATUS.SERVICE_IN_PROGRESS'):
                    $update_order_particulars_fields['replacement_due_date'] = date('Y-m-d H:i:s', strtotime('+'.$OID->replacement_due_days.' days'));
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.DELIVERED')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    break;
                case Config::get('constants.ORDER_STATUS.SERVICE_COMPLETED'):
                    $update_order_particulars_fields['replacement_due_date'] = date('Y-m-d H:i:s', strtotime('+'.$OID->replacement_due_days.' days'));
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.SERVICE_IN_PROGRESS')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    break;
                case Config::get('constants.ORDER_STATUS.CANCELLED'):
                    $update_order_particulars->whereIn('order_item_status_id', Config::get('constants.ORDER_STATUS_CANCELABLE_STATUS'));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    if ($upstatus)
                    {
                        $stock = [];
                        if (in_array($OID->order_item_status_id, [Config::get('constants.ORDER_STATUS.PLACED'), Config::get('constants.ORDER_STATUS.APPROVED'), Config::get('constants.ORDER_STATUS.PACKED')]))
                        {
                            $stock['commited_stock'] = DB::raw('commited_stock + '.$OID->qty);
                        }
                        else
                        {
                            $stock['stock_on_hand'] = DB::raw('stock_on_hand + '.$OID->qty);
                            $stock['sold_items'] = DB::raw('sold_items - '.$OID->qty);
                        }
                        DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT'))
                                ->where('supplier_product_id', $OID->supplier_product_id)
                                ->update($stock);
                        DB::table(Config::get('tables.SUB_ORDERS'))
                                ->where('sub_order_id', $OID->sub_order_id)
                                ->update(array('qty'=>DB::table(Config::get('tables.ORDER_ITEMS'))->where('sub_order_id', $OID->sub_order_id)->where('is_deleted', Config::get('constants.OFF'))->where('order_item_status_id', '<>', Config::get('constants.ORDER_STATUS.CANCELLED'))->sum('qty')));
                        DB::table(Config::get('tables.ORDERS'))
                                ->where('order_id', $OID->order_id)
                                ->update(array('qty'=>DB::table(Config::get('tables.ORDER_ITEMS'))->where('order_id', $OID->order_id)->where('is_deleted', Config::get('constants.OFF'))->where('order_item_status_id', '<>', Config::get('constants.ORDER_STATUS.CANCELLED'))->sum('qty')));
                    }
                    break;
                case Config::get('constants.ORDER_STATUS.RETURN_REFUND'):
                case Config::get('constants.ORDER_STATUS.RETURN_REPLACE'):
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.DELIVERED'), Config::get('constants.ORDER_STATUS.SERVICE_IN_PROGRESS'), Config::get('constants.ORDER_STATUS.SERVICE_COMPLETED')))
                            ->whereRaw('TIMESTAMPDIFF(SECOND,date(replacement_due_date),NOW())>=0');
                    $update_order_particulars_fields['replacement_due_date'] = date('Y-m-d H:i:s', strtotime('+'.$OID->replacement_due_days.' days'));
                    $update_order_particulars_fields['replacement_time'] = $OID->replacement_time + 1;
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    break;
                case Config::get('constants.ORDER_STATUS.REFUND_APPROVED'):
                case Config::get('constants.ORDER_STATUS.REFUND_REJECTED'):
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.RETURN_REFUND')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    break;
                case Config::get('constants.ORDER_STATUS.REFUND_PICKED'):
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.REFUND_APPROVED')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    break;
                case Config::get('constants.ORDER_STATUS.REFUND_DISPATCHED'):
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.REFUND_PICKED')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    break;
                case Config::get('constants.ORDER_STATUS.REFUNDED'):
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.REFUND_DISPATCHED')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    break;
                case Config::get('constants.ORDER_STATUS.REPLACE_APPROVED'):
                case Config::get('constants.ORDER_STATUS.REPLACE_REJECTED'):
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.RETURN_REPLACE')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    break;
                case Config::get('constants.ORDER_STATUS.REPLACE_PICKED'):
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.REPLACE_APPROVED')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    break;
                case Config::get('constants.ORDER_STATUS.REPLACE_DISPATCHED'):
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.REPLACE_PICKED')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                    break;
                case Config::get('constants.ORDER_STATUS.REPLACED'):
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.REPLACE_DISPATCHED')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                case Config::get('constants.ORDER_STATUS.CANCEL_RETURN_DISPATCHED'):
                    $update_order_particulars->whereIn('order_item_status_id', array(Config::get('constants.ORDER_STATUS.CANCELLED')));
                    $upstatus = $update_order_particulars->update($update_order_particulars_fields);
                default :
                    $upstatus = false;
            }
            if ($upstatus)
            {
                $log = [];
                $log['order_item_id'] = $OID->order_item_id;
                $log['replacement_time'] = $OID->replacement_time;
                $log['from_order_status_id'] = $OID->order_item_status_id;
                $log['to_order_status_id'] = $order_item_status_id;
                $log['updated_by'] = $account_id;
                $log['comments'] = $comments;
                $order_item_log_id = DB::table(Config::get('tables.ORDER_STATUS_LOG'))
                        ->insertGetId($log);
                $sub_order_status = DB::table(Config::get('tables.ORDER_ITEMS'))
                        ->where('sub_order_id', $OID->sub_order_id)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->groupby('order_item_status_id')
                        ->selectRaw('(sum(qty)-sum(if(order_item_status_id='.Config::get('constants.ORDER_STATUS.CANCELLED').',qty,0))) as total_qty,order_item_status_id')
                        ->orderby('total_qty', 'DESC')
                        ->lists('total_qty', 'order_item_status_id');
                if (!empty($sub_order_status))
                {
                    $sub_order_status_ids = array_keys($sub_order_status);
                    if (in_array(Config::get('constants.ORDER_STATUS.CANCELLED'), $sub_order_status_ids) && $sub_order_status_ids[0] == Config::get('constants.ORDER_STATUS.CANCELLED'))
                    {
                        $sub_order_status_id = $sub_order_status_ids[1];
                        $qty = $sub_order_status[$sub_order_status_id];
                    }
                    else
                    {
                        $sub_order_status_id = $sub_order_status_ids[0];
                        $qty = $sub_order_status[$sub_order_status_id];
                    }
                    if (DB::table(Config::get('tables.SUB_ORDERS'))
                                    ->where('sub_order_id', $OID->sub_order_id)
                                    ->where('qty', $qty)
                                    ->update(array('updated_on'=>date('Y-m-d H:i:s'), 'sub_order_status_id'=>$sub_order_status_id)))
                    {
                        $log = [];
                        $log['sub_order_id'] = $OID->sub_order_id;
                        $log['replacement_time'] = $OID->replacement_time;
                        $log['from_order_status_id'] = $OID->sub_order_status_id;
                        $log['to_order_status_id'] = $sub_order_status_id;
                        $log['comments'] = $comments;
                        $log['updated_by'] = $account_id;
                        $sub_order_log_id = DB::table(Config::get('tables.ORDER_STATUS_LOG'))
                                ->insertGetId($log);
                    }
                }
                $order_status = DB::table(Config::get('tables.SUB_ORDERS'))
                        ->where('order_id', $OID->order_id)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->groupby('sub_order_status_id')
                        ->selectRaw('(sum(qty)-sum(if(sub_order_status_id='.Config::get('constants.ORDER_STATUS.CANCELLED').',qty,0))) as total_qty,sub_order_status_id')
                        ->orderby('total_qty', 'DESC')
                        ->lists('total_qty', 'sub_order_status_id');
                if (!empty($order_status))
                {
                    $order_status_ids = array_keys($order_status);
                    if (in_array(Config::get('constants.ORDER_STATUS.CANCELLED'), $order_status_ids) && $order_status_ids[0] == Config::get('constants.ORDER_STATUS.CANCELLED'))
                    {
                        $order_status_id = $order_status_ids[1];
                        $qty = $order_status[$order_status_id];
                    }
                    else
                    {
                        $order_status_id = $order_status_ids[0];
                        $qty = $order_status[$order_status_id];
                    }
                    if (DB::table(Config::get('tables.ORDERS'))
                                    ->where('order_id', $OID->order_id)
                                    ->where('qty', $qty)
                                    ->update(array('updated_on'=>date('Y-m-d H:i:s'), 'order_status_id'=>$order_status_id)))
                    {
                        $log = [];
                        $log['order_id'] = $OID->order_id;
                        $log['replacement_time'] = $OID->replacement_time;
                        $log['from_order_status_id'] = $OID->order_status_id;
                        $log['to_order_status_id'] = $order_status_id;
                        $log['comments'] = $comments;
                        $log['updated_by'] = $account_id;
                        $order_log_id = DB::table(Config::get('tables.ORDER_STATUS_LOG'))
                                ->insertGetId($log);
                    }
                }

                if ($order_item_log_id || $sub_order_log_id || $order_log_id)
                {
                    $emailData = [];
                    $OID = $this->orderItemDetails(['order_item_code'=>$order_item_code]);
                    $notify_status = DB::table(Config::get('tables.ORDER_STATUS_LOG').' as log')
                            ->join(Config::get('tables.ORDER_STATUS_SETTINGS').' as oss', function($oss) use($account_type_id)
                            {
                                $oss->on('oss.from_order_status_id', '=', 'log.from_order_status_id')
                                ->on('oss.to_order_status_id', '=', 'log.to_order_status_id')
                                ->where('oss.account_type_id', '=', $account_type_id);
                            })
                            ->leftJoin(Config::get('tables.ORDER_STATUS_NOTIFICATIONS').' as osn', 'osn.oss_id', '=', 'oss.oss_id')
                            ->leftJoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as fos', 'fos.order_status_id', '=', 'oss.from_order_status_id')
                            ->leftJoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as tos', 'tos.order_status_id', '=', 'oss.to_order_status_id')
                            ->join(Config::get('tables.ACCOUNT_TYPES').' as at', 'at.id', '=', 'osn.account_type_id')
                            ->where('log.status_log_id', ($order_log_id ? $order_log_id : ($sub_order_log_id ? $sub_order_log_id : $order_item_log_id)))
                            ->select('fos.status_key as from_key', 'tos.status_key as to_key', 'osn.account_type_id', 'at.account_type_key', 'osn.sms', 'osn.email', 'osn.notification')
                            ->get();
                    if ($notify_status)
                    {
                        $t = '';
                        if ($order_log_id)
                        {
                            $emailData['order_details'] = $this->order_details($OID->order_id);
                            $t = 'ORDER';
                        }
                        else if ($sub_order_log_id)
                        {
                            $emailData['sub_order_details'] = $this->sub_order_details($OID->sub_order_id);
                            $t = 'SUB_ORDER';
                        }
                        else if ($order_item_log_id)
                        {
                            $emailData['order_item_details'] = $OID;
                            $t = 'ORDER_ITEM';
                        }
                        foreach ($notify_status as $n)
                        {
                            if ($n->account_type_id == Config::get('constants.ACCOUNT_TYPE.ADMIN'))
                            {
                                $id = Config::get('constants.ADMIN_ACCOUNT_ID');
                            }
                            elseif ($n->account_type_id == Config::get('constants.ACCOUNT_TYPE.USER'))
                            {
                                $id = $OID->account_id;
                            }
                            elseif ($n->account_type_id == Config::get('constants.ACCOUNT_TYPE.SUPPLIER'))
                            {
                                $id = $OID->supplier_id;
                            }
                            elseif ($n->account_type_id == Config::get('constants.ACCOUNT_TYPE.PARTNER'))
                            {
                                $id = $OID->partner_id;
                            }
                            ShoppingPortal::notify($t.'.'.$n->from_key.'_TO_'.$n->to_key.'.'.$n->account_type_key, $id, $n->account_type_id, $emailData, $n->email, $n->sms, $n->notification);
                        }
                    }
                    DB::commit();
                    return $OID;
                }
            }
        }
        DB::rollback();
        return false;
    }

    public function supplier_order_list ($arr)
    {
        $res = DB::table(Config::get('tables.SUB_ORDERS').' as spo')
                ->join(Config::get('tables.ORDER_STATUS_LOOKUP').' as spol', 'spo.order_status_id', '=', 'spol.order_status_id')
                ->join(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'spo.account_id')
                ->join(Config::get('tables.ACCOUNT_LOGIN_MST').' as lmst', 'lmst.account_id', '=', 'spo.account_id')
                ->join(Config::get('tables.ACCOUNT_SUPPLIERS').' as asu', 'asu.supplier_id', '=', 'spo.supplier_id')
                ->selectRaw(' spo.sub_order_id, asu.supplier_code, lmst.email, spo.order_id, spo.order_status_id, spo.order_code,spo.account_id, spo.qty, spol.status, spo.amount, spo.discount, spo.net_pay, spo.payment_type_id,spo.created_on, spo.updated_on, concat(am.firstname,\' \',am.lastname) as fullname');
        $res->where('spo.is_deleted', Config::get('constants.OFF'));
        $res->orderby('spo.created_on', 'desc');
        if (!empty($arr['filterTerms']))
        {
            $subsql = '';
            $arr['filterTerms'] = !is_array($arr['filterTerms']) ? array(
                $arr['filterTerms']) : $arr['filterTerms'];
            if (in_array('full_name', $arr['filterTerms']))
            {
                $subsql[] = 'CONCAT(am.firstname,\' \',am.lastname) LIKE (\'%'.$arr['search_term'].'%\')';
            }
            if (in_array('order_code', $arr['filterTerms']))
            {
                $subsql[] = 'spo.order_code like (\'%'.$arr['search_term'].'%\')';
            }
            if (!empty($subsql))
            {
                $res->whereRaw('('.implode(' OR ', $subsql).')');
            }
        }
        if (isset($arr['status_list']))
        {
            $res->where('spo.order_status_id', $arr['status_list']);
        }
        if (!empty($arr['search_term']))
        {
            $res->whereRaw('(CONCAT(am.firstname,\' \',am.lastname)like \'%'.$arr['search_term'].'%\'  OR  spo.order_code  like \'%'.$arr['search_term'].'%\' )');
        }
        if (isset($arr['order_id']) && !empty($arr['order_id']))
        {
            $res->where('spo.order_id', $arr['order_id']);
        }
        if (!empty($arr['start_date']) && empty($arr['end_date']))
        {
            $res->whereDate('spo.created_on', '>=', date('Y-m-d', strtotime($arr['start_date'])));
        }
        if (empty($arr['start_date']) && !empty($arr['end_date']))
        {
            $res->whereDate('spo.created_on', '<=', date('Y-m-d', strtotime($arr['end_date'])));
        }
        if (isset($arr['start']) && isset($arr['length']))
        {
            $res->skip($arr['start'])->take($arr['length']);
        }
        if (isset($arr['orderby']))
        {
            $res->orderby($arr['orderby'], $arr['order']);
        }
        else
        {
            $res->orderby('spo.created_on', 'asc');
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

    public function order_particulars ($data = array())
    {
        return DB::table(Config::get('tables.SUB_ORDERS').' as sub')
                        ->leftJoin(Config::get('tables.ORDER_ITEMS').' as op', 'op.sub_order_id', '=', 'sub.sub_order_id')
                        ->leftJoin(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as pi', 'pi.product_id', '=', 'op.product_id')
                        ->leftJoin(Config::get('tables.PRODUCT_CATEGORIES').' as tspc', 'tspc.category_id', '=', 'pi.category_id')
                        ->leftJoin(Config::get('tables.PRODUCT_BRANDS').' as tspb', 'tspb.brand_id', '=', 'pi.brand_id')
                        ->leftJoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as tas', 'tas.supplier_id', '=', 'pi.supplier_id')
                        ->leftJoin(Config::get('tables.ACCOUNT_LOGIN_MST').' as lmst', 'lmst.account_id', '=', 'tas.account_id')
                        ->leftJoin(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT').' as ssm', 'ssm.product_id', '=', 'pi.product_id')
                        ->where('sub.sub_order_id', $data['sub_order_id'])
                        ->selectRaw('tspb.brand_name, tspc.category, sub.net_pay as sub_total, tas.file_path, sub.order_status_id, sub.order_code, pi.product_id,sub.supplier_id, pi.product_name, pi.product_code, pi.price, pi.img_path, op.net_pay, op.amount,op.qty, sub.discount, sub.tax, ssm.current_stock, ssm.commited_stock, lmst.email')
                        ->get();
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

    public function billing_details ($data = array())
    {
        return DB::table(config::get('tablesSUPPLIER_PRODUCT_SUB_ORDER').' as spo')
                        ->leftjoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as asu', 'asu.supplier_id', '=', 'spo.supplier_id')
                        ->leftJoin(Config::get('tables.ACCOUNT_LOGIN_MST').' as lmst', 'lmst.account_id', '=', 'spo.account_id')
                        ->leftJoin(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'spo.account_id')
                        ->leftJoin(Config::get('tables.ACCOUNT_ADDRESS').' as ad', 'lmst.account_id', '=', 'ad.account_id')
                        ->leftJoin(Config::get('tables.LOCATION_COUNTRY').' as lo', 'lo.country_id', '=', 'ad.country_id')
                        ->leftJoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'ad.state_id')
                        ->where('spo.sub_order_id', $data['sub_order_id'])
                        ->selectRaw('lmst.email, lmst.uname, concat(am.firstname,\'\',am.lastname) as full_name, lmst.mobile,ad.street1, ad.street2, ad.postal_code, ad.city, ls.state, asu.company_name')
                        ->first();
    }

    public function order_cancel ($arr = array())
    {
        $status['order_status_id'] = Config::get('constants.ORDER_STATUS.CANCELLED');
        $status['last_updated'] = date(Config::get('constants.DB_DATE_TIME_FORMAT'));
        $res = DB::table(Config::get('tables.SUB_ORDERS'))
                ->whereIn('order_status_id', array(
                    0,
                    1,
                    2))
                ->where('sub_order_id', $arr['sub_order_id'])
                ->update($status);
        if (!empty($res))
        {
            $ord_status['sub_order_id'] = $arr['sub_order_id'];
            $ord_status['sub_order_status_id'] = Config::get('constants.ORDER_STATUS.CANCELLED');
            DB::table(Config::get('tables.SUB_ORDER_STATUS_LOG'))->insertGetId($ord_status);
            $wallet['current_balance'] = $arr['wallet']->current_balance + $arr['sub_total'];
            $wallet['tot_debit'] = $arr['wallet']->tot_debit - $arr['sub_total'];
            DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE'))->where('account_id', $arr['order_account_id'])->update($wallet);
            $tdata = array();
            $tdata['account_id'] = $arr['order_account_id'];
            $tdata['sub_order_id'] = $arr['sub_order_id'];
            $tdata['wallet_id'] = 1;
            $tdata['transaction_type'] = Config::get('constants.TRANSACTION_TYPE.CREDIT');
            $tdata['transaction_id'] = $this->commonObj->generateTransactionID($arr['order_account_id']);
            $tdata['rewards'] = $arr['sub_total'];
            $tdata['statementline_id'] = 15;
            $tdata['created_on'] = date('Y-m-d H:i:s');
            DB::table(Config::get('tables.ACCOUNT_TRANSACTION'))->insertGetId($tdata);
            foreach ($arr['order_particulars'] as $values)
            {
                $stock['current_stock'] = $values->current_stock + $values->qty;
                $stock['commited_stock'] = $values->commited_stock - $values->qty;
                DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT'))
                        ->where('product_id', $values->product_id)
                        ->update($stock);
                $prod['stock_value'] = $values->current_stock + $values->qty;
                DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                        ->where('product_id', $values->product_id)
                        ->update($prod);
            }
            return $res;
        }
        else
        {
            return null;
        }
    }

    /*
     * Function Name        : update_order_item_commission
     * Params               : order_item_id
     * Returns              : TRUE OR FALSE
     */

    public function get_order_item_commission ($arr = array())
    {
        extract($arr);
        $commission = array();
        $order_particulars = DB::table(Config::get('tables.ORDER_ITEMS').' as op')
                ->leftJoin(Config::get('tables.SUB_ORDERS').' as so', 'lc.sub_order_id', '=', 'op.sub_order_id')
                ->leftJoin(Config::get('tables.SUPPLIER_COMMISSIONS_SETTINGS').' as scs', 'scs.supplier_id', '=', 'so.supplier_id')
                ->leftJoin(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi', 'spi.supplier_product_id', '=', 'op.supplier_product_id')
                ->leftJoin(Config::get('tables.SUPPLIER_FLEXIBLE_COMMISSIONS').' as spc', 'spc.supplier_product_id', '=', 'op.supplier_product_id')
                ->where('op.is_deleted', Config::get('constants.OFF'))
                ->where('op.order_item_id', $order_item_id)
                ->selectRaw('op.order_id,op.sub_order_id,op.currency_id,op.net_pay,spi.qty,spi.amount,scs.commission_type,spc.commission_unit,spc.commission_value,spc.currency_id as commission_currency_id')
                ->first();
        if ($order_particulars->commission_type == Config::get('constants.COMMISSION_TYPE.FLEXIBLE'))
        {
            $commission['commission_unit'] = $order_particulars->commission_unit;
            $commission['commission_value'] = $order_particulars->commission_value;
            if ($order_particulars->commission_unit == Config::get('constants.COMMISSION_UNIT.PERCENTAGE'))
            {
                $commission['commission_amount'] = ($order_particulars->amount / 100) * $order_particulars->commission_value;
            }
            else
            {
                $rate = $this->commonObj->get_exchange_rate($order_particulars->commission_currency_id, $order_particulars->currency_id);
                $commission['commission_amount'] = $rate * $order_particulars->commission_amount;
            }
            return $commission;
        }
        return false;
    }

    public function updateOrderApprovalStatus ($arr = array())
    {
        $comments = null;
        extract($arr);
        $OrderQuery = DB::table(Config::get('tables.ORDERS').' as o')
                ->join(Config::get('tables.ORDER_APPROVAL_STATUS_SETTINGS').' as ss', function($ss) use($approval_status_id, $account_type_id)
                {
                    $ss->on('ss.from_approval_status_id', '=', 'o.approval_status_id')
                    ->where('ss.to_approval_status_id', '=', $approval_status_id)
                    ->where('ss.account_type_id', '=', $account_type_id);
                })
                ->where('o.order_code', $order_code)
                ->selectRaw('o.order_id');
        $Order = $OrderQuery->first();
        if ($Order)
        {
            DB::beginTransaction();
            $data = [];
            $data['approval_status_id'] = $approval_status_id;
            $data['account_type_id'] = $account_type_id;
            $data['account_id'] = $account_id;
            $data['comments'] = $comments;
            $sub_order_codes = DB::table(Config::get('tables.SUB_ORDERS'))
                    ->where('order_id', $Order->order_id)
                    ->where('is_deleted', Config::get('constants.OFF'))
                    ->lists('sub_order_code');
            foreach ($sub_order_codes as $sub_order_code)
            {
                $data['sub_order_code'] = $sub_order_code;
                $this->updateSubOrderApprovalStatus($data);
            }
            if (DB::table(Config::get('tables.ORDERS'))
                            ->where('order_id', $Order->order_id)
                            ->where('approval_status_id', $approval_status_id)
                            ->exists())
            {
                DB::commit();
                return true;
            }
            DB::rollback();
        }
        return false;
    }

    public function updateSubOrderApprovalStatus ($arr = array())
    {
        $comments = null;
        extract($arr);
        $SubOrderQuery = DB::table(Config::get('tables.SUB_ORDERS').' as so')
                ->join(Config::get('tables.ORDER_APPROVAL_STATUS_SETTINGS').' as ss', function($ss) use($approval_status_id, $account_type_id)
                {
                    $ss->on('ss.from_approval_status_id', '=', 'so.approval_status_id')
                    ->where('ss.to_approval_status_id', '=', $approval_status_id)
                    ->where('ss.account_type_id', '=', $account_type_id);
                })
                ->selectRaw('so.sub_order_id')
                ->where('so.sub_order_code', $sub_order_code);
        $SubOrder = $SubOrderQuery->first();
        if ($SubOrder)
        {
            DB::beginTransaction();
            $data = [];
            $data['approval_status_id'] = $approval_status_id;
            $data['account_type_id'] = $account_type_id;
            $data['account_id'] = $account_id;
            $data['comments'] = $comments;
            $order_item_codes = DB::table(Config::get('tables.ORDER_ITEMS'))
                    ->where('sub_order_id', $SubOrder->sub_order_id)
                    ->where('is_deleted', Config::get('constants.OFF'))
                    ->lists('order_item_code');
            foreach ($order_item_codes as $order_item_code)
            {
                $data['order_item_code'] = $order_item_code;
                $this->updateOrderItemApprovalStatus($data);
            }
            if (DB::table(Config::get('tables.SUB_ORDERS'))
                            ->where('sub_order_id', $SubOrder->sub_order_id)
                            ->where('approval_status_id', $approval_status_id)
                            ->exists())
            {
                DB::commit();
                return true;
            }
            DB::rollback();
        }
        return false;
    }

    public function updateOrderItemApprovalStatus ($arr = array())
    {
        $res = $order_item_log_id = $sub_order_log_id = $order_log_id = false;
        $partner_id = null;
        extract($arr);
        $OrderItemsQuery = DB::table(Config::get('tables.ORDER_ITEMS').' as op')
                ->join(Config::get('tables.ORDERS').' as o', 'o.order_id', '=', 'op.order_id')
                ->join(Config::get('tables.ORDER_APPROVAL_STATUS_SETTINGS').' as ss', function($ss) use($approval_status_id, $account_type_id)
                {
                    $ss->on('ss.from_approval_status_id', '=', 'op.approval_status_id')
                    ->where('ss.to_approval_status_id', '=', $approval_status_id)
                    ->where('ss.account_type_id', '=', $account_type_id);
                })
                ->leftJoin(Config::get('tables.PARTNER').' as pa', 'pa.partner_id', '=', 'o.partner_id')
                ->selectRaw('op.order_id,op.sub_order_id,op.order_item_id,op.order_item_code,op.currency_id,op.net_pay,op.approval_status_id,ss.oass_id,ss.update_order_status_id_to,pa.account_id as partner_account_id')
                ->where('op.order_item_code', $order_item_code);
        if (isset($partner_id) && !empty($partner_id))
        {
            $OrderItemsQuery->where('op.partner_id', $partner_id);
        }
        $OrderItem = $OrderItemsQuery->first();
        if ($OrderItem)
        {
            if ($approval_status_id == Config::get('constants.APPROVAL_STATUS.ADMIN_CONFIRMED') && !empty($OrderItem->partner_account_id) && !$this->commonObj->checkBalance(['account_id'=>$OrderItem->partner_account_id, 'currency_id'=>$OrderItem->currency_id, 'amount'=>$OrderItem->net_pay]))
            {
                return 2;
            }
            DB::transaction(function() use($arr, $OrderItem, &$res)
            {
                $partner_id = null;
                extract($arr);
                $data = [];
                $data['approval_status_id'] = $approval_status_id;
                $res = DB::table(Config::get('tables.ORDER_ITEMS'))
                        ->where('order_item_id', $OrderItem->order_item_id)
                        ->update($data);
                if ($res)
                {
                    $notify = false;
                    $log = [];
                    $log['order_item_id'] = $OrderItem->order_item_id;
                    $log['from_approval_status_id'] = $OrderItem->approval_status_id;
                    $log['to_approval_status_id'] = $approval_status_id;
                    $log['updated_by'] = $account_id;
                    $log['comments'] = $comments;
                    $order_item_log_id = DB::table(Config::get('tables.ORDER_STATUS_LOG'))
                            ->insertGetId($log);
                    switch ($approval_status_id)
                    {
                        case Config::get('constants.APPROVAL_STATUS.ADMIN_CONFIRMED'):
                            if (!empty($OrderItem->partner_account_id))
                            {
                                $this->commonObj->update_account_transaction([
                                    'from_account_id'=>$OrderItem->partner_account_id,
                                    'from_wallet_id'=>Config::get('constants.WALLET.SELLS'),
                                    'to_wallet_id'=>Config::get('constants.WALLET.SELLS'),
                                    'amt'=>$OrderItem->net_pay,
                                    'currency_id'=>$OrderItem->currency_id,
                                    'relation_id'=>$OrderItem->order_item_id,
                                    'transaction_for'=>'ORDER_ITEM_PAYMENT',
                                    'debit_remark_data'=>[],
                                    'credit_remark_data'=>[],
                                ]);
                            }
                            break;
                    }
                    if (DB::table(Config::get('tables.ORDER_ITEMS'))
                                    ->where('sub_order_id', $OrderItem->sub_order_id)
                                    ->where('is_deleted', Config::get('constants.OFF'))
                                    ->havingRaw('COUNT(order_item_id)=SUM(IF(approval_status_id='.$approval_status_id.',1,0))')
                                    ->exists())
                    {
                        if ($from_approval_status_id = DB::table(Config::get('tables.SUB_ORDERS').' as pso')
                                ->join(Config::get('tables.ORDER_APPROVAL_STATUS_SETTINGS').' as ss', function($ss) use($account_type_id, $approval_status_id)
                                {
                                    $ss->on('ss.from_approval_status_id', '=', 'pso.approval_status_id')
                                    ->where('ss.to_approval_status_id', '=', $approval_status_id)
                                    ->where('ss.account_type_id', '=', $account_type_id);
                                })
                                ->where('pso.is_deleted', Config::get('constants.OFF'))
                                ->where('pso.sub_order_id', $OrderItem->sub_order_id)
                                ->pluck('pso.approval_status_id'))
                        {
                            $data = [];
                            $data['approval_status_id'] = $approval_status_id;
                            if (DB::table(Config::get('tables.SUB_ORDERS'))
                                            ->where('sub_order_id', $OrderItem->sub_order_id)
                                            ->where('is_deleted', Config::get('constants.OFF'))
                                            ->update($data))
                            {
                                $log = [];
                                $log['sub_order_id'] = $OrderItem->sub_order_id;
                                $log['from_approval_status_id'] = $from_approval_status_id;
                                $log['to_approval_status_id'] = $approval_status_id;
                                $log['updated_by'] = $account_id;
                                $log['comments'] = $comments;
                                $sub_order_log_id = DB::table(Config::get('tables.ORDER_STATUS_LOG'))
                                        ->insertGetId($log);
                                if (DB::table(Config::get('tables.SUB_ORDERS'))
                                                ->where('order_id', $OrderItem->order_id)
                                                ->where('is_deleted', Config::get('constants.OFF'))
                                                ->havingRaw('COUNT(sub_order_id)=SUM(IF(approval_status_id='.$approval_status_id.',1,0))')
                                                ->exists())
                                {
                                    if ($from_approval_status_id = DB::table(Config::get('tables.ORDERS').' as po')
                                            ->join(Config::get('tables.ORDER_APPROVAL_STATUS_SETTINGS').' as ss', function($ss) use($account_type_id, $approval_status_id)
                                            {
                                                $ss->on('ss.from_approval_status_id', '=', 'po.approval_status_id')
                                                ->where('ss.to_approval_status_id', '=', $approval_status_id)
                                                ->where('ss.account_type_id', '=', $account_type_id);
                                            })
                                            ->where('po.is_deleted', Config::get('constants.OFF'))
                                            ->where('po.order_id', $OrderItem->order_id)
                                            ->pluck('po.approval_status_id'))
                                    {
                                        $data = [];
                                        $data['approval_status_id'] = $approval_status_id;
                                        if (DB::table(Config::get('tables.ORDERS'))
                                                        ->where('order_id', $OrderItem->order_id)
                                                        ->where('is_deleted', Config::get('constants.OFF'))
                                                        ->update($data))
                                        {
                                            $log = [];
                                            $log['order_id'] = $OrderItem->order_id;
                                            $log['from_approval_status_id'] = $from_approval_status_id;
                                            $log['to_approval_status_id'] = $approval_status_id;
                                            $log['updated_by'] = $account_id;
                                            $log['comments'] = $comments;
                                            $order_log_id = DB::table(Config::get('tables.ORDER_STATUS_LOG'))
                                                    ->insertGetId($log);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (!empty($OrderItem->update_order_status_id_to))
                    {
                        $this->updateOrderItemStatus(['order_item_code'=>$OrderItem->order_item_code, 'order_item_status_id'=>$OrderItem->update_order_status_id_to, 'account_id'=>$account_id, 'comments'=>$comments]);
                    }
                    if ($order_item_log_id || $sub_order_log_id || $order_log_id)
                    {
                        $emailData = [];
                        $order_item_details = $this->order_item_details($order_item_id);
                        $notify_status = DB::table(Config::get('tables.ORDER_STATUS_LOG').' as log')
                                ->join(Config::get('tables.ORDER_APPROVAL_STATUS_SETTINGS').' as oass', function($oss) use($account_type_id)
                                {
                                    $oss->on('oass.from_approval_status_id', '=', 'log.from_approval_status_id')
                                    ->on('oass.to_approval_status_id', '=', 'log.to_approval_status_id')
                                    ->where('oass.account_type_id', '=', $account_type_id);
                                })
                                ->leftJoin(Config::get('tables.ORDER_APPROVAL_STATUS_NOTIFICATIONS').' as oasn', 'oasn.oss_id', '=', 'oass.oss_id')
                                ->leftJoin(Config::get('tables.ORDER_APPROVAL_STATUS_LOOKUP').' as fos', 'fos.approval_status_id', '=', 'oass.from_approval_status_id')
                                ->leftJoin(Config::get('tables.ORDER_APPROVAL_STATUS_LOOKUP').' as tos', 'tos.approval_status_id', '=', 'oass.to_approval_status_id')
                                ->join(Config::get('tables.ACCOUNT_TYPES').' as at', 'at.id', '=', 'oasn.account_type_id')
                                ->where('log.status_log_id', ($order_log_id ? $order_log_id : ($sub_order_log_id ? $sub_order_log_id : $order_item_log_id)))
                                ->select('fos.status_key as from_key', 'tos.status_key as to_key', 'oasn.account_type_id', 'at.account_type_key', 'oasn.sms', 'oasn.email', 'oasn.notification')
                                ->get();
                        if ($notify_status)
                        {
                            $t = '';
                            if ($order_log_id)
                            {
                                $emailData['order_details'] = $this->order_details($order_item_details->order_id);
                                $t = 'ORDER_APPROVAL';
                            }
                            else if ($sub_order_log_id)
                            {
                                $emailData['sub_order_details'] = $this->sub_order_details($order_item_details->sub_order_id);
                                $t = 'SUB_ORDER_APPROVAL';
                            }
                            else if ($order_item_log_id)
                            {
                                $emailData['order_item_details'] = $order_item_details;
                                $t = 'ORDER_ITEM_APPROVAL';
                            }
                            foreach ($notify_status as $n)
                            {
                                if ($n->account_type_id == Config::get('constants.ACCOUNT_TYPE.ADMIN'))
                                {
                                    $id = Config::get('constants.ADMIN_ACCOUNT_ID');
                                }
                                elseif ($n->account_type_id == Config::get('constants.ACCOUNT_TYPE.USER'))
                                {
                                    $id = $order_item_details->account_id;
                                }
                                elseif ($n->account_type_id == Config::get('constants.ACCOUNT_TYPE.SUPPLIER'))
                                {
                                    $id = $order_item_details->supplier_id;
                                }
                                elseif ($n->account_type_id == Config::get('constants.ACCOUNT_TYPE.PARTNER'))
                                {
                                    $id = $order_item_details->partner_id;
                                }
                                ShoppingPortal::notify($t.'.'.$n->from_key.'_TO_'.$n->to_key.'.'.$n->account_type_key, $id, $n->account_type_id, $emailData, $n->email, $n->sms, $n->notification);
                            }
                        }
                    }
                }
            });
        }
        return $res;
    }

}
