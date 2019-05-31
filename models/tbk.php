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

require_once MOX_PATH.'tbk/TopSdk.php';

class tbk_class extends MOX_MODEL
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

        $result = array("list" => $response->results->n_tbk_item, 'total' => $response -> total_results);

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
    public function get_favorites_list($page_no = 1, $page_size = 100, $type = -1)
    {
        $c = new TopClient;
        $req = new TbkUatmFavoritesGetRequest();
        $req->setPageNo($page_no);
        $req->setPageSize($page_size);
        $req->setFields("favorites_title,favorites_id,type");
        $req->setType($type);
        $response = $c->execute($req);

        return $response->results->tbk_favorites ? $response->results->tbk_favorites : array();
    }

    /**
     * @desc 获取选品库商品列表
     * @param $platform 1:pc， 2:无线
     * @param int $page_no
     */
    public function get_favorites_goods_list($platform = 2, $page_no = 1, $page_size = 100, $adzone_id = 108693250117, $favorites_id = 19405526)
    {
        $c = new TopClient;

        $req = new TbkUatmFavoritesItemGetRequest;
        $req->setPlatform($platform);
        $req->setPageSize($page_size);
        $req->setAdzoneId($adzone_id);
        $req->setUnid("100");
        $req->setFavoritesId($favorites_id);
        $req->setPageNo($page_no);
        $req->setFields("num_iid,title,pict_url,small_images,reserve_price,zk_final_price,user_type,provcity,item_url,click_url,seller_id,volume,nick,shop_title,zk_final_price_wap,event_start_time,event_end_time,tk_rate,status,type");

        $response = $c->execute($req);

        return array('list' => $response->results->uatm_tbk_item, 'total' => $response->total_results);
    }
}
