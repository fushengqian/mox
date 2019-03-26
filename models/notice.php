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

        $user_info = FARM_APP::model('user')->get_user_info_by_id($user_id);

        $feedCount = FARM_APP::model('feed')->count('feed', '`create_time` >= "'.$user_info['last_feed_time'].'"');
        $articleCount = FARM_APP::model('article')->count('article', '`create_time` >= "'.$user_info['last_article_time'].'"');

        $notice = array('like' => 0,
                        'review' => 0,
                        'letter' => 0,
                        'newsCount' => 0,
                        'mention' => 0,
                        'feedCount' => $feedCount,
                        'articleCount' => $articleCount,
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
