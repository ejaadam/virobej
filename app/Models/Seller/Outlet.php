<?php
namespace App\Models\Seller;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\Models\Commonsettings;
use Config;
use File;

class Outlet extends Model
{
	
	public function __construct(){
		$this->commObj = new Commonsettings();
	}

    public function store_list (array $arr = array(), $count = false)
    {
        extract($arr);
        $qry = DB::table(Config::get('tables.STORES').' as s')
                ->join(Config::get('tables.STORES_EXTRAS').' as se', 'se.store_id', '=', 's.store_id')                
                ->join(Config::get('tables.SUPPLIER_MST').' as sm', 'sm.supplier_id', '=', 's.supplier_id')                
				/* ->join(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE').' as sca', function($join) use ($supplier_id)
                {
                    $join->on('sca.category_id', '=', 'sm.category_id')->where('sca.supplier_id', '=', $supplier_id);
                }) */
                ->join(Config::get('tables.BUSINESS_CATEGORY_LANG').' as pc', function($join)
                {
                    $join->on('pc.bcategory_id', '=', 'sm.category_id');
                })                
                ->join(Config::get('tables.ADDRESS_MST').' as am', function($join)
                {
                    $join->on('am.relative_post_id', '=', 's.store_id')->where('am.post_type', '=', Config::get('constants.ADDRESS_POST_TYPE.STORE'));
                }) 
                ->leftjoin(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'am.country_id')
                ->leftjoin(Config::get('tables.LOCATION_CITY').' as ll', 'll.city_id', '=', 'am.city_id')
                ->leftjoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'am.state_id')   
				->where('s.is_deleted', Config::get('constants.OFF'))
                ->where('sm.is_deleted', Config::get('constants.OFF'))                 
                ->where('sm.supplier_id', $supplier_id);					
				
				if (isset($from) && !empty($from))
				{
					$qry->whereDate('s.created_on', '>=', getGTZ($from, 'Y-m-d'));
				}
				if (isset($to) && !empty($to))
				{
					$qry->whereDate('s.created_on', '<=', getGTZ($to, 'Y-m-d'));
				}				
				if (isset($status) && !empty($status))
				{
					$qry->where('s.status', $status);
				}
				if (isset($search_term) && !empty($search_term))
				{
					$qry->where(function($sub) use($search_term, $supplier_id)
					{
						$sub->where('s.store_name', 'LIKE', '%'.$search_term.'%')->where('sm.supplier_id', $supplier_id)
								->orwhere('s.store_code', 'LIKE', '%'.$search_term.'%')->where('sm.supplier_id', $supplier_id);
					});					
				}
				if (isset($orderby) && isset($order))
				{
					$qry->orderby($orderby, $order);
				}
				else
				{
					$qry->orderby('s.store_id', 'DESC');
				}
				if ($count)
				{
					return $qry->count();
				}
				else 
				{
					if (isset($length))
					{
						$qry->skip($start)->take($length);
					}
					$qry->select('s.supplier_id', 'sm.category_id', 's.store_id', 's.store_name', 'pc.bcategory_name as category', 'am.flatno_street', 'am.address', 'll.city', 'ls.state', 'lc.country', 's.store_logo as logo', 'se.mobile_no', 's.store_code', 's.status', 's.is_approved', 'sm.company_name', 's.created_on', 's.is_approved', 'sm.has_specific_hrs', 'sm.logo as mrlogo');
					$res = $qry->get();

					array_walk($res, function(&$result, $key) use ($arr)
					{
						if (empty($result->logo))
						{
							$result->logo = asset(Config::get('path.SELLER.PROFILE_IMG_PATH.WEB').$result->mrlogo);
						}
						else
						{
							$result->logo = asset(Config::get('path.SELLER.STORE_IMG_PATH.WEB').$result->logo);
						}
						$result->created_on = showUTZ($result->created_on, 'd-M-Y H:i:s');
						$result->actions['view'] = ['url'=>route('seller.outlet.view-details', ['code'=>$result->store_code]), 'redirect'=>false, 'label'=>trans('general.btn.view')];
						if (in_array($result->status, [Config::get('constants.SELLER.STORE.STATUS.DRAFT'), Config::get('constants.SELLER.STORE.STATUS.INACTIVE')]))
						{
							$result->actions[] = ['url'=>route('seller.outlet.update-status', ['code'=>$result->store_code, 'status'=>strtolower('ACTIVE')]), 'redirect'=>false, 'label'=>trans('general.btn.activate')];
						}
						else if ($result->status == Config::get('constants.SELLER.STORE.STATUS.ACTIVE'))
						{
							$result->actions['edit'] = ['url'=>route('seller.outlet.details', ['code'=>$result->store_code]), 'redirect'=>false, 'label'=>trans('general.btn.edit')];
							$result->actions[] = ['url'=>route('seller.outlet.update-status', ['code'=>$result->store_code, 'status'=>strtolower('INACTIVE')]), 'redirect'=>false, 'label'=>trans('general.btn.deactivate')];
						}
						if ($result->is_approved == Config::get('constants.SELLER.IS_VERIFIED.PENDING') || $result->is_approved == Config::get('constants.MERCHANT.IS_VERIFIED.REJECTED'))
						{
							//$result->actions['edit'] = ['url'=>route('seller.outlet.for_store_details', ['for'=>'edit', 'code'=>$result->store_code]), 'redirect'=>false, 'label'=>trans('general.btn.edit')];							
							$result->actions['edit'] = ['url'=>route('seller.outlet.details', ['code'=>$result->store_code]), 'redirect'=>false, 'label'=>trans('general.btn.edit')];
						}					
						$result->actions[''] = ['url'=>route('seller.outlet.images', ['store_code'=>$result->store_code]), 'redirect'=>true, 'label'=>trans('general.outlet.images.outlet_imgs')];
						
						$result->status_class = Config::get('dispclass.seller.outlet.status.'.$result->status);
						$result->status_code = trans('general.seller.outlet.status.'.$result->status);
						$result->is_approved_class = Config::get('dispclass.seller.outlet.is_approved.'.$result->is_approved);
						$result->is_approved = trans('general.seller.outlet.is_approved.'.$result->is_approved);
						
					});	
					 return $res;
				}       
    }
	
	public function update_store_status (array $arr = array())
    {	
        extract($arr);
        $store = [];
        $store['updated_on'] = getGTZ();
        $store['updated_by'] = $account_id;
        $store['status'] = $status;
        $query = DB::table(Config::get('tables.STORES'))
                ->where('store_code', '=', $code)
                ->where('supplier_id', $supplier_id);

        if ($status == Config::get('constants.SELLER.STATUS.ACTIVE'))
        {
            $query->whereIn('status', [Config::get('constants.SELLER.STATUS.DRAFT'), Config::get('constants.SELLER.STATUS.INACTIVE')]);
            $store['is_approved'] = Config::get('constants.ON_FLAG');
        }
        else if ($status == Config::get('constants.SELLER.STATUS.INACTIVE'))
        {
            $query->where('status', Config::get('constants.SELLER.STATUS.ACTIVE'));
            $store['is_approved'] = Config::get('constants.OFF_FLAG');
        }
        return $res = $query->update($store);
    }
	
	public function store_view_details (array $arr = array())
    {	
        extract($arr);
        $qry = DB::table(Config::get('tables.STORES').' as s')
                ->join(Config::get('tables.STORES_EXTRAS').' as se', 'se.store_id', '=', 's.store_id')
				->join(Config::get('tables.STORE_SETTINGS').' as ss', 'ss.store_id', '=', 's.store_id')
                ->join(Config::get('tables.SUPPLIER_MST').' as sm', 'sm.supplier_id', '=', 's.supplier_id')                
				/* ->join(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE').' as sca', function($join) use ($supplier_id)
                {
                    $join->on('sca.category_id', '=', 'sm.category_id')->where('sca.supplier_id', '=', $supplier_id);
                }) */
                ->join(Config::get('tables.BUSINESS_CATEGORY_LANG').' as pc', function($join)
                {
                    $join->on('pc.bcategory_id', '=', 'sm.category_id');
                })                
                ->join(Config::get('tables.ADDRESS_MST').' as am', function($join)
                {
                    $join->on('am.relative_post_id', '=', 's.store_id')
					->where('am.post_type', '=', Config::get('constants.ADDRESS_POST_TYPE.STORE'));
                }) 
                ->leftjoin(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'am.country_id')
                ->leftjoin(Config::get('tables.LOCATION_CITY').' as ll', 'll.city_id', '=', 'am.city_id')
                ->leftjoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'am.state_id')                     
                ->where('s.store_code', 'LIKE', '%'.$arr['store_code'].'%')
                ->where('s.supplier_id', $supplier_id)
                ->where('s.is_deleted', Config::get('constants.OFF'));
        //$qry->select('s.store_id', 's.store_name', 's.store_code', 'pc.bcategory_name', 'se.email', 'se.store_logo', 'mm.mrbusiness_name', 'mm.mrlogo', 'msm.mobile', 'msm.phone_code', 'msm.phone', 'msm.store_id', 'mm.account_id', 'mss.specify_working_hours as has_specific_hrs', 'msm.status', 'msm.is_approved', 'mss.split_working_hours as isSplit', 'ms.has_specific_hrs as mr_has_specific_hrs', 'msm.is_primary', 'am.formated_address as formatted_address', 'r.rating', 'r.likes', DB::RAW('(select count(*) from '.Config::get('tables.MERCHANT_IMAGES').' mi where mi.mrid = msm.mrid and mi.store_id = msm.store_id and mi.is_deleted ='.Config::get('constants.OFF').') as image_cunt'));
        $qry->select('s.store_id', 's.store_name', 's.store_code', 'pc.bcategory_name','se.email', 's.store_logo', 'sm.company_name', 'se.mobile_no', 'se.phonecode', 'se.landline_no',  'sm.account_id', 'ss.specify_working_hours as store_has_specific_hrs', 's.status', 's.is_approved', 'ss.split_working_hours as isSplit',  's.primary_store as is_primary', 'am.address', 's.created_on', 'sm.has_specific_hrs as mr_has_specific_hrs');
        $result = $qry->first();
        if (!empty($result))
        {
            $result->created_on = (!empty($result->created_on) && $result->created_on != '0000-00-00 00:00:00') ? showUTZ($result->created_on, 'd-M-Y H:i:s') : '';      
            $result->store_logo = asset(Config::get('path.SELLER.STORE_IMG_PATH.WEB').$result->store_logo);             
            $result->status_class = Config::get('dispclass.seller.outlet.status.'.$result->status);
            $result->status = trans('general.seller.outlet.status.'.$result->status);
            $result->mobile_no = $result->phonecode.'-'.$result->mobile_no;
            $result->is_approved_class = Config::get('dispclass.seller.outlet.is_approved.'.$result->is_approved);
            $result->is_approved = trans('general.seller.outlet.is_approved.'.$result->is_approved);
            $result->image_cunt = !empty($result->image_cunt) ? $result->image_cunt : 0;
            //$result->image_link = url('retailer/stores/images/'.$result->store_code);
            $result->image_link = route('seller.outlet.images', ['store_code'=>$result->store_code]);
            $result->timing = $this->formatStoreWorkingHours($result->store_id);
            unset($result->mrlogo);
            return $result;
        }
        return NULL;
    }
	
	public function formatStoreWorkingHours ($store)
    {	
        $working_days = '';
        $days = [];
        $store_id = 0;
        if (!is_object($store))
        {
            $settings = DB::table(Config::get('tables.STORE_SETTINGS').' as ss')
                    ->join(Config::get('tables.STORES').' as s', 's.store_id', '=', 'ss.store_id')
                    ->join(Config::get('tables.SUPPLIER_MST').' as sm', 'sm.supplier_id', '=', 's.supplier_id')
                    ->selectRaw('ss.specify_working_hours, ss.split_working_hours, sm.has_specific_hrs, sm.supplier_id, s.primary_store as is_primary')
                    ->where('ss.store_id', $store)
                    ->first();
					
            $store_id = $store;
        }
        else
        {			
            $settings = $store;
            $store_id = $store->store_id;
        }
        if ($settings)
        {
            if ($settings->specify_working_hours != config('constants.SPECIFY_WRK_HRS.NOT_SPECIFY'))
            {
                $query = DB::table(Config::get('tables.STORE_BUSSINESS_HOURS').' as bh')
                        ->join(Config::get('tables.WORKING_DAYS_LANG').' as wd', 'wd.working_day_id', '=', 'bh.week_day')
                        ->selectRaw('session,days,from_time,to_time,is_closed')
                        ->orderby('bh.week_day', 'ASC')
                        ->orderby('bh.session', 'ASC')
                        ->where('bh.supplier_id', $settings->supplier_id);
                if ($settings->specify_working_hours == config('constants.SPECIFY_WRK_HRS.SELF') && ($settings->is_primary == Config::get('constants.OFF')))
                {
                    $query->where('bh.store_id', $store_id);
                    $working_days = $query->get();
                }
                else if ($settings->specify_working_hours == config('constants.SPECIFY_WRK_HRS.SELF') && ($settings->is_primary == Config::get('constants.ON')))
                {
                    $query->whereNull('bh.store_id');
                    $working_days = $query->get();
                }
                else if ($settings->specify_working_hours == config('constants.SPECIFY_WRK_HRS.GLOBAL'))
                {
                    if ($settings->has_specific_hrs == config('constants.ON'))
                    {
                        $query->whereNull('bh.store_id');
                        $working_days = $query->get();
                    }
                    else
                    {
                        return trans('general.store_timing_info.always');
                    }
                }
                else
                {
                    return "";
                }
				//return $working_days;
                if (!empty($working_days))
                {
                    $day_str = '';

                    array_walk($working_days, function($day) use($settings, &$days, $day_str)
                    {
                        $day->from_time = (!empty($day->from_time) && ($day->from_time != '0000-00-00 00:00:00')) ? trim(showUTZ($day->from_time, 'h:i a'), '0') : '';
                        $day->to_time = (!empty($day->to_time) && ($day->to_time != '0000-00-00 00:00:00')) ? trim(showUTZ($day->to_time, 'h:i a'), '0') : '';
                        if (!array_key_exists($day->days, $days))
                        {
                            if ($day->is_closed)
                            {
                                $days[$day->days] = trans('general.store_timings_web.closed', ['days'=>$day->days]);
                            }
                            else
                            {
                                $days[$day->days] = trans('general.store_timings_web.split', ['days'=>$day->days, 'from_time'=>$day->from_time, 'to_time'=>$day->to_time]);
                            }
                        }
                        else
                        {
                            if ($settings->split_working_hours == config('constants.ON'))
                            {
                                if ($day->is_closed)
                                {
                                    $days[$day->days] = trans('general.store_timings_web.closed', ['days'=>$days[$day->days]]);
                                }
                                else
                                {
                                    $days[$day->days] = trans('general.store_timings_web.splited_time', ['days'=>$days[$day->days], 'from_time'=>$day->from_time, 'to_time'=>$day->to_time]);
                                }
                            }
                        }
                    });
                    foreach ($days as $key=> $val)
                    {
                        $days[$key] = '<li class="list-group-item">'.$val.'</li>';
                    }
					
                }
                return '<ul class="list-group">'.implode('', $days).'</ul>';
            }
            else
            {
                return trans('general.store_timing_info.always');
            }
        }
        return "";
    }
	
	public function store_details (array $arr = array())
    {   
        extract($arr);
        $qry = DB::table(Config::get('tables.STORES').' as s')
                ->join(Config::get('tables.STORES_EXTRAS').' as se', 'se.store_id', '=', 's.store_id')                
                ->leftjoin(Config::get('tables.STORE_SETTINGS').' as ss', 'ss.store_id', '=', 's.store_id')
				->join(Config::get('tables.SUPPLIER_MST').' as sm', 'sm.supplier_id', '=', 's.supplier_id')
				->join(Config::get('tables.ACCOUNT_PREFERENCE').' as ap', 'ap.account_id', '=', 'sm.account_id') 
				->join(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE').' as sca', function($join) use ($supplier_id)
                {
                    $join->on('sca.category_id', '=', 'sm.category_id')->where('sca.supplier_id', '=', $supplier_id);
                }) 
                ->join(Config::get('tables.BUSINESS_CATEGORY_LANG').' as pc', function($join)
                {
                    $join->on('pc.bcategory_id', '=', 'sm.category_id');
                })
				->join(Config::get('tables.CURRENCIES').' as cr', 'cr.currency_id', '=', 'ap.currency_id')			
				->join(Config::get('tables.ADDRESS_MST').' as am', function($join)
                {
                    $join->on('am.relative_post_id', '=', 's.store_id')->where('am.post_type', '=', Config::get('constants.ADDRESS_POST_TYPE.STORE'));
                }) 
				->leftjoin(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'am.country_id')
                ->leftjoin(Config::get('tables.LOCATION_CITY').' as ll', 'll.city_id', '=', 'am.city_id')
                ->leftjoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'am.state_id')                  
                ->where('s.store_code', 'LIKE', '%'.$arr['store_code'].'%')
				->where('s.supplier_id', $supplier_id)               
                ->where('s.is_deleted', Config::get('constants.OFF'));       
		$qry->select('s.store_id', 's.store_name', 's.store_code as code', 'pc.bcategory_name as category', 'pc.bcategory_id as category_id', 'se.email', 's.store_logo', 'sm.company_name', 'se.mobile_no as mobile', 'se.phonecode', 'se.landline_no as phone',  'sm.account_id', 'ss.specify_working_hours as has_specific_hrs', 'sm.has_specific_hrs as sp_has_specific_hrs', 's.status', 's.is_approved', 'ss.split_working_hours as isSplit',  's.primary_store as is_primary', 'am.flatno_street', 'am.address', 'am.postal_code', 's.created_on', 'll.city', 'lc.country', 'ls.state', 'cr.currency_id', 'cr.currency as currency_code', 'se.title', 'se.description', 'am.city_id', 'am.geolat', 'am.geolng', 'am.country_id','am.landmark');       
        $result = $qry->first();
        if (!empty($result))
        {
            $result->flatno_street = !empty($result->flatno_street) ? $result->flatno_street : '';
            $result->address = !empty($result->address) ? $result->address : '';
            $result->landmark = !empty($result->landmark) ? $result->landmark : '';            
            $result->city = !empty($result->city) ? $result->city : '';            
            $result->adminstrativearea = !empty($result->state) ? ucwords($result->state) : '';
            $result->state = !empty($result->country) ? $result->country : '';
            $result->postal_code = !empty($result->postal_code) ? $result->postal_code : '';            
            $result->phone = !empty($result->phone) ? $result->phone : '';          
            $result->store_logo = !empty($result->store_logo) ? asset(Config::get('path.SELLER.STORE_IMG_PATH.WEB').$result->store_logo) : '';
                
            return $result;
        }
        return NULL;
    }
	
	public function store_business_hrs (array $arr)
    { 	
        $days = [];
        $res = DB::table(Config::get('tables.STORE_BUSSINESS_HOURS').' as bhr')
                ->join(Config::get('tables.WORKING_DAYS_LANG').' as wl', 'wl.working_day_id', '=', 'bhr.week_day')
                ->leftjoin(Config::get('tables.STORE_SETTINGS').' as mss', 'mss.store_id', '=', 'bhr.store_id')                
                ->where('bhr.is_deleted', Config::get('constants.OFF'))
                ->select('wl.days', 'bhr.session', 'bhr.from_time', 'bhr.to_time', 'bhr.is_closed', 'mss.split_working_hours')
                ->where('bhr.supplier_id', '=', $arr['supplier_id']); 
        if (($arr['has_store_work_time'] == 0 || $arr['has_sp_work_time'] == 1) && ($arr['is_primary'] == 1))
        {
            $result = $res->whereNull('bhr.store_id')->get();
        }
        else if (($arr['has_store_work_time'] == 3) && ($arr['is_primary'] == 0 || $arr['is_primary'] == 1))
        {
            $result = $res->where('bhr.store_id', $arr['store_id'])->get();
        }
        else if ($arr['has_store_work_time'] == 2 && $arr['has_sp_work_time'] == 1)
        {
            $result = $res->whereNull('bhr.store_id')->get();
        }
        else
        {
            $result = [];
        }
        if (!empty($result))
        {
            if ($arr['has_store_work_time'] == 3)
            {
                $primary_split_hours = $result[0]->split_working_hours;
            }
            else if ($arr['has_store_work_time'] == 2 && $arr['has_sp_work_time'] == 1)
            {
                $primary_split_hours = DB::table(Config::get('tables.STORES').' as sm')
                        ->leftjoin(Config::get('tables.STORE_SETTINGS').' as ss', function($join)
                        {
                            $join->on('ss.store_id', '=', 'sm.store_id');
                        })
                        ->where('sm.supplier_id', '=', $arr['supplier_id'])
                        ->where('sm.primary_store', Config::get('constants.ON'))
                        ->value('ss.split_working_hours');
            }

            foreach ($result as $data)
            {
                $d = strtolower($data->days);
                if ($data->is_closed != 1)
                {
                    $days[$d][$data->session]['from'] = showUTZ($data->from_time, 'h:i a');
                    $days[$d][$data->session]['to'] = showUTZ($data->to_time, 'h:i a');
                    $days[$d][$data->session]['is_closed'] = $data->is_closed;
                }
                else
                {
                    $days[$d][$data->session]['is_closed'] = $data->is_closed;
                }
            };
        }
        if (!empty($days))
        {
            $primary_split_hours = 0;
            return ['days'=>$days, 'splitHrs'=>$primary_split_hours];
        }
        else
        {
            return ['days'=>[], 'splitHrs'=>0];
        }
    }
	
	public function store_update_web (array $arr = array())
    {
        extract($arr);
        $query = DB::table(Config::get('tables.STORES'))
                ->where('store_code', '=', $store_code)
                ->select('store_id', 'primary_store as is_primary')
                ->first();
        $store_id 		   = $query->store_id;
        $is_primary 	   = $query->is_primary;
        $mst['store_name'] = strip_tags(trim($store_name));
        $mst['store_logo'] = (isset($store_logo) && !empty($store_logo)) ? $store_logo : NULL;        
        $mst['updated_by'] = $account_id;
        $mst = array_filter($mst);
        $master = DB::table(Config::get('tables.STORES'))
							->where('supplier_id', '=', $supplier_id)
							->where('store_code', '=', $store_code)
							->update($mst);
		$ext['email'] = $email;					
		$ext['mobile_no'] = (isset($mobile) && !empty($mobile)) ? $mobile : NULL;
        $ext['landline_no'] = (isset($phone) && !empty($phone)) ? $phone : NULL;					
        $ext['title'] = (isset($title) && !empty($title)) ? $title : NULL;					        			
        $ext['description'] = (isset($description) && !empty($description)) ? strip_tags(trim($description)) : NULL;			
		$store_extras = DB::table(Config::get('tables.STORES_EXTRAS'))							
							->where('store_id', '=', $store_id)
							->update($ext);
		
        //$this->commonObj->saveStoreQRCode($store_id);
        $split_working_hrs = isset($split_working_hrs) && !empty($split_working_hrs) ? 1 : 0;
        $settings['specify_working_hours'] = $has_specific_hours = $specify_working_hrs;

        if (isset($specify_working_hrs) && !empty($specify_working_hrs) && ($specify_working_hrs == 3))
        {
            $days = DB::table(Config::get('tables.WORKING_DAYS'))
                    ->lists('working_day_id', 'working_day_key');

            $mr_store_id = $operating['store_id'] = $store_id;
            $operating['supplier_id'] = $supplier_id;
            if (isset($operating_hrs) && !empty($operating_hrs))
            {
                $n = 2;
                foreach ($operating_hrs as $day=> $time)
                {					
					if ($is_primary == 1) {
						$operating['store_id'] = null;
					} else {
						$operating['store_id'] = $store_id;
					}
                    $operating['supplier_id'] = $supplier_id;
                    $operating['week_day'] = $weekday = $days[$day];
                    $operating['created_on'] = getGTZ();

                    if (isset($time['closed']) && !empty($time['closed']))
                    {
                        $operating['from_time'] = Null;
                        $operating['to_time'] = Null;
                        $operating['is_closed'] = 1;
                        // $operating['session'] = 1;
                        //  DB::table(Config::get('tables.STORE_BUSSINESS_HOURS'))->insertGetID($operating);
                        //  $operating['session'] = 2;
                        //  DB::table(Config::get('tables.STORE_BUSSINESS_HOURS'))->insertGetID($operating); 

                        for ($i = 1; $i <= 2; $i++)
                        {
                            $operating['session'] = $i;
                            $session_count = DB::table(Config::get('tables.STORE_BUSSINESS_HOURS'))->where('supplier_id', $supplier_id);
							if ($is_primary == 1) {   $session_count->whereNull('store_id');  	} else {   $session_count->where('store_id', $store_id);	}
							$session_count = $session_count->where('week_day', $weekday)
											->where('session', $i)
											->where('is_deleted', 0)
											->count();
                            if ($session_count > 0)
                            {
                                $res = DB::table(Config::get('tables.STORE_BUSSINESS_HOURS'))->where('supplier_id', $supplier_id);
								if ($is_primary == 1) {   $res->whereNull('store_id');  	} else {   $res->where('store_id', $store_id);	}                                        
                                $res->where('week_day', $weekday)
                                        ->where('session', $i)
                                        ->update($operating);
                            }
                            else
                            {
                                DB::table(Config::get('tables.STORE_BUSSINESS_HOURS'))->insertGetID($operating);
                            }
                        }
                    } 

                    if (!isset($time['closed']) || empty($time['closed']))
                    {
                        foreach ($time as $index=> $val)
                        {
                            $operating['is_closed'] = 1;
                            $operating['session'] = $index + 1;
                            $operating['from_time'] = null;
                            $operating['to_time'] = null;

                            $session_count = DB::table(Config::get('tables.STORE_BUSSINESS_HOURS'))->where('supplier_id', $supplier_id);
                            if ($is_primary == 1) {   $session_count->whereNull('store_id');  	} else {   $session_count->where('store_id', $store_id);	}
                            $session_count = $session_count->where('week_day', $weekday)
											->where('session', $operating['session'])
											->where('is_deleted', 0)
											->count();

                            if (!empty($val['from']))
                            {
                                if (!empty($val['from']))
                                {
                                    $operating['from_time'] = getGTZ($val['from'], "H:i:s");
                                }
                                if (!empty($val['to']))
                                {
                                    $operating['to_time'] = getGTZ($val['to'], "H:i:s");
                                }
                                $operating['is_closed'] = 0;
                            }

                            if (($split_working_hrs == 0) && ($operating['session'] == 2))
                            {
                                $operating['from_time'] = Null;
                                $operating['to_time'] = Null;
                                $operating['is_closed'] = 1;
                            }

                            if ($session_count > 0)
                            {
                                $res = DB::table(Config::get('tables.STORE_BUSSINESS_HOURS'))->where('supplier_id', $supplier_id);
                                if ($is_primary == 1) {   $res->whereNull('store_id');  	} else {   $res->where('store_id', $store_id);	}
                                $res = $res->where('week_day', $weekday)
                                        ->where('session', $operating['session'])
                                        ->update($operating);
                            }
                            else
                            {
                                DB::table(Config::get('tables.STORE_BUSSINESS_HOURS'))->insert($operating);
                            }
                        }
                    }
                    $time = '';
                }
            }
        }
		
        $settings['split_working_hours'] = $split_working_hrs;

        if (isset($specify_working_hrs) && !empty($specify_working_hrs) && ($specify_working_hrs == 2 || $specify_working_hrs == 1))
        {
            $res = DB::table(config('tables.STORE_BUSSINESS_HOURS'))->where('supplier_id', $supplier_id);
            if ($is_primary == 1)
            {
                $res->whereNull('store_id');
            }
            else
            {
                $res->where('store_id', $store_id);
            }
            $res->update(['is_deleted'=>1]);
        } 

        $settings['store_id'] = $store_id;

        if ($is_primary == 1)
        {
            DB::table(Config::get('tables.SUPPLIER_MST'))->where('supplier_id', '=', $supplier_id)->update(['has_specific_hrs'=>1]);
        }

        $store_settings = DB::table(Config::get('tables.STORE_SETTINGS'))
                ->where('store_id', '=', $store_id)
                ->update($settings);		       
        
        $address['city_id'] 	  = isset($address['city_id']) ? $address['city_id'] : null;        
        $address['landmark'] 	  = isset($address['landmark']) ? $address['landmark'] : '';        
        $address['flatno_street'] = isset($address['address']) ? $address['address'] : '';        
        $address['state_id'] 	  = isset($address['state_id']) ? $address['state_id'] : null;
        $address['postal_code']   = isset($address['postal_code']) ? $address['postal_code'] : null;
        $address['country_id'] 	  = isset($address['country_id']) ? $address['country_id'] : null;
        $address['address_type_id'] = Config::get('constants.ADDRESS.PRIMARY');
        $address['updated_on'] 	  = getGTZ();
		$city_name 	 			  = $this->commObj->getCityName($address['city_id']);
		$state_name  			  = $this->commObj->getStateName($address['state_id']);
		$country_name  			  = $this->commObj->getCountryName($address['country_id']);
		$address['address'] 	  = $address['flatno_street'].', '.$address['landmark'].', '.$city_name.', '.$state_name.', '.$country_name;
		$geo = $this->commObj->get_geo_address(['address'=>$address['flatno_street'].', '.$city_name.', '.$state_name.', '.$country_name]);
		if(!empty($geo)){
			if(isset($geo->geometry) && !empty($geo->geometry)){
				 $address['geolat'] = $geo->geometry->location->lat;
				 $address['geolng'] = $geo->geometry->location->lng;
			}
		}
        $address = DB::table(Config::get('tables.ADDRESS_MST'))
                ->where(function($a) use($store_id)
                {
                    $a->where('post_type', '=', Config::get('constants.ADDRESS_POST_TYPE.STORE'))
                    ->where('relative_post_id', '=', $store_id);
                })
                ->update($address);		

        if (isset($address) || isset($store_extras) || isset($store_settings) || isset($master))
        {            
			return true;
        }
		return false;
    }
	
	public function slug ($text, $replace = '-')
    {
        //replace non letter or digits by (_)
        $text = preg_replace('/\W|_/', $replace, $text);
        // Clean up extra dashes
        $text = preg_replace('/-+/', '-', trim($text, $replace)); // Clean up extra dashes
        // lowercase
        $text = strtolower($text);
        if (empty($text))
        {
            return false;
        }
        return $text;
    }
	
	public function store_create_web (array $arr = array())
    {			
        extract($arr);
        $mst = $add = [];        
        $mst['supplier_id'] = $supplier_id;
		$mst['category_id'] = $category_id;
		$mst['store_name'] = strip_tags(trim($store_name));      
		$mst['store_slug'] = $this->slug($store_name, '-');
		$mst['store_logo'] = (isset($store_logo) && !empty($store_logo)) ? $store_logo : NULL;
		$mst['is_online'] = 0;
		$mst['primary_store'] = 0;
		$mst['status'] = 0;
		$mst['is_approved'] = 0;
		$mst['currency_id'] = $currency_id;
		$mst['created_on'] = getGTZ();
        $mst['created_by'] = $mst['updated_by'] = $account_id;
		
		DB::beginTransaction();    
		
		$store_id = DB::table(Config::get('tables.STORES'))->insertGetId($mst);		                  
        if ($store_id)
        {
			$ext['store_id'] = $store_id;	
			$ext['desc_type'] = 1;	
			$ext['email'] = $email;					
			$ext['mobile_no'] = (isset($mobile) && !empty($mobile)) ? $mobile : NULL;
			$ext['country_id'] = isset($address['country_id']) ? $address['country_id'] : $country_id;
			$ext['landline_no'] = (isset($phone) && !empty($phone)) ? $phone : 0;					
			$ext['title'] = (isset($title) && !empty($title)) ? $title : NULL;					        			
			$ext['description'] = (isset($description) && !empty($description)) ? strip_tags(trim($description)) : NULL;			
			DB::table(Config::get('tables.STORES_EXTRAS'))->insertGetId($ext);       
		
            if (!empty($specify_working_hrs) && ($specify_working_hrs == 3))
            {
                $days = DB::table(Config::get('tables.WORKING_DAYS'))
                        ->lists('working_day_id', 'working_day_key');
                if (isset($operating_hrs) && !empty($operating_hrs))
                {
                    foreach ($operating_hrs as $day=> $time)
                    {
                        $operating['store_id'] = $store_id;
                        $operating['supplier_id'] = $supplier_id;
                        $operating['week_day'] = $days[$day];
                        $operating['created_on'] = getGTZ();

                        if (isset($time['closed']) && !empty($time['closed']))
                        {
                            $operating['from_time'] = Null;
                            $operating['to_time'] = Null;
                            $operating['is_closed'] = 1;
                            $operating['session'] = 1;
                            DB::table(Config::get('tables.STORE_BUSSINESS_HOURS'))->insertGetID($operating);
                            $operating['session'] = 2;
                            DB::table(Config::get('tables.STORE_BUSSINESS_HOURS'))->insertGetID($operating);
                        }

                        if (!isset($time['closed']) || empty($time['closed']))
                        {
                            foreach ($time as $index=> $val)
                            {
                                $operating['is_closed'] = 1;
                                $operating['session'] = $index + 1;
                                $operating['from_time'] = null;
                                $operating['to_time'] = null;
                                if (!empty($val['from']))
                                {
                                    if (!empty($val['from']))
                                    {
                                        $operating['from_time'] = getGTZ($val['from'], "H:i:s");
                                    }
                                    if (!empty($val['to']))
                                    {
                                        $operating['to_time'] = getGTZ($val['to'], "H:i:s");
                                    }
                                    $operating['is_closed'] = 0;
                                }
                                DB::table(Config::get('tables.STORE_BUSSINESS_HOURS'))->insertGetID($operating);
                            }
                        }
                        $time = '';
                    }
                }
            }            
            
            $settings['store_id'] = $store_id;
            $settings['specify_working_hours'] = isset($specify_working_hrs) ? $specify_working_hrs : 0;
            $settings['split_working_hours'] = isset($split_working_hrs) ? 1 : 0;
            DB::table(Config::get('tables.STORE_SETTINGS'))->insertGetId($settings);
			
			$address['address'] = isset($address['address']) ? $address['address'] : null;        
			$address['city_id'] = isset($address['city_id']) ? $address['city_id'] : null;        
			$address['district_id'] = isset($address['district_id']) ? $address['district_id'] : 0;        
			$address['state_id'] = isset($address['state_id']) ? $address['state_id'] : null;
			$address['postal_code'] = isset($address['postal_code']) ? $address['postal_code'] : null;
			$address['country_id'] = isset($address['country_id']) ? $address['country_id'] : $country_id;
			$address['geolat'] = isset($address['geolat']) ? number_format($address['geolat'], 8) : 0;
			$address['geolng'] = isset($address['geolng']) ? number_format($address['geolng'], 8) : 0;
			$address['address_type_id'] = Config::get('constants.ADDRESS.PRIMARY');
			$address['updated_on'] = getGTZ();
			$address['post_type'] = Config::get('constants.ADDRESS_POST_TYPE.STORE');
			$address['relative_post_id'] = $store_id;           
            $address_id = DB::table(Config::get('tables.ADDRESS_MST'))->insertGetId($address);
			
            DB::table(Config::get('tables.SUPPLIER_MST'))->where('supplier_id', '=', $supplier_id)->update(['enable_multistore'=>1]);
            
            $store_code = rand(100, 999).sprintf('%03d', $country_id).sprintf('%04d', $store_id);
            DB::table(Config::get('tables.STORES'))
                    ->where('store_id', '=', $store_id)
                    ->update(['address_id'=>$address_id, 'store_code'=>$store_code]);
            //$this->saveStoreQRCode($store_id);
            DB::commit();
            return true;
        }
         DB::rollback();
        return false;
    }
	
	public function saveStoreQRCode ($store_id)
    {		
        return $store_code = DB::table(Config::get('tables.STORES'))->where('store_id', $store_id)->value('store_code');
        $folder_path = getGTZ('Y').'/'.getGTZ('m').'/';
        $path = Config::get('constants.STORE_QR_CODE.LOCAL');
        if (File::exists($path.getGTZ('Y')))
        {
            if (!File::exists($path.getGTZ('Y').'/'.getGTZ('m')))
            {
                File::makeDirectory($path.getGTZ('Y').'/'.getGTZ('m'));
            }
        }
        else
        {
            File::makeDirectory($path.getGTZ('Y'));
            File::makeDirectory($path.getGTZ('Y').'/'.getGTZ('m'));
        }
        $folder_path = $folder_path.$store_code.'.png';
        $this->generateQRCode($store_code, $path.$folder_path);
        DB::table(Config::get('tbl.MERCHANT_STORE_MST'))
                ->where('store_id', $store_id)
                ->update(['qr_code'=>$folder_path]);
        return $folder_path;
    }
	
	public function getMerchantPhotos (array $arr = array(), $count = false)
    {	//return $arr;
        extract($arr);
        $query = DB::table(Config::get('tables.SUPPLIER_IMAGES').' as mi')
                ->join(Config::get('tables.STORES').' as st', 'st.store_id', '=', 'mi.store_id')
                ->where('mi.supplier_id', $supplier_id)
                ->where('mi.is_deleted', Config::get('constants.OFF'))
                ->whereIn('mi.is_verified', [0, 1])
                //->where('mi.pb_deal_id', Null)
                ->where('mi.type', Config::get('constants.SELLER_IMAGE_TYPE.IMAGES'))
                ->select('mi.id', 'st.store_name', 'mi.file_path', 'mi.created_on', 'mi.status', 'mi.is_verified');
        if (isset($from) && !empty($from))
        {
            $query->whereDate('mi.created_on', '<=', getGTZ($from, 'Y-m-d'));
        }
        if (isset($to) && !empty($to))
        {
            $query->whereDate('mi.created_on', '>=', getGTZ($to, 'Y-m-d'));
        }
        if (isset($store_code) && !empty($store_code))
        {
            $query->where('st.store_code', $store_code);
        }
        else
        {
            $query->where('mi.store_id', Null);
        }
        if (isset($start) && isset($length))
        {
            $query->skip($start)->take($length);
        }
        if ($count)
        {
            return $query->count();
        }
        else
        {
            $res = $query->get();
            array_walk($res, function($imgs)
            {
                $file = explode('.', $imgs->file_path);
                if (in_array($file[1], ['avi', 'mp4']))
                {
                    $imgs->file_path = url('imgs/merchant'.$imgs->file_path);
                }
                else
                {
                    $imgs->file_path = (!empty($imgs->file_path)) ? asset(Config::get('path.SELLER.STORE_IMG_PATH.WEB').$imgs->file_path) : asset(Config::get('constants.MERCHANT.IMG_GALLERY_PATH.SM'));
                }
                $imgs->created_on = (!empty($imgs->created_on) && $imgs->created_on != '0000-00-00 00:00:00') ? showUTZ($imgs->created_on, 'd-M-Y H:i:s') : '';
                $imgs->remove_link = route('seller.outlet.photos.delete', ['id'=>$imgs->id]);
                 $imgs->actions = [];
                if ($imgs->status == Config::get('constants.STORE_IMAGE.STATUS.PUBLISH'))
                {
                    $imgs->actions[] = ['url'=>route('seller.outlet.photos.update-status', ['id'=>$imgs->id, 'status'=>strtolower('UNPUBLISH')]), 'data'=>['confirm'=>trans('general.confirm', ['what'=>trans('general.stores.images.unpublished')])], 'redirect'=>false, 'label'=>trans('general.stores.images.unpublished')];
                }
                else if ($imgs->status == Config::get('constants.STORE_IMAGE.STATUS.UNPUBLISH'))
                {
                    $imgs->actions[] = ['url'=>route('seller.outlet.photos.update-status', ['id'=>$imgs->id, 'status'=>strtolower('PUBLISH')]), 'data'=>['confirm'=>trans('general.confirm', ['what'=>trans('general.stores.images.publish')])], 'redirect'=>false, 'label'=>trans('general.stores.images.publish')];
                }
                $imgs->actions[] = ['url'=>route('seller.outlet.photos.delete', ['id'=>$imgs->id]), 'redirect'=>false, 'data'=>['confirm'=>trans('general.delete_confirm_msg')], 'label'=>trans('general.btn.delete')];
                $imgs->status_class = Config::get('dispclass.seller.outlet.images.status.'.$imgs->status);
                $imgs->status = trans('general.stores.images.status.'.$imgs->status);
                $imgs->is_verified_class = Config::get('dispclass.seller.outlet.images.is_verified.'.$imgs->is_verified);
                $imgs->is_verified = trans('general.stores.images.is_verified.'.$imgs->is_verified); 
            });
            return $res;
        }
    }
	
	public function updateStoreImageStatus (array $arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.SUPPLIER_IMAGES').' as mi')
                        ->where('mi.supplier_id', $supplier_id)
                        ->where('mi.id', $id)
                        ->where('mi.status', '!=', $status)
                        ->update(['mi.status'=>$status]);
        return false;
    }
	
	public function deleteStoreImage (array $arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.SUPPLIER_IMAGES').' as mi')
                        ->where('mi.supplier_id', $supplier_id)
                        ->where('mi.id', $id)
                        ->where('mi.is_deleted', Config::get('constants.OFF'))
                        ->update(['mi.is_deleted'=>Config::get('constants.ON')]);
    }
	
	public function getStoreId ($store_code)
    {
        return DB::table(Config::get('tables.STORES'))
                        ->where('store_code', $store_code)
                        ->value('store_id');
    }
	
	public function getSetting ($key)
    {
        return DB::table(Config::get('tables.SETTINGS'))
                        ->where('setting_key', $key)
                        ->value('setting_value');
    }
	
	public function uploadMerchantPhotos (array $arr = array())
    {	
        $imgObj = $arr['imgObj'];
        if (!empty($arr['files']))
        {
            $failedFiles = [];
            //$settings = json_decode(stripslashes($this->getSetting($this->config->get('constants.MERCHANT.IMG_UPLOAD_SETTINGS'))));
            $settings = json_decode(stripslashes(stripslashes($this->getSetting('merchant_upload_file_settings'))));
            foreach ($arr['files'] as $data['file'])
            {
                if (isset($data['file']) && !empty($data['file']))
                {
                    $attachment = $data['file'];
                    $size = $attachment->getSize();
                    $filename = '';
                    if ($attachment != '')
                    {
                        $folder_path = '/'.getGTZ('Y').'/'.getGTZ('m').'/';
                        //$path = $this->config->get('constants.MERCHANT.IMG_GALLERY_PATH.LOCAL');
                        $path = Config::get('path.SELLER.STORE_IMG_PATH.LOCAL');
                        if (!File::exists($path))
                        {
                            File::makeDirectory($path);
                        }
                        if (File::exists($path.'/'.getGTZ('Y')))
                        {
                            if (!File::exists($path.'/'.getGTZ('Y').'/'.getGTZ('m')))
                            {
                                File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
                                File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m').'/'.'temp');
                            }
                        }
                        else
                        {
                            File::makeDirectory($path.'/'.getGTZ('Y'));
                            File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
                        }
                        if (in_array(strtolower($attachment->getClientOriginalExtension()), array('gif', 'jpg', 'jpeg', 'png', 'svg')))
                        {
                            try
                            {
                                if ($size < $settings->image_file_size)
                                {
                                    $org_name = $attachment->getClientOriginalName();
                                    $ext = $attachment->getClientOriginalExtension();
                                    //$img_size = getimagesize($attachment);
                                    $file_extentions = strtolower($ext);
                                    $filtered_name = $this->slug($org_name);
                                    $file_name = explode('_', $filtered_name);
                                    $file_name = $file_name[0];
                                    $file_name = $file_name.'.'.$ext;
                                    //$move_path 		 = $org_path;
                                    $filename = getGTZ('dmYHis').$file_name;
                                    /* image Resizing */
                                    $uploaded_file = $attachment->move($path.$folder_path.'/temp/', $filename);
                                    if (!empty($uploaded_file))
                                    {
                                        $data['file'] = $folder_path.$filename;
                                        $imgObj->imageresize($path.$folder_path.'/temp/'.$filename, $path.$folder_path.$filename, 800, 500);
                                        unlink($path.$folder_path.'/temp/'.$filename);
                                        $upload['supplier_id'] = $arr['supplier_id'];
                                        $upload['store_id'] = $arr['store_id'];
                                        /* $upload['pb_deal_id'] = $arr['pb_deal_id']; */
                                        $upload['file_path'] = $data['file'];
                                        $upload['added_by'] = $arr['account_id'];
                                        //$upload['status'] = $this->config->get('constants.ON');
                                        $upload['status'] = $arr['status'];
                                        $upload['created_on'] = getGTZ('Y-m-d');
                                        $res = DB::table(Config::get('tables.SUPPLIER_IMAGES'))->insertGetID($upload);
                                    }
                                    else
                                    {
                                        $failedFiles[] = $attachment->getClientOriginalName();
                                    }
                                }
                                else
                                {

                                    $failedFiles[] = $attachment->getClientOriginalName();
                                }
                            }
                            catch (\Exception $e)
                            {
                                $failedFiles[] = $e->getMessage();
                            }
                        }
                        /* else if (in_array(strtolower($attachment->getClientOriginalExtension()), array('mp4', 'avi')))
                        {
                            if ($size < $settings->vedio_file_size)
                            {
                                try
                                {
                                    $org_name = $attachment->getClientOriginalName();
                                    $ext = $attachment->getClientOriginalExtension();
                                    $file_extentions = strtolower($ext);
                                    $filtered_name = $this->slug($org_name);
                                    $file_name = explode('_', $filtered_name);
                                    $file_name = $file_name[0];
                                    $file_name = $file_name.'.'.$ext;
                                    //$move_path 		 = $org_path;
                                    $filename = getGTZ('dmYHis').$file_name;

                                    $uploaded_file = $attachment->move($path.$folder_path, $filename);
                                    if (!empty($uploaded_file))
                                    {
                                        $data['file'] = $folder_path.$filename;
                                        $upload['mrid'] = $arr['mrid'];
                                        $upload['store_id'] = $arr['store_id'];
                                        $upload['file_path'] = $data['file'];
                                        $upload['added_by'] = $arr['account_id'];
                                        $upload['status'] = $arr['status'];
                                        $res = DB::table($this->config->get('tbl.MERCHANT_IMAGES'))
                                                ->insertGetID($upload);
                                    }
                                    else
                                    {
                                        $failedFiles[] = $attachment->getClientOriginalName();
                                    }
                                }
                                catch (\Exception $e)
                                {
                                    $failedFiles[] = $e->getMessage();
                                }
                            }
                            else
                            {
                                $failedFiles[] = $attachment->getClientOriginalName();
                            }
                        } */
                        else
                        {
                            $failedFiles['invalid_files'][] = $attachment->getClientOriginalName();
                        }
                    }
                }
                else
                {
                    $data['file'] = '';
                }
            }

            return $failedFiles;
        }
        else
        {
            return true;
        }
    }

}
