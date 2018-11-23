<?php

namespace App\Models\Admin;
use DB;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\ImageLib;
use Config;
use URL;

class AdminCatalog extends Model
{

    public function get_categories_list ($data = array(), $count = false)
    {
        $categories = DB::table(Config::get('tables.PRODUCT_CATEGORIES').' as pc')
                ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as cp', 'cp.category_id', '=', 'pc.category_id')
                ->where('pc.is_deleted', Config::get('constants.OFF'));
        if (isset($data['search_text']) && !empty($data['search_text']))
        {
            $categories->where('pc.category', 'like', '%'.$data['search_text'].'%');
            $categories->orwhere('pc.category_code', 'like', '%'.$data['search_text'].'%');
        }
        if (!empty($data['filterTerms']))
        {
            $data['filterTerms'] = !is_array($data['filterTerms']) ? array($data['filterTerms']) : $data['filterTerms'];
            if (in_array('c_code', $data['filterTerms']) && !empty($data['search_text']))
            {
                $categories->where('pc.category_code', 'like', '%'.$data['search_text'].'%');
            }
            if (in_array('category', $data['filterTerms']) && !empty($data['search_text']))
            {
                $categories->where('pc.category', 'like', '%'.$data['search_text'].'%');
            }
        }

        if (!empty($data['parent_category_id']) && isset($data['parent_category_id']))
        {
            $categories->where('cp.parent_category_id', $data['parent_category_id']);
        }
        if (isset($data['start']) && isset($data['length']))
        {
            $categories->skip($data['start'])->take($data['length']);
        }
        if (isset($data['orderby']))
        {
            $categories->orderby($data['orderby'], $data['order']);
        }
        if ($count)
        {
            return $categories->count();
        }
        else
        {
            $categories = $categories->leftjoin(Config::get('tables.PRODUCT_CATEGORIES').' as ppc', 'ppc.category_id', '=', 'cp.parent_category_id')
                    ->selectRaw('pc.category_code,pc.category, pc.category_id,pc.replacement_service_policy_id,pc.created_on, pc.status,cp.parent_category_id,ppc.category as parent_category, pc.updated_by')
                    ->get();
            if (!empty($categories))
            {
                array_walk($categories, function(&$categoty)
                {
                    $categoty->created_on = date('d-M-Y H:i:s', strtotime($categoty->created_on));
                });
            }
            return $categories;
        }
    }

    /*
     * Function Name        : save_category
     * Params               : admin_id,category_id(optional),category()
     * Return Value         : TRUE OR FALSE
     */

    public function save_category ($arr = array())
    {
        DB::Transaction(function() use($arr)
        {
            extract($arr);
            $category['url_str'] = $this->slug($category['category']);
            if (isset($category_id) && !empty($category_id))
            {
                $category['updated_on'] = date('Y-m-d H:i:s');
                if (DB::table(Config::get('tables.PRODUCT_CATEGORIES'))
                                ->where('category_id', $category_id)
                                ->update($category))
                {
                    $category_parent['category_id'] = $category_id;
                    return $this->save_parent_category(['category_parent'=>$category_parent, 'admin_id'=>$admin_id]);
                }
            }
            else
            {
                $category_id = DB::table(Config::get('tables.PRODUCT_CATEGORIES'))
                        ->where(function($c) use($category)
                        {
                            $c->where('category', $category['category'])
                            ->orWhere('url_str', $category['url_str']);
                        })
                        ->pluck('category_id');
                if (!$category_id)
                {
                    $category['created_on'] = date('Y-m-d H:i:s');
                    $existing_cahrs = DB::table(Config::get('tables.PRODUCT_CATEGORIES'))
                            ->where('is_deleted', Config::get('constants.OFF'))
                            ->lists('category_code');
                    do
                    {
                        $newkey = $this->rand_str(3);
                    }
                    while (in_array($newkey, $existing_cahrs));
                    $category['category_code'] = $newkey;
                    $category_parent['category_id'] = DB::table(Config::get('tables.PRODUCT_CATEGORIES'))
                            ->insertGetId($category);
                    if ($category_parent['category_id'])
                    {
                        return $this->save_parent_category(['category_parent'=>$category_parent, 'admin_id'=>$admin_id]);
                    }
                }
                else
                {
                    $category_parent['category_id'] = $category_id;
                    return $this->save_parent_category(['category_parent'=>$category_parent, 'admin_id'=>$admin_id]);
                }
            }
        });
        //return true;
        return false;
    }

    public function save_parent_category ($arr)
    {
        extract($arr);
        $parents = DB::table(Config::get('tables.PRODUCT_CATEGORIES').' as c')
                ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as cp', 'cp.category_id', '=', 'c.category_id')
                ->where('c.category_id', $category_parent['parent_category_id'])
                ->where('c.is_deleted', Config::get('constants.OFF'))
                ->where('c.status', Config::get('constants.ACTIVE'))
                ->pluck('cp.parents');
        $category_parent['parents'] = (!empty($parents)) ? $parents.','.$category_parent['parent_category_id'] : $category_parent['parent_category_id'];
        if (DB::table(Config::get('tables.PRODUCT_CATEGORIES_PARENTS'))
                        ->where('category_id', $category_parent['category_id'])
                        ->where('parent_category_id', $category_parent['parent_category_id'])
                        ->exists())
        {
            return DB::table(Config::get('tables.PRODUCT_CATEGORIES_PARENTS'))
                            ->where('category_id', $category_parent['category_id'])
                            ->where('parent_category_id', $category_parent['parent_category_id'])
                            ->update($category_parent);
        }
        else
        {
            $node_val = $this->adding_categoryNode($category_parent['parent_category_id']);
            $category_parent['cat_lftnode'] = $node_val['cat_lftnode'];
            $category_parent['cat_rgtnode'] = $node_val['cat_rgtnode'];
            return DB::table(Config::get('tables.PRODUCT_CATEGORIES_PARENTS'))
                            ->insertGetId($category_parent);
        }
    }

    /*     * ******* Handling  Nester set category placing ****** */

    public function adding_categoryNode ($parent_category_id = 1)
    {
        $increament_val = 2;
        $catinfo = $this->category_details(array('category_id'=>$parent_category_id));
        if (!empty($catinfo))
        {
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
            DB::table(Config::get('tables.PRODUCT_CATEGORIES_PARENTS'))
                    ->where('cat_rgtnode', '>=', $updateFrom)
                    ->where('is_deleted', '=', Config::get('constants.OFF'))
                    ->increment('cat_rgtnode', $increament_val);
            DB::table(Config::get('tables.PRODUCT_CATEGORIES_PARENTS'))
                    ->where('cat_lftnode', '>', $updateFrom)
                    ->where('is_deleted', '=', Config::get('constants.OFF'))
                    ->increment('cat_lftnode', $increament_val);
            $newPos = ['cat_lftnode'=>$cat_lftnode, 'cat_rgtnode'=>$cat_rgtnode];
            return $newPos;
        }
    }

    public function deleting_categoryNode ($category_id, $delete_mode = 1)
    {
        $child_exist = 0;
        $newPos = false;
        $catinfo = $this->category_details(array(
            'category_id'=>$category_id));
        if (!empty($catinfo))
        {
            $updateFrom = $catinfo->cat_rgtnode;
            switch ($delete_mode)
            {
                case 1: /* full tree delete */
                    DB::table(Config::get('tables.PRODUCT_CATEGORIES_PARENTS'))
                            ->whereBetween('cat_lftnode', [$catinfo->cat_lftnode,
                                $catinfo->cat_rgtnode])
                            ->where('is_deleted', '=', Config::get('constants.OFF'))
                            ->update(array(
                                'is_deleted',
                                Config::get('constants.ON')));
                    $decreament_val = $catinfo->cat_rgtnode - $catinfo->cat_lftnode + 1;
                    break;
                case 2: /* shift_treeup_delete */
                    DB::table(Config::get('tables.PRODUCT_CATEGORIES'))
                            ->where('category_id', '=', $category_id)
                            ->update(array(
                                'is_deleted',
                                Config::get('constants.ON')));
                    DB::table(Config::get('tables.PRODUCT_CATEGORIES').' as c')
                            ->join(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as cp', 'cp.category_id', '=', 'c.category_id')
                            ->where('cp.parent_category_id', '=', $catinfo->category_id)
                            ->where('c.is_deleted', '=', Config::get('constants.OFF'))
                            ->update(array('cp.parent_category_id',
                                $catinfo->parent_category_id));
                    $decreament_val = 1;
                    DB::table(Config::get('tables.PRODUCT_CATEGORIES').' as c')
                            ->join(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as cp', 'cp.category_id', '=', 'c.category_id')
                            ->whereBetween('cp.cat_lftnode', [$catinfo->cat_lftnode,
                                $catinfo->cat_rgtnode])
                            ->where('c.is_deleted', '=', Config::get('constants.OFF'))
                            ->update(['cp.cat_lftnode'=>DB::raw('cp.cat_lftnode+'.$decreament_val),
                                'cp.cat_rgtnode'=>DB::raw('cp.cat_rgtnode+'.$decreament_val)]);
                    $decreament_val = 2;
                    break;
            }

            DB::table(Config::get('tables.PRODUCT_CATEGORIES').' as c')
                    ->join(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as cp', 'cp.category_id', '=', 'c.category_id')
                    ->where('cp.cat_rgtnode', '>', $updateFrom)
                    ->where('c.is_deleted', '=', Config::get('constants.OFF'))
                    ->decrement('cp.cat_rgtnode', $decreament_val);

            DB::table(Config::get('tables.PRODUCT_CATEGORIES').' as c')
                    ->join(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as cp', 'cp.category_id', '=', 'c.category_id')
                    ->where('cp.cat_lftnode', '>', $updateFrom)
                    ->where('c.is_deleted', '=', Config::get('constants.OFF'))
                    ->decrement('cp.cat_lftnode', $decreament_val);
        }
    }

    public function get_full_CategoryTree ($parent_category_id = NULL)
    {
        $qry = DB::table(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as np')
                ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as pp', function($pp)
                {
                    $pp->on(DB::raw('np.cat_lftnode BETWEEN pp.cat_lftnode AND pp.cat_rgtnode'), DB::raw(''), DB::raw(''));
                })
                ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES').' as node', 'node.category_id', '=', 'np.category_id')
                ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES').' as parent', 'parent.category_id', '=', 'pp.category_id')
                ->orderBy('np.cat_lftnode', 'ASC');
        $qry->selectRaw('node.category_id,node.category,np.parent_category_id,node.url_str,node.category_code');
        if (!empty($parent_category_id))
        {
            $qry->where('pp.category_id', $parent_category_id);
        }
        else
        {
            $qry->whereNull('pp.category_id');
        }
        $result = $qry->get();
        if (!empty($result))
        {
            return $result;
        }
        return NULL;
    }

    public function get_category_childs ($parent_category_id = NULL)
    {
        $qry = DB::table(DB::Raw(Config::get('tables.PRODUCT_CATEGORIES').' as node'))
                ->join(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as np', 'np.category_id', '=', 'node.category_id');
        if (empty($parent_category_id))
        {
            $qry->whereNull('np.parent_category_id');
        }
        else
        {
            $qry->where('np.parent_category_id', $parent_category_id);
        }
        $qry->selectRaw('node.category_id,node.category,np.parent_category_id,node.url_str,node.category_code');
        $result = $qry->get();
        if (!empty($result))
        {
            return $result;
        }
        return NULL;
    }

    public function getfull_child_cattree ($category_id)
    {
        $sql = 'select node.category_id,node.category,node.parent_category_id,node.url_str,node.category_code from '.Config::get('tables.PRODUCT_CATEGORIES').' as node,'.Config::get('tables.PRODUCT_CATEGORIES').' as parent where parent.category_id='.$category_id.' AND `node`.`cat_lftnode`>parent.cat_lftnode and `node`.`cat_lftnode` < parent.cat_rgtnode order by `node`.`cat_lftnode` asc';
        $result = DB::selectRaw($sql);
//            $qry = DB::table(DB::RAW(Config::get('tables.PRODUCT_CATEGORIES').' as node'));
//            $qry->join(Config::get('tables.PRODUCT_CATEGORIES').' as parent','parent.parent_category_id','=','node.parent_category_id','left outer')	;
//            $qry->where('node.cat_lftnode','>','parent.cat_lftnode');
//            $qry->where('node.cat_lftnode','<','parent.cat_rgtnode');
//		$qry->selectRaw('node.category_ids,node.category,node.parent_category_id,node.url_str,node.category_code');
//                $qry ->orderBy('node.cat_lftnode','ASC');
//		if($category_id>0){
//			$qry->where('parent.category_id',$category_id);
//		}
//		$result = $qry->get();
//                print_r($result);exit;
        if (!empty($result))
        {
            return $result;
        }
        return NULL;
    }

    public function shifting_CategoryTree ($category_id = 0, $new_parent_category_id = 0)
    {
        $catinfo = $this->category_details(array(
            'category_id'=>$category_id));
        $new_parent_catinfo = $this->category_details(array(
            'category_id'=>$new_parent_category_id));
        if (!empty($catinfo) && !empty($new_parent_catinfo))
        {

            $new_parent_lft = $new_parent_catinfo->cat_lftnode;
            $new_parent_rgt = $new_parent_catinfo->cat_rgtnode;

            $origin_lft = $catinfo->cat_lftnode;
            $origin_rgt = $catinfo->cat_rgtnode;

            if ($new_parent_rgt < $origin_lft)
            {

                $sdata['cat_lftnode'] = DB::Raw('cat_lftnode + CASE WHEN cat_lftnode BETWEEN '.$origin_lft.' AND '.$origin_rgt.'  THEN '.$new_parent_rgt.' - '.$origin_lft.' WHEN cat_lftnode BETWEEN '.$new_parent_rgt.' AND '.($origin_lft - 1).'  THEN '.$origin_rgt.' - '.($origin_lft + 1).' ELSE 0 END');

                $sdata['cat_rgtnode'] = DB::Raw('cat_rgtnode + CASE WHEN cat_rgtnode BETWEEN '.$origin_lft.' AND '.$origin_rgt.'  THEN '.$new_parent_rgt.' - '.$origin_lft.' WHEN cat_rgtnode BETWEEN '.$new_parent_rgt.' AND '.($origin_lft - 1).'  THEN '.$origin_rgt.' - '.($origin_lft + 1).' ELSE 0 END');

                DB::table(Config::get('tables.PRODUCT_CATEGORIES'))
                        ->whereRaw('(cat_lftnode BETWEEN \''.$new_parent_rgt.'\' AND \''.$origin_rgt.'\' OR cat_rgtnode BETWEEN \''.$new_parent_rgt.'\' AND \''.$origin_rgt.'\')')
                        ->update($sdata);
            }
            else if ($new_parent_rgt > $origin_rgt)
            {
                $sdata['cat_lftnode'] = DB::Raw('cat_lftnode + CASE WHEN cat_lftnode BETWEEN '.$origin_lft.' AND '.$origin_rgt.'  THEN '.$new_parent_rgt.' - '.($origin_lft - 1).' WHEN cat_lftnode BETWEEN '.($origin_rgt + 1).' AND '.($new_parent_rgt - 1).'  THEN '.$origin_lft.' - '.($origin_rgt - 1).' ELSE 0 END');
                $sdata['cat_rgtnode'] = DB::Raw('cat_rgtnode + CASE WHEN cat_rgtnode BETWEEN '.$origin_lft.' AND '.$origin_rgt.'  THEN '.$new_parent_rgt.' - '.($origin_lft - 1).' WHEN cat_rgtnode BETWEEN '.($origin_rgt + 1).' AND '.($new_parent_rgt - 1).'  THEN '.$origin_lft.' - '.($origin_rgt - 1).' ELSE 0 END');
                DB::table(Config::get('tables.PRODUCT_CATEGORIES'))
                        ->whereRaw('(cat_lftnode BETWEEN \''.$origin_lft.'\' AND \''.$new_parent_rgt.'\' OR cat_rgtnode BETWEEN \''.$origin_lft.'\' AND \''.$new_parent_rgt.'\')')
                        ->update($sdata);
            }
        }
    }

    /*     * ******* Handling  Nester set category placing ****** */

    public function rand_str ($length = 32, $chars = 'abcdefghijklmnopqrstuvwxyz1234567890')
    {
        // Length of character list
        $chars_length = (strlen($chars) - 1);

        // Start our string
        $string = $chars{rand(0, $chars_length)};

        // Generate random string
        for ($i = 1; $i < $length; $i = strlen($string))
        {
            // Grab a random character from our list
            $r = $chars{rand(0, $chars_length)};

            // Make sure the same two characters don't appear next to each other
            if ($r != $string{$i - 1})
                $string .= $r;
        }

        // Return the string
        return $string;
    }

    public function slug ($text)
    {
        //replace non letter or digits by (_)
        $text = preg_replace('/\W|_/', '-', $text);
        // Clean up extra dashes
        $text = preg_replace('/-+/', '-', trim($text, '-')); // Clean up extra dashes
        // lowercase
        $text = strtolower($text);
        if (empty($text))
        {
            return false;
        }
        return $text;
    }

    /*
     * Function Name        : category_details
     * Params               : category_id
     * Return Value         : category details
     */

    public function category_details ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.PRODUCT_CATEGORIES').' as pc')
                        ->leftjoin(Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' as pcp', 'pcp.category_id', '=', 'pc.category_id')
                        ->where('pc.category_id', $category_id)
                        ->where('pc.is_deleted', Config::get('constants.OFF'))
                        ->selectRaw('pc.*,pcp.parent_category_id,pcp.parents,pcp.cat_lftnode,pcp.cat_rgtnode,(select count(category_id) from '.Config::get('tables.PRODUCT_CATEGORIES_PARENTS').' where parent_category_id=pc.category_id) as childCounts')
                        ->first();
    }

    /*
     * Function Name        : change_category_status
     * Params               : category_id,status
     * Return Value         : TRUE OR FALSE
     */

    public function change_category_status ($data)
    {
        extract($data);
        $update['status'] = $status;
        $query = DB::table(Config::get('tables.PRODUCT_CATEGORIES'))
                ->where('category_id', $category_id);
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

    /*
     * Function Name        : delete_category
     * Params               : category_id
     * Return Value         : TRUE OR FALSE
     */

    public function delete_category ($data)
    {
        extract($data);
        $update['is_deleted'] = Config::get('constants.ON');
        return DB::table(Config::get('tables.PRODUCT_CATEGORIES'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->where('category_id', $category_id)
                        ->update($update);
    }

    /*
     * Function Name        : get_property_list
     * Params               : orderby(optional),order(optional),count(optional)
     * Return Value         : property list
     */

    public function get_property_list ($arr = array(), $count = false)
    {
        extract($arr);
        $properties = DB::table(Config::get('tables.PRODUCT_PROPERTY_KEYS').' as p')
                ->leftjoin(Config::get('tables.PRODUCT_PROPERTY_KEYS').' as pp', 'pp.property_id', '=', 'p.parent_property_id');
        if (!empty($search_term) && isset($search_term))
        {
            $properties->whereRaw('(p.property like \'%'.$search_term.'%\')');
        }
        if (isset($start) && isset($length))
        {
            $properties->skip($start)->take($length);
        }
        if (isset($orderby))
        {
            $properties->orderby($orderby, $order);
        }
        if ($count)
        {
            return $properties->count();
        }
        else
        {
            return $properties->selectRaw('p.property_id,p.parent_property_id,p.property_type,p.value_type,p.property,pp.property as parent_property,p.unit_id')->get();
        }
    }

    /*
     * Function Name        : get_property_values_list
     * Params               : property_id(optional),orderby(optional),order(optional),count(optional)
     * Return Value         : property values list
     */

    public function get_property_values_list ($arr = array(), $count = false)
    {
        extract($arr);
        $property_values = DB::table(Config::get('tables.PRODUCT_PROPERTY_KEY_VALUES').' as pv')
                ->leftjoin(Config::get('tables.UNITS').' as u', 'u.unit_id', '=', 'pv.unit_id')
                ->where('pv.is_deleted', Config::get('constants.OFF'));
        if (isset($property_id) && !empty($property_id))
        {
            $property_values->where('pv.property_id', $property_id);
        }
        if (isset($search_term) && !empty($search_term))
        {
            $property_values->whereRaw('(pv.key_value like \'%'.$search_term.'%\' OR u.unit like \'%'.$search_term.'%\' )');
        }
        if (isset($start) && isset($length))
        {
            $property_values->skip($start)->take($length);
        }
        if (isset($orderby))
        {
            $property_values->orderby($orderby, $order);
        }

        if ($count)
        {
            return $property_values->count();
        }
        else
        {
            return $property_values->selectRaw('pv.value_id,pv.key_value,pv.unit_id,u.unit')->get();
        }
    }

    /*
     * Function Name        : save_property
     * Params               : property_id(optional),property()
     * Return Value         : TRUE OR FALSE
     */

    public function save_property ($arr = array())
    {
        extract($arr);
        if (isset($property_id) && !empty($property_id))
        {
            if (DB::table(Config::get('tables.PRODUCT_PROPERTY_KEYS'))
                            ->where(array_except($property, array(
                                'value_type',
                                'property_type')))
                            ->where('property_id', '!=', $property_id)
                            ->count() <= 0)
            {
                return DB::table(Config::get('tables.PRODUCT_PROPERTY_KEYS'))
                                ->where('property_id', $property_id)
                                ->update($property);
            }
        }
        else
        {
            if (DB::table(Config::get('tables.PRODUCT_PROPERTY_KEYS'))
                            ->where(array_except($property, array(
                                'value_type',
                                'property_type')))
                            ->count() <= 0)
            {
                return DB::table(Config::get('tables.PRODUCT_PROPERTY_KEYS'))
                                ->insertGetId($property);
            }
        }
        return false;
    }

    public function delete_property ($property_id)
    {
        return DB::table(Config::get('tables.PRODUCT_PROPERTY_KEYS'))
                        ->where('property_id', $property_id)
                        ->update(['is_deleted'=>Config::get('constants.ON')]);
    }

    public function delete_property_value ($value_id)
    {
        return DB::table(Config::get('tables.PRODUCT_PROPERTY_KEY_VALUES'))
                        ->where('value_id', $value_id)
                        ->update(['is_deleted'=>Config::get('constants.ON')]);
    }

    /*
     * Function Name        : save_property_values
     * Params               : property_id,property_values()
     * Return Value         : TRUE OR FALSE
     */

    public function save_property_values ($arr = array())
    {
        extract($arr);
        $status = false;
        if (!empty($property_id) && isset($property_values) && !empty($property_values))
        {
            foreach ($property_values as $value)
            {
                $value['values']['property_id'] = $property_id;
                if (isset($value['value_id']) && !empty($value['value_id']))
                {
                    $status = DB::table(Config::get('tables.PRODUCT_PROPERTY_KEY_VALUES'))
                            ->where('value_id', $value['value_id'])
                            ->update($value['values']);
                }
                else
                {
                    if (DB::table(Config::get('tables.PRODUCT_PROPERTY_KEY_VALUES'))
                                    ->where($value['values'])
                                    ->count() <= 0)
                    {
                        $status = DB::table(Config::get('tables.PRODUCT_PROPERTY_KEY_VALUES'))
                                ->insertGetId($value['values']);
                    }
                }
            }
        }
        return $status;
    }

    /*
     * Function Name        : discount_types_list
     * Return Value         : discount types
     */

    public function discount_types_list ()
    {
        return DB::table(Config::get('tables.DISCOUNT_TYPE_LOOKUPS'))
                        ->where('status', Config::get('constants.ACTIVE'))
                        ->orderby('discount_type', 'ASC')
                        ->select('discount_type_id', 'discount_type')
                        ->get();
    }

    /*
     * Function Name        : get_discounts_list
     * Params               : search_term,start(optional),length(optional),orderby(optional),order(optional),count(optional)
     * Return Value         : discounts list
     */

    public function get_discounts_list ($arr = array(), $count = false)
    {
        extract($arr);
        $discounts = DB::table(Config::get('tables.DISCOUNTS').' as d')
                ->leftjoin(Config::get('tables.DISCOUNT_TYPE_LOOKUPS').' as dtl', 'dtl.discount_type_id', '=', 'd.discount_type_id')
                ->leftjoin(Config::get('tables.ACCOUNT_STATUS_LOOKUPS').' as sl', 'sl.status_id', '=', 'd.status')
                ->leftjoin(Config::get('tables.LOCATION_COUNTRY').' as c', 'c.country_id', '=', 'd.country_id')
                ->where('d.is_deleted', Config::get('constants.OFF'));
        if (isset($search_term) && !empty($search_term))
        {
            $discounts->whereRaw('(d.discount like \'%'.$search_term.'%\')');
        }
        if (isset($start) && isset($length))
        {
            $discounts->skip($start)->take($length);
        }
        if (isset($orderby))
        {
            $discounts->orderby($orderby, $order);
        }
        if ($count)
        {
            return $discounts->count();
        }
        else
        {
            $discounts = $discounts->selectRaw('d.*,c.country,sl.status_name,dtl.discount_type')
                    ->get();
            array_walk($discounts, function(&$discount)
            {
                $discount->created_on = date('d-M-Y H:i:s', strtotime($discount->created_on));
                $discount->start_date = date('d-M-Y H:i:s', strtotime($discount->start_date));
                $discount->end_date = date('d-M-Y H:i:s', strtotime($discount->end_date));
                $discount->posts = DB::table(Config::get('tables.DISCOUNT_POSTS').' as dp')
                        ->where('dp.discount_id', $discount->discount_id)
                        ->where('dp.is_deleted', Config::get('constants.OFF'))
                        ->selectRaw('dp.dp_id,dp.brand_ids,dp.category_ids,dp.supplier_ids,dp.product_ids,dp.product_cmb_ids,dp.discount_value_type,dp.is_qty_based')
                        ->get();
                array_walk($discount->posts, function(&$post)
                {
                    $post->values = DB::table(Config::get('tables.DISCOUNT_VALUE').' as dv')
                            ->where('dv.dp_id', $post->dp_id)
                            ->where('dv.is_deleted', Config::get('constants.OFF'))
                            ->selectRaw('dv.dv_id,dv.currency_id,dv.discount_value,dv.min_qty,dv.max_qty')
                            ->get();
                });
            });
            return $discounts;
        }
    }

    public function getDiscountDeatails ()
    {
        $discount = DB::table(Config::get('tables.DISCOUNTS').' as d')
                ->leftjoin(Config::get('tables.DISCOUNT_TYPE_LOOKUPS').' as dtl', 'dtl.discount_type_id', '=', 'd.discount_type_id')
                ->leftjoin(Config::get('tables.ACCOUNT_STATUS_LOOKUPS').' as sl', 'sl.status_id', '=', 'd.status')
                ->where('d.is_deleted', Config::get('constants.OFF'))
                ->where('d.discount_id', $discount_id)
                ->first();
        if (!empty($discount))
        {
            $discount->posts = DB::table(Config::get('tables.DISCOUNT_POSTS').' as dp')
                    ->where('dp.discount_id', $discount_id)
                    ->where('dp.is_deleted', Config::get('constants.OFF'))
                    ->selectRaw('dp.dp_id,dp.brand_ids,dp.category_ids,dp.supplier_ids,dp.product_ids,dp.product_cmb_ids,dp.discount_value_type,dp.is_qty_based')
                    ->get();
            array_walk($discount->posts, function(&$post)
            {
                $post->values = DB::table(Config::get('tables.DISCOUNT_VALUE').' as dv')
                        ->where('dv.discount_id', $discount_id)
                        ->where('dv.is_deleted', Config::get('constants.OFF'))
                        ->selectRaw('dv.dv_id,dv.currency_id,dv.discount_value,dv.min_qty,db.max_qty')
                        ->get();
            });
        }
        return $discount;
    }

    /*
     * Function Name        : save_discount
     * Params               : admin_id,discount_id(optional),discount(relative_post_id,start_date,end_date)
     * Return Value         : discounts list
     */

    public function save_discount ($arr = array())
    {
        $status = false;
        DB::transaction(function() use($arr, &$status)
        {
            extract($arr);
            $status = false;
            $discount['start_date'] = (isset($discount['start_date']) && !empty($discount['start_date'])) ? date('Y-m-d', strtotime($discount['start_date'])) : '';
            $discount['end_date'] = (isset($discount['end_date']) && !empty($discount['end_date'])) ? date('Y-m-d', strtotime($discount['end_date'])) : '';
            $discount['updated_by'] = $admin_id;
            if (isset($discount_id) && !empty($discount_id))
            {
                $status = DB::table(Config::get('tables.DISCOUNTS'))
                        ->where('discount_id', $discount_id)
                        ->update($discount);
            }
            else
            {
                $discount['created_on'] = date('Y-m-d H:i:s');
                $status = $discount_id = DB::table(Config::get('tables.DISCOUNTS'))
                        ->insertGetId($discount);
            }
            if ($discount_id)
            {
                foreach ($discount_post as $post_type)
                {
                    $post_type['discount_id'] = $discount_id;
                    $dp_id = $this->saveDiscountPost($post_type);
                    if ($dp_id)
                    {
                        foreach ($post_type['value'] as $value)
                        {
                            $value['dp_id'] = $dp_id;
                            $this->saveDiscountValue($value, $post_type['discount_value_type']);
                        }
                    }
                }
            }
        });
        return $status;
    }

    public function saveDiscountPost ($arr = array())
    {
        $brand_ids = $category_ids = $supplier_ids = $product_ids = $product_cmb_ids = [];
        extract($arr);
        $post_type = [];
        $post_type['discount_id'] = $discount_id;
        $post_type['brand_ids'] = implode(',', array_unique($brand_ids));
        $post_type['category_ids'] = implode(',', array_unique($category_ids));
        $post_type['supplier_ids'] = implode(',', array_unique($supplier_ids));
        $post_type['product_ids'] = implode(',', array_unique($product_ids));
        $post_type['product_cmb_ids'] = implode(',', array_unique($product_cmb_ids));
        $post_type = array_filter($post_type);
        $post_type['discount_value_type'] = $discount_value_type;
        $post_type['is_qty_based'] = isset($is_qty_based) ? $is_qty_based : 0;
        $dp_id_query = DB::table(Config::get('tables.DISCOUNT_POSTS'))
                ->where('discount_id', $discount_id);
        if (!empty($dp_id_query))
        {
            $dp_id_query->whereRaw('brand_ids REGEXP \'[[:<:]]('.implode('|', $brand_ids).')[[:>:]]\'');
        }
        else
        {
            $dp_id_query->whereNull('brand_ids');
        }
        if (!empty($category_ids))
        {
            $dp_id_query->whereRaw('category_ids REGEXP \'[[:<:]]('.implode('|', $category_ids).')[[:>:]]\'');
        }
        else
        {
            $dp_id_query->whereNull('category_ids');
        }
        if (!empty($supplier_ids))
        {
            $dp_id_query->whereRaw('supplier_ids REGEXP \'[[:<:]]('.implode('|', $supplier_ids).')[[:>:]]\'');
        }
        else
        {
            $dp_id_query->whereNull('supplier_ids');
        }
        if (!empty($product_ids))
        {
            $dp_id_query->whereRaw('product_ids REGEXP \'[[:<:]]('.implode('|', $product_ids).')[[:>:]]\'');
        }
        else
        {
            $dp_id_query->whereNull('product_ids');
        }
        if (!empty($product_cmb_ids))
        {
            $dp_id_query->whereRaw('product_cmb_ids REGEXP \'[[:<:]]('.implode('|', $product_cmb_ids).')[[:>:]]\'');
        }
        else
        {
            $dp_id_query->whereNull('product_cmb_ids');
        }
        $dp_id = $dp_id_query->pluck('dp_id');
        if (!empty($dp_id))
        {
            if (DB::table(Config::get('tables.DISCOUNT_POSTS'))
                            ->where('dp_id', $dp_id)
                            ->update($post_type))
            {
                return $dp_id;
            }
        }
        else
        {
            return DB::table(Config::get('tables.DISCOUNT_POSTS'))
                            ->insertGetId($post_type);
        }
    }

    public function saveDiscountValue ($arr = array(), $discount_value_type = 2)
    {
        extract($arr);
        $post_value = [];
        $post_value['dp_id'] = $dp_id;
        $post_value['discount_value'] = $discount_value;
        $post_value['min_qty'] = $min_qty;
        $post_value['max_qty'] = $max_qty;
        if (isset($dv_id) || $dv_id = DB::table(Config::get('tables.DISCOUNT_VALUE'))
                ->where($discount_value_type == Config::get('constants.DISCOUNT_VALUE_TYPE.PERCENTAGE') ? compact('dp_id', 'min_qty', 'max_qty') : compact('dp_id', 'min_qty', 'max_qty', 'currency_id'))
                ->pluck('dv_id'))
        {
            return DB::table(Config::get('tables.DISCOUNT_VALUE'))
                            ->where('dv_id', $dv_id)
                            ->update($post_value);
        }
        else
        {
            return DB::table(Config::get('tables.DISCOUNT_VALUE'))
                            ->insertGetId($post_value);
        }
    }

    public function change_discount_staus ($data)
    {
        extract($data);
        $update['status'] = $status;
        $query = DB::table(Config::get('tables.DISCOUNTS'))
                ->where('discount_id', $discount_id);
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

    public function delete_discount ($data)
    {
        extract($data);
        $update['is_deleted'] = Config::get('constants.ON');
        return DB::table(Config::get('tables.DISCOUNTS'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->where('discount_id', $discount_id)
                        ->update($update);
    }

    public function post_types_list ($img_uploadable = false)
    {
        $query = DB::table(Config::get('tables.POST_TYPE_LOOKUPS'))
                ->where('status', Config::get('constants.ACTIVE'))
                ->where('admin_discountable', Config::get('constants.ACTIVE'));
        if ($img_uploadable)
        {
            $query->where('img_uploadable', Config::get('constants.ACTIVE'));
        }
        $query->select('post_type_id', 'post_type', DB::raw('concat(\''.URL::to('admin').'/\',LOWER(REPLACE(post_type,\' \',\'_\')),\'-list-chosen\') as url'));
        return $query->get();
    }

    public function category_properties_save ($arr = array())
    {
        extract($arr);
        $property_ids = [];
        if (isset($category_id) && !empty($category_id))
        {
            foreach ($properties as $property_id=> $property)
            {
                $property['category_id'] = $category_id;
                $property_ids[] = $property_id;
                if (!($category_property_id = DB::table(Config::get('tables.PRODUCT_CATEGORY_PROPERTIES'))
                        ->where($property)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->pluck('category_property_id')))
                {
                    $category_property_id = DB::table(Config::get('tables.PRODUCT_CATEGORY_PROPERTIES'))
                            ->insertGetId($property);
                }
                else
                {
                    DB::table(Config::get('tables.PRODUCT_CATEGORY_PROPERTIES'))
                            ->where('category_property_id', $category_property_id)
                            ->update(['is_deleted'=>Config::get('constants.OFF')]);
                }
                if ($category_property_id && !empty($values[$property_id]))
                {
                    $value_ids = [];
                    foreach ($values[$property_id] as $value_id=> $value)
                    {
                        $value_ids[] = $value_id;
                        $value['category_property_id'] = $category_property_id;
                        if (!($cpv_id = DB::table(Config::get('tables.PRODUCT_CATEGORY_PROPERTY_VALUES'))
                                ->where($value)
                                ->where('is_deleted', Config::get('constants.OFF'))
                                ->pluck('cpv_id')))
                        {
                            DB::table(Config::get('tables.PRODUCT_CATEGORY_PROPERTY_VALUES'))
                                    ->insertGetId($value);
                        }
                        else
                        {
                            DB::table(Config::get('tables.PRODUCT_CATEGORY_PROPERTY_VALUES'))
                                    ->where('cpv_id', $cpv_id)
                                    ->update(['is_deleted'=>Config::get('constants.OFF')]);
                        }
                    }
                    DB::table(Config::get('tables.PRODUCT_CATEGORY_PROPERTY_VALUES'))
                            ->where('category_property_id', $category_property_id)
                            ->whereNotIn('value_id', $value_ids)
                            ->update(['is_deleted'=>Config::get('constants.ON')]);
                }
            }
            DB::table(Config::get('tables.PRODUCT_CATEGORY_PROPERTIES'))
                    ->where('category_id', $category_id)
                    ->whereNotIn('property_id', $property_ids)
                    ->update(['is_deleted'=>Config::get('constants.ON')]);
            return true;
        }
        return false;
    }

    public function product_properties_save ($arr = array())
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
                        $value_ids[] = $value_id;
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
                            ->update(['is_deleted'=>Config::get('constants.OFF')]);
                }
            }
            DB::table(Config::get('tables.PRODUCT_PROPERTY'))
                    ->where('product_id', $product_id)
                    ->whereNotIn('property_id', $property_ids)
                    ->update(['is_deleted'=>Config::get('constants.OFF')]);
            return true;
        }
        return false;
    }

    public function properties_values_checked ($category_id)
    {
        $data = array();
        $category_properties = DB::table(Config::get('tables.PRODUCT_CATEGORY_PROPERTIES'))
                ->where('category_id', $category_id)
                ->where('is_deleted', Config::get('constants.OFF'))
                ->select('category_property_id', 'property_id', 'filterable')
                ->get();
        $category_property_id = $data['properties'] = $data['filterables'] = [];
        array_walk($category_properties, function($v) use(&$category_property_id, &$data)
        {
            $category_property_id[] = $v->category_property_id;
            $data['properties'][] = $v->property_id;
            if ($v->filterable)
            {
                $data['filterables'][] = $v->property_id;
            }
        });
        $data['values'] = DB::table(Config::get('tables.PRODUCT_CATEGORY_PROPERTY_VALUES'))
                ->whereIn('category_property_id', $category_property_id)
                ->where('is_deleted', Config::get('constants.OFF'))
                ->lists('value_id');
        return $data;
    }

    public function product_properties_values_checked ($product_id)
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

    public function product_properies_for_checktree ($category_id, $parent_property_id = 0)
    {
        $properties = DB::table(Config::get('tables.PRODUCT_CATEGORY_PROPERTIES').' as cp')
                ->leftjoin(Config::get('tables.PRODUCT_PROPERTY_KEYS').' as pk', 'pk.property_id', '=', 'cp.property_id')
                ->where('pk.parent_property_id', $parent_property_id)
                ->where('cp.category_id', $category_id)
                ->where('cp.is_deleted', Config::get('constants.OFF'))
                ->selectRaw('pk.property_id as id, pk.property as label,cp.category_property_id,pk.property_type')
                ->orderBy('label', 'asc')
                ->get();
        $with_data = array();
        array_map(function($property) use(&$with_data, $category_id, $parent_property_id)
        {
            $property->children = $this->product_properies_for_checktree($category_id, $property->id);
            $with_data[] = $property;
        }, $properties);
        return $with_data;
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

    public function save_products_combinations ($arr = array())
    {
        extract($arr);
        $product_cmb['updated_by'] = $admin_id;
        if (isset($product_cmb_properties) && !empty($product_cmb_properties))
        {
            $cmbs = array();
            foreach ($product_cmb_properties as $cmb)
            {
                $data = array();
                $cmb = explode(',', $cmb);
                if (isset($cmb[2]) && !empty($cmb[2]))
                {
                    $data['cmb_ppt_id'] = $cmb[2];
                }
                $data['property_id'] = $cmb[0];
                $data['value_id'] = $cmb[1];
                $cmbs[] = $data;
            }
            if (!$this->check_combination_exist(array(
                        'product_id'=>$product_cmb['product_id'],
                        'pro_cmb'=>$cmbs)))
            {
                if (isset($product_cmb_id) && !empty($product_cmb_id))
                {
                    $product_cmb['updated_on'] = date('Y-m-d H:i:s');
                    DB::table(Config::get('tables.PRODUCT_COMBINATIONS'))
                            ->where('product_cmb_id', $product_cmb_id)
                            ->where('is_deleted', Config::get('constants.OFF'))
                            ->update($product_cmb);
                }
                else
                {
                    $product_cmb['created_on'] = date('Y-m-d H:i:s');
                    $product_cmb_id = DB::table(Config::get('tables.PRODUCT_COMBINATIONS'))
                            ->insertGetID($product_cmb);
                    DB::table(Config::get('tables.PRODUCT_COMBINATIONS'))
                            ->where('product_cmb_id', $product_cmb_id)
                            ->where('is_deleted', Config::get('constants.OFF'))
                            ->update(array(
                                'product_cmb_code'=>'CMB'.$product_cmb_id));
                }
                if (isset($product_cmb_id) && !empty($product_cmb_id))
                {
                    $cmbs = array();
                    foreach ($product_cmb_properties as $cmb)
                    {
                        $data = array();
                        $cmb = explode(',', $cmb);
                        if (isset($cmb[2]) && !empty($cmb[2]))
                        {
                            $data['cmb_ppt_id'] = $cmb[2];
                        }
                        $data['product_cmb_id'] = $product_cmb_id;
                        $data['property_id'] = $cmb[0];
                        $data['value_id'] = $cmb[1];
                        $cmbs[] = $data;
                    }
                    if (!$this->check_combination_exist(array(
                                'product_id'=>$product_cmb['product_id'],
                                'pro_cmb'=>$cmbs)))
                    {
                        $cmb_ppt_ids = [];
                        foreach ($cmbs as $cmb)
                        {
                            $cmb['product_cmb_id'] = $product_cmb_id;
                            if (isset($cmb['cmb_ppt_id']) && !empty($cmb['cmb_ppt_id']))
                            {
                                $cmb_ppt_id = $cmb['cmb_ppt_id'];
                                unset($cmb['cmb_ppt_id']);
                                DB::table(Config::get('tables.PRODUCT_CMB_PROPERTIES'))
                                        ->where('cmb_ppt_id', $cmb_ppt_id)
                                        ->where('is_deleted', Config::get('constants.OFF'))
                                        ->update($cmb);
                            }
                            else
                            {
                                $cmb_ppt_id = DB::table(Config::get('tables.PRODUCT_CMB_PROPERTIES'))
                                        ->insertGetID($cmb);
                            }
                            $cmb_ppt_ids[] = $cmb_ppt_id;
                        }
                        DB::table(Config::get('tables.PRODUCT_CMB_PROPERTIES'))
                                ->whereNotIn('cmb_ppt_id', $cmb_ppt_ids)
                                ->where('product_cmb_id', $product_cmb_id)
                                ->where('is_deleted', Config::get('constants.OFF'))
                                ->update(array(
                                    'is_deleted'=>Config::get('constants.ON')));
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function properties_list_for_com ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.PRODUCT_PROPERTY').' as pp')
                        ->leftjoin(Config::get('tables.PRODUCT_PROPERTY_KEYS').' as pk', 'pk.property_id', '=', 'pp.property_id')
                        ->where('pp.product_id', $product_id)
                        ->where('pp.choosable', Config::get('constants.ACTIVE'))
                        ->where('pp.is_deleted', Config::get('constants.OFF'))
                        ->selectRaw('pk.property_id, pk.property')
                        ->orderBy('property', 'asc')
                        ->get();
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
                        ->selectRaw('pv.value_id,concat(pv.key_value,if(u.unit is not null,concat(\' \',u.unit),\'\')) as value,pv.property_id')
                        ->orderBy('value', 'asc')
                        ->get();
    }

    public function products_combinations ($arr = array(), $count = false)
    {
        extract($arr);
        $product_combinations = DB::table(Config::get('tables.PRODUCT_COMBINATIONS').' as p')
                ->where('p.product_id', $product_id)
                ->where('p.is_deleted', Config::get('constants.OFF'));
        if (isset($orderby))
        {
            $product_combinations->orderby($orderby, $order);
        }
        if (isset($length))
        {
            $product_combinations->skip($start)->take($length);
        }
        if ($count)
        {
            return $product_combinations->count();
        }
        else
        {
            return $product_combinations->selectRaw('p.*')->get();
        }
    }

    /*
     * Function Name        : check_combination_exist
     * Params               : product_id,[[property_id,value_id]]
     * Return               : True OR False
     */

    public function check_combination_exist ($arr = array())
    {
        extract($arr);
        $exist = 0;
        $combination_properties = DB::table(Config::get('tables.PRODUCT_CMB_PROPERTIES').' as pcp')
                ->leftJoin(Config::get('tables.PRODUCT_COMBINATIONS').' as pc', 'pc.product_cmb_id', '=', 'pcp.product_cmb_id')
                ->where('pc.product_id', $product_id)
                ->where('pcp.is_deleted', Config::get('constants.OFF'))
                ->where('pc.is_deleted', Config::get('constants.OFF'))
                ->selectRaw('property_id,value_id')
                ->get();
        if (count($combination_properties) == count($pro_cmb))
        {
            foreach ($combination_properties as $pro)
            {
                foreach ($pro_cmb as $p)
                {
                    $exist += ($pro->property_id == $p['property_id'] && $pro->value_id == $p['value_id']) ? 1 : 0;
                }
            }
        }
        return ($exist > 0 && count($combination_properties) == $exist) ? true : false;
    }

    public function delete_products_combinations ()
    {
        return DB::table(Config::get('tables.PRODUCT_COMBINATIONS'))
                        ->where('product_cmb_id', $product_cmb_id)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->update(array(
                            'is_deleted'=>Config::get('constants.ON'),
                            'updated_by'=>$admin_id));
    }

    public function get_products_combinations_properties ($product_cmb_id)
    {
        return DB::table(Config::get('tables.PRODUCT_CMB_PROPERTIES').' as pcp')
                        ->leftjoin(Config::get('tables.PRODUCT_PROPERTY_KEYS').' as pk', 'pk.property_id', '=', 'pcp.property_id')
                        ->leftjoin(Config::get('tables.PRODUCT_PROPERTY_KEY_VALUES').' as pv', 'pv.value_id', '=', 'pcp.value_id')
                        ->leftjoin(Config::get('tables.UNITS').' as u', 'u.unit_id', '=', 'pv.unit_id')
                        ->where('pcp.product_cmb_id', $product_cmb_id)
                        ->where('pcp.is_deleted', Config::get('constants.OFF'))
                        ->selectRaw('pcp.cmb_ppt_id,pcp.property_id,pcp.value_id,pk.property,concat(pv.key_value,if(u.unit is not null,concat(\' \',u.unit),\'\')) as value')
                        ->get();
    }

    public function products_countries ($arr = array(), $count = false)
    {
        extract($arr);
        $product_countries = DB::table(Config::get('tables.PRODUCT_COUNTRIES').' as pc')
                ->join(Config::get('tables.LOCATION_COUNTRY').' as lc', 'lc.country_id', '=', 'pc.country_id')
                ->where('pc.product_id', $product_id)
                ->where('pc.is_deleted', Config::get('constants.OFF'));
        if (isset($orderby))
        {
            $product_countries->orderby($orderby, $order);
        }
        if (isset($length))
        {
            $product_countries->skip($start)->take($length);
        }
        if ($count)
        {
            return $product_countries->count();
        }
        else
        {
            return $product_countries->selectRaw('pc.*,lc.country')->get();
        }
    }

    public function save_products_country ($arr = array())
    {
        extract($arr);
        if (isset($pc_id) && !empty($pc_id))
        {
            $product_country['updated_by'] = $admin_id;
            $product_country['is_deleted'] = Config::get('constants.OFF');
            return DB::table(Config::get('tables.PRODUCT_COUNTRIES'))
                            ->where('pc_id', $pc_id)
                            ->update($product_country);
        }
        else
        {
            if (!DB::table(Config::get('tables.PRODUCT_COUNTRIES'))
                            ->where($product_country)
                            ->count())
            {
                $product_country['updated_by'] = $admin_id;
                $product_country['created_on'] = date('Y-m-d H:i:s');
                return DB::table(Config::get('tables.PRODUCT_COUNTRIES'))
                                ->insertGetID($product_country);
            }
        }
        return false;
    }

    public function delete_products_countries ($arr = array())
    {
        extract($arr);
        return DB::table(Config::get('tables.PRODUCT_COUNTRIES'))
                        ->where('pc_id', $pc_id)
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->update(array(
                            'is_deleted'=>Config::get('constants.ON'),
                            'updated_by'=>$admin_id));
    }

    public function product_countries_list ($product_id)
    {
        return DB::table(Config::get('tables.PRODUCT_COUNTRIES'))
                        ->where('is_deleted', Config::get('constants.OFF'))
                        ->where('product_id', $product_id)
                        ->lists('country_id');
    }

    public function relative_post_list ($post_type_id)
    {
        switch ($post_type_id)
        {
            case Config::get('constants.POST_TYPE.BRAND'):
                return DB::table(Config::get('tables.PRODUCT_BRANDS'))
                                ->where('is_deleted', Config::get('constants.OFF'))
                                ->selectRaw('brand_id as relative_post_id,brand_name as relative_post')
                                ->get();
            case Config::get('constants.POST_TYPE.CATEGORY'):
                return DB::table(Config::get('tables.PRODUCT_CATEGORIES'))
                                ->where('is_deleted', Config::get('constants.OFF'))
                                ->selectRaw('category_id as relative_post_id,category as relative_post')
                                ->get();
            case Config::get('constants.POST_TYPE.PRODUCT'):
                return DB::table(Config::get('tables.PRODUCTS'))
                                ->where('is_deleted', Config::get('constants.OFF'))
                                ->selectRaw('product_id as relative_post_id,product_name as relative_post')
                                ->get();
            case Config::get('constants.POST_TYPE.SUPPLIER'):
                return DB::table(Config::get('tables.ACCOUNT_SUPPLIERS'))
                                ->where('is_deleted', Config::get('constants.OFF'))
                                ->selectRaw('supplier_id as relative_post_id,company_name as relative_post')
                                ->get();
        }
        return false;
    }

    public function img_type_list ($post_type_id)
    {
        return DB::table(Config::get('tables.IMG_TYPE_SETTINGS').' as imgt')
                        ->join(Config::get('tables.IMG_SETTINGS').' as imgs', 'imgs.img_type_id', '=', 'imgt.img_type_id')
                        ->where('imgs.post_type_id', $post_type_id)
                        ->selectRaw('imgt.img_type_id,imgt.display_type,imgs.width,imgs.height,imgs.max_size')
                        ->get();
    }

    public function getProductAvaliablePosts ($arr = array(), $post_type)
    {
        extract($arr);
        $query = DB::table(Config::get('tables.SUPPLIER_PRODUCT_ITEMS').' as spi')
                ->join(Config::get('tables.ACCOUNT_SUPPLIERS').' as s', function($s)
                {
                    $s->on('s.supplier_id', '=', 'spi.supplier_id')
                    ->where('s.is_deleted', '=', Config::get('constants.OFF'));
                })
                ->join(Config::get('tables.PRODUCTS').' as p', function($p)
                {
                    $p->on('p.product_id', '=', 'spi.product_id')
                    ->where('p.is_deleted', '=', Config::get('constants.OFF'));
                })
                ->leftJoin(Config::get('tables.PRODUCT_COMBINATIONS').' as pcmb', function($pcmb)
                {
                    $pcmb->on('pcmb.product_id', '=', 'p.product_id')
                    ->where('pcmb.is_deleted', '=', Config::get('constants.OFF'))
                    ->on(DB::Raw('(p.is_combinations='.Config::get('constants.OFF').' or (p.is_combinations='.Config::get('constants.ON').' and pcmb.product_cmb_id=spi.product_cmb_id))'), DB::raw(''), DB::raw(''));
                })
                ->join(Config::get('tables.PRODUCT_BRANDS').' as pb', function($pb)
                {
                    $pb->on('pb.brand_id', '=', 'p.brand_id')
                    ->where('pb.is_deleted', '=', Config::get('constants.OFF'));
                })
                ->join(Config::get('tables.PRODUCT_CATEGORIES').' as pc', function($pc)
        {
            $pc->on('pc.category_id', '=', 'p.category_id')
            ->where('pc.is_deleted', '=', Config::get('constants.OFF'));
        });
        if (isset($brand_id) && !empty($brand_id) && $post_type != Config::get('constants.POST_TYPE.BRAND'))
        {
            $query->whereIn('p.brand_id', $brand_id);
        }
        if (isset($category_id) && !empty($category_id) && $post_type != Config::get('constants.POST_TYPE.CATEGORY'))
        {
            $query->whereIn('p.category_id', $category_id);
        }
        if (isset($supplier_id) && !empty($supplier_id) && $post_type != Config::get('constants.POST_TYPE.SUPPLIER'))
        {
            $query->whereIn('spi.supplier_id', $supplier_id);
        }
        if (isset($product_id) && !empty($product_id) && $post_type != Config::get('constants.POST_TYPE.PRODUCT'))
        {
            $query->whereIn('spi.product_id', $product_id);
        }
        if (isset($product_cmb_id) && !empty($product_cmb_id) && $post_type != Config::get('constants.POST_TYPE.PRODUCT_CMB'))
        {
            $query->whereIn('spi.product_cmb_id', $product_cmb_id);
        }
        switch ($post_type)
        {
            case Config::get('constants.POST_TYPE.BRAND'):
                $query->selectRaw('DISTINCT(pb.brand_id) as id,pb.brand_name as text');
                break;
            case Config::get('constants.POST_TYPE.CATEGORY'):
                $query->selectRaw('DISTINCT(pc.category_id) as id,pc.category as text');
                break;
            case Config::get('constants.POST_TYPE.SUPPLIER'):
                $query->selectRaw('DISTINCT(s.supplier_id) as id,s.company_name as text');
                break;
            case Config::get('constants.POST_TYPE.PRODUCT'):
                $query->selectRaw('DISTINCT(spi.product_id) as id,p.product_name as text');
                break;
            case Config::get('constants.POST_TYPE.PRODUCT_CMB'):
                $query->selectRaw('DISTINCT(spi.product_cmb_id) as id,concat(p.product_name,\'(\',pcmb.product_cmb,\')\') as text');
                break;
        }
        return array_filter($query->groupby('id')->orderby('text', 'ASC')->lists('text', 'id'));
    }

}
