<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\BaseController;
use App\Models\Admin\AffModel;
use App\Models\Admin\OnlineStore;
use App\Models\Commonsettings;
use App\Models\LocationModel;
use TWMailer;
use Response; 
use Request;
use View;
use URL;
use File;

class OnlineStoreController extends BaseController
{
    public function __construct ()
    {
        parent::__construct();
        $this->affObj = new AffModel();
        $this->storeObj = new OnlineStore();
		$this->lcObj = new LocationModel();		
		$this->commonObj = new Commonsettings();
    }    
	public function affiliateAdd() {	        
        $data['categories'] = $this->commonObj->getCategoriesList($this->config->get('constants.BCATEGORY_TYPE.ONLINE_STORE'));
	 	$data['netwrks'] = $this->commonObj->OnlineaffilateNetwrks();
	    $data['country_list'] = $this->commonObj->get_countries();
		$data['banners'] 	= $this->storeObj->get_banners_list(); 
		//echo '<pre>';print_r($data);exit;
	    return view('admin.affiliate.add_store', $data);         
    }
	public function saveAffiliate($id = null)
    {
        $op 		= [];
        $data 		= $this->request->all();
        $data['id'] = $id;
        $data['account_id'] = $this->userSess->account_id;				
        $data['currency_id'] = $this->userSess->currency_id;	
		if ($data['img_type'] == 2) {
			if (!empty($data['image_url']))
			{
				$data['status'] = '';
				$data['msg'] = '';
				$ext = $data['image_url']->getMimeType();
				$mine_type = array(
					'image/jpeg'=>'jpg',
					'image/jpg'=>'jpg',
					'image/png'=>'png',
					'image/gif'=>'gif');
				$ext = $mine_type[$ext];
				$random_no = rand(10000, 15);
				$filename = "category".$random_no.'.'.$ext;
				$folder_path = getGTZ('Y').'/'.getGTZ('m').'/';
				//$desiDir = $this->config->get('constants.AFFILIATE.STORE.LOGO_PATH.LOCAL');
				$desiDir = $this->config->get('path.ONLINE.STORE.LOGO_PATH.LOCAL');
				if (File::exists($desiDir.getGTZ('Y')))
				{
					if (!File::exists($desiDir.getGTZ('Y').'/'.getGTZ('m')))
					{
						File::makeDirectory($desiDir.getGTZ('Y').'/'.getGTZ('m'));
					}
				}
				else
				{
					File::makeDirectory($desiDir.getGTZ('Y'));
					File::makeDirectory($desiDir.getGTZ('Y').'/'.getGTZ('m'));
				}
				if (!empty($filename))
				{
					$data['image_url']->move($desiDir.$folder_path, $filename);
					$modfilename = $folder_path.$filename;
				}
				$op = array(
					'status'=>422,
					'msg'=>'We couldnt able to update your affiliate image.'); 
			}
			else
			{
				$modfilename = NULL;
			}
			$data['affiliate']['store_logo'] = $modfilename;
			$data['affiliate']['logo_url'] = null;
		}			
        if($res = $this->storeObj->saveAffiliate($data, $id))
        {				
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            $op['msg'] = trans('general.updated', ['which'=>'Affiliate', 'what'=>'updated']);
        }
        else
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            $op['msg'] = trans('general.already', ['which'=>'Affiliate', 'what'=>trans('general.updated')]);
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
    public function affiliateSignupCategories ()
    {
        $data = $this->request->all();
        $op = $this->storeObj->getAffiliateSignupCategories($data);
        return $this->response->json($op, $this->config->get('httperr.SUCCESS'), $this->headers, $this->options);
    }
   public function country_list ()
     {
        $data = $this->request->all();
        $res = $this->storeObj->country_lists();
        ($data);
        if ($res)
        {
            $op = $res;
            return $this->response->json($op, $this->config->get('httperr.SUCCESS'), $this->headers, $this->options);
        }
        else
        {
            $op['data'] = [];
            $op['status'] = 'error';
            $op['msg'] = 'Currencies Not Found';
            return $this->response->json($op, $this->config->get('httperr.PARAMS_MISSING'), $this->headers, $this->options);
        }
     }
  public function affiliateList ()
     { 
        $op = array();
        $data = $filter = array();
        $postdata = $this->request->all();	
	    if (!empty($postdata))
        {	
            $filter['search_text'] = isset($postdata['search_text']) && !empty($postdata['search_text']) ? trim($postdata['search_text']) : NULL;
            $filter['from'] = isset($postdata['from_date']) && !empty($postdata['from_date']) ? $postdata['from_date'] : NULL;
            $filter['to'] = isset($postdata['to_date']) && !empty($postdata['to_date']) ? $postdata['to_date'] : NULL;
			$filter['country'] = isset($postdata['country']) && !empty($postdata['country']) ? $postdata['country'] : NULL;
        }
        if ($this->request->ajax())
        {
	        $ajaxdata = [];
            $ajaxdata['data'] = array();
            if (isset($postdata['draw']))
            {
                $ajaxdata['draw'] = $postdata['draw'];
            }
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->storeObj->getAffiliateList($data, true);
            if (!empty($ajaxdata['recordsTotal']))
            {
                $filter = array_filter($filter);
                if (!empty($filter))
                {
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->storeObj->getAffiliateList($data, true);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
				    $data['start']  = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : 10;

                    $data['orderby'] = $postdata['columns'][$postdata['order'][0]['column']]['name'];
                    $data['order'] = $postdata['order'][0]['dir'];
                    $ajaxdata['data'] = $this->storeObj->getAffiliateList($data);
                }
            }
		    $this->statusCode = $this->config->get('httperr.SUCCESS');
            return $this->response->json($ajaxdata, $this->statusCode, $this->headers, $this->options);
        }
        else
        {
	   $data['categories'] = $this->commonObj->getCategoriesList($this->config->get('constants.BCATEGORY_TYPE.ONLINE_STORE'));
	 	$data['netwrks'] = $this->commonObj->OnlineaffilateNetwrks();
	    $data['country_list'] = $this->commonObj->get_countries();
		$data['banners'] 	= $this->storeObj->get_banners_list(); 
            return view('admin.affiliate.list', $data);
        }
    }
  public function updateAffiliateStatus ($status, $id)
     {
        $op = $data = [];
        $data['id'] = $id;
        $data['status'] = $this->config->get('constants.AFFILIATE.STATUS.'.strtoupper($status));
        $data['account_id'] = $this->userSess->account_id;
        if ($this->storeObj->updateAffiliateStatus($data))
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            $op['msg'] = trans('general.updateds', ['which'=>'Affiliate', 'what'=>trans('general.affiliates.status.'.$data['status'])]);
        }
        else
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            $op['msg'] = trans('general.already', ['which'=>'Affiliate', 'what'=>trans('general.affiliates.status.'.$data['status'])]);
        }
	
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	 public function affiliateDelete ($id)
     {
        $op = [];
        $data['id'] = $id;
        $data['account_id'] = $this->userSess->account_id;
        if ($this->storeObj->affiliateDelete($data))
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
            $op['msg'] = trans('general.updateds', ['which'=>'Affiliate', 'what'=>trans('general.deleted')]);
        }
        else
        {
            $op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
            $op['msg'] = trans('general.already', ['which'=>'Affiliate', 'what'=>trans('general.deleted')]);
        }
        return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
    }
	public function affiliateDetails ($id)
    {
        $op  = $data  = [];
	    $data['id']   = $id;
		if($details = $this->storeObj->affiliateDetails($data))
		{
			$op['details'] = $details;
			$op['status'] = $this->statusCode = $this->config->get('httperr.SUCCESS');
		}
		else
		{
			$op['status'] = $this->statusCode = $this->config->get('httperr.UN_PROCESSABLE');
			$op['msg'] = trans('general.not_found');
		}
		if($this->request->isMethod('post')){
			return $this->response->json($op, $this->statusCode, $this->headers, $this->options);
		}else{
			return view('admin.affiliate.details',$op);
		}
    }
       /* Online Store Categories */
    public function getOnlineCategory_list (){
		$data = $filter = array();
        $postdata = $this->request->all();
        if (!empty($postdata)){
            $filter['search_text'] = (isset($postdata['search_term']) && !empty($postdata['search_term'])) ? trim($postdata['search_term']) : '';
            $filter['from'] = (isset($postdata['from_date']) && !empty($postdata['from_date'])) ? $postdata['from_date'] : '';
            $filter['to'] = (isset($postdata['to_date']) && !empty($postdata['to_date'])) ? $postdata['to_date'] : '';
            $filter['parent_category_id'] = (isset($postdata['parent_category_id']) && !empty($postdata['parent_category_id'])) ? $postdata['parent_category_id'] : '';
			$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		    }
        if ($this->request->isMethod('post')){
            $ajaxdata = [];
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->storeObj->getOnlineCategory_list([], true);
            $ajaxdata['data'] = array();
            $ajaxdata['draw'] = $postdata['draw'];
            if ($ajaxdata['recordsTotal'])
            {
                $filter = array_filter($filter);
                if (!empty($filter)){
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->storeObj->getOnlineCategory_list($data, true);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : 10;
                    $ajaxdata['data'] = $this->storeObj->getOnlineCategory_list($data);
                }
            }
            return $this->response->json($ajaxdata, 200, $this->headers, $this->options);
         }
	  elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
          {
			$edata['manage_online_category_details'] = $this->storeObj->getOnlineCategory_list(array_merge($data,$filter));	
            $output = view('admin.online_category.manage_online_category_export',$edata);
                    
            $headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=online_category_details' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
            return Response::make($output, 200, $headers);
        }
       else{
            return view('admin.online_category.category_list');
         }
	}
	public function getOnlineCategories ()
      {
        $wdata = [];
        $postdata = $this->request->all();
        if (!empty($postdata))
        {
            if (isset($postdata['pbcat_id']))
            {
                $wdata['pbcat_id'] = $postdata['pbcat_id'];
            }
            if (isset($postdata['excbcat_id']))
            {
                $wdata['excbcat_id'] = $postdata['excbcat_id'];
            }
            if (isset($postdata['cat_id']))
            {
                $wdata['cat_id'] = $postdata['cat_id'];
            }
            if (isset($postdata['excpbcat_id']))
            {
                $wdata['excpbcat_id'] = $postdata['excpbcat_id'];
            }
        }
        $data = $this->storeObj->getOnlineCategoriesMin($wdata);
        return $this->response->json($data, 200, $this->headers, $this->options);
    }
  public function check_onlineCategory_slug ()
    {
        $op = $postdata = array();
        $postdata = $this->request->all();
        if (!empty($postdata['bcategory_slug']))
        {
            $bcategory['bcategory_id'] = (isset($postdata['bcategory_id']) && !empty($postdata['bcategory_id'])) ? $postdata['bcategory_id'] : '';
            $bcategory['bcategory_slug'] = strip_tags($postdata['bcategory_slug']);
            $bcategory['category_type'] = $this->config->get('constants.BCATEGORY_TYPE.ONLINE_STORE');
            $result = $this->storeObj->checkCategorySlug($bcategory);
            if (!empty($result))
            {
                $op['status'] = 200;
            }
            else
            {
                $op['status'] = 422;
                $op['msg'] = trans('admin/online_category/category.url_exist');
            }
        }
        else
        {
            $op = array(
                'status'=>422,
                'msg'=>trans('admin/online_category/category.enter_url'));
        }
        return $this->response->json($op, 200, $this->headers, $this->options);
    }
	
    public function saveOnlineCategory ()
    {

        $op                  = array();
        $postdata            = $this->request->all();
        $sdata['admin_id']   = $this->userSess->account_id;
        $sdata['created_by'] = $this->userSess->account_id;
        $op['status']        = 'ERR';
        $op['msg']           = trans('general.something_wrong');
        $modfilename         = NULL;
        if(!empty($postdata['image_url']))
        {
            $ext = $postdata['image_url']->getMimeType();
            $mine_type = array(
                'image/jpeg'=>'jpg',
                'image/jpg'=>'jpg',
                'image/png'=>'png',
                'image/gif'=>'gif');
            $ext = $mine_type[$ext];
            $random_no = rand(10000, 15);
            $filename = 'category'.$random_no.'.'.$ext;
            $folder_path = getGTZ('Y').'/'.getGTZ('m').'/';
            $desiDir = $this->config->get('constants.BCATEGORY_IMG_PATH.LOCAL');
            if (File::exists($desiDir.getGTZ('Y')))
            {
                if (!File::exists($desiDir.getGTZ('Y').'/'.getGTZ('m')))
                {
                    File::makeDirectory($desiDir.getGTZ('Y').'/'.getGTZ('m'));
                }
            }
            else
            {
                File::makeDirectory($desiDir.getGTZ('Y'));
                File::makeDirectory($desiDir.getGTZ('Y').'/'.getGTZ('m'));
            }
            if (!empty($filename))
            {
                $postdata['image_url']->move($desiDir.$folder_path, $filename);
                $modfilename = $folder_path.$filename;
            }
            $op = array(
                'status'=>422,
                'msg'=>'We couldnt able to update your profile image.');
        }
        else
        {
            $modfilename = NULL;
        }
        if (empty($postdata['category_id']))
        {

            $category = ['category'=>['category_image'=>$modfilename, 'meta_title'=>strip_tags($postdata['meta_title']), 'meta_desc'=>strip_tags($postdata['meta_desc']), 'meta_keywords'=>strip_tags($postdata['meta_keywords']), 'bcategory_name'=>strip_tags(trim($postdata['bcategory_name'])), 'bcategory_slug'=>strip_tags($postdata['bcategory_slug']), 'parent_bcategory_id'=>$postdata['parent_bcategory_id'], 'created_by'=>$sdata['created_by'], 'admin_id'=>$sdata['admin_id']]];
            $result = $this->storeObj->saveOnlineCategory($category);
            if (!empty($result))
            {
                $op['status'] = 'OK';
                $op['msg'] = trans('admin/online_category/category.added');
            }
            else
            {
                $op['msg'] = trans('general.there_is_no_changes');
            }
        }
        else
        {
            $arr['updated_by'] = $this->userSess->account_id;
            $arr['admin_id'] = $sdata['admin_id'];
            $arr['category_id'] = $postdata['category_id'];
            $arr['new_parent_category_id'] = $postdata['parent_bcategory_id'];
            $arr['bcategory_slug'] = strip_tags($postdata['bcategory_slug']);
            $arr['bcategory_name'] = strip_tags(trim($postdata['bcategory_name']));
            $arr['category_img'] = $modfilename;

            $arr['meta_title'] = strip_tags($postdata['meta_title']);
            $arr['meta_desc'] = strip_tags($postdata['meta_desc']);
            $arr['meta_keywords'] = strip_tags($postdata['meta_keywords']);

            $result = $this->storeObj->saveOnlineCategory($arr);
          
            if ($result == 1)
            {
                $op['status'] = 'OK';
                $op['msg'] = trans('admin/online_category/category.updated');
            }
            else
            {
                $op['status'] = 'OK';
                $op['msg'] = trans('general.something_wrong');
            }
        }
        return $this->response->json($op, 200, $this->headers, $this->options);
    }
      /* Edit Online store Category */

    public function editOnlineCategory ()
    {
        $postdata = $this->request->all();
        $wdata['bcategory_id'] = $postdata['bcategory_id'];
        $data['category'] = $this->storeObj->editOnlineCategory($wdata);

        if (!empty($data))
        {
            $wdata['root_bcategory_id'] = $data['category']->root_bcategory_id;
            $wdata['parent_bcategory_id'] = $data['category']->parent_bcategory_id;
            $wdata['cat_lftnode'] = $data['category']->cat_lftnode;
            $wdata['cat_rgtnode'] = $data['category']->cat_rgtnode;
            $data['category_path'] = $this->storeObj->getOnlineCategorypath($wdata);
            $op['data'] = $data;
            $op['status'] = 200;
            $op['msg'] = trans('admin/online_category/category.cat_success');
        }
        else
        {
            $op['status'] = 422;
            $op['data'] = $data;
            $op['msg'] = trans('admin/online_category/category.cat_not_foound');
        }
        return $this->response->json($op, 200, $this->headers, $this->options);
    }
    /* Change Online store Category Status */
   public function change_onlineCategory_status ()
    {
        $postdata = $this->request->all();
        $op['status'] = 'ERR';
        $op['msg'] = trans('general.something_wrong');
        if ($this->request->has('category_id'))
        {
            $postdata['category_id'] = $this->request->get('category_id');
            if ($this->storeObj->change_onlineCategory_status($postdata))
            {
                $op['status'] = 'OK';
                $op['msg'] = trans('admin/online_category/category.status_updated');
            }
        }
        return $this->response->json($op, 200, $this->headers, $this->options);
    }
          /*Ending Online Categories */
}
