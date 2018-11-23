<?php

namespace App\Http\Controllers\Seller;
use App\Http\Controllers\SupplierBaseController;
use App\Models\Seller\Product;
use Config;
use Lang;
use Redirect;
use Input;
use Request;
use URL;
use Response;
use App\Helpers\ShoppingPortal;
use View;
use Illuminate\Support\Facades\Validator;

class ProductController extends SupplierBaseController
{

    public function __construct ()
    {
        parent::__construct();
        $this->productObj = new Product();
    }

    public function delete_supplier_product_zone ()
    {
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (Input::has('pss_id'))
        {
            $postdata['pss_id'] = Input::get('pss_id');
            if ($this->productObj->delete_supplier_product_zone($postdata))
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.property_deleted_successfully');
            }
        }
        return Response::json($op);
    }

    public function save_supplier_product_zone ()
    {
        $postdata = Input::all();        
        $validator = Validator::make($postdata, ['geo_zone_id'=>'required',
												 'supplier_product_zone.mode_id'=>'required',
												 'supplier_product_zone.delivery_days'=>'required',
												 'supplier_product_zone.delivery_charge'=>'required'
											    ], Lang::get('create_zone_shipment.validation'));
        $status = 422;
        if (!$validator->fails())
        {
            $result['status'] = 'ERR';
            $result['msg'] = Lang::get('general.something_went_wrong');
            if (!empty($postdata))
            {
                $postdata['updated_by'] = $this->account_id;
                $postdata['supplier_id'] = $this->supplier_id;
                $result = $this->productObj->save_supplier_product_zone($postdata);
                $status = 200;
                return Response::json($result, $status);
            }
        }
        else
        {
            $result['error'] = $validator->messages(true);
        }
        return Response::json($result, $status);
    }

    public function get_combinations ()
    {
        $op = array();
        $data = array();
        $op['status'] = 'ERR';
        $comb_data = $this->productObj->products_combinations();
        if (!empty($comb_data))
        {
            $op['contents'] = $comb_data;
            $op['status'] = 'OK';
        }
        return Response::json($op);
    }

    public function is_combination ($data = array())
    {
        $product_combination_exists_check = $this->productObj->get_combination_product_by_id($data);
        return $product_combination_exists_check;
    }

    public function productCategories ()
    {
        $data = $filter = array();
        $postdata = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        $filter['search_term'] = isset($postdata['search_term']) && !empty($postdata['search_term']) ? $postdata['search_term'] : '';
        if (!empty($postdata) && !empty($data) && isset($postdata['submit']) && $postdata['submit'] == 'Export')
        {
            $data = array_merge($data, $filter);
            $res['user_details'] = $this->productObj->getProductCategories($data);
            $output = View::make('seller.products.view_categories_list_excel', $res);
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
            $data = array_merge($data, $filter);
            $res['user_details'] = $this->productObj->getProductCategories($data);
            return View::make('seller.products.view_categories_list_print', $res);
        }
        else
        {
            return View::make('seller.products.categories_list', $data);
        }
    }

    public function update_category ()
    {
        $postdata = Input::all();
        $op = array();
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata))
        {
            $postdata['supplier_id'] = $this->supplier_id;
            $postdata['updated_by'] = $this->account_id;
            $result = $this->productObj->update_category($postdata);
            if (!empty($result))
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('product_controller.category_updated');
            }
            else
            {
                $op['status'] = 'WARN';
                $op['msg'] = Lang::get('general.there_is_no_changes');
            }
        }
        return Response::json($op);
    }

    public function new_category ()
    {
        $postdata = Input::all();
        $status = 422;
        $op = array();
        $check = '';
        $category = $postdata['category']['category'];
        if (empty($postdata['categor_id']))
        {
            $check = $this->productObj->check_category_exists($category);
        }
        // $op['msg'] = Lang::get('general.something_went_wrong');
        $validator = Validator::make($postdata, Config::get('validations.CREATE_CATEGORY.RULES'), Config::get('validations.CREATE_CATEGORY.MESSAGES'));
        if (!$validator->fails())
        {
            $postdata['supplier_id'] = $this->supplier_id;
            if (empty($check))
            {
                if (isset($postdata['category_id']) && !empty($postdata['category_id']))
                {
                    $postdata['category_id'] = $postdata['categor_id'];
                }
                $postdata['category_name'] = $category;
                $postdata['parent_category_id'] = $postdata['category']['parent_category_id'];
                $postdata['updated_by'] = $this->account_id;
                $result = $this->productObj->add_category($postdata);
                if (!empty($result))
                {
                    $status = 200;
                    $op['status'] = 'OK';
                    $op['msg'] = Lang::get('product_controller.category_updated');
                }
            }
        }
        else
        {
            $op['error'] = $validator->messages(true);
        }
        return Response::json($op, $status);
    }

    //Product Items
    public function product_items ()
    {
        $data = $filter = array();
        $postdata = Input::all();
        if (!empty($postdata))
        {
            $filter['search_term'] = (isset($postdata['search_term']) && !empty($postdata['search_term'])) ? $postdata['search_term'] : '';
            $filter['currency_id'] = (isset($postdata['currency_id']) && !empty($postdata['currency_id'])) ? $postdata['currency_id'] : '';
            $filter['from'] = (isset($postdata['from']) && !empty($postdata['from'])) ? $postdata['from'] : '';
            $filter['to'] = (isset($postdata['to']) && !empty($postdata['to'])) ? $postdata['to'] : '';
            $filter['category'] = (isset($postdata['category']) && !empty($postdata['category'])) ? $postdata['category'] : '';
        }
        $data['supplier_id'] = $this->supplier_id;
        if (Request::ajax())
        {
            $data['count'] = true;
            $ajaxdata['data'] = array();
            $ajaxdata['draw'] = $postdata['draw'];
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['supplier_id'] = $this->supplier_id;
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->productObj->get_product_items($data);
            if ($ajaxdata['recordsTotal'] > 0)
            {
                $data = array_merge($data, $filter);
                $ajaxdata['recordsFiltered'] = $this->productObj->get_product_items($data);
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                    $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                    $data['order'] = $postdata['order'][0]['dir'];
                    unset($data['count']);
                    $ajaxdata['data'] = $this->productObj->get_product_items($data);
                }
            }
            return Response::json($ajaxdata);
        }
        else if (!empty($postdata) && !empty($data) && isset($postdata['submit']) && $postdata['submit'] == 'Export')
        {
            $data = array_merge($data, $filter);
            $res['product_list'] = $this->productObj->get_product_items($data);
            $output = View::make('supplier.products.view_product_list_excel', $res);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Disposition'=>'attachment; filename=Product_list'.date('Y-m-d').'.xls',
                'Content-Transfer-Encoding'=>'binary'
            );
            return Response::make($output, 200, $headers);
        }
        else if (!empty($postdata) && isset($postdata['submit']) && $postdata['submit'] == 'Print')
        {
            $data = array_merge($data, $filter);
            $res['product_list'] = $this->productObj->get_product_items($data);
            return View::make('supplier.products.view_product_list_print', $res);
        }
        else
        {
            $data['categories'] = $this->productObj->get_categories();
            $data['currencies'] = $this->productObj->currency_list();
            return View::make('supplier.products.product_list', $data);
        }
    }

    public function order_list ($product_id)
    {
        $data = array();
        $submit = '';
        $from = '';
        $postdata = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        $data['product_id'] = $product_id;
        $data['from'] = '';
        $data['to'] = '';
        if (!empty($postdata))
        {
            $data['from'] = $postdata['from'];
            $data['to'] = $postdata['to'];
        }
        //print_r($data);exit;
        if (Request::ajax())
        {
            $data['counts'] = true;
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->productObj->get_order_list($data);
            $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
            $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
            $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
            $data['order'] = $postdata['order'][0]['dir'];
            unset($data['counts']);
            $ajaxdata['data'] = $this->productObj->get_order_list($data);
            $ajaxdata['draw'] = $postdata['draw'];
            $ajaxdata['url'] = URL::to('/');
            return Response::json($ajaxdata);
        }
        else
        {
            return View::make('supplier.stock.orders_list', $data);
        }
    }

    public function productStockList ()
    {
        $data = array();
        return View::make('supplier.stock.product_list', $data);
    }

    public function add_new_products ()
    {
        $data['account_id'] = $this->account_id;
        $data['brands'] = $this->productObj->get_brands($data);
        $data['categories'] = $this->productObj->get_categories();
        $data['currencies'] = $this->productObj->currency_list();
        $op['status'] = 'OK';
        $op['content'] = View::make("supplier.products.add_new_product", $data)->render();
        return Response::json($op);
    }

    public function edit_product ($id)
    {
        if (!empty($id))
        {
            $data['product_id'] = $id;
            $data['supplier_id'] = $this->supplier_id;
            $data['account_id'] = $this->account_id;
            $data['brands'] = $this->productObj->get_brands($data);
            $data['categories'] = $this->productObj->get_categories();
            $data['details'] = $this->productObj->get_product_items($data);
            $data['currencies'] = $this->productObj->currency_list();
            //print_r($data);exit;
            $op['content'] = View::make('supplier.products.add_new_product', $data)->render();
            $op['status'] = 'OK';
            $op['msg'] = Lang::get('general.updated_successfully');
        }
        return Response::json($op);
    }

    public function product_status ()
    {
        $postdata = Input::all();
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($id) && !empty($postdata))
        {
            $postdata['account_id'] = $this->account_id;
            $result = $this->productObj->change_product_status($postdata);
            if (!empty($result))
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('product_controller.category_updated');
            }
        }
        return Response::json($op);
    }

    public function delete_product ($id)
    {
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($id))
        {
            $postdata['id'] = $id;
            $result = $this->productObj->delete_product($postdata);
            if (!empty($result))
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('product_controller.category_updated');
            }
        }
        return Response::json($op);
    }

    public function brand_list ()
    {
        $data = array();
        $postdata = Input::all();
        $data['search_term'] = '';
        $data['supplier_id'] = $this->supplier_id;
        if (!empty($postdata))
        {
            $data['search_term'] = $postdata['search_term'];
        }
        if (!empty($postdata) && !empty($data) && isset($postdata['submit']) && $postdata['submit'] == 'Export')
        {
            $res['brands'] = $this->productObj->getBrandsList($data);
            $output = View::make('seller.products.view_brand_list_excel', $res);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Disposition'=>'attachment; filename=Brand_list'.date('Y-m-d').'.xls',
                'Content-Transfer-Encoding'=>'binary'
            );
            return Response::make($output, 200, $headers);
        }
        else if (!empty($postdata) && isset($postdata['submit']) && $postdata['submit'] == 'Print')
        {
            $res['brands'] = $this->productObj->getBrandsList($data);
            return View::make('seller.products.view_brand_list_print', $res);
        }
        else
        {
            return View::make('seller.products.brand_list', $data);
        }
    }

    public function check_sku_valid ()
    {
        $postdata = Input::all();
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata))
        {
            $result = $this->productObj->check_sku_valid($postdata);
            if (!empty($result))
            {
                $op['status'] = 'ERR';
                $op['msg'] = '<span class="error_msg error">SKU aldready exists</span>';
            }
            else
            {
                $op['status'] = 'OK';
                $op['msg'] = '<span class="error_msg text-success">Valid SKU</span>';
            }
        }
        return Response::json($op);
    }

    public function properties_values_checked ()
    {
        $op = ['properties'=>[],
            'filterables'=>[],
            'values'=>[]];
        if (Input::has('category_id'))
        {
            $op = $this->productObj->properties_values_checked(Input::get('category_id'));
        }
        return Response::json($op);
    }

    public function product_property_values_for_checktree ()
    {
        $property_id = Input::has('property_id') ? Input::get('property_id') : 0;
        $values = $this->productObj->product_propery_values_for_checktree($property_id);
        return Response::json(!empty($values) ? $values : array());
    }

    public function configureProduct ($supplier_product_code)
    {
        $data = [];
        $data['supplier_product_code'] = $supplier_product_code;		
        return View::make('seller.products.product_configure', $data);
    }

    public function supplier_products ()
    {
        $data = $filter = array();
        $postdata = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        $data['currency_id'] = $this->currency_id;
        $data['country_id'] = $this->country_id;
        if (!empty($postdata))
        {
            $filter['search_term'] = (isset($postdata['search_term']) && !empty($postdata['search_term'])) ? $postdata['search_term'] : '';
        }
        if (Request::ajax())
        {
            $ajaxdata['data'] = array();
            $ajaxdata['draw'] = $postdata['draw'];
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->productObj->get_supplier_products_list($data, true);
            if ($ajaxdata['recordsTotal'] > 0)
            {
                $data = array_merge($data, $filter);
                $ajaxdata['recordsFiltered'] = $this->productObj->get_supplier_products_list($data, true);
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                    $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                    $data['order'] = $postdata['order'][0]['dir'];
                    $ajaxdata['data'] = $this->productObj->get_supplier_products_list($data);
                }
            }
            return Response::json($ajaxdata);
        }
        else if (!empty($postdata) && !empty($data) && isset($postdata['submit']) && $postdata['submit'] == 'Export')
        {
            $data = array_merge($data, $filter);
            $res['product_list'] = $this->productObj->get_supplier_products_list($data);
            $output = View::make('supplier.products.view_product_list_excel', $res);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Disposition'=>'attachment; filename=Product_list'.date('Y-m-d').'.xls',
                'Content-Transfer-Encoding'=>'binary'
            );
            return Response::make($output, 200, $headers);
        }
        else if (!empty($postdata) && isset($postdata['submit']) && $postdata['submit'] == 'Print')
        {
            $data = array_merge($data, $filter);
            $res['product_list'] = $this->productObj->get_supplier_products_list($data);
            return View::make('supplier.products.view_product_list_print', $res);
        }
        else
        {
            $data['supplier_id'] = $this->supplier_id;
            $product_categories = $this->productObj->getProductCategories(array('supplier_id'=>$this->supplier_id));
            $data['product_categories'] = $this->productObj->format_categories(0, $product_categories);
			//print_r($data);exit;
            return View('seller.products.supplier_products_list', $data);
        }
    }

    public function save_association ()
    {
        $postdata = Input::all();
        $op = array();
        $assoc_category_id = implode(',', $postdata['catlist']);
        if ($this->productObj->save_assocaiation($assoc_category_id, $postdata))
        {
            $op['status'] = 'OK';
            $op['msg'] = Lang::get('general.assoc_update');
        }
        return Response::Json($op);
    }

    public function delete_payment ()
    {
        $postdata = Input::all();
        $op = array();
        if ($res = $this->productObj->delete_payment($postdata))
        {			
            $op['status'] = 'OK';
            $op['msg'] = 'Deleted Successfully';
        } 
        return Response::Json($op);
    }

    public function product_payment_list ()
    {
        $postdata = Input::all();
        if (Request::ajax())
        {
            $data['supplier_product_id'] = $postdata['supplier_product_id'];
            $data['count'] = true;
            $ajaxdata['data'] = array();
            $ajaxdata['draw'] = $postdata['draw'];
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->productObj->product_payment_list($data);
            if ($ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $this->productObj->product_payment_list($data);
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                    $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                    $data['order'] = $postdata['order'][0]['dir'];
                    unset($data['count']);
                    $ajaxdata['data'] = $this->productObj->product_payment_list($data);
                }
            }
            return Response::json($ajaxdata);
        }
    }

    public function save_payment_types ()
    {
        $postdata = Input::all();        
        $op = array();        
        $validator = Validator::make($postdata, ['payment_list_id'=>'required', 'payment_gateway_select'=>'required'], Lang::get('add_payment.validation'));
        if (!$validator->fails())
        {
            if ($this->productObj->save_payment_types($postdata))
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.payment_ins');
            }
        }
        else
        {            
            $op['error'] = $validator->messages(true);
        }
        return Response::json($op);
    }

    public function tempimg_upload ()
    {
        $postdata = Input::all();
        $op = array();
        $file = Input::file('file');
        $ext = $file->getMimeType();
        $filename = $file->getClientOriginalName();
        $mine_type = array(
            'image/jpeg'=>'jpg',
            'image/jpg'=>'jpg',
            'image/png'=>'png',
            'image/gif'=>'gif');
        $ext = $mine_type[$ext];
        $original_name = $filename.'.'.$ext;
        $filename = $filename.date('YmdHis').'.'.$ext;
        $year = date('Y');
        $month = date('m');
        $destinationPath = Config::get('constants.TEMP_IMAGE_FOLDER');
        Input::file('file')->move($destinationPath, $filename);
        $path = $destinationPath.$filename;
        $op['img_path'] = $path;
        $op['img_name'] = $filename;
        $op['orginal_name'] = $original_name;
        return Response::json($op);
    }

    public function edit_stock ($id)
    {
        if (!empty($id))
        {
            $product_id = $id;
            $data['details'] = $this->productObj->get_product_stocks($product_id);
            $op['content'] = View::make('supplier.products.edit_stocks', $data)->render();
            $op['status'] = 'OK';
            $op['msg'] = Lang::get('general.updated_successfully');
        }
        return Response::json($op);
    }

    public function update_stock ()
    {
        $postdata = Input::all();
        $op['status'] = 'OK';
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (Request::ajax())
        {
            if (!empty($postdata))
            {
                $postdata['supplier_id'] = $this->supplier_id;
                $current_details = $this->productObj->get_product_stocks($postdata['product_id']);
                $postdata['current_stock'] = $current_details->current_stock;
                $postdata['current_stock_on_hand'] = $current_details->stock_on_hand;
                $res = $this->productObj->update_stock($postdata);
                if (!empty($res))
                {
                    $op['status'] = 'OK';
                    $op['msg'] = Lang::get('product_controller.product_stock');
                }
            }
        }
        return Response::json($op);
    }

    public function order_cancel ()
    {
        $postdata = Input::all();
        $postdata['order_id'] = '1';
        $response = $this->productObj->order_cancel($postdata);
        $data['order_details'] = $this->productObj->order_details($postdata);
        //print_r($response);
        $data['order_id'] = $postdata['order_id'];
        if ($response > 0)
        {
            echo 'Your order has been cancelled';
            $mstatus = Mailer::send(array(
                        'to'=>$emails,
                        'subject'=>'Order Cancelation - Your Order with '.App::site_settings()->site_domain.' [#'.$postdata['order_id'].'] has been successfully Cancelled!',
                        'view'=>'emails.product_order_cancelation',
                        'data'=>$data
            ));
        }
        else
        {
            echo 'Since, your ordered item has been dispatched. You cant cancel your order.';
        }
    }

    public function couriers_list ()
    {
        $data = array();
        $postdata = Input::all();
        if (Request::ajax())
        {
            $data['count'] = true;
            $ajaxdata['draw'] = $postdata['draw'];
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['supplier_id'] = $data['supplier_id'] = $this->supplier_id;
            $ajaxdata['data'] = array();
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->productObj->get_suppliers_couries($data);
            if ($ajaxdata['recordsTotal'] > 0)
            {
                $data['search_term'] = isset($postdata['search_term']) && !empty($postdata['search_term']) ? $postdata['search_term'] : '';
                $ajaxdata['recordsFiltered'] = $this->productObj->get_suppliers_couries($data);
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                    $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                    $data['order'] = $postdata['order'][0]['dir'];
                    $data['search_term'] = $postdata['search_term'];
                    unset($data['count']);
                    $ajaxdata['data'] = $this->productObj->get_suppliers_couries($data);
                }
            }
            return Response::json($ajaxdata);
        }
        else
        {
            return View::make('supplier.couriers_list', $data);
        }
    }

    public function save_courier ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        $data = Input::all();
        if (!empty($data))
        {
            $data['supplier_id'] = $this->supplier_id;
            $data['courier'] = array_filter($data['courier']);
            $data = array_filter($data);
            if ($this->productObj->save_courier($data))
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.updated_successfully');
            }
        }
        return Response::json($op);
    }

    public function courier_details ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        $data = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        if (isset($data['courier_id']) && !empty($data['courier_id']))
        {
            $op['courier'] = $this->productObj->get_courier_details($data);
            if ($op['courier'])
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.information_received');
            }
        }
        return Response::json($op);
    }

    public function delete_courier ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        $data = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        if (isset($data['courier_id']) && !empty($data['courier_id']))
        {
            if ($this->productObj->delete_courier($data))
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.courier.deleted');
            }
        }
        return Response::json($op);
    }

    public function courier_list_to_add ()
    {
        $data = array();
        $data['supplier_id'] = $this->supplier_id;
        $couries = $this->productObj->get_couries_to_add($data);
        return Response::json($couries);
    }

    public function couriers_mode_list ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        $data = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        if (isset($data['courier_id']) && !empty($data['courier_id']))
        {
            $op['modes'] = $this->productObj->courier_modes_list($data);
            if (isset($op['modes']))
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.courier.mode.deleted');
            }
        }
        return Response::json($op);
    }

    public function couriers_mode_save ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        $data = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        if ($this->productObj->courier_modes_save($data))
        {
            $op['status'] = 'OK';
            $op['msg'] = Lang::get('general.updated_successfully');
        }
        else
        {
            $op['status'] = 'OK';
            $op['msg'] = Lang::get('general.there_is_no_changes');
        }
        return Response::json($op);
    }

    public function couriers_mode_delete ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        $data = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        if ($this->productObj->courier_modes_delete($data))
        {
            $op['status'] = 'OK';
            $op['msg'] = Lang::get('general.courier.mode.deleted');
        }
        return Response::json($op);
    }

    public function Product_stock_log_Report ()
    {
        $data = array();
        //$submit = '';
        //$from = '';
        $postdata = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        $data['from'] = '';
        $data['to'] = '';
        if (!empty($postdata))
        {
            $data['from'] = $postdata['from'];
            $data['to'] = $postdata['to'];
            $data['product_id'] = $postdata['product_id'];
        }
        //print_r($data);exit;
        if (Request::ajax())
        {
            $data['counts'] = true;
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->productObj->get_stock_log_report($data);
            $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
            $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
            $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
            $data['order'] = $postdata['order'][0]['dir'];
            unset($data['counts']);
            $ajaxdata['data'] = $this->productObj->get_stock_log_report($data);
            $ajaxdata['draw'] = $postdata['draw'];
            $ajaxdata['url'] = URL::to('/');
            return Response::json($ajaxdata);
        }
        else
        {
            $data['stock_list'] = $this->productObj->get_stock_log_report($data);
            return View::make('supplier.products.product_stock_log', $data);
        }
    }

    public function cover_images ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $postdata = Input::all();
        $file_path = $this->productObj->product_img_details($postdata);
        $data['position'] = Config::get("constants.POSITION");
        $data['image_id'] = $postdata['image_id'];
        $data['img_path'] = $file_path->img_path;
        $data['product_id'] = $file_path->relative_post_id;
        $res = $this->productObj->change_cover_image($data);
        if ($res)
        {
            $op['status'] = 'OK';
            $op['msg'] = Lang::get('general.cover_pictuer_updated_successfully');
        }
        return Response::json($op);
    }

    public function supplier_products_zones ()
    {
        $data = $filter = array();
        $postdata = Input::all();		
        if (!empty($postdata))
        {
            $filter['search_term'] = (isset($postdata['search_term']) && !empty($postdata['search_term'])) ? $postdata['search_term'] : '';
            $filter['supplier_product_id'] = (isset($postdata['supplier_product_id']) && !empty($postdata['supplier_product_id'])) ? $postdata['supplier_product_id'] : '';
        }
        if (Request::ajax())
        {			
            $data['count'] = true;
            $ajaxdata['data'] = array();
            $ajaxdata['draw'] = $postdata['draw'];
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->productObj->supplier_products_zones($data);
            if ($ajaxdata['recordsTotal'] > 0)
            {
                $filter = array_filter($filter);
                if (!empty($filter))
                {
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->productObj->supplier_products_zones($data);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                    $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                    $data['order'] = $postdata['order'][0]['dir'];
                    unset($data['count']);
                    $ajaxdata['data'] = $this->productObj->supplier_products_zones($data);
                }
            }
            return Response::json($ajaxdata);
        }
        else if (!empty($postdata) && !empty($data) && isset($postdata['submit']) && $postdata['submit'] == 'Export')
        {
            $data = array_merge($data, $filter);
            $res['product_list'] = $this->productObj->supplier_products_zones($data);
            $output = View::make('admin.products.view_product_list_excel', $res);
            $headers = array(
                'Pragma'=>'public',
                'Expires'=>'public',
                'Cache-Control'=>'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control'=>'private',
                'Content-Disposition'=>'attachment; filename=Product_list'.date('Y-m-d').'.xls',
                'Content-Transfer-Encoding'=>'binary'
            );
            return Response::make($output, 200, $headers);
        }
        else if (!empty($postdata) && isset($postdata['submit']) && $postdata['submit'] == 'Print')
        {
            $data = array_merge($data, $filter);
            $res['product_list'] = $this->productObj->supplier_products_zones($data);
            return View::make('admin.products.view_product_list_print', $res);
        }
        else
        {
            return View::make('admin.products.products_list', $data);
        }
    }

    public function editProducts ($product_code)
    {
        $data = [];
        $data['supplier_id'] = $this->supplier_id;
        $data['product_id'] = 0;
        $data['image'] = 0;
        $data['combinations'] = 0;
        $data['add_stock'] = 0;
        $data['supplier_product_id'] = 0;
        $data['currency_id'] = $this->currency_id;
        $data['product_code'] = $product_code;
        $data['product_details'] = $this->productObj->get_supplier_products_list($data);
        if (!empty($data['product_details']))
        {
            $data['meta_product_info'] = $this->productObj->get_meta_info($data['product_details']->product_id);
            $product_categories = $this->productObj->getProductCategories(array('supplier_id'=>$this->supplier_id));
            $data['product_categories'] = $this->productObj->format_categories(0, $product_categories);
            return View::make('supplier.products.product_configure', $data);
        }
        else
        {
            return App::abort(404);
        }
    }

    public function addProduct ($product_code = '')
    {
        $data = [];
        $data['account_id'] = $this->account_id;
        if ($product_code)
        {
            $product = $this->productObj->get_product_id($product_code);
            $data['product_id'] = $product->product_id;
            $data['product_cmb_id'] = $product->product_cmb_id;
            $data['supplier_id'] = $this->supplier_id;
            $supplier_product_code = $this->productObj->save_supplier_product($data);
            if (!empty($supplier_product_code))
            {
                return Redirect::to('seller/products/'.$supplier_product_code);
            }
            return View::make('seller.products.add_existing_product', $data);
        }
        else
        {
            $data['brands'] = $this->productObj->get_brands($data);
            $product_categories = $this->productObj->getProductCategories(array('supplier_id'=>$this->supplier_id));
            $data['product_categories'] = $this->productObj->format_categories(0, $product_categories);
            $data['currencies'] = $this->productObj->currency_list();
            $data['fields'] = ShoppingPortal::getHTMLValidation('seller.products.save');			
            return View::make('seller.products.add_new_product', $data);
        }
    }

    public function checkProduct ()
    {
        $data = Input::all();
        $d = array_filter($data);
        if (!empty($d))
        {
            $data['supplier_id'] = $this->supplier_id;
            $op = [];
            if ($product = $this->productObj->checkProduct($data))
            {
                if (!empty($product->supplier_product_id))
                {
                    $op['status'] = 'ERR';
                    $status = 200;
                    $op['msg'] = Lang::get('geneal.product.already_exist');
                }
                else
                {
                    $op['status'] = 'OK';
                    $status = 308;
                    $op['url'] = URL::to('/seller/products/add/'.$product->product_code);
                }
            }
            else
            {
                $op['status'] = 'OK';
                $status = 308;
                $op['url'] = URL::to('/seller/products/add');
            }
        }
        else
        {
            $op['status'] = 'ERR';
            $status = 200;
            $op['msg'] = Lang::get('general.please_enter_ean_or_ipc_code');
        }
        return Response::json($op, $status);
    }

    public function save_package_info ()
    {
        $postdata = Input::all();
        $op['status'] = 'ERR';
        if (!empty($postdata))
        {
            $update = $this->productObj->save_package_info($postdata);
            if ($update)
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.updated_successfully');
            }
        }
        return Response::json($op);
    }

}
