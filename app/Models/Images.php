<?php
namespace App\Models;
use DB;
use Illuminate\Database\Eloquent\Model;
use Config;
use URL;
use Lang;

class Images
{
    /*
     * Function Name        : get_imgs
     * Params               : post_type_id - 1:BRAND, 2:CATEGORY, 3:PRODUCT, 4:PRODUCT_CMB 4,POST_TYPE_SUPPLIER
     *                        relative_post_id      - relative_post_id
     *                        limit        - number of images
     * Returns              : image_path
     */

    public function get_imgs ($arr = array(), $filter = array())
    {	
        $original = false;
        $detailed = false;
        extract($arr);
        $images = DB::table(Config::get('tables.IMGS').' as img')
                ->leftJoin(Config::get('tables.IMG_SETTINGS').' as ims', function($subquery)
                {
                    $subquery->on('ims.post_type_id', '=', 'img.post_type_id')
                    ->on('ims.img_type_id', '=', 'img.img_type');
                })
                ->leftJoin(Config::get('tables.PRODUCT_BRANDS').' as b', 'b.brand_id', '=', 'img.relative_post_id')
                ->leftJoin(Config::get('tables.PRODUCT_CATEGORIES').' as c', 'c.category_id', '=', 'img.relative_post_id')
                ->leftJoin(Config::get('tables.PRODUCTS').' as prd', 'prd.product_id', '=', 'img.relative_post_id')
                ->leftJoin(Config::get('tables.PRODUCT_DETAILS').' as prdd', 'prdd.product_id', '=', 'img.relative_post_id')
                ->leftJoin(Config::get('tables.PRODUCT_COMBINATIONS').' as pcm', 'pcm.product_cmb_id', '=', 'img.relative_post_id')
                ->leftJoin(Config::get('tables.PRODUCTS').' as cprd', 'cprd.product_id', '=', 'pcm.product_id')
                ->leftJoin(Config::get('tables.PRODUCT_DETAILS').' as cprdd', function($cprdd)
                {
                    $cprdd->on('cprdd.product_id', '=', 'img.relative_post_id')
                    ->on('cprdd.product_cmb_id', '=', 'pcm.product_cmb_id');
                })
                ->leftJoin(Config::get('tables.SUPPLIER_MST').' as s', 's.supplier_id', '=', 'img.relative_post_id')
                ->where('img.is_deleted', Config::get('constants.OFF'))
                ->where('img.status_id', Config::get('constants.ACTIVE'));				
				
        if (isset($post_type_id) && !empty($post_type_id))
        {
            $images->where('img.post_type_id', $post_type_id);
        }
        if (isset($relative_post_id) && !empty($relative_post_id))
        {
            $images->where('img.relative_post_id', $relative_post_id);
        }
        if (isset($img_type) && !empty($img_type))
        {
            $images->where('img.img_type', $img_type);
        }
		
        if (!empty($filter))
        {
            $images->join(Config::get('tables.IMG_FILTERS').' as imgf', function($imgf) use($filter)
            {
                $imgf->on('imgf.img_id', '=', 'img.img_id')
                        ->where('imgf.post_type_id', '=', $filter['post_type_id'])
                        ->where('imgf.relative_post_id', '=', $filter['relative_post_id']);
            })->orderby('imgf.primary_img', 'DESC')->orderby('imgf.sort_order', 'ASC');
        }
        else
        {
            $images->orderby('img.primary', 'DESC')->orderby('img.sort_order', 'ASC');
        }		
        $images->selectRaw('img.img_id as id,img.img_path,img.img_file,img.primary,img.sort_order,ims.file_path,img.relative_post_id,'
                .'(CASE img.post_type_id'
                .' WHEN '.Config::get('constants.POST_TYPE.BRAND').' THEN b.url_str'
                .' WHEN '.Config::get('constants.POST_TYPE.CATEGORY').' THEN c.url_str'
                .' WHEN '.Config::get('constants.POST_TYPE.PRODUCT').' THEN prdd.sku'
                .' WHEN '.Config::get('constants.POST_TYPE.PRODUCT_CMB').' THEN cprdd.sku'
                .' WHEN '.Config::get('constants.POST_TYPE.SUPPLIER').' THEN s.company_name'
                .' END) as sku');
        if (isset($img_id) && !empty($img_id))
        {
            if (is_array($img_id))
            {
                $images->whereIn('img.img_id', $img_id);
            }
            else
            {
                $images->where('img.img_id', $img_id);
            }
        }
        if (isset($limit))
        {
            $images->take($limit);
        }
        $images = $images->get();
        array_walk($images, function(&$v) use($img_size, $original, $detailed)
        {
            $v->img_path = $this->generate_img_url($v, $img_size, $original);
            if (!$detailed)
            {
                $v = (object) ['img_path'=>$v->img_path];
            }
        });
        return isset($img_id) && !is_array($img_id) ? $images[0] : $images;
    }

    /*
     * Function Name        : save_img
     * Params               : img_id,ext,img[post_type_id,relative_post_id,img_type,img_path,img,updated_by]
     * Return               : Image Details or FALSE
     */

    public function save_img ($arr = array())
    {
        $img = array(
            'img_file'=>false);
        extract($arr);
        update:
        if (isset($img['img_id']) && !empty($img['img_id']))
        {
            $img['updated_on'] = date('Y-m-d H:i:s');
            if (!empty($update))
            {
                $img_file = explode('.', $img['img_file']);
                $img['img_file'] = $img_file[0].$ext;
            }
            if (DB::table(Config::get('tables.IMGS'))
                            ->where('img_id', $img['img_id'])
                            ->update($img))
            {
                if (!empty($update))
                {
                    return $img['img_file'];
                }
            }
        }
        else
        {
            $img['created_on'] = date('Y-m-d H:i:s');
            $img['sort_order'] = DB::table(Config::get('tables.IMGS'))
                            ->where('post_type_id', $img['post_type_id'])
                            ->where('relative_post_id', $img['relative_post_id'])
                            ->where('img_type', $img['img_type'])
                            ->where('is_deleted', Config::get('constants.OFF'))
                            ->count() + 1;
            $img_id = DB::table(Config::get('tables.IMGS'))
                    ->insertGetId($img);
            if (isset($img_id) && !empty($img_id) && empty($img_file))
            {
                $img = [];
                $img['img_file'] = $this->image_name($img_id, $ext);
                $img['img_id'] = $img_id;
                goto update;
            }
        }
        return $this->get_imgs(array('img_id'=>$img_id, 'img_size'=>$img_size, 'detailed'=>$detailed, 'original'=>$original));
    }

    function image_name ($id, $ext, $len = 5)
    {
        $str = '';
        $characters = array_merge(range('a', 'z'), range('0', '9'));
        $max = count($characters) - 1;
        for ($i = 0; $i < $len; $i++)
        {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return 'img'.$id.$str.'.'.$ext;
    }

    function randomStr ($len = 3)
    {
        $str = '';
        //$characters = array_merge(range('a', 'z'), range('2', '9'));
        $characters = str_split('abcdefghjkmnpqrstuvwxyz23456789');
        $max = count($characters) - 1;
        for ($i = 0; $i < $len; $i++)
        {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    public function get_file_path ($arr = array())
    {
        extract($arr);
        $upload_path = base_path().Config::get('path.UPLOAD_FILE_PATH');
        $file_path = DB::table(Config::get('tables.IMG_SETTINGS'))
                ->where('post_type_id', $post_type_id)
                ->where('img_type_id', $img_type)
                ->value('file_path');
        if (!empty($file_path))
        {
            $upload_path.= $file_path;
            $img_path = DB::table(Config::get('tables.IMGS'))
                    ->where('post_type_id', $post_type_id)
                    ->where('relative_post_id', $relative_post_id)
                    ->value('img_path');
            if (!$img_path || empty($img_path))
            {
                $img_path = $this->randomStr();
            }
            $upload_path.='/'.$img_path.'/'.$relative_post_id;
            if (!is_dir($upload_path))
            {
                mkdir($upload_path, 0777, true);
            }
            return array(
                'img_path'=>$img_path,
                'upload_path'=>$upload_path);
        }
        return false;
    }

    public function generate_img_url ($img_details, $img_size, $original = false)
    {
        if ($original)
        {
            $url = Config::get('path.UPLOAD_FILE_PATH');
            $url .= $img_details->file_path;
            $url .= '/'.$img_details->img_path;
            $url .= '/'.$img_details->relative_post_id;
            $url .= '/'.$img_details->img_file;
            return $url;
        }
        else
        {
            $query = DB::table(Config::get('tables.IMG_DISPLAY_SETTINGS'))
                    ->select('display_size', 'width', 'height');
            if (is_array($img_size))
            {
                $img_size = $query->whereIn('display_size', $img_size)->get();
            }
            else
            {
                $img_size = $query->where('display_size', $img_size)->first();
            }
            $url = 'resources/uploads/';
            $url .= $img_details->file_path;
            $url .= '/'.implode('/', str_split($img_details->img_path));
            $url .= '/'.$img_details->sku;
            if (!empty($img_size))
            {
                if (count($img_size) <= 1)
                {
                    $url .= '/'.$img_size->width;
                    $url .= '/'.$img_size->height;
                    $url .= '/'.$img_details->relative_post_id.$img_details->img_file;
                    return URL::asset($url);
                }
                else
                {
                    $urls = [];
                    foreach ($img_size as $img)
                    {
                        $urls[$img->display_size] = URL::asset($url.'/'.$img->width.'/'.$img->height.'/'.$img_details->relative_post_id.$img_details->img_file);
                    }
                    return $urls;
                }
            }
        }
        return false;
    }

}
