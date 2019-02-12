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
        $cate = trim($_POST['cate']);

        $arr = array();

        if ($cate == 'focus') {
            $arr[] = array(
                'type' => 1,
                'authorName' => '符皓',
                'authorId' => '1001',
                'key' => '1001',
                'title' => '生活不止眼前的苟且，重新杀入模型界！',
                'desc' => '已经一年多没有碰模型了，现在闺女出生以后，更是没有了时间和精力。今天跟朋友聊天，聊到了自己的爱好，他知道我之前的爱好是做模型，我跟他聊起来模型入门有多难。',
                'content' => '已经一年多没有碰模型了，现在闺女出生以后，更是没有了时间和精力。今天跟朋友聊天，聊到了自己的爱好，他知道我之前的爱好是做模型，我跟他聊起来模型入门有多难。',
                'url' => 'http://www.moxquan.com',
                'pubDate' => '11:00',
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

            $arr[] = array(
                'type' => 1,
                'authorName' => '符月月',
                'authorId' => '1001',
                'key' => '1001',
                'title' => '中國ZBL-09式步兵戰車！',
                'desc' => '作品名稱：中國ZBL-09式步兵戰車 品牌：小號手 比例：1/35 lD：陸戰隊618',
                'content' => '作品名稱：中國ZBL-09式步兵戰車 品牌：小號手 比例：1/35 lD：陸戰隊618',
                'url' => 'http://www.moxquan.com',
                'pubDate' => '12:10',
                'source' => '模型圈',
                'softwareLogo' => '',
                'imgs' => array('http://www.moxquan.com/static/upload/20190201/2019020155100495.jpg',
                    'http://www.moxquan.com/static/upload/20190201/2019020110197535.jpg',
                    'http://www.moxquan.com/static/upload/20190201/2019020154525051.jpg',
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
        $result['nextPageToken'] = "2";
        $result['prevPageToken'] = "1";
        $result['requestCount'] = 10;
        $result['responseCount'] = 2;
        $result['totalResults'] = 2;

        $this -> jsonReturn($result, 1, 'SUCCESS', $notice, $time);
    }
}
