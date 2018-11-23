<?php
namespace App\Helpers;
use DB;
use Illuminate\Support\Facades\Config;
use Mail;
use Log;
use Lang;
use TWMailer;
use Illuminate\Database\Eloquent\Model;
use App\Models\Images;
use URL;
use Input;
use Response;

class ImageLib extends Model
{

    public function __construct ($post_type_id = 3, $img_type = 1, $img_size = 'product-img-md')
    {
        $this->imageObj = new Images();
        $this->post_type_id = $post_type_id;
        $this->img_type = $img_type;
        $this->img_size = $img_size;
    }

    /*
     * Function Name        : upload_img
     * Params               : post_type_id      - 1:BRAND, 2:CATEGORY, 3:PRODUCT, 4:PRODUCT_CMB 4,POST_TYPE_SUPPLIER
     *                        relative_post_id  - relative_post_id
     *                        file              - Input Image File
     *                        image_type        - 1:Image,2:Logo 3:Banner
     * Returns              : image_path
     */

    public function upload_img ($relative_post_id, $data = array(), $original = false, $detailed = true)
    {
        $file = Input::all();
        extract($data);
        $op = array();
        $op['status'] = 'ERR';
        $op['url'] = '';
        $data = array();
        if (isset($file['image_id']) && ($file['image_id'] != 'undefined'))
        {
            $data['img']['img_id'] = $file['image_id'];
            $data['update'] = true;
        }
        if (isset($file['img_file']) && ($file['img_file'] != 'undefined'))
        {
            $data['img']['img_file'] = $file['img_file'];
        }
        $file = $file['file'];
        $data['img']['post_type_id'] = isset($post_type_id) ? $post_type_id : $this->post_type_id;
        $data['img']['img_type'] = isset($img_type) ? $img_type : $this->img_type;
        $data['img']['relative_post_id'] = $relative_post_id;
        $destinationPath = $this->imageObj->get_file_path($data['img']);		
        if ($destinationPath)
        {
            $data['img']['img_path'] = $destinationPath['img_path'];
            $data['img']['updated_by'] = 1; // account id
            $data['ext'] = $extension = $file->getClientOriginalExtension();
            $data['img_size'] = isset($img_size) ? $img_size : $this->img_size;
            $data['original'] = $original;
            $data['detailed'] = $detailed;
            $data['original'] = false;			
            if ($img_details = $this->imageObj->save_img($data))
            {				
                $img_details = is_array($img_details) && !empty($img_details) ? $img_details[0] : $img_details;
                if (isset($data['img']['img_file']))
                {
                    $file->move($destinationPath['upload_path'], $img_details);					
                    return Response::json($op);
                }
                else
                {
                    $file->move($destinationPath['upload_path'], $img_details->img_file);
                }
                if (!empty($url))
                {
                    $op['status'] = 'OK';
                    $op['url'] = $img_details->img_path;
                }
                return Response::json($op);
            }
        }
        return false;
    }

    /*
     * Function Name        : get_imgs
     * Params               : post_type_id - 1:BRAND, 2:CATEGORY, 3:PRODUCT, 4:PRODUCT_CMB 4,POST_TYPE_SUPPLIER
     *                        relative_post_id      - relative_post_id
     *                        limit        - number of images
     *                        image_type   - 1:Image,2:Logo 3:Banner
     *                        img_size     - default:['product-img-md']
     * Returns              : image_path
     */

    public function get_imgs ($relative_post_id, $data = array(), $original = false, $detailed = false)
    {	
        extract($data);
        $filter = (isset($filter) && !empty($filter)) ? $filter : [];
        $data = array();
        $data['post_type_id'] = isset($post_type_id) ? $post_type_id : $this->post_type_id;
        $data['img_type'] = isset($img_type) ? $img_type : $this->img_type;
        $data['img_size'] = isset($img_size) ? $img_size : $this->img_size;
        $data['original'] = $original;
        $data['detailed'] = $detailed;
        $data['relative_post_id'] = $relative_post_id;		
        if (isset($limit))
        {
            $data['limit'] = $limit;
        }		
        $image_details = $this->imageObj->get_imgs($data, $filter);
        return !empty($image_details) ? $image_details : [(object) ['img_path'=>URL::asset(Config::get('path.DUMMY_IMG_PATH'))]];
    }

    public function get_imgbyID ($img_id, $data = array(), $original = false, $detailed = false)
    {
        extract($data);
        $data = array();
        $data['img_id'] = $img_id;
        $data['img_size'] = isset($img_size) ? $img_size : $this->img_size;
        $data['original'] = $original;
        $data['detailed'] = $detailed;
        $image_details = $this->imageObj->get_imgs($data);
        return (is_array($image_details)) ? $image_details[0]->img_path : $image_details->img_path;
    }

    public function getImage ()
    {
        $data = Input::all();
        $file = Config::get('path.UPLOAD_FILE_PATH').$data['file_path'].'/'.$data['img_path'].'/'.$data['relative_post_id'].'/'.$data['img_file'];
        if (file_exists($file))
        {
            list($imwidth, $imheight, $imtype, $imstring) = getimagesize($file);
            $imageWidth = (isset($_REQUEST['width']) && !empty($_REQUEST['width'])) ? $_REQUEST['width'] : 200;
            $imageHeight = (isset($_REQUEST['height']) && !empty($_REQUEST['height'])) ? $_REQUEST['height'] : ($imageWidth / $imwidth) * $imheight;
            switch ($imtype)
            {
                case IMG_GIF:
                    $im = imagecreatefromgif($imageName);
                    break;
                case IMG_JPG:
                    $im = imagecreatefromjpeg($imageName);
                    break;
                case 3 || IMG_PNG:
                    $im = imagecreatefrompng($imageName);
                    break;
            }
            if (isset($im) && !empty($im))
            {
                $im1 = imagecreatetruecolor($imageWidth, $imageHeight);
                imagefilledrectangle($im1, 0, 0, $imageWidth, $imageHeight, imagecolorallocate($im1, 255, 255, 255));
                imagecopyresampled($im1, $im, 0, 0, 0, 0, $imageWidth, $imageHeight, $imwidth, $imheight);
                header('Content-type:image/jpeg');
                header('cache-control: no-cache,  must-revalidate');
                header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($_SERVER['DOCUMENT_ROOT'].'/'.$_SERVER['PHP_SELF'])).' GMT');
                header('expires: '.gmdate('D, d M Y H:i:s', time() + 60 * 60 * 24 * 7).' GMT');
                imagejpeg($im1);
                imagedestroy($im1);
                imagedestroy($im);
            }
        }
        else
        {
            return App::abort(404);
        }
    }

}
