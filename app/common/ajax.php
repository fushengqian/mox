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

class ajax extends MOX_CONTROLLER
{
    /**
     * 日志
     */
    public function logs_action()
    {
        $type = intval($_POST['type']);
        $logs = '未知操作';
        if ($type == 1) {
            $mox_id = trim($_POST['mox_id']);
            $tel     = trim($_POST['tel']);
            $url= '/mox/'.$mox_id.'.html';
            $logs = '执行操作：给<a target="_blank" href="'.$url.'"> 商家 </a>拨打电话'.$tel.'（' . get_client() . ',' . fetch_ip() . '）';
        }
        MOX_APP::model('logs')->insert('logs', array(
                'content' => $logs,
                'level' => 'info',
                'create_time' => time()));

        return;
    }

    /**
     * 留言消息
     */
    public function union_action()
    {
        $id   = intval($_POST['id']);
        $this -> model('union') -> update_click_num($id);
        H::ajax_json_output(array());
    }

    /**
     * 获取当前城市
     * */
    public function get_city_action()
    {
        $city_id = HTTP::get_cookie('city_id');
        
        $city_info = get_city_detail($city_id);
        
        if (!$city_info)
        {
            $city_info = array('name' => '全国');
        }
        
        H::ajax_json_output($city_info);
    }

    /**
     * 留言消息
     */
    public function do_message_action()
    {
        $content = trim($_POST['content']);
        $mox_id = decode($_POST['mox_id']);
        $mobile  = trim($_POST['mobile']);
        $name    = trim($_POST['name']);

        if (empty($content) || empty($mox_id) || empty($name) || empty($mobile))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '额...您的信息填写不完整！'));
            return false;
        }

        if (strlen($mobile) != 11)
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '额...您的手机号有误！'));
            return false;
        }

        $data =  $this->model('system')->fetch_row('message', "mox_id = '".trim($mox_id)."' AND mobile = '".trim($mobile)."' AND content = '".trim($content)."'");
        if (!empty($data))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '留言已成功！'));
            return false;
        }

        // 一天只能10条留言
        $start_time = strtotime(date("Y-m-d", time()).' 00:00:00');
        $end_time = strtotime(date("Y-m-d", time()).' 23:59:59');
        $data =  $this->model('system')->fetch_all('message', "mobile = '".trim($mobile)."' AND create_time <= '".($end_time)."' AND create_time >= '".($start_time)."'");
        if (count($data) > 10)
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '留言已成功，请勿重复留言！'));
            return false;
        }

        $mox_info = $this -> model('mox') -> get_one(encode($mox_id));

        $this->model('system')->insert('message', array('mox_id' => $mox_id,
             'content' => $content,
             'mobile' => $mobile,
             'create_time' => time(),
             'ip' => fetch_ip(),
             'client' => get_client(),
             'is_user' => !empty($mox_info['user_id']) ? 1 : 0,
             'name' => $name));

        // 给庄主发送短信
        $mox_mobile = trim($mox_info['mobile']);
        $sms_param = array('name' => $mox_info['contact'],
                           'visitor' => $name,
                           'product' => $mox_info['name'],
                           'code' => $mox_info['id']);

        preg_match_all('/1[34578][0-9]{8,10}/', $mox_mobile, $mobile_arr);
        if (!empty($mobile_arr[0])) {
            foreach($mobile_arr[0] as $phone) {
                MOX_APP::model('sms')->send($phone, $sms_param, 2);
            }
        }

        H::ajax_json_output(MOX_APP::RSM(null, 1, null));
    }

    /**
     * 添加评论
     * */
    public function do_comment_action()
    {
        $content   = $_POST['content'];
        $mox_id   = $_POST['mox_id'];
        $type      = $_POST['type'] ? intval($_POST['type']) : 1;
        $point     = str_replace('star', '', $_POST['point']);
        
        if (MOX_APP::session()->info['user_name'] !== '18976679980')
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '请先登录'));
        }
        
        if (empty($content) || empty($mox_id))
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '评论内容不能为空！'));
        }
        
        //评论时间随机
        $time = time();

        $user_id = intval(MOX_APP::session()->info['uid']);

        $arr = array(
            'type'   => $type,
            'target_id' => decode($mox_id),
            'content'   => $content,
            'avg_price' => 0,
            'point'     => intval($point),
            'user_id'   => $user_id,
            'create_time' => $time,
            'status'  => 1,
        );

        $result = $this -> model('comment') -> add($arr);
        if ($result)
        {
            //添加图片
            if ($_POST['image'])
            {
                foreach($_POST['image'] as $k => $v)
                {
                    $this -> model('images') -> insert('mox_images',
                                                       array('mox_id' => decode($mox_id),
                                                            'from' => 2,
                                                            'comment_id' => intval($result),
                                                            'url'  => str_replace('/static/', '/', $v),
                                                            'create_time' => time(),
                                                            'user_id' => 0,
                                                            'brief' => trim($_POST['brief'][$k]),
                                                            'status' => 1));
                }
            }
            
            H::ajax_json_output(MOX_APP::RSM(null, 1, null));
        }
        else
        {
            H::ajax_json_output(MOX_APP::RSM(null, -1, '评论失败！'));
        }
    }
    
    /**
     * 评论图片
     * */
    public function upload_comment_action()
    {
        if (empty(MOX_APP::session()->info['uid']))
        {
            H :: ajax_json_output(MOX_APP::RSM(null, -1, '请先登录'));
            exit;
        }
        
        if (!$file = $_FILES['file'])
        {
            H :: ajax_json_output(MOX_APP::RSM(null, -1, '没有文件上传'));
        }
        
        if(!stripos($_FILES['file']['name'], 'jpeg') && !stripos($_FILES['file']['name'], 'jpg') && !stripos($_FILES['file']['name'], 'png') && !stripos($_FILES['file']['name'], 'gif'))
        {
            H :: ajax_json_output(MOX_APP::RSM(null, -1, '只能上传jpg/png/gif类型的图片'));
        }
        
        if ($_FILES['file']['size'] > 1024*1024*3 || $_FILES['file']['size'] == 0)
        {
            H :: ajax_json_output(MOX_APP::RSM(null, -1, '不能上传大于3m的图片'));
        }
        
        $upload_path = APP_PATH.'static/static_2/'.date("Ymd", time());
        
        $partList = explode('/', $upload_path);

        $path = '';
        foreach($partList as $part)
        {
            $path .= $part.'/';
            if( is_dir($path) ) continue;
            
            if( !mkdir($path) )
            {
                chmod($path, 0755);
            }
        }
        
        $file_name = date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
        $path_info = pathinfo('/'.$file_name.$file['name']);
        $upload_path .= '/'.$file_name.'.'.$path_info['extension'];
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $upload_path))
        {
            $url = '/static/static_2/'.date("Ymd").'/'.$file_name.'.'.$path_info['extension'];
            H :: ajax_json_output(MOX_APP::RSM(md5($url), 0, $url));
        }
        else
        {
            H :: ajax_json_output(MOX_APP::RSM(null, -1, '抱歉，图片上传失败'));
        }
    }
}
