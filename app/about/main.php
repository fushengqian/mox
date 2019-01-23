<?php
class main extends FARM_CONTROLLER
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
