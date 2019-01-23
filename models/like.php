<?php
class like_class extends FARM_MODEL
{
    public function get_data_list($where, $page = 1, $per_page = 10, $order_by = 'create_time desc')
    {
        if (is_array($where)) {
            $where = implode(' AND ', $where);
        }
        
        $list = $this->fetch_page('like', $where, $order_by, $page, $per_page);

        $user_ids = array(0);
        foreach($list as $k => $v) {
            $user_ids[] = $v['user_id'];
        }

        $user_arr = FARM_APP::model('user')->get_user_by_ids($user_ids);
        foreach($list as $key => $value) {
            $list[$key]['user_info'] = $user_arr[$value['user_id']];
        }

        return $list;
    }

    /**
     * 点赞
     * @param string $target_id
     * @param string  $type
     * @param int    $user_id
     * @return boolean
     */
    public function dolike($target_id, $type, $user_id)
    {
        $data = $this->fetch_row('like', "target_id = '".$target_id."' and user_id = '".$user_id."' and `type` = 'feed'");
        if ($data) {
            return false;
        }

        $like_id = $this -> insert('like', array(
            'target_id' => $target_id,
            'user_id'   => $user_id,
            'type'      => $type,
            'create_time' => time(),
            'update_time' => time(),
            'status'      => 1
        ));

        return $like_id;
    }
}