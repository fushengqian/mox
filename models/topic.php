<?php
class topic_class extends MOX_MODEL
{
    /**
     * 获取列表
     */
    public function get_data_list($where, $page = 1, $per_page = 10, $order_by = 'create_time desc')
    {
        if (is_array($where)) {
            $where = implode(' AND ', $where);
        }

        return $this->fetch_page('topic', $where, $order_by, $page, $per_page);
    }
}
