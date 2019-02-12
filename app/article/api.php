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
                  'type' => 1,
                  'id' => 1001);

        $arr[] = array(
                    'name' => '2018年年终总结帖，楼下请排队',
                    'detail' => '2018年年终总结帖，楼下请排队',
                    'img' => 'http://www.moxquan.com/static/upload/01/16-1.png',
                    'href' => 'http://www.moxquan.com',
                    'pubDate' => '2019-01-29 11:00:09',
                    'type' => 1,
                    'id' => 1001);

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
        $result['responseCount'] = 5;
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

        $arr = array();

        $list = $this->model('article')->get_article_list($cate, $page);
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

           $arr[] = array(
                           'type' => 1,
                           'authorName' => '编辑君',
                           'authorId' => '101',
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
                           'commentCount' => 0,
                           'favorite' => false,
                           'wordCount' => mb_strlen($value['content']),
                           'subType' => 1,
                           'readTime' => $value['read'],
                           'titleTranslated' => '',
                           'oscId' => $value['id'],
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
}
