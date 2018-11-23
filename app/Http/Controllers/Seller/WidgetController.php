<?php

namespace App\Http\Controllers\Seller;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\MyImage;
use App\Models\Seller\Account;
use App\Models\Seller\Widget;
use App\Models\Seller\Report;
use App\Models\Memberauth;
use App\Models\Commonsettings;
use App\Helpers\CommonNotifSettings;
use Config;
use Lang;
use Redirect;
use Session;
use Input;
use Response;
use File;
use Request;

class WidgetController extends BaseController    
{ 
    public function __construct ()
    {
        parent::__construct();
		/* $this->imgObj = new MyImage();        */ 
        $this->accObj = new Account();  
		$this->widgetObj = new Widget();
		$this->report = new Report();
		$this->commonObj = new Commonsettings();	
    }   
	
	public function dashboard ()
    {   	 
		$data = [];	
		if (Request::ajax()) {
		    $op = [];
			$postdata = $this->request->all(); 	
			if (isset($postdata['widgets']) && is_array($postdata['widgets']) && count($postdata['widgets']) > 0)
			{	
				foreach ($postdata['widgets'] as $widget)
				{
					$op[$widget] = $this->$widget();
				}
				$this->statusCode = $this->config->get('httperr.SUCCESS');
				return Response::json($op, $this->statusCode, $this->headers, $this->options);
			}
		}else {	
		    return View('seller.dashboard', $data);		
		}
    }	
	
	/* Total Sales & Sales Ratio */
    public function sales ()
    {  
        $data = $op = [];		
        $data['supplier_id'] = $this->userSess->supplier_id;
		$data['account_id'] = $this->userSess->account_id;		
        $data['currency_id'] = $this->userSess->currency_id;
		$data['currency_symbol'] = '';
		$currency = $this->report->get_currency($this->userSess->currency_id);
		if (!empty($currency))
		{
			$data['currency_symbol'] = $currency->currency_symbol;
		}
        if (!empty($data['supplier_id']))
        {
	        $op['other_months'] = [];
	        $months = ['current_month'=>0,'three_monthago'=>3,'six_monthago'=>6,'twelve_monthago'=>12];
            $op['total_sales'] = $this->widgetObj->sales_counts($data,[],true);			
            $op['today_sales'] = $this->widgetObj->sales_counts($data,['filter'=>'day','days'=>0],true);			
            $op['sales_ratio'] = $this->widgetObj->sales_counts($data,['filter'=>'day','days'=>30],false);
			foreach($months as $k=>$v)
			{			    
			    $op['other_months'][$k] = $this->widgetObj->sales_counts($data,['filter'=>'month','months'=>$v],true);
			}			
            $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            $op['msg'] = trans('general.no_datas_found');
            $op['status'] = 'error';
            $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
        }
        return $op;
    }
	
	/* Total Order  & Order Ratio */	
	public function orders ()
    {  
        $data = $op = [];
        $data['supplier_id'] = $this->userSess->supplier_id;
        if (!empty($data['supplier_id']))
        {
            $op['total_order'] = $this->widgetObj->order_counts($data,[],true);			
            $op['order_ratio'] = $this->widgetObj->order_counts($data,['filter'=>'day','days'=>30],false);			
            $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            $op['msg'] = trans('general.no_datas_found');
            $op['status'] = 'error';
            $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
        }
        return $op;
    }
	
	/* Total Visitors & Visitors Ratio */
    public function visitors ()
    {  
        $data = $op = [];	  
        $data['supplier_id'] = $this->userSess->supplier_id;       
        if (!empty($data['supplier_id']))
        {
            $op['total_visitors'] = $this->widgetObj->visitors_counts($data,[],true);				
            $op['visitors_ratio'] = $this->widgetObj->visitors_counts($data,['filter'=>'day','days'=>30],false);				
            $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            $op['msg'] = trans('general.no_datas_found');
            $op['status'] = 'error';
            $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
        }
        return $op;
    }
	
	/* Account Balance */
    public function acc_balance ()
    {  
        $data = $op = [];		
        $data['supplier_id'] = $this->userSess->supplier_id;
		$data['account_id'] = $this->userSess->account_id;		
        $data['currency_id'] = $this->userSess->currency_id;		
        if (!empty($data['account_id']))
        {	        				
			$op['data'] = $this->report->wallet_balance($data);	
            $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            $op['msg'] = trans('general.no_datas_found');
            $op['status'] = 'error';
            $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
        }
        return $op;
    }
   	
	/* Recent Transaction */
    public function transactions ()
    {
        $data = $op = [];		
        $op['draw'] = $this->request->draw;
        $data['supplier_id'] = $this->userSess->supplier_id;
        $data['account_type_id'] = $this->userSess->account_type_id;
        $data['start'] = 0;
        $data['length'] = 5;
        if (!empty($data['supplier_id']))
        {   
            //$op['data'] = $this->report->getTransactionList($data);		
            $op['data'] = $this->report->transactions($data);				
            $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            $op['msg'] = trans('general.no_datas_found');
            $op['status'] = 'error';
            $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
        }   
		return $op;
    }	
	
	/* Ratings */

    /* public function mrratings ($http = false)
    {
        $data = [];
        $data['mrid'] = $this->userSess->mrid;
        if (!empty($data['mrid']))
        {
            $op['ratings'] = $this->widgetObj->ratings($data);
            $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            $op['msg'] = trans('general.no_datas_found');
            $op['status'] = 'error';
            $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
        }
        if ($http)
        {
            return $this->response->json($op, $this->statusCode, $this->header, $this->options);
        }
        else
        {
            return $op;
        }
    } */
	
	/* public function widgets ()
    {
        $post = $this->request->all();
        if (isset($post['widgets']) && is_array($post['widgets']) && count($post['widgets']) > 0)
        {
            foreach ($post['widgets'] as $widget)
            {
                $op[$widget] = $this->$widget();
            }
            return $this->response->json($op, $this->statusCode, $this->header, $this->options);
        }
    } */  

   
	public function Bank_Details ()
    {
		$data = [];	
		if (Request::ajax()) {
			$op = [];
			$postdata = Input::all();       
			$postdata['account_id'] = $this->userSess->account_id;    
			$postdata['supplier_id'] = $this->userSess->supplier_id;  
			$postdata['account_type'] = $this->userSess->account_type_id; 		
			$res = $this->accObj->UpdateBankDetails($postdata);					
			if ($res) 
			{
				$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
                $op['msg'] = 'Account Updated Successfully';
				return Response::json($op, $this->statusCode, $this->headers, $this->options);
			}			
		} else {		
			$data['account_id'] = $this->account_id;
			$data['acc_type_id'] = $this->account_type_id;
			$data['post_type'] = Config::get('constants.ADDRESS_POST_TYPE.SELLER');		
			$data['supplier_id'] = $this->userSess->supplier_id;  
			$data['fields'] = CommonNotifSettings::getHTMLValidation('seller.account-settings.bank-details');		
			$data['bank_account_details'] = $this->accObj->GetBankAccountDetails($data);				
			return View('seller.account_settings.bank_details', $data);
		}
    }
	
	public function acc_manager(){
		
		$res = $this->widgetObj->get_supplier_manager(['supplier_id'=> $this->userSess->supplier_id]);
		if(!empty($res)){
			return $res;
		}
		return false;
	}
	
	
	/* public function shipping_information ()
    {
		if (Request::ajax()) {
			$postdata = Input::all();    
			print_r($postdata);exit;
		} else {
			return View('seller.account_settings.shipping_information');
		}
    }

	public function Bank_Details ()
    {
		$data = [];	
		if (Request::ajax()) {
			$op = [];
			$postdata = Input::all();       
			$postdata['account_id'] = $this->userSess->account_id;    
			$postdata['supplier_id'] = $this->userSess->supplier_id;  
			$postdata['account_type'] = $this->userSess->account_type_id; 		
			$res = $this->accObj->UpdateBankDetails($postdata);					
			if ($res) 
			{
				$this->statusCode = $op['status'] = $this->config->get('httperr.SUCCESS');
                $op['msg'] = 'Account Updated Successfully';
				return Response::json($op, $this->statusCode, $this->headers, $this->options);
			}			
		} else {		
			$data['account_id'] = $this->account_id;
			$data['acc_type_id'] = $this->account_type_id;
			$data['post_type'] = Config::get('constants.ADDRESS_POST_TYPE.SELLER');		
			$data['supplier_id'] = $this->userSess->supplier_id;  
			$data['fields'] = CommonNotifSettings::getHTMLValidation('seller.account-settings.bank-details');		
			$data['bank_account_details'] = $this->accObj->GetBankAccountDetails($data);				
			return View('seller.account_settings.bank_details', $data);
		}
    } */
	
}
