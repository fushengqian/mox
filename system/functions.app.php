<?php

/**
 * 生成唯一ID
 */
function setId() {
    return date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

/**
 * 生成二维码
 * @param string $url
 * @return void
 */
function qrCode($url) {
    require_once dirname(__FILE__) . './phpqrcode/phpqrcode.php';
    $errorCorrectionLevel = 'L';
    $matrixPointSize = 5;
    return \QRcode::png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
}

function get_keyword($url, $kw_start) {
    $start=stripos($url,$kw_start);
    $url=substr($url,$start+strlen($kw_start));
    $start=stripos($url,'&');
    if ($start>0) {
        $start=stripos($url,'&');
        $s_s_keyword=substr($url,0,$start);
    } else {
        $s_s_keyword=substr($url,0);
    }
    return $s_s_keyword;
}

/**
 * 获取搜索引擎的关键字
 */
function getSpiderWord($user_info)
{
    // 搜索引擎关键字映射
    static $host_keyword_map = array(
        'www.baidu.com' => 'wd',
        'm.baidu.com' => 'word',
        'm.sm.cn' => 'wd',
        'v.baidu.com' => 'word',
        'image.baidu.com' => 'word',
        'news.baidu.com' => 'word',
        'www.so.com' => 'q',
        'video.so.com' => 'q',
        'image.so.com' => 'q',
        'news.so.com' => 'q',
        'www.sogou.com' => 'query',
        'pic.sogou.com' => 'query',
        'v.sogou.com' => 'query',
    );

    // 检查来源是否搜索引擎
    if (!isset($_SERVER['HTTP_REFERER'])) {
        return '';
    }

    $urls = parse_url($_SERVER['HTTP_REFERER']);

    if (!array_key_exists($urls['host'], $host_keyword_map)) {
        return '';
    }

    $key = $host_keyword_map[$urls['host']];

    // 检查关键字参数是否存在
    if (!isset($urls['query'])) {
        return '';
    }

    $params = array();
    parse_str($urls['query'], $params);
    if (!isset($params[$key])) {
        return '';
    }

    $keywords = $params[$key];

    // 检查编码
    $encoding = mb_detect_encoding($keywords, 'utf-8,gbk');
    if ($encoding != 'utf-8') {
        $keywords = iconv($encoding, 'utf-8', $keywords);
    }

    $keywords = trim($keywords);

    if (empty($keywords)) {
        return '';
    }

    // 是否含有中文
    if (!preg_match('/[\x{4e00}-\x{9fa5}]/u', $keywords) > 0) {
        return '';
    }

    return $keywords;
}

/**
 * @desc   自动适应pc和移动屏幕
 * @return void
 */
function fix_client()
{
    $url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];

    if (is_mobile())
    {
        $to = str_replace('//www.', '//m.', $url);
    }
    else
    {
        $to = str_replace('//m.', '//www.', $url);
    }

    if ($url != $to)
    {
        header('HTTP/1.1 302 Moved Temporarily');
        HTTP::redirect($to);
        exit();
    }

    return;
}

/**
 * @desc   存入缓存
 * @param  string $code
 * @param  mix    $data
 * @param  int    $life_time
 * @return boolean
 */
function save_cache_data($code, $data, $life_time = 86400)
{
     FARM_APP::model('system') -> delete('cache', 'code = "'.$code.'"');
     return FARM_APP::model('system') -> insert('cache', array('code' => $code, 'data' => serialize($data), 'life_time' => (time()+$life_time)));
}

/**
 * @desc   读取缓存
 * @param  string $code
 * @param  mix    $data
 * @param  int    $life_time
 * @return boolean
 */
function get_cache_data($code)
{
    $data = FARM_APP::model('system') -> fetch_row('cache', 'code = "'.trim($code).'"');

    // 过期
    if (($data['life_time'] - time() < 0))
    {
        return false;
    }

    return $data['data'] ? unserialize($data['data']) : '';
}

/**
 * @desc   获取 IP 地理位置
 * @param  string $ip
 * @return array
 */
function get_city_by_ip($ip = '')
{
    if ($ip == '')
    {
        $url = "http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json";
        $ip=json_decode(file_get_contents($url),true);
        $data = $ip;
    }
    else
    {
        $url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        $ip=json_decode(file_get_contents($url));
        if((string)$ip->code=='1')
        {
            return false;
        }
        $data = (array)$ip->data;
    }

    return $data;
}

/**
 * @desc   获取经纬度
 * @param  string  $address  地址
 * @return array
 * */
function get_point($address)
{
    $url = 'https://api.map.baidu.com/geocoder/v2/';
    
    $para = array(
            'address' => $address,
            'output' => 'json',
            'ak' => '4VIFegum36qwpjzjF3MdNAjua4p24EEq'
    );
    
    $data = request($url, $para);
    
    $data = json_decode($data, true);
    
    if (!empty($data['result']['location']))
    {
        return $data['result']['location'];
    }
    
    return array();
}

/**
 * 根据经纬度获取地址
 * @param string $lat
 * @param string $lng
 * @return address
 */
function get_address($lat, $lng)
{
    $url = 'https://api.map.baidu.com/geocoder/v2/';

    $para = array(
        'location' => $lat.','.$lng,
        'output' => 'json',
        'pois' => 1,
        'ak' => '4VIFegum36qwpjzjF3MdNAjua4p24EEq'
    );

    $data = request($url, $para);

    $data = json_decode($data, true);

    if (!empty($data['result']['formatted_address']))
    {
        return $data['result']['formatted_address'];
    }

    return '';
}

/**
 * 获取头像地址
 *
 * 举个例子：$uid=12345，那么头像路径很可能(根据您部署的上传文件夹而定)会被存储为/uploads/000/01/23/45_avatar_min.jpg
 *
 * @param  int
 * @param  string
 * @return string
 */
function get_avatar_url($uid, $size = 'min')
{
    $uid = intval($uid);
    
    if (!$uid)
    {
        return G_STATIC_URL . '/common/avatar-' . $size . '-img.png';
    }
    
    foreach (FARM_APP::config()->get('image')->avatar_thumbnail as $key => $val)
    {
        $all_size[] = $key;
    }
    
    $size = in_array($size, $all_size) ? $size : $all_size[0];
    
    $uid = sprintf("%09d", $uid);
    $dir1 = substr($uid, 0, 3);
    $dir2 = substr($uid, 3, 2);
    $dir3 = substr($uid, 5, 2);
    
    if (file_exists(get_setting('upload_dir') . '/avatar/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, - 2) . '_avatar_' . $size . '.jpg'))
    {
        return get_setting('upload_url') . '/avatar/' . $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, - 2) . '_avatar_' . $size . '.jpg';
    }
    else
    {
        return G_STATIC_URL . '/common/avatar-' . $size . '-img.png';
    }
}
/**
 * 分享
 * @param string $url
 * @param string $title
 * @param string $pic
 * @param string $content
 * @return array
 */
function get_share($url, $title, $pic, $content)
{
    $qq = 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url='.$url.'&title='.$title.'&pics='.$pic.'&summary='.$content;
    $sina = 'http://service.weibo.com/share/share.php?url='.$url.'&title='.summary($title.$content, 110).'&pic='.$pic.'&searchPic=false';
    $douban = 'http://www.douban.com/share/service?href='.$url.'&name='.$title.'&text='.$content.'&image='.$pic;
    $weixin = G_DEMAIN.'/common/weixinshare/?url='.base64_encode($url).'/';
    return array('qq' => $qq, 'weibo' => $sina, 'douban' => $douban, 'weixin' => $weixin);
}

/**
 * 根据关键字生成seo
 * @param  string $keyword
 * @return array
 * */
function get_seo_by_keyword($keyword)
{
    $result = array('keyword' => $keyword);
    
    $result['title'] = $keyword.' - 模型圈';
    
    $result['description'] = '模型圈欢迎您到梨木台来旅游，我们可以为您提供蓟县梨木台农家院_天津梨木台_梨木台塞北农家院等信息。';
    
    return $result;
}

/**
 * 获取SEO配置
 * @param  string $page
 * @param  array  $data 参数
 * @param  string $config
 * @return array
 * */
function get_seo($page = 'default', $data = array())
{
   //保证页面的SEO不改变
   $url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
   $code = md5($url);
   $result = FARM_APP::model('system') -> get_seo($code);
   if ($result && G_ONLINE)
   {
       return $result;
   }

   $list = FARM_APP::model('system')->fetch_all('seo');
   foreach($list as $key => $value)
   {
       $list[$value['key']] = array('title' => $value['title'],
                                    'keywords' => $value['keywords'],
                                    'description' => $value['description']);
       unset($list[$key]);
   }
   
   $result = $list[$page];
   
   if ($result && $data)
   {
       foreach($data['title'] as $k1 => $v1)
       {
           $result['title'] = str_replace("{".($k1+1)."}", $v1, $result['title']);
       }
       
       foreach($data['keywords'] as $k2 => $v2)
       {
           $result['keywords'] = str_replace("{".($k2+1)."}", $v2, $result['keywords']);
       }
       
       //去重
       $keyword_arr = array_unique(explode(',', $result['keywords']));
       $result['keywords'] = implode(',', $keyword_arr);
       
       foreach($data['description'] as $k3 => $v3)
       {
           $result['description'] = str_replace("{".($k3+1)."}", $v3, $result['description']);
       }
   }
   
   //保证页面的SEO不改变
   if (!empty($result['title']) && $page !== 'search' && G_ONLINE)
   {
       //FARM_APP::model('system') -> insert('page', array_merge(array('url' => $url, 'code' => $code), $result));
   }
   
   return $result;
}

function base64_url_encode($parm)
{
    if (!is_array($parm))
    {
        return false;
    }
    
    return strtr(base64_encode(json_encode($parm)), '+/=', '-_,');
}

function base64_url_decode($parm)
{
    return json_decode(base64_decode(strtr($parm, '-_,', '+/=')), true);
}

function request($url, $para, $encode = true)
{
    if($para)
    {
        $url .= '?';
        foreach($para as $key=>$value)
        {
            if ($encode)
            {
                $url .= $key.'='.rawurlencode($value).'&';
            }
            else
            {
                $url .= $key.'='.$value.'&';
            }
        }
    }
    
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_HEADER, 0);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_TIMEOUT, 4);
    curl_setopt ($ch, CURLOPT_FRESH_CONNECT, 0);
    curl_setopt ($ch, CURLOPT_FORBID_REUSE, 0);
    curl_setopt ($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0 );
    
    curl_setopt ($ch, CURLOPT_URL, $url);
    
    $data = curl_exec($ch);
    
    if (empty($data))
    {
        return false;
    }
    
    return $data;
}
