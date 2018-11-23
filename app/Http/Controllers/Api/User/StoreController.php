<?php

namespace App\Http\Controllers\Api\User;
use App\Http\Controllers\Api\User\UserApiBaseController;
use App\Models\Api\User\AccountModel;
use App\Models\Api\User\StoreModel;
use CommonLib;

class StoreController extends UserApiBaseController
{    
    public function __construct ()
    {
        parent::__construct();
		$this->accountObj = new AccountModel();
        $this->storeObj = new StoreModel();
    }
    
    public function dashboard_search ()
    {
        $data = $this->request->all();
		$data['country_id'] = $this->geo->current->country_id;
		$op['online_stores'] = $this->storeObj->OnlineStoreList($data);
		if (!empty($this->request->header('lat')) && !empty($this->request->header('lng')))
		{	
			$op['in_stores'] = $this->storeObj->list_all_store($data);					
		} else {
			$op['in_stores'] = ['msg'=>'Location Service Disabled. Please enable location access to PayGyft in Settings', 'status'=>config('httperr.HEADER_MISSING')];				
		} 
        return $this->response->json($op, 200, $this->headers, $this->options);
    }

    public function list_all_store ($category = '')
    {
        $wdata = [];        
        if (!empty($this->geo->current->country_id))
        {
            $op = $wdata = [];
            $postdata = $this->request->all();
            $wdata['country_id'] = $this->geo->current->country_id;
            $wdata['boundries'] = !empty($this->geo->current->boundries) ? $this->geo->current->boundries : '';
            $wdata['locality_id'] = $this->geo->current->locality_id;
            $wdata['category_type'] = $this->config->get('constants.CATEGORY_TYPE.IN_STORE');
			$op['current_location'] = $this->geo->current->locality;
            if (!empty($category))
            {
                $wdata['page'] = isset($postdata['page']) && !empty($postdata['page']) ? $postdata['page'] : 1;
                $wdata['length'] = isset($postdata['length']) && !empty($postdata['length']) ? $postdata['length'] : 10;
                $wdata['start'] = $wdata['page'] == 1 ? 0 : ((($wdata['page'] - 1) * $wdata['length']) + 1);
            }
			
            if ($category == 'popular')
            {
                //$wdata['categories'] = $op['categories'] = $this->storeObj->popluarStoreCategories($wdata, false, true);
				//print_r($wdata);exit;
				$wdata['categories'] = $cats = $this->storeObj->popluarStoreCategories($wdata, false, true);
				$vicat[] = ['category_slug'=>'vi-stores', 'bcategory_name'=>'Vi-Stores', 'icon'=>'http://localhost/dsvb_portal/resources/uploads/categories/icons/default.png', 'next_page'=>false, 'stores'=>[]];
				$op['categories'] = array_merge($cats,$vicat);		
            }
            elseif (isset($category) && !empty($category) && $category != 'popular')
            {
                $wdata['category'] = $wdata['category_slug'] = $category;               
                if ($wdata['page'] == 1)
                {
                    //$op['category'] = $this->storeObj->storeCategories($wdata);
                    if ($wdata['category'] != 'vi-stores') {
						$op['category'] = $this->storeObj->storeCategories($wdata);
					} else {
						$op['category'][] = ['category_slug'=>'vi-stores', 'bcategory_name'=>'Vi-Stores', 'icon'=>'http://localhost/dsvb_portal/resources/uploads/categories/icons/default.png'];
					}
					$op['filters'] = !empty($op['category']) ? $this->storeObj->getStoreFilters($wdata) : [];
                }				
            } 
            if (!empty($postdata))
            {
                array_walk($postdata, function($input, $k) use(&$wdata)
                {
                    if (preg_match('/^filter_/i', $k))
                    {
                        $k = preg_replace('/^filter_/i', '', $k);
                        $wdata['fopt'][$k] = $input;
                    }
                });
                $op['filters'] = !empty($wdata['fopt']) ? $wdata['fopt'] : '';
            }
			
            $wdata['store'] = (isset($postdata['store']) && !empty($postdata['store'])) ? $postdata['store'] : null;
            $wdata['category_id'] = (isset($postdata['category_id']) && !empty($postdata['category_id'])) ? $postdata['category_id'] : null;
            $wdata['search_term'] = isset($postdata['search_term']) && !empty($postdata['search_term']) ? $postdata['search_term'] : null;
			
            if ($category == 'popular')
            {				
                $res = $this->storeObj->list_all_stores_unionall($wdata);
            }
            else
            {					
                $res = $this->storeObj->list_all_store($wdata);				
                if (!empty($category))
                {
                    $op['next_page'] = $this->storeObj->list_all_store($wdata, true);
                } 
            }           
            if ($res)
            {
                $op['data'] = $res;
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $op['data'] = [];
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');                
                $op['msg'] = trans('user/general.outlet_not_found');
            }
        }
        else
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            $op['msg'] = trans('general.set_your_location');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	/* Store Details */
	public function store_details ($store_slug = null, $store_code = null)
    {
        $wdata = [];
        $op = [];
		//print_r($this->geo);exit;
        if ($this->geo->current->isSet)
        { 
            $postdata = $this->request->all();
			
            if (!is_null($store_code))
            {
                $wdata['store'] = $store_code;
            }
            else if (is_null($store_code) && isset($postdata['store_code']) && !empty($postdata['store_code']))
            {
                $wdata['store'] = $postdata['store_code'];
            }
			
            $wdata['user_location'] = '';
            $wdata['user_location']['lat'] = $this->geo->current->lat;
            $wdata['user_location']['lng'] = $this->geo->current->lng;
            $wdata['user_location']['distance_unit'] = $this->geo->current->distance_unit; 
			$wdata['user_location']['boundries'] = !empty($this->geo->current->boundries) ? $this->geo->current->boundries : '';
            if (isset($this->userSess->account_id))
            {
                $wdata['account_id'] = $this->userSess->account_id;
            }			
			//print_r($this->storeObj->instore_details($wdata));exit;
            if ($res = $this->storeObj->instore_details($wdata))
            {
                $op['data'] = $res;
                $op['rating_content'] = trans('user/account.rating_content', ['merchant'=>$res->name]);
                $op['share']['email'] = ['title'=>trans('user/account.store_share.email.title', ['company_name'=>$res->name]), 'content'=>trans('user/account.store_share.email.content', ['mrbusiness_name'=>$res->name])];
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            }
            else
            {
                $op['msg'] = trans('general.not_found', ['which'=>trans('general.label.store')]);
                $op['status'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
            }
        }
        else
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            $op['msg'] = trans('general.set_your_location');
        } 
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function likeStore ($store_code)
    {
        $op = $data = [];
        $data = $this->request->all();
        $data['store_code'] = $store_code;
        $data['account_id'] = !empty($this->userSess->account_id) ? $this->userSess->account_id : '';
        $store_codes = [];				
        if ($this->session->has('likes'))
        {
            $store_codes = $this->session->get('likes');
        }	
        if (in_array($store_code, $store_codes))
        {		
			//print_r(12345);exit;
            if ($data['status'] == 1)
            {	
				$op['count'] = $this->storeObj->likeCount($data) . ' Likes';
                $op['msg'] = 'You already liked this store.';
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            }
            else
            {				
                $res = $this->storeObj->dislikeStore($data);								
                if ($res)
                {
                    $store_codes = array_diff($store_codes, [$store_code]);
                    $this->session->put('likes', $store_codes);
                    $op['count'] = $res.' Likes';
                    $op['msg'] = trans('user/account.thanks_to_unlike');
                    $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
                else
                {
					$op['count'] = $this->storeObj->likeCount($data) . ' Likes';
                    $op['msg'] = 'Not yet liked this store.';
                    $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
            }
        }
        else
        {
            if ($data['status'] == 1)
            {					
                if ($res = $this->storeObj->likeStore($data))
                {						
                    $store_codes = [];
                    if ($this->session->has('likes'))
                    {
                        $store_codes = $this->session->get('likes');
                    }
                    $store_codes[]  = $store_code;
                    $this->session->put('likes', $store_codes); 
                    $op['count'] 	= $res.' Likes';
                    $op['msg'] 		= trans('user/account.thanks_to_like');
                    $op['status']   = $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
                else
                {			
					$op['count'] = $this->storeObj->likeCount($data) . ' Likes';
                    $op['msg'] = 'You already liked this store.';
                    $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
            }
            else
            {				
				$op['count'] = $this->storeObj->likeCount($data) . ' Likes';
                $op['msg'] = 'Not yet liked this store.';
                $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            }
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }	
	
	public function add_store_to_favourite ()
    {
        $wdata = [];
        $wdata = $this->request->all();
        $wdata['account_id'] = $this->userSess->account_id;
        $res = $this->storeObj->add_store_to_favourite($wdata);		
        if ($res)
        {
            $op['msg'] = $res['msg'];
            if (isset($res['fav_id']))
            {
                $op['fav_id'] = $res['fav_id'];
            }
            $op['status'] = $status = $res['status'];
        }
        else
        {
            $op['data'] = [];
            $op['msg'] = 'Store exist as your favourite';
            $op['status'] = 'error';
            $status = $this->config->get('httperr.PARAMS_MISSING');
        }
        return $this->response->json($op, $status, $this->headers, $this->options);
    }
	
	public function OnlineStoreList ($category = null)
    { 
        $data 		= [];
        $postdata 	= $this->request->all();
	    $ajaxdata 	= [];
        $ajaxdata['data'] = $data = $filter = [];
        $ajaxdata['draw'] = $this->request->draw;        
        $data['country_id'] = $this->geo->browse->country_id;
		$data['is_featured'] 	= 0;
		$ajaxdata['main_category']  = true;
        if (isset($this->userSess->account_id))
        {
            $data['account_id'] = $this->userSess->account_id;
        }
        if (!empty($postdata))
        {
            $filter['search_term'] = !empty($postdata['search_term']) ? $postdata['search_term'] : null;
        }
        if (!empty($category))
        {			
	
			$wdata['category_slug'] = $category;				
			$ajaxdata['category'] = $this->storeObj->onlinestoreCategories($wdata);			
			
			
            if(strtolower($category) == 'popular')
            {
                $ajaxdata['categories'] = $this->storeObj->OnlineStoreList($data, false, true);		
				//print_r($ajaxdata);exit;	
            }
			elseif(strtolower($category) == 'trending')
			{
				$data['is_featured'] = $this->config->get('constants.ON');
			}
            else
            {
				$data['is_featured'] 	 = false;
				$data['category']        = $category;
		    }
        } 		
        $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->storeObj->OnlineStoreList($data, true);			
	    if (!empty($ajaxdata['recordsTotal']) && $ajaxdata['recordsTotal'] > 0)
        {
            $filter = array_filter($filter);
            if (!empty($filter))
            {
                $data = array_merge($data, $filter);
                $ajaxdata['recordsFiltered'] = $this->storeObj->OnlineStoreList($data, true);
            }
            $data['length'] = $ajaxdata['length'] = isset($postdata['length']) && !empty($postdata['length']) ? $postdata['length'] : 10;
            $data['page'] = $ajaxdata['cpage'] = isset($postdata['page']) && !empty($postdata['page']) ? $postdata['page'] : 1;
            $data['start'] = isset($postdata['start']) && !empty($postdata['start']) ? $postdata['start'] : ($data['page'] - 1) * $data['length'];
            $ajaxdata['move_next'] = (($data['start'] + $data['length']) < $ajaxdata['recordsFiltered']) ? true : false;
            $data['orderby'] = isset($postdata['orderby']) ? $postdata['orderby'] : (isset($postdata['order'][0]['column']) ? $postdata['columns'][$postdata['order'][0]['column']]['name'] : null);
            $data['order'] = isset($postdata['order']) ? (is_array($postdata['order']) ? $postdata['order'][0]['dir'] : $postdata['order']) : 'ASC';
		    $ajaxdata['data'] = $this->storeObj->OnlineStoreList($data);
			$ajaxdata['status'] = $status = $this->config->get('httperr.SUCCESS');
	    }
        else
        {
            $ajaxdata['msg'] = 'Data not Found';
            $ajaxdata['status'] = $status = $this->config->get('httperr.NO_DATA_FOUND');			             
        }
        return $this->response->json($ajaxdata, $status, $this->headers, $this->options);
    }
	
	public function OnlineStoreDetails()
    {
        $op 			=  [];
        $data 			=  $this->request->all();
		$op['is_login'] = false;
		//$op['similars'] = [];
		//$op['trending'] = [];
	    if(isset($this->userSess->account_id))
        {
            $data['account_id'] = $this->userSess->account_id;
            $op['is_login']   = true;
        }
        $store = $this->storeObj->OnlineStoreDetails($data);		
        if($store !=''){
				$data['category_id'] = $store->category_id;
				$data['store_id'] = $store->store_id;
				$this->session->set('similar_stores',$data);
		}
	    if($store)
        {
            $op['details'] = (array) $store;
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
        }
        else
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.NOT_FOUND');
            $op['msg'] = 'Data Not Found';
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	
}
