<?php
ini_set("display_errors", "on");

require_once FARM_PATH.'ali_sms/vendor/autoload.php';

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;

// 加载区域结点配置
Config::load();

class sms_class extends FARM_MODEL
{
    static $acsClient = null;
    static $maxDay   = 100;//每日发送上限

    /**
     * 取得AcsClient
     * @return DefaultAcsClient
     */
    public static function getAcsClient() {
        //产品名称:云通信流量服务API产品,开发者无需替换
        $product = "Dysmsapi";

        //产品域名,开发者无需替换
        $domain = "dysmsapi.aliyuncs.com";

        // TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
        $accessKeyId = "LTAI3ZGYL42za9mB"; // AccessKeyId

        $accessKeySecret = "QjXWPiE3ITe0G8xWOsY0HJB7Lm6PiU"; // AccessKeySecret

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";

        if (static::$acsClient == null)
        {
            //初始化acsClient,暂不支持region化
            $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

            // 增加服务结点
            DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

            // 初始化AcsClient用于发起请求
            static::$acsClient = new DefaultAcsClient($profile);
        }

        return static::$acsClient;
    }

    /**
     * @desc  发送短信
     * @param string $mobile
     * @param string $content
     * @param int    $type
     * @return void
     */
    public static function send($mobile, $param = array(), $type = 1)
    {
        if (strlen($mobile) != 11)
        {
            return false;
        }

        $data = FARM_APP::model('sms')->fetch_row('sms', 'mobile = "'.$mobile.'"', 'id desc');
        if (!empty($data))
        {
            //一分钟内只能发送一次
            if (time() - $data['create_time'] <= 60)
            {
                return false;
            }
        }

        $today_start = date("Y-m-d".'00:00:00', time());
        $today_end = date("Y-m-d".'23:59:59', time());
        $count = FARM_APP::model('sms')->count('sms', 'create_time <=' .strtotime($today_end).' AND create_time > '.strtotime($today_start));
        if ($count > self::$maxDay)
        {
            return false;
        }

        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        // 必填，设置短信接收号码
        $request->setPhoneNumbers($mobile);

        // 必填，设置签名名称，应严格按"签名名称"
        $request->setSignName("模型圈");

        //验证码
        $content = '暂无短信内容';

        if ($type == 1)
        {
            //您的验证码为：${code}，该验证码5分钟内有效，请勿泄露给他人。
            $request->setTemplateCode("SMS_129758678");
            $request->setTemplateParam(json_encode($param, JSON_UNESCAPED_UNICODE));
            $content = '您的验证码为：'.$param['code'].'，该验证码5分钟内有效，请勿泄露给他人。';
            echo $content;
        }
        else if($type == 2)
        {
            //有游客咨询啦
            $request->setTemplateCode("SMS_129763809");
            $request->setTemplateParam(json_encode($param, JSON_UNESCAPED_UNICODE));
            $content = '您好'.$param['name'].'，游客'.$param['visitor'].'正在咨询预定'.$param['product'].'。回复TA请点击：'.G_DEMAIN.'/message/'.$param['code'].'/';
        }

        // 可选，设置流水号
        $request->setOutId(time());

        // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
        $request->setSmsUpExtendCode("1234567");

        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);

        if ($acsResponse -> Code == 'OK')
        {
            //保存数据库
            FARM_APP::model('sms')->insert('sms', array(
                'mobile' => $mobile,
                'vcode' => !empty($param['code']) ? $param['code'] : '',
                'type' => $type,
                'content' => $content,
                'create_time' => time(),
                'from' => 'ali',
                'status' => 1));

            return true;
        }

        return false;
    }

    public function get_data_list($where, $page = 1, $per_page = 10, $order_by = 'id asc')
    {
        if (is_array($where))
        {
            $where = implode(' AND ', $where);
        }

        $list = $this->fetch_page('sms', $where, $order_by, $page, $per_page);

        return $list;
    }
}
