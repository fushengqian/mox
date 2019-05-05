<?php
class main extends FARM_CONTROLLER
{
    public function index_action()
    {
        $goods_list = $this->model('tbk')->get_goods_list();



        echo "<pre>";
        print_r($goods_list);die;

        TPL::output('main/index');
    }
}
