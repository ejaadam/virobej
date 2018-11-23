<?php
namespace App\Models;

use DB;
use App\Models\BaseModel;

class LocationModel extends BaseModel{

    public function __construct ()
    {
         parent::__construct();	 
    }
	
	public function getCountries($arr=array()) {	
	    $operate = '-1';
		extract($arr);
		$qry = DB::table(config('tables.LOCATION_COUNTRY').' as lc')
			->join($this->config->get('tables.CURRENCIES').' as cu', 'cu.currency_id', '=', 'lc.currency_id')
			->select('lc.country_id', DB::raw('lc.country as country_name'),'iso2','lc.phonecode','lc.currency_id',DB::raw('cu.currency as currency_code'))
			->where('lc.status', '=', 1);
		if($operate>=0){
			$qry->where('lc.operate', '=', $operate);
		}
		if(isset($country_id) && $country_id>0){
			$qry->where('lc.country_id', '=', $country_id);
		}
		$result = $qry->get();		
		if(!empty($result)) {
			return $result;
		}
		return [];
	}
	
	
	public function getCountry($country_id=''){		
		$country_id=0;
		$operate = '-1';
		$country_code = '';
		$op = false;
		
		$operate = '-1';
		if($country_id>0 || !empty($country_code)){
			$qry = DB::table(config('tables.LOCATION_COUNTRY'))
				->select('country_id','currency_id', DB::raw('country as country_name'),'iso2')
				->where('status', '=', 1);
	
      	if($country_id>0){
				$qry->where('country_code', '=', $country_id);
			}			
			if(!empty($country_code)){
				$qry->where('iso2', '=', $country_code);
			}
			if($operate>=0){
				$qry->where('operate', '=', $operate);
			}
			$res = $qry->first();		
		
			if(!empty($res)) {
				$op =  $res;
			}
		}
		print_R($op); die;
		return $op;	
	}

	public function get_states_list ($country_id = '', $region_id = '')
    {
        $query = DB::table($this->config->get('tables.LOCATION_STATE'))
                ->where('status', $this->config->get('constants.ON'))
                ->orderby('state');
        if (!empty($country_id))
        {
            $query->where('country_id', $country_id);
        }
        if (!empty($region_id))
        {
            $query->where('region_id', $region_id);
        }
        return $query->get();
    }
	
	
	
	public function get_region_list ($country_id = '')
    {
	
        $query = DB::table($this->config->get('tables.LOCATION_REGIONS'))
                //->where('status', $this->config->get('constants.ON'))
                ->orderBy('region', 'asc');
        if (!empty($country_id))
        {
            $query->where('country_id', $country_id);
        }
        $result = $query->get();
		
		
        return (!empty($result) && count($result) > 0) ? $result : false;
    }
	
	
	public function get_district_list ($state_id = '', $territory_state_id = '')
    {
        $query = DB::table($this->config->get('tables.LOCATION_DISTRICTS'))
                ->orderBy('district', 'asc');
        if (!empty($state_id))
        {
            if (is_numeric($state_id))
                $query->where('state_id', $state_id);
            else
            {
                $query->where('state_id', function($d) use($state_id)
                {
                    $d->from($this->config->get('tables.LOCATION_STATE'))
                            ->where('status', $this->config->get('constants.ON'))
                            ->where('state', $state_id)
                            ->pluck('state_id');
                });
            }
        }
        if (!empty($territory_state_id))
        {
            if (is_numeric($territory_state_id))
                $query->orWhere('state_id', $territory_state_id);
            else
            {
                $query->where('state_id', function($d) use($territory_state_id)
                {
                    $d->from($this->config->get('tables.LOCATION_STATE'))
                            ->where('status', $this->config->get('constants.ON'))
                            ->where('state', $territory_state_id)
                            ->pluck('state_id');
                });
            }
        }
        $result = $query->get();
        return (!empty($result) && count($result) > 0) ? $result : false;
    }

    public function get_city_list ($pincode_id='')
    {
        $query = DB::table($this->config->get('tables.LOCATION_CITY'))
                ->where('status', $this->config->get('constants.ON'));
        if (!empty($pincode_id))
        {
			
            $query->where('pincode_id', $pincode_id);
        }
        $result = $query->orderBy('city', 'asc')
                ->get();
			
        return (!empty($result) && count($result) > 0) ? $result : false;
    }
	
	
	  public function get_cities_list ($district_id)
    {
        $query = DB::table($this->config->get('tables.LOCATION_PINCODES'))
                ->where('status', $this->config->get('constants.ON'));
        if (!empty($district_id))
        {
			
            $query->where('district_id', $district_id);
        }
        $result = $query->orderBy('pincode_id', 'asc')
                ->first();
			
        return (!empty($result) && count($result) > 0) ? $result : false;
    }
	
	
	
	public function get_pioncode_list(array $data = array()){
	  extract($data);
	  $query = DB::table($this->config->get('tables.LOCATION_PINCODES'))
                ->where('status', $this->config->get('constants.ON'));
	 if (!empty($zipcode))
        {
            $query->where('pincode', $zipcode);
        }
        if (!empty($district_id))
        {
            $query->where('district_id', $district_id);
        }
		 $result = $query->orderBy('pincode_id', 'asc')
                ->first();
	  return (!empty($result) && count($result) > 0) ? $result : false;			
	}

/*   public function getCurrencies ($country_id)
    {
        $query = DB::table($this->config->get('tables.CURRENCIES'))
                ->orderBy('currency', 'asc');
        if (!empty($country_id))
        {
            $query->whereIn('id', $country_id);
        }
        return $query->lists('currency', 'id');
    } */
   public function get_territory_list ($state_id)
    {
        $result = DB::table($this->config->get('tables.LOCATION_STATE'))
                ->where('is_union_territory',$this->config->get('constants.ON'))
                ->where('linked_state_id', $state_id)
                ->where('status', $this->config->get('constants.ON'))
                ->select('state_id', 'state as state_name')
                ->get();
        return ($result) ? $result : false;
    }
   public function addnewdistrict ($district_name, $state_id)
    {
        $result = DB::table(Config::get('constants.LOCATION_DISTRICTS'))
                ->insertGetId(array(
            'district_name'=>$district_name,
            'state_id'=>$state_id));
        return $result;
    }

}
