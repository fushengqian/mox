<?php
class admin_class extends MOX_MODEL
{
    public function fetch_menu_list($select_id)
    {
        $admin_menu = (array)MOX_APP::config()->get('admin_menu');
        
        if (!$admin_menu)
        {
            return false;
        }
        
        foreach($admin_menu as $m_id => $menu)
        {
            if ($menu['children'])
            {
                foreach($menu['children'] as $c_id => $c_menu)
                {
                    if ($select_id == $c_menu['id'])
                    {
                        $admin_menu[$m_id]['children'][$c_id]['select'] = true;
                        $admin_menu[$m_id]['select'] = true;
                    }
                }
            }
        }
        
        return $admin_menu;
    }
    
    public function set_admin_login($uid)
    {
        MOX_APP::session()->admin_login = H::encode_hash(array(
            'uid' => $uid,
            'UA' => $_SERVER['HTTP_USER_AGENT'],
            'ip' => fetch_ip()
        ));
    }
    
    public function admin_logout()
    {
        if (isset(MOX_APP::session()->admin_login))
        {
            unset(MOX_APP::session()->admin_login);
        }
    }
    
    public function notifications_crond()
    {
        return;
    }

    public function get_notifications_texts()
    {
        return;
    }
}
