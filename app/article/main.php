<?php

class main extends MOX_CONTROLLER
{
    public function setup()
    {
        TPL::import_css('css/article.css');
    }

    /**
     * 文章详情
     * */
    public function detail_action()
    {
        fix_client();

        $is_preview = intval($_GET['is_preview']);
        $is_new = intval($_GET['is_new']);

        $info = $this->model('article')->get_article_by_id(intval($_GET['id']));

        //404
        if (empty($info)) {
            HTTP::error_404();
            exit;
        }

        //更新浏览数
        $this -> model('article') -> update_view(intval($_GET['id']), $info['read'], $info['create_time']);

        $share_pic = '';
        if (preg_match_all('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i', $info['content'], $matches)) {
            foreach ($matches[2] as $s) {
                if (!stripos($s, 'ttp:/')) {
                    $share_pic = G_DEMAIN . $s;
                    $info['content'] = str_replace($s, G_DEMAIN . $s, $info['content']);
                }
            }
        }

        $article_list = $this->model('article')->get_article_list('', 1, 20);

        //获取回复
        $comment_list = MOX_APP::model('comment')->fetch_all('comment', "target_id = " . intval($info['id']) . " AND `type` = 2");
        $user_ids = array();
        foreach ($comment_list as $comment) {
            $user_ids[] = $comment['user_id'];
        }
        if ($user_ids) {
            $users_info = $this->model('user')->get_user_by_ids($user_ids);
            foreach ($comment_list as $key => $comments) {
                $comment_list[$key]['user_info'] = $users_info[$comments['user_id']];
            }
        }

        TPL::assign('comment_list', $comment_list);

        $seo = array('title' => array($info['title']),
            'keywords' => array($info['keywords']),
            'description' => array(str_replace("&nbsp;", " ", ltrim($info['summary']))));
        TPL::assign('seo', get_seo('article_detail', $seo));

        TPL::assign('share', get_share(G_DEMAIN . '/article/' . $info['id'] . '.html', $info['title'], $share_pic, summary($info['content'], 120)));

        TPL::assign('info', $info);
        TPL::assign('article_list', $article_list);
        TPL::assign('is_preview', $is_preview);
        TPL::assign('is_new', $is_new);

        TPL::import_js('js/jquery.form.js');
        TPL::import_js('js/template.js');
        TPL::import_js('js/fnc.js');
        if ($is_new) {
            TPL::import_js('js/bootstrap.min.js');
        }

        $seo = array('title' => array($info['title']),
                     'keywords' => array($info['keywords']),
                     'description' => array($info['summary']));

        if ($is_preview) {
            $seo = get_seo('default', $seo);
        } else {
            $seo = get_seo('article', $seo);
        }

        TPL::assign('seo', $seo);

        TPL::output('article/detail');
    }

    /**
     * 文章发布页面
     * */
    public function publish_action()
    {
        $article_id = $_GET['id'] ? $_GET['id'] : 0;

        if ($article_id) {
            $info = $this->model('article')->get_article_by_id($article_id);
            TPL::assign('info', $info);
        }

        TPL::assign('seo', get_seo('publish'));

        TPL::import_js('ueditor/ueditor.config.js');
        TPL::import_js('ueditor/ueditor.all.min.js');
        TPL::import_js('ueditor/lang/zh-cn/zh-cn.js');
        TPL::import_js('js/jquery.form.js');
        TPL::import_js('js/template.js');
        TPL::import_js('js/fnc.js');

        TPL::output('article/publish');
    }

    /**
     * 删除文章
     * */
    public function delete_action()
    {
        $article_id = $_GET['id'] ? $_GET['id'] : 0;

        $result = $this->model('article')->delete('article', 'id = ' . intval($article_id));

        if ($result) {
            echo 'succeed...';
        } else {
            echo 'fail...';
        }

        exit;
    }
}
