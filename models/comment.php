<?php
class comment_class extends FARM_MODEL
{
    public function comment($target_id, $parent_id, $type, $user_id, $content)
    {
        $arr = array(
              'target_id' => $target_id,
              'parent_id' => $parent_id,
              'content' => $content,
              'type' => $type,
              'user_id' => $user_id,
              'create_time' => time(),
              'status' => 1);
        
        return $this->insert('comment', $arr);
    }
    
    public function get_data_list($where, $page = 1, $per_page = 10, $order_by = 'id asc')
    {
        if (is_array($where))
        {
            $where = implode(' AND ', $where);
        }
        
        return $this->fetch_page('comment', $where, $order_by, $page, $per_page);
    }

    public function get_comment_by_targetids($target_ids, $type, $user_id = 0)
    {
        $target_ids = array_unique($target_ids);

        if (empty($target_ids) && empty($user_id)) {
            return array();
        }

        if ($user_id) {
            $comment_info = $this->fetch_all('comment', "`user_id` = '".$user_id."'", 'create_time asc');
        } else {
            $comment_info = $this->fetch_all('comment', "target_id IN(" . implode(',', $target_ids) . ") AND `type` = '".$type."'", 'create_time asc');
        }

        // 批量获取用户
        $user_ids = array(0);
        foreach ($comment_info as $k => $v) {
            $user_ids[] = $v['user_id'];
        }
        $user_arr = FARM_APP::model('user')->get_user_by_ids($user_ids);
        foreach ($comment_info as $key1 => $value1) {
            $comment_info[$key1]['user_info'] = $user_arr[$value1['user_id']];
        }

        $result = array();
        foreach ($comment_info as $key => $value) {
            foreach ($target_ids as $id) {
                if ($value['target_id'] == $id) {
                    $result[$id][] = $value;
                }
            }
        }

        return $result;
    }
}