<?php

namespace App\Models\Affiliate;

use App\Models\BaseModel;
use DB;

class Payments extends BaseModel
{
	 public function __construct ()
    {
         parent::__construct(); 
		 $affObj = new AffModel();  
		 $this->applang = /* (Session::has('applang')) ? Session::get('applang') : */ 'en';
    }		
	
	public function get_paymodes($arr=array()){	
		extract($arr);
		$res = '';
		if(!empty($purpose)){
		 $qry = DB::table($this->config->get('tables.PAYMENT_TYPES'))
				   ->where('status',$this->config->get('constants.ACTIVE'))				   
				   ->select(DB::Raw('payment_type_id,payment_type,payment_key,description,image_name,check_kyc_status'));
		
			$qry->where($purpose,'=',$this->config->get('constants.ACTIVE'));
			$qry->orderBy('priority','asc'); 			
			
			if(isset($payment_type_id) && $payment_type_id>0) {
				$qry->where('payment_type_id','=',$payment_type_id);
				$res = $qry->first();				
			}
			else {
				$res = $qry->get();
			}			
		}
		return ($res)? $res : NULL;
	}
	
	
	
	public function get_currencies($arr=array()) {
		extract($arr);
        $qry = DB::table($this->config->get('tables.CURRENCIES'))                        
						->where('status','=',$this->config->get('constants.ACTIVE'))						
                        ->select('id as currency_id','currency_symbol','code as currency_code');	
		if(isset($currencies) && is_array($currencies)){
			$qry->whereIn('id',$currencies);
		}
		else if(isset($currency_id) && is_array($currency_id)){
			$qry->whereIn('id',$currency_id);
		}
								
		$res = $qry->get();		
        if (!empty($res) && count($res) > 0) {
            return $res;
        } 
        return false;
    }
	
	 
	 
}
