<?php

class sms extends MOX_CONTROLLER
{
    static $acsClient = null;

    public function login_action()
    {
        set_time_limit(0);

        $mobile = $_REQUEST['mobile'];
        $time = $_REQUEST['time'];

        if (strlen(trim($mobile)) !== 11 || empty($time)) {
            echo json_encode(array('code' => 201, 'msg' => '出错了'));
            exit();
        }

        //允许跨域
        header('Content-Type: text/plain; charset=utf-8');
        header('Access-Control-Allow-Origin: http://vip' . G_BASE_DEMAIN);
        header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');

        $result = MOX_APP::model('sms')->send($mobile, array('code' => rand(1000, 9999)), 1);

        if ($result) {
            echo json_encode(array('code' => 200, 'data' => array()));
        } else {
            echo json_encode(array('code' => 201, 'data' => array()));
        }
        exit();
    }
}
