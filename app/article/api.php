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

        $notice = array('like' => 10,
                         'review' => 3,
                         'letter' => 2,
                         'newsCount' => 1,
                         'mention' => 10,
                         'fans' => 12);

        $result['items'] = $arr;
        $result['nextPageToken'] = "D398D98FDF2FE7E64AD6659CDD5740BA";
        $result['prevPageToken'] = "E7E5554DA0FCDD769A47EC1668BC550B";
        $result['requestCount'] = 20;
        $result['responseCount'] = 20;
        $result['totalResults'] = 100;

        $this -> jsonReturn($result, 1, 'SUCCESS', $notice, $time);
    }

    /**
     * 文章列表
     */
    public function list_action()
    {
        $arr = array();

        $arr[] = array(
            'type' => 1,
            'authorName' => '符皓',
            'authorId' => '1001',
            'key' => '1001',
            'title' => '生活不止眼前的苟且，重新杀入模型界！',
            'desc' => '已经一年多没有碰模型了，现在闺女出生以后，更是没有了时间和精力。今天跟朋友聊天，聊到了自己的爱好，他知道我之前的爱好是做模型，我跟他聊起来模型入门有多难。',
            'content' => '已经一年多没有碰模型了，现在闺女出生以后，更是没有了时间和精力。今天跟朋友聊天，聊到了自己的爱好，他知道我之前的爱好是做模型，我跟他聊起来模型入门有多难。',
            'url' => 'http://www.moxquan.com',
            'pubDate' => '01-29 11:00',
            'source' => '模型圈',
            'softwareLogo' => '',
            'imgs' => array('http://www.moxquan.com/static/upload/01/16-1.png',
                            'http://www.moxquan.com/static/upload/01/14-1.png',
                            'http://www.moxquan.com/static/upload/01/13-1.png',
                            ),
            'iTags' => array(array('oscId' => 1001, 'name' => '田宫', 'tagId' => '1000')),
            'commentCount' => 102,
            'favorite' => false,
            'wordCount' => 1000,
            'subType' => 1,
            'readTime' => 10023,
            'titleTranslated' => '',
            'oscId' => 1001,
            'view_count' => 10001);

        $result = array();

        $time = date("Y-m-d H:i:s", time());

        $notice = array('like' => 10,
            'review' => 3,
            'letter' => 2,
            'newsCount' => 1,
            'mention' => 10,
            'fans' => 12);

        $result['items'] = $arr;
        $result['nextPageToken'] = "D398D98FDF2FE7E64AD6659CDD5740BA";
        $result['prevPageToken'] = "E7E5554DA0FCDD769A47EC1668BC550B";
        $result['requestCount'] = 20;
        $result['responseCount'] = 20;
        $result['totalResults'] = 100;

        $this -> jsonReturn($result, 1, 'SUCCESS', $notice, $time);
    }
}
