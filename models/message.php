<?php
class message_class extends FARM_MODEL
{
    /**
     * 发送消息
     * @param int $user_id
     * @param int $from_user_id
     * @param string $content
     * @param string $url
     * @param string $type
     * @return int
     */
    public function send($user_id, $from_user_id, $content, $url = '', $type = 'system')
    {
        $msg_id = $this -> insert('message', array(
            'content'     => $content,
            'user_id'     => $user_id,
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