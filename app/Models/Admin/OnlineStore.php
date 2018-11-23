<?php
namespace App\Models\Admin;
use DB;
use File;
use TWMailer;
use App\Models\BaseModel;
use App\Models\LocationModel;
use Config;

class OnlineStore extends BaseModel {
	
    public function __construct() {
        parent::__construct();		
		$this->lcObj = new LocationModel;
		
    }
public function get_banners_list(){
	
		$res = DB::table(Config::get('tables.ONLINE_STORE_BANNERS'))
				->where('is_deleted',Config::get('constants.OFF'))
				->select('banner_id','banner_path','banner_name')->get();		
		if(!empty($res)){
			
			array_walk($res,function($result){
				$result->banner_path = asset(Config::get('constants.AFFILIATE.STORE.BANNER_PATH.LG')).'/'.$result->banner_path;
			});
			return $res;
		}else{
			return [];
		}		
				
	}
	
	 public function getAffiliateSignupCategories (array $arr = array())
	 {
        extract($arr);
        $query = DB::table(Config::get('tables.BUSINESS_CATEGORY').' as a')
                ->join(Config::get('tables.BUSINESS_CATEGORY_TREE').' as c', 'a.bcategory_id', '=', 'c.bcategory_id')
                ->join(Config::get('tables.BUSINESS_CATEGORY_LANG').' as b', 'a.bcategory_id', '=', 'b.bcategory_id')
                ->where('a.status', '=',Config::get('constants.ACTIVE'))
                ->where('a.is_visible', '=', Config::get('constants.ON'))
                ->where('a.is_deleted', '=', Config::get('constants.OFF'))
                ->where('a.category_type', '=', 3);
        return $query->selectRaw('a.bcategory_id as id,IF(c.parent_bcategory_id=c.root_bcategory_id,0,c.parent_bcategory_id) as parent_id,b.bcategory_name as name')->get();
    }
	  public function saveAffiliate (array $arr = array(), $id = '')
      {
        extract($arr);
        $listArr = [];
        if (!empty($arr))
        {
            $store['store_name']  = $affiliate['store_name'];
            $store['store_logo']  = isset($affiliate['store_logo']) ? $affiliate['store_logo'] : Null;
            $store['mobile'] 	  = isset($affiliate['mobile']) ? $affiliate['mobile'] : Null;
            $store['program_id']  = $affiliate['program_id'];
            //$store['assoc_bcategories'] = implode(',', $affiliate['category_id']);
			if(!empty($affiliate['category_id'])){
				    $store['category_id'] = implode(',', $affiliate['category_id']); 
			}
            
            $store['expired_on']  = $affiliate['expired_on'];
            $store['logo_url'] 	  = $affiliate['logo_url'];
            //$store['mrid'] 		  = $affiliate['aff_netwrk']; 
			$store['supplier_id'] = $affiliate['aff_netwrk'];  //mycode
            $store['created_on']  = getGTZ();
            $store['is_online']   = Config::get('constants.ON');
            $store['status'] 	  = $affiliate['status'];
            $store['banner_id']   = isset($store_banner)?$store_banner:0;
            $store['store_slug']  = $this->slug($affiliate['store_name']);
            $store['is_approved'] = Config::get('constants.ON');
            $store['is_featured'] = (isset($affiliate['is_featured']) && !empty($affiliate['is_featured'])) ? $affiliate['is_featured'] :Config::get('constants.OFF');
            $store['updated_by'] = $account_id;
            if($desc_type == 2)
            {
				if(!empty($affiliate['cb_name'][0])){
					
					$cbArr = explode(',',$affiliate['cb_name'][0]);
					$cbvalue = explode(',',$affiliate['cb_val'][0]);
					foreach ($cbArr as $key=> $cbName)
					{
						$listArr[] = ['desc'=>$cbName,'val'=>(!empty($cbvalue[$key]))?$cbvalue[$key]:0];
					}
					$extra['description'] = json_encode($listArr);
				}
	        }
            else
            {
                $extra['description'] = $affiliate['description'];
            }
			
            $extra['tags'] = $affiliate['tags'];
            $extra['desc_type'] = $desc_type;
            $extra['dos_desc'] = isset($affiliate['dos_desc']) ? $affiliate['dos_desc'] : Null;
            $extra['dont_desc'] = isset($affiliate['dont_desc']) ? $affiliate['dont_desc'] : Null;
            $extra['title'] = $affiliate['meta_title'];
            $extra['keyword'] = $affiliate['meta_keyword'];
            $extra['meta_desc'] = $affiliate['meta_desc'];
            $extra['cb_notes'] = $affiliate['cb_notes'];
            $extra['url'] = $affiliate['url'];
            $extra['website'] = $affiliate['website_url'];
            $store_setting['cb_tracking_days'] = $affiliate['cb_traking_period'];
            $store_setting['cb_waiting_days'] = $affiliate['cb_waiting_period'];
			
            if (!empty($id))
            {
                $store['updated_on'] = getGTZ();
                $store               = array_filter($store);				
                DB::table(Config::get('tables.STORES'))
                        ->where('store_id', $id)
                        ->update($store);

                DB::table(Config::get('tables.STORES_EXTRAS'))
                        ->where('store_id', $id)
                        ->update($extra);

                $get_offer = DB::table(Config::get('tables.CASHBACK_OFFERS'))
                        ->where('store_id', $id)
                        ->where('supplier_id', $affiliate['aff_netwrk'])
                        ->where('cboffer_type', Config::get('constants.CBOFFER_TYPE.DISCOUNT'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->count();

                if (!DB::table(Config::get('tables.STORE_SETTINGS'))
                                ->where('store_id', $id)
                                ->exists())
                {
                    $store_setting['store_id'] = $id;
                    DB::table(Config::get('tables.STORE_SETTINGS'))
                            ->insertGetID($store_setting);
                }
                else
                {
                    DB::table(Config::get('tables.STORE_SETTINGS'))
                            ->where('store_id', $id)
                            ->update($store_setting);
                }
                $ins_offer['cashback_type'] = $affiliate['cashback_type'];
                $ins_offer['supplier_id'] = $affiliate['aff_netwrk'];
                $ins_offer['store_id'] = $id;
                $ins_offer['cboffer_type'] = Config::get('constants.ON');
                $ins_offer['store_id'] = $id;
                $ins_offer['currency_id'] = $currency_id;
                $ins_offer['old_cashback'] = !empty($affiliate['old_cashback']) ? $affiliate['old_cashback'] : 0;
                $ins_offer['new_cashback'] = !empty($affiliate['cashback']) ? $affiliate['cashback'] : 0;
                $ins_offer['expired_on'] = getGTZ();
			
			
                $countries = explode(',', $country);
                foreach ($countries as $val)
                {
                    $coun['relative_post_id'] = $val;
                    $coun['post_type'] = Config::get('constants.POST_TYPE.COUNTRY');
                    $coun['store_id'] = $id;
                    $coun['created_on'] = getGTZ();
                    if (!DB::table(Config::get('tables.ONLINE_STORE_SETTINGS'))->where('relative_post_id', '=', $val)->where('store_id', '=', $id)->where('is_deleted', Config::get('constants.OFF'))->exists())
                    {
                        DB::table(Config::get('tables.ONLINE_STORE_SETTINGS'))->insert($coun);
                    }
                }
			  
                if ($get_offer != 0)
                {
                    DB::table(Config::get('tables.CASHBACK_OFFERS'))
                            ->where('store_id', $id)
                            //->where('mrid', $affiliate['aff_netwrk'])
                            ->where('supplier_id', $affiliate['aff_netwrk'])
                            ->where('cboffer_type', Config::get('constants.CBOFFER_TYPE.DISCOUNT'))
                            ->where('is_deleted', Config::get('constants.OFF'))
                            ->update($ins_offer);
                }
                else
                {
                    DB::table(Config::get('tables.CASHBACK_OFFERS'))
                            ->insertGetID($ins_offer);
                }
                return true;
            }
            else
            {
                $affiliate['created_on'] = getGTZ();
                $store = array_filter($store);
                $store_id = DB::table(Config::get('tables.STORES'))
                        ->insertGetID($store);

                $store_setting['store_id'] = $store_id;
                DB::table(Config::get('tables.STORE_SETTINGS'))
                        ->insertGetID($store_setting);
                if (!empty($store_id))
                {
                    $update['store_code'] = $store['supplier_id'].$store_id.rand('000', '999');
                    $update['updated_on'] = getGTZ();
                    DB::table(Config::get('tables.STORES'))
                            ->where('store_id', $store_id)
                            ->update($update);
                }
                $extra['store_id'] = $store_id;
                DB::table(Config::get('tables.STORES_EXTRAS'))
                        ->insertGetID($extra);
	
                $countries = explode(',', $country);
		        foreach ($countries as $val)
                {
                    $coun['relative_post_id'] = $val;
                    $coun['post_type'] = Config::get('constants.POST_TYPE.COUNTRY');
                    $coun['store_id'] = $store_id;
                    $coun['created_on'] = getGTZ();
                    DB::table(Config::get('tables.ONLINE_STORE_SETTINGS'))->insert($coun);
                }
			
                if (isset($affiliate['cashback']) && !empty($affiliate['cashback']))
                {
                    $ins_offer['cashback_type'] = $affiliate['cashback_type'];
                  //$ins_offer['mrid'] = $affiliate['aff_netwrk'];
                    $ins_offer['supplier_id'] = $affiliate['aff_netwrk'];
                    $ins_offer['store_id'] = $store_id;
                    $ins_offer['cboffer_type'] = Config::get('constants.ON');
                    $ins_offer['store_id'] = $store_id;
					$ins_offer['currency_id'] = $currency_id;
                    $ins_offer['old_cashback'] = !empty($affiliate['old_cashback']) ? $affiliate['old_cashback'] : 0;
                    $ins_offer['new_cashback'] = !empty($affiliate['cashback']) ? $affiliate['cashback'] : 0;
                    $ins_offer['expired_on'] = getGTZ();
                    DB::table(Config::get('tables.CASHBACK_OFFERS'))
                            ->insertGetID($ins_offer);
                }
                return true;
            }
        }
    }
    public function country_lists ()
    {
        $country = DB::table(Config::get('tables.LOCATION_COUNTRY').' as lcoun')
                ->join(Config::get('tables.CURRENCIES').' as c', 'lcoun.currency_id', '=', 'c.currency_id')
                ->selectRAW('lcoun.country_id,lcoun.country,lcoun.currency_id,c.currency as code')
                ->where('lcoun.status',Config::get('constants.ACTIVE'))
                ->get();
        return $country;
    }
	public function getAffiliateList (array $arr = array(), $count = false)
    {
		
        extract($arr);
        $affiliates = DB::table(Config::get('tables.STORES').' as a')
                ->join(Config::get('tables.ONLINE_STORE_SETTINGS').' as oss', function($oss)
                {
                    $oss->on('oss.store_id', '=', 'a.store_id')
                    ->where('oss.post_type', '=', Config::get('constants.POST_TYPE.COUNTRY'))
                    ->where('oss.is_deleted', '=', Config::get('constants.OFF'));
                })
                ->join(Config::get('tables.STORES_EXTRAS').' as mx', 'mx.store_id', '=', 'a.store_id')
                ->join(Config::get('tables.SUPPLIER_MST').' as m', 'm.supplier_id', '=', 'a.supplier_id')
                ->leftjoin(Config::get('tables.LOCATION_COUNTRY').' as ctr', 'ctr.country_id', '=', 'oss.relative_post_id')
                ->where('a.is_online', Config::get('constants.ON'))
                ->where('m.is_online', Config::get('constants.ON'))
                ->where('a.is_deleted', Config::get('constants.OFF'));
				

        if (isset($from) && !empty($from))
        {
            $affiliates->whereDate('a.created_on', '<=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $affiliates->whereDate('a.created_on', '>=', getGTZ($to, 'Y-m-d'));
        }
		if (isset($top_sellers) && !empty($top_sellers))
        {
            $affiliates->where('a.top_seller',$this->config->get('constants.ON'));
        }
        if (isset($country) && !empty($country))
        {
            $affiliates->where('oss.relative_post_id', '=', $country);
        }
        if (isset($search_text) && !empty($search_text))
        {
            $affiliates->where(function($query) use ($search_text)
            {
                $query->where('a.store_name', 'like', '%'.$search_text.'%')
                        ->orWhere('a.store_code', 'LIKE', '%'.$search_text.'%')
                        ->orWhere('m.company_name', 'LIKE', '%'.$search_text.'%');
            });
        }
        if (isset($orderby) && isset($order))
        {
            if ($orderby == 'created_on')
            {
                $affiliates->orderBy('a.created_on', $order);
            }
            elseif ($orderby == 'store_name')
            {
                $affiliates->orderBy('a.store_name', $order);
            }
            elseif ($orderby == 'company_name')
            {
                $affiliates->orderBy('m.company_name', $order);
            }
            elseif ($orderby == 'status')
            {
                $affiliates->orderBy('a.status', $order);
            }
            else
            {
                $affiliates->orderby('a.store_id', 'DESC');
            }
        }
        else
        {
            $affiliates->orderby('a.store_id', 'DESC');
        }
        if ($count)
        {
            return $affiliates->count();
        }
        else
        {
            if (isset($start) && isset($length))
            {
                $affiliates->skip($start)->take($length);
            }
            $affiliates = $affiliates->selectRaw('a.is_online, a.logo_url, a.store_id, a.supplier_id, a.store_name, a.store_code, a.store_logo, a.is_approved, a.status as store_status, a.created_on, m.*, a.is_featured, ctr.country,mx.url')
                    ->get();
           
            if (!empty($affiliates))
            {
			
                array_walk($affiliates, function(&$affiliate)
                {
                    $affiliate->created_on = (!is_null($affiliate->created_on) || $affiliate->created_on != '0000-00-00 00:00:00') ? showUTZ($affiliate->created_on, 'Y-M-d H:i:s') : '';
                    $affiliate->store_code = '#'.$affiliate->store_code;
                    $affiliate->store_logo = !empty($affiliate->store_logo) ? asset(Config::get('constants.AFFILIATE.STORE.LOGO_PATH.LOCAL').$affiliate->store_logo) : asset(Config::get('constants.AFFILIATE.STORE.LOGO_PATH.DEFAULT'));
                    $affiliate->mrlogo = !empty($affiliate->mrlogo) ? asset(Config::get('constants.AFFILIATE.NETWORK.LOGO_PATH.WEB').$affiliate->mrlogo) : asset(Config::get('constants.AFFILIATE.NETWORK.LOGO_PATH.DEFAULT'));
                    $affiliate->is_online = Config::get('constants.IS_ONLNE_STORE.'.$affiliate->is_online);
					$affiliate->is_featured = trans('general.affiliates.featured.'.$affiliate->is_featured);
					$affiliate->status_class = Config::get('dispclass.affiliates.status.'.$affiliate->store_status);
                    $affiliate->status = trans('general.affiliates.status.'.$affiliate->store_status);
					
                    $affiliate->actions = [];
				     $affiliate->actions[] = ['url'=>route('admin.online.details', ['id'=>$affiliate->store_id]), 'label'=>trans('general.edit')];
				     if ($affiliate->store_status == Config::get('constants.AFFILIATE.STATUS.INACTIVE'))
                    {
                        $affiliate->actions[] = ['url'=>route('admin.online.update-status', ['id'=>$affiliate->store_id, 'status'=>strtolower('ACTIVE')]), 'label'=>trans('general.affiliates.status.'.Config::get('constants.AFFILIATE.STATUS.ACTIVE'))];
                    }
                    elseif ($affiliate->store_status == Config::get('constants.AFFILIATE.STATUS.ACTIVE'))
                    {
                        $affiliate->actions[] = ['url'=>route('admin.online.update-status', ['id'=>$affiliate->store_id, 'status'=>strtolower('INACTIVE')]), 'label'=>trans('general.affiliates.status.'.Config::get('constants.AFFILIATE.STATUS.INACTIVE'))];
                    } 
			       $affiliate->actions[] = ['url'=>route('admin.online.delete', ['id'=>$affiliate->store_id]), 'data'=>['confirm'=>trans('general.delete_confirm_msg')], 'label'=>trans('general.delete')];
                   
                  /* $affiliate->actions[] = ['url'=>route('admin.affiliates.details', ['id'=>$affiliate->store_id]), 'label'=>trans('general.edit')];
                    //$affiliate->actions[] = ['url'=>route('admin.affiliates.add-country', ['id'=>$affiliate->store_id]), 'redirect'=>true, 'label'=>'Update Country'];
                    $affiliate->is_featured = trans('general.affiliates.featured.'.$affiliate->is_featured);
				
                   
                    $affiliate->actions[] = ['url'=>route('admin.affiliates.delete', ['id'=>$affiliate->store_id]), 'data'=>['confirm'=>trans('general.delete_confirm_msg')], 'label'=>trans('general.delete')];
                   
				   $affiliate->status_class = $this->config->get('dispclass.affiliates.status.'.$affiliate->store_status);
                    $affiliate->status = trans('general.affiliates.status.'.$affiliate->store_status);
                    $affiliate->actions[] = ['url'=>route('admin.affiliates.coupons.list', ['id'=>$affiliate->store_id]), 'redirect'=>true, 'label'=>trans('admin/affiliate.coupons')];
					$affiliate->actions[] = ['url'=>route('admin.affiliates.details', ['id'=>$affiliate->store_id]), 'redirect'=>true, 'label'=>trans('admin/affiliate.details')]; */
                });
            }
            
            return $affiliates;
        }
    }
     public function updateAffiliateStatus (array $arr = array())
     {
        extract($arr);
        $affiliate = [];
        $affiliate['updated_on'] = getGTZ();
        $affiliate['status'] = $status;
        $query = DB::table(Config::get('tables.STORES'))
                ->where('store_id', $id);
        if ($status == Config::get('constants.AFFILIATE.STATUS.ACTIVE'))
        {
            $query->where('status', Config::get('constants.AFFILIATE.STATUS.INACTIVE'));
        }
        elseif ($status == Config::get('constants.AFFILIATE.STATUS.INACTIVE'))
        {
            $query->where('status', Config::get('constants.AFFILIATE.STATUS.ACTIVE'));
        }
        return $query->update($affiliate);
    }
	public function affiliateDelete (array $arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.STORES'))
                        ->where('store_id', $id)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->update(['is_deleted'=>Config::get('constants.ON'), 'updated_on'=>getGTZ()]);
    }

    public function affiliateDetails (array $arr = array())
    {
        extract($arr);
        if (isset($id) && $id > 0)
        {
            $res = DB::table(Config::get('tables.STORES').' as a')
                    ->join(Config::get('tables.STORE_SETTINGS').' as msst', 'msst.store_id', '=', 'a.store_id')
                    ->join(Config::get('tables.SUPPLIER_MST').' as mst', 'mst.supplier_id', '=', 'a.supplier_id')
                    ->leftjoin(Config::get('tables.STORES_EXTRAS').' as mse', 'mse.store_id', '=', 'a.store_id')
					->leftjoin(Config::get('tables.ONLINE_STORE_BANNERS').' as br', 'br.banner_id', '=', 'a.banner_id')
                    ->leftjoin(Config::get('tables.CASHBACK_OFFERS').' as caf', function($j)
              {
                $j->on('caf.store_id', '=', 'a.store_id');
                $j->on('caf.supplier_id', '=', 'a.supplier_id');
             });
            $res->where('a.is_deleted', Config::get('constants.OFF'))
                    ->where('a.store_id', $id)
                    ->selectRaw('a.logo_url,a.store_id,a.supplier_id,a.store_name,a.category_id as category_id, a.program_id,a.store_logo,
					a.is_featured,mse.website,mse.description,mse.tags,mse.title, mse.keyword,caf.cashback_type,
					caf.new_cashback,caf.old_cashback,a.status, a.expired_on,mse.url,mse.dos_desc,mse.dont_desc,mse.conditions,
					msst.cb_tracking_days,msst.cb_waiting_days,mse.desc_type,mse.meta_desc,mse.cb_notes,a.banner_id,br.banner_path,mst.company_name');
            $result = $res->first();
		
            if (!empty($result))
            {
                $result->expired_on = (!is_null($result->expired_on) || $result->expired_on != '0000-00-00 00:00:00') ? showUTZ($result->expired_on, 'Y-m-d') : '';
				$result->banner_path = asset(Config::get('constants.AFFILIATE.STORE.BANNER_PATH.LG')).'/'.$result->banner_path;
                $result->store_logo = !empty($result->store_logo) ? asset(Config::get('constants.AFFILIATE.STORE.LOGO_PATH.LOCAL').$result->store_logo) : asset(Config::get('constants.AFFILIATE.STORE.LOGO_PATH.DEFAULT'));
                $countries = DB::table(Config::get('tables.ONLINE_STORE_SETTINGS'))
                        ->where('store_id', $id)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->pluck('relative_post_id');
                $result->country_ids = $countries;
				if($result->desc_type == 2){
					$result->description = json_decode($result->description,true);
				}
                return $result;
            }
        }
        else
        {
            return false;
        }
    }
	
	public function getOnlineCategory_list (array $data = array(), $count = false)
    {
        extract($data);
        $categories = DB::table(Config::get('tables.BUSINESS_CATEGORY').' as bc')
                ->leftjoin(Config::get('tables.BUSINESS_CATEGORY_TREE').' as bct', 'bct.bcategory_id', '=', 'bc.bcategory_id')
                ->leftjoin(Config::get('tables.BUSINESS_CATEGORY_LANG').' as bcl', 'bcl.bcategory_id', '=', 'bc.bcategory_id')
                ->leftjoin(Config::get('tables.BUSINESS_CATEGORY_LANG').' as bcl2', 'bcl2.bcategory_id', '=', 'bct.parent_bcategory_id')
                ->where('bcl.lang_id', Config::get('app.locale_id'))
                ->where('bc.is_deleted', Config::get('constants.OFF'))
                ->where('bc.category_type', Config::get('constants.BCATEGORY_TYPE.ONLINE_STORE'));

        if (isset($from) && !empty($from))
        {
            $categories->whereDate('bc.created_on', '>=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $categories->whereDate('bc.created_on', '<=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($search_text) && !empty($search_text))
        {
            $categories->where('bcl.bcategory_name', 'like', '%'.$search_text.'%');
            $categories->orwhere('bcl2.bcategory_name', 'like', '%'.$search_text.'%');
        }
        if (!empty($parent_category_id) && isset($parent_category_id))
        {
            $categories->where('bcl.parent_category_id', $parent_category_id);
        }
        if (isset($start) && isset($length))
        {
            $categories->skip($start)->take($length);
        }
        if ($count)
        {
            return $categories->count();
        }
        else
        {
            $categories = $categories->selectRaw('bc.*,bcl.lang_id,bcl.bcategory_name,bct.*,bcl2.bcategory_name as parent_name')
                    ->orderBy('bc.created_on', 'DESC')
                    ->get();
            if (!empty($categories))
            {
                array_walk($categories, function(&$categoty)
                {
                    $categoty->created_on = !empty($categoty->created_on) ? showUTZ($categoty->created_on, 'd-M-Y H:i:s') : null;
                    $categoty->status_name = trans('admin/online_category/category.category_status.'.$categoty->status);
                    $categoty->status_dispCls = $this->config->get('dispclass.category_status.'.$categoty->status);
                    $categoty->parent_name_lbl = trans('admin/online_category/category.parent_name_lbl');
                });
            }
            return $categories;
        }
    }
    public function getOnlineCategoriesMin (array $wdata = [])
    {  
        extract($wdata);
	$parent_data=DB::table(Config::get('tables.BUSINESS_CATEGORY_TREE').' as bct')
			             ->where('bct.parent_bcategory_id','=',0)
						 ->where('bct.category_type', Config::get('constants.BCATEGORY_TYPE.ONLINE_STORE'))
						 ->where('bct.cat_lftnode','=',1)
						 ->first();
						 
        $qry = DB::table(Config::get('tables.BUSINESS_CATEGORY').' as cat')
                ->join(Config::get('tables.BUSINESS_CATEGORY_LANG').' as catL', 'cat.bcategory_id', '=', 'catL.bcategory_id')
                ->join(Config::get('tables.BUSINESS_CATEGORY_TREE').' as catT', 'cat.bcategory_id', '=', 'catT.bcategory_id')
                ->where('catL.lang_id', Config::get('app.locale_id'))
                ->where('cat.category_type', Config::get('constants.BCATEGORY_TYPE.ONLINE_STORE'))				
				->where('cat.status', Config::get('constants.ON'))
                ->where('cat.is_deleted', Config::get('constants.OFF'));
				
        if (isset($excbcat_id) && !empty($excbcat_id))
        {
            $qry->where('cat.bcategory_id', '!=', $excbcat_id);
        }
        if (isset($cat_id) && !empty($cat_id))
        {
            $qry->where('catT.parent_bcategory_id', '=', $cat_id);
        }
        if (isset($excpbcat_id) && !empty($excpbcat_id))
        {
            $qry->where('catT.parent_bcategory_id', '=', $excpbcat_id);
        }
        if (!empty($pbcat_id))
        {
            $qry->where('catT.parent_bcategory_id', '=', $pbcat_id);
        }
        else
        {
	         $qry->where('catT.parent_bcategory_id','=',0)
                   ->orWhereNull('catT.parent_bcategory_id');
        } 
        $qry->selectRaw('catL.bcategory_name,cat.bcategory_id,catT.parent_bcategory_id,catT.root_bcategory_id,if(catT.cat_lftnode = catT.cat_rgtnode - 1,0,1) as haschild,catT.cat_lftnode,catT.cat_rgtnode');
        $qry->orderBy('catL.bcategory_name', 'ASC');
        $result = $qry->get();
	
        if (!empty($result))
        {
            return $result;
        }
        return NULL;
    }
  public function checkCategorySlug (array $wdata = [])
    {
        extract($wdata);
        $qry = DB::table(Config::get('tables.BUSINESS_CATEGORY'))
                ->where('slug', $bcategory_slug)
                ->where('is_deleted', 0);
        if (isset($bcategory_id) && !empty($bcategory_id))
        {
            $qry->where('bcategory_id', '!=', $bcategory_id);
        }
        if (isset($category_type) && !empty($category_type))
        {
            $qry->where('category_type', $category_type);
        }
        $category_id = $qry->pluck('bcategory_id');
        if (!$category_id)
        {
            return true;
        }
        return false;
    }
	
    public function saveOnlineCategory (array $arr = array())
    { 
       DB::Transaction(function() use($arr)
        {
            extract($arr);
		/* print_r($arr); */
         if (isset($category_id) && !empty($category_id))
            {
                $old_bcat_parentID = DB::table(Config::get('tables.BUSINESS_CATEGORY_TREE'))
                        ->where('bcategory_id', $category_id)
                        ->select('parent_bcategory_id')
                        ->first();
					
              /* if (!empty($old_bcat_parentID->parent_bcategory_id))
                {
                    if (isset($new_parent_category_id))
                    {
                        if ($old_bcat_parentID->parent_bcategory_id != $new_parent_category_id)
                        {
						
                            $result = $this->shifting_onlineCategoryTree(['new_parent_category_id'=>$new_parent_category_id, 'bcategory_id'=>$category_id]);
                            if ($result == 2)
                            {
                                $op['status'] = 'Error';
                                $op['msg'] = trans('admin/online_category/category.main_catNot_possible');
                            }
                            elseif ($result == 0)
                            {
                                $update['bc.updated_on'] = getGTZ();
                                $update['bcl.bcategory_name'] = $bcategory_name;
                                $update['bc.slug'] = $bcategory_slug;
                                $update['bc.category_img'] = $category_img;
                                $update['bcl.meta_title'] = $meta_title;
                                $update['bcl.meta_desc'] = $meta_desc;
                                $update['bcl.meta_keywords'] = $meta_keywords;
                                $update['bc.category_img'] = $category_img;
                                $update['bc.updated_by'] = $updated_by;
                                $update = array_filter($update);
                                $categories = DB::table(Config::get('tables.BUSINESS_CATEGORY').' as bc')
                                        ->leftjoin(Config::get('tables.BUSINESS_CATEGORY_LANG').' as bcl', 'bcl.bcategory_id', '=', 'bc.bcategory_id')
                                        ->where('bc.category_type', Config::get('constants.BCATEGORY_TYPE.ONLINE_STORE'))
                                        ->where('bcl.lang_id', Config::get('app.locale_id'))
                                        ->where('bc.bcategory_id', $category_id)
                                        ->update($update);
                                return $categories;
                            }
                        }
                    }
                } */
                $update['bc.updated_on'] = getGTZ();
                $update['bcl.bcategory_name'] = $bcategory_name;
                $update['bc.slug'] = $bcategory_slug;
                $update['bc.category_img'] = $category_img;
                $update['bcl.meta_title'] = $meta_title;
                $update['bcl.meta_desc'] = $meta_desc;
                $update['bcl.meta_keywords'] = $meta_keywords;
                $update['bc.category_img'] = $category_img;
                $update['bc.updated_by'] = $updated_by;
                $update = array_filter($update);
                $categories = DB::table(Config::get('tables.BUSINESS_CATEGORY').' as bc')
                        ->leftjoin(Config::get('tables.BUSINESS_CATEGORY_LANG').' as bcl', 'bcl.bcategory_id', '=', 'bc.bcategory_id')
                        ->where('bc.category_type', Config::get('constants.BCATEGORY_TYPE.ONLINE_STORE'))
                        ->where('bcl.lang_id', Config::get('app.locale_id'))
                        ->where('bc.bcategory_id', $category_id)
                        ->update($update);
                return $categories;
            }
            else
            {
                $bcategory['slug']          = $category['bcategory_slug'];
                $bcategory['created_on']    = getGTZ();
                $bcategory['category_img']  = $category['category_image'];
                $bcategory['category_type'] = Config::get('constants.BCATEGORY_TYPE.ONLINE_STORE');
                $bcategory['created_by']    = $category['created_by'];
                $bcategory['status']        = Config::get('constants.CATEGORY_STATUS.ACTIVE');
                $bcategory['is_visible']    =  Config::get('constants.ON');
				$category_parent=[];
                $category_parent['bcategory_id'] = DB::table(Config::get('tables.BUSINESS_CATEGORY'))
                        ->insertGetId($bcategory);
			
                $bcategory_lang['bcategory_id'] = $category_parent['bcategory_id'];
                $bcategory_lang['lang_id'] = Config::get('app.locale_id');
                $bcategory_lang['bcategory_name'] = $category['bcategory_name'];
                $bcategory_lang['meta_title'] = $category['meta_title'];
                $bcategory_lang['meta_desc'] = $category['meta_desc'];
                $bcategory_lang['meta_keywords'] = $category['meta_keywords'];
                $category_lang['bcategory_id'] = DB::table(Config::get('tables.BUSINESS_CATEGORY_LANG'))
                        ->insertGetId($bcategory_lang); 
						
                if ($category_parent['bcategory_id'])
                {
				   $category_parent['parent_bcategory_id'] = $category['parent_bcategory_id'];
                    $category_parent['category_type'] = $bcategory['category_type'];					
                    return $this->save_parent_onlineCategory(['category_parent'=>$category_parent, 'admin_id'=>$category['admin_id']]);
                }
            }
		});
        return true;
    }

    public function save_parent_onlineCategory (array $arr)
    {  
      
        extract($arr);
        if (DB::table($this->config->get('tables.BUSINESS_CATEGORY_TREE'))
                        ->where('bcategory_id', $category_parent['bcategory_id'])
                        ->exists())
        {
            $category_parent['updated_on'] = getGTZ();
            return DB::table(Config::get('tables.BUSINESS_CATEGORY_TREE'))
                            ->where('bcategory_id', $category_parent['bcategory_id'])
                            ->update($category_parent);
        }
        else
        { 

            $node_val = $this->adding_onlineCategoryNode(['category_parent_id'=>$category_parent['parent_bcategory_id']]);
		
            if (!empty($node_val))
            {
                $category_parent['root_bcategory_id'] = $node_val['root_id'];
                $category_parent['cat_lftnode'] = $node_val['cat_lftnode'];
                $category_parent['cat_rgtnode'] = $node_val['cat_rgtnode'];
            }
            else
            {
           
				
				$parent_check=DB::table(Config::get('tables.BUSINESS_CATEGORY_TREE'))
				                ->where('category_type',Config::get('constants.BCATEGORY_TYPE.ONLINE_STORE'))
                                ->where('cat_lftnode', 1)
                                ->where('parent_bcategory_id',0)
                                 ->first();
				if(!empty($parent_check)){
					$category_parent['parent_bcategory_id']=$parent_check->root_bcategory_id;
					$category_parent['root_bcategory_id'] = $parent_check->root_bcategory_id;
				}
				else {
                $category_parent['parent_bcategory_id'] = 0;
				$category_parent['root_bcategory_id'] = $category_parent['bcategory_id'];
				}
				
                $category_parent['cat_lftnode'] = 1;
                $category_parent['cat_rgtnode'] = 2;
            }
			
            $catTreeID = DB::table(Config::get('tables.BUSINESS_CATEGORY_TREE'))
                    ->insertGetID($category_parent);
            return $catTreeID;
        }
        return true;
    }
  public function adding_onlineCategoryNode (array $arr)
    {  
        extract($arr);
	
        $increament_val = 2;
        $catinfo = $this->editOnlineCategory(array('bcategory_id'=>$category_parent_id));
        if (!empty($catinfo))
        {
            $rootId = $this->get_categoryRoot($catinfo->root_bcategory_id);
            $root_id = $rootId->bcategory_id;
            $cat_lftnode = 1;
            $cat_rgtnode = 2;
            if ($catinfo->childCounts > 0)
            {
                $updateFrom = $catinfo->cat_rgtnode;
                $cat_lftnode = $catinfo->cat_rgtnode;
                $cat_rgtnode = $cat_lftnode + 1;
            }
            else
            {
                $updateFrom = $catinfo->cat_lftnode;
                $cat_lftnode = $catinfo->cat_lftnode + 1;
                $cat_rgtnode = $cat_lftnode + 1;
            }
			
            DB::table(Config::get('tables.BUSINESS_CATEGORY_TREE'))
                    ->where('cat_rgtnode', '>=', $updateFrom)
                    ->where('root_bcategory_id', $root_id)
                    ->increment('cat_rgtnode', $increament_val);

            DB::table(Config::get('tables.BUSINESS_CATEGORY_TREE'))
                    ->where('cat_lftnode', '>', $updateFrom)
                    ->where('root_bcategory_id', $root_id)
                    ->increment('cat_lftnode', $increament_val);

            $newPos = ['cat_lftnode'=>$cat_lftnode, 'cat_rgtnode'=>$cat_rgtnode, 'root_id'=>$root_id];
            return $newPos;
        }
        return false;
    }
  public function editOnlineCategory (array $arr = array())
    {
        extract($arr);
        if (isset($bcategory_id) && $bcategory_id > 0)
        {
            $result = DB::table(Config::get('tables.BUSINESS_CATEGORY').' as pc')
                    ->leftjoin(Config::get('tables.BUSINESS_CATEGORY_TREE').' as pcp', 'pcp.bcategory_id', '=', 'pc.bcategory_id')
                    ->leftjoin(Config::get('tables.BUSINESS_CATEGORY_LANG').' as bcl', function($bcl)
                    {
                        $bcl->on('bcl.bcategory_id', '=', 'pc.bcategory_id')
                        ->where('bcl.lang_id', '=', Config::get('app.locale_id'));
                    })
                    ->where('pc.bcategory_id', $bcategory_id)
                     ->where('pc.is_deleted', Config::get('constants.OFF'))
                    ->where('pc.category_type', Config::get('constants.BCATEGORY_TYPE.ONLINE_STORE'))
                    ->selectRaw('pc.*,bcl.*,pcp.parent_bcategory_id,pcp.root_bcategory_id,pcp.cat_lftnode,pcp.cat_rgtnode,IF(pcp.cat_lftnode=pcp.cat_rgtnode-1,0,1) as childCounts,(select parent_bcategory_id from '.Config::get('tables.BUSINESS_CATEGORY_TREE').' where bcategory_id=pcp.parent_bcategory_id) as gparent_bcategory_id')
				->first();
				
            if (!empty($result))
            {
                $result->category_img = !empty($result->category_img) ? Config::get('constants.BCATEGORY_IMG_PATH.LOCAL').$result->category_img : NULL;
                return $result;
            }
        }
        return false;
    }
	
    public function get_categoryRoot ($rootId)
    {
        $root = DB::table(Config::get('tables.BUSINESS_CATEGORY_TREE').' as node')
                ->where('node.root_bcategory_id', $rootId)
                ->where('node.cat_lftnode', 1)
                ->select('node.bcategory_id')
                ->first();
        return $root;
    }
  public function shifting_onlineCategoryTree (array $arr)
    {
        extract($arr);
        if (isset($new_parent_category_id))
        {
            $catinfo = $this->editOnlineCategory(array('bcategory_id'=>$bcategory_id));
            $new_parent_catinfo = $this->editOnlineCategory(array('bcategory_id'=>$new_parent_category_id));
            if (!empty($catinfo) || !empty($new_parent_catinfo))
            {
                if ($catinfo->parent_bcategory_id != 0)
                {
                    // Updating rule
                    $ruleId = $bcategory_id;
                    $ruleLeftId = $catinfo->cat_lftnode;
                    $ruleRightId = $catinfo->cat_rgtnode;

                    // New parent rule
                    $newParentRuleId = $new_parent_category_id;
                    $newParentRuleLeftId = $new_parent_catinfo->cat_lftnode;
                    $newParentRuleRightId = $new_parent_catinfo->cat_rgtnode;

                    if ($newParentRuleRightId < $ruleRightId)
                    {
                        $ruleNewLeftId = $newParentRuleRightId;
                        $ruleNewRightId = $newParentRuleRightId + 1;
                    }
                    else if ($catinfo->cat_lftnode == ($catinfo->cat_rgtnode - 1))
                    {
                        $ruleNewLeftId = $newParentRuleRightId - 2;
                        $ruleNewRightId = $newParentRuleRightId - 1;
                    }
                    else
                    {
                        $dec = ( $catinfo->cat_rgtnode - $catinfo->cat_lftnode ) + 1;
                        $ruleNewLeftId = $newParentRuleRightId - $dec;
                        $ruleNewRightId = $newParentRuleRightId - 1;
                    }
                    $updated = getGTZ();
                    $dec = ( $catinfo->cat_rgtnode - $catinfo->cat_lftnode ) + 1;
                    $table = Config::get('tables.BUSINESS_CATEGORY_TREE');
                    DB::update("UPDATE $table
											SET cat_lftnode = CASE
												/* d */
												WHEN $ruleNewRightId > $ruleRightId AND
													 cat_lftnode > $ruleLeftId AND
													 cat_lftnode <= $ruleNewLeftId + 1 AND
													 cat_rgtnode > $ruleRightId THEN cat_lftnode - $dec

												WHEN $ruleNewRightId > $ruleRightId AND
													 cat_lftnode > $ruleLeftId AND
													 cat_lftnode <= $ruleNewLeftId + 1 AND
													 cat_rgtnode < $ruleRightId THEN cat_lftnode + $dec

												/* u */
												WHEN $ruleNewRightId < $ruleRightId AND
													 cat_lftnode >= $ruleNewLeftId AND
													 cat_lftnode < $ruleLeftId THEN cat_lftnode + 2
												ELSE cat_lftnode
											END,
											cat_rgtnode = CASE
												WHEN $ruleNewRightId > $ruleRightId AND
													 cat_rgtnode > $ruleRightId AND
													 cat_rgtnode <= $ruleNewRightId THEN cat_rgtnode - $dec
												WHEN $ruleNewRightId < $ruleRightId AND
													 cat_rgtnode >= $ruleNewLeftId AND
													 cat_rgtnode <= $ruleRightId THEN cat_rgtnode + 2
												ELSE cat_rgtnode
											END
											WHERE root_bcategory_id = $catinfo->root_bcategory_id");
                    DB::update("UPDATE $table
									SET parent_bcategory_id = $newParentRuleId,
										cat_lftnode = $ruleNewLeftId,
										cat_rgtnode = $ruleNewRightId
										WHERE bcategory_id = $ruleId AND root_bcategory_id = $catinfo->root_bcategory_id");
                    return 0;
                }
                return 2;
            }
        }
        return 1;
    }
	public function getOnlineCategorypath (array $wdata = [])
    {
        extract($wdata);
        $qry = DB::table(Config::get('tables.BUSINESS_CATEGORY').' as cat')
                ->join(Config::get('tables.BUSINESS_CATEGORY_LANG').' as catL', 'cat.bcategory_id', '=', 'catL.bcategory_id')
                ->join(Config::get('tables.BUSINESS_CATEGORY_TREE').' as catT', 'cat.bcategory_id', '=', 'catT.bcategory_id')
                ->where('catL.lang_id', Config::get('app.locale_id'))
                ->where('cat.category_type', Config::get('constants.BCATEGORY_TYPE.ONLINE_STORE'))
                ->where('cat.is_deleted', Config::get('constants.OFF'));
        if (!empty($root_bcategory_id) && !empty($cat_lftnode) && !empty($cat_rgtnode))
        {
            $qry->where('catT.cat_lftnode', '<', $cat_lftnode);
            $qry->where('catT.cat_rgtnode', '>', $cat_rgtnode);
            $qry->where('catT.root_bcategory_id', '=', $root_bcategory_id);
        }
        if (isset($parent_bcategory_id) && !empty($parent_bcategory_id))
        {
            $qry->where('catT.bcategory_id', '!=', $parent_bcategory_id);
        }
        $qry->selectRaw('catL.bcategory_name,cat.bcategory_id');
        $qry->orderBy('catT.cat_lftnode', 'ASC');

        $result = $qry->get();
        if (!empty($result))
        {
            return $result;
        }
        return NULL;
    }
	/* online Category change status */
    public function change_onlineCategory_status (array $data = [])
     {
        extract($data);
        $update['status'] = $status;
        $query = DB::table(Config::get('tables.BUSINESS_CATEGORY'))
                ->where('category_type', Config::get('constants.BCATEGORY_TYPE.ONLINE_STORE'))
                ->where('bcategory_id', $category_id);
        if ($status == Config::get('constants.CATEGORY_STATUS.ACTIVE'))
        {
            $query->where(function($sub)
            {
                $sub->where('status', Config::get('constants.CATEGORY_STATUS.INACTIVE'))
                        ->orwhere('status', Config::get('constants.CATEGORY_STATUS.DRAFT'));
            });
        }
        elseif ($status == Config::get('constants.CATEGORY_STATUS.INACTIVE'))
        {
            $query->where('status', Config::get('constants.CATEGORY_STATUS.ACTIVE'));
        }
        return $query->update($update);
    }
}