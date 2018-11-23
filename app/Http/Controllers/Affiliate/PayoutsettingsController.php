<?php

namespace App\Http\Controllers\Affiliate;
use App\Models\Affiliate\Payouts;
use App\Models\Affiliate\Payments;
use App\Models\Affiliate\AffModel;



class PayoutsettingsController extends AffBaseController
{
	private $packageObj = '';
    public function __construct ()
    {
        parent::__construct();
		$this->paymentObj = new Payments;
		$this->payoutsObj = new Payouts;
	
    }
	
	public function payouts_settings(){
		$payout_settings = $this->payoutsObj->getAccount_payoutsettings(array('account_id' => $this->userSess->account_id));
		$sarr['account_id'] = $this->userSess->account_id;
        $existing_payout_banks = $this->payoutsObj->get_editable_banks($sarr);	
		if ($payout_settings)
        {
            $vdata['payment_type'] = $payout_settings;
				foreach ($payout_settings as $pset)
				{	
					$setting_name = strtolower(str_replace(' ', '_', $pset->payment_type)).'_settings'; 
					 
					if ($setting_name == 'bank_transfer_settings') 
					{ 
						if(!isset($vdata['bank_transfer_settings'])){ 
							$vdata['bank_transfer_settings'][]= $this->bank_transfer_new_settings(); 
						} 
						$vdata[$setting_name][] = $this->$setting_name($pset,$existing_payout_banks); 
					}
					elseif ($setting_name == 'cashfree_transfer_settings')
					{
						$vdata[$setting_name][] = $this->$setting_name($pset); 
					}
					elseif ($setting_name == 'paytm_transfer_settings')
					{
						$vdata[$setting_name][] = $this->$setting_name($pset);
					}
				}
            
		}
		return view('affiliate.settings.payouts',$vdata);
    }
	public function bank_transfer_new_settings()
    {
		$data = array();
        $data['payment_type_id'] = $this->config->get('constants.BANK_TRANSFER');
        $data['bank_account_types'] = $this->payoutsObj->getBank_accouttypes_lang(array('payment_type_id'=>$data['payment_type_id']));
        $data['currency_list'] = $this->paymentObj->get_currencies();		
      return view('affiliate.settings.bank_transfer_settings', $data)->render();
    }
	public function bank_transfer_settings ($bankinfo,$existing_payout_banks=array())
    {
        
		$data = array();
		$wdata = array();	
        if ($bankinfo)
        {	
			
            $data['acpayout_setting_id'] = $bankinfo->acpayout_setting_id;
			$acpayout_setting_id =    $data['acpayout_setting_id'];
            $data['currency_id'] = $bankinfo->currency_id;
			$data['bank_account_types'] = $this->payoutsObj->getBank_accouttypes_lang(array('payment_type_id'=>$bankinfo->payment_type_id));
			$data['nick_name'] = $bankinfo->nick_name;
            $data['payment_type_id'] = $bankinfo->payment_type_id;
			$data['status'] = $bankinfo->withdrawal_status;
			$data['is_approved'] = $bankinfo->is_approved;
			
            
            if ($bankinfo->account_details != '')
            {
				$wdata = $bankinfo->account_details;
			    $data['acinfo'] = json_decode(stripslashes($wdata));							
            }
			if($existing_payout_banks)
			{	
				
				$data['editable'] = !in_array($acpayout_setting_id,$existing_payout_banks)? true:false;
			}
        }
        $data['currency_list'] = $this->paymentObj->get_currencies();
        return view('affiliate.settings.bank_transfer_settings', $data)->render();
    }
	public function account_payout_settings_update ()
    {
        $postdata = $this->request -> all();
        $op = array();
        if (!empty($postdata) && isset($postdata['payment_type_id']) && $postdata['payment_type_id'] > 0)
        {	
			
			/*  $rules =  [            
				'nick_name' => 'required',
				'currency_id' => 'required',
				'bank_account_type' => 'required',
				'account_name' => 'required',
				'account_no' => 'required',
				'bank_name' => 'required',
				'bank_branch' => 'required',
				'ifsccode' => 'required',
				'cashfree_account_id' => 'required',
				'status' => 'required',
				
			];
			$messages = [
				 'nick_name.required' => trans('affiliate/settings/payout_settings.nick_name'),
				 'currency_id.required' => trans('affiliate/settings/payout_settings.select_currency'), 
				 'bank_account_type.required' => trans('affiliate/settings/payout_settings.bank_account_type'),
				 'account_name.required' => trans('affiliate/settings/payout_settings.account_name'),
				 'account_no.required' => trans('affiliate/settings/payout_settings.account_no'),
				 'bank_name.required' => trans('affiliate/settings/payout_settings.bank_name'),
				 'bank_branch.required' => trans('affiliate/settings/payout_settings.bank_branch'),
				 'cashfree_account_id.required' => trans('affiliate/settings/payout_settings.cashfree_account_id'),
				 'status.required' => trans('affiliate/settings/payout_settings.status'),
				 
			];  
			$validator = Validator($postdata, $rules,$messages);
				if ($validator->fails()){ 
					$errs = $validator->errors();
					foreach($rules  as $key=>$formats){
						$op['errs'][$key] =  $validator->errors()->first($key);   
					}
					return response()->json($op,500);
				}
				else{  */
					$sdata = '';
					$wdata = '';
					$acdet = array();
					if (isset($postdata['account_name']))
					{
						$acdet['account_name'] = $postdata['account_name'];
					}
					if (isset($postdata['account_no']))
					{
						$acdet['account_no'] = $postdata['account_no'];
					}
					if (isset($postdata['bank_name']))
					{
						$acdet['bank_name'] = $postdata['bank_name'];
					}
					if (isset($postdata['bank_branch']))
					{
						$acdet['bank_branch'] = $postdata['bank_branch'];
					}
					if (isset($postdata['ifsccode']))
					{
						$acdet['ifsccode'] = $postdata['ifsccode'];
					}
					if (isset($postdata['bank_account_type']))
					{
						$acdet['bank_account_type_id'] = $postdata['bank_account_type'];
					}
					if (isset($postdata['cashfree_account_id']))
					{
						$acdet['cashfree_account_id'] = $postdata['cashfree_account_id'];
					}
					if (isset($postdata['paytm_account_id']))
					{
						$acdet['paytm_account_id'] = $postdata['paytm_account_id'];
					}
					$sdata['currency_id'] = $postdata['currency_id'];
					$sdata['status'] = $postdata['status'];
					$sdata['account_details'] = addslashes(json_encode($acdet));
					$sdata['payment_type_id'] = $postdata['payment_type_id'];
					$sdata['account_id'] = $this->userSess->account_id;
					//$sdata['nick_name'] = $postdata['nick_name'];
					$wdata['acpayout_setting_id'] = isset($postdata['acpayout_setting_id']) ? $postdata['acpayout_setting_id']: 0;
					$res = $this->payoutsObj->account_payout_settings_update($sdata, $wdata, $acdet);
					if($res)
					{ 
						$op['status'] = 200;
						$op['msg']   = "<div id='div' class='alert alert-success'>".$res['msg']."</div>";
					}
					return response()->json($op,200);
				
        }
       
    }
	public function checkTpin ()
    {
		$tpin='';
		$op = array();
        $postdata = $this -> request -> all();
		if (isset($postdata['tpin']))
            {
                $tpin = $postdata['tpin']; //print_r(  $tpin );exit;
            }
		$account_id = $this->userSess->account_id;
		if ($tpin != '' &&  $this->session->has('userdata'))
        {	
            $ud = $this->affObj->getUser_loginDetails( $account_id,array(
                'trans_pass_key')); 
            if ($ud->trans_pass_key == md5($tpin))
            {
                $op['status'] = 200;
            }
            else
            {
                $op['status'] = 500;
                $op['msg'] = trans('affiliate/settings/user_controller.sec_pwd_not_matched');
				
            }
        }
		return response()->json($op);        
    }
	public function cashfree_transfer_settings ($account_details)
    { 
		
        $data = array();
		$data['acpayout_setting_id'] = $account_details->acpayout_setting_id;
		$acpayout_setting_id = $data['acpayout_setting_id'];
        $data['account_id'] = $this->userSess->account_id;
        $data['payment_type_id'] = $this->config->get('constants.CASHFREE_TRANSFER');
        if (isset($account_details->payment_type_id) && $account_details->payment_type_id == $this->config->get('constants.CASHFREE_TRANSFER'))
        {
            $data['currency_id'] = $account_details->currency_id;
            $data['status'] = $account_details->status;
            $data['withdrawal_status'] = $account_details->withdrawal_status;
            if (isset($account_details->account_details) && !empty($account_details->account_details))
            {
                $data['acinfo'] = json_decode(stripslashes($account_details->account_details));
            }
        }
        $data['currency_list'] = $this->paymentObj->get_currencies();
        return view('affiliate.settings.cashfree_transfer_settings', $data)->render();
    } 
	public function paytm_transfer_settings ($account_details)
    {
        $data = array();
		$data['acpayout_setting_id'] = $account_details->acpayout_setting_id;
		$acpayout_setting_id = $data['acpayout_setting_id'];
        $data['account_id'] = $this->userSess->account_id;
        $data['payment_type_id'] = $this->config->get('constants.PAYTM_TRANSFER');
        if (isset($account_details->payment_type_id) && $account_details->payment_type_id == $this->config->get('constants.PAYTM_TRANSFER'))
        {
            $data['currency_id'] = $account_details->currency_id;
            $data['status'] = $account_details->status;
            $data['withdrawal_status'] = $account_details->withdrawal_status;
            if (isset($account_details->account_details) && !empty($account_details->account_details))
            {
                $data['acinfo'] = json_decode(stripslashes($account_details->account_details));
            }
        }
        $data['currency_list'] = $this->paymentObj->get_currencies();
        return view('affiliate.settings.paytm_transfer_settings', $data)->render();
    }
}