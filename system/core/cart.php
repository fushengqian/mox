<?php
/*
+--------------------------------------------------------------------------
|   Mox v1.0
|   ========================================
|   by Mox Software
|   © 2018 - 2019 Mox. All Rights Reserved
|   http://www.moxquan.com
|   ========================================
|   Support: 540335306@qq.com
+---------------------------------------------------------------------------
*/
class core_cart
{
    public function get_info($type)
    {
        return MOX_APP::session()->cart[$type];
    }
    
    public function clear_cart($type = 'goods')
    {
        unset(MOX_APP::session()->cart[$type]);
    }
    
    public function minus_cart($id, $minus = true, $type = 'goods')
    {
        empty(MOX_APP::session()->cart[$type]) && MOX_APP::session()->cart[$type] = array();
        
        foreach(MOX_APP::session()->cart[$type] as $key => &$value)
        {
            if(intval($value['id']) === intval($id))
            {
                if($value['num'] > 1 && $minus)
                {
                    $value['num'] = $value['num'] - 1;
                }
                else
                {
                    unset(MOX_APP::session()->cart[$type][$key]);
                }
                
                return true;
            }
            
        }
        
        return true;
    }
    
    public function add_cart($id, $type = 'goods')
    {
        empty(MOX_APP::session()->cart[$type]) && MOX_APP::session()->cart[$type] = array();
        
        $is_exist = false;
        foreach(MOX_APP::session()->cart[$type] as $key => &$value)
        {
           if($value['id'] == $id)
           {
              $value['num'] = $value['num'] + 1;
              return true;
           }
        }
        
        if (!$is_exist)
        {
            MOX_APP::session()->cart[$type][] = array('id' => $id, 'num' => 1);
        }
        
        return true;
    }
}
