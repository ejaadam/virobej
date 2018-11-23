<?php
namespace App\Http\Controllers\Api\Seller;
use App\Http\Controllers\Api\APIBase;
use App\Models\Api\Seller\APISupplierOrder;
use App\Models\Memberauth;
use App\Models\Seller\Supplier;
use App\Models\Commonsettings;
use Input;
use Config;
use Response;
use App\Helpers\ShoppingPortal;
use Lang;
use URL;
use Session;

class APISupplierOrderController extends APIBase
{

    public function __construct ()
    {
        parent::__construct();
        $this->orderObj = new APISupplierOrder($this->commonObj);
    }

    public function subOrderList ($status)
    {
        $data = $filter = array();
        $postdata = Input::all();
        $data['order_status_arr'] = array('placed'=>0, 'completed'=>1, 'processing'=>2, 'dispatched'=>3, 'delivered'=>4, 'cancelled'=>5, 'returned'=>6);
        if (array_key_exists($postdata['status'], $data['order_status_arr']))
        {
            $data['order_status'] = $postdata['status'];
            $data['sub_order_status_id'] = $data['order_status_arr'][$data['order_status']];
            $data['supplier_id'] = isset($this->supplier_id) ? $this->supplier_id : $postdata['supplier_id'];
            $data['account_type_id'] = isset($this->acc_type_id) ? $this->acc_type_id : $postdata['acc_type_id'];
            $filter['search_term'] = (isset($postdata['search_term']) && !empty($postdata['search_term'])) ? $postdata['search_term'] : '';
            $filter['start_date'] = (isset($postdata['start_date']) && !empty($postdata['start_date'])) ? $postdata['start_date'] : '';
            $filter['end_date'] = (isset($postdata['end_date']) && !empty($postdata['end_date'])) ? $postdata['end_date'] : '';
            $ajaxdata['draw'] = $postdata['draw'];
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['data'] = array();			
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->orderObj->getSubOrderList($data, true);
            if ($ajaxdata['recordsTotal'])
            {
                $filter = array_filter($filter);
                if (!empty($filter))
                {
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->orderObj->getSubOrderList($data, true);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                    if (isset($postdata['order'][0]['dir']))
                    {
                        $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                        $data['order'] = $postdata['order'][0]['dir'];
                    }
                    $data['search_term'] = isset($postdata['search_term']) ? $postdata['search_term'] : '';
                    $ajaxdata['data'] = $this->orderObj->getSubOrderList($data);
                }
            }
            $this->statusCode = 200;
            return Response::json($ajaxdata, $this->statusCode, $this->headers, $this->options);
        }
        else
        {
            return App::abort(404);
        }
    }

    public function subOrderDetails ($sub_order_code = '')
    {
        $data = array();
        $postdata = Input::all();
        $postdata['sub_order_code'] = $data['sub_order_code'] = $sub_order_code;
        $data['account_type_id'] = $postdata['account_type_id'] = $this->acc_type_id;
        $postdata['supplier_id'] = $this->supplier_id;
        $data['sub_order_details'] = $this->orderObj->get_sub_order_details($data);
        $data['order_particulars'] = $this->orderObj->get_order_particulars_list($postdata);
        $data['shipping_details'] = $this->orderObj->order_shipping_details($postdata);				
        if (!empty($data['shipping_details']))
        {
            $data['shipping_details']->address = implode(', ', [$data['shipping_details']->address1, $data['shipping_details']->address2, $data['shipping_details']->city, $data['shipping_details']->state, $data['shipping_details']->country.(!empty($data['shipping_details']->postal_code) ? '-'.$data['shipping_details']->postal_code : '').(!empty($data['shipping_details']->mobile_no) ? ' Mobile No.: '.$data['shipping_details']->mobile_no : '')]);
        } 
        $this->statusCode = 200;
        return Response::json($data, $this->statusCode, $this->headers, $this->options);
    }

    public function updateSubOrderApprovalStatus ()
    {
        $op = array();
        $op['msg'] = Lang::get('general.something_went_wrong');
        $data = Input::all();
        $data['account_id'] = $this->account_id;
        $data['account_type_id'] = $this->acc_type_id;
        $data['approval_status_id'] = (isset($data['status']) && array_key_exists($data['status'], Config::get('constants.APPROVAL_STATUS'))) ? Config::get('constants.APPROVAL_STATUS.'.$data['status']) : null;
        if (isset($data['sub_order_code']) && !empty($data['sub_order_code']) && isset($data['status']) && !empty($data['status']))
        {
            if ($this->orderObj->updateSubOrderApprovalStatus($data))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.updated_successfully');
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function updateOrderItemApprovalStatus ()
    {
        $op = array();
        $op['msg'] = Lang::get('general.something_went_wrong');
        $data = Input::all();
        $data['account_id'] = $this->account_id;
        $data['account_type_id'] = $this->acc_type_id;
        $data['approval_status_id'] = (isset($data['status']) && array_key_exists($data['status'], Config::get('constants.APPROVAL_STATUS'))) ? Config::get('constants.APPROVAL_STATUS.'.$data['status']) : null;
        if (isset($data['order_item_code']) && !empty($data['order_item_code']) && isset($data['status']) && !empty($data['status']))
        {
            if ($this->orderObj->updateOrderItemApprovalStatus($data))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.updated_successfully');
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function updateOrderItemStatus ()
    {
        $op = array();
        $op['msg'] = Lang::get('general.something_went_wrong');
        $data = Input::all();
        $data['account_id'] = $this->account_id;
        $data['account_type_id'] = $this->acc_type_id;
        $data['order_item_status_id'] = (isset($data['status']) && array_key_exists($data['status'], Config::get('constants.ORDER_STATUS'))) ? Config::get('constants.ORDER_STATUS.'.$data['status']) : null;
        if (isset($data['order_item_code']) && !empty($data['order_item_code']) && isset($data['order_item_status_id']) && !empty($data['order_item_status_id']))
        {
            if ($this->orderObj->changeOrderItemStatus($data))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.updated_successfully');
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function updateSubOrderStatus ()
    {
        $op = array();
        $op['msg'] = Lang::get('general.something_went_wrong');
        $data = Input::all();
        $data['account_id'] = $this->account_id;
        $data['supplier_id'] = $this->supplier_id;
        $data['account_type_id'] = $this->acc_type_id;
        $data['order_status_id'] = (isset($data['status']) && array_key_exists($data['status'], Config::get('constants.ORDER_STATUS'))) ? Config::get('constants.ORDER_STATUS.'.$data['status']) : null;				
        if (isset($data['order_code']) && !empty($data['order_code']) && isset($data['order_status_id']) && !empty($data['order_status_id']))
        {
            if ($res = $this->orderObj->updateSubOrderStatus($data))
            {				
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.updated_successfully');
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

}
