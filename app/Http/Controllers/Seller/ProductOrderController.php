<?php

namespace App\Http\Controllers\Seller;
use App\Http\Controllers\SupplierBaseController;
use App\Models\Seller\ProductOrder;
use Config;
use Lang;
use Redirect;
use View;

class ProductOrderController extends SupplierBaseController
{

    public function __construct ()
    {
        parent::__construct();
        $this->proObj = new ProductOrder();
    }

    public function subOrderList ($status)
    {
        $data = array();
        $data['order_status_arr'] = array('placed'=>0, 'completed'=>1, 'processing'=>2, 'dispatched'=>3, 'delivered'=>4, 'cancelled'=>5, 'returned'=>6);
        if (array_key_exists($status, $data['order_status_arr']))
        {
            $data['order_status'] = $status;
            return View::make('seller.orders.orders_list', $data);
        }
        else
        {
            return App::abort(404);
        }
    }

    public function return_request_list ()
    {
        $data = array();
        $postdata = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        if (!empty($postdata))
        {
            $data['search_term'] = (isset($postdata['search_term']) && !empty($postdata['search_term'])) ? $postdata['search_term'] : '';
        }
        if (Request::ajax())
        {
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->proObj->get_sub_order_list(array('count'=>true, 'supplier_id'=>$data['supplier_id']));
            $ajaxdata['draw'] = $postdata['draw'];
            $ajaxdata['url'] = URL::to('/');
            if (!empty($ajaxdata['recordsFiltered']))
            {
                $ajaxdata['data'] = [];
                $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                $data['order'] = $postdata['order'][0]['dir'];
                $data['search_term'] = $postdata['search_term'];
                $ajaxdata['data'] = $this->proObj->get_return_list($data);
            }
            return Response::json($ajaxdata);
        }
        else if (!empty($postdata) && !empty($data) && isset($postdata['submit']) && $postdata['submit'] == 'Export')
        {
            $res['user_details'] = $this->proObj->get_return_list($data);
            $output = View::make('admin.products.view_categories_list_excel', $res);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Disposition'=>'attachment; filename=Category_list'.date('Y-m-d').'.xls',
                'Content-Transfer-Encoding'=>'binary'
            );
            return Response::make($output, 200, $headers);
        }
        else if (!empty($postdata) && isset($postdata['submit']) && $postdata['submit'] == 'Print')
        {
            $res['user_details'] = $this->proObj->get_return_list($data);
            return View::make('admin.products.view_categories_list_print', $res);
        }
        else
        {
            $data['details'] = $this->proObj->get_return_list($data);
            return View::make('supplier.orders.return_request_list', $data);
        }
    }

    public function subOrderDetails ($sub_order_code = '')
    {
        $data = array();
        $data['sub_order_code'] = $sub_order_code;
        return View::make('seller.orders.order_details', $data);
    }

    public function subOrderDetailsbk ($sub_order_code)
    {
        $data = array();
        $op['status'] = 'ERR';
        $postdata = Input::all();
        $data['account_type_id'] = $this->account_type_id;
        $data['sub_order_code'] = $sub_order_code;
        $data['sub_order_details'] = $this->proObj->getSubOrderDetails($data);
        if (!empty($data['sub_order_details']))
        {
            $data['sub_order_code'] = $data['sub_order_details']->sub_order_code;
            $data['order_particulars'] = $this->proObj->get_order_particulars_list($data);
            $data['shipping_details'] = $this->proObj->order_shipping_details($data);
            //echo '<pre>';print_r($data);exit;
            if (!empty($postdata) && !empty($data) && isset($postdata['submit']) && $postdata['submit'] == 'Export')
            {
                $output = View::make('admin.products.view_categories_list_excel', $res);
                $headers = array(
                    'Pragma'=>'public',
                    'Expires'=>'public',
                    'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                    'Cache-Control'=>'private',
                    'Content-Disposition'=>'attachment; filename=Category_list'.date('Y-m-d').'.xls',
                    'Content-Transfer-Encoding'=>'binary'
                );
                return Response::make($output, 200, $headers);
            }
            else if (!empty($postdata) && isset($postdata['submit']) && $postdata['submit'] == 'Print')
            {
                return View::make('admin.products.view_categories_list_print', $res);
            }
            else
            {
                if (Request::ajax())
                {
                    $op['status'] = 'OK';
                    $op['pagetitle'] = $sub_order_code.Lang::get('general.order_details');
                    $op['layoutContent'] = View::make('admin.products.order.sub_order_details', $data)->render();
                    return Response::json($op);
                }
                else
                {
                    //$this->layout->pagetitle = $sub_order_code.Lang::get('general.order_details');
                    //return View::make('admin.products.order.sub_order_details', $data);
                    return View::make('supplier.orders.order_details', $data);
                }
            }
        }
    }

    public function OrderDetails ()
    {
        $data = array();
        $op['status'] = 'ERR';
        $postdata = Input::all();

        if (Request::ajax())
        {
            $op['status'] = 'OK';
            $op['pagetitle'] = $postdata['sub_order_code'].Lang::get('general.order_details');
            $op['layoutContent'] = View::make('supplier.orders.order_details', $postdata)->render();
            return Response::json($op);
        }
    }

}
