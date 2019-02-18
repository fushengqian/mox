<?php
class api extends FARM_CONTROLLER
{
    /**
     * 清空消息列表
     * */
    public function clear_action()
    {
        $user_id = FARM_APP::session()->info['uid'];

        // 全部消息置为已读
        $this->model('message')->set_readed($user_id);

        $this -> jsonReturn(null, 1, 'SUCCESS');
    }

    /**
     * 获取系统消息
     */
    public function system_action()
    {
        $user_id = FARM_APP::session()->info['uid'];

        $list = $this->model('message')->get_data_list(array('user_id = '.intval($user_id), 'type = "system"'), 1, 1000);

        $arr = array();
        foreach ($list as $k => $v) {
            $author = array('id' => 100,
                            'name' => '模型圈',
                            'portrait' => 'http://www.moxquan.com/static/avatar/default.jpg',
                            'relation' => 4,
                            'gender' => 1,
                            'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false));

            $origin = array('id' => 0,
                            'desc' => '',
                            'href' => '',
                            'type' => 1);

            $arr[] = array('id' => $v['id'],
                            'content' => strip_tags($v['content']),
                            'pubDate' => date_friendly($v['create_time']),
                            'appClient' => 1,
                            'origin' => $origin,
                            'author' => $author);
        }

        $result['items'] = $arr;
        $result['nextPageToken'] = 2;
        $result['prevPageToken'] = 0;
        $result['requestCount'] = 200;
        $result['responseCount'] = count($arr);
        $result['totalResults'] = count($arr);

        $time = date("Y-m-d H:i:s", time());

        $this -> jsonReturn($result, 1, 'SUCCESS', null, $time);
    }

    /**
     * 获取点赞消息
     */
    public function like_action()
    {
        $user_id = FARM_APP::session()->info['uid'];

        $list = $this->model('message')->get_data_list(array('user_id = '.intval($user_id), 'type = "like"'), 1, 1000);

        $arr = array();
        foreach ($list as $k => $v) {
            $user_info = $this->model('user')->get_user_info_by_id($v['from_user_id']);
            $sender = array('id' => $user_info['id'],
                            'name' => $user_info['user_name'],
                            'portrait' => G_DEMAIN.$user_info['avatar'],
                            'relation' => 4,
                            'gender' => 1,
                            'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false));

            $feed = $this->model('feed')->fetch_row('feed', "id = '".$v['target_id']."'");
            $origin = array('id' => $feed['id'],
                            'desc' => summary(strip_tags($feed['content']), 45),
                            'href' => '',
                            'type' => 100);

            $arr[] = array('id' => $v['id'],
                            'content' => strip_tags($v['content']),
                            'pubDate' => date_friendly($v['create_time']),
                            'appClient' => 1,
                            'origin' => $origin,
                            'sender' => $sender);
        }

        $result['items'] = $arr;
        $result['nextPageToken'] = 2;
        $result['prevPageToken'] = 0;
        $result['requestCount'] = 200;
        $result['responseCount'] = count($arr);
        $result['totalResults'] = count($arr);

        $time = date("Y-m-d H:i:s", time());

        $this -> jsonReturn($result, 1, 'SUCCESS', null, $time);
    }
}
