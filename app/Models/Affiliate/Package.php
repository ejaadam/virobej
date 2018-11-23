<?php

namespace App\Models\Affiliate;

use App\Models\BaseModel;
use App\Models\Affiliate\Payments;
use DB;
use AppService;

class Package extends BaseModel
{
	private $paymentObj = '';	
	public function __construct ()
    {
         parent::__construct();
		 $this->paymentObj = new Payments;
		 $this->affObj = new AffModel();
		 $this->walletObj = new Wallet;
		 $this->transactionObj = new Transaction;
    }
    //
	public function get_packages($arr = array())
	{
		$pkg =  DB::table($this->config->get('tables.AFF_PACKAGE_MST').' as pm')
                ->join($this->config->get('tables.AFF_PACKAGE_PRICING').' as pp', 'pp.package_id', '=', 'pm.package_id')
                ->join($this->config->get('tables.CURRENCIES').' as cur', function($join)
                {
                    $join->on('cur.currency_id', '=', 'pp.currency_id');
                })
                ->join($this->config->get('tables.AFF_PACKAGE_LANG').' as pl', function($subquery) use($arr)
                {
                    $subquery->on('pm.package_id', '=', 'pl.package_id')
                    ->where('pl.lang_id','=',$this->config->get('app.locale_id'));
                });
				
		if(isset($arr['package_level']) && !empty($arr['package_level']))
		{
			$pkg =$pkg->where('pm.package_level','>',$arr['package_level']); 
		}
		
		if(isset($arr['package_id']) && !empty($arr['package_id']))
		{
			$pkg =$pkg->where('pm.package_id','=',$arr['package_id']); 
		}
		
		$pkg = $pkg->where('pp.currency_id',$arr['currency_id'])
			->where('pm.status',$this->config->get('constants.ON'))
			->where('pm.is_deleted',$this->config->get('constants.OFF'))
			->where('pm.is_adjustment_package',$this->config->get('constants.OFF'))
			->select(DB::RAW('pm.package_id,pm.package_code	,pm.package_level,pm.is_refundable,pm.refundable_days,pm.expire_days ,pm.package_image,pm.is_upgradable, pm.is_adjustment_package,pm.instant_benefit_credit,cur.currency_id, pp.price, pp.package_qv,pp.weekly_capping_qv, pp.shopping_points, cur.currency as currency_code, pl.package_name, pl.description'))->latest('pm.created_on')->get();

		if(!empty($pkg))
		{
			array_walk($pkg, function(&$package) {
				if(!empty($package->package_image)) {
					$package->package_image_url =  url($package->package_image, [],true);
			  	}
			});
			return (isset($arr['list']) && !$arr['list'])? $pkg[0] : $pkg;
		}	
		return false;
	}
	
	public function get_mypackage($arr = array())
	{
		$pkg =  DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION').' as usub')
				->join($this->config->get('tables.AFF_PACKAGE_MST').' as pm', 'usub.package_id', '=', 'pm.package_id')
				->join($this->config->get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'usub.currency_id')
				->join($this->config->get('tables.AFF_PACKAGE_LANG').' as pl', function($subquery)use($arr)
                {
                    $subquery->on('pm.package_id', '=', 'pl.package_id')
                    ->where('pl.lang_id','=',$this->config->get('app.locale_id'));
                })
                ->where('usub.account_id',$arr['account_id'])				
                ->where('usub.is_deleted',$this->config->get('constants.OFF'))
				//->where('usub.status',$this->config->get('constants.ON'))			 
				->select(DB::RAW('usub.currency_id,cur.currency as currency_code, pl.package_name,usub.purchase_code,pm.package_image,usub.amount,usub.handle_amt,usub.paid_amt,usub.purchased_date,usub.is_refundable,usub.subscribe_id,usub.refund_expire_on,usub.package_qv,usub.weekly_capping_qv,usub.shopping_points,usub.is_upgradable,usub.package_level'))->latest('usub.purchased_date')->get();
		if(!empty($pkg))
		{
			array_walk($pkg, function(&$package)
		 	{			 
				if(!empty($package->package_image))
			  	{
					$package->package_image_url =  url($package->package_image, [],true);		
			  	}
			});
			return $pkg;
		}	
		return false;
	}
	
	public function upgrade_history($arr = array())
	{
		//$arr['subscrib_id'] = 2;
		$pkg =  DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP').' as usubtop')
		        ->join($this->config->get('tables.AFF_PACKAGE_MST').' as pm', 'usubtop.package_id', '=', 'pm.package_id')
				->join($this->config->get('tables.AFF_PACKAGE_LANG').' as pl', function($subquery)use($arr)
                {
                    $subquery->on('pm.package_id', '=', 'pl.package_id')
                    ->where('pl.lang_id','=',$this->config->get('app.locale_id'));
                })
                ->where('usubtop.account_id',$arr['account_id'])
				->where('usubtop.subscrib_id',$arr['subscrib_id'])
				
                ->where('usubtop.is_deleted',$this->config->get('constants.OFF'))
				//->where('usub.status',$this->config->get('constants.ON'))
			 
				->select(DB::RAW('usubtop.currency_id,usubtop.subscrib_topup_id,(SELECT curr.code FROM ' . $this->config->get('tables.CURRENCIES') . ' as curr WHERE curr.id = usubtop.currency_id) as currency_code,pl.package_name,pm.package_image,usubtop.amount,usubtop.handle_amt,usubtop.paid_amt,usubtop.topup_date,usubtop.is_refundable,usubtop.subscrib_id,usubtop.refund_expire_on,usubtop.package_qv,usubtop.weekly_capping_qv,usubtop.shopping_points,usubtop.package_level'))->latest('usubtop.subscrib_topup_id')->get();
		if(!empty($pkg))
		{
			array_walk($pkg, function(&$package)
			{			 
				if(!empty($package->package_image))
				{
					$package->package_image_url =  url($package->package_image, [],true);		
				}
			});
			return $pkg;
		}	
		return false;
	}
	
	public function purchase_paymodes(){
		$modes = $this->paymentObj->get_paymodes(['purpose'=>$this->config->get('constants.PAYMODE_PURPOSE_BUYPACKAGE')]);
		return ($modes)? $modes:NULL;
	}
	
	public function doPurchase($postdata = array()){		
		$proceed = true;
		$package_level = 0;
		$op = ['status'=>'err'];	
		if(isset($postdata['userSess']) && !empty($postdata['userSess']) && !empty($postdata['userSess']->account_id) && is_numeric($postdata['userSess']->account_id)){
			$userSess = $postdata['userSess'];
			if(isset($postdata['package_id']) && is_numeric($postdata['package_id']) && $postdata['package_id']>0 && !empty($postdata['paymode'])) {
				$pack_details = '';
				if(isset($arr['pack_details'])){
					$pack_details = $arr['pack_details'];
				}
				else {
					$pack_details = $this->get_packages([
						'list' => false,
						'package_id'=>$postdata['package_id'],
						'currency_id'=>$userSess->currency_id]);
				}	
				
				if($pack_details){	
			
					$payout = $this->paymentObj->get_paymodes(['purpose'=>$this->config->get('constants.PAYMODE_PURPOSE_BUYPACKAGE'),'payment_type_id'=>$postdata['paymode'],'list'=>false]);

					if (!empty($payout) && $payout->check_kyc_status)
					{
						$payout->kyc_settings = json_decode($payout->kyc_settings);				
						if ($payout->kyc_settings->currency == $userSess->currency_id && $pack_details->price >= $payout->kyc_settings->amount) {
							$op['msg'] = trans('affiliate/package/purchase.validate.kyc_required');
							$op['msgtype'] = 'warning';
							$proceed = false;
						}
					}					
					
					if ($proceed) {						
						$paymet_gateway_id = $postdata['paymode'];						
						$currency_id = $userSess->currency_id;
						$package_level = $pack_details->package_level;
						
						$sbdata = [
							'payment_gatway' => $paymet_gateway_id,
							'transaction_id' => AppService::getTransID($userSess->account_id),
							'order_type' => $this->config->get('constants.PACKAGE_NEW'),
							'pack_details' => $pack_details,
							'userSess' => $userSess];
						
						if($paymet_gateway_id==$this->config->get('constants.PAYMENT_TYPES.WALLET')){														
							$sbdata['pg_relation_id'] = $this->config->get('constants.WALLETS.PW');
							$op   = $this->add_subscription_topup($sbdata);							
						}
					}
				}
				else {
					$op['msg'] = trans('affiliate/package/purchase.validate.packmissing');
					$op['msgtype'] = 'danger';
				}
			}
			else {
				$op['msg'] = trans('affiliate/package/purchase.validate.packinvalide');
				$op['msgtype'] = 'danger';
			}
		}
		else {			
			$op['msg'] = trans('affiliate/package/purchase.validate.sess_exp');
			$op['msgtype'] = 'danger';
		}
		return $op;
	}
	
	public function add_subscription_topup($postdata=array()){
		$op = [];
		if($postdata){
			$subscribe_id = 0;				
			$pack_details = $postdata['pack_details'];
			$userSess = $postdata['userSess'];				
			$payment_gatway = $postdata['payment_gatway'];	
			$current_date = getGTZ();
			$transaction_id = AppService::getTransID($userSess->account_id);
			$purchase_currency = $userSess->currency_code;		
			
			$tpData = [
				'account_id'=>$userSess->account_id,
				'package_id'=> $pack_details->package_id,
				'package_level'=>$pack_details->package_level,
				'transaction_id'=>$transaction_id,
				'order_type'=> $postdata['order_type'],
				'payment_type'=> $payment_gatway,
				'currency_id'=> $pack_details->currency_id,
				'pg_relational_id'=> $postdata['pg_relation_id'],
				'amount'=> $pack_details->price,
				'handle_amt'=> 0,
				'paid_amt'=> $pack_details->price,
				'package_qv'=> $pack_details->package_qv,
				'weekly_capping_qv'=> $pack_details->weekly_capping_qv,
				'shopping_points'=> $pack_details->shopping_points,	
				'is_adjustment_package'=> $pack_details->is_adjustment_package,	
				'is_upgradable'=> $pack_details->is_upgradable,	
				'is_refundable'=> $pack_details->is_refundable,	
				'refundable_days'=> $pack_details->refundable_days,	
				'refund_expire_on'=> date('Y-m-d',strtotime(getGTZ().' '.$pack_details->refundable_days.' days')),
				'expire_days'=> $pack_details->expire_days,																					
				'create_date' => getGTZ(),
				'status' => $this->config->get('constants.PACKAGE_PURCHASE_STATUS_PENDING'),
				'payment_status' => $this->config->get('constants.PAYMENT_UNPAID')];
			
			if($payment_gatway == $this->config->get('constants.PAYMENT_TYPES.WALLET')){
				$postdata['wallet_id'] = $postdata['pg_relation_id'];
				$postdata['transaction_id'] = $postdata['transaction_id'];
				
				if(isset($data['usrbal_Info'])){
					$usrbal_Info = $data['usrbal_Info'];
				}
				else {				
					$usrbal_Info = $this->walletObj->account_balance(['account_id'=>$userSess->account_id,'currency_id'=> $userSess->currency_id,'wallet_id'=>$postdata['wallet_id']]);				
				}
				
				if ($usrbal_Info) {
					if ($usrbal_Info->current_balance >= $pack_details->price){
						$avail_balance = $usrbal_Info->current_balance;		
						
						$tpData['status'] = $this->config->get('constants.PACKAGE_PURCHASE_STATUS_PENDING');
						$tpData['payment_status'] = $this->config->get('constants.PAYMENT_PAID');
						
						
							
						$subscribe_topup_id = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP'))
									->insertGetId($tpData);
															
						$purchase_code = $this->config->get('constants.SUBSCRIBE_CODE_PREFIX').date('ym').$subscribe_topup_id;
						
						$tupRes = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP'))
									->where('subscribe_topup_id',$subscribe_topup_id)
									->update(['purchase_code'=>$purchase_code]);
								
						$postdata['status'] = $this->config->get('constants.PACKAGE_PURCHASE_STATUS_WAITFOR_ACTIVATE');	
						$postdata['payment_status'] = $this->config->get('constants.PAYMENT_PAID');				
						$postdata['purchase_code'] = $purchase_code;
						$postdata['subscribe_topup_id'] = $subscribe_topup_id;
						$postdata['usrbal_Info'] = $usrbal_Info;		
						
						$op = $this->save_subscription($postdata);															
					}
					else {
						$op['msg'] = trans('affiliate/package/purchase.validate.walletbal_insufficient');
						$op['msgtype'] = 'danger';
					}
				}
				else {
					$op['msg'] = trans('affiliate/package/purchase.validate.walletmissing');
					$op['msgtype'] = 'danger';
				}
			}	
			else {
				$op['msg'] = trans('affiliate/package/purchase.validate.paymode_missing');
				$op['msgtype'] = 'danger';
			}		
		}
		return $op;
	}
	
	
	public function save_subscription($postdata=array()){
	
		if($postdata && !empty($postdata['subscribe_topup_id'])){
		
			if(!empty($postdata['pack_details']) && !empty($postdata['userSess'])){
				$userSess = $postdata['userSess'];
				$pack_details = $postdata['pack_details'];				
				
								
				$existCnt = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION'))
									->where('purchase_code',$postdata['purchase_code'])
									->count();
								
				if($existCnt == 0) {					
					if(isset($postdata['usrbal_Info'])){
						$usrbal_Info = $postdata['usrbal_Info'];
					}
					else {				
						$usrbal_Info = $this->walletObj->account_balance(['account_id'=>$userSess->account_id,'currency_id'=> $userSess->currency_id,'wallet_id'=>$postdata['wallet_id']]);				
					}
					
					if ($usrbal_Info->current_balance >= $pack_details->price ) {
					
						$usrbal_Info = $this->walletObj->update_account_balance(array('wallet_id'=>$postdata['pg_relation_id'],'account_id'=>$userSess->account_id,'currency_id'=>$userSess->currency_id,'amount'=>$pack_details->price,'type'=>$this->config->get('constants.TRANSACTION_TYPE.DEBIT'),'return'=>'current'));
						
						$trans = '';					
						$trans['account_id']         = $userSess->account_id;
						$trans['statementline_id']   = 1;
						$trans['payment_type_id']    = $postdata['payment_gatway'];					
						$trans['amt']             	 = $pack_details->price;					
						$trans['handle_amt']         = 0;
						$trans['paid_amt']           = $pack_details->price;
						$trans['currency_id']        = $userSess->currency_id;
						$trans['wallet_id']          = $postdata['wallet_id'];
						$trans['transaction_id']     = $postdata['transaction_id'];
						$trans['transaction_type']   = $this->config->get('constants.TRANSACTION_TYPE.DEBIT');					
						$trans['relation_id']        = $postdata['subscribe_topup_id'];						
						$trans['remark']             = 'Package: '.$pack_details->package_name.', Code: '.$postdata['purchase_code'];
						$trans['created_on']         = getGTZ();
						$trans['current_balance']  	 = $usrbal_Info->current_balance;
						$trans['status']             = $this->config->get('constants.ACTIVE');		
						
						$transResID = DB::table($this->config->get('tables.ACCOUNT_TRANSACTION'))
									 ->insertGetId($trans);
						
						if($transResID>0){
							
							$current_date = getGTZ();
							$expire_date = date($this->config->get('constants.DB_DATE_FORMAT'), strtotime($current_date." +".$pack_details->expire_days." days"));					
							
							$scData = [];
							$scData['purchase_code'] = $postdata['purchase_code'];
							$scData['account_id'] = $userSess->account_id;
							$scData['package_id'] = $pack_details->package_id;
							$scData['package_level'] = $pack_details->package_level;
							$scData['transaction_id'] = $postdata['transaction_id'];
							$scData['payment_type'] = $postdata['payment_gatway'];
							$scData['pg_relation_id'] = $postdata['pg_relation_id'];
							$scData['currency_id'] = $userSess->currency_id;
							$scData['amount'] = $pack_details->price;
							$scData['handle_amt'] = 0;
							$scData['paid_amt'] = $pack_details->price;	
							$scData['is_adjustment_package'] = $pack_details->is_adjustment_package;
							$scData['is_upgradable'] = $pack_details->is_upgradable;
							$scData['is_refundable'] = $pack_details->is_refundable;
							
							if($pack_details->is_refundable==$this->config->get('constants.OFF') && $pack_details->instant_benefit_credit==$this->config->get('constants.ON')){
								$scData['package_qv'] = $pack_details->package_qv;
								$scData['weekly_capping_qv'] = $pack_details->weekly_capping_qv;
								$scData['shopping_points'] = $pack_details->shopping_points;		
							}
							else if($pack_details->is_refundable==$this->config->get('constants.ACTIVE')){
								$scData['refund_expire_on'] = date($this->config->get('constants.DB_DATE_FORMAT'), strtotime($current_date."+".$pack_details->refundable_days." ".$this->config->get('constants.REFUNDABLE_PERIODE_IN')));
							}									
			
							$scData['status'] = $postdata['status'];
							$scData['payment_status'] = $this->config->get('constants.PAYMENT_PAID');							
							$scData['purchased_date'] = $current_date;
							$scData['expire_on'] = $expire_date;
							$subscribe_id = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION'))
											->insertGetId($scData);
											
							if($subscribe_id){
								$tupRes = DB::table($this->config->get('tables.ACCOUNT_SUBSCRIPTION_TOPUP'))
												->where('subscribe_topup_id',$postdata['subscribe_topup_id'])
												->update([
													'subscribe_id'=>$subscribe_id,
													'status'=>$this->config->get('constants.PACKAGE_PURCHASE_STATUS_CONFIRMED'),
													'payment_status'=>$this->config->get('constants.PACKAGE_PURCHASE_STATUS_CONFIRMED'),
													'updated_date'=>getGTZ()]);		
								if($tupRes){
									if($userSess->can_sponsor == $this->config->get('constants.OFF')){
										$this->affObj->updateLineage($userSess,$pack_details);
									}								
									$op['status'] = 200;
									$op['msgtype'] = 'success';
									if($pack_details->is_refundable==$this->config->get('constants.OFF') && $pack_details->instant_benefit_credit==$this->config->get('constants.ON')){
										$op['msg'] = trans('affiliate/package/purchase.validate.success_with_benefit_credit',['package_name'=>$pack_details->package_name,'purchase_code'=>$postdata['purchase_code']]);
									}
									else {	
										$op['status'] = 200;
										$op['msg'] = trans('affiliate/package/purchase.validate.success_onhold_benefits',['package_name'=>$pack_details->package_name,'purchase_code'=>$postdata['purchase_code'],'refund_on'=>date('M d, Y',strtotime(showUTZ('Y-m-d'),$pack_details->refundable_days))]);
									}									
								}
								else {
									$op['msg'] = trans('affiliate/package/purchase.validate.package_purchase_incomplete');
									$op['msgtype'] = 'success';	
								}								
							}
							else {
								$op['msg'] = trans('affiliate/package/purchase.validate.package_purchase_incomplete');
								$op['msgtype'] = 'danger';
							}
						}
						else {
							$op['msg'] = trans('affiliate/package/purchase.validate.transaction_failed');
							$op['msgtype'] = 'danger';
						}
					}
					else {
						$op['msg'] = trans('affiliate/package/purchase.validate.walletbal_insufficient');
						$op['msgtype'] = 'danger';
					}
				}
				else {
					$op['msg'] = trans('affiliate/package/purchase.validate.package_purchase_ilegale');
					$op['msgtype'] = 'danger';
				}
			}
			else {
				$op['msg'] = trans('affiliate/package/purchase.validate.packmissing');
				$op['msgtype'] = 'danger';
			}
		}
		else {
			$op['msg'] = trans('affiliate/package/purchase.validate.packmissing');
			$op['msgtype'] = 'danger';
		}
		return $op;
	}	
	
	public function package_list()
	{
		$sql= DB::table($this->config->get('tables.AFF_PACKAGE_MST').' as pm')					
				->join($this->config->get('tables.AFF_PACKAGE_LANG') . ' as pl', function($join){
					$join->on('pl.package_id','=','pm.package_id');
					$join->where('pl.lang_id', '=', $this->config->get('app.locale_id'));
					$join->where('pm.status', '=', $this->config->get('constants.STATUS'));
				})
				->select('pm.package_id','pl.package_name')
				->where('pm.is_deleted',$this->config->get('constants.NOT_DELETED'));	
		
		$result = $sql->get();
		
		if(!empty($result)){
			return $result;
		}
		return NULL;
	}	
}
