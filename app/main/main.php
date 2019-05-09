<?php
/**
 * 五个主要菜单：
 * 农家乐、农庄、度假村、山庄、采摘园
 * */
class main extends MOX_CONTROLLER
{
    public function index_action()
    {
        if (!empty($_GET['area_uname']))
        {
            $area_info = get_area_detail($_GET['area_uname']);
            TPL::assign('area_info', $area_info);
            
            $city_info = $this -> model('system') -> get_city_detail($area_info['city_id'], '', '', false);
            TPL::assign('city_info', $city_info);
            
            $usename = $area_info['name'];
        }
        else
        {
            $city_info = $this -> model('system') -> get_city_detail('', $_GET['city_uname'], '', true);
            TPL::assign('city_info', $city_info);
            
            $usename = $city_info['name'];
        }
        
        $name = $this -> _getMenuName($_GET['menu']);
        TPL::assign('usename', $usename);
        TPL::assign('name', $name);
        
        if (!empty($area_info))
        {
            $list = $this -> model('mox') -> get_data_list('area_id = '.intval($area_info['id']).' AND `name` like "%'.$name.'%"', 1, 20, 'avg_point desc');
        }
        else
        {
            $list = $this -> model('mox') -> get_data_list('city_id = '.intval($city_info['id']).' AND `name` like "%'.$name.'%"', 1, 20, 'avg_point desc');
        }
        
        //数据不能为空
        if (count($list) < 3)
        {
            $name = str_replace('园', '', $name);
            $name = str_replace('村', '', $name);
            $list = $this -> model('mox') -> get_data_list('city_id = '.intval($city_info['id']).' AND `brief` like "%'.$name.'%"', 1, 20, 'avg_point desc');
        }
        
        TPL::assign('list', $list);
        
        //最新加入
        $new_list = $this -> model('mox') -> get_recommend_list($city_info['id'], 15);
        TPL::assign('new_list', $new_list);
        
        //设置seo
        $this -> _setSeo($usename, $_GET['menu']);
        
        TPL::output('main/index');
    }
    
    private function _getMenuName($uname)
    {
        $arr = array('nongjiale'   => '农家乐',
                     'nongzhuang'  => '农庄',
                     'shanzhuang'  => '山庄',
                     'dujiacun'    => '度假村',
                     'caizhaiyuan' => '采摘园');
        
        return $arr[$uname] ? $arr[$uname] : '';
    }
    
    private function _setSeo($usename, $menu)
    {
        $seo = array('title'       => array($usename),
                     'keywords'    => array($usename),
                     'description' => array($usename));
        
        $arr = get_seo($menu, $seo);
        
        TPL::assign('seo', $arr);
        
        return;
    }
}
