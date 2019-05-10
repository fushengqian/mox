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

class upload extends MOX_CONTROLLER
{
    /**
     * 上传文件
     */
    public function do_action()
    {
        $user_id = MOX_APP::session()->info['uid'];
        if (empty($user_id)) {
            $this -> jsonReturn([], -1, '您的登录信息已过期！');
        }

        // 生成token
        if (empty($_REQUEST['token'])) {
            $token = md5("feed_upload_" . $user_id . time());
        } else {
            $token = trim($_REQUEST['token']);
        }

        if (!$file = $_FILES['resource']) {
            $this -> jsonReturn(null, -1, '没有文件上传');
        }

        if (!stripos($_FILES['resource']['name'], 'jpeg') && !stripos($_FILES['resource']['name'], 'jpg') && !stripos($_FILES['resource']['name'], 'png') && !stripos($_FILES['resource']['name'], 'gif')) {
            $this -> jsonReturn(null, -1, '只能上传jpg/png/gif类型的图片');
        }

        if ($_FILES['resource']['size'] > 1024 * 1024 * 10 || $_FILES['resource']['size'] == 0) {
            $this -> jsonReturn(null, -1, '不能上传大于10m的图片');
        }

        $upload_path = APP_PATH . 'static/upload/' . date("Ymd", time());
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

        if (move_uploaded_file($_FILES['resource']['tmp_name'], $upload_path)) {
            $url = '/static/upload/' . date("Ymd") . '/' . $file_name . '.' . $path_info['extension'];

            $result = array(
                      'token' => $token,
                      'resources' => array(array('name' => $file_name.'.'.$path_info['extension'],
                                                  'thumb' => G_DEMAIN.$url,
                                                  'href' => G_DEMAIN.$url,
                                                  'type' => $path_info['extension'])));

                MOX_APP::model('system')->insert('upload_token', array('token' => $token,
                                                                                     'user_id' => $user_id,
                                                                                     'url' => G_DEMAIN.$url));


            $this -> jsonReturn($result, 1, '图片上传成功！');
        } else {
            $this -> jsonReturn(null, -1, '抱歉，图片上传失败');
        }
    }
}
