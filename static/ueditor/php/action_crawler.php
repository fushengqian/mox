<?php
/**
 * 抓取远程图片
 * User: Jinqn
 * Date: 14-04-14
 * Time: 下午19:18
 */
set_time_limit(0);
include("Uploader.class.php");

/* 上传配置 */
$config = array(
    "pathFormat" => $CONFIG['catcherPathFormat'],
    "maxSize" => $CONFIG['catcherMaxSize'],
    "allowFiles" => $CONFIG['catcherAllowFiles'],
    "oriName" => "remote.png"
);
$fieldName = $CONFIG['catcherFieldName'];

/* 抓取远程图片 */
$list = array();
if (isset($_POST[$fieldName])) {
    $source = $_POST[$fieldName];
} else {
    $source = $_GET[$fieldName];
}

use sinacloud\sae\Storage as Storage;

foreach ($source as $imgUrl)
{
    $item = new Uploader($imgUrl, $config, "remote");
    $info = $item -> getFileInfo();
    
    /*$filePath = pathinfo($imgUrl);
    $toUrl = 'D:/wamp/www/mox/upload/image/'.date("Ymd", time()).'/'.md5($imgUrl).'.jpg';
    $info = resize_image($imgUrl, $toUrl);
    
    $info = array('state'   => 'SUCCESS',
                  'url'      => '/upload/image/'.md5($imgUrl).'.jpg',
                  'size'     => 100,
                  'title'    => $filePath['basename'],
                  'original' => $filePath['basename'],
                  'source'   => htmlspecialchars($imgUrl)
            );*/
    
    array_push($list, $info);
}

/* 返回抓取数据 */
return json_encode(array(
    'state'=> count($list) ? 'SUCCESS':'ERROR',
    'list'=> $list
));

/**
 * 重设图片大小
 * */
function resize_image($image, $thumbname, $type = '', $maxWidth = 280, $maxHeight = 180, $interlace = true)
{
    $path = pathinfo($thumbname);
    if (!is_dir($path['dirname']))
    {
        mkdir($path['dirname'], 0777, true);
    }
    
    $imageInfo = getimagesize($image);
    
    if ($imageInfo !== false)
    {
        $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
        $info = array(
                "width" => $imageInfo[0],
                "height" => $imageInfo[1],
                "type" => $imageType,
                "mime" => $imageInfo['mime']
        );
    }
    
    if ($info !== false)
    {
        $srcWidth = $info['width'];
        $srcHeight = $info['height'];
        $type = empty($type) ? $info['type'] : $type;
        $type = strtolower($type);
        $interlace = $interlace ? 1 : 0;
        unset($info);
        
        //计算缩放比例
        $scale = max($maxWidth / $srcWidth, $maxHeight / $srcHeight);
        
        //判断原图和缩略图比例 如原图宽于缩略图则裁掉两边 反之..
        
        if($maxWidth / $srcWidth > $maxHeight / $srcHeight)
        {
            //高于
            $srcX = 0;
            $srcY = ($srcHeight - $maxHeight / $scale) / 2 ;
            $cutWidth = $srcWidth;
            $cutHeight = $maxHeight / $scale;
        }
        else
        {
            //宽于
            $srcX = ($srcWidth - $maxWidth / $scale) / 2;
            $srcY = 0;
            $cutWidth = $maxWidth / $scale;
            $cutHeight = $srcHeight;
        }
        
        // 载入原图
        $createFun = 'ImageCreateFrom' . ($type == 'jpg' ? 'jpeg' : $type);
        $srcImg = $createFun($image);
        
        // 创建缩略图
        if ($type != 'gif' && function_exists('imagecreatetruecolor'))
        {
            $thumbImg = imagecreatetruecolor($maxWidth, $maxHeight);
        }
        else
        {
            $thumbImg = imagecreate($maxWidth, $maxHeight);
        }
        
        // 复制图片
        if (function_exists("ImageCopyResampled"))
        {
            imagecopyresampled($thumbImg, $srcImg, 0, 0, $srcX, $srcY, $maxWidth, $maxHeight, $cutWidth, $cutHeight);
        }
        else
        {
            imagecopyresized($thumbImg, $srcImg, 0, 0, $srcX, $srcY, $maxWidth, $maxHeight, $cutWidth, $cutHeight);
        }
        
        if ('gif' == $type || 'png' == $type)
        {
            $background_color = imagecolorallocate($thumbImg, 0, 255, 0);
            imagecolortransparent($thumbImg, $background_color);
        }
        
        // 对jpeg图形设置隔行扫描
        if ('jpg' == $type || 'jpeg' == $type)
        {
            imageinterlace($thumbImg, $interlace);
        }
        
        // 生成图片
        $imageFun = 'image' . ($type == 'jpg' ? 'jpeg' : $type);
        $imageFun($thumbImg, $thumbname);
        imagedestroy($thumbImg);
        imagedestroy($srcImg);
        
        return $thumbname;
    }
    
    return false;
}