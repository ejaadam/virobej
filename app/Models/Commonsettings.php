<?php
namespace App\Models;
use DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Database\Eloquent\Model;
use Config;
use Request;

class Commonsettings extends Model
{

    public function product_visibility_list ()
    {
        return DB::table(Config::get('tables.PRODUCT_VISIBLITY_LOOKUPS'))
                        ->get();
    }

    public function generate_url ($postdata)
    {
        if (isset($postdata['category_id']) && !empty($postdata['category_id']))
        {
            $cat_url = DB::table(Config::get('tables.PRODUCT_CATEGORIES').' as child_category')
                    ->leftJoin(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as parent_category', 'child_category.category_id', '=', 'parent_category.category_id')
                    ->leftJoin(Config::get('tables.PRODUCT_CATEGORIES').' as parent_category', 'child_category.category_id', '=', 'parent_category.category_id')
                    ->where('child_category.category_id', $postdata['category_id'])
                    ->selectRaw('child_category.url_str as child_url_str,parent_category.url_str as parent_url_str,child_category.category_code')
                    ->first();

            if (!empty($cat_url))
            {
                $url_string = $cat_url->parent_url_str.'/'.$cat_url->child_url_str.'/br?spath='.$cat_url->category_code;
            }
            if (!empty($url_string))
            {
                return $url_string;
            }
        }
    }

    public function get_brand ($brand_id)
    {
        return DB::table(Config::get('tables.PRODUCT_BRANDS'))
                        ->where('brand_id', $brand_id)
                        ->pluck('url_str');
    }

    public function get_property ($property_id)
    {
        return DB::table(Config::get('tables.PRODUCT_PROPERTY_KEYS'))
                        ->where('property_id', $property_id)
                        ->pluck('property');
    }

    public function get_property_details ($value_id)
    {
        return DB::table(Config::get('tables.PRODUCT_PROPERTY_KEY_VALUES').' as pk')
                        ->leftJoin(Config::get('tables.PRODUCT_PROPERTY_KEYS').' as pks', 'pks.property_id', '=', 'pk.property_id')
                        ->where('pk.value_id', $value_id)
                        ->select('pks.property', 'pk.key_value')
                        ->first();
    }

    public function product_condition_list ()
    {
        return DB::table(Config::get('tables.PRODUCT_CONDITION_LOOKUPS'))
                        ->get();
    }

    public function top_menu_list ($arr)
    {
        extract($arr);
        $res = DB::table(Config::get('tables.SITE_MENU_NAVIGATION').' as smn')
                ->leftJoin(Config::get('tables.SITE_MENUS').' as sm', 'smn.menu_id', '=', 'sm.menu_id')
                ->where('sm.is_deleted', Config::get('constants.OFF'))
                ->where('smn.type', 1)
                ->select('smn.navigation_name', 'smn.navigation_id', 'smn.no_of_columns');
        if (isset($menu_id) && !empty($menu_id))
        {
            $res->where('sm.menu_id', $menu_id);
        }
        $res = $res->get();
        return $res;
    }

    public function menu_position_list ()
    {
        return DB::table(Config::get('tables.SITE_MENU_POSITIONS_LOOKUPS'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->select('position_name', 'menu_postion_id')
                        ->get();
    }

    public function group_list ($data)
    {
        $res = DB::table(Config::get('tables.SITE_MENU_NAVIGATION').' as smn')
                ->leftJoin(Config::get('tables.SITE_MENUS').' as sm', 'smn.menu_id', '=', 'sm.menu_id')
                ->where('sm.is_deleted', Config::get('constants.OFF'))
                ->where('sm.menu_id', $data['menu_id'])
                ->where('smn.type', 2)
                ->select('smn.navigation_name', 'smn.navigation_id')
                ->get();
        return $res;
    }

    public function redirect_disabled_list ()
    {
        return DB::table(Config::get('tables.PRODUCT_REDIRECT_LOOKUPS'))
                        ->get();
    }

    static public function getSettings ()
    {
        return DB::table(Config::get('tables.SITE_SETTINGS').' as ss')
                        ->join(Config::get('tables.LANGUAGE_LOOKUPS').' as ll', 'll.language_id', '=', 'ss.site_language_id')
                        ->selectRaw('ss.*,ll.iso_code as language_iso_code')
                        ->first();
    }

    public function get_settings_id ($setting_key = 0)
    {

        return DB::table(Config::get('tables.SETTINGS'))
                        ->where('setting_key', $setting_key)
                        ->first();
    }

    public static function defaultCurrency ()
    {
        return DB::table(Config::get('tables.CURRENCIES'))
                        ->where('default_currency', Config::get('constants.ON'))
                        ->select('currency_id', 'currency', 'currency_symbol', 'flag_char', 'default_currency')
                        ->first();
    }

    public function payment_mode_list ()
    {
        return DB::table(Config::get('tables.PAYMENT_MODES_LOOKUPS'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->where('status', Config::get('constants.ON'))
                        ->get();
    }

    public function get_available_payment_gateway ($data)
    {
        return DB::table(Config::get('tables.PAYMENT_TYPES'))
                        ->whereRaw('payment_modes  REGEXP \'[[:<:]]'.$data['mode_id'].'[[:>:]]\'')
                        ->where('status', Config::get('constants.OFF'))
                        ->select('payment_type_id', 'payment_type')
                        ->get();
    }

    public function get_setting_value ($key)
    {
        return DB::table(Config::get('tables.SETTINGS'))
                        ->where('setting_key', $key)
                        ->pluck('setting_value');
    }

    public function getUserAddress ($account_id, $acc_type_id)
    {
        return DB::table(Config::get('tables.ADDRESS_MST').' as am')
                        ->join(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'am.state_id')
                        ->join(Config::get('tables.LOCATION_CITY').' as lct', 'lct.city_id', '=', 'am.city_id')
                        ->join(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'am.country_id')
                        ->selectRaw('am.flatno_street, am.address, am.city_id, lct.city, am.state_id, ls.state, am.country_id, lc.country as country, am.postal_code')
                        ->where('am.is_deleted', Config::get('constants.OFF'))
                        ->where('am.relative_post_id', $account_id)
                        ->where('am.post_type', $acc_type_id)
                        ->get();
    }

    public function decimal_places ($amt)
    {
        $decimal_places = 2;
        $decimal_val = explode('.', $amt);
        if (isset($decimal_val[1]))
        {
            $decimal = rtrim($decimal_val[1], 0);
            if (strlen($decimal) > 2)
                $decimal_places = strlen($decimal);
        }
        return $decimal_places;
    }

    public function get_exchange_rate ($from_currency_id, $to_currency_id)
    {
        $rate = ($from_currency_id != $to_currency_id) ? DB::table(Config::get('tables.CURRENCY_EXCHANGE_SETTINGS'))
                        ->where('from_currency_id', $from_currency_id)
                        ->where('to_currency_id', $to_currency_id)
                        ->pluck('rate') : 1;
        return (!empty($rate) ) ? $rate : 1;
    }

    public function getCountryList ($whereIncountry_ids = [])
    {
        $query = DB::table(Config::get('tables.LOCATION_COUNTRY'))
				->where('status', 1)
                ->orderBy('country', 'asc');
        if (!empty($whereIncountry_ids))
        {
            $query->whereIn('country_id', $whereIncountry_ids);
        }
        return $query->lists('country', 'country_id');
    }

    public function getCurrencies ($whereInCurrencies = [])
    {
        $query = DB::table(Config::get('tables.CURRENCIES'))
                ->orderBy('currency', 'asc');
        if (!empty($whereInCurrencies))
        {
            $query->whereIn('currency_id', $whereInCurrencies);
        }
        return $query->lists('currency', 'currency_id');
    }
	
	public function getCountrybycode($code)
    {
        return DB::table(Config::get('tables.LOCATION_COUNTRY'))
                        ->where('iso2', $code)
                        ->select('country_id','country','phonecode','mobile_validation')->first();
    }

    public function getCountryName ($country_id)
    {
        return DB::table(Config::get('tables.LOCATION_COUNTRY'))
                        ->where('country_id', $country_id)
                        ->value('country');
    }

    public function getStateName ($state_id)
    {
        return DB::table(Config::get('tables.LOCATION_STATE'))
                        ->where('state_id', $state_id)
                        ->value('state');
    }

    public function getCityName ($city_id)
    {
        return DB::table(Config::get('tables.LOCATION_CITY'))
                        ->where('city_id', $city_id)
                        ->value('city');
    }

    public function get_wallet_list ()
    {
        return DB::table(Config::get('tables.WALLET'))
                        ->where('withdrawal_status', Config::get('constants.ACTIVE'))
                        ->select('wallet_id', 'wallet_name')
                        ->orderby('wallet_name', 'ASC')
                        ->first();
    }

    public function get_wallet_name ($wallet_id)
    {
        return DB::table(Config::get('tables.WALLET'))
                        ->where('wallet_id', $wallet_id)
                        ->pluck('wallet_name');
    }

    public function get_currency_name ($currency_id)
    {
        return DB::table(Config::get('tables.CURRENCIES'))
                        ->where('currency_id', $currency_id)
                        ->pluck('currency');
    }

    public function get_currency ($currency_id)
    {
        return DB::table(Config::get('tables.CURRENCIES'))
                        ->where('currency_id', $currency_id)
                        ->select('currency', 'currency_symbol')
                        ->first();
    }

    public function get_user_balance ($account_id, $wallet_id, $currency_id)
    {
        fetch:
        $result = DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE'))
                ->where(array(
                    'account_id'=>$account_id,
                    'wallet_id'=>$wallet_id,
                    'currency_id'=>$currency_id))
                ->first();

        if (empty($result))
        {
            $curresult = DB::table(Config::get('tables.CURRENCIES'))
                    ->where('currency_id', $currency_id)
                    ->where('status', Config::get('constants.ON'))
                    ->count();
            $ewalresult = DB::table(Config::get('tables.WALLET'))
                    ->where(array(
                        'wallet_id'=>$wallet_id,
                        'status'=>Config::get('constants.ON')))
                    ->count();

            if ($curresult && $ewalresult)
            {
                $insert['account_id'] = $account_id;
                $insert['current_balance'] = '0';
                $insert['tot_credit'] = '0 ';
                $insert['tot_debit'] = '0';
                $insert['currency_id'] = $currency_id;
                $insert['wallet_id'] = $wallet_id;
                $status = DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE'))
                        ->insertGetId($insert);

                goto fetch;
            }
        }

        return $result;
    }

    public function get_country_list ($arr = array(), $forselect2 = false)
    {
        extract($arr);
        $query = DB::table(Config::get('tables.LOCATION_COUNTRY').' as lc')
                ->where('lc.status', Config::get('constants.ACTIVE'));
        if (isset($state_id) && !empty($state_id))
        {
            $query->leftjoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.country_id', '=', 'lc.country_id')->where('ls.state_id', $state_id);
        }
        if (isset($country) && !empty($country))
        {
            $query->where('lc.country', 'like', $country.'%');
        }
        if ($forselect2)
        {
            $query->select('lc.country_id as id', 'lc.country as text');
        }
        else
        {
            $query->select('lc.country_id', 'lc.country as text', 'lc.phonecode');
        }
        return $query->orderby('country', 'ASC')
                        ->distinct('lc.country_id')->get();
    }

    public function get_state_list ($arr = array(), $forselect2 = false)
    {
        extract($arr);
        $query = DB::table(Config::get('tables.LOCATION_STATE').' as ls')
                ->where('ls.status', Config::get('constants.ACTIVE'));
        if (!empty($country_id))
        {
            $query->where('ls.country_id', $country_id);
        }
        if (isset($state) && !empty($state))
        {
            $query->where('ls.state', 'like', $state.'%');
        }
        if (!empty($city_id))
        {
            $query->join(Config::get('tables.LOCATION_DISTRICTS').' as ld', 'ld.state_id', '=', 'ls.state_id')
                    ->join(Config::get('tables.LOCATION_PINCODES').' as lp', 'lp.district_id', '=', 'ld.district_id')
                    ->join(Config::get('tables.LOCATION_CITY').' as lct', 'lct.pincode_id', '=', 'lp.pincode_id')
                    ->where('lct.city_id', $city_id);
        }
        if ($forselect2)
        {
            $query->select('ls.state_id as id', 'ls.state as text');
        }
        else
        {
            $query->select('ls.state_id', 'ls.state');
        }
        return $query->orderby('ls.state', 'ASC')
                        ->distinct('ls.state_id')->get();
    }

    public function get_city_list ($arr = array(), $forselect2 = false)
    {
        extract($arr);
        $query = DB::table(Config::get('tables.LOCATION_CITY').' as lc')
                ->where('lc.status', Config::get('constants.ACTIVE'));
        if (!empty($state_id))
        {
            $query->join(Config::get('tables.LOCATION_PINCODES').' as lp', 'lp.pincode_id', '=', 'lc.pincode_id')
                    ->join(Config::get('tables.LOCATION_DISTRICTS').' as ld', function($ld) use($state_id)
                    {
                        $ld->on('ld.district_id', '=', 'lp.district_id')
                        ->where('ld.state_id', '=', $state_id);
                    });
        }
        if (isset($city) && !empty($city))
        {
            $query->where('lc.city', 'like', $city.'%');
        }
        if (!empty($district_id))
        {
            $query->where('lc.district_id', '=', $district_id);
        }
        if ($forselect2)
        {
            $query->select('lc.city_id as id', 'lc.city as text');
        }
        else
        {
            $query->select('lc.city_id', 'lc.city');
        }
        return $query->orderby('lc.city', 'ASC')->distinct('lc.city_id')->get();
    }

    public function language_list ()
    {
        return DB::table(Config::get('constants.LANGUAGE_LOOKUPS'))
                        ->get();
    }

    public function locale_list ()
    {
        return DB::table(Config::get('constants.LOCALE_LOOKUPS'))
                        ->get();
    }

    public function time_zone_list ()
    {
        return DB::table(Config::get('constants.TIME_ZONE_LOOKUPS'))
                        ->get();
    }

    public function get_currencies_list ($arr = array())
    {
        $curr = DB::table(Config::get('tables.CURRENCIES'))
                ->where('status', Config::get('constants.ACTIVE'))
                ->orderby('currency', 'ASC')
                ->select('currency', 'currency_id', 'currency_symbol', 'decimal_places', 'default_currency', 'flag_char');
        if (isset($arr['allowed_curr']))
        {
            $curr->whereIn('currency_id', $arr['allowed_curr']);
        }
        return $curr->get();
    }

    public function get_currency_code ($currency_id)
    {
        return DB::table(Config::get('tables.CURRENCIES'))
                        ->where('currency_id', $currency_id)
                        ->pluck('currency');
    }

    public function get_currency_symbol ($currency_id)
    {
        return DB::table(Config::get('tables.CURRENCIES'))
                        ->where('currency_id', $currency_id)
                        ->pluck('currency_symbol');
    }

    public function get_courier_list ($arr = array())
    {
        extract($arr);
        if (isset($supplier_id))
        {
            return DB::table(Config::get('tables.COURIER_MODES').' as sc')
                            ->leftJoin(Config::get('tables.COURIER_SERVICE_PROVIDERS').' as csp', 'csp.courier_id', '=', 'sc.courier_id')
                            ->Join(Config::get('tables.ORDER_ITEMS').' as op', 'op.mode_id', '=', 'sc.mode_id')
                            ->where('sc.is_deleted', Config::get('constants.OFF'))
                            ->orderBy('csp.courier', 'asc')
                            ->select('csp.courier_id', 'csp.courier')
                            ->get();
        }
        else
        {
            return DB::table(Config::get('tables.COURIER_SERVICE_PROVIDERS').' as csp')
                            ->where('csp.is_deleted', Config::get('constants.OFF'))
                            ->orderBy('csp.courier', 'asc')
                            ->select('csp.courier_id', 'csp.courier')
                            ->get();
        }
        return false;
    }

    public function get_courier_mode_list ()
    {
        return DB::table(Config::get('tables.COURIER_MODE_LOOKUPS'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->orderBy('mode', 'asc')
                        ->select('mode_id', 'mode')
                        ->get();
    }

    public function get_stores_list ($supplier_id)
    {
        return DB::table(Config::get('tables.STORES').' as st')
                        ->join(Config::get('tables.STORES_EXTRAS').' as se', 'st.store_id', '=', 'se.store_id')
                        ->join(Config::get('tables.ACCOUNT_SUPPLIERS').' as as', 'st.supplier_id', '=', 'as.supplier_id')
                        ->leftJoin(Config::get('tables.LOCATION_CITY').' as lci', 'lci.city_id', '=', 'se.city_id')
                        ->leftJoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'lci.state_id')
                        ->leftJoin(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ls.country_id')
                        ->where('st.supplier_id', $supplier_id)
                        ->selectRaw('st.store_id,as.company_name,st.store_name,se.mobile_no,CONCAT(se.address1,\' \',se.address2) as address,se.address1,se.address2,se.email,se.website,ls.country_id,lci.city, st.status,st.store_code,st.updated_on')
                        ->get();
    }

    public function get_category_parents ($arr = array())
    {
        $category_id = 0;
        extract($arr);
        if ($category_id > 0)
        {
            $res = DB::table(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as np')
                    ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as pp', function($pp)
                    {
                        $pp->on(DB::raw('np.cat_lftnode BETWEEN pp.cat_lftnode AND pp.cat_rgtnode'), DB::raw(''), DB::raw(''));
                    })
                    ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES').' as parent', 'parent.category_id', '=', 'pp.category_id')
                    ->where('np.category_id', $category_id)
                    ->orderby('pp.cat_lftnode')
                    ->selectRaw('parent.category as categories,parent.category_id,parent.url_str,parent.category_code')
                    ->get();
            return ($res) ? $res : NULL;
        }
        else
        {
            return NULL;
        }
    }

    public function getBreadcrums ($arr = array(), $prefix = '')
    {
        extract($arr);
        $breadcrums = [];
        $brand = [];
        if (isset($brand_id) && !empty($brand_id))
        {
            $brand = DB::table(Config::get('tables.PRODUCT_BRANDS'))
                    ->where('brand_id', $brand_id)
                    ->where('is_deleted', Config::get('constants.OFF'))
                    ->where('status', Config::get('constants.ACTIVE'))
                    ->select('brand_name', 'url_str')
                    ->first();
        }
        if (isset($category_id) && !empty($category_id))
        {
            $breadcrums = DB::table(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as np')
                    ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as pp', function($pp)
                    {
                        $pp->on(DB::raw('np.cat_lftnode BETWEEN pp.cat_lftnode AND pp.cat_rgtnode'), DB::raw(''), DB::raw(''));
                    })
                    ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES').' as parent', 'parent.category_id', '=', 'pp.category_id')
                    ->where('np.category_id', $category_id)
                    ->orderby('pp.cat_lftnode')
                    ->selectRaw('parent.category as title,parent.category_code,parent.url_str')
                    ->get();
            $pre_url = '';
            array_walk($breadcrums, function(&$item) use(&$pre_url, $prefix, $brand)
            {
                $pre_url.=$item->url_str.'/';
                $item->url = !empty($brand) ? URL::to($prefix.$pre_url.'/'.$brand->url_str.'~brand/br?spath='.$item->category_code) : URL::to($prefix.$pre_url.'br?spath='.$item->category_code);
                unset($item->url_str);
                unset($item->category_code);
            });
        }
        return $breadcrums;
    }

    public function get_categories_list ($arr = array())
    {
        $withUrl = false;
        extract($arr);
        $query = DB::table(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as np')
                ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as pp', function($pp)
                {
                    $pp->on(DB::raw('np.cat_lftnode BETWEEN pp.cat_lftnode AND pp.cat_rgtnode'), DB::raw(''), DB::raw(''));
                })
                ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES').' as node', 'node.category_id', '=', 'np.category_id')
                ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES').' as parent', 'parent.category_id', '=', 'pp.category_id');
        if (isset($supplier_id) && !empty($supplier_id))
        {
            /* $query->whereIn('np.category_id', function($check) use($supplier_id)
            {
                $check->from(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE'))
                        ->where('supplier_id', $supplier_id)
                        ->where('is_deleted', Config::get('constants.OFF'))
						->select('category_id')
                        ->get();
            }); */
        }
        if (!empty($parent_category_id))
        {
            $query->where('pp.parent_category_id', $parent_category_id);
        }
        if (!empty($search_term))
        {
            $query->where('node.category', 'like', '%'.$search_term.'%');
        }
        $categories = $query->where('node.is_deleted', Config::get('constants.OFF'))
                ->orderBy('name', 'asc')
                ->groupby('node.category_id')
                ->selectRaw('node.category_id as id, GROUP_CONCAT(parent.`category` ORDER BY parent.category_id ASC SEPARATOR \' - \') as name')
                ->get();
        if ($withUrl && !empty($categories))
        {
            array_walk($categories, function(&$cat)
            {
                $cat->url = $this->generateBrowseURL(array('category'=>$cat), false);
            });
        }
        return $categories;
    }

    public function get_all_categories ($parent_category_id = 0, $search_term = '', $with_childrens = true, $with_parents = true)
    {
        $query = DB::table(Config::get('tables.PRODUCT_CATEGORIES').' as c')
                ->join(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as cp', 'cp.category_id', '=', 'c.category_id')
                ->where('cp.parent_category_id', $parent_category_id)
                ->where('c.is_deleted', Config::get('constants.OFF'))
                ->orderBy('c.category', 'asc')
                ->select('c.category_id', 'c.category');
        if (!empty($search_term))
        {
            $query->where('c.category', 'like', '%'.$search_term.'%');
        }
        $categories = $query->get();
        $with_parents = (!$with_parents) ? $with_childrens : $with_parents;
        if ($with_parents)
        {
            $with_data = array();
            array_map(function($category) use(&$with_data, $parent_category_id, $with_childrens)
            {
                $parent_categories = array();
                if (isset($parent_category_id) && !empty($parent_category_id))
                {
                    while ($parent_category_id)
                    {
                        $parent_categories[] = $parent_category_id;
                        $parent_category_id = DB::table(Config::get('tables.PRODUCT_CATEGORIES').' as c')
                                ->join(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as cp', 'cp.category_id', '=', 'c.category_id')
                                ->where('c.category_id', $parent_category_id)
                                ->where('c.is_deleted', Config::get('constants.OFF'))
                                ->pluck('cp.parent_category_id');
                    }
                }
                $category->parent_categories = $parent_categories;
                if ($with_childrens)
                {
                    $category->sub_categories = $this->get_all_categories($category->category_id);
                }
                $with_data[] = $category;
            }, $categories);
            return $with_data;
        }
        return $categories;
    }

    public function get_brands_list ($arr = array())
    {
        extract($arr);
        $brands = DB::table(Config::get('tables.PRODUCT_BRANDS'));
        if (isset($supplier_id) && !empty($supplier_id))
        {
            $brands->whereIn('brand_id', function($check) use($supplier_id)
            {
                $check->from(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE'))
                        ->where('supplier_id', $supplier_id)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->lists('brand_id');
            });
        }
        return $brands->where('is_deleted', Config::get('constants.OFF'))
                        ->orderBy('brand_name', 'asc')
                        ->select('brand_id', 'brand_name')
                        ->get();
    }

    public function get_suppliers_list ()
    {
        return DB::table(Config::get('tables.SUPPLIER_MST'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->orderBy('company_name', 'asc')
                        ->select('supplier_id as id', 'company_name as name')
                        ->get();
    }

    public function get_suppliers_code_list ()
    {
        return DB::table(Config::get('tables.ACCOUNT_SUPPLIERS'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->orderBy('supplier_code', 'asc')
                        ->select('supplier_id as id', 'supplier_code as code')
                        ->get();
    }

    public function get_products_list ($arr = array())
    {
        extract($arr);
        $qrery = DB::table(Config::get('tables.PRODUCT_INFO'));
        if (isset($category_id) && !empty($category_id))
        {
            $qrery->where('category_id', $category_id);
        }
        if (isset($brand_id) && !empty($brand_id))
        {
            $qrery->where('brand_id', $brand_id);
        }
        return $qrery->orderBy('product_name', 'asc')
                        ->select('product_id', 'product_name')
                        ->get();
    }

    public function tax_status_list ()
    {
        return DB::table(Config::get('tables.TAX_STATUS_LOOKUPS'))
                        ->select('status_id', 'status')
                        ->get();
    }

    public function zone_list ()
    {
        return DB::table(Config::get('tables.GEO_ZONE'))
                        ->select('geo_zone_id', 'zone')
                        ->get();
    }

    public function get_supplier_products_list ($arr = array())
    {
        extract($arr);
        $qrery = DB::table(Config::get('tables.SUPPLIER_PRODUCT_INFO').' as spi')
                ->leftjoin(Config::get('tables.ACCOUNT_SUPPLIERS').' as sup', 'sup.supplier_id', '=', 'spi.supplier_id')
                ->where('spi.supplier_id', $supplier_id);
        if (isset($cataroty_id) && !empty($cataroty_id))
        {
            $qrery->where('spi.cataroty_id', $cataroty_id);
        }
        if (isset($brand_id) && !empty($brand_id))
        {
            $qrery->where('spi.brand_id', $brand_id);
        }
        return $qrery->orderBy('spi.product_name', 'asc')
                        ->select('spi.product_id', 'spi.product_name')
                        ->get();
    }

    public function get_order_status_list ()
    {
        return DB::table(Config::get('tables.ORDER_STATUS_LOOKUP'))
                        ->orderBy('status', 'asc')
                        ->select('order_status_id as id', 'status as name')
                        ->get();
    }

    public function get_units_list ()
    {
        return DB::table(Config::get('tables.UNITS'))
                        ->orderBy('unit', 'asc')
                        ->select('unit_id', 'unit', 'description')
                        ->get();
    }

    public function get_parent_properties_list ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.PRODUCT_PROPERTY_KEYS'))
                        ->orderBy('property', 'asc')
                        ->whereNull('parent_property_id')
                        ->select('property_id', 'property')
                        ->get();
    }

    public function get_address_type_list ()
    {
        return DB::table(Config::get('tables.ADDRESS_TYPE_LOOKUP'))
                        ->select('address_type_id', 'address_type')
                        ->get();
    }

    public function get_tax_list ()
    {
        return DB::table(Config::get('tables.TAXES'))
                        ->select('tax_id', 'tax')
                        ->get();
    }

    public function commission_type ()
    {
        return DB::table(Config::get('tables.SUPPLIER_COMMISSIONS_LOOKUPS'))
                        ->get();
    }

    public function wallet_list ()
    {
        return DB::table(Config::get('tables.WALLET'))
                        ->where('creditable', 1)
                        ->get();
    }

    public function payment_list ()
    {
        return DB::table(Config::get('tables.PAYMENT_TYPES'))
                        ->where('status', Config::get('constants.ON'))
                        ->get();
    }

    public function withdrawalPaymentList ()
    {
        return DB::table(Config::get('tables.PAYMENT_TYPES').' as p')
                        ->join(Config::get('tables.WITHDRAWAL_PAYMENT_TYPE').' as pw', 'pw.payment_type_id', '=', 'p.payment_type_id')
                        ->where('p.status', Config::get('constants.ON'))
                        ->selectRaw('p.payment_type_id,p.payment_type')
                        ->get();
    }

    public function properies_for_checktree ($parent_property_id = null)
    {
        $properties = DB::table(Config::get('tables.PRODUCT_PROPERTY_KEYS'))
                ->where('is_deleted', Config::get('constants.OFF'));
        if (empty($parent_property_id))
        {
            $properties->whereNull('parent_property_id');
        }
        else
        {
            $properties->where('parent_property_id', $parent_property_id);
        }
        $properties = $properties->selectRaw('property_id as id, property as label')
                ->orderBy('label', 'asc')
                ->get();
        $with_data = array();
        array_map(function($property) use(&$with_data, $parent_property_id)
        {
            $property->children = $this->properies_for_checktree($property->id);
            $with_data[] = $property;
        }, $properties);
        return $with_data;
    }

    public function propery_values_for_checktree ($property_id)
    {
        return DB::table(Config::get('tables.PRODUCT_PROPERTY_KEY_VALUES').' as v')
                        ->leftjoin(Config::get('tables.UNITS').' as u', 'u.unit_id', '=', 'v.unit_id')
                        ->where('v.property_id', $property_id)
                        ->where('v.is_deleted', Config::get('constants.OFF'))
                        ->selectRaw('v.value_id as id,concat(v.key_value,if(u.unit is not null,concat(\' \',u.unit),\'\')) as label,v.property_id')
                        ->orderBy('label', 'asc')
                        ->get();
    }

    public function tax_classes_list ()
    {
        return DB::table(Config::get('tables.TAX_CLASSES'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->select('tax_class_id', 'tax_class')
                        ->get();
    }

    public function faq_category ()
    {
        return DB::table(Config::get('tables.FAQ_CATEGORIES'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->get();
    }

    public function support_category ()
    {

        return DB::table(Config::get('tables.SUPPORT_ISSUES_CATEGORY'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->get();
    }

    /*
     * Function Name        : update_account_transaction
     * Params               : (from_account_id, to_account_id)(BOTH or ANYONE), from_wallet_id,to_wallet_id, currency_id, amt, relation_id, transaction_for, handle[[][amt,transaction_for]](optional)
     * Returns              : Transaction ID or False
     */

    public function update_account_transaction ($arr = array(), $debit_only = false, $credit_only = false)
    {
        $debit_remark_data = [];
        $credit_remark_data = [];
        $debitted = $credited = false;
        $tax_amt = $handle_amt = 0;
        extract($arr);
        $relation_id = (isset($relation_id) && empty($relation_id)) ? (is_array($relation_id) ? implode(',', $relation_id) : $relation_id) : null;
        $from_account_id = (isset($from_account_id) && !empty($from_account_id)) ? $from_account_id : Config::get('constants.ADMIN_ACCOUNT_ID');
        $to_account_id = (isset($to_account_id) && !empty($to_account_id)) ? $to_account_id : Config::get('constants.ADMIN_ACCOUNT_ID');
        $payment_type_id = isset($payment_type_id) && !empty($payment_type_id) ? $payment_type_id : Config::get('constants.PAYMENT_TYPES.WALLET');
        $to_wallet_id = (isset($to_wallet_id) && !empty($to_wallet_id)) ? $to_wallet_id : $from_wallet_id;
        if ($from_account_id != Config::get('constants.ADMIN_ACCOUNT_ID'))
        {
            if (isset($handle) && !empty($handle))
            {
                array_walk($handle, function(&$charge) use(&$handle_amt, $currency_id)
                {
                    $handle_amt += $charge['amt'];
                    $charge['currency_id'] = $currency_id;
                });
            }
            if (isset($taxes) && !empty($taxes))
            {
                array_walk($taxes, function($tax) use(&$tax_amt)
                {
                    $tax_amt += $tax['amt'];
                });
            }
        }
        if (!$credit_only)
        {
            if ($bal = $this->updateBalance($from_account_id, $from_wallet_id, $currency_id, $amt, false))
            {
                $from_transaction_id = isset($from_transaction_id) && !empty($from_transaction_id) ? $from_transaction_id : $this->generateTransactionID($from_account_id);
                $debitted = DB::table(Config::get('tables.ACCOUNT_TRANSACTION'))
                        ->insertGetID([
                    'account_id'=>$from_account_id,
                    'from_or_to_account_id'=>$to_account_id,
                    'wallet_id'=>$from_wallet_id,
                    'payment_type_id'=>$payment_type_id,
                    'relation_id'=>$relation_id,
                    'amt'=>$amt,
                    'paid_amt'=>$amt,
                    'tax'=>$tax_amt,
                    'currency_id'=>$currency_id,
                    'status'=>Config::get('constants.TRANSACTION_STATUS.CONFIRMED'),
                    'ip_address'=>Request::getClientIp(true),
                    'created_on'=>date('Y-m-d H:i:s'),
                    'transaction_id'=>$from_transaction_id,
                    'transaction_type'=>Config::get('constants.TRANSACTION_TYPE.DEBIT'),
                    'handle_amt'=>$handle_amt,
                    'current_balance'=>$bal,
                    'statementline_id'=>Config::get('stline.'.$transaction_for.'.DEBIT'),
                    'remark'=>json_encode(['key'=>$transaction_for, 'data'=>$debit_remark_data])
                ]);
                if ($debitted && $from_account_id != Config::get('constants.ADMIN_ACCOUNT_ID'))
                {
                    if (isset($taxes) && !empty($taxes))
                    {
                        foreach ($taxes as $charge)
                        {
                            if (!$this->update_account_transaction(array(
                                        'from_account_id'=>$from_account_id,
                                        'from_wallet_id'=>$from_wallet_id,
                                        'currency_id'=>$currency_id,
                                        'amt'=>$charge['amt'],
                                        'relation_id'=>$relation_id,
                                        'transaction_for'=>$charge['transaction_for']
                                    )))
                            {
                                break;
                            }
                        }
                    }
                    if (isset($handle) && !empty($handle))
                    {
                        foreach ($handle as $charge)
                        {
                            if (!$this->update_account_transaction(array(
                                        'from_account_id'=>$from_account_id,
                                        'from_wallet_id'=>$from_wallet_id,
                                        'currency_id'=>$currency_id,
                                        'amt'=>$charge['amt'],
                                        'relation_id'=>$relation_id,
                                        'transaction_for'=>$charge['transaction_for']
                                    )))
                            {
                                break;
                            }
                        }
                    }
                }
            }
        }
        if ($to_account_id != Config::get('constants.ADMIN_ACCOUNT_ID'))
        {
            if (isset($handle) && !empty($handle))
            {
                array_walk($handle, function(&$charge) use(&$handle_amt, $currency_id)
                {
                    $handle_amt += $charge['amt'];
                    $charge['currency_id'] = $currency_id;
                });
            }
            if (isset($taxes) && !empty($taxes))
            {
                array_walk($taxes, function($tax) use(&$tax_amt)
                {
                    $tax_amt += $tax['amt'];
                });
            }
        }
        if (!$debit_only && ($credit_only || $debitted))
        {
            $currency_id = (isset($to_currency_id) && !empty($to_currency_id)) ? $to_currency_id : $currency_id;
            $amt = (isset($to_amt) && !empty($to_amt)) ? $to_amt : $amt;
            $to_transaction_id = isset($to_transaction_id) && !empty($to_transaction_id) ? $to_transaction_id : $this->generateTransactionID($to_account_id);
            if ($bal = $this->updateBalance($to_account_id, $to_wallet_id, $currency_id, $amt))
            {
                $credited = DB::table(Config::get('tables.ACCOUNT_TRANSACTION'))
                        ->insertGetID([
                    'account_id'=>$to_account_id,
                    'from_or_to_account_id'=>$from_account_id,
                    'wallet_id'=>$to_wallet_id,
                    'payment_type_id'=>$payment_type_id,
                    'relation_id'=>$relation_id,
                    'amt'=>$amt,
                    'paid_amt'=>$amt,
                    'tax'=>$tax_amt,
                    'currency_id'=>$currency_id,
                    'status'=>Config::get('constants.TRANSACTION_STATUS.CONFIRMED'),
                    'ip_address'=>Request::getClientIp(true),
                    'created_on'=>date('Y-m-d H:i:s'),
                    'transaction_id'=>$to_transaction_id,
                    'transaction_type'=>Config::get('constants.TRANSACTION_TYPE.CREDIT'),
                    'handle_amt'=>$handle_amt,
                    'current_balance'=>$bal,
                    'statementline_id'=>Config::get('stline.'.$transaction_for.'.CREDIT'),
                    'remark'=>json_encode(['key'=>$transaction_for, 'data'=>$credit_remark_data])
                ]);
                if ($credited && $to_account_id != Config::get('constants.ADMIN_ACCOUNT_ID'))
                {
                    if (isset($taxes) && !empty($taxes))
                    {
                        foreach ($taxes as $charge)
                        {
                            if (!$this->update_account_transaction(array(
                                        'from_account_id'=>$to_account_id,
                                        'from_wallet_id'=>$to_wallet_id,
                                        'currency_id'=>$currency_id,
                                        'amt'=>$charge['amt'],
                                        'relation_id'=>$relation_id,
                                        'transaction_for'=>$charge['transaction_for']
                                    )))
                            {
                                break;
                            }
                        }
                    }
                    if (isset($handle) && !empty($handle))
                    {
                        foreach ($handle as $charge)
                        {
                            if (!$this->update_account_transaction(array(
                                        'from_account_id'=>$to_account_id,
                                        'from_wallet_id'=>$to_wallet_id,
                                        'currency_id'=>$currency_id,
                                        'amt'=>$charge['amt'],
                                        'relation_id'=>$relation_id,
                                        'transaction_for'=>$charge['transaction_for']
                                    )))
                            {
                                break;
                            }
                        }
                    }
                }
            }
        }
        if ($debit_only && $debitted)
        {
            return $from_transaction_id;
        }
        else if ($credit_only && $credited)
        {
            return $to_transaction_id;
        }
        else if ($credited)
        {
            return $from_transaction_id;
        }
        return false;
    }

    public function checkBalance ($arr = array())
    {
        $wallet_id = Config::get('constants.WALLET.SELLS');
        extract($arr);
        return DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE'))
                        ->where('account_id', $account_id)
                        ->where('currency_id', $currency_id)
                        ->where('wallet_id', $wallet_id)
                        ->where('current_balance', '>=', $amount)
                        ->exists();
    }

    public function updateBalance ($account_id, $wallet_id, $currency_id, $amount, $increment = true)
    {
        $balance = DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE'))
                ->where('account_id', $account_id)
                ->where('currency_id', $currency_id)
                ->where('wallet_id', $wallet_id)
                ->first();

        if ($balance)
        {
            if ($increment || (!$increment && $balance->current_balance >= $amount))
            {
                if ($increment)
                {
                    $current_balance = $balance->current_balance + $amount;
                    $balance->tot_credit = $balance->tot_credit + $amount;
                }
                else
                {
                    $current_balance = $balance->current_balance - $amount;
                    $balance->tot_debit = $balance->tot_debit + $amount;
                }
                DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE'))
                        ->where('account_id', $account_id)
                        ->where('currency_id', $currency_id)
                        ->where('wallet_id', $wallet_id)
                        ->update(['tot_credit'=>$balance->tot_credit, 'tot_debit'=>$balance->tot_debit, 'current_balance'=>$current_balance]);
                return $current_balance;
            }
        }
        elseif ($increment)
        {
            if ($increment)
            {
                DB::table(Config::get('tables.ACCOUNT_WALLET_BALANCE'))
                        ->insert(['account_id'=>$account_id, 'currency_id'=>$currency_id, 'wallet_id'=>$wallet_id, 'tot_credit'=>$amount, 'current_balance'=>$amount]);
                return $amount;
            }
        }
        return false;
    }

    public function generateTransactionID ($account_id)
    {
        $disp = $this->rKeyGen(3, 1);
        return $disp.$account_id.date('dmYHis');
    }

    function rKeyGen ($digits, $datatype)
    {
        $key = '';
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

    public function replacement_days ()
    {
        return DB::table(Config::get('tables.SERVICE_POLICIES'))
                        ->where('policy_type', 1)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->selectRaw('CONCAT(policy_period,\' \',policy_title) as replacement_day,service_policy_id')
                        ->get();
    }

    public function product_parent_categories ()
    {
        return DB::table(Config::get('tables.PRODUCT_CATEGORIES').' as c')
                        ->join(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as cp', 'cp.category_id', '=', 'c.category_id')
                        ->whereNull('cp.parent_category_id')
                        ->where('c.is_deleted', Config::get('constants.OFF'))
                        ->select('c.category_id', 'c.category')
                        ->get();
    }

    public static function generate_BrowseURLS ($data, $mode)
    {
        $url = '';
        if (!empty($data) && !empty($mode))
        {
            switch ($mode)
            {
                case 3:  //browse-from-brands
                    $urlPath[] = $data['cat_path'];
                    $urlPath[] = $data['brand_slug'].'~brand';
                    $urlPath[] = 'br';
					
                    $qVars[] = 'spath='.$data['sid'];

                    $path = implode('/', $urlPath);
                    $qstr = !empty($qVars) ? '?'.implode('&', $qVars) : '';
                    $url = URL::to($path.$qstr);
                    break;
            }
            switch ($mode)
            {
                case 4:  //browse-category-brands
                    $urlPath[] = 'catalogue';
                    $urlPath[] = 'brands';
                    $urlPath[] = $data['cat_path'];
                    $qVars[] = 'spath='.$data['sid'];

                    $path = implode('/', $urlPath);
                    $qstr = !empty($qVars) ? '?'.implode('&', $qVars) : '';
                    $url = URL::to($path.$qstr);
                    break;
            }
        }
        return $url;
    }

    public function getAvaliablePaymentModes ()
    {
        return DB::table(Config::get('tables.PAYMENT_MODES_LOOKUPS'))
                        ->where('status', Config::get('constants.ACTIVE'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->select('paymode_id as id', 'mode_name as name')
                        ->get();
    }

    public function getAvaliablePaymentTypes ()
    {
        $payments = DB::table(Config::get('tables.PAYMENT_TYPES'))
                ->where('status', Config::get('constants.ACTIVE'))
                ->select('payment_type as title', 'image_name as img')
                ->get();
        array_walk($payments, function(&$p)
        {
            $p->img = URL::asset($p->img);
        });
        return $payments;
    }

    public function gender_list ()
    {
        return DB::table(Config::get('tables.ACCOUNT_GENDER_LOOKUPS'))
                        ->get();
    }

    public function getSliders ($arr = array())
    {
        extract($arr);
        $this->imageObj = new ImageController();
        $sliders = DB::table(Config::get('tables.FEATURED_SLIDERS').' as s')
                ->leftjoin(Config::get('tables.PAGES').' as p', 'p.page_id', '=', 's.page_id')
                ->where('p.page', $page)
                ->where('s.is_deleted', Config::get('constants.OFF'))
                ->where('p.is_deleted', Config::get('constants.OFF'))
                ->whereNull('partner_id')
                ->where('s.status', Config::get('constants.ACTIVE'))
                ->orderby('s.sort_order', 'ASC')
                ->selectRaw('slider_id, slider_type, title, page')
                ->get();
        array_walk($sliders, function(&$slider) use($currency_id, $country_id)
        {
            $slider->blocks = DB::table(Config::get('tables.BLOCKS').' as b')
                    ->where('b.slider_id', $slider->slider_id)
                    ->where('b.is_deleted', Config::get('constants.OFF'))
                    ->where('b.status', Config::get('constants.ACTIVE'))
                    ->orderby('b.sort_order', 'ASC')
                    ->selectRaw('url, title, subtitle, description, sub_description, supplier_product_id, img_id, block_type')
                    ->get();
            unset($slider->slider_id);
            array_walk($slider->blocks, function(&$block) use($slider, $currency_id, $country_id)
            {
                if ($block->block_type == Config::get('constants.BLOCK_TYPE.PRODUCT'))
                {
                    $product_details = $this->supplierProductDetailsSimple(['supplier_product_id'=>$block->supplier_product_id, 'currency_id'=>$currency_id, 'country_id'=>$country_id]);
                    if (!empty($product_details))
                    {
                        $block->title = $product_details->product_name;
                        $block->description = $product_details->price;
                        $block->img_path = $product_details->imgs[0]->img_path;
                    }
                }
                elseif ($slider->slider_type == Config::get('constants.SLIDER_TYPE.FEATURED'))
                {
                    $block->img_path = $this->imageObj->get_imgbyID($block->img_id, ['img_size'=>$slider->page.'-slider-block-featured']);
                }
                else if ($slider->slider_type == Config::get('constants.SLIDER_TYPE.IMG'))
                {
                    $block->img_path = $this->imageObj->get_imgbyID($block->img_id, ['img_size'=>$slider->page.'-slider-block-img']);
                    $block->img_path = $block->img_path.'?strich=1';
                }
                unset($block->img_id);
            });
        });
        return $sliders;
    }

    public function supplierProductDetailsSimple ($arr = array())
    {
        extract($arr);
        $productQry = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as pi')
                ->join(Config::get('tables.PRODUCTS').' as p', 'p.product_id', '=', 'pi.product_id')
                ->join(Config::get('tables.PRODUCT_MRP_PRICE').' as mrp', function($mrp) use($currency_id)
                {
                    $mrp->on('mrp.product_id', '=', 'pi.product_id')
                    ->where('mrp.currency_id', '=', $currency_id);
                })
                ->join(Config::get('tables.SUPPLIER_PRODUCT_PRICE').' as pp', function($pp)
                {
                    $pp->on('pp.product_id', '=', 'mrp.product_id')
                    ->on('pp.supplier_id', '=', 'pi.supplier_id')
                    ->where('pp.currency_id', '=', 'mrp.currency_id');
                })
                ->leftjoin(Config::get('tables.SUPPLIER_PRODUCT_CMB_PRICE').' as pcp', function($pcp)
                {
                    $pcp->on('pcp.product_cmb_id', '=', 'pi.product_cmb_id')
                    ->on('pcp.supplier_id', '=', 'pi.supplier_id')
                    ->on('pcp.currency_id', '=', 'mrp.currency_id');
                })
                ->join(Config::get('tables.SUPPLIER_BRAND_ASSOCIATE').' as spba', function($spba)
                {
                    $spba->on('spba.brand_id', '=', 'p.brand_id')
                    ->on('spba.supplier_id', '=', 'pi.supplier_id');
                })
                ->join(Config::get('tables.SUPPLIER_CATEGORY_ASSOCIATE').' as spca', function($spba)
                {
                    $spba->on('spca.category_id', '=', 'p.category_id')
                    ->on('spca.supplier_id', '=', 'pi.supplier_id');
                })
                ->leftjoin(Config::get('tables.CURRENCIES').' as cur', 'cur.currency_id', '=', 'mrp.currency_id');
        if (isset($supplier_product_id) && !empty($supplier_product_id))
        {
            $productQry->where('pi.supplier_product_id', $supplier_product_id);
        }
        elseif (isset($supplier_product_code) && !empty($supplier_product_code))
        {
            $productQry->where('pi.supplier_product_code', $supplier_product_code);
        }
        $product = $productQry->selectRaw('pp.off_perc,pi.supplier_id,p.product_id,pi.product_cmb_id,p.product_name,spba.supplier_brand_id,spca.supplier_category_id,p.category_id,p.brand_id,pi.supplier_product_id,cur.currency_id,cur.currency,cur.currency_symbol, mrp.mrp_price, (if(impact_on_price is not null,pcp.impact_on_price,0)+pp.price) as price')
                ->first();
        if ($product)
        {
            $this->imageObj = new ImageController();
            $product->imgs = $this->imageObj->get_imgs($product->product_id);
            $product->qty = 1;
            $product->country_id = $country_id;
            $this->get_product_discounts($product, true);
            $product->price = $product->currency_symbol.' '.number_format($product->price, 2, '.', ',').' '.$product->currency;
        }
        return $product;
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
                        ->where(function($subquery3)use($product)
                        {
                            $subquery3->whereNotNull('dp.caregories_ids')
                            ->whereRaw('find_in_set('.$product->category_id.',dp.caregories_ids)');
                        });
                    })
                    ->where(function($subquery2) use($product)
                    {
                        $subquery2->whereNull('dp.supplier_ids')
                        ->where(function($subquery3)use($product)
                        {
                            $subquery3->whereNotNull('dp.supplier_ids')
                            ->whereRaw('find_in_set('.$product->supplier_id.',dp.supplier_ids)');
                        });
                    })
                    ->where(function($subquery2) use($product)
                    {
                        $subquery2->whereNull('dp.product_ids')
                        ->where(function($subquery3)use($product)
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

    public function subscribe ($arr = array())
    {
        extract($arr);
        if (DB::table(Config::get('tables.NEWSLETTER_SUBSCRIBERS'))
                        ->where($subscribe)
                        ->where('is_deleted', Config::get('constants.ON'))
                        ->update(['is_deleted'=>Config::get('constants.OFF')]))
        {
            return true;
        }
        else
        {
            return DB::table(Config::get('tables.NEWSLETTER_SUBSCRIBERS'))
                            ->insertGetId($subscribe);
        }
        return false;
    }

    public function unsubscribe ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.NEWSLETTER_SUBSCRIBERS'))
                        ->where('email_id', openssl_decrypt($id, Config::get('cipher'), Config::get('key')))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->update(['is_deleted'=>Config::get('constants.ON')]);
    }

    public function sendNewLetter ($arr = array())
    {
        extract($arr);
        $to = DB::table(Config::get('tables.NEWSLETTER_SUBSCRIBERS'))
                ->where($subscribe)
                ->where('is_deleted', Config::get('constants.ON'))
                ->select('email_id')
                ->lists('email_id');
        foreach ($to as $email)
        {
            $data['unsubscribe'] = URL::to('api/v1/customer/unsubscribe/'.openssl_encrypt($id, Config::get('cipher'), Config::get('key')));
            Mailer::send($email, $view, $subject, $data);
        }
        return true;
    }

    public static function generateBrowseURL ($arr = array(), $full = true)
    {
        extract($arr);
        $url_strings = [];
        $query_strings = [];
        if (isset($category->category_id) || isset($category->id) || isset($category_id))
        {
            if (isset($category->parent_url_str))
            {
                $url_strings[] = $category->parent_url_str;
            }
            if (isset($category->url_str))
            {
                $url_strings[] = $category->url_str;
                $query_strings[] = 'spath='.$category->category_code;
            }
        }
        if (isset($brand->brand_id) || isset($brand_id))
        {
            if (isset($brand->url_str))
            {
                $url_strings[] = $brand->url_str.'~brand';
            }
        }
        $url_strings[] = 'br';
        if (isset($filters))
        {
            foreach ($filters as $filter)
            {
                foreach ($filter as $option)
                {
                    $query_strings[] = 'f['.$filter.']['.$option.']='.$option;
                }
            }
        }
        $url = implode('/', $url_strings).'?'.implode('&', $query_strings);
        if (isset($basepath) && !empty($basepath))
        {
            $url = $basepath.'/'.$url;
        }
        return ($full) ? URL::to($url) : $url;
    }

    public function get_tags ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.TAG'))
                        ->where('tag_name', 'like', '%'.$search_term.'%')
                        ->selectRaw('tag_id as id,tag_name as text')
                        ->get();
    }

    public function get_pincodes ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.LOCATION_PINCODES'))
                        ->where('pincode', 'like', $search_term.'%')
                        ->selectRaw('pincode_id as id, pincode as text')
                        ->get();
    }

    public function decode_attribute_string ($arr = array())
    {
        if (!empty($arr))
        {
            $str = json_decode(stripslashes(($item_val->specification)));
            if (!empty($str))
            {
                return $str;
            }
        }
    }

    public function get_category_childs ($parent_category_id = NULL)
    {

        $category = DB::table(Config::get('tables.PARTNER_PRODUCTS').' as tpc')
                ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES').' as 5pc', '5pc.category_id', '=', 'tpc.category_id')
                ->join(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as cp', 'cp.category_id', '=', '5pc.category_id')
                ->where('cp.parent_category_id', $parent_category_id)
                ->selectRaw('5pc.category_id, 5pc.category, cp.parent_category_id, 5pc.url_str, 5pc.category_code')
                ->get();
        if (!empty($category))
        {
            return $category;
        }
        return NULL;
    }

    public static function getWorkingDays ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.WORKING_LOOKUPS'))
                        ->orderby('day_id', 'ASC')
                        ->lists('day', 'day_id');
    }

    public function getSupplierAccountDetails ($arr = array())
    {
        extract($arr);
        $query = DB::table(Config::get('tables.ACCOUNT_MST').' as am')
                ->join(Config::get('tables.SUPPLIER_MST').' as s', 's.account_id', '=', 'am.account_id')
                ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')				
                ->join(Config::get('tables.ACCOUNT_PREFERENCE').' as apf', 'apf.account_id', '=', 'am.account_id')				
                ->join(Config::get('tables.STORES').' as st',function($jk){
					$jk->on('st.supplier_id', '=', 's.supplier_id')
					   ->where('st.primary_store','=',Config('constants.ON'));
				})	
				->leftjoin(config('tables.ADDRESS_MST').' as aa', function($join) use ($post_type)
                {
					if ($post_type != Config::get('constants.ADDRESS_POST_TYPE.STORE')) {
						$join->on('aa.relative_post_id', '=', 's.supplier_id')->where('aa.post_type', '=', $post_type);
					} else {
						$join->on('aa.relative_post_id', '=', 'st.store_id')->where('aa.post_type', '=', $post_type);
					}
                })  
                ->leftjoin(Config::get('tables.STORES_EXTRAS').' as se', 'se.store_id', '=', 'st.store_id')
                ->leftjoin(Config::get('tables.LOCATION_CITY').' as lci', 'lci.city_id', '=', 'aa.city_id')
                ->leftjoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'aa.state_id')
                ->leftjoin(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'aa.country_id')
                ->leftjoin(Config::get('tables.CURRENCIES').' as cu', 'cu.currency_id', '=', 'lc.currency_id') 
                ->leftjoin(Config::get('tables.TAG').' as tg', 'cu.currency_id', '=', 'lc.currency_id') 
				->leftjoin(config('tables.BUSINESS_CATEGORY_LANG').' as bcl', function($bcl)
                {
                    $bcl->on('bcl.bcategory_id', '=', 's.category_id')
                    ->where('bcl.lang_id', '=', config('app.locale_id'));
                })
                ->selectRaw('am.account_id, s.supplier_id, s.website,s.supplier_code,ad.firstname, ad.lastname, concat(ad.firstname,\' \',ad.lastname) as full_name, am.email, am.mobile, am.uname, lc.country, lc.country_id, lc.phonecode, cu.currency as currency_code, s.supplier_code, s.type_of_bussiness, s.company_name, s.is_verified, aa.flatno_street as account_address1, aa.address as account_address2, lci.city, aa.city_id, ls.state_id, ls.country_id, lci.city as account_city, ls.state_id as account_state_id, aa.country_id, aa.postal_code, se.working_days, se.working_hours_from, se.working_hours_to, aa.address_id, st.store_id, st.store_name, se.mobile_no as store_mobile, se.email as store_email, se.website as store_website, se.landline_no, s.office_phone, ad.gender, ad.dob, s.status, bcl.bcategory_name, s.service_type, s.description, ad.profile_img, s.logo as mr_logo, s.banner, s.website, s.business_filing_status as bfs,apf.is_email_verified,apf.is_mobile_verified,s.category_id,s.reg_no,s.person_in_charge,(select GROUP_CONCAT(tg.tag_name) FROM '.Config::get('tables.TAG').' as tg WHERE FIND_IN_SET(tg.tag_id,st.tags)) as tags'); 
        if (isset($account_id) && !empty($account_id))
        {
            $query->where('am.account_id', $account_id);
        }
        if (isset($supplier_id) && !empty($supplier_id))
        {
            $query->where('s.supplier_id', $supplier_id);
        }
        $supplier = $query->first();
        if (!empty($supplier))
        {
            $supplier->working_days = explode(',', $supplier->working_days);
			$supplier->parent_category = $this->get_parent_category($supplier->category_id);			
			/* $supplier->profile_img = !empty($supplier->profile_img) ? asset($this->config->get('path.SELLER.PROFILE_IMG_PATH.LOCAL').$supplier->profile_img) : asset($this->config->get('path.SELLER.PROFILE_IMG_PATH.DEFAULT'));
			
			$supplier->profile_img = !empty($supplier->profile_img) ? asset($this->config->get('constants.MERCHANT.LOGO_PATH.SM').$supplier->profile_img) : asset($this->config->get('constants.MERCHANT.LOGO_PATH.SM').$result->mrlogo);
			
			$supplier->mr_logo = !empty($supplier->mr_logo) ? asset($this->config->get('constants.MERCHANT.LOGO_PATH.SM').$supplier->mr_logo) : asset($this->config->get('constants.MERCHANT.LOGO_PATH.SM').$result->mrlogo);
			
			$supplier->banner = !empty($supplier->banner) ? asset($this->config->get('constants.MERCHANT.LOGO_PATH.SM').$supplier->banner) : asset($this->config->get('constants.MERCHANT.LOGO_PATH.SM').$result->mrlogo); */
					
        }
		//echo"<pre>"; print_r($supplier);exit;
        return $supplier;
    }
	
	public function get_parent_category($cateagory_id){
		//print_R($cateagory_id);exit;
		$qry = DB::table(config('tables.BUSINESS_CATEGORY_TREE').' as a')
				->join(config('tables.BUSINESS_CATEGORY_TREE').' as b',function($j){
						$j->on('b.cat_lftnode','<=','a.cat_lftnode')
						->on('b.cat_rgtnode','>=','a.cat_rgtnode')
						->on('b.root_bcategory_id','=','a.root_bcategory_id');
					})
				->join(config('tables.BUSINESS_CATEGORY_LANG').' as c','c.bcategory_id','=','b.bcategory_id')
				->where('a.bcategory_id',$cateagory_id)
				->select(DB::Raw('group_concat(c.bcategory_name ORDER BY b.cat_lftnode ASC SEPARATOR " > ") as parent'));
				$result = $qry->first();
		return $result->parent;
				
		
	}

    public function getBankingAccountTypes ()
    {
        return DB::table(Config::get('tables.BANKING_ACCOUNT_TYPES'))
                        ->selectRaw('id, account_type')
                        ->orderby('account_type', 'ASC')
                        ->get();
    }

    public function getBusinessTypes ()
    {
        return DB::table(Config::get('tables.TYPE_OF_BUSINESS'))
                        ->selectRaw('business_id,business')
                        ->orderby('business', 'ASC')
                        ->get();
    }

    public function getDocumentTypes ($arr = array())
    {
        extract($arr);
        $query = DB::table(Config::get('tables.DOCUMENT_TYPES'))
                ->selectRaw('document_type_id,type')
                ->orderby('type', 'ASC');
        if (isset($proof_type) && !empty($proof_type))
        {
            $query->where('proof_type', $proof_type);
        }
        return $query->get();
    }

    public static function getDocumentDetails ($document_type_id, $prefix = '')
    {
        $details = DB::table(Config::get('tables.DOCUMENT_TYPES'))
                ->where('document_type_id', $document_type_id)
                ->pluck('other_fields');
        $details = json_decode($details, true);
        if (!empty($details))
        {
            array_walk($details, function(&$field) use($prefix)
            {
                $k = [];
                $k[(!empty($prefix) ? $prefix.'.' : '').$field['id']] = $field['validate']['rules'];
                $field['rules'] = $k;
                $field['validate']['message'] = [$field['id']=>$field['validate']['message']];
                if (!empty($prefix))
                {
                    $field['validate']['message'] = [$prefix=>$field['validate']['message']];
                }
                $field['message'] = array_dot($field['validate']['message']);
                $field = (array) $field;
            });
            $a = [];
            foreach (array_column($details, 'message') as $m)
            {
                $a = array_merge($a, $m);
            }
        }
        return $details;
    }

    public function getSupplierVerificationStatus ($supplier_id)
    {
        return DB::table(Config::get('tables.SUPPLIER_MST'))
                        ->where('supplier_id', $supplier_id)
                        ->value('is_verified');
    }

    public function isProductAddable ($partner_id)
    {
        return DB::table(Config::get('tables.PARTNER_SUBSCRIPTION'))
                        ->where('partner_id', $partner_id)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->where(function($av)
                        {
                            $av->where('avaliable_products', '<', 0)
                            ->orWhere('avaliable_products', '>', 'added_products');
                        })
                        ->exists();
    }

    /*
     * Function Name        : updateProductDiscounts
     * Params               : (object)product(supplier_product_id,qty,amount)
     */

    public static function updateProductDiscounts (&$product, $with_sales_commission = false)
    {
        $product->qty = (isset($product->qty) && !empty($product->qty)) ? $product->qty : 1;
        $discount_amt = ['site'=>(object) ['amount'=>0,
                'percentage'=>0],
            'supplier'=>(object) ['amount'=>($product->price - $product->mrp_price),
                'percentage'=>0]
        ];
        $discount_info = [];
        $sales_commission = (object) [
                    'currency_id'=>$product->currency_id,
                    'price'=>$product->price,
                    'supplier_price'=>$product->mrp_price,
                    'supplier_discount_per'=>0,
                    'supplier_sold_price'=>0,
                    'commission_unit'=>NULL,
                    'commission_value'=>0,
                    'commission_amount'=>0,
                    'site_price'=>0,
                    'mrp_price'=>0,
                    'qty'=>$product->qty,
                    'price_sub_total'=>0,
                    'commission_sub_total'=>0
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
                        ->where(function($subquery3)use($product)
                        {
                            $subquery3->whereNotNull('dp.caregories_ids')
                            ->whereRaw('find_in_set('.$product->category_id.',dp.caregories_ids)');
                        });
                    })
                    ->where(function($subquery2) use($product)
                    {
                        $subquery2->whereNull('dp.supplier_ids')
                        ->where(function($subquery3)use($product)
                        {
                            $subquery2->whereNotNull('dp.supplier_ids')
                            ->whereRaw('find_in_set('.$product->supplier_id.',dp.supplier_ids)');
                        });
                    })
                    ->where(function($subquery2) use($product)
                    {
                        $subquery2->whereNull('dp.product_ids')
                        ->where(function($subquery3)use($product)
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
            $sales_commission->supplier_price = $product->mrp_price;
            $discount_amt['supplier'] = $discount_amt['supplier']->amount + (($product->price / 100) * $discount_amt['supplier']->percentage);
            $sales_commission->supplier_sold_price = $product->price - $discount_amt['supplier'];
            $sales_commission->supplier_discount_per = $product->price > 0 ? round((($product->price - $sales_commission->supplier_sold_price) / $product->price) * 100) : 0;
            $sales_commission->commission_unit = (int) $product->commission_unit;
            $sales_commission->commission_value = (float) $product->commission_value;
            $sales_commission->commission_amount = (($sales_commission->commission_unit == Config::get('constants.COMMISSION_UNIT.PERCENTAGE')) ? (($sales_commission->supplier_sold_price / 100) * $sales_commission->commission_value) : $sales_commission->commission_value);
            $product->mrp_price = $sales_commission->supplier_sold_price + $sales_commission->commission_amount;
            $sales_commission->site_price = $discount_amt['site']->amount + (($product->mrp_price / 100) * $discount_amt['site']->percentage);
            $product->mrp_price = $product->mrp_price - $sales_commission->site_price;
            $sales_commission->admin_discount_per = $product->mrp_price > 0 ? round(($sales_commission->site_price / $product->mrp_price) * 100) : 0;
            $sales_commission->mrp_price = $product->mrp_price;
            $product->discount = $product->price > 0 ? round((($product->price - $product->mrp_price) / $product->price) * 100) : 0;
            $product->off_per = ($product->discount) ? $product->discount.'% '.trans('product_browse.off') : '';
            $sales_commission->price_sub_total = $sales_commission->mrp_price * $product->qty;
            $sales_commission->commission_amount = $sales_commission->commission_amount - $sales_commission->site_price;
            $sales_commission->commission_amount = ($sales_commission->commission_amount > 0) ? $sales_commission->commission_amount : 0;
            $sales_commission->commission_sub_total = $sales_commission->commission_sub_total * $product->qty;
            if ($with_sales_commission)
            {
                $product->sales_commission = $sales_commission;
                $product->discount_info = $discount_info;
            }
        }
    }
	
	/* Get Parent Category */
/* 
    public function getCategoryParent (array $wdata = [], $category_type = NULL)
    {
        extract($wdata);
        $qry = DB::table(Config::get('tables.BUSINESS_CATEGORY_TREE').' as catT')
                ->join(Config::get('tables.BUSINESS_CATEGORY').' as cat', 'cat.bcategory_id', '=', 'catT.bcategory_id')
                ->join(Config::get('tables.BUSINESS_CATEGORY_LANG').' as catL', 'catL.bcategory_id', '=', 'cat.bcategory_id')
                ->where('catL.lang_id', Config::get('app.locale_id'))
                ->where('cat.is_deleted', Config::get('constants.OFF'));
        if (!empty($category_type))
        {
            $qry->where('cat.category_type', $category_type);
        }
        else
        {
            $qry->where('cat.category_type', Config::get('constants.BCATEGORY_TYPE.DEAL'));
        }
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
    } */

    public static function getCategoryParents ($category_id)
    {
        $parents = DB::table(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as np')
                ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as pp', function($pp)
                {
                    $pp->on(DB::raw('np.cat_lftnode BETWEEN pp.cat_lftnode AND pp.cat_rgtnode'), DB::raw(''), DB::raw(''));
                })
                ->where('np.category_id', $category_id)
                ->selectRaw('group_concat(pp.category_id) as parents')
                ->orderby('pp.cat_lftnode')
                ->pluck('parents');
        return ($parents) ? explode(',', $parents) : [];
    }

    public function addToWishList ($arr = array())
    {
        extract($arr);
        $data = [];
        $wistlist = DB::table(Config::get('tables.ACCOUNT_WISH_LIST'))
                ->where('supplier_product_id', $supplier_product_id)
                ->select('is_deleted', 'wish_list_id');
        if (isset($account_type_id) && !empty($account_type_id) && isset($relative_account_id) && !empty($relative_account_id))
        {
            $wistlist->where('account_type_id', $account_type_id)
                    ->where('relative_account_id', $relative_account_id);
            $data['account_type_id'] = $account_type_id;
            $data['relative_account_id'] = $relative_account_id;
        }
        else
        {
            $wistlist->where('account_id', $account_id);
        }
        $wistlist = $wistlist->first();
        $data['supplier_product_id'] = $supplier_product_id;
        $data['account_id'] = $account_id;
        if ($wistlist)
        {
            if ($wistlist->is_deleted)
            {
                $data['is_deleted'] = Config::get('constants.OFF');
                return DB::table(Config::get('tables.ACCOUNT_WISH_LIST'))
                                ->where('wish_list_id', '=', $wistlist->wish_list_id)
                                ->update($data);
            }
        }
        else
        {
            $data['created_on'] = date('Y-m-d H:i:s');
            return DB::table(Config::get('tables.ACCOUNT_WISH_LIST'))
                            ->insertGetId($data);
        }
        return false;
    }

    public function removeFromWishList ($arr)
    {
        extract($arr);
        $wistlist = DB::table(Config::get('tables.ACCOUNT_WISH_LIST'))
                ->where('supplier_product_id', $supplier_product_id)
                ->where('account_id', $account_id);
        if (isset($account_type_id) && !empty($account_type_id) && isset($relative_account_id) && !empty($relative_account_id))
        {
            $wistlist->where('account_type_id', $account_type_id)
                    ->where('relative_account_id', $relative_account_id);
        }
        return $wistlist->update(['is_deleted'=>Config::get('constants.ON')]);
    }

    public static function generateAPPKey ($arr = array())
    {
        extract($arr);
        if (isset($partner_id) && isset($subscribe_id))
        {
            return base64_encode($partner_id.','.$subscribe_id.','.date('YMdHis'));
        }
        return false;
    }

    public function get_account_list ()
    {

        $query = DB::table(Config::get('tables.ACCOUNT_TYPES'))
                ->selectRaw('account_type_name, id')
                ->where('has_wallet', Config::get('constants.ON'))
                ->orderby('id', 'ASC');

        return $query->get();
    }

    public function randomString ($length = 8)
    {
        $str = '';
        $characters = array_merge(range('a', 'z'), range('0', '9'));
        for ($i = 0; $i < $length; $i++)
        {
            $rand = mt_rand(0, 35);
            $str .= $characters[$rand];
        }
        return $str;
    }

    public function generateSlug ($string)
    {
        return strtolower(str_replace(' ', '-', $string));
    }

    public function getPostIDbyKey ($post_type, $code)
    {
        $id = null;
        switch ($post_type)
        {
            case 'supplier':
            case Config::get('constants.POST_TYPE.SUPPLIER'):
                $id = DB::table(Config::get('tables.ACCOUNT_SUPPLIERS'))
                        ->where('supplier_code', $code)
                        ->pluck('supplier_id');
                break;
            case 'products':
            case Config::get('constants.POST_TYPE.PRODUCT'):
                $id = DB::table(Config::get('tables.PRODUCT_INFO'))
                        ->where('product_code', $code)
                        ->pluck('product_id');
                break;
        }
        return $id;
    }

    public function getSipplierShippingFee ($product_id, $currency_id)
    {
        return 0;
    }

    /**
     * @param object $product with properties array $user int $country_id, int $city_id, int $region_id,int $weight, int $currency_id,int $supplier_id,int $mode_id<i>(optional)</i>
     * @return None set shipping_charge and delivery_days property to the $product.
     */
    public function getShipmentDetails (&$product)
    {
        $product->shipping_charge = 0;
        $product->delivery_days = 0;
        $product->mode = null;
        if (isset($product->product_id) && !empty($product->product_id) && !isset($product->weight))
        {
            $weightQry = DB::table(Config::get('tables.PRODUCTS_LIST'))
                    ->where('product_id', $product->product_id);
            if (isset($product->product_cmb_id) && !empty($product->product_cmb_id))
            {
                $weightQry->where('product_cmb_id', $product->product_cmb_id);
            }
            $product->weight = $weightQry->selectRaw('greatest(weight,volumetric_weight) as weight')->pluck('weight');
        }
        if (isset($product->supplier_product_id) && !empty($product->supplier_product_id) && !isset($product->is_shipping_beared))
        {
            $is_shipping_beared = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS'))
                    ->where('supplier_product_id', $product->supplier_product_id)
                    ->pluck('is_shipping_beared');
            $product->is_shipping_beared = $is_shipping_beared;
        }
        $supplierdetails = DB::table(Config::get('tables.SUPPLIER_PREFERENCE').' as sp')
                ->leftJoin(Config::get('tables.SUPPLIER_PICKUP_ADDRESS').' as spa', 'spa.supplier_id', '=', 'sp.supplier_id')
                ->leftJoin(Config::get('tables.LOCATION_PINCODES').' as lp', 'lp.pincode', '=', 'spa.postal_code')
                ->leftJoin(Config::get('tables.LOCATION_DISTRICTS').' as ld', 'ld.district_id', '=', 'lp.district_id')
                ->leftJoin(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'ld.state_id')
                ->selectRaw('sp.is_ownshipment,sp.logistic_id,spa.postal_code,ls.region_id,ls.country_id')
                ->where('sp.supplier_id', $product->supplier_id)
                ->first();
        if (!empty($supplierdetails))
        {
            $supplierdetails->is_shipping_beared = $product->is_shipping_beared;
            $supplierdetails->logistic_id = (!empty($supplierdetails) && !$supplierdetails->is_ownshipment && !empty($supplierdetails->logistic_id)) ? $supplierdetails->logistic_id : Config::get('constants.DEFAULT.LOGISTIC_ID');
            $supplierdetails->mode_id = $product->mode_id = isset($product->mode_id) && !empty($product->mode_id) ? $product->mode_id : Config::get('constants.DEFAULT.MODE_ID');

            $weight_slab = DB::table(Config::get('tables.PRODUCT_WEIGHT_SLAB'))
                    ->where('min_grams', '<=', $product->weight)
                    ->where(function($max) use($product)
                    {
                        $max->whereNUll('max_grams')
                        ->orWhere(function($m) use($product)
                        {
                            $m->WhereNotNull('max_grams')
                            ->where('max_grams', '>=', $product->weight);
                        });
                    })
                    ->select('weight_slab_id', 'for_each_grams')
                    ->first();
            if ($weight_slab)
            {
                $supplierdetails->for_each_grams = $weight_slab->for_each_grams;
                $shippment_details = DB::table(Config::get('tables.SUPPLIER_PRODUCT_SHIPPMENT_SETTINGS').' as sh')
                        ->join(Config::get('tables.COURIER_MODE_LOOKUPS').' as m', 'm.mode_id', '=', 'sh.mode_id')
                        ->where('sh.country_id', $product->user['country_id'])
                        ->where('sh.weight_slab_id', $weight_slab->weight_slab_id)
                        ->where('sh.currency_id', $product->currency_id)
                        ->where('sh.mode_id', $product->mode_id)
                        ->where('sh.logistic_id', $supplierdetails->logistic_id)
                        ->where(function($sc) use($product)
                        {
                            $sc->where('sh.supplier_id', $product->supplier_id)
                            ->orWhereNull('sh.supplier_id');
                        })
                        ->select('m.mode', 'sh.delivery_charge', 'sh.delivery_days', 'sh.zone_delivery_days', 'sh.zone_delivery_charges', 'sh.national_delivery_days', 'sh.national_delivery_charges')
                        ->first();
                if (!empty($supplierdetails) && !empty($shippment_details))
                {
                    $supplierdetails->user = $product->user;
                    $supplierdetails->shippment_details = $shippment_details;
                    $product->shipping_info = $supplierdetails;
                    $product->shipping_charge = null;
                    $product->mode = $shippment_details->mode;
                    if ($supplierdetails->postal_code == $product->user['postal_code'])
                    {
                        $product->shipping_charge = $product->qty * (($product->weight / $weight_slab->for_each_grams) * $shippment_details->delivery_charge);
                        $product->delivery_days = $shippment_details->delivery_days;
                    }
                    else if ($supplierdetails->region_id == $product->user['region_id'])
                    {
                        $product->shipping_charge = $product->qty * (($product->weight / $weight_slab->for_each_grams) * $shippment_details->zone_delivery_charges);
                        $product->delivery_days = $shippment_details->zone_delivery_days;
                    }
                    elseif ($supplierdetails->country_id == $product->user['country_id'])
                    {
                        $product->shipping_charge = $product->qty * (($product->weight / $weight_slab->for_each_grams) * $shippment_details->national_delivery_charges);
                        $product->delivery_days = $shippment_details->national_delivery_days;
                    }
                    if (isset($product->commission_info))
                    {
                        $product->commission_info->shipping_charge = $product->shipping_charge;
                    }
                    $product->shipping_info->shipping_charge = $product->shipping_charge;
                    if ($product->is_shipping_beared)
                    {
                        $product->shipping_charge = 0;
                    }
                }
            }
        }
    }

    /*
     * Function Name        : taxValue
     * Params               : $product
     */

    public function taxValue (&$product)
    {
        $product->tax_info = (object) ['total_tax_per'=>0, 'total_tax_amount'=>0, 'taxes'=>[]];
        $date = isset($date) ? date('Y-m-d', strtotime($date)) : date('Y-m-d');
        $taxes = $taxes = DB::table(Config::get('tables.TAXES').' as t')
                ->join(Config::get('tables.TAX_VALUES').' as tv', function($tv) use($product)
                {
                    $tv->on('tv.tax_id', '=', 't.tax_id')
                    ->where('tv.post_type_id', '=', Config::get('constants.POST_TYPE.CATEGORY'))
                    ->where('tv.relative_id', '=', $product->category_id)
                    ->where('tv.is_deleted', '=', Config::get('constants.OFF'));
                })
                ->where(function($range) use($product)
                {
                    $range->where('tv.is_range', Config::get('constants.OFF'))
                    ->orWhere(function($range1) use($product)
                    {
                        $range1->where('tv.is_range', Config::get('constants.ON'))
                        ->where('tv.range_start_from', '>=', $product->price)
                        ->where('tv.range_end_to', '<=', $product->price);
                    });
                })
                ->where('t.start_date', '<=', $date)
                ->where('t.end_date', '>=', $date)
                ->where('t.is_deleted', Config::get('constants.OFF'))
                ->where('t.status', Config::get('constants.ACTIVE'))
                ->select('t.tax', 't.value_type', 'tv.tax_value', 't.currency_id')
                ->get();
        $product->tax_info->taxes = $taxes;
        array_walk($taxes, function($tax) use(&$product)
        {
            if ($tax->value_type == Config::get('constants.TAX_VALUE_TYPE.PERCENTAGE'))
            {
                $product->tax_info->total_tax_per+=$tax->tax_value;
            }
            elseif (isset($currency_id) && !empty($currency_id))
            {
                $rate = $this->get_exchange_rate($tax->currency_id, $currency_id);
                $product->tax_info->total_tax_amount+=($rate * $tax->tax_value);
            }
        });
    }

	/* NOTIFICATIONS  */
	public function getNotifications (array $arr = array(), $count = false, $limit = 5)
    {  
        extract($arr);
        $res = DB::table(Config::get('tables.ACCOUNT_NOTIFICATIONS').' as n')
                ->leftJoin(Config::get('tables.ACCOUNT_NOTIFICATIONS_READ').' as st', function ($subquery) use($account_id)
                {
                    $subquery->on('st.notification_id', '=', 'n.notification_id')
                    ->where('st.account_id', '=', $account_id);
                })
                ->whereRaw('FIND_IN_SET('.$account_id.',n.account_ids)')
                ->where('n.is_deleted', Config::get('constants.OFF'))
                ->selectRaw('n.notification_id,n.data ,n.created_on,st.read_on,n.is_starred')
                ->orderby('n.created_on', 'desc');
        if (isset($notification_type) && !empty($notification_type))
        {
            $res->where('n.notification_type', $notification_type);
        }
        if (isset($notification_id) && !empty($notification_id))
        {
            $res->where('n.notification_id', $notification_id);
        }
        if ($count)
        {
            return $res->count();
        }
        else
        {
            if (isset($length))
            {
                $res->skip($start)->take($length);
            }
            $notifications = $res->get();		
            foreach ($notifications as &$notification)
            { 
                $notification->data = json_decode($notification->data);				
                $notification->data->notification->id = $notification->notification_id;
                $notification->data->notification->created_on = showUTZ($notification->created_on, 'd-M-Y H:i:s');
                $notification->data->notification->icon = asset($notification->data->notification->icon);
                $notification->data->notification->banner = asset('imgs/banner/220/150/image.jpg');
                $notification->data->notification->is_starred = $notification->is_starred;
                $notification->data->notification->is_read = !empty($notification->read_on) ? 1 : 0;
                $notification = $notification->data->notification;
                unset($notification->sound);
                unset($notification->vibrate);
            }
            return (isset($notification_id) && !empty($notification_id) && isset($notifications[0])) ? $notifications[0] : $notifications;
        }
    }
	
	public function markNotificationRead (array $arr = array())
    {
        if (!empty($arr['account_id']) && !empty($arr['notification_id']))
        {
            if (!DB::table(Config::get('tables.ACCOUNT_NOTIFICATIONS_READ'))
                            ->where($arr)
                            ->exists())
            {
                $arr['read_on'] = getGTZ();
                DB::table(Config::get('tables.ACCOUNT_NOTIFICATIONS_READ'))
                        ->insertGetID($arr);
                return true;
            }
        }
        return false;
    }
	
	public function updateNotificationToken (array $arr = array())
    {
        extract($arr);
        if (isset($account_id) && !empty($account_id))
        {
            return DB::table(Config::get('tables.ACCOUNT_LOG'))
						->where('account_id', $account_id)
						->where('account_log_id', $account_log_id)
						->update(['fcm_registration_id'=>$fcm_registration_id]);
        }
        else
        {
            if (DB::table(Config::get('tables.NOTIFICATION_SUBSCRIBERS'))
						->where('fcm_registration_id', $fcm_registration_id)
						->exists())
            {
                return DB::table(Config::get('tables.NOTIFICATION_SUBSCRIBERS'))
							->where('fcm_registration_id', $fcm_registration_id)
							->where('is_deleted', Config::get('constants.ON'))
							->update(['is_deleted'=>Config::get('constants.OFF'), 'updated_on'=>getGTZ()]);
            }
            else
            {
                return DB::table(Config::get('tables.NOTIFICATION_SUBSCRIBERS'))
                        ->insertGetID(['fcm_registration_id'=>$fcm_registration_id, 'created_on'=>getGTZ()]);
            }
        }
    }
	
   /*  public function getNotifications ($arr = array(), $count = false, $limit = 5)
    {
        extract($arr);
        $res = DB::table(Config::get('tables.ACCOUNT_NOTIFICATIONS').' as n')
                ->leftJoin(Config::get('tables.ACCOUNT_NOTIFICATIONS_READ').' as nr', function ($subquery) use($account_id)
                {
                    $subquery->on('nr.notification_id', '=', 'n.notification_id')
                    ->where('nr.account_id', '=', $account_id);
                })
                ->whereRaw('FIND_IN_SET('.$account_id.',n.account_ids)')
                ->where('n.is_deleted', Config::get('constants.OFF'))
                ->selectRaw('n.notification_id,n.data,n.created_on,nr.read_on')
                ->orderby('n.created_on', 'desc');
        if ($count)
        {
            return $res->count();
        }
        else
        {
            $notifications = $res->take($limit)->get();
            array_walk($notifications, function(&$notification)
            {
                $notification->data = json_decode($notification->data);
                $notification->data->id = $notification->notification_id;
                $notification->data->created_on = date('d-M-Y H:i:s', strtotime($notification->created_on));
                $notification = $notification->data;
            });
            return $notifications;
        }
    }	 

    public function updateNotificationToken ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.DEVICE_LOG'))
                        ->where('device_log_id', $device_log_id)
                        ->update(['fcm_registration_id'=>$fcm_registration_id]);
    }

    public function markNotificationRead ($arr = array())
    {
        $arr['read_on'] = date('Y-m-d H:i:s');
        return DB::table(Config::get('tables.ACCOUNT_NOTIFICATIONS_READ'))
                        ->insertGetID($arr);
    } */

    public function getAccountActivationKey ($account_id)
    {
        return $activation_key = DB::table(Config::get('tables.ACCOUNT_MST'))
                ->where('account_id', $account_id)
                ->value('activation_key');
    }

    public function updateAccountVerificationCode ($device_log_id)
    {		
        $code = rand(100000, 999999);
        return (DB::table(Config::get('tables.DEVICE_LOG'))
                        ->where('device_log_id', $device_log_id)
                        ->update(['code'=>$code])) ? $code : false;
						
    }

    public function checkAccountVerificationCode ($device_log_id, $code, $update = true)
    {
		return true;
        $query = DB::table(Config::get('tables.DEVICE_LOG'))
                ->where('device_log_id', $device_log_id)
                ->where('code', $code);
        return $update ? $query->update(['code'=>null]) : $query->exists();
    }

    public function checkAccount ($username, $account_type_id = NULL)
    {
        $query = DB::table(Config::get('tables.ACCOUNT_MST').' as am')
                ->join(Config::get('tables.ACCOUNT_DETAILS').' as ad', 'ad.account_id', '=', 'am.account_id')
                ->selectRaw('am.account_id, am.account_type_id,  concat(ad.firstname,\' \',ad.lastname) as full_name, ad.firstname, ad.lastname, am.email, am.mobile, am.uname, am.is_deleted, am.pass_key, am.login_block')
                ->where('am.is_deleted', Config::get('constants.OFF'))
                //->where('am.account_type_id', '!=', Config::get('constants.ACCOUNT_TYPE.SELLER'))
                ->where(function($subquery) use($username)
				{
					$subquery->where('am.uname', $username)
							->orWhere('am.email', 'like' , '%'.$username.'%')
							->orWhere('am.mobile', $username);
				});
				if (!empty($account_type_id))
				{
					$query->where('am.account_type_id', $account_type_id);
				}
        return $query->first();
    }

    /**
     * @param array $arr associate array of username and password
     * @param int $account_type_id check account type<i>Option(Default NULL)</i>
     * @return boolean in update means true else false
     */
    public function updatePassword ($arr, $account_type_id = NULL)
    {
        extract($arr);
        DB::beginTransaction();
        if ($this->checkAccountVerificationCode($device_log_id, $verification_code))
        {
            $query = DB::table(Config::get('tables.ACCOUNT_MST'))
                    ->where(function($subquery) use($username)
            {
                $subquery->where('uname', $username)
                ->orWhere('email', $username)
                ->orWhere('mobile', $username);
            });
            if (!empty($account_type_id))
            {
                $query->where('account_type_id', $account_type_id);
            }
            if ($query->update(['pass_key'=>md5($password)]))
            {
                DB::commit();
                return 1;
            }
            return 2;
        }
        DB::rollback();
        return false;
    }

    /**
     * @param array $arr associate array with account_id, current_password,new_password
     * @return bool True if password changed or else false if incorrect old password/Old and new passwords are same
     */
    public function changePassword ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.ACCOUNT_LOGIN_MST'))
                        ->where('account_id', $account_id)
                        ->where('pass_key', md5($current_password))
                        ->update(['pass_key'=>md5($new_password)]);
    }

    /**
     * @param string $activation_key  which account's activation_key
     * @return bool True if email id verified else false if code is incorrect
     */
    public function updateEmailVerification ($activation_key)
    {
        $account_id = DB::table(Config::get('tables.ACCOUNT_MST'))
                ->where('activation_key', $activation_key)                
                ->value('account_id');
        if ($account_id)
        {
            return DB::table(Config::get('tables.ACCOUNT_PREFERENCE'))
                            ->where('account_id', $account_id)
                            ->where('is_email_verified', Config::get('constants.OFF'))
                            ->update(['is_email_verified'=>Config::get('constants.ON')]);
        }
        return false;
    }
	
	public function verifyEmail($account_id){
		if ($account_id)
        {
            return DB::table(Config::get('tables.ACCOUNT_PREFERENCE'))
                            ->where('account_id', $account_id)
                            ->where('is_email_verified', config('constants.OFF'))
                            ->update(['is_email_verified'=>config('constants.ON')]);
        }
        return false;
	}

    /**
     * @param int $account_id  which account's mobile verification to be changed
     * @param int $device_log_id  to check verification code sent in mobile
     * @param int $code User input code
     * @return bool True if mobile id verified else false if code is incorrect
     */
    public function updateMobileVerification ($account_id, $device_log_id, $code)
    {		
        DB::beginTransaction();
        if ($this->checkAccountVerificationCode($device_log_id, $code))
        {
            if (DB::table(Config::get('tables.ACCOUNT_PREFERENCE'))
                            ->where('account_id', $account_id)
                            ->where('is_mobile_verified', Config::get('constants.OFF'))
                            ->update(['is_mobile_verified'=>Config::get('constants.ON')]))
            {
                DB::commit();
                return true;
            }
        }
        DB::rollback();
        return false;
    }
	public function update_mobile_verification($account_id)
	{
		if (DB::table(Config::get('tables.ACCOUNT_PREFERENCE'))
						->where('account_id', $account_id)
						->where('is_mobile_verified', Config::get('constants.OFF'))
						->update(['is_mobile_verified'=>Config::get('constants.ON')]))
		{
			return true;
		}
		return false;
	}

    public function getProductsCount ($currency_id)
    {
        $count = DB::table(Config::get('tables.PRODUCTS').' as p')
                ->join(Config::get('tables.PRODUCT_DETAILS').' as pd', 'pd.product_id', '=', 'p.product_id')
                ->where('p.is_deleted', Config::get('constants.OFF'))
                ->selectRaw('count(pd.product_id) as total_products,sum(IF(pd.is_verified='.Config::get('constants.ON').',1,0)) as verified_products')
                ->first();
        if ($count)
        {
            $count->total_products = number_format($count->total_products, 0, '.', ',');
            $count->verified_products = number_format($count->verified_products, 0, '.', ',');
        }
        else
        {
            $count = (object) ['total_products'=>0, 'verified_products'=>0];
        }
        return $count;
    }

    public function getProductItemCount ($currency_id)
    {
        $count = DB::table(Config::get('tables.SUPPLIER_PRODUCT_STOCK_MANAGEMENT').' as ps')
                ->join(Config::get('tables.PRODUCT_DETAILS').' as p', function($p) use($currency_id)
                {
                    $p->on('p.product_id', '=', 'ps.product_id')
                    ->where('p.is_verified', '=', Config::get('constants.ON'));
                })
                ->selectRaw('sum(ps.stock_on_hand) as stock_on_hand,sum(sold_items) as sold_items')
                ->first();
        if ($count)
        {
            $count->stock_on_hand = number_format($count->stock_on_hand, 0, '.', ',');
            $count->sold_items = number_format($count->sold_items, 0, '.', ',');
        }
        else
        {
            $count = (object) ['stock_on_hand'=>0, 'sold_items'=>0];
        }
        return $count;
    }

    public function getOrderCount ()
    {
        $count = DB::table(Config::get('tables.ORDERS'))
                ->where('is_deleted', '=', Config::get('constants.OFF'))
                ->selectRaw('count(order_id) as total,sum(IF(order_status_id='.Config::get('constants.ORDER_STATUS.PLACED').' AND approval_status_id='.Config::get('constants.APPROVAL_STATUS.PENDING').',1,0)) as new')
                ->first();
        if ($count)
        {
            $count->total = number_format($count->total, 0, '.', ',');
            $count->new = number_format($count->new, 0, '.', ',');
        }
        else
        {
            $count = (object) ['total'=>0, 'new'=>0];
        }
        return $count;
    }

    public function getCustomerCount ()
    {
        $count = DB::table(Config::get('tables.ACCOUNT_MST').' as am')
                /* ->join(Config::get('tables.ACCOUNT_LOGIN_MST').' as al', function($p)
                {
                    $p->on('al.account_id', '=', 'am.account_id')
                    ->where('al.account_type_id', '=', Config::get('constants.ACCOUNT_TYPE.USER'));
                }) */				
                ->where('account_type_id', Config::get('constants.ACCOUNT_TYPE.USER'))
                ->where('is_deleted', Config::get('constants.OFF'))
                ->selectRaw('count(am.account_id) as total,sum(IF(am.status='.Config::get('constants.ACTIVE').',1,0)) as active')
                ->first();
        if ($count)
        {
            $count->total = number_format($count->total, 0, '.', ',');
            $count->active = number_format($count->active, 0, '.', ',');
        }
        else
        {
            $count = (object) ['total'=>0, 'active'=>0];
        }
        return $count;
    }

    public static function currency_format ($arr = array())
    {
        $currency = $currency_symbol = null;
        $decimal = 2;
        extract($arr);
        if (isset($currency_id) && (!isset($currency) || !isset($currency_symbol)))
        {
            $c = DB::table(Config::get('tables.CURRENCIES'))
                    ->where('currency_id', $currency_id)
                    ->select('currency', 'currency_symbol', 'decimal_places')
                    ->first();
            if (!empty($c))
            {
                $currency = $c->currency;
                $currency_symbol = $c->currency_symbol;
                $decimal = $c->decimal_places;
            }
        }
        return $currency.' '.number_format($amt, $decimal, '.', ',').' '.$currency_symbol;
    }

    public static function randomCode ($length = 8, $c = 'A-Za-z0-9')
    {
        switch ($c)
        {
            case 'A-Za-z0-9':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz1234567890';
                $chars_count = 41;
                break;
            case 'A-Za-z':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
                $chars_count = 41;
                break;
            case 'A-Z0-9':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                $chars_count = 35;
                break;
            case 'a-z0-9':
                $chars = 'abcdefghijkmnpqrstuvwxyz1234567890';
                $chars_count = 35;
                break;
            case 'a-z':
                $chars = 'abcdefghijkmnpqrstuvwxyz';
                $chars_count = 25;
                break;
            case 'A-Z':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $chars_count = 25;
                break;
        }
        $string = $chars{rand(0, $chars_count)};
        for ($i = 1; $i < $length; $i = strlen($string))
        {
            $r = $chars{rand(0, $chars_count)};
            if ($r != $string{$i - 1})
                $string .= $r;
        }
        return $string;
    }

    public static function generateUserCode ()
    {
        $user_codes = DB::table(Config::get('tables.ACCOUNT_LOGIN_MST'))
                ->lists('user_code');
        re_create:
        $code = self::randomCode(8, 'A-Z0-9');
        if (in_array($code, $user_codes))
        {
            goto re_create;
        }
        else
        {
            return $code;
        }
    }

    public function checkPincode ($pincode,$country_id,$with_cities = false)
    {	
	    $lQuery = DB::table(Config::get('tables.LOCATION_PINCODES').' as lp')
                ->where('lp.pincode', $pincode)
                ->where('lp.country_id', $country_id)
                ->join(Config::get('tables.LOCATION_DISTRICTS').' as ld', 'ld.district_id', '=', 'lp.district_id')
                ->join(Config::get('tables.LOCATION_STATE').' as ls', 'ls.state_id', '=', 'ld.state_id')
                ->join(Config::get('tables.LOCATION_REGIONS').' as lr', 'lr.region_id', '=', 'ls.region_id')
                ->join(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'ls.country_id'); 		
        if ($with_cities)
        {
            $locations = $lQuery->select('lp.pincode_id', 'lp.pincode', 'lc.country_id', 'lc.country', 'ls.state_id', 'ls.state', 'ls.region_id', 'lr.region', 'ld.district_id', 'ld.district')
                    ->first();
            if (!empty($locations))
            {
                $locations->cities = DB::table(Config::get('tables.LOCATION_CITY'))
                        ->where('pincode_id', $locations->pincode_id)
                        ->select('city_id as id', 'city as text')
                        ->get();
            }
            return $locations;
        }
        else
        {
            return $lQuery->select('lp.pincode_id', 'lp.pincode', 'lc.country_id', 'lc.country', 'ls.state_id', 'ls.state', 'ls.region_id', 'lr.region', 'ld.district_id', 'ld.district')
                            ->first();
        }
    }
	
	
	public function get_geo_address($arr){
		extract($arr);
		$addr = [];
		$ch = curl_init();         
            $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&key='.Config::get('services.google.map_api_key');
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
		    if(!empty($result)){
				$addr = $result->results[0];
			}
		return $addr;
	}

    public function postRequest ($url, $data = array())
    {
        $request = curl_init();
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_HTTPHEADER, ['X-Device-Token:'.Config::get('device_log')->token, 'Content-Type: application/json']);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($request, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($request);
        $status = curl_getinfo($request, CURLINFO_HTTP_CODE);
        if ($response === FALSE)
        {
            die('Curl failed: '.curl_error($request));
        }
        curl_close($request);
        return (object) ['status'=>$status, 'responseJSON'=>!empty($response) ? json_decode($response) : []];
    }	
	
	public function country_list (array $arr = array(), $check_status = true){
        $qry =  DB::table(config('tables.LOCATION_COUNTRY'))
                ->orderby('country', 'ASC')
                ->selectRAW('country_id,country,phonecode');

        if ($check_status)
        {
            $qry->where('status', config('constants.ON'));
        }
        return $qry->get();
    }
	
	public function sellerCategories (array $arr = array(), $type = null)
    {
        extract($arr);
        $categories = DB::table(config('tables.BUSINESS_CATEGORY').' as pc')
                ->join(config('tables.BUSINESS_CATEGORY_LANG').' as bcl', function($bcl)
                {
                    $bcl->on('bcl.bcategory_id', '=', 'pc.bcategory_id')
                    ->where('bcl.lang_id', '=', config('app.locale_id'));
                })
                //->leftjoin(config('tables.BUSINESS_CATEGORY_TREE').' as cc', 'cc.bcategory_id', '=', 'pc.bcategory_id')
				->join(config('tables.BUSINESS_CATEGORY_TREE').' as cc', function($cc)
                {
                    $cc->on('cc.bcategory_id', '=', 'pc.bcategory_id')
						->where('cc.parent_bcategory_id', '=', 1485);
                })
                ->where('pc.is_deleted', config('constants.OFF'))
                ->where('pc.status', config('constants.ACTIVE'))
                ->where('pc.is_visible', config('constants.ON'))
                ->orderBy('pc.bcategory_id', 'ASC')
				->where('pc.category_type', 1)
                ->selectRaw('pc.bcategory_id as id, bcl.bcategory_name as name, cc.parent_bcategory_id as parent_id, cc.parents, pc.slug as url');        
        return $categories = $categories->get();        
    }
	
	/* public function sellerAllCategories (array $arr = array(), $type = null)
    {
        extract($arr);
        $categories = DB::table(config('tables.BUSINESS_CATEGORY').' as pc')
				->where('pc.category_type', 1)
                ->join(config('tables.BUSINESS_CATEGORY_TREE').' as sct', 'sct.bcategory_id', '=', 'pc.bcategory_id')
				->join(config('tables.BUSINESS_CATEGORY_TREE').' as bct', function($cc)
                {
						$cc->on('bct.cat_lftnode', '>', 'sct.cat_lftnode')
						->on('bct.cat_rgtnode', '<', 'sct.cat_rgtnode')
						->on('bct.root_bcategory_id', '=', 'sct.root_bcategory_id');
						//->where('cc.parent_bcategory_id', '=', 1485);
                })
				->join(config('tables.BUSINESS_CATEGORY_LANG').' as bcl', function($bcl)
                {
                    $bcl->on('bcl.bcategory_id', '=', 'bct.bcategory_id')
                    ->where('bcl.lang_id', '=', config('app.locale_id'));
                })
	            ->where('pc.is_deleted', config('constants.OFF'))
                ->where('pc.status', config('constants.ACTIVE'))
                ->where('pc.is_visible', config('constants.ON'))
                ->orderBy('pc.bcategory_id', 'ASC')
				
                ->selectRaw('pc.bcategory_id as id, bcl.bcategory_name as name, bct.parent_bcategory_id as parent_id');        
        return $categories = $categories->get();        
    } */
	
	 public function sellerAllCategories (array $wdata = [])
    {
        extract($wdata);
        $qry = DB::table(Config('tables.BUSINESS_CATEGORY').' as cat')
                ->join(Config('tables.BUSINESS_CATEGORY_LANG').' as catL', 'cat.bcategory_id', '=', 'catL.bcategory_id')
                ->join(Config('tables.BUSINESS_CATEGORY_TREE').' as catT', 'cat.bcategory_id', '=', 'catT.bcategory_id')
                ->where('catL.lang_id', config('constants.ACTIVE'))
                ->where('cat.is_visible', config('constants.ACTIVE'))
                ->where('cat.category_type', config('constants.ACTIVE'))
                ->where('cat.status', config('constants.ACTIVE'))
                ->where('cat.is_deleted', 0);
        $qry->selectRaw('catL.bcategory_name as name,cat.bcategory_id as id,catT.parent_bcategory_id as parent_id');
        $qry->orderBy('catL.bcategory_name', 'ASC');
        $result = $qry->get();
        if (!empty($result))
        {
            return $result;
        }
        return NULL;
    }
	
	public function sellerCategories_bk (array $wdata = [])
    {
        extract($wdata);
        $qry = DB::table(config('tables.PRODUCT_CATEGORIES').' as cat')
                //->join(config('tables.BUSINESS_CATEGORY_LANG').' as catL', 'cat.bcategory_id', '=', 'catL.bcategory_id')
                ->join(config('tables.PRODUCT_CATEGORIES_PARENTS').' as cp', 'cp.category_id', '=', 'cat.category_id')
                //->where('catL.lang_id', config('app.locale_id'))
                //->where('cat.category_type', config('constants.BCATEGORY_TYPE.IN_STORE'))
                //->where('cat.is_visible', config('constants.ON'))
                ->where('cat.status', config('constants.ON'))
                ->where('cat.is_deleted', config('constants.OFF'));
        if (isset($excbcat_id) && !empty($excbcat_id))
        {
            $qry->where('cat.bcategory_id', '!=', $excbcat_id);
        }
        if (isset($cat_id) && !empty($cat_id))
        {
            $qry->where('cp.parent_category_id', '=', $cat_id);
        }
        if (isset($excpbcat_id) && !empty($excpbcat_id))
        {
            $qry->where('cp.parent_category_id', '=', $excpbcat_id);
        }
        if (!empty($pbcat_id))
        {
            $qry->where('cp.parent_category_id', '=', $pbcat_id);
        }
        /* else
          {
          $qry->where(function ($query)
          {
          $query->where('catT.parent_bcategory_id', 0);
          $query->orWhereNull('catT.parent_bcategory_id');
          });
          } */
        $qry->selectRaw('cat.category as name, cat.category_id as id, cp.parent_category_id as parent_id');
        $qry->orderBy('cat.category', 'ASC');
        $result = $qry->get();
        if (!empty($result))
        {
            return $result;
        }
        return NULL;
    }
  public function get_currency_exchange($from_currency_id,$to_currency_id){

		$result = '';
		$result = DB::table(Config('tables.CURRENCY_EXCHANGE_SETTINGS'))
							->select('rate')
							->where(array('from_currency_id'=>$from_currency_id,'to_currency_id'=>$to_currency_id))
							->first();

		if(!empty($result) && count($result) > 0)
		{              
		  return $result;
		  
		}
		return false;
	}
  public function checkMerchantCreditLimit ($arr = array())
    {
        $store_id = null;
        $system_received_amount = 0;
        extract($arr);
        if (!$is_premium || $has_credit_limit)
        {
            $details = DB::table($this->config->get('tbl.MERCHANT_PROFIT_SHARING').' as p')
                    ->where(function($sqry) use($store_id)
                    {
                        $sqry->whereNull('p.store_id');
                        if (!empty($store_id))
                        {
                            $sqry->orWhere('p.store_id', '=', $store_id);
                        }
                    })
                    ->where('p.status', $this->config->get('constants.ON'))
                    ->where('p.is_deleted', $this->config->get('constants.OFF'))
                    ->where('p.supplier_id', $supplier_id)
                    ->orderBy('p.store_id', 'desc')
                    ->selectRaw('p.profit_sharing,p.cashback_on_pay,p.cashback_on_redeem,p.cashback_on_shop_and_earn')
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
                $credit_limit = json_decode($this->getSetting('merchant_credit_limit_by_currency'), true);
                $credit_limit = array_key_exists($currency_id, $credit_limit) ? $credit_limit[$currency_id] : 0;
                return $settlement_amount >= 0 || DB::table($this->config->get('tbl.MERCHANT_MST').' as m')
                                ->join($this->config->get('tbl.ACCOUNT_BALANCE').' as ab', function($ab) use($currency_id)
                                {
                                    $ab->on('ab.account_id', '=', 'm.account_id')
                                    ->where('ab.currency_id', '=', $currency_id)
                                    ->where('ab.wallet_id', '=', $this->config->get('constants.WALLET.xpc'));
                                })
                                ->where('m.supplier_id', $supplier_id)
                                ->where(DB::raw('ab.current_balance+'.$settlement_amount), '>=', $credit_limit)
                                ->exists();
            }
        }
        return true;
    }

  public function getCategoriesList ($category_type = null)
    {
        if (is_null($category_type))
        {
            $category_type = config('constants.BCATEGORY_TYPE.DEAL');
        }
        return DB::table(config('tables.BUSINESS_CATEGORY').' as bc')
                        ->join(config('tables.BUSINESS_CATEGORY_LANG').' as bcl', function($subquery)
                        {
                            $subquery->on('bcl.bcategory_id', '=', 'bc.bcategory_id')
                            ->where('bcl.lang_id', '=', config('app.locale_id'));
                        })
                        ->join(config('tables.BUSINESS_CATEGORY_TREE').' as bct', function($subquery)
                        {
                            $subquery->on('bct.bcategory_id', '=', 'bc.bcategory_id');
                        })
                        ->where('bc.is_visible', config('constants.ON'))
                        ->where('bc.category_type', $category_type)
                        ->where('bc.status', config('constants.ACTIVE'))
                        ->where('bc.is_deleted', config('constants.NOT_DELETED'))
                
                        ->selectRaw('bcl.bcategory_name as category, bc.bcategory_id as id, bc.slug, if(bct.cat_lftnode = bct.cat_rgtnode - 1,0,1) as has_sub')
                        ->get();
     }
 public function OnlineaffilateNetwrks ()
    {
     return DB::table(config('tables.SUPPLIER_MST'))
                        ->where('status', config('constants.ON'))
                        ->where('is_online', config('constants.ON'))
                        ->where('is_deleted', config('constants.OFF'))
                        ->selectRaw('supplier_id,company_name')
						->get();
    }
 public function get_countries ($country_id = '')
    {
  
        $res = DB::table(Config::get('tables.LOCATION_COUNTRY'))
                ->where('status', Config::get('constants.ACTIVE'))
                ->where('operate', Config::get('constants.ACTIVE'));
        if (!empty($country_id))
        {
            $res->where('country_id', $country_id);
        }
        $query = $res->select('country', 'country_id', 'iso2', 'phonecode','mobile_validation')
                ->orderBy('country', 'asc')
                ->get();
        return $query;
    }
	
	public function getIpCountry ()
    {
	
		$ip =	Request::ip();
		if($ip == '::1'){
			$ip = '103.231.216.102';
		}
        try 
        {
            $ipInfo = json_decode(file_get_contents('http://ipinfo.io/'.$ip.'/json'));
	        return $ipInfo->country;
        }
        catch (Exception $e)
        {
            return NULL;
        }
    }
	
	public function get_access_leveles($account_type = ''){
		$qry = DB::table(Config('tables.ACCESS_LEVEL_LOOKUP'))
				->where('is_deleted',0);
				if($account_type !=''){
					$qry = $qry->where('account_type_id',$account_type);
				}
			$qry->select('access_id','access_name');
			$result = $qry->get();
			return $result;
	}
	
	/* public function logoutAllDevices ($account_id,$account_log_id = null)
    {
        $qry = DB::table($this->config->get('tables.ACCOUNT_LOG'))
                ->where('account_id', $account_id)
                ->where('is_deleted', $this->config->get('constants.OFF'));
		if(!empty($account_log_id) && $account_log_id != null)
		{
		    $qry->where('account_log_id', '!=', $account_log_id);
		}
        $tokens = $qry->lists('token', 'account_log_id');
        foreach ($tokens as $id=> $token)
        {
            $token = explode('-', $token);
            if (!empty($token[0]) && file_exists($this->config->get('session.files').'/'.$token[0]) && unlink($this->config->get('session.files').'/'.$token[0]))
            {
                $this->logoutAccountLog($id);
                //$this->logoutAccountLog($account_id);
            }
        }
        return true;
    } */
	
	/* LogOut All Devices */
	public function logoutAllDevices ($account_id,$account_log_id = null)
    {  	   
        $qry = DB::table(Config::get('tables.ACCOUNT_LOG').' as al')
		        ->join(Config::get('tables.ACCOUNT_MST').' as am', 'am.account_id', '=', 'al.account_id')
		        ->join(Config::get('tables.ACCOUNT_TYPES').' as at', 'at.id', '=', 'am.account_type_id')
                ->where('al.account_id', $account_id)
                ->where('al.is_deleted', Config::get('constants.OFF'));
		if(!empty($account_log_id) && $account_log_id != null)
		{
		    $qry->where('al.account_log_id', '!=', $account_log_id);
		}		
        $tokens = $qry->selectRaw('al.token,al.account_log_id,at.account_type_key')->get();
		//$paths=[];
        foreach ($tokens as $token)
        {
            $token->token = explode('-', $token->token);
			$path= Config::get('session.files').'/'.$token->token[0];
            if (!empty($token->token[0]) && file_exists($path))
            {
				$data = unserialize(file_get_contents($path));
				unset($data[$token->account_type_key]);
			/* 	$paths['account_type_key']=$token->account_type_key;
				$paths['path']=$path;
				$paths['data']=$data; */
				file_put_contents($path, serialize($data));				
                $this->logoutAccountLog($token->account_log_id);
            }
        }       
        //return $paths;
		return true;
    }
	
	public function logoutAccountLog ($id, $isCurrent = false)
    {
        if (DB::table(Config::get('tables.ACCOUNT_LOG'))
                        ->where('account_log_id', $id)
                        ->update(['is_deleted'=>Config::get('constants.ON')]))
        {
		    if ($isCurrent)
            {
				session()->forget(Config::get('app.role'));
				if (!Config::get('app.is_api'))
				{
					Cookie::queue(Cookie::forget(Config::get('app.session_key')));
				}
            }
            return true;
        }
        return false;
    }

}
