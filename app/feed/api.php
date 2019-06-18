<?php
/**
+--------------------------------------------------------------------------
|   Mox 1.0.1
|   ========================================
|   by Mox Software
|   © 2018 - 2019 Mox. All Rights Reserved
|   http://www.mox365.com
|   ========================================
|   Support: 540335306@qq.com
|   Author: FSQ
+---------------------------------------------------------------------------
*/

class api extends MOX_CONTROLLER
{
    /**
     * 动态列表
     **/
    public function list_action()
    {
        $page_size = 15;

        $my_user_id = MOX_APP::session()->info['uid'];
        if (!$my_user_id) {
            $my_user_id = 0;
        }

        // 当前页
        $page = !empty($_POST['page']) ? $_POST['page'] : 1;
        $user_id = $_POST['userId'] ? trim($_POST['userId']) : 0;
        $tab = $_POST['tab'] ? $_POST['tab'] : 0;//0：最新; 1：关注; 2：热门

        $where = array();

        // 我的关注
        if ($my_user_id && $user_id && ($my_user_id == $user_id)) {
            $my_follower_list =  $this -> model('system')->fetch_all('follow', "user_id = '".$my_user_id."'");
            $my_follower = array($my_user_id);
            if ($my_follower_list) {
                foreach ($my_follower_list as $f){
                    $my_follower[] = $f['follow_user_id'];
                }
            }
            $where[] = 'user_id in ('.implode(',', array_unique($my_follower)).')';
        }

        // 某个人的动态
        if($user_id && ($my_user_id != $user_id)) {
            $where[] = 'user_id = "'.$user_id.'"';
        }

        // 热门动态
        if ($tab == '2') {
            $where[] = "is_home = 1";
        }

        $feed_list = $this->model('feed')-> get_data_list($where, $page, $page_size);

        $feed_arr = array();
        foreach ($feed_list as $k => $v) {
            $arr =  array('appClient' => 1,
                            'author' => array('id' => $v['user_info']['id'], 'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false),
                            'name' => $v['user_info']['user_name'], 'portrait' => G_DEMAIN.$v['user_info']['avatar'], 'relation' => 4),
                            'commentCount' => $v['comment_num'],
                            'content' => strip_tags($v['content']),
                            'href' => G_DEMAIN.'/feed/'.$v['id'],
                            'id' => $v['id'],
                            'images' => $v['images'],
                            'likeCount' => $v['like_num'],
                            'liked' => false,
                            'pubDate' => date_friendly($v['update_time']),
                            'statistics' => array('comment' => $v['comment_num'], 'favCount' => 0, 'like' => $v['like_num'], 'transmit' => rand(100, 1000), 'view' => rand(1000, 9999)));
            $feed_arr[] = $arr;
        }

        $result = array();

        $time = date("Y-m-d H:i:s", time());

        $result['list'] = $feed_arr;
        $result['pagesize'] = $page_size;
        $result['total'] = 1000;

        // 更新最后一次刷新时间
        $this->model('user')->update_user_last_feed_time($my_user_id);

        $this -> jsonReturn($result, 1, 'SUCCESS', null, $time);
    }

    /**
     * 发布动态
     **/
    public function create_action()
    {
        $user_id = MOX_APP::session()->info['uid'];
        if (empty($user_id)) {
            $this -> jsonReturn([], -1, '您的登录信息已过期！');
        }

        $content  = trim($_POST['content']);
        $topic_id = trim($_POST['topic_id']);
        $token    = trim($_POST['upload_token']);
        $images   = $_POST['images'];

        $user_id = intval(MOX_APP::session()->info['uid']);
        $user_id = $this->model('user')->get_us($user_id);
        $pics = array();

        // 上传token图片
        if ($token) {
            $pic_list = MOX_APP::model('system')->fetch_all('upload_token', 'token = "' . $token . '"');
            empty($pic_list) && $pic_list = array();
            foreach ($pic_list as $k => $v) {
                $pics[] = $v['url'];
            }
        }

        if (!empty($images) && empty($token)) {
            $pics = $images;
        }

        $this->model('feed')->create($content, $pics, $user_id, $topic_id);

        $result = array('user_id' => $user_id);

        $this->jsonReturn($result);
    }

    /**
     * 动态详情
     * */
    public function detail_action()
    {
        $user_id = MOX_APP::session()->info['uid'];
        $feed_id = trim($_POST['id']);

        $this->model('action')->add($user_id, 0, '查看动态'.$feed_id, get_client(), fetch_ip());

        if (empty($feed_id)) {
            $this -> jsonReturn([], -1, '系统参数有误！');
        }

        $feed = $this->model('feed')->get_detail($feed_id);

        $author = array('id' => $feed['user_info']['id'],
                        'name' => $feed['user_info']['user_name'],
                        'portrait' => G_DEMAIN.$feed['user_info']['avatar'],
                        'relation' => 4,
                        'gender' => $feed['user_info']['sex'],
                        'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false));

        $statistics = array('comment' => $feed['comment_num'], 'favCount' => 0, 'like' => $feed['like_num'], 'transmit' => 0, 'view' => rand(1000, 9999));

        $about = array(
                 'id' => 1001,
                 'title' => '大爱野猫',
                 'content' => '莫名其妙地开始喜欢肥猫。老田的老板件，配了牛魔王座舱蚀刻片，一天完成的练习作。旧化还可以更狠一些 建议喷后置 掉漆也狠一些 比如机翼上部的飞行员踩踏部位。',
                 'type' => 1,
                 'href' => 'http://www.mox365.com',
                 'viewCount' => 100232,
                 'commentCount' => 1023,
                 'transmitCount' => 1923,
                 'images' => $feed['images']
        );

        $result = array('id' => intval($feed['id']),
                        'content' => $feed['content'],
                        'appClient' => 1,
                        'commentCount' => intval($feed['comment_num']),
                        'likeCount' => intval($feed['like_num']),
                        'liked' => false,
                        'pubDate' => date_friendly($feed['create_time']),
                        'author' => $author,
                        'code' => array('brush' => $feed['id'], 'content' => $feed['id']),
                        'href' => G_DEMAIN.'/feed/'.$feed['id'].'.html',
                        'audio' => array(),
                        'images' => $feed['images'],
                        'statistics' => $statistics,
                        'about' => null);

        $this->jsonReturn($result);
    }
}
