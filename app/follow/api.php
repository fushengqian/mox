<?php
class api extends FARM_CONTROLLER
{
    /**
     * 关注
     */
    public function do_action()
    {
        $user_id = FARM_APP::session()->info['uid'];
        if (empty($user_id)) {
            $this -> jsonReturn([], -1, '您的登录信息已过期！');
        }

        $follow_user_id = trim($_POST['id']);
        if (empty($follow_user_id)) {
            $this -> jsonReturn([], -1, '抱歉，系统出错！');
        }

        $data = $this -> model('system')->fetch_row('follow', "user_id = '".$user_id."' and follow_user_id = '".$follow_user_id."'");
        if (!$data) {
            $arr = array('user_id' => $user_id,
                         'follow_user_id' => $follow_user_id,
                         'update_time' => time(),
                         'create_time' => time(),
                         'status' => 1);
            $this -> model('system') -> insert('follow', $arr);

            $this->model('message')->send($follow_user_id, $user_id, 'hi，我刚刚关注了你，一起玩模型吧！', '', 'letter', 0, '');
        } else {
            $this->model('system')->delete('follow', 'id = ' . intval($data['id']));
        }

        //1：彼此关注 2：我关注ta 3：ta关注我 4：都没有关注
        $relation = 4;
        $data1 = $this -> model('system')->fetch_row('follow', "user_id = '".$user_id."' and follow_user_id = '".$follow_user_id."'");
        $data2 = $this -> model('system')->fetch_row('follow', "user_id = '".$follow_user_id."' and follow_user_id = '".$user_id."'");
        if ($data1 && $data2) {
            $relation = 1;
        } else if($data1) {
            $relation = 2;
        } else if ($data2){
            $relation = 3;
        }

        $this -> jsonReturn(array('relation' => $relation));
    }

    /**
     * 我的粉丝
     */
    public function fan_action()
    {
        $page = !empty($_POST['page']) ? $_POST['page'] : 1;
        $page_size = 15;

        $user_id = FARM_APP::session()->info['uid'];

        if (empty($user_id)) {
            $this -> jsonReturn([], -1, '您的登录信息已过期！');
        }

        $list = $this -> model('system')->fetch_page('follow', "follow_user_id = '".$user_id."'", 'id desc', $page, $page_size);

        $arr = array();
        $ids = array(0);
        empty($list) && $list = array();
        foreach ($list as $k => $v) {
            $ids[] = $v['user_id'];
        }

        $user_arr = $this -> model('user')->get_user_by_ids($ids);
        foreach ($user_arr as $val) {
            $arr[] = array('id' => $val['id'],
                           'name' => $val['user_name'],
                           'portrait' => G_DEMAIN.$val['avatar'],
                           'gender' => $val['sex'],
                           'desc' => $val['intro'],
                           'relation' => 3);
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
     * 我的关注
     */
    public function following_action()
    {
        $page = !empty($_POST['page']) ? $_POST['page'] : 1;
        $page_size = 15;

        $user_id = FARM_APP::session()->info['uid'];
        $user_id = !empty($_POST['id']) ? $_POST['id'] : $user_id;

        if (empty($user_id)) {
            $this -> jsonReturn([], -1, '您的登录信息已过期！');
        }

        $list = $this -> model('system')->fetch_page('follow', "user_id = '".$user_id."'", 'id desc', $page, $page_size);

        $arr = array();
        $ids = array(0);
        empty($list) && $list = array();
        foreach ($list as $k => $v) {
            $ids[] = $v['follow_user_id'];
        }

        $user_arr = $this -> model('user')->get_user_by_ids($ids);
        foreach ($user_arr as $val) {
            $arr[] = array('id' => $val['id'],
                            'name' => $val['user_name'],
                            'portrait' => G_DEMAIN.$val['avatar'],
                            'gender' => $val['sex'],
                            'desc' => $val['intro'],
                            'relation' => 2);
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
}
