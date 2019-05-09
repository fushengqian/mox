<?php
class action_class extends MOX_MODEL
{
    /**
     * 记录用户行为
     * @param int $user_id
     * @param int $uuid
     * @param string $content
     * @param string $client
     * @param string $ip
     * @return int
     */
    public function add($user_id = 0, $uuid = 0, $content = '', $client = '', $ip = '')
    {
        $msg_id = $this -> insert('action', array(
                        'content' => $content,
                        'user_id' => $user_id,
                        'uuid' => $uuid,
                        'client' => $client,
                        'ip' => $ip,
                        'create_time' => time(),
                        'status' => 1));

        return $msg_id;
    }

    public function get_data_list($where, $page = 1, $per_page = 10, $order_by = 'id desc')
    {
        if (is_array($where)) {
            $where = implode(' AND ', $where);
        }
        
        $list = $this->fetch_page('action', $where, $order_by, $page, $per_page);
        $user_ids = array(0);
        foreach($list as $k => $v) {
            $user_ids[] = $v['user_id'];
        }

        $user_arr = MOX_APP::model('user')->get_user_by_ids($user_ids);
        foreach($list as $key => $value) {
            $list[$key]['user_info'] = $user_arr[$value['user_id']];
        }

        return $list;
    }
}