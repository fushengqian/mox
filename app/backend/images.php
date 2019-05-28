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
        $para = array(
            'allowed_types' => 'jpg,jpeg,png,gif',
            'upload_path' => APP_PATH . 'data/upload/' . date("Ymd", time()) . '/',
            'is_image' => FALSE,
            'max_size' => 100000
        );

        MOX_APP::upload()->initialize($para);

        if (isset($_GET['mox_upload_file'])) {
            MOX_APP::upload()->do_upload($_GET['mox_upload_file'], file_get_contents('php://input'));
        } else if (isset($_FILES['mox_upload_file'])) {
            MOX_APP::upload()->do_upload('mox_upload_file');
        } else {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('没文件上传！')));
        }

        if (MOX_APP::upload()->get_error()) {
            switch (MOX_APP::upload()->get_error()) {
                default:
                    H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t(MOX_APP::upload()->get_error())));
                    break;

                case 'upload_invalid_filetype':
                    H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('文件类型无效!')));
                    break;

                case 'upload_invalid_filesize':
                    H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('文件尺寸过大, 最大允许尺寸为'.get_setting('upload_size_limit').'kb')));
                    break;
            }
        }

        if (!$upload_data = MOX_APP::upload()->data()) {
            H::ajax_json_output(MOX_APP::RSM(null, -1, MOX_APP::lang()->_t('上传失败, 请与管理员联系!')));
        }

        $upload_url = '/data/upload/'.date("Ymd", time()).'/'. $upload_data['file_name'];

        H::ajax_json_output(MOX_APP::RSM(array(
            'url' => trim($upload_url)
        ), 1, null));
    }
}
