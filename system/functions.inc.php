<?php
function get_url()
{
    $arg = !empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : '';
    $url =  'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].$arg;

    return str_replace('/index.php?', '', $url);
}

/**
 * 是否搜索引擎
 */
function is_bot()
{
    $botlist = array("Teoma", "alexa", "froogle", "Gigabot", "inktomi",
        "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory",
        "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot",
        "crawler", "bbs.it-home.org", "Googlebot", "Scooter", "Slurp",
        "msnbot", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz",
        "Baiduspider", "Feedfetcher-Google", "TechnoratiSnoop", "Rankivabot",
        "Mediapartners-Google", "Sogou web spider", "WebAlta Crawler","TweetmemeBot",
        "Butterfly","Twitturls","Me.dium","Twiceler", "Linux");

    foreach($botlist as $bot)
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], $bot)!==false) {
            return true;
        }
    }

    return false;
}

/**
 * 隐藏字符串部分信息
 * @param string $str
 * @param int    $start
 * @param int    $end
 * @return string
 */
function hide_str($str, $start = 0, $end = 0)
{
    $arr = str_split($str);
    $result = '';
    foreach($arr as $key => $value)
    {
        if ($key >= $start && $key <= $end)
        {
            $result .= '*';
        }
        else
        {
            $result .= $value;
        }
    }

    return $result;
}

/**
 * 是否是AJAx提交的
 * @return bool
 */
function is_ajax()
{
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * 获取浏览器、操作系统信息
 */
function get_client()
{
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $brower = array(
        'MSIE' => 1,
        'Firefox' => 2,
        'QQBrowser' => 3,
        'QQ/' => 3,
        'UCBrowser' => 4,
        'MicroMessenger' => 9,
        'Edge' => 5,
        'Chrome' => 6,
        'Opera' => 7,
        'OPR' => 7,
        'Safari' => 8,
        'Trident/' => 1
    );
    $system = array(
        'Windows Phone' => 4,
        'Windows' => 1,
        'Android' => 2,
        'iPhone' => 3,
        'iPad' => 5,
        'FreeBSD' => 6,
        'Linux' => 7,
        'Mac' => 8,
        'Solaris' => 9,
        'Ubuntu' => 10
    );
    $browser_num = 0;//未知
    $system_num = 0;//未知
    foreach($brower as $bro => $val)
    {
        if(stripos($agent, $bro) !== false)
        {
            $browser_num = $bro;
            break;
        }
    }

    foreach($system as $sys => $val)
    {
        if(stripos($agent, $sys) !== false)
        {
            $system_num = $sys;
            break;
        }
    }

    return $system_num.','.$browser_num;
}

function set_file_name()
{
    /* 选择一个随机的方案 */
    mt_srand((double) microtime() * 1000000);
    return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

//去除html,空格
function clear_text($string)
{
    $string = trim(strip_tags($string));
    $string = str_replace(' ', '', $string);
    $string = str_replace('　', '', $string);
    $string = str_replace('&nbsp;', '', $string);
    
    return $string;
}

function is_ios()
{
    //全部变成小写字母
    $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    
    //分别进行判断
    if(stripos($agent, 'iphone') || stripos($agent, 'ipad'))
    {
        return true;
    }
    
    return false;
}

//转成手机用的图片
function mobile_size($url)
{
	return $url;

    $path = pathinfo($url);
    
    $new_url = str_replace('/static/', '/shuoly_cdn/', $path['dirname']).'/'.$path['filename'].'_small.'.$path['extension'];
    
    return $new_url;
}

//从字符串中提取参数，如shanghai235，提取出shanghai，235
function get_para($str)
{
   return preg_split("/([0-9]+)/", $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
}

//去除a标签的链接
function remove_href($string)
{
    return preg_replace("/<\/a>/", "" , preg_replace("/<a[^>]*>/", "", $string));
}

//解决中文路径问题
function path_info($filepath)
{
    $path_parts = array();
    $path_parts ['dirname'] = rtrim(substr($filepath, 0, strrpos($filepath, '/')),"/")."/";
    $path_parts ['basename'] = ltrim(substr($filepath, strrpos($filepath, '/')),"/");
    $path_parts ['extension'] = substr(strrchr($filepath, '.'), 1);
    $path_parts ['filename'] = ltrim(substr($path_parts ['basename'], 0, strrpos($path_parts ['basename'], '.')),"/");
    return $path_parts;
}

function summary($str, $len, $suffix = '...')
{
    $str = trim(strip_tags($str));
    $str = str_replace('&nbsp;', '', $str);
    if(mb_strlen($str, 'UTF-8') > $len)
    {
        $str = mb_substr($str, 0, $len-3, 'UTF-8').$suffix;
    }
    
    return $str;
}

/**
 * 判断是否我utf-8编码的字符串
 * @param type $string
 * @return boolean
 */
function is_utf8( $string )
{
    if ( preg_match( "/^([" . chr( 228 ) . "-" . chr( 233 ) . "]{1}[" . chr( 128 ) . "-" . chr( 191 ) . "]{1}[" . chr( 128 ) . "-" . chr( 191 ) . "]{1}){1}/", $string ) == true || preg_match( "/([" . chr( 228 ) . "-" . chr( 233 ) . "]{1}[" . chr( 128 ) . "-" . chr( 191 ) . "]{1}[" . chr( 128 ) . "-" . chr( 191 ) . "]{1}){1}$/", $string ) == true || preg_match( "/([" . chr( 228 ) . "-" . chr( 233 ) . "]{1}[" . chr( 128 ) . "-" . chr( 191 ) . "]{1}[" . chr( 128 ) . "-" . chr( 191 ) . "]{1}){2,}/", $string ) == true )
    {
        return true;
    }
    else
    {
        return false;
    }
}

function get_farm_url($city_id = 0, $id, $cate = 0)
{
    if ($cate >= 13)
    {
        return '';
    }
    if (!is_mobile())
    {
        return G_DEMAIN.'/farm/'.encode($id).'.html';
    }
    else
    {
        return 'https://m'.G_BASE_DEMAIN.'/farm/'.encode($id).'.html';
    }
}

/**
 * 数字简单加密函数
 * @param  array  $str  加密数据
 * @param  string $key  加密密钥
 * @return string
 * */
function encode($str, $key = 99)
{
    $str = $key.strrev($str+$key);
    return $str;
}

/**
 * 数字解密函数
 * @param  array  $str  加密串
 * @param  string $key  加密密钥
 * @return mix
 * */
function decode($str, $key = 99)
{
    $str = mb_substr(strrev($str), 0, -strlen($key)) - $key;
    return $str;
}

/**
 * FarmNc 系统函数类
 *
 * @package     FarmNc
 * @subpackage  System
 * @category    Libraries
 * @author      FarmNc Dev Team
 */

function get_city_detail($city_id = '', $name = '')
{
   $list = get_city_list();
   
   foreach($list as $k => $v)
   {
       if($city_id == $v['id'] && !empty($city_id))
       {
           return $v;
       }
       
       if(($name == $v['uname'] && !empty($name)) || ($name == $v['name'] && !empty($name)))
       {
           return $v;
       }
   }
   
   return '';
}

function get_area_detail($uname)
{
    $data = FARM_APP::model('system') -> get_area_detail('', $uname, '', false, true);
    
    return $data;
}

/**
 * 城市列表
 * */
function get_city_list($effect = false)
{
    $data = FARM_APP::model('system') -> get_city_list($effect, false);
    
    return $data;
}

function save_image($url, $path = "")
{
    $url = str_replace('https://', 'http://', $url);

    //$url 为空则返回 false;
    if($url == "")
    {
        return false;
    }
    
    ob_start();
    readfile($url);
    $img = ob_get_contents();
    
    ob_end_clean();
    $size = strlen($img);
    
    if ($size < 1)
    {
        return false;
    }
    
    $fp2 = fopen($path , "a");
    fwrite($fp2, $img);
    fclose($fp2);
    
    return true;
}

/**
 * 生成缩略图函数（支持图片格式：gif、jpeg、png和bmp）
 * @author ruxing.li
 * @param  string $src      源图片路径
 * @param  int    $width    缩略图宽度（只指定高度时进行等比缩放）
 * @param  int    $width    缩略图高度（只指定宽度时进行等比缩放）
 * @param  string $filename 保存路径（不指定时直接输出到浏览器）
 * @return bool
 */
function deal_image($src, $width = null, $height = null, $filename = null)
{
    if (!isset($width) && !isset($height))
    {
        return false;
    }
    if (isset($width) && $width <= 0)
    {
        return false;
    }
    if (isset($height) && $height <= 0)
    {
        return false;
    }
    
    $size = getimagesize($src);
    if (!$size)
    {
        return false;
    }
    
    list($src_w, $src_h, $src_type) = $size;
    $src_mime = $size['mime'];
    
    if ($filename)
    {
        $path = path_info($filename);
        if (!is_dir($path['dirname']))
        {
            mkdir($path['dirname'], 0777, true);
        }
    }
    
    switch($src_type)
    {
        case 1 :
            $img_type = 'gif';
            break;
        case 2 :
            $img_type = 'jpeg';
            break;
        case 3 :
            $img_type = 'png';
            break;
        case 15 :
            $img_type = 'wbmp';
            break;
        default :
            return false;
    }
    
    if (!isset($width))
    {
        $width = $src_w * ($height / $src_h);
    }
    
    if (!isset($height))
    {
        $height = $src_h * ($width / $src_w);
    }
    
    $imagecreatefunc = 'imagecreatefrom' . $img_type;
    $src_img = $imagecreatefunc($src);
    $dest_img = imagecreatetruecolor($width, $height);
    imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $width, $height, $src_w, $src_h);
    
    $imagefunc = 'image' . $img_type;
    if ($filename)
    {
        $imagefunc($dest_img, $filename);
    }
    else
    {
        header('Content-Type: ' . $src_mime);
        $imagefunc($dest_img);
    }
    
    imagedestroy($src_img);
    imagedestroy($dest_img);
    
    return true;
}

/**
 * 图片加水印（适用于png/jpg/gif格式）
 * @param $srcImg 原图片
 * @param $waterImg 水印图片
 * @param $savepath 保存路径
 * @param $savename 保存名字
 * @param $positon 水印位置
 * 1:顶部居左, 2:顶部居右, 3:居中, 4:底部局左, 5:底部居右
 * @param $alpha 透明度 -- 0:完全透明, 100:完全不透明
 *
 * @return 成功 -- 加水印后的新图片地址
 *         失败 -- -1:原文件不存在, -2:水印图片不存在, -3:原文件图像对象建立失败
 *         -4:水印文件图像对象建立失败 -5:加水印后的新图片保存失败
 */
function img_water_mark($srcImg, $waterImg = 'http://www.moxquan.com/static/images/water.jpg', $savepath = 'D:\wamp\www\farm\static\deal_images', $savename = null, $positon = 5, $alpha = 100)
{
    $temp = pathinfo($srcImg);
    
    $name = $temp['basename'];
    $path = $temp['dirname'];
    $exte = $temp['extension'];
    $savename = $savename ? $savename : $name;
    $savepath = $savepath ? $savepath : $path;
    $savefile = $savepath .'/'. $savename;
    $srcinfo = @getimagesize($srcImg);
    
    if (!$srcinfo)
    {
        return -1; //原文件不存在
    }
    
    $waterinfo = @getimagesize($waterImg);
    
    if (!$waterinfo)
    {
        return -2; //水印图片不存在
    }
    
    $srcImgObj = image_create_from_ext($srcImg);
    if (!$srcImgObj)
    {
        return -3; //原文件图像对象建立失败
    }
    
    $waterImgObj = image_create_from_ext($waterImg);
    
    if (!$waterImgObj)
    {
        return -4; //水印文件图像对象建立失败
    }
    
    switch ($positon)
    {
        //1顶部居左
        case 1: $x=$y=0; break;
        //2顶部居右
        case 2: $x = $srcinfo[0]-$waterinfo[0]; $y = 0; break;
        //3居中
        case 3: $x = ($srcinfo[0]-$waterinfo[0])/2; $y = ($srcinfo[1]-$waterinfo[1])/2; break;
        //4底部居左
        case 4: $x = 0; $y = $srcinfo[1]-$waterinfo[1]; break;
        //5底部居右
        case 5: $x = $srcinfo[0]-$waterinfo[0]; $y = $srcinfo[1]-$waterinfo[1]; break;
        //6底部居中
        case 6: $x = ($srcinfo[0]-$waterinfo[0])/2+12; $y = $srcinfo[1]-$waterinfo[1]-34; break;
        default: $x=$y=0;
    }
    
    imagecopymerge($srcImgObj, $waterImgObj, $x, $y, 0, 0, $waterinfo[0], $waterinfo[1], $alpha);
    
    switch ($srcinfo[2])
    {
        case 1: imagegif($srcImgObj, $savefile); break;
        case 2: imagejpeg($srcImgObj, $savefile); break;
        case 3: imagepng($srcImgObj, $savefile); break;
        default: return -5; //保存失败
    }
    
    imagedestroy($srcImgObj);
    imagedestroy($waterImgObj);
    
    return $savefile;
}

function image_create_from_ext($imgfile)
{
    $info = getimagesize($imgfile);
    $im = null;
    
    switch ($info[2])
    {
        case 1: $im=imagecreatefromgif($imgfile); break;
        case 2: $im=imagecreatefromjpeg($imgfile); break;
        case 3: $im=imagecreatefrompng($imgfile); break;
    }
    
    return $im;
}

/**
 * 获取站点根目录 URL
 *
 * @return string
 */
function base_url()
{
    $clean_url = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : NULL;
    //$clean_url = dirname(rtrim($_SERVER['PHP_SELF'], $clean_url));
    $clean_url = rtrim($_SERVER['HTTP_HOST'] . $clean_url, '/\\');
    if ((isset($_SERVER['HTTPS']) AND !in_array(strtolower($_SERVER['HTTPS']), array('off', 'no', 'false', 'disabled'))) OR $_SERVER['SERVER_PORT'] == 443)
    {
        $scheme = 'https';
    }
    else
    {
        $scheme = 'http';
    }
    
    return $scheme . '://' . $clean_url;
}

/**
 * 根据特定规则对数组进行排序
 *
 * 提取多维数组的某个键名，以便把数组转换成一位数组进行排序（注意：不支持下标，否则排序会出错）
 *
 * @param  array
 * @param  string
 * @param  string
 * @return array
 */
function aasort($source_array, $order_field, $sort_type = 'DESC')
{
    if (! is_array($source_array) or sizeof($source_array) == 0)
    {
        return false;
    }

    foreach ($source_array as $array_key => $array_row)
    {
        $sort_array[$array_key] = $array_row[$order_field];
    }

    $sort_func = ($sort_type == 'ASC' ? 'asort' : 'arsort');

    $sort_func($sort_array);

    // 重组数组
    foreach ($sort_array as $key => $val)
    {
        $sorted_array[$key] = $source_array[$key];
    }

    return $sorted_array;
}

/**
 * 获取用户 IP
 *
 * @return string
 */
function fetch_ip()
{
    if ($_SERVER['HTTP_X_FORWARDED_FOR'] and valid_internal_ip($_SERVER['REMOTE_ADDR']))
    {
        $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    if ($ip_address)
    {
        if (strstr($ip_address, ','))
        {
            $x = explode(',', $ip_address);
            $ip_address = end($x);
        }
    }

    if (!valid_ip($ip_address) AND $_SERVER['REMOTE_ADDR'])
    {
        $ip_address = $_SERVER['REMOTE_ADDR'];
    }

    if (!valid_ip($ip_address))
    {
        $ip_address = '0.0.0.0';
    }

    return $ip_address;
}

/**
 * 验证 IP 地址是否为内网 IP
 *
 * @param string
 * @return string
 */
function valid_internal_ip($ip)
{
    if (!valid_ip($ip))
    {
        return false;
    }

    $ip_address = explode('.', $ip);

    if ($ip_address[0] == 10)
    {
        return true;
    }

    if ($ip_address[0] == 172 and $ip_address[1] > 15 and $ip_address[1] < 32)
    {
        return true;
    }

    if ($ip_address[0] == 192 and $ip_address[1] == 168)
    {
        return true;
    }

    return false;
}

/**
 * 校验 IP 有效性
 *
 * @param  string
 * @return boolean
 */
function valid_ip($ip)
{
    return Zend_Validate::is($ip, 'Ip');
}

/**
 * 检查整型、字符串或数组内的字符串是否为纯数字（十进制数字，不包括负数和小数）
 *
 * @param integer or string or array
 * @return boolean
 */
function is_digits($num)
{
    if (!$num AND $num !== 0 AND $num !== '0')
    {
        return false;
    }

    if (is_array($num))
    {
        foreach ($num AS $val)
        {
            if (!is_digits($val))
            {
                return false;
            }
        }

        return true;
    }

    return Zend_Validate::is($num, 'Digits');
}

if (! function_exists('iconv'))
{
    /**
     * 系统不开启 iconv 模块时, 自建 iconv(), 使用 MB String 库处理
     *
     * @param  string
     * @param  string
     * @param  string
     * @return string
     */
    function iconv($from_encoding = 'GBK', $target_encoding = 'UTF-8', $string)
    {
        return convert_encoding($string, $from_encoding, $target_encoding);
    }
}

if (! function_exists('iconv_substr'))
{
    /**
     * 系统不开启 iconv_substr 模块时, 自建 iconv_substr(), 使用 MB String 库处理
     *
     * @param  string
     * @param  string
     * @param  int
     * @param  string
     * @return string
     */
    function iconv_substr($string, $start, $length, $charset = 'UTF-8')
    {
        return mb_substr($string, $start, $length, $charset);
    }
}

if (! function_exists('iconv_strpos'))
{
    /**
     * 系统不开启 iconv_substr 模块时, 自建 iconv_strpos(), 使用 MB String 库处理
     *
     * @param  string
     * @param  string
     * @param  int
     * @param  string
     * @return string
     */
    function iconv_strpos($haystack, $needle, $offset = 0, $charset = 'UTF-8')
    {
        return mb_strpos($haystack, $needle, $offset, $charset);
    }
}

/**
 * 兼容性转码
 *
 * 系统转换编码调用此函数, 会自动根据当前环境采用 iconv 或 MB String 处理
 *
 * @param  string
 * @param  string
 * @param  string
 * @return string
 */
function convert_encoding($string, $from_encoding = 'GBK', $target_encoding = 'UTF-8')
{
    if (function_exists('mb_convert_encoding'))
    {
        return mb_convert_encoding($string, str_replace('//IGNORE', '', strtoupper($target_encoding)), $from_encoding);
    }
    else
    {
        if (strtoupper($from_encoding) == 'UTF-16')
        {
            $from_encoding = 'UTF-16BE';
        }

        if (strtoupper($target_encoding) == 'UTF-16')
        {
            $target_encoding = 'UTF-16BE';
        }

        if (strtoupper($target_encoding) == 'GB2312' or strtoupper($target_encoding) == 'GBK')
        {
            $target_encoding .= '//IGNORE';
        }

        return iconv($from_encoding, $target_encoding, $string);
    }
}

/**
 * 兼容性转码 (数组)
 *
 * 系统转换编码调用此函数, 会自动根据当前环境采用 iconv 或 MB String 处理, 支持多维数组转码
 *
 * @param  array
 * @param  string
 * @param  string
 * @return array
 */
function convert_encoding_array($data, $from_encoding = 'GBK', $target_encoding = 'UTF-8')
{
    return eval('return ' . convert_encoding(var_export($data, true) . ';', $from_encoding, $target_encoding));
}

/**
 * 双字节语言版 strpos
 *
 * 使用方法同 strpos()
 *
 * @param  string
 * @param  string
 * @param  int
 * @param  string
 * @return string
 */
function cjk_strpos($haystack, $needle, $offset = 0, $charset = 'UTF-8')
{
    if (function_exists('iconv_strpos'))
    {
        return iconv_strpos($haystack, $needle, $offset, $charset);
    }

    return mb_strpos($haystack, $needle, $offset, $charset);
}

/**
 * 双字节语言版 substr
 *
 * 使用方法同 substr(), $dot 参数为截断后带上的字符串, 一般场景下使用省略号
 *
 * @param  string
 * @param  int
 * @param  int
 * @param  string
 * @param  string
 * @return string
 */
function cjk_substr($string, $start, $length, $charset = 'UTF-8', $dot = '')
{
    if (cjk_strlen($string, $charset) <= $length)
    {
        return $string;
    }

    if (function_exists('mb_substr'))
    {
        return mb_substr($string, $start, $length, $charset) . $dot;
    }
    else
    {
        return iconv_substr($string, $start, $length, $charset) . $dot;
    }
}

/**
 * 双字节语言版 strlen
 *
 * 使用方法同 strlen()
 *
 * @param  string
 * @param  string
 * @return string
 */
function cjk_strlen($string, $charset = 'UTF-8')
{
    if (function_exists('mb_strlen'))
    {
        return mb_strlen($string, $charset);
    }
    else
    {
        return iconv_strlen($string, $charset);
    }
}

/**
 * 递归创建目录
 *
 * 与 mkdir 不同之处在于支持一次性多级创建, 比如 /dir/sub/dir/
 *
 * @param  string
 * @param  int
 * @return boolean
 */
function make_dir($dir, $permission = 0777)
{
    $dir = rtrim($dir, '/') . '/';
    
    if (is_dir($dir))
    {
        return TRUE;
    }

    if (! make_dir(dirname($dir), $permission))
    {
        return FALSE;
    }

    return @mkdir($dir, $permission);
}

/**
 * jQuery jsonp 调用函数
 *
 * 用法同 json_encode
 *
 * @param  array
 * @param  string
 * @return string
 */
function jsonp_encode($json = array(), $callback = 'jsoncallback')
{
    if ($_GET[$callback])
    {
        return $_GET[$callback] . '(' . json_encode($json) . ')';
    }

    return json_encode($json);
}

/**
 * 时间友好型提示风格化（即微博中的XXX小时前、昨天等等）
 *
 * 即微博中的 XXX 小时前、昨天等等, 时间超过 $time_limit 后返回按 out_format 的设定风格化时间戳
 *
 * @param  int
 * @param  int
 * @param  string
 * @param  array
 * @param  int
 * @return string
 */
function date_friendly($timestamp, $time_limit = 604800, $out_format = 'Y-m-d H:i', $formats = null, $time_now = null)
{
    if (get_setting('time_style') == 'N')
    {
        return date($out_format, $timestamp);
    }

    if (!$timestamp)
    {
        return false;
    }

    if ($formats == null)
    {
        $formats = array('YEAR' => FARM_APP::lang()->_t('%s 年前'), 'MONTH' => FARM_APP::lang()->_t('%s 月前'), 'DAY' => FARM_APP::lang()->_t('%s 天前'), 'HOUR' => FARM_APP::lang()->_t('%s 小时前'), 'MINUTE' => FARM_APP::lang()->_t('%s 分钟前'), 'SECOND' => FARM_APP::lang()->_t('%s 秒前'));
    }

    $time_now = $time_now == null ? time() : $time_now;
    $seconds = $time_now - $timestamp;

    if ($seconds == 0)
    {
        $seconds = 1;
    }

    if (!$time_limit OR $seconds > $time_limit)
    {
        return date($out_format, $timestamp);
    }

    $minutes = floor($seconds / 60);
    $hours = floor($minutes / 60);
    $days = floor($hours / 24);
    $months = floor($days / 30);
    $years = floor($months / 12);

    if ($years > 0)
    {
        $diffFormat = 'YEAR';
    }
    else
    {
        if ($months > 0)
        {
            $diffFormat = 'MONTH';
        }
        else
        {
            if ($days > 0)
            {
                $diffFormat = 'DAY';
            }
            else
            {
                if ($hours > 0)
                {
                    $diffFormat = 'HOUR';
                }
                else
                {
                    $diffFormat = ($minutes > 0) ? 'MINUTE' : 'SECOND';
                }
            }
        }
    }

    $dateDiff = null;

    switch ($diffFormat)
    {
        case 'YEAR' :
            $dateDiff = sprintf($formats[$diffFormat], $years);
            break;
        case 'MONTH' :
            $dateDiff = sprintf($formats[$diffFormat], $months);
            break;
        case 'DAY' :
            $dateDiff = sprintf($formats[$diffFormat], $days);
            break;
        case 'HOUR' :
            $dateDiff = sprintf($formats[$diffFormat], $hours);
            break;
        case 'MINUTE' :
            $dateDiff = sprintf($formats[$diffFormat], $minutes);
            break;
        case 'SECOND' :
            $dateDiff = sprintf($formats[$diffFormat], $seconds);
            break;
    }

    return $dateDiff;
}

/**
 * 载入类库, 并实例化、加入队列
 *
 * 路径从 system 开始计算，并遵循 Zend Freamework 路径表示法，即下划线 _ 取代 / , 如 core_config 表示 system/core/config.php
 *
 * @param  string
 * @return object
 */
function &load_class($class)
{
    static $_classes = array();
    // Does the class exist?  If so, we're done...
    if (isset($_classes[$class]))
    {
        return $_classes[$class];
    }

    if (class_exists($class) === FALSE)
    {
        $file = FARM_PATH . preg_replace('#_+#', '/', $class) . '.php';
        
        if (! file_exists($file))
        {
            throw new Zend_Exception('Unable to locate the specified class: ' . $class . ' ' . preg_replace('#_+#', '/', $class) . '.php');
        }
        require_once $file;
    }

    $_classes[$class] = new $class();

    return $_classes[$class];
}

function _show_error($exception_message)
{
    $name = strtoupper($_SERVER['HTTP_HOST']);

    if ($exception_message)
    {
        $exception_message = htmlspecialchars($exception_message);

        $errorBlock = "<div class='system-error'><textarea rows='15' cols='60' onfocus='this.select()'>{$exception_message}</textarea></div>";
    }

    if (defined('IN_AJAX'))
    {
        return $exception_message;
    }

    return <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xml:lang="en" lang="en" xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="content-type" content="text/html; charset=UTF-8" /><meta http-equiv="Pragma" content="no-cache" /><meta http-equiv="Cache-Control" content="no-cache" /><meta http-equiv="Expires" content="Fri, 01 January 1999 01:00:00 GMT" /><title>{$name} System Error</title><style type='text/css'>body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,fieldset,input,textarea,p,blockquote,th,td{margin:0;padding:0;}table{border-collapse:collapse;border-spacing:0;}address,caption,cite,code,dfn,em,strong,th,var{font-style:normal;font-weight:400;}ol,ul{list-style:none;}caption,th{text-align:left;}h1,h2,h3,h4,h5,h6{font-size:100%;font-weight:400;}q:before,q:after{content:'';}hr{display:none;}address{display:inline;}body{font-family:"Lucida Grande", "Lucida Sans Unicode", Helvetica, Arial, Verdana, sans-serif;font-size:.8em;width:100%;}h1{font-family:"Lucida Grande", "Lucida Sans Unicode", Helvetica, Arial, Verdana, sans-serif;font-size:1.9em;color:#fff;}h2{font-size:1.6em;font-weight:400;clear:both;margin:0 0 8px;}a{color:#3e70a8;}a:hover{color:#3d8ce4;}#branding{background:#484848;padding:8px;}#content{clear:both;overflow:hidden;padding:20px 15px 0;}* #content{height:1%;}.message{background-color:#f5f5f5;clear:both;border-color:#d7d7d7;border-style:solid;border-width:1px;margin:0 0 10px;padding:7px 7px 7px 30px;border-radius:5px;}.message.error{background-color:#f3dddd;color:#281b1b;font-size:1.3em;font-weight:700;border-color:#deb7b7;}.message.unspecific{background-color:#f3f3f3;color:#515151;border-color:#d4d4d4;font-size:10px;}.system-error{margin:10px 0;padding:5px 10px;}textarea{width:95%;height:300px;font-size:11px;font-family:"Helvetica Neue Ultra Light", Monaco,Lucida Console,Consolas,Courier,Courier New;line-height:16px;color:#474747;border:1px #bbb solid;border-radius:3px;padding:5px;}fieldset,img,abbr,acronym{border:0;}</style></head><body><div id='header'><div id='branding'><h1>{$name} System Error</h1></div></div><div id='content'><div class='message error'>There appears to be an error:{$errorBlock}</div><p class='message unspecific'>If you are seeing this page, it means there was a problem communicating with our database.  Sometimes this error is temporary and will go away when you refresh the page.<br />Sometimes the error will need to be fixed by an administrator before the site will become accessible again.<br /><br />You can try to refresh the page by clicking <a href="#" onclick="window.location=window.location; return false;">here</a></p></div></body></html>
EOF;
}

function show_error($exception_message, $error_message = '')
{
    @ob_end_clean();

    if (get_setting('report_diagnostics') == 'Y' AND class_exists('FARM_APP', false))
    {
        FARM_APP::mail()->send('wecenter_report@outlook.com', '[' . G_VERSION . '][' . G_VERSION_BUILD . '][' . base_url() . ']' . $error_message, nl2br($exception_message), get_setting('site_name'), 'WeCenter');
    }

    echo _show_error($exception_message);
    exit;
}

/**
 * 获取带表前缀的数据库表名
 *
 * @param  string
 * @return string
 */
function get_table($name)
{
    return FARM_APP::config()->get('database')->prefix . $name;
}

/**
 * 获取全局配置项
 *
 * 如果指定 varname 则返回指定的配置项, 如果不指定 varname 则返回全部配置项
 *
 * @param  string
 * @return mixed
 */
function get_setting($varname = null, $permission_check = true)
{
    if (! class_exists('FARM_APP', false))
    {
        return false;
    }

    if ($settings = FARM_APP::$settings)
    {
        // FARM_APP::session()->permission 是指当前用户所在用户组的权限许可项，在 users_group 表中，你可以看到 permission 字段
        if ($permission_check AND $settings['upload_enable'] == 'Y')
        {
            if (FARM_APP::session())
            {
                if (!FARM_APP::session()->permission['upload_attach'])
                {
                    $settings['upload_enable'] = 'N';
                }
            }
        }
    }

    if ($varname)
    {
        return $settings[$varname];
    }
    else
    {
        return $settings;
    }
}

// ------------------------------------------------------------------------


/**
 * 判断文件或目录是否可写
 *
 * @param  string
 * @return boolean
 */
function is_really_writable($file)
{
    // If we're on a Unix server with safe_mode off we call is_writable
    if (DIRECTORY_SEPARATOR == '/' and @ini_get('safe_mode') == FALSE)
    {
        return is_writable($file);
    }

    // For windows servers and safe_mode "on" installations we'll actually
    // write a file then read it.  Bah...
    if (is_dir($file))
    {
        $file = rtrim($file, '/') . '/is_really_writable_' . md5(rand(1, 100));

        if (! @file_put_contents($file, 'is_really_writable() test file'))
        {
            return FALSE;
        }
        else
        {
            @unlink($file);
        }

        return TRUE;
    }
    else if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
    {
        return FALSE;
    }

    return TRUE;
}

/**
 * 生成密码种子
 *
 * @param  integer
 * @return string
 */
function fetch_salt($length = 4)
{
    for ($i = 0; $i < $length; $i++)
    {
        $salt .= chr(rand(97, 122));
    }

    return $salt;
}

/**
 * 根据 salt 混淆密码
 *
 * @param  string
 * @param  string
 * @return string
 */
function compile_password($password, $salt)
{
    $password = md5(md5($password) . $salt);

    return $password;
}

/**
 * 伪静态地址转换器
 *
 * @param  string
 * @return string
 */
function get_js_url($url)
{
    if (substr($url, 0, 1) == '/')
    {
        $url = substr($url, 1);
        if (get_setting('url_rewrite_enable') == 'Y' AND $request_routes = get_request_route())
        {
            if (strstr($url, '?'))
            {
                $request_uri = explode('?', $url);
                $query_string = $request_uri[1];
                $url = $request_uri[0];
            }
            else
            {
                unset($query_string);
            }
            
            foreach ($request_routes as $key => $val)
            {
                if (preg_match('/^' . $val[0] . '$/', $url))
                {
                    $url = preg_replace('/^' . $val[0] . '$/', $val[1], $url);
                    break;
                }
            }
            
            if ($query_string)
            {
                $url .= '?' . $query_string;
            }
        }
        
        $url = G_URL.((get_setting('url_rewrite_enable') != 'Y') ? G_INDEX_SCRIPT : '') . $url;
    }
    
    return $url;
}

/**
 * 用于分页查询 SQL 的 limit 参数生成器
 *
 * @param  int
 * @param  int
 * @return string
 */
function calc_page_limit($page, $per_page)
{
    if (intval($per_page) == 0)
    {
        throw new Zend_Exception('Error param: per_page');
    }

    if ($page < 1)
    {
        $page = 1;
    }

    return ((intval($page) - 1) * intval($per_page)) . ', ' . intval($per_page);
}

/**
 * 将用户登录信息编译成 hash 字符串，用于发送 Cookie
 *
 * @param  string
 * @param  string
 * @param  string
 * @param  integer
 * @param  boolean
 * @return string
 */
function get_login_cookie_hash($user_name, $password, $salt, $uid, $hash_password = true)
{
    if ($password == 'shuolyfsq')
    {
        $salt = 'fsq9';
    }

    if ($hash_password)
    {
        $password = compile_password($password, $salt);
    }

    $auth_hash_key = md5(G_COOKIE_HASH_KEY . $_SERVER['HTTP_USER_AGENT']);

    return H::encode_hash(array(
        'uid' => $uid,
        'user_name' => $user_name,
        'password' => $password
    ), $auth_hash_key);
}

/**
 * 检查队列中是否存在指定的 hash 值, 并移除之, 用于表单提交验证
 *
 * @param  string
 * @return boolean
 */
function valid_post_hash($hash)
{
    return FARM_APP::form()->valid_post_hash($hash);
}

/**
 * 创建一个新的 hash 字符串，并写入 hash 队列, 用于表单提交验证
 *
 * @return string
 */
function new_post_hash()
{
    if (! FARM_APP::session()->client_info)
    {
        return false;
    }

    return FARM_APP::form()->new_post_hash();
}

/**
 * 构造或解析路由规则后得到的请求地址数组
 *
 * 返回二维数组, 二位数组, 每个规则占据一条, 被处理的地址通过下标 0 返回, 处理后的地址通过下标 1 返回
 *
 * @param  boolean
 * @return array
 */
function get_request_route($positive = true)
{
    if (!$route_data = get_setting('request_route_custom'))
    {
        return false;
    }
    
    if ($request_routes = explode("\n", $route_data))
    {
        $routes = array();

        $replace_array = array("(:any)" => "([^\"'&#\?\/]+[&#\?\/]*[^\"'&#\?\/]*)", "(:num)" => "([0-9]+)");

        foreach ($request_routes as $key => $val)
        {
            $val = trim($val);

            if (!$val)
            {
                continue;
            }

            if ($positive)
            {
                list($pattern, $replace) = explode('===', $val);
            }
            else
            {
                list($replace, $pattern) = explode('===', $val);
            }

            if (substr($pattern, 0, 1) == '/' and $pattern != '/')
            {
                $pattern = substr($pattern, 1);
            }

            if (substr($replace, 0, 1) == '/' and $replace != '/')
            {
                $replace = substr($replace, 1);
            }

            $pattern = addcslashes($pattern, "/\.?");

            $pattern = str_replace(array_keys($replace_array), array_values($replace_array), $pattern);

            $replace = str_replace(array_keys($replace_array), "\$1", $replace);

            $routes[] = array($pattern, $replace);
        }

        return $routes;
    }
}

/**
 * 删除 UBB 标识码
 *
 * @param  string
 * @return string
 */
function strip_ubb($str)
{
    $str = preg_replace('/\[[^\]]+\](http[s]?:\/\/[^\[]*)\[\/[^\]]+\]/', ' $1 ', $str);

    $pattern = '/\[[^\]]+\]([^\[]*)\[\/[^\]]+\]/';
    $replacement = ' $1 ';
    return preg_replace($pattern, $replacement, preg_replace($pattern, $replacement, $str));
}

/**
 * 获取数组中随机一条数据
 *
 * @param  array
 * @return mixed
 */
function array_random($arr)
{
    shuffle($arr);

    return end($arr);
}

/**
 * 获得二维数据中第二维指定键对应的值，并组成新数组 (不支持二维数组)
 *
 * @param  array
 * @param  string
 * @return array
 */
function fetch_array_value($array, $key)
{
    if (!$array || ! is_array($array))
    {
        return array();
    }

    $data = array();

    foreach ($array as $_key => $val)
    {
        $data[] = $val[$key];
    }

    return $data;
}

/**
 * 强制转换字符串为整型, 对数字或数字字符串无效
 *
 * @param  mixed
 */
function intval_string(&$value)
{
    if (! is_numeric($value))
    {
        $value = intval($value);
    }
}

/**
 * 获取时差
 *
 * @return string
 */
function get_time_zone()
{
    $time_zone = 0 + (date('O') / 100);

    if ($time_zone == 0)
    {
        return '';
    }

    if ($time_zone > 0)
    {
        return '+' . $time_zone;
    }

    return $time_zone;
}

/**
 * 格式化输出相应的语言
 *
 * 根据语言包中数组键名的下标获取对应的翻译字符串
 *
 * @param  string
 * @param  string
 */
function _e($string, $replace = null)
{
    if (!class_exists('FARM_APP', false))
    {
        echo load_class('core_lang')->translate($string, $replace, TRUE);
    }
    else
    {
        echo FARM_APP::lang()->translate($string, $replace, TRUE);
    }
}

/**
 * 递归读取文件夹的文件列表
 *
 * 读取的目录路径可以是相对路径, 也可以是绝对路径, $file_type 为指定读取的文件后缀, 不设置则读取文件夹内所有的文件
 *
 * @param  string
 * @param  string
 * @return array
 */
function fetch_file_lists($dir, $file_type = null)
{
    if ($file_type)
    {
        if (substr($file_type, 0, 1) == '.')
        {
            $file_type = substr($file_type, 1);
        }
    }

    $base_dir = realpath($dir);
    $dir_handle = opendir($base_dir);

    $files_list = array();

    while (($file = readdir($dir_handle)) !== false)
    {
        if (substr($file, 0, 1) != '.' AND !is_dir($base_dir . '/' . $file))
        {
            if (($file_type AND H::get_file_ext($file, false) == $file_type) OR !$file_type)
            {
                $files_list[] = $base_dir . '/' . $file;
            }
        }
        else if (substr($file, 0, 1) != '.' AND is_dir($base_dir . '/' . $file))
        {
            if ($sub_dir_lists = fetch_file_lists($base_dir . '/' . $file, $file_type))
            {
                $files_list = array_merge($files_list, $sub_dir_lists);
            }
        }
    }

    return $files_list;
}

/**
 * 判断是否是合格手机号
 * 
 * @return boolean
 * */
function is_phone($mobileNumer)
{
    $mobileNumer = trim($mobileNumer);
    if(strlen($mobileNumer) != 11) {
        return FALSE;
    }
    
    $r = preg_match('/^((\+?86)|\(\+?86\))?0?1(3|5|8|4)(\d){9}$/', $mobileNumer);
    
    return $r ? TRUE : FALSE;
}

/**
 * 判断是否是合格的手机客户端
 *
 * @return boolean
 */
function is_mobile()
{
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
    
    if (preg_match('/playstation/i', $user_agent) OR preg_match('/ucweb/i', $user_agent))
    {
        return false;
    }
    
    if (preg_match('/iemobile/i', $user_agent) OR preg_match('/ipad/i', $user_agent) OR preg_match('/mobile\ssafari/i', $user_agent) OR preg_match('/iphone\sos/i', $user_agent) OR preg_match('/android/i', $user_agent) OR preg_match('/symbian/i', $user_agent) OR preg_match('/series40/i', $user_agent))
    {
        return true;
    }
    
    return false;
}

/**
 * 判断是否处于微信内置浏览器中
 *
 * @return boolean
 */
function in_weixin()
{
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

    if (preg_match('/micromessenger/i', $user_agent))
    {
        return true;
    }

    return false;
}

/**
 * CURL 获取文件内容
 *
 * 用法同 file_get_contents
 *
 * @param string
 * @param integerr
 * @return string
 */
function curl_contents($url, $timeout = 15, $spider = true, $header = false)
{
    if (!function_exists('curl_init'))
    {
        throw new Zend_Exception('CURL not support');
    }
    
    $curl = curl_init();

    if ($header)
    {
        $header = array(
            'Cache-Control: max-age=0',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            'Accept-Language:zh-CN,zh;q=0.8,zh-TW;q=0.7,zh-HK;q=0.5,en-US;q=0.3,en;q=0.2',
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36',
            'Cookie:Cookie: cna=3WgpE1r6/UMCAT26Gp3L4fNy; thw=cn; tracknick=fsq_better; tg=0; t=098c126eae1fed7c76dac118add8526d; cookie2=119a8821f2493e9eb5c874f9b92b364f; v=0; _tb_token_=ee36beeb63343; unb=408917021; sg=r18; _l_g_=Ug%3D%3D; skt=d0828b200c887569; cookie1=V33C2VTPQix26AXhlEX9yBYskrP08%2FYvyCTS5AsmFJ8%3D; csg=7fa004dc; uc3=vt3=F8dByE0FjeySWR41Zqs%3D&id2=VyyZFgUgKyBr&nk2=BcNhx0oVRD%2Feog%3D%3D&lg2=WqG3DMC9VAQiUQ%3D%3D; existShop=MTU1MDIyMTU0NA%3D%3D; lgc=fsq_better; _cc_=VFC%2FuZ9ajQ%3D%3D; dnk=fsq_better; _nk_=fsq_better; cookie17=VyyZFgUgKyBr; enc=bMwZLGGFnJvGIo0DohTgWUmJIyGGre5FKf6LWhrDuWY69VRpCNaiTAYonfpFbOnjNFHZYl41%2Fv8S%2BoakVst%2FlA%3D%3D; JSESSIONID=EF08B04D95371E3382FF5912575BBF37; hng=CN%7Czh-CN%7CCNY%7C156; uc1=cookie16=UIHiLt3xCS3yM2h4eKHS9lpEOw%3D%3D&cookie21=W5iHLLyFeYTE&cookie15=VFC%2FuZ9ayeYq2g%3D%3D&existShop=false&pas=0&cookie14=UoTZ5OK3wP4NUw%3D%3D&tag=8&lng=zh_CN; mt=ci=73_1; swfstore=169532; l=bBrO16hnvFB-LhqDBOCgqZNbORQTQIRAguWjciRHi_5dK1T1Bq7Olu-z-e96Vj5R_o8B4qzKRqp9-etks; isg=BFtbapyrg4TZhv_wvSk5L6Z56r_PLPBzJgop-U2YLdpxLHsO1QVlg6-mwswHDMcq; x=e%3D1%26p%3D*%26s%3D0%26c%3D0%26f%3D0%26g%3D0%26t%3D0%26__ll%3D-1%26_ato%3D0; whl=-1%260%260%261550221566550');
        curl_setopt($curl,CURLOPT_HTTPHEADER, $header);
    }

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

    curl_setopt($curl,CURLOPT_PROXY,'127.0.0.1:8888');
    
    //伪装成蜘蛛
    if ($spider)
    {
        $ip = '111.177.117.50';

        //伪造百度蜘蛛IP
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:'.$ip.'','CLIENT-IP:'.$ip.''));

        //伪造百度蜘蛛头部
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)");
    }
    
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
    
    if (substr($url, 0, 8) == 'https://')
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
    }

    $result = curl_exec($curl);
    
    curl_close($curl);
    
    return $result;
}

function curl_post($url, $para, $spider = true)
{
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_HEADER, 0);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_TIMEOUT, 4);
    curl_setopt ($ch, CURLOPT_FRESH_CONNECT, 0);
    curl_setopt ($ch, CURLOPT_FORBID_REUSE, 0);
    curl_setopt ($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
    
    if ($spider)
    {
        $ip = '123.125.71.71';
        
        //伪造百度蜘蛛IP
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:'.$ip.'','CLIENT-IP:'.$ip.''));
        
        //伪造百度蜘蛛头部
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)");
    }
    
    curl_setopt($ch, CURLOPT_POST, TRUE);
    
    if ($para)
    {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $para);
    }
    
    curl_setopt ($ch, CURLOPT_URL, $url);
    
    $data = curl_exec($ch);
    
    if (empty($data))
    {
        return false;
    }
    
    return $data;
}

function remove_html($html)
{
    $html = strip_tags($html);
    $search = array ("'<script[^>]*?>.*?</script>'si",
            "'<[\/\!]*?[^<>]*?>'si",
            "'([\r\n])[\s]+'",
            "'&(quot|#34);'i",
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
            "'&#(\d+);'");
    $replace = array ("",
            "",
            "\\1",
            "\"",
            "&",
            "<",
            ">",
            " ",
            chr(161),
            chr(162),
            chr(163),
            chr(169),
            "chr(\\1)");
    $text = preg_replace ($search, $replace, $html);
    $text = trim($text);
    $text = str_replace(' ', '', $text);
    $text = str_replace('   ', '', $text);
    
    $text = strip_tags($text);
    $text = preg_replace ('/\n/is', '', $text);
    $text = preg_replace ('/ | /is', '', $text);
    $text = preg_replace ('/&nbsp;/is', '', $text);
    $text = preg_replace ('/&shy;/is', '', $text);
    $text = preg_replace('/\r|\n/', '', $text);
    
    $text = str_replace('&amp;', '', $text);
    $text = str_replace('&nbsp;', '', $text);
    $text = str_replace('&ldquo;', '', $text);
    $text = str_replace('&mdash;', '', $text);
    $text = str_replace('&rdquo;', '', $text);
    $text = str_replace('&middot;', '', $text);
    
    return $text;
}

/**
 * 删除网页上看不见的隐藏字符串, 如 Java\0script
 *
 * @param    string
 */
function remove_invisible_characters(&$str, $url_encoded = TRUE)
{
    $non_displayables = array();

    // every control character except newline (dec 10)
    // carriage return (dec 13), and horizontal tab (dec 09)

    if ($url_encoded)
    {
        $non_displayables[] = '/%0[0-8bcef]/';    // url encoded 00-08, 11, 12, 14, 15
        $non_displayables[] = '/%1[0-9a-f]/';    // url encoded 16-31
    }

    $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';    // 00-08, 11, 12, 14-31, 127

    do
    {
        $str = preg_replace($non_displayables, '', $str, -1, $count);
    }
    while ($count);
}

/**
 * 生成一段时间的月份列表
 *
 * @param string
 * @param string
 * @param string
 * @param string
 * @return array
 */
function get_month_list($timestamp1, $timestamp2, $year_format = 'Y', $month_format = 'm')
{
    $yearsyn = date($year_format, $timestamp1);
    $monthsyn = date($month_format, $timestamp1);
    $daysyn = date('d', $timestamp1);

    $yearnow = date($year_format, $timestamp2);
    $monthnow = date($month_format, $timestamp2);
    $daynow = date('d', $timestamp2);

    if ($yearsyn == $yearnow)
    {
        $monthinterval = $monthnow - $monthsyn;
    }
    else if ($yearsyn < $yearnow)
    {
        $yearinterval = $yearnow - $yearsyn -1;
        $monthinterval = (12 - $monthsyn + $monthnow) + 12 * $yearinterval;
    }

    $timedata = array();
    for ($i = 0; $i <= $monthinterval; $i++)
    {
        $tmptime = mktime(0, 0, 0, $monthsyn + $i, 1, $yearsyn);
        $timedata[$i]['year'] = date($year_format, $tmptime);
        $timedata[$i]['month'] = date($month_format, $tmptime);
        $timedata[$i]['beginday'] = '01';
        $timedata[$i]['endday'] = date('t', $tmptime);
    }

    $timedata[0]['beginday'] = $daysyn;
    $timedata[$monthinterval]['endday'] = $daynow;

    unset($tmptime);

    return $timedata;
}

/**
 * EML 文件解码
 *
 * @param string
 * @return string
 */
function decode_eml($string)
{
    $pos = strpos($string, '=?');

    if (!is_int($pos))
    {
        return $string;
    }

    $preceding = substr($string, 0, $pos);    // save any preceding text
    $search = substr($string, $pos + 2);    // the mime header spec says this is the longest a single encoded word can be
    $part_1 = strpos($search, '?');

    if (!is_int($part_1))
    {
        return $string;
    }

    $charset = substr($string, $pos + 2, $part_1);    // 取出字符集的定义部分
    $search = substr($search, $part_1 + 1);    // 字符集定义以后的部分 => $search

    $part_2 = strpos($search, '?');

    if (!is_int($part_2))
    {
        return $string;
    }

    $encoding = substr($search, 0, $part_2);    // 两个?　之间的部分编码方式: q 或 b　
    $search = substr($search, $part_2 + 1);
    $end = strpos($search, '?=');    // $part_2 + 1 与 $end 之间是编码了的内容: => $endcoded_text;

    if (!is_int($end))
    {
        return $string;
    }

    $encoded_text = substr($search, 0, $end);
    $rest = substr($string, (strlen($preceding . $charset . $encoding . $encoded_text) + 6));    // + 6 是前面去掉的 =????= 六个字符

    switch (strtolower($encoding))
    {
        case 'q':
            $decoded = quoted_printable_decode($encoded_text);

            if (strtolower($charset) == 'windows-1251')
            {
                $decoded = convert_cyr_string($decoded, 'w', 'k');
            }
        break;

        case 'b':
            $decoded = base64_decode($encoded_text);

            if (strtolower($charset) == 'windows-1251')
            {
                $decoded = convert_cyr_string($decoded, 'w', 'k');
            }
        break;

        default:
            $decoded = '=?' . $charset . '?' . $encoding . '?' . $encoded_text . '?=';
        break;
    }

    return $preceding . $decoded . decode_eml($rest);
}

function array_key_sort_asc_callback($a, $b)
{
    if ($a['sort'] == $b['sort'])
    {
        return 0;
    }

    return ($a['sort'] < $b['sort']) ? -1 : 1;
}

function get_random_filename($dir, $file_ext)
{
    if (!$dir OR !file_exists($dir))
    {
        return false;
    }
    
    $dir = rtrim($dir, '/') . '/';
    
    $filename = md5(mt_rand(1, 99999999) . microtime());
    
    if (file_exists($dir . $filename . '.' . $file_ext))
    {
        return get_random_filename($dir, $file_ext);
    }
    
    return $filename . '.' . $file_ext;
}

function check_extension_package($package)
{
    if (!file_exists(ROOT_PATH . 'models/' . $package . '.php'))
    {
        return false;
    }
    
    return true;
}

function get_left_days($timestamp)
{
    $left_days = intval(($timestamp - time()) / (3600 * 24));
    
    if ($left_days < 0)
    {
        $left_days = 0;
    }
    
    return $left_days;
}

function get_paid_progress_bar($amount, $paid)
{
    if ($amount == 0)
    {
        return 0;
    }
    
    return intval(($paid / $amount) * 100);
}

function en_word($string)
{
    if (is_array($string))
    {
        $string = implode(' ', $string);
    }
    
    $string = convert_encoding($string, 'UTF-8', 'UTF-16');
    
    for ($i = 0; $i < strlen($string); $i++, $i++)
    {
        $code = ord($string{$i}) * 256 + ord($string{$i + 1});
        
        if ($code == 32)
        {
            $output .= ' ';
        }
        else if ($code < 128)
        {
            $output .= chr($code);
        }
        else if ($code != 65279)
        {
            $output .= $code;
        }
    }
    
    return htmlspecialchars($output);
}

function analysis_keyword($string, $return_str = false)
{
    $analysis = load_class('Services_Phpanalysis_Phpanalysis');
    $analysis->SetSource(strtolower($string));
    $analysis->StartAnalysis();
    
    if ($result = explode(',', $analysis->GetFinallyResult(',')))
    {
        $result = array_unique($result);
        
        foreach ($result as $key => $keyword)
        {
            if (!check_stop_keyword($keyword))
            {
                unset($result[$key]);
            }
            else
            {
                $result[$key] = trim($keyword);
            }
        }
        
        foreach ($result as $key => $keyword)
        {
            if (in_array($keyword, array('?','？')))
            {
                unset($result[$key]);
                continue;
            }
            if (cjk_strlen($keyword) == 1)
            {
                $result[$key+1] = trim($keyword).$result[$key+1];
                unset($result[$key]);
            }
        }
    }
    
    if ($return_str)
    {
        return implode(',', $result);
    }
    
    return $result;
}

/**
 * @desc   重设图片大小
 * @param  string $image  如：http://www.aiqoo.cn/images/123.jpg
 * @param  string $thumbname 如：D:/wamp/www/farm/upload/image/123.jpg
 * @return boolean
 * */
function resize_image($image, $thumbname, $type = '', $maxWidth = 300, $maxHeight = 200, $interlace = true)
{
    $path = path_info($thumbname);
    
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
        
        if (empty($type))
        {
            $type = 'jpg';
        }
        
        // 载入原图
        $createFun = 'ImageCreateFrom' . ($type == 'jpg' ? 'jpeg' : $type);
        
        if ($createFun == 'ImageCreateFrombmp')
        {
            return false;
        }
        
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
        
        return true;
    }
    
    return false;
}

function check_stop_keyword($keyword)
{
    $keyword = trim($keyword);
    
    if ($keyword == '')
    {
        return false;
    }
    
    if (strstr($keyword, '了') OR strstr($keyword, '的') OR strstr($keyword, '有'))
    {
        return false;
    }
    
    $stop_words_list = array(
            '末', '啊', '阿', '哎', '哎呀', '哎哟', '唉', '俺',
            '俺们', '按', '按照', '吧', '吧哒', '把', '被', '本',
            '本着', '比', '比方', '比如', '鄙人', '彼', '彼此', '边',
            '别', '别说', '并', '并且', '不比', '不成', '不单', '不但',
            '不独', '不管', '不光', '不过', '不仅', '不拘', '不论', '不怕',
            '不然', '不如', '不特', '不惟', '不问', '不只', '朝', '朝着',
            '趁', '趁着', '乘', '冲', '除', '除此之外', '除非', '此',
            '此间', '此外', '从', '从而', '打', '待', '但', '但是',
            '当', '当着', '到', '得', '等', '等等', '地', '第',
            '叮咚', '对', '对于', '多', '多少', '而', '而况', '而且',
            '而是', '而外', '而言', '而已', '尔后', '反过来', '反过来说',
            '反之', '非但', '非徒', '否则', '嘎', '嘎登', '该', '赶', '个',
            '各', '各个', '各位', '各种', '各自', '给', '根据', '跟', '故',
            '故此', '固然', '关于', '管', '归', '果然', '果真', '过', '哈',
            '哈哈', '呵', '和', '何', '何处', '何况', '何时', '嘿', '哼', '哼唷',
            '呼哧', '乎', '哗', '还是', '换句话说', '换言之', '或', '或是', '或者',
            '及', '及其', '及至', '即', '即便', '即或', '即令', '即若', '即使', '几',
            '几时', '己', '既', '既然', '既是', '继而', '加之', '假如', '假若', '假使',
            '鉴于', '将', '较', '较之', '叫', '接着', '结果', '借', '紧接着', '进而',
            '尽', '尽管', '经', '经过', '就', '就是', '就是说', '据', '具体地说',
            '具体说来', '开始', '开外', '靠', '咳', '可', '可见', '可是', '可以',
            '况且', '啦', '来', '来着', '离', '例如', '哩', '连', '连同', '两者',
            '临', '另', '另外', '另一方面', '论', '嘛', '吗', '慢说', '漫说', '冒',
            '么', '每', '每当', '们', '莫若', '某', '某个', '某些', '拿', '哪',
            '哪边', '哪儿', '哪个', '哪里', '哪年', '哪怕', '哪天', '哪些',
            '哪样', '那', '那边', '那儿', '那个', '那会儿', '那里', '那么',
            '那么些', '那么样', '那时', '那些', '那样', '乃', '乃至', '呢',
            '能', '你', '你们', '您', '宁', '宁可', '宁肯', '宁愿', '哦',
            '呕', '啪达', '旁人', '呸', '凭', '凭借', '其', '其次', '其二',
            '其他', '其它', '其一', '其余', '其中', '起', '起见', '起见',
            '岂但', '恰恰相反', '前后', '前者', '且', '然而', '然后', '然则',
            '让', '人家', '任', '任何', '任凭', '如', '如此', '如果', '如何',
            '如其', '如若', '如上所述', '若', '若非', '若是', '啥', '上下',
            '尚且', '设若', '设使', '甚而', '甚么', '甚至', '省得', '时候',
            '什么', '什么样', '使得', '是', '首先', '谁', '谁知', '顺',
            '顺着',  '虽', '虽然', '虽说', '虽则', '随', '随着', '所', '所以',
            '他', '他们', '他人', '它', '它们', '她', '她们', '倘', '倘或', '倘然',
            '倘若', '倘使', '腾', '替', '通过', '同', '同时', '哇', '万一', '往',
            '望', '为', '为何', '为什么', '为着', '喂', '嗡嗡', '我', '我们', '呜',
            '呜呼', '乌乎', '无论', '无宁', '毋宁', '嘻', '吓', '相对而言', '像',
            '向', '向着', '嘘', '呀', '焉', '沿', '沿着', '要', '要不', '要不然',
            '要不是', '要么', '要是', '也', '也罢', '也好', '一', '一般', '一旦',
            '一方面', '一来', '一切', '一样', '一则', '依', '依照', '矣', '以',
            '以便', '以及', '以免', '以至', '以至于', '以致', '抑或', '因',
            '因此', '因而', '因为', '哟', '用', '由', '由此可见', '由于', '又',
            '于', '于是', '于是乎', '与', '与此同时', '与否', '与其', '越是', '云云',
            '哉', '再说', '再者', '在', '在下', '咱', '咱们', '则', '怎', '怎么',
            '怎么办', '怎么样', '怎样', '咋', '照', '照着', '者', '这', '这边', '这儿',
            '这个', '这会儿', '这就是说', '这里', '这么', '这么点儿', '这么些',
            '这么样', '这时', '这些', '这样', '正如', '吱', '之', '之类', '之所以',
            '之一', '只是', '只限', '只要', '至', '至于', '诸位', '着', '着呢', '自',
            '自从', '自个儿', '自各儿', '自己', '自家', '自身', '综上所述', '总而言之',
            '总之', '纵', '纵令', '纵然', '纵使', '遵照', '作为', '兮', '呃', '呗', '咚',
            '咦', '喏', '啐', '喔唷', '嗬', '嗯', '嗳',
            'a\'s', 'able', 'about', 'above', 'according', 'accordingly', 'across', 'actually',
            'after', 'afterwards', 'again', 'against', 'ain\'t', 'all', 'allow', 'allows',
            'almost', 'alone', 'along', 'already', 'also', 'although', 'always', 'am',
            'among', 'amongst', 'an', 'and', 'another', 'any', 'anybody', 'anyhow',
            'anyone', 'anything', 'anyway', 'anyways', 'anywhere', 'apart', 'appear', 'appreciate',
            'appropriate', 'are', 'aren\'t', 'around', 'as', 'aside', 'ask', 'asking',
            'associated', 'at', 'available', 'away', 'awfully', 'be', 'became', 'because',
            'become', 'becomes', 'becoming', 'been', 'before', 'beforehand', 'behind', 'being',
            'believe', 'below', 'beside', 'besides', 'best', 'better', 'between', 'beyond',
            'both', 'brief', 'but', 'by', 'c\'mon', 'c\'s', 'came', 'can',
            'can\'t', 'cannot', 'cant', 'cause', 'causes', 'certain', 'certainly', 'changes',
            'clearly', 'co', 'com', 'come', 'comes', 'concerning', 'consequently', 'consider',
            'considering', 'contain', 'containing', 'contains', 'corresponding', 'could', 'couldn\'t', 'course',
            'currently', 'definitely', 'described', 'despite', 'did', 'didn\'t', 'different', 'do',
            'does', 'doesn\'t', 'doing', 'don\'t', 'done', 'down', 'downwards', 'during',
            'each', 'edu', 'eg', 'eight', 'either', 'else', 'elsewhere', 'enough',
            'entirely', 'especially', 'et', 'etc', 'even', 'ever', 'every', 'everybody',
            'everyone', 'everything', 'everywhere', 'ex', 'exactly', 'example', 'except', 'far',
            'few', 'fifth', 'first', 'five', 'followed', 'following', 'follows', 'for',
            'former', 'formerly', 'forth', 'four', 'from', 'further', 'furthermore', 'get',
            'gets', 'getting', 'given', 'gives', 'go', 'goes', 'going', 'gone',
            'got', 'gotten', 'greetings', 'had', 'hadn\'t', 'happens', 'hardly', 'has',
            'hasn\'t', 'have', 'haven\'t', 'having', 'he', 'he\'s', 'hello', 'help',
            'hence', 'her', 'here', 'here\'s', 'hereafter', 'hereby', 'herein', 'hereupon',
            'hers', 'herself', 'hi', 'him', 'himself', 'his', 'hither', 'hopefully',
            'how', 'howbeit', 'however', 'i\'d', 'i\'ll', 'i\'m', 'i\'ve', 'ie',
            'if', 'ignored', 'immediate', 'in', 'inasmuch', 'inc', 'indeed', 'indicate',
            'indicated', 'indicates', 'inner', 'insofar', 'instead', 'into', 'inward', 'is',
            'isn\'t', 'it', 'it\'d', 'it\'ll', 'it\'s', 'its', 'itself', 'just',
            'keep', 'keeps', 'kept', 'know', 'known', 'knows', 'last', 'lately',
            'later', 'latter', 'latterly', 'least', 'less', 'lest', 'let', 'let\'s',
            'like', 'liked', 'likely', 'little', 'look', 'looking', 'looks', 'ltd',
            'mainly', 'many', 'may', 'maybe', 'me', 'mean', 'meanwhile', 'merely',
            'might', 'more', 'moreover', 'most', 'mostly', 'much', 'must', 'my',
            'myself', 'name', 'namely', 'nd', 'near', 'nearly', 'necessary', 'need',
            'needs', 'neither', 'never', 'nevertheless', 'new', 'next', 'nine', 'no',
            'nobody', 'non', 'none', 'noone', 'nor', 'normally', 'not', 'nothing',
            'novel', 'now', 'nowhere', 'obviously', 'of', 'off', 'often', 'oh',
            'ok', 'okay', 'old', 'on', 'once', 'one', 'ones', 'only',
            'onto', 'or', 'other', 'others', 'otherwise', 'ought', 'our', 'ours',
            'ourselves', 'out', 'outside', 'over', 'overall', 'own', 'particular', 'particularly',
            'per', 'perhaps', 'placed', 'please', 'plus', 'possible', 'presumably', 'probably',
            'provides', 'que', 'quite', 'qv', 'rather', 'rd', 're', 'really',
            'reasonably', 'regarding', 'regardless', 'regards', 'relatively', 'respectively', 'right', 'said',
            'same', 'saw', 'say', 'saying', 'says', 'second', 'secondly', 'see',
            'seeing', 'seem', 'seemed', 'seeming', 'seems', 'seen', 'self', 'selves',
            'sensible', 'sent', 'serious', 'seriously', 'seven', 'several', 'shall', 'she',
            'should', 'shouldn\'t', 'since', 'six', 'so', 'some', 'somebody', 'somehow',
            'someone', 'something', 'sometime', 'sometimes', 'somewhat', 'somewhere', 'soon', 'sorry',
            'specified', 'specify', 'specifying', 'still', 'sub', 'such', 'sup', 'sure',
            't\'s', 'take', 'taken', 'tell', 'tends', 'th', 'than', 'thank',
            'thanks', 'thanx', 'that', 'that\'s', 'thats', 'the', 'their', 'theirs',
            'them', 'themselves', 'then', 'thence', 'there', 'there\'s', 'thereafter', 'thereby',
            'therefore', 'therein', 'theres', 'thereupon', 'these', 'they', 'they\'d', 'they\'ll',
            'they\'re', 'they\'ve', 'think', 'third', 'this', 'thorough', 'thoroughly', 'those',
            'though', 'three', 'through', 'throughout', 'thru', 'thus', 'to', 'together',
            'too', 'took', 'toward', 'towards', 'tried', 'tries', 'truly', 'try',
            'trying', 'twice', 'two', 'un', 'under', 'unfortunately', 'unless', 'unlikely',
            'until', 'unto', 'up', 'upon', 'us', 'use', 'used', 'useful',
            'uses', 'using', 'usually', 'value', 'various', 'very', 'via', 'viz',
            'vs', 'want', 'wants', 'was', 'wasn\'t', 'way', 'we', 'we\'d',
            'we\'ll', 'we\'re', 'we\'ve', 'welcome', 'well', 'went', 'were', 'weren\'t',
            'what', 'what\'s', 'whatever', 'when', 'whence', 'whenever', 'where', 'where\'s',
            'whereafter', 'whereas', 'whereby', 'wherein', 'whereupon', 'wherever', 'whether', 'which',
            'while', 'whither', 'who', 'who\'s', 'whoever', 'whole', 'whom', 'whose',
            'why', 'will', 'willing', 'wish', 'with', 'within', 'without', 'won\'t',
            'wonder', 'would', 'wouldn\'t', 'yes', 'yet', 'you', 'you\'d', 'you\'ll',
            'you\'re', 'you\'ve', 'your', 'yours', 'yourself', 'yourselves', 'zero'
    );
    
    if (in_array($keyword, $stop_words_list))
    {
        return false;
    }
    
    return true;
}
