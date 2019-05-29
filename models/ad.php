<?php
/**
+--------------------------------------------------------------------------
|   Mox 1.0.1
|   ========================================
|   by Mox Software
|   © 2018 - 2019 Mox. All Rights Reserved
|   http://www.moxquan.com
|   ========================================
|   Support: 540335306@qq.com
|   Author: FSQ
+---------------------------------------------------------------------------
*/

class ad_class extends MOX_MODEL
{
    /**
     * 获取广告位
     */
    public function getPosition()
    {
        $position = [['position_code' => 'mobile_slide', 'name' => '移动首页轮播'],
                     ['position_code' => 'mobile_nav', 'name' => '移动首页导航栏']];

        return $position;
    }

    /**
     * 添加广告
     * @param int $user_id
     * @param int $uuid
     * @param string $content
     * @param string $client
     * @param string $ip
     * @return int
     */
    public function add($user_id = 0, $uuid = 0, $content = '', $client = '', $ip = '')
    {
        $ad_id = $this -> insert('action', array(
                                        'content' => $content,
                                        'user_id' => $user_id,
                                        'uuid' => $uuid,
                                        'client' => $client,
                                        'ip' => $ip,
                                        'create_time' => time(),
                                        'status' => 1));

        return $ad_id;
    }

    public function get_data_list($where, $page = 1, $per_page = 10, $order_by = 'id desc')
    {
        if (is_array($where)) {
            $where = implode(' AND ', $where);
        }
        
        $list = $this->fetch_page('ad', $where, $order_by, $page, $per_page);
        $position = $this -> getPosition();

        foreach($list as $key => $value) {
            foreach($position as $v) {
                if ($v['position_code'] == $value['position_code']) {
                    $list[$key]['position'] = $v;
                }
            }
        }

        return $list;
    }
}