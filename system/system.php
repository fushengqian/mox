<?php
/**
+--------------------------------------------------------------------------
|   Mox
|   ========================================
|   by Mox Software
|   © 2018 - 2019 Mox. All Rights Reserved
|   http://www.mox365.com
|   ========================================
|   Support: 540335306@qq.com
|   Author: FSQ
+---------------------------------------------------------------------------
*/

if (! defined('MOX_PATH'))
{
    define('MOX_PATH', dirname(__FILE__) . '/');
}

require_once (MOX_PATH . 'init.php');

if (defined('G_GZIP_COMPRESS') AND G_GZIP_COMPRESS === TRUE)
{
    if (@ini_get('zlib.output_compression') == FALSE)
    {
        if (extension_loaded('zlib'))
        {
            if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
            {
                ob_start('ob_gzhandler');
            }
        }
    }
}

require_once (MOX_PATH . 'MOX_APP.inc.php');
require_once (MOX_PATH . 'MOX_CONTROLLER.inc.php');
require_once (MOX_PATH . 'mox_model.inc.php');
