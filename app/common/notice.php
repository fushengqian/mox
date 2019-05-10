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

class notice extends MOX_CONTROLLER
{
    /**
     * 最新通知
     */
    public function new_action()
    {
        set_time_limit(0);

        $ret = array('phone' => '18976679980', 'token' => md5('token'), 'expireDate' => date("Y-m-d H:i:s", (time()+86400)));

        $this -> jsonReturn($ret);
    }
}
