<?php
/**
 * 百度相关服务
 * */
class Baidu
{
    //百度秘钥
    const _ak = '4VIFegum36qwpjzjF3MdNAjua4p24EEq';
    
    /**
     * @desc  place查找
     * @param string $q 关键字，如人民广场
     * @param srting $region 地区，如上海
     * @param int    $scope 检索结果详细程度。取值为1或空，则返回基本信息；取值为2，返回检索POI详细信息 
     * @return array
     * */
    public function placeSearch($q, $region, $scope = 2, $page = 0, $size = 10)
    {
        $url = 'https://api.map.baidu.com/place/v2/search';
        
        $para = array(
                'query' => $q,
                'page_num' => $page,
                'page_size' => $size,
                'scope' => $scope,
                'region' => $region,
                'output' => 'json',
                'ak' => self::_ak
        );
        
        $data = request($url, $para, false);
        
        $data = json_decode($data, true);
        
        if ($data['results'])
        {
            return $data['results'];
        }
        
        return array();
    }
    
    /**
     * @desc   place详情
     * @param  string $name place名称
     * @param  string $region 地区，如上海
     * @return array
     * */
    public function getPlaceDetail($name, $region)
    {
        $data = $this -> placeSearch($name, $region, 2, 0, 1);
        
        $result = array();
        
        if ($data[0])
        {
            $data = $data[0];
            
            $url = 'https://api.map.baidu.com/place/v2/detail';
            
            $para = array(
                    'uid' => $data['uid'],
                    'scope' => 2,
                    'output' => 'json',
                    'ak' => self::_ak
            );
            
            $result = request($url, $para);
            
            if ($result)
            {
                $data = json_decode($result, true);
                
                if ($data['result']['detail_info'])
                {
                    return $data['result']['detail_info'];
                }
            }
        }
        
        return $result;
    }
    
    /**
     * @desc   关键词建议获取
     * @param  string $word 基础词汇
     * @return array
     * */
    public function getSuggestion($word, $region = '全国')
    {
        $url = 'https://api.map.baidu.com/place/v2/suggestion/';
        
        $para = array(
                'q' => $word,
                'output' => 'json',
                'region' => $region,
                'ak' => self::_ak
        );
        
        $data = request($url, $para);
        
        return $data;
    }
    
    /**
     * @desc   根据地址获取经纬度
     * @param  string  $address  地址
     * @return array
     * */
    public function get_point($address)
    {
        $url = 'https://api.map.baidu.com/geocoder/v2/';
        
        $para = array(
                'address' => $address,
                'output' => 'json',
                'ak' => self::_ak
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
     * @desc   路径规划
     * @param  string $origin             起点地址
     * @param  string $destination        终点地址
     * @param  string $city               城市名称
     * @param  string $origin_city        起点城市
     * @param  string $destination_region 终点城市
     * @param  string $mode               出行模式,driving(自驾)、walking(步行)、transit(公交)、riding(骑行)
     * @return array
     * */
    public function get_direction($origin, $destination, $city, $mode = 'driving', $origin_city = '', $destination_region = '')
    {
        $url = 'https://api.map.baidu.com/direction/v1';
        
        $para = array(
                'origin'             => $origin,
                'destination'        => $destination,
                'mode'               => $mode,
                'region'             => $city,
                'origin_region'      => $origin_city,
                'destination_region' => $destination_region,
                'output' => 'json',
                'ak' => self::_ak
        );
        
        $data = request($url, $para);
        
        $data = json_decode($data, true);
        
        if ($data['result'])
        {
            return $data['result'];
        }
        
        return array();
    }
}
