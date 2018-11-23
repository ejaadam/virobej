<?php

namespace App\Http\Controllers;

ini_set('memory_limit', '128M');
ini_set('post_max_size', '128M');
ini_set('upload_max_filesize', '128M');

class MyImage
{
	
    public function imageresize ($file, $dest, $rewidth, $reheight = 0, $convert = 0)
    {
        list($imwidth, $imheight, $imtype, $imstring) = getimagesize($file);
        if ($reheight == 0 && $imwidth != 0)
        {
            $reheight = ($rewidth / $imwidth) * $imheight;
        }
        switch ($imtype)
        {
            case 1: $im = imagecreatefromgif($file);
                break;
            case 2: $im = imagecreatefromjpeg($file);
                break;
            case 3: $im = imagecreatefrompng($file);
                break;
        }
        $im1 = imagecreatetruecolor($rewidth, $reheight);
        $bg = imagecolorallocate($im1, 255, 255, 255);
        imagefilledrectangle($im1, 0, 0, $rewidth, $reheight, $bg);
        imagecopyresampled($im1, $im, 0, 0, 0, 0, $rewidth, $reheight, $imwidth, $imheight);
        if ($convert)
        {
            imagejpeg($im1, $dest, 100);
        }
        else
        {
            switch ($imtype)
            {
                case 1:
                    imagegif($im1, $dest);
                    break;
                case 2:
                    imagejpeg($im1, $dest, 100);
                    break;
                case 3:
                    imagepng($im1, $dest);
                    break;
            }
        }
        imagedestroy($im);
    }

    public function stream_image ($file, $dest, $rewidth, $reheight = 0)
    {
        list($imwidth, $imheight, $imtype, $imstring) = getimagesize($file);
        if ($reheight == 0)
        {
            $reheight = ($rewidth / $imwidth) * $imheight;
        }
        switch ($imtype)
        {
            case 1: $im = imagecreatefromgif($file);
                break;
            case 2: $im = imagecreatefromjpeg($file);
                break;
            case 3: $im = imagecreatefrompng($file);
                break;
        }
        $im1 = imagecreatetruecolor($rewidth, $reheight);
        $bg = imagecolorallocate($im1, 255, 255, 255);
        imagefilledrectangle($im1, 0, 0, $rewidth, $reheight, $bg);
        imagecopyresampled($im1, $im, 0, 0, 0, 0, $rewidth, $reheight, $imwidth, $imheight);
        $im1 = imagecreatetruecolor($rewidth, $reheight);
        $bg = imagecolorallocate($im1, 255, 255, 255);
        imagefilledrectangle($im1, 0, 0, $rewidth, $reheight, $bg);
        imagecopyresampled($im1, $im, 0, 0, 0, 0, $rewidth, $reheight, $imwidth, $imheight);
        header('Content-type:image/jpeg');
        header('cache-control: no-cache,  must-revalidate');
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($_SERVER['DOCUMENT_ROOT'].'/'.$_SERVER['PHP_SELF'])).' GMT');
        header('expires: '.gmdate('D, d M Y H:i:s', time() + (60 * 60 * 24 * 7)).' GMT');
        imagejpeg($im1);
        imagedestroy($im1);
    }

}
