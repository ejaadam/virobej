<?php
namespace App\Models\Seller;
use DB;
use Illuminate\Database\Eloquent\Model;
use Config;
use URL;
use Lang;

class Product extends Model
{

    /**
     * @param array $arr [supplier_id]
     * @param bool $count to get count<i>(Default-false)</i>
     * @param bool $ids_only to get id list<i>(Default-false)</i>
     * @return array|bool return array of categories or false
     */
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
                    ->selectRaw('sc.supplier_category_id as id,pc.category, sc.updated_on,ls.status_id,ls.status_class,ls.status,IF(pcp.parent_category_id IS NULL, 0, parent_category_id) as parent_category_id, pc.category_id, ppc.category as parent_category')
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
                            'url'=>URL::route('api.v1.seller.catalog.categories.change-status'),
                            'data'=>[
                                'id'=>$category->id,
                                'status'=>Config::get('constants.INACTIVE')
                            ],
                        ];
                    }
                    $category->actions[] = [
                        'title'=>'Delete',
                        'url'=>URL::route('api.v1.seller.catalog.categories.delete'),
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

    public function delete_supplier_product_zone ($data)
    {
        extract($data);
        $update['is_deleted'] = Config::get('constants.ON');
        return Db::table(Config::get('tables.SUPPLIER_PRODUCT_SHIPPMENT_SETTINGS'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->where('pss_id', $pss_id)
                        ->update($update);
    }

    public function save_supplier_product_zone ($arr = array())
    {
        extract($arr);
        $supplier_product_zone['updated_by'] = $updated_by;
        $supplier_product_zone['supplier_id'] = $supplier_id;
        if (isset($supplier_product_zone['pss_id']) && !empty($supplier_product_zone['pss_id']))
        {
            $pss_id = $supplier_product_zone['pss_id'];
        }
        else
        {
            unset($supplier_product_zone['pss_id']);
        }
        if (!isset($pss_id) || empty($pss_id))
        {
            $pss_id = DB::table(Config::get('tables.SUPPLIER_PRODUCT_SHIPPMENT_SETTINGS'))
                    ->where('supplier_product_id', $supplier_product_id)
                    ->where('is_deleted', '=', Config::get('constants.OFF'))
                    ->pluck('pss_id');
        }
        if (isset($pss_id) && !empty($pss_id))
        {
            // $supplier_product_zone['geo_zone_id'] = $geo_zone_id;
            DB::table(Config::get('tables.SUPPLIER_PRODUCT_SHIPPMENT_SETTINGS'))
                    ->where('supplier_product_id', $supplier_product_id)
                    ->where('pss_id', $pss_id)
                    ->update($supplier_product_zone);
        }
        else
        {
            $supplier_product_zone['created_on'] = date('Y-m-d');
            $supplier_product_zone['supplier_product_id'] = $supplier_product_id;
            $pss_id = DB::table(Config::get('tables.SUPPLIER_PRODUCT_SHIPPMENT_SETTINGS'))
                    ->insertGetId($supplier_product_zone);
        }
        if ($pss_id)
        {
            unset($supplier_product_zone['created_on']);
            unset($supplier_product_zone['supplier_product_id']);
            unset($supplier_product_zone['updated_by']);
            unset($supplier_product_zone['supplier_id']);
            if (DB::table(Config::get('tables.SUPPLIER_PRODUCT_SHIPPMENT_CHARGES'))
                            ->where('pss_id', $pss_id)
                            ->where('geo_zone_id', $geo_zone_id)
                            ->count())
            {
                $supplier_product_zone['geo_zone_id'] = $geo_zone_id;
                $upd = DB::table(Config::get('tables.SUPPLIER_PRODUCT_SHIPPMENT_CHARGES'))
                        ->where('pss_id', $pss_id)
                        ->where('geo_zone_id', $geo_zone_id)
                        ->update($supplier_product_zone);
                if (!empty($upd))
                {
                    //$op['status'] = 'OK';
                    $op['msg'] = Lang::get('general.zone_updated_successfully');
                }
                else
                {
                    //$op['status'] = 'WARN';
                    $op['msg'] = Lang::get('general.there_is_no_changes');
                }
                return $op;
            }
            else
            {
                $supplier_product_zone['geo_zone_id'] = $geo_zone_id;
                $supplier_product_zone['pss_id'] = $pss_id;
                $ins = DB::table(Config::get('tables.SUPPLIER_PRODUCT_SHIPPMENT_CHARGES'))
                        ->insertGetId($supplier_product_zone);
                if (!empty($ins))
                {
                    //$op['status'] = 'OK';
                    $op['msg'] = Lang::get('general.geo_zone_created_successfully');
                }
                else
                {
                    //$op['status'] = 'WARN';
                    $op['msg'] = Lang::get('general.there_is_no_changes');
                }
                return $op;
            }
        }
        return false;
    }

    public function supplier_products_zones ($arr = array())
    {	
        extract($arr);
        $zones = DB::table(Config::get('tables.SUPPLIER_PRODUCT_SHIPPMENT_SETTINGS').' as ss')
                ->join(Config::get('tables.SUPPLIER_PRODUCT_SHIPPMENT_CHARGES').' as sc', 'sc.pss_id', '=', 'ss.pss_id')
                ->join(Config::get('tables.COURIER_MODE_LOOKUPS').' as cm', 'cm.mode_id', '=', 'sc.mode_id')
                ->leftjoin(Config::get('tables.GEO_ZONE').' as z', 'z.geo_zone_id', '=', 'sc.geo_zone_id')
                ->leftjoin(Config::get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'sc.currency_id')
                ->where('ss.is_deleted', Config::get('constants.OFF'));
        if (!empty($search_term))
        {
            $zones->where(function($subquery) use($search_term)
            {
                $subquery->where('p.product_name', 'like', '%'.$search_term.'%');
                $subquery->orWhere('p.description', 'like', '%'.$search_term.'%');
            });
        }
        if (isset($supplier_product_id) && !empty($supplier_product_id))
        {
            $zones->where('ss.supplier_product_id', $supplier_product_id);
        }
        if (isset($spc_id) && !empty($spc_id))
        {
            $zones->where('ss.spc_id', $spc_id);
        }
        if (isset($orderby))
        {
            $zones->orderby($orderby, $order);
        }
        if (isset($count) && $count)
        {
            return $zones->count();
        }
        else
        {
            $zones = $zones->selectRaw('sc.*,z.zone,cm.mode,cur.currency')->get();
            $zone_charges_with_currency = array();
            //print_r($zones);
            //exit;
            foreach ($zones as $zone)
            {
                $zone_charges = $zone->delivery_charge.' '.$zone->currency;
                $zone->zone_charges = $zone_charges;
                $zone_charges_with_currency[] = $zone;
            }
            return $zone_charges_with_currency;
        }
    }

    public function update_category ($data)
    {
        $update['c.category'] = $data['category'];
        $update['cp.parent_category_id'] = $data['parent_category_id'];
        $update['c.updated_by'] = $data['updated_by'];
        return Db::table(Config::get('tables.PRODUCT_CATEGORIES').' as c')
                        ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as cp', 'cp.category_id', '=', 'c.category_id')
                        ->where('c.category_id', $data['category_id'])
                        ->update($update);
    }

    public function add_category ($data = array())
    {
        $result = '';
        if (isset($data['category']) && !empty($data['category']))
        {
            if (isset($data['categor_id']) && !empty($data['categor_id']))
            {
                $edit['c.category'] = $data['category_name'];
                $edit['cp.parent_category_id'] = $data['parent_category_id'];
                $edit['c.updated_by'] = $data['updated_by'];
                $edit['c.status'] = Config::get('constants.ON');
                $res = Db::table(Config::get('tables.PRODUCT_CATEGORIES').' as c')
                        ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as cp', 'cp.category_id', '=', 'c.category_id')
                        ->where('c.category_id', $data['categor_id'])
                        ->update($edit);
                if ($res)
                {
                    return $res;
                }
                return false;
            }
            else
            {
                $ins['category'] = $data['category_name'];
                $ins['parent_category_id'] = $data['parent_category_id'];
                $ins['updated_by'] = $data['updated_by'];
                $ins['status'] = Config::get('constants.ON');
                $ins['created_on'] = date('Y-m-d H:i:s');
                $res = Db::table(Config::get('tables.PRODUCT_CATEGORIES'))
                        ->insertGetId($ins);


                if ($res != '')
                {
                    DB::table(Config::get('tables.PRODUCT_CATEGORIES_PARENTS'))
                            ->insertGetID(['category_id'=>$res, 'parent_category_id'=>$data['parent_category_id']]);
                    $add_associate['category_id'] = $res;
                    $add_associate['supplier_id'] = $data['supplier_id'];
                    $add_associate['updated_by'] = $data['updated_by'];
                    $add_associate['status'] = Config::get('constants.ON');
                    $add_associate['updated_on'] = date('Y-m-d H:i:s');
                    $result = Db::table(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE'))
                            ->insertGetId($add_associate);
                }
            }
        }
        if (!empty($result))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function get_order_list ($data)
    {
        $product = DB::table(Config::get('tables.ORDER_ITEMS').' as sop')
                ->leftjoin(Config::get('tables.SUB_ORDERS').' as spso', 'spso.sub_order_id', '=', 'sop.sub_order_id')
                ->leftjoin(Config::get('tables.ACCOUNT_EXTRAS').' as tae', 'tae.account_id', '=', 'spso.account_id')
                ->where('sop.product_id', $data['product_id'])
                ->where('spso.supplier_id', $data['supplier_id'])
                ->selectRaw('sop.created_on, spso.order_code, tae.firstname, sop.qty, sop.amount, sop.discount,
                 	sop.net_pay, sop.order_status, sop.updated_date, spso.sub_order_status_id');
        if (isset($data['from']) && !empty($data['from']))
        {
            $product->whereDate('sop.created_on', '>=', date('Y-m-d', strtotime($data['from'])));
        }
        if (isset($data['to']) && !empty($data['to']))
        {
            $product->whereDate('sop.created_on', '<=', date('Y-m-d', strtotime($data['to'])));
        }
        if (isset($data['orderby']))
        {
            $product->orderby($data['orderby'], $data['order']);
        }
        if (isset($data['counts']) && !empty($data['counts']))
        {
            return $product->count();
        }
        else
        {
            return $product->get();
        }
    }

    public function get_product_items ($data, $count = false)
    {
        extract($data);
        $product = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as pi')
                ->leftjoin(Config::get('tables.PRODUCTS').' as p', 'p.product_id', '=', 'pi.product_id')
                ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES').' as pc', 'pc.category_id', '=', 'p.category_id')
                ->leftjoin(Config::get('tables.PRODUCT_BRANDS').' as pb', 'pb.brand_id', '=', 'p.brand_id')
                ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT').' as ssm', 'ssm.product_id', '=', 'pi.product_id')
                ->leftjoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as tas', 'tas.supplier_id', '=', 'pi.supplier_id')
                ->where('pi.is_deleted', Config::get('constants.OFF'))
                ->where('pi.supplier_id', $supplier_id)
                ->selectRaw('ssm.*,p.product_name,p.product_code,pi.*, pc.category, pc.category_id, pi.status, pi.pre_order, tas.*, pb.brand_name,pi.created_on');
        if (isset($data['product_id']) && !empty($data['product_id']))
        {
            $product->where('pi.product_id', $data['product_id']);
        }
        if (isset($data['category']) && !empty($data['category']))
        {
            $product->where('pi.category_id', $data['category']);
        }
        if (isset($data['search_term']) && !empty($data['search_term']))
        {
            $product->whereRaw('(pi.product_name like \'%'.$data['search_term'].'%\'  OR  pc.category_name like \'%'.$data['search_term'].'%\' OR  pb.brand_name like \'%'.$data['search_term'].'%\' OR pi.product_code like  \'%'.$data['search_term'].'%\')');
        }
        if (isset($data['from']) && !empty($data['from']))
        {
            $product->whereDate('pi.created_on', '>=', date('Y-m-d', strtotime($data['from'])));
        }
        if (isset($data['to']) && !empty($data['to']))
        {
            $product->whereDate('pi.created_on', '<=', date('Y-m-d', strtotime($data['to'])));
        }
        if (isset($data['start']) && isset($data['length']))
        {
            $product->skip($data['start'])->take($data['length']);
        }
        if (isset($data['orderby']))
        {
            $product->orderby($data['orderby'], $data['order']);
        }
        else
        {
            $product->orderby('pi.created_on', 'desc');
        }
        if ($count)
        {
            return $product->count();
        }
        else
        {
            if (isset($data['product_id']) && !empty($data['product_id']))
            {
                return $product->first();
            }
            else
            {
                return $product->get();
            }
        }
    }

    public function product_payment_list ($data)
    {
        //print_r($data); exit;
        $product = DB::table(Config::get('tables.PRODUCT_PAYMENT_TYPES').' as ppt')
                ->leftjoin(Config::get('tables.PAYMENT_TYPES').' as pt', 'pt.payment_type_id', '=', 'ppt.payment_type_id')
                ->leftjoin(Config::get('tables.PAYMENT_MODES_LOOKUPS').' as pm', 'pm.paymode_id', '=', 'ppt.paymode_id')
                ->where('ppt.supplier_product_id', $data['supplier_product_id'])
                ->where('ppt.is_deleted', Config::get('constants.OFF'))
                ->select('ppt.ptype_id', 'ppt.created_on', 'pm.mode_name', 'pt.payment_type', 'ppt.supplier_product_id');
        if (isset($data['start']) && isset($data['length']))
        {
            $product->skip($data['start'])->take($data['length']);
        }
        if (isset($data['orderby']))
        {
            $product->orderby($data['orderby'], $data['order']);
        }
        else
        {
            $product->orderby('ppt.created_on', 'desc');
        }
        if (isset($data['count']) && !empty($data['count']))
        {
            return $product->count();
        }
        else
        {
            return $product->get();
        }
    }

    public function get_categories ()
    {
        return DB::table(Config::get('tables.PRODUCT_CATEGORIES'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->get();
    }

    public function get_brands ($data = array())
    {
        $res = DB::table(Config::get('tables.PRODUCT_BRANDS'));
        $res->where('is_deleted', Config::get('constants.OFF'));
        if ($data['account_id'] != 'account_id')
        {
            $res->where('status', Config::get('constants.ACTIVE'));
        }
        else
        {
            $res->whereIn('status', array(
                0,
                1));
        }
        return $res->get();
    }

    public function save_newproducts ($arr)
    {
        $data['product_name'] = $arr['product'];
        $data['product_code'] = rand();
        $data['brand_id'] = $arr['brand'];
        $data['category_id'] = $arr['category'];
        if (isset($arr['image_name']) && !empty($arr['image_name']))
        {
            $data['img_path'] = $arr['image_name'];
        }
        $data['price'] = $arr['price'];
        if (!empty($arr['actual_price']))
        {
            $data['mrp_price'] = $arr['actual_price'];
        }
        else
        {
            $data['mrp_price'] = NULL;
        }
        $data['currency_id'] = $arr['currency_id'];
        $data['discount_value'] = $arr['discount'];
        $data['description'] = $arr['description'];
        $data['in_stock'] = $arr['in_stock'];
        $data['created_on'] = date('Y-m-d H:i:s');
        $data['status'] = 1;
        $data['sku'] = $arr['sku_id'];
        if (!isset($arr['update']))
        {
            $data['supplier_id'] = $arr['supplier_id'];
            $data['created_by'] = $arr['account_id'];
            $ins = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                    ->insertGetId($data);
            $data['product_code'] = $this->generate_product_code($ins);
            if (!empty($ins))
            {
                if (!empty($arr['tag_names']))
                {
                    $tagnames = explode(',', $arr['tag_names']);
                    foreach ($tagnames as $tagname)
                    {
                        $tag_id = DB::table(Config::get('tables.TAG'))
                                ->insertGetId(array(
                            'tag_name'=>$tagname));
                        $result = DB::table(Config::get('tables.PRODUCT_TAG'))
                                ->insertGetId(array(
                            'tag_id'=>$tagid,
                            'product_id'=>$ins));
                    }
                }
                $stock['product_id'] = $ins;
                $stock['stock_on_hand'] = $arr['stock'];
                $stock['commited_stock'] = 0;
                $stock['current_stock'] = $arr['stock'];
                $stock['created_on'] = date('Y-m-d H:i:s');
                return DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT'))
                                ->insertGetId($stock);
            }
        }
        else if (isset($arr['update']) && !empty($arr['product_id']))
        {
            $data['update_by'] = $arr['account_id'];
            $update_details = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                    ->where('product_id', $arr['product_id'])
                    ->update($data);
            if (!empty($update_details))
            {
                $up_stock['stock_on_hand'] = $arr['stock'];
                $up_stock['current_stock'] = $arr['stock'];
                $up_stock['updated_on'] = date('Y-m-d H:i:s');
                $update_status = DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT'))
                        ->where('product_id', $arr['product_id'])
                        ->update($up_stock);
            }
            if (!empty($update_status))
            {
                return 'updated';
            }
            else
            {
                return 'updated';
            }
        }
        return false;
    }

    public function change_product_status ($data)
    {
        $update['status'] = $data['status'];
        $update['update_by'] = $data['account_id'];
        return Db::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                        ->where('product_id', $data['id'])
                        ->update($update);
    }

    public function delete_payment ($data)
    {	
		$update['is_deleted'] = 1;
        return DB::table(Config::get('tables.PRODUCT_PAYMENT_TYPES'))
                        ->where('ptype_id', $data['ptype_id'])
                        ->update($update);
    }

    public function delete_product ($data)
    {
        $update['is_deleted'] = 1;
        return Db::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                        ->where('product_id', $data['id'])
                        ->update($update);
    }

    public function products_combinations ()
    {
        return DB::table(Config::get('tables.PRODUCT_COMBINATIONS').' as p')
                        ->where('p.is_deleted', Config::get('constants.OFF'))
                        ->selectRaw('p.*')->get();
    }

    public function getBrandsList ($data = array(), $count = false)
    {
        extract($data);
        $brands = DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE').' as sa')
                ->leftjoin(Config::get('tables.PRODUCT_BRANDS').' as pb', 'pb.brand_id', '=', 'sa.brand_id')
                ->leftjoin(Config::get('tables.LOGIN_STATUS_LOOKUPS').' as ls', 'ls.status_id', '=', 'sa.status')
                ->where('sa.is_deleted', Config::get('constants.OFF'))
                ->where('sa.supplier_id', $supplier_id);
        if (!empty($data['search_term']) && isset($data['search_term']))
        {
            $brands->whereRaw('(pb.brand_name like \'%'.$data['search_term'].'%\')');
        }
        if (isset($start) && isset($length))
        {
            $brands->skip($start)->take($length);
        }
        if (isset($orderby))
        {
            $brands->orderby($orderby, $order);
        }
        if ($count)
        {
            return $brands->count();
        }
        else
        {
            $brands = $brands->selectRaw('sa.supplier_brand_id as id,sa.updated_on,pb.brand_id,pb.brand_name,pb.url_str,pb.sku,ls.status_id,ls.status_class,ls.status')->get();
            if (!empty($brands))
            {
                array_walk($brands, function(&$brand)
                {
                    $brand->updated_on = date('d-M-Y H:i:s', strtotime($brand->updated_on));
                    $brand->actions = [];
                    if ($brand->status_id == Config::get('constants.INACTIVE'))
                    {
                        $brand->actions[] = [
                            'title'=>'Active',
                            'url'=>URL::route('supplier.catalog.brands.change-status'),
                            'data'=>[
                                'id'=>$brand->id,
                                'status'=>Config::get('constants.ACTIVE')
                            ],
                        ];
                    }
                    if ($brand->status_id == Config::get('constants.ACTIVE'))
                    {
                        $brand->actions[] = [
                            'title'=>'Inactive',
                            'url'=>URL::route('supplier.catalog.brands.change-status'),
                            'data'=>[
                                'id'=>$brand->id,
                                'status'=>Config::get('constants.INACTIVE')
                            ],
                        ];
                    }
                    $brand->actions[] = [
                        'title'=>'Delete',
                        'url'=>URL::route('supplier.catalog.brands.delete'),
                        'data'=>[
                            'confirm'=>'Are you sure, you wants to delete?',
                            'id'=>$brand->id
                        ],
                    ];
                });
            }
            return $brands;
        }
    }

    public function get_products_list ($arr = array())
    {
        extract($arr);
        $products = DB::table(Config::get('tables.PRODUCTS').' as p')
                ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES').' as pc', 'pc.category_id', '=', 'p.category_id')
                ->leftjoin(Config::get('tables.PRODUCT_BRANDS').' as pb', 'pb.brand_id', '=', 'p.brand_id')
                ->where('p.is_deleted', Config::get('constants.OFF'));
        if (!empty($search_term))
        {
            $products->where(function($subquery) use($search_term)
            {
                $subquery->where('p.product_name', 'like', '%'.$search_term.'%');
                $subquery->orWhere('p.description', 'like', '%'.$search_term.'%');
            });
        }
        if (isset($category_id) && !empty($category_id))
        {
            $subquery->where('p.category_id', $category_id);
        }
        if (isset($brand_id) && !empty($brand_id))
        {
            $subquery->where('p.brand_id', $brand_id);
        }
        if (isset($length))
        {
            $products->skip($start)->take($length);
        }
        if (isset($count) && $count)
        {
            return $products->count();
        }
        else
        {
            return $products->selectRaw('p.*,pc.category, pb.brand_name')->get();
        }
    }

    public function check_sku_valid ($data)
    {
        return DB::table(Config::get('tables.PRODUCTS_INFO'))
                        ->where('sku', $data['sku'])
                        ->select('sku')
                        ->get();
    }

    public function saveProcuctPrice ($arr = array())
    {
        $status1 = $status2 = false;
        extract($arr);
        $spp['mrp_price'] = isset($spp['mrp_price']) ? $spp['mrp_price'] : 0;
        $spp['price'] = isset($spp['price']) ? $spp['price'] : 0;
        $spp['currency_id'] = isset($spp['currency_id']) ? $spp['currency_id'] : Config::get('constants.DEFAULT.CURRENCY_ID');
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
                    ->insetGetID(['product_id'=>$spp['product_id'], 'currency_id'=>$spp['currency_id'], 'mrp_price'=>$spp['mrp_price']]);
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

    public function save_supplier_product ($arr = array())
    {
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
        $product = DB::table(Config::get('tables.PRODUCTS_LIST'))
                ->where('product_id', $supplier_product['product_id']);
        if (isset($supplier_product['product_cmb_id']) && !empty($supplier_product['product_cmb_id']))
        {
            $product->where('product_cmb_id', $supplier_product['product_cmb_id']);
        }
        else
        {
            $product->whereNULL('product_cmb_id');
        }
        $product = $product->select('category_id', 'brand_id', 'product_code')->first();
        $supplier_product['updated_by'] = $supplier_product['account_id'];
        unset($supplier_product['account_id']);
        $this->saveSupplierBrand(['supplier_brand'=>['supplier_id'=>$arr['supplier_id'], 'brand_id'=>$product->brand_id]]);
        $this->saveSupplierCategory(['supplier_category'=>['supplier_id'=>$arr['supplier_id'], 'category_id'=>$product->category_id]]);
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
            $supplier_product['transaction_type'] = 1;
            //$this->updateStock($supplier_product);
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
                $supplier_brand_id = DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                        ->insert($supplier_brand);
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

    public function save_payment_types ($data)
    {
        foreach ($data['payment_gateway_select'] as $payment_type_id)
        {
            $res = DB::table(Config::get('tables.PRODUCT_PAYMENT_TYPES'))
                    ->insert(array(
                'payment_type_id'=>$payment_type_id,
                'supplier_product_id'=>$data['supplier_product_id'],
                'created_on'=>date(''.Config::get('constants.DB_DATE_TIME_FORMAT')),
                'paymode_id'=>$data['payment_list_id']));
        }
        if ($res)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function get_supplier_products_list ($arr = array(), $count = false)
    {
        extract($arr);
        $products = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi')
                ->join(Config::get('tables.PRODUCTS').' as p', function($p)
                {
                    $p->on('p.product_id', '=', 'spi.product_id')
                    ->where('p.is_deleted', '=', Config::get('constants.OFF'));
                })
                ->leftJoin(Config::get('tables.PRODUCT_COMBINATIONS').' as pcm', 'pcm.product_cmb_id', '=', 'spi.product_cmb_id')
                ->where('spi.supplier_id', $supplier_id)
                ->where('spi.is_deleted', Config::get('constants.OFF'));
        if (!empty($search_term))
        {
            $products->where(function($subquery) use($search_term)
            {
                $subquery->where('p.product_name', 'like', '%'.$search_term.'%');
                $subquery->orWhere('p.description', 'like', '%'.$search_term.'%');
            });
        }
        if (isset($category_id) && !empty($category_id))
        {
            $products->where('p.category_id', $category_id);
        }
        if (isset($brand_id) && !empty($brand_id))
        {
            $products->where('p.brand_id', $brand_id);
        }
        if ($count)
        {
            return $products->count();
        }
        else
        {
            $products->leftjoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as s', 's.supplier_id', '=', 'spi.supplier_id')
                    ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES').' as pc', 'pc.category_id', '=', 'p.category_id')
                    ->leftjoin(Config::get('tables.PRODUCT_BRANDS').' as pb', 'pb.brand_id', '=', 'p.brand_id')
                    ->leftJoin(Config::get('tables.PRODUCT_MRP_PRICE').' as mrp', function($mrp) use($currency_id)
                    {
                        $mrp->on('mrp.product_id', '=', 'spi.product_id')
                        ->where('mrp.currency_id', '=', $currency_id);
                    })
                    ->leftJoin(Config::get('tables.SUPPLIER_PRODUCT_PRICE').' as sppi', function($sppi)
                    {
                        $sppi->on('sppi.product_id', '=', 'mrp.product_id')
                        ->on('sppi.currency_id', '=', 'mrp.currency_id')
                        ->on('sppi.supplier_id', '=', 'spi.supplier_id');
                    })
                    ->leftJoin(Config::get('tables.SUPPLIER_PRODUCT_CMB_PRICE').' as spcp', function($pcp)
                    {
                        $pcp->on('spcp.product_cmb_id', '=', 'spi.product_cmb_id')
                        ->on('spcp.supplier_id', '=', 'spi.supplier_id')
                        ->on('spcp.currency_id', '=', 'mrp.currency_id');
                    })
                    ->leftjoin(Config::get('tables.CURRENCIES').' as c', 'c.currency_id', '=', 'mrp.currency_id')
                    ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT').' as sm', 'sm.supplier_product_id', '=', 'spi.supplier_product_id')
                    ->leftjoin(Config::get('tables.PRODUCT_CONDITION_LOOKUPS').' as cl', 'cl.condition_id', '=', 'spi.condition_id')
                    ->join(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE').' as spba', function($spba)
                    {
                        $spba->on('spba.brand_id', '=', 'p.brand_id')
                        ->on('spba.supplier_id', '=', 'spi.supplier_id');
                    })
                    ->join(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE').' as spca', function($spba)
                    {
                        $spba->on('spca.category_id', '=', 'p.category_id')
                        ->on('spca.supplier_id', '=', 'spi.supplier_id');
                    })
                    ->leftjoin(Config::get('tables.RATING').' as r', function($rating)
                    {
                        $rating->on('r.relative_post_id', '=', 'spi.supplier_id')
                        ->where('r.post_type_id', '=', Config::get('constants.POST_TYPE.SUPPLIER'));
                    });
            if (isset($product_code) && !empty($product_code))
            {
                $products->where('spi.supplier_product_code', $product_code);
                return $products->selectRaw('p.weight,p.length,p.width,p.height,p.created_by,pcm.product_cmb as product_combination,p.is_exclusive,p.is_combinations,spi.is_replaceable,p.assoc_category_id,p.redirect_id,spi.condition_id,p.visiblity_id,spi.supplier_product_id,spi.product_cmb_id,spi.supplier_product_code,p.product_id,p.product_name,p.description,p.product_code,p.sku,p.category_id,p.brand_id,pc.category,pb.brand_name,s.company_name,s.supplier_id,s.supplier_code,spi.pre_order,spi.status,spi.created_on,spi.updated_on,sm.stock_on_hand,mrp.mrp_price,sppi.price,c.currency_id,c.currency,c.currency_symbol,(select count(product_cmb_id) from '.Config::get('tables.PRODUCT_COMBINATIONS').' where product_id =p.product_id) as combination_cnt')->first();
            }
            if (isset($supplier_product_id) && !empty($supplier_product_id))
            {
                $products->where('spi.supplier_product_id', $supplier_product_id);
                return $products->selectRaw('p.weight,p.length,p.width,p.height,p.created_by,pcm.product_cmb as product_combination,p.is_exclusive,spi.is_replaceable,p.assoc_category_id,p.redirect_id,spi.condition_id,p.visiblity_id,spi.supplier_product_id,spi.supplier_product_code,p.product_id,p.product_name,p.description,p.product_code,p.sku,p.category_id,p.brand_id,pc.category,pb.brand_name,s.company_name,s.supplier_id,s.supplier_code,spi.pre_order,spi.status,spi.created_on,spi.updated_on,sm.stock_on_hand,mrp.mrp_price,sppi.price,c.currency_id,c.currency,c.currency_symbol,(select count(product_cmb_id) from '.Config::get('tables.PRODUCT_COMBINATIONS').' where product_id =p.product_id) as combination_cnt')->first();
            }
            if (isset($length))
            {
                $products->skip($start)->take($length);
            }
            if (isset($orderby))
            {
                $products->orderby($orderby, $order);
            }
            $products = $products->selectRaw('spi.supplier_product_id,spba.supplier_brand_id,spca.supplier_category_id,if(pcm.product_cmb is not null,concat(p.product_name,\' \',pcm.product_cmb),p.product_name) as product_name,p.is_combinations,pcm.product_cmb,spi.supplier_product_id,spi.supplier_product_code,spi.product_cmb_id,p.product_id,p.product_name,p.product_code,p.sku,p.category_id,p.brand_id,pc.category,pb.brand_name,s.company_name,s.supplier_id,s.supplier_code,spi.pre_order,spi.status,spi.created_on,sm.stock_on_hand,mrp.mrp_price,sppi.price,c.currency_id,c.currency,c.currency_symbol')->get();
            $this->imageObj = new ImageController();
            array_walk($products, function(&$product) use($country_id)
            {
                $product->created_on = date('d-M-Y H:i:s', strtotime($product->created_on));
                $imgdata = ($product->is_combinations) ? ['filter'=>['post_type_id'=>Config::get('constants.POST_TYPE.PRODUCT_CMB'), 'relative_post_id'=>$product->product_cmb_id]] : [];
                $product->imgs = $this->imageObj->get_imgs($product->product_id, $imgdata);
                $product->qty = 1;
                $product->country_id = $country_id;
                $this->get_product_discounts($product);
                $product->mrp_price = Commonsettings::currency_format(['amt'=>$product->mrp_price, 'currency_symbol'=>$product->currency_symbol, 'currency'=>$product->currency]);
                $product->price = Commonsettings::currency_format(['amt'=>$product->price, 'currency_symbol'=>$product->currency_symbol, 'currency'=>$product->currency]);
                $product->stock_on_hand = number_format($product->stock_on_hand, 0, '.', ',');
            });
            return $products;
        }
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
                    ->selectRaw('d.discount_id,d.discount,d.description,d.discount_by,dtl.discount_type,dp.discount_value_type,dv.discount_value,dv.currency_id')
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

    public function save_assocaiation ($assoc_category_id, $arr)
    {
        extract($arr);
        $is_replaceable = '';
        $is_exclusive = '';
        return DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi')
                        ->leftjoin(Config::get('tables.PRODUCTS').' as p', 'p.product_id', '=', 'spi.product_id')
                        ->where('spi.supplier_product_id', $supplier_product_id)
                        ->update(array(
                            'p.assoc_category_id'=>$assoc_category_id,
                            'p.is_exclusive'=>$is_exclusive,
                            'p.category_id'=>$product['category_id'],
                            'p.brand_id'=>$product['brand_id'],
                            'spi.is_replaceable'=>$is_replaceable));
    }

    public function get_meta_info ($product_id)
    {
        return Db::table(Config::get('tables.META_INFO').' as mi')
                        ->where('mi.relative_post_id', $product_id)
                        ->select('mi.description', 'mi.meta_keys')
                        ->first();
    }

    public function save_property ($arr = array())
    {
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

    public function save_properties ()
    {
        $postdata = Input::all();
        $postdata = array_filter($postdata);
        $op['status'] = 'ERR';
        $op['msg'] = Lang::get('general.something_went_wrong');
        if (!empty($postdata))
        {
            $result = $this->catalogObj->save_property($postdata);
            if (!empty($result))
            {
                $op['status'] = 'OK';
                $op['msg'] = Lang::get('general.property_updated_successfully');
            }
            else
            {
                $op['status'] = 'WARN';
                $op['msg'] = Lang::get('general.there_is_no_changes');
            }
        }
        return Response::json($op);
    }

    public function addCmbToSupplier ($arr = array())
    {
        $supplier_products = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                ->where('product_id', $product_id)
                ->where('supplier_id', $supplier_id)
                ->select('supplier_id', 'product_id', 'product_cmb_id')
                ->get();
        if (!empty($supplier_products) && count($supplier_products) == 1 && $supplier_products[0]->product_cmb_id == $product_cmb_id)
        {
            $supplier_product = $supplier_products[0];
            if (DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                            ->where('product_id', $supplier_product->product_id)
                            ->where('supplier_id', $supplier_product->supplier_id)
                            ->update(['product_cmb_id'=>$product_cmb_id]))
            {
                DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT'))
                        ->where('product_id', $supplier_product->product_id)
                        ->where('supplier_id', $supplier_product->supplier_id)
                        ->update(['product_cmb_id'=>$product_cmb_id]);
                $currencies = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                        ->where('product_id', $supplier_product->product_id)
                        ->where('supplier_id', $supplier_product->supplier_id)
                        ->lists('currency_id');
                foreach ($currencies as $$currency)
                {
                    DB::table(Config::get('tables.SUPPLIER_PRODUCT_CMB_PRICE'))
                            ->insertGetId([
                                'supplier_id'=>$supplier_product->supplier_id,
                                'product_cmb_id'=>$product_cmb_id,
                                'currency_id'=>$$currency
                    ]);
                }
            }
        }
        else
        {

        }
    }

    public function saveProductCmbPrice ($arr = array())
    {
        extract($arr);
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

    public function product_propery_values_for_checktree ($property_id)
    {
        return DB::table(Config::get('tables.PRODUCT_PROPERTY_KEY_VALUES').' as pv')
                        ->leftjoin(Config::get('tables.UNITS').' as u', 'u.unit_id', '=', 'pv.unit_id')
                        ->where('pv.property_id', $property_id)
                        ->where('pv.is_deleted', Config::get('constants.OFF'))
                        ->selectRaw('pv.value_id as id,concat(pv.key_value,if(u.unit is not null,concat(\' \',u.unit),\'\')) as label,pv.property_id')
                        ->orderBy('label', 'asc')
                        ->get();
    }

    public function check_category_exists ($data)
    {
        return Db::table(Config::get('tables.PRODUCT_CATEGORIES'))
                        ->where('category', $data)
                        ->first();
    }

    public function generate_product_code ($product_id)
    {
        $function_ret = '';
        $profix = $product_id;
        $iLoop = true;
        $disp = $this->rKeyGen(3, 1);
        $disp1 = 'PR'.$disp.$profix;
        return $disp1;
    }

    function rKeyGen ($digits, $datatype)
    {
        $key = '';
        $tem = '';
        $poss = array();
        $poss_ALP = array();
        $j = 0;
        if ($datatype == 1)
        {
            for ($i = 49; $i < 58; $i++)
            {
                $poss[$j] = chr($i);
                $poss_ALP[$j] = $poss[$j];
                $j = $j + 1;
            }
            for ($k = 1; $k <= $digits; $k++)
            {
                $key = $key.$poss[rand(1, 8)];
            }
            $key;
        }
        else
        {
            $key = $this->rKeyGen_ALPHA($digits, false);
        }
        return $key;
    }

    function rKeyGen_ALPHA ($digits, $lc)
    {
        $key = '';
        $tem = '';
        $poss = array();
        $j = 0;
        // Place numbers 0 to 10 in the array
        for ($i = 50; $i < 57; $i++)
        {
            $poss[$j] = chr($i);
            $j = $j + 1;
        }
        // Place A to Z in the array
        for ($i = 65; $i < 90; $i++)
        {
            $poss[$j] = chr($i);
            $j = $j + 1;
        }
        // Place a to z in the array
        for ($k = 97; $k < 122; $k++)
        {
            $poss[$j] = chr($k);
            $j = $j + 1;
        }
        $ub = 0;
        if ($lc == true)
            $ub = 61;
        else
            $ub = 35;
        for ($k = 1; $k <= 3; $k++)
        {
            $key = $key.$poss[rand(0, $ub)];
        }
        for ($k = 4; $k <= $digits; $k++)
        {
            $key = $key.$poss[rand(0, $ub)];
        }
        return $key;
    }

    public function get_products ($supplier_id)
    {
        return DB::table(Config::get('tables.SUPPLIER_PRODUCT_INFO'))
                        ->where('supplier_id', $supplier_id)
                        ->count('product_id');
    }

    public function get_sales ($supplier_id)
    {
        return DB::table(Config::get('tables.SUB_ORDERS'))
                        ->where('supplier_id', $supplier_id)
                        ->where('is_deleted', 0)
                        ->whereIn('sub_order_status_id', array(
                            1,
                            4))
                        ->sum('net_pay');
    }

    public function get_orders ($supplier_id)
    {
        return DB::table(Config::get('tables.SUB_ORDERS'))
                        ->where('supplier_id', $supplier_id)
                        ->where('is_deleted', 0)
                        ->whereIn('sub_order_status_id', array(
                            1,
                            4))
                        ->count('sub_order_status_id');
    }

    public function get_open_orders ($supplier_id)
    {
        return DB::table(Config::get('tables.SUB_ORDERS'))
                        ->where('supplier_id', $supplier_id)
                        ->where('is_deleted', 0)
                        ->where('sub_order_status_id', 0)
                        ->count('sub_order_id');
    }

    public function placed_orders ($supplier_id)
    {
        return DB::table(Config::get('tables.SUB_ORDERS'))
                        ->where('supplier_id', $supplier_id)
                        ->where('sub_order_status_id', 0)
                        ->count('sub_order_id');
    }

    public function get_customers ($supplier_id)
    {
        return DB::table(Config::get('tables.SUPPLIER_ORDER_CUSTOMERS'))
                        ->where('supplier_id', $supplier_id)
                        ->count('account_id');
    }

    public function get_product_stocks ($id)
    {
        return DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT').' as stm')
                        ->join(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as pi', 'pi.product_id', '=', 'stm.product_id')
                        ->where('stm.product_id', $id)->selectRaw('stm.*,pi.product_name')->first();
    }

    public function order_details ($data = array())
    {
        return DB::table(Config::get('tables.SUB_ORDERS').' as sop')
                        ->leftJoin(Config::get('tables.ORDER_ITEMS').' as op', 'op.order_id', '=', 'sop.order_id')
                        ->leftJoin(Config::get('tables.PRODUCT_ITEMS').' as pi', 'pi.product_id', '=', 'op.product_id')
                        ->leftJoin(Config::get('tables.SUPPLIER_PRODUCT_ORDER_STATUS_LOOKUPS').' as posl', 'posl.order_status_id', '=', 'sop.order_status_id')
                        ->where('sop.order_id', $data['order_id'])
                        ->selectRaw('sop.*,pi.*,sop.order_code,sop.amount,sop.order_date,sop.last_updated')
                        ->get();
    }

    public function order_cancel ($arr = array())
    {
        $status['order_status_id'] = 5;
        $status['last_updated'] = Config::get('tables.CURRENT_DATE');
        return DB::table(Config::get('tables.SUB_ORDERS'))
                        ->whereIn('order_status_id', array(
                            0,
                            1,
                            2))
                        ->where('order_id', $arr['order_id'])
                        ->update($status);
    }

    public function currency_list ()
    {
        return DB::table(Config::get('tables.CURRENCIES'))
                        ->orderby('currency', 'asc')
                        ->get();
    }

    public function get_suppliers_couries ($arr = array())
    {
        extract($arr);
        $query = DB::table(Config::get('tables.ACCOUNT_LOGISTICS').' as al')
                ->leftJoin(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'al.account_id')
                ->where('alis_deleted', Config::get('constants.OFF'));
        if (!empty($search_term))
        {
            $query->where('al.logistic', 'like', '%'.$search_term.'%');
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
            $query->orderBy('al.logistic', 'asc');
        }
        if (isset($count) && $count)
        {
            return $query->count();
        }
        else
        {
            return $query->select('al.logistic_id', 'al.logistic', 'am.created_on')->get();
        }
    }

    public function save_courier ($arr)
    {
        extract($arr);
        if (isset($courier) && !empty($courier))
        {
            if (isset($courier_id) && !empty($courier_id))
            {
                $courier['updated_on'] = date('Y-m-d H:i:s');
                return DB::table(Config::get('tables.COURIER_SERVICE_PROVIDERS'))
                                ->where('courier_id', $courier_id)
                                ->update($courier);
            }
            else
            {
                $courier['created_on'] = date('Y-m-d H:i:s');
                $courier_id = DB::table(Config::get('tables.COURIER_SERVICE_PROVIDERS'))
                        ->insertGetId($courier);
            }
        }
        if (!empty($courier_id))
        {
            if (DB::table(Config::get('tables.SUPPLIER_COURIER'))
                            ->where('courier_id', $courier_id)
                            ->where('supplier_id', $supplier_id)
                            ->count() <= 0)
            {
                $created_on = date('Y-m-d H:i:s');
                return DB::table(Config::get('tables.SUPPLIER_COURIER'))
                                ->insertGetId(compact('supplier_id', 'courier_id', 'created_on'));
            }
        }
        return false;
    }

    public function get_courier_details ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.COURIER_SERVICE_PROVIDERS').' as csp')
                        ->leftJoin(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'csp.country_id')
                        ->leftJoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'csp.state_id')
                        ->where('csp.is_deleted', Config::get('constants.OFF'))
                        ->where('csp.courier_id', $courier_id)
                        ->selectRaw('csp.*,lc.country,ls.state')
                        ->first();
    }

    public function delete_courier ($arr)
    {
        extract($arr);
        $courier = array();
        $courier['updated_on'] = date('Y-m-d H:i:s');
        $courier['is_deleted'] = Config::get('constants.ON');
        return DB::table(Config::get('tables.SUPPLIER_COURIER'))
                        ->where('courier_id', $courier_id)
                        ->where('supplier_id', $supplier_id)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->update($courier);
    }

    public function get_couries_to_add ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.COURIER_SERVICE_PROVIDERS').' as csp')
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->whereNotIn('courier_id', function($subquery) use($supplier_id)
                        {
                            $subquery->from(Config::get('tables.SUPPLIER_COURIER'))
                            ->where('supplier_id', $supplier_id)
                            ->where('is_deleted', Config::get('constants.OFF'))
                            ->lists('courier_id');
                        })
                        ->select('courier_id', 'courier')
                        ->get();
    }

    public function courier_modes_list ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.COURIER_MODE_LOOKUPS'))
                        ->where('courier_id', $courier_id)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->select('mode_id', 'mode')
                        ->get();
    }

    public function courier_modes_save ($arr = array())
    {
        extract($arr);
        if (isset($mode_id) && !empty($mode_id))
        {
            return DB::table(Config::get('tables.COURIER_MODE_LOOKUPS'))
                            ->where('mode_id', $mode_id)
                            ->where('is_deleted', Config::get('constants.OFF'))
                            ->update($mode);
        }
        else
        {
            return DB::table(Config::get('tables.COURIER_MODE_LOOKUPS'))
                            ->insertGetId($mode);
        }
    }

    public function courier_modes_details ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.COURIER_MODE_LOOKUPS'))
                        ->where('mode_id', $mode_id)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->first();
    }

    public function courier_modes_delete ($arr = array())
    {
        extract($arr);
        $mode = array(
            'is_deleted'=>Config::get('constants.ON'));
        return DB::table(Config::get('tables.COURIER_MODE_LOOKUPS'))
                        ->where('mode_id', $mode_id)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->update($mode);
    }

    public function get_stock_log_report ($data = array())
    {
        $product = DB::table(Config::get('tables.SUPPLER_PRODUCT_STOCK_LOG').' as sop')
                ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi', 'spi.supplier_product_id', '=', 'sop.supplier_product_id')
                ->leftjoin(Config::get('tables.PRODUCTS').' as pro', 'pro.product_id', '=', 'spi.product_id')
                ->leftjoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as sup', 'sup.supplier_id', '=', 'spi.supplier_id')
                ->leftjoin(Config::get('tables.ACCOUNT_MST').' as mst', 'mst.account_id', '=', 'sup.account_id')
                ->where('spi.supplier_id', $data['supplier_id'])
                ->selectRaw('sop.*,pro.product_name,sup.supplier_id,mst.account_id,sup.company_name');
        if (isset($data['product_id']) && !empty($data['product_id']))
        {
            $product->where('pro.product_id', $data['product_id']);
        }
        if (isset($data['supplier_id']) && !empty($data['supplier_id']))
        {
            $product->where('sup.supplier_id', $data['supplier_id']);
        }
        if (isset($data['search_term']) && !empty($data['search_term']))
        {
            $product->whereRaw('(sup.company_name like \'%'.$arr['search_txt'].'%\'  OR  pro.product_name like \'%'.$arr['search_txt'].'%\')');
        }
        if (isset($data['from']) && !empty($data['from']))
        {
            $product->whereDate('sop.created_on', '>=', date('Y-m-d', strtotime($data['from'])));
        }
        if (isset($data['to']) && !empty($data['to']))
        {
            $product->whereDate('sop.created_on', '<=', date('Y-m-d', strtotime($data['to'])));
        }
        if (isset($data['orderby']))
        {
            $product->orderby($data['orderby'], $data['order']);
        }
        if (isset($data['counts']) && !empty($data['counts']))
        {
            return $product->count();
        }
        else
        {
            return $product->get();
        }
    }

    public function product_items ($data = array())
    {
        return DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as sop')
                        ->where('sop.supplier_product_id', $data['supplier_product_id'])
                        ->first();
    }

    public function get_combination_value ($data = array())
    {
        return DB::table(Config::get('tables.SUPPLIER_PRODUCT_COMBINATIONS').' as spc')
                        ->where('spc.product_cmb_id', $data['product_cmb_id'])
                        ->where('spc.supplier_id', $data['supplier_id'])
                        ->first();
    }

    public function get_product_id ($product_code)
    {
        return DB::table(Config::get('tables.PRODUCTS_LIST').' as p')
                        ->where('p.product_code', $product_code)
                        ->where('p.is_deleted', Config::get('constants.OFF'))
                        ->select('p.product_code', 'p.product_cmb_id', 'p.product_id', 'p.product_name', 'p.sku')
                        ->first();
    }

    public function get_combination_product_by_id ($product_id)
    {
        if (!empty($product_id))
        {
            //   $data['is_combinations'] =  Config::get('constants.ON');
            /* return   DB::table(Config::get('tables.PRODUCT_COMBINATIONS').' as pc')
              ->leftJoin(Config::get('tables.PRODUCTS').' as p','p.product_id','=','pc.product_id')
              ->where('pc.product_id',$product_id)
              ->where('is_combinations',Config::get('constants.OFF'))
              ->where('p.is_deleted',Config::get('constants.OFF'))
              ->where('p.status',Config::get('constants.ON'))
              ->where('pc.status',Config::get('constants.ON'))
              ->where('pc.is_deleted',Config::get('constants.OFF'))
              ->distinct('pc.product_id')
              ->update($data); */
            return DB::table(Config::get('tables.PRODUCTS').' as p')
                            ->where('p.product_id', $product_id)
                            ->where('p.is_deleted', Config::get('constants.OFF'))
                            ->update(['p.is_combinations'=>DB::raw('(select if(count(pc.product_id)>0,1,0) from '.Config::get('tables.PRODUCT_COMBINATIONS').' as `pc` where `pc`.`product_id` = p.product_id and `pc`.`status` = '.Config::get('constants.ON').' and `pc`.`is_deleted` = '.Config::get('constants.OFF').')')]);
        }
        return false;
    }

    //supplier_id,product_id must ,
    //keys:product_code=supplier_product_code,supplier_product_id-get_product_combination_list
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
                    ->select('pc.product_cmb_code', 'pc.product_cmb_id')
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

    public function change_cover_image ($data = array())
    {
        $image = DB::table(Config::get('tables.IMGS'))
                ->update(array(
            'primary'=>Config::get('constants.OFF')));
        return DB::table(Config::get('tables.IMGS'))
                        ->where('img_id', $data['image_id'])
                        ->update(array(
                            'primary'=>Config::get('constants.ON')));
        return false;
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

    public function checkProduct ($arr = array())
    {
        extract($arr);
        $query = DB::table(Config::get('tables.PRODUCTS').' as p')
				->leftjoin(Config::get('tables.PRODUCT_DETAILS').' as pd', 'pd.product_id', '=', 'p.product_id')
                ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi', function($spi) use($supplier_id)
				{
					$spi->on('spi.product_id', '=', 'pd.product_id')
					->on('spi.product_cmb_id', '=', 'pd.product_cmb_id')
					->where('spi.supplier_id', '=', $supplier_id);
				});
        if (isset($eanbarcode) && !empty($eanbarcode))
        {
            $query->where('pd.eanbarcode', $eanbarcode);
        }
        if (isset($upcbarcode) && !empty($upcbarcode))
        {
            $query->where('pd.upcbarcode', $upcbarcode);
        }
        $product = $query->select('pd.product_code', 'spi.supplier_product_id')->first();
        return $product;
    }

    public function save_package_info ($data)
    {
        extract($data);
        $product['size_unit_id'] = Config::get('constants.DEFAULT.SIZE_UNIT_ID');
        $product['weight_unit_id'] = Config::get('constants.DEFAULT.WEIGHT_UNIT_ID');
        return DB::table(Config::get('tables.PRODUCTS').' as p')
                        ->where('p.product_id', $product['product_id'])
                        ->update($product);
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

}
