<?php
/**
+--------------------------------------------------------------------------
|   Mox 1.0.1
|   ========================================
|   by Mox Software
|   © 2018 - 2019 Mox. All Rights Reserved
|   http://www.moxquan.com
|   ========================================
|   Support: 540335306@qq.com
|   Author: FSQ
+---------------------------------------------------------------------------
*/

class api extends MOX_CONTROLLER
{
    /**
     * 头条文章
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

        if ($cate == 'focus') {
            $cate = '';
        }

        $user_id = intval(MOX_APP::session()->info['uid']);
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
                           'source' => 'Mox',
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

        // 更新最后一次刷新时间
        $this->model('user')->update_user_last_article_time($user_id);

        $this -> jsonReturn($result, 1, 'SUCCESS', null, $time);
    }

    /**
     * 文章详情
     */
    public function detail_action() {
        $article_id = trim($_POST['id']);

        // 跟踪用户行为
        $user_id = intval(MOX_APP::session()->info['uid']);
        $this->model('action')->add($user_id, 0, '阅读文章：'.$article_id, get_client(), fetch_ip());

        $info = $this->model('article')->get_article_by_id($article_id);

        $user_info = $this->model('user')->get_user_info_by_id($info['user_id']);
        $author = array('id' => $user_info['id'],
                        'name' => $user_info['user_name'],
                        'portrait' => G_DEMAIN.$user_info['avatar'],
                        'relation' => 4,
                        'gender' => $user_info['sex'],
                        'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false));

        $comment_count = MOX_APP::model('comment')->count('comment', 'target_id = "'.$article_id.'" and `type`="article"');
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

    public function publish_action()
    {
        $user_id = intval(MOX_APP::session()->info['uid']);

        if (empty($user_id)) {
            $this -> jsonReturn(null, -1, '登录信息已过期！');
        }

        if (!$_POST['title']) {
            $this -> jsonReturn(null, -1, '标题不能为空！');
        }

        if (!$_POST['content']) {
            $this -> jsonReturn(null, -1, '内容不能为空！');
        }

        $id       = intval($_POST['id']);
        $title    = $_POST['title'];
        $content  = $_POST['content'];
        $keywords = implode(',', analysis_keyword($title));
        $summary  = summary(strip_tags($_POST['content']), 200);
        $from     = $_POST['from'] ? $_POST['from'] : 'Mox';
        $status   = 2;
        $cate     = $_POST['cate'] ? $_POST['cate'] : 'focus';

        $artcle_id = $this -> model('article') -> publish($id, $cate, $title, $content, $keywords, $from, $summary, $status, $user_id);

        $this->jsonReturn(array('id' => $artcle_id));
    }
}
