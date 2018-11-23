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
use Lang;

class APISupplierCatalog extends Model
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
                    ->selectRaw('sc.supplier_category_id as id, pc.category, sc.updated_on, ls.status_id, ls.status_class, ls.status, pcp.parent_category_id, pc.category_id, ppc.category as parent_category')
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
                            'url'=>URL::to('api/v1/seller/catalog/categories/change-status'),
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

    public function changeCategoryStaus ($data)
    {
        extract($data);
        $update = [];
        $update['status'] = $status;
        $update['updated_by'] = $account_id;
        $update['updated_on'] = date('Y-m-d H:i:s');
        $query = Db::table(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE'))
                ->where('supplier_category_id', $id)
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

    public function deleteCategory ($arr = array())
    {
        extract($arr);
        $update = [];
        $update['is_deleted'] = Config::get('constants.ON');
        $update['updated_by'] = $account_id;
        $update['updated_on'] = date('Y-m-d H:i:s');
        return Db::table(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE'))
                        ->where('supplier_category_id', $id)
                        ->where('supplier_id', $supplier_id)
                        ->update($update);
    }

    public function add_categories ($data = array())
    {
        extract($data);
        $op = array();
        if (!empty($data))
        {
            if (Db::table(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE'))
                            ->where('category_id', $category_id)
                            ->where('supplier_id', $supplier_id)->first())
            {
                $op['statusCode'] = 208;
                $op['msg'] = Lang::get('general.actions.already_exist', ['label'=>Lang::get('general.fields.category')]);
            }
            else
            {
                if (Db::table(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE'))->insert($data))
                {
                    $op['statusCode'] = 200;
                    $op['msg'] = Lang::get('general.actions.added', ['label'=>Lang::get('general.fields.category')]);
                }
            }
            return $op;
        }
        return false;
    }

    public function getBrandsList ($data = array(), $count = false)
    {
        extract($data);
        $brands = DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE').' as sa')
                ->join(Config::get('tables.PRODUCT_BRANDS').' as pb', 'pb.brand_id', '=', 'sa.brand_id')
                ->where('sa.is_deleted', Config::get('constants.OFF'))
                ->where('sa.supplier_id', $supplier_id);
        if (!empty($data['search_term']) && isset($data['search_term']))
        {
            $brands->whereRaw('(pb.brand_name like \'%'.$data['search_term'].'%\')');
        }

        if ($count)
        {
            return $brands->count();
        }
        else
        {
            if (isset($start) && isset($length))
            {
                $brands->skip($start)->take($length);
            }
            if (isset($orderby))
            {
                $brands->orderby($orderby, $order);
            }
            $brands = $brands->leftjoin(Config::get('tables.LOGIN_STATUS_LOOKUPS').' as ls', 'ls.status_id', '=', 'sa.status')
                            ->leftjoin(Config::get('tables.VERIFICATION_STATUS_LOOKUPS').' as vs', 'vs.is_verified', '=', 'sa.is_verified')
                            ->selectRaw('sa.supplier_brand_id as id,sa.updated_on,pb.brand_name,ls.status_id,ls.status_class,ls.status,vs.verification,vs.verification_class')->get();
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
                            'url'=>URL::to('api/v1/seller/catalog/brands/change-status'),
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
                            'url'=>URL::to('api/v1/seller/catalog/brands/change-status'),
                            'data'=>[
                                'id'=>$brand->id,
                                'status'=>Config::get('constants.INACTIVE')
                            ],
                        ];
                    }

                    $brand->actions[] = [
                        'title'=>'Delete',
                        'url'=>URL::to('api/v1/seller/catalog/brands/delete'),
                        'data'=>[
                            'confirm'=>'Are you sure? You want to delete '.$brand->brand_name.'?',
                            'id'=>$brand->id
                        ],
                    ];
                    unset($brand->status_id);
                    unset($brand->id);
                });
            }
            return $brands;
        }
    }

    public function addBrand ($arr = array())
    {
        extract($arr);
        $res = false;
        DB::beginTransaction();
        $brand = DB::table(Config::get('tables.PRODUCT_BRANDS'))
                ->where('brand_name', $brand_name)
                ->selectRaw('brand_id,is_deleted')
                ->first();
        if (!empty($brand))
        {
            $brand_id = $brand->brand_id;
            if ($brand->is_deleted)
            {
                DB::table(Config::get('tables.PRODUCT_BRANDS'))
                        ->where('brand_id', $brand_id)
                        ->update(['is_deleted'=>Config::get('constants.OFF'), 'updated_on'=>date('Y-m-d H:i:s'), 'updated_by'=>$account_id]);
            }
        }
        else
        {
            $ibrand['brand_name'] = $brand_name;
            $ibrand['sku'] = $ibrand['url_str'] = str_replace(' ', '-', strtolower($brand_name));
            $ibrand['created_by'] = $ibrand['updated_by'] = $account_id;
            $ibrand['created_on'] = $ibrand['updated_on'] = date('Y-m-d H:i:s');
            $brand_id = DB::table(Config::get('tables.PRODUCT_BRANDS'))
                    ->insertGetId($ibrand);
        }
        if (!DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                        ->where('supplier_id', $supplier_id)
                        ->where('brand_id', $brand_id)
                        ->exists())
        {
            $ins_associate['brand_id'] = $brand_id;
            $ins_associate['supplier_id'] = $supplier_id;
            $ins_associate['updated_by'] = $account_id;
            $ins_associate['updated_on'] = date('Y-m-d H:i:s');
            $res = DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                    ->insertGetId($ins_associate);
        }
        else
        {
            $res = DB::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                    ->where('supplier_id', $supplier_id)
                    ->where('brand_id', $brand_id)
                    ->where('is_deleted', Config::get('constants.ON'))
                    ->update(['is_deleted'=>Config::get('constants.OFF'), 'updated_on'=>date('Y-m-d H:i:s'), 'updated_by'=>$account_id]);
        }
        $res ? DB::commit() : DB::rollback();
        return $res;
    }

    /**
     * @param array $data associate of status, account_id, supplier_id
     * @return bool true if false else false
     */
    public function changeBrandStatus ($data = array())
    {
        extract($data);
        $update = [];
        $update['status'] = $status;
        $update['updated_by'] = $account_id;
        $update['updated_on'] = date('Y-m-d H:i:s');
        $query = Db::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                ->where('supplier_brand_id', $id)
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

    public function checkBrandName ($data)
    {
        return Db::table(Config::get('tables.PRODUCT_BRANDS'))
                        ->where('brand_name', 'like', '%'.$data['brand'].'%')
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->lists('brand_name');
    }

    /**
     *
     * @param array $arr associate array of id,account_id,supplier_id
     * @return bool true if record is deleted else false
     */
    public function deleteBrand ($arr = array())
    {
        extract($arr);
        $update = [];
        $update['is_deleted'] = Config::get('constants.ON');
        $update['updated_by'] = $account_id;
        $update['updated_on'] = date('Y-m-d H:i:s');
        return Db::table(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                        ->where('supplier_brand_id', $id)
                        ->where('supplier_id', $supplier_id)
                        ->update($update);
    }

    public function getProductStockList ($data, $count = false, $categoriesList = false, $brandsList = false)
    {
        extract($data);
        $productStocks = DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT').' as ssm')
                ->join(Config::get('tables.SUPPLIER_PRODUCTS_LIST').' as sp', 'sp.supplier_product_id', '=', 'ssm.supplier_product_id')
                ->join(Config::get('tables.PRODUCT_STOCK_STATUS_LOOKUPS').' as ss', 'ss.stock_status_id', '=', 'sp.stock_status_id')
                ->where('sp.is_deleted', Config::get('constants.OFF'))
                ->where('sp.supplier_id', $supplier_id);
        if ($categoriesList)
        {
            return $productStocks->selectRaw('DISTINCT(sp.category_id),sp.category')
                            ->orderby('sp.category', 'ASC')
                            ->lists('category', 'category_id');
        }
        if ($brandsList)
        {
            return $productStocks->selectRaw('DISTINCT(sp.brand_id),sp.brand_name')
                            ->orderby('sp.brand_name', 'ASC')
                            ->lists('brand_name', 'brand_id');
        }
        if (isset($category_id) && !empty($category_id))
        {
            $productStocks->where('sp.category_id', $category_id);
        }
        if (isset($brand_id) && !empty($brand_id))
        {
            $productStocks->where('sp.brand_id', $brand_id);
        }
        if (isset($search_term) && !empty($search_term))
        {
            $productStocks->whereRaw('(sp.product_name like \'%'.$search_term.'%\'  OR  sp.category like \'%'.$data['search_term'].'%\' OR  sp.brand_name like \'%'.$data['search_term'].'%\' OR sp.supplier_product_code like  \'%'.$data['search_term'].'%\')');
        }
        if (isset($from) && !empty($from))
        {
            $productStocks->whereDate('ssm.updated_on', '>=', date('Y-m-d', strtotime($from)));
        }
        if (isset($to) && !empty($to))
        {
            $productStocks->whereDate('ssm.updated_on', '<=', date('Y-m-d', strtotime($to)));
        }
        if (isset($start) && isset($start))
        {
            $productStocks->skip($start)->take($length);
        }
        if (isset($orderby))
        {
            $productStocks->orderby($orderby, $order);
        }
        else
        {
            $productStocks->orderby('ssm.updated_on', 'desc');
        }
        if ($count)
        {
            return $productStocks->count();
        }
        else
        {
            $stocks = $productStocks->selectRaw('ssm.stock_on_hand,ssm.commited_stock,ssm.sold_items,ssm.updated_on,sp.product_name,sp.product_code,sp.category, sp.brand_name,ss.status')
                    ->get();
            array_walk($stocks, function(&$stock)
            {
                $stock->updated_on = date('d-M-Y H:i:s', strtotime($stock->updated_on));
                $stock->stock_on_hand = number_format($stock->stock_on_hand, 0, ',', '.');
                $stock->commited_stock = number_format($stock->commited_stock, 0, ',', '.');
                $stock->sold_items = number_format($stock->sold_items, 0, ',', '.');
                $stock->edit_product = URL::route('supplier.products.edit', ['supplier_product_code'=>$stock->product_code]);
            });
            return $stocks;
        }
    }

}
