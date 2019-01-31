<?php
class api extends FARM_CONTROLLER
{
    /**
     * 动态列表
     **/
    public function list_action()
    {
        $page_size = 15;

        // 当前页
        $page = !empty($_POST['page']) ? $_POST['page'] : 1;
        $user_id = $_POST['authorId'] ? $_POST['authorId'] : 0;

        $where = '';
        if ($user_id) {
            $where = array('user_id' => $user_id);
        }

        $feed_list = $this->model('feed')-> get_data_list($where, $page, $page_size);

        $feed_arr = array();
        foreach ($feed_list as $k => $v) {
            $arr =  array('appClient' => 1,
                            'author' => array('id' => $v['user_info']['id'], 'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false),
                            'name' => $v['user_info']['user_name'], 'portrait' => G_DEMAIN.$v['user_info']['avatar'], 'relation' => '4'),
                            'commentCount' => $v['comment_num'],
                            'content' => strip_tags($v['content']),
                            'href' => G_DEMAIN.'/feed/'.$v['id'].'.html',
                            'id' => $v['id'],
                            'images' => $v['images'],
                            'likeCount' => $v['like_num'],
                            'liked' => false,
                            'pubDate' => date_friendly($v['create_time']),
                            'statistics' => array('comment' => $v['comment_num'], 'favCount' => 0, 'like' => $v['like_num'], 'transmit' => 0, 'view' => rand(1000, 9999)));
            $feed_arr[] = $arr;
        }

        $result = array();

        $time = date("Y-m-d H:i:s", time());

        $notice = array('like' => 0,
                         'review' => 0,
                         'letter' => 0,
                         'newsCount' => 0,
                         'mention' => 0,
                         'fans' => 0);

        $result['items'] = $feed_arr;
        $result['nextPageToken'] = ($page+1);
        $result['prevPageToken'] = ($page-1) > 0 ? ($page-1) : 1;
        $result['requestCount'] = $page_size;
        $result['responseCount'] = count($feed_arr);
        $result['totalResults'] = 1000;

        $this -> jsonReturn($result, 1, 'SUCCESS', $notice, $time);
    }

    /**
     * 发布动态
     **/
    public function create_action()
    {
        $user_id = FARM_APP::session()->info['uid'];
        if (empty($user_id)) {
            $this -> jsonReturn([], -1, '您的登录信息已过期！');
        }

        $content  = trim($_POST['content']);
        $topic_id = trim($_POST['topic_id']);
        $token    = trim($_POST['images']);

        $user_id = intval(FARM_APP::session()->info['uid']);
        $user_id = $this->model('user')->get_us($user_id);

        // 图片
        if ($token) {
            $pic_list = FARM_APP::model('system')->fetch_all('upload_token', 'token = "' . $token . '"');
            $pics = array();
            empty($pic_list) && $pic_list = array();
            foreach ($pic_list as $k => $v) {
                $pics[] = $v['url'];
            }
        }

        $this->model('feed')->create($content, $pics, $user_id, $topic_id);

        $result = array('user_id' => $user_id);

        $this->jsonReturn($result);
    }
}
