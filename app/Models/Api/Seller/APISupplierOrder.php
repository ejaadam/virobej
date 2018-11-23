<?php
namespace App\Models\Api\Seller;
use DB;
use Illuminate\Database\Eloquent\Model;
use Config;
use App\Helpers\ShoppingPortal;
use App\Models\MemberAuth;
use URL;
use App\Helpers\ImageLib;

class APISupplierOrder extends Model
{

    public function __construct (&$commonObj)
    {
        parent::__construct();
        $this->commonObj = $commonObj;
    }

    public function getSubOrderList ($arr = array(), $count = false)
    {	
        extract($arr);
        $res = DB::table(Config::get('tables.SUB_ORDERS').' as spo')
                ->join(Config::get('tables.ORDERS').' as po', 'po.order_id', '=', 'spo.order_id')
                ->join(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'spo.account_id')
                ->join(Config::get('tables.ORDER_SHIPPING_DETAILS').' as sd', 'sd.order_id', '=', 'po.order_id')
                ->join(Config::get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'spo.currency_id')
                ->join(Config::get('tables.ORDER_STATUS_LOOKUP').' as psos', 'psos.order_status_id', '=', 'spo.sub_order_status_id')
                ->where('spo.is_deleted', Config::get('constants.OFF'))
                ->where('spo.supplier_id', $supplier_id)
                ->orderby('spo.created_on', 'desc');
         if (isset($status_list))
        {
    //        $res->where('spo.sub_order_status_id', $status_list);
        }

        if (isset($sub_order_status_id) && !is_null($sub_order_status_id))
        {
            $res->where('spo.sub_order_status_id', $sub_order_status_id);
        }
        if (!empty($search_term))
        {
            $res->whereRaw('(sd.full_name like \'%'.$search_term.'%\'  OR  spo.sub_order_code  like \'%'.$search_term.'%\' )');
        }
        if (isset($order_id) && !empty($order_id))
        {
            $res->where('spo.order_id', $order_id);
        }
        if (!empty($start_date) && empty($end_date))
        {
            $res->whereRaw('DATE(spo.created_on) >=\''.date('Y-m-d', strtotime($start_date)).'\'');
        }
        else if (empty($start_date) && !empty($end_date))
        {
            $res->whereRaw('DATE(spo.created_on) <=\''.date('Y-m-d', strtotime($end_date)).'\'');
        }
        else if ((!empty($start_date)) && (!empty($end_date)))
        {
            $res->whereRaw('DATE(spo.created_on) >=\''.date('Y-m-d', strtotime($start_date)).'\'');
            $res->whereRaw('DATE(spo.created_on) <=\''.date('Y-m-d', strtotime($end_date)).'\'');
        }
        if (isset($start) && isset($length))
        {
            $res->skip($start)->take($length);
        }
        if (isset($orderby))
        {
            $res->orderby($orderby, $order);
        }
        else
        {
            $res->orderby('spo.created_on', 'asc');
        } 
        if ($count)
        {
            return $res->count();
        }
        else
        {
            $orders = $res->selectRaw('spo.sub_order_code as order_code, sd.full_name as customer_name, spo.qty, spo.sub_total, spo.net_pay, c.currency, c.currency_symbol, spo.approval_status_id, spo.sub_order_status_id, psos.status as order_status, psos.order_status_class, spo.created_on as ordered_on')
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
                    'target'=>'_new',
                    'url'=>URL::to('seller/order/details/'.$order->order_code)
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
                            'order_code'=>$order->order_code
                        ],
                        'url'=>URL::to($action->type == 1 ? 'api/v1/seller/order/update-approval-status' : 'api/v1/seller/order/update-status')
                    ];
                });
                unset($order->sub_order_status_id);
                unset($order->approval_status_id);
                unset($order->currency);
                unset($order->currency_symbol);
            });
            return $orders;
        }
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
                ->where('so.sub_order_code', $sub_order_code)
                ->where('so.supplier_id', $supplier_id);
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
                                    'transaction_for'=>Config::get('stline.ORDER_ITEM_PAYMENT'),
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

    public function changeOrderItemStatus ($arr = array(), $notify = true)
    {
        $order_item_log_id = $l2 = $order_log_id = false;
        extract($arr);
        $comments = (isset($comments) && !empty($comments)) ? $comments : '';
        $OID = $this->orderItemDetails(['order_item_code'=>$order_item_code]);
        if (!empty($order_item_id))
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
                        DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT'))
                                ->where('supplier_product_id', $OID->supplier_product_id)
                                ->increment('stock_on_hand', $OID->qty);
                        DB::table(Config::get('tables.SUB_ORDERS'))
                                ->where('sub_order_id', $OID->sub_order_id)
                                ->update(array(
                                    'qty'=>DB::table(Config::get('tables.ORDER_ITEMS'))->where('sub_order_id', $OID->sub_order_id)->where('is_deleted', Config::get('constants.OFF'))->where('order_item_status_id', '<>', Config::get('constants.ORDER_STATUS.CANCELLED'))->sum('qty')));
                        DB::table(Config::get('tables.ORDERS'))
                                ->where('order_id', $OID->order_id)
                                ->update(array(
                                    'qty'=>DB::table(Config::get('tables.ORDER_ITEMS'))->where('order_id', $OID->order_id)->where('is_deleted', Config::get('constants.OFF'))->where('order_item_status_id', '<>', Config::get('constants.ORDER_STATUS.CANCELLED'))->sum('qty')));
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
                    if ($notify)
                    {
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
                    }
                    DB::commit();
                    return $OID;
                }
            }
            DB::rollback();
        }
        return false;
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
        if (isset($order_code) && !empty($order_code))
        {
            $orderQuery->where('po.order_code', $order_code);
        }
        if (isset($order_id) && !empty($order_id))
        {
            $orderQuery->where('po.order_id', $order_id);
        }
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
                'url'=>URL::to('supplier/products/orders/'.$order->order_code)
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
                    'url'=>URL::to($action->type == 1 ? 'supplier/products/orders/update-approval-status' : 'supplier/products/orders/update-status')
                ];
            });
            unset($order->currency);
            unset($order->currency_symbol);
            unset($order->approval_status_id);
            unset($order->order_status_id);
        }
        return $order;
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
                ->where('so.supplier_id', $supplier_id)
                ->select('so.sub_order_id')
                ->where('so.sub_order_code', $order_code)
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

    public function order_details ($wdata = array())
    {
        return DB::table(Config::get('tables.SUB_ORDERS').' as sub')
                        ->join(Config::get('tables.ORDER_ITEMS').' as op', 'op.sub_order_id', '=', 'sub.sub_order_id')
                        ->join(Config::get('tables.SUPPLIER_PRODUCT_LIST').' as pi', function($pi)
                        {
                            $pi->on('pi.supplier_product_id', '=', 'op.supplier_product_id')
                            ->on('pi.currency_id', '=', 'op.currency_id');
                        })
                        ->join(Config::get('tables.ACCOUNT_SUPPLIERS').' as tas', 'tas.supplier_id', '=', 'pi.supplier_id')
                        ->leftJoin(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT').' as ssm', 'ssm.product_id', '=', 'pi.product_id')
                        ->where('sub.sub_order_id', $wdata['sub_order_id'])
                        ->selectRaw('pi.brand_name, pi.category, sub.net_pay as sub_total, tas.file_path, sub.sub_order_status_id,sub.sub_order_code, pi.product_id, sub.supplier_id, pi.product_name, pi.product_code, op.price,op.net_pay,op.sub_total, op.qty, op.discount, sub.tax, ssm.current_stock, ssm.commited_stock, ssm.sold_items')
                        ->get();
    }

    public function get_sub_order_details ($arr = array(), $options = false)
    {
        extract($arr);
        $query = DB::table(Config::get('tables.SUB_ORDERS').' as pso')
                ->leftjoin(Config::get('tables.ORDERS').' as po', 'po.order_id', '=', 'pso.order_id')
                ->join(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'po.account_id')
                ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'po.account_id')
                ->leftjoin(Config::get('tables.SUPPLIER_MST').' as s', 's.supplier_id', '=', 'pso.supplier_id')
                ->leftjoin(Config::get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'pso.currency_id')
                ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as psos', 'psos.order_status_id', '=', 'pso.sub_order_status_id')
                ->leftjoin(Config::get('tables.ORDER_APPROVAL_STATUS').' as oas', 'oas.approval_status_id', '=', 'pso.approval_status_id')
                ->where('po.is_deleted', Config::get('constants.OFF'))
                ->where('pso.is_deleted', Config::get('constants.OFF'))
                ->where('pso.sub_order_code', $sub_order_code);
        $sub_order = $query->selectRaw('pso.sub_order_code, pso.created_on as ordered_on, pso.sub_order_status_id, pso.approval_status_id, pso.qty, pso.sub_total, pso.net_pay, pso.shipping_charge, po.order_code, concat(ad.firstname,\' \',ad.lastname) as customer_name, c.currency, c.currency_symbol, psos.status as sub_order_status, s.supplier_code, s.company_name, psos.order_status_class as sub_order_status_class, oas.status as approval_status')
                ->first();
        if (!empty($sub_order))
        {
            $sub_order->ordered_on = date('d-M-Y H:i:s', strtotime($sub_order->ordered_on));
            $sub_order->qty = number_format($sub_order->qty, 0, '.', ',');
            $sub_order->sub_total = $sub_order->currency_symbol.' '.number_format($sub_order->sub_total, 2, '.', ',').' '.$sub_order->currency;
            $sub_order->net_pay = $sub_order->currency_symbol.' '.number_format($sub_order->net_pay, 2, '.', ',').' '.$sub_order->currency;
            $sub_order->shipping_charge = $sub_order->currency_symbol.' '.number_format($sub_order->shipping_charge, 2, '.', ',').' '.$sub_order->currency;
            $sub_order->actions = [];
            $approval_actions = DB::table(Config::get('tables.ORDER_APPROVAL_STATUS_SETTINGS').' as ss')
                    ->join(Config::get('tables.ORDER_APPROVAL_STATUS').' as oas', function($ss)
                    {
                        $ss->on('oas.approval_status_id', '=', 'ss.to_approval_status_id');
                    })
                    ->where('ss.from_approval_status_id', $sub_order->approval_status_id)
                    ->where('ss.account_type_id', $account_type_id)
                    ->selectRaw('oas.status,oas.status_key,ss.is_comment_required,1 as type');
            $status_actions = DB::table(Config::get('tables.ORDER_STATUS_SETTINGS').' as ss')
                    ->join(Config::get('tables.ORDER_STATUS_LOOKUP').' as oas', function($ss)
                    {
                        $ss->on('oas.order_status_id', '=', 'ss.to_order_status_id');
                    })
                    ->where('ss.from_order_status_id', $sub_order->sub_order_status_id)
                    ->where('ss.account_type_id', $account_type_id)
                    ->selectRaw('oas.status,oas.status_key,ss.is_comment_required,2 as type');
            $actions = $approval_actions->unionAll($status_actions)->get();
            array_walk($actions, function($action) use(&$sub_order)
            {
                $sub_order->actions[$action->status_key] = [
                    'title'=>$action->status,
                    'data'=>[
                        'status'=>$action->status_key,
                        'sub_order_code'=>$sub_order->sub_order_code
                    ],
                    'url'=>URL::to($action->type == 1 ? 'seller/order/update-approval-status' : 'seller/order/update-status')
                ];
            });
            unset($sub_order->currency);
            unset($sub_order->currency_symbol);
            unset($sub_order->approval_status_id);
            unset($sub_order->sub_order_status_id);
        }
        return $sub_order;
    }

    public function get_order_particulars_list ($arr = array())
    {
        extract($arr);
        $orders = DB::table(Config::get('tables.ORDER_ITEMS').' as op')
                ->join(Config::get('tables.ORDERS').' as po', 'po.order_id', '=', 'op.order_id')
                ->join(Config::get('tables.SUB_ORDERS').' as pso', 'pso.sub_order_id', '=', 'op.sub_order_id')
                ->join(Config::get('tables.SUPPLIER_PRODUCTS_LIST').' as spi', function($spi)
                {
                    $spi->on('spi.supplier_product_id', '=', 'op.supplier_product_id')
                    ->on('spi.currency_id', '=', 'op.currency_id');
                })
                ->join(Config::get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'op.currency_id')
                ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as pois', 'pois.order_status_id', '=', 'op.order_item_status_id')
                ->leftjoin(Config::get('tables.PAYMENT_TYPES').' as p', 'p.payment_type_id', '=', 'po.payment_type_id')
                ->leftjoin(Config::get('tables.PAYMENT_STATUS_LOOKUPS').' as ps', 'ps.payment_status_id', '=', 'po.payment_status_id')
                ->leftjoin(Config::get('tables.COURIER_MODE_LOOKUPS').' as cm', 'cm.mode_id', '=', 'op.mode_id')
                ->where('op.is_deleted', Config::get('constants.OFF'))
                ->where('po.is_deleted', Config::get('constants.OFF'))
                ->where('pso.is_deleted', Config::get('constants.OFF'))
                ->where('pso.supplier_id', $supplier_id);
        if (isset($filterTerms['order_code']) && !empty($filterTerms['order_code']))
        {
            $orders->where('pso.sub_order_code', $filterTerms['order_code']);
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
            $order_items = $orders->selectRaw('op.order_item_code, op.qty, op.price, op.mrp_price, op.net_pay, op.shipping_charge, op.discount, op.approval_status_id, op.order_item_status_id, po.order_code, pso.sub_order_code, op.product_id, spi.product_name, spi.is_combinations, spi.product_code, c.currency, c.currency_symbol, pois.status as order_item_status, pois.order_status_class as order_item_status_class, spi.category_id, spi.brand_id, spi.category, pso.supplier_id, spi.replacement_service_policy_id, spi.brand_name, p.payment_type, ps.payment_status, ps.payment_status_class, cm.mode')->get();            
			$this->imageObj = new ImageLib();
            array_walk($order_items, function(&$order_item) use($account_type_id)
            {
                $imgdata = ($order_item->is_combinations) ? ['filter'=>['post_type_id'=>Config::get('constants.POST_TYPE.PRODUCT_CMB'), 'relative_post_id'=>$order_item->product_cmb_id]] : [];
                $order_item->imgs = $this->imageObj->get_imgs($order_item->product_id, $imgdata);
                $order_item->qty = number_format($order_item->qty, 0, '.', ',');
                $order_item->price = $order_item->currency_symbol.' '.number_format($order_item->price, 2, '.', ',').' '.$order_item->currency;
                $order_item->mrp_price = $order_item->currency_symbol.' '.number_format($order_item->mrp_price, 2, '.', ',').' '.$order_item->currency;
                $order_item->net_pay = $order_item->currency_symbol.' '.number_format($order_item->net_pay, 2, '.', ',').' '.$order_item->currency;
                $order_item->shipping_charge = $order_item->currency_symbol.' '.number_format($order_item->shipping_charge, 2, '.', ',').' '.$order_item->currency;
                $order_item->actions = [];
                $approval_actions = DB::table(Config::get('tables.ORDER_APPROVAL_STATUS_SETTINGS').' as ss')
                        ->join(Config::get('tables.ORDER_APPROVAL_STATUS').' as oas', function($ss)
                        {
                            $ss->on('oas.approval_status_id', '=', 'ss.to_approval_status_id');
                        })
                        ->where('ss.from_approval_status_id', $order_item->approval_status_id)
                        ->where('ss.account_type_id', $account_type_id)
                        ->selectRaw('oas.status,oas.status_key,ss.is_comment_required,1 as type');
                $status_actions = DB::table(Config::get('tables.ORDER_STATUS_SETTINGS').' as ss')
                        ->join(Config::get('tables.ORDER_STATUS_LOOKUP').' as oas', function($ss)
                        {
                            $ss->on('oas.order_status_id', '=', 'ss.to_order_status_id');
                        })
                        ->where('ss.from_order_status_id', $order_item->order_item_status_id)
                        ->where('ss.account_type_id', $account_type_id)
                        ->selectRaw('oas.status,oas.status_key,ss.is_comment_required,2 as type');
                $actions = $approval_actions->unionAll($status_actions)->get();
                array_walk($actions, function($action) use(&$order_item)
                {
                    $order_item->actions[$action->status_key] = [
                        'title'=>$action->status,
                        'data'=>[
                            'status'=>$action->status_key,
                            'order_item_code'=>$order_item->order_item_code
                        ],
                        'url'=>URL::to($action->type == 1 ? 'api\v1\seller\order\item\update-approval-status' : 'api\v1\seller\order\item\update-status')
                    ];
                });
            });
            return $order_items;
        }
    }

    public function order_shipping_details ($data = array())
    {	
		extract($data);
		//return $sub_order_code;
        $query = DB::table(Config::get('tables.SUB_ORDERS').' as pso')
                ->join(Config::get('tables.ORDERS').' as po', 'po.order_id', '=', 'pso.order_id')				
                ->Join(Config::get('tables.ORDER_SHIPPING_DETAILS').' as spd', 'spd.order_id', '=', 'pso.order_id')
                ->leftJoin(Config::get('tables.LOCATION_COUNTRY').' as lo', 'lo.country_id', '=', 'spd.country_id')
                ->leftJoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'spd.state_id')
                ->leftJoin(Config::get('tables.LOCATION_CITY').' as lc', 'lc.city_id', '=', 'spd.city_id'); 
        if (isset($order_id) && !empty($order_id))
        {
            $query->where('spd.order_id', $order_id);
        }
        if (isset($sub_order_id) && !empty($sub_order_id))
        {
            $query->where('spd.sub_order_id', $sub_order_id);
        }
		if (isset($sub_order_code) && !empty($sub_order_code))
        {
            $query->where('pso.sub_order_code', $sub_order_code);
        }
        return $query->selectRaw('spd.*,lo.country,ls.state,lc.city')->first();
        
    }

    /*
     * Function Name        : updateOrderItemStatus
     * Params               : order_item_code, order_item_status_id,account_id,account_type_id,comments(optional)
     * Returns              : Detail OR FALSE
     */

    public function updateOrderItemStatus ($arr = array())
    {	
        $shipment = [];
        $order_item_log_id = $l2 = $sub_order_log_id = $order_log_id = false;
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
                    $order_item_details = $this->orderItemDetails(['order_item_id'=>$OID->order_item_id]);
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
                            $emailData['order_details'] = $this->getOrderDetails(['order_id'=>$order_item_details->order_id, 'account_type_id'=>$account_type_id]);
                            $t = 'ORDER';
                        }
                        else if ($sub_order_log_id)
                        {
                            $emailData['sub_order_details'] = $this->sub_order_details($order_item_details->sub_order_id);
                            $t = 'SUB_ORDER';
                        }
                        else if ($order_item_log_id)
                        {
                            $emailData['order_item_details'] = $order_item_details;
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
                            //ShoppingPortal::notify($t.'.'.$n->from_key.'_TO_'.$n->to_key.'.'.$n->account_type_key, $id, $n->account_type_id, $emailData, $n->email, $n->sms, $n->notification);
                        }
                    }
                    DB::commit();
                    return $order_item_details;
                }
            }
        }
        DB::rollback();
        return false;
    }

    public function orderItemDetails ($arr = array())
    {	
        extract($arr);
        $query = DB::table(Config::get('tables.ORDER_ITEMS').' as op')
                ->leftjoin(Config::get('tables.ORDERS').' as po', 'po.order_id', '=', 'op.order_id')
                ->leftjoin(Config::get('tables.SUB_ORDERS').' as pso', 'pso.sub_order_id', '=', 'op.sub_order_id')
                ->leftjoin(Config::get('tables.SUPPLIER_MST').' as s', 's.supplier_id', '=', 'pso.supplier_id')
                ->join(Config::get('tables.SUPPLIER_PRODUCTS_LIST').' as pi', function($pi)
                {
                    $pi->on('pi.supplier_product_id', '=', 'op.supplier_product_id')
                    ->on('pi.currency_id', '=', 'op.currency_id');
                })
                ->leftjoin(Config::get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'op.currency_id')
                ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as pos', 'pos.order_status_id', '=', 'po.order_status_id')
                ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as psos', 'psos.order_status_id', '=', 'pso.sub_order_status_id')
                ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as pois', 'pois.order_status_id', '=', 'op.order_item_status_id')
                ->leftjoin(Config::get('tables.PAYMENT_TYPES').' as p', 'p.payment_type_id', '=', 'po.payment_type_id')
                ->leftjoin(Config::get('tables.PAYMENT_STATUS_LOOKUPS').' as ps', 'ps.payment_status_id', '=', 'ps.payment_status_id')
                ->leftjoin(Config::get('tables.COURIER_MODE_LOOKUPS').' as cm', 'cm.mode_id', '=', 'op.mode_id')
                ->where('op.is_deleted', Config::get('constants.OFF'))
                ->where('po.is_deleted', Config::get('constants.OFF'))
                ->where('pso.is_deleted', Config::get('constants.OFF'))
                ->selectRaw('op.*,po.account_id,po.order_code,pso.sub_order_code,pi.product_name,pi.product_code,pi.category_id,pi.brand_id,pi.category,op.replacement_due_days,pi.brand_name,s.supplier_id,s.supplier_code,s.company_name,c.currency,c.currency_symbol,po.order_status_id,pos.status as order_status,pos.order_status_class,pso.sub_order_status_id,psos.status as sub_order_status,psos.order_status_class as sub_order_status_class,pois.status as order_item_status,pois.order_status_class as order_item_status_class,p.payment_type,ps.payment_status,ps.payment_status_class,cm.mode');
        if (isset($order_item_id) && !empty($order_item_id))
        {
            $query->where('op.order_item_id', $order_item_id);
        }
        else
        {
            $query->where('op.order_item_code', $order_item_code);
        }
        return $query->first();
    }

}
