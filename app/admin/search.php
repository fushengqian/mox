<?php
class search extends FARM_ADMIN_CONTROLLER
{
    /**
     * 清除缓存
     * */
    public function clear_action()
    {
        $this->crumb(FARM_APP::lang()->_t('清空搜索缓存'), "admin/search/clear/");
        
        TPL::assign('menu_list', $this->model('admin')->fetch_menu_list(600));
        
        $sql = 'truncate table shuoly_search_cache';
        $this -> model('system') -> query($sql);
        
        TPL::output('admin/search/clear');
    }
}