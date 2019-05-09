<?php
class message_class extends MOX_MODEL
{
    /**
     * 发送消息
     * @param int $user_id
     * @param int $from_user_id
     * @param string $content
     * @param string $url
     * @param string $type
     * @param string $target_id
     * @param string $image
     * @return int
     */
    public function send($user_id, $from_user_id, $content, $url = '', $type = 'system', $target_id = 0, $image = '')
    {
        $msg_id = $this -> insert('message', array(
            'content'     => $content,
            'target_id' => $target_id,
            'user_id'     => $user_id,
            'image' => $image,
            'from_user_id' => $from_user_id,
            'is_read'     => 0,
            'url'       => $url,
            'type' => $type,
            'create_time' => time(),
            'update_time' => time(),
            'status'      => 0
        ));

        return $msg_id;
    }

    public function get_data_list($where, $page = 1, $per_page = 10, $order_by = 'id desc')
    {
        if (is_array($where))
        {
            $where = implode(' AND ', $where);
        }
        
        $list = $this->fetch_page('message', $where, $order_by, $page, $per_page);

        return $list;
    }

    /**
     * 更新为已读
     * @param  int
     */
    public function set_readed($user_id)
    {
        if (!$user_id)
        {
            return false;
        }

        return $this->shutdown_update('message', array(
            'update_time' => time(),
            'is_read' => 1
        ), 'user_id = ' . intval($user_id). ' AND is_read = 0');
    }
}