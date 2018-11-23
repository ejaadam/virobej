<?php
namespace App\Models\Api\User;
use DB;
use App\Models\BaseModel;
use App\Models\Api\CommonModel;
use CommonLib;

class StoreModel extends BaseModel {
	
    public function __construct() 
	{
        parent::__construct();		
		$this->commonObj = new CommonModel();
    }		 
	
	public function onlinestoreCategories (array $params = array(), $parents_only = false, $load_siblings = false)
    {	
        extract($params);
        if (isset($category_slug) && !empty($category_slug) && ($category_slug != ''))
        {
			
            $cats = DB::table($this->config->get('tables.BUSINESS_CATEGORY').' as sc')
					->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as sct', 'sct.bcategory_id', '=', 'sc.bcategory_id')					
					->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as bct', function($bct)
                    {
                        $bct->on('bct.parent_bcategory_id', '=', 'sc.bcategory_id');
                    })    
					->join($this->config->get('tables.STORES').' as msm', function($join){
						$join->on(DB::Raw('FIND_IN_SET(msm.category_id,bct.bcategory_id)'), DB::Raw(''), DB::Raw(''));
					})  
					//->join($this->config->get('tables.STORES').' as msm', 'msm.category_id', '=', 'bct.category_id')                    
					->where('sc.slug', 'LIKE', '%'.$category_slug.'%') 
                    ->where('sc.category_type', '=', $this->config->get('constants.CATEGORY_TYPE.ONLINE_STORE')) ; 
        }
        else
        {
            $cats = DB::table($this->config->get('tables.STORES').' as msm')
                    ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as pc', 'pc.bcategory_id', '=', 'msm.category_id')
                    ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as bct', 'bct.parent_bcategory_id', '=', 'pc.root_bcategory_id')
                    ->where('pc.category_type', '=', $this->config->get('constants.CATEGORY_TYPE.ONLINE_STORE')); 
        }
		$cats->where('msm.is_online', '=', $this->config->get('constants.ON'))
                ->join($this->config->get('tables.BUSINESS_CATEGORY').' as bc', 'bc.bcategory_id', '=', 'bct.bcategory_id')
                ->join($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as bcl', 'bcl.bcategory_id', '=', 'bc.bcategory_id')
                ->selectRaw('bc.slug as category_slug, bcl.bcategory_name,bc.icon,bct.cat_lftnode,bct.cat_rgtnode,bc.bcategory_id');

       /*  if (isset($district_id) && !empty($district_id))
        {
            $cats->join($this->config->get('tables.ADDRESS_MST').' as am', function($join) use($district_id)
            {
                $join->on('am.address_id', '=', 'msm.address_id')
                        ->where('am.district_id', '=', $district_id)
                        ->where('am.post_type', '=', $this->config->get('constants.POST_TYPE.STORE'));
            });
        } */
        $catresult = $cats->distinct('bc.bcategory_id')->get();		
        if (empty($catresult) && $load_siblings)
        {
            $cats = DB::table($this->config->get('tables.STORES').' as msm')
                    ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as pc', function($join)
                    {
                        $join->on('pc.bcategory_id', '=', 'msm.category_id')
                        ->where('pc.category_type', '=', 1);
                    })
                    ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as bct', 'bct.parent_bcategory_id', '=', 'pc.parent_bcategory_id')
                    ->join($this->config->get('tables.BUSINESS_CATEGORY').' as bc', 'bc.bcategory_id', '=', 'bct.bcategory_id')
                    ->join($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as bcl', 'bcl.bcategory_id', '=', 'bc.bcategory_id')
                    ->selectRaw('bc.slug as category_slug, bcl.bcategory_name,bc.icon,bct.cat_lftnode,bct.cat_rgtnode')
                    ->where('msm.is_online', '=', $this->config->get('constants.OFF'));           
			
            $catresult = $cats->distinct('bc.bcategory_id')->get();
        }

        array_walk($catresult, function(&$category)
        {
           /*  if (($category->cat_lftnode + 1) == $category->cat_rgtnode)
            {
                $category->has_sub = 0;
            }
            else
            {
                $category->has_sub = 1;
            }
            $category->url = route('user.online-partners', ['category'=>$category->category_slug]); */
            //$category->icon = asset($this->config->get('path.BCATEGORY_ICONS_PATH.API').$category->icon);
			$category->icon = asset('resources/uploads/categories/icons/'.$category->icon);
            unset($category->cat_lftnode, $category->cat_rgtnode);
        });
        return $catresult;
    }
	
	public function storeCategories (array $params = array(), $parents_only = false, $load_siblings = false)
    {			
        extract($params);
        $cats = DB::table($this->config->get('tables.BUSINESS_CATEGORY').' as sc')
                ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as sct', 'sct.bcategory_id', '=', 'sc.bcategory_id')
                ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as bct', function($bct)
                {
                    $bct->on('bct.cat_lftnode', '>', 'sct.cat_lftnode')
						->on('bct.cat_rgtnode', '<', 'sct.cat_rgtnode')
						->on('bct.root_bcategory_id', '=', 'sct.root_bcategory_id');                    
                })   
                ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as cct', function($cct)
                {
                    $cct->on('cct.parent_bcategory_id', '=', 'sc.bcategory_id');
                })       
				->join($this->config->get('tables.STORES').' as msm', 'msm.category_id', '=', 'bct.bcategory_id')
                ->orderby('sct.bcategory_id', 'ASC')	 			
                ->where('sc.category_type', $this->config->get('constants.CATEGORY_TYPE.IN_STORE'));		
		      
		
        $cats->where('msm.status', $this->config->get('constants.ON'))                
                ->where('msm.is_approved', $this->config->get('constants.ON')); 
        $cats->join($this->config->get('tables.BUSINESS_CATEGORY').' as bc', 'bc.bcategory_id', '=', 'bct.bcategory_id')
                ->join($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as bcl', 'bcl.bcategory_id', '=', 'bc.bcategory_id')
                ->selectRaw('bc.slug as category_slug, bcl.bcategory_name, bc.icon, bct.cat_lftnode, bct.cat_rgtnode, bc.bcategory_id, bct.parent_bcategory_id')				
                ->orderby('bct.cat_lftnode', 'DESC'); 
				
		if (isset($category_slug) && !empty($category_slug))
        {				
			//$cats->where('sc.slug', 'LIKE', '%'.$category_slug.'%');            
			$cats->where('bc.slug', $category_slug);            
        }       
		
        $catresult = $catresults = $cats->where('bc.status', $this->config->get('constants.ON'))->distinct('bc.bcategory_id')->get();
        
        $unset_items = [];
        array_walk($catresult, function(&$category, $key) use ($catresults, &$unset_items)
        {            
            //$category->icon = asset($this->config->get('path.BCATEGORY_ICONS_PATH.API').$category->icon);
			$category->icon = asset('resources/uploads/categories/icons/'.$category->icon);
           /*  if (($category->cat_lftnode + 1) == $category->cat_rgtnode)
            {
                $category->has_sub = 0;
            }
            else
            {
                $category->has_sub = 1;
            }
            array_walk($catresults, function(&$subcat) use (&$category, $key, &$unset_items)
            {
                if (!isset($category->child))
                {
                    $category->child = [];
                }
                if ($category->bcategory_id == $subcat->parent_bcategory_id)
                {
                    $category->child[] = $subcat;
                    $unset_items[] = $subcat->bcategory_id;
                }
            }); */
        });
        array_walk($catresult, function(&$cat)
        {
            unset($cat->cat_lftnode, $cat->cat_rgtnode);
        });
        $catresult = array_filter($catresult, function($category) use ($catresults, $unset_items)
        {
            return in_array($category->bcategory_id, $unset_items) ? false : true;
        });
        array_walk($catresult, function(&$category)
        {
            unset($category->bcategory_id);
            unset($category->parent_bcategory_id);
        });
        return array_values($catresult);
    }
	
	public function popluarStoreCategories (array $params = array())
    {	
        extract($params);
        $categories = DB::table($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as bct')
                ->where('bct.category_type', '=', $this->config->get('constants.CATEGORY_TYPE.IN_STORE'))
                ->leftJoin($this->config->get('tables.STORES').' as msm', function($msm)
                {
                    $msm->on('msm.category_id', '=', 'bct.bcategory_id')
                    ->where('msm.is_online', '=', $this->config->get('constants.OFF'))
                    ->where('msm.is_approved', '=', $this->config->get('constants.ON'))
                    ->where('msm.status', '=', $this->config->get('constants.ACTIVE'))                    
                    ->where('msm.is_deleted', '=', $this->config->get('constants.OFF'));
                });
            /*  ->leftJoin($this->config->get('tables.MERCHANT_SETTINGS').' as ms', 'ms.mrid', '=', 'msm.mrid')
                ->leftJoin($this->config->get('tables.ADDRESS_MST').' as am', function($am) use($country_id)
				{
					$am->on('am.relative_post_id', '=', 'msm.store_id')
					->where('am.post_type', '=', $this->config->get('constants.POST_TYPE.STORE'))
					->where('am.country_id', '=', $country_id);
				}); */
				
				
        $categories->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as pct', function($bc)
                {
                    $bc->on('pct.cat_lftnode', '<', 'bct.cat_lftnode')
                    ->on('pct.cat_rgtnode', '>', 'bct.cat_rgtnode')
                    ->on('bct.root_bcategory_id', '=', 'pct.parent_bcategory_id');
                })
                ->join($this->config->get('tables.BUSINESS_CATEGORY').' as pc', function($pc)
                {
                    $pc->on('pc.bcategory_id', '=', 'pct.bcategory_id')
						->where('pc.is_deleted', '=', $this->config->get('constants.OFF'))
						->where('pc.status', '=', $this->config->get('constants.ACTIVE'))
						->where('pc.is_visible', '=', $this->config->get('constants.ON'));
                })
                ->join($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as pcl', function($join)
                {
                    $join->on('pcl.bcategory_id', '=', 'pc.bcategory_id')
                    ->where('pcl.lang_id', '=', config('app.locale_id'));
                });				
				//return $categories = $categories->get();
        if (isset($length))
        {
            $categories->skip($start)->take($length);
        }
        $catresult = $categories->groupby('pc.bcategory_id')
                ->selectRaw('pc.slug as category_slug, pcl.bcategory_name, pc.icon')
				->orderby('pc.position', 'ASC')
                ->get();
        array_walk($catresult, function(&$category)
        {            
            $category->icon = asset($this->config->get('path.BCATEGORY_ICONS_PATH.API').$category->icon);
			//$category->icon = asset('resources/uploads/categories/icons/'.$category->icon);
            
        });
        return $catresult;
    }
	
	public function list_all_stores_unionall (array $arr = array(), $next_page = false, $featured = false, $with_category = false)
    {
        extract($arr);
        $query = null;
        $i = 1;		
        array_walk($categories, function(&$category) use(&$i, &$query, $arr, $next_page, $featured)
        {
            $arr['category'] = $category->category_slug;
            $arr['start'] = 0;
            $arr['length'] = 10;
            if ($i == 1)
            {
                $query = $this->list_all_store($arr, $next_page, $featured, true, true);
                $i++;
            }
            else
            {
                $query->unionall($this->list_all_store($arr, $next_page, $featured, true, true));
            }
            $category->next_page = $this->list_all_store($arr, true);
            $category->stores = [];
        });
        $stores = $query->get();
		
        array_walk($stores, function(&$store) use(&$categories, &$stores, $with_category)
        {
            if (empty($store->store_name))
            {
                $store->store_name = $store->business_name;
            }
            else
            {
                $store->store_name = ucwords($store->store_name);
            }
            //$store->logo = asset(!empty($store->logo) ? $this->config->get('path.STORE_LOGO_PATH.API').$store->logo : $this->config->get('path.MERCHANT.LOGO_PATH.API').$store->logo);
			
			if (!empty($store->logo))
			{
				$store->logo = asset($this->config->get('path.SELLER.STORE_IMG_PATH.WEB').$store->logo);
			} else {
				$store->logo = asset($this->config->get('path.SELLER.PROFILE_IMG_PATH.WEB').$store->mrlogo);
			}
				
            $store->offer = !empty($store->offer > 0) ? trans('user/account.offer_upto', ['offerval'=>$store->offer]) : '';
            
            
            unset($store->business_name);
            unset($store->category_id);
            unset($store->supplier_id);
            unset($store->store_id);
            unset($store->store_slug);
            unset($store->mrlogo);
            if ($with_category)
            {
                array_walk($categories, function(&$category) use($store)
                {
                    if ($category->category_slug == $store->category_slug)
                    {
                        $category->stores[] = $store;
                    }
                });
            }
        });
        return $with_category ? $categories : $stores;
    }
	
	public function list_all_store (array $arr = array(), $next_page = false, $featured = false, $with_category_slug = false, $has_query = false)
    {	//return $arr;		
        extract($arr);		
        $qry = DB::table($this->config->get('tables.STORES').' as msm')
                ->join($this->config->get('tables.SUPPLIER_MST').' as mm', function($mm)
                {
                    $mm->on('mm.supplier_id', '=', 'msm.supplier_id')
                    ->where('mm.is_closed', '=', $this->config->get('constants.OFF'))
                    ->where('mm.is_deleted', '=', $this->config->get('constants.OFF'))
                    ->where('mm.block', '=', $this->config->get('constants.OFF'))
                    ->where('mm.is_verified', '=', $this->config->get('constants.ON'))
                    ->where('mm.status', '=', $this->config->get('constants.ON'));
                })            
                ->join($this->config->get('tables.ADDRESS_MST').' as am', function($join)
				{
					$join->on('am.relative_post_id', '=', 'msm.store_id')
					->where('am.post_type', '=', $this->config->get('constants.ADDRESS_POST_TYPE.STORE'));					
				}); 				
		if (!empty($boundries)) {
			$qry->whereBetween('am.geolat', [$boundries->minlat, $boundries->maxlat]);
			$qry->whereBetween('am.geolng', [$boundries->minlng, $boundries->maxlng]);
		}		
		$qry->where('msm.status', $this->config->get('constants.ON'))                
                ->where('msm.is_approved', $this->config->get('constants.ON'))
                ->where('msm.is_online', $this->config->get('constants.OFF'))    
                ->orderBy('msm.store_name', 'ASC'); 
		
        $qry->leftjoin($this->config->get('tables.CASHBACK_OFFERS').' as co', function($j)
                {
                    $j->on('co.supplier_id', '=', 'msm.supplier_id')
                    ->where('co.cboffer_type', '=', $this->config->get('constants.CBOFFER_TYPE.DISCOUNT'))
                    ->whereNull('co.store_id')
                    ->where('co.status', '=', $this->config->get('constants.ON'))
                    ->where('co.is_deleted', '=', $this->config->get('constants.OFF'));
                    //->where('co.is_approved', '=', $this->config->get('constants.ON'));
                }); 	
		
        if (isset($locality_id) && !empty($locality_id))
        {
            //$qry->where('am.city_id', '=', $locality_id);
        }
        if (isset($search_term) && !empty($search_term))
        {
            $search_term = str_replace(' ', '|', $search_term);
            $qry->where(function($s) use($search_term)
            {
                $s->where('msm.store_name', 'REGEXP', $search_term);
                //$s->orwhere('mm.company_name', 'REGEXP', $search_term);
            });
        }
        if ($featured)
        {
            $qry->where('msm.is_featured', $this->config->get('constants.ON'));
        }
        if (!empty($category))
        {
            if ($category == 'featured')
            {

                $qry->where('msm.is_featured', $this->config->get('constants.ON'));
            }
			elseif ($category == 'vi-stores')
            {

                $qry->where('msm.is_premium', $this->config->get('constants.ON'));
            }
            else
            {				
                $parent = DB::table($this->config->get('tables.BUSINESS_CATEGORY').' as sc')
                        ->where('sc.slug', '=', $arr['category'])
                        ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as sct', 'sct.bcategory_id', '=', 'sc.bcategory_id')
                        ->selectRaw('sct.bcategory_id,sct.root_bcategory_id,sct.cat_lftnode,sct.cat_rgtnode');
                $qry->join($this->config->get('tables.BUSINESS_CATEGORY').' as bcc', function($bcc)
                        {
                            $bcc->on('bcc.bcategory_id', '=', 'msm.category_id')
                            ->where('bcc.status', '=', $this->config->get('constants.ACTIVE'))
                            ->where('bcc.is_deleted', '=', $this->config->get('constants.OFF'));
                        })
                        ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as c', 'c.bcategory_id', '=', 'msm.category_id')
                        ->join(DB::raw('('.$parent->tosql().') as p'), function($p)
                        {
                            $p->on('c.root_bcategory_id', '=', 'p.root_bcategory_id')
                            ->on('c.cat_lftnode', '>=', 'p.cat_lftnode')
                            ->on('c.cat_rgtnode', '<=', 'p.cat_rgtnode');
                        })
                        ->addBinding($parent->getBindings(), 'join'); 
            }
        }

        if (!empty($country_id))
        {
            $qry->where('am.country_id', $country_id);
        }
        if (!empty($category_id))
        {
            $qry->where('msm.category_id', $category_id);
        }
        if (isset($fopt['categories']) && !empty($fopt['categories']))
        {
            $qry->where('msm.category_id', $fopt['categories']);
        }
        if (isset($fopt['discount']) && !empty($fopt['discount']))
        {
            $s = ['upto_10'=>10, 'upto_25'=>25, 'upto_30'=>30, 'upto_50'=>50];
            $fopt->where('co.cashback_value', '>=', $s[$fopt['discount']]);
        }
        if (!empty($store))
        {
            $qry->where(function($s) use($store)
            {
                $s->where('msm.store_name', 'LIKE', '%'.$store.'%');
                $s->orwhere('mm.company_name', 'LIKE', '%'.$store.'%');
            });
        }
		
        $qry->select('mm.supplier_id', 'msm.category_id', 'msm.store_name', 'msm.store_slug', 'msm.store_code', 'mm.company_name as business_name', 'msm.store_code', 'msm.store_logo as logo', 'mm.logo as mrlogo', 'msm.store_id', 'co.new_cashback as offer');
		//return $qry = $qry->get();
        if ($with_category_slug)
        {
            $qry->join($this->config->get('tables.BUSINESS_CATEGORY').' as bc', function($bc)
                    {
                        $bc->on('bc.bcategory_id', '=', 'msm.category_id')
                        ->where('bc.is_deleted', '=', $this->config->get('constants.OFF'))
                        ->where('bc.status', '=', $this->config->get('constants.ACTIVE'))
                        ->where('bc.is_visible', '=', $this->config->get('constants.ON'));
                    })
                    ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as bct', 'bct.bcategory_id', '=', 'bc.bcategory_id')
                /*     ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as pct', function($bc)
                    {
                        $bc->on('pct.cat_lftnode', '<', 'bct.cat_lftnode')
                        ->on('pct.cat_rgtnode', '>', 'bct.cat_rgtnode')
                        ->on('bct.root_bcategory_id', '=', 'pct.parent_bcategory_id');
                    }); */
                    ->join($this->config->get('tables.BUSINESS_CATEGORY').' as pc', function($pc)
                    {
                        $pc->on('pc.bcategory_id', '=', 'bct.bcategory_id')
                        ->where('pc.is_deleted', '=', $this->config->get('constants.OFF'))
                        ->where('pc.status', '=', $this->config->get('constants.ACTIVE'))
                        ->where('pc.is_visible', '=', $this->config->get('constants.ON'));
                    })
                    ->addSelect('pc.slug as category_slug'); 
        }
        if ($has_query)
        {
            if (isset($length) && isset($start))
            {
                $qry->skip($start)->take($length);
            }
            return $qry;
        }
        else if ($next_page)
        {
            return ($start + $length) < $qry->count() ? $page + 1 : false;
        }
        else
        {
            if (isset($length) && isset($start))
            {
                $qry->skip($start)->take($length);
            }
            $res = $qry->get();
            $stores = [];
            array_walk($res, function(&$result) use(&$stores)
            {
                if (empty($result->store_name))
                {
                    $result->store_name = $result->business_name;
                }
                else
                {
                    $result->store_name = ucwords($result->store_name);
                }				
				if (!empty($result->logo))
				{
					$result->logo = asset($this->config->get('path.SELLER.STORE_IMG_PATH.WEB').$result->logo);
				} else {
					$result->logo = asset($this->config->get('path.SELLER.PROFILE_IMG_PATH.WEB').$result->mrlogo);
				}               
                $result->offer = !empty($result->offer > 0) ? trans('user/account.offer_upto', ['offerval'=>$result->offer]) : '';
				unset($result->business_name);
                unset($result->category_id);
                unset($result->supplier_id);
                unset($result->store_slug);
                unset($result->store_id);                
                unset($result->mrlogo);                
            });
            return $res;
        }
    }
	
	public function getStoreFilters (array $arr = array())
    {	
        extract($arr);
        $properties = [];
        if (isset($category_slug) && !empty($category_slug))
        {
			
            $cats = DB::table($this->config->get('tables.BUSINESS_CATEGORY').' as sc')
                    ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as sct', 'sct.bcategory_id', '=', 'sc.bcategory_id')
					->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as bct', function($bct)
                    {
                        $bct->on('bct.parent_bcategory_id', '=', 'sct.bcategory_id');							
                    })
					->where('sc.slug', 'like', $category_slug)
					->where('sc.category_type', 1)
                    ->join($this->config->get('tables.STORES').' as msm', 'msm.category_id', '=', 'sct.bcategory_id');
        }
        else
        {
            $cats = DB::table($this->config->get('tables.MERCHANT_STORE_MST').' as msm')
                            ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as pc', 'pc.bcategory_id', '=', 'msm.category_id')
                            ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as bct', 'bct.parent_bcategory_id', '=', 'pc.root_bcategory_id')->where('pc.category_type', 1);
        } 
		
		
		$cats->where('msm.is_online', '=', $this->config->get('constants.OFF'))
                ->join($this->config->get('tables.BUSINESS_CATEGORY').' as bc', 'bc.bcategory_id', '=', 'bct.bcategory_id')
                ->join($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as bcl', 'bcl.bcategory_id', '=', 'bc.bcategory_id')
                ->selectRaw('CONVERT(bcl.bcategory_id,CHAR) as value, bcl.bcategory_name as title, 0 as selected, CONCAT(\''.asset($this->config->get('path.BCATEGORY_ICONS_PATH.API')).'/'.'\', bc.icon) as img, bc.slug');
		//return $cats = $cats->get();
       /*  if (isset($district_id) && !empty($district_id))
        {
            $cats->join($this->config->get('tables.ADDRESS_MST').' as am', function($join) use($district_id)
            {
                $join->on('am.address_id', '=', 'msm.address_id')
                        ->where('am.district_id', '=', $district_id)
                        ->where('am.post_type', '=', $this->config->get('constants.POST_TYPE.STORE'));
            });
        } */

        $cats = $cats->distinct('bc.bcategory_id')->get();
        array_walk($cats, function(&$cat)
        {
            $cat->selected = $cat->selected ? true : false;
        });
        $properties[] = [
            'title'=>'Categories',
            'filter_name'=>'filter_categories[]',
            'type'=>'check',
            'ui_type'=>'inline',
            'apply_search'=>true,
            'values'=>$cats
        ];
        $properties[] = [
            'title'=>'Categories',
            'filter_name'=>'filter_discount[]',
            'type'=>'radio',
            'ui_type'=>'inline',
            'apply_search'=>true,
            'values'=>[
                [
                    'value'=>'upto_10',
                    'title'=>'Upto 10%',
                    'selected'=>false
                ],
                [
                    'value'=>'upto_25',
                    'title'=>'Upto 25%',
                    'selected'=>false
                ],
                [
                    'value'=>'upto_30',
                    'title'=>'Upto 30%',
                    'selected'=>false
                ],
                [
                    'value'=>'upto_50',
                    'title'=>'Upto 50%',
                    'selected'=>false
                ]
            ],
        ];

        return $properties;
    }
	
	public function dislikeStore (array $arr = array())
    {
        $data = [];
        $res = '';
        extract($arr);
        $data['post_type_id'] = $post_type_id = 3;   /* store */
        $result = DB::table($this->config->get('tables.STORES'))
                ->where('store_code', $store_code)
                ->select('store_id', 'supplier_id')
                ->first();

        if (!empty($result))
        {
            $data['relative_post_id'] = $result->store_id;
            $this->clear_favourite_store(['account_id'=>$account_id, 'store_id'=>$result->store_id]);
            if (DB::table($this->config->get('tables.ACCOUNT_RATINGS'))                            
                            ->where('account_id', $account_id)
                            ->where('is_deleted', 0)
                            ->where('post_type_id', $data['post_type_id'])
                            ->where('relative_post_id', $data['relative_post_id'])
                            ->exists())
            {				
                $res = DB::table($this->config->get('tables.ACCOUNT_RATINGS'))                        
                        ->where('account_id', $account_id)
                        ->where('post_type_id', $data['post_type_id'])
                        ->where('relative_post_id', $data['relative_post_id'])
                        ->update(['is_deleted'=>$this->config->get('constants.ON'), 'is_like'=>$this->config->get('constants.OFF'), 'updated_by'=>$account_id, 'updated_on'=>getGTZ()]);
            }

            if (!empty($res) || empty($account_id))
            {
                if (DB::table($this->config->get('tables.RATING'))
                                ->where('post_type_id', $data['post_type_id'])
                                ->where('relative_post_id', $data['relative_post_id'])
                                ->exists())
                {
                    if (DB::table($this->config->get('tables.RATING'))
                                    ->where('post_type_id', $data['post_type_id'])
                                    ->where('relative_post_id', $data['relative_post_id'])
                                    ->decrement('likes', 1, ['updated_on'=>getGTZ()]))
                    {
                        return DB::table($this->config->get('tables.RATING'))
                                        ->where('post_type_id', $data['post_type_id'])
                                        ->where('relative_post_id', $data['relative_post_id'])->value('likes');
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return DB::table($this->config->get('tables.RATING'))
                                    ->insertGetID([ 'post_type'=>$data['post_type'], 'post_id'=>$data['post_id'], 'likes'=>0, 'updated_on'=>getGTZ()]);
                }
            }
        }
			
        return false;
    }
	
	public function likeStore (array $arr = array())
    {
        $data = [];
        $res = '';
        extract($arr);
        $data['post_type_id'] = $post_type_id = 3;   /* Store */
		
        $result = DB::table($this->config->get('tables.STORES'))
                ->where('store_code', $store_code)
                ->select('store_id', 'supplier_id')
                ->first();
		
		
        if (!empty($result))
        {
            $data['relative_post_id'] = $result->store_id;				
            if (DB::table($this->config->get('tables.ACCOUNT_RATINGS'))
                            //->where($data)
                            ->where('account_id', $account_id)
                            ->where('post_type_id', $post_type_id)
                            ->where('relative_post_id', $data['relative_post_id'])
                            ->exists())
            {				
                $this->add_store_to_favourite(['account_id'=>$account_id, 'store_code'=>$store_code, 'storeInfo'=>$result]);
                $res = DB::table($this->config->get('tables.ACCOUNT_RATINGS'))
                        //->where($data)
                        ->where('account_id', $account_id)
                        ->where('post_type_id', $post_type_id)
                        ->where('relative_post_id', $data['relative_post_id'])
                        ->update(['is_like'=>$this->config->get('constants.ON'), 'updated_by'=>$account_id, 'is_deleted'=>$this->config->get('constants.OFF'), 'updated_on'=>getGTZ()]);
            }
            else
            {
                $this->add_store_to_favourite(['account_id'=>$account_id, 'store_code'=>$store_code, 'storeInfo'=>$result]);
                if (!empty($account_id))
                {
                    $data['is_like'] = $this->config->get('constants.ON');
                    $data['account_id'] = $account_id;
                    $data['updated_by'] = $account_id;
                    $data['created_on'] = getGTZ();					
                    $res = DB::table($this->config->get('tables.ACCOUNT_RATINGS'))
                            ->insertGetID($data);
                }
            }			
            if (!empty($res) || empty($account_id))
            {
                if (DB::table($this->config->get('tables.RATING'))
                                ->where('post_type_id', $post_type_id)
                                ->where('relative_post_id', $data['relative_post_id'])
                                ->exists())
                {
                    if (DB::table($this->config->get('tables.RATING'))
                                    ->where('post_type_id', $data['post_type_id'])
                                    ->where('relative_post_id', $data['relative_post_id'])
                                    ->increment('likes', 1, ['updated_on'=>getGTZ()]))
                    {
                        return DB::table($this->config->get('tables.RATING'))
                                        ->where('post_type_id', $data['post_type_id'])
                                        ->where('relative_post_id', $data['relative_post_id'])->value('likes');
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return DB::table($this->config->get('tables.RATING'))
                                    ->insertGetID([ 'post_type_id'=>$data['post_type_id'], 'relative_post_id'=>$data['relative_post_id'], 'likes'=>1, 'updated_on'=>getGTZ()]);
                }
            }
			return false;
        }
        return false;
    }
	
	public function likeCount (array $arr = array())
	{
		extract($arr);
		$result = DB::table($this->config->get('tables.STORES'))
                ->where('store_code', $store_code)
                ->select('store_id', 'supplier_id')
                ->first();				
		return DB::table($this->config->get('tables.RATING'))
				->where('post_type_id', 3)
				->where('relative_post_id', $result->store_id)
				->value('likes');
	}
	
			
	/* InStore Details */
	public function instore_details (array $arr = array())
    {	//return $arr;		
        $data = [];
        extract($arr);		
        $qry = DB::table($this->config->get('tables.STORES').' as st')					
                ->join($this->config->get('tables.SUPPLIER_MST').' as sm', function($sm)
                {
                    $sm->on('sm.supplier_id', '=', 'st.supplier_id')
					   ->where('sm.status', '=', $this->config->get('constants.ON'))
					   ->where('sm.block', '=', $this->config->get('constants.OFF'))
					   //->where('sm.expired_on', '>=', getGTZ(null, 'Y-m-d'))
					   //->where('sm.is_verified', '=', $this->config->get('constants.OFF'))
					   ->where('sm.is_closed', '=', $this->config->get('constants.OFF'))
					   ->where('sm.is_deleted', '=', $this->config->get('constants.OFF'));
                })             
                ->join($this->config->get('tables.CASHBACK_SETTINGS').' as scs', function($scs)
                {
                    $scs->on('scs.supplier_id', '=', 'st.supplier_id')
                    ->on(function($c)
                    {
                        $c->whereNull('scs.store_id');
                        $c->orWhere('scs.store_id', '=', 'st.store_id');
                    });
                })
                ->join($this->config->get('tables.ADDRESS_MST').' as am', function($am)
                {
                    $am->on('am.address_id', '=', 'st.address_id')
                       ->where('am.post_type', '=', $this->config->get('constants.ADDRESS_POST_TYPE.STORE'));
                });
			
		if (!empty($user_location['boundries'])) 
		{				
			//$qry->whereBetween('am.geolat', [$user_location['boundries']->minlat, $user_location['boundries']->maxlat]);
			//$qry->whereBetween('am.geolng', [$user_location['boundries']->minlng, $user_location['boundries']->maxlng]);
		}
		
            $qry->leftjoin($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'am.country_id')
                 ->leftjoin($this->config->get('tables.LOCATION_CITY').' as lcty', 'lcty.city_id', '=', 'am.city_id')
                //->join($this->config->get('tables.LOCATION_DISTRICTS').' as ld', 'ld.district_id', '=', 'am.district_id')
                ->leftjoin($this->config->get('tables.STORES_EXTRAS').' as se', function($se)
                {
                    $se->on('se.store_id', '=', 'st.store_id')
                       ->where('se.is_deleted', '=', $this->config->get('constants.OFF'));
                })
                ->join($this->config->get('tables.BUSINESS_CATEGORY').' as bc', 'bc.bcategory_id', '=', 'st.category_id')
                ->join($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as bcl', function($bcl)
                {
                    $bcl->on('bcl.bcategory_id', '=', 'st.category_id')
					    ->where('bcl.lang_id', '=', $this->config->get('app.locale_id'));
                })
                ->leftjoin($this->config->get('tables.RATING').' as rt', function($rt)
                {
                    $rt->on('rt.relative_post_id', '=', 'st.store_id')
                       ->where('rt.post_type_id', '=', $this->config->get('constants.RATING_POST_TYPE.STORE'));
                }) 
                ->where('st.store_code', '=', $store)
                ->where('st.status', $this->config->get('constants.ON'))
                ->where('st.block', $this->config->get('constants.OFF'))  
                ->where('st.is_approved', $this->config->get('constants.ON'));	
		
				
        $qry->selectRaw("sm.supplier_id, sm.category_id, sm.company_name as business_name, sm.logo as mrlogo, st.store_name as name, st.store_code, st.store_logo as logo, rt.rating, rt.likes, st.store_id, bcl.bcategory_name, se.description, am.address as location, am.landmark, am.geolat as lat, am.geolng as lng, CONCAT_WS(' ',lc.phonecode,se.mobile_no) as mobile, calculate_distance_with_unit(am.geolat, am.geolng,".$user_location['lat'].",".$user_location['lng'].",'".$user_location['distance_unit']."') as distance, scs.redeem as accept_xpay");
		
		
		
        if (isset($account_id) && !empty($account_id))
        {
             $qry->leftjoin($this->config->get('tables.FAVOURITES').' as fav', function($fav) use($account_id)
                    {
                        $fav->on('fav.relative_post_id', '=', 'st.store_id')
							->where('fav.post_type', '=', $this->config->get('constants.FAVOURITES_POST_TYPE.STORE'))
							->where('fav.account_id', '=', $account_id)
							->where('fav.is_deleted', '=', $this->config->get('constants.OFF'));
                    })
                    ->leftjoin($this->config->get('tables.ACCOUNT_RATINGS').' as ar', function($join) use($account_id)
                    {
                        $join->on('ar.relative_post_id', '=', 'st.store_id')
                        ->where('ar.account_id', '=', $account_id)
                        ->where('ar.post_type_id', '=', $this->config->get('constants.RATING_POST_TYPE.STORE'));
                    })
                    ->addSelect(DB::RAW('IF(fav.fav_id is not null,fav.fav_id,0) as fav_id'), DB::raw('IF(ar.is_like is not null,ar.is_like,0) as current_liked_status')); 
			//$qry->addSelect(DB::RAW('0 as fav_id'), DB::RAW('0 as current_liked_status'));
        }
        else
        {
            $qry->addSelect(DB::RAW('0 as fav_id'), DB::RAW('0 as current_liked_status'));
        }
        $result = $qry->first();
        if (!empty($result))
        {
            if (empty($result->name))
            {
                $result->name = $result->business_name;
            }
            unset($result->business_name);
            if (empty($result->logo))
            {
                $result->logo = asset($this->config->get('path.SELLER.PROFILE_IMG_PATH.WEB').$result->mrlogo);
            }
            else
            {                
                $result->logo = asset($this->config->get('path.SELLER.STORE_IMG_PATH.WEB').$result->logo);
            }
            $result->direction = null;
            $result->likes = !empty($result->likes) ? $result->likes : 1;
            $result->likes = trans_choice('general.likes', $result->likes, ['likes'=>$result->likes]);
            $result->landmark = !empty($result->landmark) ? $result->landmark : 'Near IOB';
            $result->rating = !empty($result->rating) && $result->rating != "0.00" ? round((float) $result->rating) : 0;
            $result->direction = 'https://maps.google.com/maps?saddr='.$user_location['lat'].','.$user_location['lng'].'&daddr='.$result->lat.','.$result->lng;
            unset($result->mrlogo);
            $result->imgs = $this->getStoreImgs(['store_id'=>$result->store_id]);
			
            $result->stores = DB::table($this->config->get('tables.STORES').' as st')			
								->join($this->config->get('tables.CASHBACK_SETTINGS').' as scs', function($scs)
								{
									$scs->on('scs.supplier_id', '=', 'st.supplier_id')
								    ->nest(function($s)
									{
										$s->whereNull('scs.store_id')
										->orOn('scs.store_id', '=', 'st.store_id');
									}) 
									 ->nest(function($c)
									{
										$c->where('scs.shop_and_earn', '=', $this->config->get('constants.ON'))
										->orWhere('scs.redeem', '=', $this->config->get('constants.ON'))
										->orWhere('scs.pay', '=', $this->config->get('constants.ON'));
									}); 
								})
								->where(function($c)
								{
									$c->where('scs.is_cashback_period', '=', $this->config->get('constants.OFF'))
									->orWhere(function($c1)
									{
										$c1->where('scs.is_cashback_period', '=', $this->config->get('constants.ON'))
										->whereDate('scs.cashback_start', '<=', getGTZ())
										->whereDate('scs.cashback_end', '>=', getGTZ());
									});
								})  
							 	->leftjoin($this->config->get('tables.RATING').' as rt', function($rt)
								{
									$rt->on('rt.relative_post_id', '=', 'st.store_id');
									$rt->where('rt.post_type_id', '=', $this->config->get('constants.RATING_POST_TYPE.STORE'));
								}) 								
								->join($this->config->get('tables.ADDRESS_MST').' as am', function($join) use($result)
								{
									$join->on('am.address_id', '=', 'st.address_id')
									     ->where('am.post_type', '=', $this->config->get('constants.ADDRESS_POST_TYPE.STORE'));
								})  
								->leftjoin($this->config->get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'am.country_id')							
								->where('st.supplier_id', $result->supplier_id)
								->where('st.store_id', '!=', $result->store_id)
								->where('st.is_deleted', '=', $this->config->get('constants.OFF'))
								->where('st.is_approved', '=', $this->config->get('constants.ON'))								
								->selectRaw('st.store_id, st.store_code, st.store_name as store, st.store_logo as logo, am.address, am.geolat as lat, am.geolng as lng, rt.likes, rt.rating,calculate_distance_with_unit(am.geolat, am.geolng, '.$user_location['lat'].','.$user_location['lng'].','.$user_location['distance_unit'].') as distance'); 
            // Merchant stores
            $store_count = $result->stores->count();
            $result->stores = $result->stores->get();			 
            $result->stores_has_many = ($store_count > 1) ? 1 : 0;
            $result->store_count = trans_choice('general.store_count', $store_count, ['store_count'=>$store_count]);         
			
            // Store Reviews
            $data = [];
            $data['store_id'] = $result->store_id;
            $data['start'] = 0;
            $data['length'] = 5;
            $result->reviews = $this->commonObj->storeReviewsList($data);
            $result->reviews_count = $this->commonObj->storeReviewsList($data, true);
            $result->reviews_has_many = ($result->reviews_count > $data['length']) ? 1 : 0;
            array_walk($result->stores, function(&$store) use($result, $user_location, $arr)
            {                
                $store->logo = asset($this->config->get('path.SELLER.STORE_IMG_PATH.WEB').$store->logo);
                $store->address = !empty($store->address) ? $store->address : '';
                $store->likes = !empty($store->likes) ? $store->likes : 0;
                //$store->likes = trans_choice('general.likes', $store->likes, ['likes'=>$store->likes]);
                $store->rating = !empty($store->rating) && $store->rating != "0.00" ? round((float) $store->rating) : 0;
                $store->store_id = $store->store_code;
            }); 
            $result->store_id = $result->store_code;
            //$result->distance = $this->getDistance($user_location['lat'], $user_location['lng'], $result->lat, $result->lng, $user_location['distance_unit']);
            unset($result->bcategory_id);
            unset($result->supplier_id);
        }
        return $result;
    }
    
    public function getDistance ($lat, $lng, $store_lat, $store_lng, $units)
    {	
		$source = 'origins='.$lat.','.$lng.'&destinations='.$store_lat.','.$store_lng.'&key='.$this->config->get('services.google.map_api_key');       
        if (isset($lat) && isset($lng))
        {
            $ch = curl_init();            
            $url = 'https://maps.googleapis.com/maps/api/distancematrix/json?units=kilometers&'.$source;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            if ($result === FALSE)
            {
                die('Curl failed: '.curl_error($ch));
            }
            curl_close($ch);
            $result = json_decode($result);
			if ($result) {
				return $result->rows[0]->elements[0]->distance->text;
			}            
        }        
        return false;
    }
	
	/* InStore Images */
	public function getStoreImgs (array $arr = array())
    {
        extract($arr);
        $imgs = DB::table($this->config->get('tables.SUPPLIER_IMAGES'))
		        ->where('store_id', $store_id)                
                ->where('is_verified', '!=', 2)
                ->where('is_deleted', $this->config->get('constants.OFF'))
				->where('status', $this->config->get('constants.ON'))
				->where('type', $this->config->get('constants.SELLER_IMAGE_TYPE.IMAGES'))
				->selectRaw('file_path as img, description')
                ->get();
        array_filter($imgs, function($img)
        {
            //$img->img = !empty($img->img) ? asset($this->config->get('constants.SELLERR.IMG_GALLERY_PATH.LG').$img->img) : asset($this->config->get('constants.SELLERR.IMG_GALLERY_PATH.DEFAULT'));
			$img->img = asset($this->config->get('path.SELLER.STORE_IMG_PATH.WEB').$img->img);            
            $img->description = !empty($img->description) ? $img->description : '';
        });
        return $imgs;
    }
	
	public function clear_favourite_store (array $arr = array(), $fav_id = 0)
    {
		$data['post_type_id'] = $post_type_id = 3;   /* Store */
        if (!empty($fav_id) || (isset($arr['store_id']) && isset($arr['account_id']) && $arr['store_id'] > 0 && $arr['account_id'] > 0))
        {
            $qry = DB::table($this->config->get('tables.FAVOURITES'))
                    ->where('post_type', $post_type_id);

            if (!empty($fav_id))
            {
                $qry->where('fav_id', $fav_id);
            }
            else if (isset($arr['store_id']) && isset($arr['account_id']) && $arr['store_id'] > 0 && $arr['account_id'] > 0)
            {
                $qry->where('account_id', $arr['account_id']);
                $qry->where('relative_post_id', $arr['store_id']);
            }

            return $qry->update(['is_deleted'=>$this->config->get('constants.ON')]);
        }
        else
        {
            return DB::table($this->config->get('tables.FAVOURITES'))
                            ->where('account_id', $arr['account_id'])
                            ->where('post_type', $this->config->get('constants.POST_TYPE.STORE'))
                            ->update(['is_deleted'=>$this->config->get('constants.ON')]);
        }
    }
	
	public function add_store_to_favourite (array $arr = array())
    {
        extract($arr);
		$post_type = 3;   /* Store */
        if (isset($storeInfo))
        {
            $result = $storeInfo;
        }
        else
        {
            $result = DB::table($this->config->get('tables.STORES'))
                    ->where('store_code', $store_code)
                    ->where('is_deleted', $this->config->get('constants.OFF'))
                    ->where('status', $this->config->get('constants.ACTIVE'))
                    ->selectRAW('supplier_id, store_id')
                    ->first(); 
        }
		
        if (!empty($result) && !empty($result->supplier_id))
        {
            $supplier_id = $result->supplier_id;
            $store_id = $result->store_id;
            if (!DB::table($this->config->get('tables.FAVOURITES'))
                            ->where('account_id', $account_id)
                            ->where('post_type', $post_type)
                            ->where('relative_post_id', $store_id)
                            ->exists())
            {				
                $res = DB::table($this->config->get('tables.FAVOURITES'))
                        ->insertGetId(['account_id'=>$account_id,
										'post_type'=>$post_type,
										'relative_post_id'=>$store_id,
										'supplier_id'=>$supplier_id, 'created_on'=>getGTZ()]);
                if ($res)
                {
                    return ['msg'=>trans('user/account.store_added_to_favourite'), 'fav_id'=>$res, 'status'=>$this->config->get('httperr.SUCCESS')];
                }
            }
            else if ($ress = DB::table($this->config->get('tables.FAVOURITES'))
                    ->where('account_id', $account_id)
                    ->where('post_type', $post_type)
                    ->where('relative_post_id', $store_id)
                    ->where('is_deleted', $this->config->get('constants.ON'))
                    ->value('fav_id'))
            {
                $uress = DB::table($this->config->get('tables.FAVOURITES'))
                        ->where('post_type', $post_type)
                        ->where('relative_post_id', $store_id)
                        ->where('account_id', $account_id)
                        ->update(['is_deleted'=>$this->config->get('constants.OFF')]);
                if ($uress)
                {
                    return ['msg'=>trans('user/account.store_added_to_favourite'), 'fav_id'=>$ress, 'status'=>$this->config->get('httperr.SUCCESS')];
                }
            }
            else
            {
                return ['msg'=>trans('user/account.store_exist_in_favourite'), 'status'=>$this->config->get('httperr.ALREADY_UPDATED')];
            }
        }
        else
        {
            return ['msg'=>trans('general.not_found', ['which'=>trans('general.outlet')]), 'status'=>$this->config->get('httperr.NOT_FOUND')];
        }
    }
	
	public function OnlineStoreList (array $arr = array(), $count = false, $featured_categories_only = false)
    {	
        extract($arr);	
        $qry = DB::table($this->config->get('tables.STORES').' as msm')				
                ->join($this->config->get('tables.ONLINE_STORE_SETTINGS').' as oss', function($oss) use($country_id)
                {
                    $oss->on('oss.store_id', '=', 'msm.store_id')
                    ->where('oss.post_type', '=', 18)       /* Country   */
                    ->where('oss.relative_post_id', '=', $country_id)
                    ->where('oss.is_deleted', '=', $this->config->get('constants.OFF')); 
                })  
                ->join($this->config->get('tables.SUPPLIER_MST').' as mm', 'mm.supplier_id', '=', 'msm.supplier_id')              
                ->join($this->config->get('tables.STORES_EXTRAS').' as se', 'se.store_id', '=', 'msm.store_id')
                ->leftjoin($this->config->get('tables.CASHBACK_OFFERS').' as co', function($join)
                {
                    $join->on('co.supplier_id', '=', 'msm.supplier_id')
					     ->on('co.store_id', '=', 'msm.store_id')
                         //->where('co.cboffer_type', '=', $this->config->get('constants.CBOFFER_TYPE.DISCOUNT'));
                         ->where('co.cboffer_type', '=', $this->config->get('constants.OFFER_TYPE.CASH_BACK'));
                })
                ->leftjoin($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'co.currency_id')  
                ->where('mm.is_verified', $this->config->get('constants.ON'))               
                ->where('msm.status', $this->config->get('constants.ON'))                                
                ->where('msm.is_approved', $this->config->get('constants.ON'))
                ->where('msm.is_deleted', $this->config->get('constants.OFF'))   
                ->where('mm.is_deleted', $this->config->get('constants.OFF'))
                ->where('mm.status', $this->config->get('constants.ON')) 
				->where('mm.is_closed', $this->config->get('constants.OFF'))  
                ->where('mm.is_online', $this->config->get('constants.ON'));
				
		
			
			    
		if (isset($category) && !empty($category))
		{
			/* $qry->join($this->config->get('tables.BUSINESS_CATEGORY').' as bc', 'bc.bcategory_id', '=', 'msm.category_id')
					->where('bc.slug', 'LIKE', '%'.$category.'%'); */
			$bcategory_id = DB::table($this->config->get('tables.BUSINESS_CATEGORY'))
                    ->where('category_type', '=', $this->config->get('constants.BCATEGORY_TYPE.ONLINE_STORE'))
                    ->where('slug', '=', $category)
                    ->value('bcategory_id');
            if ($bcategory_id)
            {
               $qry->whereRaw('FIND_IN_SET('.$bcategory_id.',msm.category_id)');
            }
		}      
			
        if (!empty($is_featured) && ($is_featured == true))
        {
            $qry->where('msm.is_featured', $is_featured);
        }
		
        if ($featured_categories_only)
        {
			//return $qry->get();
            return $qry->join($this->config->get('tables.BUSINESS_CATEGORY').' as bc', 'bc.bcategory_id', '=', 'msm.category_id')
                            ->join($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as bcl', function($bcl)
                            {
                                $bcl->on('bcl.bcategory_id', '=', 'bc.bcategory_id')
                                ->where('bcl.lang_id', '=', $this->config->get('app.locale_id'));
                            })
                            ->where('bc.is_deleted', '=', $this->config->get('constants.OFF'))
                            ->where('bc.status', '=', $this->config->get('constants.ACTIVE'))
                            ->where('bc.is_visible', '=', $this->config->get('constants.ON'))
                            ->where('bc.category_type', '=', $this->config->get('constants.BCATEGORY_TYPE.ONLINE_STORE'))
                            //->where('bc.is_featured', '=', $this->config->get('constants.ON'))
                            ->groupby('bc.bcategory_id')
                            ->distinct('bc.bcategory_id') 
                            ->selectRaw('bcl.bcategory_name as title,slug as url') 
                            ->get();
        }		
        if (!empty($arr['category_id']) && $arr['category_id'] > 0)
        {
            //$qry->where('msm.category_id', '=', $arr['category_id']);
        }
        if (!empty($arr['search_term']))
        {
            $qry->where('msm.store_name', 'LIKE', '%'.$arr['search_term'].'%');
            //$qry->orwhere('mm.mrbusiness_name', 'LIKE', '%'.$arr['store'].'%');
        }
        if ($count)
        {
		return  $qry->count();
			 //print_r($cnt); die;
        }
        else
        {
            if (isset($length))
            {
                $qry->skip($start)->take($length);
            }
            if (isset($orderby) && isset($order))
            {
                $qry->orderby($orderby, $order);
            }
            else
            {
                $qry->orderby('store_name', 'DESC');
            }            
            $qry->selectRaw('msm.store_name, msm.store_code, msm.store_slug, msm.store_logo, mm.logo, co.cashback_type, co.new_cashback, c.currency_id');
            $res = $qry->get();
            array_walk($res, function(&$store)
            {                  
				$store->logo = !empty($store->store_logo) ? asset($this->config->get('path.ONLINE.STORE.LOGO_PATH.MOBILE').$store->store_logo) : asset($this->config->get('path.ONLINE.STORE.LOGO_PATH.DEFAULT'));		
				if ($store->cashback_type == $this->config->get('constants.CASHBACK_VALUE_TYPE.PERCENTAGE'))
                {
                    $store->offer = trans('user/account.offer_upto', ['offerval'=>$store->new_cashback]);     					
                }
                else if ($store->cashback_type == $this->config->get('constants.CASHBACK_VALUE_TYPE.FIXED'))
                {
					//$store->offer = trans('user/account.offer_upto', ['offerval'=>CommonLib::currency_format($store->new_cashback, $store->currency_id, true, false)]);
					$store->offer = trans('user/account.offer_upto', ['offerval'=>$store->new_cashback]); 
				}
                else
                {
                    $store->offer = trans('user/account.offer_upto').$store->new_cashback.trans('general.offer_points');
                }
                unset($store->store_logo);
                unset($store->new_cashback);
                unset($store->currency_id);                
                unset($store->cashback_type);                
            });
            return $res;
        }
    }
	
	public function OnlineStoreDetails (array $arr = array())
    {	
        $account_id = null;
        extract($arr);
        $qry = DB::table($this->config->get('tables.STORES').' as msm')
				->join($this->config->get('tables.STORES_EXTRAS').' as se', 'se.store_id', '=', 'msm.store_id')
				->join($this->config->get('tables.SUPPLIER_MST').' as mm', 'mm.supplier_id', '=', 'msm.supplier_id')
				->join($this->config->get('tables.STORE_SETTINGS').' as mss', 'mss.store_id', '=', 'msm.store_id')
				->leftjoin($this->config->get('tables.ONLINE_STORE_BANNERS').' as sb', 'sb.banner_id', '=', 'msm.banner_id')
				->leftjoin($this->config->get('tables.RATING').' as r', function($join)
                {
                    $join->on('r.relative_post_id', '=', 'msm.store_id')
                    ->where('r.post_type_id', '=', $this->config->get('constants.RATING_POST_TYPE.STORE'));
                })
				->leftjoin($this->config->get('tables.CASHBACK_OFFERS').' as co', function($join)
                {
                    $join->on('co.supplier_id', '=', 'msm.supplier_id')
                    ->on('co.store_id', '=', 'msm.store_id')
                    ->where('co.cboffer_type', '=', $this->config->get('constants.CBOFFER_TYPE.DISCOUNT'));
                })
				->leftjoin($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'co.currency_id')
				->join($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as bcl', function($join)
                {
					$join->on('bcl.bcategory_id', '=', 'msm.category_id')
						->where('bcl.lang_id', '=', $this->config->get('app.locale_id'));
                })
				->where('msm.status', $this->config->get('constants.ON'))
				->where('msm.block', $this->config->get('constants.OFF'))
				->where('mm.block', $this->config->get('constants.OFF'))
				->where('msm.is_approved', $this->config->get('constants.ON'))
				->where('mm.is_deleted', $this->config->get('constants.OFF'))
				->where('mm.status', $this->config->get('constants.ON'))
				->where('mm.is_online', $this->config->get('constants.ON'))
				->where('msm.store_code', $store_code)
				->selectRaw('msm.supplier_id, msm.store_id, se.url as link, se.website as website_url, msm.store_name, msm.store_logo, msm.logo_url, msm.store_code, r.avg_rating as rating, co.cashback_type, co.new_cashback, c.currency_symbol, c.decimal_places, se.description, se.desc_type, se.dos_desc, se.dont_desc, se.conditions, mss.cb_tracking_days, mss.cb_waiting_days, se.cb_notes, msm.category_id, sb.banner_path');
				
        $store = $qry->first();

        if ($store)
        {			
			$store->logo = !empty($store->store_logo) ? asset($this->config->get('path.ONLINE.STORE.LOGO_PATH.MOBILE').$store->store_logo) : asset($this->config->get('path.ONLINE.STORE.LOGO_PATH.DEFAULT'));
            $store->link = str_replace('{USER_ID}', $account_id, $store->link);
            $store->offer = trans('user/account.offer_upto', ['offerval'=>$store->new_cashback]);            
            $store->rating = !empty($store->rating) ? $store->rating : 0;            
            $store->banner_path = asset($this->config->get('path.ONLINE.STORE.BANNER_PATH.MOBILE').$store->banner_path);
            $store->promotions = [];
            $store->cashback = [
                'start'=>'Today',
                //'tracked_in'=>(String) $store->cb_tracking_days,
                'tracked_in'=>2 . ' Days',
                //'redeemable_in'=>(String) $store->cb_waiting_days,
                'redeemable_in'=>60 . ' Days',
                'terms'=>!empty($store->cb_notes) ? '<ul><li>'.$store->cb_notes.'</li></ul>' : ''
            ];
            if ($store->link != null)
            {
                $store->link = str_replace($this->config->get('constants.INSTORE_URL_REPLACE'), $account_id, $store->link);
            }
            if ($store->website_url != null)
            {
                $store->website_url = str_replace($this->config->get('constants.INSTORE_URL_REPLACE'), $account_id, $store->website_url);
            }
            if ($store->conditions == null)
            {
                $store->conditions = '';
            }			
            if ($store->desc_type == 2)
            {
                $store->description = json_decode($store->description, true);
                if (!empty($store->description))
                {
                    $description = '<ul>';
                    foreach ($store->description as $desc)
                    {
                        $description = $description.'<li>'.$desc['desc'].' '.$desc['val'].'%</li>';
                    }
                    $store->description = $description;
                }
            }
           $store->promotions['data'] = DB::table($this->config->get('tables.CASHBACK_OFFERS').' as co')
                    ->join($this->config->get('tables.STORES').' as msm', 'msm.store_id', '=', 'co.store_id')
                    ->leftjoin($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'co.currency_id')
                    //->where('co.cboffer_type', '=', $this->config->get('constants.CBOFFER_TYPE.COUNPON'))
                        ->where('co.cboffer_type', '=', 1)
                    ->where('co.supplier_id', $store->supplier_id)
                    //->where('co.store_id', $store->store_id)
                        ->where('co.store_id', 4)
                    ->where('co.is_deleted', $this->config->get('constants.OFF'))                    
                    ->where('co.status', $this->config->get('constants.CASHBACK_OFFER_STATUS.ACTIVE'))
                    ->where('co.is_approved', $this->config->get('constants.CASHBACK_OFFER_APPROVAL.APPROVED'))
                    ->selectRaw('co.cboffer_title, co.coupon_code, co.description, co.cashback_url, co.sub_id_name, co.cashback_type, co.new_cashback, c.currency_symbol, c.decimal_places,co.expired_on, co.image_url, co.start_date,co.end_date,msm.store_code,co.terms') 
                    ->get();

            if (!empty($store->promotions['data']))
            {
                array_walk($store->promotions['data'], function(&$offer) use($account_id, $store)
                {                    
                    $offer->offer =  trans('user/account.offer_upto', ['offerval'=>$offer->new_cashback]);                      
                    if ($offer->cashback_url == null)
                    {
                        $offer->cashback_url = str_replace($this->config->get('constants.INSTORE_URL_REPLACE'), $account_id, $store->link);
                    }
                    $offer->expired_on = showUTZ($offer->expired_on, 'd-M-Y');
                    $now = time(); // or your date as well
                    $exp_date = strtotime($offer->expired_on);
                    $datediff = $exp_date - $now;
                    $offer->expired_on = (round($datediff / (60 * 60 * 24))).' days';
                    $offer->start_date = showUTZ($offer->start_date, 'd-M-Y');
                    $offer->end_date = showUTZ($offer->end_date, 'd-M-Y');
                    $offer->image_url = asset($this->config->get('constants.AFFILIATE.STORE.COUPONS.WEB.SM')).'/'.$offer->image_url;
                    //$offer->store_details_url   = route('user.online-partner',$offer->store_code);
                    unset($offer->sub_id_name);
                    unset($offer->new_cashback);
                    unset($offer->currency_symbol);
                    unset($offer->decimal_places);
                    unset($offer->cashback_type);
                });
            }
            else
            {
                $store->promotions['msg'] = 'Data Not Found';
                $store->promotions['data'] = [];
            }
             unset($store->logo_url);
            unset($store->store_logo);
            unset($store->new_cashback);
            unset($store->currency_symbol);
            unset($store->decimal_places);
            unset($store->cashback_type);
            unset($store->supplier_id);
            //unset($store->store_id);
            unset($store->cb_tracking_days);
            unset($store->cb_waiting_days);
            unset($store->cb_notes);  
        }
        return $store;
    }
	
	
	
	public function checkMerchantCreditLimit ($arr = array())
    {
		//print_R($arr); die;
        $store_id = null;
        $system_received_amount = 0;
        extract($arr);
        if (!$is_premium || $has_credit_limit)
        {
            $details = DB::table($this->config->get('tables.PROFIT_SHARING').' as p')
                    ->where(function($sqry) use ($store_id)
					{
						$sqry->where('p.store_id','=',$this->config->get('constants.OFF'))
						->orWhere('p.store_id', $store_id);
					})				
                    ->where('p.status', $this->config->get('constants.ON'))
                    ->where('p.is_deleted', $this->config->get('constants.OFF'))
                    ->where('p.supplier_id', $supplier_id)
                    ->orderBy('p.store_id', 'desc')
                    ->selectRaw('p.profit_sharing, p.cashback_on_pay, p.cashback_on_redeem, p.cashback_on_shop_and_earn')
                    ->first();

            if (!empty($details))
            {
                $tot_tax = 0;
                $charges = [];
                $cashback_profit = ($bill_amount / 100) * $details->profit_sharing;
                if ($cashback_profit > 0)
                {
                    $taxes = $this->getTax(['country_id'=>$country_id]);
					
                    array_walk($taxes, function(&$tax) use($cashback_profit, &$tot_tax)
                    {
                        $tax->tax_amt = $cashback_profit * $tax->tax_value / 100;
                        $tot_tax = $tot_tax + $tax->tax_amt;
                    });
                }
                $transaction_charge = $bill_amount * ($this->config->get('constants.TRANSACTION_CHARGE') / 100);
                $settlement_amount = $system_received_amount - ($cashback_profit + $tot_tax + $transaction_charge);
                $credit_limit = json_decode($this->getSetting('supplier_credit_limit_by_currency'), true);						
                $credit_limit = array_key_exists($currency_id, $credit_limit) ? $credit_limit[$currency_id] : 0;								
                return $settlement_amount >= 0 || DB::table($this->config->get('tables.SUPPLIER_MST').' as m')
                                ->join($this->config->get('tables.ACCOUNT_BALANCE').' as ab', function($ab) use($currency_id)
                                {
                                    $ab->on('ab.account_id', '=', 'm.account_id')
                                    ->where('ab.currency_id', '=', $currency_id)
                                    ->where('ab.wallet_id', '=', $this->config->get('constants.WALLET_NEW.VI-SP'));
                                })
                                ->where('m.supplier_id', $supplier_id)
                                ->where(DB::raw('ab.current_balance+'.$settlement_amount), '>=', $credit_limit)
                                ->exists();
            }
        }
        return true;
    }
    


}