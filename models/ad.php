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

class ad_class extends MOX_MODEL
{
    /**
     * 获取activity图
     * @param int $size
     * @return array
     */
    public function get_activity($size = 1)
    {
        $code = "mobile_activity";
        if (!is_mobile()) {
            $code = "pc_activity";
        }

        return $this->fetch_page('ad', 'position_code = "'.$code.'" AND status = 1', 'id desc', 1, $size);
    }

    /**
     * 获取banner图
     * @param int $size
     * @return array
     */
    public function get_banner($size = 5)
    {
        $code = "mobile_slide";
        if (!is_mobile()) {
            $code = "pc_slide";
        }

        return $this->fetch_page('ad', 'position_code = "'.$code.'" AND status = 1', 'id desc', 1, $size);
    }

    /**
     * 获取广告位
     */
    public function get_position()
    {
        $position = [['position_code' => 'mobile_slide', 'name' => '移动首页轮播', "width" => 200, "height" => 200],
                     ['position_code' => 'mobile_activity', 'name' => '移动首页导航栏', "width" => 200, "height" => 100]];

        return $position;
    }

    public function get_data_list($where, $page = 1, $per_page = 10, $order_by = 'id desc')
    {
        if (is_array($where)) {
            $where = implode(' AND ', $where);
        }
        
        $list = $this->fetch_page('ad', $where, $order_by, $page, $per_page);
        $position = $this -> get_position();

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