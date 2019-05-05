<?php
require_once FARM_PATH.'tbk/TopSdk.php';

class tbk_class extends FARM_MODEL
{
    /**
     * @desc 获取商品列表
     * @param string $keyword
     * @param int $cate 后台类目ID，用,分割，最大10个
     * @param string $sort 排序_des（降序），排序_asc（升序），销量（total_sales），淘客佣金比率（tk_rate）， 累计推广量（tk_total_sales），总支出佣金（tk_total_commi）
     * @param boolean $is_tmall 是否商城商品
     * @param boolean $is_overseas 是否海外商品
     * @param int $platform 1:PC, 2:移动 默认1
     * @param int $page_no
     * @param int $page_size
     * @return array
     */
    public function get_goods_list($keyword = '小号手', $cate = '', $sort = 'tk_total_commi', $is_tmall = false, $is_overseas = false, $platform = 1, $page_no = 1, $page_size = 20)
    {
        $c = new TopClient;
        $req = new TbkItemGetRequest;
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,seller_id,volume,nick");
        $req->setQ($keyword);
        $req->setCat($cate);
        $req->setSort($sort);
        $req->setIsTmall($is_tmall);
        $req->setIsOverseas($is_overseas);
        $req->setPlatform($platform);
        $req->setPageNo($page_no);
        $req->setPageSize($page_size);
        $response = $c->execute($req);

        $result = array("list" => $response->results, 'total' => $response -> total_results);

        return $result;
    }

    /**
     * @desc 获取商品详情
     * @param string $goods_ids
     * @param int    $platform
     * @param string $ip
     * @return array
     */
    public function get_goods_detail($goods_ids = '', $platform = 1, $ip = '')
    {
        $c = new TopClient;
        $req = new TbkItemInfoGetRequest;
        $req->setNumIids($goods_ids);
        $req->setPlatform($platform);
        $req->setIp($ip);
        $response = $c->execute($req);

        $result = $response -> results;

        return $result;
    }

    /**
     * @desc 获取选品库列表
     * @param int $page_no
     */
    public function get_favorites_list($page_no = 1, $page_size = 20, $type = -1)
    {
        return array();
    }
}
