<?php
namespace App\Models\Seller;
use DB;
use Illuminate\Database\Eloquent\Model;
use Config;

class Account extends Model
{   
	public function GetBussinessFilingStatus ()
    {	      
		return DB::table(Config::get('tables.BUSINESS_FILING_STATUS'))->get();       
    }
	public function UpdateGeneralDetails ($arr)
    {			
        extract($arr);        
		DB::table(Config::get('tables.ACCOUNT_DETAILS'))->where('account_id', '=', $account_id)->update($details);
		DB::table(Config::get('tables.ACCOUNT_MST'))->where('account_id', '=', $account_id)->update($amst);		
        return true;
    }
	public function UpdateBusinessDetails ($arr)
    {				
        extract($arr); 
	
		if(isset($business_status) && !empty($business_status) && $business_status==1){
			$op_address=$address;
		}
		else{
			$op_address=$operating_address;
		}
		$tag_ids = [];
		if(isset($tags) && !empty($tags)){
			$exitstags = $this->get_exists_tag($tags);
			$newtags = array_diff(explode(',',$tags),$exitstags);
			
			if(!empty($newtags)&& is_array($newtags)){
				foreach($newtags as $user_tag){
					$id = DB::table(config('tables.TAG'))
						   ->insertGetID(['tag_name'=>$user_tag,'tag_type'=>config('constants.TAG_TYPE.STORE')]);
					$tag_ids[] = $id;
				}
			}
			$store_tags = array_merge(array_keys($exitstags),$tag_ids);
			DB::table(config('tables.STORES'))
				->where('store_id',$primary_store_id)
				->update(['tags'=>implode(',',$store_tags)]);
		}
		DB::table(Config::get('tables.SUPPLIER_MST'))->where('supplier_id', '=', $supplier_id)->update($mrmst);
		
		DB::table(Config::get('tables.ADDRESS_MST'))->where('post_type', '=', 3)->where('relative_post_id', '=', $supplier_id)->update($address);   
		
		
		$adress_mst_check=DB::table(Config::get('tables.ADDRESS_MST'))
		                ->where('relative_post_id', $supplier_id)
				        ->where('address_type_id',2)
		                ->where('post_type',3)
				       ->first();
	   if($adress_mst_check > 0)
	   {
		   DB::table(Config::get('tables.ADDRESS_MST'))
	  	  ->where('post_type', '=', 3)
	  	  ->where('address_type_id', '=', 2)
		  ->where('relative_post_id', '=', $supplier_id)
		   ->update($op_address);
	   }
		else{
		    $op_address['post_type']=3;
	     	$op_address['relative_post_id']=$supplier_id;
		    $op_address['address_type_id']=2;
		 DB::table(Config::get('tables.ADDRESS_MST'))
							->insertGetID($op_address);
		}
        return true;
    }
	
	public function get_exists_tag($tags){
	
		$tags = explode(',',$tags);
		return DB::table(config('tables.TAG'))
			->where('is_deleted',0)
			->whereIn('tag_name',$tags)
			->lists('tag_name','tag_id');
		
	
	
	}
	
	public function UpdateBankDetails ($arr)
    {	
    	extract($arr);	
		$count = DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))
				->where('supplier_id', '=', $supplier_id)
				->where('is_deleted', '=', 0)->count();	
		if ($count == 0) {			
			$ps['payment_settings'] = json_encode($payment_setings);        
			$ps['supplier_id'] = $supplier_id;
			$ps['updated_by'] = $account_id;
			DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))->insertGetID($ps);  
		} else {
			$ps['payment_settings'] = json_encode($payment_setings);   
			$ps['updated_by'] = $account_id;
			DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))->where('supplier_id', '=', $supplier_id)->update($ps);
		}
        return true;
    }
	
	public function Update_Pickup_address($arr){
		extract($arr);	

		$check=DB::table(Config::get('tables.ADDRESS_MST'))
		         ->where('relative_post_id', $relative_post_id)
				 ->where('address_type_id',$address_type_id)
		         ->where('post_type', $post_type)
				 ->first();
		$address_mst['flatno_street']=$flat_no;
		$address_mst['landmark']=$address['landmark'];
		$address_mst['postal_code']=$address['postal_code'];
		$address_mst['city_id']=$address['city_id'];
		$address_mst['state_id']=$address['state_id'];
		$address_mst['country_id']=$country_id;
		$address_mst['email']	=(isset($address['email']))?$address['email']:null;
		$address_mst['phone_no']=(isset($address['phone_no']))?$address['phone_no']:null;
		if(count($check) >0){
		       DB::table(Config::get('tables.ADDRESS_MST'))
			                ->where('relative_post_id', $relative_post_id)
			                 ->where('post_type', $post_type)
							 ->where('address_type_id',$address_type_id)
							 ->update($address_mst);
		    }
		else{
			
		    $address_mst['post_type']=$post_type;
	     	$address_mst['relative_post_id']=$relative_post_id;
		    $address_mst['address_type_id']=$address_type_id;
			 DB::table(Config::get('tables.ADDRESS_MST'))
							->insertGetID($address_mst);
		   }
		return true;
	}
	
	public function pickup_address_check($relative_post_id,$address_type_id,$post_type){
	
		$check=DB::table(Config::get('tables.ADDRESS_MST'))
		         ->where('relative_post_id', $relative_post_id)
				 ->where('address_type_id',$address_type_id)
		         ->where('post_type', $post_type)
				 ->first();
		return $check;
	}
	
	public function location_details($relative_post_id,$address_type_id,$post_type){
		
		  $qry =DB::table(Config::get('tables.ADDRESS_MST').' as am')
                ->leftJoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'am.state_id', 'left')
                ->leftJoin(Config::get('tables.LOCATION_CITY').' as ld', 'ld.city_id', '=', 'am.city_id', 'left')
                ->where('am.relative_post_id', $relative_post_id)
                ->where('am.address_type_id',$address_type_id)
		        ->where('am.post_type', $post_type)
				->first();
		return $qry;
		
	}
	public function GetBankAccountDetails ($arr)
    {	    
		extract($arr);
		$payment_settings = DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))
				->where('supplier_id', '=', $supplier_id)
				->where('is_deleted', '=', 0)
				->value('payment_settings');
				
		if ($payment_settings) {
			return json_decode($payment_settings);
		} else {
			return false;
		}
    }
	
	public function old_password_check ($id)
    {
        return DB::table(Config::get('tables.ACCOUNT_MST'))
                        ->where('account_id', $id)
                        ->value('pass_key');                        
    }
	
	public function update_new_password ($password, $id)
    {
        $pass = md5($password);
        return DB::table(Config::get('tables.ACCOUNT_MST'))
                        ->where('account_id', $id)
                        ->update(array('pass_key'=>$pass));
    }
	
	public function update_retailer_settings (array $arr)
    {	
        extract($arr);
        $redeem_wallets = [];
        $update['redeem'] = Config::get('constants.OFF');
        if (isset($services['shop_and_earn']) && $services['shop_and_earn'] > 0)
        {
            $update['shop_and_earn'] = ($services['shop_and_earn'] > 0) ? Config::get('constants.ON') : Config::get('constants.OFF');
        }
        else
        {
            $update['shop_and_earn'] = Config::get('constants.OFF');
        }		
        if (isset($services['pay']) && $services['pay'] > 0)
        {
            $update['pay'] = ($services['pay'] > 0) ? Config::get('constants.ON') : Config::get('constants.OFF');
			$update['redeem'] = Config::get('constants.ON');
			$redeem_wallets[Config::get('constants.WALLETS.VIM')] = Config::get('constants.WALLETS.VIM');
			$redeem_wallets[Config::get('constants.WALLETS.VIS')] = Config::get('constants.WALLETS.VIS');
			$redeem_wallets[Config::get('constants.WALLETS.VIB')] = Config::get('constants.WALLETS.VIB');
        }
        else
        {
            $update['pay'] = Config::get('constants.OFF');
			$update['redeem'] = Config::get('constants.OFF');
			$redeem_wallets[Config::get('constants.WALLETS.VIM')] = Config::get('constants.OFF');
			$redeem_wallets[Config::get('constants.WALLETS.VIS')] = Config::get('constants.OFF');
			$redeem_wallets[Config::get('constants.WALLETS.VIB')] = Config::get('constants.OFF');
        }       
        $wallets = [];
        $wallet = [];		
        $getdata = DB::table(Config::get('tables.CASHBACK_SETTINGS'))
                ->where('supplier_id', $supplier_id)
                ->first();
        if (isset($getdata->member_redeem_wallets))
        {
            $wallets = explode(',', $getdata->member_redeem_wallets);
            if (!empty($redeem_wallets))
            {
                foreach ($redeem_wallets as $key => $wallet_id)
                {
                    if ($wallet_id != Config::get('constants.OFF'))
                    {
                        if (!in_array($wallet_id, $wallets))
                        {
                            $wallet[] = $wallet_id;
                        }
                        else
                        {
                            $wallet[] = $wallet_id;
                        }
                    }
                    else
                    {
                        if (in_array($key, $wallets))
                        {
                            unset($wallets[array_search($key, $wallets)]);
                        }
                    }
                }
            }
        }
        $update['member_redeem_wallets'] = !empty($wallet) ? implode(',', $wallet) : 0;				
        return DB::table(Config::get('tables.CASHBACK_SETTINGS'))
                        ->where('supplier_id', $supplier_id)
                        ->update($update);
    }
	
	public function getSetting ($key)
    {		
        return DB::table(Config::get('tables.SETTINGS'))
                        ->where('setting_key', 'like', '%'.$key.'%')
                        ->value('setting_value');
    }
	
	public function add_profit_sharing (array $arr)
    {	
        extract($arr);		
        $settings = $this->getSetting('seller_commissions_instore');
        if ($setting = json_decode($settings))
        {			
            if ($profit_sharing >= $setting->minimum_commission)
            {       
				$insData['cashback_on_shop_and_earn'] = $profit_sharing;
                $insData['cashback_on_pay'] = ($profit_sharing - $setting->minimum_commission) + 1;
                $insData['cashback_on_redeem'] = ($profit_sharing - $setting->minimum_commission) + 1;                				
				$insData['commision_type'] = 1;				
                $insData['profit_sharing'] = $profit_sharing;
                $insData['supplier_id'] = $supplier_id;
                //$insData['bcategory_id'] = $bcategory_id;
                $insData['created_on'] =date('Y-m-d H:i:s');
                $insData['created_by'] = $created_by;
                $insData['is_cashback_period'] = $is_cashback_period;
                $insData['cashback_start'] = isset($cashback_start) && !empty($is_cashback_period) ? $cashback_start : null;
                $insData['cashback_end'] = isset($cashback_end) && !empty($is_cashback_period) ? $cashback_end : null;
                $res = DB::table(Config::get('tables.PROFIT_SHARING'))->insertGetID($insData);       
				$add_offer['new_cashback'] = $insData['cashback_on_shop_and_earn'];
				$add_offer['old_cashback'] = 0;
				$add_offer['currency_id'] = $currency_id;
				$add_offer['country_id'] = $country_id;
				$add_offer['cashback_type'] = Config::get('constants.CASHBACK_TYPE.PERCENTAGE');
				$add_offer['cboffer_type'] = Config::get('constants.CBOFFER_TYPE.DISCOUNT');
				$add_offer['start_date'] = $insData['cashback_start'];
				$add_offer['end_date'] = $insData['cashback_end'];
				$add_offer['supplier_id'] = $supplier_id;
				$add_offer['status'] = 1;				
				DB::table(Config::get('tables.CASHBACK_OFFERS'))->insertGetID($add_offer);
                return true;
            }
        }
        return false;
    }
	
	public function SupplierCashbackSettings (array $arr = array())
    {
        extract($arr);
        $settings = DB::table(Config::get('tables.CASHBACK_SETTINGS'))
                ->where('supplier_id', '=', $supplier_id);
        if (isset($store_id) && !empty($store_id))
        {
            $settings->where(function($c) use($store_id)
            {
                $c->whereNotNull('store_id')->orWhere('store_id', $store_id);
            });
        }
        $settings = $settings->selectRaw('shop_and_earn, redeem, pay, member_redeem_wallets')->first();
        if (empty($settings))
        {
            $data = [];
            $data['supplier_id'] = $supplier_id;
            $data['store_id'] = (isset($store_id) && !empty($store_id)) ? $store_id : null;
            DB::table(Config::get('tables.CASHBACK_SETTINGS'))->insertGetID($data);
            $settings = (object) ['shop_and_earn'=>1, 'redeem'=>1, 'pay'=>1, 'member_redeem_wallets'=>'1,2,3,4,5'];
        }		
        if (!empty($settings))
        {
            $info = trans('general.supplier_cashback_settings');       			
            $info['pay']['value'] = $settings->pay;
            $info['shop_and_earn']['value'] = $settings->shop_and_earn;
            $settings->member_redeem_wallets = explode(',', $settings->member_redeem_wallets);			            
			
			$info['accept_vim']['value'] = (in_array(1, $settings->member_redeem_wallets)) ? 1 : 0;
            $info['accept_esp']['value'] = (in_array(2, $settings->member_redeem_wallets)) ? 1 : 0;
            $info['accept_bp']['value'] = (in_array(3, $settings->member_redeem_wallets)) ? 1 : 0;
            $info['accept_ngo']['value'] = (in_array(4, $settings->member_redeem_wallets)) ? 1 : 0;			
            $info['accept_pw']['value'] = (in_array(5, $settings->member_redeem_wallets)) ? 1 : 0;		
			
            $settings->current = DB::table(Config::get('tables.PROFIT_SHARING'))
                    ->where('supplier_id', '=', $supplier_id)
                    ->orderBy('sps_id', 'DESC')
                    ->where('status', '=', Config::get('constants.ON'));
            if (isset($store_id) && !empty($store_id))
            {
                $settings->current->where(function($c) use($store_id)
                {
                    $c->whereNotNull('store_id')->where('store_id', $store_id);
                });
            }
            $settings->current = $settings->current->selectRaw('profit_sharing, is_cashback_period, cashback_start, cashback_end')
                    ->first();

            if (!empty($settings->current))
            {
                $info['current_commission']['profit_sharing']['value'] = trans('general.supplier_cashback_settings.current_commission.profit_sharing.value', ['value'=>$settings->current->profit_sharing]);
                $info['current_commission']['period']['value'] = trans('general.supplier_cashback_settings.current_commission.period.value.'.$settings->current->is_cashback_period, ['from'=>showUTZ($settings->current->cashback_start), 'to'=>showUTZ($settings->current->cashback_end)]);
            }
            else
            {
                $info['current_commission'] = false;
            }

            $settings->pending = DB::table(Config::get('tables.PROFIT_SHARING'))
                    ->where('supplier_id', '=', $supplier_id)
                    ->whereIn('status', [0, 2, 4]);
            if (isset($store_id) && !empty($store_id))
            {
                $settings->pending->where(function($c) use($store_id)
                {
                    $c->whereNotNull('st.store_id')
                            ->where('store_id', $store_id);
                });
            }
            $settings->pending = $settings->pending->selectRaw('profit_sharing,is_cashback_period,cashback_start,cashback_end,status')
                    ->first();				
            if (!empty($settings->pending))
            {
                $info['pending_request']['status'] = $info['pending_request']['status'][$settings->pending->status];
                $info['pending_request']['profit_sharing']['value'] = trans('general.supplier_cashback_settings.pending_request.profit_sharing.value', ['value'=>$settings->pending->profit_sharing]);
                $info['pending_request']['period']['value'] = trans('general.supplier_cashback_settings.pending_request.period.value.'.$settings->pending->is_cashback_period, ['from'=>showUTZ($settings->pending->cashback_start, 'M d, Y'), 'to'=>showUTZ($settings->pending->cashback_end, 'M d, Y')]);
                $info['new'] = ($settings->pending->status == 0) ? false : 1;
            }
            else
            {
                $info['pending_request'] = false;
            }
        }
        return $info;
    }
	
    public function UpdateTax_info(array $arr = array()){ 

       extract($arr);
	     $insert_details['relative_post_id']=$relative_post_id;
		 $insert_details['post_type']=$account_type_id;
		 $insert_details['pan_card_no']=$pan_number;
		 $insert_details['pan_card_name']		=	$pan_name;
		 if(!empty($details['pan_card_image'])){
		     $insert_details['pan_card_image']	=	$details['pan_card_image'];
		  }
		 $insert_details['created_on']=getGTZ();
		 
		  
	     $check_tax_details =DB::table(Config::get('tables.ACCOUNT_TAX_DOCUMENTS'))
                        ->where('relative_post_id', $relative_post_id)
                        ->where('post_type', $account_type_id)
						->first();
	    if(count($check_tax_details) > 0){
			  DB::table(Config::get('tables.ACCOUNT_TAX_DOCUMENTS'))
                        ->where('relative_post_id', $relative_post_id)
						->where('post_type', $account_type_id)
                        ->update($insert_details);
		      }
		 else {
			 DB::table(Config::get('tables.ACCOUNT_TAX_DOCUMENTS'))->insertGetID($insert_details);
		 }
		 return true;
    }
	 
	 public function UpdateGstInfo(array $arr = array()){
		 
		  extract($arr);
		  $insert_details['relative_post_id']=$relative_post_id;
		  $insert_details['post_type']=$account_type_id;
		  if(isset($gstin_no)){
			 $insert_details['gstin_no']=$gstin_no;
		  }
		  if(isset($tan_no)){
			    $insert_details['tan_no']=$tan_no; 
		  }
		   if(isset($no_gstin)){
			 $insert_details['is_registered']=$no_gstin;
		   }
		  else{
			  $insert_details['is_registered']=1;
			  $insert_details['tax_class_id']=Config::get('constants.TAX_TYPES.CGST');
		   }
		
		 if(!empty($details['gstin_image'])){
		     $insert_details['tax_document_path']=$details['gstin_image'];
		  } 
		 if(!empty($details['tan_image'])){
		     $insert_details['tan_path']=$details['tan_image'];
		  } 
		   $check_tax_details =DB::table(Config::get('tables.ACCOUNT_TAX_DOCUMENTS'))
                        ->where('relative_post_id', $relative_post_id)
                        ->where('post_type', $account_type_id)
						->first();
	    if(count($check_tax_details) > 0){
			  DB::table(Config::get('tables.ACCOUNT_TAX_DOCUMENTS'))
                        ->where('relative_post_id', $relative_post_id)
						->where('post_type', $account_type_id)
                        ->update($insert_details);
		      }
		 else {
			 DB::table(Config::get('tables.ACCOUNT_TAX_DOCUMENTS'))->insertGetID($insert_details);
		 }
		 return true;
		 
	 }
	 public function UpdateProofDetails(array $arr = array()){
		  extract($arr);
		$check_details =DB::table(Config::get('tables.ACCOUNT_TAX_DOCUMENTS'))
                        ->where('relative_post_id', $relative_post_id)
                        ->where('post_type', $account_type_id)
						->first();
	  if(count($check_details) > 0){
		       if(!empty($proof_no)){ 
				       $proof_details['id_proof_no']=$proof_no;
				   }
			   if(!empty($id_proof_type)){
                  $proof_details['id_proof_document_type_id']=$id_proof_type;
				  }				
			   if(isset($details['id_proof'])){
						  $proof_details['id_proof_path']=$details['id_proof'];
				   }
		       if(!empty($address_proof_no)){
				   $proof_details['address_proof_no']=$address_proof_no;
			      }
			   if(!empty($address_proof_type)){    
					  $proof_details['address_proof_document_type_id']=$address_proof_type;
				   }
			   if(isset($details['address_proof'])){
						   $proof_details['address_proof_path']=$details['address_proof'];
				   }
			    $proof_details['updated_on']=getGTZ();
                 return DB::table(Config::get('tables.ACCOUNT_TAX_DOCUMENTS'))
                        ->where('relative_post_id', $relative_post_id)
						->where('post_type', $account_type_id)
                        ->update($proof_details);
		      }
			 else {
                     $proof_details['relative_post_id']=$relative_post_id;
		             $proof_details['post_type']=$account_type_id;
					 $proof_details['id_proof_no']=$proof_no;
                     $proof_details['id_proof_document_type_id']=$id_proof_type;
					 $proof_details['id_proof_path']=$details['id_proof'];
                     $proof_details['address_proof_no']=$address_proof_no;
					 $proof_details['address_proof_document_type_id']=$address_proof_type;
					 $proof_details['address_proof_path']=$details['address_proof'];
					return  DB::table(Config::get('tables.ACCOUNT_TAX_DOCUMENTS'))->insertGetID($proof_details);
			 }			 
			  
			  
	 }
	 
	 public function get_tax_information($relative_post_id,$post_type){
		 
		 $check_details =DB::table(Config::get('tables.ACCOUNT_TAX_DOCUMENTS'))
                        ->where('relative_post_id', $relative_post_id)
                        ->where('post_type', $post_type)
						->first();
		 return $check_details;
	 }
		public function get_IDProof(){
			
			$id_proof =DB::table(Config::get('tables.DOCUMENT_TYPES').' as dt')
			       ->where('dt.proof_type','=',Config::get('constants.DOCUMENT_TYPE.ID_PROOF'))
				   ->where('dt.status','=',Config::get('constants.ACTIVE'))
				   ->get();
				   return $id_proof;
		}
		
		public function get_proof_name($relative_post_id,$account_type_id){
			$query= DB::table(Config::get('tables.ACCOUNT_TAX_DOCUMENTS').' as ad')
			        ->join(Config::get('tables.DOCUMENT_TYPES') . ' as ud', 'ud.document_type_id', '=', 'ad.id_proof_document_type_id')
			         ->join(Config::get('tables.DOCUMENT_TYPES') . ' as dt', 'dt.document_type_id', '=', 'ad.address_proof_document_type_id')
                       ->where('relative_post_id', $relative_post_id)
						->where('post_type', $account_type_id)
						->select('ud.type as id_proof_name','dt.type as address_proof_name')
						->first();
				return $query;
		}
	  public function get_AdressProof(){
		  
		  $address_proof =DB::table(Config::get('tables.DOCUMENT_TYPES').' as dt')
			       ->where('dt.proof_type','=',Config::get('constants.DOCUMENT_TYPE.ADDRESS_PROOF'))
				    ->where('dt.status','=',Config::get('constants.ACTIVE'))
				   ->get();
				   
				return $address_proof;
	}
	
	public function CompletedSteps (array $arr = array())
    {	
	    extract($arr);
        return  DB::table(config::get('tables.SUPPLIER_MST'))
		            ->where('supplier_id', $supplier_id)
					->value('completed_steps');             
    }
	
	/* public function accountCreationSteps (array $arr = array())
    {	
        return  DB::table(config::get('tables.ACCOUNT_CREATION_STEPS'))
		            ->where('supplier_id', $supplier_id)
					->value('completed_steps');             
    } */

    public function UpdateCompletedSteps (array $arr = array())
    {	
        extract($arr);
        if ($account_type_id == config::get('constants.ACCOUNT_TYPE.SELLER'))
        {			
            $completed_steps = DB::table(config::get('tables.SUPPLIER_MST'))->where('supplier_id', $supplier_id)->value('completed_steps');
        }
        $completed_steps = !empty($completed_steps) ? explode(',', $completed_steps) : [];
        if (!in_array($current_step, $completed_steps))
        {
            $completed_steps[] = $current_step;
        }       
        $completed_steps = array_unique($completed_steps);

        /* $is_verified = !DB::table(config::get('tables.ACCOUNT_CREATION_STEPS'))
                        ->whereNotIn('step_id', $completed_steps)                        
                        ->where('account_type_id', $account_type_id)                        
                        ->orderby('priority', 'ASC')
                        ->exists(); */
		/* if(){
		
		} */
		$next = DB::table(Config::get('tables.ACCOUNT_CREATION_STEPS'))
                ->where('priority', '>', $current_step)
                //->havingRaw('min(priority)')
                ->orderby('priority')
                ->selectRaw('step_id, route')
                ->first();        
			
		$nextstep = !empty($next->step_id) ? $next->step_id : 0;	        
		if ($account_type_id == config::get('constants.ACCOUNT_TYPE.SELLER'))
		{
			$result = DB::table(Config::get('tables.SUPPLIER_MST'))->where('supplier_id', $supplier_id)
					->update(['completed_steps'=>implode(',', $completed_steps), 'updated_by'=>$account_id, 'next_step'=>$nextstep]);
			return (isset($next->route) && !empty($next->route)) ? \URL::route($next->route) : \URL::to('seller/dashboard');
		}                    
        return false;
    }
	
	public function Check_user_deetails(array $arr = array()){
		 extract($arr);
		 $query= DB::table(Config::get('tables.ACCOUNT_MST').' as am')
                  ->where('am.account_id','=',$account_id)
				  ->where('am.status','=',Config::get('constants.ACTIVE'))
				  ->where('am.is_deleted','=',Config::get('constants.OFF'))
				  ->first();
				  return $query;
	}

	
   public function saveProfilePIN (array $arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
                        ->update(['security_pin'=>md5($profile_pin)]);
    }

	public function get_store_tags($arr=[]){
		extract($arr);
		$qry = DB::table(config('tables.TAG'))
			->where('is_deleted',0)
			->where('tag_type',config('constants.TAG_TYPE.STORE'));
		if(isset($term) && !empty($term)){
			$qry->where('tag_name','like','%'.$term.'%');
		}
		$result 	= $qry->pluck('tag_name');
		
		return $result;
	}

	public function changeEmailID (array $arr = array())
    {
        extract($arr);
        if (DB::table(Config::get('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
                        ->update(['email'=>$new_email]))
        {
		
		     DB::table(Config::get('tables.ACCOUNT_PREFERENCE'))
                        ->where('account_id', $account_id)                     
                        ->update(['is_email_verified'=>Config::get('constants.INACTIVE')]);
            //$this->verifyEmailID($account_id);
            return true;
        }
        return false;
    }
	
	public function changeMobile(array $arr = array()){
		extract($arr);
        if (DB::table(Config::get('tables.ACCOUNT_MST'))
                        ->where('account_id', $account_id)
                        ->update(['mobile'=>$new_phone_no]))
        {
            return true;
        }
        return false;
	}
	
	public function verifyEmailID ($account_id)
    {	
        return DB::table(Config::get('tables.ACCOUNT_PREFERENCE'))
                        ->where('account_id', $account_id)
                        ->where('is_email_verified',Config::get('constants.INACTIVE'))
                        ->update(['is_email_verified'=>Config::get('constants.ACTIVE')]);
    }
	
	public function get_store_images($arr){
		extract($arr);
		return DB::table(config('tables.SUPPLIER_IMAGES'))
			->where('supplier_id',$suppiler_id)
			->select('file_path')
			->get($arr);
	}
	
	public function save_store_images($arr){
		return DB::table(config('tables.SUPPLIER_IMAGES'))
			->where('supplier_id',$arr['supplier_id'])
			->insertGetID($arr);
	}
	

}
