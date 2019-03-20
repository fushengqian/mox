<?php
class api extends FARM_CONTROLLER
{
    /**
     * 头条资讯
     */
    public function banner_action()
    {
        $arr = $this->model('article')->get_banner(5);

        $result = array();
        $time = date("Y-m-d H:i:s", time());
        $result['items'] = $arr;
        $result['nextPageToken'] = "1";
        $result['prevPageToken'] = "1";
        $result['requestCount'] = 5;
        $result['responseCount'] = count($arr);
        $result['totalResults'] = count($arr);

        $this -> jsonReturn($result, 1, 'SUCCESS', null, $time);
    }

    /**
     * 文章列表
     */
    public function list_action()
    {
        $cate = $_POST['cate'] ? trim($_POST['cate']) : 'focus';
        $page_size = 10;
        $page = !empty($_POST['page']) ? intval($_POST['page']) : 1;
        $key = $_POST['key'] ? trim($_POST['key']) : '';

        $user_id = intval(FARM_APP::session()->info['uid']);
        $this->model('action')->add($user_id, 0, '查看文章列表：'.$cate, get_client(), fetch_ip());

        $arr = array();

        if ($key) {
            $para = explode('_', $key);
            $article_id = intval($para[2]);
            $list = $this->model('article')->get_article_list('', $page, $page_size, $article_id);
        } else {
            $list = $this->model('article')->get_article_list($cate, $page, $page_size);
        }

        $userids = array(0);
        foreach ($list as $user) {
            $userids[] = $user['user_id'];
        }

        $user_arr = $this->model('user')->get_user_by_ids($userids);

        foreach ($list as $key => $value) {
           $images = array();
           if (preg_match_all('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $value['content'], $matches)) {
               foreach ($matches[2] as $s) {
                   if (!stripos($s, 'ttp:/')) {
                       $images[] = G_DEMAIN . $s;
                   } else {
                       $images[] = $s;
                   }
               }
           }

           $user_info = !empty($user_arr[$value['user_id']]) ? $user_arr[$value['user_id']] : array();

           $arr[] = array(
                           'type' => 1,
                           'authorName' => $user_info['user_name'],
                           'authorId' => $user_info['id'],
                           'key' => $value['id'],
                           'title' => $value['title'],
                           'desc' => $value['summary'],
                           'content' => summary(strip_tags($value['content']), 120),
                           'url' => $value['url'],
                           'pubDate' => date_friendly($value['create_time']),
                           'source' => '模型圈',
                           'softwareLogo' => '',
                           'imgs' => $images,
                           'iTags' => array(array('oscId' => 1, 'name' => '模型', 'tagId' => '1')),
                           'commentCount' => intval($value['comment_num']),
                           'favorite' => false,
                           'wordCount' => mb_strlen($value['content']),
                           'sub_type' => 1,
                           'readTime' => $value['read'],
                           'titleTranslated' => '',
                           'osc_id' => intval($value['id']),
                           'view_count' => $value['read']);
        }

        $result = array();

        $time = date("Y-m-d H:i:s", time());

        $result['items'] = $arr;
        $result['nextPageToken'] = ($page+1);
        $result['prevPageToken'] = ($page-1) > 0 ? ($page-1) : 1;
        $result['requestCount'] = $page_size;
        $result['responseCount'] = count($arr);
        $result['totalResults'] = 1000;

        $this -> jsonReturn($result, 1, 'SUCCESS', null, $time);
    }

    /**
     * 文章详情
     */
    public function detail_action() {
        $article_id = trim($_POST['id']);

        // 跟踪用户行为
        $user_id = intval(FARM_APP::session()->info['uid']);
        $this->model('action')->add($user_id, 0, '阅读文章：'.$article_id, get_client(), fetch_ip());

        $info = $this->model('article')->get_article_by_id($article_id);

        $user_info = $this->model('user')->get_user_info_by_id($info['user_id']);
        $author = array('id' => $user_info['id'],
                        'name' => $user_info['user_name'],
                        'portrait' => G_DEMAIN.$user_info['avatar'],
                        'relation' => 4,
                        'gender' => $user_info['sex'],
                        'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false));

        $comment_count = FARM_APP::model('comment')->count('comment', 'target_id = "'.$article_id.'" and `type`="article"');
        $statistics = array('comment' => $comment_count, 'favCount' => 0, 'like' => 0, 'transmit' => 0, 'view' => intval($info['read']));

        $result = array('id' => intval($info['id']),
                        'title' => $info['title'],
                        'body' => $info['content'],
                        'pubDate' => date_friendly($info['create_time']),
                        'href' => G_DEMAIN.'/article/'.$info['id'].'.html',
                        'type' => 1,
                        'favorite' => false,
                        'summary' => $info['summary'],
                        'author' => $author,
                        'images' => $info['images'],
                        'extra' => null,
                        'statistics' => $statistics,
                        'software' => null,
                        'newsId' => intval($info['id']),
                        'iTags' => null,
                        'tags' => null,
                        'about' => null);

        $this->jsonReturn($result);
    }
}
