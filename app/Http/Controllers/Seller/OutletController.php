<?php
namespace App\Http\Controllers\Seller;
use App\Http\Controllers\SupplierBaseController;
use App\Http\Controllers\MyImage;
use App\Models\Seller\Outlet;
use App\Models\Seller\Supplier;
use App\Models\Commonsettings;
use Config;
use Lang;
use Redirect;
use Input;
use View;
use Request;
use Response;
use File;


class OutletController extends SupplierBaseController
{

    public $data = array();

    public function __construct ()
    {
        parent::__construct();
		$this->outlet = new Outlet();	
		$this->imgObj = new MyImage();  
		$this->commObj = new Commonsettings();      	
    }    

    public function OutletList ()
    {		
		if (Request::ajax()) {
			$postdata = $this->request->all();
			$op['recordsFiltered'] = 0;
			$op['data'] = $filter = [];
			$op['draw'] = $this->request->draw;
			$wdata['account_id'] = $this->userSess->account_id;
			$wdata['supplier_id'] = $this->userSess->supplier_id;
			$wdata['country_id'] = $this->country_id;
			if (!empty($postdata))
			{
				$filter['category_id'] = !empty($postdata['category_id']) ? $postdata['category_id'] : '';
				$filter['search_term'] = !empty($postdata['search_term']) ? trim($postdata['search_term']) : '';
				$filter['status'] = !empty($postdata['status']) ? ($postdata['status'] == 3) ? 0 : $postdata['status'] : '';
				$filter['from'] = !empty($postdata['from_date']) ? $postdata['from_date'] : '';
				$filter['to'] = !empty($postdata['to_date']) ? $postdata['to_date'] : '';
				$filter['store'] = !empty($postdata['store']) ? $postdata['store'] : '';
				$submit = isset($postdata['submit']) ? $postdata['submit'] : '';
			}
			$wdata = array_merge($wdata, $filter);
			
			$op['recordsTotal'] = $this->outlet->store_list($wdata, true);
			//print_r($op);exit;
			if (!empty($op['recordsTotal']) && $op['recordsTotal'] > 0)
			{
				$op['recordsFiltered'] = $op['recordsTotal'];  //if no records in data in tables 0 records//
				$wdata['start'] = !empty($postdata['page']) ? ($postdata['page'] - 1) : 0;
				$wdata['length'] = !empty($postdata['length']) ? $postdata['length'] : 10;
				$op['length'] = $wdata['length'];
				$op['cpage'] = ($wdata['start'] + 1);
				$op['move_next'] = (($wdata['start'] + 1) < ceil($op['recordsFiltered'] / $wdata['length'])) ? true : false;
				if (isset($wdata['order']))
				{
					$wdata['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
					$wdata['order'] = $postdata['order'][0]['dir'];
				}
				$op['data'] = $this->outlet->store_list($wdata);
			}			
			return \Response::json($op, Config::get('httperr.SUCCESS'));
		} else {
			return View::make('seller.outlet.outlet_list');
		}
    }
	
	public function store_images ($store_code)
    {
        $data['store_code'] = $store_code; 
        $data['status'] = trans('general.stores.images.status');        
		//print_r($data);exit;
		//$data['ufields'] = CommonLib::getHTMLValidation('retailer.profile-settings.photos.upload');		
        return view('seller.outlet.outlet_images', $data);
    }

	public function list_photos ()
    {
        $data = $filter = array();
        $data = $ajaxdata = [];
		$postdata = $this->request->all();
        $data['account_id'] = $this->userSess->account_id;
        $data['store_id'] = $this->userSess->store_id;
        $data['supplier_id'] = $this->userSess->supplier_id;
        $data['store_code'] = $postdata['store_code'];
		//print_r($data);EXIT;
        $ajaxdata = array();
        $this->statusCode = $this->config->get('httperr.SUCCESS');
        
        if (!empty($postdata))
        {
            $filter['search_text'] = isset($postdata['search_text']) && !empty($postdata['search_text']) ? $postdata['search_text'] : NULL;
            $filter['from'] = isset($postdata['from']) && !empty($postdata['from']) ? $postdata['from'] : NULL;
            $filter['to'] = isset($postdata['to']) && !empty($postdata['to']) ? $postdata['to'] : NULL;
            $filter['store_code'] = isset($postdata['store_code']) && !empty($postdata['store_code']) ? $postdata['store_code'] : NULL;
        }
        $ajaxdata = [];
        $ajaxdata['data'] = array();
        if (isset($postdata['draw']))
        {
            $ajaxdata['draw'] = $postdata['draw'];
        }
        //$ajaxdata['store_info'] = $this->accountObj->getStoreDetails($data);
        $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->outlet->getMerchantPhotos($data, true);
        if (!empty($ajaxdata['recordsTotal']))
        {
            $filter = array_filter($filter);
            if (!empty($filter))
            {
                $data = array_merge($data, $filter);
                $ajaxdata['recordsFiltered'] = $this->outlet->getMerchantPhotos($data, true);
            }
            if (!empty($ajaxdata['recordsFiltered']))
            {
                $ajaxdata['length'] = $data['length'] = $ajaxdata['length'] = isset($postdata['length']) && !empty($postdata['length']) ? $postdata['length'] : 10;
                $data['page'] = $ajaxdata['cpage'] = isset($postdata['lengtpageh']) && !empty($postdata['page']) ? $postdata['page'] : 1;
                $ajaxdata['start'] = $data['start'] = isset($postdata['start']) && !empty($postdata['start']) ? $postdata['start'] : ($data['page'] - 1) * $data['length'];
                $ajaxdata['move_next'] = (($data['start'] + $data['length']) < $ajaxdata['recordsFiltered']) ? true : false;
                $data['orderby'] = isset($postdata['orderby']) ? $postdata['orderby'] : (isset($postdata['order'][0]['column']) ? $postdata['columns'][$postdata['order'][0]['column']]['name'] : null);
                $data['order'] = isset($postdata['order']) ? (is_array($postdata['order']) ? $postdata['order'][0]['dir'] : $postdata['order']) : 'ASC';

                $ajaxdata['data'] = $this->outlet->getMerchantPhotos($data);
            }
        }
        return $this->response->json($ajaxdata, $this->statusCode, $this->headers, $this->options);
    }
	
	public function updateStoreImageStatus ($status, $id)
    {
        $op = [];
        $data['id'] = $id;
        $status = strtoupper($status);
        if (array_key_exists($status, $this->config->get('constants.STORE_IMAGE.STATUS')))
        {
            $data['status'] = $this->config->get('constants.STORE_IMAGE.STATUS.'.$status);
            $data['supplier_id'] = $this->userSess->supplier_id;
            if ($this->outlet->updateStoreImageStatus($data))
            {
                $this->statusCode = $this->config->get('httperr.SUCCESS');
                $op['msg'] = trans('general.updated', ['which'=>trans('general.stores.images.outlet_img'), 'what'=>trans('general.stores.images.status.'.$data['status'])]);
            }
            else
            {
                $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
                $op['msg'] = trans('general.already', ['which'=>trans('general.stores.images.outlet_img'), 'what'=>trans('general.stores.images.status.'.$data['status'])]);
            }
        }
        else
        {
            $this->statusCode = $this->config->get('httperr.NOT_FOUND');
            $op['msg'] = trans('general.not_found', ['which'=>trans('general.label.page')]);
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function deleteStoreImage ($id)
    {
        $op = [];
        $data['id'] = $id;
        $data['supplier_id'] = $this->userSess->supplier_id;
        if ($this->outlet->deleteStoreImage($data))
        {
            $this->statusCode = $this->config->get('httperr.SUCCESS');
            $op['msg'] = trans('general.updated', ['which'=>trans('general.stores.images.outlet_img'), 'what'=>trans('general.deleted')]);
        }
        else
        {
            $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
            $op['msg'] = trans('general.already', ['which'=>trans('general.stores.images.outlet_img'), 'what'=>trans('general.deleted')]);
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function take_photo ()
    {
        $data = $op = [];
        $data = $this->request->all();        
        //$full_token = explode('-', $this->userSess->full_token);		
        /* $sdata = $this->userSess->$full_token[1];
          $data['store_id'] = $sdata['store_id']; */
        $data['account_id'] = $this->userSess->account_id;
        $data['supplier_id'] = $this->userSess->supplier_id;
        $data['account_type_id'] = $this->userSess->account_type_id;
		
        if (!empty($data['files']) && !empty($data['store_code']))
        {
            //$data['store_id'] = isset($data['store_code']) && !empty($data['store_code']) ? $this->commonObj->getStoreId($data['store_code']) : null;
            $data['store_id'] = $this->outlet->getStoreId($data['store_code']);
			$settings = json_decode(stripslashes(stripslashes($this->outlet->getSetting('merchant_upload_file_settings'))));
            if (!empty($data['store_id']))
            {
                unset($data['store_code']);				                
                $data['imgObj'] = $this->imgObj;				
                $result = $this->outlet->uploadMerchantPhotos($data);
				//print_r($result);exit;
                if (is_array($result) && empty($result))
                {
                    $op['msg'] = 'Image Added Successfully';
                    $this->statusCode = $this->config->get('httperr.SUCCESS');
                }
                else if ((is_array($result)) && !empty($result))
                {
                    if (array_key_exists('invalid_files', $result))
                    {
                        $files = implode(', ', $result['invalid_files']);
                        $op['msg'] = trans('seller/account.invalid_file', ['file'=>$files]);
                        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                    }
                    else
                    {
                        $files = implode(', ', $result);
                        $op['msg'] = trans('seller/account.not_uploadable', ['file'=>$files, 'size'=>$settings->vedio_file_size]);
                        $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                    }
                }
                else
                {
                    $op['msg'] = trans('general.no_changes');
                    $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                }
            }
            else
            {
                $op['msg'] = trans('general.store_not_found');
                $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            }
        }
        else
        {
            $op['msg'] = trans('general.no_changes');
            $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function update_store_status ($status, $code)
    {
        $op = [];
        $data['code'] = $code;
        $status = strtoupper($status);		
        if (array_key_exists($status, Config::get('constants.SELLER.STORE.STATUS')))
        {
            $data['status'] = Config::get('constants.SELLER.STORE.STATUS.'.$status);
            $data['account_id'] = $this->userSess->account_id;
            $data['supplier_id'] = $this->userSess->supplier_id;
            $data['account_type_id'] = $this->userSess->account_type_id;
			
            if ($res = $this->outlet->update_store_status($data))
            {				
                $op['status'] = $this->statusCode = Config::get('httperr.SUCCESS');
                $op['msg'] = trans('general.updated', ['which'=>trans('general.stores.title'), 'what'=>trans('general.merchants.status.'.$data['status'])]);
            }
            else
            {
                $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
                $op['msg'] = trans('general.already', ['which'=>trans('general.stores.title'), 'what'=>trans('general.merchants.status.'.$data['status'])]);
            }
        }
        else
        {
            $op['status'] = $this->statusCode = Config::get('httperr.NOT_FOUND');
            $op['msg'] = trans('general.not_found', ['which'=>trans('general.label.page')]);
        }
        return \Response::json($op, $this->statusCode);
    }
	
	public function store_view_details ($store_code)
    { 
	    $postdata = $wdata = $opening_hour = $hrs = $op = [];
        $postdata = $this->request->all();
        $tot_orders = 0;
        $postdata['store_code'] = $store_code;
        $postdata['country_id'] = $this->country_id;
        $postdata['supplier_id'] = $this->userSess->supplier_id;
        $res = $this->outlet->store_view_details($postdata);		
        if (!empty($res))
        {  	        
            $op['view'] = $res;           
            $this->statusCode = Config::get('httperr.SUCCESS');          
        }
        else
        {
            $this->statusCode = Config::get('httperr.NOT_FOUND');
            //$op['msg'] = trans('general.not_found', ['which'=>trans('general.merchants.stores.store')]);
            $op['msg'] = 'Store Not Found';
        }		
		return \Response::json($op, $this->statusCode);
    }
	
	public function store_details ($store_code)
    {
        $wdata = $opening_hour = $hrs = [];      
        $tot_orders = 0;        
        $postdata['store_code'] = $store_code;
        $postdata['country_id'] = $this->country_id;
        $postdata['supplier_id'] = $this->userSess->supplier_id;
        $res = $this->outlet->store_details($postdata);	
        if (!empty($res))
        {
            $hrs = $this->outlet->store_business_hrs(['store_id'=>$res->store_id, 'has_sp_work_time'=>$res->sp_has_specific_hrs, 'has_store_work_time'=>$res->has_specific_hrs, 'supplier_id'=>$this->userSess->supplier_id, 'is_primary'=>$res->is_primary]);					
            $tot_orders = 5;
            $op['data'] = $res;
            $op['data']->operating_hrs = isset($hrs['days']) ? $hrs['days'] : null;
            $op['open_hrs'] = isset($hrs['days']) ? $hrs['days'] : null;
            $op['total_sales'] = $tot_orders;
            $op['currency'] = $res->currency_code;
            unset($res->currency_code);
            $op['store_rating'] = 5;
            $opening_hour = [];
            if (isset($hrs['days']))
            {
                foreach ($hrs['days'] as $kday=> $wday)
                {
                    $i = 0;
                    $sessTime = ['label'=>($i == 0 ? ucwords($kday) : '')];
                    $split = count($wday) == 2 ? true : false;
                    foreach ($wday as $key=> $wsess)
                    {

                        if ($wsess['is_closed'] == 0)
                        {
                            $sessTime['split_'.($i + 1)] = $wsess['from'].' - '.$wsess['to'];
                            //if ($key == 2) { $sessTime['session'] = 'open'; }

                            $sessTime['session'] = 'Open';
                        }
                        else
                        {
                            $sessTime['session'] = 'Closed';
                        }
                        $i++;
                    }
                    $opening_hour[] = $sessTime;
                }
            }
            $op['open_hrs'] = $opening_hour;
            $op['split_hours'] = isset($hrs['splitHrs']) && $hrs['splitHrs'] == 0 ? 0 : 1;
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            return \Response::json($op, $this->statusCode);
        }
        else
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            $op['msg'] = trans('general.not_found', ['which'=>trans('general.seller.seller')]);
        }
        return \Response::json($op, $this->statusCode);
    }
	
	public function store_update_web ($store_code)
    {
        $op = $wdata = $postdata = [];
        $postdata = $this->request->all();
	    $wdata['store_code'] = $store_code;
        $wdata['supplier_id'] = $this->userSess->supplier_id;
        $wdata['account_id'] = $this->userSess->account_id;
        $wdata['country_id'] = $this->userSess->country_id;  
		if (!empty($postdata['store_logo']))
		{
			$attachment = $postdata['store_logo'];
			$size = $attachment->getSize();
			if ($size < 2049133)
			{
				$filename = '';
				if ($attachment != '')
				{
					$folder_path = '/'.getGTZ('Y').'/'.getGTZ('m').'/';						
					$path = $this->config->get('path.SELLER.STORE_IMG_PATH.LOCAL');
					$org_path = $this->config->get('path.SELLER.STORE_IMG_PATH.ORIGINAL');
					$postdata['account_id'] = $this->userSess->account_id;
					$postdata['role_id'] = $this->userSess->account_type_id;
					
					if (File::exists($path.'/'.getGTZ('Y')))
					{
						if (!File::exists($path.'/'.getGTZ('Y').'/'.getGTZ('m')))
						{
							File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
					}
					else
					{
						File::makeDirectory($path.'/'.getGTZ('Y'));
						File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
					}
					
					if (File::exists($org_path.'/'.getGTZ('Y')))
					{
						if (!File::exists($org_path.'/'.getGTZ('Y').'/'.getGTZ('m')))
						{
							File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
					}
					else
					{
						File::makeDirectory($org_path.'/'.getGTZ('Y'));
						File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
					}
					$ext = $attachment->getMimeType();
						$mine_type = array(
							'image/jpeg'=>'jpg',
							'image/jpg'=>'jpg',
							'image/png'=>'png',
							'image/gif'=>'gif');
						$ext = $mine_type[$ext];
					if(in_array($ext, array('jpg', 'jpeg', 'png')))
					{
						$org_name = $attachment->getClientOriginalName();
						$file_extentions = strtolower($ext);
						$filtered_name = $this->slug($org_name);
						$file_name = explode('_', $filtered_name);
						$file_name = $file_name[0];
						$file_name = $file_name.'.'.$ext;							
						$filename = getGTZ('dmYHis').$file_name;
						
						$uploaded_file = $attachment->move($path.$folder_path, $filename);
						//$this->imgObj->imageresize($org_path.$folder_path.$filename, $path.$folder_path.$filename, 200, 200);
						if ($uploaded_file) {								
							$postdata['store_logo'] = $folder_path.$filename;
						}
					}
					else
					{
						$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
						$op['msg'] = 'Invalid Profile ImageFile Formate';
						return Response::json($op, $this->statusCode, $this->headers, $this->options);
					}
				}					
			}
			else
			{					
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
				$op['msg'] = 'Profile Image Size is greater than 1 MB';
				return Response::json($op, $this->statusCode, $this->headers, $this->options);
			}				
		}
		$filter = array_filter($postdata);
		$wdata 	= array_merge($wdata, $filter);				
		$res 	= $this->outlet->store_update_web($wdata);		
		if(!empty($res))
		{
			$op['msg'] = 'Shop Updated Successfully.';
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
		}
		else
		{
			$op['data'] = [];
			$op['status'] = $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
			$op['msg'] = trans('retailer/account.no_changes');
		}        
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	
	public function store_create_web ()
    {
        $op = $wdata = $postdata = [];
        $postdata = $this->request->all();
        $wdata['supplier_id'] = $this->userSess->supplier_id;
        $wdata['category_id'] = $this->userSess->category_id;
        $wdata['currency_id'] = $this->userSess->currency_id;
        $wdata['country_id'] = $this->userSess->country_id;
        $wdata['account_id'] = $this->userSess->account_id;
		
        if (!empty($postdata['store_logo']))
		{
			$attachment = $postdata['store_logo'];
			$size = $attachment->getSize();
			if ($size < 2049133)
			{
				$filename = '';
				if ($attachment != '')
				{
					$folder_path = '/'.getGTZ('Y').'/'.getGTZ('m').'/';						
					$path = $this->config->get('path.SELLER.STORE_IMG_PATH.LOCAL');
					$org_path = $this->config->get('path.SELLER.STORE_IMG_PATH.ORIGINAL');
					$postdata['account_id'] = $this->userSess->account_id;
					$postdata['role_id'] = $this->userSess->account_type_id;
					
					if (File::exists($path.'/'.getGTZ('Y')))
					{
						if (!File::exists($path.'/'.getGTZ('Y').'/'.getGTZ('m')))
						{
							File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
					}
					else
					{
						File::makeDirectory($path.'/'.getGTZ('Y'));
						File::makeDirectory($path.'/'.getGTZ('Y').'/'.getGTZ('m'));
					}
					
					if (File::exists($org_path.'/'.getGTZ('Y')))
					{
						if (!File::exists($org_path.'/'.getGTZ('Y').'/'.getGTZ('m')))
						{
							File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
						}
					}
					else
					{
						File::makeDirectory($org_path.'/'.getGTZ('Y'));
						File::makeDirectory($org_path.'/'.getGTZ('Y').'/'.getGTZ('m'));
					}
						$ext = $attachment->getMimeType();
						$mine_type = array(
							'image/jpeg'=>'jpg',
							'image/jpg'=>'jpg',
							'image/png'=>'png',
							'image/gif'=>'gif');
						$ext = $mine_type[$ext];
					if (in_array($ext, array('jpg', 'jpeg', 'png')))
					{
						$org_name = $attachment->getClientOriginalName();
						//$ext = $attachment->getClientOriginalExtension();
						$file_extentions = strtolower($ext);
						$filtered_name = $this->slug($org_name);
						$file_name = explode('_', $filtered_name);
						$file_name = $file_name[0];
						$file_name = $file_name.'.'.$ext;							
						$filename = getGTZ('dmYHis').$file_name;
						
						$uploaded_file = $attachment->move($path.$folder_path, $filename);
						//$this->imgObj->imageresize($org_path.$folder_path.$filename, $path.$folder_path.$filename, 200, 200);
						if ($uploaded_file) {								
							$postdata['store_logo'] = $folder_path.$filename;
						}else{
							$postdata['store_logo'] = null;
						}
					}
					else
					{
						$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
						$op['msg'] = 'Invalid Profile ImageFile Formate';
						return Response::json($op, $this->statusCode, $this->headers, $this->options);
					}
				}					
			}
			else
			{					
				$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
				$op['msg'] = 'Profile Image Size is greater than 1 MB';
				return Response::json($op, $this->statusCode, $this->headers, $this->options);
			}				
		}			
        $filter = array_filter($postdata);
		$wdata = array_merge($wdata, $filter);
		$res = $this->outlet->store_create_web($wdata);
		//print_r($res);exit;
		if (!empty($res))
		{
			$op['msg'] = $res['msg'];
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
		}
		else
		{
			$op['data'] = [];
			$op['status'] = $this->statusCode = $this->config->get('httperr.ALREADY_UPDATED');
			$op['msg'] = trans('retailer/store.no_changes');
		}    
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }

}
