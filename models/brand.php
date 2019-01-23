<?php
class brand_class extends FARM_MODEL
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
