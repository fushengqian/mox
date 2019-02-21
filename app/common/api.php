<?php
class api extends FARM_CONTROLLER
{
    static $acsClient = null;

    /**
     * 版本检查
     */
    public function version_action()
    {
        $appid = $_POST['appId'] ? intval($_POST['appId']) : 2;
        $uuid = $_POST['uuid'] ? intval($_POST['uuid']) : 0;

        $user_id = intval(FARM_APP::session()->info['uid']);
        if (!empty($user_id)) {
            // 更新登录时间
            $this->model('user')->update_user_last_login($user_id);
        }

        // 跟踪用户行为
        $this->model('action')->add($user_id, $uuid, '客户端版本比对', get_client(), fetch_ip());

        $ret = array();

        // android
        if ($appid == '1') {
            $arr = array('code' => 418,
                'name' => '版本更新',
                'release' => '修复线上的bug',
                'message' => '哈，我们有新版本发布啦！',
                'downloadUrl' => 'http://www.moxquan.com/download/moxquan-latest.apk',
                'publishDate' => '2019-02-14');
            $ret[] = $arr;
        }

        $this -> jsonReturn($ret);
    }

    /**
     * 发送短信验证码
     */
    public function sms_action()
    {
        set_time_limit(0);

        $this -> jsonReturn();

        $mobile = $_REQUEST['phone'];

        if (strlen(trim($mobile)) !== 11) {
            echo json_encode(array('code' => 201, 'msg' => '出错了'));
            exit();
        }

        $result = FARM_APP::model('sms')->send($mobile, array('code' => rand(1000, 9999)), 1);

        if ($result) {
            $this -> jsonReturn();
        } else {
            $this -> jsonReturn(array(), 0, '抱歉，验证码发送失败！');
        }
    }

    /**
     * 注册验证短信验证码
     */
    public function validate_action()
    {
        $phone = trim($_POST['phone']);
        set_time_limit(0);

        // 手机号不能重复
        $data = $this->model('user')->get_user_info_by_phone($phone);
        if ($data) {
            $this -> jsonReturn(null, -1, '该手机号已经存在！');
        }

        $ret = array('phone' => '18976679980', 'token' => md5('token'), 'expireDate' => date("Y-m-d H:i:s", (time()+86400)));

        $this -> jsonReturn($ret);
    }
}
