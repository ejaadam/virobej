<?php
namespace App\Http\Controllers\Api\Seller;
use App\Http\Controllers\Api\APIBase;
use App\Models\Api\Seller\APISupplierProduct;
use App\Models\Memberauth;
use App\Models\Seller\Supplier;
use Input;
use Config;
use Response;
use App\Helpers\ShoppingPortal;
use App\Helpers\ImageLib;
use Lang;
use URL;
use Session;
use Illuminate\Support\Facades\Validator;

class APISupplierProductController extends APIBase
{

    public function __construct ()
    {
        parent::__construct();
        $this->productObj = new APISupplierProduct();
    }

    public function productList ()
    {
        $data = $filter = $ajaxdata = array();
        $postdata = Input::all();		
        $data['supplier_id'] = $this->supplier_id;
        $data['category_id'] = isset($postdata['category_id']) ? $postdata['category_id'] : '';
        $data['brand_id'] = isset($postdata['brand_id']) ? $postdata['brand_id'] : '';
        $data['country_id'] = isset($postdata['country_id']) ? $postdata['country_id'] : $this->country_id;
        $data['currency_id'] = isset($postdata['currency_id']) ? $postdata['currency_id'] : $this->currency_id;
        $filter['search_term'] = isset($postdata['search_term']) && !empty($postdata['search_term']) ? $postdata['search_term'] : '';
        $data['count'] = true;
        $ajaxdata['data'] = array();
        $ajaxdata['draw'] = isset($postdata['draw']) ? $postdata['draw'] : 1;
        $ajaxdata['url'] = URL::to('/');
        if ($ajaxdata['draw'] == 1 || isset($postdata['withFilters']))
        {
            $ajaxdata['filters']['categories'] = $this->productObj->get_supplier_products_list($data, false, true) + [0=>'All'];
            ksort($ajaxdata['filters']['categories']);
            $ajaxdata['filters']['brands'] = $this->productObj->get_supplier_products_list($data, false, false, true) + [0=>'All'];
            ksort($ajaxdata['filters']['brands']);
        }
        $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->productObj->get_supplier_products_list($data);
        if ($ajaxdata['recordsTotal'] > 0)
        {
            $data = array_merge($data, $filter);
            $ajaxdata['recordsFiltered'] = $this->productObj->get_supplier_products_list($data);
            if (!empty($ajaxdata['recordsFiltered']))
            {
                $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                if (isset($postdata['order'][0]['dir']))
                {
                    $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                    $data['order'] = $postdata['order'][0]['dir'];
                }
                unset($data['count']);
                $ajaxdata['data'] = $this->productObj->get_supplier_products_list($data);
            }
        }
        $this->statusCode = 200;
        return Response::json($ajaxdata, $this->statusCode, $this->headers, $this->options);
    }

    public function saveSupplierProduct ()
    {
        $postdata = Input::all();
		print_r($postdata);exit;
        $op['msg'] = Lang::get('general.something_went_wrong');
        //$validator = Validator::make($postdata, Config::get('validations.EXISTING_PRODUCT.RULES'), Config::get('validations.EXISTING_PRODUCT.MESSAGES'));
        $validator = Validator::make($postdata, [
													'product_id'=>'required',
													'supplier_product.product_cmb_id'=>'sometimes|required',													
													'supplier_product.pre_order'=>'required',
													'supplier_product.condition_id'=>'required',
													'supplier_product.is_replaceable'=>'required',
													'spcp.impact_on_price'=>'required'
												], Lang::get('product_items.validation_ext_product'));
        if (!$validator->fails())
        {
            if (!empty($postdata))
            {
                $postdata['supplier_product']['supplier_id'] = $this->supplier_id;
                $postdata['account_id'] = $this->account_id;
                $result = $this->productObj->save_supplier_product($postdata);
                if (!empty($result))
                {
                    $this->statusCode = 200;
                    $op['msg'] = Lang::get('general.actions.updated', ['label'=>Lang::get('general.fields.category')]);
                }
                else
                {
                    $this->statusCode = 208;
                    $op['msg'] = Lang::get('general.there_is_no_changes');
                }
            }
        }
        else
        {
            $op['error'] = $validator->messages(true);
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function changeProductStatus ()
    {
        $postdata = Input::all();
        $op = [];
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata))
        {
            $postdata['account_id'] = $this->account_id;
            $postdata['supplier_id'] = $this->supplier_id;
            $result = $this->productObj->changeProductStatus($postdata);
            if (!empty($result))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.product.status.'.$postdata['status']);
            }
        }
        else
        {
            $this->statusCode = 422;
            $op['msg'] = Lang::get('general.parameteres_missing');
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function deleteProduct ()
    {
        $postdata = Input::all();
        $this->response = [];
        $this->response['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata['supplier_product_id']))
        {
            $postdata['account_id'] = $this->account_id;
            $postdata['supplier_id'] = $this->supplier_id;
            $result = $this->productObj->deleteProduct($postdata);
            if (!empty($result))
            {
                $this->statusCode = 200;
                $this->response['msg'] = Lang::get('general.product.deleted');
            }
        }
        else
        {
            $this->statusCode = 422;
            $this->response['msg'] = Lang::get('general.parameteres_missing');
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function addableProductsList ()
    {
        $post = Input::all();
        $post['supplier_id'] = $this->supplier_id;
        $codes = $this->productObj->getAddableProductsList($post, true);
        $this->statusCode = 200;
        return Response::json($codes, $this->statusCode, $this->headers, $this->options);
    }

    public function supplierProductPriceList ()
    {
        $data = Input::all();
        $ajaxdata = [
            'data'=>[],
            'recordsTotal'=>0,
            'recordsFiltered'=>0,
            'draw'=>$data['draw']
        ];
        $ajaxdata['data'] = array();
        $data['supplier_id'] = $this->supplier_id;
        if (isset($data['product_id']) && !empty($data['product_id']))
        {
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->productObj->getSupplierProductPrices($data, true);
            if (!empty($ajaxdata['recordsFiltered']))
            {
                $data['orderby'] = $data['columns'][$data['order'][0]['column']]['name'];
                $data['order'] = $data['order'][0]['dir'];
                $ajaxdata['data'] = $this->productObj->getSupplierProductPrices($data);
            }
        }
        $this->statusCode = 200;
        return Response::json($ajaxdata, $this->statusCode, $this->headers, $this->options);
    }

    public function configureProduct ()
    {			
        $data = Input::all();
        if (isset($data['supplier_product_code']) && !empty($data['supplier_product_code']))
        {
            $data['supplier_id'] = $this->supplier_id;
            $data['currency_id'] = $this->currency_id;
            $data['country_id'] = $this->country_id;
            $data['product_id'] = 0;
            $data['image'] = 0;
            $data['combinations'] = 0;
            $data['add_stock'] = 0;
            $data['supplier_product_id'] = 0;
            $data['product_details'] = $this->productObj->get_supplier_products_list($data);
            if (!empty($data['product_details']))
            {
                $data['product_id'] = $data['product_details']->product_id;
                $data['editable'] = $this->account_id == $data['product_details']->created_by ? true : false;
                $data['combination_list'] = $this->productObj->get_product_combination_list($data);
                $data['meta_product_info'] = $this->productObj->get_meta_info($data['product_details']->product_id);
                $product_categories = $this->productObj->getProductCategories(array('supplier_id'=>$this->supplier_id));
                $data['product_categories'] = $this->productObj->format_categories(0, $product_categories);
                $data['get_tags'] = $this->productObj->get_tags($data['product_details']->product_id);
                $data['is_shipment'] = $this->productObj->is_ownshipment($data['supplier_id']);
                $data['countries'] = $this->productObj->getProductCountries(['product_id'=>$data['product_id']]);
                $this->statusCode = 200;
            }
        }
        return Response::json($data, $this->statusCode, $this->headers, $this->options);
    }

    public function productCombinationsList ()
    {
        $data = $ajaxdata = $filter = array();
        $post = Input::all();
        $data['currency_id'] = $this->currency_id;
        $data['supplier_id'] = $this->supplier_id;
        $data['product_id'] = (isset($post['product_id']) && !empty($post['product_id'])) ? $post['product_id'] : null;
        $data['supplier_product_id'] = (isset($post['supplier_product_id']) && !empty($post['supplier_product_id'])) ? $post['supplier_product_id'] : null;
        $filter['search_term'] = (isset($post['search_term']) && !empty($post['search_term'])) ? $post['search_term'] : ' ';
        $ajaxdata['draw'] = $post['draw'];
        $ajaxdata['data'] = array();
        $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->productObj->getCombinationsList($data, true);
        if (!empty($ajaxdata['recordsFiltered']))
        {
            $filter['start'] = (isset($post['start']) && !empty($post['start'])) ? $post['start'] : 0;
            $filter['length'] = (isset($post['length']) && !empty($post['length'])) ? $post['length'] : Config::get('constants.DATA_TABLE_RECORDS');
            $data['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
            $data['order'] = $post['order'][0]['dir'];
            $ajaxdata['data'] = $this->productObj->getCombinationsList($data);
        }
        $this->statusCode = 200;
        return Response::json($ajaxdata, $this->statusCode, $this->headers, $this->options);
    }

    public function changeProductCombinationStatus ()
    {
        $postdata = Input::all();
        $op = [];
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata))
        {
            if ($this->productObj->combination_status_chages($postdata))
            {
                if (!empty($postdata['product_id']))
                {
                    $this->statusCode = 200;
                    $op['status_val'] = $postdata['status'];
                    $op['msg'] = Lang::get('product_combinations.psus');
                }
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function deleteProductCombination ()
    {
        $data = Input::all();
        $op = [];
        if (!empty($data))
        {
            $data['updated_by'] = $this->account_id;
            $data['product_cmb_id'] = $data['product_cmb_id'];
            $data['is_deleted'] = Config::get('constants.ON');
            if ($this->productObj->delete_products_combinations($data) && !empty($data['product_id']))
            {
                $res = $this->is_combination($data['product_id']);
                $this->statusCode = 200;
                $op['msg'] = Lang::get('product_combinations.pcds');
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function saveProcuctPrice ()
    {
		
        $postdata = Input::all();
		$postdata['spp']['supplier_id'] = $this->supplier_id;
		$result = $this->productObj->saveProcuctPrice($postdata);		
        $validator = Validator::make($postdata, ['spp.mrp_price'=>'required', 'spp.price'=>'required'], Lang::get('product_items.validation'));
        if (!$validator->fails())
        {
            if ($result)
			{
				$this->statusCode = 200;
				$op['msg'] = Lang::get('general.price_updated_successfully');
			}
			else
			{
				$this->statusCode = 208;
				$op['msg'] = Lang::get('general.there_is_no_changes');
			}
        }
        else
        {
            $op['error'] = $validator->messages(true);
        } 
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function image_details ()
    {
        $op = array();
        $postdata = Input::all();
		//print_r($postdata);exit;
        $this->imageObj = new ImageLib();
        $imgdata = [];
        if (isset($postdata['image_id']) && !empty($postdata['image_id']))
        {
            $file_path = $this->imageObj($postdata['image_id']);
        }
        elseif (isset($postdata['product_id']) && !empty($postdata['product_id']))
        {
            if (!empty($postdata['combination_id']) && isset($postdata['combination_id']))
            {
                $imgdata = ['filter'=>['post_type_id'=>Config::get('constants.POST_TYPE.PRODUCT_CMB'), 'relative_post_id'=>$postdata['combination_id'], 'img_size'=>'product-img-md']];
            }
			
            $file_path = $this->imageObj->get_imgs($postdata['product_id'], $imgdata, false, true);	
        }		
        if ($file_path)
        {
            $op['contents'] = $file_path;
            $this->statusCode = 200;
            if (isset($postdata['product_id']))
            {
                $this->statusCode = 200;
            }
            else
            {
                $op['status'] = 'image_details';
                $op['url'] = is_array($file_path) ? $file_path[0]->image_path : $file_path->image_path;
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function priceDeductions ()
    {
        $postdata = Input::all();
        $postdata['supplier_id'] = $this->supplier_id;
        $postdata['country_id'] = $this->country_id;
        $postdata['currency_id'] = $this->currency_id;
        if (!empty($postdata))
        {
            $deductions = $this->productObj->getDeductions($postdata);
            if (!empty($deductions))
            {
                $this->statusCode = 200;
                $op['deductions'] = $deductions;
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function productPropertiesValuesChecked ()
    {
        $op = ['properties'=>[],
            'filterables'=>[],
            'values'=>[]];
        if (Input::has('product_id'))
        {
            $this->statusCode = 200;
            $op = $this->productObj->getProductPropertiesValuesChecked(Input::get('product_id'));
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function productPropertiesForChecktree ()
    {
        $parent_property_id = Input::has('parent_property_id') ? Input::get('parent_property_id') : NULL;		
        $category_id = Input::has('category_id') ? Input::get('category_id') : NULL;
        if ($category_id)
        {
            $this->statusCode = 200;
            $properties = $this->productObj->getProductProperiesForChecktree($category_id, $parent_property_id);
        }
        return Response::json(!empty($properties) ? $properties : array(), $this->statusCode, $this->headers, $this->options);
    }

    public function saveProductCountry ()
    {
        $postdata = Input::all();
        $validator = Validator::make($postdata, ['product.country_id'=>'required'], ['product.country_id.required'=>'Please Select Country']);
        if (!$validator->fails())
        {
            $postdata['supplier_id'] = $this->supplier_id;
            $postdata['account_id'] = $this->account_id;
            $result = $this->productObj->saveProductCountry($postdata);
            if ($result)
            {
                $this->statusCode = 200;
                $op['msg'] = 'Country Added.';
            }
            else
            {
                $this->statusCode = 208;
                $op['msg'] = Lang::get('general.there_is_no_changes');
            }
        }
        else
        {
            $op['error'] = $validator->messages(true);
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function deleteProductCountry ()
    {
        $postdata = Input::all();
        if (isset($postdata['product_id']) && !empty($postdata['product_id']) && isset($postdata['country_id']) && !empty($postdata['country_id']))
        {
            $postdata['account_id'] = $this->account_id;
            $result = $this->productObj->deleteProductCountry($postdata);
            if ($result)
            {
                $this->statusCode = 200;
                $op['countries'] = $this->productObj->getProductCountries(['product_id'=>$postdata['product_id']]);
                $op['msg'] = 'Country Deleted.';
            }
            else
            {
                $this->statusCode = 208;
                $op['msg'] = Lang::get('general.there_is_no_changes');
            }
        }
        else
        {
            $op['msg'] = 'Parameters Missing';
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function saveProduct ()
    {
        $postdata = Input::all();
        $op = [];
        $this->statusCode = 422;
        $postdata['account_id'] = $this->account_id;
        $postdata['supplier_id'] = $this->supplier_id;
        $postdata['currency_id'] = $this->currency_id;
        if (!empty($postdata['catlist']))
        {
            $postdata['assoc_category_id'] = implode(',', $postdata['catlist']);
        }
        $result = $this->productObj->saveProduct($postdata);
		//print_r($result);exit;
        if (!empty($result))
        {   
			if ($result['data'] == 1)
			{
				$this->statusCode = 200;
				$op['product_code'] = $result['product_code'];
				$op['status'] = $result['status'];
				$op['msg'] = Lang::get('general.property_updated_successfully');
			}
			else if ($result['data'] == 3)
			{
				$this->statusCode = 208;
				$op['msg'] = Lang::get('general.sku_already_exist');
            }
            else if ($result == 4)
            {
                $this->statusCode = 208;
                $op['msg'] = Lang::get('general.ean_barcode_already_exist');
            }
            else if ($result == 5)
            {
                $this->statusCode = 208;
                $op['msg'] = Lang::get('general.upc_barcode_already_exist');
            }
            else
            {
                $this->statusCode = 200;
                $op['product_code'] = $result['product_code'];
                $op['status'] = $result['status'];
                $op['msg'] = Lang::get('general.product.created');
            }
        }
        else
        {
            $this->statusCode = 208;
            $op['msg'] = Lang::get('general.there_is_no_changes');
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function updateStock ()
    {
        $op = [];
        $postdata = Input::all();
        $postdata['account_id'] = $this->account_id;
        $res = $this->productObj->updateStock($postdata);
        if (!empty($res))
        {
            $this->statusCode = 200;
            $op['msg'] = Lang::get('general.product.stock_updated_successfully');
            $op['stock'] = $this->productObj->get_current_stocks(array('supplier_product_id'=>$postdata['supplier_product_id']));
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function update_sortorder ()
    {
        $op = array();
        $op['msg'] = Lang::get('general.there_is_no_changes');
        $post = Input::all();
        if (!empty($post))
        {
            if ($this->productObj->update_sortorder($post))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.tax_updated_successfully');
            }
            else
            {
                $this->statusCode = 208;
                $op['msg'] = Lang::get('general.there_is_no_changes');
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function save_products_combinations ()
    {
        $data = Input::all();
        //echo '<pre>';print_r($data);exit;
        $validator = Validator::make($data, Config::get('validations.PRODUCT_COMBINATION.RULES'), Config::get('validations.PRODUCT_COMBINATION.MESSAGES'));
        //echo '<pre>';print_r($validator->messages(true));exit;
        $this->statusCode = 422;
        $op['error'] = $validator->messages(true);
        if (!$validator->fails())
        {
            $op['msg'] = '';
            $data['account_id'] = $this->account_id;
            $data['supplier_id'] = $this->supplier_id;
            if (!empty($data))
            {
                if ($op = $this->productObj->save_products_combinations($data))
                {
                    if (!empty($data['product_cmb']['product_id']))
                    {
                        $res = $this->is_combination($data['product_cmb']['product_id']);
                    }
                    $this->statusCode = 200;
                }
                else
                {
                    $op['msg'] = Lang::get('product_combinations.already_exists');
                    $this->statusCode = 422;
                }
            }
        }
        else
        {
            $result['error'] = $validator->messages(true);
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function is_combination ($data = array())
    {
        $product_combination_exists_check = $this->productObj->get_combination_product_by_id($data);
        return $product_combination_exists_check;
    }

    public function combinationsList ()
    {
        $data = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        $combinations = $this->productObj->productCombinationsList($data);
        $this->statusCode = 200;
        return Response::json($combinations, $this->statusCode, $this->headers, $this->options);
    }

    public function properties_list_for_com ()
    {
        $data = [];
        $data['product_id'] = Input::get('product_id');
        $properties = $this->productObj->properties_list_for_com($data);
        return (!empty($properties)) ? $properties : [];
    }

    public function values_list_for_com ()
    {
        $data = [];
        $data['product_id'] = Input::get('product_id');
        $data['property_id'] = Input::get('property_id');
        $values = $this->productObj->values_list_for_com($data);
        return (!empty($values)) ? $values : [];
    }

    public function get_combination_details ()
    {
        $data = [];
        $op = [];
        $data = Input::all();
        $res = $this->productObj->combination_list_by_id($data);
        if ($res)
        {
            $this->statusCode = 200;
            $op['data'] = $res;
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function combination_list_for_com ()
    {
        $postdata = Input::all();
        if (!empty($postdata))
        {
            $this->statusCode = 200;
            $result = $this->productObj->combination_list_for_com($postdata);
        }
        return Response::json($result, $this->statusCode, $this->headers, $this->options);
    }

    public function add_image ()
    {
        $op = array();
        $data = array();
        $post = Input::all();		
        if (Input::file('file'))
        {	            
            $ic = new ImageLib();
            $file_path = $ic->upload_img($post['product_id']);						
			//$file_path=$this->upload_file(Input::file('myfile'),Config::get('path.PRODUCT_IMG_UPLOAD_PATH'));
            if (!empty($file_path))
            {
                $this->statusCode = 200;
                $op['img_path'] = $file_path;
            }
        }
        else if (!empty($post))
        {
            $data = $post;
            $data['created_on'] = date('Y-m-d');
            $data['updated_by'] = $this->account_id;
            $position = count($this->productObj->product_img_details($post['product_img']));
            $data['position'] = $position;
            $res = $this->productObj->add_product_image($data);
            if ($res)
            {
                $op['file'] = $res;
                $op['msg'] = Lang::get('general.image_successfully_added');
                $this->statusCode = 200;
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function save_combination_images ()
    {
        $postdata = Input::all();
        $op['msg'] = Lang::get('general.something_went_wrong');
        $res = $this->productObj->save_combination_images($postdata);
        if (!empty($res))
        {
            if ($res == 2)
            {
                $this->statusCode = 208;
                $op['msg'] = Lang::get('general.image_already_exist');
            }
            if ($res == 1)
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.image_successfully_added');
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function image_remove ()
    {
        $op = array();
        $postdata = Input::all();
        if ($this->productObj->image_remove($postdata))
        {
            $data['product_id'] = $postdata['product_id'];
            if (isset($postdata['combination_id']) && !empty($postdata['combination_id']))
            {
                $data['combination_id'] = $postdata['combination_id'];
            }
            $this->statusCode = 200;
            $op['contents'] = $this->productObj->product_img_details($data);
            $op['msg'] = Lang::get('general.image.removed_successfully');
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function default_pro_img ()
    {
        $postdata = Input::all();
        if (!empty($postdata))
        {
            if ($this->productObj->default_pro_img($postdata))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.updated_successfully');
            }
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function delete_selected_image ()
    {
        $postdata = Input::all();
        $op['msg'] = Lang::get('general.something_went_wrong');
        $res = $this->productObj->delete_selected_image($postdata);
        if (!empty($res))
        {
            $data['product_id'] = $postdata['product_id'];
            $this->statusCode = 200;
            $op['contents'] = $this->productObj->product_img_details($data);
            $op['msg'] = Lang::get('general.image_deleted_successfully');
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function save_properties ()
    {
        $postdata = Input::all();
        $postdata = array_filter($postdata);
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata))
        {
            $result = $this->productObj->save_property($postdata);
            if (!empty($result))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.updated_successfully');
            }
            else
            {
                $this->statusCode = 208;
                $op['msg'] = Lang::get('general.there_is_no_changes');
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

}
