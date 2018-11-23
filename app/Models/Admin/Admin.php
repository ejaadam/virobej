<?php

namespace App\Models\Admin;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\ImageLib;
use Config;
use URL;

class Admin extends Model
{

    public function __construct ()
    {
        //$this->imageObj = new ImageController();        
		$this->imageObj = new ImageLib();
    }

    public function brands_list_chosen ($arr = array())
    {
        extract($arr);
        $query = DB::table(Config::get('tables.PRODUCT_BRANDS'));
        if (isset($ids) && !empty($ids))
        {
            $query->whereIn('brand_id', $ids);
        }
        if (isset($data['q']) && !empty($data['q']))
        {
            $query->where('brand_name', 'like', '%'.$data['q'].'%');
        }
        return $query->selectRaw('brand_id as id, brand_name as text')
                        ->get();
    }

    public function pages_list_chosen ()
    {
        return DB::table(Config::get('tables.PAGES'))
                        ->selectRaw('page_id as id, page as text')
                        ->get();
    }

    public function categories_list_chosen ($arr = array())
    {
        extract($arr);
        $query = DB::table(Config::get('tables.PRODUCT_CATEGORIES'));
        if (isset($ids) && !empty($ids))
        {
            $query->whereIn('category_id', $ids);
        }
        if (isset($data['q']) && !empty($data['q']))
        {
            $query->where('category', 'like', '%'.$data['q'].'%');
        }
        return $query->selectRaw('category_id as id, category as text')
                        ->get();
    }

    public function products_list_chosen ($arr = array())
    {
        extract($arr);
        $query = DB::table(Config::get('tables.PRODUCTS'));
        if (isset($ids) && !empty($ids))
        {
            $query->whereIn('product_id', $ids);
        }
        if (isset($data['q']) && !empty($data['q']))
        {
            $query->where('product', 'like', '%'.$data['q'].'%');
        }
        return $query->selectRaw('product_id as id, product_name as text')
                        ->where('is_verified', 1)
                        ->where('is_deleted', 0)
                        ->get();
    }

    public function supplier_list_chosen ($arr = array())
    {
        extract($arr);
        $query = DB::table(Config::get('tables.ACCOUNT_SUPPLIERS'));
        if (isset($ids) && !empty($ids))
        {
            $query->whereIn('supplier_id', $ids);
        }
        if (isset($data['q']) && !empty($data['q']))
        {
            $query->where('company_name', 'like', '%'.$data['q'].'%');
        }
        return $query->select('supplier_id as id', 'company_name as text')
                        ->where('is_verified', 1)
                        ->where('is_deleted', 0)
                        ->get();
    }

    public function product_combinations_list_chosen ($arr = array())
    {
        extract($arr);
        $query = DB::table(Config::get('tables.PRODUCT_COMBINATIONS'));
        if (isset($ids) && !empty($ids))
        {
            $query->whereIn('product_cmb_id', $ids);
        }
        if (isset($data['q']) && !empty($data['q']))
        {
            $query->where('product_cmb', 'like', '%'.$data['q'].'%');
        }
        return $query->selectRaw('product_cmb_id as id, product_cmb as text')
                        ->get();
    }    

    public function check_product_code ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi')
                        ->where('spi.is_deleted', Config::get('constants.OFF'))
                        ->selectRaw('spi.supplier_product_id')
                        ->where('spi.supplier_product_code', $supplier_product_code)
                        ->first();
    }    

    public function productDetailsURL ($product)
    {
        if (isset($product->product_cmb_code))
        {
            return implode('/', [
                        'product',
                        $product->category_string,
                        $product->product_slug.'-'.$product->cmb_sku]).'?pid='.$product->product_code.'&cid='.$product->product_cmb_code;
        }
        else
        {
            return implode('/', [
                        'product',
                        $product->category_string,
                        $product->product_slug]).'?pid='.$product->product_code;
        }
    }

    public function getSupplierURL ($data)
    {
        return 'seller/'.str_replace(' ', '-', strtolower($data->supplier)).'?id='.$data->supplier_code;
    }

    public function getProductSuppliersURL ($product)
    {
        return 'product/sellers/'.$product->category_string.'/'.$product->product_name.'?pid='.$product->product_code;
    }

}
