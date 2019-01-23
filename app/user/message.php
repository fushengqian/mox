<?php
class message extends FARM_CONTROLLER
{
    /**
     * 我的留言列表
     * */
    public function index_action()
    {
        TPL::import_css('css/user.css');
        
        TPL::assign('seo', get_seo('user_message'));
        
        $user_id = FARM_APP::session()->info['uid'];
        
        if (empty($user_id))
        {
            HTTP::redirect(G_DEMAIN.'/user/login/');
            exit;
        }

        $user_info = $this->model('user') -> get_user_info_by_id($user_id);
        
        $list = $this->model('message') -> get_data_list('farm_id = '.intval($user_info['farm_id']), 1, 100, 'id desc');

        $this -> model('message') -> set_readed(intval($user_info['farm_id']));

        TPL::assign('list', $list);
        
        TPL::output('user/message/index');
    }
}