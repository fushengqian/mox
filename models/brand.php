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

class brand_class extends MOX_MODEL
{
    /**
     * 获取列表
     */
    public function get_data_list($where, $page = 1, $per_page = 10, $order_by = 'id asc')
    {
        if (is_array($where)) {
            $where = implode(' AND ', $where);
        }

        return $this->fetch_page('brand', $where, $order_by, $page, $per_page);
    }
}
