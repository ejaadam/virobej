<?php

ini_set('memory_limit', '256M');
//$imageName = isset($_REQUEST['img']) && !empty($_REQUEST['img']) && file_exists($_REQUEST['img']) ? $_REQUEST['img'] : 'resources/assets/imgs/dummy.png';
$imageName = isset($_REQUEST['img']) && !empty($_REQUEST['img']) && file_exists($_REQUEST['img']) ? $_REQUEST['img'] : 'resources/uploads/profile-imgs/default_img.png';
//print_r($_REQUEST['img']);exit;
if (file_exists($imageName))
{
    list($original_width, $original_height, $imtype, $imagestring) = getimagesize($imageName);
    switch ($imtype)
    {
        case 1:
            $im = imagecreatefromgif($imageName);
            break;
        case 2:
            $im = imagecreatefromjpeg($imageName);
            break;
        case 3:
            $im = imagecreatefrompng($imageName);
            break;
    }
    if (isset($im) && !empty($im))
    {
        $imageWidth = $w = (isset($_REQUEST['width']) && !empty($_REQUEST['width'])) ? $_REQUEST['width'] : $original_width;
        $imageHeight = (isset($_REQUEST['height']) && !empty($_REQUEST ['height'])) ? $_REQUEST['height'] : ($imageWidth / $original_width) * $original_height;
        $strich = isset($_REQUEST['strich']) && !empty($_REQUEST['strich']) ? $_REQUEST['strich'] : false;
        $pad = isset($_REQUEST['pad']) && !empty($_REQUEST['pad']) ? $_REQUEST['pad'] : false;
        if (isset($_REQUEST['screen_width']) && !empty($_REQUEST['screen_width']) && $_REQUEST['screen_width'] != 1440)
        {
            $imageWidth = (int) (( $imageWidth / 1440 ) * $_REQUEST['screen_width']);
        }
        if (isset($_REQUEST['screen_height']) && !empty($_REQUEST['screen_height']) && $_REQUEST['screen_height'] != 860)
        {
            $imageHeight = (int) (($imageHeight / 860 ) * $_REQUEST['screen_height']);
        }
        $srcWidth = $imageWidth;
        $srcHeight = $imageHeight;
        $x = $y = 0;
        if (!$strich && ($original_width != $imageWidth || $imageHeight != $original_height))
        {
            if ($original_width == $original_height)
            {
                if ($imageWidth == $imageHeight)
                {
                    $srcWidth = $imageWidth;
                    $srcHeight = $imageHeight;
                }
                elseif ($imageWidth > $imageHeight)
                {
                    $srcHeight = $srcWidth = $imageHeight;
                }
                elseif ($imageWidth < $imageHeight)
                {
                    $srcHeight = $srcWidth = $imageWidth;
                }
            }
            if (($original_width > $original_height && $imageWidth >= $imageHeight) || ($original_width < $original_height && $imageWidth <= $imageHeight))
            {
                $srcHeight = $imageHeight;
                $srcWidth = (int) ($srcHeight * ( $original_width / $original_height));
                if ($srcWidth > $imageWidth)
                {
                    $srcWidth = $imageWidth;
                    $srcHeight = (int) ($srcWidth * ( $original_height / $original_width));
                }
            }
            if (($original_width < $original_height && $imageWidth >= $imageHeight) || ($original_width > $original_height && $imageWidth <= $imageHeight))
            {
                $srcWidth = $imageWidth;
                $srcHeight = (int) ($srcWidth * ( $original_height / $original_width));
                if ($srcHeight > $imageHeight)
                {
                    $srcHeight = $imageHeight;
                    $srcWidth = (int) ($srcHeight * ( $original_width / $original_height));
                }
            }
            if ($pad)
            {
                $x = (int) (($imageWidth - $srcWidth ) / 2);
                $y = (int) (($imageHeight - $srcHeight ) / 2);
            }
            else
            {
                $imageWidth = $srcWidth;
                $imageHeight = $srcHeight;
            }
        }
        $im1 = imagecreatetruecolor($imageWidth, $imageHeight);
        if (($imtype == 1 ) OR ( $imtype == 3))
        {
            imagealphablending($im1, false);
            imagesavealpha($im1, true);
            $trans = imagecolorallocatealpha($im1, 255, 255, 255, 127);
            imagefilledrectangle($im1, 0, 0, $imageWidth, $imageHeight, $trans);
        }
        imagecopyresampled($im1, $im, $x, $y, 0, 0, $srcWidth, $srcHeight, $original_width, $original_height);
        if (!$strich && $pad)
        {
            imagealphablending($im1, false);
            imagesavealpha($im1, true);
            if (($imtype == 1 ) OR ( $imtype == 3))
            {
                $trans = imagecolorallocatealpha($im1, 0, 0, 0, 127);
            }
            else
            {
                $color = imagecolorsforindex($im, imagecolorat($im, 0, 0));
                $trans = imagecolorallocatealpha($im1, $color['red'], $color['green'], $color['green'], $color['alpha']);
            }
            if ($srcWidth == $imageWidth && $srcHeight != $imageHeight)
            {
                imagefilledrectangle($im1, 0, 0, $imageWidth, $y, $trans);
                imagefilledrectangle($im1, 0, ($y + $srcHeight), $imageWidth, $imageHeight, $trans);
            }
            if ($srcHeight == $imageHeight && $srcWidth != $imageWidth)
            {
                imagefilledrectangle($im1, 0, 0, $x, $imageHeight, $trans);
                imagefilledrectangle($im1, ($x + $srcWidth), 0, $imageWidth, $imageHeight, $trans);
            }
        }
        header('Cache-Control :max-age=604800, public');
        header('X-Original-size :'.$original_width.'x'.$original_height);
        header('X-Given-size :'.$imageWidth.'x'.$imageHeight);
        header('X-Avaliable-size :'.$srcWidth.'x'.$srcHeight);
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($_SERVER['DOCUMENT_ROOT'].'/'.$_SERVER['PHP_SELF'])).' GMT');
        header('expires: '.gmdate('D, d M Y H:i:s', time() + 604800).' GMT'); //60*60*24*7
        switch ($imtype)
        {
            case 2:
                header('Content-type:image/jpeg');
                $quality = isset($_REQUEST['q']) && !empty($_REQUEST['q']) && $_REQUEST['q'] >= 0 && $_REQUEST['q'] <= 100 ? $_REQUEST['q'] : 100;
                imagejpeg($im1, NULL, $quality);
                break;
            case 1:
            case 3:
                header('Content-type:image/png');
                $quality = isset($_REQUEST['q']) && !empty($_REQUEST['q']) && $_REQUEST['q'] >= 0 && $_REQUEST['q'] <= 9 ? $_REQUEST['q'] : 9;
                imagepng($im1, NULL, $quality);
                break;
        }
        imagedestroy($im1);
        imagedestroy($im);
    }
}
else
{
    header('location:index.php');
}
