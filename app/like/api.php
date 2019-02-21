<?php
class api extends FARM_CONTROLLER
{
    /**
     * 点赞
     **/
    public function do_action()
    {
        $user_id = intval(FARM_APP::session()->info['uid']);

        if (empty($user_id)) {
            $this -> jsonReturn([], -1, '您的登录信息已过期！');
        }

        $target_id = trim($_POST['targetId']);
        if (empty($target_id)) {
            $this -> jsonReturn([], -1, '系统参数有误！');
        }

        $user_id = $this->model('user')->get_us($user_id);

        $result = $this->model('like')->dolike($target_id, 'feed', $user_id);
        if (!$result) {
            $this -> jsonReturn([], -1, '抱歉，点赞失败！');
        }

        // 跟踪用户行为
        $this->model('action')->add($user_id, 0, '点赞：'.$target_id, get_client(), fetch_ip());

        //发送消息
        $feed = $this->model('feed')->fetch_row('feed', "id = '".$target_id."'");
        $user_info = $this->model('user')-> get_user_info_by_id($user_id);
        $url = G_DEMAIN.'/feed/'.$target_id.'.html';
        $this->model('message')->send($feed['user_id'], $user_id, '圈友 <b>'.$user_info['user_name'].'</b> 赞了您的动态 <span style="color:#2d64b3;">“'.summary(strip_tags($feed['content']), 30).'”</span>，快去看看吧！', $url, 'like', $target_id);

        $this->model('points')->send($user_id, 'like');

        $user_info = $this->model('user')->get_user_info_by_id($user_id);

        $author = array('id' => $user_info['id'],
                        'name' => $user_info['user_name'],
                        'portrait' => G_DEMAIN.$user_info['avatar'],
                        'relation' => 4,
                        'gender' => $user_info['sex'],
                        'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false));

        $result = array('pubDate' => date_friendly(time()), 'author' => $author, 'liked' => true);

        $this -> jsonReturn($result, 1, '点赞成功！');
    }

    /**
     * 点赞列表
     */
    public function list_action()
    {
        $target_id = trim($_POST['targetId']);
        $type      = trim($_POST['type']);

        $like_list = FARM_APP::model('like')->get_data_list(array('target_id = "'.$target_id.'"', 'type = "'.$type.'"'), 1, 200);

        $likes = array();
        foreach ($like_list as $k => $v) {
            $author = array('id' => $v['user_info']['id'],
                'name' => $v['user_info']['user_name'],
                'portrait' => G_DEMAIN.$v['user_info']['avatar'],
                'relation' => 4,
                'gender' => $v['user_info']['sex'],
                'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false));

            $likes[] = array('id' => $v['id'],
                'liked' => true,
                'pubDate' => date_friendly($v['create_time']),
                'author' => $author);
        }

        $result['items'] = $likes;
        $result['nextPageToken'] = 0;
        $result['prevPageToken'] = 0;
        $result['requestCount'] = 200;
        $result['responseCount'] = count($likes);
        $result['totalResults'] = count($likes);

        $time = date("Y-m-d H:i:s", time());

        $this -> jsonReturn($result, 1, 'SUCCESS', null, $time);
    }
}
