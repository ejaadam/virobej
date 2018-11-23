<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Commonsettings;
use Input;
use Response;
use Session;
class DataController extends Controller
{

    public function __construct ()
    {
        $this->commonObj = new Commonsettings();
    }

    public function product_visibility_list ()
    {
        $product_visibility_list = $this->commonObj->product_visibility_list();
        return Response::json(!empty($product_visibility_list) ? $product_visibility_list : array());
    }

    public function product_condition_list ()
    {
        $product_condition_list = $this->commonObj->product_condition_list();
        return Response::json(!empty($product_condition_list) ? $product_condition_list : array());
    }

    public function redirect_disabled_list ()
    {
        $redirect_disabled_list = $this->commonObj->redirect_disabled_list();
        return Response::json(!empty($redirect_disabled_list) ? $redirect_disabled_list : array());
    }

    public function top_menu_list ()
    {
        $postdata = Input::all();
        $top_menu_list = $this->commonObj->top_menu_list($postdata);
        return Response::json(!empty($top_menu_list) ? $top_menu_list : array());
    }

    public function menu_position_list ()
    {
        $menu_position_list = $this->commonObj->menu_position_list();
        return Response::json(!empty($menu_position_list) ? $menu_position_list : array());
    }

    public function group_list ()
    {
        $postdata = Input::all();
        $group_list = $this->commonObj->group_list($postdata);
        return Response::json(!empty($group_list) ? $group_list : array());
    }

    public function check_portal_setting ()
    {
        return 1;
        return false;
    }

    public function payment_mode_list ()
    {
        $payment_mode_list = $this->commonObj->payment_mode_list();
        return Response::json(!empty($payment_mode_list) ? $payment_mode_list : array());
    }

    public function get_available_payment_gateway ()
    {
        $postdata = Input::all();
        $get_available_payment_gateway = $this->commonObj->get_available_payment_gateway($postdata);
        return Response::json(!empty($get_available_payment_gateway) ? $get_available_payment_gateway : array());
    }

    public function countries_list ()
    {
        $data = Input::all();
        $countries = $this->commonObj->get_country_list($data, true);
        return Response::json(!empty($countries) ? $countries : array());
    }

    public function states_list ()
    {
        $data = Input::all();
        $states = $this->commonObj->get_state_list($data, true);
        return Response::json(!empty($states) ? $states : array());
    }

    public function city_list ()
    {
        $data = Input::all();
        $cities = $this->commonObj->get_city_list($data, true);
        return Response::json(!empty($cities) ? $cities : array());
    }

    public function store_list ()
    {
        $supplier_id = Input::get('supplied_id');

        $stores = $this->commonObj->get_stores_list($supplier_id);
        return Response::Json(!empty($stores) ? $stores : array());
    }

    public function courier_list ()
    {
        $data = array();
        if (isset($this->supplier_id))
        {
            $data['supplier_id'] = $this->supplier_id;
        }
        $couriers = $this->commonObj->get_courier_list($data);
        return Response::json(!empty($couriers) ? $couriers : array());
    }

    public function courier_mode_list ()
    {
        $courier_modes = $this->commonObj->get_courier_mode_list();
        return Response::json(!empty($courier_modes) ? $courier_modes : array());
    }

    public function all_product_categories_list ()
    {
        $data = array();
        $data['parent_category_id'] = Input::get('parent_category_id') ? Input::get('parent_category_id') : 0;
        $data['search_term'] = Input::get('search_term') ? Input::get('search_term') : '';
        $categories = $this->commonObj->get_all_categories($data['parent_category_id'], $data['search_term']);
        return Response::json(!empty($categories) ? $categories : array());
    }

    public function product_categories_list ()
    {
        $data = array();
        $data['parent_category_id'] = Input::has('parent_category_id') ? Input::get('parent_category_id') : '';
        $data['search_term'] = Input::has('search_term') ? Input::get('search_term') : '';
        if (Input::has('withUrl'))
        {
            $data['withUrl'] = true;
        }
        $data['supplier_id'] = Input::has('supplier_id') ? Input::get('supplier_id') : '';
        $categories = $this->commonObj->get_categories_list($data);
        //echo '<pre>';print_r($categories);exit;
        return Response::json(!empty($categories) ? $categories : array());
    }

    public function product_brands_list ()
    {
        $brands = $this->commonObj->get_brands_list();
        return Response::json(!empty($brands) ? $brands : array());
    }

    public function supplier_list ()
    {
        $suppliers = $this->commonObj->get_suppliers_list();
        return Response::json(!empty($suppliers) ? $suppliers : array());
    }

    public function supplier_code_list ()
    {
        $suppliers = $this->commonObj->get_suppliers_code_list();
        return Response::json(!empty($suppliers) ? $suppliers : array());
    }

    public function products_list ()
    {
        $data = array();
        if (Input::has('category_id'))
        {
            $data['category_id'] = Input::get('category_id');
        }
        if (Input::has('brand_id'))
        {
            $data['brand_id'] = Input::get('brand_id');
        }
        $products = $this->commonObj->get_products_list($data);
        return Response::json(!empty($products) ? $products : array());
    }

    public function supplier_products_list ()
    {
        $data = array();
        if (Input::has('category_id'))
        {
            $data['category_id'] = Input::get('category_id');
        }
        if (Input::has('brand_id'))
        {
            $data['brand_id'] = Input::get('brand_id');
        }
        if (Input::has('supplier_id'))
        {
            $data['supplier_id'] = Input::get('supplier_id');
        }
        $products = $this->commonObj->get_supplier_products_list($data);
        return Response::json(!empty($products) ? $products : array());
    }

    public function currencies_list ()
    {
        $currencies = $this->commonObj->get_currencies_list();
        return Response::json(!empty($currencies) ? $currencies : array());
    }

    public function zone_list ()
    {
        $zones = $this->commonObj->zone_list();
        return Response::json(!empty($zones) ? $zones : array());
    }

    public function tax_status_list ()
    {
        $tax_status = $this->commonObj->tax_status_list();
        return Response::json(!empty($tax_status) ? $tax_status : array());
    }

    public function order_status_list ()
    {
        $order_status_list = $this->commonObj->get_order_status_list();
        return Response::json(!empty($order_status_list) ? $order_status_list : array());
    }

    public function units_list ()
    {
        $units = $this->commonObj->get_units_list();
        return Response::json(!empty($units) ? $units : array());
    }

    public function parent_properties_list ()
    {
        $data = array();
        $properties = $this->commonObj->get_parent_properties_list();
        return Response::json(!empty($properties) ? $properties : array());
    }

    public function address_type_list ()
    {
        $data = array();
        $address_type = $this->commonObj->get_address_type_list();
        return Response::json(!empty($address_type) ? $address_type : array());
    }

    public function tax_list ()
    {
        $data = array();
        $tax_list = $this->commonObj->get_tax_list();
        return Response::json(!empty($tax_list) ? $tax_list : array());
    }

    public function support_category ()
    {
        $data = array();
        $support_category = $this->commonObj->support_category();
        return Response::json(!empty($support_category) ? $support_category : array());
    }

    public function wallet_list ()
    {
        $data = array();
        $wallet_list = $this->commonObj->wallet_list();
        return Response::json(!empty($wallet_list) ? $wallet_list : array());
    }

    public function commission_type ()
    {
        $data = array();
        $commission_type = $this->commonObj->commission_type();
        return Response::json(!empty($commission_type) ? $commission_type : array());
    }

    public function properies_for_checktree ()
    {
        $parent_property_id = Input::has('parent_property_id') ? Input::get('parent_property_id') : 0;
        $properties = $this->commonObj->properies_for_checktree($parent_property_id);
        return Response::json(!empty($properties) ? $properties : array());
    }

    public function properies_values_for_checktree ()
    {
        $property_id = Input::has('property_id') ? Input::get('property_id') : 0;
        $values = $this->commonObj->propery_values_for_checktree($property_id);
        return Response::json(!empty($values) ? $values : array());
    }

    public function tax_classes_list ()
    {
        $classes = $this->commonObj->tax_classes_list();
        return Response::json(!empty($classes) ? $classes : array());
    }

    public function payment_list ()
    {
        $payment_list = $this->commonObj->payment_list();
        return Response::json(!empty($payment_list) ? $payment_list : array());
    }

    public function withdrawlPaymentList ()
    {
        $payment_list = $this->commonObj->withdrawalPaymentList();
        return Response::json(!empty($payment_list) ? $payment_list : array());
    }

    public function faq_category ()
    {
        $faq_category = $this->commonObj->faq_category();
        return Response::json(!empty($faq_category) ? $faq_category : array());
    }

    public function replacement_days ()
    {
        $data = array();
        $replacement_days = $this->commonObj->replacement_days();
        return Response::json(!empty($replacement_days) ? $replacement_days : array());
    }

    public function product_parent_categories ()
    {
        $data = array();
        $product_parent_categories = $this->commonObj->product_parent_categories();
        return Response::json(!empty($product_parent_categories) ? $product_parent_categories : array());
    }

    public function gender_list ()
    {
        $tax_status = $this->commonObj->gender_list();
        return Response::json(!empty($tax_status) ? $tax_status : array());
    }

    public function get_tags ()
    {
        $tags = $this->commonObj->get_tags(Input::all());
        return Response::json(!empty($tags) ? $tags : array());
    }

    public function get_pincodes ()
    {
        $pincodes = $this->commonObj->get_pincodes(Input::all());
        return Response::json(!empty($pincodes) ? $pincodes : array());
    }

    public function account_list ()
    {
        $account = $this->commonObj->get_account_list();
        return Response::json(!empty($account) ? $account : array());
    }

    public function checkPincode ()
    {

        $status = 406;
        if (Input::has('pincode'))
        {
            $pincode = $this->commonObj->checkPincode(Input::get('pincode'),Input::get('country_id'), true);			
            if (!empty($pincode))
            {
                $pincode->msg = '';
                $status = 200;
            }
            else
            {
                $pincode = [];
                $pincode['msg'] = 'Service Not Avaliable';
            }
        }
        return Response::json($pincode, $status);
    }

    public function childrensCategories ()
    {
        $status = 406;
        $category = $this->commonObj->get_category_childs(1);
        if (!empty($category))
        {
            $status = 200;
            foreach ($category as $cat)
            {
                $cat->url = $this->commonObj->generateBrowseURL(array('category'=>$cat));
            }
        }
        return Response::json($category, $status);
    }
	
	public function sellerCategories ()
    {
        $wdata = [];
        $postdata = Input::all();
        if (!empty($postdata))
        {
            if (isset($postdata['pbcat_id']))
            {
                $wdata['pbcat_id'] = $postdata['pbcat_id'];
            }
            if (isset($postdata['excbcat_id']))
            {
                $wdata['excbcat_id'] = $postdata['excbcat_id'];
            }
            if (isset($postdata['cat_id']))
            {
                $wdata['cat_id'] = $postdata['cat_id'];
            }
            if (isset($postdata['excpbcat_id']))
            {
                $wdata['excpbcat_id'] = $postdata['excpbcat_id'];
            }
        }
        $op['data'] = $this->commonObj->sellerCategories($wdata);
        //$op['countries'] = $this->accountObj->country_iso2();
        /* if (!empty($op['countries']))
        {
            array_walk($op['countries'], function(&$ctry)
            {
                if (!empty($ctry->iso2))
                {
                    $ctry->flag = URL($this->config->get('constants.FLAGS_PATH')).'/'.strtolower($ctry->iso2).'.png';
                }
                else
                {
                    $ctry->flag = '';
                }
            });
        }         */
		$status = 200;
		return Response::json($op, $status);
    }
	
	public function sellerAllCategories ()
    {
        $wdata = [];
        $postdata = Input::all();
        if (!empty($postdata))
        {
            if (isset($postdata['pbcat_id']))
            {
                $wdata['pbcat_id'] = $postdata['pbcat_id'];
            }
            if (isset($postdata['excbcat_id']))
            {
                $wdata['excbcat_id'] = $postdata['excbcat_id'];
            }
            if (isset($postdata['cat_id']))
            {
                $wdata['cat_id'] = $postdata['cat_id'];
            }
            if (isset($postdata['excpbcat_id']))
            {
                $wdata['excpbcat_id'] = $postdata['excpbcat_id'];
            }
        }
        $op['data'] = $this->commonObj->sellerAllCategories($wdata);
        //$op['data'] = $this->commonObj->sellerCategories($wdata);
        //$op['countries'] = $this->accountObj->country_iso2();
        /* if (!empty($op['countries']))
        {
            array_walk($op['countries'], function(&$ctry)
            {
                if (!empty($ctry->iso2))
                {
                    $ctry->flag = URL($this->config->get('constants.FLAGS_PATH')).'/'.strtolower($ctry->iso2).'.png';
                }
                else
                {
                    $ctry->flag = '';
                }
            });
        }         */
		$status = 200;
		return Response::json($op, $status);
    }

}
