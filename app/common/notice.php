<?php
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
