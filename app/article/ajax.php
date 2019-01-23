<?php
class ajax extends FARM_CONTROLLER
{
    public function publish_action()
    {
        $user_info = FARM_APP::user()->get_info();

        if (empty($user_info['uid']))
        {
            H::ajax_json_output(FARM_APP::RSM(null, - 1, '请先登录哦~'));
        }

        if (!$_POST['title'])
        {
            H::ajax_json_output(FARM_APP::RSM(null, - 1, '请输入文章标题！'));
        }

        if (!$_POST['content'])
        {
            H::ajax_json_output(FARM_APP::RSM(null, - 1, '请输入文章内容！'));
        }

        if ($_POST['is_preview']) {
            $_POST['status'] = 2;
        } else {
            $_POST['status'] = 1;
        }
        
        $id       = intval($_POST['id']);
        $city_id  = $_POST['city_id'];
        $title    = $_POST['title'];
        $content  = $_POST['content'];
        $keywords = $_POST['keywords'];
        $summary  = $_POST['summary'];
        $from     = $_POST['from'] ? $_POST['from'] : '模型圈';
        $farm_id  = $_POST['farm_id'];
        $status   = intval($_POST['status']);
        $user_id  = intval(FARM_APP::session()->info['uid']);

        if (strstr($content, '3002744801') || strstr($title, '你的一生')) {
            H::ajax_json_output(FARM_APP::RSM(null, - 1, '发布文章失败，请稍后再试！'));
        }
        
        $url = $this -> model('article') -> publish($id, $farm_id, $city_id, $title, $content, $keywords, $from, $summary, $status, $user_id);

        if ($_POST['is_preview']) {
            $url .= '?is_preview=1';
        } else {
            $url .= '?is_new=1';
        }

        H::ajax_json_output(FARM_APP::RSM(array(
                'url' => $url
        ), 1, '发布文章成功！'));
    }
}
