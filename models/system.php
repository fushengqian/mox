<?php
class system_class extends FARM_MODEL
{
    /**
     * 根据url获取SEO
     * 
     * @param  string $code
     * @return array
     * */
    public function get_seo($code)
    {
        return $this->fetch_row('page', 'code = "'.$code.'"');
    }
    
    public function get_area_detail($id = '', $uname = '', $city_id = '', $son = true, $is_open = false)
    {
        $where = '1 = 1';
        
        if ($id)
        {
            $where .= ' AND id = '.intval($id);
        }
        
        if ($is_open)
        {
            $where .= ' AND `status` = 1';
        }
        
        if($uname)
        {
           $where .= ' AND uname = "'.htmlspecialchars($uname).'"';
        }
        
        if($city_id)
        {
           $where .= ' AND city_id = '.intval($city_id); 
        }
        
        $data = $this->fetch_row('area', $where);
        
        if($data && $son)
        {
           $data['son'] = $this->fetch_all('area', 'pid = "'.intval($data['id']).'"');
        }
        
        return $data;
    }
    
    public function get_area_by_name($name, $city_id = '')
    {
        $where = 'name = "'.htmlspecialchars($name).'"';
        
        if($city_id)
        {
            $where .= ' AND city_id = '.intval($city_id);
        }
        
        $data = $this->fetch_row('area', $where);
        
        if (!$data)
        {
            $name = str_replace('新区', '', $name);
            $name = str_replace('区', '', $name);
            $name = str_replace('县', '', $name);
            
            $where = 'name = "'.htmlspecialchars($name).'"';
            
            if($city_id)
            {
                $where .= ' AND city_id = '.intval($city_id);
            }
            
            $data = $this->fetch_row('area', $where);
            
            if (!$data)
            {
                $where = 'name = "'.htmlspecialchars($name).'"';
                
                if($city_id)
                {
                    $where .= ' AND city_id = '.intval($city_id);
                }
                
                $data = $this->fetch_row('area', $where);
            }
        }
        
        return $data;
    }
    
    public function get_all_province()
    {
        return $this -> fetch_all('province');
    }
    
    public function get_province_by_name($name)
    {
        $name = str_replace('省', '', $name);
        return $this->fetch_row('province', 'piovname = "'.($name).'"');
    }
    
    public function get_province_city($provid)
    {
        return $this -> fetch_all('city', 'provid = '.intval($provid));
    }
    
    public function get_province_by_id($id)
    {
        return $this->fetch_row('province', 'piovid ='.intval($id));
    }
    
    public function get_city_by_name($name)
    {
        $name = str_replace('\\', '', $name);
            
        $data = $this->fetch_row('city', 'name = "'.htmlspecialchars($name).'"');
        
        if(!$data)
        {
           $name = str_replace('市', '', $name);
           $data = $this->fetch_row('city', 'name = "'.htmlspecialchars($name).'"');
        }
        
        if(!$data)
        {
            $name = str_replace('县', '', $name);
            $data = $this->fetch_row('city', 'name = "'.htmlspecialchars($name).'"');
        }
        
        return $data;
    }
    
    public function get_nearby_city($city_id)
    {
        $city = $this->fetch_row('city', 'id = "'.intval($city_id).'"');
        $list = $this->fetch_all('city', 'provid ='.intval($city['provid']). ' AND id <> '.intval($city_id));
        
        if (empty($list))
        {
            $list = $this->fetch_all('city', 'id <> '.intval($city_id));
        }
        
        return $list;
    }
    
    public function get_city_detail($id = '', $uname = '', $name = '', $is_effect = false)
    {
        if ($id)
        {
            if ($is_effect)
            {
                return $this->fetch_row('city', 'id = "'.intval($id).'" AND `status` = 1');
            }
            else
            {
                return $this->fetch_row('city', 'id = "'.intval($id).'"');
            }
        }
        
        if ($uname)
        {
            $data = $this->fetch_row('city', 'uname = "'.htmlspecialchars($uname).'"');
            if ($data)
            {
                return $data;
            }
        }
        
        return $this->fetch_row('city', 'name = "'.htmlspecialchars($name).'"');
    }
    
    public function get_city_area($city_id, $orderby = 'id asc')
    {
        $list = $this->fetch_all('area', 'city_id = '.intval($city_id), $orderby);
        
        $result = array();
        foreach($list as $k => $v)
        {
            if ($v['pid'] == '0')
            {
               $result[] = $v;
               unset($list[$k]);
            }
        }
        
        foreach($result as $key => $value)
        {
            foreach($list as $item)
            {
               if($value['id'] == $item['pid'])
               {
                  $result[$key]['son'][] = $item;
               }
            }
        }
        
        return $result;
    }
    
    public function get_city_list($use_effect = true, $serialize = true)
    {
        if($use_effect)
        {
            $list = $this->fetch_all('city', 'status = 1', 'id ASC');
        }
        else
        {
            $list = $this->fetch_all('city', '', 'id ASC');
        }
        
        if ($serialize)
        {
            return serialize($list);
        }
        
        return $list;
    }
}
