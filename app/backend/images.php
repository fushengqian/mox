<?php
define('IN_AJAX', TRUE);

class images extends MOX_ADMIN_CONTROLLER
{
    public function setup()
    {
        HTTP::no_cache_header();
    }

    // 图片上传
    public function upload_action()
    {
        $model_id  = trim($_GET['model_id']);
        if (!$model_id) {
            return false;
        }

        $para = array(
            'allowed_types' => 'jpg,jpeg,png,gif',
            'upload_path' => APP_PATH . 'static/upload/' . date("Ymd", time()) . '/',
            'is_image' => FALSE,
            'max_size' => 100000
        );

        MOX_APP::upload()->initialize($para);

        if (isset($_GET['mox_upload_file'])) {
            MOX_APP::upload()->do_upload($_GET['mox_upload_file'], file_get_contents('php://input'));
        } else if (isset($_FILES['mox_upload_file'])) {
            MOX_APP::upload()->do_upload('mox_upload_file');
        } else {
            return false;
        }

        if (MOX_APP::upload()->get_error()) {
            switch (MOX_APP::upload()->get_error()) {
                default:
                    die("{'error':'错误代码: " . MOX_APP::upload()->get_error() . "'}");
                    break;

                case 'upload_invalid_filetype':
                    die("{'error':'文件类型无效'}");
                    break;

                case 'upload_invalid_filesize':
                    die("{'error':'文件尺寸过大, 最大允许尺寸为 " . get_setting('upload_size_limit') . " KB'}");
                    break;
            }
        }

        if (!$upload_data = MOX_APP::upload()->data()) {
            die("{'error':'上传失败, 请与管理员联系'}");
        }

        $upload_url = '/static/upload/'.date("Ymd", time()).'/'. $upload_data['file_name'];

        if (file_exists(APP_PATH.$upload_url)) {
            $info = $this->model('model')-> fetch_row('model', 'id = "'.$model_id.'"');
            $pics = json_decode($info['pics'], true);
            if (!in_array($upload_url, $pics)) {
                $pics[] = array('url' => $upload_url, 'brief' => '', 'user_id' => 0);
            }
            $arr = array('pics' => json_encode($pics), 'update_time' => time());
            $this->model('model')-> update('model', $arr, 'id = "'.$model_id.'"');
        }

        exit(htmlspecialchars(json_encode($upload_data), ENT_NOQUOTES));
    }
}
