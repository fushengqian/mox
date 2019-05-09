<?php
class main extends MOX_CONTROLLER
{
    public function index_action()
    {
        $goods_list = $this->model('tbk')->get_favorites_goods_list();


        TPL::import_css('css/mall.css');

        TPL::assign('goods_list', $goods_list);

        //echo "<pre>";
        //print_r($goods_list);

        TPL::output('mall/index');
    }
}
