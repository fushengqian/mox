<?php
class api extends FARM_CONTROLLER
{
    /**
     * 提交评论
     **/
    public function comment_action()
    {
        $user_id = intval(FARM_APP::session()->info['uid']);

        if (empty($user_id)) {
            $this -> jsonReturn([], -1, '您的登录信息已过期！');
        }

        $target_id = trim($_POST['targetId']);
        $parent_id = !empty($_POST['parent_id']) ? trim($_POST['parent_id']) : 0;
        $content   = trim($_POST['content']);
        $type   = intval($_POST['type']);
        $toUserId = trim($_POST['toUserId']);

        if (empty($target_id) || empty($content)) {
            $this -> jsonReturn([], -1, '抱歉，系统出错！');
        }

        if (!empty($toUserId)) {
            $uinfo = $this->model('user')->get_user_info_by_id($toUserId);
            $content = '@'.$uinfo['user_name']."：".$content;
            // 发送一条私信
            $this->model('message')->send($toUserId, $user_id, '我回复了你：'.$content, '', 'letter', 0, '');
        }

        if ($type == 1) {
            $type = 'article';
        } else {
            $type = 'feed';
        }

        $this->model('action')->add($user_id, 0, '评论了'.$target_id, get_client(), fetch_ip());

        $user_id = $this->model('user')->get_us($user_id);

        $result = $this->model('comment')->comment($target_id, $parent_id, $type, $user_id, $content);
        if (!$result) {
            $this -> jsonReturn([], -1, '评论失败！');
        }

        $user_info = $this->model('user')->get_user_info_by_id($user_id);
        $user_info['avatar'] = G_DEMAIN.$user_info['avatar'];
        $arr = array('user_info' => $user_info, 'comment' => $content);

        //发送消息
        if ($type == 'feed') {
            $feed = $this->model('feed')->fetch_row('feed', "id = '" . $target_id . "'");
            $url = G_DEMAIN . '/feed/' . $target_id . '.html';
            $this->model('message')->send($feed['user_id'], $user_id, '圈友 <b>' . $user_info['user_name'] . '</b> 评论了您的动态 <span style="color:#2d64b3;">“' . summary(strip_tags($content), 30) . '”</span>，快去看看吧！', $url, 'comment', $target_id);
        }

        $this->model('points')->send($user_id, 'comment');

        $this -> jsonReturn($arr, 1, '评论成功！');
    }

    /**
     * 评论列表
     */
    public function list_action()
    {
        $target_id = $_POST['targetId'] ? trim($_POST['targetId']) : 0;
        $type      = $_POST['type'] ? intval($_POST['type']) : '0';
        $page      = $_POST['page'] ? trim($_POST['page']) : 0;
        $user_id = intval(FARM_APP::session()->info['uid']);

        if ($type == 1) {
            $type = 'article';
        } else {
            $type = 'feed';
        }

        $comment_list = FARM_APP::model('comment')->get_comment_by_targetids(array($target_id), $type, $user_id);

        $comments = array();

        if (empty($target_id)) {
            foreach ($comment_list as $k => $v) {
                $author = array('id' => $v[0]['user_info']['id'],
                                'name' => $v[0]['user_info']['user_name'],
                                'portrait' => G_DEMAIN.$v[0]['user_info']['avatar'],
                                'relation' => 4,
                                'gender' => $v[0]['user_info']['sex'],
                                'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false));

                $feed = $this->model('feed')->fetch_row('feed', "id = '".$v[0]['target_id']."'");
                $origin = array('id' => $v[0]['target_id'],
                                'desc' => summary(strip_tags($feed['content']), 35),
                                'href' => '',
                                'type' => 100);

                $comments[] = array('id' => $v[0]['id'],
                                    'content' => strip_tags($v[0]['content']),
                                    'pubDate' => date_friendly($v[0]['create_time']),
                                    'appClient' => 1,
                                    'origin' => $origin,
                                    'author' => $author);
            }
        } else {
            foreach ($comment_list[$target_id] as $k => $v) {
                $author = array('id' => $v['user_info']['id'],
                                'name' => $v['user_info']['user_name'],
                                'portrait' => G_DEMAIN.$v['user_info']['avatar'],
                                'relation' => 4,
                                'gender' => $v['user_info']['sex'],
                                'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false));

                $comments[] = array('id' => $v['id'],
                                    'content' => strip_tags($v['content']),
                                    'pubDate' => date_friendly($v['create_time']),
                                    'appClient' => 1,
                                    'author' => $author);
            }
        }

        if ($page > 1) {
            $comments = array();
        }

        $result['items'] = $comments;
        $result['nextPageToken'] = 2;
        $result['prevPageToken'] = 0;
        $result['requestCount'] = 200;
        $result['responseCount'] = count($comments);
        $result['totalResults'] = count($comments);

        $time = date("Y-m-d H:i:s", time());

        $this -> jsonReturn($result, 1, 'SUCCESS', null, $time);
    }
}
