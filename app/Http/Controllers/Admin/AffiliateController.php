<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\BaseController;
use App\Models\Admin\AffModel;
use App\Models\LocationModel;
use TWMailer;
use Response; 
use Request;
use View;
use URL;

class AffiliateController extends BaseController
{
    public function __construct ()
    {
        parent::__construct();
        $this->affObj = new AffModel();
		$this->lcObj = new LocationModel();		
    }    

  public function create_root_user ($user_type = ''){
		$data=[];
		$data['countries']= $this->lcObj->getCountries(['allow_signup'=>true]); 
		return view('admin.affiliate.create_user',$data);
    }
  
    public function save_root_user(){
		$user_id = 0;
        $user_role = '1';
        $user_type = 'root';
        $data = array();
        $response['status'] = "error";
        $response['msg'] = "Invalid Details";
        $data['user_details'] = '';
        $postdata=$this->request->all();
		   /* Mailing to the newly created user */
           /*  $data['fullname'] = $postdata['first_name']." ".$postdata['last_name'];
            $data['username'] = $postdata['uname'];
            $data['pwd'] = $postdata['password'];
            $data['tpin'] = $postdata['tpin'];
            $email_data = array(
                'email'=>$postdata['email']);
                $emailfilename = 'emails.franchisee_user';
                $email_sub = 'Creating your '.Config::get('constants.DOMAIN_NAME').' Franchisee Program login';
	    	$mstatus = TWMailer::send(array(
				'to'=>$postdata['email'],
				'subject'=> $email_sub
				'view'=>'emails.checkmail',
				'data'=>$data,
				'from'=>$this->siteConfig->noreplay_emailid,
				'fromname'=>$this->siteConfig->domain), $this->siteConfig);  */
       return $result = $this->affObj->save_user($postdata);
	}
   public function checkUnameAvaliable ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $op['msg'] = 'Invalid Username';
        $postdata =  $this->request->all();
        if (isset($postdata['uname']))
        {
            $result = $this->affObj->check_user($postdata['uname']);
            if (!empty($result))
            {
                $op['status'] = 'ERR';
                $op['msg'] = 'Username Already Exist';
            }
            else
            {
                $op['status'] = 'OK';
                $op['msg'] = 'Username Avaliable';
            }
        }
        return Response::json($op);
    }
 public function checkEmailAvaliable ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $op['msg'] = 'Invalid Email';
        $postdata =  $this->request->all();
        if (isset($postdata['email']))
        {
            $result = $this->affObj->check_email($postdata['email']);
         if (!empty($result)){
                $op['status'] = 'ERR';
                $op['msg'] = 'Email Already Exist';
            }
         else {
				 $op['status'] = 'OK';
                $op['msg'] = 'Email Avaliable';
            }
        }
        return Response::json($op);
    }
 public function CheckMobileAvailable ()
    {
        $op = array();
        $op['status'] = 'ERR';
        $op['msg'] = 'Invalid Mobile';
        $postdata =  $this->request->all();
        if (isset($postdata['mobile']))
        {
			$result = $this->affObj->check_mobile($postdata['mobile']);
         if (!empty($result)){
                $op['status'] = 'ERR';
                $op['msg'] = 'Mobile Already Exist';
            }
         else {
				 $op['status'] = 'OK';
                $op['msg'] = 'Mobile Avaliable';
            }
        }
        return Response::json($op);
    }
  public function package_purchase_report(){
	
		$data = $filter = array();
		$ewallet_id = '';
		$postdata = $this->request->all();
        $data['wallet_list'] = $this->ewalletsObj->get_wallet_list();
		$data['wallet_id'] = $data['transaction_type'] = $data['from_date'] = $data['to_date'] = '';
		
		if (!empty($postdata))  
        {			
            $filter['from_date'] = !empty($postdata['from_date']) ? $postdata['from_date'] : '';
			$filter['to_date'] = !empty($postdata['to_date']) ? $postdata['to_date'] : '';
			$filter['search_term'] = !empty($postdata['search_term']) ? $postdata['search_term'] : '';
			$filter['wallet_id'] = !empty($postdata['ewallet_id']) ? $postdata['ewallet_id'] : '';
			$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		    $filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : '';
        }
		if ($this->request->ajax())         
        {   
	        
			$data['count'] = true;
			$dat = array_merge($data,$filter); 
		    $ajaxdata['recordsTotal'] = $this->adminreportObj->package_purchase($dat);
			$ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
                $ajaxdata['recordsFiltered'] = $ajaxdata['recordsTotal'];
		
                $dat['start'] = !empty($postdata['start']) ? $postdata['start'] : 0;
				$dat['length'] = !empty($postdata['length']) ? $postdata['length'] : 10;
				 if (isset($postdata['order']))
				{
					$dat['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
					$dat['order'] = $postdata['order'][0]['dir'];
				} 
				unset($dat['count']);                    				
				$ajaxdata['data'] = $this->adminreportObj->package_purchase($dat);
			
            }
             return \Response::json($ajaxdata); 
        
		}
	elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
	    {
			$edata['purchase_details'] = $this->adminreportObj->package_purchase(array_merge($data,$filter));	
            $output = view('admin.account.package_purchase_Excel',$edata);
                    
            $headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=package_purchase_report' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
            return Response::make($output, 200, $headers);
        }
        elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print')
		{
			
			$pdata['purchase_details'] = $this->adminreportObj->package_purchase(array_merge($data,$filter));
            return view('admin.account.package_purchase_print',$pdata);
                           
        }
		else
        {
            return view('admin.account.package_purchase_report',$data);  
		}
}
   public function view_root_user($user_role = 1){
		$data = $filter = array();
		$ewallet_id = '';
		$postdata = $this->request->all();
	  
     if (!empty($postdata))  {			
	      $filters['search_text'] = (isset($postdata['search_text']) && !empty($postdata['search_text'])) ? $postdata['search_text'] : '';
          $filters['start_date'] = (isset($postdata['start_date']) && !empty($postdata['start_date'])) ? $postdata['start_date'] : '';
          $filters['end_date'] = (isset($postdata['end_date']) && !empty($postdata['end_date'])) ? $postdata['end_date'] : '';
		  $filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		  $filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : ''; 
        }
     if (Request::ajax()) {
            $ajaxdata['draw'] = !empty($postdata['draw']) ? $postdata['draw'] : 10;
            $ajaxdata['url'] = URL::to('/');
            $ajaxdata['data'] = array();
			$data['user_role'] = $user_role;
            $dat = array_merge($data, $filters);
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->affObj->manage_userdetails($dat, true); 
       if ($ajaxdata['recordsTotal'] > 0){
                $filter = array_filter($filters);
               if (!empty($filter)){
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->affObj->manage_userdetails($data, true);
                }
               if (!empty($ajaxdata['recordsFiltered'])){
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : Config::get('constants.DATA_TABLE_RECORDS');
					if (isset($data['order'])) {
						$data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
						$data['order'] = $postdata['order'][0]['dir'];
					}
                    $data = array_merge($data, $filters);
                    $ajaxdata['data'] = $this->affObj->manage_userdetails($data);
                }
            }
            return Response::json($ajaxdata);
        }
	   elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export'){
			$data['user_role'] = $user_role;
			$edata['manage_user_details'] = $this->affObj->manage_userdetails(array_merge($data,$filter));	
            $output = View::make('admin.affiliate.manage_affiliate_export',$edata);
            $headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=View Profile of User' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
            return Response::make($output, 200, $headers);
        }
        elseif(isset($filter['printbtn']) && $filter['printbtn']=='Print'){
			$data['user_role'] = $user_role;
			$pdata['manage_user_details'] = $this->affObj->manage_userdetails(array_merge($data,$filter));
            return View::make('admin.affiliate.manage_affiliate_print',$pdata);
        } 
		else{
            return View::make('admin.affiliate.manage_affiliate');  
		} 
    }
    public function view_details($uname) {
         $op = array();
        if ($details = $this->affObj->view_details($uname)) {
            $op['status'] = $this->statusCode =config('httperr.SUCCESS');
            $op['details'] = $details;
            $op['msg'] = trans('admin/affiliate/admin.user_details_success');
        }
        else{
            $op['status'] = $this->statusCode =config('httperr.NOT_FOUND');
            $op['msg'] = trans('general.not_found');
        }
        return $this->response->json($op);
	}
   public function change_password(){
         $data = array();
          return view('admin.affiliate.change_pwd',$data);
      } 
	public function user_block_status($account_id,$status) {
        $op = $postdata = array();
        $postdata['account_id'] = $account_id;
        $postdata['status'] =config('constants.ACCOUNT_USER.'.strtoupper($status));
	     return	$data=$this->affObj->user_block_status($postdata);
    }
	public function updateding_email(){
	  $data['status'] = '';
      $data['msg'] = '';
	  $postdata = $this->request->all();
	  return  $rdata = $this->affObj->Update_email($postdata);
	}
   public function update_mobile(){
		$data['status'] = '';
		 $data['msg'] = '';
		 $postdata = $this->request->all();
		 return  $rdata = $this->affObj->Update_mobile($postdata);
	}
   public function updatepwd() {
        $data['status'] = '';
        $data['msg'] = '';
        $postdata = $this->request->all();
	    if (!empty($postdata)){
	    $rdata = $this->affObj->update_password($postdata);
        return $rdata;
        }
	}
    public function edit_detail ($uname) {
         $op = array();
        if ($details = $this->affObj->user_edit($uname)) {
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            $op['edit'] = $details;
        }
        else {
            $op['status'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
            $op['msg'] = trans('general.not_found');
        }
        return $this->response->json($op,$this->statusCode); 
    }
   public function update_details () {
        $postdata = $this->request->all(); 
        if (!empty($postdata)) {
            $res =$this->affObj->user_update($postdata);
            return $res;
        }
        return $this->response->json($op);
    }
	public function reset_security_pin(){
		  $data = array();
       return view('admin.affiliate.change_security_pin',$data);
	}
   public function updatepin(){
        $data['status'] = '';
        $data['msg'] = '';
        $postdata = $this->request->all();
	    if (!empty($postdata)){
	     $rdata = $this->affObj->update_pin($postdata);
        return $rdata;
        }
	}
	public function affiliateAdd(){
		
		echo "SdsadSA"; die;
	}
}
