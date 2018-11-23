<?php
namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\AffBaseController;
use App\Models\Affiliate\Withdrawal;
use App\Models\Affiliate\Wallet;
use App\Models\Affiliate\Payments;

class WithdrawalController extends AffBaseController
{
	private $packageObj = '';
    public function __construct ()
    {
        parent::__construct();
		$this->withdrawalObj = new Withdrawal;
		$this->paymentObj = new Payments;
		$this->walletObj = new Wallet;
    }
	
	public function new_withdrawal()
	{
		$op = array();
        $data = array();
        $postdata = $this-> request->all();
        $wdata['account_id'] = $this->userSess->account_id;
        $data['balance_list'] = $this->withdrawalObj->withdrawal_wallet_balance_list($wdata);
      
		return view('affiliate.withdrawal.create',$data);
	}
	public function withdrawal_list ($status)
    {
        $data = array();
        $data['status_array'] = array('pending'=>0, 'transferred'=>1, 'processing'=>2, 'cancelled'=>3, 0=>'pending', 1=>'transferred', 2=>'processing', 3=>'cancelled');
        if (in_array($status, $data['status_array']))
        {
            $data['status_key'] = $status;
            $data['pg_title'] = ucwords($status).' Withdrawals';
            $data['status'] = $data['status_array'][$status];
            $data['status_label_array'] = array('label label-warning', 'label label-success', 'label label-info', 'label label-danger');
        }
        $data['wallet_list'] = $this->walletObj->get_wallet_list(array('withdrawal_status'=>$this->config->get('constants.ON')));
        return view('affiliate.withdrawal.withdrawal_list', $data);
    }
	public function payoutTypesList ()
    {
        $data = [];
		$op = [];
        $data['withdrawal'] = $this->withdrawalObj->withdrawal_payout_list(); //print_r($data);exit;
			if($data){ 
				return  $this->response->json($data,200);
			}
			
    }
	public function payoutDetails ()
    {
        $op = [];
        $data = $this->request->all();// print_r($data);exit;
		
        if ($this->request->has('payout_type_key') && ($payoutType = $this->withdrawalObj->payoutTypeDetails($this->request->get('payout_type_key'))))
        {	
            $data['payout_type_key'] = $payoutType->payout_type_key;
			$data['payment_type_id'] = $payoutType->payment_type_id;
            $data['currency_id'] = $this->request->has('currency_id') ? $this->request->get('currency_id') : $this->userSess->currency_id;
			
            $data['account_id'] = $this->userSess->account_id;
            if ($payoutType && !empty($payoutType) && (empty($payoutType->currency_allowed) || (!empty($payoutType->currency_allowed) && array_key_exists($data['currency_id'], $payoutType->currency_allowed))) && ($payoutType->is_country_based ==$this->config->get('constants.OFF') || ($payoutType->is_country_based == $this->config->get('constants.ON') && isset($payoutType->currency_allowed[$data['currency_id']]))))
            {	
                if ($payoutType->is_user_country_based == $this->config->get('constants.OFF') || ($payoutType->is_user_country_based == $this->config->get('constants.ON') && in_array($this->userSess->currency_id, $payoutType->currency_allowed[$data['currency_id']])  ))
                {	
					
                    $settings = $this->withdrawalObj->get_balance_bycurrency($data);	//print_r($settings);exit;
                    if (!empty($settings))
                    {
                        $data['amount'] = isset($data['amount']) && !empty($data['amount']) ? $data['amount'] : $settings['max'] ;
                        if ($data['amount'] >= $settings['min'] && $data['amount'] <= $settings['max'])
                        {
                            $proceed = true;
                            $total_breakdowns = 0;
                            if (isset($data['breakdowns']) && !empty($data['breakdowns']))
                            {
                                foreach ($settings['breakdowns'] as $balance_breakdowns)
                                {
                                    if (isset($data['breakdowns'][$balance_breakdowns->wallet_id][$balance_breakdowns->currency_id]))
                                    {
                                        $dreakdown = $data['breakdowns'][$balance_breakdowns->wallet_id][$balance_breakdowns->currency_id];
                                        if ($proceed && $dreakdown > 0 && ($dreakdown < $balance_breakdowns->min || $dreakdown > $balance_breakdowns->max))
                                        {
                                            $proceed = false;
                                        }
                                        $total_breakdowns+=$dreakdown;
                                    }
                                }
                            }
                            else
                            {
                                $total_breakdowns = $data['amount'];
                            }
                            if ($proceed)
                            {	
                                $op = array_merge((array) $op, $settings);
                                $op['amount'] = $data['amount'] = $total_breakdowns;
								$op['account_details']  = $this->withdrawalObj->get_preBank_info($data);
								$op['currency_code'] = $settings['currency_code'];
								
								
                                $op['currency_symbol'] = $settings['currency_symbol'];
                                unset($payoutType->payment_type_id);
                                unset($payoutType->is_country_based);
                                unset($payoutType->is_user_country_based);
                                unset($payoutType->countries_not_allowed);
                                unset($payoutType->countries_allowed);
                                $op['payout_type_details'] = $payoutType; //print_r( $op['payout_type_details']);exit;
                                $op['status'] = 200;
                            }
                            else
                            {
                                $op['msg'] =trans('user\general.invalid_breakdown');
                            }
                        }
                        else
                        {
                            $op['msg'] =trans('user\general.insufficient_bal');
                        }
                    }
                    else
                    {
                        $op['msg'] =trans('user\general.contact_administrator');
                    }
                }
                else
                {
                    $op['msg'] =trans('user\general.country_not_allowed');
                }
            }
            else
            {
                $op['msg'] =trans('user\general.please_contact_administrator');
            }
		
            return $this->response->json($op,200);
        }
        
    }
	public function saveWithdraw ()
    {
        $op = [];
		$data = $this->request->all();
		
        if ($this->request->has('payout_type_key') && ($payoutType = $this->withdrawalObj->payoutTypeDetails($this->request->get('payout_type_key'))))
        
        {
            $data['payment_type_id'] = $payoutType->payment_type_id;
			$data['currency_id'] = $this->request->has('currency_id') ? $this->request->get('currency_id') : $this->userSess->currency_id;
            $data['account_id'] = $this->userSess->account_id;
            if ($payoutType && !empty($payoutType) && (empty($payoutType->currency_allowed) || (!empty($payoutType->currency_allowed) && array_key_exists($data['currency_id'], $payoutType->currency_allowed))) && ($payoutType->is_country_based == $this->config->get('constants.OFF') || ($payoutType->is_country_based ==$this->config->get('constants.ON') && isset($payoutType->countries_allowed[$data['currency_id']]))))
            {
                if ($payoutType->is_user_country_based ==$this->config->get('constants.OFF') || ($payoutType->is_user_country_based ==$this->config->get('constants.ON') && in_array($this->user_details->country_id, $payoutType->countries_allowed[$data['currency_id']])))
                {
                    $settings = $this->withdrawalObj->get_balance_bycurrency($data);
                    if (!empty($settings))
                    {
                        $data['amount'] = isset($data['amount']) && !empty($data['amount']) ? $data['amount'] : $settings['balance'];
                        if ($data['amount'] >= $settings['min'] && $data['amount'] <= $settings['max'])
                        {
                            $proceed = true;
                            $total_breakdowns = 0;
                            if (isset($data['breakdowns']) && !empty($data['breakdowns']))
                            {
                                foreach ($settings['breakdowns'] as $balance_breakdowns)
                                {
                                    if (isset($data['breakdowns'][$balance_breakdowns->wallet_id][$balance_breakdowns->currency_id]))
                                    {
                                        $dreakdown = $data['breakdowns'][$balance_breakdowns->wallet_id][$balance_breakdowns->currency_id];
                                        if ($proceed && $dreakdown > 0 && ($dreakdown < $balance_breakdowns->min || $dreakdown > $balance_breakdowns->max))
                                        {
                                            $proceed = false;
                                        }
                                        $total_breakdowns+=$dreakdown;
                                    }
                                }
                            }
                            else
                            {
                                $total_breakdowns = $data['amount'];
                            }
                            if ($proceed && $total_breakdowns == $data['amount'])
                            {
                                $validation = ($payoutType->is_country_based ==$this->config->get('constants.OFF')) ? $payoutType->payout_type_key : $payoutType->payout_type_key.'.'.$settings['currency_code'];
                                $validator = Validator::make($data,$this->config->get('validations.WITHDRAWAL.'.$validation.'.RULES'),$this->config->get('validations.WITHDRAWAL.'.$validation.'.MESSAGES'));
                                if (!$validator->fails())
                                {
                                    unset($settings['breakdowns']);
                                    $data = array_merge((array) $data, $settings);
                                    $op = array_merge((array) $op, $settings);
                                    $op['account_details'] = $this->withdrawalObj->get_preBank_info($data);
                                    $op['currency_code'] = $settings['currency_code'];
                                    $op['currency_symbol'] = $settings['currency_symbol'];
                                    $op['payout_type_details'] = $payoutType;
                                    if ($this->withdrawalObj->saveWithdrawal($data))
                                    {
                                        $this->statusCode = 200;
                                        $op['msg'] = trans('affiliate/withdrawal.request_updated_successfully');
                                    }
                                    else
                                    {
                                        $op['msg'] = trans('affiliate/general.something_went_wrong');
                                    }
                                }
                                else
                                {
                                    $op['error'] = $validator->messages(true);
                                }
                            }
                            else
                            {
                                $op['msg'] = trans('affiliate/withdrawal.invalid_breakdown');
                            }
                        }
                        else
                        {
                            $op['msg'] = trans('affiliate/withdrawal.insufficient_bal');
                        }
                    }
                    else
                    {
                        $op['msg'] = trans('affiliate/general.please_contact_administrator');
                    }
                }
                else
                {
                    $op['msg'] = trans('affiliate/withdrawal.country_not_allowed');
                }
            }
            else
            {
                $op['msg'] = trans('affiliate/general.please_contact_administrator');
            }
           return $this->response->json($op,200);
        }
        
    }
	
	
	public function history($status='')
	{
		$data = $wdata = $filter = array();
		$post = $this->request->all();		
		$status_arr = ['pending'=>0,'transferred'=>1,'processing'=>2,'cancelled'=>3];
		$data['withdrawal_status'] = ucfirst($status);
		$filter['search_term'] = $this->request->has('search_term')? $this->request->get('search_term') : '';
		$filter['from'] = $this->request->has('from_date')? $this->request->get('from_date') : '';
		$filter['to'] = $this->request->has('to_date')? $this->request->get('to_date') : '';
		$filter['payment_type_id'] = $this->request->has('payment_type_id')? $this->request->get('payment_type_id') : '';
		$filter['currency_id'] = $this->request->has('currency_id')? $this->request->get('currency_id') : '';
		$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		$filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : '';
		$filter['status'] = $status_arr[strtolower($status)];
			
		if (\Request::ajax()) 
		{
			$wdata['count'] = true;
			
			if (isset($post['order']))
			{
				$wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
				$wdata['order'] = $post['order'][0]['dir'];
			}
            $ajaxdata['recordsTotal'] = $this->withdrawalObj->withdrawal_details($wdata);
			//print_r($ajaxdata['recordsTotal']);   //total records
		  	$ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
			
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
				$ajaxdata['recordsFiltered'] = $this->withdrawalObj->withdrawal_details(array_merge($wdata,$filter));  //filtered
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;				
				$wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
				//print_r($ajaxdata);
				unset($wdata['count']);                    
				$ajaxdata['data'] = $this->withdrawalObj->withdrawal_details(array_merge($wdata,$filter));  ///get data
				/*if(!empty($ajaxdata['data'])){
					array_walk($ajaxdata['data'],function(&$wtdata){
						$wtdata->created_on = date('d-M-Y H:i:s',strtotime($wtdata->created_on));
						if($wtdata->expected_date!=NULL){
						$wtdata->expected_date = date('d-M-Y',strtotime($wtdata->expected_date));
						}
						switch($wtdata->status){
						case 1:
							$wtdata->status_label = '<label class="label label-success">Transferred</label>';
						break;
						case 2:
							$wtdata->status_label = '<label class="label label-info">Processing</label>';
						break;
						case 0:
							$wtdata->status_label = '<label class="label label-warning">Pending</label>';
						break;
						case 3:
							$wtdata->status_label = '<label class="label label-danger">Cancelled</label>';
						break;
						} 
					});
				}*/
				//print_r($ajaxdata);
			}
		    $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);
		}
		else if (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')     //export data
		  {
			$epdata['export_data']= $this->withdrawalObj->withdrawal_details(array_merge($wdata,$filter));
			//print_r($epdata);
			//exit;
            $output = view('affiliate.withdrawal.withdrawal_export', $epdata);
            $headers = array(
                'Pragma' => 'public',
                'Expires' => 'public',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control' => 'private',
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename=Withdrawal_list_' . date("d-M-Y") . '.xls',
                'Content-Transfer-Encoding' => ' binary'
            );
            return \Response::make($output, 200, $headers);
        } 
		else if (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')   //print data
		{
		//print_r($filter['printbtn']);
		//exit;
			$pdata['print_data']= $this->withdrawalObj->withdrawal_details(array_merge($wdata,$filter));
			//print_r($pdata['print_data']);
			//exit;
            return view('affiliate.withdrawal.withdrawal_print', $pdata);
                           
        }
		else {
			$data['payments']=$this->paymentObj->get_paymodes();
			$data['currencies']=$this->paymentObj->get_currencies();
			return view('affiliate.withdrawal.history',$data);
		}
		
	}
	/*public function withdrawal_pending()
	{
	  $filter = array(); //array creation//
		$post = $this->request->all();  //get all value  from form data using post//
		//print_r($post);
		//exit;
		$filter['account_id'] = $this->userSess->account_id;   //variable creation //
        if (!empty($post))   //not empty value
        {			
            $filter['from'] = !empty($post['from']) ? $post['from'] : '';
			print_r($filter['from']);
			$filter['to'] = !empty($post['to']) ? $post['to'] : '';
			$filter['search_term'] = !empty($post['search_term']) ? $post['search_term'] : '';
			//$filter['currency_id'] = !empty($post['currency_id']) ? $post['currency_id'] : '';
			//$filter['wallet_id'] = !empty($post['wallet_id']) ? $post['wallet_id'] : '';
			$submit = isset($post['submit']) ? $post['submit'] : '';
        }
		return view('affiliate.withdrawal.history',$data);
	}*/
}