<?php
/*
+--------------------------------------------------------------------------
|   Mox
|   ========================================
|   by Mox Software
|   © 2015 - 2016 WeCenter. All Rights Reserved
|   http://www.moxquan.com
|   ========================================
|   Support: Mox@qq.com
|
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
           return MOX_APP::session()->info[$key];
        }
        
        return MOX_APP::session()->info;
    }
}