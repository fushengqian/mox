<?php
class api extends FARM_CONTROLLER
{
    /**
     * 获取消息数量
     */
    public function notice_action()
    {
        $this -> jsonReturn(null, 1, 'SUCCESS');
    }

    /**
     * 发送私信
     */
    public function send_action()
    {
        $user_id = !empty($_POST['user_id']) ? trim($_POST['user_id']) : 0;
        $content = !empty($_POST['content']) ? trim($_POST['content']) : '';

        if (!$user_id) {
            $this -> jsonReturn(null, -1, '抱歉，系统出错了！');
        }

        $type = 1;
        $resource = '';
        if ($_FILES['file'] || $_POST['file']) {
            $type = 3;
            // 上传图片到临时目录
            $resource = $this -> _sendImage();
            if (empty($resource)) {
                $this -> jsonReturn(null, -1, '抱歉，不能上传超过10m的图片！');
            }
        }

        $from_user_id = FARM_APP::session()->info['uid'];

        $msg_id = $this->model('message')->send($user_id, $from_user_id, $content, '', 'letter', 0, $resource);

        $user_arr = $this->model('user')->get_user_by_ids(array($from_user_id, $user_id));
        $sender = array('id' => $from_user_id,
                        'name' => $user_arr[$from_user_id]['user_name'],
                        'portrait' => G_DEMAIN.$user_arr[$from_user_id]['avatar'],
                        'relation' => 4,
                        'gender' => $user_arr[$from_user_id]['sex'],
                        'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false));
        $receiver = array('id' => $user_id,
                        'name' => $user_arr[$user_id]['user_name'],
                        'portrait' => G_DEMAIN.$user_arr[$user_id]['avatar'],
                        'relation' => 4,
                        'gender' => $user_arr[$user_id]['sex'],
                        'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false));

        $result = array(
                    'id' => $msg_id,
                    'content' => $content,
                    'pubDate' => date("Y-m-d H:i:s", time()),
                    'type' => $type,
                    'resource' => $resource,
                    'sender' => $sender,
                    'receiver' => $receiver);

        $this -> jsonReturn($result, 1, 'SUCCESS');
    }

    /**
     * 清空消息列表
     */
    public function clear_action()
    {
        $user_id = FARM_APP::session()->info['uid'];

        // 全部消息置为已读
        $this->model('message')->set_readed($user_id);

        $this -> jsonReturn(null, 1, 'SUCCESS');
    }

    /**
     * 获取系统消息
     */
    public function system_action()
    {
        $user_id = FARM_APP::session()->info['uid'];
        $page_size = 15;
        $page = !empty($_POST['page']) ? $_POST['page'] : 1;

        $list = $this->model('message')->get_data_list(array('user_id = '.intval($user_id), 'type = "system"'), $page, $page_size);

        $arr = array();
        foreach ($list as $k => $v) {
            $author = array('id' => 100,
                            'name' => '模型圈',
                            'portrait' => 'http://www.moxquan.com/static/avatar/default.jpg',
                            'relation' => 4,
                            'gender' => 1,
                            'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false));

            $origin = array('id' => 0,
                            'desc' => '',
                            'href' => '',
                            'type' => 1);

            $arr[] = array('id' => $v['id'],
                            'content' => strip_tags($v['content']),
                            'pubDate' => date_friendly($v['create_time']),
                            'appClient' => 1,
                            'origin' => $origin,
                            'author' => $author);
        }

        $result['items'] = $arr;
        $result['nextPageToken'] = ($page+1);
        $result['prevPageToken'] = ($page-1) > 0 ? ($page-1) : 1;
        $result['requestCount'] = $page_size;
        $result['responseCount'] = count($arr);
        $result['totalResults'] = count($arr);

        $time = date("Y-m-d H:i:s", time());

        $this -> jsonReturn($result, 1, 'SUCCESS', null, $time);
    }

    /**
     * 获取点赞消息
     */
    public function commentlike_action()
    {
        $page_size = 15;
        $page = !empty($_POST['page']) ? $_POST['page'] : 1;

        $user_id = FARM_APP::session()->info['uid'];

        $list = $this->model('message')->get_data_list(array('user_id = '.intval($user_id), '(type = "comment" or type = "like")'), $page, $page_size);

        $arr = array();
        foreach ($list as $k => $v) {
            if (empty($v['from_user_id'])) {
                $v['from_user_id'] = '10100';
            }
            $user_info = $this->model('user')->get_user_info_by_id($v['from_user_id']);
            $author = array('id' => $user_info['id'],
                            'name' => $user_info['user_name'],
                            'portrait' => G_DEMAIN.$user_info['avatar'],
                            'relation' => 4,
                            'gender' => 1,
                            'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false));

            $feed = $this->model('feed')->fetch_row('feed', "id = '".$v['target_id']."'");
            $origin = array('id' => $feed['id'],
                            'desc' => '',
                            'href' => '',
                            'type' => 100);

            $arr[] = array('id' => $v['id'],
                            'content' => strip_tags($v['content']),
                            'pubDate' => date_friendly($v['create_time']),
                            'appClient' => 1,
                            'origin' => $origin,
                            'author' => $author);
        }

        $result['items'] = $arr;
        $result['nextPageToken'] = ($page+1);
        $result['prevPageToken'] = ($page-1) > 0 ? ($page-1) : 1;
        $result['requestCount'] = $page_size;
        $result['responseCount'] = count($arr);
        $result['totalResults'] = count($arr);

        $time = date("Y-m-d H:i:s", time());

        $this -> jsonReturn($result, 1, 'SUCCESS', null, $time);
    }

    /**
     * 私信
     */
    public function letter_action()
    {
        $page_size = 15;
        $page = !empty($_POST['page']) ? $_POST['page'] : 1;
        $user_id = FARM_APP::session()->info['uid'];

        // from_user_id为空就是获取所有发送给我的消息，否则就是获取我和对方之间的私信
        $from_user_id  = !empty($_POST['user_id']) ? $_POST['user_id'] : 0;

        if (!empty($from_user_id)) {
            $cond = array('(user_id = '.intval($user_id).' or user_id = '.intval($from_user_id).')',
                          '(from_user_id = '.intval($user_id).' or from_user_id = '.intval($from_user_id).')',
                          'type = "letter"');
            $list = $this->model('message')->get_data_list($cond, $page, $page_size, 'id asc');
        } else {
            $list = $this->model('message')->get_data_list(array('user_id = '.intval($user_id), 'type = "letter"'), $page, $page_size);
        }

        $arr = array();
        foreach ($list as $k => $v) {
            $user_info = $this->model('user')->get_user_info_by_id($v['from_user_id']);
            $sender = array('id' => $user_info['id'],
                            'name' => $user_info['user_name'],
                            'portrait' => G_DEMAIN.$user_info['avatar'],
                            'relation' => 4,
                            'gender' => 1,
                            'identity' => array('officialMember' => false, 'tenthAnniversary' => false, 'softwareAuthor' => false));

            $feed = $this->model('feed')->fetch_row('feed', "id = '".$v['target_id']."'");
            $origin = array('id' => $feed['id'],
                            'desc' => '',
                            'href' => '',
                            'type' => 100);

            $arr[] = array('id' => $v['id'],
                            'content' => strip_tags($v['content']),
                            'pubDate' => date("Y-m-d H:i:s", $v['create_time']),
                            'appClient' => 1,
                            'origin' => $origin,
                            'type' => !empty($v['image']) ? 3 : 1,
                            'resource' => $v['image'],
                            'sender' => $sender);
        }

        $result['items'] = $arr;
        $result['nextPageToken'] = ($page+1);
        $result['prevPageToken'] = ($page-1) > 0 ? ($page-1) : 1;
        $result['requestCount'] = $page_size;
        $result['responseCount'] = count($arr);
        $result['totalResults'] = count($arr);

        $time = date("Y-m-d H:i:s", time());

        $this -> jsonReturn($result, 1, 'SUCCESS', null, $time);
    }

    /**
     * 私信图片
     */
    private function _sendImage() {
        if (!$file = $_FILES['file']) {
            return '';
        }

        if ($_FILES['file']['size'] > 1024 * 1024 * 10 || $_FILES['file']['size'] == 0) {
            return '';
        }

        $upload_path = APP_PATH . 'static/upload/temp/' . date("Ymd", time());
        $partList = explode('/', $upload_path);

        $path = '';
        foreach ($partList as $part) {
            $path .= $part . '/';
            if (is_dir($path)) {
                continue;
            }
            if (!mkdir($path)) {
                chmod($path, 0755);
            }
        }

        $file_name = date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $path_info = pathinfo('/' . $file_name . $file['name']);
        $upload_path .= '/' . $file_name . '.' . $path_info['extension'];

        if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_path)) {
            return G_DEMAIN.'/static/upload/temp/' . date("Ymd") . '/' . $file_name . '.' . $path_info['extension'];
        } else {
            return '';
        }
    }
}
