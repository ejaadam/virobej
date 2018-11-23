<?php
namespace App\Models\Admin;
use DB;
use File;
use TWMailer;
use App\Models\BaseModel;
use App\Models\LocationModel;
use Config;

class CategoryManagement extends BaseModel {
	
	/* Common For All Type of Categories */	
    public function get_categoryRoot ($rootId)
    {
        $root = DB::table(Config::get('tables.BUSINESS_CATEGORY_TREE').' as node')
                ->where('node.root_bcategory_id', $rootId)
                ->where('node.cat_lftnode', 1)
                ->select('node.bcategory_id')
                ->first();
        return $root;
    }
	
	/* Common For All Type of Categories */	
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
	/*  End Common Functions */
	
	public function getInStoreCategory_list (array $data = array(), $count = false)
    {
        extract($data);
        $categories = DB::table(Config::get('tables.BUSINESS_CATEGORY').' as bc')
                ->leftjoin(Config::get('tables.BUSINESS_CATEGORY_TREE').' as bct', 'bct.bcategory_id', '=', 'bc.bcategory_id')
                ->leftjoin(Config::get('tables.BUSINESS_CATEGORY_LANG').' as bcl', 'bcl.bcategory_id', '=', 'bc.bcategory_id')
                ->leftjoin(Config::get('tables.BUSINESS_CATEGORY_LANG').' as bcl2', 'bcl2.bcategory_id', '=', 'bct.parent_bcategory_id')
                ->where('bcl.lang_id', Config::get('app.locale_id'))
                ->where('bc.is_deleted', Config::get('constants.OFF'))
                ->where('bc.category_type', Config::get('constants.BCATEGORY_TYPE.IN_STORE'));

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
	
	public function getInStoreCategoriesMin (array $wdata = [])
    { 
        extract($wdata);
	    $parent_data=DB::table(Config::get('tables.BUSINESS_CATEGORY_TREE').' as bct')
			             ->where('bct.parent_bcategory_id','=',0)
						 ->where('bct.category_type', Config::get('constants.BCATEGORY_TYPE.IN_STORE'))
						 ->where('bct.cat_lftnode','=',1)
						 ->first(); 
						 
        $qry = DB::table(Config::get('tables.BUSINESS_CATEGORY').' as cat')
                ->join(Config::get('tables.BUSINESS_CATEGORY_LANG').' as catL', 'cat.bcategory_id', '=', 'catL.bcategory_id')
                ->join(Config::get('tables.BUSINESS_CATEGORY_TREE').' as catT', 'cat.bcategory_id', '=', 'catT.bcategory_id')
                ->where('catL.lang_id', Config::get('app.locale_id'))
                ->where('cat.category_type', Config::get('constants.BCATEGORY_TYPE.IN_STORE'))				
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
             // $qry->whereIN('catT.parent_bcategory_id', array(0,$parent_data->root_bcategory_id))     
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
	
    public function saveInStoreCategory (array $arr = array())
    {  
        DB::Transaction(function() use($arr)
        {
            extract($arr);
            if (isset($category_id) && !empty($category_id))
            {
                $old_bcat_parentID = DB::table(Config::get('tables.BUSINESS_CATEGORY_TREE'))
                        ->where('bcategory_id', $category_id)
                        ->select('parent_bcategory_id')
                        ->first();
					
                if (!empty($old_bcat_parentID->parent_bcategory_id))
                {
                    if (isset($new_parent_category_id))
                    {
                        if ($old_bcat_parentID->parent_bcategory_id != $new_parent_category_id)
                        {
                            $result = $this->shifting_InStoreCategoryTree(['new_parent_category_id'=>$new_parent_category_id, 'bcategory_id'=>$category_id]);
                            if ($result == 2)
                            {
                                $op['status'] = 'Error';
                                $op['msg'] = trans('admin/site-configuration/online_category/category.main_catNot_possible');
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
                                $categories = DB::table($this->config->get('tables.BUSINESS_CATEGORY').' as bc')
                                        ->leftjoin($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as bcl', 'bcl.bcategory_id', '=', 'bc.bcategory_id')
                                        ->where('bc.category_type', $this->config->get('constants.BCATEGORY_TYPE.IN_STORE'))
                                        ->where('bcl.lang_id', $this->config->get('app.locale_id'))
                                        ->where('bc.bcategory_id', $category_id)
                                        ->update($update);
                                return $categories;
                            }
                        }
                    }
                }
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
                        ->where('bc.category_type', Config::get('constants.BCATEGORY_TYPE.IN_STORE'))
                        ->where('bcl.lang_id', Config::get('app.locale_id'))
                        ->where('bc.bcategory_id', $category_id)
                        ->update($update);
                return $categories;
            }
            else
            {
                $bcategory['slug'] = $category['bcategory_slug'];
                $bcategory['created_on'] = getGTZ();
                $bcategory['category_img'] = $category['category_image'];
                $bcategory['category_type'] = Config::get('constants.BCATEGORY_TYPE.IN_STORE');
                $bcategory['created_by'] = $category['created_by'];
                $bcategory['status'] = Config::get('constants.CATEGORY_STATUS.ACTIVE');
                $bcategory['is_visible'] =  Config::get('constants.ON');
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
                    return $this->save_parent_InStoreCategory(['category_parent'=>$category_parent, 'admin_id'=>$category['admin_id']]);
                }
            }
		});
        return true;
    }
	
    public function save_parent_InStoreCategory (array $arr)
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
				                ->where('category_type',Config::get('constants.BCATEGORY_TYPE.IN_STORE'))
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
        $catinfo = $this->editInStoreCategory(array('bcategory_id'=>$category_parent_id));
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
	
    public function editInStoreCategory (array $arr = array())
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
                    ->where('pc.category_type', Config::get('constants.BCATEGORY_TYPE.IN_STORE'))
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
	
    public function shifting_InStoreCategoryTree (array $arr)
    {
        extract($arr);
        if (isset($new_parent_category_id))
        {
            $catinfo = $this->editInStoreCategory(array('bcategory_id'=>$bcategory_id));
            $new_parent_catinfo = $this->editInStoreCategory(array('bcategory_id'=>$new_parent_category_id));
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
	
	public function getInStoreCategorypath (array $wdata = [])
    {
        extract($wdata);
        $qry = DB::table(Config::get('tables.BUSINESS_CATEGORY').' as cat')
                ->join(Config::get('tables.BUSINESS_CATEGORY_LANG').' as catL', 'cat.bcategory_id', '=', 'catL.bcategory_id')
                ->join(Config::get('tables.BUSINESS_CATEGORY_TREE').' as catT', 'cat.bcategory_id', '=', 'catT.bcategory_id')
                ->where('catL.lang_id', Config::get('app.locale_id'))
                ->where('cat.category_type', Config::get('constants.BCATEGORY_TYPE.IN_STORE'))
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
    public function change_InStoreCategory_status (array $data = [])
	{
        extract($data);
        $update['status'] = $status;
        $query = DB::table(Config::get('tables.BUSINESS_CATEGORY'))
                ->where('category_type', Config::get('constants.BCATEGORY_TYPE.IN_STORE'))
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
	
	/* Manage Product Category */	
	
	public function getProductCategory_list (array $data = array(), $count = false)
    {
        extract($data);
        $categories = DB::table(Config::get('tables.BUSINESS_CATEGORY').' as bc')
                ->leftjoin(Config::get('tables.BUSINESS_CATEGORY_TREE').' as bct', 'bct.bcategory_id', '=', 'bc.bcategory_id')
                ->leftjoin(Config::get('tables.BUSINESS_CATEGORY_LANG').' as bcl', 'bcl.bcategory_id', '=', 'bc.bcategory_id')
                ->leftjoin(Config::get('tables.BUSINESS_CATEGORY_LANG').' as bcl2', 'bcl2.bcategory_id', '=', 'bct.parent_bcategory_id')
                ->where('bcl.lang_id', Config::get('app.locale_id'))
                ->where('bc.is_deleted', Config::get('constants.OFF'))
                ->where('bc.category_type', Config::get('constants.BCATEGORY_TYPE.PRODUCT'));

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
	
	public function getProductCategoriesMin (array $wdata = [])
    {   //print_r($wdata);exit;
        extract($wdata);
	    $parent_data=DB::table(Config::get('tables.BUSINESS_CATEGORY_TREE').' as bct')
			             ->where('bct.parent_bcategory_id','=',0)
						 ->where('bct.category_type', Config::get('constants.BCATEGORY_TYPE.PRODUCT'))
						 ->where('bct.cat_lftnode','=',1)
						 ->first(); 
						 
        $qry = DB::table(Config::get('tables.BUSINESS_CATEGORY').' as cat')
                ->join(Config::get('tables.BUSINESS_CATEGORY_LANG').' as catL', 'cat.bcategory_id', '=', 'catL.bcategory_id')
                ->join(Config::get('tables.BUSINESS_CATEGORY_TREE').' as catT', 'cat.bcategory_id', '=', 'catT.bcategory_id')
                ->where('catL.lang_id', Config::get('app.locale_id'))
                ->where('cat.category_type', Config::get('constants.BCATEGORY_TYPE.PRODUCT'))				
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
             // $qry->whereIN('catT.parent_bcategory_id', array(0,$parent_data->root_bcategory_id))     
            $qry->where('catT.parent_bcategory_id','=',0)    			 
                   ->orWhereNull('catT.parent_bcategory_id');
        } 
        $qry->selectRaw('catL.bcategory_name,cat.bcategory_id,catT.parent_bcategory_id,catT.root_bcategory_id,if(catT.cat_lftnode = catT.cat_rgtnode - 1,0,1) as haschild,catT.cat_lftnode,catT.cat_rgtnode');
        $qry->orderBy('catL.bcategory_name', 'ASC');
        $result = $qry->get();
	
        if (!empty($result))
        {
	        //print_r($result);exit;
            return $result;
        }
        return NULL;
    }
	
    public function saveProductCategory (array $arr = array())
    {  
        DB::Transaction(function() use($arr)
        {
            extract($arr);
            if (isset($category_id) && !empty($category_id))
            {
                $old_bcat_parentID = DB::table(Config::get('tables.BUSINESS_CATEGORY_TREE'))
                        ->where('bcategory_id', $category_id)
                        ->select('parent_bcategory_id')
                        ->first();
					
                if (!empty($old_bcat_parentID->parent_bcategory_id))
                {
                    if (isset($new_parent_category_id))
                    {
                        if ($old_bcat_parentID->parent_bcategory_id != $new_parent_category_id)
                        {
                            $result = $this->shifting_ProductCategoryTree(['new_parent_category_id'=>$new_parent_category_id, 'bcategory_id'=>$category_id]);
                            if ($result == 2)
                            {
                                $op['status'] = 'Error';
                                $op['msg'] = trans('admin/site-configuration/online_category/category.main_catNot_possible');
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
                                $categories = DB::table($this->config->get('tables.BUSINESS_CATEGORY').' as bc')
                                        ->leftjoin($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as bcl', 'bcl.bcategory_id', '=', 'bc.bcategory_id')
                                        ->where('bc.category_type', $this->config->get('constants.BCATEGORY_TYPE.PRODUCT'))
                                        ->where('bcl.lang_id', $this->config->get('app.locale_id'))
                                        ->where('bc.bcategory_id', $category_id)
                                        ->update($update);
                                return $categories;
                            }
                        }
                    }
                }
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
                        ->where('bc.category_type', Config::get('constants.BCATEGORY_TYPE.PRODUCT'))
                        ->where('bcl.lang_id', Config::get('app.locale_id'))
                        ->where('bc.bcategory_id', $category_id)
                        ->update($update);
                return $categories;
            }
            else
            {
                $bcategory['slug'] = $category['bcategory_slug'];
                $bcategory['created_on'] = getGTZ();
                $bcategory['category_img'] = $category['category_image'];
                $bcategory['category_type'] = Config::get('constants.BCATEGORY_TYPE.PRODUCT');
                $bcategory['created_by'] = $category['created_by'];
                $bcategory['status'] = Config::get('constants.CATEGORY_STATUS.ACTIVE');
                $bcategory['is_visible'] =  Config::get('constants.ON');
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
                    return $this->save_parent_ProductCategory(['category_parent'=>$category_parent, 'admin_id'=>$category['admin_id']]);
                }
            }
		});
        return true;
    }
	
    public function save_parent_ProductCategory (array $arr)
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

            $node_val = $this->adding_ProductCategoryNode(['category_parent_id'=>$category_parent['parent_bcategory_id']]);
		
            if (!empty($node_val))
            {
                $category_parent['root_bcategory_id'] = $node_val['root_id'];
                $category_parent['cat_lftnode'] = $node_val['cat_lftnode'];
                $category_parent['cat_rgtnode'] = $node_val['cat_rgtnode'];
            }
            else
            {	
				$parent_check=DB::table(Config::get('tables.BUSINESS_CATEGORY_TREE'))
				                ->where('category_type',Config::get('constants.BCATEGORY_TYPE.PRODUCT'))
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
	
    public function adding_ProductCategoryNode (array $arr)
    {  
        extract($arr);
	
        $increament_val = 2;
        $catinfo = $this->editProductCategory(array('bcategory_id'=>$category_parent_id));
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
	
    public function editProductCategory (array $arr = array())
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
                    ->where('pc.category_type', Config::get('constants.BCATEGORY_TYPE.PRODUCT'))
                    ->selectRaw('pc.*,bcl.*,pcp.parent_bcategory_id,pcp.root_bcategory_id,pcp.cat_lftnode,pcp.cat_rgtnode,IF(pcp.cat_lftnode=pcp.cat_rgtnode-1,0,1) as childCounts,(select parent_bcategory_id from '.Config::get('tables.BUSINESS_CATEGORY_TREE').' where bcategory_id=pcp.parent_bcategory_id) as gparent_bcategory_id')
				->first();
				
            if (!empty($result))
            {		
		       /*  $data['root_bcategory_id'] = $result->root_bcategory_id;
				$data['cat_lftnode'] = $result->cat_lftnode;
				$data['cat_rgtnode'] = $result->cat_rgtnode;
				$result->parents = $this->commonObj->getCategoryParent($data); */

				//unset($result->root_bcategory_id, $result->cat_lftnode, $result->cat_rgtnode);
			
                $result->category_img = !empty($result->category_img) ? Config::get('constants.BCATEGORY_IMG_PATH.LOCAL').$result->category_img : NULL;
                return $result;
            }
        }
        return false;
    }
	
    public function shifting_ProductCategoryTree (array $arr)
    {
        extract($arr);
        if (isset($new_parent_category_id))
        {
            $catinfo = $this->editProductCategory(array('bcategory_id'=>$bcategory_id));
            $new_parent_catinfo = $this->editProductCategory(array('bcategory_id'=>$new_parent_category_id));
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
	
	public function getProductCategorypath (array $wdata = [])
    {
        extract($wdata);
        $qry = DB::table(Config::get('tables.BUSINESS_CATEGORY').' as cat')
                ->join(Config::get('tables.BUSINESS_CATEGORY_LANG').' as catL', 'cat.bcategory_id', '=', 'catL.bcategory_id')
                ->join(Config::get('tables.BUSINESS_CATEGORY_TREE').' as catT', 'cat.bcategory_id', '=', 'catT.bcategory_id')
                ->where('catL.lang_id', Config::get('app.locale_id'))
                ->where('cat.category_type', Config::get('constants.BCATEGORY_TYPE.PRODUCT'))
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
    public function change_ProductCategory_status (array $data = [])
	{
        extract($data);
        $update['status'] = $status;
        $query = DB::table(Config::get('tables.BUSINESS_CATEGORY'))
                ->where('category_type', Config::get('constants.BCATEGORY_TYPE.PRODUCT'))
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