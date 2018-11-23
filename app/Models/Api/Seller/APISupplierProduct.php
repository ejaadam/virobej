<?php

namespace App\Models\Api\Seller;
use DB;
use Illuminate\Database\Eloquent\Model;
use Config;
use App\Helpers\ShoppingPortal;
use App\Models\Commonsettings;
use App\Models\MemberAuth;
use App\Helpers\ImageLib;
use URL;

class APISupplierProduct extends Model
{

    public function __construct ()
    {
        parent::__construct();
    }

    public function get_supplier_products_list ($arr = array(), $count = false, $categoriesList = false, $brandsList = false)
    {
        extract($arr);
        $products = DB::table(Config::get('tables.SUPPLIER_PRODUCTS_LIST').' as sp')
                ->where('sp.supplier_id', $supplier_id)
                ->where('sp.currency_id', '=', $currency_id)
                ->where('sp.is_deleted', '=', Config::get('constants.OFF'))
                ->where('sp.spi_is_deleted', Config::get('constants.OFF'));

        if ($categoriesList)
        {
            return $products->selectRaw('DISTINCT(sp.category_id),sp.category')
                            ->orderby('sp.category', 'ASC')
                            ->lists('category', 'category_id');
        }
        if ($brandsList)
        {
            return $products->selectRaw('DISTINCT(sp.brand_id),sp.brand_name')
                            ->orderby('sp.brand_name', 'ASC')
                            ->lists('brand_name', 'brand_id');
        }
        if (!empty($search_term))
        {
            $products->where(function($subquery) use($search_term)
            {
                $subquery->where('sp.product_name', 'like', '%'.$search_term.'%');
                $subquery->orWhere('sp.description', 'like', '%'.$search_term.'%');
            });
        }
        if (isset($category_id) && !empty($category_id))
        {
            $products->where('sp.category_id', $category_id);
        }
        if (isset($brand_id) && !empty($brand_id))
        {
            $products->where('sp.brand_id', $brand_id);
        }
        if ($count)
        {
            return $products->count();
        }
        else
        {
            $products->leftjoin(Config::get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'sp.currency_id')
                    ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT').' as sm', 'sm.supplier_product_id', '=', 'sp.supplier_product_id')
                    ->leftjoin(Config::get('tables.VERIFICATION_STATUS_LOOKUPS').' as v', 'v.is_verified', '=', 'sp.is_verified')
                    ->leftjoin(Config::get('tables.LOGIN_STATUS_LOOKUPS').' as ls', 'ls.status_id', '=', 'sp.spi_status')
                    ->leftjoin(Config::get('tables.PRODUCT_CONDITION_LOOKUPS').' as cl', 'cl.condition_id', '=', 'sp.condition_id')
                    ->leftjoin(Config::get('tables.PRODUCT_COUNTRIES').' as pc', 'pc.product_id', '=', 'sp.product_id')
                    ->join(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE').' as spba', function($spba)
                    {
                        $spba->on('spba.brand_id', '=', 'sp.brand_id')
                        ->on('spba.supplier_id', '=', 'sp.supplier_id');
                    })
                    ->join(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE').' as spca', function($spba)
                    {
                        $spba->on('spca.category_id', '=', 'sp.category_id')
                        ->on('spca.supplier_id', '=', 'sp.supplier_id');
                    });
            if (isset($supplier_product_code) && !empty($supplier_product_code))
            {

                $products->where('sp.supplier_product_code', $supplier_product_code);
                return $products->selectRaw('sp.weight, sp.length, sp.width, sp.height, sp.created_by, sp.product_cmb, sp.is_exclusive, sp.is_combinations, sp.is_replaceable, sp.assoc_category_id, sp.redirect_id, sp.condition_id, sp.visiblity_id, sp.supplier_product_id, sp.product_cmb_id, sp.supplier_product_code, sp.product_id, sp.product_name, sp.description, sp.product_code, sp.sku, sp.category_id, sp.brand_id, sp.category, sp.brand_name, sp.supplier_id, sp.pre_order, sp.spi_status, sp.spi_created_on, sp.spi_updated_on, sm.stock_on_hand, sp.mrp_price, sp.price, c.currency_id, c.currency, c.currency_symbol, sp.is_featured, sp.promote_to_homepage, sp.eanbarcode, sp.upcbarcode, pc.country_id as product_country')->first();
            }
            if (isset($supplier_product_id) && !empty($supplier_product_id))
            {
                $products->where('spi.supplier_product_id', $supplier_product_id);
                return $products->selectRaw('sp.weight, sp.length, sp.width, sp.height, sp.created_by, sp.product_cmb, sp.is_exclusive, sp.is_replaceable, sp.assoc_category_id, sp.redirect_id, sp.condition_id, sp.visiblity_id, sp.supplier_product_id, sp.supplier_product_code, sp.product_id, sp.product_name, sp.description, sp.product_code, sp.sku, sp.category_id, sp.brand_id, sp.category, sp.brand_name, sp.supplier_id, sp.pre_order, sp.spi_status, sp.created_on, sp.spi_updated_on, sm.stock_on_hand, sp.mrp_price, sp.price, c.currency_id, c.currency, c.currency_symbol, sp.is_featured, sp.promote_to_homepage, sp.eanbarcode, sp.upcbarcode')->first();
            }
            if (isset($length))
            {
                $products->skip($start)->take($length);
            }
            if (isset($orderby))
            {
                $products->orderby($orderby, $order);
            }
            $products = $products->selectRaw('sp.supplier_product_id, sp.supplier_id, spba.supplier_brand_id, spca.supplier_category_id, sp.product_name, sp.is_combinations, sp.supplier_product_id, sp.supplier_product_code, sp.product_cmb_id, sp.product_id, sp.product_code, sp.sku, sp.category_id, sp.brand_id, sp.category, sp.brand_name, sp.pre_order, sp.spi_status,ls.status,ls.status_class,v.verification,v.verification_class, sp.spi_created_on, sm.stock_on_hand, sp.mrp_price, sp.price, sp.currency_id, c.currency, c.currency_symbol, sp.is_featured, sp.promote_to_homepage')->get();
            $this->imageObj = new ImageLib();
            array_walk($products, function(&$product) use($country_id)
            {
                $product->spi_created_on = date('d-M-Y H:i:s', strtotime($product->spi_created_on));
                $imgdata = ($product->is_combinations) ? ['filter'=>['post_type_id'=>Config::get('constants.POST_TYPE.PRODUCT_CMB'), 'relative_post_id'=>$product->product_cmb_id]] : [];
                $product->imgs = $this->imageObj->get_imgs($product->product_id, $imgdata);
                $product->qty = 1;
                $product->country_id = $country_id;
                //$this->get_product_discounts($product);
                $product->mrp_price = Commonsettings::currency_format(['amt'=>$product->mrp_price, 'currency_symbol'=>$product->currency_symbol, 'currency'=>$product->currency]);
                $product->price = Commonsettings::currency_format(['amt'=>$product->price, 'currency_symbol'=>$product->currency_symbol, 'currency'=>$product->currency]);
                $product->stock_on_hand = $product->stock_on_hand > 0 ? number_format($product->stock_on_hand, 0, '.', ',') : $product->stock_on_hand;
            });
            return $products;
        }
    }

    public function changeProductStatus ($data)
    {
        extract($data);
        $update = [];
        $update['status'] = $status;
        $update['updated_by'] = $account_id;
        $update['updated_on'] = date('Y-m-d H:i:s');
        $query = Db::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                ->where('supplier_product_id', $supplier_product_id)
                ->where('supplier_id', $supplier_id);
        if ($status == Config::get('constants.ACTIVE'))
        {
            $query->where('status', Config::get('constants.INACTIVE'));
        }
        elseif ($status == Config::get('constants.INACTIVE'))
        {
            $query->where('status', Config::get('constants.ACTIVE'));
        }
        return $query->update($update);
    }

    public function deleteProduct ($arr = array())
    {
        extract($arr);
        $update = [];
        $update['is_deleted'] = Config::get('constants.ON');
        $update['updated_by'] = $account_id;
        $update['updated_on'] = date('Y-m-d H:i:s');
        return Db::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                        ->where('supplier_product_id', $supplier_product_id)
                        ->where('supplier_id', $supplier_id)
                        ->update($update);
    }

    public function get_product_discounts (&$product, $with_sales_commission = false)
    {
        $discount_info = [];
        $discount_amt = ['site'=>(object) ['amount'=>0, 'percentage'=>0], 'supplier'=>(object) ['amount'=>0, 'percentage'=>0]];
        $commission_info = (object) [
                    'mrp_price'=>$product->mrp_price,
                    'supplier_price'=>$product->price,
                    'supplier_discount_per'=>0,
                    'supplier_sold_price'=>0,
                    'site_commission_unit'=>NULL,
                    'site_commission_value'=>0,
                    'site_commission_amount'=>0,
                    'site_margin_price'=>0,
                    'site_discount_per'=>0,
                    'site_sold_price'=>0,
                    'partner_margin_price'=>0,
                    'partner_sold_price'=>0,
                    'partner_commission_unit'=>NULL,
                    'partner_commission_value'=>0,
                    'partner_commission_amount'=>0,
                    'price_sub_total'=>0,
                    'site_commission_sub_total'=>0,
                    'shipping_fee'=>0,
                    'supplier_price_sub_total'=>0,
                    'collection_fee'=>0,
                    'fixed_fee'=>0,
                    'partner_margin_sub_total'=>0,
                    'supplier_tax_total'=>0,
                    'partner_tax_total'=>0,
                    'partner_commission_sub_total'=>0
        ];
        if (isset($product->supplier_product_id) && !empty($product->supplier_product_id) && isset($product->qty) && $product->qty > 0)
        {
            $current_date = date('Y-m-d');
            $discounts = DB::table(Config::get('tables.DISCOUNTS').' as d')
                    ->join(Config::get('tables.DISCOUNT_TYPE_LOOKUPS').' as dtl', 'dtl.discount_type_id', '=', 'd.discount_type_id')
                    ->leftJoin(Config::get('tables.DISCOUNT_POSTS').' as dp', 'dp.discount_id', '=', 'd.discount_id')
                    ->leftJoin(Config::get('tables.DISCOUNT_VALUE').' as dv', 'dv.dp_id', '=', 'dp.dp_id')
                    ->where('d.is_deleted', Config::get('constants.OFF'))
                    ->where('dtl.status', Config::get('constants.ACTIVE'))
                    ->where('d.status', Config::get('constants.DISCOUNT_STATUS.PUBLISHED'))
                    ->where(DB::raw('date(d.start_date)'), '<=', $current_date)
                    ->where(DB::raw('date(d.end_date)'), '>=', $current_date)
                    ->groupby('dp.discount_id')
                    ->selectRaw('d.discount_id, d.discount, d.description, d.discount_by, dtl.discount_type, dp.discount_value_type, dv.discount_value, dv.currency_id')
                    ->where(function($value_type) use($product)
                    {
                        $value_type->where(function($per)
                        {
                            $per->where('dp.discount_value_type', Config::get('constants.DISCOUNT_VALUE_TYPE.PERCENTAGE'))
                            ->whereNull('dv.currency_id');
                        })
                        ->orWhere(function($amount) use($product)
                        {
                            $amount->where('dp.discount_value_type', Config::get('constants.DISCOUNT_VALUE_TYPE.FIXED_AMOUNT'))
                            ->where('dv.currency_id', $product->currency_id);
                        });
                    })
                    ->where('d.country_id', '==', $product->country_id)
                    ->where(function($subquery) use($product)
                    {
                        $subquery->where('dp.is_qty_based', Config::get('constants.OFF'))
                        ->orWhere(function($qty_based) use($product)
                        {
                            $qty_based->where('dp.is_qty_based', Config::get('constants.ON'))
                            ->where(function($qt)use($product)
                            {
                                $qt->where('dv.min_qty', '>=', $product->qty)
                                ->where(function($max_qty) use($product)
                                {
                                    $max_qty->where('dv.max_qty', '=', 0)
                                    ->orWhere(function($subquery2)use($product)
                                    {
                                        $subquery2->where('dv.max_qty', '>', 0)
                                        ->where('dv.max_qty', '<=', $product->qty);
                                    });
                                });
                            });
                        });
                    })
                    ->where(function($subquery2) use($product)
                    {
                        $subquery2->whereNull('dp.brand_ids')
                        ->where(function($subquery3) use($product)
                        {
                            $subquery3->whereNotNull('dp.brand_ids')
                            ->whereRaw('find_in_set('.$product->product_id.',dp.brand_ids)');
                        });
                    })
                    ->where(function($subquery2) use($product)
                    {
                        $subquery2->whereNull('dp.caregories_ids')
                        ->where(function($subquery3) use($product)
                        {
                            $subquery3->whereNotNull('dp.caregories_ids')
                            ->whereRaw('find_in_set('.$product->category_id.',dp.caregories_ids)');
                        });
                    })
                    ->where(function($subquery2) use($product)
                    {
                        $subquery2->whereNull('dp.supplier_ids')
                        ->where(function($subquery3) use($product)
                        {
                            $subquery3->whereNotNull('dp.supplier_ids')
                            ->whereRaw('find_in_set('.$product->supplier_id.',dp.supplier_ids)');
                        });
                    })
                    ->where(function($subquery2) use($product)
                    {
                        $subquery2->whereNull('dp.product_ids')
                        ->where(function($subquery3) use($product)
                        {
                            $subquery3->whereNotNull('dp.product_ids')
                            ->whereRaw('find_in_set('.$product->product_id.',dp.product_ids)');
                        });
                    })
                    ->where(function($subquery2) use($product)
            {
                $subquery2->whereNull('dp.product_cmb_ids')
                ->where(function($subquery3)use($product)
                {
                    $subquery3->whereNotNull('dp.product_cmb_ids')
                    ->whereRaw('find_in_set('.$product->product_cmb_id.',dp.product_cmb_ids)');
                });
            });
            $discounts = $discounts->get();
            if (!empty($discounts))
            {
                array_walk($discounts, function(&$discount) use(&$product, &$discount_amt, &$discount_info)
                {
                    if ($discount->discount_by == Config::get('constants.ACCOUNT_TYPE.ADMIN'))
                    {
                        if ($discount->discount_value_type == Config::get('constants.DISCOUNT_VALUE_TYPE.FIXED_AMOUNT'))
                        {
                            $discount_amt['site']->amount+= $discount->discount_value;
                        }
                        else
                        {
                            $discount_amt['site']->percentage+= $discount->discount_value;
                        }
                    }
                    else
                    {
                        if ($discount->discount_value_type == Config::get('constants.DISCOUNT_VALUE_TYPE.FIXED_AMOUNT'))
                        {
                            $discount_amt['supplier']->amount+= $discount->discount_value;
                        }
                        else
                        {
                            $discount_amt['supplier']->percentage+= $discount->discount_value;
                        }
                    }
                    $product->discounts[] = $discount->description;
                    $discount_info[] = $discount;
                });
            }
            $commission_info->supplier_price = $product->price;
            $commission_info->supplier_sold_price = $product->price - ($discount_amt['supplier']->amount + (($product->price / 100) * $discount_amt['supplier']->percentage));
            $commission_info->supplier_discount_per = $product->mrp_price > 0 ? round((($product->mrp_price - $commission_info->supplier_sold_price) / $product->mrp_price) * 100) : 0;
            $commission_info->site_sold_price = $commission_info->supplier_sold_price - ($discount_amt['site']->amount + (($commission_info->supplier_sold_price / 100) * $discount_amt['site']->percentage));
            $commission_info->site_discount_per = $commission_info->supplier_sold_price > 0 ? round((($commission_info->site_sold_price - $commission_info->supplier_sold_price) / $commission_info->supplier_sold_price) * 100) : 0;
            $product->price = $commission_info->site_sold_price;
            $product->discount = $product->price > 0 ? round((($product->mrp_price - $product->price) / $product->mrp_price) * 100) : 0;
            $product->off_per = ($product->discount) ? $product->discount.'% '.trans('product_browse.off') : '';
            $commission_info->price_sub_total = $product->price * $product->qty;
            $product->sub_total = $product->price * $product->qty;
            $commission_info->supplier_price_sub_total = $commission_info->supplier_sold_price * $product->qty;
            $commission_info->partner_margin_sub_total = $commission_info->partner_margin_price * $product->qty;
            if ($with_sales_commission)
            {
                $product->commission_info = $commission_info;
                $product->discount_info = $discount_info;
            }
        }
    }

    public function getAddableProductsList ($arr = array(), $withNew = false)
    { 
        extract($arr);
        /* $product = DB::table(Config::get('tables.PRODUCTS_LIST').' as p')
                ->selectRaw('p.product_id,p.product_cmb_id,p.product_code as id,p.product_name as name,p.eanbarcode,p.upcbarcode,p.is_combinations')
                ->where('p.is_deleted', Config::get('constants.OFF'))
                ->whereNotIn('p.product_code', function($pn) use($supplier_id)
                {
                    $pn->from(Config::get('tables.SUPPLIER_PRODUCTS_LIST'))
                    ->where('supplier_id', $supplier_id)
                    ->where('spi_is_deleted', Config::get('constants.OFF'))
                    ->groupby('product_code')
                    ->lists('product_code');
                })
                ->where(function($s) use($search_term)
        {
            $search_term = '%'.$search_term.'%';
            $s->where('p.eanbarcode', 'like', $search_term)
            ->orWhere('p.upcbarcode', 'like', $search_term);
        }); */
		$product = DB::table(Config::get('tables.PRODUCTS').' as p')
					->leftjoin(Config::get('tables.PRODUCT_DETAILS').' as pd', 'pd.product_id', '=', 'p.product_id')
					->selectRaw('p.product_id, pd.product_cmb_id, pd.product_code as id, p.product_name as name, pd.eanbarcode, pd.upcbarcode, p.is_combinations')
					->where('p.is_deleted', Config::get('constants.OFF'))
					->whereNotIn('pd.product_code', function($pn) use($supplier_id)
					{
						$pn->from(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
							->where('supplier_id', $supplier_id)
							->where('is_deleted', Config::get('constants.OFF'))
							->groupby('supplier_product_code')
							->select('supplier_product_code')->get();
					})
					->where(function($s) use($search_term)
					{
						$search_term = '%'.$search_term.'%';
						$s->where('pd.eanbarcode', 'like', $search_term)
						->orWhere('pd.upcbarcode', 'like', $search_term);
					});
        $products = $product->get();        
        array_walk($products, function($product)
        {
            $ids = explode(',', $product->id);
            $imgdata = ($product->is_combinations) ? ['filter'=>['post_type_id'=>Config::get('constants.POST_TYPE.PRODUCT_CMB'), 'relative_post_id'=>isset($product->product_cmb_id) ? $product->product_cmb_id : null]] : [];
            $img = ImageLib::get_imgs($product->product_id, $imgdata);
            $product->img = is_array($img) ? $img[0]->img_path : $img->img_path;
            unset($product->is_combinations);
            unset($product->product_id);
            unset($product->product_cmb_id);
        });
        if ($withNew)
        {
            $products[] = (object) ['id'=>'new', 'name'=>'Add New', 'img'=>'', 'eanbarcode'=>$search_term, 'upcbarcode'=>$search_term, 'is_combinations'=>false];
        }
        return $products;
    }

    public function getCombinationsList ($arr = array(), $count = false)
    {
        extract($arr);
        if (!empty($product_id) && !empty($supplier_product_id))
        {
            $query = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi')
                    ->join(Config::get('tables.PRODUCT_COMBINATIONS').' as pc', function($pc)
                    {
                        $pc->on('pc.product_cmb_id', '=', 'spi.product_cmb_id')
                        ->where('pc.is_deleted', '=', Config::get('constants.OFF'));
                    })
                    ->where('spi.supplier_id', $supplier_id)
                    ->where('spi.product_id', $product_id)
                    ->where('spi.is_deleted', Config::get('constants.OFF'));
            if (!empty($search_term))
            {
                $query->where('spi.product_cmb_code', 'like', '%'.$search_term.'%');
            }
            if (isset($start) && isset($length))
            {
                $query->skip($start)->take($length);
            }
            if (isset($orderby))
            {
                $query->orderby($orderby, $order);
            }
            else
            {
                $query->orderBy('spi.created_on', 'desc');
            }
            if ($count)
            {
                return $query->count();
            }
            else
            {
                $combinations = $query->leftJoin(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT').' as sm', 'sm.supplier_product_id', '=', 'spi.supplier_product_id')
                                ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_PRICE').' as spp', function($join)use($currency_id)
                                {
                                    $join->on('spp.supplier_id', '=', 'spi.supplier_id');
                                    $join->on('spp.product_id', '=', 'spi.product_id');
                                    $join->where('spp.currency_id', '=', $currency_id);
                                })
                                ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_CMB_PRICE').' as pcp', function($join)use($currency_id)
                                {
                                    $join->on('pcp.supplier_id', '=', 'spi.supplier_id')
                                    ->on('pcp.product_cmb_id', '=', 'spi.product_cmb_id')
                                    ->on('pcp.currency_id', '=', 'spp.currency_id');
                                })
                                ->leftjoin(Config::get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'spp.currency_id')
                                ->select('pcp.impact_on_price', 'spi.status', 'spi.product_id', 'sm.stock_on_hand', 'spi.created_on', 'spi.product_cmb_id', 'cur.currency')->get();
                $impact_on_price_charge = array();
                array_walk($combinations, function(& $combination)
                {
                    $combination->created_on = date('d-M-Y H:i:s', strtotime($combination->created_on));
                    $combination->currency_with_charge = $combination->impact_on_price.' '.$combination->currency;
                    $combination->actions = [];
                    $combination->actions[] = [
                        'url'=>'',
                        'title'=>'Configure',
                        'class'=>'edit_combination_product',
                        'data'=>[
                            'product_cmb_id'=>$combination->product_cmb_id,
                            'product_id'=>$combination->product_id
                        ]
                    ];
                    if ($combination->status == Config::get('constants.ACTIVE'))
                    {
                        $combination->actions[] = [
                            'title'=>'Inactive',
                            'url'=>URL::route('api.v1.supplier.products.combinations.change-status'),
                            'data'=>[
                                'product_cmb_id'=>$combination->product_cmb_id,
                                'product_id'=>$combination->product_id,
                                'status'=>Config::get('constants.INACTIVE')
                            ]
                        ];
                    }
                    else if ($combination->status == Config::get('constants.INACTIVE'))
                    {
                        $combination->actions[] = [
                            'title'=>'Active',
                            'url'=>URL::route('api.v1.supplier.products.combinations.change-status'),
                            'data'=>[
                                'product_cmb_id'=>$combination->product_cmb_id,
                                'product_id'=>$combination->product_id,
                                'status'=>Config::get('constants.ACTIVE')
                            ]
                        ];
                    }
                    $combination->actions[] = [
                        'title'=>'Delete',
                        'url'=>URL::route('api.v1.supplier.products.combinations.delete'),
                        'data'=>[
                            'product_cmb_id'=>$combination->product_cmb_id,
                            'product_id'=>$combination->product_id
                        ]
                    ];
                });
                return $combinations;
            }
        }
    }

    public function combination_status_chages ($data = array())
    {
        extract($data);
        $update = [];
        $update['status'] = $status;
        $update['updated_by'] = $account_id;
        $update['updated_on'] = date('Y-m-d H:i:s');
        $query = Db::table(Config::get('tables.PRODUCT_COMBINATIONS'))
                ->where('product_cmb_id', $product_cmb_id);
        if ($status == Config::get('constants.ACTIVE'))
        {
            $query->where('status', Config::get('constants.INACTIVE'));
        }
        elseif ($status == Config::get('constants.INACTIVE'))
        {
            $query->where('status', Config::get('constants.ACTIVE'));
        }
        if ($query->update($update))
        {
            $this->get_combination_product_by_id($product_id);
            return $res;
        }
        return false;
    }

    public function delete_products_combinations ($data = array())
    {
        extract($data);
        $product['is_deleted'] = $is_deleted;
        $product['updated_by'] = $updated_by;
        if (!empty($product_cmb_id))
        {
            return DB::table(Config::get('tables.PRODUCT_COMBINATIONS'))
                            ->where('product_cmb_id', $product_cmb_id)
                            ->update($product);
        }
        return false;
    }

    public function get_combination_product_by_id ($product_id)
    {
        if (!empty($product_id))
        {
            return DB::table(Config::get('tables.PRODUCTS').' as p')
                            ->where('p.product_id', $product_id)
                            ->where('p.is_deleted', Config::get('constants.OFF'))
                            ->update(['p.is_combinations'=>DB::raw('(select if(count(pc.product_id)>0,1,0) from '.Config::get('tables.PRODUCT_COMBINATIONS').' as `pc` where `pc`.`product_id` = p.product_id and `pc`.`is_deleted` = '.Config::get('constants.OFF').')')]);
        }
        return false;
    }

    public function getSupplierProductPrices ($arr = array(), $count = false)
    {
        extract($arr);
        $prices = DB::table(Config::get('tables.SUPPLIER_PRODUCT_PRICE').' as pp')
                ->leftjoin(Config::get('tables.PRODUCT_MRP_PRICE').' as mrp', function($mrp)
                {
                    $mrp->on('mrp.product_id', '=', 'pp.product_id');
                    $mrp->on('mrp.currency_id', '=', 'pp.currency_id');
                })
                ->leftjoin(Config::get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'pp.currency_id')
                ->where('pp.is_deleted', Config::get('constants.OFF'))
                ->where('pp.product_id', $product_id)
                ->where('pp.supplier_id', $supplier_id);
        if (isset($orderby))
        {
            $prices->orderby($orderby, $order);
        }
        if ($count)
        {
            return $prices->count();
        }
        else
        {
            $prices = $prices->selectRaw('pp.spp_id,mrp.mrp_price,pp.price,c.currency_id, c.currency,c.currency_symbol')
                    ->get();
            array_walk($prices, function(&$price)
            {
                $price->actions = [];
                $price->actions[] = [
                    'title'=>'Configure',
                    'class'=>'edit-product-price',
                    'data'=>[
                        'currency_id'=>$price->currency_id,
                        'currency'=>$price->currency,
                        'spp_id'=>$price->spp_id,
                    ]
                ];
//                $price->actions[] = [
//                    'title'=>'Delete',
//                    'url'=>URL::route('api.v1.supplier.products.price.delete'),
//                    'data'=>[
//                        'currency_id'=>$price->currency_id,
//                        'spp_id'=>$price->spp_id,
//                    ]
//                ];
            });
            return $prices;
        }
    }

    public function saveProductCmbPrice ($arr = array())
    {
        extract($arr);
        $spcp = $spp['spcp'];
        if (isset($spcp) && !empty($spcp))
        {
            if ($spcp_id = DB::table(Config::get('tables.SUPPLIER_PRODUCT_CMB_PRICE'))
                    ->where('supplier_id', $spcp['supplier_id'])
                    ->where('product_cmb_id', $spcp['product_cmb_id'])
                    ->where('currency_id', $spcp['currency_id'])
                    ->pluck('spcp_id'))
            {
                return DB::table(Config::get('tables.SUPPLIER_PRODUCT_CMB_PRICE'))
                                ->where('spcp_id', $spcp_id)
                                ->update(['impact_on_price'=>$spcp['impact_on_price']]);
            }
            else
            {
                return DB::table(Config::get('tables.SUPPLIER_PRODUCT_CMB_PRICE'))
                                ->insertGetId($spcp);
            }
        }
        return false;
    }

    public function product_img_details ($data = array())
    {
        if (!empty($data['combination_id']) && isset($data['combination_id']))
        {
            $combination = true;
            unset($data['product_id']);
        }
        else
        {
            $combination = false;
        }
        $result = DB::table(Config::get('tables.IMGS').' as img')
                ->leftJoin(Config::get('tables.IMG_SETTINGS').' as ims', function($subquery)
        {
            $subquery->on('ims.post_type_id', '=', 'img.post_type_id')
            ->on('ims.img_type_id', '=', 'img.img_type');
        });
        if ($combination == true)
        {
            $result->join(Config::get('tables.IMG_FILTERS').' as imf', function($subquery)use($data)
            {
                $subquery->on('imf.img_id', '=', 'img.img_id')
                        ->where('imf.post_type_id', '=', Config::get('constants.POST_TYPE.PRODUCT_CMB'))
                        ->where('imf.relative_post_id', '=', $data['combination_id'])
                        ->where('imf.is_deleted', '=', Config::get('constants.OFF'));
            });
            $result->orderBy('imf.sort_order');
        }
        else
        {
            $result->orderBy('img.sort_order');
        }
        $result->where('img.is_deleted', Config::get('constants.OFF'))
                ->where('img.status_id', Config::get('constants.ON'));
        if (isset($data['product_id']) && !empty($data['product_id']))
        {
            $res = $result->where('img.relative_post_id', $data['product_id'])
                    ->select('img.*', 'ims.file_path')
                    ->get();
            return $res;
        }
        if (isset($combination) && !empty($combination))
        {
            $res = $result->select('img.img_path', 'img.relative_post_id', 'img.img_file', 'img.img_id', 'imf.sort_order', 'ims.file_path')
                    ->get();
            return $res;
        }
        if (isset($data['image_id']) && !empty($data['image_id']))
        {
            $res = $result->where('img_id', $data['image_id'])
                    ->first();
            return $res;
        }
    }

    public function getDeductions ($arr = array())
    {
        extract($arr);
        $deductinos = [];
        $deducation_amounts = [];
        if (!empty($supplier_product_id) && !isset($is_shipping_beared))
        {
            $is_shipping_beared = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                    ->where('supplier_product_id', $supplier_product_id)
                    ->pluck('is_shipping_beared');
        }
        if (!empty($product_id) && !empty($is_shipping_beared))
        {
            $weight_slab_id = DB::table(Config::get('tables.PRODUCT_DETAILS').' as pt')
                    ->where('pt.product_id', $product_id)
                    ->selectRaw('(select ws.weight_slab_id from '.Config::get('tables.PRODUCT_WEIGHT_SLAB').' as ws where ws.min_grams <= pt.weight and (ws.max_grams is null or (ws.max_grams is not null && ws.max_grams >= pt.weight)) limit 1) as weight_slab_id')
                    ->first();
            if (!empty($weight_slab_id))
            {

                $data['default_logistic_id'] = DB::table(Config::get('tables.ACCOUNT_LOGISTICS').' as l')
                        ->where('l.is_default', Config::get('constants.ON'))
                        ->pluck('l.logistic_id');
                $res = DB::table(Config::get('tables.SUPPLIER_PRODUCT_SHIPPMENT_SETTINGS').' as ss')
                        ->leftJoin(Config::get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'ss.currency_id')
                        ->leftJoin(Config::get('tables.PRODUCT_WEIGHT_SLAB').' as ws', 'ws.weight_slab_id', '=', 'ss.weight_slab_id')
                        ->where('ss.logistic_id', $data['default_logistic_id'])
                        ->where('ss.country_id', $country_id)
                        ->where(function($sc) use($supplier_id)
                        {
                            $sc->where('supplier_id', $supplier_id)
                            ->orWhereNull('supplier_id');
                        })
                        ->where('ss.weight_slab_id', $weight_slab_id->weight_slab_id)
                        ->select('ss.delivery_charge', 'ss.zone_delivery_charges', 'ss.national_delivery_charges', 'cur.currency_id', 'cur.currency_symbol', 'cur.currency', 'ws.weight_slab_title')
                        ->first();
                if ($res)
                {
                    $deducation_amounts[] = $res->delivery_charge;
                    $deductions[] = ['title'=>'Shipping Fee', 'info'=>'Local Shipping ,weight '.$res->weight_slab_title, 'value'=>Commonsettings::currency_format(['amt'=>$res->delivery_charge, 'currency'=>$res->currency, 'currency_symbol'=>$res->currency_symbol])];
                }
            }
        }
        $commission_info = (object) [
                    'site_commission_unit'=>NULL,
                    'site_commission_value'=>0,
                    'site_commission_amount'=>0,
                    'site_commission_sub_total'=>0,
                    'partner_commission_amount'=>0,
                    'partner_commission_sub_total'=>0
        ];
        $commission = DB::table(Config::get('tables.SUPPLIER_COMMISSIONS_SETTINGS'))
                ->where('supplier_id', $supplier_id)
                ->where(function($cv) use($currency_id)
                {
                    $cv->where('commission_unit', Config::get('constants.COMMISSION_UNIT.PERCENTAGE'))
                    ->orWhere(function($cvor) use($currency_id)
                    {
                        $cvor->where('commission_unit', Config::get('constants.COMMISSION_UNIT.FIXED_RATE'))
                        ->where('currency_id', $currency_id);
                    });
                })
                ->selectRaw('commission_type,commission_unit,commission_value')
                ->first();
        if ($commission)
        {
            if ($commission_info == Config::get('constants.COMMISSION_TYPE.FIXED'))
            {
                $commission_info->site_commission_unit = $commission->commission_unit;
                $commission_info->site_commission_value = (float) $commission->commission_value;
            }
            elseif ($commission->commission_type == Config::get('constants.COMMISSION_TYPE.FLEXIBLE'))
            {
                $commission = DB::table(Config::get('tables.SUPPLIER_FLEXIBLE_COMMISSIONS'))
                        ->where('relation_id', $product_id)
                        ->where('supplier_id', $supplier_id)
                        ->where('post_type_id', Config::get('constants.POST_TYPE.PRODUCT'))
                        ->where(function($cv) use($currency_id)
                        {
                            $cv->where('commission_unit', Config::get('constants.COMMISSION_UNIT.PERCENTAGE'))
                            ->orWhere(function($cvor) use($currency_id)
                            {
                                $cvor->where('commission_unit', Config::get('constants.COMMISSION_UNIT.FIXED_RATE'))
                                ->where('currency_id', $currency_id);
                            });
                        })
                        ->selectRaw('commission_unit,commission_value')
                        ->first();
                if ($commission)
                {
                    $commission_info->site_commission_unit = $commission->commission_unit;
                    $commission_info->site_commission_value = (float) $commission->commission_value;
                }
                else
                {
                    $category_id = B::table(Config::get('tables.SUPPLIER_FLEXIBLE_COMMISSIONS'))
                            ->where('product_id', $product_id)
                            ->pluck('category_id');
                    $commission = $this->getCategoryCommission(['category_id'=>$category_id, 'supplier_id'=>$supplier_id, 'currency_id'=>$currency_id]);
                    if ($commission)
                    {
                        $commission_info->site_commission_unit = $commission->commission_unit;
                        $commission_info->site_commission_value = (float) $commission->commission_value;
                    }
                }
            }
        }
        $amount = $commission_info->site_commission_unit == Config::get('constants.COMMISSION_UNIT.PERCENTAGE') ? (($price / 100) * $commission_info->site_commission_value) : $commission_info->site_commission_value;
        $deducation_amounts[] = $amount;
        $amount = Commonsettings::currency_format(['amt'=>$commission_info->site_commission_value, 'currency_id'=>$currency_id]);
        $deductions[] = $commission_info->site_commission_unit == Config::get('constants.COMMISSION_UNIT.PERCENTAGE') ? ['title'=>'Commission', 'info'=>number_format($commission_info->site_commission_value, 0, '.', ',').'%', 'value'=>$amount] : ['title'=>'Commission', 'info'=>Commonsettings::currency_format(['amt'=>$commission_info->site_commission_value, 'currency_id'=>$currency_id]).' Fixed Commission', 'value'=>$amount];
        $fees = DB::table(Config::get('tables.SYSTEM_FEES_SETTING').' as sfs')
                ->leftJoin(Config::get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'sfs.currency_id')
                ->where('sfs.supplier_id', $supplier_id)
                ->where('sfs.country_id', $country_id)
                ->where(function($c) use($currency_id)
                {
                    $c->where('sfs.fee_unit', Config::get('constants.FEE_UNIT.PERCENTAGE'))
                    ->orWhere(function($c2) use($currency_id)
                    {
                        $c2->where('sfs.fee_unit', Config::get('constants.FEE_UNIT.FIXED_RATE'))
                        ->where('sfs.currency_id', $currency_id);
                    });
                })
                ->where('sfs.is_deleted', Config::get('constants.OFF'))
                ->where('sfs.status', Config::get('constants.ON'))
                ->select('sfs.fee_title as title', 'sfs.fee_unit', 'sfs.fee_value', 'cur.currency', 'cur.currency_symbol')
                ->get();
        if (!empty($fees))
        {
            array_walk($fees, function(&$fee) use($price, $currency_id, &$deducation_amounts)
            {
                $amount = ($fee->fee_unit == Config::get('constants.FEE_UNIT.PERCENTAGE') ? (($price / 100) * $fee->fee_value) : $fee->fee_value);
                $deducation_amounts[] = $amount;
                $fee->value = Commonsettings::currency_format(['amt'=>$amount, 'currency_id'=>$currency_id]);
                $fee->info = ($fee->fee_unit == Config::get('constants.FEE_UNIT.PERCENTAGE') ? $fee->fee_value.'% of Order item Price' : $fee->value);
                unset($fee->fee_unit);
                unset($fee->fee_value);
                unset($fee->currency);
                unset($fee->currency_symbol);
            });
            $deductions = array_merge($deductions, $fees);
        }
        if (!empty($country_id))
        {
            $geo_zone_id = DB::table(Config::get('tables.GEO_ZONE_LOCATIONS'))
                    ->where('country_id', $country_id)
                    ->pluck('geo_zone_id');
        }
        if (!empty($product_id))
        {
            $category_id = DB::table(Config::get('tables.PRODUCTS'))
                    ->where('product_id', $product_id)
                    ->pluck('category_id');
        }
        $info = [];
        $tax_info = (object) ['total_tax_per'=>0, 'total_tax_amount'=>0, 'taxes'=>[]];
        $date = isset($date) ? date('Y-m-d', strtotime($date)) : date('Y-m-d');
        $taxes = DB::table(Config::get('tables.TAXES').' as t')
                ->join(Config::get('tables.TAX_VALUES').' as tv', function($tv) use($category_id)
                {
                    $tv->on('tv.tax_id', '=', 't.tax_id')
                    ->where('tv.post_type_id', '=', Config::get('constants.POST_TYPE.CATEGORY'))
                    ->where('tv.relative_id', '=', $category_id)
                    ->where('tv.is_deleted', '=', Config::get('constants.OFF'));
                })
                ->where(function($range) use($price)
                {
                    $range->where('tv.is_range', Config::get('constants.OFF'))
                    ->orWhere(function($range1) use($price)
                    {
                        $range1->where('tv.is_range', Config::get('constants.ON'))
                        ->where('tv.range_start_from', '>=', $price)
                        ->where('tv.range_end_to', '<=', $price);
                    });
                })
                ->where('t.start_date', '<=', $date)
                ->where('t.end_date', '>=', $date)
                ->where('t.is_deleted', Config::get('constants.OFF'))
                ->where('t.status', Config::get('constants.ACTIVE'))
                ->select('t.tax', 't.value_type', 'tv.tax_value', 't.currency_id')
                ->get();
        $tax_info->taxes = $taxes;
        array_walk($taxes, function($tax) use(&$tax_info, &$info, $currency_id)
        {
            if ($tax->value_type == Config::get('constants.TAX_VALUE_TYPE.PERCENTAGE'))
            {
                $tax_info->total_tax_per+=$tax->tax_value;
                $info[] = $tax->tax_value.'% '.$tax->tax;
            }
            elseif (isset($currency_id) && !empty($currency_id))
            {
                $rate = $this->get_exchange_rate($tax->currency_id, $currency_id);
                $tax_info->total_tax_amount+=($rate * $tax->tax_value);
                $info[] = Commonsettings::currency_format(['amt'=>$tax->tax_value, 'currency_id'=>$tax->currency_id]).$tax->tax;
            }
        });
        $tot_deduction = array_sum($deducation_amounts);
        $info[] = ' ('.Commonsettings::currency_format(['amt'=>$tot_deduction, 'currency_id'=>$currency_id]).')';
        $tax_info->tax_per = $tax_info->total_tax_per + (($tax_info->total_tax_amount / ($tot_deduction > 0 ? $tot_deduction : 1)) * 100);
        $tax = (($tot_deduction / 100) * $tax_info->tax_per);
        $tax = round($tax, 2);
        $deducation_amounts[] = $tax;
        $deductions[] = ['title'=>'Tax', 'info'=>implode(',', $info), 'value'=>Commonsettings::currency_format(['amt'=>$tax, 'currency_id'=>$currency_id])];
        $deductions[] = ['title'=>'Settlement Value', 'info'=>'Amount credited to you', 'value'=>Commonsettings::currency_format(['amt'=>($price - $tot_deduction), 'currency_id'=>$currency_id])];
        return $deductions;
    }

    public function getCategoryCommission ($arr = array())
    {
        extract($arr);
        $commission = DB::table(Config::get('tables.SUPPLIER_FLEXIBLE_COMMISSIONS'))
                ->where('relation_id', $category_id)
                ->where('supplier_id', $supplier_id)
                ->where('post_type_id', Config::get('constants.POST_TYPE.CATEGORY'))
                ->where(function($cv) use($currency_id)
                {
                    $cv->where('commission_unit', Config::get('constants.COMMISSION_UNIT.PERCENTAGE'))
                    ->orWhere(function($cvor) use($currency_id)
                    {
                        $cvor->where('commission_unit', Config::get('constants.COMMISSION_UNIT.FIXED_RATE'))
                        ->where('currency_id', $currency_id);
                    });
                })
                ->selectRaw('commission_unit,commission_value')
                ->first();
        if ($commission)
        {
            return $commission;
        }
        else
        {
            $arr['category_id'] = DB::table(Config::get('tables.PRODUCT_CATEGORIES').' as pc')
                    ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as pcp', 'pcp.category_id', '=', 'pc.category_id')
                    ->where('c.category_id', $category_id)
                    ->where('c.is_deleted', Config::get('constants.OFF'))
                    ->pluck('cp.parent_category_id');
            return !empty($arr['category_id']) ? $this->getCategoryCommission($arr) : false;
        }
    }

    public function getProductPropertiesValuesChecked ($product_id)
    {
        $data = array();
        $product_properties = DB::table(Config::get('tables.PRODUCT_PROPERTY'))
                ->where('product_id', $product_id)
                ->where('is_deleted', Config::get('constants.OFF'))
                ->select('pp_id', 'property_id', 'choosable', 'key_value')
                ->get();
        $pp_ids = $data['properties'] = $data['choosable'] = $data['key_value'] = [];
        array_walk($product_properties, function($v) use(&$pp_ids, &$data)
        {
            $pp_ids[] = $v->pp_id;
            $data['properties'][] = $v->property_id;
            if ($v->choosable)
            {
                $data['choosable'][] = $v->property_id;
            }
            if (!empty($v->key_value))
            {
                $data['key_value'][$v->property_id] = $v->key_value;
            }
        });		
        $data['values'] = DB::table(Config::get('tables.PRODUCT_PROPERTY_VALUES'))
                ->whereIn('pp_id', $pp_ids)
                ->where('is_deleted', Config::get('constants.OFF'))
                ->lists('value_id');
        return $data;
    }

    public function getProductProperiesForChecktree ($category_id, $parent_property_id = NULL)
    {	
        $query = DB::table(Config::get('tables.PRODUCT_CATEGORY_PROPERTIES').' as cp')
                ->leftjoin(Config::get('tables.PRODUCT_PROPERTY_KEYS').' as pk', 'pk.property_id', '=', 'cp.property_id')
                ->where('cp.category_id', $category_id)
                ->where('cp.is_deleted', Config::get('constants.OFF'))
                ->selectRaw('pk.property_id as id, pk.property as label,cp.category_property_id,pk.property_type')
                ->orderBy('label', 'asc');
        if (empty($parent_property_id))
        {
            $query->whereNull('pk.parent_property_id');
        }
        else
        {
            $query->where('pk.parent_property_id', $parent_property_id);
        }
        $properties = $query->get();
        $with_data = array();
        array_walk($properties, function($property) use(&$with_data, $category_id)
        {
            $property->children = $this->getProductProperiesForChecktree($category_id, $property->id);
            $with_data[] = $property;
        }, $properties);
        return $with_data;
    }

    public function get_product_combination_list ($data)
    {
        if (isset($data['supplier_product_id']))
        {
            $res = $this->get_supplier_products_list($data);
            if ($res)
            {
                $data['product_id'] = $res->product_id;
                $data['supplier_id'] = $res->supplier_id;
            }
        }
        if (isset($data['product_code']))
        {
            $res = $this->get_supplier_products_list($data);
            if ($res)
            {
                $data['product_id'] = $res->product_id;
                $data['supplier_id'] = $res->supplier_id;
            }
        }
        if (!empty($data['product_id']) && !empty($data['product_id']))
        {
            $res = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi')
                    ->Join(Config::get('tables.PRODUCT_COMBINATIONS').' as pc', 'pc.product_cmb_id', '=', 'spi.product_cmb_id')
                    ->where('spi.product_id', $data['product_id'])
                    ->where('spi.supplier_id', $data['supplier_id'])
                    ->where('spi.is_deleted', Config::get('constants.OFF'))
                    ->select('pc.product_cmb_id')
                    ->get();
            if (!empty($res))
            {
                return $res;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    public function get_meta_info ($product_id)
    {
        return Db::table(Config::get('tables.META_INFO').' as mi')
                        ->where('mi.relative_post_id', $product_id)
                        ->select('mi.description', 'mi.meta_keys')
                        ->first();
    }

    public function getProductCategories ($arr = array(), $count = false, $ids_only = false)
    {
        extract($arr);
        $categories = DB::table(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE').' as sc')
                ->where('sc.is_deleted', Config::get('constants.OFF'))
                ->where('sc.supplier_id', $supplier_id);
        if ((isset($search_term) && !empty($search_term)) || !$count)
        {
            $categories->leftjoin(Config::get('tables.PRODUCT_CATEGORIES').' as pc', 'pc.category_id', '=', 'sc.category_id')
                    ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as pcp', 'pcp.category_id', '=', 'pc.category_id')
                    ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES').' as ppc', 'ppc.category_id', '=', 'pcp.parent_category_id');
        }
        if (isset($search_term) && !empty($search_term))
        {
            $categories->where(function($s) use($search_term)
            {
                $search_term = '%'.$search_term.'%';
                $s->where('pc.category', 'like', $search_term)
                        ->orWhere('ppc.category', 'like', $search_term);
            });
        }
        if (isset($start) && isset($length))
        {
            $categories->skip($start)->take($length);
        }
        if (isset($orderby))
        {
            $categories->orderby($orderby, $order);
        }
        if ($count)
        {
            return $categories->count();
        }
        else if ($ids_only)
        {
            return $categories->select('sc.category_id')->lists('sc.category_id');
        }
        else
        {
            $categories = $categories->leftjoin(Config::get('tables.LOGIN_STATUS_LOOKUPS').' as ls', 'ls.status_id', '=', 'sc.status')
                    ->selectRaw('sc.supplier_category_id as id,pc.category, sc.updated_on,ls.status_id,ls.status_class,ls.status,pcp.parent_category_id,pc.category_id, ppc.category as parent_category')
                    ->get();
            if (!empty($categories))
            {
                array_walk($categories, function(&$category)
                {
                    $category->updated_on = date('d-M-Y H:i:s', strtotime($category->updated_on));
                    $category->actions = [];
                    if ($category->status_id == Config::get('constants.INACTIVE'))
                    {
                        $category->actions[] = [
                            'title'=>'Active',
                            'url'=>URL::route('api.v1.seller.catalog.categories.change-status'),
                            'data'=>[
                                'id'=>$category->id,
                                'status'=>Config::get('constants.ACTIVE')
                            ],
                        ];
                    }
                    if ($category->status_id == Config::get('constants.ACTIVE'))
                    {
                        $category->actions[] = [
                            'title'=>'Inactive',                            
                            'url'=>URL::to('api/v1/seller/catalog/categories/change-status'),
                            'data'=>[
                                'id'=>$category->id,
                                'status'=>Config::get('constants.INACTIVE')
                            ],
                        ];
                    }
                    $category->actions[] = [
                        'title'=>'Delete',                        
						'url'=>URL::to('api/v1/seller/catalog/categories/delete'),
                        'data'=>[
                            'confirm'=>'Are you sure, you wants to delete?',
                            'id'=>$category->id
                        ],
                    ];
                });
            }
            return $categories;
        }
    }

    public function format_categories ($parent_category_id, $data, $with_childrens = true)
    {
        // array_filter is used to select the particular parent_category_id
        $categories = array_filter($data, function($c) use($parent_category_id)
        {
            return $parent_category_id == $c->parent_category_id;
        });
        if ($with_childrens)
        {
            $with_data = array();
            array_map(function($category) use(&$with_data, $data, $parent_category_id, $with_childrens)
            {
                $category->sub_categories = $this->format_categories($category->category_id, $data);
                $with_data[] = $category;
            }, $categories);
            return $with_data;
        }
        return $categories;
    }

    public function get_tags ($product_id)
    {
        return DB::table(Config::get('tables.PRODUCT_TAG').' as pt')
                        ->join(Config::get('tables.TAG').' as tag', 'pt.tag_id', '=', 'tag.tag_id')
                        ->where('pt.product_id', $product_id)
                        ->where('pt.is_deleted', Config::get('constants.OFF'))
                        ->select('tag.tag_id', 'tag.tag_name')
                        ->get();
    }

    public function is_ownshipment ($supplier_id)
    {
        return DB::table(Config::get('tables.SUPPLIER_PREFERENCE'))
                        ->where('supplier_id', $supplier_id)
                        ->pluck('is_ownshipment');
    }

    public function save_supplier_product ($arr = array())
    {	//return $arr;
        $supplier_product_code = '';
        $supplier_product = (isset($arr['supplier_product']) && !empty($arr['supplier_product'])) ? $arr['supplier_product'] : $arr;
        if (isset($supplier_product['product_id']))
        {
            $arr['product_id'] = $supplier_product['product_id'];
        }
        if (isset($supplier_product['product_cmb_id']))
        {
            $arr['product_cmb_id'] = $supplier_product['product_cmb_id'];
        }
        $product = DB::table(Config::get('tables.PRODUCTS').' as p')
					->join(Config::get('tables.PRODUCT_DETAILS').' as pd', 'pd.product_id', '=', 'p.product_id')
                ->where('p.product_id', $supplier_product['product_id']);
        if (isset($supplier_product['product_cmb_id']) && !empty($supplier_product['product_cmb_id']))
        {
            $product->where('pd.product_cmb_id', $supplier_product['product_cmb_id']);
        }
        else
        {
            $product->whereNULL('pd.product_cmb_id');
        }
        $product = $product->select('p.category_id', 'p.brand_id', 'pd.product_code')->first();
        $supplier_product['updated_by'] = $supplier_product['account_id'];
        unset($supplier_product['account_id']);
        if (isset($product->brand_id))
        {
            $this->saveSupplierBrand(['supplier_brand'=>['supplier_id'=>$arr['supplier_id'], 'brand_id'=>$product->brand_id], 'account_id'=>$arr['account_id']]);
        }
        if (isset($product->category_id))
        {
            $this->saveSupplierCategory(['supplier_category'=>['supplier_id'=>$arr['supplier_id'], 'category_id'=>$product->category_id]]);
        }
        if (isset($supplier_product['spp']))
        {
            $spp = $supplier_product['spp'];
            unset($supplier_product['spp']);
        }
        else
        {
            $spp = ['supplier_id'=>$arr['supplier_id'], 'product_id'=>$supplier_product['product_id']];
        }
        if (isset($supplier_product['product_cmb_id']))
        {
            $spp['product_cmb_id'] = $supplier_product['product_cmb_id'];
        }
        $sp = DB::table(Config::get('tables.SUPPLIER_PRODUCTS_LIST'))
                ->where('supplier_id', $supplier_product['supplier_id'])
                ->where('product_id', $supplier_product['product_id']);
        if (isset($supplier_product['product_cmb_id']) && !empty($supplier_product['product_cmb_id']))
        {
            $sp->where('product_cmb_id', $supplier_product['product_cmb_id']);
        }
        $supplier_product_info = $sp->select('supplier_product_id', 'supplier_product_code')->first();
        if (!empty($supplier_product_info))
        {
            $supplier_product_id = $supplier_product_info->supplier_product_id;
            $supplier_product_code = $supplier_product_info->supplier_product_code;
        }

        if ((isset($supplier_product_id) && !empty($supplier_product_id)))
        {
            $arr['updated_on'] = date('Y-m-d H:i:s');
            $result = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                    ->where('supplier_product_id', $supplier_product_id)
                    ->update($supplier_product);
        }
        else
        {
            $supplier_product['created_on'] = date('Y-m-d H:i:s');
            if ($supplier_product_id = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))->insertGetId($supplier_product))
            {
                $supplier_product_code = Config::get('constants.SUPPLIER_PRODUCT_CODE_PREFIX').$supplier_product_id;
                DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                        ->where('supplier_product_id', $supplier_product_id)
                        ->update(array('supplier_product_code'=>$supplier_product_code));
            }
        }
        if (!empty($supplier_product_id))
        {
            $supplier_product['supplier_product_id'] = $supplier_product_id;
            $spp['currency_id'] = $arr['currency_id'];
            $supplier_product['transaction_type'] = 1;
            $this->updateStock($supplier_product);
            $this->saveProcuctPrice(['spp'=>$spp]);
            return $supplier_product_code;
        }
        return false;
    }

    public function saveSupplierBrand ($arr = array())
    {
        extract($arr);
        if (isset($supplier_brand) && !empty($supplier_brand))
        {
            if (isset($supplier_brand_id) && !empty($supplier_brand_id) || $supplier_brand_id = DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                    ->where($supplier_brand)
                    ->pluck('supplier_brand_id'))
            {
                $supplier_brand['status'] = Config::get('constants.ACTIVE');
                DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                        ->where('supplier_brand_id', $supplier_brand_id)
                        ->update($supplier_brand);
            }
            else
            {
                $supplier_brand['status'] = Config::get('constants.ACTIVE');
                $supplier_brand['updated_by'] = $account_id;
                $supplier_brand_id = DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                        ->insertGetID($supplier_brand);
            }
            return $supplier_brand_id;
        }
        else
        {
            return false;
        }
    }

    public function saveSupplierCategory ($arr = array())
    {
        extract($arr);
        if (isset($supplier_category) && !empty($supplier_category))
        {
            if (isset($supplier_category_id) && !empty($supplier_category_id) || $supplier_category_id = DB::table(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE'))
                    ->where($supplier_category)
                    ->pluck('supplier_category_id'))
            {
                $supplier_category['status'] = Config::get('constants.ACTIVE');
                DB::table(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE'))
                        ->where('supplier_category_id', $supplier_category_id)
                        ->update($supplier_category);
            }
            else
            {
                $supplier_category['status'] = Config::get('constants.ACTIVE');
                $supplier_category_id = DB::table(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE'))
                        ->insert($supplier_category);
            }
            return $supplier_category_id;
        }
        else
        {
            return false;
        }
    }

    public function saveProcuctPrice ($arr = array())
    {
        $status1 = $status2 = false;
        extract($arr);
        $supplier_product['is_shipping_beared'] = isset($supplier_product['is_shipping_beared']) && !empty($supplier_product['is_shipping_beared']) ? Config::get('constants.ON') : Config::get('constants.OFF');
        $spp['mrp_price'] = isset($spp['mrp_price']) ? $spp['mrp_price'] : 0;
        $spp['price'] = isset($spp['price']) ? $spp['price'] : 0;
        $spp['currency_id'] = !empty($spp['currency_id']) ? $spp['currency_id'] : 1;
        $spp['off_perc'] = ($spp['mrp_price'] > 0) ? intval(($spp['mrp_price'] - $spp['price']) / $spp['mrp_price'] * 100) : 0;

        if (DB::table(Config::get('tables.PRODUCT_MRP_PRICE'))
                        ->where('product_id', $spp['product_id'])
                        ->where('currency_id', $spp['currency_id'])
                        ->exists())
        {
            DB::table(Config::get('tables.PRODUCT_MRP_PRICE'))
                    ->where('product_id', $spp['product_id'])
                    ->where('currency_id', $spp['currency_id'])
                    ->update(['mrp_price'=>$spp['mrp_price']]);
        }
        else
        {
            DB::table(Config::get('tables.PRODUCT_MRP_PRICE'))
                    ->insertGetID(['product_id'=>$spp['product_id'], 'currency_id'=>$spp['currency_id'], 'mrp_price'=>$spp['mrp_price']]);
        }
        unset($spp['mrp_price']);
        if ((isset($spp_id) && !empty($spp_id)) || $spp_id = DB::table(Config::get('tables.SUPPLIER_PRODUCT_PRICE'))
                ->where('supplier_id', $spp['supplier_id'])
                ->where('product_id', $spp['product_id'])
                ->where('currency_id', $spp['currency_id'])
                ->pluck('spp_id'))
        {
            $status1 = DB::table(Config::get('tables.SUPPLIER_PRODUCT_PRICE'))
                    ->where('spp_id', $spp_id)
                    ->update($spp);
            if (isset($supplier_product['is_shipping_beared']))
            {
                DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                        ->where('supplier_id', $spp['supplier_id'])
                        ->where('product_id', $spp['product_id'])
                        ->update(['is_shipping_beared'=>$supplier_product['is_shipping_beared']]);
            }
        }
        else
        {
            $status1 = DB::table(Config::get('tables.SUPPLIER_PRODUCT_PRICE'))
                    ->insertGetId($spp);
        }
        if (isset($spp['spcp']['product_cmb_id']) && !empty($spp['spcp']['product_cmb_id']))
        {
            $spp['spcp']['currency_id'] = $spp['currency_id'];
            $status2 = $this->saveProductCmbPrice(['spcp'=>$spp['spcp']]);
        }
        return $status1 || $status2;
    }

    public function saveProduct ($arr = array())
    {	//return $arr;
        $product = array();
        $assoc_category_id = '';
        extract($arr);
        $product = array_filter($product);
        $status1 = $status2 = $status3 = $status4 = false;
        if (isset($product_id) && !empty($product_id))
        {
            $details['updated_by'] = $account_id;
            $res = DB::table(Config::get('tables.PRODUCT_DETAILS'))
                    ->where('product_id', $product_id)
                    ->where('created_by', $account_id);
            if (!empty($product_cmb_id) && isset($product_cmb_id))
            {
                $res->where('product_cmb_id', $product_cmb_id);
            }
            $status1 = $res->update($details);
            if ($status1)
            {
                $status1 = DB::table(Config::get('tables.PRODUCTS'))
                        ->where('product_id', $product_id)
                        ->update($product);
            }
        }
        else
        {
            $sku = DB::table(Config::get('tables.PRODUCT_DETAILS'))
                    ->whereRaw('LCASE(`sku`) = \''.strtolower($details['sku']).'\'')
                    ->pluck('sku');
            if (!empty($sku))
            {
                return ['data'=>'3'];
            }
            if (isset($details['eanbarcode']) && !empty($details['eanbarcode']))
            {
                $eanbarcode = DB::table(Config::get('tables.PRODUCT_DETAILS'))
                        ->whereRaw('LCASE(`eanbarcode`) = \''.strtolower($details['eanbarcode']).'\'')
                        ->pluck('eanbarcode');
                if (!empty($eanbarcode))
                {
                    return ['data'=>'4'];
                }
            }
            if (isset($details['upcbarcode']) && !empty($details['upcbarcode']))
            {
                $upcbarcode = DB::table(Config::get('tables.PRODUCT_DETAILS'))
                        ->whereRaw('LCASE(`upcbarcode`) = \''.strtolower($details['upcbarcode']).'\'')
                        ->pluck('upcbarcode');
                if (!empty($upcbarcode))
                {
                    return ['data'=>'5'];
                }
            }
            if (empty($sku) && empty($upcbarcode) && empty($eanbarcode))
            {
                if ($product_id = $pid = $status = DB::table(Config::get('tables.PRODUCTS'))
                        ->insertGetId($product))
                {
                    $details['product_id'] = $product_id;
                    $details['product_code'] = 'PRO'.$product_id;
                    $details['created_on'] = date('Y-m-d H:i:s');
                    $details['created_by'] = $account_id;
                    $details['product_slug'] = $details['sku'];
                    $status2 = DB::table(Config::get('tables.PRODUCT_DETAILS'))->insertGetId($details);
                }
            }
        }
        if (!empty($tags))
        {
            $tagnamess = explode(',', $tags);

            foreach ($tagnamess as $tagname)
            {
                if (!is_numeric($tagname))
                {
                    if (!($tag_id = DB::table(Config::get('tables.TAG'))
                            ->where('tag_name', $tagname)
                            ->pluck('tag_id')))
                    {
                        $tag_id = DB::table(Config::get('tables.TAG'))
                                ->insertGetId(array('tag_name'=>$tagname));
                    }
                }
                else
                {
                    $tag_id = $tagname;
                }
                if ($tag_id)
                {
                    if (!DB::table(Config::get('tables.PRODUCT_TAG'))
                                    ->where('product_id', $product_id)
                                    ->where('tag_id', $tag_id)
                                    ->exists())
                    {
                        DB::table(Config::get('tables.PRODUCT_TAG'))
                                ->insert(array('tag_id'=>$tag_id, 'product_id'=>$product_id));
                    }
                    else
                    {
                        DB::table(Config::get('tables.PRODUCT_TAG'))
                                ->where('product_id', $product_id)
                                ->where('tag_id', $tag_id)
                                ->where('is_deleted', Config::get('constants.ON'))
                                ->update(['is_deleted'=>Config::get('constants.OFF')]);
                    }
                }
            }
            DB::table(Config::get('tables.PRODUCT_TAG'))
                    ->where('product_id', $product_id)
                    ->whereNotIn('tag_id', $tagnamess)
                    ->update(array('is_deleted'=>Config::get('constants.ON')));

            $status3 = 1;
        }

        if (!empty($meta_info))
        {
            //$meta_info['post_type_id'] = Config::get('constants.POST_TYPE.PRODUCT');
            $meta_info['relative_post_id'] = $product_id;
            $status4 = $this->saveMetaInfo(['meta_info'=>$meta_info]);
        }

        $supplier_product['product_id'] = $product_id;
        $supplier_product['account_id'] = $account_id;
        $supplier_product['supplier_id'] = $supplier_id;
        if (isset($featured) && !empty($featured))
        {
            $supplier_product['is_featured'] = $featured;
        }
        else
        {
            $supplier_product['is_featured'] = 0;
        }
        if (isset($promote_to_homepage) && !empty($promote_to_homepage))
        {
            $supplier_product['promote_to_homepage'] = $promote_to_homepage;
        }
        else
        {
            $supplier_product['promote_to_homepage'] = 0;
        }
        $status = $this->save_supplier_product(array(
            'supplier_id'=>$supplier_id,
            'product_id'=>$product_id,
            'account_id'=>$account_id,
            'supplier_product'=>$supplier_product,
			'currency_id'=>$currency_id
        ));

        if ($status1 || $status2 || $status3 || $status4)
        {
			if (isset($pid) && !empty($pid)) {
				return ['product_code'=>$status, 'status'=>'OK', 'data'=>'6']; 
			} else {
				return ['product_code'=>$status, 'status'=>'OK', 'data'=>'1']; 
			}           
        }
        else
        {
            return false;
        }
    }

    public function saveMetaInfo ($arr = array())
    { 
        extract($arr);
        if ((isset($meta_info_id) && !empty($meta_info_id)) || $meta_info_id = DB::table(Config::get('tables.META_INFO'))
                ->where('relative_post_id', $meta_info['relative_post_id'])
                ->where('post_type_id', $meta_info['post_type_id'])
                ->pluck('meta_info_id'))
        {
            return DB::table(Config::get('tables.META_INFO'))
                            ->where('meta_info_id', $meta_info_id)
                            ->update($meta_info);
        }
        else
        {
            $arr['created_on'] = date('Y-m-d H:i:s');
            return DB::table(Config::get('tables.META_INFO'))
                            //->where('meta_info_id', $meta_info_id)
                            ->insertGetID($meta_info);
        }
        return false;
    }

    public function saveProductCountry ($arr = array())
    {
        extract($arr);
        if (DB::table(Config::get('tables.PRODUCT_COUNTRIES'))
                        ->where($product)
                        ->exists())
        {
            $product['updated_on'] = date('Y-m-d H:i:s');
            $product['updated_by'] = $account_id;
            $product['is_deleted'] = Config::get('constants.OFF');
            $product['log'] = 'concat(log,\','.json_encode(['date'=>date('Y-m-d H:i:s'), 'updated_by'=>$account_id, 'action'=>'ADDED']).'\')';
            return DB::table(Config::get('tables.PRODUCT_COUNTRIES'))
                            ->where('product_id', $product['product_id'])
                            ->where('country_id', $product['country_id'])
                            ->where('is_deleted', Config::get('constants.ON'))
                            ->update($product);
        }
        else
        {
            $product['created_on'] = date('Y-m-d H:i:s');
            $product['updated_by'] = $account_id;
            return DB::table(Config::get('tables.PRODUCT_COUNTRIES'))
                            ->insertGetID($product);
        }
        return false;
    }

    public function deleteProductCountry ($arr = array())
    {
        extract($arr);
        $product = [];
        $product['product_id'] = $product_id;
        $product['country_id'] = $country_id;
        $product['updated_on'] = date('Y-m-d H:i:s');
        $product['updated_by'] = $account_id;
        $product['is_deleted'] = Config::get('constants.ON');
        $product['log'] = 'concat(log,\','.json_encode(['date'=>date('Y-m-d H:i:s'), 'updated_by'=>$account_id, 'action'=>'DELETED']).'\')';
        return DB::table(Config::get('tables.PRODUCT_COUNTRIES'))
                        ->where('product_id', $product['product_id'])
                        ->where('country_id', $product['country_id'])
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->update($product);
    }

    public function updateStock ($arr = array())
    {
        //return $arr;
        extract($arr);
        $stock_on_hand = DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT'))
                ->where('supplier_product_id', $supplier_product_id)
                ->value('stock_on_hand');
        $stock_value = !empty($stock_value) ? $stock_value : 0;
        if (is_numeric($stock_on_hand) && $stock_on_hand >= 0)
        {
            $current_stock_value = ($arr['transaction_type'] == 1) ? $stock_on_hand + $stock_value : $stock_on_hand - $stock_value;
            if (DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT'))
                            ->where('supplier_product_id', $supplier_product_id)
                            ->update(['stock_on_hand'=>$current_stock_value]))
            {
                return DB::table(Config::get('tables.SUPPLER_PRODUCT_STOCK_LOG'))
                                ->insertGetId([
                                    'supplier_product_id'=>$supplier_product_id,
                                    'transaction_type'=>$transaction_type,
                                    'stock_value'=>$stock_value,
                                    'current_stock_value'=>$current_stock_value,
                                    'updated_by'=>$account_id,
                                    'created_on'=>date('Y-m-d H:i:s')
                ]);
            }
        }
        else
        {
            return DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT'))
                            ->insertGetID([
                                'supplier_product_id'=>$supplier_product_id,
                                'product_id'=>$product_id,
                                'stock_on_hand'=>$stock_value,
                                'created_on'=>date('Y-m-d H:i:s'),
                                'product_cmb_id'=>isset($product_cmb_id) && !empty($product_cmb_id) ? $product_cmb_id : NULL
            ]);
        }
        return false;
    }

    public function get_current_stocks ($arr = array())
    {
        $res = DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT').' as stm')
                ->where('stm.supplier_product_id', $arr['supplier_product_id']);
        if (!empty($arr['stm.product_cmb_id']) && isset($arr['product_cmb_id']))
        {
            $res->where('stm.product_cmb_id', $arr['product_cmb_id']);
        }
        $res->select('stm.stock_on_hand');
        $result = $res->first();
        if (!empty($result))
        {
            return $result;
        }
    }

    public function update_sortorder ($data = array())
    {
        $sortorder = '';
        extract($data);
        if (isset($combination_id) && !empty($combination_id))
        {
            $table = Config::get('tables.IMG_FILTERS');
        }
        else
        {
            $table = Config::get('tables.IMGS');
        }
        if (!empty($sortorder))
        {
            foreach ($sortorder as $key=> $val)
            {
                $query = DB::table($table.' as pt')
                        ->where('img_id', $key);
                if (isset($combination_id) && !empty($combination_id))
                {
                    $query->where('post_type_id', 4)
                            ->where('relative_post_id', $combination_id);
                    if ($val == 1)
                    {
                        $query->update(array(
                            'sort_order'=>$val,
                            'primary_img'=>Config::get('constants.ON')));
                    }
                    else
                    {
                        $query->update(array(
                            'sort_order'=>$val, 'primary_img'=>Config::get('constants.OFF')));
                    }
                }
                else
                {
                    $query->where('post_type_id', 3)
                            ->where('is_deleted', Config::get('constants.OFF'))
                            ->where('status_id', Config::get('constants.ON'));
                    if ($val == 1)
                    {
                        $query->update(array(
                            'sort_order'=>$val,
                            'primary'=>1));
                    }
                    else
                    {
                        $query->update(array(
                            'sort_order'=>$val));
                    }
                }
            }
        }
        else
        {
            return false;
        }
    }

    public function save_products_combinations ($arr = array())
    {
        //return $arr;
        extract($arr);
        $data['property_id'] = 0;
        $data['value_id'] = 0;
        if (isset($supplier_product_id) && !empty($supplier_product_id))
        {
            if (!empty($product_cmb_properties) && isset($product_cmb_properties))
            {
                $cmbs = implode(',', $arr['product_cmb_properties']);
            }
            $product_cmb['pro_com_value_id'] = $cmbs;
            $check = $this->check_combination_exist(array('product_id'=>$product_cmb['product_id'], 'pro_cmb'=>$cmbs, 'product_cmb_id'=>$product_cmb_id));
            //print_r($check);exit;
            if ($check)
            {
                return '111';
                if (isset($product_cmb_id) && !empty($product_cmb_id))
                {
                    // Update Here
                    return '111';
                    /* $product_cmb['updated_by'] = $account_id;
                      $product_cmb['supplier_id'] = $supplier_id;
                      $product_cmb['updated_on'] = date('Y-m-d H:i:s');
                      $res_pc = DB::table(Config::get('tables.PRODUCT_COMBINATIONS'))
                      ->where('product_cmb_id', $product_cmb_id)
                      ->where('is_deleted', Config::get('constants.OFF'))
                      ->update($product_cmb); */
                    //supplier product combinations update
                }
            }
            else
            {
                unset($product_cmb['is_exclusive']);
                unset($product_cmb['sku']);
                unset($product_cmb['product_cmb_code']);
                if (isset($product['same_details']))
                {
                    $prod_cmb_id = DB::table(Config::get('tables.PRODUCT_COMBINATIONS'))->insertGetID($product_cmb);
                    if (isset($prod_cmb_id) && !empty($prod_cmb_id))
                    {
                        $detail = DB::table(Config::get('tables.PRODUCT_DETAILS'))
                                ->where('product_id', $product_id);
                        if (empty($product_cmb_id))
                        {
                            $detail->whereNULL('product_cmb_id');
                        }
                        else
                        {
                            $detail->where('product_cmb_id', $product_cmb_id);
                        }
                        $details = (array) $detail->first();
                        unset($details['product_details_id']);
                        unset($details['eanbarcode']);
                        unset($details['upcbarcode']);
                        $details['product_cmb_id'] = $prod_cmb_id;
                        $details['eanbarcode'] = $prod_cmb_id;
                        $details['upcbarcode'] = $prod_cmb_id;
                        $product_details_id = DB::table(Config::get('tables.PRODUCT_DETAILS'))->insertGetID($details);
                    }
                }
                else
                {
                    $sku = DB::table(Config::get('tables.PRODUCT_DETAILS'))
                            ->whereRaw('LCASE(`sku`) = \''.strtolower($det['sku']).'\'')
                            ->pluck('sku');
                    if (!empty($sku))
                    {
                        return 3;
                    }

                    if (empty($sku))
                    {
                        $prod['is_combinations'] = Config::get('constants.ON');
                        $prod['brand_id'] = $brand_id;
                        $prod['category_id'] = $category_id;
                        if ($product_id = $status = DB::table(Config::get('tables.PRODUCTS'))->insertGetId($prod))
                        {
                            $product_cmb['product_id'] = $product_id;
                            $prod_cmb_id = DB::table(Config::get('tables.PRODUCT_COMBINATIONS'))->insertGetID($product_cmb);
                            $det['product_id'] = $product_id;
                            $det['product_cmb_id'] = $prod_cmb_id;
                            $det['product_code'] = 'PRO'.$product_id;
                            $det['eanbarcode'] = $product_id;
                            $det['upcbarcode'] = $product_id;
                            $det['product_slug'] = $det['sku'];
                            $det['description'] = $det['descrip'];
                            $det['weight'] = $det['wgt'];
                            $det['width'] = $det['wdh'];
                            $det['height'] = $det['hgt'];
                            $det['length'] = $det['len'];
                            $det['created_on'] = date('Y-m-d H:i:s');
                            $det['created_by'] = $account_id;
                            unset($det['descrip']);
                            unset($det['wgt']);
                            unset($det['wdh']);
                            unset($det['hgt']);
                            unset($det['len']);
                            $details_id = DB::table(Config::get('tables.PRODUCT_DETAILS'))->insertGetId($det);
                        }
                    }
                }
                $supp_pro_cmb_id = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                        ->where('supplier_product_id', $supplier_product_id)
                        ->select('supplier_product_code', 'supplier_id', 'store_id', 'product_id', 'product_cmb_id', 'condition_id', 'is_existing', 'is_replaceable', 'is_assured', 'status', 'pre_order', 'stock_status_id', 'is_deleted')
                        ->first();
                $spp = array('supplier_id'=>$supplier_id, 'product_id'=>$supp_pro_cmb_id->product_id, 'supplier_product_id'=>$supplier_product_id, 'product_cmb_id'=>$prod_cmb_id);
                $spp['spcp'] = array('supplier_id'=>$supplier_id, 'currency_id'=>1, 'product_cmb_id'=>$prod_cmb_id, 'impact_on_price'=>$product_cmb_price['impact_on_price']);
                $this->saveProductCmbPrice(['spp'=>$spp]);

                if (empty($supp_pro_cmb_id->product_cmb_id))
                {

                    $update = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                            ->where('supplier_product_id', '=', $supplier_product_id)
                            ->update(array('product_cmb_id'=>$prod_cmb_id));

                    $update_stock_combination = DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT'))
                            ->where('supplier_product_id', '=', $supplier_product_id)
                            ->update(array('product_cmb_id'=>$prod_cmb_id));
                }
                if (!empty($supp_pro_cmb_id->product_cmb_id))
                {
                    if ($prod_cmb_id != $supp_pro_cmb_id->product_cmb_id)
                    {

                        $supp_pro_cmb_id->created_on = date(Config::get('constants.DB_DATE_TIME_FORMAT'));
                        $arrData = array();
                        $arrData = (array) $supp_pro_cmb_id;
                        unset($arrData['supplier_product_code']);
                        $arrData['updated_by'] = $account_id;
                        $insert_id = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))->insertGetID($arrData);
                        //insert stock
                        $insert_stock_combination = array('product_id'=>$supp_pro_cmb_id->product_id, 'supplier_product_id'=>$insert_id, 'product_cmb_id'=>$prod_cmb_id);
                        if (!empty($insert_stock_combination))
                        {
                            DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT'))
                                    ->insertGetID($insert_stock_combination);
                        }
                        if (!empty($insert_id))
                        {
                            $supplier_product_code = Config::get('constants.SUPPLIER_PRODUCT_CODE_PREFIX').$insert_id;
                            DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                                    ->where('supplier_product_id', $insert_id)
                                    ->update(array('supplier_product_code'=>$supplier_product_code, 'product_cmb_id'=>$prod_cmb_id));
                        }
                    }
                }

                if (!empty($prod_cmb_id))
                {
                    $op['status'] = 'OK';
                    $op['msg'] = Lang::get('product_combinations.c_insert_s');
                }
                return $op;
            }
        }
        return false;
    }

    public function check_combination_exist ($arr = array())
    {
        extract($arr);
        $pro_cmb = array_filter(explode(',', $pro_cmb));
        $exist = 0;
        $product_cmb_ids = DB::table(Config::get('tables.PRODUCT_COMBINATIONS').' as pc')
                ->where('pc.product_id', $product_id)
                ->where('pc.is_deleted', Config::get('constants.OFF'))
                ->lists('product_cmb_id');
        foreach ($product_cmb_ids as $product_cmb_id)
        {
            $combination_properties = DB::table(Config::get('tables.PRODUCT_CMB_PROPERTIES').' as pcp')
                    ->where('pcp.product_cmb_id', $product_cmb_id)
                    ->where('pcp.is_deleted', Config::get('constants.OFF'))
                    ->selectRaw('property_id, value_id')
                    ->get();
            if (count($combination_properties) == count($pro_cmb))
            {
                foreach ($combination_properties as $pro)
                {
                    foreach ($pro_cmb as $p)
                    {
                        $p = explode(':', $p);
                        $exist += ($pro->property_id == $p[0] && $pro->value_id == $p[1]) ? 1 : 0;
                    }
                }
            }
        }
        return ($exist > 0 && count($combination_properties) <= $exist) ? true : false;
    }

    public function productCombinationsList ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.PRODUCT_COMBINATIONS').' as pc')
                        ->leftJoin(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi', 'spi.product_cmb_id', '=', 'pc.product_cmb_id')
                        //->selectRaw('concat(pc.product_cmb_code,\' (\',pc.product_cmb,\')\') as product_cmb, pc.product_cmb_id')
                        //->where('spi.supplier_id', '!=', $supplier_id)
                        //->where('pc.is_verified', Config::get('constants.ON'))
                        ->where('pc.is_deleted', Config::get('constants.OFF'))
                        //->where('pc.status', Config::get('constants.ACTIVE'))
                        ->where('spi.product_id', $product_id)
                        ->get();
    }

    public function properties_list_for_com ($arr = array())
    {
        extract($arr);
        $res = DB::table(Config::get('tables.PRODUCT_PROPERTY').' as pp')
                ->leftjoin(Config::get('tables.PRODUCT_PROPERTY_KEYS').' as pk', 'pk.property_id', '=', 'pp.property_id')
                ->where('pp.product_id', $product_id)
                ->where('pp.choosable', Config::get('constants.ACTIVE'))
                ->where('pp.is_deleted', Config::get('constants.OFF'))
                ->selectRaw('pk.property_id, pk.property')
                ->orderBy('property', 'asc')
                ->get();
        return $res;
    }

    public function values_list_for_com ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.PRODUCT_PROPERTY').' as pp')
                        ->leftjoin(Config::get('tables.PRODUCT_PROPERTY_VALUES').' as ppv', 'ppv.pp_id', '=', 'pp.pp_id')
                        ->leftjoin(Config::get('tables.PRODUCT_PROPERTY_KEY_VALUES').' as pv', 'pv.value_id', '=', 'ppv.value_id')
                        ->leftjoin(Config::get('tables.UNITS').' as u', 'u.unit_id', '=', 'pv.unit_id')
                        ->where('pp.property_id', $property_id)
                        ->where('pp.product_id', $product_id)
                        ->where('ppv.is_deleted', Config::get('constants.OFF'))
                        ->selectRaw('pv.value_id,concat(pv.key_value,if(u.unit is not null,u.unit,\'\')) as value,pv.property_id')
                        ->orderBy('value', 'asc')
                        ->get();
    }

    public function combination_list_by_id ($arr = array())
    {
        $currency_id = '';
        extract($arr);
        if (!empty($product_cmb_id) && isset($product_cmb_id))
        {
            $res = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi')
                    ->leftjoin(Config::get('tables.PRODUCT_CMB_PROPERTIES').' as pcp', 'pcp.product_cmb_id', '=', 'spi.product_cmb_id')
                    ->leftjoin(Config::get('tables.PRODUCT_PROPERTY_KEYS').' as ppk', 'ppk.property_id', '=', 'pcp.property_id')
                    ->leftjoin(Config::get('tables.PRODUCT_PROPERTY_KEY_VALUES').' as ppkv', 'ppkv.value_id', '=', 'pcp.value_id')
                    ->leftjoin(Config::get('tables.PRODUCT_COMBINATIONS').' as pc', 'pc.product_cmb_id', '=', 'spi.product_cmb_id')
                    ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT').' as sm', 'sm.supplier_product_id', '=', 'spi.supplier_product_id')
                    ->leftjoin(Config::get('tables.PRODUCT_MRP_PRICE').' as mrp', function($mrp) use($currency_id)
                    {
                        $mrp->on('mrp.product_id', '=', 'spi.product_id');
                        if (!empty($currency_id))
                        {
                            $mrp->where('mrp.currency_id', '=', $currency_id);
                        }
                    })
                    ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_PRICE').' as spp', function($spp) use($currency_id)
                    {
                        $spp->on('spp.supplier_id', '=', 'spi.supplier_id');
                        $spp->on('spp.product_id', '=', 'spi.product_id');
                        if (!empty($currency_id))
                        {
                            $spp->where('spp.currency_id', '=', $currency_id);
                        }
                    })
                    ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_CMB_PRICE').' as spc', function($spc)
                    {
                        $spc->on('spc.product_cmb_id', '=', 'pcp.product_cmb_id');
                        $spc->on('spc.currency_id', '=', 'spp.currency_id');
                    })
                    ->leftjoin(Config::get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'spc.currency_id')
                    ->where('pcp.is_deleted', Config::get('constants.OFF'))
                    ->where('pcp.product_cmb_id', $product_cmb_id)
                    ->selectRaw('group_concat(concat(pcp.property_id,\':\',pcp.value_id)) as vals,group_concat(concat(ppk.property,\':\',ppkv.key_value)) as labels,mrp.mrp_price,spp.price,spc.impact_on_price,pcp.cmb_ppt_id,pc.pro_com_value_id,cur.currency,pc.product_cmb_code,sm.stock_on_hand,pc.sku,pc.product_cmb,pc.product_cmb_id,pc.product_id')
                    ->get();
            if ($res)
            {
                $currency_with_charge = array();
                foreach ($res as $rs)
                {
                    $currency = $rs->impact_on_price.' '.$rs->currency;
                    $rs->currency_with_charge = $currency;
                    $currency_with_charge[] = $rs;
                }
                return $currency_with_charge;
            }
        }
        return false;
    }

    public function combination_list_for_com ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.PRODUCT_COMBINATIONS').' as pc')
                        ->leftJoin(Config::get('tables.PRODUCT_DETAILS').' as pd', function($sps)
                        {
                            $sps->on('pd.product_cmb_id', '=', 'pc.product_cmb_id')
                            ->on('pd.product_id', '=', 'pc.product_id');
                        })
                        ->leftJoin(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT').' as sps', function($sps)
                        {
                            $sps->on('sps.product_cmb_id', '=', 'pc.product_cmb_id')
                            ->on('sps.product_id', '=', 'pc.product_id');
                        })
                        ->where('pc.product_id', $product_id)
                        ->select('pc.product_cmb_id', 'pd.product_code', 'sps.stock_on_hand')
                        ->get();
    }

    public function add_product_image ($data)
    {
        if (!empty($data))
        {
            $product_imgs = array();
            $pos = $data['position'];
            foreach ($data['files'] as $product)
            {
                $pos++;
                $post['product_img']['img_path'] = $product;
                $post['product_img']['position'] = $pos;
                $post['product_img']['product_id'] = $data['product_img']['product_id'];
                $post['product_img']['created_on'] = date('Y-m-d');
                $post['product_img']['updated_by'] = $data['updated_by'];
                $product_imgs[] = $post['product_img'];
            }
            if (!empty($product_imgs))
            {
                $image = DB::table(Config::get('tables.IMGS'))
                        ->insert($product_imgs);
                if (!empty($image))
                {
                    return true;
                }
            }
            return false;
        }
    }

    public function save_combination_images ($data)
    {
        extract($data);
        foreach ($selected as $selected)
        {
            $res = DB::table(Config::get('tables.IMG_FILTERS'))
                    ->where('img_id', $selected)
                    ->where('relative_post_id', $combination_id)
                    ->where('is_deleted', Config::get('constants.OFF'))
                    ->select('img_id')
                    ->get();
            if (empty($res))
            {
                DB::table(Config::get('tables.IMG_FILTERS'))
                        ->insert(array(
                            'img_id'=>$selected,
                            'post_type_id'=>4,
                            'relative_post_id'=>$combination_id));
            }
        }
        return true;
    }

    public function image_remove ($data = array())
    {
        DB::table(Config::get('tables.IMG_FILTERS'))
                ->where('img_id', $data['img_id'])
                ->where('relative_post_id', $data['combination_id'])
                ->update(array(
                    'is_deleted'=>Config::get('constants.ON')));
        $get_sort_order = DB::table(Config::get('tables.IMG_FILTERS'))
                ->where('relative_post_id', $data['combination_id'])
                ->where('img_id', $data['img_id'])
                ->pluck('sort_order');
        if ($get_sort_order)
        {
            DB::table(Config::get('tables.IMG_FILTERS'))
                    ->where('sort_order', '>', $get_sort_order)
                    ->where('relative_post_id', $data['combination_id'])
                    ->where('is_deleted', Config::get('constants.OFF'))
                    ->decrement('sort_order');
            DB::table(Config::get('tables.IMG_FILTERS'))
                    ->where('is_deleted', Config::get('constants.OFF'))
                    ->where('relative_post_id', $data['combination_id'])
                    ->where('sort_order', Config::get('constants.ON'))
                    ->update(array(
                        'primary'=>Config::get('constants.ON')));
        }
        return true;
    }

    public function default_pro_img ($data)
    {
        DB::table(Config::get('tables.IMGS').' as img')
                ->where('img.relative_post_id', $data['product_id'])
                ->where('img_id', '<>', $data['img_id'])
                ->where('post_type_id', Config::get('constants.POST_TYPE.PRODUCT'))
                ->update(array('primary'=>Config::get('constants.OFF')));
        return DB::table(Config::get('tables.IMGS').' as img')
                        ->where('img.relative_post_id', $data['product_id'])
                        ->where('post_type_id', Config::get('constants.POST_TYPE.PRODUCT'))
                        ->where('img.img_id', $data['img_id'])
                        ->update(array('primary'=>Config::get('constants.ON')));
    }

    public function delete_selected_image ($data)
    {
        extract($data);
        foreach ($selected as $selected)
        {
            $res = DB::table(Config::get('tables.IMGS'))
                    ->where('img_id', $selected)
                    ->update(array(
                'is_deleted'=>1));
        }
        return true;
    }

    public function save_property ($arr = array())
    {
        extract($arr);
        extract($arr);
        $property_ids = [];
        if (isset($product_id) && !empty($product_id))
        {
            foreach ($properties as $property_id=> $property)
            {
                $property['product_id'] = $product_id;
                $property_ids[] = $property_id;
                if (!($pp_id = DB::table(Config::get('tables.PRODUCT_PROPERTY'))
                        ->where($property)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->pluck('pp_id')))
                {
                    $pp_id = DB::table(Config::get('tables.PRODUCT_PROPERTY'))
                            ->insertGetId($property);
                }
                else
                {
                    DB::table(Config::get('tables.PRODUCT_PROPERTY'))
                            ->where('pp_id', $pp_id)
                            ->update(['is_deleted'=>Config::get('constants.OFF')]);
                }
                if ($pp_id && !empty($values[$property_id]))
                {
                    $value_ids = [];
                    foreach ($values[$property_id] as $value_id=> $value)
                    {
                        $value_ids[] = $value;
                        $value['pp_id'] = $pp_id;
                        if (!($ppv_id = DB::table(Config::get('tables.PRODUCT_PROPERTY_VALUES'))
                                ->where($value)
                                ->where('is_deleted', Config::get('constants.OFF'))
                                ->pluck('ppv_id')))
                        {
                            DB::table(Config::get('tables.PRODUCT_PROPERTY_VALUES'))
                                    ->insertGetId($value);
                        }
                        else
                        {
                            DB::table(Config::get('tables.PRODUCT_PROPERTY_VALUES'))
                                    ->where('ppv_id', $ppv_id)
                                    ->update(['is_deleted'=>Config::get('constants.OFF')]);
                        }
                    }

                    DB::table(Config::get('tables.PRODUCT_PROPERTY_VALUES'))
                            ->where('pp_id', $pp_id)
                            ->whereNotIn('value_id', $value_ids)
                            ->update(['is_deleted'=>Config::get('constants.ON')]);
                }
            }
            DB::table(Config::get('tables.PRODUCT_PROPERTY'))
                    ->where('product_id', $product_id)
                    ->whereNotIn('property_id', $property_ids)
                    ->update(['is_deleted'=>Config::get('constants.ON')]);
            return true;
        }
        return false;
    }

    public function getProductCountries ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.PRODUCT_COUNTRIES').' as pc')
                        ->join(Config::get('tables.LOCATION_COUNTRY').' as c', 'c.country_id', '=', 'pc.country_id')
                        ->where('pc.product_id', $product_id)
                        ->where('pc.is_deleted', Config::get('constants.OFF'))
                        ->select('c.country', 'c.country_id')
                        ->lists('country', 'country_id');
    }

}
