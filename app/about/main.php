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
    public function index_action()
    {
        fix_client();

        TPL::assign('seo', get_seo('about'));
        
        TPL::output('about/index');
    }
    
    public function jobs_action()
    {
        fix_client();

        TPL::assign('seo', get_seo('jobs'));
        
        TPL::output('about/jobs');
    }
    
    public function contact_action()
    {
        fix_client();

        TPL::assign('seo', get_seo('contact'));
        
        TPL::output('about/contact');
    }
}
