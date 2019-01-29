<?php
class api extends FARM_CONTROLLER
{
    /**
     * 动态列表
     **/
    public function list_action()
    {
        $result = array();

        $time = date("Y-m-d H:i:s", time());

        $notice = array('like' => 0,
                         'review' => 0,
                         'letter' => 0,
                         'newsCount' => 0,
                         'mention' => 0,
                         'fans' => 0);

        $arr = array(
            array('appClient' => 1,
                  'author' => array('id' => 1001, 'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false),
                                    'name' => '阿呆', 'portrait' => 'http://www.moxquan.com/static/avatar/6.gif', 'relation' => ''),
                  'commentCount' => 0,
                  'content' => '测试一下吧',
                  'href' => 'https://my.oschina.net/u/3984670/tweet/19680400',
                  'id' => 1000,
                  'images' => array(array('h' => 0, 'w' => 0, 'href' => 'http://www.moxquan.com/static/upload/20190124/6be44c2a0b21a5cbffcfd1a3efe79b58.png', 'name' => '6be44c2a0b21a5cbffcfd1a3efe79b58', 'thumb' => 'http://www.moxquan.com/static/upload/20190124/6be44c2a0b21a5cbffcfd1a3efe79b58.png', 'type' => 'png'),
                      array('h' => 0, 'w' => 0, 'href' => 'http://www.moxquan.com/static/upload/20190124/ac2f72192ba59de3c3cd9d88a1cf6fa7.png', 'name' => '6be44c2a0b21a5cbffcfd1a3efe79b58', 'thumb' => 'http://www.moxquan.com/static/upload/20190124/6be44c2a0b21a5cbffcfd1a3efe79b58.png', 'type' => 'png'),
                      array('h' => 0, 'w' => 0, 'href' => 'http://www.moxquan.com/static/upload/20190124/b67a4aa66849137e35333a32004adcc9.png', 'name' => 'b67a4aa66849137e35333a32004adcc9', 'thumb' => 'http://www.moxquan.com/static/upload/20190124/b67a4aa66849137e35333a32004adcc9.png', 'type' => 'png'),
                      array('h' => 0, 'w' => 0, 'href' => 'http://www.moxquan.com/static/upload/20190124/6be44c2a0b21a5cbffcfd1a3efe79b58.png', 'name' => '6be44c2a0b21a5cbffcfd1a3efe79b58', 'thumb' => 'http://www.moxquan.com/static/upload/20190124/6be44c2a0b21a5cbffcfd1a3efe79b58.png', 'type' => 'png'),
                              ),
                  'likeCount' =>100,
                  'liked' => false,
                  'pubDate' => '2019-01-28 16:00:22',
                  'statistics' => array('comment' => 0, 'favCount' => 0, 'like' => 0, 'transmit' => 0, 'view' => 102)),
            array('appClient' => 1,
                'author' => array('id' => 1001, 'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false),
                    'name' => '小新', 'portrait' => 'http://www.moxquan.com/static/avatar/5.gif', 'relation' => ''),
                'commentCount' => 0,
                'content' => '测试一下啦',
                'href' => 'https://my.oschina.net/u/3984670/tweet/19680400',
                'id' => 1001,
                'likeCount' =>1000,
                'liked' => false,
                'pubDate' => '2019-01-28 16:00:22',
                'statistics' => array('comment' => 0, 'favCount' => 0, 'like' => 0, 'transmit' => 0, 'view' => 102)),
            array('appClient' => 1,
                'author' => array('id' => 1001, 'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false),
                    'name' => '正男哥哥', 'portrait' => 'http://www.moxquan.com/static/avatar/4.gif', 'relation' => ''),
                'commentCount' => 0,
                'content' => '测试一下吗',
                'href' => 'https://my.oschina.net/u/3984670/tweet/19680400',
                'id' => 1002,
                'likeCount' =>100,
                'liked' => false,
                'pubDate' => '2019-01-28 16:00:22',
                'statistics' => array('comment' => 0, 'favCount' => 0, 'like' => 0, 'transmit' => 0, 'view' => 102)),
        );

        $result['items'] = $arr;
        $result['nextPageToken'] = "D398D98FDF2FE7E64AD6659CDD5740BA";
        $result['prevPageToken'] = "E7E5554DA0FCDD769A47EC1668BC550B";
        $result['requestCount'] = 20;
        $result['responseCount'] = 20;
        $result['totalResults'] = 1000;

        $this -> jsonReturn($result, 1, 'SUCCESS', $notice, $time);
    }
}
