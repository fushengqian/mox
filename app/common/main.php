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

class main extends MOX_CONTROLLER
{
    /**
     * 分享到微信
     */
    public function weixinshare_action()
    {
        $url = $_REQUEST['url'] ? $_REQUEST['url'] : G_DEMAIN;
        TPL::assign('url', $url);
        TPL::output('common/shareweixin');
    }

    /**
     * 生成二维码
     */
    public function qr_action()
    {
        $url = $_REQUEST['url'] ? $_REQUEST['url'] : G_DEMAIN;
        $url = str_replace('/', '', $url);
        $url = base64_decode($url);
        return qrCode($url);
    }
}
