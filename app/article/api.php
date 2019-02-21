<?php
class api extends FARM_CONTROLLER
{
    /**
     * 头条资讯
     */
    public function banner_action()
    {
        $arr = array();

        $arr[] = array(
                  'name' => '生活不止眼前的苟且，重新杀入模型界！',
                  'detail' => '已经一年多没有碰模型了，现在闺女出生以后，更是没有了时间和精力。今天跟朋友聊天，聊到了自己的爱好，他知道我之前的爱好是做模型，我跟他聊起来模型入门有多难。然后他说了一句，你明明有这么好的爱好却放弃',
                  'img' => 'http://www.moxquan.com/static/upload/01/14-1.png',
                  'href' => 'http://www.moxquan.com',
                  'pubDate' => '2019-01-29 11:00:09',
                  'type' => 6,
                  'id' => 1);

        $result = array();

        $time = date("Y-m-d H:i:s", time());

        $notice = array('like' => 0,
                         'review' => 0,
                         'letter' => 0,
                         'newsCount' => 0,
                         'mention' => 0,
                         'fans' => 0);

        $result['items'] = $arr;
        $result['nextPageToken'] = "1";
        $result['prevPageToken'] = "1";
        $result['requestCount'] = 5;
        $result['responseCount'] = 1;
        $result['totalResults'] = 5;

        $this -> jsonReturn($result, 1, 'SUCCESS', $notice, $time);
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

           $user_info = $this->model('user')->get_user_info_by_id($value['user_id']);

           $arr[] = array(
                           'type' => 1,
                           'authorName' => $user_info['user_name'],
                           'authorId' => $user_info['id'],
                           'key' => $value['id'],
                           'title' => $value['title'],
                           'desc' => $value['summary'],
                           'content' => strip_tags($value['content']),
                           'url' => $value['url'],
                           'pubDate' => date_friendly($value['create_time']),
                           'source' => '模型圈',
                           'softwareLogo' => '',
                           'imgs' => $images,
                           'iTags' => array(array('oscId' => 1, 'name' => '模型', 'tagId' => '1')),
                           'commentCount' => rand(100, 999),
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

        $notice = array('like' => 0,
                        'review' => 0,
                        'letter' => 0,
                        'newsCount' => 0,
                        'mention' => 0,
                        'fans' => 0);

        $result['items'] = $arr;
        $result['nextPageToken'] = ($page+1);
        $result['prevPageToken'] = ($page-1) > 0 ? ($page-1) : 1;
        $result['requestCount'] = $page_size;
        $result['responseCount'] = count($arr);
        $result['totalResults'] = 1000;

        $this -> jsonReturn($result, 1, 'SUCCESS', $notice, $time);
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

        $statistics = array('comment' => 0, 'favCount' => 0, 'like' => 0, 'transmit' => 0, 'view' => rand(1000, 9999));

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
