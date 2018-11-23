<?php
namespace App\Models\Seller;
use DB;
use Illuminate\Database\Eloquent\Model;
use Config;
use CommonLib;


class ManageUsers extends Model
{
  public function get_user_list(array $arr = array(), $count = false){
  //return $arr;
		$from = 0;
        $to = 0;
        $ewallet_id = 0;
        $user_id = 0;
        $display_id = 0;
        $payout_type = 0;
        extract($arr);
        $user_list = DB::table(Config('tables.STORE_EMPLOYEES').' as sc')
		                    ->join(Config('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'sc.account_id')
				            ->join(Config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
				             ->join(Config('tables.ACCESS_LEVEL_LOOKUP').' as acl', 'acl.access_id', '=', 'sc.access_level')
				            ->join(Config('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'am.account_id')
							->leftJoin(Config('tables.ADDRESS_MST').' as adm', 'adm.relative_post_id', '=', 'sc.account_id')
							->where('sc.supplier_id','=',$supplier_id);

		if (isset($search_term) && !empty($search_term))
        {
           
            $user_list->where(function($sub) use($search_term)
            {
                $sub->where('am.email', 'LIKE', '%'.$search_term.'%');
                        /* ->orwhere('st.statementline', 'LIKE', '%'.$search_term.'%')
                        ->orwhere('a.transaction_id', 'LIKE', '%'.$search_term.'%'); */
            });
        }
        if($count)
        {
            return $user_list->count();
        }
        else
        {
			
            if ((isset($start)) && (isset($length)))
            {
                $user_list->skip($start)->take($length);
            }
            $trans = $user_list->selectRaw('am.account_id as user_account_id,am.uname,am.email,am.mobile,am.login_block,am.user_code,am.status as user_status,acl.access_name,sc.status as employee_status,CONCAT_WS(\' \',ad.firstname,ad.lastname) as full_name,sc.account_id')->get();
		
		   array_walk($trans, function(&$users)
            {

		    if($users->employee_status==config::get('constants.ACTIVE')) {
               $users->emp_status = trans('seller/account.user_status.status.'.$users->employee_status);
		     }
			else{
				$users->emp_status = trans('seller/account.user_status.status.'.$users->employee_status);
			} 
			  $users->status_class = config('dispclass.transaction.status.'.$users->user_status);
			  $users->actions 		= [];
			   $users->actions[] 	= ['url'=>route('seller.manage_users.edit_user', ['account_id'=>$users->user_account_id]),'class'=>'edit','label'=>'Edit'];	
			    $users->actions[] 	= ['url'=>route('seller.manage_users.get_stores',['account_id'=>$users->user_account_id]),'class'=>'allocation','label'=>'Store Allocate'];	
			 if ($users->login_block == 1) {
                   $users->actions[] = ['url'=>route('seller.manage_users.login_block', ['account_id'=>$users->user_account_id, 'status'=>'unblock']),'class'=>'login_block', 'data'=>[
                        'account_id'=>$users->user_account_id,
						'status'=>'unblock'
                    ],'label'=>trans('seller/account.login_unblock')];							   
                 }
                 if($users->login_block == 0) {
                    $users->actions[] = ['url'=>route('seller.manage_users.login_block', ['account_id'=>$users->user_account_id, 'status'=>'block']),'class'=> 'login_block', 'data'=>[
                        'account_id'=>$users->user_account_id,
						'status'=>'block'
                    ],'label'=>trans('seller/account.login_block')];		   
                }
				 if ($users->employee_status == Config::get('constants.MANAGE_USER.STATUS.INACTIVE'))
                    {
                        $users->actions[] = ['url'=>route('seller.manage_users.update-status', ['id'=>$users->account_id, 'status'=>strtolower('ACTIVE')]),'class'=>'user_status', 'data'=>[
                        'account_id'=>$users->account_id,
						'status'=>strtolower('ACTIVE'),
                        ],'label'=>'Activate'];
                    }
                    elseif ($users->employee_status == Config::get('constants.MANAGE_USER.STATUS.ACTIVE'))
                    {
                        $users->actions[] = ['url'=>route('seller.manage_users.update-status', ['id'=>$users->account_id, 'status'=>strtolower('INACTIVE')]),'class'=>'user_status', 'data'=>[
                        'account_id'=>$users->account_id,
						'status'=>strtolower('INACTIVE'),
                        ],'label'=>'Inactivate '];
                    } 
				$users->actions[] = ['url'=>route('seller.manage_users.reset-password',['account_id'=>$users->user_account_id]), 'class'=>'reset-password', 'data'=>[
                        'account_id'=>$users->user_account_id,
						'fullname'=>$users->full_name,
						'email'=>$users->email,
                    ], 'redirect'=>false, 'label'=>trans('seller/account.reset-password.reset_password')];
				
          });
            return $trans;
        } 
	}
	
	
	public function save_user($arr){
	
		extract($arr);
		$password = md5(rand('000000','999999'));
		$fullname = explode(' ',$full_name);
			$fist_name = $fullname[0];
			unset($fullname[0]);
			$last_name = (is_array($fullname) && !empty($fullname))?implode(' ',$fullname):null;
		if(!empty($account_id)){
			DB::table(config('tables.ACCOUNT_MST'))
		              ->where('account_id','=',$account_id)
					  ->update(['email'=>$email,'mobile'=>$mobile,'uname'=>$username]);
					  
					  DB::table(config('tables.ACCOUNT_DETAILS'))
					      ->where('account_id','=',$account_id)
						  ->update(['firstname'=>$fist_name,'lastname'=>$last_name,'updated_on'=>getGTZ()]);

					   DB::table(config('tables.STORE_EMPLOYEES'))
					  ->where('account_id','=',$account_id)
					  ->update(['access_level'=>$role,'status'=>isset($status)?$status:0,'updated_on'=>getGTZ()]);	 
                         return true;			  
		}
		else{
			
		  $user_account_id = DB::table(config('tables.ACCOUNT_MST'))
					  ->insertGetId(['email'=>$email,'account_type_id'=>$account_type,'mobile'=>$mobile,'uname'=>$username,'pass_key'=>$password]);
		    if(!empty($user_account_id)){
						 DB::table(config('tables.ACCOUNT_DETAILS'))
						->insertGetId(['account_id'=>$user_account_id,'firstname'=>$fist_name,'lastname'=>$last_name,'updated_on'=>getGTZ()]);
						
						  DB::table(config('tables.STORE_EMPLOYEES'))
						  ->insertGetId(['supplier_id'=>$supplier_id,'access_level'=>$role,'account_id'=>$user_account_id,'status'=>isset($status)?$status:0,'created_on'=>date('Y-m-d H:i:s'),'updated_on'=>getGTZ()]);
							
						DB::table(config('tables.ACCOUNT_PREFERENCE'))
						->insertGetId(['account_id'=>$user_account_id,'language_id'=>$language_id,'country_id'=>$country_id,'currency_id'=>$currency_id]);
				return $user_account_id;
		    }
		
		}
		return false;

	}
  public function user_block_login (array $data = array())
     {
        $op = array();
        extract($data);
        if (isset($status) && $status == 1)
        {
            $query= DB::table(config('tables.ACCOUNT_MST'))
                            ->where('is_deleted',config::get('constants.NOT_DELETED'))
                            ->where('account_type_id',config::get('constants.ACCOUNT_TYPE.SELLER')) 
                           ->where('account_id', $account_id) 
							->update(['login_block'=>config::get('constants.ON')]);
							if(!empty($query)){
					         	return json_encode(array(
						  	    'status'=>200,
						        'msg'=>trans('seller/account.user_block'),
						        'alertclass'=>'alert-success'));
							}
        }
        else
        {
            $query_unblock= DB::table(config('tables.ACCOUNT_MST'))
                            ->where('is_deleted',config::get('constants.NOT_DELETED'))
                            ->where('account_type_id',config::get('constants.ACCOUNT_TYPE.SELLER'))
                            ->where('account_id', $account_id)
                            ->update(['login_block'=>config::get('constants.OFF')]);
						if(!empty($query_unblock)){
					     	 return json_encode(array(
							 'status'=>200,
						     'msg'=>trans('seller/account.user_unblock'),
						     'alertclass'=>'alert-success'));
							}
        }
    }

	
	public function get_user_account_details($arr){
		
		extract($arr);
        $qry = DB::table(Config('tables.ACCOUNT_MST').' as am')
					->join(Config('tables.STORE_EMPLOYEES').' as sc','sc.account_id','=','am.account_id')
					->join(Config('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
					->join(Config('tables.ACCESS_LEVEL_LOOKUP').' as acl', 'acl.access_id', '=', 'sc.access_level')
					->join(Config('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'am.account_id')
					->join(Config('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ap.country_id')
					->where('sc.supplier_id',$supplier_id)
					->where('am.account_id',$account_id)
					->where('am.is_deleted',0)
					->selectRaw('am.uname,am.account_id,am.email,am.mobile,lc.country,lc.country_id,sc.access_level,sc.status,CONCAT_WS(\' \',ad.firstname,ad.lastname) as full_name')
					->first();
		if(!empty($qry)){
		/* 	$qry->status = trans('general.seller.status.'.$qry->status); */
			return $qry;
		}
		return false;
	}

	
	public function update_password ($postdata)
    {
		$op=[];
		if($postdata['reset_user_account_id']>0 && !empty(trim($postdata['new_pwd'])))
		{
			$data['pass_key'] = md5($postdata['new_pwd']);
		   
			if ($data['pass_key'] != DB::table(config('tables.ACCOUNT_MST'))
							->where('account_id',$postdata['reset_user_account_id'])
							->value('pass_key'))
			   { 
				$status = DB::table(config('tables.ACCOUNT_MST'))
					->where('account_id',$postdata['reset_user_account_id'])
					->update($data);
				if (!empty($status) && isset($status))
				{
					   	$status=config::get('httperr.SUCCESS');
						/* 'uname'=>$postdata['full_name']; */
						$msg=trans('admin/affiliate/settings/changepwd.password_changed');
						 return array('msg'=>$msg,'status'=>$status);
				}
				else
				{
						$msg =trans('general.something_wrong');
						 $status=config::get('httperr.UN_PROCESSABLE');
						 return array('msg'=>$msg,'status'=>$status);
				}
			  }
			else{
				$msg = trans('admin/affiliate/settings/changepwd.same_as_old');
				 $status= config::get('httperr.UN_PROCESSABLE');
				 return array('msg'=>$msg,'status'=>$status);
			}
		}
        return json_encode(array('msg'=>trans('admin/affiliate/settings/changepwd.missing_parameters'), 'alertclass'=>'alert-warning'));
    }
	
	public function get_supplierStores (array $arr = array())
    {
        extract($arr);
        $qry = DB::table(Config('tables.STORES').' as st')
					->join(Config('tables.SUPPLIER_MST').' as sm','sm.supplier_id','=','st.supplier_id')
					->join(Config('tables.ADDRESS_MST').' as ad',function($q){
						$q->on('ad.address_id','=','st.address_id')	
							->where('ad.address_type_id','=',1);
					})
					->leftJoin(Config('tables.STORE_EMPLOYEE_ASSOCIATE').' as ems',function($k)use($account_id){
						$k->on('ems.relation_id','=','st.store_id')
						  ->where('ems.account_id','=',$account_id)
						  ->where('ems.is_deleted','=',0);
					})
					->where('sm.supplier_id',$supplier_id)
					->select('ems.id','st.store_id','st.store_code','st.store_name','ad.address','ems.status','ems.id');
			$stores = 	$qry->get();
		
			array_walk($stores, function(&$store)
			{
				$store->status = $store->status ? true : false;
				$store->id = !empty($store->id) ? $store->id : '';
				$store->address = !empty($store->address) ? $store->address : '';
			});
        return $stores;
    }
	
	public function saveAdminStores (array $arr = array())
    {
		//print_R($arr);exit;
        extract($arr);
        DB::table(Config('tables.STORE_EMPLOYEE_ASSOCIATE'))
                ->where('account_id', $id)
                ->where('post_type', '=', Config('constants.TYPE_STORE'))
                ->where('is_deleted', '=', Config('constants.OFF'))
                ->update(['is_deleted'=>Config('constants.ON'), 'updated_by'=>$account_id, 'updated_on'=>getGTZ()]);
        if (isset($stores) && !empty($stores))
        {
            foreach ($stores as $store)
            {
                $res = false;
                $store['status'] = isset($store['status']) && !empty($store['status']) ? Config('constants.ON') : Config('constants.OFF');
                $store['updated_on'] = getGTZ();
                $store['updated_by'] = $account_id;
                $store['relation_id'] = $store['store_id'];
                unset($store['store_id']);
                if (DB::table(Config('tables.STORE_EMPLOYEE_ASSOCIATE').' as aa')
                                ->where('aa.account_id', $id)
                                ->where('aa.relation_id', $store['relation_id'])
                                ->where('aa.post_type', '=', Config('constants.TYPE_STORE'))
                                ->exists())
                {
                    $store['is_deleted'] = Config('constants.OFF');
                    $res = DB::table(Config('tables.STORE_EMPLOYEE_ASSOCIATE').' as aa')
                            ->where('aa.account_id', $id)
                            ->where('aa.relation_id', $store['relation_id'])
                            ->where('aa.post_type', '=', Config('constants.TYPE_STORE'))
                            ->where('aa.is_deleted', Config('constants.ON'))
                            ->update($store);
                }
                else
                {
                    $store['post_type'] = Config('constants.TYPE_STORE');
                    $store['created_date'] = getGTZ();
                    $store['created_by'] = $account_id;
                    $store['account_id'] = $id;
                    $res = DB::table(Config('tables.STORE_EMPLOYEE_ASSOCIATE'))
                            ->insertGetID($store);
                }
            }
        }
        return true;
    }
   public function updateManageUserStatus (array $arr = array())
     {
        extract($arr);
        $user = [];
        $user['updated_on'] = getGTZ();
        $user['status'] = $status;
        $query = DB::table(Config::get('tables.STORE_EMPLOYEES'))
                ->where('account_id', $id);
        if ($status == Config::get('constants.MANAGE_USER.STATUS.ACTIVE'))
        {
            $query->where('status', Config::get('constants.MANAGE_USER.STATUS.INACTIVE'));
        }
        elseif ($status == Config::get('constants.MANAGE_USER.STATUS.INACTIVE'))
        {
            $query->where('status', Config::get('constants.MANAGE_USER.STATUS.ACTIVE'));
        }
        return $query->update($user); 
    }
}