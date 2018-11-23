<?php
namespace App\Models\Admin;
use DB;
use App\Models\BaseModel;
use App\Helpers\ImageLib;
use Config;
use URL;

class AdminCommon extends BaseModel
{

    public function __construct () {
        parent::__construct();		
    }
	
 public function get_region_id ($state_id = '')
    {
        if (!empty($state_id))
        {
            return DB::table($this->config->get('tables.LOCATION_STATE'))
                            ->where('state_id', $state_id)
                            ->pluck('region_id');
        }
        return false;
    }
  public function addnewdistrict ($district_name, $state_id)
    { 
	 $result = DB::table($this->config->get('tables.LOCATION_DISTRICTS'))
                ->insertGetId(array(
            'district_name'=>$district_name,
            'state_id'=>$state_id));
        return $result;
    }
  public function addnewcity ($city_name, $state_id, $district_id)
    {
        $result = DB::table(Config::get('constants.LOCATION_CITY'))
                ->insertGetId(array(
            'city_name'=>$city_name,
            'state_id'=>$state_id,
            'district_id'=>$district_id));
        return $result;
    }
}
