<?php
/*
+--------------------------------------------------------------------------
|   FarmNc 
|   ========================================
|   by FarmNc Software
|   © 2015 - 2016 WeCenter. All Rights Reserved
|   http://www.FarmNc.net
|   ========================================
|   Support: FarmNc@qq.com
|
+---------------------------------------------------------------------------
*/

class core_user
{
    public function __construct()
    {
        if (FARM_APP::session()->info AND ! $_COOKIE[G_COOKIE_PREFIX . 'user_id'])
        {
            // Cookie 清除则 Session 也清除
            unset(FARM_APP::session()->info);
        }
        
        if (! FARM_APP::session()->info AND $_COOKIE[G_COOKIE_PREFIX . 'user_id'])
        {
            $auth_hash_key = md5(G_COOKIE_HASH_KEY . $_SERVER['HTTP_USER_AGENT']);
            
            // 解码 Cookie
            $sso_user_login = H::decode_hash($_COOKIE[G_COOKIE_PREFIX . 'user_id'], $auth_hash_key);
            
            if ($sso_user_login['user_name'] AND $sso_user_login['password'] AND $sso_user_login['uid'])
            {
                if (FARM_APP::model('user')->check_hash_login($sso_user_login['user_name'], $sso_user_login['password']))
                {
                    FARM_APP::session()->info['uid'] = $sso_user_login['uid'];
                    FARM_APP::session()->info['user_name'] = $sso_user_login['user_name'];
                    FARM_APP::session()->info['password'] = $sso_user_login['password'];
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
           return FARM_APP::session()->info[$key];
        }
        
        return FARM_APP::session()->info;
    }
}