<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\BaseController;
use App\Models\Admin\CategoryManagement;
use App\Models\Commonsettings;
use App\Models\LocationModel;
use TWMailer;
use Response; 
use Request;
use View;
use URL;
use File;

class CategoryManagementController  extends BaseController
{
    
	public function __construct ()
    {
        parent::__construct();
      
        $this->categoryObj = new CategoryManagement();
		$this->lcObj = new LocationModel();		
		$this->commonObj = new Commonsettings();
    }   
	 
	public function getInStoreCategory_list ()
	{	
		$data = $filter = array();
        $postdata = $this->request->all();
     if (!empty($postdata)){
			$filter['search_text'] = (isset($postdata['search_term']) && !empty($postdata['search_term'])) ? trim($postdata['search_term']) : '';
			$filter['parent_category_id'] = (isset($postdata['parent_category_id']) && !empty($postdata['parent_category_id'])) ? $postdata['parent_category_id'] : '';
			$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';
		
	  }
        if ($this->request->isMethod('post')){
			
            $ajaxdata = [];
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->categoryObj->getInStoreCategory_list([], true);
            $ajaxdata['data'] = array();
            $ajaxdata['draw'] = $postdata['draw'];

            if ($ajaxdata['recordsTotal'])
            {
                $filter = array_filter($filter);
                if (!empty($filter)){
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->categoryObj->getInStoreCategory_list($data, true);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : 10;
                    $ajaxdata['data'] = $this->categoryObj->getInStoreCategory_list($data);
                }
            }
            return $this->response->json($ajaxdata, 200, $this->headers, $this->options);
         }
    elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
        {
			$edata['manage_category_details'] = $this->categoryObj->getInStoreCategory_list(array_merge($data,$filter));	
            $output = view('admin.in-store_category.manage_category_export',$edata);
                    
            $headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=in_store_category_details' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
            return Response::make($output, 200, $headers);
        }
       else{
            return view('admin.in-store_category.category_list');
         }
	}
	public function getInStoreCategories ()
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
		
        $data = $this->categoryObj->getInStoreCategoriesMin($wdata);
        return $this->response->json($data, 200, $this->headers, $this->options);
    }
	
	/* Common For All Categories */
    public function check_InStoreCategory_slug ()
    {
        $op = $postdata = array();
        $postdata = $this->request->all();
        if (!empty($postdata['bcategory_slug']))
        {
            $bcategory['bcategory_id'] = (isset($postdata['bcategory_id']) && !empty($postdata['bcategory_id'])) ? $postdata['bcategory_id'] : '';
            $bcategory['bcategory_slug'] = strip_tags($postdata['bcategory_slug']);
            $bcategory['category_type'] = $this->config->get('constants.BCATEGORY_TYPE.IN_STORE');
            $result = $this->categoryObj->checkCategorySlug($bcategory);
            if (!empty($result))
            {
                $op['status'] = 200;
            }
            else
            {
                $op['status'] = 422;
                $op['msg'] = trans('admin/in_store_category.url_exist');
            }
        }
        else
        {
            $op = array(
                'status'=>422,
                'msg'=>trans('admin/in_store_category.enter_url'));
        }
        return $this->response->json($op, 200, $this->headers, $this->options);
    }
	
    public function saveInStoreCategory ()
    {
        $op = array();
        $postdata = $this->request->all();	
        $sdata['admin_id'] = $this->userSess->account_id;
        $sdata['created_by'] = $this->userSess->account_id;
        $op['status'] = 'ERR';
        $op['msg'] = trans('general.something_wrong');
        $modfilename = NULL;
        if (!empty($postdata['image_url']))
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
            $result = $this->categoryObj->saveInStoreCategory($category);
            if (!empty($result))
            {
                $op['status'] = 'OK';
                $op['msg'] = trans('admin/in_store_category.added');
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

            $result = $this->categoryObj->saveInStoreCategory($arr);
            if ($result == 1)
            {
                $op['status'] = 'OK';
                $op['msg'] = trans('admin/in_store_category.updated');
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

    public function editInStoreCategory ()
    {
        $postdata = $this->request->all();
        $wdata['bcategory_id'] = $postdata['bcategory_id'];
        $data['category'] = $this->categoryObj->editInStoreCategory($wdata);

        if (!empty($data))
        {
            $wdata['root_bcategory_id'] = $data['category']->root_bcategory_id;
            $wdata['parent_bcategory_id'] = $data['category']->parent_bcategory_id;
            $wdata['cat_lftnode'] = $data['category']->cat_lftnode;
            $wdata['cat_rgtnode'] = $data['category']->cat_rgtnode;
            $data['category_path'] = $this->categoryObj->getInStoreCategorypath($wdata);
            $op['data'] = $data;
            $op['status'] = 200;
            $op['msg'] = trans('admin/in_store_category.cat_success');
        }
        else
        {
            $op['status'] = 422;
            $op['data'] = $data;
            $op['msg'] = trans('admin/in_store_category.cat_not_foound');
        }
        return $this->response->json($op, 200, $this->headers, $this->options);
    }
    /* Change Online store Category Status */
   public function change_InStoreCategory_status ()
    {
        $postdata = $this->request->all();
        $op['status'] = 'ERR';
        $op['msg'] = trans('general.something_wrong');
        if ($this->request->has('category_id'))
        {
            $postdata['category_id'] = $this->request->get('category_id');
            if ($this->categoryObj->change_InStoreCategory_status($postdata))
            {
                $op['status'] = 'OK';
                $op['msg'] = trans('admin/in_store_category.status_updated');
            }
        }
        return $this->response->json($op, 200, $this->headers, $this->options);
    }
	
	/* Manage Products Category */	
	
	public function getProductCategory_list ()
	{	
		$data = $filter = array();
        $postdata = $this->request->all();
        if (!empty($postdata))
		{
			$filter['search_text'] = (isset($postdata['search_term']) && !empty($postdata['search_term'])) ? trim($postdata['search_term']) : '';
			$filter['parent_category_id'] = (isset($postdata['parent_category_id']) && !empty($postdata['parent_category_id'])) ? $postdata['parent_category_id'] : '';
			$filter['exportbtn'] = $this->request->has('exportbtn')? $this->request->get('exportbtn') : '';		
	    }
        if ($this->request->isMethod('post'))
		{
            $ajaxdata = [];
            $ajaxdata['recordsTotal'] = $ajaxdata['recordsFiltered'] = $this->categoryObj->getProductCategory_list([], true);
            $ajaxdata['data'] = array();
            $ajaxdata['draw'] = $postdata['draw'];

            if ($ajaxdata['recordsTotal'])
            {
                $filter = array_filter($filter);
                if (!empty($filter)){
                    $data = array_merge($data, $filter);
                    $ajaxdata['recordsFiltered'] = $this->categoryObj->getProductCategory_list($data, true);
                }
                if (!empty($ajaxdata['recordsFiltered']))
                {
                    $data['start'] = (isset($postdata['start']) && !empty($postdata['start'])) ? $postdata['start'] : 0;
                    $data['length'] = (isset($postdata['length']) && !empty($postdata['length'])) ? $postdata['length'] : 10;
                    $ajaxdata['data'] = $this->categoryObj->getProductCategory_list($data);
                }
            }
            return $this->response->json($ajaxdata, 200, $this->headers, $this->options);
        }
        elseif(isset($filter['exportbtn']) && $filter['exportbtn']=='Export')
        {
			$edata['manage_category_details'] = $this->categoryObj->getProductCategory_list(array_merge($data,$filter));	
            $output = view('admin.products.product_category_export',$edata);
                    
            $headers = array(
				'Pragma' => 'public',
				'Expires' => 'public',
				'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
				'Cache-Control' => 'private',
				'Content-Type' => 'application/vnd.ms-excel',
				'Content-Disposition' => 'attachment; filename=product_category_details' . date("d-M-Y") . '.xls',
				'Content-Transfer-Encoding' => ' binary'
				);
            return Response::make($output, 200, $headers);
        }
        else{
            return view('admin.products.category_list');
        }
	}
	
	public function getProductCategories ()
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
        $data = $this->categoryObj->getProductCategoriesMin($wdata);
        return $this->response->json($data, 200, $this->headers, $this->options);
    }
	
    public function check_ProductCategory_slug ()
    {
        $op = $postdata = array();
        $postdata = $this->request->all();
        if (!empty($postdata['bcategory_slug']))
        {
            $bcategory['bcategory_id'] = (isset($postdata['bcategory_id']) && !empty($postdata['bcategory_id'])) ? $postdata['bcategory_id'] : '';
            $bcategory['bcategory_slug'] = strip_tags($postdata['bcategory_slug']);
            $bcategory['category_type'] = $this->config->get('constants.BCATEGORY_TYPE.PRODUCT');
            $result = $this->categoryObj->checkCategorySlug($bcategory);
            if (!empty($result))
            {
                $op['status'] = 200;
            }
            else
            {
                $op['status'] = 422;
                $op['msg'] = trans('admin/in_store_category.url_exist');
            }
        }
        else
        {
            $op = array(
                'status'=>422,
                'msg'=>trans('admin/in_store_category.enter_url'));
        }
        return $this->response->json($op, 200, $this->headers, $this->options);
    }
	
    public function saveProductCategory ()
    {  
        $op = array();
        $postdata = $this->request->all();		
        $sdata['admin_id'] = $this->userSess->account_id;
        $sdata['created_by'] = $this->userSess->account_id;
        $op['status'] = 'ERR';
        $op['msg'] = trans('general.something_wrong');
        $modfilename = NULL;
        if (!empty($postdata['image_url']))
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
            $result = $this->categoryObj->saveProductCategory($category);
            if (!empty($result))
            {
                $op['status'] = 'OK';
                $op['msg'] = trans('admin/in_store_category.added');
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

            $result = $this->categoryObj->saveProductCategory($arr);
            if ($result == 1)
            {
                $op['status'] = 'OK';
                $op['msg'] = trans('admin/in_store_category.updated');
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
    public function editProductCategory ()
    {
        $postdata = $this->request->all();		
        $wdata['bcategory_id'] = $postdata['bcategory_id'];
        $data['category'] = $this->categoryObj->editProductCategory($wdata);

        if (!empty($data))
        {
            $wdata['root_bcategory_id'] = $data['category']->root_bcategory_id;
            //$wdata['parent_bcategory_id'] = $data['category']->parent_bcategory_id;
            $wdata['cat_lftnode'] = $data['category']->cat_lftnode;
            $wdata['cat_rgtnode'] = $data['category']->cat_rgtnode;
            $data['category_path'] = $this->categoryObj->getProductCategorypath($wdata);
            $op['data'] = $data;
            $op['status'] = 200;
            $op['msg'] = trans('admin/in_store_category.cat_success');
        }
        else
        {
            $op['status'] = 422;
            $op['data'] = $data;
            $op['msg'] = trans('admin/in_store_category.cat_not_foound');
        }
        return $this->response->json($op, 200, $this->headers, $this->options);
    }
	
    /* Change Product store Category Status */
    public function change_ProductCategory_status ()
    {
        $postdata = $this->request->all();
        $op['status'] = 'ERR';
        $op['msg'] = trans('general.something_wrong');
        if ($this->request->has('category_id'))
        {
            $postdata['category_id'] = $this->request->get('category_id');
            if ($this->categoryObj->change_ProductCategory_status($postdata))
            {
                $op['status'] = 'OK';
                $op['msg'] = trans('admin/in_store_category.status_updated');
            }
        }
        return $this->response->json($op, 200, $this->headers, $this->options);
    }
}
