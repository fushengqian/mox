<?php
/*
+--------------------------------------------------------------------------
|   FarmNc v1.0
|   ========================================
|   by FarmNc Software
|   © 2015 - 2016 FarmNc. All Rights Reserved
|   http://www.farmNc.net
|   ========================================
|   Support: 540335306@qq.com
+---------------------------------------------------------------------------
*/
class core_cart
{
    public function get_info($type)
    {
        return FARM_APP::session()->cart[$type];
    }
    
    public function clear_cart($type = 'goods')
    {
        unset(FARM_APP::session()->cart[$type]);
    }
    
    public function minus_cart($id, $minus = true, $type = 'goods')
    {
        empty(FARM_APP::session()->cart[$type]) && FARM_APP::session()->cart[$type] = array();
        
        foreach(FARM_APP::session()->cart[$type] as $key => &$value)
        {
            if(intval($value['id']) === intval($id))
            {
                if($value['num'] > 1 && $minus)
                {
                    $value['num'] = $value['num'] - 1;
                }
                else
                {
                    unset(FARM_APP::session()->cart[$type][$key]);
                }
                
                return true;
            }
            
        }
        
        return true;
    }
    
    public function add_cart($id, $type = 'goods')
    {
        empty(FARM_APP::session()->cart[$type]) && FARM_APP::session()->cart[$type] = array();
        
        $is_exist = false;
        foreach(FARM_APP::session()->cart[$type] as $key => &$value)
        {
           if($value['id'] == $id)
           {
              $value['num'] = $value['num'] + 1;
              return true;
           }
        }
        
        if (!$is_exist)
        {
            FARM_APP::session()->cart[$type][] = array('id' => $id, 'num' => 1);
        }
        
        return true;
    }
}
