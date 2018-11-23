<?php
namespace App\Models\Admin;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\ImageLib;
use Config;
use URL;
use App\Helpers\ShoppingPortal;

class Suppliers extends Model
{

    public function __construct (&$commonObj)
    {
        $this->commonObj = $commonObj;
    }
	
	public function store_list (array $arr = array(), $count = false)
    {		
        extract($arr);
        $qry = DB::table(Config::get('tables.STORES').' as msm')
                ->join(Config::get('tables.STORES_EXTRAS').' as se', 'se.store_id', '=', 'msm.store_id')
                ->join(Config::get('tables.SUPPLIER_MST').' as mm', 'mm.supplier_id', '=', 'msm.supplier_id')
				 ->leftjoin(Config::get('tables.BUSINESS_CATEGORY').' as bc', function($subquery)
                {
                    $subquery->on('bc.bcategory_id', '=', 'mm.category_id')
							 ->where('bc.is_deleted', '=', Config::get('constants.NOT_DELETED'));
                })
                ->leftjoin(Config::get('tables.BUSINESS_CATEGORY_LANG').' as bcl', function($subquery)
                {
                    $subquery->on('bcl.bcategory_id', '=', 'mm.category_id')
							 ->where('bcl.lang_id', '=', Config::get('app.locale_id'));
                })              
                //->join(Config::get('tables.MERCHANT_SETTINGS').' as ms', 'ms.mrid', '=', 'msm.mrid')				
                ->join(Config::get('tables.ADDRESS_MST').' as am', function($join)
                {
                    $join->on('am.address_id', '=', 'msm.address_id');
                })
                ->join(Config::get('tables.LOCATION_COUNTRY').' as loc', 'loc.country_id', '=', 'am.country_id')
                ->leftjoin(Config::get('tables.ACCOUNT_MST').' as acm', 'acm.account_id', '=', 'msm.updated_by')
                ->leftjoin(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'msm.updated_by')            
                ->where('mm.block', Config::get('constants.OFF'))
                ->where('mm.is_deleted', Config::get('constants.OFF')) 
                ->where('msm.is_deleted', Config::get('constants.OFF'));
				
		if (isset($id) && !empty($id))
        {			
            $qry->where('mm.supplier_code', 'LIKE', '%'.$id.'%');
        }
        /* if (isset($mrcode) && !empty($mrcode))
        {
            $qry->where('mm.mrcode', $mrcode);
        } */
        if (isset($from) && !empty($from))
        {
            $qry->whereDate('mm.created_on', '<=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $qry->whereDate('mm.created_on', '>=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($search_term) && !empty($search_term))
        {
           /*  $search_term = '%'.$search_term.'%';
            $qry->where(function($wcond) use($search_term)
            {
                $wcond->Where('mm.mrcode', 'like', $search_term)
                        ->orwhere('mm.mrbusiness_name', 'like', $search_term)
                        ->orwhere('msm.store_code', 'like', $search_term)
                        ->orwhere('msm.store_name', 'like', $search_term);
            }); */
        }        
        if ($count)
        {
            return $qry->count();
        }
        else
        {
            if (isset($orderby) && $orderby != 'order_date' && isset($order))
            {
                $qry->orderby($orderby, $order);
            }
            else
            {
                $qry->orderby('msm.store_id', 'DESC');
            }
            if (isset($length) && $length > 0)
            {
                $qry->skip($start)->take($length);
            }
            $qry->select('msm.supplier_id as mrid', 'mm.completed_steps', 'msm.category_id', 'msm.store_id', 'msm.store_name', 'bcl.bcategory_name as category_name', 'am.address as formated_address', 'am.geolat as lat', 'am.geolng as lng', 'loc.phonecode', 'msm.store_logo as logo', 'se.mobile_no as mobile', 'msm.store_code', 'msm.status', 'msm.is_approved', 'mm.company_name as mrbusiness_name', 'mm.supplier_code as mrcode', 'mm.logo as mrlogo', DB::raw('CONCAT(ad.firstname,\' \',ad.lastname) as updated_by_full_name'), 'acm.uname as updated_by_uname', 'msm.created_on', DB::raw('(select count(store_id) from '.Config::get('tables.STORES').' where supplier_id=msm.supplier_id and is_deleted=0) as stores_count'),'msm.is_premium');
            $stores = $qry->get();	
            $total_steps = DB::table(Config::get('tables.ACCOUNT_CREATION_STEPS'))
                    //->where('post_type', Config::get('constants.POST_TYPE.STORE'))
                    ->where('account_type_id', Config::get('constants.ACCOUNT_TYPE.SELLER'))
                    ->where('status', Config::get('constants.ON'))
                    ->count();
					
            array_walk($stores, function(&$store, $key) use($total_steps)
            {
                $store->mrlogo = Config::get('path.MERCHANT.LOGO_PATH.DEFAULT');
                $store->created_on = (!empty($store->created_on) && $store->created_on != '0000-00-00 00:00:00') ? showUTZ($store->created_on, 'd-M-Y H:i:s') : '';
                $store->completed_steps = !empty($store->completed_steps) ? count(explode(',', $store->completed_steps)) : 0;
                if (empty($store->logo))
                {
                    $store->logo = asset(Config::get('constants.MERCHANT.LOGO_PATH.SM').$store->mrlogo);
                }
                else
                {
                    $store->logo = asset(Config::get('constants.STORE_LOGO_PATH.SM').$store->logo);                    
                }
				$store->logo = Config::get('path.MERCHANT.LOGO_PATH.DEFAULT');
                $store->address_url = str_replace(['lat', 'lng'], [$store->lat, $store->lng], Config::get('constants.MAP_URL'));
                $store->status_class = trans('admin/general.seller.status_class.'.$store->status);
           /*      $store->actions['view'] = ['url'=>route('admin.retailers.stores.details', ['for'=>'view', 'code'=>$store->store_code]), 'redirect'=>false, 'label'=>trans('general.btn.view')];
                $store->actions[] = ['url'=>route('admin.retailers.stores.edit-outlet', ['store_code'=>$store->store_code]), 'label'=>trans('general.btn.edit')];
                if ($store->status == Config::get('constants.MERCHANT.STORE.STATUS.INACTIVE'))
                {
                    $store->actions[] = ['url'=>route('admin.retailers.stores.update-status', ['store_code'=>$store->store_code, 'status'=>strtolower('ACTIVE')]), 'redirect'=>false, 'label'=>trans('general.btn.activate')];
                }
                else if ($store->status == Config::get('constants.MERCHANT.STORE.STATUS.ACTIVE'))
                {
                    $store->actions[] = ['url'=>route('admin.retailers.stores.update-status', ['store_code'=>$store->store_code, 'status'=>strtolower('INACTIVE')]), 'redirect'=>false, 'label'=>trans('general.btn.deactivate')];
                }
                if ($store->is_approved == Config::get('constants.MERCHANT.STORE.IS_APPROVED.APPROVED'))
                {
                    $store->actions[] = ['url'=>route('admin.retailers.stores.update-is-approved', ['store_code'=>$store->store_code, 'status'=>strtolower('REJECTED')]), 'redirect'=>false, 'label'=>trans('general.btn.reject')];
                }
                else if ($store->is_approved == Config::get('constants.MERCHANT.STORE.IS_APPROVED.NOT_APPROVED'))
                {
                    if ($store->completed_steps == $total_steps)
                    {
                        $store->actions[] = ['url'=>route('admin.retailers.stores.update-is-approved', ['store_code'=>$store->store_code, 'status'=>strtolower('APPROVED')]), 'redirect'=>false, 'label'=>trans('general.btn.approve')];
                        $store->actions[] = ['url'=>route('admin.retailers.stores.update-is-approved', ['store_code'=>$store->store_code, 'status'=>strtolower('REJECTED')]), 'redirect'=>false, 'label'=>trans('general.btn.reject')];
                    }
                }
                elseif ($store->is_approved == Config::get('constants.MERCHANT.STORE.IS_APPROVED.REJECTED'))
                {
                    if ($store->completed_steps == $total_steps)
                    {
                        $store->actions[] = ['url'=>route('admin.retailers.stores.update-is-approved', ['store_code'=>$store->store_code, 'status'=>strtolower('APPROVED')]), 'redirect'=>false, 'label'=>trans('general.btn.approve')];
                    }
                }
                if ($store->stores_count > 1)
                {
                    $store->actions[] = ['url'=>route('admin.retailers.stores.delete', ['store_code'=>$store->store_code]), 'redirect'=>false, 'label'=>trans('general.btn.delete')];
                }
                $store->actions[] = ['url'=>route('admin.retailers.stores.images.list', ['store_code'=>$store->store_code]), 'redirect'=>true, 'label'=>trans('general.btn.outlet_imgs')]; */
				
				if ($store->is_premium == Config::get('constants.ON'))
                {
                        $store->actions[] = ['url'=>route('admin.seller.update_premium', ['store_code'=>$store->store_code, 'status'=>'0']), 'redirect'=>false, 'label'=>'Deactive Premium'];
                }else{
					 $store->actions[] = ['url'=>route('admin.seller.update_premium', ['store_code'=>$store->store_code, 'status'=>'1']),'redirect'=>false, 'label'=>'Active Premium'];
				}

                $store->is_approved_class = trans('admin/general.seller.stores.approval_status_class.'.$store->is_approved);
                $store->status = trans('admin/general.seller.status.'.$store->status);
                $store->is_premium = trans('admin/general.seller.is_premium.'.$store->is_premium);
                $store->completed_steps .='/'.$total_steps;
                $store->is_approved = trans('admin/general.seller.stores.is_approved.'.$store->is_approved).(!$store->is_approved ? ' ('.$store->completed_steps.')' : '');
                unset($store->lat);
                unset($store->lng);
                unset($store->completed_steps);
            });
            return $stores;
        }
    }
	
	public function admin_info (array $data)
    {
        extract($data);
        if (isset($supplier_code) && !empty($supplier_code))
        {
            $query = DB::table(Config::get('tables.ADMIN_ASSOCIATE').' as aso')
                    ->join(Config::get('tables.STORES').' as mst', function($chk_store)
                    {
                        $chk_store->on('mst.store_id', '=', 'aso.relation_id')
							//->where('aso.post_type', '=', Config::get('constants.POST_TYPE.STORE'));
							->where('aso.post_type', '=', 3);
                    })
                    ->join(Config::get('tables.STORES_EXTRAS').' as se', 'se.store_id', '=', 'mst.store_id')
                    ->join(Config::get('tables.SUPPLIER_MST').' as ms', 'ms.supplier_id', '=', 'mst.supplier_id')
                    ->join(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'aso.account_id')
                    ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                    ->where('am.account_type_id', Config::get('constants.ACCOUNT_TYPE.MERCHANT_SUB_ADMIN'))
                    ->select('am.account_id', 'mst.supplier_id as mrid', 'mst.store_id', 'am.email', 'am.uname', 'se.firstname', 'ad.lastname', 'mst.store_name', 'am.signedup_on', 'am.mobile', DB::raw('CONCAT_WS(\' \', ad.firstname, ad.lastname) as full_name'))
                    ->where('am.is_deleted', Config::get('constants.NOT_DELETED'))
                    ->where('ms.supplier_code', '=', $supplier_code);
            $result = $query->get();
            array_walk($result, function(&$data)
            {
                $data->signedup_on = showUTZ($data->signedup_on, 'd-M-Y H:i:s');
            });
            return $result;
        }
    }
	
	public function country_list ()
    {
        $result = DB::table(Config::get('tables.SUPPLIER_MST').' as mm')
                ->join(Config::get('tables.ADDRESS_MST').' as adm', function($subquery)
                {
                    $subquery->on('adm.relative_post_id', '=', 'mm.supplier_id')
							->where('adm.post_type', '=', Config::get('constants.ACCOUNT_TYPE.SELLER'))
							->where('adm.address_type_id', '=', Config::get('constants.PRIMARY_ADDRESS'));
                })
                ->join(Config::get('tables.LOCATION_COUNTRY').' as loc', 'loc.country_id', '=', 'adm.country_id')
                ->select('adm.country_id', 'loc.country')      
				->distinct()
                ->orderBy('loc.country', 'asc')
                ->get();
        if (!empty($result) && count($result) > 0)
        {
            return $result;
        }
        return null;
    }
	
	public function merchant_filter_bcategory ()
    {
        $bcategory = DB::table(Config::get('tables.SUPPLIER_MST').' as mm')
					->join(Config::get('tables.BUSINESS_CATEGORY').' as bc', 'bc.bcategory_id', '=', 'mm.category_id')
					->join(Config::get('tables.BUSINESS_CATEGORY_LANG').' as bcl', function($subquery)
					{
						$subquery->on('bcl.bcategory_id', '=', 'bc.bcategory_id')
						->where('bcl.lang_id', '=', Config::get('app.locale_id'));
					})
					->where('mm.is_deleted', Config::get('constants.NOT_DELETED'))
					->select('bc.bcategory_id', 'bcl.bcategory_name', 'bc.slug')
					->distinct()
					->orderBy('bcl.bcategory_name', 'asc');
        $result = $bcategory->get();
        if (!empty($result) && count($result) > 0)
        {
            return $result;
        }
        return null;
    }
	
	public function get_suppliers_list (array $data = array(), $count = false)
    {	
        $mr_status = null;
        extract($data);
        $retailer = DB::table(Config::get('tables.SUPPLIER_MST').' as mm')                
                ->join(Config::get('tables.ACCOUNT_MST').' as accmst', 'accmst.account_id', '=', 'mm.account_id')
                ->join(Config::get('tables.ADDRESS_MST').' as adm', function($subquery)
                {
                    $subquery->on('adm.relative_post_id', '=', 'mm.supplier_id')
								->where('adm.post_type', '=', Config::get('constants.ACCOUNT_TYPE.SELLER'))
								->where('adm.address_type_id', '=', Config::get('constants.PRIMARY_ADDRESS'));
                })
                ->leftjoin(Config::get('tables.BUSINESS_CATEGORY').' as bc', function($subquery)
                {
                    $subquery->on('bc.bcategory_id', '=', 'mm.category_id')
							 ->where('bc.is_deleted', '=', Config::get('constants.NOT_DELETED'));
                })
                ->leftjoin(Config::get('tables.BUSINESS_CATEGORY_LANG').' as bcl', function($subquery)
                {
                    $subquery->on('bcl.bcategory_id', '=', 'mm.category_id')
							 ->where('bcl.lang_id', '=', Config::get('app.locale_id'));
                })
                ->leftjoin(Config::get('tables.LOCATION_COUNTRY').' as loc', 'loc.country_id', '=', 'adm.country_id') 
                ->where('mm.is_deleted', Config::get('constants.OFF'))
                ->where('mm.is_online', Config::get('constants.OFF'));
		
        if (empty($mr_status))
        {
            $retailer->where('mm.is_verified', Config::get('constants.ON'));
        }
        else
        {
            $retailer->where('mm.is_verified', Config::get('constants.OFF'));
        }
		
        if (!empty($from) && isset($from))
        {
            $retailer->whereDate('mm.created_on', '<=', getGTZ($from, 'Y-m-d'));
        }
        if (!empty($to) && isset($to))
        {
            $retailer->whereDate('mm.created_on', '>=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($search_term) && !empty($search_term))
        {
           /*  if (!empty($filterTerms) && !empty($filterTerms))
            {
                $search_term = '%'.$search_term.'%';
                $search_field = ['Mrcode'=>'mm.mrcode', 'Mrbusiness_name'=>'mm.mrbusiness_name', 'Mrmobile'=>'accmst.mobile', 'Mremail'=>'accmst.email', 'Mruname'=>'accmst.uname'];
                $retailer->where(function($sub) use($filterTerms, $search_term, $search_field)
                {
                    foreach ($filterTerms as $search)
                    {
                        if (array_key_exists($search, $search_field))
                        {
                            $sub->orWhere(DB::raw($search_field[$search]), 'like', $search_term);
                        }
                    }
                });
            } */

            $retailer->where(function($wcond) use($search_term)
            {
                $wcond->Where('mm.mrcode', 'like', $search_term)
                        ->orwhere('mm.mrbusiness_name', 'like', $search_term)
                        ->orwhere('accmst.uname', 'like', $search_term)
                        ->orwhere('accmst.mobile', 'like', $search_term)
                        ->orwhere('accmst.email', 'like', $search_term);
            });
        }

        if (isset($country) && !empty($country))
        {
            $retailer->where('adm.country_id', $country);
        }
        if (isset($bcategory) && !empty($bcategory))
        {
            $retailer->where('bc.bcategory_id', $bcategory);
        }

        if (isset($count) && !empty($count))
        {
            return $retailer->count();
        }
        else
        {
            if (isset($start) && isset($length))
            {
                $retailer->skip($start)->take($length);
            }
            if (isset($orderby) && isset($order))
            {
                if ($orderby == 'created_on')
                {
                    $retailer->orderBy('mm.created_on', $order);
                }
                elseif ($orderby == 'mrcode')
                {
                    $retailer->orderBy('mm.mrcode', $order);
                }
                elseif ($orderby == 'mrbusiness_name')
                {
                    $retailer->orderBy('mm.mrbusiness_name', $order);
                }
                elseif ($orderby == 'country')
                {
                    $retailer->orderBy('loc.country', $order);
                }
                elseif ($orderby == 'bcategory_name')
                {
                    $retailer->orderBy('bcl.bcategory_name', $order);
                }
                elseif ($orderby == 'country')
                {
                    $retailer->orderBy('loc.country', $order);
                }
                elseif ($orderby == 'activated_on')
                {
                    //$retailer->orderBy('ms.activated_on', $order);
                }
                elseif ($orderby == 'status')
                {
                    $retailer->orderBy('mm.status', $order);
                }
            }
            else
            {
                $retailer->orderBy('mm.created_on', 'DESC');
            }
            //$retailers = $retailer->selectRaw('mm.mrid, mm.mrcode, mm.account_id,mm.mrbusiness_name, mm.status, loc.country, bcl.bcategory_name, bc.bcategory_id, ms.activated_on, ms.is_verified, mm.created_on, mm.is_deleted, mm.block, accmst.mobile,accmst.email,accmst.uname')->get();
            $retailers = $retailer->selectRaw('mm.supplier_id as mrid, mm.supplier_code as mrcode, mm.account_id, mm.company_name as mrbusiness_name, mm.status, loc.country, bcl.bcategory_name, bc.bcategory_id, mm.activated_on, mm.is_verified, mm.created_on, mm.is_deleted, mm.block, accmst.mobile,accmst.email,accmst.uname')->get();
            array_walk($retailers, function(&$retailer)
            {
                $retailer->activated_on = ($retailer->activated_on) ? showUTZ($retailer->activated_on, 'd-M-Y H:i:s') : '--';
                $retailer->created_on = showUTZ($retailer->created_on, 'd-M-Y H:i:s');
                $retailer->actions = [];
                $retailer->actions[] = ['url'=>route('admin.seller.details', ['mrcode'=>$retailer->mrcode]), 'redirect'=>true, 'label'=>trans('admin/general.btn.details')];
            /*  $retailer->actions[] = ['url'=>route('admin.retailers.edit', ['mrcode'=>$retailer->mrcode]), 'redirect'=>true, 'label'=>trans('general.btn.edit')];
                $retailer->actions[] = ['url'=>route('admin.qlogin.retailer-qlogin'), 'class'=>'quick_login', 'data'=>['uname'=>$retailer->uname], 'redirect'=>false, 'label'=>trans('general.btn.quick_login')];
                $retailer->actions[] = ['url'=>route('admin.retailers.stores.list', ['mrcode'=>$retailer->mrcode]), 'redirect'=>true, 'label'=>trans('general.btn.view_outlets')];
                $retailer->actions[] = ['url'=>route('admin.retailers.staffs.list', ['mrcode'=>$retailer->mrcode]), 'redirect'=>true, 'label'=>trans('general.btn.view_staffs')];
                $retailer->actions[] = ['url'=>route('admin.retailers.profit-sharing.details', ['id'=>$retailer->mrid, 'for'=>'view']), 'label'=>trans('general.btn.commission_details')];
                $retailer->actions[] = ['url'=>route('admin.retailers.kyc-doc', ['mrcode'=>$retailer->mrcode]), 'redirect'=>true, 'label'=>trans('general.btn.verify_kyc')];
                $retailer->actions[] = ['url'=>route('admin.qlogin.retailer-qlogin', ['id'=>$retailer->account_id]), 'redirect'=>false, 'label'=>trans('general.btn.login')];
                $retailer->actions[] = ['url'=>route('admin.retailers.delete', ['mrcode'=>$retailer->mrcode]), 'redirect'=>false, 'label'=>trans('general.btn.delete')];
                $retailer->actions[] = ['url'=>route('admin.finance.fund-transfer.to_merchant', ['type'=>'credit', 'mrcode'=>$retailer->mrcode]), 'redirect'=>true, 'label'=>trans('general.btn.add_fund')];
                $retailer->actions[] = ['url'=>route('admin.finance.fund-transfer.to_merchant', ['type'=>'debit', 'mrcode'=>$retailer->mrcode]), 'redirect'=>true, 'label'=>trans('general.btn.deduct_fund')];
                $retailer->actions[] = ['url'=>route('admin.retailers.transaction-log.list', ['for'=>'list', 'account_id'=>$retailer->account_id]), 'redirect'=>true, 'label'=>trans('general.btn.transactions')];
                $retailer->actions[] = ['url'=>route('admin.reports.order.commissions', [ 'mrcode'=>$retailer->mrcode]), 'redirect'=>true, 'label'=>trans('general.btn.commissions')];
				*/
                if (in_array($retailer->is_verified, [ Config::get('constants.SELLER.IS_VERIFIED.PENDING'), Config::get('constants.SELLER.IS_VERIFIED.REJECTED')]))
                {
                    $retailer->actions[] = ['url'=>route('admin.seller.verify-status', [ 'mrcode'=>$retailer->mrcode, 'is_verified'=>strtolower('VERIFIED')]), 'redirect'=>false, 'label'=>trans('general.seller.is_verified.'.Config::get('constants.SELLER.IS_VERIFIED.VERIFIED'))];
                }
                if (in_array($retailer->is_verified, [ Config::get('constants.SELLER.IS_VERIFIED.PENDING'), Config::get('constants.SELLER.IS_VERIFIED.VERIFIED')]))
                {
                    $retailer->actions[] = ['url'=>route('admin.seller.verify-status', [ 'mrcode'=>$retailer->mrcode, 'is_verified'=>strtolower('REJECTED')]), 'redirect'=>false, 'label'=>trans('general.seller.is_verified.'.Config::get('constants.SELLER.IS_VERIFIED.REJECTED'))];
                }  
                if (in_array($retailer->status, [ Config::get('constants.SELLER.STATUS.DRAFT'), Config::get('constants.SELLER.STATUS.INACTIVE')]))
                {
                    $retailer->actions[] = ['url'=>route('admin.seller.update-status', [ 'mrcode'=>$retailer->mrcode, 'is_verified'=>strtolower('ACTIVE')]), 'redirect'=>false, 'label'=>trans('general.seller.status.'.Config::get('constants.SELLER.STATUS.ACTIVE'))];
                }
                if (in_array($retailer->status, [ Config::get('constants.SELLER.STATUS.DRAFT'), Config::get('constants.SELLER.STATUS.ACTIVE')]))
                {
                    $retailer->actions[] = ['url'=>route('admin.seller.update-status', [ 'mrcode'=>$retailer->mrcode, 'is_verified'=>strtolower('INACTIVE')]), 'redirect'=>false, 'label'=>trans('general.seller.status.'.Config::get('constants.SELLER.STATUS.INACTIVE'))];
                }  
                if ($retailer->block == Config::get('constants.ACCOUNT.BLOCK.UNBLOCK'))
                {
                    $retailer->actions[] = ['url'=>route('admin.seller.update-block', [ 'mrcode'=>$retailer->mrcode, 'block'=>strtolower('BLOCK')]), 'redirect'=>false, 'label'=>trans('general.account.block.'.Config::get('constants.ACCOUNT.BLOCK.BLOCK'))];
                }  
                if ($retailer->block == Config::get('constants.ACCOUNT.BLOCK.BLOCK'))
                {
                    $retailer->actions[] = ['url'=>route('admin.seller.update-block', [ 'mrcode'=>$retailer->mrcode, 'block'=>strtolower('UNBLOCK')]), 'redirect'=>false, 'label'=>trans('general.account.block.'.Config::get('constants.ACCOUNT.BLOCK.UNBLOCK'))];
                }                
                $retailer->block_class = trans('admin/general.account.block_status_class.'.$retailer->block);
                $retailer->block = trans('admin/general.account.block.'.$retailer->block);
                $retailer->status_class = trans('admin/general.seller.status_class.'.$retailer->status);
                $retailer->status = trans('admin/general.seller.status.'.$retailer->status);                
                $retailer->is_verified_class = trans('admin/general.seller.verification_class.'.$retailer->is_verified);
                $retailer->is_verified = trans('admin/general.seller.is_verified.'.$retailer->is_verified);
            });
            return $retailers;
        }
    }	
	
	public function updateRetailerBlock (array $arr = array())
    {
        extract($arr);
        $b = [];
        $b['updated_on'] = getGTZ();
        $b['updated_by'] = $account_id;
        $b['block'] = $block;
        $qry = DB::table(Config::get('tables.SUPPLIER_MST'))
                ->where('supplier_code', $mrcode);
        if ($block == Config::get('constants.ACCOUNT.BLOCK.UNBLOCK'))
        {
            $qry->where('block', Config::get('constants.ACCOUNT.BLOCK.BLOCK'));
        }
        else if ($block == Config::get('constants.ACCOUNT.BLOCK.BLOCK'))
        {
            $qry->where('block', Config::get('constants.ACCOUNT.BLOCK.UNBLOCK'));
        }
        return $qry->update($b);
    }
	
	public function updateRetailerStatus (array $data = array())
    {
		extract($data);
        $b = [];
        $b['updated_on'] = getGTZ();
        $b['updated_by'] = $account_id;
        $b['status'] = $status;
        $qry = DB::table(Config::get('tables.SUPPLIER_MST'))
                ->where('supplier_code', $mrcode);
        if ($status == Config::get('constants.SELLER.STATUS.ACTIVE'))
        {
            $qry->whereIn('status', [ Config::get('constants.SELLER.STATUS.DRAFT'), Config::get('constants.SELLER.STATUS.INACTIVE')]);
        }
        else if ($status == Config::get('constants.SELLER.STATUS.INACTIVE'))
        {
            $qry->whereIn('status', [ Config::get('constants.SELLER.STATUS.DRAFT'), Config::get('constants.SELLER.STATUS.ACTIVE')]);
        }
        return $qry->update($b);
    }
	
	public function verify_status (array $data)
    {
        extract($data);
        $op = [];
        $merchant = DB::table(Config::get('tables.SUPPLIER_MST').' as mm')                
                ->join(Config::get('tables.ACCOUNT_MST').' as amst', 'amst.account_id', '=', 'mm.account_id')
                ->join(Config::get('tables.ACCOUNT_DETAILS').' as amd', 'amd.account_id', '=', 'amst.account_id')
                ->where('mm.is_deleted', Config::get('constants.NOT_DELETED'))
                ->where('mm.supplier_code', $mrcode)
                ->select('amst.email', 'amst.mobile', 'mm.account_id', 'mm.supplier_id as mrid', 'mm.completed_steps', 'amd.firstname', 'amd.lastname')
                ->first();
        $completed_steps = !empty($merchant->completed_steps) ? explode(',', $merchant->completed_steps) : [];					
        $activationSteps = implode(', ', DB::table(Config::get('tables.ACCOUNT_CREATION_STEPS').' as aas')                                                
                        ->where('aas.status', Config::get('constants.ON'))
                        ->where('aas.account_type_id', Config::get('constants.ACCOUNT_TYPE.SELLER'))
                        ->orderby('aas.priority', 'ASC')
                        ->whereNotIn('aas.step_id', $completed_steps)
                        ->lists('name as step'));
        if (empty($activationSteps))
        {
            $statuses = DB::table(Config::get('tables.PROFIT_SHARING').' as pr')
                    ->join(Config::get('tables.CASHBACK_OFFERS').' as cb', 'pr.supplier_id', '=', 'cb.supplier_id')
                    ->where('pr.supplier_id', $merchant->mrid)
                    ->select('pr.status as pr_status', 'cb.status as cb_status')
                    ->first();

            if ((!empty($statuses)) && ($statuses->pr_status == 1) && ($statuses->cb_status == 1))
            {		
				
                if (!empty($status) && $status == 1)
                {					
                    $res = DB::table(Config::get('tables.SUPPLIER_MST'))
                            ->where('supplier_id', $merchant->mrid)
                            ->update(['is_verified'=>Config::get('constants.ON')]);
                    if (!empty($res))
                    {
                        DB::table(Config::get('tables.SUPPLIER_MST'))
                                ->where('supplier_id', $merchant->mrid)
                                ->update(['status'=>Config::get('constants.ON'), 'updated_on'=>getGTZ(), 'updated_by'=>$account_id]);
                        DB::table(Config::get('tables.STORES'))
                                ->where('supplier_id', $merchant->mrid)
                                ->where('primary_store', Config::get('constants.ON'))
                                ->update(['is_approved'=>Config::get('constants.ON'), 'updated_on'=>getGTZ(), 'updated_by'=>$account_id]);
                        $op['status'] = 200;
                        $op['msg'] = trans('admin/seller.status_verify_block.verify_succ');
                        //CommonLib::notify($merchant->account_id, 'retailer.mr_account_verified', [], [], false);
                    }
                }
                else
                {
                    $res = DB::table(Config::get('tables.SUPPLIER_MST'))
                            ->where('supplier_id', $merchant->mrid)
                            ->update(['is_verified'=>Config::get('constants.OFF')]);

                    DB::table(Config::get('tables.ACCOUNT_PREFERENCE'))
                            ->where('account_id', $merchant->account_id)
                            ->update(['is_verified'=>Config::get('constants.OFF')]);
					
					DB::table(Config::get('tables.STORES'))
                                        ->where('supplier_id', $merchant->mrid)
                                        ->where('primary_store', Config::get('constants.ON'))
                                        ->update(['is_approved'=>Config::get('constants.OFF'), 'updated_on'=>getGTZ(), 'updated_by'=>$account_id]);										
					$op['status'] = 200;
                    $op['msg'] = trans('admin/seller.status_verify_block.unverify_succ');
					//CommonLib::notify($merchant->account_id, 'confirm_merchant_acc_activation');                    
                }
            }
            else
            {
                $op['status'] = 400;
                $op['msg'] = trans('admin/seller.cashback_not_verify');
            }
        }
        else
        {
            $op['status'] = 400;
            $op['msg'] = trans('admin/seller.status_verify_block.verification_message', ['steps'=>$activationSteps]);
        }
        return (!empty($op)) ? $op : NULL;
    }
	
	public function suppliers_details (array $data = array())
    {
        extract($data);
        $query = DB::table(Config::get('tables.SUPPLIER_MST').' as mm')
                ->join(Config::get('tables.ADDRESS_MST').' as adm', function($subquery)
                {
                    $subquery->on('adm.relative_post_id', '=', 'mm.supplier_id')
							->where('adm.post_type', '=', Config::get('constants.ACCOUNT_TYPE.SELLER'))
							->where('adm.address_type_id', '=', Config::get('constants.PRIMARY_ADDRESS'));
                })
                ->join(Config::get('tables.ACCOUNT_MST').' as acm', 'acm.account_id', '=', 'mm.account_id')
                ->leftjoin(Config::get('tables.BUSINESS_CATEGORY').' as bc', 'bc.bcategory_id', '=', 'mm.category_id')
                ->leftjoin(Config::get('tables.BUSINESS_CATEGORY_LANG').' as bcl', function($subquery)
                {
                    $subquery->on('bcl.bcategory_id', '=', 'bc.bcategory_id')
                    ->where('bcl.lang_id', '=', Config::get('app.locale_id'));
                })
                ->join(Config::get('tables.LOCATION_COUNTRY').' as loc', 'loc.country_id', '=', 'adm.country_id')              
                ->leftjoin(Config::get('tables.CASHBACK_SETTINGS').' as mcs', 'mcs.supplier_id', '=', 'mm.supplier_id')
            //    ->join(Config::get('tables.MERCHANT_EXTRAS').' as me', 'me.mrid', '=', 'mm.mrid')
             /*    ->leftjoin(Config::get('tables.ACCOUNT_TAX_DETAILS').' as atd', function($atd)
                {
                    $atd->on('atd.relative_postid', '=', 'mm.mrid')
                    ->where('atd.post_type', '=', Config::get('constants.POST_TYPE.MERCHANT'));
                }) */
                ->leftjoin(Config::get('tables.PROFIT_SHARING').' as ps', 'ps.supplier_id', '=', 'mm.supplier_id')
                ->where('mm.supplier_code', '=', $supplier_code)
                ->where('mm.is_online', '=', Config::get('constants.OFF')) 
                ->where('mm.is_deleted', Config::get('constants.NOT_DELETED'))
            //    ->selectRaw('mm.mrcode, mm.mrbusiness_name, mm.status, mm.block, mm.mrlogo, mm.created_on, ms.is_verified,ms.completed_steps, ms.activated_on,mcs.shop_and_earn,mcs.redeem,mcs.pay,mcs.is_cashback_period,mcs.cashback_start,mcs.cashback_end,mcs.member_redeem_wallets, mcs.is_redeem_otp_required, loc.country, bcl.bcategory_name, mm.is_deleted, me.description,atd.tax_info,ps.profit_sharing,ps.cashback_on_pay,ps.cashback_on_redeem,ps.cashback_on_shop_and_earn'); 
                ->selectRaw('mm.supplier_code as mrcode, mm.company_name as mrbusiness_name, mm.status, mm.block, mm.logo as mrlogo, mm.created_on, mm.is_verified, mm.completed_steps, mm.activated_on, mcs.shop_and_earn, mcs.redeem, mcs.pay, mcs.is_cashback_period, mcs.cashback_start, mcs.cashback_end, mcs.member_redeem_wallets, mcs.is_redeem_otp_required, loc.country, bcl.bcategory_name, mm.is_deleted, ps.profit_sharing, ps.cashback_on_pay, ps.cashback_on_redeem, ps.cashback_on_shop_and_earn'); 
        $result = $query->first();

        if (!empty($result))
        {
            $result->profit_sharing = !empty($result->profit_sharing) ? $result->profit_sharing.'%' : '-';
            $result->cashback_on_pay = !empty($result->cashback_on_pay) ? $result->cashback_on_pay.'% in '.$result->profit_sharing : '-';
            $result->cashback_on_redeem = !empty($result->cashback_on_redeem) ? $result->cashback_on_redeem.'% in '.$result->profit_sharing : '-';
            $result->cashback_on_shop_and_earn = !empty($result->cashback_on_shop_and_earn) ? $result->cashback_on_shop_and_earn.'% in '.$result->profit_sharing : '-';
           /*  $result->tax_info = !empty($result->tax_info) ? json_decode(stripslashes($result->tax_info), true) : [];
            array_walk($result->tax_info, function(&$v, $k)
            {
                $v = ['lable'=>trans('admin/general.tax_info.'.$k), 'value'=>$v];
            }); */
            $result->member_redeem_wallets = !empty($result->member_redeem_wallets) ? DB::table(Config::get('tables.WALLET_LANG'))
                            ->whereIn('wallet_id', explode(',', $result->member_redeem_wallets))
                            ->lists('wallet') : [];
            $result->cashback_start = !empty($result->cashback_start) ? showUTZ($result->cashback_start, 'd-M-Y H:i:s') : null;
            $result->cashback_end = !empty($result->cashback_end) ? showUTZ($result->cashback_end, 'd-M-Y H:i:s') : null;
            $result->completed_steps = !empty($result->completed_steps) ? explode(',', $result->completed_steps) : [];

            $result->created_on = showUTZ($result->created_on, 'd-M-Y H:i:s');
            $result->activated_on = showUTZ($result->activated_on, 'd-M-Y H:i:s');
            $result->redeem_otp_status_name = trans('admin/seller.details.redeem_otp_status.'.$result->is_redeem_otp_required);                        
            $result->redeem_otp_disp_class = trans('admin/general.account.retailer_redeem_status.'.$result->is_redeem_otp_required);
            if ($result->is_verified == Config::get('constants.OFF'))
            {               		
				$result->verify_disp_class = trans('admin/general.seller.verification_class.'.$result->is_verified);
				$result->is_verified_name = trans('admin/general.seller.is_verified.'.$result->is_verified);
            }
            if ($result->block == Config::get('constants.OFF'))
            {               		
				$result->status_disp_class = trans('admin/general.seller.status_class.'.$result->status);
				$result->status_name = trans('admin/general.seller.status.'.$result->status);   
            }
            else
            {
				$result->status_disp_class = trans('admin/general.seller.status_class.'.$result->block);
				$result->status_name = trans('admin/general.seller.status.'.$result->block);               
            }		
				
            $result->activationSteps = DB::table(Config::get('tables.ACCOUNT_CREATION_STEPS').' as aas')                    
                    //->where('aas.post_type', Config::get('constants.POST_TYPE.MERCHANT'))
                    ->where('aas.status', Config::get('constants.ON'))
                    ->where('aas.account_type_id', Config::get('constants.ACCOUNT_TYPE.SELLER'))
                    ->orderby('aas.priority', 'ASC')
                    ->selectRaw('aas.step_id as id, aas.name as step, aas.name as step_description, aas.route')
                    ->get();
            array_walk($result->activationSteps, function(&$step) use($result)
            {
                $step->is_completed = (in_array($step->id, $result->completed_steps));
                unset($step->route);
            });  
        }
        return $result;
    }
	
	public function supplier_primeaccount_info (array $data)
    {
        extract($data);
        if (isset($supplier_code) && !empty($supplier_code))
        {
            $query = DB::table(Config::get('tables.SUPPLIER_MST').' as mm')     
					->join(Config::get('tables.ACCOUNT_MST').' as am', function($subquery)
                    {
                        $subquery->on('am.account_id', '=', 'mm.account_id')
							->where('am.account_type_id', '=', Config::get('constants.ACCOUNT_TYPE.SELLER'));
                    })
					->join(Config::get('tables.ADDRESS_MST').' as adm', function($subquery)
					{
						$subquery->on('adm.relative_post_id', '=', 'mm.supplier_id')
								->where('adm.post_type', '=', Config::get('constants.ACCOUNT_TYPE.SELLER'))
								->where('adm.address_type_id', '=', Config::get('constants.PRIMARY_ADDRESS'));
					})
                    ->join(Config::get('tables.ACCOUNT_DETAILS').' as ads', 'ads.account_id', '=', 'mm.account_id')                   
                    ->where('mm.is_deleted', Config::get('constants.NOT_DELETED'))
                    ->where('mm.supplier_code', '=', $supplier_code)
                    ->selectRaw('am.uname, am.mobile, am.email, concat_ws(\' \', ads.firstname, ads.lastname) as fullname, concat_ws(\',\', adm.flatno_street, adm.landmark, adm.address, adm.postal_code) as address');
            $result = $query->first();
            if (!empty($result) && count($result) > 0)
            {
                return $result;
            }
            return null;
        }
    }
	
	public function suppliers_details_bk ($wdata = array())
    {
        extract($wdata);
        $query = DB::table(Config::get('tables.SUPPLIER_MST').' as asu')                
                ->Join(Config::get('tables.ACCOUNT_MST').' as amst', 'amst.account_id', '=', 'asu.account_id')
				->Join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'asu.account_id')
                ->Join(config::get('tables.ACCOUNT_STATUS_LOOKUPS').' as asl', 'asl.status_id', '=', 'amst.status')
				->join(config::get('tables.ADDRESS_MST').' as addr', function($ad)
                {
                    $ad->on('addr.relative_post_id', '=', 'asu.supplier_id')
                    ->where('addr.post_type', '=', Config::get('constants.ACCOUNT_TYPE.SELLER'))
                    ->where('addr.address_type_id', '=', Config::get('constants.PRIMARY_ADDRESS'));
                })            
                ->leftJoin(config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'addr.country_id')
                ->leftJoin(config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'addr.state_id')
                ->leftJoin(Config::get('tables.LOCATION_CITY').' as lci', 'lci.city_id', '=', 'addr.city_id') 
                //->selectRaw('mst.email, asu.*, mst.uname, mst.user_code, concat(amst.firstname, amst.lastname) as full_name, amst.status_id, addr.*, mst.mobile, lc.country, ls.state, lc.country_id as cou_id, addr.country_id, lc.phonecode, asu.office_fax, asu.office_phone, asl.status_name, lci.city, mst.mobile, asu.account_id');
                ->selectRaw('asu.*, addr.*, amst.email, amst.uname, amst.user_code, CONCAT(ad.firstname, ad.lastname) as full_name, amst.status as status_id, amst.mobile, lc.country, ls.state, lc.country_id as cou_id, addr.country_id, lc.phonecode, asu.office_fax, asu.office_phone, asl.status_name, lci.city, asu.account_id');
        if (isset($account_id) && !empty($account_id))
        {
            $query->where('asu.account_id', $account_id);
        }
        if (isset($supplier_id) && !empty($supplier_id))
        {
            $query->where('asu.supplier_id', $supplier_id);
        }
        if (isset($uname) && !empty($uname))
        {
            $query->where('amst.uname', $uname);
        }
        $details = $query->first();

        if ($details)
        {
            $details->completed_steps = explode(',', $details->completed_steps);
            $details->verified_steps = explode(',', $details->verified_steps);
            $details->steps = DB::table(Config::get('tables.ACCOUNT_CREATION_STEPS'))
                    ->selectRaw('step_id, name')
                    ->orderby('priority', 'ASC')
                    ->get();
			//return $details;
            array_walk($details->steps, function(&$step) use(&$details)
            {
                $step->comleted = in_array($step->step_id, $details->completed_steps) ? 'Completed' : 'Not Completed';
                $step->status = in_array($step->step_id, $details->verified_steps) ? 'Verified' : 'Not Verified';
                $step->fields = [];
                $step->links = [];
                switch ($step->step_id)
                {
                    case Config::get('constants.ACCOUNT_CREATION_STEPS.ACCOUNT_DETAILS'):
                        $step->fields[] = ['label'=>'Supplier Name', 'value'=>$details->full_name, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Username', 'value'=>$details->uname, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Supplier Code', 'value'=>$details->user_code, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Email', 'value'=>$details->email, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Mobile', 'value'=>$details->mobile, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Created On', 'value'=>date('d-M-Y H:i:s', strtotime($details->created_on)), 'type'=>'text'];
                        $step->fields[] = ['label'=>'Company Name', 'value'=>$details->company_name, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Address', 'value'=>implode(', ', array_filter([$details->flatno_street, $details->address, $details->city, $details->state, implode('-', [$details->country, $details->postal_code])])), 'type'=>'text'];
                        $step->fields[] = ['label'=>'Website', 'value'=>$details->website, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Phone', 'value'=>$details->office_phone, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Fax', 'value'=>$details->office_fax, 'type'=>'text'];
                        $step->fields[] = ['label'=>'Status', 'value'=>$details->status_name, 'type'=>'text'];
                        break;
                    case Config::get('constants.ACCOUNT_CREATION_STEPS.ACCOUNT_UPDATE'):
                        $details->store_details = DB::table(Config::get('tables.STORES').' as s')
                                ->leftJoin(Config::get('tables.STORES_EXTRAS').' as se', 'se.store_id', '=', 's.store_id')
                                ->leftJoin(Config::get('tables.LOCATION_CITY').' as lci', 'lci.city_id', '=', 'se.city_id')
                                ->leftJoin(Config::get('tables.LOCATION_PINCODES').' as lp', 'lp.pincode_id', '=', 'lci.pincode_id')
                                ->leftJoin(Config::get('tables.LOCATION_DISTRICTS').' as ld', 'ld.district_id', '=', 'lp.district_id')
                                ->leftJoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'ld.state_id')
                                ->leftJoin(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ls.country_id')
                                ->where('s.supplier_id', $details->supplier_id)
                                ->where('s.primary_store', Config::get('constants.ON'))
                                ->selectRaw('s.store_id, s.store_name, s.store_code, se.address1, se.address2, lci.city, se.website, se.postal_code, se.landline_no, se.phonecode, se.mobile_no, se.email, se.working_days, se.working_hours_from, se.working_hours_to, lc.country, ls.state, se.city_id, ls.state_id, lc.country_id')
                                ->first();
                        if ($details->store_details)
                        {
                            $step->fields[] = ['label'=>'Store Name', 'value'=>$details->store_details->store_name, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Store Code', 'value'=>$details->store_details->store_code, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Email', 'value'=>$details->store_details->email, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Mobile', 'value'=>$details->store_details->mobile_no, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Address', 'value'=>implode(', ', array_filter([$details->store_details->address1, $details->store_details->address2, $details->store_details->city, $details->store_details->state, implode('-', [$details->store_details->country, $details->store_details->postal_code])])), 'type'=>'text'];
                            $step->fields[] = ['label'=>'Website', 'value'=>$details->store_details->website, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Working Hours', 'value'=>implode('-', [$details->store_details->working_hours_from, $details->store_details->working_hours_to]), 'type'=>'text'];
                        }
                        break;
                    case Config::get('constants.ACCOUNT_CREATION_STEPS.STORE_BANK'):
                        $details->payment_details = DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))
                                ->where('supplier_id', $details->supplier_id)
                                ->selectRaw('payment_settings, sps_id')
                                ->first();

                        if (isset($details->payment_details->sps_id) && !empty($details->payment_details->sps_id))
                        {
                            $details->payment_details->payment_settings = json_decode($details->payment_details->payment_settings);
                            $step->fields[] = ['label'=>'Bank Name', 'value'=>$details->payment_details->payment_settings->bank_name, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Account Holder Name', 'value'=>$details->payment_details->payment_settings->account_holder_name, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Account No', 'value'=>$details->payment_details->payment_settings->account_no, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Account Type', 'value'=>$details->payment_details->payment_settings->account_type, 'type'=>'text'];
                            $step->fields[] = ['label'=>'IFSC CODE', 'value'=>$details->payment_details->payment_settings->ifsc_code, 'type'=>'text'];
                            if (isset($details->payment_details->payment_settings->country_id))
                            {
                                $details->payment_details->payment_settings->country = $this->commonObj->getCountryName($details->payment_details->payment_settings->country_id);
                                $details->payment_details->payment_settings->state = $this->commonObj->getStateName($details->payment_details->payment_settings->state_id);
                                $details->payment_details->payment_settings->city = $this->commonObj->getCityName($details->payment_details->payment_settings->city_id);
                            }
                            else
                            {
                                $details->payment_details->payment_settings->country = 0;
                                $details->payment_details->payment_settings->state = 0;
                                $details->payment_details->payment_settings->city = 0;
                            }
                            $details->payment_details->payment_settings->setting_id = $details->payment_details->sps_id;                           
                            $step->fields[] = ['label'=>'Address', 'value'=>implode(', ', array_filter([$details->payment_details->payment_settings->address1, $details->payment_details->payment_settings->address2, $details->payment_details->payment_settings->city, $details->payment_details->payment_settings->state, implode('-', [$details->payment_details->payment_settings->country, $details->payment_details->payment_settings->postal_code])])), 'type'=>'text'];
                        }
                        break;
                    case Config::get('constants.ACCOUNT_CREATION_STEPS.VERIFY_KYC'):
                        $details->kyc_details = DB::table(Config::get('tables.KYC_DOCUMENTS').' as skv')
                                ->leftjoin(config::get('tables.DOCUMENT_TYPES').' as dt', 'dt.document_type_id', '=', 'skv.id_proof_document_type_id')
                                ->where('skv.relative_post_id', $details->supplier_id)
                                ->where('skv.post_type', Config::get('constants.ACCOUNT_TYPE.SELLER'))
                                ->selectRaw('skv.pan_card_no, skv.pan_card_name, skv.dob, skv.pan_card_image, skv.vat_no, skv.cst_no, skv.auth_person_name, skv.auth_person_id_proof, skv.status_id, dt.type')
                                ->first();
                        if ($details->kyc_details)
                        {
                            $step->fields[] = ['label'=>'PAN Card No', 'value'=>$details->kyc_details->pan_card_no, 'type'=>'text'];
                            $step->fields[] = ['label'=>'PAN Card Name', 'value'=>$details->kyc_details->pan_card_name, 'type'=>'text'];
                            $step->fields[] = ['label'=>'PAN Card Image', 'value'=>URL::asset($details->kyc_details->pan_card_image), 'type'=>'link'];
                            $step->fields[] = ['label'=>'DOB', 'value'=>date('d-m-Y', strtotime($details->kyc_details->dob)), 'type'=>'text'];
                            $step->fields[] = ['label'=>'VAT No', 'value'=>$details->kyc_details->vat_no, 'type'=>'text'];
                            $step->fields[] = ['label'=>'CST No', 'value'=>$details->kyc_details->cst_no, 'type'=>'text'];
                            $step->fields[] = ['label'=>'Authorized Person Name', 'value'=>$details->kyc_details->auth_person_name, 'type'=>'text'];
                            $step->fields[] = ['label'=>'ID Proof Type', 'value'=>$details->kyc_details->type, 'type'=>'text'];
                            $step->fields[] = ['label'=>'ID Proof', 'value'=>URL::asset($details->kyc_details->auth_person_id_proof), 'type'=>'link'];
                        }
                        break;
                }
            });
        }
        return $details;
    }

    public function save_suppliers ($data, $admin_id)
    {
        $user_name = $this->genetare_user_code();
        $password = $this->rKeyGen(10, 0);
        //insert in to account master
        $account_id = DB::table(Config::get('tables.ACCOUNT_MST'))->insertGetID(array(
            'salutation'=>1,
            'firstname'=>$data['supplier_first_name'],
            'lastname'=>$data['supplier_last_name'],
            'status_id'=>1,
            'created_on'=>date('Y-m-d H:i:s'),
            'updated_by'=>$admin_id
        ));
        //$supplier_code = $this->generate_supplier_code($account_id);
        $supplier_code = Commonsettings::generateUserCode();
        $activation_key = md5($supplier_code);
        //insert in to login_mst
        $login = DB::table(Config::get('tables.ACCOUNT_LOGIN_MST'))->insertGetId(array(
            'account_id'=>$account_id,
            'uname'=>$supplier_code,
            'email'=>$data['email'],
            'mobile'=>$data['mobile'],
            'pass_key'=>md5(''.$password),
            'account_type_id'=>Config::get('constants.ACCOUNT_TYPE.SUPPLIER'),
            'user_code'=>$supplier_code,
            'activation_key'=>$activation_key
        ));
        $account_PRE = [];
        $account_PRE['account_id'] = $account_id;
        $user_preference_id = DB::table(Config::get('tables.ACCOUNT_PREFERENCE'))
                ->insertGetId($account_PRE);
        //insert in to account supplier
        $supplier_id = DB::table(Config::get('tables.ACCOUNT_SUPPLIERS'))->insertGetId(array(
            'account_id'=>$account_id,
            'company_name'=>$data['company_name'],
            'supplier_code'=>$supplier_code,
            'office_phone'=>$data['phone'],
            'office_fax'=>$data['fax'],
            'website'=>$data['website'],
            'created_on'=>date('Y-m-d H:i:s'),
            'file_path'=>Config::get('path.SUPPLIER_PRODUCT_IMAGE_PATH').$supplier_code.'/',
        ));
        //insert into account address
        $address_id = DB::table(Config::get('tables.ACCOUNT_ADDRESS'))->insertGetId(array(
            'account_id'=>$account_id,
            'address_type_id'=>1,
            'street1'=>$data['street1'],
            'street2'=>$data['street2'],
            'city_id'=>$data['city_id'],
            'state_id'=>$data['state_id'],
            'country_id'=>$data['country_id'],
            'postal_code'=>$data['Postcode'],
            'created_on'=>date('Y-m-d H:i:s')
        ));
        if (!empty($account_id))
        {
            $email_data = array(
                'acc_email'=>$data['email'],
                'user_name'=>$user_name,
                'password'=>$password);

            if (!empty($supplier_id))
            {
                ShoppingPortal::notify('SUPPLIER_SIGNUP', $supplier_id, Config::get('constants.ACCOUNT_TYPE.SUPPLIER'), $email_data, true, true, true, true);
                return $supplier_id;
            }
        }
        else
        {
            return false;
        }
    }

    public function verification_list ($arr)
    {
        $res = DB::table(Config::get('tables.ACCOUNT_VERIFICATION').' as av')
                ->join(Config::get('tables.DOCUMENT_TYPES').' as dt', 'dt.document_type_id', '=', 'av.document_type_id')
                ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'av.account_id')
                ->join(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'av.account_id')
                ->where('av.is_deleted', Config::get('constants.OFF'));
        if (isset($arr['search_term']) && !empty($arr['search_term']))
        {
            $res->where('am.firstname', 'like', '%'.$arr['search_term'].'%');
        }
        if (isset($arr['account_id']) && !empty($arr['account_id']))
        {
            $res->where('av.account_id', $arr['account_id']);
        }
        if (isset($arr['uname']) && !empty($arr['uname']))
        {
            $res->where('am.uname', $arr['uname']);
        }

        if (isset($arr['status']) && $arr['status'] != '')
        {
            $res->where('av.status_id', $arr['status']);
        }
        if (isset($arr['type_filer']) && !empty($arr['type_filer']))
        {
            $res->where('av.document_type_id', $arr['type_filer']);
        }
        if (!empty($arr['from']))
        {
            $res->whereDate('av.created_on', '>=', date('Y-m-d', strtotime($arr['from'])));
        }
        if (!empty($arr['to']))
        {
            $res->whereDate('av.created_on', '<=', date('Y-m-d', strtotime($arr['to'])));
        }
        if (isset($arr['start']) && isset($arr['length']))
        {
            $res->skip($arr['start'])->take($arr['length']);
        }
        if (isset($arr['orderby']) && !empty($arr['orderby']))
        {
            $res->orderby('av.created_on', $arr['order']);
        }
        else
        {
            $res->orderby('av.created_on', 'DESC');
        }
        if (isset($arr['counts']) && $arr['counts'] == true)
        {
            return $res->count();
        }
        else
        {
            $verifications = $res->selectRaw('av.*, dt.type, dt.document_type_id, dt.other_fields as doc_other_fields, am.uname, concat(ad.firstname,\' \',ad.lastname) as full_name')->get();
            array_walk($verifications, function(&$v)
            {
                $v->other_fields = !empty($v->other_fields) ? json_decode($v->other_fields) : [];
                $v->doc_other_fields = !empty($v->doc_other_fields) ? json_decode($v->doc_other_fields, true) : [];
                array_walk($v->other_fields, function(&$field, $k) use($v)
                {
                    $field = ['id'=>$k, 'label'=>$v->doc_other_fields[$k]['label'], 'value'=>$field];
                });
                unset($v->doc_other_fields);
            });
            return $verifications;
        }
    }

    public function doc_list ()
    {
        return DB::table(Config::get('tables.DOCUMENT_TYPES'))
                        ->select('document_type_id', 'type', 'other_fields')
                        ->get();
    }
	
	public function change_status ($data = array())
    {	
        extract($data);
        if (DB::table(Config::get('tables.ACCOUNT_VERIFICATION'))
                        ->where('uv_id', $data['uv_id'])
                        ->update(array('status_id'=>$data['status'])))
        {
            $data['account_id'] = DB::table(Config::get('tables.ACCOUNT_VERIFICATION'))
                    ->where('uv_id', $data['uv_id'])
                    ->pluck('account_id');

            $proofs_verified = DB::table(Config::get('tables.ACCOUNT_VERIFICATION').' as accs')
                    ->leftJoin(Config::get('tables.DOCUMENT_TYPES').' as dt', 'accs.document_type_id', '=', 'dt.document_type_id')
                    ->where('accs.account_id', $data['account_id'])
                    ->where('accs.status_id', 1)
                    ->where('accs.is_deleted', Config::get('constants.OFF'))
                    ->groupby('accs.account_id')
                    ->selectRaw('sum(if(dt.proof_type=1,1,0)) as id_proof, sum(if(accs.document_type_id=19,1,0)) as pan_proof, sum(if(accs.document_type_id=4,1,0)) as bank_proof')
                    ->first();

            if ($proofs_verified->pan_proof >= 1 && ($proofs_verified->id_proof >= 1 || $proofs_verified->bank_proof >= 1))
            {
               /*  $steps = DB::table(Config::get('tables.ACCOUNT_SUPPLIERS'))
                        ->where('account_id', $data['account_id'])
                        ->pluck('verified_steps');
                $steps = explode(',', $steps);
                if (in_array(4, $steps))
                {
                    $steps[] = 4;
                }
                $udata = [];
                $udata['verified_steps'] = implode(',', $steps); */
                $udata['updated_by'] = $admin_id;
                $udata['is_verified'] = 1;
                DB::table(Config::get('tables.ACCOUNT_SUPPLIERS'))
                        ->where('account_id', $data['account_id'])
                        ->update($udata);
            }
        }
        return true;
    }

    public function delete_doc ($data)
    {
        return DB::table(Config::get('tables.ACCOUNT_VERIFICATION'))
                        ->where('uv_id', $data['uv_id'])
                        ->update(array(
                            'is_deleted'=>Config::get('constants.ON')));
    }

    

    public function get_stores_list ($arr = array())
    {
        $res = DB::table(Config::get('tables.STORES').' as st')
                ->join(Config::get('tables.STORES_EXTRAS').' as se', 'st.store_id', '=', 'se.store_id')
                ->join(Config::get('tables.ACCOUNT_SUPPLIERS').' as as', 'st.supplier_id', '=', 'as.supplier_id')
                ->leftJoin(Config::get('tables.LOCATION_CITY').' as lci', 'lci.city_id', '=', 'se.city_id')
                ->leftJoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'se.state_id')
                ->leftJoin(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'se.country_id')
                ->selectRaw('st.store_id, as.company_name, st.store_name, se.mobile_no, CONCAT(se.address1,\' \',se.address2) as address, se.address1, se.address2, se.email, se.website, se.country_id, lci.city, st.status, st.store_code, st.updated_on, se.city_id, se.state_id, se.postal_code, st.supplier_id, st.store_logo, se.landline_no');

        if (!empty($arr['start_date']))
        {
            $res->whereDate('st.created_on', '>=', date('Y-m-d', strtotime($arr['start_date'])));
        }

        if (!empty($arr['end_date']))
        {
            $res->whereDate('st.created_on', '<=', date('Y-m-d', strtotime($arr['end_date'])));
        }

        if (!empty($arr['filterTerms']) && !empty($arr['search_text']))
        {
            $subsql = '';
            $arr['filterTerms'] = !is_array($arr['filterTerms']) ? array($arr['filterTerms']) : $arr['filterTerms'];
            if (in_array('store_name', $arr['filterTerms']))
            {
                $subsql[] = 'st.store_name like (\'%'.$arr['search_text'].'%\')';
            }
            if (in_array('phone', $arr['filterTerms']))
            {
                $subsql[] = 'se.mobile_no like (\'%'.$arr['search_text'].'%\')';
            }
            if (in_array('code', $arr['filterTerms']))
            {
                $subsql[] = 'st.store_code like (\'%'.$arr['search_text'].'%\')';
            }
            if (!empty($subsql))
            {
                $res->whereRaw('('.implode(' OR ', $subsql).')');
            }
        }

        if (!empty($arr['store_code']) && !empty($arr['store_code']))
        {
            $res->where('st.store_code', $arr['store_code']);
            return $res->first();
        }
        if (isset($arr['start']) && isset($arr['length']))
        {
            $res->skip($arr['start'])->take($arr['length']);
        }
        if (isset($arr['orderby']))
        {
            $res->orderby($arr['orderby'], $arr['order']);
        }
        else
        {
            $res->orderby('st.store_name', 'DESC');
        }
        if (isset($arr['counts']) && $arr['counts'] == true)
        {
            return $res->count();
        }
        else
        {
            return $res->get();
        }
    }

    public function update_stores ($arr = array())
    {
        //return $arr;
        $supplier_id = '';
        $store_code = '';
        $update_values = '';
        extract($arr);
        if (isset($store_code) && !empty($store_code))
        {
            $res = DB::table(Config::get('tables.STORES').' as st')
                    ->join(Config::get('tables.STORES_EXTRAS').' as se', 'st.store_id', '=', 'se.store_id')
                    ->where('st.store_code', $store_code)
                    ->update(array(
                'st.supplier_id'=>$create['supplier_id'],
                'st.store_name'=>$create['store_name'],
                'st.store_logo'=>$create['store_logo'],
                'st.status'=>$create['status'],
                'se.mobile_no'=>$store_extras['mobile_no'],
                'se.landline_no'=>$store_extras['landline_no'],
                'se.email'=>$store_extras['email'],
                'se.address1'=>$store_extras['address1'],
                'se.address2'=>$store_extras['address2'],
                'se.city_id'=>$store_extras['city'],
                'se.postal_code'=>$store_extras['postal_code'],
                'se.website'=>$store_extras['website']));
            if ($res)
            {
                return 2;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $create['primary_store'] = 1;
            $create['updated_by'] = $admin_id;
            $create['created_on'] = date('Y-m-d H:i:s');
            $store_id = DB::table(Config::get('tables.STORES'))
                    ->insertGetId($create);
            $store_code = 'SUP'.rand().$store_id;
            $createe['store_code'] = $store_code;
            if ($createe['store_code'])
            {
                DB::table(Config::get('tables.STORES'))
                        ->where('store_id', $store_id)
                        ->update($createe);
            }
            $store_extras['store_id'] = $store_id;
            $store_extras['country_id'] = $store_extras['country'];
            $store_extras['state_id'] = $store_extras['state'];
            $store_extras['city_id'] = $store_extras['city'];
            unset($store_extras['country']);
            unset($store_extras['state']);
            unset($store_extras['city']);
            //return $store_extras;
            DB::table(Config::get('tables.STORES_EXTRAS'))
                    ->insert($store_extras);
            return 1;
        }
    }    

    public function verifyStep ($arr = array())
    {	
        $status = '';
        extract($arr);
        $query = DB::table(Config::get('tables.SUPPLIER_MST'))
                ->where('supplier_id', $supplier_id)
                ->whereRaw('FIND_IN_SET('.$step_id.',completed_steps)')
                ->where(function($sub) use($status, $step_id)
        {
            $sub->whereNULL('verified_steps')
            ->orWhere(function($sub2)use($status, $step_id)
            {
                $sub2->whereNotNull('verified_steps');
                if (!empty($status))
                {
                    $sub2->whereRaw('!FIND_IN_SET('.$step_id.',verified_steps)');
                }
                else
                {
                    $sub2->whereRaw('FIND_IN_SET('.$step_id.',verified_steps)');
                }
            });
        });
        $steps = $query->selectRaw('completed_steps, verified_steps')->first();
        if (!empty($steps))
        {
            $verified_steps = explode(',', $steps->verified_steps);
            if (!empty($status))
            {
                if (!in_array($step_id, $verified_steps))
                    $verified_steps[] = $step_id;
            }
            else
            {
                unset($verified_steps[array_search($step_id, $verified_steps)]);
            }
            $verified_steps = array_filter($verified_steps);
            $verified_steps_count = count($verified_steps);
            $steps_count = DB::table(Config::get('tables.ACCOUNT_CREATION_STEPS'))
                    ->count();
            $udata = [];
            $udata['is_verified'] = ($steps_count == $verified_steps_count) ? Config::get('constants.ON') : Config::get('constants.OFF');
            $verified_steps = implode(',', $verified_steps);
            $verified_steps = !empty($verified_steps) ? $verified_steps : NULL;
            $udata['verified_steps'] = $verified_steps;
            $udata['updated_by'] = $admin_id;
            $s = DB::table(Config::get('tables.SUPPLIER_MST'))
                    ->where('supplier_id', $supplier_id)
                    ->update($udata);
            if ($udata['is_verified'])
            {
                $email_data['supplier_details'] = $this->suppliers_details(['supplier_id'=>$supplier_id]);
                //ShoppingPortal::notify('SUPPLIER_ACCOUNT_ACTIVATION', $supplier_id, Config::get('constants.ACCOUNT_TYPE.SELLER'), $email_data, true, true, true, true);
            }
            //return $s ? ($udata['is_verified'] ? 2 : 1) : false;
            return $s ? ($udata['is_verified'] ? 1 : 1) : false;
        }
        return false;
    }
	
    public function supplier_edit ($postdata)
    {
        extract($postdata);
        $currentdate = date('Y-m-d H:i:s');
        $res1 = $res2 = $res3 = $res4 = $res5 = $res6 = false;
        if (!empty($postdata))
        {
            $res1 = DB::table(Config::get('tables.ACCOUNT_MST'))
                    ->where('account_id', $postdata['supplier_account_id'])
                    ->update(array('email'=>$postdata['email'], 'mobile'=>$postdata['mobile']));

            $res2 = DB::table(Config::get('tables.SUPPLIER_MST'))
                    ->where('account_id', $postdata['supplier_account_id'])
                    ->update(array('office_phone'=>$postdata['officePhone'], 'office_fax'=>$postdata['officeFax']));
			
            if (isset($address_id) && !empty($address_id))
            {

                $res3 = DB::table(Config::get('tables.ADDRESS_MST'))
                        ->where('relative_post_id', $postdata['supplier_account_id'])
                        ->where('post_type', Config::get('constants.ACCOUNT_TYPE.SELLER'))
                        ->update(array(
							'city_id'=>$postdata['city'],
							'state_id'=>$postdata['state'],
							'country_id'=>$postdata['country'],
							'postal_code'=>$postdata['Postcode'],
							'flatno_street'=>$postdata['street1'],
							'address'=>$postdata['street2'],
							'updated_on'=>$currentdate
						));
            }
            else
            {
                $address['account_id'] = $supplier_account_id;
                $address['flatno_street'] = $street1;
                $address['address'] = $street2;
                $address['city_id'] = $city;
                $address['state_id'] = $state;
                $address['country_id'] = $country;
                $address['postal_code'] = $Postcode;
                $address['status'] = 0;
                $address['created_on'] = date('Y-m-d H:i:s');
                $address['address_type_id'] = Config::get('constants.ADDRESS.PRIMARY');
                $res3 = $res2 = DB::table(Config::get('tables.ADDRESS_MST'))->insertGetId($address);
            }

            if (isset($store_id) && !empty($store_id))
            {
                $store_details['updated_on'] = $currentdate;
                $store_details['updated_by'] = $account_id;
                $res4 = DB::table(Config::get('tables.STORES'))
                        ->where('store_id', $postdata['store_id'])
                        ->update($store_details);
            }
            else
            {
                $store_details['supplier_id'] = $supplier_id;
                $store_details['status'] = Config::get('constants.ON');
                $store_details['updated_by'] = $account_id;
                $store_details['created_on'] = $currentdate;
                $store_id = DB::table(Config::get('tables.STORES'))->insertGetId($store_details);
                $store_code = 'SUP'.rand(22222,99999).$store_id;
                $create['primary_store'] = 1;
                $create['store_code'] = $store_code;
                if ($create['store_code'])
                {
                    $res4 = DB::table(Config::get('tables.STORES'))->where('store_id', $store_id)->update($create);
                    unset($store_extra);
                    $store_extras['store_id'] = $store_id;
                    DB::table(Config::get('tables.STORES_EXTRAS'))->insertGetId($store_extras);
                }
            }

            if (isset($store_extra) && !empty($store_extra))
            {
                $res4 = DB::table(Config::get('tables.STORES_EXTRAS'))
                        ->where('store_id', $postdata['store_id'])
                        ->update($store_extra);
            }

            if (isset($setting_id) && !empty($setting_id))
            {
                $res5 = DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))
                        ->where('supplier_id', $postdata['supplier_id'])
                        ->where('sps_id', $setting_id)
                        ->update(['payment_settings'=>json_encode($payment_settings)]);
            }
            else
            {

                $res5 = DB::table(Config::get('tables.SUPPLIER_PAYMENT_SETTINGS'))->insertGetId(['supplier_id'=>$postdata['supplier_id'], 'payment_settings'=>json_encode($payment_settings), 'updated_by'=>$postdata['account_id']]);
            }
        }
        return $res1 || $res2 || $res3 || $res4 || $res5;
    }

    public function genetare_user_code ()
    {
        $user_codes = DB::table(Config::get('tables.ACCOUNT_LOGIN_MST').' as um')
                ->where('um.uname', 'LIKE', '%'.'SUP'.'%')
                ->lists('um.uname');
        re_create:
        $code = 'SUP'.mt_rand(10000000, 99999999);
        if (in_array($code, $user_codes))
        {
            goto re_create;
        }
        else
        {
            return $code;
        }
    }

    public function generate_supplier_code ($account_id)
    {
        $function_ret = '';
        $profix = $account_id;
        $iLoop = true;
        $disp = $this->rKeyGen(3, 1);
        $disp1 = 'SP'.$disp.$profix;
        return $disp1;
    }

    public function change_pwd ($data, $wdata = array())
    {		
        if (!empty($data) && !empty($wdata))
        {
            $suppliers['pass_key'] = md5($data['login_password']);
            return DB::table(Config::get('tables.ACCOUNT_MST'))
                            ->where('account_id', $wdata['account_id'])
                            ->update($suppliers);
        }
        return false;
    }

    function rKeyGen ($digits, $datatype)
    {
        $key = '';
        $tem = '';
        $poss = array();
        $poss_ALP = array();
        $j = 0;
        if ($datatype == 1)
        {
            for ($i = 49; $i < 58; $i++)
            {
                $poss[$j] = chr($i);
                $poss_ALP[$j] = $poss[$j];
                $j = $j + 1;
            }
            for ($k = 1; $k <= $digits; $k++)
            {
                $key = $key.$poss[rand(1, 8)];
            }
            $key;
        }
        else
        {
            $key = $this->rKeyGen_ALPHA($digits, false);
        }
        return $key;
    }

    function rKeyGen_ALPHA ($digits, $lc)
    {
        $key = '';
        $tem = '';
        $poss = array();
        $j = 0;
        // Place numbers 0 to 10 in the array
        for ($i = 50; $i < 57; $i++)
        {
            $poss[$j] = chr($i);
            $j = $j + 1;
        }
        // Place A to Z in the array
        for ($i = 65; $i < 90; $i++)
        {
            $poss[$j] = chr($i);
            $j = $j + 1;
        }
        // Place a to z in the array
        for ($k = 97; $k < 122; $k++)
        {
            $poss[$j] = chr($k);
            $j = $j + 1;
        }
        $ub = 0;
        if ($lc == true)
            $ub = 61;
        else
            $ub = 35;
        for ($k = 1; $k <= 3; $k++)
        {
            $key = $key.$poss[rand(0, $ub)];
        }
        for ($k = 4; $k <= $digits; $k++)
        {
            $key = $key.$poss[rand(0, $ub)];
        }
        return $key;
    }

    public function email_validate ($postdata)
    {
        return (DB::table(Config::get('tables.ACCOUNT_LOGIN_MST'))
                        ->where('email', $postdata['email'])
                        ->count()) > 0 ? false : true;
    }

    public function order_details ($data = array())
    {
        return DB::table(Config::get('tables.SUB_ORDERS').' as sop')
                        ->leftJoin(Config::get('tables.ORDER_ITEMS').' as op', 'op.order_id', '=', 'sop.order_id')
                        ->leftJoin(Config::get('tables.PRODUCT_ITEMS').' as pi', 'pi.product_id', '=', 'op.product_id')
                        ->leftJoin(Config::get('tables.SUPPLIER_PRODUCT_ORDER_STATUS_LOOKUPS').' as posl', 'posl.order_status_id', '=', 'sop.order_status_id')
                        ->where('sop.order_id', $data['order_id'])
                        ->selectRaw('sop.*,pi.*,sop.order_code,sop.amount,sop.order_date,sop.last_updated')
                        ->get();
    }

    public function order_cancel ($arr = array())
    {
        $status['order_status_id'] = 5;
        $status['last_updated'] = date(Config::get('constants.DB_DATE_TIME_FORMAT'));
        return DB::table(Config::get('tables.SUB_ORDERS'))
                        ->whereIn('order_status_id', array(
                            0,
                            1,
                            2))
                        ->where('order_id', $arr['order_id'])
                        ->update($status);
    }

    public function get_stock_log_report ($data = array())
    {
        $product = DB::table(Config::get('tables.SUPPLER_PRODUCT_STOCK_LOG').' as sop')
                ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi', 'spi.supplier_product_id', '=', 'sop.supplier_product_id')
                ->leftjoin(Config::get('tables.PRODUCTS').' as pro', 'pro.product_id', '=', 'spi.product_id')
                ->leftjoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as sup', 'sup.supplier_id', '=', 'spi.supplier_id')
                ->leftjoin(Config::get('tables.ACCOUNT_MST').' as mst', 'mst.account_id', '=', 'sup.account_id')
                // ->where('spi.supplier_id', $data['supplier_id'])
                ->selectRaw('sop.*,pro.product_name,sup.supplier_id,mst.account_id,sup.company_name');
        if (isset($data['product_id']) && !empty($data['product_id']))
        {
            $product->where('pro.product_id', $data['product_id']);
        }
        if (isset($data['supplier_id']) && !empty($data['supplier_id']))
        {
            $product->where('sup.supplier_id', $data['supplier_id']);
        }
        if (isset($data['search_term']) && !empty($data['search_term']))
        {
            $product->whereRaw('(sup.company_name like \'%'.$arr['search_txt'].'%\'  OR  pro.product_name like \'%'.$arr['search_txt'].'%\')');
        }
        if (!empty($arr['from']))
        {
            $res->whereDate('sop.created_on', '>=', date('Y-m-d', strtotime($arr['from'])));
        }
        if (!empty($arr['to']))
        {
            $res->whereDate('sop.created_on', '<=', date('Y-m-d', strtotime($arr['to'])));
        }
        if (isset($data['orderby']))
        {
            $product->orderby($data['orderby'], $data['order']);
        }
        if (isset($data['counts']) && !empty($data['counts']))
        {
            return $product->count();
        }
        else
        {
            return $product->get();
        }
    }

    public function supplier_check ($data = array())
    {
        return DB::table(Config::get('tables.ACCOUNT_LOGIN_MST').' as lmst')
                        ->join(Config::get('tables.ACCOUNT_MST').' as amst', 'amst.account_id', '=', 'lmst.account_id')
                        ->leftjoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as as', 'as.account_id', '=', 'lmst.account_id')
                        ->leftjoin(Config::get('tables.SUPPLIER_COMMISSIONS_SETTINGS').' as com', 'com.supplier_id', '=', 'as.supplier_id')
                        ->selectRaw('as.supplier_id, com.supplier_id as commission_supplier, com.commission_type, com.currency_id, com.commission_unit, com.commission_value, lmst.email, lmst.user_code, concat(amst.firstname, amst.lastname) as full_name, lmst.mobile, lmst.uname, lmst.account_id, as.company_name')
                        ->where('amst.is_deleted', Config::get('constants.OFF'))
                        ->where('lmst.uname', $data['supplier_name'])
                        ->where('lmst.account_type_id', 3)
                        ->first();
    }

    public function save_commissions ($postdata)
    {
        $get_commission = DB::table(Config::get('tables.SUPPLIER_COMMISSIONS_SETTINGS'))
                ->where('is_deleted', Config::get('constants.OFF'))
                ->where('supplier_id', $postdata['supplier_id'])
                ->pluck('commission_id');
        $data = array();
        $currentdate = date('Y-m-d H:i:s');
        $data['supplier_id'] = $postdata['supplier_id'];
        if ($postdata['fixed_rates'] == 1)
        {
            $data['commission_value'] = $postdata['amount'];
            $data['commission_unit'] = $postdata['commission_unit'];
            if ($postdata['commission_unit'] == 2)
            {
                $data['currency_id'] = $postdata['currency_id'];
            }
            else
            {
                //$data['currency_id'] = 0;
                $data['currency_id'] = $postdata['currency_id'];
            }
        }

        if ($postdata['fixed_rates'] == 2)
        {
            $data['commission_value'] = 0;
            $data['commission_unit'] = 0;
            //$data['currency_id'] = 0;
            $data['currency_id'] = $postdata['currency_id'];
        }
        $data['commission_type'] = $postdata['fixed_rates'];
        $data['status'] = Config::get('constants.ACTIVE');

        if (!empty($get_commission))
        {
            $data['updated_by'] = $postdata['admin_id'];
            $result = DB::table(Config::get('tables.SUPPLIER_COMMISSIONS_SETTINGS'))
                    ->where('is_deleted', Config::get('constants.OFF'))
                    ->where('commission_id', $get_commission)
                    ->update($data);
        }
        else
        {
            $data['created_on'] = $currentdate;
            $data['created_by'] = $postdata['admin_id'];
            $result = DB::table(Config::get('tables.SUPPLIER_COMMISSIONS_SETTINGS'))->insertGetId($data);
        }
        if (!empty($result))
        {
            return $result;
        }
        else
        {
            return NULL;
        }
    }

    public function supplier_payment_report ($arr = array(), $count = false)
    {
        extract($arr);
        $payments = DB::table(Config::get('tables.ODER_SALES_COMMISSION').' as sosc')
                ->leftjoin(Config::get('tables.ORDERS').' as o', 'o.order_id', '=', 'sosc.order_id')
                ->leftjoin(Config::get('tables.SUB_ORDERS').' as so', 'so.sub_order_id', '=', 'sosc.sub_order_id')
                ->leftjoin(Config::get('tables.ORDER_ITEMS').' as oi', 'oi.order_item_id', '=', 'sosc.order_item_id')
                ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi', 'spi.supplier_product_id', '=', 'oi.supplier_product_id')
                ->leftjoin(Config::get('tables.PRODUCTS').' as pro', 'pro.product_id', '=', 'spi.product_id')
                ->leftjoin(Config::get('tables.PRODUCT_COMBINATIONS').' as proc', 'proc.product_cmb_id', '=', 'spi.product_cmb_id')
                ->leftjoin(Config::get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'sosc.currency_id')
                ->leftJoin(Config::get('tables.ODER_SALES_COMMISSION_PAYMENT').' as soscp', 'soscp.osc_id', '=', 'sosc.osc_id')
                ->leftJoin(Config::get('tables.PAYMENT_STATUS_LOOKUPS').' as p', 'p.payment_status_id', '=', 'soscp.supplier_payment_status_id')
                ->leftJoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as s', 's.supplier_id', '=', 'sosc.supplier_id')
                ->leftJoin(Config::get('tables.ACCOUNT_LOGIN_MST').' as d', 'd.account_id', '=', 's.account_id')
                ->leftjoin(Config::get('tables.ACCOUNT_MST').' as ud', 'ud.account_id', '=', 's.account_id')
                ->where('sosc.is_deleted', 0);
        if (!empty($from))
        {
            $payments->whereDate('a.created_on', '>=', date('Y-m-d', strtotime($from)));
        }
        if (!empty($to))
        {
            $payments->whereDate('a.created_on', '<=', date('Y-m-d', strtotime($to)));
        }
        if (isset($term) && !empty($term))
        {
            $payments->where(function($subquery) use($term)
            {
                $subquery->orWhere('d.uname', 'like', '%'.$term.'%')
                        ->orWhere('a.remark', 'like', '%'.$term.'%');
            });
        }
        if (isset($start) && isset($length))
        {
            $payments->skip($start)
                    ->take($length);
        }
        if (isset($orderby) && isset($order))
        {
            $payments->orderBy($orderby, $order);
        }
        else
        {
            $payments->orderBy('sosc.osc_id', 'DESC');
        }
        if ($count)
        {
            return $payments->count();
        }
        else
        {
            $payments->selectRaw('sosc.osc_id,sosc.qty,sosc.mrp_price,sosc.supplier_sold_price,sosc.supplier_price_sub_total,sosc.created_on,oi.order_item_code,cur.currency,cur.currency_symbol,p.payment_status,s.company_name,s.supplier_code,o.order_code,so.sub_order_code,oi.discount,if(proc.product_cmb is not null,concat(pro.product_name,proc.product_cmb),pro.product_name) as product_name');
            $payments = $payments->get();
            array_walk($payments, function(&$payment)
            {
                $payment->Fcreated_on = date('d-M-Y H:i:s', strtotime($payment->created_on));
                $payment->Fqty = number_format($payment->qty, 0, '.', ',');
                $payment->Fsupplier_price_sub_total = $payment->currency_symbol.' '.number_format($payment->supplier_price_sub_total, 0, '.', ',').' '.$payment->currency;
                $payment->Fsupplier_sold_price = $payment->currency_symbol.' '.number_format($payment->supplier_sold_price, 0, '.', ',').' '.$payment->currency;
                $payment->Fmrp_price = $payment->currency_symbol.' '.number_format($payment->mrp_price, 0, '.', ',').' '.$payment->currency;
            });
            return $payments;
        }
    }

    public function get_wallet_list ()
    {
        return DB::table(Config::get('tables.WALLET').' as wa')
                        ->where('wa.status', Config::get('constants.ON'))
                        ->get();
    }

    public function supplier_order_details ($arr = array())
    {
        extract($arr);
        $orders = DB::table(Config::get('tables.SUB_ORDERS').' as sub')
                ->leftjoin(Config::get('tables.ORDERS').' as pr', 'pr.order_id', '=', 'sub.order_id')
                ->leftjoin(Config::get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'pr.payment_type_id')
                ->leftjoin(Config::get('tables.PAYMENT_STATUS_LOOKUPS').' as ps', 'ps.payment_status_id', '=', 'pr.payment_status_id')
                ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as os', 'os.order_status_id', '=', 'pr.order_status_id')
                ->leftjoin(Config::get('tables.ORDER_STATUS_LOOKUP').' as psos', 'psos.order_status_id', '=', 'sub.sub_order_status_id')->leftJoin(Config::get('tables.ACCOUNT_LOGIN_MST').' as d', 'd.account_id', '=', 'sub.account_id')
                ->leftjoin(Config::get('tables.ACCOUNT_MST').' as ud', 'ud.account_id', '=', 'd.account_id')
                ->leftjoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as s', 's.supplier_id', '=', 'sub.supplier_id')
                ->leftjoin(Config::get('tables.SUPPLIER_COMMISSIONS_LOOKUPS').' as cl', 'cl.commission_type_id', '=', 'sub.commission_type')
                ->where('sub.is_deleted', Config::get('constants.OFF'))
                ->where('sub.sub_order_code', $id)
                ->selectRaw('sub.sub_order_code,sub.created_on,s.supplier_code,s.company_name,pt.payment_type,ps.payment_status,ps.payment_status_class,os.status as order_status,os.order_status_class,sub.commission_type as sub_commission_type,cl.commission_type,sub.commission_amount,sub.commission_unit,sub.commission_value')
                ->first();
        if (!empty($orders))
        {
            return $orders;
        }
        else
        {
            return NULL;
        }
    }

    public function supplier_payment_details ($arr = array())
    {
        extract($arr);
        $payments = DB::table(Config::get('tables.ODER_SALES_COMMISSION').' as sosc')
                ->leftjoin(Config::get('tables.ORDERS').' as o', 'o.order_id', '=', 'sosc.order_id')
                ->leftjoin(Config::get('tables.SUB_ORDERS').' as so', 'so.sub_order_id', '=', 'sosc.sub_order_id')
                ->leftjoin(Config::get('tables.ORDER_ITEMS').' as oi', 'oi.order_item_id', '=', 'sosc.order_item_id')
                ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi', 'spi.supplier_product_id', '=', 'oi.supplier_product_id')
                ->leftjoin(Config::get('tables.PRODUCTS').' as pro', 'pro.product_id', '=', 'spi.product_id')
                ->leftjoin(Config::get('tables.PRODUCT_COMBINATIONS').' as proc', 'proc.product_cmb_id', '=', 'spi.product_cmb_id')
                ->leftjoin(Config::get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'sosc.currency_id')
                ->leftJoin(Config::get('tables.ODER_SALES_COMMISSION_PAYMENT').' as soscp', 'soscp.osc_id', '=', 'sosc.osc_id')
                ->leftJoin(Config::get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'o.payment_type_id')
                ->leftJoin(Config::get('tables.PAYMENT_STATUS_LOOKUPS').' as p', 'p.payment_status_id', '=', 'soscp.supplier_payment_status_id')
                ->leftJoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as s', 's.supplier_id', '=', 'sosc.supplier_id')
                ->leftJoin(Config::get('tables.ACCOUNT_LOGIN_MST').' as d', 'd.account_id', '=', 's.account_id')
                ->leftjoin(Config::get('tables.ACCOUNT_MST').' as ud', 'ud.account_id', '=', 's.account_id')
                ->where('sosc.is_deleted', 0)
                ->where('oi.order_item_code', $order_item_code)
                ->selectRaw('sosc.*,oi.order_item_code,cur.currency,pt.payment_type,cur.currency_symbol,p.payment_status,s.company_name,s.supplier_code,o.order_code,so.sub_order_code,oi.discount,if(proc.product_cmb is not null,concat(pro.product_name,proc.product_cmb),pro.product_name) as product_name')
                ->first();
        if (!empty($payments))
        {
            return $payments;
        }
        else
        {
            return NULL;
        }
    }

    public function updateNextStep ($arr = array())
    {
        $ASdata = [];
        extract($arr);
        $next = DB::table(Config::get('tables.ACCOUNT_CREATION_STEPS'))
                ->where('priority', '>', $current_step)
                ->havingRaw('min(priority)')
                ->selectRaw('step_id,route')
                ->first();
        $ASdata['next_step'] = $next->step_id;
        if (!empty($next_step))
        {
            $ASdata['completed_steps'] = DB::table(Config::get('tables.ACCOUNT_CREATION_STEPS'))
                    ->where('priority', '<=', $next_step)
                    ->selectRaw('GROUP_CONCAT(step_id) as completed_steps')
                    ->pluck('completed_steps');
        }
        DB::table(Config::get('tables.ACCOUNT_SUPPLIERS'))
                ->where('supplier_id', $supplier_id)
                ->update($ASdata);
        return (isset($next->route) && !empty($next->route)) ? URL::route($next->route) : URL::to('supplier/dashboard');
    }

    public function getSupplierPreferences ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.ACCOUNT_MST').' as lm')
                        ->join(Config::get('tables.SUPPLIER_MST').' as s', 's.account_id', '=', 'lm.account_id')
                        ->leftjoin(Config::get('tables.SUPPLIER_PREFERENCE').' as sp', 'sp.supplier_id', '=', 's.supplier_id')
                        ->where('lm.uname', $uname)
                        ->selectRaw('sp.*,s.supplier_id')
                        ->first();
    }

    public function savePerferences ($arr = array())
    {
        extract($arr);
        $preferences['is_ownshipment'] = (isset($preferences['is_ownshipment']) && !empty($preferences['is_ownshipment'])) ? Config::get('constants.ON') : Config::get('constants.OFF');
        if (DB::table(Config::get('tables.SUPPLIER_PREFERENCE'))
                        ->where('supplier_id', $supplier_id)
                        ->exists())
        {
            return DB::table(Config::get('tables.SUPPLIER_PREFERENCE'))
                            ->where('supplier_id', $supplier_id)
                            ->update($preferences);
        }
        else
        {
            $preferences['supplier_id'] = $supplier_id;
            return DB::table(Config::get('tables.SUPPLIER_PREFERENCE'))
                            ->insert($preferences);
        }
    }

    public function getBrandsList ($data, $count = false)
    {
        extract($data);
        $brands = DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE').' as spa')
                ->join(Config::get('tables.PRODUCT_BRANDS').' as pb', 'pb.brand_id', '=', 'spa.brand_id')
                ->join(Config::get('tables.SUPPLIER_MST').' as s', 's.supplier_id', '=', 'spa.supplier_id')
                ->where('spa.is_deleted', Config::get('constants.OFF'));
        if (!empty($search_term) && isset($search_term))
        {
            $search_term = '%'.$search_term.'%';
            $brands->where(function($s) use($search_term)
            {
                $s->where('pb.brand_name', 'like', $search_term)
                        ->orWhere('s.company_name', 'like', $search_term);
            });
        }
        if (isset($brand_id) && !empty($brand_id))
        {
            $brands->where('spa.brand_id', $brand_id);
        }
        if (isset($supplier_id) && !empty($supplier_id))
        {
            $brands->where('spa.supplier_id', $supplier_id);
        }
        if ($count)
        {
            return $brands->count();
        }
        else
        {
            if (isset($data['start']) && isset($data['length']))
            {
                $brands->skip($data['start'])->take($data['length']);
            }
            if (isset($data['orderby']))
            {
                $brands->orderby($data['orderby'], $data['order']);
            }
            $brands = $brands->leftjoin(Config::get('tables.LOGIN_STATUS_LOOKUPS').' as ls', 'ls.status_id', '=', 'spa.status')
                    ->leftjoin(Config::get('tables.VERIFICATION_STATUS_LOOKUPS').' as vs', 'vs.is_verified', '=', 'spa.is_verified')
                    ->selectRaw('pb.created_on, pb.brand_id, pb.brand_name, ls.status_id, ls.status_class, ls.status, pb.is_verified as main_is_verified, pb.is_exclusive_for_supplier,vs.is_verified,vs.verification,vs.verification_class,s.company_name')
                    ->get();
            if (!empty($brands))
            {
                array_walk($brands, function(&$brand)
                {
                    $brand->created_on = date('d-M-Y H:i:s', strtotime($brand->created_on));
                    $brand->status = !empty($brand->status) ? $brand->status : 'Inactive';
                    $brand->status_class = !empty($brand->status_class) ? $brand->status_class : 'label label-danger';
                });
            }
            return $brands;
        }
    }

    public function deleteBrand ($data)
    {
        extract($data);
        return Db::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                        ->where('brand_id', $data['id'])
                        ->update(['is_deleted'=>1, 'updated_by'=>$account_id, 'updated_on'=>date('Y-m-d H:i:s')]);
    }

    public function updateBrandStatus ($arr)
    {
        extract($arr);
        $update = [];
        $update['status'] = $status;
        $update['updated_by'] = $account_id;
        $update['updated_on'] = date('Y-m-d H:i:s');
        $query = Db::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                ->where('brand_id', $id);
        if ($status == Config::get('constants.ACTIVE'))
        {
            $query->where('status', Config::get('constants.INACTIVE'));
        }
        elseif ($status == Config::get('constants.INACTIVE'))
        {
            $query->where('status', Config::get('constants.ACTIVE'));
        }
        return $query->update($update);
    }

    public function updateBrandVerification ($arr = array())
    {
        extract($arr);
        $update = [];
        $update['is_verified'] = $status;
        $update['updated_by'] = $account_id;
        $update['updated_on'] = date('Y-m-d H:i:s');
        $query = Db::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                ->where('brand_id', $brand_id);
        if ($status == Config::get('constants.ACTIVE'))
        {
            $query->where('is_verified', Config::get('constants.INACTIVE'));
        }
        elseif ($status == Config::get('constants.INACTIVE'))
        {
            $query->where('is_verified', Config::get('constants.ACTIVE'));
        }
        return $query->update($update);
    }

    public function saveBrand ($arr)
    {
        extract($arr);
        $res = false;
        DB::beginTransaction();
        $brand = DB::table(Config::get('tables.PRODUCT_BRANDS'))
                ->where('brand_name', $brand_name)
                ->selectRaw('brand_id,is_deleted')
                ->first();
        if (!empty($brand))
        {
            $brand_id = $brand->brand_id;
            if ($brand->is_deleted)
            {
                DB::table(Config::get('tables.PRODUCT_BRANDS'))
                        ->where('brand_id', $brand_id)
                        ->update(['is_deleted'=>Config::get('constants.OFF'), 'updated_on'=>date('Y-m-d H:i:s'), 'updated_by'=>$account_id]);
            }
        }
        else
        {
            $ibrand['brand_name'] = $brand_name;
            $ibrand['sku'] = $ibrand['url_str'] = str_replace(' ', '-', strtolower($brand_name));
            $ibrand['created_by'] = $ibrand['updated_by'] = $account_id;
            $ibrand['created_on'] = $ibrand['updated_on'] = date('Y-m-d H:i:s');
            $brand_id = DB::table(Config::get('tables.PRODUCT_BRANDS'))
                    ->insertGetId($ibrand);
        }
        if (!DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                        ->where('supplier_id', $supplier_id)
                        ->where('brand_id', $brand_id)
                        ->exists())
        {
            $ins_associate['brand_id'] = $brand_id;
            $ins_associate['supplier_id'] = $supplier_id;
            $ins_associate['updated_by'] = $account_id;
            $ins_associate['updated_on'] = date('Y-m-d H:i:s');
            $res = DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                    ->insertGetId($ins_associate);
        }
        else
        {
            $res = DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                    ->where('supplier_id', $supplier_id)
                    ->where('brand_id', $brand_id)
                    ->where('is_deleted', Config::get('constants.ON'))
                    ->update(['is_deleted'=>Config::get('constants.OFF'), 'updated_on'=>date('Y-m-d H:i:s'), 'updated_by'=>$account_id]);
        }
        $res ? DB::commit() : DB::rollback();
        return $res;
    }
	
	public function get_meta_info ($arr = array())
    {
        extract($arr);
        $query = Db::table(Config::get('tables.META_INFO'));
        if (isset($meta_info_id) && !empty($meta_info_id))
        {
            $query->where('meta_info_id', $meta_info_id);
        }
        if (isset($post_type_id) && !empty($post_type_id) && isset($relative_post_id) && !empty($relative_post_id))
        {
            $query->where('post_type_id', $post_type_id)
                    ->where('relative_post_id', $relative_post_id);
        }

        return $query->first();
    }
	
	public function save_meta_info ($arr = array())
    {		
        extract($arr);
        $meta_info_id = Db::table(Config::get('tables.META_INFO'))
                ->where('post_type_id', $meta_info['post_type_id'])
                ->where('relative_post_id', $meta_info['relative_post_id'])
                ->pluck('meta_info_id');
        if (!$meta_info_id)
        {
            $meta_info['created_on'] = date('Y-m-d H:i:s');
            return Db::table(Config::get('tables.META_INFO'))
                            ->insertGetId($meta_info);
        }
        else
        {
            return Db::table(Config::get('tables.META_INFO'))
                            ->where('meta_info_id', $meta_info_id)
                            ->update($meta_info);
        }
        return false;
    }
	
	public function getProfitSharingList (array $arr = array(), $count = false)
    {
        extract($arr);        
        $merchants = DB::table(Config::get('tables.PROFIT_SHARING').' as ps')
                ->join(Config::get('tables.SUPPLIER_MST').' as sm', 'sm.supplier_id', '=', 'ps.supplier_id')
                ->where('ps.is_deleted', Config::get('constants.OFF'));
		
        if (isset($status))
        {
            $merchants->where('ps.status', $status);
        }
        if (isset($from) && !empty($from))
        {
            //$merchants->whereDate('ps.updated_on', '<=', showUTZ($from, 'Y-m-d'));
        }
        else if (isset($to) && !empty($to))
        {
            //$merchants->whereDate('ps.updated_on', '>=', showUTZ($to, 'Y-m-d'));
        }
        if ((isset($search_text) && !empty($search_text)))
        {
            $merchants->where('sm.company_name', 'like', '%'.$search_text.'%')
                        ->orwhere('sm.supplier_code', 'like', '%'.$search_text.'%')                        
                        ->orwhere('msm.reg_company_name', 'like', '%'.$search_text.'%');
        }        
		 
        if ($count)
        {
            return $merchants->count();
        }
        else
        {
            if (isset($start) && isset($length))
            {
                $merchants->skip($start)->take($length);
            }
            if (isset($orderby))
            {
                //$merchants->orderby($orderby, $order);
            }
            else
            {
                //$merchants->orderby('ps.sps_id', 'DESC');
            }
            $merchants = $merchants->join(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'ps.created_by')
                    ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'ps.created_by')
                    ->selectRaw('ps.sps_id as id, sm.company_name, sm.supplier_code, ps.status, ps.created_on, ps.updated_on, ps.profit_sharing, ps.cashback_on_pay, ps.cashback_on_redeem, ps.cashback_on_shop_and_earn, CONCAT(ad.firstname,\' \',ad.lastname,\' (\',am.uname,\')\') as created_by')
                    ->get();
					
			//return $merchants;	
			
            if (!empty($merchants))
            {
                array_walk($merchants, function(&$merchant)
                {
                    $merchant->created_on = showUTZ($merchant->created_on, 'd-M-Y H:i:s');
                    $merchant->updated_on = ($merchant->updated_on != null) ? showUTZ($merchant->updated_on, 'd-M-Y H:i:s') : '--';
                    //$merchant->store_name = !empty($merchant->store_name) ? $merchant->store_name : '-';
                    //$merchant->store_code = !empty($merchant->store_code) ? $merchant->store_code : '-';
                    $merchant->profit_sharing = !empty($merchant->profit_sharing) ? $merchant->profit_sharing.'%' : '-';
                    $merchant->cashback_on_pay = !empty($merchant->cashback_on_pay) ? $merchant->cashback_on_pay.'%' : '-';
                    $merchant->cashback_on_redeem = !empty($merchant->cashback_on_redeem) ? $merchant->cashback_on_redeem.'%' : '-';
                    $merchant->cashback_on_shop_and_earn = !empty($merchant->cashback_on_shop_and_earn) ? $merchant->cashback_on_shop_and_earn.'%' : '-';
                    $merchant->actions = [];
                    $merchant->actions[] = ['url'=>route('admin.seller.commission.details', ['id'=>$merchant->id, 'for'=>'view']), 'label'=>'View'];
                    switch ($merchant->status)
                    {
                        case Config::get('constants.MERCHANT.PROFIT_SHARING.STATUS.PENDING'):
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.details', ['id'=>$merchant->id, 'for'=>'edit']), 'label'=>trans('general.seller.profit-sharing.status.'.Config::get('constants.SELLER.PROFIT_SHARING.STATUS.ACCEPTED'))];
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.update-status', [ 'id'=>$merchant->id, 'status'=>strtolower('REJECTED')]), 'label'=>trans('general.seller.profit-sharing.status.'.Config::get('constants.SELLER.PROFIT_SHARING.STATUS.REJECTED'))];
                            break;
                        case Config::get('constants.MERCHANT.PROFIT_SHARING.STATUS.ACCEPTED'):
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.details', ['id'=>$merchant->id, 'for'=>'edit']), 'label'=>'Edit'];
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.update-status', [ 'id'=>$merchant->id, 'status'=>strtolower('REJECTED')]), 'label'=>trans('general.seller.profit-sharing.status.'.Config::get('constants.SELLER.PROFIT_SHARING.STATUS.REJECTED'))];
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.update-status', [ 'id'=>$merchant->id, 'status'=>strtolower('CLOSED')]), 'label'=>trans('general.seller.profit-sharing.status.'.Config::get('constants.SELLER.PROFIT_SHARING.STATUS.CLOSED'))];
                            break;
                        case Config::get('constants.MERCHANT.PROFIT_SHARING.STATUS.REJECTED'):
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.details', ['id'=>$merchant->id, 'for'=>'edit']), 'label'=>trans('general.seller.profit-sharing.status.'.Config::get('constants.SELLER.PROFIT_SHARING.STATUS.ACCEPTED'))];
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.update-status', ['id'=>$merchant->id, 'status'=>strtolower('CLOSED')]), 'label'=>trans('general.seller.profit-sharing.status.'.Config::get('constants.SELLER.PROFIT_SHARING.STATUS.CLOSED'))];
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.delete', ['id'=>$merchant->id]), 'confirm'=>'Are you sure, you wants to delete?', 'label'=>'Delete'];
                            break;
                        case Config::get('constants.MERCHANT.PROFIT_SHARING.STATUS.CLOSED'):
                            $merchant->actions[] = ['url'=>route('admin.seller.commission.delete', ['id'=>$merchant->id]), 'confirm'=>'Are you sure, you wants to delete?', 'label'=>'Delete'];
                            break;
                    }
                    $merchant->status_class = Config::get('dispclass.seller.profit-sharing.status.'.$merchant->status);
                    $merchant->status = trans('general.seller.profit-sharing.status.'.$merchant->status);
                });
            }
            return $merchants;
        }
    }
	
	public function getProfitSharingDetails (array $arr = array())
    {
        $merchant['details'] = null;
        $merchant['new_request'] = null;
        $merchant['current_details'] = null;
        extract($arr);
        $mr_details = DB::table(Config::get('tables.PROFIT_SHARING').' as ps')
						->join(Config::get('tables.SUPPLIER_MST').' as sm', 'sm.supplier_id', '=', 'ps.supplier_id')
						->join(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'sm.account_id')
						->join(Config::get('tables.ADDRESS_MST').' as add', function($join) 
						{
							$join->on('add.relative_post_id', '=', 'sm.supplier_id')
								 ->where('add.address_type_id', '=', 1)
								 ->where('add.post_type', '=', Config::get('constants.ACCOUNT_TYPE.SELLER'));
						})           
                ->leftjoin(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'add.country_id')
                ->leftjoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'add.state_id')                
                ->leftjoin(Config::get('tables.LOCATION_CITY').' as ll', 'll.city_id', '=', 'add.city_id')
                ->join(Config::get('tables.CASHBACK_SETTINGS').' as cs', 'cs.supplier_id', '=', 'ps.supplier_id')            
				->selectRaw('ps.created_on, ps.sps_id as id, sm.company_name, sm.supplier_code, ps.status, ps.profit_sharing, ps.cashback_on_pay, ps.cashback_on_redeem, ps.cashback_on_shop_and_earn, am.uname, am.mobile, am.email, CONCAT_WS(\', \', add.flatno_street, add.address, ll.city, ls.state, concat(lc.country, \'-\', add.postal_code)) as address, ps.supplier_id, cs.pay, cs.shop_and_earn as offer_cashback, cs.is_cashback_period, cs.member_redeem_wallets, cs.cashback_start, cs.cashback_end')
                ->where('sps_id', $id)
                ->first();
			
        if (!empty($mr_details))
        {

            $mr_details->created_on = showUTZ($mr_details->created_on, 'd-M-Y H:i:s');
            $mr_details->profit_sharing = !empty($mr_details->profit_sharing) ? $mr_details->profit_sharing.'%' : '-';
            $mr_details->profit_sharing_in_label = '%';
            ///$mr_details->store_name = !empty($mr_details->store_name) ? $mr_details->store_name : '-';
            //$mr_details->store_code = !empty($mr_details->store_code) ? $mr_details->store_code : '-';

            switch ($mr_details->status)
            {
                case Config::get('constants.SELLER.PROFIT_SHARING.STATUS.PENDING'):
                    $mr_details->action = ['url'=>route('admin.seller.commission.update-status', ['id'=>$mr_details->id, 'status'=>strtolower('ACCEPTED')]), 'label'=>trans('general.seller.profit-sharing.status.'.Config::get('constants.SELLER.PROFIT_SHARING.STATUS.ACCEPTED'))];
                    break;
                default:
                    $mr_details->action = ['url'=>route('admin.seller.commission.save', ['id'=>$mr_details->id]), 'label'=>trans('general.btn.save')];
            }
            $merchant['details'] = $mr_details;
        }
		
		
        $new_request = DB::table(Config::get('tables.PROFIT_SHARING').' as ps')
                ->selectRaw('ps.status, ps.profit_sharing, ps.cashback_on_pay, ps.cashback_on_redeem, ps.cashback_on_shop_and_earn')
                ->where('ps.status', Config::get('constants.SELLER.PROFIT_SHARING.STATUS.PENDING'))
                ->where('sps_id', $id)
                ->first();

        if (empty($new_request->status))
        {
            $merchant['new_request'] = $new_request;
        }		
        $current_details = DB::table(Config::get('tables.PROFIT_SHARING').' as ps')
                ->selectRaw('ps.status, ps.profit_sharing, ps.cashback_on_pay, ps.cashback_on_redeem, ps.cashback_on_shop_and_earn')
                ->where('ps.status', Config::get('constants.SELLER.PROFIT_SHARING.STATUS.ACCEPTED'))
                ->orderBy('ps.sps_id', 'DESC')                
                ->where('supplier_id', $mr_details->supplier_id)
                ->first();

        if (!empty($current_details))
        {
            $merchant['current_details'] = $current_details;
        }
        return $merchant;
    }
	
	public function profitSharingStatusUpdate (array $arr = array())
    {	
        $profit_share = [];
        extract($arr);
        $profit_share['updated_on'] = getGTZ();
        $profit_share['updated_by'] = $account_id;
        $profit_share['status'] = $status;		
        $query = DB::table(Config::get('tables.PROFIT_SHARING'))
                ->where('sps_id', $id);
        switch ($status)
        {
            case Config::get('constants.SELLER.PROFIT_SHARING.STATUS.ACCEPTED'):
                $query->whereIn('status', [Config::get('constants.SELLER.PROFIT_SHARING.STATUS.PENDING'), Config::get('constants.SELLER.PROFIT_SHARING.STATUS.REJECTED')]);
                DB::beginTransaction();				
                if ($query->update($profit_share))
                {					
                    $profit_share['status'] = Config::get('constants.SELLER.PROFIT_SHARING.STATUS.CLOSED');
                    $details = DB::table(Config::get('tables.PROFIT_SHARING'))
                            ->where('sps_id', $id)
                            ->selectRaw('supplier_id, bcategory_id, cashback_on_shop_and_earn, is_cashback_period, cashback_start, cashback_end')
                            ->first();

                    $update['status'] = 2;
                    $update['updated_by'] = $account_id;
                    $update['updated_on'] = getGTZ();
                    //$update['bcategory_id'] = $details->bcategory_id;
                    /* DB::table(Config::get('tables.CASHBACK_OFFFERS'))
                            ->where('supplier_id', $details->supplier_id)
                            ->where('cboffer_type', Config::get('constants.CBOFFER_TYPE.DISCOUNT'))
                            ->where('is_deleted', config('constants.OFF'))
                            ->where('is_approved', Config::get('constants.ON'))
                            ->whereIn('status', [0, 1])
                            ->update($update); */

                    $update_offer['status'] = Config::get('constants.ON');
                    $update_offer['is_approved'] = Config::get('constants.ON');
                    $update_offer['updated_by'] = $account_id;
                    $update_offer['updated_on'] = getGTZ();
                    $update_offer['new_cashback'] = $details->cashback_on_shop_and_earn;
                    $update_offer['start_date'] = getGTZ($details->cashback_start, 'Y-m-d');
                    $update_offer['end_date'] = getGTZ($details->cashback_end, 'Y-m-d');

                    $update_setting['is_cashback_period'] = $details->is_cashback_period;
                    $update_setting['cashback_start'] = getGTZ($details->cashback_start, 'Y-m-d');
                    $update_setting['cashback_end'] = getGTZ($details->cashback_end, 'Y-m-d');
					
                    DB::table(Config::get('tables.CASHBACK_SETTINGS'))
                            ->where('supplier_id', $details->supplier_id)
                            ->update($update_setting);

                    /* DB::table(Config::get('tables.CASHBACK_OFFFERS'))
                            ->where('supplier_id', $details->supplier_id)
                            ->where('cboffer_type', Config::get('constants.CBOFFER_TYPE.DISCOUNT'))
                            ->where('is_deleted', Config::get('constants.OFF'))
                            ->where('bcategory_id', $details->bcategory_id)
                            //->where('status', config('constants.OFF'))
                            ->update($update_offer); */

                    DB::table(Config::get('tables.PROFIT_SHARING'))
                            ->where('sps_id', $id)
                            //->where('store_id', $details->store_id)
                            //->where('bcategory_id', $details->bcategory_id)
                            ->where('sps_id', '!=', $id)
                            ->where('status', '!=', Config::get('constants.SELLER.PROFIT_SHARING.STATUS.CLOSED'))
                            ->where('is_deleted', Config::get('constants.OFF'))
                            ->update($profit_share);

                    //$settings['completed_steps'] = DB::Raw('CONCAT(completed_steps,\',5\')');
                    //DB::table(Config::get('tables.MERCHANT_SETTINGS'))->where('supplier_id', $details->supplier_id)->update($settings);
                    DB::commit();
                    return true;
                }
                DB::rollback();
                return false;
            case Config::get('constants.SELLER.PROFIT_SHARING.STATUS.REJECTED'):
                $query->whereIn('status', [Config::get('constants.SELLER.PROFIT_SHARING.STATUS.PENDING'), Config::get('constants.SELLER.PROFIT_SHARING.STATUS.ACCEPTED')]);
                break;
            case Config::get('constants.SELLER.PROFIT_SHARING.STATUS.CLOSED'):
                $query->whereIn('status', [Config::get('constants.SELLER.PROFIT_SHARING.STATUS.ACCEPTED'), Config::get('constants.SELLER.PROFIT_SHARING.STATUS.REJECTED')]);
                break;
        }
        return $query->update($profit_share);
    }
  public function proof_details_list (array $arr = array(), $count = false)
    {
		
        $res = DB::table(Config::get('tables.SUPPLIER_MST').' as sm')
                ->join(Config::get('tables.ACCOUNT_TAX_DOCUMENTS').' as atd', 'atd.relative_post_id', '=', 'sm.supplier_id')
                ->join(Config::get('tables.ACCOUNT_MST').' as at', 'at.account_id', '=', 'sm.account_id')
                ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'at.account_id')
	             ->leftjoin(Config::get('tables.DOCUMENT_TYPES').' as dt', 'dt.document_type_id', '=', 'atd.id_proof_document_type_id')
                 ->leftjoin(Config::get('tables.DOCUMENT_TYPES').' as dts', 'dts.document_type_id', '=', 'atd.address_proof_document_type_id') 
				  ->leftjoin(Config::get('tables.TAX_CLASSES').' as tx', 'tx.tax_class_id', '=', 'atd.tax_class_id')
                ->where('atd.is_deleted', Config::get('constants.OFF'));
		        
        if (isset($arr['search_term']) && !empty($arr['search_term']))
        {
			
            $res->where('ad.firstname', 'like', '%'.$arr['search_term'].'%');
        }
        if (isset($arr['account_id']) && !empty($arr['account_id']))
        {
            $res->where('at.account_id', $arr['account_id']);
        }
        if (isset($arr['uname']) && !empty($arr['uname']))
        {
            $res->where('at.uname', $arr['uname']);
        }

        if (isset($arr['status']) && $arr['status'] != '')
        {
            $res->where('atd.status_id', $arr['status']);
        }
        if (isset($arr['type_filer']) && !empty($arr['type_filer']))
        {
            $res->where('atd.id_proof_document_type_id', $arr['type_filer']);
            $res->orwhere('atd.address_proof_document_type_id', $arr['type_filer']);
        }
        if (!empty($arr['from']))
        {
            $res->whereDate('atd.created_on', '>=', date('Y-m-d', strtotime($arr['from'])));
        }
        if (!empty($arr['to']))
        {
            $res->whereDate('atd.created_on', '<=', date('Y-m-d', strtotime($arr['to'])));
        }
        if (isset($arr['start']) && isset($arr['length']))
        {
            $res->skip($arr['start'])->take($arr['length']);
        }
        if (isset($arr['orderby']) && !empty($arr['orderby']))
        {
            $res->orderby('atd.created_on', $arr['order']);
        }
        else
        {
            $res->orderby('atd.created_on', 'DESC');
        }
        if (isset($arr['counts']) && $arr['counts'] == true)
        {
            return $res->count();
        }
        else
        {
         $verifications = $res->selectRaw('atd.tax_id,atd.updated_on,dt.type as id_proof_type,atd.pan_card_no,atd.pan_card_name,atd.pan_card_image,atd.id_proof_path,atd.id_proof_status,tx.tax_class,atd.tax_document_path,atd.status_id,atd.address_proof_status,atd.address_proof_path,dts.type as address_proof_type, atd.created_on,atd.is_registered,at.uname,sm.supplier_code,concat(ad.firstname,\' \',ad.lastname) as full_name')->get();
		 
		 array_walk($verifications, function($users)
            {

				$users->actions = [];
                 $users->actions[] = ['url'=>route('admin.seller.edit-profile', ['tax_id'=>$users->tax_id]), 'data-url'=>'{{route("admin.seller.edit-profile")}}','data-rule-required'=>'true', 'class'=>'edit_info','data'=>[
						'tax_id'=>$users->tax_id,
						'url'=>route('admin.seller.edit-profile')
						
                    ], 'redirect'=>false, 'label'=>'View Details'];
            });
            return $verifications;
        }
    }
	public function get_seller_proof_details($data = array(), $tax_id = ''){
		
		$res = DB::table(Config::get('tables.SUPPLIER_MST').' as sm')
                ->join(Config::get('tables.ACCOUNT_TAX_DOCUMENTS').' as atd', 'atd.relative_post_id', '=', 'sm.supplier_id')
                ->join(Config::get('tables.ACCOUNT_MST').' as at', 'at.account_id', '=', 'sm.account_id')
                ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'at.account_id')
	             ->leftjoin(Config::get('tables.DOCUMENT_TYPES').' as dt', 'dt.document_type_id', '=', 'atd.id_proof_document_type_id')
                 ->leftjoin(Config::get('tables.DOCUMENT_TYPES').' as dts', 'dts.document_type_id', '=', 'atd.address_proof_document_type_id') 
				  ->leftjoin(Config::get('tables.TAX_CLASSES').' as tx', 'tx.tax_class_id', '=', 'atd.tax_class_id')
                  ->where('atd.is_deleted', Config::get('constants.OFF'))
				  ->where('atd.tax_id','=',$tax_id)
	              ->selectRaw('atd.tax_id,atd.updated_on,dt.type as id_proof_type,atd.pan_card_no,atd.gst_status,atd.tan_status,atd.pan_card_name,atd.id_proof_path,atd.id_proof_status,tx.tax_class,atd.tax_document_path,atd.status_id,atd.address_proof_status,atd.address_proof_path,dts.type as address_proof_type,atd.is_registered,atd.created_on,atd.gstin_no,atd.tax_document_path,atd.tan_no,atd.tan_path,sm.company_name,at.uname,sm.supplier_code, concat(ad.firstname,\' \',ad.lastname) as full_name')
	               ->first();
				/* print_r($res);die;    */
		return $res;
	}
	
	public function update_proof_staus(array $data = array()){

		extract($data);
        $proof_details = [];
        $proof_details['updated_on'] = getGTZ();
        $proof_details['updated_by'] = $account_id;
       /*  $proof_details['status_id'] = $status; */
		/* if(!empty($status['status']){
			$proof_details['status_id']=$status['status'];
			/* $proof_details['id_proof_status']=Config::get('constants.TAX_PROOFDOC_STATUS.NOT_VERIFIED');
			$proof_details['address_proof_status']=Config::get('constants.TAX_PROOFDOC_STATUS.NOT_VERIFIED'); 
	    } */
		if(!empty($status['pan_status'])){
			$proof_details['pan_card_status']=$status['pan_status'];
		}
		if(!empty($status['id_proof_status'])){
			$proof_details['id_proof_status']=$status['id_proof_status'];
		}
		if(!empty($status['addres_status'])){
			$proof_details['address_proof_status']=$status['addres_status'];
		}
		if(!empty($status['tan_status'])){
			$proof_details['tan_status']=$status['tan_status'];
		}
		
		if(!empty($status['gst_status'])){
		
			$proof_details['gst_status']=$status['gst_status'];
		}
		
		if((isset($status['pan_status']) && $status['pan_status']==1) && (isset($status['id_proof_status']) && $status['id_proof_status']==1) && (isset($status['addres_status']) && $status['addres_status']==1) &&  (isset($status['gst_status']) && $status['gst_status']==1) && (isset($status['tan_status']) && $status['tan_status']==1))
		{
              $proof_details['status_id']=Config::get('constants.TAX_PROOFDOC_STATUS.VERIFIED');
			
		}
		else{
			$proof_details['status_id']=Config::get('constants.TAX_PROOFDOC_STATUS.NOT_VERIFIED');
		}
		
		/* if($status==Config::get('constants.TAX_DOCUMENT_STATUS.APPROVED')){
			$proof_details['status_id']=$status;
			$proof_details['id_proof_status']=Config::get('constants.TAX_PROOFDOC_STATUS.VERIFIED');
			$proof_details['address_proof_status']=Config::get('constants.TAX_PROOFDOC_STATUS.VERIFIED');
		}
		if($status==Config::get('constants.TAX_DOCUMENT_STATUS.REJECTED')){
			$proof_details['status_id']=$status;
			$proof_details['id_proof_status']=Config::get('constants.TAX_PROOFDOC_STATUS.REJECTED');
			$proof_details['address_proof_status']=Config::get('constants.TAX_PROOFDOC_STATUS.REJECTED');
		} */
			
        $qry = DB::table(Config::get('tables.ACCOUNT_TAX_DOCUMENTS'))
                ->where('tax_id', $tax_id)
			 	->update($proof_details);
             return $qry;
	}
	
	public function update_premium_status($arr){
		extract($arr);
		return DB::table(config('tables.STORES'))
				->where('store_code',$store_code)
				->update(['is_premium'=>$status]);
	}
 }
