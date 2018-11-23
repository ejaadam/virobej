<?php

namespace App\Http\Controllers\Api\User;
use App\Http\Controllers\Api\User\UserApiBaseController;
use App\Models\Api\User\WalletModel;
use App\Models\Api\User\ProductsModel;
use CommonLib;

class ProductController extends UserApiBaseController
{
    
    public function __construct ()
    {
        parent::__construct();        
		$this->productsObj = new ProductsModel();
    }
	
	public function Product_Categories ($category = null)
    {
        $op = [];
		$wdata['country_id'] = $this->geo->current->country_id;		
        $wdata['category_type'] = $this->config->get('constants.CATEGORY_TYPE.PRODUCT');	
		if (!empty($category))
        {
			$wdata['category_slug'] = $category;			
		} 
        $res = $this->productsObj->ProductCategories($wdata, false, true);		
		if (!empty($res))
		{
			$op['data'] = $res;
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
		}
		else
		{
			$op['data'] = [];
			$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			$op['msg'] = 'Product Categories Not Found';
		}
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function ProductList_By_Category ($category = null)
    {
        $op = [];
		$wdata['country_id'] = $this->geo->browse->country_id;	
		$wdata['currency_id'] = $this->geo->browse->currency_id;  
        $wdata['category_type'] = $this->config->get('constants.CATEGORY_TYPE.PRODUCT');	
		if (!empty($category))
        {
			$wdata['category'] = $category;	
			$op['category'] = $this->productsObj->Get_Category_By_Slug($wdata);
		} 
        $res = $this->productsObj->ProductList_By_Category($wdata, false, true);		
		if (!empty($res))
		{
			$op['data'] = $res;
			
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
		}
		else
		{
			$op['data'] = [];
			$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			$op['msg'] = 'Products Not Found';
		}
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function Product_Details ()
    {
        $op = [];
		$wdata = $this->request->all();
		$wdata['country_id'] = $this->geo->browse->country_id;	
		$wdata['currency_id'] = $this->geo->browse->currency_id;  
        $wdata['category_type'] = $this->config->get('constants.CATEGORY_TYPE.PRODUCT');			 
        $res = $this->productsObj->Product_Details($wdata);		
		//print_r($res);exit; 
		if (!empty($res))
		{
			$op['data'] = $res;
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
		}
		else
		{
			$op['data'] = [];
			$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			$op['msg'] = 'Products Not Found';
		}
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
    
}
