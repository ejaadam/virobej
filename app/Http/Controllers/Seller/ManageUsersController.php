<?php

namespace App\Http\Controllers\Seller;
use App\Http\Controllers\SupplierBaseController;
use App\Http\Controllers\MyImage;
use App\Models\Seller\Account;
use App\Models\Seller\ManageUsers;
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
use CommonLib;

class ManageUsersController extends SupplierBaseController
{
	public function __construct ()
	{
		parent::__construct();
		$this->imgObj = new MyImage();        
		$this->accObj = new Account();  
		/* $this->userObj= new ManageUsers(); */
		$this->commonObj = new Commonsettings();	
		$this->manageuserObj = new ManageUsers();	
	}   
	
	public function add_user ()
	{
		$data 	 = [];
		$country = $this->commonObj->get_countries($this->country_id);
		$data['access_level'] = $this->commonObj->get_access_leveles(Config('constants.ACCOUNT_TYPE.SELLER'));
		if(!empty($country[0])){
			$data['flag'] 			= asset('assets/flags/'.$country[0]->iso2.'.png');
			$data['country_id'] 	= $country[0]->country_id;
			$data['country'] 		= $country[0]->country;
			$data['phonecode'] 		= $country[0]->phonecode;
			$data['mobile_validation'] = str_replace('$/','',(str_replace('/^','',$country[0]->mobile_validation)));
		}
		return View('seller.users.new_user',$data);
		
	}

	public function manage_user_list(){
		
		$op = $data = $filter = array();
		if (\Request::ajax())
        {
			 $data['supplier_id'] = $this->userSess->supplier_id;
			/*$data['account_id'] = $this->userSess->account_id;
			$data['currency_id'] = $this->userSess->currency_id;
			$data['account_type_id'] = $this->userSess->account_type_id; */
			
			$post = $this->request->all();
			$filter['search_term'] = isset($post['search_term']) ? $post['search_term'] : null;
			$filter['filterTerms'] = $this->request->has('filterTerms') ? $this->request->get('filterTerms') : '';
			$ajaxdata['draw'] = isset($post['draw']) ? $post['draw'] : 1;
			$data = array_merge($data, $filter);
			$ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->manageuserObj->get_user_list($data, true);
			$ajaxdata['data'] = [];
			if ($ajaxdata['recordsTotal'])
			{
				if (!empty($filter))
				{
					$data = array_merge($data, $filter);
					$ajaxdata['recordsFiltered'] = $this->manageuserObj->get_user_list($data, true);
				}
				if ($ajaxdata['recordsFiltered'])
				{
					$data['start'] = (isset($post['start']) && !empty($post['start'])) ? $post['start'] : 0;
					$data['length'] = (isset($post['length']) && !empty($post['length'])) ? $post['length'] : $this->config->get('constants.DATA_TABLE_RECORDS');
					if ($post['order'])
					{
						$data['orderby'] = isset($post['columns']) ? $post['columns'][$post['order'][0]['column']]['name'] : (isset($post['orderby']) ? $post['orderby'] : null);
						$data['order'] = isset($post['order'][0]['dir']) ? $post['order'][0]['dir'] : (isset($post['order']) ? $post['order'] : null);
					}
					$ajaxdata['data'] = $this->manageuserObj->get_user_list($data);
					
				}
			}
			$this->statusCode = 200;
			return $this->response->json($ajaxdata, $this->statusCode, $this->headers, $this->options);	
		} 
		else 
		{
			$data 	 = [];
			$country = $this->commonObj->get_countries($this->country_id);
			$data['access_level'] = $this->commonObj->get_access_leveles(Config('constants.ACCOUNT_TYPE.SELLER'));
			if(!empty($country[0])){
				$data['flag'] 			= asset('assets/flags/'.$country[0]->iso2.'.png');
				$data['country_id'] 	= $country[0]->country_id;
				$data['country'] 		= $country[0]->country;
				$data['phonecode'] 		= $country[0]->phonecode;
				$data['mobile_validation'] = str_replace('$/','',(str_replace('/^','',$country[0]->mobile_validation)));
			}
			return View('seller.users.manage_user_list', $data);
		}
		
	}

	public function login_block($account_id,$status){
	
			$op = $postdata = array();
			$postdata['account_id'] = $account_id;
			$postdata['status'] =config('constants.ACCOUNT_USER.'.strtoupper($status));
		   $data=$this->manageuserObj->user_block_login($postdata);
		   
		   print_R( $data); die;
	}
	
    public function reset_password() {
        $data['status'] = '';
        $data['msg'] = '';
        $postdata = $this->request->all();
	    if (!empty($postdata)){
	    $rdata = $this->manageuserObj->update_password($postdata);
		$op['msg']=$rdata['msg'];
		$this->statusCode = $rdata['status'];
		return Response::json($op, $this->statusCode, $this->headers, $this->options);
        }
	}

	public function save_user()
	{
		$postdata = $this->request->all();
		$postdata['supplier_id'] 	= $this->supplier_id;
		$postdata['country_id'] 	= $this->country_id;
		$postdata['currency_id'] 	= $this->currency_id;
		$postdata['language_id'] 	= 1;
		$postdata['account_type']	= $this->config->get('constants.ACCOUNT_TYPE.CASHIER');
		$res = $this->manageuserObj->save_user($postdata);
	
		$this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
		if(!empty($res)){
			$op['msg'] = 'User Created Successfully';
			$this->statusCode = $this->config->get('httperr.SUCCESS');
		}
		return Response::json($op, Config('httperr.SUCCESS'), $this->headers, $this->options);	
	}
	
	public function edit_user_acc($id){
		
		$postdata['supplier_id'] = $this->supplier_id;
		$postdata['account_id'] 		 = $id;
		$result = $this->manageuserObj->get_user_account_details($postdata);
		if(!empty($result)){
			$op['data'] = $result;
			/* $op['url']=route('seller.manage_users.update_user_details'); */
			
			$op['msg'] = '';
			$op['status'] = $this->statusCode = Config('httperr.SUCCESS');	
		}else{
			$op['msg'] = 'User Not Found';
			$op['status'] = $this->statusCode = Config('httperr.UN_PROCESSABLE');
		}
		
		return Response::json($op,$this->statusCode,$this->headers,$this->options);
	}
	
	public function get_stores ($id)
    {

        $op = $data = [];
        $data 				 = $this->request->all();
        $data['supplier_id'] = $this->userSess->supplier_id;
        $data['account_id']  = $id;
        $res 				 = $this->manageuserObj->get_supplierStores($data);
		if ($res)
        {
            $op['stores'] = $res;
            $op['save_url'] = route('seller.manage_users.save_allocation', ['id'=>$id]);
            $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            $op['stores'] = '';
            $this->statusCode = $this->config->get('httperr.PARAMS_MISSING');
            $op['msg'] = trans('general.not_found', ['which'=>'Stores']);
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function save_allocation ($id)
    {
        $op 		= [];
        $data 		= $this->request->all();
        $data['id'] = $id;
        $data['supplier_id'] 	= $this->userSess->supplier_id;
        $data['account_id'] 	= $this->userSess->account_id;
        if ($this->manageuserObj->saveAdminStores($data, $id))
        {
			$data['account_id'] = $id;
            $op['stores'] = $this->manageuserObj->get_supplierStores($data);
            $op['msg'] = trans('general.updated');
            $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            $op['msg'] = trans('general.not_update');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
  public function updatManageUserStatus ($status, $id)
    {
     $op = $data = [];
        $data['id'] = $id;
        $data['status'] = $this->config->get('constants.MANAGE_USER.STATUS.'.strtoupper($status));
        $data['account_id'] = $this->userSess->account_id;
        if ($this->manageuserObj->updateManageUserStatus($data))
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            $op['msg'] = trans('general.updateds', ['which'=>'User', 'what'=>trans('general.user.status.'.$data['status'])]);
        }
        else
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            $op['msg'] = trans('general.already', ['which'=>'User', 'what'=>trans('general.user.status.'.$data['status'])]);
        }
	
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
}