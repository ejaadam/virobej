<?php
namespace App\Http\Controllers\Api\Seller;
use App\Http\Controllers\Api\APIBase;
use App\Models\Api\Seller\APISupplierCatalog;
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


class APISupplierCatalogController extends APIBase
{

    public function __construct ()
    {
        parent::__construct();
        $this->catalogObj = new APISupplierCatalog();
    }

    public function productCategories ()
    {
        $data = $filter = $ajaxdata = array();
        $postdata = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        $filter['search_term'] = isset($postdata['search_term']) && !empty($postdata['search_term']) ? $postdata['search_term'] : '';
        $ajaxdata['draw'] = $postdata['draw'];
        $ajaxdata['data'] = array();
        $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->catalogObj->getProductCategories($data, true);
        if (!empty($ajaxdata['recordsTotal']))
        {
            $data = array_merge($data, $filter);
            $ajaxdata['recordsFiltered'] = $this->catalogObj->getProductCategories($data, true);
            if (!empty($ajaxdata['recordsFiltered']))
            {
                $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
                if (isset($postdata['order'][0]['dir']))
                {
                    $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                    $data['order'] = $postdata['order'][0]['dir'];
                }
                $data['search_term'] = isset($postdata['search_term']) && !empty($postdata['search_term']) ? $postdata['search_term'] : '';
                $ajaxdata['data'] = $this->catalogObj->getProductCategories($data);
            }
        }
        $this->statusCode = 200;
        return Response::json($ajaxdata, $this->statusCode, $this->headers, $this->options);
    }

    public function addCategories ()
    {
        $data = array();
        $data['category_id'] = Input::get('category_id');
        $op = array();
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($data['category_id']))
        {
            $data['supplier_id'] = $this->supplier_id;
            $data['updated_by'] = $this->account_id;
            $res = $this->catalogObj->add_categories($data);
            if ($res)
            {
                $this->statusCode = $res['statusCode'];
                $op['msg'] = $res['msg'];
            }
        }
        else
        {
            $this->statusCode = 422;
            $op['msg'] = Lang::get('general.parameteres_missing');
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function changeCategoryStaus ()
    {
        $postdata = Input::all();
        $op = [];
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata))
        {
            $postdata['account_id'] = $this->account_id;
            $postdata['supplier_id'] = $this->supplier_id;
            $result = $this->catalogObj->changeCategoryStaus($postdata);
            if (!empty($result))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.actions.status.'.$postdata['status'], ['label'=>Lang::get('general.fields.category')]);
            }
        }
        else
        {
            $this->statusCode = 422;
            $op['msg'] = Lang::get('general.parameteres_missing');
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function deleteCategory ()
    {
        $postdata = Input::all();
        $this->response = [];
        $this->response['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata['id']))
        {
            //$postdata['id'] = $id;
            $postdata['account_id'] = $this->account_id;
            $postdata['supplier_id'] = $this->supplier_id;
            $result = $this->catalogObj->deleteCategory($postdata);
            if (!empty($result))
            {
                $this->statusCode = 200;
                $this->response['msg'] = Lang::get('general.actions.deleted', ['label'=>Lang::get('general.fields.category')]);
            }
        }
        else
        {
            $this->statusCode = 422;
            $this->response['msg'] = Lang::get('general.parameteres_missing');
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function availableCategory ()
    {
        $data = array();
        $op = array();
        $data['parent_category_id'] = Input::has('parent_category_id') ? Input::get('parent_category_id') : '';
        $data['search_term'] = Input::has('search_term') ? Input::get('search_term') : '';
        if (Input::has('withUrl'))
        {
            $data['withUrl'] = true;
        }
        $data['supplier_id'] = Input::has('supplier_id') ? Input::get('supplier_id') : '';		
        if ($data['supplier_id'])
        {
            $categories = $this->commonObj->get_categories_list($data);
            if (!empty($categories))
            {
                $this->statusCode = 200;
                $op = $categories;
            }
            else
            {
                $this->statusCode = 208;
                $op['msg'] = Lang::get('general.actions.not_avaliable', ['label'=>Lang::get('general.fields.category')]);
            }
        }
        else
        {
            $this->statusCode = 422;
            $op['msg'] = Lang::get('general.parameteres_missing');
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function brandList ()
    {
        $data = $ajaxdata = $filter = array();
        $postdata = Input::all();
        $data['supplier_id'] = isset($this->supplier_id) && !empty($this->supplier_id) ? $this->supplier_id : $postdata['supplier_id'];
        $filter['search_term'] = isset($postdata['search_term']) && !empty($postdata['search_term']) ? $postdata['search_term'] : NULL;
        $ajaxdata['draw'] = $postdata['draw'];
        $ajaxdata['data'] = array();
        $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->catalogObj->getBrandsList($data, true);
        if (!empty($ajaxdata['recordsTotal']))
        {
            $filter = array_filter($filter);
            if (!empty($filter))
            {
                $data = array_merge($data, $filter);
                $ajaxdata['recordsFiltered'] = $this->catalogObj->getBrandsList($data, true);
            }
            $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
            $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
            if (isset($postdata['order'][0]['dir']))
            {
                $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                $data['order'] = $postdata['order'][0]['dir'];
            }
            $ajaxdata['data'] = $this->catalogObj->getBrandsList($data);
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['supplier_id'] = isset($this->supplier_id) && !empty($this->supplier_id) ? $this->supplier_id : $postdata['supplier_id'];
        }
        $this->statusCode = 200;
        return Response::json($ajaxdata, $this->statusCode, $this->headers, $this->options);
    }

    public function newBrand ()
    {
        $postdata = Input::all();
        $op = array();
        //$validator = Validator::make($postdata, Config::get('validations.CREATE_BRAND.RULES'), Config::get('validations.CREATE_BRAND.MESSAGES'));
        $validator = Validator::make($postdata, ['brand_name'=>'required|min:2|max:100'], ['brand_name.required'=>'Brand Name Required', 'brand_name.min'=>'Brand Name must be atleast :min characters', 'brand_name.max'=>'Brand Name must be lesser that :max characters']);
        if (!$validator->fails())
        {
            $postdata['supplier_id'] = $this->supplier_id;
            $postdata['account_id'] = $this->account_id;
            if ($this->catalogObj->addBrand($postdata))
            {
                $this->statusCode = 200;
                $op['msg'] = 'Brand Successfully Added.';
            }
            else
            {
                $this->statusCode = 208;
                $op['msg'] = 'Brand Already Exists.';
            }
        }
        else
        {
            $op['error'] = $validator->messages(true);
            $this->statusCode = 208;
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function updateBrand ()
    {
        $postdata = Input::all();

        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata))
        {
            $postdata['supplier_id'] = $this->supplier_id;
            $result = $this->catalogObj->updateBrand($postdata);
            if (!empty($result))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('product_controller.category_updated');
            }
            else
            {
                $this->statusCode = 208;
                $op['msg'] = Lang::get('general.there_is_no_changes');
            }
        }
        return Response::json($op, $this->statusCode, $this->headers, $this->options);
    }

    public function changeBrandStatus ()
    {
        $this->response = [];
        $postdata = Input::all();
        $this->response['msg'] = Lang::get('general.something_went_wrong');
        if (isset($postdata['id']) && !empty($postdata['id']))
        {
            $postdata['account_id'] = $this->account_id;
            $postdata['supplier_id'] = $this->supplier_id;
            if ($this->catalogObj->changeBrandStatus($postdata))
            {
                $this->statusCode = 200;
                $this->response['msg'] = Lang::get('general.brand.status.'.$postdata['status']);
            }
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function checkBrand ($data = '')
    {
        $data[] = NULL;
        $postdata = Input::all();
        if (!empty($postdata))
        {
            $res = $this->catalogObj->checkBrandName($postdata);
            if (!empty($res))
            {
                $this->statusCode = 200;
                $data = $res;
            }
        }
        return Response::json($data, $this->statusCode, $this->headers, $this->options);
    }

    public function deleteBrand ()
    {

        $postdata = Input::all();
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (isset($postdata['id']) && !empty($postdata['id']))
        {
            $postdata['account_id'] = $this->account_id;
            $postdata['supplier_id'] = $this->supplier_id;
            if ($this->catalogObj->deleteBrand($postdata))
            {
                $this->statusCode = 200;
                $op['msg'] = Lang::get('general.brand.deleted');
            }
        }
        return Response::json($this->response, $this->statusCode, $this->headers, $this->options);
    }

    public function productStockList ()
    {
        $data = $filter = array();
        $postdata = Input::all();
        $data['supplier_id'] = $this->supplier_id;
        $filter['search_term'] = isset($postdata['search_term']) ? $postdata['search_term'] : NULL;
        $filter['status'] = isset($postdata['status']) ? $postdata['status'] : NULL;
        $filter['from'] = isset($postdata['from']) ? $postdata['from'] : NULL;
        $filter['to'] = isset($postdata['to']) ? $postdata['to'] : NULL;
        $filter['category_id'] = isset($postdata['category_id']) ? $postdata['category_id'] : NULL;
        $filter['brand_id'] = isset($postdata['brand_id']) ? $postdata['brand_id'] : NULL;
        $ajaxdata['data'] = array();
        $ajaxdata['draw'] = $postdata['draw'];
        $ajaxdata['url'] = URL::to('/');
        if ($postdata['draw'] == 1 || isset($postdata['withFilters']))
        {
            $ajaxdata['filters']['categories'] = $this->catalogObj->getProductStockList($data, false, true) + [0=>'All'];
            ksort($ajaxdata['filters']['categories']);
            $ajaxdata['filters']['brands'] = $this->catalogObj->getProductStockList($data, false, false, true) + [0=>'All'];
            ksort($ajaxdata['filters']['brands']);
        }
        $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->catalogObj->getProductStockList($data, true);
        if (!empty($ajaxdata['recordsTotal']))
        {
            $filter = array_filter($filter);
            if (!empty($filter))
            {
                $data = array_merge($data, $filter);
                $ajaxdata['recordsFiltered'] = $this->catalogObj->getProductStockList($data, true);
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
                $ajaxdata['data'] = $this->catalogObj->getProductStockList($data);
            }
        }
        $this->statusCode = 200;
        return Response::json($ajaxdata, $this->statusCode, $this->headers, $this->options);
    }

}
