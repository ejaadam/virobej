<?php

namespace App\Http\Middleware;

use \App\Models\Api\CommonModel;
use Closure;
use Log;
class SetLocation
{

    public function handle ($request, Closure $next, $type = 'browse')
    {
        $this->request = $request;		
		$this->session = $request->session();	
		Log::info('Set Location: '.$this->request->route()->getName().' with lat: '.$this->request->header('lat').', lng: '.$this->request->header('lng').' and country_id: '.$this->request->country_id.' from '.$this->request->header('User-Agent'));	
		
		if ($this->request->is('api/v1/user/store/details') || $this->request->is('api/v1/user/store/search') || $this->request->is('api/v1/user/store/search/*')) {
			if (empty($this->request->header('lat')) && empty($this->request->header('lng')))
			{
				//$response = ['msg'=>'Set latitude and longitude', 'status'=>config('httperr.HEADER_MISSING')];	
				$response = ['msg'=>'Location Service Disabled. Please enable location access to PayGyft in Settings', 'status'=>config('httperr.HEADER_MISSING')];	
				return response()->json($response, config('httperr.HEADER_MISSING'));
			}
		}	

		//$this->session->forget('geo');		
		//print_r($geo);exit;		
		$geo = $this->session->has('geo') ? $this->session->get('geo') : (object) [
							'current'=>(object) ['isSet'=>false, 'lat'=>0, 'lng'=>0, 'country_id'=>0, 'locality'=>'', 'locality_id'=>0, 'state_id'=>0, 'district_id'=>0, 'pincode'=>0, 'location'=>'', 'distance_unit'=>1, 'currency_id'=>0, 'country'=>'', 'country_code'=>''],
							'browse'=>(object) ['isSet'=>false, 'lat'=>0, 'lng'=>0, 'country_id'=>0, 'locality'=>'', 'locality_id'=>0, 'state_id'=>0, 'district_id'=>0, 'pincode'=>0, 'location'=>'', 'distance_unit'=>1, 'currency_id'=>0, 'country'=>'', 'country_code'=>'']
				];
		
		if (($this->request->has('country_id') && !empty($this->request->country_id)) || (!empty($this->request->header('lat')) && !empty($this->request->header('lng'))))
        {			
			$this->config = config();
			$this->siteConfig = $this->config->get('settings');				
			if ($this->request->has('country_id') && !empty($this->request->country_id))
			{	
				if ($geo->{$type}->country_id != $this->request->country_id)
                {	
					$country_id = $request->country_id;
					$this->commonObj = new CommonModel($this);
					if ($type == 'browse' || !$geo->browse->isSet)
					{
						/* browse Location */
						$country_info = $this->commonObj->country_details($country_id);					
						$geo->browse->country_id = $country_id;                    
						$geo->browse->country = $country_info->country;                    
						$geo->browse->country_code = $country_info->country_code;                    
						$geo->browse->distance_unit = $country_info->distance_unit;                    
						$geo->browse->currency_id = $country_info->currency_id;    
						$geo->browse->isSet = true;										
					} 
					if ($type == 'current' || !$geo->browse->isSet)
					{
						/* current Location */
						$country_info = $this->commonObj->country_details($country_id);					
						$geo->current->country_id = $country_id;                    
						$geo->current->country = $country_info->country;                    
						$geo->current->country_code = $country_info->country_code;                    
						$geo->current->distance_unit = $country_info->distance_unit;                    
						$geo->current->currency_id = $country_info->currency_id;    
						$geo->current->isSet = true;	
					}			
				}
			}
			else if (!empty($this->request->header('lat')) && !empty($this->request->header('lng')))
			{			
				$lat = (double) $this->request->header('lat');
				$lng = (double) $this->request->header('lng');							
				//print_r($lat);exit;		
				if (($lat != $geo->{$type}->lat) || ($lng != $geo->{$type}->lng))
				{					
					//print_r($geo);exit;																						
					$this->commonObj = new CommonModel($this);							
					if ($type == 'current' || !$geo->current->isSet)
					{
						$location_info = $this->commonObj->getLocationInfo(['lat'=>$lat, 'lng'=>$lng]);							
						if($location_info)
						{													
							$geo->current = $location_info;
							$geo->current->boundries = $this->boundingCoordinates($lat, $lng, $this->siteConfig->distance, $this->siteConfig->distance_unit);
							$geo->current->isSet = true;
							$geo->current->lat = $lat;
							$geo->current->lng = $lng;																			
						}				
					}       
					
					if ($type == 'browse' || !$geo->browse->isSet)
					{
						$location_info = $this->commonObj->getLocationInfo(['lat'=>$lat, 'lng'=>$lng]);
						if($location_info)
						{							
							$geo->browse = $location_info;  
							$geo->browse->boundries = $this->boundingCoordinates($lat, $lng, $this->siteConfig->distance, $this->siteConfig->distance_unit);	
							$geo->browse->isSet = true;                        
							$geo->browse->lat = $lat;
							$geo->browse->lng = $lng;
							$geo->current = $geo->browse;
						}
					}
				}
			}			
			$geo->current = !$geo->current->isSet && $geo->browse->isSet ? $geo->browse : $geo->current;
            $geo->browse = !$geo->browse->isSet && $geo->current->isSet ? $geo->current : $geo->browse;			
			
			if (!$geo->current->isSet && $this->config->has('app.accountInfo') && !empty($this->config->get('app.accountInfo')))
			{
				//print_r(123);exit;
				$this->userSess = (object) $this->config->get('app.accountInfo');
				$geo->current->lat = $this->userSess->address->lat;
				$geo->current->lng = $this->userSess->address->lng;
				$geo->current->locality = $this->userSess->address->locality;
				$geo->current->locality_id = $this->userSess->address->locality_id;
				$geo->current->district_id = $this->userSess->address->district_id;
				$geo->current->state_id = $this->userSess->address->state_id;
				$geo->current->country_code = $this->userSess->address->country_code;			
				//$geo->current->phonecode = $this->userSess->address->phonecode;
				$geo->current->currency_id = $this->userSess->currency_id;
				$geo->current->distance_unit = 1;
				$geo->current->boundries = $this->boundingCoordinates($geo->current->lat, $geo->current->lng, $this->siteConfig->distance, $this->siteConfig->distance_unit);
				$geo->current->isSet = true;
			}			
		} 
		$request->session()->set('geo', $geo);
			//print_r($geo);exit;
		/* Log::info('response data for '.$request->fullUrl().' with token '.$request->header('usrtoken').' gives Content '.$response->getContent().' from '.$request->header('User-Agent')); */
        return $next($request);
    }	
	
	function boundingCoordinates ($lat, $lng, $distance, $distance_unit = null)
    {
        //$distance = 10;
        if ($distance_unit == 1 || $distance_unit == null)
        {
            // earth's radius in km = ~6371
            $radius = 6371.01;
        }
        else
        {
            // earth's radius in Miles = ~3959
            $radius = 3959;
        }
        // latitude boundaries
        $maxlat = $lat + rad2deg($distance / $radius);
        $minlat = $lat - rad2deg($distance / $radius);

        // longitude boundaries (longitude gets smaller when latitude increases)
        $maxlng = $lng + rad2deg($distance / $radius / cos(deg2rad($lat)));
        $minlng = $lng - rad2deg($distance / $radius / cos(deg2rad($lat)));

        return (object) ['minlat'=>$minlat, 'maxlat'=>$maxlat, 'minlng'=>$minlng, 'maxlng'=>$maxlng];
    }

}
