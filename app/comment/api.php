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

        if (empty($target_id) || empty($content)) {
            $this -> jsonReturn([], -1, '抱歉，系统出错！');
        }

        $user_id = $this->model('user')->get_us($user_id);

        $result = $this->model('comment')->comment($target_id, $parent_id, 'feed', $user_id, $content);
        if (!$result) {
            $this -> jsonReturn([], -1, '评论失败！');
        }

        $user_info = $this->model('user')->get_user_info_by_id($user_id);
        $user_info['avatar'] = G_DEMAIN.$user_info['avatar'];
        $arr = array('user_info' => $user_info, 'comment' => $content);

        //发送消息
        $feed = $this->model('like')->fetch_row('feed', "id = '".$target_id."'");
        $url = G_DEMAIN.'/feed/'.$target_id.'.html';
        $this->model('message')->send($feed['user_id'], 0, '圈友 <b>'.$user_info['user_name'].'</b> 评论了您的动态 <span style="color:#2d64b3;">“'.summary(strip_tags($content), 30).'”</span>，快去看看吧！', $url, 'comment');

        $this->model('points')->send($user_id, 'comment');

        $this -> jsonReturn($arr, 1, '评论成功！');
    }

    /**
     * 评论列表
     */
    public function list_action()
    {
        $target_id = trim($_POST['targetId']);
        $type      = trim($_POST['type']);
        $page      = trim($_POST['page']);
        $user_id = intval(FARM_APP::session()->info['uid']);

        $comment_list = FARM_APP::model('comment')->get_comment_by_targetids(array($target_id), $type, $user_id);

        $comments = array();
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

        if ($page > 0) {
            $comments = array();
        }

        $result['items'] = $comments;
        $result['nextPageToken'] = 2;
        $result['prevPageToken'] = 2;
        $result['requestCount'] = 200;
        $result['responseCount'] = count($comments);
        $result['totalResults'] = count($comments);

        $time = date("Y-m-d H:i:s", time());

        $notice = array('like' => 0,
                        'review' => 0,
                        'letter' => 0,
                        'newsCount' => 0,
                        'mention' => 0,
                        'fans' => 0);

        $this -> jsonReturn($result, 1, 'SUCCESS', $notice, $time);
    }
}
