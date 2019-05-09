<?php
class logs_class extends MOX_MODEL
{
    public function get_data_list($where, $page = 1, $per_page = 10, $order_by = 'id asc')
    {
        if (is_array($where))
        {
            $where = implode(' AND ', $where);
        }
        
        $list = $this->fetch_page('logs', $where, $order_by, $page, $per_page);

        return $list;
    }
}