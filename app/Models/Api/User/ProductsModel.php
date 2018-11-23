<?php
namespace App\Models\Api\User;
use DB;
use App\Models\BaseModel;
use CommonLib;
use Log;
use URL;

class ProductsModel extends BaseModel {
	
    public function __construct() 
	{
        parent::__construct();				
    }
	
	public function dashboard_products (array $arr = [])
    {	
        extract($arr);
        $products = DB::table($this->config->get('tables.PRODUCTS').' as pro')                
							 ->Join($this->config->get('tables.PRODUCT_DETAILS').' as pd', 'pd.product_id', '=', 'pro.product_id')
							->Join($this->config->get('tables.SUPPLIER_PRODUCT_PRICE').' as spp', function($join) use($currency_id)
							{
								$join->on('spp.product_id', '=', 'pro.product_id');                    
								if (isset($currency_id) && $currency_id > 0)
								{
									$join->where('spp.currency_id', '=', $currency_id);
								}
							})							 
							->join($this->config->get('tables.IMGS').' as imgs', function($img)
							{
								$img->on('imgs.relative_post_id', '=', 'pro.product_id')
									->where('imgs.post_type_id', '=', $this->config->get('constants.POST_TYPE.PRODUCT'))
									->where('imgs.status_id', '=', $this->config->get('constants.ON'));
							})
							->selectRAW('pro.product_name, spp.price, spp.off_perc, pd.product_code, imgs.img_path, imgs.relative_post_id, imgs.img_file')
							->where('pro.is_deleted', $this->config->get('constants.OFF'))
							->groupby('pd.product_code')
							->get(); 
		array_walk($products, function(&$product) use ($currency_id)
		{
			$price = $product->price;
			$product->image = asset('resources/uploads/default.png');	
			if (isset($product->img_file) && !empty($product->img_file)) {				
				$product->image = asset($this->config->get('path.PRODUCT_IMG_PATH.API').$product->img_path.'/'.$product->relative_post_id.'/'.$product->img_file);	
			}
			
			$product->price = CommonLib::currency_format($price, $currency_id, true, false);
			$selling_price = $price - ($price * ($product->off_perc / 100));
			$product->selling_price = CommonLib::currency_format($selling_price, $currency_id, true, false);
			$product->off_perc = $product->off_perc . '% off';
			$product->rating = 4;
			unset($product->img_file, $product->relative_post_id, $product->img_path);
		});		 
		return $products;
    }
	
	public function ProductCategories (array $params = array(), $parents_only = false, $load_siblings = false)
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
                ->orderby('sct.bcategory_id', 'ASC')
                ->where('sc.category_type', $this->config->get('constants.CATEGORY_TYPE.PRODUCT'));				
		
		$cats->join($this->config->get('tables.BUSINESS_CATEGORY').' as bc', 'bc.bcategory_id', '=', 'bct.bcategory_id')
                ->join($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as bcl', 'bcl.bcategory_id', '=', 'bc.bcategory_id')
                ->selectRaw('bc.slug as category_slug, bcl.bcategory_name, bc.icon, bct.cat_lftnode, bct.cat_rgtnode, bc.bcategory_id, bct.parent_bcategory_id')
                ->orderby('bct.cat_lftnode', 'ASC');
				
        if (isset($category_slug) && !empty($category_slug))
        {
            $cats = $cats->where('sc.slug', $category_slug);
        } else {
			$cats->where('bc.is_visible', '=', $this->config->get('constants.ON'));
		}
        
        
        $catresult = $catresults = $cats->distinct('bc.bcategory_id')->get();
        $unset_items = [];
        array_walk($catresult, function(&$category, $key) use ($catresults, &$unset_items)
        {	                       
			$category->icon = asset($this->config->get('path.BCATEGORY_ICONS_PATH.API').$category->icon);
            if (($category->cat_lftnode + 1) == $category->cat_rgtnode)
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
            });
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
	
	public function ProductList_By_Category_old_from_view (array $arr = [])
    {				
		extract($arr);
        $supplier_products = DB::table($this->config->get('tables.SUPPLIER_PRODUCTS_LIST').' as sp')
                ->join($this->config->get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT').' as spsm', 'spsm.supplier_product_id', '=', 'sp.supplier_product_id')
                ->join($this->config->get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'sp.currency_id')
                ->join($this->config->get('tables.SUPPLIER_MST').' as s', function($s)
                {
                    $s->on('s.supplier_id', '=', 'sp.supplier_id')
                    ->where('s.status', '=', $this->config->get('constants.ACTIVE'));
                })
                ->join($this->config->get('tables.SUPPLIER_BRAND_ASSOCIATE').' as spba', function($spba)
                {
                    $spba->on('spba.brand_id', '=', 'sp.brand_id')
                    ->on('spba.supplier_id', '=', 'sp.supplier_id');
                })
                ->join($this->config->get('tables.SUPPLIER_CATEGORY_ASSOCIATE').' as spca', function($spba)
                {
                    $spba->on('spca.category_id', '=', 'sp.category_id')
                    ->on('spca.supplier_id', '=', 'sp.supplier_id');
                })
                ->where(function($subquery)
                {
                    $subquery->where('sp.pre_order', $this->config->get('constants.ACTIVE'))
                    ->orWhere('spsm.stock_on_hand', '>', 0);
                })   
                ->where('sp.status', $this->config->get('constants.ACTIVE'))
                ->where('sp.spi_status', $this->config->get('constants.ACTIVE'))
                ->where('sp.spi_is_deleted', $this->config->get('constants.OFF'))
                ->where('sp.is_deleted', $this->config->get('constants.OFF'))
                ->orderby('sp.product_avg_rating', 'DESC') 
                ->where('sp.is_deleted', $this->config->get('constants.OFF'));
				
		if (!empty($category))
        {            	
                $parent = DB::table($this->config->get('tables.BUSINESS_CATEGORY').' as sc')
                        ->where('sc.slug', '=', $category)
                        ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as sct', 'sct.bcategory_id', '=', 'sc.bcategory_id')
                        ->selectRaw('sct.bcategory_id,sct.root_bcategory_id,sct.cat_lftnode,sct.cat_rgtnode');
                $supplier_products->join($this->config->get('tables.BUSINESS_CATEGORY').' as bcc', function($bcc)
                        {
                            $bcc->on('bcc.bcategory_id', '=', 'sp.category_id')
                            ->where('bcc.status', '=', $this->config->get('constants.ACTIVE'))
                            ->where('bcc.is_deleted', '=', $this->config->get('constants.OFF'));
                        })
                        ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as ct', 'ct.bcategory_id', '=', 'sp.category_id')
                        ->join(DB::raw('('.$parent->tosql().') as p'), function($p)
                        {
                            $p->on('ct.root_bcategory_id', '=', 'p.root_bcategory_id')
                            ->on('ct.cat_lftnode', '>=', 'p.cat_lftnode')
                            ->on('ct.cat_rgtnode', '<=', 'p.cat_rgtnode');
                        })
                        ->addBinding($parent->getBindings(), 'join');             
        }	
					
		$products = $supplier_products->selectRaw('sp.product_id, sp.product_name, sp.product_code, sp.mrp_price, sp.price, sp.product_avg_rating as rating')->get();			

		array_walk($products, function(&$product) use ($currency_id)
		{
			$images = DB::table($this->config->get('tables.IMGS').' as imgs')
                        ->where('imgs.relative_post_id', '=', $product->product_id)
						->where('imgs.post_type_id', '=', $this->config->get('constants.POST_TYPE.PRODUCT'))
						->where('imgs.status_id', '=', $this->config->get('constants.ON'))
						->selectRAW('imgs.img_path, imgs.relative_post_id, imgs.img_file')
						->first();
			$price = $product->price;
			$mrp_price = $product->mrp_price;    	
			$product->image = asset('resources/uploads/default.png');	
			if (isset($images) && !empty($images)) {				
				$product->image = asset($this->config->get('path.PRODUCT_IMG_PATH.API').$images->img_path.'/'.$images->relative_post_id.'/'.$images->img_file);	
			}		 
			$product->off_perc = 100 - (($product->price / $product->mrp_price) * 100) . '% off' ;
			$product->mrp_price = CommonLib::currency_format($product->mrp_price, $currency_id, true, false);
			$product->price = CommonLib::currency_format($product->price, $currency_id, true, false);			
			//$product->rating = 4;
			unset($product->img_file, $product->relative_post_id, $product->img_path, $product->product_id);
		});	
		
		return $products;
    }
	
	public function ProductList_By_Category (array $arr = [])
    {	
        extract($arr);
        $qry = DB::table($this->config->get('tables.PRODUCTS').' as pro')                
							 ->Join($this->config->get('tables.PRODUCT_DETAILS').' as pd', 'pd.product_id', '=', 'pro.product_id')
							 ->Join($this->config->get('tables.PRODUCT_MRP_PRICE').' as pmp', function($join) use($currency_id)
							{
								$join->on('pmp.product_id', '=', 'pro.product_id');                    
								if (isset($currency_id) && $currency_id > 0)
								{
									$join->where('pmp.currency_id', '=', $currency_id);
								}
							})
							->Join($this->config->get('tables.SUPPLIER_PRODUCT_PRICE').' as spp', function($join) use($currency_id)
							{
								$join->on('spp.product_id', '=', 'pro.product_id');                    
								if (isset($currency_id) && $currency_id > 0)
								{
									$join->where('spp.currency_id', '=', $currency_id);
								}
							})
							->leftJoin($this->config->get('tables.RATING').' as r', function($join) use($currency_id)
							{
								$join->on('r.relative_post_id', '=', 'pro.product_id')
									 ->where('r.post_type_id', '=', $this->config->get('constants.POST_TYPE.PRODUCT'));					
							});							 							
		
		if (!empty($category))
        {            	
                $parent = DB::table($this->config->get('tables.BUSINESS_CATEGORY').' as sc')
                        ->where('sc.slug', '=', $arr['category'])
                        ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as sct', 'sct.bcategory_id', '=', 'sc.bcategory_id')
                        ->selectRaw('sct.bcategory_id,sct.root_bcategory_id,sct.cat_lftnode,sct.cat_rgtnode');
                $qry->join($this->config->get('tables.BUSINESS_CATEGORY').' as bcc', function($bcc)
                        {
                            $bcc->on('bcc.bcategory_id', '=', 'pro.category_id')
                            ->where('bcc.status', '=', $this->config->get('constants.ACTIVE'))
                            ->where('bcc.is_deleted', '=', $this->config->get('constants.OFF'));
                        })
                        ->join($this->config->get('tables.BUSINESS_CATEGORY_TREE').' as c', 'c.bcategory_id', '=', 'pro.category_id')
                        ->join(DB::raw('('.$parent->tosql().') as p'), function($p)
                        {
                            $p->on('c.root_bcategory_id', '=', 'p.root_bcategory_id')
                            ->on('c.cat_lftnode', '>=', 'p.cat_lftnode')
                            ->on('c.cat_rgtnode', '<=', 'p.cat_rgtnode');
                        })
                        ->addBinding($parent->getBindings(), 'join');             
        }							
		
		$products = $qry->selectRAW('pro.product_id, pro.product_name, spp.price, spp.off_perc, pd.product_code, pmp.mrp_price, r.avg_rating as rating')
							->where('pro.is_deleted', $this->config->get('constants.OFF'))
							->groupby('pd.product_code')
							->get(); 
							
		array_walk($products, function(&$product) use ($currency_id)
		{
			$images = DB::table($this->config->get('tables.IMGS').' as imgs')
                        ->where('imgs.relative_post_id', '=', $product->product_id)
						->where('imgs.post_type_id', '=', $this->config->get('constants.POST_TYPE.PRODUCT'))
						->where('imgs.status_id', '=', $this->config->get('constants.ON'))
						->selectRAW('imgs.img_path, imgs.relative_post_id, imgs.img_file')
						->first();
			$price = $product->price;
			$mrp_price = $product->mrp_price;
			$product->image = asset('resources/uploads/default.png');	
			if (isset($images) && !empty($images)) {				
				$product->image = asset($this->config->get('path.PRODUCT_IMG_PATH.API').$images->img_path.'/'.$images->relative_post_id.'/'.$images->img_file);	
			}			
			$product->mrp_price = CommonLib::currency_format($mrp_price, $currency_id, true, false);
			$product->price = CommonLib::currency_format($price, $currency_id, true, false);			
			$product->off_perc = $product->off_perc . '% off';
			$product->rating = !empty($product->rating) ? $product->rating : 5;
			unset($product->img_file, $product->relative_post_id, $product->img_path, $product->product_id);
		});		 
		return $products;
    }	
	
	public function Get_Category_By_Slug (array $arr = [])
    {	
		return DB::table($this->config->get('tables.BUSINESS_CATEGORY').' as cat')                
					->Join($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as bcl', 'bcl.bcategory_id', '=', 'cat.bcategory_id')
					->where('cat.slug', '=', $arr['category'])->value('bcl.bcategory_name');
							
	}
	
	public function Product_Details (array $arr = [])
    {
		extract($arr);
		$details = DB::table($this->config->get('tables.PRODUCTS').' as pro')                
							->Join($this->config->get('tables.PRODUCT_COMBINATIONS').' as pcmb', 'pcmb.product_id', '=', 'pro.product_id')							 
							->Join($this->config->get('tables.PRODUCT_DETAILS').' as pd', function($join) 
							{
							   $join->on('pd.product_id', '=', 'pro.product_id')                    
									->where(function($s)
									{
										$s->where('pro.is_combinations', '=', 0)
										->whereNull('pd.product_cmb_id');
									})
									->orWhere(function($t)
									{
										$t->where('pro.is_combinations', '=', 1)
										   ->on('pd.product_cmb_id', '=', 'pcmb.product_cmb_id');
									});
							}) 
							->Join($this->config->get('tables.PRODUCT_BRANDS').' as pb', 'pb.brand_id', '=', 'pro.brand_id') 
							->Join($this->config->get('tables.BUSINESS_CATEGORY').' as bc', 'bc.bcategory_id', '=', 'pro.category_id')
							->Join($this->config->get('tables.BUSINESS_CATEGORY_LANG').' as bcl', 'bcl.bcategory_id', '=', 'bc.bcategory_id')
							 ->Join($this->config->get('tables.PRODUCT_MRP_PRICE').' as pmp', function($join) use($currency_id)
							{
								$join->on('pmp.product_id', '=', 'pro.product_id');                    
								if (isset($currency_id) && $currency_id > 0)
								{
									$join->where('pmp.currency_id', '=', $currency_id);
								}
							})						
							->Join($this->config->get('tables.SUPPLIER_PRODUCT_PRICE').' as spp', function($join) use($currency_id)
							{
								$join->on('spp.product_id', '=', 'pro.product_id');                    
								if (isset($currency_id) && $currency_id > 0)
								{
									$join->where('spp.currency_id', '=', $currency_id);
								}
							})							
							->Join($this->config->get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi', 'spi.product_id', '=', 'pro.product_id')
							->Join($this->config->get('tables.STORES').' as s', 's.store_id', '=', 'spi.store_id')
							->leftjoin($this->config->get('tables.SERVICE_POLICIES').' as sp', 'sp.service_policy_id', '=', 'bc.replacement_service_policy_id')
							->where('pd.product_code', '=', $product_code)							
							->selectRAW('pro.product_id, pro.product_name, spp.price, spp.off_perc, pd.product_code, pmp.mrp_price, s.store_name as sold_by, spi.created_on, sp.policy_period, sp.policy_title, sp.policy_desc, pro.is_combinations, bc.slug as category_url_str, pd.product_slug') 
							->where('pro.is_deleted', $this->config->get('constants.OFF'));	
							
		$details = $details->first();		
		
		if (!empty($details))
		{
			$images = DB::table($this->config->get('tables.IMGS').' as imgs')
                        ->where('imgs.relative_post_id', '=', $details->product_id)
						->where('imgs.post_type_id', '=', $this->config->get('constants.POST_TYPE.PRODUCT'))
						->where('imgs.status_id', '=', $this->config->get('constants.ON'))
						->selectRAW('imgs.img_path, imgs.relative_post_id, imgs.img_file')
						->get();
			$imgs = [];
			if (!empty($images)) {			
				foreach($images as $key => $val) {
					$imgs[] = asset($this->config->get('path.PRODUCT_IMG_PATH.API').$val->img_path.'/'.$val->relative_post_id.'/'.$val->img_file);	
				}				
			}			
			$details->images =  $imgs;					
			$details->mrp_price = CommonLib::currency_format($details->mrp_price, $currency_id, true, false);
			$details->price = CommonLib::currency_format($details->price, $currency_id, true, false);			
			$details->off_perc = $details->off_perc . '% off';
			$rating = DB::table($this->config->get('tables.RATING').' as r')
						->where('r.relative_post_id', '=', $details->product_id)
						->where('r.post_type_id', '=', $this->config->get('constants.POST_TYPE.PRODUCT'))
						->selectRAW('r.rating_1, r.rating_2, r.rating_3, r.rating_4, r.rating_5, r.avg_rating, r.rating_count, r.likes, r.positive_rating')
						->first();
			$details->seller_rating	= round(($rating->positive_rating / $rating->rating_count) * 100) . '%';	
			unset($rating->positive_rating);	
			$details->rating = (array) $rating;						
			$details->return_warrenty = ['title'=>$details->policy_period . ' ' .$details->policy_title, 'description'=>$details->policy_desc];			
			
			$diff = abs(strtotime($details->created_on) - strtotime(date('Y-m-d')));
			$years = floor($diff / (365*60*60*24));
			$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
			$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
			$details->time_on_virob = ($months > 0) ? $months.' Months' : (($days > 0) ? $days . ' Days' : '');
			$details->seller_size = 2;
			
			$reviews = DB::table($this->config->get('tables.ACCOUNT_RATINGS').' as r')
						->Join($this->config->get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'r.account_id') 
						->where('r.relative_post_id', '=', $details->product_id)
						->where('r.post_type_id', '=', $this->config->get('constants.POST_TYPE.PRODUCT'))
						->selectRAW('r.feedback, r.created_on, r.rating, r.is_like, ad.firstname, r.is_verified')
						->get();
			if (!empty($reviews))
			{
				foreach ($reviews as $val)
				{
					$diff = abs(strtotime($val->created_on) - strtotime(date('Y-m-d')));
					$years = floor($diff / (365*60*60*24));
					$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
					$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
					$val->created_on = ($months > 0) ? $months.' Months Ago' : (($days > 0) ? $days . ' Days Ago' : '') ;
				}
			}
			$details->reviews_has_many = (count($reviews) > 3) ? 1 : 0;
			$details->reviews = array_values($reviews);			
			unset($details->policy_period, $details->policy_title, $details->policy_desc, $details->created_on);
			//return $details;
			if ($details->is_combinations == $this->config->get('constants.ACTIVE'))
            {
				$combinations = DB::table($this->config->get('tables.PRODUCT_COMBINATIONS'))
                            ->where('is_deleted', $this->config->get('constants.OFF'))
                            ->where('product_id', $details->product_id)
                            ->orderby('updated_on', 'DESC')
                            ->select('product_cmb_id', 'product_cmb', 'sku as cmb_sku', 'product_cmb_code')                            
                            ->get();
				$details->product_cmbs = [];
                $product_cmb_code = isset($product_cmb_code) ? $product_cmb_code : $combinations[0]->product_cmb_code;
				array_walk($combinations, function(&$combination) use(&$details, $product_cmb_code)
				{
					if ($combination->product_cmb_code == $product_cmb_code)
					{
						$details = (object) array_merge((array) $details, (array) $combination);
					}
					$details->product_cmbs[$combination->product_cmb_id] = $this->productDetailsURL((object) array_merge((array) $details, (array) $combination));
				}); 			
				
				$details->properties = DB::table($this->config->get('tables.PRODUCT_PROPERTY').' as pp')
                             ->join($this->config->get('tables.PRODUCT_PROPERTY_KEYS').' as pk', 'pk.property_id', '=', 'pp.property_id')
                             ->join($this->config->get('tables.PRODUCT_PROPERTY_VALUES').' as ppv', 'ppv.pp_id', '=', 'pp.pp_id')
                             ->join($this->config->get('tables.PRODUCT_PROPERTY_KEY_VALUES').' as pv', 'pv.value_id', '=', 'ppv.value_id')
                             ->join($this->config->get('tables.PRODUCT_CMB_PROPERTIES').' as pcp', function($pcp) use($details)
                            {
                                $pcp->on('pcp.property_id', '=', 'pv.property_id')
									->on('pcp.value_id', '=', 'ppv.value_id')
                                    ->where('pcp.product_cmb_id', '=', $details->product_cmb_id);
                            }) 
                            ->leftjoin($this->config->get('tables.UNITS').' as u', 'u.unit_id', '=', 'pv.unit_id')
                            ->where('ppv.is_deleted', $this->config->get('constants.OFF'))
                            ->where('pp.is_deleted', $this->config->get('constants.OFF'))                            
                            ->selectRaw('pp.pp_id, pk.property, pp.choosable, pk.values_options_type, pv.value_id, concat(pv.key_value,if(u.unit is not null,concat(\' \',u.unit),\'\')) as value')   
							->where('pp.product_id', $details->product_id)
                            ->get();
				
				$details->choosable_properties = array_filter($details->properties, function($property) use(&$details)
				{					
					if ($property->choosable == $this->config->get('constants.ACTIVE'))
					{						
						$values = DB::table($this->config->get('tables.PRODUCT_PROPERTY_VALUES').' as ppv')
								->join($this->config->get('tables.PRODUCT_PROPERTY_KEY_VALUES').' as pv', 'pv.value_id', '=', 'ppv.value_id')
								->leftjoin($this->config->get('tables.UNITS').' as u', 'u.unit_id', '=', 'pv.unit_id')
								->where('ppv.pp_id', $property->pp_id)
								->where('ppv.is_deleted', $this->config->get('constants.OFF'));
						if ($details->is_combinations == $this->config->get('constants.ACTIVE'))
						{
							$values->leftjoin($this->config->get('tables.PRODUCT_CMB_PROPERTIES').' as pcp', function($pcp)
									{
										$pcp->on('pcp.property_id', '=', 'pv.property_id')
										->on('pcp.value_id', '=', 'ppv.value_id');
									})
									->whereIn('pcp.product_cmb_id', array_keys($details->product_cmbs))
									->selectRaw('pv.value_id,pcp.product_cmb_id,concat(pv.key_value,if(u.unit is not null,concat(\' \',u.unit),\'\')) as value');
						}
						else
						{
							$values->selectRaw('pv.value_id,concat(pv.key_value,if(u.unit is not null,concat(\' \',u.unit),\'\')) as value');
						}
						$property->values = $values->groupby('pv.value_id')->get();
						array_walk($property->values, function(&$value) use($property, $details)
						{
							$value->url = (isset($value->product_cmb_id) && isset($details->product_cmbs[$value->product_cmb_id])) ? $details->product_cmbs[$value->product_cmb_id] : '#';
							$value->selected = ($value->value_id == $property->value_id) ? true : false;
							unset($value->value_id);
						});
						return true;
					}
					else
					{
						return false;
					}
				});
				array_walk($details->properties, function($property)
				{
					unset($property->pp_id);
					unset($property->value_id);
					if ($property->choosable)
					{
						unset($property->values);
					}
					unset($property->type);
					unset($property->choosable);				
				});
				
				unset($details->cmb_sku);
				unset($details->product_cmb);
				unset($details->product_cmbs);
				unset($details->product_cmb_id);
			}
			else
			{
				$details->properties = DB::table($this->config->get('tables.PRODUCT_PROPERTY').' as pp')
						->join($this->config->get('tables.PRODUCT_PROPERTY_KEYS').' as pk', 'pk.property_id', '=', 'pp.property_id')
						->join($this->config->get('tables.PRODUCT_PROPERTY_VALUES').' as ppv', 'ppv.pp_id', '=', 'pp.pp_id')
						->join($this->config->get('tables.PRODUCT_PROPERTY_KEY_VALUES').' as pv', 'pv.value_id', '=', 'ppv.value_id')
						->leftjoin($this->config->get('tables.UNITS').' as u', 'u.unit_id', '=', 'pv.unit_id')
						->where('ppv.is_deleted', $this->config->get('constants.OFF'))
						->where('pp.is_deleted', $this->config->get('constants.OFF'))						
						->selectRaw('pp.pp_id, pk.property, pp.choosable, pk.values_options_type as type, pv.value_id, concat(pv.key_value,if(u.unit is not null,concat(\' \',u.unit),\'\')) as value') 
						->where('pp.product_id', $details->product_id)
						->get();
			}	
		}
		return $details;	
	}
	
	public function productDetailsURL ($product)
    {
        $data = [];
        $data[] = 'product';
        if ($product->category_url_str != 'all')
        {
            $data[] = $product->category_url_str;
        }
        if (isset($product->product_cmb_code))
        {
            $data[] = $product->product_slug.'-'.$product->cmb_sku.'?pid='.$product->product_code.'&cid='.$product->product_cmb_code;
        }
        else
        {
            $data[] = $product->product_slug.'?pid='.$product->product_code;
        }
        return URL::to(implode('/', $data));
    }


}