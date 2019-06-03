<?php
/**
+--------------------------------------------------------------------------
|   Mox 1.0.1
|   ========================================
|   by Mox Software
|   © 2018 - 2019 Mox. All Rights Reserved
|   http://www.mox365.com
|   ========================================
|   Support: 540335306@qq.com
|   Author: FSQ
+---------------------------------------------------------------------------
*/

class core_user
{
    public function __construct()
    {
        if (MOX_APP::session()->info AND ! $_COOKIE[G_COOKIE_PREFIX . 'user_id'])
        {
            // Cookie 清除则 Session 也清除
            unset(MOX_APP::session()->info);
        }
        
        if (! MOX_APP::session()->info AND $_COOKIE[G_COOKIE_PREFIX . 'user_id'])
        {
            $auth_hash_key = md5(G_COOKIE_HASH_KEY . $_SERVER['HTTP_USER_AGENT']);
            
            // 解码 Cookie
            $sso_user_login = H::decode_hash($_COOKIE[G_COOKIE_PREFIX . 'user_id'], $auth_hash_key);
            
            if ($sso_user_login['user_name'] AND $sso_user_login['password'] AND $sso_user_login['uid'])
            {
                if (MOX_APP::model('user')->check_hash_login($sso_user_login['user_name'], $sso_user_login['password']))
                {
                    MOX_APP::session()->info['uid'] = $sso_user_login['uid'];
                    MOX_APP::session()->info['user_name'] = $sso_user_login['user_name'];
                    MOX_APP::session()->info['password'] = $sso_user_login['password'];
                    return true;
                }
            }
            
            return false;
        }
    }
    
    public function get_info($key)
    {
        if($key)
        {
           if (!empty(MOX_APP::session()->info[$key])) {
               return MOX_APP::session()->info[$key];
           }

           $admin_info = H::decode_hash(MOX_APP::session()->admin_login);
           if (!empty($admin_info[$key])) {
               return $admin_info[$key];
           }

           return '';
        }
        
        return MOX_APP::session()->info;
    }
}