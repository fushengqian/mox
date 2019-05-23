<?php
require_once ('Services/Baidu.php');
class area extends MOX_ADMIN_CONTROLLER
{
    //地区（包括市、区、县、镇、乡、村）列表
    public function index_action()
    {
        $page = $_GET['page'] ? $_GET['page'] : 1;
        $order = $_GET['order'] ? $_GET['order'] : 'num desc';
        
        $this->crumb(MOX_APP::lang()->_t('地区管理'), "backend/hot/list/");
        
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(401));
        
        $keyword = $_GET['keyword'] ? trim($_GET['keyword']) : '';
        $where = 'id > 1';
        
        if ($keyword)
        {
            $p = $this -> model('system') -> get_city_by_name($keyword);
        }
        
        if ($p)
        {
            $pid = $p['id'];
        }
        else if($keyword)
        {
            $where .= ' AND (`name` like "%'.$keyword.'%")';
        }
        
        if ($pid)
        {
            $where .= ' AND `city_id` = '.intval($pid);
        }
        
        $list = $this -> model('system') -> fetch_page('area', $where, $order, $page, 60);
        
        $city_list = $this -> model('system') -> get_city_list(false, false);
        
        empty($list) && $list = array();
        foreach($list as $k => $v)
        {
            foreach($city_list as $k1 => $v1)
            {
                if ($v1['id'] == $v['city_id'])
                {
                    $list[$k]['cname'] = $v1['name'];
                    $list[$k]['cuname'] = $v1['uname'];
                }
            }
        }
        
        TPL::assign('list', $list);
        
        TPL::assign('pagination', MOX_APP::pagination()->initialize(array(
            'base_url' => get_js_url('/backend/area/index/'),
            'total_rows' => $this->model('system')->found_rows(),
            'per_page' => 60
        ))->create_links());
        
        TPL::assign('total_rows', $this->model('system')->found_rows());
        TPL::assign('order', $order);
        
        TPL::output('backend/area/list');
    }
    
    //添加地区页面
    public function add_action()
    {
        $this->crumb(MOX_APP::lang()->_t('新增地区'), "backend/hot/list/");
        
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(401));
        
        TPL::output('backend/area/add');
    }
    
    //更新
    public function update_action()
    {
        $uname = trim($_POST['uname']);
        $name = trim($_POST['name']);
        $id  = intval($_POST['id']);
        $status = $_POST['status'];
        
        $arr = array('uname' => $uname, 'name' => $name, 'status' => $status);

        $result = $this -> model('system') -> update('area', $arr, 'id = '.$id);
        
        if ($result)
        {
            H::ajax_json_output(MOX_APP::RSM(array(), 1, null));
        }
        else
        {
            H::ajax_json_output('保存失败');
        }
    }
    
    //新增存储
    public function save_action()
    {
        $name = trim($_POST['name']);
        
        if (empty($_POST['city_name']))
        {
            H::ajax_json_output('城市名称不能为空');
        }
        else
        {
            $city_info = $this->model('system') -> get_city_detail('', '', $_POST['city_name']);
            
            if (empty($city_info))
            {
                $area_info = $this->model('system') -> get_area_by_name(trim($_POST['city_name']));
                $city_id = (int)$area_info['city_id'];
            }
            else
            {
                $city_id = (int)$city_info['id'];
            }
            
            if (empty($city_id))
            {
                H::ajax_json_output('城市有误，系统查不到这个城市');
            }
        }
        
        $info = $this->model('system') -> get_area_by_name(trim($_POST['name']), $city_id);
        if ($info)
        {
            H::ajax_json_output('额，该数据已经存在了！');
        }
        
        if ($_POST['parent'])
        {
            $parent = $this->model('system') -> get_area_by_name(trim($_POST['parent']), $city_id);
        }
        
        $uname = P::encode($name);
        $unameinfo = $this->model('system') -> get_area_detail('', $uname);
        
        if ($unameinfo)
        {
            $uname = $city_info['uname'].$uname;
        }
        
        $arr = array('name'    => $name,
                     'uname'   => $uname,
                     'city_id' => $city_id,
                     'pid'     => $parent['id'] ? $parent['id'] : 0,
                     'status'  => 0
        );
        
        //经纬度
        $baidu = new Baidu();
        
        $lng_lat = $baidu -> get_point($_POST['city_name'].$area_info['name'].$name);
        $arr['lng'] = $lng_lat['lng'];
        $arr['lat'] = $lng_lat['lat'];
        
        //数据统计
        $arr['num']     = $this -> model('mox') -> get_count($city_id, $name);
        $arr['njl_num'] = $this -> model('mox') -> get_count($city_id, $name, '农家乐');
        $arr['nz_num'] = $this -> model('mox') -> get_count($city_id, $name, '农庄');
        $arr['fishing_num'] = $this -> model('mox') -> get_count($city_id, $name, '钓');
        $arr['holiday_num'] = $this -> model('mox') -> get_count($city_id, $name, '度假');
        $arr['caizhai_num'] = $this -> model('mox') -> get_count($city_id, $name, '摘');
        $arr['wenquan_num'] = $this -> model('mox') -> get_count($city_id, $name, '温泉');
        
        $result = $this -> model('system') -> insert('area', $arr);
        
        if ($result)
        {
            H::ajax_json_output(MOX_APP::RSM(array(), 1, null));
        }
        else
        {
            H::ajax_json_output('保存失败');
        }
    }
}