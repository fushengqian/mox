<?php
class notice_class extends FARM_MODEL
{
    /**
     * 获取消息
     * @param int $user_id
     * @return array
     */
    public function get_notice($user_id) {

        if (empty($user_id)) {
            return null;
        }

        $notice = array('like' => 10,
                        'review' => 5,
                        'letter' => 6,
                        'newsCount' => 0,
                        'mention' => 12,
                        'fans' => 2);

        return $notice;
    }
}
