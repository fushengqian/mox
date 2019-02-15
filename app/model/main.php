<?php

class main extends FARM_CONTROLLER
{
    public function setup()
    {
        TPL::import_css('css/article.css');
    }

    /**
     * 模型详情
     * */
    public function detail_action()
    {
        $model_id = trim($_GET['id']);

        $info = $this->model('model')->get_model_by_id($model_id);

        //404
        if (empty($info)) {
            HTTP::error_404();
            exit;
        }

        $article_list = $this->model('article')->get_article_list('', 1, 20);

        //获取回复
        $comment_list = FARM_APP::model('comment')->fetch_all('comment', "target_id = " . intval($info['id']) . " AND `type` = 3");
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
        TPL::assign('info', $info);
        TPL::assign('article_list', $article_list);

        TPL::import_js('js/jquery.form.js');
        TPL::import_js('js/template.js');
        TPL::import_js('js/fnc.js');

        $seo = array('title' => array($info['brand_name'], $info['name']),
                     'keywords' => array($info['brand_name'], $info['code']),
                     'description' => array($info['brand_name'], $info['name'], $info['scale'], $info['code']));
        TPL::assign('seo', get_seo('model', $seo));

        TPL::output('model/detail');
    }
}
