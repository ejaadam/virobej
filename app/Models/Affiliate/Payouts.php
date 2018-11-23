<?php

namespace App\Models\Affiliate;

use App\Models\BaseModel;
use DB;

class Payouts extends BaseModel
{
	public function __construct ()
    {
         parent::__construct(); 
		 $affObj = new AffModel();  
		 $this->applang = /* (Session::has('applang')) ? Session::get('applang') : */ 'en';
    }		
	
	public function getAccount_payoutsettings ($arr = array())
    {
        $account_id = 0;
        extract($arr);
      
        /* $res = DB::select("select pt.payment_type_id,pt.payment_type,ups.status as account_payout_status,ups.withdrawal_status,ups.acpayout_setting_id,ups.nick_name,ups.currency_id, ups.account_details from ".$this->config->get('tables.PAYMENT_TYPES')." as pt left join ".config ('tables.ACCOUNT_PAYOUT_SETTINGS' )." as ups on ups.payment_type_id = pt.payment_type_id and ups.account_id = '".$account_id."' where pt.status=1"); */
		
		$qry = DB::table($this->config->get('tables.PAYMENT_TYPES').' as pt')
                ->leftjoin($this->config->get('tables.ACCOUNT_PAYOUT_SETTINGS').' as aps', function($join) use($account_id){
				$join->on('pt.payment_type_id','=','aps.payment_type_id');
				$join->where('aps.account_id','=',$account_id);
				$join->where('pt.status', '=' ,$this->config->get('constants.ACTIVE'));
				});
				$qry ->join($this->config->get('tables.PAYOUT_TYPES_LANG').' as ptl', 'ptl.payment_type_id', ' = ', 'pt.payment_type_id');	
				$qry ->select('pt.payment_type_id','ptl.payment_type','aps.status as status','aps.withdrawal_status','aps.acpayout_setting_id','aps.nick_name','aps.currency_id','aps.account_details','aps.is_approved');
				$res = $qry ->get();
                
        return ($res) ? $res : false;
		
    }
	public function getBank_accouttypes_lang ($arr = array())
    {
        extract($arr);
      
		
		 $res= DB::table($this->config->get('tables.BANK_ACCOUNT_TYPES').' as bat')
                        ->join($this->config->get('tables.BANK_ACCOUNT_TYPES_LANG').' as batl', function($subquery)
                        {
                            $subquery->on('batl.bank_account_type_id', '=', 'bat.bank_account_type_id')
                            ->where('batl.lang_id', '=',$this->config->get('app.locale_id'));
                        })
                        ->where('bat.payment_type_id', $payment_type_id)
                        ->select('batl.accounttype_name','bat.bank_account_type_id as bank_account_id')
						->get();
		
		return ($res) ? $res : false;
    }
	public function get_editable_banks ($arr = array())
    {
        $upres = DB::table($this->config->get('tables.ACCOUNT_WITHDRAWAL'))
                ->where('account_id', '=' ,$arr['account_id'])
                ->where('payment_type_id', '=' ,$this->config->get('constants.BANK_TRANSFER'))
                ->where('status','=' ,$this->config->get('constants.STATUS_CONFIRMED'))
                ->where('is_deleted', '=' ,$this->config->get('constants.OFF'))
                ->lists('acpayout_setting_id');
        return $upres;
    }
	public function account_payout_settings_update ($sdata, $wdata, $acdet)
    {
        $upres =false;
		$msg = '';
        if ($sdata['account_id'] > 0 )
        {
			$res = DB::table($this->config->get('tables.ACCOUNT_PAYOUT_SETTINGS'))
                    ->where($wdata)
                    ->count();
			if ($res > 0)
            {
                $upres = DB::table($this->config->get('tables.ACCOUNT_PAYOUT_SETTINGS'))
                        ->where($wdata)
                        ->update($sdata);
				$msg = trans('affiliate/settings/payout_settings_controller.info_update') ;
			}
            else
            {
                $upres = DB::table($this->config->get('tables.ACCOUNT_PAYOUT_SETTINGS'))
                        ->insertGetId($sdata);
				$msg =  trans('affiliate/settings/payout_settings_controller.info_add') ;
			}                     
        }
		$op['status'] = $upres;
		$op['msg'] 		= $msg;
        return $op;
    }	 
}
