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
            H::ajax_json_output(MOX_APP::RSM(null, -1, '请先登录'));
            exit;
        }

        $base64_img = trim($_POST['file']);

        $file_path = 'static/upload/'.date("Ymd", time());
        $upload_path = APP_PATH . $file_path;
        $partList = explode('/', $upload_path);
        $path = '';
        $time = time();

        foreach ($partList as $part) {
            $path .= $part . '/';
            if (is_dir($path)) continue;
            if (!mkdir($path)) {
                chmod($path, 0755);
            }
        }

        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)) {
            $type = !empty($result[2]) ? $result[2] : 'png';
            if (in_array($type,array('pjpeg','jpeg','jpg','gif','bmp','png'))) {
                $new_file = $upload_path.'/'.md5($time.$user_id).'.'.$type;

                if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_img)))) {

                    $url_res = G_DEMAIN.'/'.$file_path.'/'.md5($time.$user_id).'.'.$type;

                    // 生成缩略图
                    $path_s = APP_PATH.str_replace(G_DEMAIN.'/', '', $url_res);
                    $path_info = path_info($path_s);
                    $url = G_DEMAIN.'/'.$file_path.'/'.md5($url_res).'.'.$type;
                    $preview = $path_info['dirname'].md5($url).'.'.$path_info['extension'];
                    $preview_url =  str_replace($path_info['filename'], md5($url), $url_res);
                    if (!file_exists($preview)) {
                        $imageTool = new IMAGE($url_res, $path_info['dirname']);
                        $imageTool->compressImage(120, 120, true, md5($url));
                    }

                    // 加上水印，并删除原来的图片
                    $imageTool = new IMAGE($url_res, $upload_path.'/');
                    $water = APP_PATH.'static/images/water.jpg';
                    $imageTool->addWatermark($water, 30, true);
                    @unlink($upload_path.'/'.md5($time.$user_id).'.'.$type);

                    H::ajax_json_output(MOX_APP::RSM(array('url' => $url, 'preview' => $preview_url), 1, '上传成功！'));
                } else {
                    H:: ajax_json_output(MOX_APP::RSM(null, -1, '抱歉，图片上传失败！'));
                }
            } else {
                //文件类型错误
                H:: ajax_json_output(MOX_APP::RSM(null, -1, '只能上传jpg/png/gif类型的图片'));
            }
        } else {
                H:: ajax_json_output(MOX_APP::RSM(null, -1, '没有文件上传'));
        }

        return false;
    }
}
