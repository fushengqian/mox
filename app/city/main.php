<?php
/**
+--------------------------------------------------------------------------
|   Mox 1.0.1
|   ========================================
|   by Mox Software
|   © 2018 - 2019 Mox. All Rights Reserved
|   http://www.moxquan.com
|   ========================================
|   Support: 540335306@qq.com
|   Author: FSQ
+---------------------------------------------------------------------------
*/

class main extends MOX_CONTROLLER
{
    /**
     * 城市首页
     * */
    public function index_action()
    {
        $city_info = $this -> model('system') -> get_city_detail('', $_GET['city_uname'], '', true);
        TPL::assign('city_info', $city_info);
        
        $city_id = $city_info['id'];
        
        //城市标签
        $tag_list = $this -> model('tag') -> get_city_tag($city_id);
        TPL::assign('tag_list', $tag_list);
        
        //给移动用的标签
        $tag_list1 = array();
        foreach($tag_list as $tag)
        {
            foreach($tag as $k => $t)
            {
                $tag_list1[] = $t;
            }
        }
        TPL::assign('tag_list1', array_chunk($tag_list1, 8));
        
        //地区数据
        $area_list = $this -> model('mox') -> get_area_list($city_info['id'], 3);
        TPL::assign('area_list', $area_list);
        
        //农家乐
        $njl_list = $this -> _getNjlTuijian($city_id);
        TPL::assign('njl_list', $njl_list);
        
        //农庄
        $nz_list = $this -> _getNzTuijian($city_id);
        TPL::assign('nz_list', $nz_list);
        
        //度假村
        $dj_list = $this -> _getDjTuijian($city_id);
        TPL::assign('dj_list', $dj_list);
        
        //banner
        $banner_list = $this -> model('mox') -> get_data_list('city_id = '.$city_id, 1, 1, 'update_time desc');
        TPL::assign('banner_list', $banner_list);
        
        //最新seo
        $seo_list = $this -> model('mox') -> get_data_list('city_id = '.$city_id, 2, 35, 'id desc');
        TPL::assign('seo_list', $seo_list);
        
        //农家乐左图
        $njl_left = $this -> model('recommend') -> get_position('1-10', 1);
        TPL::assign('njl_left', $njl_left);
        
        //农庄左图
        $nz_left = $this -> model('recommend') -> get_position('1-11', 1);
        TPL::assign('nz_left', $nz_left);
        
        //度假左图
        $dj_left = $this -> model('recommend') -> get_position('1-12', 1);
        TPL::assign('dj_left', $dj_left);
        
        $seo = array('title'       => array($city_info['name']),
                     'keywords'    => array($city_info['name']),
                     'description' => array($city_info['name']));
        
        TPL::assign('seo', get_seo('city_home', $seo));
        
        //开通的地区
        $open_area_list = $this -> model('mox') -> get_open_area($city_info['id']);
        TPL::assign('open_area_list', $open_area_list);
        
        //同省份开通的城市
        $open_city_list = $this -> model('mox') -> get_open_city($city_info['provid']);
        TPL::assign('open_city_list', $open_city_list);
        
        if (is_mobile())
        {
            TPL::output('city/m.index');
        }
        else
        {
            TPL::import_css('css/city_index.css');
            TPL::output('city/index');
        }
    }
    
    //获取行政区
    private function _getAreaData($city_id)
    {
        return $this -> model('system') -> get_city_area($city_id, 'num desc');
    }
    
    //农家乐|农家院|渔家乐
    private function _getNjlTuijian($city_id)
    {
        $data = $this -> model('mox') -> get_data_list('city_id = '.$city_id.' and cate in(1,6,9,10)', 1, 9, 'avg_point desc');
        
        if (count($data) < 9)
        {
            $num = 9-count($data);
            
            $data1 = $this -> model('mox') -> get_data_list('city_id = '.$city_id, 1, $num, 'avg_point desc');
            
            empty($data1) && $data1 = array();
            $data = array_merge($data, $data1);
        }
        
        return $data;
    }
    
    //生态农庄|生态园|采摘园
    private function _getNzTuijian($city_id)
    {
        $data = $this -> model('mox') -> get_data_list('city_id = '.$city_id.' and cate in(0,3)', 1, 9, 'avg_point desc');
        
        if (count($data) < 9)
        {
            $num = 9-count($data);
            $data1 = $this -> model('mox') -> get_data_list('city_id = '.$city_id, 2, $num, 'avg_point desc');
            empty($data1) && $data1 = array();
            $data = array_merge($data, $data1);
        }
        
        return $data;
    }
    
    //度假村|度假山庄|度假酒店
    private function _getDjTuijian($city_id)
    {
        $data = $this -> model('mox') -> get_data_list('city_id = '.$city_id.' and cate in(4,7,8)', 1, 9, 'avg_point desc');
        
        if (count($data) < 9)
        {
            $num = 9-count($data);
            
            $data1 = $this -> model('mox') -> get_data_list('city_id = '.$city_id, 3, $num, 'avg_point desc');
            
            empty($data1) && $data1 = array();
            
            $data = array_merge($data, $data1);
        }
        
        return $data;
    }
}
