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

        $list = $this->fetch_page('message', 'user_id = "'.$user_id.'" AND is_read = 0', 'id desc', 1, 999);

        $notice = array('like' => 0,
                        'review' => 0,
                        'letter' => 0,
                        'newsCount' => 0,
                        'mention' => 0,
                        'fans' => 0);

        foreach ($list as $k => $v) {
            switch ($v['type']) {
                case 'like':
                    $notice['like']++;
                break;
                case 'comment':
                    $notice['review']++;
                break;
                case 'letter':
                    $notice['letter']++;
                break;
                case 'newsCount':
                    $notice['newsCount']++;
                break;
                case 'like':
                    $notice['mention']++;
                break;
                case 'fans':
                    $notice['fans']++;
                break;
            }
        }

        return $notice;
    }
}
