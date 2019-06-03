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

class core_captcha
{
    private $captcha;
    
    function makecaptchaAction(){
        $this->_helper->layout->disableLayout();
        $captcha = new Zend_Captcha_Image(array(
            'font'=>'annex/font/Faktos.ttf', //字体文件路径
            'fontsize'=>30, //字号
            'imgdir'=>'temp/captcha', //验证码图片存放位置
            'width'=>150, //图片宽
            'height'=>70, //图片高
            'gcFreq'=>3,
            'wordlen'=>5 )); //字母数
        $captcha->generate(); //生成图片
        $captcha->render();
        $captcha_imgpath=$captcha->getImgDir().$captcha->getId().'.png';
        header("Content-type: image/png");
        imagepng(imagecreatefrompng($captcha_imgpath));
    }
    
    public function __construct()
    {
        $img_dir = ROOT_PATH . 'cache/captcha/';
        if (!is_dir($img_dir))
        {
            @mkdir($img_dir);
        }
        
        $this->captcha = new Zend_Captcha_Image(array(
            'font' => $this->get_font(),
            'imgdir' => $img_dir,
            'fontsize' => 18,
            'width' => 100,
            'height' => 40,
            'wordlen' => 4,
            'session' => new Zend_Session_Namespace(G_COOKIE_PREFIX . '_Captcha'),
            'timeout' => 600
        ));
        
        $this->captcha->setDotNoiseLevel(rand(1, 2));
        $this->captcha->setLineNoiseLevel(rand(1, 2));
    }
    
    public function get_font()
    {
        return $captcha_fonts = MOX_PATH . 'core/fonts/elephant.ttf';
    }
    
    public function generate()
    {
        $this->captcha->generate();
        HTTP::no_cache_header();
        $this->captcha->render();
        $captcha_imgpath=$this->captcha->getImgDir().$this->captcha->getId().'.png';
        header("Content-type: image/png");
        imagepng(imagecreatefrompng($captcha_imgpath));
        die;
    }
    
    public function is_validate($validate_code, $generate_new = true)
    {
        if (strtolower($this->captcha->getWord()) == strtolower($validate_code))
        {
            if ($generate_new)
            {
                $this->captcha->generate();
            }
            
            return true;
        }
        
        return false;
    }
}