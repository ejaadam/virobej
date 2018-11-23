<?php

namespace App\Http\Controllers\Affiliate;
use App\Library\MailerLib;
use App\Library\Sendsms;
use App\Models\Affiliate\Package;
use App\Models\Affiliate\Wallet;
use App\Models\Affiliate\AffModel;
use App\Models\Affiliate\Payments;


use Illuminate\Support\Facades\Input;

use Request;

use Response; 

class TranferController extends AffBaseController
{
	private $smsObj;
	private $packageObj = '';
	
  public function __construct ()
    {
        parent::__construct();
		$this->walletObj = new Wallet;
		$this->affObj = new AffModel;
	    $this->smsObj = new Sendsms;	
		$this->paymentsObj= new Payments;
		
    }
	
	public function fundtransfer()
	{
	    $arr=[];
		$arr['account_id']=$this->userSess->account_id;
		$data = array();
        $data['show_all'] 			 = 0;
        $data['account_verif_count'] = 0;
	    $account_verification_count  = $this->affObj->get_user_verification_total($arr);
			
		$data['account_verif_count'] = 1 ;
			$data['userdetails'] = $this->affObj->getUser_treeInfo($arr);
			
    	if (!empty($data['userdetails'])&& $data['userdetails']->status == 1)
       {
		    $charge = 0;
				$data['currency'] =json_encode($this->walletObj->get_currencies($arr));
				
		    $ud = $this->affObj->getUser_loginDetails($arr, array('trans_pass_key'));
        if ($ud)
         {
			
            Session::put('fund_transfer', $ud->trans_pass_key);
         }
        if (empty($postdata))
         {
		    $data['user_setting_key_charges'] = $this->walletObj->getSetting_key_charges();
         }
		$data['current_balance'] = 0;
	    $data['user_balance_det'] = $this->walletObj->getWalletBalnceTotal(array(
                	'account_id'=>$this->userSess->account_id));
		//	print_r( $data['user_balance_det'] ); exit;
        $data['availbalance'] = 0;
	    $data['fund_trasnfer_settings'] = json_encode($this->walletObj->get_fund_transfer_settings(array(
                        'transfer_type'=>$this->config->get('constants.FUND_TRANSFER'))));
	    $data['wallet_list'] = $this->walletObj->get_all_wallet_list();
	//	print_r( $data['wallet_list']); exit;
		$data['from_user_id']=$arr['account_id'];
        $data['security_pwd'] = $data['userdetails']->trans_pass_key;
        return view('affiliate.wallet.fundtransfer',$data); 
		
		
        }
    }
		
	public function searchacc($user_name = '')
    {
		
        $op = array();
        $op['status'] = 'error';
        $op['msg'] = trans('affiliate/wallet/fundtransfer.invalid_username');
        $user_pass = 0;
        if (Request::ajax())
        {
            $postdata = $this->request->all();	
			
		    if (!isset($postdata['username']) && empty($postdata['username']))
            {
                $postdata['username'] = $user_name;
                $user_pass = 1;
            }
        }
        else
        {
            $postdata['username'] = $user_name;
        }
        if ($postdata)
        {
            $userid = 0;
            $status = $this->affObj->usercheck_for_fundtransfer($postdata['username']);
            $userid = $this->userSess->account_id;
            if ($status['user_id'] == $userid)
            {
                $op['status'] = 'error';
                $op['msg'] = trans('affiliate/wallet/fundtransfer.invalid_username');
            }
            else
            {
                $op = $status;
            }
        }
        if (Request::ajax())
        {
            if ($user_pass == 1)	
            {
                return $op;
            }
            return Response::json($op);
        }
        else
        {
            return $op;
        }
    }
	
	public function get_tac_code ()
    {
		
        $data['siteConfig'] =$this->siteConfig;
		$arr=[];
		$postdata = $this->request->all();
        $user_id = $this->userSess->account_id;
        $data['userdetails']=$this->userSess;
		$arr['account_id']=$this->userSess->account_id;
		$op = array(
            'status'=>'error',
            'msg'=>'null');
        $postdata = $this->request->all();
        $req = (Input::get('req')) ? Input::get('req') : '';
        Session::forget($arr['account_id'].'_fund_transfer_tac_code');
        $current_user_id = $arr['account_id'];
	    $data['email'] = $data['userdetails']->email;
		$tac_code = rand(100000, 999999);
        $data['tac_code'] = $tac_code;
       $op['tac_code'] = $tac_code;                
	    $data['user'] = $data['userdetails'];
        if (isset($user_id) && !empty($user_id) && $current_user_id == $user_id)
        { 
          if (empty($req) && !Session::has($user_id.'_fund_transfer_tac_code'))
            {
			    Session::put($user_id.'_fund_transfer_tac_code', $tac_code);
                $htmls = view('emails.account.settings.tac_code', $data)->render();
		        $mstatus = new MailerLib(array(
                    'to'=>$data['email'],
                    'subject'=>'TAC Code for Fund Transfer',
                    'html'=>$htmls,
					'from'=>$this->config->get('constants.SYSTEM_MAIL_ID'),
					'fromname'=>$this->config->get('constants.DOMAIN_NAME')
                ));
				$res=$this->smsObj->send_sms(['reset_code'=>$tac_code,'phonecode'=>$this->userSess->phonecode,'mobile'=>$this->userSess->mobile,'site_name'=>$this->siteConfig->site_name],$this->config->get('sms_service.FUNDTRANSFER_CODE'));
	            $op['status'] = 'ok';
			    $op['msg'] = trans('affiliate/wallet/fundtransfer.tac_code_email_send_msg', array(
                            'email_id'=>$data['email']));
            }
			 else if (!empty($req) && $req == 'usrexg' && !Session::has($user_id.'_usrexchange_tac_code'))
            {
                Session::put($user_id.'_usrexchange_tac_code', $tac_code);
                $htmls = view('emails.account.settings.tac_code', $data)->render();
                $mstatus = new MailerLib(array(
                    'to'=>$data['email'],
                    'subject'=>'TAC Code for Exchange Currency',
                    'html'=>$htmls,
                    'from'=>$this->config->get('constants.SYSTEM_MAIL_ID'),
					'fromname'=>$this->config->get('constants.DOMAIN_NAME')
                ));
                $op['status'] = 'ok';
               $op['msg'] = trans('affiliate/wallet/fundtransfer.tac_code_email_send_msg', array(
                            'email_id'=>$data['email']));
            }
            else if (!empty($req) && $req == 'usrauth' && !Session::has($user_id.'usrtoken'))
            {
                Session::put($user_id.'usrtoken', $tac_code);
                $htmls = view('emails.account.settings.tac_code', $data)->render();
                $mstatus = new MailerLib(array(
                    'to'=>$data['email'],
                    'subject'=>'TAC Code for Account Login',
                    'html'=>$htmls,
                     'from'=>$this->config->get('constants.SYSTEM_MAIL_ID'),
					'fromname'=>$this->config->get('constants.DOMAIN_NAME')
                ));
                $op['status'] = 'ok';
                $op['msg'] = trans('affiliate/wallet/fundtransfer.tac_code_email_send_msg', array(
                            'email_id'=>$data['email']));
            }
            else
            {
                $op['status'] = 'ERR';
                $op['msg'] = trans('affiliate/wallet/fundtransfer.tac_code_email_already_send_msg', array(
                            'email_id'=>$data[email]));
            }
        }
        return json_encode($op);
    }
	
	
	public function fund_transfer_to_user_confirm ()
    {
		$data=[];
		$sessdata = [];
		$arr['account_id']=$this->userSess->account_id;
        if (!Request::ajax())
        {
            App::abort(403, 'Unauthorized access');
            exit;
        }
		$userdetails = $this->affObj->getUser_treeInfo(['account_id'=>$this->userSess->account_id]);
		
        if (!empty($userdetails)&& $userdetails->block == 0)
        {
			$data['userObj'] = $this->affObj;
			//print_r($data['userObj']); exit;
			$sessdata['current_balance'] = 0;
			$sessdata['account_settings']= $this->walletObj->get_user_settings($arr);
			$postdata = $this->request->all();	
			if ($postdata)
			{
				$sessdata['currency_id'] = $currency_id = $postdata['currency_id'];//us->currency_id
				if (isset($postdata['currency_code']))
				{
					$sessdata['currency_code'] = $postdata['currency_code'];
				}
				else 
				{
					$sessdata['currency_code'] = $this->walletObj->get_currency_name($postdata['currency_id']);
				}
				$sessdata['wallet_id'] = $wallet_id = $postdata['wallet_id'];
				$sessdata['ewallet_name'] = $postdata['ewallet_name'];
	           if (isset($postdata['ewallet_name'])) 
				{
					$sessdata['ewallet_name'] = $this->walletObj->get_wallet_name($postdata['wallet_id']);
				}	
				$sessdata['to_user'] = $postdata['to_user'];
				$sessdata['rec_name'] = $postdata['rec_name'];
				$sessdata['rec_email'] = $postdata['rec_email'];
				$sessdata['totamount'] = $postdata['totamount'];
				$sessdata['min_trans_amount'] = $postdata['min_trans_amount'];
				$sessdata['max_trans_amount'] = $postdata['max_trans_amount'];
				$sessdata['charge'] = $postdata['charge'];
				$sessdata['from_user_id']=$arr['account_id'];	 
			}

            $charge = 0;            
			$sessdata['currency'] =json_encode($this->walletObj->get_currencies($arr));
            $upassInfo = $this->affObj->getUser_loginDetails($arr, ['trans_pass_key']);	
            		
		    if ($upassInfo)
            {
                Session::put('fund_transfer', $upassInfo->trans_pass_key);				
            }
			
            $user_balance_det = $this->walletObj->get_user_balance(1,$arr, $wallet_id, $currency_id);	  
			$sessdata['availbalance'] = 0;
            if ($user_balance_det)
            {
                $sessdata['availbalance'] = $user_balance_det->current_balance;				
            }
            $sessdata['fund_trasnfer_settings'] = json_encode($this->walletObj->get_fund_transfer_settings(array('transfer_type'=>$this->config->get('constants.FUND_TRANSFER'))));
            $sessdata['from_account_id'] = $arr;			
            $sessdata['security_pwd'] = $userdetails->trans_pass_key;
			$this->session->put('ftsess',$sessdata);
			$data = array_merge($data,$sessdata);
			
        }		
		else {
			   $data['error']= trans('affiliate/wallet/fundtransfer.cant_transfer_fund');
			 }
			
		    return view('affiliate.wallet.fund_transfer_confirm',$data);
    }
	
	public function fund_transfer_to_user ()
    {
		 $arr=[];
		 $arr['account_id']=$this->userSess->account_id;
	 	 $userdetails = $this->affObj->getUser_treeInfo($arr);
	     $data['created_on'] = $postdata['created_on'] = date('Y-m-d H:i:s');
		 //print_r($data['created_on']); exit;
         if (!Request::ajax())
        {
            App::abort(403, 'Unauthorized access');
            exit;
        }
        $op = array(
            'status'=>'error',
            'msg'=>'null');
        $postdata = $this->request->all();	
		$postdata = array_merge($postdata,$this->session->get('ftsess'));
        if ($postdata['submit'] == 'Back')
        {
            $returndata['viewdata'] = $this->fund_transfer_to_user_back();
            return Response::json($returndata);
		   
        }
       else{
            $email_data = array();
            $data = array();
            $data['siteConfig'] =$this->siteConfig;
            $payment_type = 1;
            $tac_check = 0;
			$data['account_settings']= $this->walletObj->get_user_settings($arr);
            if ($data['account_settings']->otp_status == $this->config->get('constants.ON'))
            {
                if (!empty($postdata) && isset($postdata['from_user_id']) && Session::has($postdata['from_user_id'].'_fund_transfer_tac_code') && Session::get($postdata['from_user_id'].'_fund_transfer_tac_code') == $postdata['tac_code'])
				{
                    $tac_check = 1;
                }
            }
            else
            {
                $tac_check = 1;
            }
            if ($tac_check)
       {
                $data['from_uname'] = $this->userSess->uname;
                $data['from_full_name'] = $this->userSess->full_name;
                $data['from_email'] = $this->userSess->email;
                $this->email = $this->userSess->email;
                $email_data['from_email'] = $this->email;
                $ewallet_id = $postdata['wallet_id'];
				$bal_details = $this->walletObj->get_user_balance($payment_type, $arr, $ewallet_id, $postdata['currency_id']);
                if ($bal_details && count($bal_details) > 0 && $bal_details->current_balance > 0 && $bal_details->current_balance >= $postdata['totamount'])
                 {
                    $fund_trasnfer_settings = $this->walletObj->get_fund_transfer_settings(array(
                        'currency_id'=>$postdata['currency_id'],
                        'transfer_type'=>$this->config->get('tblconstants.FUND_TRANSFER')));
                    $fund_trasnfer_settings = $fund_trasnfer_settings[0];
                    $check_to_user = $this->searchacc($postdata['to_user']);
                if ($postdata['totamount'] >= $fund_trasnfer_settings->min_amount && $postdata['totamount'] <= $fund_trasnfer_settings->max_amount && $check_to_user['status'] == "ok")
                {
                        $postdata['to_user_id'] = $check_to_user['account_id'];
                        $cur_balance = $bal_details->current_balance;
						$from_cur_balance = $cur_balance;
                        $dataArray['user_id'] = $this->userSess->account_id;
                        $dataArray['wallet_id'] = $postdata['wallet_id'];
						//print_r(  $dataArray['wallet_id']); exit;
					    $dataArray['currency_id'] = $postdata['currency_id'];
						$dataArray['transaction_type'] = $this->config->get('constants.DEBIT');
                        $dataArray['amount'] = $postdata['totamount'];
						$dataArray['paidamt'] = $postdata['totamount'];
					    $dataArray['payment_type'] = $payment_type;
					    $from_transaction_id = $this->walletObj->generateTransactionID($this->userSess->account_id);
					    $data['from_transaction_id'] = $from_transaction_id;
                        $all_user_details = $userdetails;
					    $status = $this->walletObj->update_user_balance($dataArray);
                if ($status)
                {
                           $cur_balance1 = '';
                           $bal_details1 = $this->walletObj->get_user_balance($payment_type, $arr, $ewallet_id, $postdata['currency_id']);
                if ($bal_details1 && count($bal_details1) > 0)
                            {
                                $cur_balance1 = $bal_details1->current_balance;
                            }
							$status = array(
                                    'from_account_id' => $this->userSess->account_id,
                                    'to_account_id' => $postdata['to_user_id'],
                                    'transaction_id' => $from_transaction_id,
									'from_account_wallet_id' => $userdetails->wallet_id,
                                    'to_account_wallet_id' =>$ewallet_id,
                                    'currency_id' => $postdata['currency_id'],
                                    'amount' => $dataArray['amount'],
                                    'paidamt' => $dataArray['amount'],
                                    'handleamt' =>'0',
				                 	'ast_type' => $this->config->get('constants.USER'),
                                    'transfered_on' =>$postdata['created_on'] = date('Y-m-d H:i:s'),
				                 //	'transfered_by' => $admin_account_id,
                                    'ip_address' => Request::getClientIp(true),
                                    'status' =>$this->config->get('constants.STATUS_CONFIRMED'));
									//print_r($status); exit;
							$tstatus = $this->walletObj->fund_user_transaction($status);
                            $dataTransArray = array(
                                'account_id'=>$this->userSess->account_id,
                                'payment_type'=>1,
                                'currency_id'=>$postdata['currency_id'],
                                'statementline'=>9,
                                'amount'=>$dataArray['amount'],
                                'paidamt'=>$dataArray['amount'],
                                'handleamt'=>$postdata['charge'],
                                'wallet_id'=>$ewallet_id,
                                'transaction_type'=>$this->config->get('constants.DEBIT'),
                                'remark'=>'To '.$postdata['to_user'],
							    'ip_address'=>Request::getClientIp(true),
                                'transaction_id'=>$from_transaction_id,
                                'current_balance'=>$cur_balance1,
                                'status'=>1);			
                            $tstatus = $this->walletObj->add_user_transaction($dataTransArray);
                            $dataArray = '';
                            $dataArray['user_id'] = $postdata['to_user_id'];
                            $dataArray['wallet_id'] = $ewallet_id;
                            $dataArray['currency_id'] = $postdata['currency_id'];
                            $dataArray['transaction_type'] = $this->config->get('constants.CREDIT');
                            $dataArray['amount'] = $postdata['totamount'];
                            $dataArray['paidamt'] = $postdata['totamount'];
                            $dataArray['payment_type'] = 1;
                            $to_transaction_id = $this->walletObj->generateTransactionID($postdata['to_user_id']);
                            $data['to_transaction_id'] = $to_transaction_id;
                            $status1 = $this->walletObj->update_user_balance($dataArray);
                            if ($status1)
                            {
                               $bal_details = $this->walletObj->get_user_balance($dataArray['payment_type'], array('account_id'=>$postdata['to_user_id']), $ewallet_id, $postdata['currency_id']);
                               if ($bal_details && count($bal_details) > 0)
                                {
                                    $cur_balance = $bal_details->current_balance;
                                }
                                $dataTransArray = array(
                                    'account_id'=>$postdata['to_user_id'],
                                    'payment_type'=>1,
                                    'currency_id'=>$postdata['currency_id'],
                                    'statementline'=>10,
                                    'amount'=>$postdata['totamount'],
                                    'paidamt'=>$postdata['totamount'],
                                    'wallet_id'=>$ewallet_id,
                                    'transaction_type'=>$this->config->get('constants.CREDIT'),
                                    'remark'=>'(From '.$all_user_details->uname.')',
                                    'ip_address'=>Request::getClientIp(true),
                                    'transaction_id'=>$to_transaction_id,
                                    'current_balance'=>$cur_balance,
                                    'status'=>1);
                                $tstatus1 = $this->walletObj->add_user_transaction($dataTransArray);
                            }
                            $to_userdetails = $this->walletObj->get_userdetails_byid($postdata['to_user_id']);
							$email_data['to_email'] = $to_userdetails->email;
                            $data['to_uname'] = $to_userdetails->uname;
                            $data['to_full_name'] = $to_userdetails->first_name.' '.$to_userdetails->last_name;
                            $data['amount'] = $dataArray['amount'];
                            $currency = $this->walletObj->get_currency_name($dataArray['currency_id']);
                            $data['currency'] = $currency[0];
                            if ($status && $tstatus && $status1 && $tstatus1)
                            {
                              Session::put('success', trans('affiliate/wallet/fundtransfer.transfer_fund_completed'));
							  $htmls = view('emails.account.settings.fundtransfer_fromuser', $data)->render();
                              $mstatus = new MailerLib(array(
                                    'to'=>$email_data['from_email'],
                                    'subject'=>'Fund Transfer Notification',
                                    'html'=>$htmls, 
                                    'from'=>$this->config->get('constants.SYSTEM_MAIL_ID'),
					                'fromname'=>$this->config->get('constants.DOMAIN_NAME')
                                ));
                                $htmls = view('emails.account.settings.fundtransfer_touser', $data)->render();
                                $mstatus = new MailerLib(array(
                                    'to'=>$email_data['to_email'],
                                    'subject'=>'Fund Transfer Notification',
                                    'html'=>$htmls,
                                    'from'=>$this->config->get('constants.SYSTEM_MAIL_ID'),
					                'fromname'=>$this->config->get('constants.DOMAIN_NAME')
                                ));
                                Session::forget('fund_transfer');
                                $op['status'] = 'ok';
                                $op['msg'] = trans('affiliate/wallet/fundtransfer.transfer_fund_completed');
                            }
                            else
                            {
                                Session::put('success', trans('affiliate/wallet/fundtransfer.transfer_fund_failed'));
                                $op['status'] = 'error';
                                $op['msg'] =trans('affiliate/wallet/fundtransfer.transfer_fund_failed');
                            }
                        }
                        else
                        {
                            Session::put('success', trans('affiliate/wallet/fundtransfer.transfer_fund_failed'));
                            $op['status'] = 'error';
                            $op['msg'] = trans('affiliate/wallet/fundtransfer.transfer_fund_failed');
                        }
                }
                    else
                    {
                        Session::forget('fund_transfer');
                        $op['status'] = 'error';
                        $op['msg'] = trans('affiliate/wallet/fundtransfer.cant_transfer_amt');
                    }
            }
        }
            else
            {
                Session::put('success',trans('affiliate/wallet/fundtransfer.incorrect_tac_code'));
                $op['status'] = 'error';
                $op['msg'] = trans('affiliate/wallet/fundtransfer.incorrect_tac_code');
            }
            return Response::json($op);
    }
	}
	
	
	
public function transactions(){
		$data = array();
		return view('affiliate.wallet.fundtransfer_history',$data);
	}
	
	public function fundtransfer_history()
	
	{
		
		$data = $wdata = $filter = array();
		$post = $this->request->all();	
		$data['currencies']=$this->paymentsObj->get_currencies();
		$data['wallet_list']=$this->walletObj->get_all_wallet_list();
		$filter['account_id'] = $this->userSess->account_id; 
		if (isset($post['order']))
		{
			$wdata['orderby'] = $post['columns'][$post['order'][0]['column']]['name'];
			$wdata['order'] = $post['order'][0]['dir'];
		}																										
		$filter['search_term'] = $this->request->has('search_term')? $this->request->get('search_term') : '';
		$filter['from_date'] = $this->request->has('from_date')? $this->request->get('from_date') : '';
		$filter['to_date'] = $this->request->has('to_date')? $this->request->get('to_date') : '';
		$filter['wallet_id'] = $this->request->has('wallet_id')? $this->request->get('wallet_id') : '';
		$filter['currency_id'] = $this->request->has('currency_id')? $this->request->get('currency_id') : '';
		$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		$filter['printbtn'] = $this->request->has('printbtn')? $this->request->get('printbtn') : '';
		if (\Request::ajax())        
		{
			$wdata['count'] = true;
		    $ajaxdata['recordsTotal'] = $this->walletObj->transfer_history_details(array_merge($wdata,$filter)); 
			
		  	$ajaxdata['draw'] = !empty($post['draw']) ? $post['draw'] : '';
            $ajaxdata['recordsFiltered'] = 0;
            $ajaxdata['data'] = array();
			
            if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
            {
				$ajaxdata['recordsFiltered'] = $this->walletObj->transfer_history_details(array_merge($wdata,$filter));  //filtered
                $wdata['start'] = !empty($post['start']) ? $post['start'] : 0;				
				$wdata['length'] = !empty($post['length']) ? $post['length'] : 10;
				//print_r($ajaxdata);
				unset($wdata['count']);                    

				$ajaxdata['data'] = $this->walletObj->transfer_history_details(array_merge($wdata,$filter));  ///get data all results display//
				
			}
		    $statusCode = 200;
            return $this->response->json($ajaxdata, $statusCode, [], JSON_PRETTY_PRINT);
		}
		else if (isset($filter['exportbtn']) && $filter['exportbtn'] == 'Export')     //export data
		  {
			$epdata['export_data']= $this->walletObj->transfer_history_details(array_merge($wdata,$filter));
			//print_r($epdata);
			//exit;
            $output = view('affiliate.wallet.fundtransfer_export_history', $epdata);
            $headers = array(
                'Pragma' => 'public',
                'Expires' => 'public',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Cache-Control' => 'private',
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename=Fund_Transfer_List_' . date("d-M-Y") . '.xls',
                'Content-Transfer-Encoding' => ' binary'
            );
            return $this->response->make($output, 200, $headers);
        } 
		else if (isset($filter['printbtn']) && $filter['printbtn'] == 'Print')   //print data
		{
	
			$pdata['print_data']= $this->walletObj->transfer_history_details(array_merge($wdata,$filter));
			
            return view('affiliate.wallet.fundtransfer_print_history', $pdata);
                           
        }
		
		else
		{
		return view('affiliate.wallet.fundtransfer_history',$data);
		}
	}
	 
	public function fund_transfer_to_user_back ()
    {
	   $data['current_balance'] = 0;
	   $data['availbalance'] = 0;
	   $data['wallet_list'] = $this->walletObj->get_all_wallet_list(array(
                'fundtransfer_status'=>1));
	   $data['from_user_id'] = $this->userSess->account_id;
	   $viewdata = view('affiliate.wallet.fund_transfer_form',$data)->render();
       return $viewdata;
    }

	
	
	 
}
